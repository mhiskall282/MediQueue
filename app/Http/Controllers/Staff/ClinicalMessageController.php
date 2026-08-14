<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ClinicalMessage;
use App\Models\Notification;
use App\Models\QueueEntry;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClinicalMessageController extends Controller
{
    /**
     * Display the clinical staff consultation messaging inbox.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $receivedMessages = ClinicalMessage::with(['sender', 'recipient', 'queueEntry.service', 'queueEntry.patient'])
            ->where(function ($q) use ($user) {
                $q->where('recipient_id', $user->id)
                  ->orWhereNull('recipient_id'); // Broadcasts to all clinical staff
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        $sentMessages = ClinicalMessage::with(['recipient', 'queueEntry.service', 'queueEntry.patient'])
            ->where('sender_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $staffMembers = User::whereIn('role', [
            User::ROLE_DOCTOR,
            User::ROLE_NURSE,
            User::ROLE_PHARMACIST,
            User::ROLE_LAB_TECH,
            User::ROLE_STAFF,
            User::ROLE_ADMIN,
        ])->where('id', '!=', $user->id)->get();

        $activeTickets = QueueEntry::with(['service', 'patient'])
            ->whereIn('status', [QueueEntry::STATUS_WAITING, QueueEntry::STATUS_CALLED, QueueEntry::STATUS_IN_SERVICE])
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        return view('staff.messages.index', compact('receivedMessages', 'sentMessages', 'staffMembers', 'activeTickets'));
    }

    /**
     * Send an inter-staff clinical consult request or care note.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_id'   => ['nullable', 'exists:users,id'],
            'queue_entry_id' => ['nullable', 'exists:queue_entries,id'],
            'subject'        => ['required', 'string', 'max:150'],
            'message'        => ['required', 'string', 'max:2000'],
            'urgency'        => ['required', 'in:ROUTINE,URGENT,STAT_EMERGENCY'],
        ]);

        $message = ClinicalMessage::create([
            'sender_id'      => Auth::id(),
            'recipient_id'   => $validated['recipient_id'] ?? null,
            'queue_entry_id' => $validated['queue_entry_id'] ?? null,
            'subject'        => $validated['subject'],
            'message'        => $validated['message'],
            'urgency'        => $validated['urgency'],
            'is_read'        => false,
        ]);

        // Send In-App Notification to Recipient
        if ($message->recipient_id) {
            Notification::create([
                'user_id' => $message->recipient_id,
                'type'    => 'clinical.consult_request',
                'title'   => "[{$message->urgency}] Clinical Consult from Dr./Nurse ".Auth::user()->name,
                'body'    => $message->subject.': '.substr($message->message, 0, 100).'...',
            ]);
        }

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'clinical_message.sent',
            'entity_type' => 'ClinicalMessage',
            'entity_id'   => $message->id,
            'metadata'    => [
                'recipient_id' => $message->recipient_id,
                'urgency'      => $message->urgency,
                'subject'      => $message->subject,
            ],
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', "Clinical communication dispatched successfully (Priority: {$message->urgency}).");
    }

    /**
     * Mark a clinical note as read.
     */
    public function markAsRead(ClinicalMessage $message): RedirectResponse
    {
        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'Clinical communication marked as read.');
    }
}
