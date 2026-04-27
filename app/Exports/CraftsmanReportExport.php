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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Carbon;

class CraftsmanReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected User $craftsman;
    protected $startDate;
    protected $endDate;
    protected string $reportType;

    public function __construct(User $craftsman, $startDate, $endDate, string $reportType = 'appointments')
    {
        $this->craftsman = $craftsman;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reportType = $reportType;
    }

    public function collection()
    {
        return match($this->reportType) {
            'appointments' => $this->getAppointmentsData(),
            'quotes' => $this->getQuotesData(),
            'reviews' => $this->getReviewsData(),
            'summary' => $this->getSummaryData(),
            default => collect([]),
        };
    }

    public function headings(): array
    {
        return match($this->reportType) {
            'appointments' => [
                'ID', 'Client', 'Data', 'Ora', 'Serviciu', 'Status', 'Adresă', 'Note', 'Creat la'
            ],
            'quotes' => [
                'ID', 'Client', 'Serviciu', 'Descriere', 'Sumă', 'Status', 'Validă până', 'Creat la'
            ],
            'reviews' => [
                'ID', 'Client', 'Rating', 'Titlu', 'Comentariu', 'Răspuns', 'Creat la'
            ],
            'summary' => [
                'Metrică', 'Valoare'
            ],
            default => [],
        };
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
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
        return match($this->reportType) {
            'appointments' => 'Programări',
            'quotes' => 'Oferte',
            'reviews' => 'Recenzii',
            'summary' => 'Sumar',
            default => 'Raport',
        };
    }

    protected function getAppointmentsData()
    {
        return Appointment::where('meserias_id', $this->craftsman->id)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->with(['user', 'service'])
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'client' => $a->user->name ?? 'N/A',
                'data' => Carbon::parse($a->date)->format('d.m.Y'),
                'ora' => $a->time,
                'serviciu' => $a->service->name ?? 'N/A',
                'status' => $this->translateStatus($a->status),
                'adresa' => $a->address ?? 'N/A',
                'note' => $a->notes ?? '',
                'creat_la' => $a->created_at->format('d.m.Y H:i'),
            ]);
    }

    protected function getQuotesData()
    {
        return Quote::where('meserias_id', $this->craftsman->id)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->with(['quoteRequest.user', 'quoteRequest.service'])
            ->get()
            ->map(fn($q) => [
                'id' => $q->id,
                'client' => $q->quoteRequest->user->name ?? 'N/A',
                'serviciu' => $q->quoteRequest->service->name ?? 'N/A',
                'descriere' => \Str::limit($q->description ?? '', 100),
                'suma' => number_format($q->amount, 2) . ' RON',
                'status' => $this->translateStatus($q->status),
                'valida' => $q->valid_until ? Carbon::parse($q->valid_until)->format('d.m.Y') : 'N/A',
                'creat_la' => $q->created_at->format('d.m.Y H:i'),
            ]);
    }

    protected function getReviewsData()
    {
        return Review::where('meserias_id', $this->craftsman->id)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->with('user')
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'client' => $r->user->name ?? 'Anonim',
                'rating' => $r->rating . '/5 ⭐',
                'titlu' => $r->title ?? '',
                'comentariu' => \Str::limit($r->comment ?? '', 200),
                'raspuns' => \Str::limit($r->specialist_response ?? '', 100),
                'creat_la' => $r->created_at->format('d.m.Y H:i'),
            ]);
    }

    protected function getSummaryData()
    {
        $appointments = Appointment::where('meserias_id', $this->craftsman->id)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get();

        $quotes = Quote::where('meserias_id', $this->craftsman->id)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get();

        $reviews = Review::where('meserias_id', $this->craftsman->id)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get();

        return collect([
            ['Perioada', Carbon::parse($this->startDate)->format('d.m.Y') . ' - ' . Carbon::parse($this->endDate)->format('d.m.Y')],
            ['', ''],
            ['PROGRAMĂRI', ''],
            ['Total Programări', $appointments->count()],
            ['Finalizate', $appointments->where('status', 'completed')->count()],
            ['Confirmate', $appointments->where('status', 'confirmed')->count()],
            ['În așteptare', $appointments->where('status', 'pending')->count()],
            ['Anulate', $appointments->where('status', 'cancelled')->count()],
            ['', ''],
            ['OFERTE', ''],
            ['Total Oferte', $quotes->count()],
            ['Acceptate', $quotes->where('status', 'accepted')->count()],
            ['În așteptare', $quotes->where('status', 'pending')->count()],
            ['Respinse', $quotes->where('status', 'rejected')->count()],
            ['Valoare Acceptate', number_format($quotes->where('status', 'accepted')->sum('amount'), 2) . ' RON'],
            ['', ''],
            ['RECENZII', ''],
            ['Total Recenzii', $reviews->count()],
            ['Rating Mediu', round($reviews->avg('rating'), 1) . '/5'],
            ['5 Stele', $reviews->where('rating', 5)->count()],
            ['4 Stele', $reviews->where('rating', 4)->count()],
            ['3 Stele', $reviews->where('rating', 3)->count()],
            ['2 Stele', $reviews->where('rating', 2)->count()],
            ['1 Stea', $reviews->where('rating', 1)->count()],
        ]);
    }

    protected function translateStatus(string $status): string
    {
        return match($status) {
            'pending' => 'În așteptare',
            'confirmed' => 'Confirmat',
            'completed' => 'Finalizat',
            'cancelled' => 'Anulat',
            'accepted' => 'Acceptat',
            'rejected' => 'Respins',
            'expired' => 'Expirat',
            default => $status,
        };
    }
}
