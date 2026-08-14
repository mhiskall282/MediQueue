<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 24px 12px;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            color: #ffffff;
            padding: 32px 28px;
            text-align: left;
            border-bottom: 3px solid #6366f1;
        }
        .hospital-badge {
            display: inline-block;
            background: rgba(99, 102, 241, 0.25);
            color: #e0e7ff;
            border: 1px solid rgba(165, 180, 252, 0.4);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 9999px;
            margin-bottom: 12px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.5px;
            color: #ffffff;
        }
        .header p {
            margin: 6px 0 0 0;
            font-size: 12px;
            color: #c7d2fe;
        }
        .content {
            padding: 32px 28px;
            line-height: 1.6;
        }
        .greeting {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .heading {
            font-size: 18px;
            font-weight: 800;
            color: #1e1b4b;
            margin-top: 0;
            margin-bottom: 16px;
            letter-spacing: -0.3px;
        }
        .message-body {
            font-size: 14px;
            color: #334155;
            line-height: 1.65;
            margin-bottom: 24px;
        }
        .ticket-hero {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            border: 2px dashed #818cf8;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
        }
        .ticket-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #4338ca;
            margin-bottom: 4px;
        }
        .ticket-number {
            font-size: 38px;
            font-weight: 900;
            font-family: 'Courier New', Courier, monospace;
            color: #312e81;
            letter-spacing: 2px;
            margin: 4px 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
            background: #f8fafc;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .details-table th, .details-table td {
            padding: 10px 14px;
            text-align: left;
        }
        .details-table tr:not(:last-child) td {
            border-bottom: 1px solid #e2e8f0;
        }
        .details-label {
            font-weight: 700;
            color: #475569;
            width: 40%;
        }
        .details-value {
            font-weight: 600;
            color: #0f172a;
        }
        .cta-container {
            text-align: center;
            margin: 28px 0 12px 0;
        }
        .btn-cta {
            display: inline-block;
            background: #4f46e5;
            color: #ffffff !important;
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
        }
        .footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 24px 28px;
            font-size: 11px;
            line-height: 1.6;
            border-top: 1px solid #1e293b;
        }
        .footer-brand {
            color: #ffffff;
            font-weight: 800;
            font-size: 12px;
            margin-bottom: 4px;
        }
        .disclaimer {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid #334155;
            font-size: 10px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        {{-- Hospital Header --}}
        <div class="header">
            <div class="hospital-badge">🏥 UGMC Smart Clinic Telemetry</div>
            <h1>University of Ghana Medical Centre</h1>
            <p>MediQueue Intelligent Outpatient & Clinical Care Gateway</p>
        </div>

        {{-- Main Email Content --}}
        <div class="content">
            <h2 class="heading">{{ $heading }}</h2>

            <div class="greeting">Dear {{ $user->name }},</div>

            <div class="message-body">
                {!! nl2br(e($messageContent)) !!}
            </div>

            {{-- Queue Ticket Card (if present) --}}
            @if(isset($details['queue_number']))
                <div class="ticket-hero">
                    <div class="ticket-title">Your Live Queue Ticket</div>
                    <div class="ticket-number">{{ $details['queue_number'] }}</div>
                    @if(isset($details['service_name']))
                        <div style="font-size:13px; color:#3730a3; font-weight:700; margin-top:4px;">
                            Department: {{ $details['service_name'] }}
                        </div>
                    @endif
                </div>
            @endif

            {{-- Key-Value Metadata Table --}}
            @if(!empty($details))
                <table class="details-table">
                    @foreach($details as $key => $value)
                        @if($key !== 'queue_number' && $key !== 'service_name')
                            <tr>
                                <td class="details-label">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                <td class="details-value">{{ $value }}</td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            @endif

            <div class="cta-container">
                <a href="{{ config('app.url', 'https://mediqueue-25vl.onrender.com') }}" class="btn-cta">
                    Open MediQueue Hospital Portal &rarr;
                </a>
            </div>
        </div>

        {{-- Official Hospital Footer --}}
        <div class="footer">
            <div class="footer-brand">University of Ghana Medical Centre (UGMC)</div>
            <div>Legon Medical Quarter, Legon Bypass, Accra, Ghana</div>
            <div>Contact: +233 30 290 8400 &bull; Web: <a href="https://ugmc.ug.edu.gh" style="color:#818cf8; text-decoration:none;">ugmc.ug.edu.gh</a></div>
            
            <div class="disclaimer">
                <strong>HIPAA & ISO 27001 Confidentiality Notice:</strong> This electronic message and any attachments contain protected health information (PHI) intended solely for the recipient. If you received this notification in error, please disregard and notify the UGMC System Administrator immediately.
            </div>
        </div>
    </div>
</body>
</html>
