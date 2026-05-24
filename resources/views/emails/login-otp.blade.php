<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .card { background: #fff; border-radius: 10px; max-width: 460px; margin: auto; padding: 36px; }
        .logo { font-weight: 700; color: #16a34a; font-size: 18px; margin-bottom: 24px; }
        .otp { font-size: 42px; font-weight: 800; letter-spacing: 12px; color: #111; text-align: center;
               background: #f0fdf4; border: 2px dashed #16a34a; border-radius: 8px; padding: 16px 0; margin: 24px 0; }
        .note { font-size: 13px; color: #6b7280; margin-top: 16px; }
        .footer { margin-top: 32px; font-size: 12px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">🌾 Crop Yield Portal</div>
        <p>Hi {{ $userName }},</p>
        <p>Use the code below to complete your login. It expires in <strong>10 minutes</strong>.</p>
        <div class="otp">{{ $otp }}</div>
        <p class="note">⚠️ Never share this code with anyone. If you didn't request it, you can safely ignore this email — your account is secure.</p>
        <div class="footer">Crop Yield Portal &bull; Automated security email</div>
    </div>
</body>
</html>
