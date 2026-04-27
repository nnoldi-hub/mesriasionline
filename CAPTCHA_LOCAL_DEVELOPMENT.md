# Configurare reCAPTCHA pentru Dezvoltare Locală

## Soluția Recomandată: reCAPTCHA cu localhost

Google reCAPTCHA **funcționează perfect pe localhost**! Nu trebuie să ai un domeniu live.

### Pași simpli:

1. **Accesează Google reCAPTCHA Admin**:
   - https://www.google.com/recaptcha/admin
   - Autentifică-te cu contul Google

2. **Înregistrează site-ul**:
   - Click pe "**+**" (Register a new site)
   - **Label**: "Meseriasi Local Development"
   - **reCAPTCHA type**: selectează **"reCAPTCHA v2"** → **"I'm not a robot" Checkbox"**
   - **Domains**: adaugă (câte unul pe linie):
     ```
     localhost
     127.0.0.1
     ```
   - Acceptă Terms of Service
   - Click **Submit**

3. **Copiază cheile** pe care le primești

4. **Adaugă în `.env`**:
   ```env
   NOCAPTCHA_SECRET=6LeXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
   NOCAPTCHA_SITEKEY=6LeYYYYYYYYYYYYYYYYYYYYYYYYYYYYY
   ```

5. **Curăță cache-ul**:
   ```bash
   php artisan config:clear
   ```

6. **Testează**:
   - Accesează: `http://localhost/login` sau `http://127.0.0.1/login`
   - Captcha ar trebui să apară și să funcționeze!

✅ **Avantaje**:
- Funcționează exact ca în producție
- Testezi comportamentul real
- Aceleași chei pot fi folosite și pe `localhost:8000` (dacă folosești `php artisan serve`)

---

## Alternativa: Dezactivare temporară (doar development)

Dacă preferi să **omiți complet** captcha în timpul dezvoltării:

### Metoda 1: Condiționare bazată pe environment

Modifică validarea în controllere să verifice environment-ul:

**În `app/Http/Controllers/AuthController.php`** (linia 28-34):
```php
public function login(Request $request)
{
    $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];
    
    // Adaugă captcha doar în producție
    if (config('app.env') !== 'local') {
        $rules['g-recaptcha-response'] = 'required|captcha';
    }
    
    $request->validate($rules, [
        'g-recaptcha-response.required' => 'Te rugăm să completezi reCAPTCHA.',
        'g-recaptcha-response.captcha' => 'Verificarea reCAPTCHA a eșuat.',
    ]);
    
    // ... rest of code
}
```

### Metoda 2: Ascunde componentul în view

În componenta recaptcha, poți verifica environment-ul:

**În `resources/views/components/recaptcha.blade.php`**:
```blade
@if(config('app.env') !== 'local')
    {{-- Google reCAPTCHA v2 Component --}}
    <div class="g-recaptcha-wrapper mb-4">
        <div class="g-recaptcha" 
             data-sitekey="{{ config('captcha.sitekey') }}"
             data-theme="{{ $theme ?? 'light' }}"
             data-size="{{ $size ?? 'normal' }}">
        </div>
        @error('g-recaptcha-response')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    @once
        @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endpush
    @endonce
@else
    {{-- Development mode: CAPTCHA disabled --}}
    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
        ⚠️ Development mode: reCAPTCHA is disabled
    </div>
@endif
```

### Metoda 3: Folosește chei de test fake

În `.env`:
```env
NOCAPTCHA_SECRET=6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe
NOCAPTCHA_SITEKEY=6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI
```

Acestea sunt chei de test oficiale Google care **trec întotdeauna validarea** (dar nu afișează captcha real).

⚠️ **Nu uita să le înlocuiești în producție!**

---

## Recomandarea Finală

**Folosește Opțiunea 1** (reCAPTCHA cu localhost) pentru că:
- ✅ Este simplă și rapidă
- ✅ Nu modifici codul
- ✅ Testezi exact cum va funcționa în producție
- ✅ Nu riști să uiți să activezi captcha înainte de deploy

Doar adaugă `localhost` când înregistrezi site-ul pe Google - **durează 2 minute**!

---

## Întrebări Frecvente

### Q: Pot folosi aceleași chei și pentru producție?
**A**: Da! Când vei avea domeniul live, mergi la Google reCAPTCHA Admin, editează site-ul și adaugă domeniul de producție la lista de domenii (ex: `meseriasi.ro`). Aceleași chei vor funcționa pentru ambele!

### Q: Funcționează pe `localhost:8000` (php artisan serve)?
**A**: Da! Când adaugi `localhost` ca domeniu, funcționează pe orice port.

### Q: Ce fac dacă văd "ERROR for site owner: Invalid domain"?
**A**: Verifică că ai adăugat exact `localhost` (fără http://) în lista de domenii pe Google reCAPTCHA Admin.

### Q: Pot testa fără internet?
**A**: Nu, reCAPTCHA necesită conexiune la serverele Google pentru validare. Dacă vrei să lucrezi offline, folosește Metoda 2 (dezactivare temporară).
