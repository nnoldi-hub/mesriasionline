<?php

namespace App\Console\Commands;

use App\Services\RecruitmentLeadImporter;
use Illuminate\Console\Command;
use RuntimeException;

/**
 * Importă lead-uri de recrutare dintr-un CSV sau direct din Google Sheet.
 *
 *   php artisan recruitment:import-leads ~/fisier.csv --dry-run
 *   php artisan recruitment:import-leads --sheet --dry-run      (folosește RECRUITMENT_SHEET_URL din .env)
 *   php artisan recruitment:import-leads "https://docs.google.com/spreadsheets/d/..."
 */
class ImportRecruitmentLeads extends Command
{
    protected $signature = 'recruitment:import-leads
                            {file? : Calea către un CSV sau un link Google Sheets}
                            {--sheet : Folosește sheet-ul configurat în RECRUITMENT_SHEET_URL}
                            {--dry-run : Arată ce s-ar importa, fără să salveze}';

    protected $description = 'Importă lead-uri de recrutare meseriași dintr-un CSV sau Google Sheet (doar numere noi)';

    public function handle(RecruitmentLeadImporter $importer): int
    {
        $source = $this->option('sheet')
            ? config('services.recruitment_sheet.url')
            : $this->argument('file');

        if (! $source) {
            $this->error('Dă calea unui CSV, un link de Google Sheet, sau folosește --sheet (cu RECRUITMENT_SHEET_URL setat în .env).');
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');

        try {
            $result = str_starts_with($source, 'http')
                ? $importer->importFromGoogleSheet($source, $dryRun)
                : $importer->importFromFile($this->expandHome($source), $dryRun);
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        foreach ($result['warnings'] as $warning) {
            $this->warn('  ' . $warning);
        }
        if ($result['rows']) {
            $this->table(['Nume', 'Telefon', 'Meserie', 'Oraș', 'Sursă'], $result['rows']);
        }

        $verb = $dryRun ? 'S-ar importa' : 'Importate';
        $this->info("{$verb}: {$result['imported']} · Existente deja (sărite): {$result['skipped']} · Invalide: {$result['invalid']}");

        if ($dryRun) {
            $this->comment('Dry-run: nu s-a salvat nimic. Rulează fără --dry-run ca să importi.');
        }

        return self::SUCCESS;
    }

    private function expandHome(string $path): string
    {
        if (str_starts_with($path, '~/')) {
            return rtrim((string) getenv('HOME'), '/') . substr($path, 1);
        }
        return $path;
    }
}
