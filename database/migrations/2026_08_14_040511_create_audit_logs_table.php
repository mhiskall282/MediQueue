<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the immutable audit_logs table.
     * Records important administrative and operational actions for accountability.
     * Note: No update/delete operations should be performed on this table.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Actor (nullable for system-generated events)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('The user who performed the action (null for system events)');

            // Action descriptor
            $table->string('action')->index()->comment('Action identifier, e.g. service.created, queue.called, queue.completed');

            // Entity reference (polymorphic-style without full polymorphism for simplicity)
            $table->string('entity_type')->nullable()->comment('The type of entity affected, e.g. Service, QueueEntry');
            $table->unsignedBigInteger('entity_id')->nullable()->comment('The ID of the affected entity');

            // Contextual data
            $table->json('metadata')->nullable()->comment('Structured context data. Never contains passwords or PHI.');
            $table->string('ip_address', 45)->nullable()->comment('IP address of the actor');

            // Audit logs use created_at only (immutable)
            $table->timestamp('created_at')->useCurrent();

            $table->index(['entity_type', 'entity_id'], 'idx_entity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
