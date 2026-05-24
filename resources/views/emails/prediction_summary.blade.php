<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Crop Yield Prediction Report</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #f8fafc; margin: 0; padding: 0; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #fff; }
        .header { background: linear-gradient(135deg, #16a34a, #059669); padding: 40px 40px 30px; text-align: center; }
        .header h1 { color: #fff; font-size: 28px; margin: 0 0 8px; font-weight: 800; }
        .header p { color: rgba(255,255,255,0.85); margin: 0; font-size: 14px; }
        .content { padding: 40px; }
        .greeting { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
        .subtitle { color: #64748b; font-size: 14px; margin-bottom: 28px; }
        .result-card { background: linear-gradient(135deg, #16a34a, #059669); border-radius: 16px; padding: 24px; text-align: center; margin-bottom: 24px; }
        .result-card .yield { font-size: 48px; font-weight: 900; color: #fff; margin: 0; }
        .result-card .label { color: rgba(255,255,255,0.8); font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }
        .metrics { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px; }
        .metric { background: #f8fafc; border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0; }
        .metric .m-label { font-size: 11px; color: #94a3b8; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }
        .metric .m-value { font-size: 18px; font-weight: 800; color: #1e293b; margin-top: 4px; }
        .risk { display: inline-block; padding: 6px 16px; border-radius: 100px; font-size: 13px; font-weight: 700; margin-bottom: 24px; }
        .risk-low { background: #dcfce7; color: #166534; }
        .risk-medium { background: #fef9c3; color: #854d0e; }
        .risk-high { background: #fee2e2; color: #991b1b; }
        .section-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid #f1f5f9; }
        .rec-item { padding: 12px; background: #f8fafc; border-radius: 10px; margin-bottom: 8px; font-size: 13px; color: #475569; border-left: 3px solid #16a34a; }
        .cta { text-align: center; margin-top: 28px; }
        .cta a { display: inline-block; background: linear-gradient(135deg, #16a34a, #059669); color: #fff; text-decoration: none; padding: 14px 28px; border-radius: 12px; font-weight: 700; font-size: 15px; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 40px; text-align: center; }
        .footer p { color: #94a3b8; font-size: 12px; margin: 4px 0; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <p style="font-size:36px;margin:0 0 10px;">🌾</p>
        <h1>Crop Yield Prediction Report</h1>
        <p>Generated {{ $prediction->created_at->format('d M Y, g:i A') }}</p>
    </div>
    <div class="content">
        <p class="greeting">Hello{{ $prediction->user ? ', ' . $prediction->user->name : '' }}! 👋</p>
        <p class="subtitle">Your yield prediction for <strong>{{ $prediction->crop->translated_crop_name }}</strong> is ready. Here's a detailed summary.</p>

        <div class="result-card">
            <div class="label">Predicted Yield</div>
            <div class="yield">{{ $prediction->predicted_yield }}</div>
            <div class="label">tonnes per hectare</div>
        </div>

        <div style="text-align:center;margin-bottom:20px;">
            <span class="risk risk-{{ strtolower($prediction->risk_level) }}">
                @if($prediction->risk_level === 'Low') ✅ @elseif($prediction->risk_level === 'Medium') ⚠️ @else 🚨 @endif
                {{ $prediction->risk_level }} Risk · {{ $prediction->suitability_score }}/100 Suitability Score
            </span>
        </div>

        <div class="metrics">
            <div class="metric"><div class="m-label">🌡️ Temperature</div><div class="m-value">{{ $prediction->temperature }}°C</div></div>
            <div class="metric"><div class="m-label">🌧️ Rainfall</div><div class="m-value">{{ $prediction->rainfall }}mm</div></div>
            <div class="metric"><div class="m-label">💧 Humidity</div><div class="m-value">{{ $prediction->humidity }}%</div></div>
            <div class="metric"><div class="m-label">🧪 Soil pH</div><div class="m-value">{{ $prediction->soil_ph }}</div></div>
        </div>

        <div class="section-title">✨ AI Expert Insight & Guide</div>
        <div class="rec-item" style="background:#f0fdf4; border-left:4px solid #16a34a; color:#166534; font-weight:500; text-align:left;">
            {!! str_replace(['<p>', '<ul>', '<li>'], ['<p style="margin-top:0; margin-bottom:10px;">', '<ul style="margin-top:0; margin-bottom:10px; padding-left:20px;">', '<li style="margin-bottom:5px;">'], Str::markdown($prediction->recommendation)) !!}
        </div>

        <div class="cta">
            <a href="{{ url('/predictions/' . $prediction->id) }}">View Full Report →</a>
        </div>
    </div>
    <div class="footer">
        <p>🌾 Crop Yield Prediction Portal</p>
        <p>INT221 MVC Framework Project · Laravel 12</p>
        <p style="margin-top:8px;">You're receiving this because you made a prediction on our portal.</p>
    </div>
</div>
</body>
</html>
