# Configurare Google reCAPTCHA

## Problema
Componentul captcha este corect implementat în formularele de login și înregistrare, dar nu apare deoarece lipsesc cheile API din fișierul `.env`.

## Soluția

### Pasul 1: Obține cheile Google reCAPTCHA

1. Accesează [Google reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin)
2. Autentifică-te cu contul tău Google
3. Click pe **"+"** sau **"Register a new site"**
4. Completează formularul:
   - **Label**: Meseriasi Platform (sau orice nume dorești)
   - **reCAPTCHA type**: Selectează **"reCAPTCHA v2"** → **"I'm not a robot" Checkbox**
   - **Domains**: Adaugă:
     - `localhost`
     - `127.0.0.1`
     - domeniul tău de producție (ex: `meseriasi.ro`)
   - Acceptă Terms of Service
5. Click pe **"Submit"**
6. Vei primi două chei:
   - **Site Key** (cheia publică - vizibilă în HTML)
   - **Secret Key** (cheia privată - folosită pe server)

### Pasul 2: Configurează cheile în `.env`

1. Deschide fișierul `.env` din rădăcina proiectului
   - Dacă nu există, copiază `.env.example` ca `.env`

2. Caută sau adaugă următoarele linii (în jurul liniei 74):

```env
NOCAPTCHA_SECRET=cheia-ta-secreta-aici
NOCAPTCHA_SITEKEY=cheia-ta-site-aici
```

3. Înlocuiește valorile cu cheile tale reale primite de la Google

**Exemplu:**
```env
NOCAPTCHA_SECRET=6LdXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
NOCAPTCHA_SITEKEY=6LdYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY
```

### Pasul 3: Curăță cache-ul

După ce ai salvat fișierul `.env`, rulează în terminal:

```bash
php artisan config:clear
php artisan cache:clear
```

### Pasul 4: Testează

1. Accesează pagina de login: `http://localhost/login`
2. Acum ar trebui să vezi widget-ul reCAPTCHA ("I'm not a robot")
3. Încearcă să te autentifici - captcha ar trebui să funcționeze

## Verificare implementare

### Formulare care au captcha:
✅ Login (`resources/views/auth/login.blade.php`)
✅ Register Meseriaș (`resources/views/auth/register.blade.php`)
✅ Register Client (`resources/views/auth/register-client.blade.php`)
✅ Contact (`resources/views/pages/contact.blade.php`)
✅ Request Quote (`resources/views/quotes/create.blade.php`)

### Controllere cu validare captcha:
✅ `AuthController::login()` - linia 30
✅ `RegisterController::register()` - linia 35
✅ `ClientRegisterController::register()` - linia 24
✅ `PageController::contact()` - linia 27
✅ `QuoteController::store()` - linia 71

### Componenta reCAPTCHA:
✅ `resources/views/components/recaptcha.blade.php`
✅ Layout cu `@stack('scripts')` pentru încărcare script

## Troubleshooting

### Problema: Captcha nu apare încă
- Verifică că ai salvat fișierul `.env`
- Asigură-te că ai rulat `php artisan config:clear`
- Verifică în browserul web (F12 → Console) dacă există erori JavaScript
- Verifică că cheile corespund tipului reCAPTCHA v2 (nu v3)

### Problema: Eroare "ERROR for site owner: Invalid domain"
- Adaugă domeniul/localhost în setările site-ului reCAPTCHA pe Google Admin Console
- Pentru dezvoltare locală, asigură-te că ai adăugat `localhost` și `127.0.0.1`

### Problema: Eroare de validare
- Verifică că `NOCAPTCHA_SECRET` este cheia secretă (nu site key)
- Asigură-te că pachetul `anhskohbo/no-captcha` este instalat (ar trebui să fie deja)

### Problema: Captcha apare dar validarea eșuează mereu
- Verifică că `NOCAPTCHA_SECRET` este corectă în `.env`
- Testează cheile manual pe [Google reCAPTCHA Verify](https://www.google.com/recaptcha/api/siteverify)

## Notă de securitate

⚠️ **IMPORTANT**: 
- Nu commita niciodată fișierul `.env` în Git
- Fișierul `.gitignore` deja exclude `.env`
- Păstrează `NOCAPTCHA_SECRET` confidențială
- Pentru producție, folosește chei diferite decât pentru dezvoltare

## Configurare suplimentară (opțional)

În `config/captcha.php` poți modifica setările:

```php
return [
    'secret' => env('NOCAPTCHA_SECRET'),
    'sitekey' => env('NOCAPTCHA_SITEKEY'),
    'options' => [
        'timeout' => 30, // timeout pentru request-ul de verificare
    ],
];
```

## Documentație

- [Google reCAPTCHA Documentation](https://developers.google.com/recaptcha/docs/display)
- [no-captcha Package](https://github.com/anhskohbo/no-captcha)
