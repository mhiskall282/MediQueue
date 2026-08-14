---
name: database-design
description: Relational database schema design, migrations, indexing, and pessimistic transaction locking for MediQueue.
---

# Database Design Skill Guide

Use this skill when designing tables, migrations, database indexes, or transactional queries for **MediQueue**.

---

## 1. Core Migration Code Template

```php
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
        Schema::create('queue_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->index();
            $table->unsignedInteger('sequence_number');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('restrict');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->enum('status', ['WAITING', 'CALLED', 'SERVING', 'COMPLETED', 'CANCELLED', 'NO-SHOW'])
                  ->default('WAITING')
                  ->index();

            $table->enum('priority', ['NORMAL', 'HIGH', 'EMERGENCY'])
                  ->default('NORMAL')
                  ->index();

            $table->timestamp('called_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Composite index for fast active queue queries
            $table->index(['service_id', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_tickets');
    }
};
```
