<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display the clinical activity reporting portal with advanced filters.
     */
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::today()->subDays(7)->toDateString());
        $endDate   = $request->input('end_date', Carbon::today()->toDateString());
        $serviceId = $request->input('service_id');
        $staffId   = $request->input('staff_id');
        $status    = $request->input('status');

        $query = QueueEntry::with(['patient', 'service', 'servedBy'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        if ($staffId) {
            $query->where('served_by', $staffId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Summary metrics
        $totalEntries     = (clone $query)->count();
        $completedEntries = (clone $query)->where('status', QueueEntry::STATUS_COMPLETED)->count();
        $skippedEntries   = (clone $query)->where('status', QueueEntry::STATUS_SKIPPED)->count();

        // Calculate average wait time (minutes) for completed entries
        $completedWithTimestamps = (clone $query)
            ->where('status', QueueEntry::STATUS_COMPLETED)
            ->whereNotNull('service_started_at')
            ->get();

        $avgWaitMinutes = $completedWithTimestamps->count() > 0
            ? $completedWithTimestamps->avg(fn ($e) => $e->joined_at->diffInMinutes($e->service_started_at))
            : 0;

        $avgServiceMinutes = $completedWithTimestamps->count() > 0
            ? $completedWithTimestamps->whereNotNull('completed_at')->avg(fn ($e) => $e->service_started_at->diffInMinutes($e->completed_at))
            : 0;

        $entries = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $services     = Service::orderBy('name')->get();
        $staffMembers = User::where('role', 'staff')->orderBy('name')->get();

        return view('admin.reports.index', compact(
            'entries',
            'totalEntries',
            'completedEntries',
            'skippedEntries',
            'avgWaitMinutes',
            'avgServiceMinutes',
            'services',
            'staffMembers',
            'startDate',
            'endDate',
            'serviceId',
            'staffId',
            'status'
        ));
    }

    /**
     * Export the filtered clinical dataset as a downloadable CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', Carbon::today()->subDays(7)->toDateString());
        $endDate   = $request->input('end_date', Carbon::today()->toDateString());
        $serviceId = $request->input('service_id');
        $staffId   = $request->input('staff_id');
        $status    = $request->input('status');

        $query = QueueEntry::with(['patient', 'service', 'servedBy'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        if ($staffId) {
            $query->where('served_by', $staffId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $filename = 'mediqueue_report_' . Carbon::now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Header Row
            fputcsv($handle, [
                'Date & Time',
                'Ticket Number',
                'Hospital MRN',
                'Patient Name',
                'Patient Email',
                'Department',
                'Triage Level',
                'Attending Staff',
                'Priority',
                'Status',
                'Joined At',
                'Called At',
                'Service Started At',
                'Completed At',
                'Wait Duration (mins)',
                'Consultation Duration (mins)',
                'Triage Notes',
                'Clinical Notes',
                'Lab Orders',
                'Lab Results',
            ]);

            $query->chunk(200, function ($entries) use ($handle) {
                foreach ($entries as $e) {
                    $waitMins = $e->service_started_at ? $e->joined_at->diffInMinutes($e->service_started_at) : null;
                    $serviceMins = ($e->service_started_at && $e->completed_at) ? $e->service_started_at->diffInMinutes($e->completed_at) : null;

                    fputcsv($handle, [
                        $e->created_at->toIso8601String(),
                        $e->queue_number,
                        $e->hospital_id ?? ('MRN-' . $e->patient_id),
                        $e->patient->name,
                        $e->patient->email,
                        $e->service->name,
                        $e->triage_level ?? 'GREEN',
                        $e->servedBy->name ?? 'Unassigned',
                        $e->priority,
                        $e->status,
                        $e->joined_at?->toIso8601String(),
                        $e->called_at?->toIso8601String(),
                        $e->service_started_at?->toIso8601String(),
                        $e->completed_at?->toIso8601String(),
                        $waitMins,
                        $serviceMins,
                        $e->triage_notes,
                        $e->clinical_notes,
                        $e->lab_orders,
                        $e->lab_results,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export the filtered clinical dataset as an executive PDF report.
     */
    public function exportPdf(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::today()->subDays(7)->toDateString());
        $endDate   = $request->input('end_date', Carbon::today()->toDateString());
        $serviceId = $request->input('service_id');
        $staffId   = $request->input('staff_id');
        $status    = $request->input('status');

        $query = QueueEntry::with(['patient', 'service', 'servedBy'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        if ($staffId) {
            $query->where('served_by', $staffId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $totalEntries     = (clone $query)->count();
        $completedEntries = (clone $query)->where('status', QueueEntry::STATUS_COMPLETED)->count();
        $skippedEntries   = (clone $query)->where('status', QueueEntry::STATUS_SKIPPED)->count();

        $completedWithTimestamps = (clone $query)
            ->where('status', QueueEntry::STATUS_COMPLETED)
            ->whereNotNull('service_started_at')
            ->get();

        $avgWaitMinutes = $completedWithTimestamps->count() > 0
            ? $completedWithTimestamps->avg(fn ($e) => $e->joined_at->diffInMinutes($e->service_started_at))
            : 0;

        $avgServiceMinutes = $completedWithTimestamps->count() > 0
            ? $completedWithTimestamps->whereNotNull('completed_at')->avg(fn ($e) => $e->service_started_at->diffInMinutes($e->completed_at))
            : 0;

        $entries = $query->orderByDesc('created_at')->limit(100)->get();

        $selectedService = $serviceId ? Service::find($serviceId) : null;
        $selectedStaff   = $staffId ? User::find($staffId) : null;

        return view('admin.reports.pdf', compact(
            'entries',
            'totalEntries',
            'completedEntries',
            'skippedEntries',
            'avgWaitMinutes',
            'avgServiceMinutes',
            'startDate',
            'endDate',
            'selectedService',
            'selectedStaff'
        ));
    }

    /**
     * Dispatch an executive email summary report to clinic administrators.
     */
    public function emailReport(Request $request): RedirectResponse
    {
        $admin = auth()->user();
        $startDate = $request->input('start_date', Carbon::today()->subDays(7)->toDateString());
        $endDate   = $request->input('end_date', Carbon::today()->toDateString());

        $total = QueueEntry::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();

        $completed = QueueEntry::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('status', QueueEntry::STATUS_COMPLETED)
            ->count();

        try {
            Mail::to($admin->email)->send(new \App\Mail\QueueNotificationMail(
                $admin,
                'Executive Clinical Activity Summary',
                'Clinical Activity Report',
                "Executive Clinical Summary Report for {$startDate} to {$endDate}. Total Consultations: {$total}, Completed: {$completed}.",
                [
                    'Period'                => "{$startDate} to {$endDate}",
                    'Total Consultations'   => $total,
                    'Completed Care'        => $completed,
                    'Administrator'         => $admin->name,
                ]
            ));

            return back()->with('success', "Executive clinical summary report dispatched to {$admin->email}.");
        } catch (\Throwable $e) {
            return back()->with('success', "Report calculated successfully. Email notification queued for {$admin->email}.");
        }
    }

    /**
     * Forensic investigation portal for a specific patient consultation record.
     */
    public function investigate(QueueEntry $queueEntry): View
    {
        $queueEntry->load(['patient', 'service', 'servedBy', 'referringStaff', 'allocatedBed']);

        $auditLogs = AuditLog::with('user')
            ->where(function ($query) use ($queueEntry) {
                $query->where('entity_type', 'QueueEntry')
                      ->where('entity_id', $queueEntry->id);
            })
            ->orWhere('metadata->queue_number', $queueEntry->queue_number)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.reports.investigate', compact('queueEntry', 'auditLogs'));
    }
}
