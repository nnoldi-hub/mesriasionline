<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionExpiringNotification;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;

class SendSubscriptionExpiryReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'subscriptions:send-expiry-reminders
                            {--days=3 : Trimite reminder cu atâtea zile înainte de expirare}
                            {--dry-run : Preview fără a trimite efectiv notificările}';

    /**
     * The console command description.
     */
    protected $description = 'Trimite reminder de expirare abonament meseriașilor (și notifică adminul)';

    public function handle(AdminNotificationService $adminNotifier): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $subscriptions = Subscription::whereIn('status', ['active', 'trial'])
            ->whereNotNull('ends_at')
            ->whereNull('expiry_reminder_sent_at')
            ->whereBetween('ends_at', [now(), now()->addDays($days)])
            ->with(['user', 'plan'])
            ->get();

        $this->info("Găsite {$subscriptions->count()} abonamente care expiră în următoarele {$days} zile.");

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;

            if (!$user) {
                continue;
            }

            if ($dryRun) {
                $this->line("  [DRY-RUN] Ar trimite reminder către {$user->email} (expiră {$subscription->ends_at->format('d.m.Y')})");
                continue;
            }

            $user->notify(new SubscriptionExpiringNotification($subscription));

            $adminNotifier->send(
                "Abonament pe cale să expire: {$user->name}",
                "Meseriașul {$user->name} ({$user->email}) are abonamentul \"{$subscription->plan->name}\" " .
                "care expiră pe {$subscription->ends_at->format('d.m.Y')}.\n\n" .
                "Vezi profilul: " . url('/admin/craftsmen/' . $user->id . '/edit')
            );

            $subscription->update(['expiry_reminder_sent_at' => now()]);

            $this->line("  Reminder trimis către {$user->email} (expiră {$subscription->ends_at->format('d.m.Y')})");
        }

        if ($dryRun) {
            $this->warn('Acesta a fost un dry-run. Nu s-a trimis nimic.');
        }

        return Command::SUCCESS;
    }
}
