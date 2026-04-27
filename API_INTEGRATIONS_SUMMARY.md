# API & INTEGRĂRI - DOCUMENTAȚIE COMPLETĂ

## 📋 Cuprins
1. [Prezentare Generală](#prezentare-generală)
2. [Google Maps Integration](#google-maps-integration)
3. [WhatsApp Business Integration](#whatsapp-business-integration)
4. [Export/Import Functionality](#exportimport-functionality)
5. [Webhook System](#webhook-system)
6. [REST API](#rest-api)
7. [Configurare](#configurare)
8. [Utilizare](#utilizare)

---

## 1. Prezentare Generală

Platforma Meseriași include 5 sisteme majore de integrare:

### ✅ Sisteme Implementate
1. **Webhook System** - Notificări automate către servicii externe
2. **REST API** - API public pentru acces programatic
3. **Google Maps Integration** - Hărți interactive și geocoding
4. **WhatsApp Business** - Notificări prin WhatsApp
5. **Export/Import Data** - Import/export în masă CSV/Excel

---

## 2. Google Maps Integration

### 🎯 Funcționalități

#### 2.1 Map Service
**Fișier**: `app/Services/MapService.php`

**Metode disponibile:**

```php
// Geocoding - convertește adresă în coordonate
$coordinates = $mapService->geocodeAddress('București, Sector 1');
// Returns: ['lat' => 44.4268, 'lng' => 26.1025, 'formatted_address' => '...']

// Reverse geocoding - convertește coordonate în adresă
$address = $mapService->reverseGeocode(44.4268, 26.1025);
// Returns: 'București, Sector 1, România'

// Calcul distanță între 2 puncte
$distance = $mapService->calculateDistance(44.4268, 26.1025, 45.7489, 21.2087);
// Returns: 445.23 (km)

// Găsește meseriași în raza de X km
$craftsmen = $mapService->findCraftsmenInRadius(
    lat: 44.4268,
    lng: 26.1025,
    radiusKm: 50,
    categoryId: 1 // optional
);

// Generează date pentru markeri hartă
$markers = $mapService->generateMarkersData($craftsmen);
```

**Caracteristici:**
- ✅ Cache pentru geocoding (30 zile)
- ✅ Fallback la formula Haversine dacă API fail
- ✅ Filtrare după categorie
- ✅ Sortare după distanță
- ✅ Optimizare bounding box pentru performanță

#### 2.2 Map Component (Blade)
**Fișier**: `resources/views/components/map.blade.php`

**Utilizare în view:**

```blade
<x-map 
    map-id="homepage-map"
    height="400px"
    :config="[
        'center' => ['lat' => 45.9432, 'lng' => 24.9668],
        'zoom' => 7,
        'markers' => $markersData,
        'showRadius' => true,
        'radius' => 50,
        'interactive' => true
    ]"
/>
```

**Funcționalități JavaScript:**

```javascript
// Acces instanță hartă
const map = window.mapInstances['homepage-map'];

// Actualizează markeri
map.updateMarkers(newMarkersData);

// Navighează la locație
map.panTo(44.4268, 26.1025, 12);

// Șterge toți markerii
map.clearMarkers();
```

#### 2.3 Map Controller & Routes
**Fișier**: `app/Http/Controllers/MapController.php`

**Endpoint-uri API:**

```javascript
// Geocoding
POST /api/map/geocode
Body: { address: "București, Sector 1" }

// Reverse geocoding
POST /api/map/reverse-geocode
Body: { lat: 44.4268, lng: 26.1025 }

// Calcul distanță
POST /api/map/distance
Body: { lat1, lng1, lat2, lng2, unit: 'km' }

// Căutare în rază
GET /api/map/search-radius?lat=44.4268&lng=26.1025&radius=50&category_id=1

// Obține markeri meseriași
GET /api/map/craftsmen-markers?category_id=1&location_id=2
```

**Exemplu utilizare AJAX:**

```javascript
fetch('/api/map/search-radius?lat=44.4268&lng=26.1025&radius=30', {
    headers: {
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.then(data => {
    console.log(`Găsit ${data.count} meseriași`);
    window.mapInstances['map-id'].updateMarkers(data.markers);
});
```

---

## 3. WhatsApp Business Integration

### 🎯 Funcționalități

#### 3.1 WhatsApp Service
**Fișier**: `app/Services/WhatsAppService.php`

**Metode disponibile:**

```php
// Trimite template message
$sent = $whatsappService->sendTemplateMessage(
    to: '0722123456',
    templateName: 'appointment_confirmation',
    parameters: [...]
);

// Trimite confirmare programare
$sent = $whatsappService->sendAppointmentConfirmation(
    phone: '0722123456',
    appointmentData: [
        'client_name' => 'Ion Popescu',
        'date' => '15.01.2025',
        'time' => '14:00',
        'specialist_name' => 'Maria Ionescu'
    ]
);

// Trimite reminder programare
$sent = $whatsappService->sendAppointmentReminder($phone, $appointmentData);

// Trimite notificare ofertă
$sent = $whatsappService->sendQuoteNotification($phone, $quoteData);

// Trimite notificare schimbare status
$sent = $whatsappService->sendStatusChangeNotification(
    phone: '0722123456',
    status: 'confirmed',
    data: [...]
);

// Trimite mesaj text simplu (testing)
$sent = $whatsappService->sendTextMessage(
    to: '0722123456',
    message: 'Mesaj test'
);
```

**Caracteristici:**
- ✅ Format automat număr telefon la E.164
- ✅ Template messages pentru notificări
- ✅ Verificare signature webhook
- ✅ Logging complet
- ✅ Error handling

#### 3.2 Template Messages

**Template-uri disponibile:**
1. `appointment_confirmation` - Confirmare programare
2. `appointment_reminder` - Reminder programare (24h înainte)
3. `quote_response` - Răspuns ofertă preț
4. `appointment_confirmed` - Programare confirmată
5. `appointment_completed` - Programare finalizată
6. `appointment_cancelled` - Programare anulată
7. `status_update` - Schimbare generică status

**Exemplu parametri template:**

```php
[
    ['type' => 'body', 'parameters' => [
        ['type' => 'text', 'text' => 'Ion Popescu'],      // {{1}}
        ['type' => 'text', 'text' => '15.01.2025'],       // {{2}}
        ['type' => 'text', 'text' => '14:00'],            // {{3}}
        ['type' => 'text', 'text' => 'Maria Ionescu'],    // {{4}}
    ]]
]
```

#### 3.3 Webhook Support

**Verificare signature:**

```php
public function handleWebhook(Request $request)
{
    $signature = $request->header('X-Hub-Signature-256');
    $payload = $request->getContent();
    
    if (!$this->whatsappService->verifyWebhookSignature($payload, $signature)) {
        abort(403, 'Invalid signature');
    }
    
    $this->whatsappService->handleWebhook($request->all());
}
```

**Event-uri procesate:**
- Message delivery status (sent, delivered, read, failed)
- Incoming messages (pentru auto-reply)

---

## 4. Export/Import Functionality

### 🎯 Funcționalități

#### 4.1 Export Controller
**Fișier**: `app/Http\Controllers\ExportController.php`

**Export disponibile:**

```php
// 1. Export programări
GET /export/appointments?format=xlsx&start_date=2025-01-01&end_date=2025-01-31&status=confirmed

// 2. Export recenzii
GET /export/reviews?format=csv&rating=5

// 3. Export meseriași (admin only)
GET /export/craftsmen?format=xlsx
```

**Parametri filtrare programări:**
- `format` - xlsx sau csv (default: xlsx)
- `start_date` - Data început (optional)
- `end_date` - Data sfârșit (optional)
- `status` - pending, confirmed, completed, cancelled (optional)

**Parametri filtrare recenzii:**
- `format` - xlsx sau csv
- `rating` - 1-5 (optional)

**Coloane export programări:**
```
ID | Client | Meseriaș | Serviciu | Data programare | Status | Preț | Notițe | Creat la
```

**Coloane export recenzii:**
```
ID | Client | Meseriaș | Rating | Rating numeric | Comentariu | Data
```

**Coloane export meseriași:**
```
ID | Nume | Email | Telefon | Locație | Servicii | Rating mediu | Nr. recenzii | Activ | Înregistrat la
```

#### 4.2 Import Controller
**Fișier**: `app/Http/Controllers/ImportController.php`

**Endpoint-uri disponibile:**

```php
// 1. Previzualizare import meseriași
POST /import/craftsmen/preview
Form-data: file (CSV)

// 2. Import meseriași
POST /import/craftsmen
Form-data: file (CSV), skip_duplicates (boolean)

// 3. Import servicii
POST /import/services
Form-data: file (CSV)

// 4. Download template meseriași
GET /import/templates/craftsmen

// 5. Download template servicii
GET /import/templates/services
```

**Format CSV meseriași:**

```csv
name,email,phone,password,location,bio,years_of_experience,services,is_active
Ion Popescu,ion@example.com,0722123456,parola123,"București","Electrician",10,"Instalații electrice,Reparații",true
```

**Coloane obligatorii:**
- `name` - Nume complet
- `email` - Email valid și unic

**Coloane opționale:**
- `phone` - Format: 10 cifre (0722123456)
- `password` - Default: "password123"
- `location` - Nume oraș (se creează automat dacă nu există)
- `bio` - Descriere scurtă
- `years_of_experience` - Ani experiență (număr)
- `services` - Lista servicii separate prin virgulă
- `is_active` - true/false (default: true)

**Format CSV servicii:**

```csv
name,description,category,base_price,unit
Instalații electrice,"Montare prize, întrerupătoare",Electrician,150,oră
Zugrăveli interioare,Vopsire pereți,Zugravi & Vopsitori,50,mp
```

**Validare import:**
- ✅ Verificare email duplicat
- ✅ Validare format telefon
- ✅ Validare câmpuri obligatorii
- ✅ Preview înainte de import
- ✅ Raportare erori pe rând
- ✅ Skip duplicates opțional

#### 4.3 Import Interface (Admin)
**Fișier**: `resources/views/admin/import/index.blade.php`

**Funcționalități UI:**
- 📤 Upload fișier CSV (drag & drop)
- 👁️ Previzualizare import cu statistici
- ✅ Validare în timp real
- 📊 Raport detaliat erori
- 📥 Download template-uri
- 🔄 Skip duplicates checkbox

**Statistici previzualizare:**
- Total rânduri
- Rânduri valide
- Rânduri cu erori
- Detalii erori pe rând

---

## 5. Webhook System

### 🎯 Rezumat Webhook System
*Implementat complet în sesiunea anterioară*

**Componente:**
- `app/Models/Webhook.php` - Model webhook
- `app/Models/WebhookDelivery.php` - Istoric livrări
- `app/Services/WebhookService.php` - Logică dispatch
- `app/Http/Controllers/WebhookController.php` - Management
- 6 Observers pentru auto-trigger

**Event-uri suportate:**
```
appointment.created, appointment.updated, appointment.cancelled
quote.created, quote.accepted, quote.rejected
quote_request.created, quote_request.responded
review.created, message.received, user.registered, user.updated
```

**Securitate:**
- HMAC SHA-256 signature
- Retry automat (3 încercări)
- Rate limiting
- Validare URL

**Documentație completă:** Vezi `WEBHOOK_DOCUMENTATION.md`

---

## 6. REST API

### 🎯 Rezumat REST API
*Implementat anterior*

**Endpoint-uri disponibile:**

```
GET /api/craftsmen - Lista meseriași
GET /api/craftsmen/{id} - Detalii meseriaș
GET /api/categories - Lista categorii
GET /api/locations - Lista localități
```

**Autentificare:** Token-based (optional pentru public endpoints)

**Rate limiting:** 60 requests/minut

---

## 7. Configurare

### 7.1 Variabile Mediu (.env)

```env
# Google Maps
GOOGLE_MAPS_API_KEY=your_api_key_here
GOOGLE_PLACES_API_KEY=your_places_key_here  # optional, folosește GOOGLE_MAPS_API_KEY

# WhatsApp Business API
WHATSAPP_BUSINESS_API_KEY=your_whatsapp_token
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_VERIFY_TOKEN=your_verify_token
WHATSAPP_WEBHOOK_SECRET=your_webhook_secret
```

### 7.2 Configurare Services (config/services.php)

```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'calendar_redirect_uri' => env('GOOGLE_CALENDAR_REDIRECT_URI'),
    'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    'places_api_key' => env('GOOGLE_PLACES_API_KEY', env('GOOGLE_MAPS_API_KEY')),
],

'whatsapp' => [
    'business_api_key' => env('WHATSAPP_BUSINESS_API_KEY'),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    'verify_token' => env('WHATSAPP_VERIFY_TOKEN'),
    'webhook_secret' => env('WHATSAPP_WEBHOOK_SECRET'),
],
```

### 7.3 Permisiuni

**Import/Export:**
- Export programări/recenzii: Orice user autentificat (vezi doar propriile date)
- Export meseriași: Admin only (`can:manage-users`)
- Import: Admin only (`can:manage-users`)

**Map API:**
- Toate endpoint-urile sunt publice (fără autentificare)
- Rate limiting: 60 requests/minut

---

## 8. Utilizare

### 8.1 Integrare Google Maps în View

**Homepage cu hartă meseriași:**

```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-4">Meseriași din zona ta</h2>
    
    <x-map 
        map-id="craftsmen-map"
        height="500px"
        :config="[
            'center' => ['lat' => 45.9432, 'lng' => 24.9668],
            'zoom' => 7,
            'markers' => $craftsmen->map(fn($c) => [
                'id' => $c->id,
                'lat' => $c->latitude,
                'lng' => $c->longitude,
                'name' => $c->name,
                'avatar' => $c->profile_photo_url,
                'rating' => $c->reviews_avg_rating,
                'reviews_count' => $c->reviews_count,
                'url' => route('craftsman.show', $c->slug),
            ])->toArray(),
            'interactive' => true
        ]"
    />
</div>
@endsection
```

**Profile meseriaș cu coverage area:**

```blade
<x-map 
    map-id="coverage-map"
    height="400px"
    :config="[
        'center' => ['lat' => $craftsman->latitude, 'lng' => $craftsman->longitude],
        'zoom' => 11,
        'markers' => [[
            'id' => $craftsman->id,
            'lat' => $craftsman->latitude,
            'lng' => $craftsman->longitude,
            'name' => $craftsman->name,
        ]],
        'showRadius' => true,
        'radius' => $craftsman->service_radius ?? 50,
        'interactive' => false
    ]"
/>
```

### 8.2 Utilizare WhatsApp Service

**În Observer/Controller:**

```php
use App\Services\WhatsAppService;

public function handle(Appointment $appointment)
{
    $whatsapp = app(WhatsAppService::class);
    
    // Trimite confirmare
    $whatsapp->sendAppointmentConfirmation(
        $appointment->client->phone,
        [
            'client_name' => $appointment->client->name,
            'date' => $appointment->scheduled_at->format('d.m.Y'),
            'time' => $appointment->scheduled_at->format('H:i'),
            'specialist_name' => $appointment->craftsman->name,
        ]
    );
}
```

**În Queue Job (recomandat):**

```php
dispatch(new SendWhatsAppNotification(
    $phone,
    'appointment_reminder',
    $data
))->delay(now()->addHours(23));
```

### 8.3 Export programări în Controller

```php
public function exportMyAppointments()
{
    return redirect()->route('export.appointments', [
        'format' => 'xlsx',
        'start_date' => now()->startOfMonth()->toDateString(),
        'end_date' => now()->endOfMonth()->toDateString(),
    ]);
}
```

### 8.4 Utilizare Map Service pentru căutare

```php
use App\Services\MapService;

public function searchNearby(Request $request, MapService $mapService)
{
    // Geocode adresa utilizatorului
    $userLocation = $mapService->geocodeAddress($request->address);
    
    if (!$userLocation) {
        return back()->with('error', 'Adresa nu a putut fi găsită');
    }
    
    // Găsește meseriași în rază de 30km
    $craftsmen = $mapService->findCraftsmenInRadius(
        lat: $userLocation['lat'],
        lng: $userLocation['lng'],
        radiusKm: 30,
        categoryId: $request->category_id
    );
    
    // Generează markeri pentru hartă
    $markers = $mapService->generateMarkersData($craftsmen);
    
    return view('search.results', [
        'craftsmen' => $craftsmen,
        'markers' => $markers,
        'userLocation' => $userLocation,
    ]);
}
```

---

## 📊 Statistici Implementare

### Fișiere Create/Modificate

**Google Maps:**
- ✅ `app/Services/MapService.php` (300+ linii)
- ✅ `app/Http/Controllers/MapController.php` (150+ linii)
- ✅ `resources/views/components/map.blade.php` (200+ linii)
- ✅ `resources/views/layouts/app.blade.php` (modificat)
- ✅ `config/services.php` (modificat)
- ✅ `routes/web.php` (5 rute noi)

**WhatsApp Business:**
- ✅ `app/Services/WhatsAppService.php` (250+ linii)
- ✅ `config/services.php` (modificat)

**Export/Import:**
- ✅ `app/Http/Controllers/ExportController.php` (180+ linii)
- ✅ `app/Http/Controllers/ImportController.php` (400+ linii)
- ✅ `resources/views/admin/import/index.blade.php` (300+ linii)
- ✅ `routes/web.php` (12 rute noi)

**Total:**
- **10 fișiere noi**
- **3 fișiere modificate**
- **~2000 linii cod nou**
- **17 rute noi**

---

## ✅ Checklist Funcționalități

### Google Maps Integration
- [x] MapService cu geocoding și distance calculation
- [x] Map Blade component refolosibil
- [x] MapController cu API endpoints
- [x] Căutare meseriași în rază
- [x] Markeri interactivi cu info windows
- [x] Desenare rază coverage
- [x] Cache pentru geocoding
- [x] Fallback la Haversine formula

### WhatsApp Business Integration
- [x] WhatsAppService pentru trimitere mesaje
- [x] Template messages pentru notificări
- [x] Format automat număr telefon
- [x] Webhook signature verification
- [x] Message delivery tracking
- [x] Support pentru toate tipurile de notificări

### Export/Import Functionality
- [x] Export programări (filtrate)
- [x] Export recenzii
- [x] Export meseriași (admin)
- [x] Import meseriași cu preview
- [x] Import servicii
- [x] Template CSV download
- [x] Validare comprehensivă
- [x] Raportare erori detaliate
- [x] Interface admin user-friendly

---

## 🚀 Beneficii

### Pentru Utilizatori
- 🗺️ Vizualizare geografică meseriași
- 📍 Căutare după proximitate
- 📊 Export date personale (GDPR compliant)
- 📱 Notificări WhatsApp instant

### Pentru Meseriași
- 📍 Vizibilitate pe hartă
- 📊 Export rapoarte activitate
- 📱 Comunicare directă WhatsApp
- 🎯 Coverage area vizualizat

### Pentru Administratori
- 📥 Import în masă meseriași/servicii
- 📊 Export rapoarte complete
- 🔔 Notificări automate webhook
- 📍 Monitorizare geografică platformă

### Pentru Dezvoltatori
- 🔌 API REST public
- 📡 Webhook system robust
- 📚 Documentație completă
- ⚡ Servicii refolosibile

---

## 🎯 Următorii Pași Recomandați

1. **Testing complet:**
   - Test integrare Google Maps cu API key valid
   - Test WhatsApp cu cont Business API
   - Test import/export cu date reale

2. **Optimizări:**
   - Implementare marker clustering pentru hărți cu multe puncte
   - Queue jobs pentru WhatsApp messages
   - Background processing pentru import-uri mari

3. **Features suplimentare:**
   - Auto-complete locații cu Google Places
   - Routing/directions pe hartă
   - WhatsApp chatbot pentru FAQ
   - Scheduled exports

---

**Documentație completă - Secțiunea 14: API & Integrări**
*Versiune: 1.0*
*Data: Ianuarie 2025*
