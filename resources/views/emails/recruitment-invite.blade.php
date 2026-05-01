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
        .btn { display: inline-block; background: #C0392B; color: #fff !important; font-weight: 700; font-size: 16px; padding: 14px 28px; border-radius: 10px; text-decoration: none; margin: 16px 0; }
        .info-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 14px 16px; margin: 16px 0; font-size: 13px; color: #1d4ed8; }
        .footer { margin-top: 28px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">meseriasionline.ro</div>
    <h1>Ești invitat să îți activezi contul, {{ $lead->name }}! 🎉</h1>
    <p>Felicitări! Înregistrarea ta ca <strong>{{ $lead->tradeLabel }}</strong> din <strong>{{ $lead->city }}</strong> a fost aprobată.</p>
    <p>Dă click pe butonul de mai jos pentru a-ți crea contul complet și a începe să primești clienți:</p>

    <div style="text-align: center; margin: 24px 0;">
        <a href="{{ $activationUrl }}" class="btn">Activează-mi contul →</a>
    </div>

    <div class="info-box">
        ℹ️ Acest link este valabil doar pentru tine. Nu îl distribui altor persoane.
    </div>

    <p>Dacă butonul nu funcționează, copiază și inserează acest link în browser:</p>
    <p style="word-break: break-all; font-size: 12px; color: #6b7280;">{{ $activationUrl }}</p>

    <div class="footer">
        © {{ date('Y') }} meseriasionline.ro<br>
        Ai primit acest email deoarece te-ai înscris pe meseriasionline.ro.
        Dacă nu recunoști această înregistrare, ignora acest email.
    </div>
</div>
</body>
</html>
