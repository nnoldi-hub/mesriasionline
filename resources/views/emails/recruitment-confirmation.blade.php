<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 20px; }
        .container { max-width: 560px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 32px; border: 1px solid #e5e7eb; }
        .logo { font-size: 22px; font-weight: 900; color: #C0392B; margin-bottom: 24px; }
        h1 { font-size: 20px; color: #111827; margin-bottom: 12px; }
        p { color: #4b5563; line-height: 1.6; margin-bottom: 12px; }
        .badge { display: inline-block; background: #dcfce7; color: #166534; font-size: 13px; font-weight: 600; padding: 4px 12px; border-radius: 99px; margin-bottom: 20px; }
        .footer { margin-top: 28px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">meseriasionline.ro</div>
    <div class="badge">✅ Înregistrare primită</div>
    <h1>Bună ziua, {{ $lead->name }}!</h1>
    <p>Am primit cu succes înregistrarea ta ca <strong>{{ $lead->tradeLabel }}</strong> în zona <strong>{{ $lead->city }}</strong>.</p>
    <p>Echipa noastră va verifica profilul tău în maxim <strong>24 de ore</strong> și vei primi un email cu link-ul de activare a contului complet.</p>
    <p>Contul complet îți permite să:</p>
    <ul style="color: #4b5563; line-height: 1.8; padding-left: 20px;">
        <li>Primești cereri de lucrări direct pe telefon</li>
        <li>Îți creezi un profil profesional cu galerie foto</li>
        <li>Ești găsit mai ușor de clienți din zona ta</li>
    </ul>
    <p>Mulțumim că faci parte din comunitatea meseriasionline.ro!</p>
    <div class="footer">
        © {{ date('Y') }} meseriasionline.ro — Platforma meseriașilor de încredere<br>
        Ai primit acest email deoarece te-ai înscris pe meseriasionline.ro.
    </div>
</div>
</body>
</html>
