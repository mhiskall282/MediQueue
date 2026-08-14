<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\QueueController as PatientQueueController;
use App\Http\Controllers\Staff\AppointmentController as StaffAppointmentController;
use App\Http\Controllers\Staff\ClinicalReferralController;
use App\Http\Controllers\Staff\EmergencyIntakeController;
use App\Http\Controllers\Staff\OnCallController;
use App\Http\Controllers\Staff\QueueController as StaffQueueController;
use App\Http\Controllers\Staff\TriageController as StaffTriageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MediQueue Web Routes
|--------------------------------------------------------------------------
|
| Route Groups:
| 1. Public   — Landing page, /docs technical documentation, /display waiting screen
| 2. Auth     — Login / Register / Logout (Throttle protected)
| 3. Patient  — Join queue, live tracking, appointments, history
| 4. Staff    — Queue operations, emergency triage, hospital beds, appointment check-in
| 5. Admin    — Service catalogue, user management, clinic settings, audit, reports
|
*/

// ============================================================
// 1. Public Routes
// ============================================================

Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'staff'   => redirect()->route('staff.dashboard'),
            default   => redirect()->route('patient.dashboard'),
        };
    }
    return view('welcome');
})->name('home');

// Interactive In-App Documentation Hub
Route::get('/docs', [DocsController::class, 'index'])->name('docs');

// Hospital Waiting Room TV Public Screen
Route::get('/display',      [DisplayController::class, 'index'])->name('display');
Route::get('/display/data', [DisplayController::class, 'data'])->name('display.data');

// ============================================================
// 2. Authentication Routes
// ============================================================

Route::middleware('guest')->group(function () {
    Route::get('/login',     [LoginController::class, 'create'])->name('login');
    Route::post('/login',    [LoginController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/register',  [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:5,1');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// ============================================================
// 3. Patient Routes
// ============================================================

Route::prefix('patient')
    ->middleware(['auth', 'role:patient'])
    ->name('patient.')
    ->group(function () {
        // Dashboard & Notifications
        Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
        Route::get('/history',   [PatientDashboardController::class, 'history'])->name('history');
        Route::post('/notifications/read', [PatientDashboardController::class, 'markNotificationsRead'])->name('notifications.read');

        // Queue operations (Rate limited to prevent queue flooding)
        Route::get('/queue',                           [PatientQueueController::class, 'index'])->name('queue.index');
        Route::get('/queue/service/{service}',         [PatientQueueController::class, 'show'])->name('queue.show');
        Route::post('/queue',                          [PatientQueueController::class, 'store'])->middleware('throttle:30,1')->name('queue.store');
        Route::get('/queue/{queueEntry}/status',       [PatientQueueController::class, 'status'])->name('queue.status');
        Route::post('/queue/{queueEntry}/cancel',      [PatientQueueController::class, 'cancel'])->name('queue.cancel');

        // JSON polling endpoint for live updates
        Route::get('/queue/{queueEntry}/status.json',  [PatientQueueController::class, 'statusJson'])->name('queue.status.json');

        // Advance Clinic Appointments
        Route::get('/appointments',                    [PatientAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/create',             [PatientAppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments',                   [PatientAppointmentController::class, 'store'])->name('appointments.store');
        Route::post('/appointments/{appointment}/cancel', [PatientAppointmentController::class, 'cancel'])->name('appointments.cancel');
    });

// ============================================================
// 4. Staff Routes
// ============================================================

Route::prefix('staff')
    ->middleware(['auth', 'role:staff,admin'])
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard',                       [StaffQueueController::class, 'dashboard'])->name('dashboard');
        Route::post('/queue/call-next',                [StaffQueueController::class, 'callNext'])->name('queue.call-next');
        Route::post('/queue/{queueEntry}/start',       [StaffQueueController::class, 'startService'])->name('queue.start');
        Route::post('/queue/{queueEntry}/complete',    [StaffQueueController::class, 'complete'])->name('queue.complete');
        Route::post('/queue/{queueEntry}/skip',        [StaffQueueController::class, 'skip'])->name('queue.skip');
        Route::post('/queue/{queueEntry}/recall',      [StaffQueueController::class, 'recall'])->name('queue.recall');

        // Emergency Triage & Bed Allocation
        Route::post('/queue/{queueEntry}/triage',       [StaffTriageController::class, 'updateTriage'])->name('queue.triage');
        Route::post('/queue/{queueEntry}/allocate-bed', [StaffTriageController::class, 'allocateBed'])->name('queue.allocate-bed');
        Route::post('/queue/{queueEntry}/release-bed',  [StaffTriageController::class, 'releaseBed'])->name('queue.release-bed');
        Route::get('/beds',                            [StaffTriageController::class, 'bedsIndex'])->name('beds.index');

        // Clinic Appointments Schedule & Check-In Desk
        Route::get('/appointments',                    [StaffAppointmentController::class, 'index'])->name('appointments.index');
        Route::post('/appointments/{appointment}/check-in', [StaffAppointmentController::class, 'checkIn'])->name('appointments.check-in');

        // Clinical Referrals, Lab Investigation Transfer Loops & Patient Discharge
        Route::post('/referral/{queueEntry}/order-lab',    [ClinicalReferralController::class, 'orderLabAndTransfer'])->name('referral.order-lab');
        Route::post('/referral/{queueEntry}/complete-lab', [ClinicalReferralController::class, 'completeLabAndReturn'])->name('referral.complete-lab');
        Route::post('/referral/{queueEntry}/discharge',    [ClinicalReferralController::class, 'discharge'])->name('referral.discharge');

        // Emergency Trauma & Unconscious Patient Rapid Admission Protocol
        Route::get('/emergency',                                     [EmergencyIntakeController::class, 'index'])->name('emergency.index');
        Route::post('/emergency/unconscious-intake',                  [EmergencyIntakeController::class, 'unconsciousIntake'])->name('emergency.unconscious-intake');
        Route::post('/emergency/{queueEntry}/link-permanent-id',     [EmergencyIntakeController::class, 'linkPermanentId'])->name('emergency.link-permanent-id');

        // Doctor On-Call Duty Rostering & Paging
        Route::get('/on-call',                         [OnCallController::class, 'index'])->name('oncall.index');
        Route::post('/on-call/{doctor}/toggle',        [OnCallController::class, 'toggleOnCall'])->name('oncall.toggle');
        Route::post('/on-call/{doctor}/page',          [OnCallController::class, 'pageDoctor'])->name('oncall.page');

        // JSON live status polling for staff dashboard
        Route::get('/queue/live-status',               [StaffQueueController::class, 'liveStatus'])->name('queue.live-status');
    });

// ============================================================
// 5. Admin Routes
// ============================================================

Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Service catalogue management
        Route::resource('services', ServiceController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::post('/services/{service}/toggle', [ServiceController::class, 'toggle'])->name('services.toggle');

        // User management & administrative password resets
        Route::get('/users',                              [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create',                       [UserController::class, 'create'])->name('users.create');
        Route::post('/users',                             [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit',                  [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',                       [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle',               [UserController::class, 'toggle'])->name('users.toggle');
        Route::post('/users/{user}/role',                 [UserController::class, 'updateRole'])->name('users.role');
        Route::post('/users/{user}/reset-password',       [UserController::class, 'resetPassword'])->name('users.reset-password');

        // Clinic & System Settings
        Route::get('/settings',                           [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings',                           [SettingController::class, 'update'])->name('settings.update');

        // Clinical Reports, CSV Export, Email Dispatch, and Forensic Investigation
        Route::get('/reports',                             [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export',                      [ReportController::class, 'exportCsv'])->name('reports.export');
        Route::post('/reports/email',                      [ReportController::class, 'emailReport'])->name('reports.email');
        Route::get('/reports/investigate/{queueEntry}',    [ReportController::class, 'investigate'])->name('reports.investigate');

        // Audit log
        Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
    });
