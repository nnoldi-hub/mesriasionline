<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;

class NotifyLowRatedCraftsmen extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'admin:notify-low-ratings
                            {--min-reviews=3 : Minim de recenzii aprobate pentru a fi luat în calcul}
                            {--max-rating=3 : Prag sub care se declanșează alerta (rating mediu)}
                            {--cooldown-days=30 : Nu re-alerta pentru același meseriaș mai devreme de atâtea zile}
                            {--dry-run : Preview fără a trimite efectiv email-ul}';

    /**
     * The console command description.
     */
    protected $description = 'Notifică adminul despre meseriași cu rating mediu scăzut (fără nicio acțiune automată asupra contului)';

    public function handle(AdminNotificationService $adminNotifier): int
    {
        $minReviews = (int) $this->option('min-reviews');
        $maxRating = (float) $this->option('max-rating');
        $cooldownDays = (int) $this->option('cooldown-days');
        $dryRun = $this->option('dry-run');

        $craftsmen = User::where('role', 'specialist')
            ->where(function ($q) use ($cooldownDays) {
                $q->whereNull('low_rating_alert_sent_at')
                  ->orWhere('low_rating_alert_sent_at', '<=', now()->subDays($cooldownDays));
            })
            ->withCount(['reviews as approved_reviews_count' => fn ($q) => $q->where('is_approved', true)])
            ->withAvg(['reviews as approved_reviews_avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->having('approved_reviews_count', '>=', $minReviews)
            ->having('approved_reviews_avg_rating', '<=', $maxRating)
            ->get();

        $this->info("Găsiți {$craftsmen->count()} meseriași cu rating mediu sub {$maxRating} (min. {$minReviews} recenzii).");

        foreach ($craftsmen as $craftsman) {
            $avg = round($craftsman->approved_reviews_avg_rating, 2);

            if ($dryRun) {
                $this->line("  [DRY-RUN] {$craftsman->name} — rating mediu {$avg} din {$craftsman->approved_reviews_count} recenzii");
                continue;
            }

            $adminNotifier->send(
                "Rating scăzut: {$craftsman->name} ({$avg}/5)",
                "Meseriașul {$craftsman->name} ({$craftsman->email}) are un rating mediu de {$avg}/5 " .
                "din {$craftsman->approved_reviews_count} recenzii aprobate.\n\n" .
                "Verifică recenziile și decide dacă e nevoie de intervenție (avertisment, dezactivare, etc.):\n" .
                url('/admin/craftsmen/' . $craftsman->id . '/edit')
            );

            $craftsman->update(['low_rating_alert_sent_at' => now()]);

            $this->line("  Alertă trimisă pentru {$craftsman->name} ({$avg}/5)");
        }

        if ($dryRun) {
            $this->warn('Acesta a fost un dry-run. Nu s-a trimis nimic.');
        }

        return Command::SUCCESS;
    }
}
