<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Service;
use App\Services\QueueService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(
        protected QueueService $queueService
    ) {}

    /**
     * Display clinic appointment schedule for staff.
     */
    public function index(Request $request): View
    {
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $serviceId    = $request->input('service_id');

        $query = Appointment::with(['patient', 'service', 'doctor', 'queueEntry'])
            ->whereDate('appointment_date', $selectedDate);

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        $appointments = $query->orderBy('time_slot')->get();

        $totalToday     = $appointments->count();
        $checkedInCount = $appointments->where('status', Appointment::STATUS_CHECKED_IN)->count();
        $pendingCount   = $appointments->where('status', Appointment::STATUS_BOOKED)->count();

        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('staff.appointments.index', compact(
            'appointments',
            'selectedDate',
            'serviceId',
            'totalToday',
            'checkedInCount',
            'pendingCount',
            'services'
        ));
    }

    /**
     * Single-click Check-In: Converts booked appointment into an active queue ticket.
     */
    public function checkIn(Request $request, Appointment $appointment): RedirectResponse
    {
        if ($appointment->status !== Appointment::STATUS_BOOKED) {
            return back()->with('error', 'Appointment has already been checked-in or cancelled.');
        }

        try {
            // Atomic ticket creation via QueueService
            $queueEntry = $this->queueService->join(
                $appointment->patient,
                $appointment->service,
                'NORMAL'
            );

            // Update appointment status and link generated queue entry
            $appointment->update([
                'status'                   => Appointment::STATUS_CHECKED_IN,
                'generated_queue_entry_id' => $queueEntry->id,
            ]);

            AuditLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'appointment.checked_in',
                'entity_type' => 'Appointment',
                'entity_id'   => $appointment->id,
                'metadata'    => [
                    'queue_number' => $queueEntry->queue_number,
                    'patient_name' => $appointment->patient->name,
                    'staff_name'   => auth()->user()->name,
                ],
                'ip_address'  => $request->ip(),
            ]);

            return back()->with('success', "Patient checked-in! Active queue ticket {$queueEntry->queue_number} issued.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Send pre-consultation medical instructions or preparation requirements to the patient.
     */
    public function sendInstructions(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'doctor_instructions' => ['required', 'string', 'max:1000'],
        ]);

        $appointment->update([
            'doctor_instructions' => $validated['doctor_instructions'],
        ]);

        \App\Models\Notification::create([
            'user_id' => $appointment->patient_id,
            'type'    => 'appointment_instructions',
            'title'   => "Pre-Appointment Instructions for {$appointment->appointment_date->format('M d')}",
            'body'    => "Clinical Team Note: {$validated['doctor_instructions']}",
            'data'    => ['appointment_id' => $appointment->id],
        ]);

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'appointment.instructions_sent',
            'entity_type' => 'Appointment',
            'entity_id'   => $appointment->id,
            'metadata'    => [
                'patient_name' => $appointment->patient->name,
                'instructions' => $validated['doctor_instructions'],
                'staff_name'   => auth()->user()->name,
            ],
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', "Pre-appointment medical instructions dispatched to {$appointment->patient->name}.");
    }
}
