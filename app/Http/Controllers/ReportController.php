<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Incident;
use App\Models\Transaction;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IncidentsExport;
use App\Exports\TransactionsExport;
use App\Exports\VehiclesReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view reports']);
    }

    /**
     * Display reports dashboard
     */
    public function index()
    {
        // Date range default: this month
        $dateFrom = request('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = request('date_to', now()->endOfMonth()->format('Y-m-d'));

        // Overall statistics
        $stats = [
            'total_vehicles' => Vehicle::count(),
            'active_vehicles' => Vehicle::active()->count(),
            'total_patients' => Patient::count(),
            'total_incidents' => Incident::whereBetween('date', [$dateFrom, $dateTo])->count(),
            'total_revenue' => Transaction::revenue()->whereBetween('date', [$dateFrom, $dateTo])->sum('amount'),
            'total_expense' => Transaction::expense()->whereBetween('date', [$dateFrom, $dateTo])->sum('amount'),
        ];
        $stats['net_profit'] = $stats['total_revenue'] - $stats['total_expense'];

        // Vehicle performance
        $vehicleStats = Vehicle::withCount([
            'incidents' => function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date', [$dateFrom, $dateTo]);
            }
        ])->withSum([
            'transactions as revenue' => function($q) use ($dateFrom, $dateTo) {
                $q->where('type', 'thu')->whereBetween('date', [$dateFrom, $dateTo]);
            }
        ], 'amount')
        ->withSum([
            'transactions as expense' => function($q) use ($dateFrom, $dateTo) {
                $q->where('type', 'chi')->whereBetween('date', [$dateFrom, $dateTo]);
            }
        ], 'amount')
        ->orderBy('incidents_count', 'desc')
        ->get();

        // Daily revenue/expense chart data
        $dailyStats = Transaction::whereBetween('date', [$dateFrom, $dateTo])
            ->select(
                DB::raw('DATE(date) as day'),
                DB::raw('SUM(CASE WHEN type = "thu" THEN amount ELSE 0 END) as revenue'),
                DB::raw('SUM(CASE WHEN type = "chi" THEN amount ELSE 0 END) as expense')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Top patients by incidents
        $topPatients = Patient::withCount([
            'incidents' => function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date', [$dateFrom, $dateTo]);
            }
        ])
        ->having('incidents_count', '>', 0)
        ->orderBy('incidents_count', 'desc')
        ->limit(10)
        ->get();

        $vehicles = Vehicle::orderBy('license_plate')->get();

        return view('reports.index', compact(
            'stats',
            'vehicleStats',
            'dailyStats',
            'topPatients',
            'vehicles',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Export incidents to Excel
     */
    public function exportIncidentsExcel(Request $request)
    {
        $this->authorize('export reports');

        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));
        $vehicleId = $request->input('vehicle_id');

        $filename = 'incidents_' . $dateFrom . '_to_' . $dateTo . '.xlsx';

        return Excel::download(
            new IncidentsExport($dateFrom, $dateTo, $vehicleId),
            $filename
        );
    }

    /**
     * Export transactions to Excel
     */
    public function exportTransactionsExcel(Request $request)
    {
        $this->authorize('export reports');

        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));
        $vehicleId = $request->input('vehicle_id');
        $type = $request->input('type');

        $filename = 'transactions_' . $dateFrom . '_to_' . $dateTo . '.xlsx';

        return Excel::download(
            new TransactionsExport($dateFrom, $dateTo, $vehicleId, $type),
            $filename
        );
    }

    /**
     * Export vehicle report to Excel
     */
    public function exportVehicleReportExcel(Request $request)
    {
        $this->authorize('export reports');

        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));

        $filename = 'vehicles_report_' . $dateFrom . '_to_' . $dateTo . '.xlsx';

        return Excel::download(
            new VehiclesReportExport($dateFrom, $dateTo),
            $filename
        );
    }

    /**
     * Export incidents to PDF
     */
    public function exportIncidentsPdf(Request $request)
    {
        $this->authorize('export reports');

        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));
        $vehicleId = $request->input('vehicle_id');

        $query = Incident::with(['vehicle', 'patient', 'dispatcher'])
            ->whereBetween('date', [$dateFrom, $dateTo]);

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        $incidents = $query->orderBy('date', 'desc')->get();

        $totals = [
            'count' => $incidents->count(),
            'revenue' => $incidents->sum('total_revenue'),
            'expense' => $incidents->sum('total_expense'),
        ];
        $totals['net'] = $totals['revenue'] - $totals['expense'];

        $pdf = Pdf::loadView('reports.pdf.incidents', compact('incidents', 'dateFrom', 'dateTo', 'totals'));
        
        return $pdf->download('incidents_' . $dateFrom . '_to_' . $dateTo . '.pdf');
    }

    /**
     * Export transactions to PDF
     */
    public function exportTransactionsPdf(Request $request)
    {
        $this->authorize('export reports');

        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));
        $vehicleId = $request->input('vehicle_id');
        $type = $request->input('type');

        $query = Transaction::with(['vehicle', 'incident.patient'])
            ->whereBetween('date', [$dateFrom, $dateTo]);

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        if ($type) {
            $query->where('type', $type);
        }

        $transactions = $query->orderBy('date', 'desc')->get();

        $totals = [
            'count' => $transactions->count(),
            'revenue' => $transactions->where('type', 'thu')->sum('amount'),
            'expense' => $transactions->where('type', 'chi')->sum('amount'),
        ];
        $totals['net'] = $totals['revenue'] - $totals['expense'];

        $pdf = Pdf::loadView('reports.pdf.transactions', compact('transactions', 'dateFrom', 'dateTo', 'totals'));
        
        return $pdf->download('transactions_' . $dateFrom . '_to_' . $dateTo . '.pdf');
    }
}
