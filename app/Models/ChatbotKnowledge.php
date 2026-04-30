<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotKnowledge extends Model
{
    protected $table = 'chatbot_knowledge';

    protected $fillable = [
        'question_example',
        'keywords',
        'answer',
        'cta_label',
        'cta_url',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority'  => 'integer',
    ];

    /**
     * Returnează keywordurile ca array.
     */
    public function getKeywordsArrayAttribute(): array
    {
        return array_map('trim', explode(',', strtolower($this->keywords)));
    }

    /**
     * Verifică dacă mesajul se potrivește cu keywordurile acestei intrări.
     */
    public function matchesMessage(string $message): bool
    {
        $msg = strtolower($message);
        foreach ($this->keywords_array as $keyword) {
            if (!empty($keyword) && str_contains($msg, $keyword)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Scope: doar intrările active, ordonate după prioritate.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderByDesc('priority');
    }
}
