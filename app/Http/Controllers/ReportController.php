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
        $this->middleware(['auth', 'owner_or_permission:view reports']);
    }

    /**
     * Display reports dashboard
     */
    public function index()
    {
        // Check if user is vehicle owner
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        $ownedVehicleIds = [];
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
        }

        // Date range default: this month
        $dateFrom = request('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = request('date_to', now()->endOfMonth()->format('Y-m-d'));

        // Overall statistics (scoped to owner's vehicles if owner)
        $incidentsQuery = Incident::whereBetween('date', [$dateFrom, $dateTo]);
        $transactionsQuery = Transaction::whereBetween('date', [$dateFrom, $dateTo]);
        
        if ($isVehicleOwner && !empty($ownedVehicleIds)) {
            $incidentsQuery->whereIn('vehicle_id', $ownedVehicleIds);
            $transactionsQuery->whereIn('vehicle_id', $ownedVehicleIds);
        }

        $statistics = [
            'total_incidents' => (clone $incidentsQuery)->count(),
            'total_revenue' => (clone $transactionsQuery)->revenue()->sum('amount'),
            'total_expense' => (clone $transactionsQuery)->expense()->sum('amount'),
            'total_planned_expense' => (clone $transactionsQuery)->plannedExpense()->sum('amount'),
        ];
        $statistics['net_profit'] = $statistics['total_revenue'] - $statistics['total_expense'] - $statistics['total_planned_expense'];

        // Vehicle performance (scoped to owner's vehicles if owner)
        $vehicleQuery = Vehicle::query();
        if ($isVehicleOwner && !empty($ownedVehicleIds)) {
            $vehicleQuery->whereIn('id', $ownedVehicleIds);
        }
        
        $vehiclePerformance = $vehicleQuery->withCount([
            'incidents' => function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date', [$dateFrom, $dateTo]);
            }
        ])->withSum([
            'transactions as total_revenue' => function($q) use ($dateFrom, $dateTo) {
                $q->where('type', 'thu')->whereBetween('date', [$dateFrom, $dateTo]);
            }
        ], 'amount')
        ->withSum([
            'transactions as total_expense' => function($q) use ($dateFrom, $dateTo) {
                $q->where('type', 'chi')->whereBetween('date', [$dateFrom, $dateTo]);
            }
        ], 'amount')
        ->withSum([
            'transactions as total_planned_expense' => function($q) use ($dateFrom, $dateTo) {
                $q->where('type', 'du_kien_chi')->whereBetween('date', [$dateFrom, $dateTo]);
            }
        ], 'amount')
        ->get()
        ->map(function($vehicle) {
            $vehicle->net_profit = ($vehicle->total_revenue ?? 0) - ($vehicle->total_expense ?? 0) - ($vehicle->total_planned_expense ?? 0);
            return $vehicle;
        })
        ->sortByDesc('incidents_count');

        // Daily revenue/expense chart data (scoped to owner's vehicles if owner)
        $dailyRevenueQuery = Transaction::whereBetween('date', [$dateFrom, $dateTo]);
        if ($isVehicleOwner && !empty($ownedVehicleIds)) {
            $dailyRevenueQuery->whereIn('vehicle_id', $ownedVehicleIds);
        }
        
        $dailyRevenue = $dailyRevenueQuery->select(
                DB::raw('DATE(date) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN type = "thu" THEN amount ELSE 0 END) as revenue'),
                DB::raw('SUM(CASE WHEN type = "chi" THEN amount ELSE 0 END) as expense'),
                DB::raw('SUM(CASE WHEN type = "du_kien_chi" THEN amount ELSE 0 END) as planned_expense'),
                DB::raw('SUM(CASE WHEN type = "thu" THEN amount WHEN type = "chi" THEN -amount WHEN type = "du_kien_chi" THEN -amount ELSE 0 END) as net')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top patients by incidents (scoped to owner's vehicles if owner)
        $topPatientsQuery = Patient::query();
        
        if ($isVehicleOwner && !empty($ownedVehicleIds)) {
            $topPatientsQuery->whereHas('incidents', function($q) use ($ownedVehicleIds, $dateFrom, $dateTo) {
                $q->whereIn('vehicle_id', $ownedVehicleIds)
                  ->whereBetween('date', [$dateFrom, $dateTo]);
            });
        }
        
        $topPatients = $topPatientsQuery->withCount([
            'incidents' => function($q) use ($dateFrom, $dateTo, $isVehicleOwner, $ownedVehicleIds) {
                $q->whereBetween('date', [$dateFrom, $dateTo]);
                if ($isVehicleOwner && !empty($ownedVehicleIds)) {
                    $q->whereIn('vehicle_id', $ownedVehicleIds);
                }
            }
        ])
        ->with(['incidents' => function($q) use ($dateFrom, $dateTo, $isVehicleOwner, $ownedVehicleIds) {
            $q->whereBetween('date', [$dateFrom, $dateTo]);
            if ($isVehicleOwner && !empty($ownedVehicleIds)) {
                $q->whereIn('vehicle_id', $ownedVehicleIds);
            }
        }])
        ->having('incidents_count', '>', 0)
        ->orderBy('incidents_count', 'desc')
        ->limit(10)
        ->get()
        ->map(function($patient) {
            $patient->total_spent = $patient->incidents->sum('total_revenue');
            return $patient;
        });

        return view('reports.index', compact(
            'statistics',
            'vehiclePerformance',
            'dailyRevenue',
            'topPatients',
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

        // Check if user is vehicle owner
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        $ownedVehicleIds = [];
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
        }

        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));
        $vehicleId = $request->input('vehicle_id');
        
        // Restrict vehicle_id for owners
        if ($isVehicleOwner && !empty($ownedVehicleIds)) {
            if ($vehicleId && !in_array($vehicleId, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền xuất báo cáo xe này.');
            }
        }

        $filename = 'incidents_' . $dateFrom . '_to_' . $dateTo . '.xlsx';

        return Excel::download(
            new IncidentsExport($dateFrom, $dateTo, $vehicleId, $ownedVehicleIds),
            $filename
        );
    }

    /**
     * Export transactions to Excel
     */
    public function exportTransactionsExcel(Request $request)
    {
        $this->authorize('export reports');

        // Check if user is vehicle owner
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        $ownedVehicleIds = [];
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
        }

        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));
        $vehicleId = $request->input('vehicle_id');
        $type = $request->input('type');
        
        // Restrict vehicle_id for owners
        if ($isVehicleOwner && !empty($ownedVehicleIds)) {
            if ($vehicleId && !in_array($vehicleId, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền xuất báo cáo xe này.');
            }
        }

        $filename = 'transactions_' . $dateFrom . '_to_' . $dateTo . '.xlsx';

        return Excel::download(
            new TransactionsExport($dateFrom, $dateTo, $vehicleId, $type, $ownedVehicleIds),
            $filename
        );
    }

    /**
     * Export vehicle report to Excel
     */
    public function exportVehicleReportExcel(Request $request)
    {
        $this->authorize('export reports');

        // Check if user is vehicle owner
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        $ownedVehicleIds = [];
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
        }

        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));

        $filename = 'vehicles_report_' . $dateFrom . '_to_' . $dateTo . '.xlsx';

        return Excel::download(
            new VehiclesReportExport($dateFrom, $dateTo, $ownedVehicleIds),
            $filename
        );
    }

    /**
     * Export incidents to PDF
     */
    public function exportIncidentsPdf(Request $request)
    {
        $this->authorize('export reports');

        // Check if user is vehicle owner
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        $ownedVehicleIds = [];
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
        }

        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));
        $vehicleId = $request->input('vehicle_id');

        $query = Incident::with([
            'vehicle', 
            'patient', 
            'dispatcher',
            'fromLocation',
            'toLocation',
            'drivers.user',
            'medicalStaff.user',
            'partner'
        ])->whereBetween('date', [$dateFrom, $dateTo]);

        // Scope to owner's vehicles if owner
        if ($isVehicleOwner && !empty($ownedVehicleIds)) {
            $query->whereIn('vehicle_id', $ownedVehicleIds);
        }

        if ($vehicleId) {
            // Check access for owner
            if ($isVehicleOwner && !in_array($vehicleId, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền xuất báo cáo xe này.');
            }
            $query->where('vehicle_id', $vehicleId);
        }

        // Sort by from_location name, then by date (oldest to newest)
        $incidents = $query->join('locations', 'incidents.from_location_id', '=', 'locations.id')
            ->select('incidents.*')
            ->orderBy('locations.name', 'asc')
            ->orderBy('incidents.date', 'asc')
            ->get();
        
        // Re-load relationships after join (join can break eager loading)
        $incidents->load([
            'vehicle', 
            'patient', 
            'dispatcher',
            'fromLocation',
            'toLocation',
            'drivers.user',
            'medicalStaff.user',
            'partner'
        ]);

        $totals = [
            'count' => $incidents->count(),
        ];

        $pdf = Pdf::loadView('reports.pdf.incidents', compact('incidents', 'dateFrom', 'dateTo', 'totals'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('bao_cao_chuyen_vien_' . $dateFrom . '_to_' . $dateTo . '.pdf');
    }

    /**
     * Export transactions to PDF
     */
    public function exportTransactionsPdf(Request $request)
    {
        $this->authorize('export reports');

        // Check if user is vehicle owner
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        $ownedVehicleIds = [];
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
        }

        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));
        $vehicleId = $request->input('vehicle_id');
        $type = $request->input('type');

        $query = Transaction::with(['vehicle', 'incident.patient'])
            ->whereBetween('date', [$dateFrom, $dateTo]);

        // Scope to owner's vehicles if owner
        if ($isVehicleOwner && !empty($ownedVehicleIds)) {
            $query->whereIn('vehicle_id', $ownedVehicleIds);
        }

        if ($vehicleId) {
            // Check access for owner
            if ($isVehicleOwner && !in_array($vehicleId, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền xuất báo cáo xe này.');
            }
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
