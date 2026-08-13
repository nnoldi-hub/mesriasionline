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
        .credentials-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .credentials-box .row { margin-bottom: 8px; }
        .credentials-box .label { font-size: 12px; color: #6b7280; }
        .credentials-box .value { font-size: 16px; font-weight: 700; color: #111827; font-family: monospace; }
        .warning-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 16px; margin: 16px 0; font-size: 13px; color: #92400e; }
        .footer { margin-top: 28px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">meseriasionline.ro</div>
    <h1>Bine ai venit, {{ $lead->name }}! 🎉</h1>
    <p>Contul tău de <strong>{{ $lead->tradeLabel }}</strong> din <strong>{{ $lead->city }}</strong> a fost creat de echipa noastră. Iată datele tale de conectare:</p>

    <div class="credentials-box">
        <div class="row">
            <div class="label">Email</div>
            <div class="value">{{ $user->email }}</div>
        </div>
        <div class="row">
            <div class="label">Parolă temporară</div>
            <div class="value">{{ $password }}</div>
        </div>
    </div>

    <div style="text-align: center; margin: 24px 0;">
        <a href="{{ $loginUrl }}" class="btn">Intră în cont →</a>
    </div>

    <div class="warning-box">
        🔒 Din motive de siguranță, te rugăm să-ți schimbi parola imediat ce te conectezi (din secțiunea de profil / setări cont).
    </div>

    <p>După conectare, completează-ți profilul (poze, servicii, descriere) ca să începi să primești clienți.</p>

    <div class="footer">
        © {{ date('Y') }} meseriasionline.ro<br>
        Ai primit acest email deoarece te-ai înscris pe meseriasionline.ro și un membru al echipei ți-a activat contul.
    </div>
</div>
</body>
</html>
