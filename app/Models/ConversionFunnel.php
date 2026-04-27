<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversionFunnel extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'craftsman_id',
        'visited_at',
        'profile_viewed_at',
        'contact_clicked_at',
        'message_sent_at',
        'quote_requested_at',
        'quote_received_at',
        'quote_accepted_at',
        'appointment_booked_at',
        'review_submitted_at',
        'source',
        'medium',
        'campaign',
        'final_status',
        'total_value',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'profile_viewed_at' => 'datetime',
        'contact_clicked_at' => 'datetime',
        'message_sent_at' => 'datetime',
        'quote_requested_at' => 'datetime',
        'quote_received_at' => 'datetime',
        'quote_accepted_at' => 'datetime',
        'appointment_booked_at' => 'datetime',
        'review_submitted_at' => 'datetime',
        'total_value' => 'decimal:2',
    ];

    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_CONVERTED = 'converted';
    const STATUS_ABANDONED = 'abandoned';

    /**
     * User who went through the funnel
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Craftsman being viewed/contacted
     */
    public function craftsman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'craftsman_id');
    }

    /**
     * Get current funnel stage (1-9)
     */
    public function getCurrentStageAttribute(): int
    {
        if ($this->review_submitted_at) return 9;
        if ($this->appointment_booked_at) return 8;
        if ($this->quote_accepted_at) return 7;
        if ($this->quote_received_at) return 6;
        if ($this->quote_requested_at) return 5;
        if ($this->message_sent_at) return 4;
        if ($this->contact_clicked_at) return 3;
        if ($this->profile_viewed_at) return 2;
        if ($this->visited_at) return 1;
        return 0;
    }

    /**
     * Get stage name
     */
    public function getStageNameAttribute(): string
    {
        $stages = [
            0 => 'Necunoscut',
            1 => 'Vizitator',
            2 => 'Profil Vizualizat',
            3 => 'Contact Click',
            4 => 'Mesaj Trimis',
            5 => 'Ofertă Solicitată',
            6 => 'Ofertă Primită',
            7 => 'Ofertă Acceptată',
            8 => 'Programare Făcută',
            9 => 'Recenzie Trimisă',
        ];

        return $stages[$this->current_stage] ?? 'Necunoscut';
    }

    /**
     * Check if funnel is converted
     */
    public function getIsConvertedAttribute(): bool
    {
        return $this->final_status === self::STATUS_CONVERTED 
            || $this->quote_accepted_at !== null 
            || $this->appointment_booked_at !== null;
    }

    /**
     * Calculate time to conversion
     */
    public function getTimeToConversionAttribute(): ?int
    {
        if (!$this->visited_at) return null;
        
        $conversionTime = $this->appointment_booked_at ?? $this->quote_accepted_at;
        if (!$conversionTime) return null;

        return $this->visited_at->diffInMinutes($conversionTime);
    }

    /**
     * Scope for converted funnels
     */
    public function scopeConverted($query)
    {
        return $query->where('final_status', self::STATUS_CONVERTED);
    }

    /**
     * Scope for abandoned funnels
     */
    public function scopeAbandoned($query)
    {
        return $query->where('final_status', self::STATUS_ABANDONED);
    }

    /**
     * Scope for date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for minimum stage
     */
    public function scopeReachedStage($query, int $stage)
    {
        $stageFields = [
            1 => 'visited_at',
            2 => 'profile_viewed_at',
            3 => 'contact_clicked_at',
            4 => 'message_sent_at',
            5 => 'quote_requested_at',
            6 => 'quote_received_at',
            7 => 'quote_accepted_at',
            8 => 'appointment_booked_at',
            9 => 'review_submitted_at',
        ];

        if (isset($stageFields[$stage])) {
            return $query->whereNotNull($stageFields[$stage]);
        }

        return $query;
    }

    /**
     * Update funnel to next stage
     */
    public function advanceToStage(string $stage): bool
    {
        $field = $stage . '_at';
        
        if (in_array($field, $this->fillable) && $this->{$field} === null) {
            $this->{$field} = now();
            
            // Check if converted
            if (in_array($stage, ['quote_accepted', 'appointment_booked'])) {
                $this->final_status = self::STATUS_CONVERTED;
            }
            
            return $this->save();
        }

        return false;
    }
}
