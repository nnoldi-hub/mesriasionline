<?php

namespace App\Console\Commands;

use App\Models\PublicJobRequest;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;

class NotifyUnansweredJobRequests extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'requests:notify-unanswered
                            {--hours=3 : Câte ore de la postare fără niciun meseriaș interesat declanșează alerta}
                            {--dry-run : Preview fără a trimite efectiv email-ul}';

    /**
     * The console command description.
     */
    protected $description = 'Notifică adminul despre cererile publice de clienți fără niciun meseriaș interesat';

    public function handle(AdminNotificationService $adminNotifier): int
    {
        $hours = (int) $this->option('hours');
        $dryRun = $this->option('dry-run');

        $jobRequests = PublicJobRequest::with(['category', 'location'])
            ->withCount(['responses as interested_count' => fn ($q) => $q->where('action', 'interested')])
            ->where('status', 'open')
            ->whereNull('no_interest_alert_sent_at')
            ->where('created_at', '<=', now()->subHours($hours))
            ->having('interested_count', '=', 0)
            ->orderBy('created_at')
            ->get();

        $this->info("Găsite {$jobRequests->count()} cereri fără niciun meseriaș interesat de peste {$hours} ore.");

        if ($jobRequests->isEmpty()) {
            return Command::SUCCESS;
        }

        if ($dryRun) {
            foreach ($jobRequests as $jobRequest) {
                $this->line("  [DRY-RUN] #{$jobRequest->id} {$jobRequest->title} — {$jobRequest->name} ({$jobRequest->location_display}) — postată {$jobRequest->created_at->format('d.m.Y H:i')}");
            }
            $this->warn('Acesta a fost un dry-run. Nu s-a trimis nimic.');
            return Command::SUCCESS;
        }

        $lines = $jobRequests->map(function (PublicJobRequest $jobRequest) {
            return "- #{$jobRequest->id} \"{$jobRequest->title}\" | {$jobRequest->name} ({$jobRequest->phone}) | " .
                "{$jobRequest->location_display} | {$jobRequest->urgency_label} | postată " .
                $jobRequest->created_at->format('d.m.Y H:i');
        })->implode("\n");

        $adminNotifier->send(
            "{$jobRequests->count()} cereri clienți fără niciun meseriaș interesat",
            "Următoarele cereri publice sunt deschise de peste {$hours} ore și niciun meseriaș nu s-a arătat interesat:\n\n" .
            $lines .
            "\n\nVezi lista completă: " . url('/admin/cereri-publice')
        );

        $jobRequests->each->update(['no_interest_alert_sent_at' => now()]);

        $this->info("Notificare trimisă către admin pentru {$jobRequests->count()} cereri.");

        return Command::SUCCESS;
    }
}
