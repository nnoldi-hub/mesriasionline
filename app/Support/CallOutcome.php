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
            // Data aleasă în formular e ora României; o salvăm în fusul aplicației.
            return Carbon::parse($chosen, self::TZ)->setTimezone(config('app.timezone', 'UTC'));
        }

        $days = self::OUTCOMES[$outcome]['next_days'] ?? null;
        if ($days === null) {
            return null; // trebuie aleasă manual
        }

        if ($outcome === 'fara_raspuns' && $attempts >= 3) {
            $days = 3;
        }

        // „Acceptă” → rămâne în lista de azi până îi creezi contul
        if ($days === 0) {
            return now();
        }

        return self::callingSlot(now(self::TZ)->addDays($days))
            ->setTimezone(config('app.timezone', 'UTC'));
    }

    /** Fusul orar în care suni (aplicația rulează pe UTC). */
    public const TZ = 'Europe/Bucharest';

    /**
     * Programul tău de apeluri, ora României: seara 18:00 în zilele lucrătoare,
     * sâmbăta 10:00, duminica liberă (se mută pe luni seara).
     */
    public static function callingSlot(Carbon $day): Carbon
    {
        $day = $day->setTimezone(self::TZ);

        if ((int) $day->format('N') === 7) { // duminică
            $day = $day->addDays(1);
        }

        return (int) $day->format('N') === 6
            ? $day->setTime(10, 0)
            : $day->setTime(18, 0);
    }

    /** Când propui să revii la telefon, potrivit cu ziua și ora (României) la care trimiți mesajul. */
    public static function callbackAsk(): string
    {
        $now = now(self::TZ);
        $day = (int) $now->format('N');
        $evening = (int) $now->format('G') >= 17;

        return match (true) {
            $day === 7               => 'Vă pot suna mâine seară, după ora 18?',
            $day === 6               => 'Vă pot suna luni seara, după ora 18?',
            $day === 5 && $evening   => 'Vă pot suna mâine dimineață, între 10 și 12?',
            $evening                 => 'Vă pot suna mâine seară, după ora 18?',
            default                  => 'Vă pot suna diseară, după ora 18?',
        };
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

    /** Pluralul meseriilor, pentru „caut 10 zugravi”. */
    private const TRADE_PLURALS = [
        'electrician' => 'electricieni',
        'instalator'  => 'instalatori',
        'tamplar'     => 'tâmplari',
        'zugrav'      => 'zugravi',
        'mecanic'     => 'mecanici',
    ];

    /**
     * Primul mesaj WhatsApp, trimis DOAR după un apel fără răspuns.
     * Scurt, personal, fără link: scopul e să obții un apel, nu să explici tot.
     */
    public static function whatsappMissedCall(
        string $sender,
        string $leadName,
        string $trade,
        string $tradeLabel,
        ?string $source,
        string $city,
        bool $fromPublicForm = false
    ): string {
        $greeting = ($leadName && $leadName !== 'Necunoscut') ? "Bună ziua, {$leadName}!" : 'Bună ziua!';
        $tradeLower = mb_strtolower($tradeLabel);
        $plural = self::TRADE_PLURALS[$trade] ?? 'meseriași';

        if ($fromPublicForm) {
            return "{$greeting} Sunt {$sender} de la meseriasionline.ro. Am încercat să vă sun, "
                . "v-ați înscris la noi ca {$tradeLower} și vreau să vă fac profilul, gratuit. "
                . self::callbackAsk();
        }

        $where = match ($source) {
            'olx', 'publi24' => "am văzut anunțul dvs. de {$tradeLower} pe " . self::SOURCES[$source],
            'facebook'       => "am văzut pe Facebook că lucrați ca {$tradeLower}",
            'google_maps'    => "am găsit pe Google că lucrați ca {$tradeLower}",
            'recomandare'    => "mi-ați fost recomandat ca {$tradeLower} de încredere",
            default          => "am înțeles că lucrați ca {$tradeLower}",
        };

        return "{$greeting} Sunt {$sender} de la meseriasionline.ro. Am încercat să vă sun, {$where}. "
            . "Caut 10 {$plural} serioși în {$city} cărora să le trimit cereri de la clienți. "
            . 'Pentru primii 10 e gratuit, iar profilul vi-l fac eu. '
            . self::callbackAsk();
    }

    /** Mesajul de după o discuție reală la telefon: aici are sens linkul. */
    public static function whatsappAfterCall(string $sender, string $formUrl): string
    {
        return "Bună ziua, sunt {$sender} de la meseriasionline.ro, am vorbit la telefon. "
            . 'Îmi puteți trimite aici 3–5 poze cu lucrări făcute de dvs. și vă fac eu profilul, gratuit. '
            . "Detalii: {$formUrl}";
    }

    public static function whatsappUrl(string $phone, string $text): string
    {
        $intl = '40' . ltrim(self::normalizePhone($phone), '0');

        return 'https://wa.me/' . $intl . '?text=' . rawurlencode($text);
    }
}
