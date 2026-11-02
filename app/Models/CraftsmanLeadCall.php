<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Un apel (sau o încercare de apel) către un lead de recrutare.
 */
class CraftsmanLeadCall extends Model
{
    protected $fillable = [
        'craftsman_lead_id',
        'user_id',
        'outcome',
        'refusal_reason',
        'note',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CraftsmanLead::class, 'craftsman_lead_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
