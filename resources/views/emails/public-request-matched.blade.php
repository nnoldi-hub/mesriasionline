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
        .info-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 14px 16px; margin: 16px 0; font-size: 13px; color: #1d4ed8; }
        .footer { margin-top: 28px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">meseriasionline.ro</div>
    <div class="badge">✅ Cerere completă</div>
    <h1>Bună, {{ $jobRequest->name }}!</h1>
    <p>Vești bune — cererea ta <strong>„{{ $jobRequest->title }}"</strong> a primit deja
        <strong>{{ \App\Models\PublicJobRequest::MAX_INTERESTED }} meseriași interesați</strong>.</p>
    <p>Fiecare dintre ei ți-a trimis (sau îți va trimite în scurt timp) datele de contact separat, pe email.
        Compară ofertele și alege meseriașul potrivit pentru tine.</p>

    <div class="info-box">
        ℹ️ Am oprit trimiterea cererii către alți meseriași, ca să nu fii sunat de prea multe persoane deodată.
    </div>

    <p>Dacă niciunul dintre cei {{ \App\Models\PublicJobRequest::MAX_INTERESTED }} nu răspunde în timp util,
        poți oricând trimite o cerere nouă.</p>

    <div class="footer">
        © {{ date('Y') }} meseriasionline.ro — Platforma meseriașilor de încredere
    </div>
</div>
</body>
</html>
