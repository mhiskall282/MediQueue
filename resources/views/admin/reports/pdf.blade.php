<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinical Operational Summary Report ({{ $startDate }} to {{ $endDate }}) — MediQueue</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .hospital-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
        }
        .report-subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .meta-right {
            text-align: right;
            font-size: 10px;
            color: #475569;
        }
        .kpi-container {
            display: flex;
            width: 100%;
            margin-bottom: 18px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #f8fafc;
        }
        .kpi-box {
            flex: 1;
            padding: 10px;
            text-align: center;
            border-right: 1px solid #e2e8f0;
        }
        .kpi-box:last-child {
            border-right: none;
        }
        .kpi-val {
            font-size: 16px;
            font-weight: 800;
            color: #1e293b;
            display: block;
        }
        .kpi-lbl {
            font-size: 9px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-align: left;
            padding: 6px 8px;
            border-bottom: 1px solid #cbd5e1;
            text-transform: uppercase;
            font-size: 8.5px;
        }
        table.data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #fafafa;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-orange { background: #ffedd5; color: #9a3412; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-completed { background: #dcfce7; color: #166534; }
        .badge-skipped { background: #ffedd5; color: #9a3412; }
        .badge-waiting { background: #f1f5f9; color: #475569; }

        .footer {
            margin-top: 24px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            font-size: 8.5px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
        .print-toolbar {
            background: #1e293b;
            color: #ffffff;
            padding: 10px 16px;
            margin-bottom: 16px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .print-btn {
            background: #4f46e5;
            color: #ffffff;
            border: none;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="print-toolbar no-print">
        <div>
            <strong>MediQueue Clinical Report Export</strong> — Generated for Period: {{ $startDate }} to {{ $endDate }}
        </div>
        <div>
            <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
        </div>
    </div>

    {{-- Report Header --}}
    <table class="header-table">
        <tr>
            <td style="vertical-align: top;">
                <div class="hospital-title">🏥 MediQueue Hospital Systems</div>
                <div class="report-subtitle">Clinical Activity & Operational Performance Summary</div>
                <div style="font-size: 9px; color: #64748b; margin-top: 4px;">
                    Department: <strong>{{ $selectedService->name ?? 'All Clinical Departments' }}</strong> |
                    Clinician: <strong>{{ $selectedStaff->name ?? 'All Attending Staff' }}</strong>
                </div>
            </td>
            <td class="meta-right" style="vertical-align: top;">
                <div>Report Period: <strong>{{ date('M d, Y', strtotime($startDate)) }} – {{ date('M d, Y', strtotime($endDate)) }}</strong></div>
                <div>Generated: <strong>{{ now()->format('M d, Y H:i T') }}</strong></div>
                <div>Classification: <strong>CONFIDENTIAL MEDICAL RECORD</strong></div>
            </td>
        </tr>
    </table>

    {{-- KPI Cards --}}
    <div class="kpi-container">
        <div class="kpi-box">
            <span class="kpi-val">{{ number_format($totalEntries) }}</span>
            <span class="kpi-lbl">Total Consultations</span>
        </div>
        <div class="kpi-box">
            <span class="kpi-val" style="color: #16a34a;">{{ number_format($completedEntries) }} ({{ $totalEntries > 0 ? round(($completedEntries / $totalEntries) * 100) : 0 }}%)</span>
            <span class="kpi-lbl">Completed Care</span>
        </div>
        <div class="kpi-box">
            <span class="kpi-val" style="color: #ea580c;">{{ number_format($skippedEntries) }}</span>
            <span class="kpi-lbl">Skipped / No-Shows</span>
        </div>
        <div class="kpi-box">
            <span class="kpi-val" style="color: #4f46e5;">~{{ round($avgWaitMinutes) }} min</span>
            <span class="kpi-lbl">Avg Patient Wait</span>
        </div>
        <div class="kpi-box">
            <span class="kpi-val" style="color: #7c3aed;">~{{ round($avgServiceMinutes) }} min</span>
            <span class="kpi-lbl">Avg Consultation</span>
        </div>
    </div>

    {{-- Activity Log Table --}}
    <div style="font-size: 11px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">
        Patient Activity & Consultation Record Log (Showing {{ $entries->count() }} of {{ $totalEntries }} records)
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Date / Time</th>
                <th>Ticket</th>
                <th>MRN</th>
                <th>Patient Name</th>
                <th>Department</th>
                <th>Clinician</th>
                <th>Triage</th>
                <th>Wait</th>
                <th>Consult</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $e)
                <tr>
                    <td style="white-space: nowrap;">{{ $e->created_at->format('M d H:i') }}</td>
                    <td style="font-family: monospace; font-weight: bold; color: #4f46e5;">{{ $e->queue_number }}</td>
                    <td style="font-family: monospace; font-size: 8.5px; color: #64748b;">{{ $e->hospital_id ?? ('MRN-' . $e->patient_id) }}</td>
                    <td><strong>{{ $e->patient->name }}</strong></td>
                    <td>{{ $e->service->name }}</td>
                    <td>{{ $e->servedBy->name ?? 'Unassigned' }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($e->triage_level ?? 'green') }}">
                            {{ $e->triage_level ?? 'GREEN' }}
                        </span>
                    </td>
                    <td>{{ $e->service_started_at ? $e->joined_at->diffInMinutes($e->service_started_at) . 'm' : '-' }}</td>
                    <td>{{ ($e->service_started_at && $e->completed_at) ? $e->service_started_at->diffInMinutes($e->completed_at) . 'm' : '-' }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($e->status) }}">
                            {{ $e->status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; color: #94a3b8; padding: 20px;">
                        No consultation records found matching filter criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div>MediQueue Clinical Intelligence Platform &bull; ISO-27001 / HIPAA Compliant Telemetry</div>
        <div>Page 1 of 1 &bull; End of Executive Summary</div>
    </div>

</body>
</html>
