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
     * Normalizează diacriticele românești pentru comparare fuzzy.
     */
    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $from = ['ă', 'â', 'î', 'ș', 'ş', 'ț', 'ţ', 'Ă', 'Â', 'Î', 'Ș', 'Ş', 'Ț', 'Ţ'];
        $to   = ['a', 'a', 'i', 's', 's', 't', 't', 'a', 'a', 'i', 's', 's', 't', 't'];
        return str_replace($from, $to, $text);
    }

    /**
     * Verifică dacă mesajul se potrivește cu keywordurile acestei intrări.
     * Compară atât cu cât și fără diacritice.
     */
    public function matchesMessage(string $message): bool
    {
        $msg           = mb_strtolower($message);
        $msgNormalized = $this->normalize($message);

        foreach ($this->keywords_array as $keyword) {
            if (empty($keyword)) {
                continue;
            }
            $kw           = mb_strtolower($keyword);
            $kwNormalized = $this->normalize($keyword);

            // Potrivire directă (cu diacritice)
            if (str_contains($msg, $kw)) {
                return true;
            }
            // Potrivire fără diacritice
            if (str_contains($msgNormalized, $kwNormalized)) {
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
