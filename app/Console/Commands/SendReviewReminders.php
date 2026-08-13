<?php

namespace App\Console\Commands;

use App\Models\QuoteRequest;
use App\Notifications\ClientReviewPendingNotification;
use App\Notifications\ReviewRequestNotification;
use Illuminate\Console\Command;

class SendReviewReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'reviews:send-reminders
                            {--days=3 : Câte zile de la cererea inițială fără recenzie declanșează reminder-ul}
                            {--dry-run : Preview fără a trimite efectiv notificarea}';

    /**
     * The console command description.
     */
    protected $description = 'Trimite un reminder clienților care nu au lăsat încă o recenzie după finalizarea lucrării';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $quoteRequests = QuoteRequest::where('status', 'completed')
            ->whereNotNull('review_requested_at')
            ->whereNull('review_reminder_sent_at')
            ->where('review_requested_at', '<=', now()->subDays($days))
            ->whereDoesntHave('review')
            ->with(['client', 'craftsman'])
            ->get();

        $this->info("Găsite {$quoteRequests->count()} cereri fără recenzie de peste {$days} zile.");

        foreach ($quoteRequests as $quoteRequest) {
            if (!$quoteRequest->client) {
                continue;
            }

            if ($dryRun) {
                $this->line("  [DRY-RUN] Ar trimite reminder către {$quoteRequest->client->email} și ar anunța meseriașul pentru lucrarea \"{$quoteRequest->title}\"");
                continue;
            }

            $quoteRequest->client->notify(new ReviewRequestNotification($quoteRequest, isReminder: true));

            if ($quoteRequest->craftsman) {
                $quoteRequest->craftsman->notify(new ClientReviewPendingNotification($quoteRequest));
            }

            $quoteRequest->update(['review_reminder_sent_at' => now()]);

            $this->line("  Reminder trimis către {$quoteRequest->client->email}, meseriaș anunțat");
        }

        if ($dryRun) {
            $this->warn('Acesta a fost un dry-run. Nu s-a trimis nimic.');
        }

        return Command::SUCCESS;
    }
}
