<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\CraftsmanLead;
use App\Models\PublicJobRequest;
use App\Models\Review;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;

class SendAdminDailyDigest extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'admin:daily-digest {--dry-run : Preview fără a trimite efectiv email-ul}';

    /**
     * The console command description.
     */
    protected $description = 'Trimite adminului un sumar zilnic cu activitatea platformei (lead-uri, cereri, recenzii, programări)';

    public function handle(AdminNotificationService $adminNotifier): int
    {
        $newLeads = CraftsmanLead::where('created_at', '>=', now()->subDay())->count();

        $openJobRequests = PublicJobRequest::where('status', 'open')->count();

        $jobRequestsWithoutInterest = PublicJobRequest::where('status', 'open')
            ->withCount(['responses as interested_count' => fn ($q) => $q->where('action', 'interested')])
            ->having('interested_count', '=', 0)
            ->get()
            ->count();

        $pendingReviews = Review::where('is_approved', false)->count();

        $pendingAppointments = Appointment::where('status', 'pending')->count();

        $body = "Sumarul zilei pentru meseriasionline.ro:\n\n" .
            "- Lead-uri noi de recrutare (ultimele 24h): {$newLeads}\n" .
            "- Cereri publice deschise: {$openJobRequests} (din care fără niciun meseriaș interesat: {$jobRequestsWithoutInterest})\n" .
            "- Recenzii în așteptare de aprobare: {$pendingReviews}\n" .
            "- Programări în așteptare: {$pendingAppointments}\n\n" .
            "Dashboard: " . url('/admin');

        if ($this->option('dry-run')) {
            $this->line($body);
            $this->warn('Acesta a fost un dry-run. Nu s-a trimis nimic.');
            return Command::SUCCESS;
        }

        $adminNotifier->send('Sumar zilnic meseriasionline.ro', $body);

        $this->info('Digest zilnic trimis către admin.');

        return Command::SUCCESS;
    }
}
