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
        // 1. Create Hospital Beds table
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->string('ward_name', 100);
            $table->string('bed_number', 50)->unique();
            $table->enum('bed_type', ['ICU', 'GENERAL', 'OBSERVATION', 'TRIAGE_BAY'])->default('GENERAL');
            $table->enum('status', ['AVAILABLE', 'OCCUPIED', 'MAINTENANCE', 'RESERVED'])->default('AVAILABLE')->index();
            $table->foreignId('current_patient_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Add Triage Level, Notes, and Bed Allocation to queue_entries table
        Schema::table('queue_entries', function (Blueprint $table) {
            $table->enum('triage_level', ['RED', 'ORANGE', 'YELLOW', 'GREEN', 'BLUE'])->default('GREEN')->after('priority')->index();
            $table->text('triage_notes')->nullable()->after('triage_level');
            $table->foreignId('allocated_bed_id')->nullable()->after('triage_notes')->constrained('beds')->onDelete('set null');
        });

        // 3. Create Appointments table
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('appointment_date')->index();
            $table->string('time_slot', 20)->comment('e.g. 09:00, 10:30');
            $table->text('symptoms_notes')->nullable();
            $table->enum('status', ['BOOKED', 'CHECKED_IN', 'CANCELLED', 'COMPLETED'])->default('BOOKED')->index();
            $table->foreignId('generated_queue_entry_id')->nullable()->constrained('queue_entries')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
        
        Schema::table('queue_entries', function (Blueprint $table) {
            $table->dropForeign(['allocated_bed_id']);
            $table->dropColumn(['allocated_bed_id', 'triage_notes', 'triage_level']);
        });

        Schema::dropIfExists('beds');
    }
};
