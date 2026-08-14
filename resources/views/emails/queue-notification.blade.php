<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subjectLine }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 24px; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
        .header { background: #4f46e5; color: #ffffff; padding: 24px; text-align: center; }
        .content { padding: 32px 24px; line-height: 1.6; }
        .ticket-box { background: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 8px; padding: 16px; text-align: center; margin: 20px 0; }
        .ticket-num { font-size: 32px; font-weight: 800; color: #4338ca; letter-spacing: 1px; }
        .footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin:0; font-size:22px; font-weight:700;">MediQueue Healthcare</h1>
            <p style="margin:4px 0 0 0; font-size:13px; opacity:0.9;">Smart Clinic Queue Management</p>
        </div>

        <div class="content">
            <h2 style="font-size:18px; color:#0f172a; margin-top:0;">{{ $heading }}</h2>
            <p style="font-size:14px; color:#475569;">Hello {{ $user->name }},</p>
            <p style="font-size:14px; color:#475569;">{{ $messageContent }}</p>

            @if(isset($details['queue_number']))
                <div class="ticket-box">
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#64748b;">Queue Ticket Number</div>
                    <div class="ticket-num">{{ $details['queue_number'] }}</div>
                    @if(isset($details['service_name']))
                        <div style="font-size:13px; color:#475569; font-weight:600; margin-top:4px;">{{ $details['service_name'] }}</div>
                    @endif
                </div>
            @endif

            <p style="font-size:13px; color:#64748b; margin-top:24px;">
                You can track your live queue status at any time by signing into your account.
            </p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} MediQueue Central Clinic. This is an automated notification.
        </div>
    </div>
</body>
</html>
