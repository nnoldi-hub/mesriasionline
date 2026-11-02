<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Rezultatele posibile ale unui apel de recrutare și ce înseamnă fiecare
 * pentru lista „De sunat azi”.
 */
final class CallOutcome
{
    /**
     * label:     ce vezi pe buton
     * next_days: în câte zile revine automat lead-ul în listă (null = alegi tu data)
     * status:    statusul CraftsmanLead în care trece lead-ul (null = rămâne cum e)
     * closed:    lead-ul iese din lista de apeluri
     */
    public const OUTCOMES = [
        'fara_raspuns' => ['label' => 'Nu a răspuns',            'next_days' => 1,    'status' => null],
        'interesat'    => ['label' => 'Interesat',               'next_days' => 2,    'status' => 'contactat'],
        'astept_poze'  => ['label' => 'Aștept poze / date',      'next_days' => 1,    'status' => 'contactat'],
        'accepta'      => ['label' => 'Acceptă, îi fac contul ✅', 'next_days' => 0,    'status' => 'contactat'],
        'revino'       => ['label' => 'Revin la o dată',         'next_days' => null, 'status' => 'contactat'],
        'refuzat'      => ['label' => 'Refuzat',                 'closed' => true,    'status' => 'respins'],
        'numar_gresit' => ['label' => 'Număr greșit',            'closed' => true,    'status' => 'respins'],
        // setat automat după prea multe apeluri fără răspuns; nu apare în formular
        'nu_raspunde'  => ['label' => 'Nu răspunde (abandonat)', 'closed' => true,    'status' => 'respins', 'auto' => true],
    ];

    public const REFUSAL_REASONS = [
        'nu_am_timp'          => 'Nu are timp',
        'am_destul_de_lucru'  => 'Are destul de lucru',
        'vrea_dovada_clienti' => 'Vrea să vadă întâi clienți',
        'nu_crede_platforme'  => 'Nu crede în platforme',
        'teama_costuri'       => 'Teamă de costuri ascunse',
        'nu_mai_lucreaza'     => 'Nu mai lucrează în domeniu',
        'alt'                 => 'Alt motiv (vezi nota)',
    ];

    public const SOURCES = [
        'olx'         => 'OLX',
        'publi24'     => 'Publi24',
        'facebook'    => 'Facebook',
        'google_maps' => 'Google Maps',
        'whatsapp'    => 'Grup WhatsApp',
        'recomandare' => 'Recomandare',
        'teren'       => 'Teren / magazin',
        'alt'         => 'Alt',
    ];

    /** Rezultate care contează ca „am vorbit cu omul”. */
    public const TALKED = ['interesat', 'astept_poze', 'accepta', 'revino', 'refuzat'];

    /** După atâtea apeluri fără răspuns, lead-ul e abandonat. */
    public const MAX_NO_ANSWER = 5;

    public static function formOutcomes(): array
    {
        return array_filter(self::OUTCOMES, fn ($o) => empty($o['auto']));
    }

    public static function isClosed(string $outcome): bool
    {
        return ! empty(self::OUTCOMES[$outcome]['closed']);
    }

    public static function statusFor(string $outcome): ?string
    {
        return self::OUTCOMES[$outcome]['status'] ?? null;
    }

    public static function label(?string $outcome): string
    {
        return $outcome ? (self::OUTCOMES[$outcome]['label'] ?? $outcome) : 'Nesunat';
    }

    /**
     * Data următoarei acțiuni. Fără răspuns: a doua zi, iar după 3 încercări
     * la 3 zile, ca să nu-l bombardezi.
     */
    public static function nextActionAt(string $outcome, int $attempts, ?string $chosen): ?Carbon
    {
        if (self::isClosed($outcome)) {
            return null;
        }

        if ($chosen) {
            return Carbon::parse($chosen);
        }

        $days = self::OUTCOMES[$outcome]['next_days'] ?? null;
        if ($days === null) {
            return null; // trebuie aleasă manual
        }

        if ($outcome === 'fara_raspuns' && $attempts >= 3) {
            $days = 3;
        }

        // „Acceptă” → rămâne în lista de azi până îi creezi contul
        return $days === 0 ? now() : now()->addDays($days)->setTime(10, 0);
    }

    /** 0740 123 456 / +40740123456 / 0040... → 0740123456 */
    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '0040')) {
            $digits = substr($digits, 4);
        } elseif (str_starts_with($digits, '40') && strlen($digits) === 11) {
            $digits = substr($digits, 2);
        }

        return str_starts_with($digits, '0') ? $digits : '0' . $digits;
    }

    public static function whatsappUrl(string $phone, string $text): string
    {
        $intl = '40' . ltrim(self::normalizePhone($phone), '0');

        return 'https://wa.me/' . $intl . '?text=' . rawurlencode($text);
    }
}
