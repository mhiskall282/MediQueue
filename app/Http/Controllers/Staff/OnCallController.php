<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DoctorRoster;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnCallController extends Controller
{
    /**
     * Display live On-Call Doctor duty board and emergency roster.
     */
    public function index(Request $request): View
    {
        $doctors = User::where('role', 'staff')->orderBy('name')->get();

        $activeOnCall = User::where('role', 'staff')->where('is_on_call', true)->get();

        $todayRosters = DoctorRoster::with('doctor')
            ->whereDate('duty_date', Carbon::today())
            ->orderBy('shift_type')
            ->get();

        return view('staff.oncall.index', compact('doctors', 'activeOnCall', 'todayRosters'));
    }

    /**
     * Toggle a doctor's active on-call duty status.
     */
    public function toggleOnCall(Request $request, User $doctor): RedirectResponse
    {
        $validated = $request->validate([
            'is_on_call'     => ['required', 'boolean'],
            'on_call_shift'  => ['nullable', 'string'],
            'specialization' => ['nullable', 'string'],
        ]);

        $doctor->update([
            'is_on_call'     => $validated['is_on_call'],
            'on_call_shift'  => $validated['on_call_shift'] ?? $doctor->on_call_shift,
            'specialization' => $validated['specialization'] ?? $doctor->specialization,
        ]);

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'doctor.on_call_toggled',
            'entity_type' => 'User',
            'entity_id'   => $doctor->id,
            'metadata'    => [
                'doctor_name' => $doctor->name,
                'is_on_call'  => $doctor->is_on_call,
                'shift'       => $doctor->on_call_shift,
            ],
            'ip_address'  => $request->ip(),
        ]);

        $statusText = $doctor->is_on_call ? 'ACTIVE ON-CALL' : 'OFF DUTY';
        return back()->with('success', "Dr. {$doctor->name} status updated to {$statusText}.");
    }

    /**
     * Send an urgent emergency page/alert to an on-call clinician.
     */
    public function pageDoctor(Request $request, User $doctor): RedirectResponse
    {
        $validated = $request->validate([
            'urgency_reason' => ['required', 'string', 'max:500'],
            'location'       => ['nullable', 'string', 'max:100'],
        ]);

        Notification::create([
            'user_id' => $doctor->id,
            'type'    => 'urgent_doctor_page',
            'title'   => '🚨 EMERGENCY DOCTOR PAGE',
            'body'    => "Urgent attendance required at " . ($validated['location'] ?? 'Emergency Wing') . ": " . $validated['urgency_reason'],
            'data'    => ['paged_by' => auth()->user()->name, 'timestamp' => now()->toIso8601String()],
        ]);

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'doctor.paged',
            'entity_type' => 'User',
            'entity_id'   => $doctor->id,
            'metadata'    => [
                'doctor_name' => $doctor->name,
                'reason'      => $validated['urgency_reason'],
                'paged_by'    => auth()->user()->name,
            ],
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', "Emergency page dispatched to Dr. {$doctor->name}!");
    }
}
