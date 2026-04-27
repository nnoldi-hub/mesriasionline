<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ReportExportService;
use App\Exports\CraftsmanReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    protected ReportExportService $reportService;

    public function __construct(ReportExportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Show report options for craftsman
     */
    public function craftsmanReports()
    {
        return view('reports.craftsman-reports');
    }

    /**
     * Export craftsman report as PDF
     */
    public function exportCraftsmanPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $craftsman = Auth::user();
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $pdf = $this->reportService->generateCraftsmanReport($craftsman, $startDate, $endDate);

        return $pdf->download('raport-meserias-' . $startDate->format('Y-m-d') . '-' . $endDate->format('Y-m-d') . '.pdf');
    }

    /**
     * Export craftsman report as Excel
     */
    public function exportCraftsmanExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:appointments,quotes,reviews,summary',
        ]);

        $craftsman = Auth::user();
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $type = $request->type;

        $filename = 'raport-' . $type . '-' . $startDate->format('Y-m-d') . '-' . $endDate->format('Y-m-d') . '.xlsx';

        return Excel::download(new CraftsmanReportExport($craftsman, $startDate, $endDate, $type), $filename);
    }

    /**
     * Show report options for client
     */
    public function clientReports()
    {
        return view('reports.client-reports');
    }

    /**
     * Export client report as PDF
     */
    public function exportClientPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $client = Auth::user();
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $pdf = $this->reportService->generateClientReport($client, $startDate, $endDate);

        return $pdf->download('raport-activitate-' . $startDate->format('Y-m-d') . '-' . $endDate->format('Y-m-d') . '.pdf');
    }

    /**
     * Export affiliate report as PDF
     */
    public function exportAffiliatePdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = Auth::user();
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $data = $this->reportService->getAffiliateReportData($user, $startDate, $endDate);

        if (isset($data['error'])) {
            return back()->with('error', $data['error']);
        }

        $pdf = Pdf::loadView('reports.pdf.affiliate-report', $data);

        return $pdf->download('raport-afiliere-' . $startDate->format('Y-m-d') . '-' . $endDate->format('Y-m-d') . '.pdf');
    }
}
