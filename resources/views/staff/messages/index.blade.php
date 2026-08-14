<x-layouts.app title="Inter-Staff Clinical Messaging & Consult Requests">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">Inter-Staff Clinical Communications</h1>
                    <span class="badge bg-indigo-100 text-indigo-800 font-bold text-xs">
                        ENCRYPTED & AUDITED
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Multi-disciplinary medical messaging, STAT emergency consult requests, lab notes, and pharmacy verifications.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Left: Compose New Clinical Note / Request (5 cols) --}}
            <div class="lg:col-span-5">
                <div class="card p-6 shadow-sm sticky top-24">
                    <h2 class="text-sm font-black text-slate-900 mb-1 flex items-center gap-2">
                        <span>💬</span> Dispatch Clinical Note / Consult
                    </h2>
                    <p class="text-xs text-slate-500 mb-4">Send a direct message or STAT request to an attending colleague.</p>

                    <form method="POST" action="{{ route('staff.messages.store') }}" class="space-y-4">
                        @csrf

                        {{-- Recipient Clinician --}}
                        <div>
                            <label class="form-label text-xs">Target Clinician / Colleague</label>
                            <select name="recipient_id" class="form-input text-xs">
                                <option value="">📢 Broadcast to All On-Duty Clinical Staff</option>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}">
                                        {{ $staff->name }} ({{ $staff->role_title }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Urgency Level --}}
                        <div>
                            <label class="form-label text-xs">Clinical Urgency / Priority</label>
                            <select name="urgency" class="form-input text-xs font-bold" required>
                                <option value="ROUTINE">🟢 Routine Consult / Care Note</option>
                                <option value="URGENT">🟠 Urgent Consult (15 min response)</option>
                                <option value="STAT_EMERGENCY">🔴 STAT Emergency / Trauma Alarm</option>
                            </select>
                        </div>

                        {{-- Optional Queue Ticket Link --}}
                        <div>
                            <label class="form-label text-xs">Attach Patient Ticket <span class="text-slate-400 font-normal">(Optional)</span></label>
                            <select name="queue_entry_id" class="form-input text-xs font-mono">
                                <option value="">— None / General Operational Message —</option>
                                @foreach($activeTickets as $ticket)
                                    <option value="{{ $ticket->id }}">
                                        {{ $ticket->queue_number }} ({{ $ticket->service->name }}) — {{ $ticket->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Subject --}}
                        <div>
                            <label class="form-label text-xs">Subject / Clinical Intent</label>
                            <input
                                type="text"
                                name="subject"
                                required
                                class="form-input text-xs"
                                placeholder="e.g. STAT Blood Culture Request or Medication Interaction Query"
                            >
                        </div>

                        {{-- Message Body --}}
                        <div>
                            <label class="form-label text-xs">Clinical Instructions & Notes</label>
                            <textarea
                                name="message"
                                rows="4"
                                required
                                class="form-input text-xs"
                                placeholder="Input medical specifics, diagnostic observations, or clinical requests..."
                            ></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-full text-xs font-bold justify-center py-2.5">
                            <span>🚀</span> Dispatch Clinical Communication
                        </button>
                    </form>
                </div>
            </div>

            {{-- Right: Clinical Communications Inbox (7 cols) --}}
            <div class="lg:col-span-7 space-y-4">
                <div class="card overflow-hidden">
                    <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">
                            Clinical Inbox ({{ $receivedMessages->total() }} communications)
                        </span>
                        <span class="text-[11px] text-slate-400">Authenticated Clinicians</span>
                    </div>

                    @if($receivedMessages->isEmpty())
                        <div class="p-12 text-center text-slate-500 text-sm">
                            <span class="text-3xl block mb-2">📬</span>
                            No clinical messages in your inbox.
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach($receivedMessages as $msg)
                                <div class="p-5 {{ !$msg->is_read ? 'bg-indigo-50/40 border-l-4 border-indigo-600' : 'bg-white' }} space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="badge {{ $msg->urgency_badge_class }} text-[9px] font-black uppercase">
                                                {{ $msg->urgency }}
                                            </span>
                                            <span class="text-xs font-bold text-slate-900">
                                                From: {{ $msg->sender->name }} ({{ $msg->sender->role_title }})
                                            </span>
                                        </div>
                                        <span class="text-[11px] text-slate-400">
                                            {{ $msg->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    <h3 class="text-sm font-black text-slate-900">{{ $msg->subject }}</h3>
                                    
                                    @if($msg->queueEntry)
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-[11px] text-slate-700 font-mono">
                                            <span>🎫 Attached Ticket:</span>
                                            <strong>{{ $msg->queueEntry->queue_number }}</strong> ({{ $msg->queueEntry->service->name }}) &bull; Patient: {{ $msg->queueEntry->user->name }}
                                        </div>
                                    @endif

                                    <p class="text-xs text-slate-700 leading-relaxed bg-white/80 p-3 rounded-xl border border-slate-100">
                                        {{ $msg->message }}
                                    </p>

                                    <div class="flex items-center justify-between pt-1 text-[11px]">
                                        <span class="text-slate-400">
                                            Recipient: {{ $msg->recipient ? $msg->recipient->name : 'All On-Duty Staff' }}
                                        </span>
                                        @if(!$msg->is_read)
                                            <form method="POST" action="{{ route('staff.messages.read', $msg) }}">
                                                @csrf
                                                <button type="submit" class="text-indigo-600 font-bold hover:underline">
                                                    Mark as Read &bull; Acknowledge
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-emerald-600 font-medium">✓ Acknowledged</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($receivedMessages->hasPages())
                            <div class="p-4 border-t border-slate-100">
                                {{ $receivedMessages->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
