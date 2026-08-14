<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Mail\QueueNotificationMail;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Display patient's appointments.
     */
    public function index(Request $request): View
    {
        $patient = auth()->user();

        $upcomingAppointments = Appointment::with(['service', 'doctor', 'queueEntry'])
            ->where('patient_id', $patient->id)
            ->where('status', Appointment::STATUS_BOOKED)
            ->orderBy('appointment_date')
            ->orderBy('time_slot')
            ->get();

        $pastAppointments = Appointment::with(['service', 'doctor', 'queueEntry'])
            ->where('patient_id', $patient->id)
            ->where('status', '!=', Appointment::STATUS_BOOKED)
            ->orderByDesc('appointment_date')
            ->paginate(10);

        return view('patient.appointments.index', compact('upcomingAppointments', 'pastAppointments'));
    }

    /**
     * Show appointment booking form.
     */
    public function create(): View
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $doctors  = User::where('role', 'staff')->orderBy('name')->get();

        return view('patient.appointments.create', compact('services', 'doctors'));
    }

    /**
     * Store newly booked appointment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_id'       => ['required', 'exists:services,id'],
            'doctor_id'        => ['nullable', 'exists:users,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'time_slot'        => ['required', 'string', 'max:20'],
            'symptoms_notes'   => ['nullable', 'string', 'max:1000'],
        ]);

        $patient = auth()->user();

        // Check for double booking for this patient at the same date and slot
        $existing = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', $validated['appointment_date'])
            ->where('time_slot', $validated['time_slot'])
            ->where('status', Appointment::STATUS_BOOKED)
            ->first();

        if ($existing) {
            return back()->withInput()->with('error', 'You already have an active appointment booked for this date and time slot.');
        }

        $appointment = Appointment::create([
            'patient_id'       => $patient->id,
            'service_id'       => $validated['service_id'],
            'doctor_id'        => $validated['doctor_id'] ?? null,
            'appointment_date' => $validated['appointment_date'],
            'time_slot'        => $validated['time_slot'],
            'symptoms_notes'   => $validated['symptoms_notes'] ?? null,
            'status'           => Appointment::STATUS_BOOKED,
        ]);

        $service = Service::find($validated['service_id']);

        // Audit Trail
        AuditLog::create([
            'user_id'     => $patient->id,
            'action'      => 'appointment.booked',
            'entity_type' => 'Appointment',
            'entity_id'   => $appointment->id,
            'metadata'    => [
                'service' => $service->name,
                'date'    => $appointment->appointment_date->toDateString(),
                'slot'    => $appointment->time_slot,
            ],
            'ip_address'  => $request->ip(),
        ]);

        // In-App Notification
        Notification::create([
            'user_id' => $patient->id,
            'type'    => 'appointment_confirmed',
            'title'   => 'Appointment Confirmed',
            'body'    => "Your appointment for {$service->name} is confirmed on {$appointment->appointment_date->format('M d, Y')} at {$appointment->time_slot}.",
            'data'    => ['appointment_id' => $appointment->id],
        ]);

        // Send Email Confirmation
        try {
            Mail::to($patient->email)->send(new QueueNotificationMail(
                $patient,
                'Appointment Confirmed — ' . $service->name,
                'Appointment Scheduled',
                "Your appointment has been successfully scheduled. When you arrive at the clinic on {$appointment->appointment_date->format('M d, Y')}, you can check-in at reception to receive your queue ticket.",
                [
                    'Department' => $service->name,
                    'Date'       => $appointment->appointment_date->format('M d, Y'),
                    'Time Slot'  => $appointment->time_slot,
                ]
            ));
        } catch (\Throwable $e) {
            // Silently continue if mailer is log driver
        }

        return redirect()->route('patient.appointments.index')->with('success', 'Appointment successfully scheduled!');
    }

    /**
     * Cancel an upcoming appointment.
     */
    public function cancel(Appointment $appointment): RedirectResponse
    {
        if ($appointment->patient_id !== auth()->id()) {
            abort(403);
        }

        if ($appointment->status !== Appointment::STATUS_BOOKED) {
            return back()->with('error', 'Only upcoming booked appointments can be cancelled.');
        }

        $appointment->update(['status' => Appointment::STATUS_CANCELLED]);

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'appointment.cancelled',
            'entity_type' => 'Appointment',
            'entity_id'   => $appointment->id,
            'metadata'    => ['date' => $appointment->appointment_date->toDateString()],
            'ip_address'  => request()->ip(),
        ]);

        return back()->with('success', 'Appointment cancelled.');
    }
}
