---
name: laravel-development
description: Practical Laravel development workflow, Eloquent modeling, Form Requests, Services, and Blade integration for MediQueue.
---

# Laravel Development Skill Guide

Use this skill when developing or modifying backend components, controllers, models, migrations, and services for **MediQueue**.

---

## 1. Step-by-Step Feature Implementation Protocol

1. **Migration Creation**:
   - `php artisan make:migration create_queue_tickets_table`
   - Define exact column types, default values, and foreign keys.

2. **Model Definition**:
   - `php artisan make:model QueueTicket`
   - Set `$fillable`, attribute casting (`casts`), and relationship methods.

3. **Form Request**:
   - `php artisan make:request StoreQueueTicketRequest`
   - Implement `rules()` and user friendly `messages()`.

4. **Service Class**:
   - Create `app/Services/QueueTicketService.php`.
   - Implement business actions inside DB transactions.

5. **Controller Implementation**:
   - `php artisan make:controller KioskController`
   - Inject service class into controller constructor or method.

6. **Automated Test**:
   - `php artisan make:test KioskRegistrationTest`
   - Execute test using Pest / PHPUnit.

---

## 2. Service Pattern Implementation Example

```php
namespace App\Services;

use App\Models\QueueTicket;
use App\Models\Service;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Exception;

class QueueTicketService
{
    /**
     * Issue an atomic queue ticket for a patient.
     */
    public function issueTicket(array $patientData, int $serviceId): QueueTicket
    {
        return DB::transaction(function () use ($patientData, $serviceId) {
            // Find or create patient record
            $patient = Patient::firstOrCreate(
                ['phone' => $patientData['phone']],
                [
                    'name' => $patientData['name'],
                    'national_id' => $patientData['national_id'] ?? null,
                    'patient_code' => 'P-' . strtoupper(uniqid())
                ]
            );

            // Prevent duplicate active ticket for same service today
            $existingTicket = QueueTicket::where('patient_id', $patient->id)
                ->where('service_id', $serviceId)
                ->whereIn('status', ['WAITING', 'CALLED', 'SERVING'])
                ->whereDate('created_at', today())
                ->first();

            if ($existingTicket) {
                return $existingTicket;
            }

            // Lock service for sequence generation
            $service = Service::where('id', $serviceId)->lockForUpdate()->firstOrFail();

            $lastSequence = QueueTicket::where('service_id', $serviceId)
                ->whereDate('created_at', today())
                ->max('sequence_number') ?? 0;

            $nextSequence = $lastSequence + 1;
            $ticketNumber = sprintf('%s-%03d', $service->code, $nextSequence);

            return QueueTicket::create([
                'ticket_number' => $ticketNumber,
                'sequence_number' => $nextSequence,
                'patient_id' => $patient->id,
                'service_id' => $serviceId,
                'status' => 'WAITING',
                'priority' => $patientData['priority'] ?? 'NORMAL',
            ]);
        });
    }
}
```
