# Formular Recrutare Meseriași — Progress

## 🎯 Obiectiv
Formular-magnet public pentru recrutarea a 50 meseriași (10 per meserie):
**Electrician | Instalator | Tâmplar | Zugrav | Mecanic**

Flow: Formular public → Lead salvat în DB → Email confirmare → Admin gestionează lead-urile → Invitație cont → Profil complet

---

## 📋 Etape de Dezvoltare

### ✅ Etapa 1 — Model + Migrare `craftsman_leads`
- [x] Migrare `craftsman_leads` (name, phone, city, trade, experience_range, email, status, notes, invite_token, account_created_at)
- [x] Model `CraftsmanLead` cu fillable, casts, relații

### ✅ Etapa 2 — Formular Public `/inscriere-meserias`
- [x] Controller `CraftsmanRecruitmentController` (store + success)
- [x] View landing page cu form (Secțiunea 1: date rapide + Secțiunea 2: cont + Secțiunea 3: upload opțional)
- [x] View confirmare după submit
- [x] Validare + CSRF + rate limit (10 req/min)
- [x] Route publică `/inscriere-meserias`

### ✅ Etapa 3 — Flow Conversie Lead → Cont
- [x] Metodă `sendInvite()` în controller: generează token unic, trimite email cu link
- [x] Route `/inscriere-meserias/activare/{token}` — pre-completează formularul de cont
- [x] La activare: creează userul din datele lead-ului, marchează `account_created_at`
- [x] Email confirmare înregistrare (`emails/recruitment-confirmation.blade.php`)
- [x] Email invitație cu link activare (`emails/recruitment-invite.blade.php`)

### ✅ Etapa 4 — Admin Panel Leads
- [x] Controller `Admin\CraftsmanLeadController` (index, show, update, sendInvite, getActivationLink, destroy)
- [x] View index cu statistici per meserie + filtre + tabel paginat
- [x] View detaliu lead cu buton „Trimite invitație", „Copiază link WhatsApp", schimbare status, note
- [x] Route admin `/admin/leads/*`
- [x] Link în sidebar admin cu badge „leads noi"

### ⬜ Etapa 5 — Rafinamente & Follow-up
- [ ] Mesaj WhatsApp template (copy gata de folosit)
- [ ] Statistici pe dashboard admin (leaduri noi azi, rată conversie)
- [ ] Export CSV leads
- [ ] SMS follow-up automat (dacă e configurat Twilio)

---

## 📁 Fișiere Create/Modificate

| Fișier | Tip | Status |
|--------|-----|--------|
| `database/migrations/..._create_craftsman_leads_table.php` | Migrare | ✅ |
| `app/Models/CraftsmanLead.php` | Model | ✅ |
| `app/Http/Controllers/CraftsmanRecruitmentController.php` | Controller public | ✅ |
| `app/Http/Controllers/Admin/CraftsmanLeadController.php` | Controller admin | ✅ |
| `resources/views/recruitment/form.blade.php` | View formular public | ✅ |
| `resources/views/recruitment/success.blade.php` | View confirmare | ✅ |
| `resources/views/recruitment/activate.blade.php` | View activare cont | ✅ |
| `resources/views/admin/leads/index.blade.php` | Admin - lista leads | ✅ |
| `resources/views/admin/leads/show.blade.php` | Admin - detaliu lead | ✅ |
| `routes/web.php` | Routes adăugate | ✅ |

---

## 🧪 Testare

```bash
# Rulare migrare
php artisan migrate

# Testare rute
php artisan route:list --path=inscriere
php artisan route:list --path=admin/leads
```

---

## 📊 Statistici țintă

| Meserie | Țintă | Înscrieri | Conturi create |
|---------|-------|-----------|----------------|
| Electrician | 10 | 0 | 0 |
| Instalator | 10 | 0 | 0 |
| Tâmplar | 10 | 0 | 0 |
| Zugrav | 10 | 0 | 0 |
| Mecanic | 10 | 0 | 0 |
| **TOTAL** | **50** | **0** | **0** |
