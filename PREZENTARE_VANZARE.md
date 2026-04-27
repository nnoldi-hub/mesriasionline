# Platformă Digitală de Conectare Meseriași–Clienți
## Document de Prezentare — Ofertă de Vânzare

---

## 1. DESPRE PRODUS

**Meseriași.ro** este o platformă web completă, gata de lansare, care conectează meseriași (specialiști în servicii la domiciliu) cu clienții care au nevoie de ei. Modelul de business este similar cu **Bark.com**, **Thumbtack** sau **PoruncaMeșterului**, adaptat 100% pentru piața din România.

Platforma a fost construită de la zero cu tehnologii moderne, include sisteme complete de monetizare și este pregătită să genereze venituri începând din prima zi.

---

## 2. STACK TEHNIC

| Component | Tehnologie |
|-----------|------------|
| Backend | **Laravel 12.x** (PHP 8.2+) |
| Frontend | Blade + Vite + TailwindCSS |
| Baza de date | MySQL / PostgreSQL |
| Plăți | **Stripe** (integrat complet) |
| Hărți | **Google Maps API** |
| Notificări | Email + SMS + **WhatsApp Business** + Push Web |
| Securitate | reCAPTCHA v2 + 2FA (Google Authenticator) + HMAC Webhooks |
| Export | Excel / CSV / PDF (DomPDF) |

**Versiune curentă:** 1.6.0-dev | **Ultima actualizare:** Aprilie 2026

---

## 3. FUNCȚIONALITĂȚI COMPLETE

### 3.1 — Marketplace Public
- Homepage cu listare meseriași + filtrare după categorie și locație
- Căutare meseriași în proximitate (radius în km, prin Google Maps)
- Pagini SEO-optimizate pentru fiecare categorie și oraș (ideal pentru trafic organic)
- Sitemap XML generat dinamic
- Blog + secțiune Interviuri + secțiune Întrebări & Răspunsuri

### 3.2 — Profil Meseriaș
- Pagină publică dedicată (`/meserias/slug`)
- Galerie portofoliu cu imagini comprimate automat
- Lista servicii cu prețuri
- Recenzii clienți cu rating (stele)
- Badge **Verificat** și badge **Featured** (promovat)
- Link-uri social media (Facebook, WhatsApp, LinkedIn etc.)
- Disponibilitate setabilă (program săptămânal)
- Certificări profesionale

### 3.3 — Dashboard Meseriaș
- Gestionare completă profil, servicii, galerie
- Sistem oferte (primire cereri, trimitere prețuri)
- Calendar programări
- Mesagerie internă (chat cu clienți)
- Notificări în timp real (email, SMS, WhatsApp, push)
- Statistici vizualizări profil și conversii
- Focalizare pe segment de piață și zonă geografică

### 3.4 — Dashboard Administrator
- Gestionare completă utilizatori (activare, dezactivare, promovare)
- Moderare recenzii, articole, întrebări
- Rapoarte și export date (Excel/CSV/PDF)
- Gestionare webhooks
- Statistici globale platformă

### 3.5 — Sistem Monetizare (Stripe — integrat complet)
Trei modele de monetizare, toate implementate:

| Model | Descriere |
|-------|-----------|
| **Abonamente meseriași** | Free / Starter / Pro (lunar sau anual) |
| **Plată per lead** | Meseriașul plătește pentru a răspunde la cereri |
| **Comision la tranzacție** | % din valoarea booking-ului plătit pe platformă |

Include: checkout securizat, webhook-uri Stripe, facturare automată, gestionare abonamente active/expirate.

### 3.6 — Sistem Social & Engagement
- Favoriți (salvare meseriași preferați, cu note personale)
- Recomandări personalizate AI (algoritm multi-semnal: favoriți, istoric căutări, vizualizări profil, locație)
- Partajare pe social media
- Sistem afiliere cu comisioane (referral links, tracking conversii, plăți afiliate)

### 3.7 — API & Integrări
- **REST API public** — acces programatic la date platformă
- **Sistem Webhooks** — notificări în timp real către sisteme externe (HMAC SHA-256 signed)
- **Google Maps** — geocoding, reverse geocoding, calcul distanțe, hartă interactivă
- **WhatsApp Business** — notificări automate meseriași și clienți
- **Google 2FA** — autentificare în doi pași pentru conturi

### 3.8 — Performanță
- **65 indexuri de bază de date** pe 14 tabele optimizate
- Eager loading elimină problemele N+1
- Compresie automată imagini la upload
- Reducere timp de încărcare: homepage ≤ 200ms, profil ≤ 150ms
- **Îmbunătățire globală performanță: 60–80%**

### 3.9 — Securitate
- Google reCAPTCHA v2 pe toate formularele publice
- Detectare activitate suspectă (brute force, boți, SQLi, XSS)
- Blocare automată IP-uri abuzive
- 2FA cu Google Authenticator
- Autentificare securizată cu roluri (Admin / Specialist / Client)

---

## 4. MODELE DE BAZE DE DATE INCLUSE

Platforma include **20+ modele Eloquent** complet implementate:

`User` · `Service` · `Review` · `Appointment` · `Message` · `Conversation` · `QuoteRequest` · `Quote` · `Article` · `ArticleQuestion` · `Category` · `Location` · `Certificate` · `Favorite` · `ProfileView` · `Notification` · `Affiliate` · `AffiliateCommission` · `Webhook` · `WebhookDelivery` · `Transaction` · `Subscription`

---

## 5. STARE CURENTĂ A DEZVOLTĂRII

| Modul | Status | Completare |
|-------|--------|-----------|
| Homepage & Listare | ✅ Complet | 100% |
| Autentificare & Securitate | ✅ Complet | 100% |
| Profil Meseriaș | ✅ Complet | 95% |
| Dashboard Admin | ✅ Complet | 90% |
| Dashboard Meseriaș | ✅ Complet | 95% |
| Blog & Conținut | ✅ Complet | 90% |
| Sistem Recenzii | ✅ Complet | 80% |
| SEO & Sitemap | ✅ Complet | 100% |
| Plăți Stripe | ✅ Complet | 100% |
| Onboarding Wizard | ✅ Complet | 100% |
| Sistem Afiliere | ✅ Complet | 90% |
| Chat & Mesagerie | ✅ Complet | 90% |
| Sistem Webhooks & API | ✅ Complet | 90% |
| Recomandări Personalizate | ✅ Complet | 90% |
| Notificări Multi-canal | ✅ Complet | 90% |
| Responsive Design | ✅ Complet | 95% |

**Estimare cod:** 50.000+ linii de cod, 200+ fișiere de logică aplicație.

---

## 6. CE PRIMEȘTE CUMPĂRĂTORUL

- ✅ **Codul sursă complet** (Laravel + Blade + assets)
- ✅ **Migrările bazei de date** (structura completă, gata de rulat)
- ✅ **Documentație tehnică** (API, webhooks, performanță, securitate)
- ✅ **Documentația de configurare** (.env, Stripe, Google Maps, WhatsApp, reCAPTCHA)
- ✅ **Suport la transfer** (asistență la instalare și configurare inițială)

---

## 7. PENTRU CINE ESTE POTRIVIT

- **Antreprenori** care vor să lanseze rapid un marketplace de servicii în România sau regiune
- **Agenții digitale** care vor să revândă sau să personalizeze platforma pentru clienți
- **Investitori** interesați de piața serviciilor la domiciliu (piață estimată la sute de milioane EUR/an în România)
- **Companii existente** în domeniul construcțiilor, curățeniei, instalațiilor etc. care vor să digitalizeze procesul de conectare meșteri–clienți

---

## 8. AVANTAJE FAȚĂ DE CONSTRUIREA DE LA ZERO

| | Construire de la zero | Achiziție platformă |
|---|---|---|
| Timp | 12–18 luni | **Imediată** |
| Cost dezvoltare | 80.000 – 150.000 EUR | — |
| Risc tehnic | Ridicat | **Zero** |
| Monetizare | Luni/ani | **Din ziua 1** |
| Documentație | Trebuie creată | **Inclusă** |
| Integrări (Stripe, Maps, WhatsApp) | Trebuie implementate | **Gata** |

---

## 9. CONTACT

Pentru demonstrații live, acces la mediu de test sau negocieri, luați legătura direct cu autorul proiectului.

---

*Document generat: Aprilie 2026*
