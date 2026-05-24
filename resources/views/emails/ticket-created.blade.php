<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Ticket Created</title>
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
            background-color: #f3f4f6;
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
        .message-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            margin: 16px 0;
            font-style: italic;
            color: #6b7280;
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
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>✓ Support Ticket Created</h1>
            <p>We've received your message and assigned a ticket number</p>
        </div>

        {{-- Content --}}
        <div class="content">
            <p>Hello {{ $ticket->user->name }},</p>

            <p style="margin: 16px 0;">Thank you for contacting CropYield Portal support! We've received your message and created a support ticket for you.</p>

            {{-- Ticket Info --}}
            <div class="ticket-info">
                <p><strong>Ticket #:</strong> {{ $ticket->ticket_number }}</p>
                <p><strong>Subject:</strong> {{ $ticket->subject }}</p>
                <p><strong>Status:</strong> <span style="color: #dc2626; font-weight: 600;">{{ $ticket->getStatusLabel() }}</span></p>
                <p><strong>Created:</strong> {{ $ticket->created_at->format('M d, Y \a\t h:i A') }}</p>
            </div>

            <div class="section-title">Your Message</div>
            <div class="message-box">
                {{ $ticket->message }}
            </div>

            <div class="section-title">What happens next?</div>
            <p>Our support team will review your ticket and respond within 24 business hours. We appreciate your patience and will do our best to resolve your issue quickly.</p>

            <p style="margin-top: 24px; margin-bottom: 24px;">You can track your ticket status and view responses by logging into your CropYield account.</p>

            <center>
                <a href="{{ url('/dashboard') }}" class="cta-button">View Your Account</a>
            </center>

            <div class="section-title">Need Immediate Help?</div>
            <p>If your issue is urgent, please reply to this email directly or contact us at <strong>support@cropyield.com</strong>. Our support team is available Monday - Friday, 9 AM - 6 PM IST.</p>

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
