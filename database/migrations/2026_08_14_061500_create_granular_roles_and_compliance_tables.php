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
        // 1. Expand User Roles, Staff Approval, and Licensing
        Schema::table('users', function (Blueprint $table) {
            $table->string('medical_license_number', 100)->nullable()->after('hospital_id')->comment('Practicing Medical License / Registration No.');
            $table->boolean('is_approved')->default(true)->after('is_active')->index()->comment('Staff account approval status by Administrator');
            $table->timestamp('approved_at')->nullable()->after('is_approved');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->onDelete('set null');
        });

        // 2. Inter-Staff Clinical Messaging & Consultation Requests table
        Schema::create('clinical_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recipient_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('queue_entry_id')->nullable()->constrained('queue_entries')->onDelete('set null');
            $table->string('subject', 150);
            $table->text('message');
            $table->enum('urgency', ['ROUTINE', 'URGENT', 'STAT_EMERGENCY'])->default('ROUTINE')->index();
            $table->boolean('is_read')->default(false)->index();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // 3. Security Incident & HIPAA / ISO-27001 Compliance Alert Logs
        Schema::create('security_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('event_type', 100)->index()->comment('FAILED_LOGIN_SPIKE, PRIVILEGE_ESCALATION, UNAPPROVED_ACCESS_ATTEMPT, SUSPICIOUS_IP');
            $table->enum('severity', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])->default('MEDIUM')->index();
            $table->text('description');
            $table->json('context_data')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_resolved')->default(false)->index();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_alerts');
        Schema::dropIfExists('clinical_messages');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'medical_license_number',
                'is_approved',
                'approved_at',
                'approved_by',
            ]);
        });
    }
};
