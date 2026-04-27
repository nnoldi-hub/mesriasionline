<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Quote;
use App\Models\Review;
use App\Models\PlatformDailyStat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Carbon;

class PlatformReportExport implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        return [
            new PlatformSummarySheet($this->startDate, $this->endDate),
            new DailyStatsSheet($this->startDate, $this->endDate),
            new UsersSheet($this->startDate, $this->endDate),
            new TopCraftsmenSheet($this->startDate, $this->endDate),
        ];
    }
}

class PlatformSummarySheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $aggregated = PlatformDailyStat::getAggregatedStats($this->startDate, $this->endDate);

        $totalCraftsmen = User::where('user_type', 'meserias')->count();
        $totalClients = User::where('user_type', 'client')->count();
        $activeCraftsmen = User::where('user_type', 'meserias')
            ->where('last_login_at', '>=', now()->subDays(30))
            ->count();

        return collect([
            ['Perioada', Carbon::parse($this->startDate)->format('d.m.Y') . ' - ' . Carbon::parse($this->endDate)->format('d.m.Y')],
            ['Generat la', now()->format('d.m.Y H:i')],
            ['', ''],
            ['TRAFIC', ''],
            ['Vizite Totale', number_format($aggregated['total_visits'])],
            ['Vizitatori Unici', number_format($aggregated['unique_visitors'])],
            ['Pagini Vizualizate', number_format($aggregated['page_views'])],
            ['', ''],
            ['UTILIZATORI', ''],
            ['Total Meșteri', number_format($totalCraftsmen)],
            ['Total Clienți', number_format($totalClients)],
            ['Meșteri Activi (30 zile)', number_format($activeCraftsmen)],
            ['Înregistrări Noi', number_format($aggregated['new_registrations'])],
            ['Meșteri Noi', number_format($aggregated['new_craftsmen'])],
            ['Clienți Noi', number_format($aggregated['new_clients'])],
            ['', ''],
            ['ANGAJAMENT', ''],
            ['Profiluri Vizualizate', number_format($aggregated['profile_views'])],
            ['Mesaje Trimise', number_format($aggregated['messages_sent'])],
            ['Cereri Ofertă', number_format($aggregated['quote_requests'])],
            ['Oferte Trimise', number_format($aggregated['quotes_sent'])],
            ['Oferte Acceptate', number_format($aggregated['quotes_accepted'])],
            ['Programări', number_format($aggregated['appointments_booked'])],
            ['Recenzii', number_format($aggregated['reviews_submitted'])],
            ['', ''],
            ['RATE CONVERSIE', ''],
            ['Vizită → Contact', round($aggregated['avg_visit_to_contact_rate'], 2) . '%'],
            ['Contact → Ofertă', round($aggregated['avg_contact_to_quote_rate'], 2) . '%'],
            ['Ofertă → Booking', round($aggregated['avg_quote_to_booking_rate'], 2) . '%'],
        ]);
    }

    public function headings(): array
    {
        return ['Metrică', 'Valoare'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    public function title(): string
    {
        return 'Sumar';
    }
}

class DailyStatsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return PlatformDailyStat::whereBetween('date', [$this->startDate, $this->endDate])
            ->orderBy('date')
            ->get()
            ->map(fn($s) => [
                'data' => $s->date->format('d.m.Y'),
                'vizite' => $s->total_visits,
                'unici' => $s->unique_visitors,
                'pagini' => $s->page_views,
                'inregistrari' => $s->new_registrations,
                'profiluri' => $s->profile_views,
                'mesaje' => $s->messages_sent,
                'cereri' => $s->quote_requests,
                'oferte' => $s->quotes_sent,
                'acceptate' => $s->quotes_accepted,
                'programari' => $s->appointments_booked,
                'recenzii' => $s->reviews_submitted,
            ]);
    }

    public function headings(): array
    {
        return [
            'Data', 'Vizite', 'Unici', 'Pagini', 'Înreg.', 
            'Profiluri', 'Mesaje', 'Cereri', 'Oferte', 'Accept.', 'Progr.', 'Recenzii'
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    public function title(): string
    {
        return 'Statistici Zilnice';
    }
}

class UsersSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return User::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'desc')
            ->limit(500)
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'nume' => $u->name,
                'email' => $u->email,
                'tip' => $u->user_type === 'meserias' ? 'Meșter' : 'Client',
                'locatie' => $u->city ?? 'N/A',
                'verificat' => $u->email_verified_at ? 'Da' : 'Nu',
                'creat' => $u->created_at->format('d.m.Y H:i'),
            ]);
    }

    public function headings(): array
    {
        return ['ID', 'Nume', 'Email', 'Tip', 'Locație', 'Verificat', 'Înregistrat'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    public function title(): string
    {
        return 'Utilizatori Noi';
    }
}

class TopCraftsmenSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return User::where('user_type', 'meserias')
            ->withCount(['reviewsReceived as reviews_count' => function($q) {
                $q->whereBetween('created_at', [$this->startDate, $this->endDate]);
            }])
            ->withAvg(['reviewsReceived as avg_rating' => function($q) {
                $q->whereBetween('created_at', [$this->startDate, $this->endDate]);
            }], 'rating')
            ->withCount(['appointments as appointments_count' => function($q) {
                $q->whereBetween('created_at', [$this->startDate, $this->endDate]);
            }])
            ->having('reviews_count', '>', 0)
            ->orderByDesc('reviews_count')
            ->limit(50)
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'nume' => $u->name,
                'email' => $u->email,
                'categorie' => $u->categories->first()->name ?? 'N/A',
                'locatie' => $u->city ?? 'N/A',
                'rating' => round($u->avg_rating ?? 0, 1) . '/5',
                'recenzii' => $u->reviews_count,
                'programari' => $u->appointments_count,
            ]);
    }

    public function headings(): array
    {
        return ['ID', 'Nume', 'Email', 'Categorie', 'Locație', 'Rating', 'Recenzii', 'Programări'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    public function title(): string
    {
        return 'Top Meșteri';
    }
}
