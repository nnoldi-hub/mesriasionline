<?php

namespace App\Console\Commands;

use App\Models\CraftsmanLead;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;

class NotifyStaleLeads extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'recruitment:notify-stale-leads
                            {--days=2 : Câte zile de la înscriere fără contactare declanșează alerta}
                            {--dry-run : Preview fără a trimite efectiv email-ul}';

    /**
     * The console command description.
     */
    protected $description = 'Notifică adminul despre lead-urile de recrutare rămase necontactate (status "nou")';

    public function handle(AdminNotificationService $adminNotifier): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $leads = CraftsmanLead::where('status', 'nou')
            ->whereNull('stale_reminder_sent_at')
            ->where('created_at', '<=', now()->subDays($days))
            ->orderBy('created_at')
            ->get();

        $this->info("Găsite {$leads->count()} lead-uri netratate de peste {$days} zile.");

        if ($leads->isEmpty()) {
            return Command::SUCCESS;
        }

        if ($dryRun) {
            foreach ($leads as $lead) {
                $this->line("  [DRY-RUN] {$lead->name} ({$lead->trade_label}, {$lead->city}) — înscris {$lead->created_at->format('d.m.Y')}");
            }
            $this->warn('Acesta a fost un dry-run. Nu s-a trimis nimic.');
            return Command::SUCCESS;
        }

        $lines = $leads->map(function (CraftsmanLead $lead) {
            return "- {$lead->name} | {$lead->trade_label} | {$lead->city} | {$lead->phone} | înscris " . $lead->created_at->format('d.m.Y');
        })->implode("\n");

        $adminNotifier->send(
            "{$leads->count()} lead-uri de recrutare netratate de peste {$days} zile",
            "Următoarele lead-uri sunt încă în status \"Nou\" și nu au fost contactate:\n\n" .
            $lines .
            "\n\nVezi lista completă: " . url('/admin/leads')
        );

        $leads->each->update(['stale_reminder_sent_at' => now()]);

        $this->info("Notificare trimisă către admin pentru {$leads->count()} lead-uri.");

        return Command::SUCCESS;
    }
}
