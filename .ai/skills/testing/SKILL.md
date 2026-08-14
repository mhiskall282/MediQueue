---
name: testing
description: Pest / PHPUnit test suite creation, HTTP feature testing, database assertion, and edge-case verification for MediQueue.
---

# Testing Skill Guide

Use this skill when writing automated tests, running test commands, or verifying application functionality for **MediQueue**.

---

## 1. Feature Test Code Template (Pest PHP / PHPUnit)

```php
namespace Tests\Feature\Patient;

use Tests\TestCase;
use App\Models\Service;
use App\Models\QueueTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KioskRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_register_and_receive_unique_ticket()
    {
        $service = Service::factory()->create([
            'name' => 'General Consultation',
            'code' => 'GC',
            'is_active' => true,
        ]);

        $response = $this->post(route('kiosk.store'), [
            'service_id' => $service->id,
            'name' => 'John Doe',
            'phone' => '0123456789',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('queue_tickets', [
            'ticket_number' => 'GC-001',
            'status' => 'WAITING',
        ]);

        $this->assertDatabaseHas('patients', [
            'name' => 'John Doe',
            'phone' => '0123456789',
        ]);
    }

    public function test_prevents_registration_for_inactive_service()
    {
        $service = Service::factory()->create([
            'name' => 'Dental Surgery',
            'code' => 'DEN',
            'is_active' => false,
        ]);

        $response = $this->post(route('kiosk.store'), [
            'service_id' => $service->id,
            'name' => 'Jane Smith',
            'phone' => '0987654321',
        ]);

        $response->assertSessionHasErrors(['service_id']);
        $this->assertDatabaseCount('queue_tickets', 0);
    }
}
```
