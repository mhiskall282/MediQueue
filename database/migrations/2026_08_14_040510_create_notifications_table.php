<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the in-app notifications table.
     * Architected to support future email/SMS delivery via a delivery_channel column.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('type')->comment('Notification type: queue.joined, queue.called, queue.serving, queue.completed');
            $table->string('title');
            $table->text('body');
            $table->json('data')->nullable()->comment('Structured data payload, e.g. {"queue_number": "GC-005", "service_name": "General Consultation"}');
            $table->timestamp('read_at')->nullable()->index();

            $table->timestamps();

            $table->index(['user_id', 'read_at'], 'idx_user_unread');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
