<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 20px; }
        .container { max-width: 560px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 32px; border: 1px solid #e5e7eb; }
        .logo { font-size: 22px; font-weight: 900; color: #C0392B; margin-bottom: 24px; }
        .badge { display: inline-block; background: #dcfce7; color: #166534; font-size: 13px; font-weight: 600; padding: 4px 12px; border-radius: 99px; margin-bottom: 20px; }
        h1 { font-size: 20px; color: #111827; margin-bottom: 12px; }
        p { color: #4b5563; line-height: 1.6; margin-bottom: 12px; }
        .btn { display: inline-block; background: #C0392B; color: #fff !important; font-weight: 700; font-size: 16px; padding: 14px 28px; border-radius: 10px; text-decoration: none; margin: 16px 0; }
        .contact-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .contact-box .label { font-size: 12px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 2px; }
        .contact-box .value { font-size: 15px; color: #111827; font-weight: 600; margin-bottom: 12px; }
        .message-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 16px; margin: 16px 0; font-size: 14px; color: #92400e; font-style: italic; }
        .footer { margin-top: 28px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; }
        .social { margin-top: 10px; }
        .social a { color: #C0392B; text-decoration: none; font-weight: 600; margin-right: 14px; }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">meseriasionline.ro</div>
    <div class="badge">🎉 Meseriaș interesat</div>
    <h1>Bună, {{ $jobRequest->name }}!</h1>
    <p><strong>{{ $craftsman->name }}</strong> este interesat de cererea ta:</p>
    <p>„{{ $jobRequest->title }}"</p>

    @if($craftsmanMessage)
        <div class="message-box">
            💬 „{{ $craftsmanMessage }}"
        </div>
    @endif

    <div class="contact-box">
        <div class="label">Telefon</div>
        <div class="value">{{ $craftsman->phone ?? 'Vezi profilul' }}</div>
        <div class="label">Profil meseriaș</div>
        <div class="value" style="margin-bottom: 0;">{{ $craftsman->name }}</div>
    </div>

    <div style="text-align: center; margin: 24px 0;">
        <a href="{{ $profileUrl }}" class="btn">Vezi profilul meseriașului →</a>
    </div>

    <p>Te sfătuim să suni cât mai curând — meseriașii răspund de obicei primilor clienți care îi contactează.</p>

    <div class="footer">
        Echipa OmulPotrivit — meseriasionline.ro
        @if($facebookUrl || $instagramUrl || $tiktokUrl || $youtubeUrl)
            <div class="social">
                @if($facebookUrl)<a href="{{ $facebookUrl }}">Facebook</a>@endif
                @if($instagramUrl)<a href="{{ $instagramUrl }}">Instagram</a>@endif
                @if($tiktokUrl)<a href="{{ $tiktokUrl }}">TikTok</a>@endif
                @if($youtubeUrl)<a href="{{ $youtubeUrl }}">YouTube</a>@endif
            </div>
        @endif
        <div style="margin-top: 10px;">© {{ date('Y') }} meseriasionline.ro — Platforma meseriașilor de încredere</div>
    </div>
</div>
</body>
</html>
