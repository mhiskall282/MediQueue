<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Granular Clinical & Administrative Roles
    public const ROLE_ADMIN      = 'admin';
    public const ROLE_DOCTOR     = 'doctor';
    public const ROLE_NURSE      = 'nurse';
    public const ROLE_PHARMACIST = 'pharmacist';
    public const ROLE_LAB_TECH   = 'lab_tech';
    public const ROLE_STAFF      = 'staff'; // General Clinical Receptionist / Assistant
    public const ROLE_PATIENT    = 'patient';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hospital_id',
        'name',
        'email',
        'password',
        'role',
        'phone',
        'specialization',
        'extended_privileges',
        'medical_license_number',
        'must_change_password',
        'last_login_ip',
        'last_login_at',
        'email_notifications_enabled',
        'is_on_call',
        'on_call_shift',
        'emergency_contact_phone',
        'is_active',
        'is_approved',
        'approved_at',
        'approved_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'           => 'datetime',
            'password'                    => 'hashed',
            'is_active'                   => 'boolean',
            'is_approved'                 => 'boolean',
            'is_on_call'                  => 'boolean',
            'must_change_password'        => 'boolean',
            'email_notifications_enabled' => 'boolean',
            'last_login_at'               => 'datetime',
            'approved_at'                 => 'datetime',
            'extended_privileges'         => 'array',
        ];
    }

    public function rosters(): HasMany
    {
        return $this->hasMany(DoctorRoster::class, 'doctor_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function messagesSent(): HasMany
    {
        return $this->hasMany(ClinicalMessage::class, 'sender_id');
    }

    public function messagesReceived(): HasMany
    {
        return $this->hasMany(ClinicalMessage::class, 'recipient_id');
    }

    public function securityAlerts(): HasMany
    {
        return $this->hasMany(SecurityAlert::class, 'user_id');
    }

    public function scopeOnCall($query)
    {
        return $query->whereIn('role', [self::ROLE_STAFF, self::ROLE_DOCTOR])->where('is_on_call', true);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('is_approved', false);
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (User $user) {
            if (empty($user->hospital_id)) {
                $prefix = match ($user->role) {
                    self::ROLE_ADMIN      => 'MED-ADM',
                    self::ROLE_DOCTOR     => 'MED-DOC',
                    self::ROLE_NURSE      => 'MED-NUR',
                    self::ROLE_PHARMACIST => 'MED-PHM',
                    self::ROLE_LAB_TECH   => 'MED-LAB',
                    self::ROLE_STAFF      => 'MED-STF',
                    default               => 'MRN-2026',
                };
                $user->hospital_id = sprintf('%s-%05d', $prefix, rand(10000, 99999));
            }
        });
    }

    // ================================================================
    // Granular Role & Least Privilege Checkers
    // ================================================================

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isDoctor(): bool
    {
        return $this->role === self::ROLE_DOCTOR;
    }

    public function isNurse(): bool
    {
        return $this->role === self::ROLE_NURSE;
    }

    public function isPharmacist(): bool
    {
        return $this->role === self::ROLE_PHARMACIST;
    }

    public function isLabTech(): bool
    {
        return $this->role === self::ROLE_LAB_TECH;
    }

    public function isPatient(): bool
    {
        return $this->role === self::ROLE_PATIENT;
    }

    /**
     * Check if user is a qualified clinical health professional.
     */
    public function isClinicalProfessional(): bool
    {
        return in_array($this->role, [
            self::ROLE_DOCTOR,
            self::ROLE_NURSE,
            self::ROLE_PHARMACIST,
            self::ROLE_LAB_TECH,
            self::ROLE_ADMIN,
        ], true);
    }

    /**
     * Check if user is non-clinical hospital staff (Receptionist / Front Desk / HR / Help Desk).
     */
    public function isNonClinicalStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    /**
     * Check if user is any staff role (clinical or non-clinical).
     */
    public function isStaff(): bool
    {
        return in_array($this->role, [
            self::ROLE_STAFF,
            self::ROLE_DOCTOR,
            self::ROLE_NURSE,
            self::ROLE_PHARMACIST,
            self::ROLE_LAB_TECH,
        ], true);
    }

    public function isStaffOrAdmin(): bool
    {
        return $this->isStaff() || $this->isAdmin();
    }

    public function isApproved(): bool
    {
        return (bool) $this->is_approved;
    }

    /**
     * Check if an admin has explicitly extended a custom privilege to this user.
     */
    public function hasPrivilege(string $privilege): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $extended = $this->extended_privileges ?? [];
        return in_array($privilege, $extended, true);
    }

    /**
     * Can this user perform medical consultations & discharges?
     */
    public function canConsult(): bool
    {
        return $this->isAdmin() || $this->isDoctor() || $this->hasPrivilege('can_consult');
    }

    /**
     * Can this user perform 5-tier Manchester triage?
     */
    public function canTriage(): bool
    {
        return $this->isAdmin() || $this->isNurse() || $this->isDoctor() || $this->hasPrivilege('can_triage');
    }

    /**
     * Can this user execute lab diagnostics?
     */
    public function canExecuteLab(): bool
    {
        return $this->isAdmin() || $this->isLabTech() || $this->isDoctor() || $this->hasPrivilege('can_execute_lab');
    }

    /**
     * Can this user dispense pharmacy orders?
     */
    public function canDispensePharmacy(): bool
    {
        return $this->isAdmin() || $this->isPharmacist() || $this->hasPrivilege('can_dispense_pharmacy');
    }

    /**
     * Can this user assign hospital beds?
     */
    public function canAssignBeds(): bool
    {
        return $this->isAdmin() || $this->isNurse() || $this->isDoctor() || $this->hasPrivilege('can_assign_beds');
    }

    /**
     * Human-readable formatted role label.
     */
    public function getRoleTitleAttribute(): string
    {
        return match($this->role) {
            self::ROLE_ADMIN      => 'Hospital Administrator',
            self::ROLE_DOCTOR     => 'Medical Doctor / Physician',
            self::ROLE_NURSE      => 'Staff Nurse / Triage Specialist',
            self::ROLE_PHARMACIST => 'Clinical Pharmacist',
            self::ROLE_LAB_TECH   => 'Laboratory Technologist',
            self::ROLE_STAFF      => 'Clinical Operations Staff',
            default               => 'Registered Patient',
        };
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match($this->role) {
            self::ROLE_ADMIN      => 'bg-purple-100 text-purple-800 border-purple-300',
            self::ROLE_DOCTOR     => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            self::ROLE_NURSE      => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::ROLE_PHARMACIST => 'bg-amber-100 text-amber-800 border-amber-300',
            self::ROLE_LAB_TECH   => 'bg-teal-100 text-teal-800 border-teal-300',
            self::ROLE_STAFF      => 'bg-slate-100 text-slate-800 border-slate-300',
            default               => 'bg-blue-100 text-blue-800 border-blue-300',
        };
    }

    // ================================================================
    // Queue & Service Relationships
    // ================================================================

    public function queueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class, 'user_id');
    }

    public function servedEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class, 'served_by');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN      => 'Administrator',
            self::ROLE_DOCTOR     => 'Doctor',
            self::ROLE_NURSE      => 'Nurse',
            self::ROLE_PHARMACIST => 'Pharmacist',
            self::ROLE_LAB_TECH   => 'Lab Tech',
            self::ROLE_STAFF      => 'Staff',
            default               => 'Patient',
        };
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }
}
