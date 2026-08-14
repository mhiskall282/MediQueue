<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the core queue_entries table.
     * This is the central transactional entity of MediQueue.
     * Each record represents a patient's position in a service queue.
     */
    public function up(): void
    {
        Schema::create('queue_entries', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('patient_id')
                  ->constrained('users')
                  ->onDelete('restrict')
                  ->comment('The patient this queue entry belongs to');

            $table->foreignId('service_id')
                  ->constrained('services')
                  ->onDelete('restrict')
                  ->comment('The clinic service this entry is for');

            $table->foreignId('served_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('The staff member currently serving or who served this patient');

            // Queue Number
            $table->string('queue_number', 20)->comment('Human-readable queue number, e.g. GC-005');
            $table->unsignedInteger('sequence_number')->comment('Numeric sequence within service for the day, e.g. 5');

            // State & Priority
            $table->enum('status', ['WAITING', 'CALLED', 'IN_SERVICE', 'COMPLETED', 'CANCELLED', 'SKIPPED'])
                  ->default('WAITING')
                  ->index();

            $table->enum('priority', ['NORMAL', 'URGENT'])->default('NORMAL')->index();

            // Lifecycle timestamps (nullable because they depend on state progression)
            $table->timestamp('joined_at')->useCurrent()->comment('When the patient joined the queue');
            $table->timestamp('called_at')->nullable()->comment('When staff called this patient');
            $table->timestamp('service_started_at')->nullable()->comment('When service actually began');
            $table->timestamp('completed_at')->nullable()->comment('When service was completed');
            $table->timestamp('cancelled_at')->nullable()->comment('When the entry was cancelled');
            $table->timestamp('skipped_at')->nullable()->comment('When the patient was skipped');

            $table->timestamps();

            // Indexes for performance
            $table->index(['service_id', 'status', 'created_at'], 'idx_service_status_date');
            $table->index(['patient_id', 'status'], 'idx_patient_status');
            $table->index(['service_id', 'sequence_number', 'created_at'], 'idx_service_sequence_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_entries');
    }
};
