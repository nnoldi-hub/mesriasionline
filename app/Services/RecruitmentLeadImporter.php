<?php

namespace App\Services;

use App\Models\CraftsmanLead;
use App\Support\CallOutcome;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Importă lead-uri de recrutare dintr-un CSV: fișier local sau Google Sheet.
 * Coloanele se recunosc după nume, deci ordinea nu contează. Numerele de
 * telefon deja existente sunt sărite, așa că importul poate fi rulat oricât
 * de des (sincronizare doar în sensul sheet → aplicație, doar rânduri noi).
 */
class RecruitmentLeadImporter
{
    /** Cuvinte din antet → câmp intern (primul care se potrivește câștigă). */
    private const HEADER_MAP = [
        'phone'   => ['telefon', 'phone', 'tel'],
        'trade'   => ['meserie', 'categorie', 'trade'],
        'city'    => ['zona', 'oras', 'sector', 'city', 'localitate'],
        'name'    => ['nume', 'name'],
        'url'     => ['link'],
        'source'  => ['sursa', 'source'],
        'notes'   => ['observa', 'notit', 'note'],
        'variant' => ['versiune', 'varianta'],
    ];

    /**
     * Descarcă un Google Sheet ca CSV și îl importă.
     * Acceptă linkul normal de editare sau direct linkul de export CSV.
     */
    public function importFromGoogleSheet(string $url, bool $dryRun = false): array
    {
        $response = Http::timeout(20)->get(self::toCsvExportUrl($url));

        if (! $response->successful()) {
            throw new RuntimeException("Google a răspuns cu eroarea {$response->status()}. Verifică linkul sheet-ului.");
        }

        $body = $response->body();
        if (str_contains(Str::lower($response->header('Content-Type')), 'html') || Str::startsWith(ltrim($body), '<')) {
            throw new RuntimeException('Sheet-ul nu e accesibil public. În Google Sheets: Distribuie → „Oricine are linkul” → Persoană care vizualizează.');
        }

        return $this->importCsvString($body, $dryRun);
    }

    public function importFromFile(string $path, bool $dryRun = false): array
    {
        if (! is_readable($path)) {
            throw new RuntimeException("Nu pot citi fișierul: {$path}");
        }

        return $this->importCsvString((string) file_get_contents($path), $dryRun);
    }

    /**
     * @return array{imported:int, skipped:int, invalid:int, rows:array, warnings:array}
     */
    public function importCsvString(string $csv, bool $dryRun = false): array
    {
        $csv = preg_replace('/^\xEF\xBB\xBF/', '', $csv); // BOM UTF-8
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $csv);
        rewind($handle);

        $header = fgetcsv($handle, 0, ',', '"', '');
        if (! $header) {
            throw new RuntimeException('CSV-ul e gol.');
        }

        $cols = $this->mapColumns($header);
        foreach (['phone' => 'Telefon', 'trade' => 'Meserie'] as $key => $label) {
            if (! isset($cols[$key])) {
                throw new RuntimeException("Nu găsesc coloana „{$label}” în antet: " . implode(' | ', $header));
            }
        }

        $existing = CraftsmanLead::pluck('phone')
            ->map(fn ($p) => CallOutcome::normalizePhone((string) $p))
            ->flip();

        $result = ['imported' => 0, 'skipped' => 0, 'invalid' => 0, 'rows' => [], 'warnings' => []];

        while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
            $get = fn (string $key) => isset($cols[$key]) ? trim((string) ($row[$cols[$key]] ?? '')) : '';

            $rawPhone = $get('phone');
            if ($rawPhone === '') {
                continue; // rând gol
            }

            $phone = CallOutcome::normalizePhone($rawPhone);
            if (strlen($phone) !== 10) {
                $result['invalid']++;
                $result['warnings'][] = "Telefon invalid: {$rawPhone}";
                continue;
            }

            if (isset($existing[$phone])) {
                $result['skipped']++;
                continue;
            }

            [$trade, $tradeNote] = $this->mapTrade($get('trade'));
            if (! $trade) {
                $result['invalid']++;
                $result['warnings'][] = "Meserie necunoscută „{$get('trade')}” la {$phone}";
                continue;
            }
            $existing[$phone] = true;

            $url = $get('url');
            $notes = collect([
                $tradeNote,
                $get('variant') ? 'Mesaj inițial: ' . $get('variant') : null,
                $get('notes') ?: null,
                'Importat ' . now()->format('d.m.Y'),
            ])->filter()->implode("\n");

            $data = [
                'name'        => $this->titleCase($get('name')) ?: 'Necunoscut',
                'phone'       => $phone,
                'city'        => $this->mapCity($get('city')),
                'trade'       => $trade,
                'status'      => 'nou',
                'source'      => $this->mapSource($get('source'), $url),
                'source_url'  => filter_var($url, FILTER_VALIDATE_URL) ? Str::limit($url, 250, '') : null,
                'admin_notes' => Str::limit($notes, 1000, ''),
                'utm_source'  => 'import',
            ];

            $result['rows'][] = [$data['name'], $phone, CraftsmanLead::$tradeLabels[$trade], $data['city'], $data['source']];

            if (! $dryRun) {
                CraftsmanLead::create($data);
            }
            $result['imported']++;
        }
        fclose($handle);

        return $result;
    }

    /** Linkul de editare al unui Google Sheet → linkul de export CSV (păstrează tab-ul ales prin gid). */
    public static function toCsvExportUrl(string $url): string
    {
        if (! preg_match('#docs\.google\.com/spreadsheets/d/([a-zA-Z0-9_-]+)#', $url, $m)) {
            return $url; // deja un link CSV sau altă sursă
        }
        if (str_contains($url, '/export') || str_contains($url, 'output=csv')) {
            return $url;
        }

        $gid = preg_match('/[#&?]gid=(\d+)/', $url, $g) ? $g[1] : '0';

        return "https://docs.google.com/spreadsheets/d/{$m[1]}/export?format=csv&gid={$gid}";
    }

    // ─── Ajutoare ─────────────────────────────────────────────────────────────

    private function mapColumns(array $header): array
    {
        $cols = [];
        foreach ($header as $i => $title) {
            $t = Str::lower(Str::ascii(trim((string) $title)));
            foreach (self::HEADER_MAP as $key => $needles) {
                if (isset($cols[$key])) {
                    continue;
                }
                foreach ($needles as $needle) {
                    if (str_contains($t, $needle)) {
                        $cols[$key] = $i;
                        continue 3;
                    }
                }
            }
        }
        return $cols;
    }

    /** Returnează [trade_enum, notă despre specializare sau null]. */
    private function mapTrade(string $raw): array
    {
        $t = trim(Str::lower(Str::ascii($raw)));

        if (str_contains($t, 'auto')) {
            return ['mecanic', "Specializare: {$raw}"];
        }

        $map = [
            'electric' => 'electrician',
            'elctric'  => 'electrician', // greșeală frecventă de tastare
            'instalat' => 'instalator',
            'zugrav'   => 'zugrav',
            'tamplar'  => 'tamplar',
            'mecanic'  => 'mecanic',
        ];

        foreach ($map as $needle => $trade) {
            if (str_contains($t, $needle)) {
                $isExact = Str::lower(Str::ascii(CraftsmanLead::$tradeLabels[$trade])) === $t;
                return [$trade, $isExact ? null : "Specializare: {$raw}"];
            }
        }

        return [null, null];
    }

    private function mapCity(string $raw): string
    {
        $t = Str::upper(Str::ascii(trim($raw)));

        if ($t === '') {
            return 'București';
        }
        if (preg_match('/SECTOR\s*(\d)/', $t, $m)) {
            return 'București Sector ' . $m[1];
        }
        if (str_contains($t, 'BUCURESTI') && str_contains($t, 'ILFOV')) {
            return 'București-Ilfov';
        }
        if (str_contains($t, 'BUCURESTI')) {
            return 'București';
        }

        return Str::limit($this->titleCase($raw), 100, '');
    }

    private function mapSource(string $raw, string $url): string
    {
        $t = Str::lower($raw . ' ' . $url);

        return match (true) {
            str_contains($t, 'olx')      => 'olx',
            str_contains($t, 'publi24')  => 'publi24',
            str_contains($t, 'facebook') => 'facebook',
            str_contains($t, 'google')   => 'google_maps',
            str_contains($t, 'whatsapp') => 'whatsapp',
            str_contains($t, 'recomand') => 'recomandare',
            default                      => 'alt',
        };
    }

    private function titleCase(string $s): string
    {
        return trim(mb_convert_case(mb_strtolower(trim($s)), MB_CASE_TITLE, 'UTF-8'));
    }
}
