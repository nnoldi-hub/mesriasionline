# Security Features - Configuration Guide

## Google reCAPTCHA v2 Setup

### 1. Get reCAPTCHA Keys

1. Go to [Google reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin)
2. Click **"+"** to create a new site
3. Fill in the details:
   - **Label**: Fixacasa PRO
   - **reCAPTCHA type**: Select "reCAPTCHA v2" → "I'm not a robot" Checkbox
   - **Domains**: Add your domains (e.g., `fixacasa.ro`, `localhost`)
4. Click **Submit**
5. Copy the **Site Key** and **Secret Key**

### 2. Configure Laravel

Add the keys to your `.env` file:

```env
# Google reCAPTCHA v2
NOCAPTCHA_SECRET=your-secret-key-here
NOCAPTCHA_SITEKEY=your-site-key-here
```

### 3. Where reCAPTCHA is Active

reCAPTCHA is automatically displayed and validated on:

- ✅ Login form (`/login`)
- ✅ Client registration (`/register/client`)
- ✅ Craftsman registration (`/register`)
- ✅ Contact form (`/contact`)
- ✅ Quote request form (`/cereri-oferta/nou`)

### 4. Testing

For local development/testing, you can use Google's test keys:

```env
# Test keys (always pass validation)
NOCAPTCHA_SITEKEY=6LeIxAcTAAAAAA JcZVRqyHh71UMIEGNQ_MXjiZKhI
NOCAPTCHA_SECRET=6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe
```

⚠️ **Important**: Replace test keys with real keys in production!

---

## Suspicious Activity Detection

### Overview

The system automatically detects and blocks suspicious activities to protect against:

- 🔒 Brute force attacks
- 🤖 Bot/scraper traffic  
- 💉 SQL injection attempts
- 🛡️ XSS (Cross-site scripting) attacks
- 📍 Unusual login locations
- ⚡ Rapid form submissions
- 🔄 Session hijacking attempts

### Detection Types

| Type | Trigger | Action | Block Duration |
|------|---------|--------|----------------|
| **Failed Login** | 3+ failed attempts | Log warning | - |
| **Brute Force** | 5+ failed attempts | Block IP | 30 min - 24h |
| **Rapid Submission** | < 5 sec between forms | Log medium | 2 hours |
| **SQL Injection** | SQL keywords in input | Block immediately | 8 hours |
| **XSS Attempt** | Script tags in input | Block immediately | 8 hours |
| **Bot Behavior** | Bot user-agent | Log only | - |
| **Unusual Location** | Different IP for user | Log warning | - |
| **User Agent Change** | Different browser/device | Log medium | - |

### Risk Scoring System

Activities are scored 0-100 based on:
- **Type severity** (failed login: 10, SQL injection: 90)
- **Frequency multiplier** (more attempts = higher score)

**Auto-blocking triggers:**
- ❌ 1+ critical severity activities (score 80+)
- ⚠️ 3+ high severity activities (score 50-79)
- 📊 5+ medium severity activities (score 30-49)  
- 📈 10+ low severity activities (score 10-29)
- 🔢 Total risk score ≥ 150 in last hour

### Block Durations

Based on severity:
- **Critical** (90-100): 24 hours
- **High** (50-89): 8 hours
- **Medium** (30-49): 2 hours
- **Low** (10-29): 30 minutes

### Monitoring Suspicious Activities

Check the `suspicious_activities` table in the database:

```sql
-- View recent suspicious activities
SELECT * FROM suspicious_activities 
ORDER BY created_at DESC 
LIMIT 50;

-- View currently blocked IPs
SELECT ip_address, type, severity, blocked_until 
FROM suspicious_activities 
WHERE is_blocked = 1 
  AND (blocked_until IS NULL OR blocked_until > NOW());

-- View activities by type
SELECT type, COUNT(*) as count, AVG(risk_score) as avg_risk
FROM suspicious_activities 
GROUP BY type 
ORDER BY count DESC;
```

### Manually Unblock an IP

```sql
UPDATE suspicious_activities 
SET is_blocked = 0, blocked_until = NULL 
WHERE ip_address = '123.456.789.0';
```

### What Users See When Blocked

Blocked users see a custom error page (`errors/blocked.blade.php`) with:
- Clear explanation of why they were blocked
- Information that the block is temporary
- Contact support option
- Incident ID for reference

### Performance Considerations

- Uses Laravel Cache for fast lookups (no database hit on every request)
- Failed login attempts cached for 30 minutes
- Form submission timestamps cached for 5 minutes
- User location/agent cached for 30 days
- Only high/critical activities logged to system log

### Integration Points

**Middleware**: `App\Http\Middleware\DetectSuspiciousActivity`
- Applied globally to all web routes
- Checks IP blocking status
- Scans for SQL/XSS attempts
- Logs bot behavior
- Tracks rapid submissions

**Service**: `App\Services\SuspiciousActivityDetector`
- Core detection logic
- Risk scoring algorithms
- Auto-blocking decisions
- Cache management

**Model**: `App\Models\SuspiciousActivity`
- Database persistence
- Query helpers
- Block status checks

### Customization

To adjust detection sensitivity, edit:
- `app/Services/SuspiciousActivityDetector.php`
- `app/Models/SuspiciousActivity.php`

Example: Change failed login threshold:
```php
// In checkFailedLogin() method
if ($attempts >= 3) { // Change from 3 to 5
    $this->log(...);
}
```

---

## Best Practices

1. ✅ Always use real reCAPTCHA keys in production
2. ✅ Monitor `suspicious_activities` table weekly
3. ✅ Review high/critical severity logs in `storage/logs`
4. ✅ Keep backup of `.env` file securely
5. ✅ Test login flow after deploying changes
6. ⚠️ Don't block your own IP during testing!

## Support

For issues or questions:
- Check `storage/logs/laravel.log` for errors
- Review `suspicious_activities` table for patterns
- Contact development team with incident ID

---

**Last Updated**: January 14, 2026  
**Version**: 1.6.0
