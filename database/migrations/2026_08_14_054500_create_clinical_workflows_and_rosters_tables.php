<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add Hospital MRN and On-Call Medical Fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('hospital_id', 50)->nullable()->unique()->after('id')->comment('Medical Record Number (MRN) e.g. MRN-2026-00123');
            $table->string('specialization', 100)->nullable()->after('role')->comment('e.g. Emergency Medicine, Cardiology, General Surgery');
            $table->boolean('is_on_call')->default(false)->after('specialization')->index()->comment('Whether doctor is currently active on-call');
            $table->string('on_call_shift', 50)->nullable()->after('is_on_call')->comment('e.g. DAY_SHIFT, NIGHT_EMERGENCY, 24H_TRAUMA');
            $table->string('emergency_contact_phone', 30)->nullable()->after('on_call_shift');
        });

        // 2. Add Clinical Referral & Emergency Unconscious fields to queue_entries table
        Schema::table('queue_entries', function (Blueprint $table) {
            $table->string('hospital_id', 50)->nullable()->after('patient_id')->index()->comment('MRN / Emergency Patient Tracking ID');
            $table->boolean('is_emergency_unconscious')->default(false)->after('triage_notes')->index()->comment('Emergency Unconscious / John Doe patient flag');
            $table->foreignId('referring_staff_id')->nullable()->after('served_by')->constrained('users')->onDelete('set null')->comment('Doctor who initiated lab referral');
            $table->string('clinical_workflow_stage', 50)->default('INITIAL_TRIAGE')->after('status')->index()->comment('INITIAL_TRIAGE, IN_CONSULTATION, SENT_TO_LAB, LAB_COMPLETED, RETURNED_FOR_REVIEW, DISCHARGED');
            $table->text('clinical_notes')->nullable()->after('triage_notes')->comment('Doctor consultation notes, diagnosis, prescription');
            $table->text('lab_orders')->nullable()->after('clinical_notes')->comment('Lab tests ordered e.g. Full Blood Count, X-Ray Chest, ECG');
            $table->text('lab_results')->nullable()->after('lab_orders')->comment('Laboratory findings returned');
        });

        // 3. Create Doctor On-Call Duty Roster table
        Schema::create('doctor_rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->date('duty_date')->index();
            $table->string('shift_type', 50)->comment('DAY, NIGHT, ON_CALL_TRAUMA, ICU_COVER');
            $table->enum('status', ['SCHEDULED', 'ACTIVE', 'COMPLETED', 'SWAPPED'])->default('SCHEDULED')->index();
            $table->text('duty_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_rosters');

        Schema::table('queue_entries', function (Blueprint $table) {
            $table->dropForeign(['referring_staff_id']);
            $table->dropColumn([
                'hospital_id',
                'is_emergency_unconscious',
                'referring_staff_id',
                'clinical_workflow_stage',
                'clinical_notes',
                'lab_orders',
                'lab_results'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'hospital_id',
                'specialization',
                'is_on_call',
                'on_call_shift',
                'emergency_contact_phone'
            ]);
        });
    }
};
