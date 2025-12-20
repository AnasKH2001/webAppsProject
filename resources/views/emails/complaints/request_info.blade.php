<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 20px auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px; }
        .header { border-bottom: 2px solid #0056b3; padding-bottom: 10px; margin-bottom: 20px; }
        .footer { margin-top: 30px; font-size: 0.8em; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
        .highlight { font-weight: bold; color: #0056b3; }
        .message-box { background: #f9f9f9; border-left: 4px solid #0056b3; padding: 15px; margin: 20px 0; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Government Complaints System</h2>
        </div>

        <p>Dear <span class="highlight">{{ $complaint->citizen->name }}</span>,</p>

        <p>Regarding your complaint with reference number <span class="highlight">{{ $complaint->reference_number }}</span>, the relevant government entity (<span class="highlight">{{ $complaint->entity->name }}</span>) has requested additional information to proceed with your case.</p>

        <h3>Request Message:</h3>
        <div class="message-box">
            {{ $messageContent }}
        </div>

        <p>Please log in to the mobile application to provide the requested details or upload the necessary documents.</p>

        <p>Thank you for your cooperation.</p>

        <div class="footer">
            <p>This is an automated notification from the Damascus University Software Project.</p>
            <p>Reference: {{ $complaint->reference_number }} | Date: {{ now()->format('Y-m-d H:i') }}</p>
        </div>
    </div>
</body>
</html>