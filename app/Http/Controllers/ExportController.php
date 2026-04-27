<?php

namespace App\Http\Controllers;

use App\Exports\CraftsmanReportExport;
use App\Exports\PlatformReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Review;

class ExportController extends Controller
{
    /**
     * Export appointments to Excel/CSV
     */
    public function exportAppointments(Request $request)
    {
        $request->validate([
            'format' => 'sometimes|in:xlsx,csv',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled',
        ]);

        $query = Appointment::with(['client', 'craftsman', 'service']);

        // Apply filters
        if ($request->start_date) {
            $query->where('scheduled_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('scheduled_at', '<=', $request->end_date);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Only show user's own appointments
        if (auth()->user()->role === 'craftsman') {
            $query->where('craftsman_id', auth()->id());
        } elseif (auth()->user()->role === 'client') {
            $query->where('client_id', auth()->id());
        }

        $appointments = $query->get();

        // Prepare data for export
        $data = $appointments->map(function ($appointment) {
            return [
                'ID' => $appointment->id,
                'Client' => $appointment->client->name,
                'Meseriaș' => $appointment->craftsman->name,
                'Serviciu' => $appointment->service->name ?? '-',
                'Data programare' => $appointment->scheduled_at->format('d.m.Y H:i'),
                'Status' => $this->translateStatus($appointment->status),
                'Preț' => $appointment->price ? number_format($appointment->price, 2) . ' RON' : '-',
                'Notițe' => $appointment->notes ?? '-',
                'Creat la' => $appointment->created_at->format('d.m.Y H:i'),
            ];
        });

        $format = $request->input('format', 'xlsx');
        $filename = 'programari_' . now()->format('Y-m-d') . '.' . $format;

        return Excel::download(
            new \Maatwebsite\Excel\Concerns\FromCollection(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $data;
                public function __construct($data) { $this->data = $data; }
                public function collection() { return $this->data; }
                public function headings(): array {
                    return ['ID', 'Client', 'Meseriaș', 'Serviciu', 'Data programare', 'Status', 'Preț', 'Notițe', 'Creat la'];
                }
            }),
            $filename
        );
    }

    /**
     * Export reviews to Excel/CSV
     */
    public function exportReviews(Request $request)
    {
        $request->validate([
            'format' => 'sometimes|in:xlsx,csv',
            'rating' => 'sometimes|integer|min:1|max:5',
        ]);

        $query = Review::with(['client', 'craftsman']);

        // Filter by rating if provided
        if ($request->rating) {
            $query->where('rating', $request->rating);
        }

        // Only show reviews for craftsman
        if (auth()->user()->role === 'craftsman') {
            $query->where('craftsman_id', auth()->id());
        }

        $reviews = $query->latest()->get();

        // Prepare data for export
        $data = $reviews->map(function ($review) {
            return [
                'ID' => $review->id,
                'Client' => $review->client->name,
                'Meseriaș' => $review->craftsman->name,
                'Rating' => str_repeat('★', $review->rating) . str_repeat('☆', 5 - $review->rating),
                'Rating numeric' => $review->rating,
                'Comentariu' => $review->comment ?? '-',
                'Data' => $review->created_at->format('d.m.Y H:i'),
            ];
        });

        $format = $request->input('format', 'xlsx');
        $filename = 'recenzii_' . now()->format('Y-m-d') . '.' . $format;

        return Excel::download(
            new \Maatwebsite\Excel\Concerns\FromCollection(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $data;
                public function __construct($data) { $this->data = $data; }
                public function collection() { return $this->data; }
                public function headings(): array {
                    return ['ID', 'Client', 'Meseriaș', 'Rating', 'Rating numeric', 'Comentariu', 'Data'];
                }
            }),
            $filename
        );
    }

    /**
     * Export craftsmen list (admin only)
     */
    public function exportCraftsmen(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $request->validate([
            'format' => 'sometimes|in:xlsx,csv',
        ]);

        $craftsmen = User::where('role', 'craftsman')
            ->with(['services', 'location'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->get();

        // Prepare data for export
        $data = $craftsmen->map(function ($craftsman) {
            return [
                'ID' => $craftsman->id,
                'Nume' => $craftsman->name,
                'Email' => $craftsman->email,
                'Telefon' => $craftsman->phone ?? '-',
                'Locație' => $craftsman->location->name ?? '-',
                'Servicii' => $craftsman->services->pluck('name')->join(', '),
                'Rating mediu' => $craftsman->reviews_avg_rating ? number_format($craftsman->reviews_avg_rating, 2) : '-',
                'Nr. recenzii' => $craftsman->reviews_count,
                'Activ' => $craftsman->is_active ? 'Da' : 'Nu',
                'Înregistrat la' => $craftsman->created_at->format('d.m.Y'),
            ];
        });

        $format = $request->input('format', 'xlsx');
        $filename = 'meseriasi_' . now()->format('Y-m-d') . '.' . $format;

        return Excel::download(
            new \Maatwebsite\Excel\Concerns\FromCollection(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $data;
                public function __construct($data) { $this->data = $data; }
                public function collection() { return $this->data; }
                public function headings(): array {
                    return ['ID', 'Nume', 'Email', 'Telefon', 'Locație', 'Servicii', 'Rating mediu', 'Nr. recenzii', 'Activ', 'Înregistrat la'];
                }
            }),
            $filename
        );
    }

    /**
     * Translate appointment status to Romanian
     */
    protected function translateStatus(string $status): string
    {
        return match($status) {
            'pending' => 'În așteptare',
            'confirmed' => 'Confirmat',
            'completed' => 'Finalizat',
            'cancelled' => 'Anulat',
            default => $status,
        };
    }
}
