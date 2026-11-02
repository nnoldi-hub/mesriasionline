<?php

namespace App\Console\Commands;

use App\Models\CraftsmanLead;
use App\Support\CallOutcome;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Importă lead-uri de recrutare dintr-un CSV (export din Google Sheets:
 * Fișier → Descărcați → CSV). Recunoaște coloanele după nume, deci ordinea
 * nu contează. Numerele care există deja în baza de date sunt sărite.
 *
 * Coloane recunoscute (după cuvinte din antet):
 *   Telefon* | Meserie / Categorie* | Zona / Oraș | Nume | Link | Sursa | Versiune | Observații
 *
 * Exemplu:
 *   php artisan recruitment:import-leads ~/meseriasi-olx.csv --dry-run
 *   php artisan recruitment:import-leads ~/meseriasi-olx.csv
 *
 * Fișierul CSV NU se pune în repo (repo-ul e public, conține numere de telefon).
 */
class ImportRecruitmentLeads extends Command
{
    protected $signature = 'recruitment:import-leads
                            {file : Calea către fișierul CSV}
                            {--dry-run : Arată ce s-ar importa, fără să salveze}';

    protected $description = 'Importă lead-uri de recrutare meseriași dintr-un CSV (ex. export Google Sheets)';

    /** Cuvinte din antet → câmp intern (primul care se potrivește câștigă). */
    private const HEADER_MAP = [
        'phone'  => ['telefon', 'phone', 'tel'],
        'trade'  => ['meserie', 'categorie', 'trade'],
        'city'   => ['zona', 'oras', 'oraș', 'sector', 'city', 'localitate'],
        'name'   => ['nume', 'name'],
        'url'    => ['link'],
        'source' => ['sursa', 'sursă', 'source'],
        'notes'  => ['observa', 'notit', 'notiț', 'note'],
        'variant'=> ['versiune', 'varianta'],
    ];

    public function handle(): int
    {
        $path = $this->expandHome($this->argument('file'));
        $dryRun = (bool) $this->option('dry-run');

        if (! is_readable($path)) {
            $this->error("Nu pot citi fișierul: {$path}");
            return self::FAILURE;
        }

        $handle = fopen($path, 'r');
        $header = $this->readRow($handle);
        if (! $header) {
            $this->error('Fișierul e gol.');
            return self::FAILURE;
        }

        $cols = $this->mapColumns($header);
        foreach (['phone', 'trade'] as $required) {
            if (! isset($cols[$required])) {
                $this->error("Nu găsesc coloana obligatorie „{$required}” în antet: " . implode(' | ', $header));
                return self::FAILURE;
            }
        }

        $existing = CraftsmanLead::pluck('phone')
            ->map(fn ($p) => CallOutcome::normalizePhone((string) $p))
            ->flip();

        $imported = $skipped = $invalid = 0;
        $rows = [];

        while (($row = $this->readRow($handle)) !== null) {
            $get = fn (string $key) => isset($cols[$key]) ? trim((string) ($row[$cols[$key]] ?? '')) : '';

            $rawPhone = $get('phone');
            if ($rawPhone === '') {
                continue; // rând gol
            }

            $phone = CallOutcome::normalizePhone($rawPhone);
            if (strlen($phone) !== 10) {
                $invalid++;
                $this->warn("  Telefon invalid, sărit: {$rawPhone}");
                continue;
            }

            if (isset($existing[$phone])) {
                $skipped++;
                continue;
            }
            $existing[$phone] = true;

            [$trade, $tradeNote] = $this->mapTrade($get('trade'));
            if (! $trade) {
                $invalid++;
                $this->warn("  Meserie necunoscută „{$get('trade')}” pentru {$phone}, sărit.");
                continue;
            }

            $url = $get('url');
            $notes = collect([
                $tradeNote,
                $get('variant') ? 'Mesaj inițial: ' . $get('variant') : null,
                $get('notes') ?: null,
                'Importat din CSV ' . now()->format('d.m.Y'),
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

            $rows[] = [$data['name'], $phone, CraftsmanLead::$tradeLabels[$trade], $data['city'], $data['source']];

            if (! $dryRun) {
                CraftsmanLead::create($data);
            }
            $imported++;
        }
        fclose($handle);

        if ($rows) {
            $this->table(['Nume', 'Telefon', 'Meserie', 'Oraș', 'Sursă'], $rows);
        }

        $verb = $dryRun ? 'S-ar importa' : 'Importate';
        $this->info("{$verb}: {$imported} · Existente deja (sărite): {$skipped} · Invalide: {$invalid}");

        if ($dryRun) {
            $this->comment('Dry-run: nu s-a salvat nimic. Rulează fără --dry-run ca să importi.');
        } elseif ($imported) {
            $this->comment('Le găsești în admin → De sunat azi → „Nesunați încă”.');
        }

        return self::SUCCESS;
    }

    // ─── Ajutoare ─────────────────────────────────────────────────────────────

    private function readRow($handle): ?array
    {
        $row = fgetcsv($handle, 0, ',', '"', '');
        if ($row === false) {
            return null;
        }
        // elimină BOM-ul UTF-8 de la începutul fișierului
        if (isset($row[0])) {
            $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
        }
        return $row;
    }

    private function mapColumns(array $header): array
    {
        $cols = [];
        foreach ($header as $i => $title) {
            $t = Str::lower(Str::ascii(trim($title)));
            foreach (self::HEADER_MAP as $key => $needles) {
                if (isset($cols[$key])) {
                    continue;
                }
                foreach ($needles as $needle) {
                    if (str_contains($t, Str::ascii($needle))) {
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
        $t = Str::lower(Str::ascii($raw));

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
                $isExact = Str::lower(Str::ascii(CraftsmanLead::$tradeLabels[$trade])) === trim($t);
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

    private function expandHome(string $path): string
    {
        if (str_starts_with($path, '~/')) {
            return rtrim((string) getenv('HOME'), '/') . substr($path, 1);
        }
        return $path;
    }
}
