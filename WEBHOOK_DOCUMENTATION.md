# Documentație Sistem Webhooks

## Cuprins
1. [Introducere](#introducere)
2. [Configurare](#configurare)
3. [Evenimente Disponibile](#evenimente-disponibile)
4. [Structura Payload](#structura-payload)
5. [Securitate](#securitate)
6. [Implementare Client](#implementare-client)
7. [Testare](#testare)
8. [Troubleshooting](#troubleshooting)

---

## Introducere

Sistemul de webhooks permite aplicațiilor externe să primească notificări în timp real când anumite evenimente au loc pe platforma Meseriași. Când un eveniment se declanșează, sistemul trimite un HTTP POST request cu detaliile evenimentului către URL-ul configurat.

### Caracteristici
- ✅ Notificări în timp real pentru 13 tipuri de evenimente
- ✅ Securitate prin semnături HMAC SHA-256
- ✅ Sistem automat de retry pentru request-uri eșuate
- ✅ Statistici detaliate și istoric de livrări
- ✅ Testare webhook-uri direct din interfață
- ✅ Suport pentru evenimente multiple per webhook

---

## Configurare

### 1. Creare Webhook

Accesează secțiunea **Webhooks** din contul tău și creează un webhook nou:

1. **Nume**: Un nume descriptiv pentru webhook
2. **URL**: URL-ul unde vei primi notificările (trebuie HTTPS)
3. **Evenimente**: Selectează evenimentele pentru care vrei notificări
4. **Secret**: Opțional - dacă nu specifici, va fi generat automat

```php
// Exemplu: URL-ul tău trebuie să accepte POST requests
POST https://example.com/webhooks/meseriasi
```

### 2. Endpoint Requirements

URL-ul tău trebuie să:
- Fie accesibil public
- Accepte POST requests
- Răspundă în maxim 10 secunde
- Returneze status code 2xx pentru succes
- Folosească HTTPS (TLS/SSL)

---

## Evenimente Disponibile

### Programări (Appointments)

#### `appointment.created`
Declanșat când o programare nouă este creată.

**Payload:**
```json
{
  "event": "appointment.created",
  "timestamp": "2026-01-14T12:00:00+00:00",
  "data": {
    "id": 123,
    "specialist_id": 45,
    "client_name": "Ion Popescu",
    "client_email": "ion@example.com",
    "client_phone": "0712345678",
    "service_id": 10,
    "appointment_date": "2026-01-20",
    "appointment_time": "14:00:00",
    "status": "pending",
    "is_home_service": false,
    "created_at": "2026-01-14T12:00:00+00:00"
  }
}
```

#### `appointment.confirmed`
Programarea a fost confirmată de specialist.

#### `appointment.completed`
Programarea a fost marcată ca finalizată.

#### `appointment.cancelled`
Programarea a fost anulată.

**Payload pentru statusuri:**
```json
{
  "event": "appointment.confirmed",
  "timestamp": "2026-01-14T13:00:00+00:00",
  "data": {
    "id": 123,
    "specialist_id": 45,
    "client_name": "Ion Popescu",
    "appointment_date": "2026-01-20",
    "appointment_time": "14:00:00",
    "old_status": "pending",
    "new_status": "confirmed",
    "updated_at": "2026-01-14T13:00:00+00:00"
  }
}
```

---

### Oferte (Quotes)

#### `quote_request.created`
Client a trimis o cerere de ofertă.

**Payload:**
```json
{
  "event": "quote_request.created",
  "timestamp": "2026-01-14T12:00:00+00:00",
  "data": {
    "id": 456,
    "specialist_id": 45,
    "client_name": "Maria Ionescu",
    "client_email": "maria@example.com",
    "client_phone": "0723456789",
    "service_id": 15,
    "description": "Renovare baie 10mp",
    "created_at": "2026-01-14T12:00:00+00:00"
  }
}
```

#### `quote.created`
Specialist a trimis o ofertă.

**Payload:**
```json
{
  "event": "quote.created",
  "timestamp": "2026-01-14T14:00:00+00:00",
  "data": {
    "id": 789,
    "quote_request_id": 456,
    "specialist_id": 45,
    "amount": 5000.00,
    "description": "Ofertă renovare baie incluzând...",
    "status": "pending",
    "created_at": "2026-01-14T14:00:00+00:00"
  }
}
```

#### `quote.accepted`
Oferta a fost acceptată de client.

#### `quote.rejected`
Oferta a fost respinsă de client.

---

### Recenzii (Reviews)

#### `review.created`
O recenzie nouă a fost adăugată.

**Payload:**
```json
{
  "event": "review.created",
  "timestamp": "2026-01-14T15:00:00+00:00",
  "data": {
    "id": 234,
    "specialist_id": 45,
    "client_id": 67,
    "rating": 5,
    "comment": "Serviciu excelent, recomand!",
    "is_approved": false,
    "created_at": "2026-01-14T15:00:00+00:00"
  }
}
```

#### `review.approved`
Recenzia a fost aprobată de administrator.

---

### Mesaje (Messages)

#### `message.received`
Un mesaj nou a fost primit.

**Payload:**
```json
{
  "event": "message.received",
  "timestamp": "2026-01-14T16:00:00+00:00",
  "data": {
    "id": 567,
    "conversation_id": 89,
    "sender_id": 100,
    "content": "Bună ziua, am o întrebare...",
    "created_at": "2026-01-14T16:00:00+00:00"
  }
}
```

---

### Utilizatori (Users)

#### `user.registered`
Un utilizator nou s-a înregistrat.

**Payload:**
```json
{
  "event": "user.registered",
  "timestamp": "2026-01-14T10:00:00+00:00",
  "data": {
    "id": 200,
    "name": "Alexandru Popescu",
    "email": "alex@example.com",
    "user_type": "specialist",
    "created_at": "2026-01-14T10:00:00+00:00"
  }
}
```

#### `user.verified`
Utilizatorul și-a verificat email-ul.

---

## Securitate

### HMAC SHA-256 Signature

Fiecare request include un header `X-Webhook-Signature` care conține o semnătură HMAC SHA-256 a payload-ului.

**Headers trimise:**
```
X-Webhook-Event: appointment.created
X-Webhook-ID: 12345
X-Webhook-Delivery-ID: 67890
X-Webhook-Signature: sha256=abc123def456...
```

### Verificare Semnătură (PHP)

```php
<?php

function verifyWebhookSignature($payload, $signature, $secret)
{
    $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    return hash_equals($expectedSignature, $signature);
}

// Exemplu utilizare
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? '';
$secret = 'your_webhook_secret_here';

if (!verifyWebhookSignature($payload, $signature, $secret)) {
    http_response_code(401);
    exit('Invalid signature');
}

$data = json_decode($payload, true);
// Procesează evenimentul...
```

### Verificare Semnătură (Node.js)

```javascript
const crypto = require('crypto');

function verifyWebhookSignature(payload, signature, secret) {
    const expectedSignature = 'sha256=' + 
        crypto.createHmac('sha256', secret)
              .update(payload)
              .digest('hex');
    
    return crypto.timingSafeEqual(
        Buffer.from(signature),
        Buffer.from(expectedSignature)
    );
}

// Exemplu utilizare cu Express
app.post('/webhook', express.raw({type: 'application/json'}), (req, res) => {
    const signature = req.headers['x-webhook-signature'];
    const secret = 'your_webhook_secret_here';
    
    if (!verifyWebhookSignature(req.body, signature, secret)) {
        return res.status(401).send('Invalid signature');
    }
    
    const data = JSON.parse(req.body);
    // Procesează evenimentul...
    
    res.status(200).send('OK');
});
```

### Verificare Semnătură (Python)

```python
import hmac
import hashlib

def verify_webhook_signature(payload, signature, secret):
    expected_signature = 'sha256=' + hmac.new(
        secret.encode('utf-8'),
        payload.encode('utf-8'),
        hashlib.sha256
    ).hexdigest()
    
    return hmac.compare_digest(expected_signature, signature)

# Exemplu utilizare cu Flask
from flask import Flask, request

@app.route('/webhook', methods=['POST'])
def webhook():
    payload = request.get_data(as_text=True)
    signature = request.headers.get('X-Webhook-Signature', '')
    secret = 'your_webhook_secret_here'
    
    if not verify_webhook_signature(payload, signature, secret):
        return 'Invalid signature', 401
    
    data = request.get_json()
    # Procesează evenimentul...
    
    return 'OK', 200
```

---

## Implementare Client

### Exemplu Endpoint Complet (PHP/Laravel)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    private $secret = 'your_webhook_secret_here';

    public function handle(Request $request)
    {
        // 1. Verifică semnătura
        if (!$this->verifySignature($request)) {
            Log::warning('Invalid webhook signature');
            return response('Unauthorized', 401);
        }

        // 2. Extrage datele
        $event = $request->header('X-Webhook-Event');
        $data = $request->all();

        // 3. Procesează evenimentul
        try {
            $this->processEvent($event, $data);
        } catch (\Exception $e) {
            Log::error('Webhook processing error: ' . $e->getMessage());
            return response('Error processing webhook', 500);
        }

        // 4. Returnează 200 OK
        return response('OK', 200);
    }

    private function verifySignature(Request $request)
    {
        $signature = $request->header('X-Webhook-Signature');
        $payload = $request->getContent();
        
        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $this->secret);
        
        return hash_equals($expectedSignature, $signature);
    }

    private function processEvent($event, $data)
    {
        switch ($event) {
            case 'appointment.created':
                $this->handleNewAppointment($data);
                break;
            
            case 'appointment.confirmed':
                $this->handleAppointmentConfirmed($data);
                break;
            
            case 'quote_request.created':
                $this->handleNewQuoteRequest($data);
                break;
            
            case 'review.created':
                $this->handleNewReview($data);
                break;
            
            // Adaugă alte evenimente...
            
            default:
                Log::info("Unhandled webhook event: {$event}");
        }
    }

    private function handleNewAppointment($data)
    {
        // Trimite notificare SMS
        // Adaugă în calendar extern
        // Notifică echipa...
        Log::info('New appointment created', $data['data']);
    }

    // Implementează celelalte metode...
}
```

### Best Practices

1. **Răspunde rapid**: Confirmă primirea webhook-ului cu 200 OK, apoi procesează async
2. **Idempotență**: Salvează `X-Webhook-Delivery-ID` pentru a evita procesări duplicate
3. **Rate limiting**: Nu limita request-urile de la IP-ul platformei
4. **Error handling**: Returnează 2xx doar dacă procesarea a reușit
5. **Logging**: Log-uiește toate webhook-urile primite pentru debugging

---

## Testare

### 1. Test din Interfață

Folosește butonul **"Testează"** din pagina de detalii a webhook-ului pentru a trimite un payload de test.

### 2. Testare Locală cu ngrok

Pentru testare în development:

```bash
# Instalează ngrok
npm install -g ngrok

# Expune portul local
ngrok http 8000

# Folosește URL-ul HTTPS generat ca webhook URL
https://abc123.ngrok.io/webhook
```

### 3. Request Inspector

Pentru debugging, folosește servicii gratuite:
- [webhook.site](https://webhook.site)
- [requestbin.com](https://requestbin.com)

Exemplu:
```
1. Accesează webhook.site
2. Copiază URL-ul generat
3. Adaugă-l ca webhook în platformă
4. Declanșează un eveniment
5. Inspectează payload-ul și headers
```

---

## Troubleshooting

### Webhook nu primește notificări

**Verificări:**
1. Webhook-ul este activ? (`is_active = true`)
2. URL-ul este accesibil public și folosește HTTPS?
3. Evenimentul este selectat în configurare?
4. Endpoint-ul răspunde în <10 secunde?

**Soluții:**
```php
// Verifică în pagina webhook dacă delivery history arată erori
// Caută în istoric delivery-uri eșuate și mesajele de eroare

// Testează manual endpoint-ul:
curl -X POST https://your-url.com/webhook \
  -H "Content-Type: application/json" \
  -H "X-Webhook-Event: appointment.created" \
  -H "X-Webhook-Signature: sha256=test" \
  -d '{"event":"appointment.created","timestamp":"2026-01-14T12:00:00Z","data":{}}'
```

### Erori de semnătură

```php
// Debug: Log payload și semnătura așteptată
Log::info('Payload: ' . $payload);
Log::info('Signature received: ' . $signature);
Log::info('Expected signature: sha256=' . hash_hmac('sha256', $payload, $secret));

// Atenție: Asigură-te că folosești raw payload, nu JSON decoded
$payload = file_get_contents('php://input'); // ✅ Correct
$payload = json_encode($request->all());     // ❌ Wrong
```

### Timeout errors

Dacă procesarea durează mult:

```php
// Răspunde imediat și procesează async
public function handle(Request $request)
{
    // Validare rapidă
    if (!$this->verifySignature($request)) {
        return response('Unauthorized', 401);
    }

    // Trimite în queue pentru procesare
    ProcessWebhook::dispatch($request->all())->onQueue('webhooks');

    // Răspunde imediat
    return response('OK', 200);
}
```

### Rate Limiting

Sistemul trimite maxim 1 request/secundă per webhook. Pentru volume mari:

```php
// Folosește batch processing
// Grupează evenimente și procesează periodic
// Evită blocking operations în webhook handler
```

---

## Întrebări Frecvente

### Cât de des sunt trimise webhook-urile?

Webhook-urile sunt trimise **imediat** când evenimentul se întâmplă (în <1 secundă).

### Ce se întâmplă dacă endpoint-ul meu este down?

Sistemul va reîncerca automat:
- 1 încercare imediată
- Retry după 1 minut
- Retry după 5 minute
- Maximum 3 încercări totale

### Pot avea mai multe webhook-uri pentru același eveniment?

Da! Poți crea mai multe webhook-uri, fiecare cu propriul set de evenimente și URL.

### Cum pot dezactiva temporar un webhook?

Folosește butonul **"Dezactivează"** din pagina de detalii sau editare. Webhook-ul va rămâne configurat dar nu va mai primi notificări.

### Pot vedea payload-ul trimis?

Da! Accesează pagina de detalii a webhook-ului → secțiunea **"Istoric Trimiteri"** → expandează detaliile oricărui delivery pentru a vedea payload-ul exact.

### Webhook-urile consumă din limita de requests API?

Nu, webhook-urile sunt separate de API REST și nu consumă din limita ta de requests.

---

## Suport

Pentru asistență tehnică:
- **Email**: support@meseriasi.ro
- **Documentație API**: https://docs.meseriasi.ro
- **Status Platform**: https://status.meseriasi.ro

---

**Data ultimei actualizări**: 14 Ianuarie 2026
**Versiune documentație**: 1.0
