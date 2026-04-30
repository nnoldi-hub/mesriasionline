# Plan Profesional de Dezvoltare: Chatbot AI — MeseriasiOnline

**Stack**: Laravel 12 · PHP 8.2 · Blade · Alpine.js · Tailwind CSS · OpenAI API  
**Data început**: 30 Aprilie 2026  
**Scop**: Chatbot intern care ghidează meseriașii să se înregistreze, ajută clienții să creeze cereri și automatizează suportul.

---

## Arhitectura aleasă

| Componentă | Decizie | Motiv |
|---|---|---|
| Frontend | Blade + Alpine.js | Deja folosit în proiect (x-data), fără dependențe noi |
| Backend | `ChatbotController` + `/api/chatbot` | Standard REST, simplu și testabil |
| OpenAI Client | `openai-php/client` | Pachet oficial, tipizat, Laravel-friendly |
| Istoric conversație | Laravel Session | Simplu, fără baze de date suplimentare |
| Rate Limiting | Laravel Throttle Middleware | Protecție abuz, deja configurat în proiect |
| Intenții avansate | Keyword detection + structured prompts | Fără costuri extra, fiabil |

---

## FAZA 1 — Backend Core
**Durata estimată**: ~2 ore  
**Status**: ✅ COMPLETAT

### Pași:
- [x] Instalare `openai-php/client` via Composer
- [x] Adăugare config OpenAI în `config/services.php`
- [x] Creare `ChatbotController` cu logică completă
- [x] Endpoint `POST /api/chatbot` cu throttle dedicat
- [x] System prompt complet cu contextul platformei
- [x] Istoricul conversației via Session (max 10 mesaje)
- [x] Validare input + sanitizare

### Fișiere create/modificate:
- `app/Http/Controllers/ChatbotController.php` ✅
- `config/services.php` (adăugat config OpenAI) ✅
- `routes/api.php` (adăugat ruta chatbot) ✅

---

## FAZA 2 — Frontend Widget
**Durata estimată**: ~2 ore  
**Status**: ✅ COMPLETAT

### Pași:
- [x] Creare componentă Blade `chatbot-widget.blade.php`
- [x] UI floating button (dreapta jos)
- [x] Fereastră chat cu bule de mesaje
- [x] Indicatorul de scriere (typing indicator)
- [x] Quick replies / sugestii rapide
- [x] Integrare în `layouts/app.blade.php`
- [x] Stiluri Tailwind responsive (mobile + desktop)

### Fișiere create/modificate:
- `resources/views/components/chatbot-widget.blade.php` ✅
- `resources/views/layouts/app.blade.php` (inclus widget) ✅

---

## FAZA 3 — Context & Personalitate
**Durata estimată**: ~1 oră  
**Status**: ✅ COMPLETAT

### Pași:
- [x] System prompt extins cu toate informațiile platformei
- [x] Categorii de servicii din baza de date (injectat dinamic)
- [x] Reguli stricte (ce poate/nu poate spune chatbotul)
- [x] Personalitate prietenoasă, profesională, în română
- [x] Răspunsuri predefinite pentru întrebări frecvente

---

## FAZA 4 — Funcționalități Avansate
**Durata estimată**: ~3 ore  
**Status**: ✅ COMPLETAT

### Pași:
- [x] Detectare intenție: recrutare meseriaș
- [x] Detectare intenție: creare cerere client
- [x] Detectare intenție: recomandare categorie
- [x] Răspunsuri cu link-uri acționabile (butoane CTA)
- [x] Flow colectare date meseriaș (ghidare pas cu pas)

### Fișiere create/modificate:
- `app/Services/ChatbotService.php` ✅
- `app/Http/Controllers/ChatbotController.php` (actualizat) ✅

---

## FAZA 5 — Securitate, Rate Limiting & Logging
**Durata estimată**: ~1 oră  
**Status**: ✅ COMPLETAT

### Pași:
- [x] Rate limiting: 20 req/minut per IP
- [x] Sanitizare completă a input-ului
- [x] Lungime maximă mesaj: 500 caractere
- [x] Logging conversații suspicioase
- [x] Blocarea prompturilor de tip "jailbreak"
- [x] CSRF protecție pe endpoint

---

## FAZA 6 — Persistență DB & Tracking Conversii
**Status**: ✅ COMPLETAT

### Pași:
- [x] Migrare DB: tabele `chatbot_conversations` + `chatbot_messages`
- [x] Model `ChatbotConversation` (scopes, stats, intentLabel)
- [x] Model `ChatbotMessage` (role, actions JSON, tokens_used)
- [x] Controller actualizat: salvare automată conversații în DB
- [x] Endpoint `POST /api/chatbot/convert` — tracking click CTA
- [x] Widget actualizat cu `trackConversion(url)` (non-blocking)

---

## FAZA 7 — Panou Admin Monitorizare
**Status**: ✅ COMPLETAT

### Pași:
- [x] `ChatbotAdminController` (index / show / destroy)
- [x] View `admin/chatbot/index.blade.php`: KPI cards, grafic zilnic Chart.js, tabel filtrabil
- [x] View `admin/chatbot/show.blade.php`: transcript complet, meta date, acțiune delete
- [x] Rute admin (`admin.chatbot.index` / `.show` / `.destroy`)
- [x] Link în sidebar admin cu badge conversații azi

---

## Rezumat fișiere create

| Fișier | Descriere |
|---|---|
| `app/Http/Controllers/ChatbotController.php` | Controller principal + salvare DB + tracking conversie |
| `app/Http/Controllers/Admin/ChatbotAdminController.php` | Panou admin: index, show, destroy |
| `app/Services/ChatbotService.php` | Logica AI + prompts + detecție intenție |
| `app/Models/ChatbotConversation.php` | Model conversație cu stats + scopes |
| `app/Models/ChatbotMessage.php` | Model mesaje individuale |
| `database/migrations/2026_04_30_100001_create_chatbot_tables.php` | Tabele chatbot_conversations + chatbot_messages |
| `resources/views/components/chatbot-widget.blade.php` | UI widget frontend |
| `resources/views/admin/chatbot/index.blade.php` | Dashboard admin: KPI + grafic + tabel filtrabil |
| `resources/views/admin/chatbot/show.blade.php` | Detaliu conversație completă |
| `config/services.php` | Config OpenAI adăugat |
| `routes/web.php` | Rute chatbot public + admin |
| `resources/views/layouts/app.blade.php` | Widget inclus în layout |
| `resources/views/admin/partials/sidebar.blade.php` | Link Chatbot AI în sidebar |
| `.env.example` | Variabile OPENAI documentate |

---

## Setup server (rulează o singură dată)

```bash
composer require openai-php/client
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
```

Adaugă în `.env`:
```env
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4o-mini
OPENAI_MAX_TOKENS=500
```

Admin panel disponibil la: `/admin/chatbot`

---

## Variabile de mediu necesare (.env)

```env
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4o-mini
OPENAI_MAX_TOKENS=500
```

---

## Testare manuală

1. Accesează orice pagină a aplicației
2. Click pe butonul de chat (dreapta jos, roșu)
3. Trimite mesaje de test:
   - "Vreau să mă înscriu ca meseriaș"
   - "Am nevoie de un electrician"
   - "Cum funcționează platforma?"
   - "Care sunt comisioanele?"

---

## Note tehnice

- **Model recomandat**: `gpt-4o-mini` (cost redus, viteză bună)
- **Max tokens**: 500 (răspunsuri concise)
- **Istoric**: ultimele 10 mesaje (5 perechi user/assistant)
- **Session key**: `chatbot_history`
- **Timeout**: 15 secunde per request OpenAI
