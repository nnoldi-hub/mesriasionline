<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'body',
        'notification_type',
        'variables',
        'category',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Categorii disponibile pentru template-uri
     */
    public const CATEGORIES = [
        'appointments' => 'Programări',
        'reviews' => 'Recenzii',
        'messages' => 'Mesaje',
        'quotes' => 'Oferte și Cotații',
        'auth' => 'Autentificare',
        'general' => 'General',
    ];

    /**
     * Tipuri de notificări disponibile
     */
    public const NOTIFICATION_TYPES = [
        'new_appointment' => 'Programare nouă',
        'new_review' => 'Recenzie nouă',
        'new_message' => 'Mesaj nou',
        'new_quote_request' => 'Cerere ofertă nouă',
        'quote_received' => 'Ofertă primită',
        'quote_accepted' => 'Ofertă acceptată',
        'welcome' => 'Bun venit',
        'password_reset' => 'Resetare parolă',
    ];

    /**
     * Variabile disponibile pentru fiecare tip de notificare
     */
    public const AVAILABLE_VARIABLES = [
        'new_appointment' => [
            '{{user_name}}' => 'Numele utilizatorului',
            '{{appointment_date}}' => 'Data și ora programării',
            '{{service_name}}' => 'Numele serviciului',
            '{{notes}}' => 'Note adiționale',
            '{{action_url}}' => 'Link către programări',
        ],
        'new_review' => [
            '{{user_name}}' => 'Numele meștereului',
            '{{rating}}' => 'Rating-ul primit (1-5)',
            '{{rating_stars}}' => 'Rating în stele (⭐)',
            '{{comment}}' => 'Comentariul recenziei',
            '{{action_url}}' => 'Link către recenzii',
        ],
        'new_message' => [
            '{{user_name}}' => 'Numele destinatarului',
            '{{sender_name}}' => 'Numele expeditorului',
            '{{message_preview}}' => 'Preview-ul mesajului',
            '{{action_url}}' => 'Link către conversație',
        ],
        'new_quote_request' => [
            '{{user_name}}' => 'Numele meșterului',
            '{{request_title}}' => 'Titlul cererii',
            '{{request_description}}' => 'Descrierea cererii',
            '{{urgency}}' => 'Nivelul de urgență',
            '{{budget}}' => 'Bugetul estimat',
            '{{action_url}}' => 'Link către cerere',
        ],
        'quote_received' => [
            '{{user_name}}' => 'Numele clientului',
            '{{craftsman_name}}' => 'Numele meșterului',
            '{{price}}' => 'Prețul oferit',
            '{{description}}' => 'Descrierea ofertei',
            '{{action_url}}' => 'Link către ofertă',
        ],
        'quote_accepted' => [
            '{{user_name}}' => 'Numele meșterului',
            '{{client_name}}' => 'Numele clientului',
            '{{request_title}}' => 'Titlul cererii',
            '{{price}}' => 'Prețul agreat',
            '{{action_url}}' => 'Link către detalii',
        ],
        'welcome' => [
            '{{user_name}}' => 'Numele utilizatorului',
            '{{email}}' => 'Email-ul utilizatorului',
            '{{role}}' => 'Rolul utilizatorului',
            '{{action_url}}' => 'Link către dashboard',
        ],
    ];

    /**
     * Boot method pentru generare automată slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });

        // Asigură că există doar un template default per notification_type
        static::saving(function ($template) {
            if ($template->is_default && $template->notification_type) {
                static::where('notification_type', $template->notification_type)
                    ->where('id', '!=', $template->id ?? 0)
                    ->update(['is_default' => false]);
            }
        });
    }

    /**
     * Scope pentru template-uri active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pentru template-uri după tip de notificare
     */
    public function scopeForNotificationType($query, string $type)
    {
        return $query->where('notification_type', $type);
    }

    /**
     * Scope pentru template-uri default
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Obține template-ul default pentru un tip de notificare
     */
    public static function getDefaultForType(string $notificationType): ?self
    {
        return static::active()
            ->forNotificationType($notificationType)
            ->default()
            ->first();
    }

    /**
     * Parsează și înlocuiește variabilele în subiect
     */
    public function parseSubject(array $data = []): string
    {
        return $this->replaceVariables($this->subject, $data);
    }

    /**
     * Parsează și înlocuiește variabilele în body
     */
    public function parseBody(array $data = []): string
    {
        return $this->replaceVariables($this->body, $data);
    }

    /**
     * Înlocuiește variabilele în text
     */
    protected function replaceVariables(string $text, array $data): string
    {
        foreach ($data as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $text = str_replace($placeholder, $value ?? '', $text);
        }
        
        return $text;
    }

    /**
     * Obține variabilele disponibile pentru acest template
     */
    public function getAvailableVariablesAttribute(): array
    {
        if ($this->notification_type && isset(self::AVAILABLE_VARIABLES[$this->notification_type])) {
            return self::AVAILABLE_VARIABLES[$this->notification_type];
        }
        
        return [];
    }

    /**
     * Obține numele categoriei
     */
    public function getCategoryNameAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    /**
     * Obține numele tipului de notificare
     */
    public function getNotificationTypeNameAttribute(): string
    {
        return self::NOTIFICATION_TYPES[$this->notification_type] ?? $this->notification_type ?? 'General';
    }
}
