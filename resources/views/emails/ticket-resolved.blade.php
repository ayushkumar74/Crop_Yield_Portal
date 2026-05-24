<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Ticket Resolved</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f9fafb;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            padding: 32px 24px;
            color: white;
            text-align: center;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 32px 24px;
        }
        .ticket-info {
            background-color: #f0fdf4;
            border-left: 4px solid #059669;
            padding: 16px;
            margin: 24px 0;
            border-radius: 4px;
        }
        .ticket-info p {
            margin: 8px 0;
            font-size: 14px;
        }
        .ticket-info strong {
            color: #059669;
            font-weight: 600;
        }
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin: 24px 0 12px;
        }
        .response-box {
            background-color: #f9fafb;
            border-left: 4px solid #059669;
            border-radius: 8px;
            padding: 16px;
            margin: 16px 0;
            color: #374151;
        }
        .response-box p {
            line-height: 1.8;
            margin: 12px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #059669;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 24px 0;
            transition: background-color 0.3s;
        }
        .cta-button:hover {
            background-color: #047857;
        }
        .footer {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }
        .footer a {
            color: #059669;
            text-decoration: none;
        }
        .badge {
            display: inline-block;
            background-color: #d1fae5;
            color: #065f46;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>✓ Issue Resolved</h1>
            <p>Your support ticket has been resolved</p>
        </div>

        {{-- Content --}}
        <div class="content">
            <p>Hello {{ $ticket->user->name }},</p>

            <p style="margin: 16px 0;">Good news! Your support ticket has been resolved by our support team.</p>

            {{-- Ticket Info --}}
            <div class="ticket-info">
                <p><span class="badge">RESOLVED</span></p>
                <p><strong>Ticket #:</strong> {{ $ticket->ticket_number }}</p>
                <p><strong>Subject:</strong> {{ $ticket->subject }}</p>
                <p><strong>Resolved On:</strong> {{ $ticket->resolved_at->format('M d, Y \a\t h:i A') }}</p>
            </div>

            @if($ticket->admin_response)
                <div class="section-title">Support Response</div>
                <div class="response-box">
                    {!! nl2br(e($ticket->admin_response)) !!}
                </div>
            @endif

            <div class="section-title">Your Original Ticket</div>
            <p><strong>{{ $ticket->subject }}</strong></p>
            <div class="response-box" style="border-left-color: #d1d5db; margin-top: 12px;">
                {{ $ticket->message }}
            </div>

            <p style="margin-top: 24px;">If you believe your issue is not fully resolved or you have additional questions, please reply to this email or create a new support ticket.</p>

            <center>
                <a href="{{ url('/contact') }}" class="cta-button">Create New Ticket</a>
            </center>

            <div class="section-title">Your Feedback Matters</div>
            <p>We'd love to hear about your experience! If we've helped you, please let us know. Your feedback helps us improve our support and services.</p>

            <p style="margin-top: 32px;">Best regards,<br><strong>CropYield Portal Support Team</strong></p>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>© {{ date('Y') }} CropYield Portal. All rights reserved.</p>
            <p style="margin-top: 12px;">
                <a href="{{ url('/') }}">Visit Website</a> • 
                <a href="{{ url('/about') }}">About Us</a> • 
                <a href="mailto:support@cropyield.com">Contact Support</a>
            </p>
        </div>
    </div>
</body>
</html>
