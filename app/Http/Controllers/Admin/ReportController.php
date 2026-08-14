<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\QueueNotificationMail;
use App\Models\AuditLog;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display reporting dashboard with analytics and filters.
     */
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', Carbon::today()->toDateString());
        $serviceId = $request->input('service_id');
        $staffId   = $request->input('staff_id');
        $status    = $request->input('status');

        $query = QueueEntry::with(['patient', 'service', 'servedBy'])
            ->whereDate('joined_at', '>=', $startDate)
            ->whereDate('joined_at', '<=', $endDate);

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        if ($staffId) {
            $query->where('served_by', $staffId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $entries = (clone $query)->orderByDesc('joined_at')->paginate(20)->withQueryString();

        // Calculate KPIs on filtered dataset
        $totalEntries     = (clone $query)->count();
        $completedEntries = (clone $query)->where('status', QueueEntry::STATUS_COMPLETED)->count();
        $skippedEntries   = (clone $query)->where('status', QueueEntry::STATUS_SKIPPED)->count();
        $cancelledEntries = (clone $query)->where('status', QueueEntry::STATUS_CANCELLED)->count();

        // Calculate average wait time (joined_at to called_at) for called/completed tickets
        $avgWaitMinutes = (clone $query)->whereNotNull('called_at')
            ->get()
            ->avg(fn ($e) => $e->wait_duration_minutes) ?? 0;

        // Calculate average service duration (service_started_at to completed_at)
        $avgServiceMinutes = (clone $query)->where('status', QueueEntry::STATUS_COMPLETED)
            ->whereNotNull('service_started_at')
            ->whereNotNull('completed_at')
            ->get()
            ->avg(fn ($e) => $e->service_duration_minutes) ?? 0;

        $services = Service::orderBy('name')->get();
        $staffMembers = User::whereIn('role', ['staff', 'admin'])->orderBy('name')->get();

        return view('admin.reports.index', compact(
            'entries',
            'services',
            'staffMembers',
            'startDate',
            'endDate',
            'serviceId',
            'staffId',
            'status',
            'totalEntries',
            'completedEntries',
            'skippedEntries',
            'cancelledEntries',
            'avgWaitMinutes',
            'avgServiceMinutes'
        ));
    }

    /**
     * Export filtered queue records to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', Carbon::today()->toDateString());
        $serviceId = $request->input('service_id');
        $staffId   = $request->input('staff_id');
        $status    = $request->input('status');

        $query = QueueEntry::with(['patient', 'service', 'servedBy'])
            ->whereDate('joined_at', '>=', $startDate)
            ->whereDate('joined_at', '<=', $endDate);

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        if ($staffId) {
            $query->where('served_by', $staffId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $records = $query->orderByDesc('joined_at')->get();

        $filename = sprintf('mediqueue_report_%s_to_%s.csv', $startDate, $endDate);

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'report.exported_csv',
            'entity_type' => 'QueueEntry',
            'entity_id'   => null,
            'metadata'    => ['count' => $records->count(), 'start' => $startDate, 'end' => $endDate],
            'ip_address'  => $request->ip(),
        ]);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Ticket Number',
                'Patient ID',
                'Patient Name',
                'Patient Email',
                'Department / Service',
                'Attending Staff ID',
                'Attending Staff Name',
                'Attending Staff Role',
                'Status',
                'Priority',
                'Joined Timestamp',
                'Called Timestamp',
                'Service Started Timestamp',
                'Completed Timestamp',
                'Wait Duration (Minutes)',
                'Consultation Duration (Minutes)',
            ]);

            foreach ($records as $record) {
                fputcsv($file, [
                    $record->queue_number,
                    $record->patient_id,
                    $record->patient->name ?? 'N/A',
                    $record->patient->email ?? 'N/A',
                    $record->service->name ?? 'N/A',
                    $record->served_by ?? 'N/A',
                    $record->servedBy->name ?? 'Unassigned',
                    $record->servedBy->role_label ?? 'N/A',
                    $record->status,
                    $record->priority,
                    $record->joined_at ? $record->joined_at->toIso8601String() : '',
                    $record->called_at ? $record->called_at->toIso8601String() : '',
                    $record->service_started_at ? $record->service_started_at->toIso8601String() : '',
                    $record->completed_at ? $record->completed_at->toIso8601String() : '',
                    $record->wait_duration_minutes ?? 0,
                    $record->service_duration_minutes ?? 0,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Dispatch clinical and operational report summary to clinic administrator via email.
     */
    public function emailReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate   = $request->input('end_date', Carbon::today()->toDateString());

        $totalCount     = QueueEntry::whereDate('joined_at', '>=', $startDate)->whereDate('joined_at', '<=', $endDate)->count();
        $completedCount = QueueEntry::whereDate('joined_at', '>=', $startDate)->whereDate('joined_at', '<=', $endDate)->where('status', QueueEntry::STATUS_COMPLETED)->count();
        $skippedCount   = QueueEntry::whereDate('joined_at', '>=', $startDate)->whereDate('joined_at', '<=', $endDate)->where('status', QueueEntry::STATUS_SKIPPED)->count();

        $recipientEmail = Setting::get('clinic_email', auth()->user()->email);

        $bodyContent = sprintf(
            "Clinic Operational Report (%s to %s):\n- Total Patients Registered: %d\n- Completed Consultations: %d\n- Skipped No-Shows: %d\n- Dispatched by Administrator: %s",
            $startDate,
            $endDate,
            $totalCount,
            $completedCount,
            $skippedCount,
            auth()->user()->name
        );

        Mail::to($recipientEmail)->send(new QueueNotificationMail(
            auth()->user(),
            'Clinic Operational Summary Report',
            "Executive Clinic Report: {$startDate} to {$endDate}",
            $bodyContent,
            [
                'Report Range' => "{$startDate} to {$endDate}",
                'Total Volume' => (string) $totalCount,
                'Completed'    => (string) $completedCount,
                'Dispatched By'=> auth()->user()->name,
            ]
        ));

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'report.dispatched_email',
            'entity_type' => 'Report',
            'entity_id'   => null,
            'metadata'    => ['recipient' => $recipientEmail, 'start' => $startDate, 'end' => $endDate],
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', "Operational summary report successfully emailed to {$recipientEmail}!");
    }

    /**
     * Forensic Chain of Custody & Clinical Investigation View for a specific ticket.
     */
    public function investigate(QueueEntry $queueEntry): View
    {
        $queueEntry->load(['patient', 'service', 'servedBy']);

        // Find all audit logs corresponding to this ticket or related actions
        $auditLogs = AuditLog::with('user')
            ->where(function ($q) use ($queueEntry) {
                $q->where('entity_id', $queueEntry->id)
                  ->where('entity_type', 'QueueEntry');
            })
            ->orWhereJsonContains('metadata->queue_number', $queueEntry->queue_number)
            ->orderBy('created_at')
            ->get();

        return view('admin.reports.investigate', compact('queueEntry', 'auditLogs'));
    }
}
