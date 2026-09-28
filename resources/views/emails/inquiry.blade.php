<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #334155; }
        .container { max-width: 600px; margin: 20px auto; padding: 40px; border: 1px solid #e2e8f0; border-radius: 24px; }
        .header { border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; mb: 30px; }
        .label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px; }
        .value { font-size: 16px; font-weight: 600; color: #0f172a; margin-bottom: 24px; }
        .message-box { background: #f8fafc; padding: 24px; border-radius: 16px; border-left: 4px solid #3b82f6; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0; color: #1e293b;">New Inquiry Received</h2>
            <p style="margin:5px 0 0; font-size: 14px; color: #64748b;">You have a new inquiry from the Help Center.</p>
        </div>
        
        <div style="margin-top: 30px;">
            <div class="label">Full Name</div>
            <div class="value">{{ $data['name'] }}</div>
            
            <div class="label">Email Address</div>
            <div class="value">{{ $data['email'] }}</div>
            
            <div class="label">Message</div>
            <div class="message-box">
                {{ $data['message'] }}
            </div>
        </div>
        
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #94a3b8;">
            Sent from Director of Education, BTC Help Center • {{ now()->format('M d, Y H:i') }}
        </div>
    </div>
</body>
</html>
