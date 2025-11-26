<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VehicleMaintenancesExport;
use Barryvdh\DomPDF\Facade\Pdf;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view vehicles'])->only(['index', 'show']);
        $this->middleware(['auth', 'permission:create vehicles'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:edit vehicles'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:delete vehicles'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vehicle::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('license_plate', 'like', "%{$search}%")
                  ->orWhere('driver_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $vehicles = $query->orderBy('license_plate')->paginate(15);

        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate',
            'model' => 'nullable|string|max:100',
            'driver_name' => 'nullable|string|max:100',
            'driver_id' => 'nullable|exists:staff,id',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,maintenance',
            'note' => 'nullable|string',
        ]);

        $vehicle = Vehicle::create($validated);

        return redirect()->route('vehicles.index')
            ->with('success', "Đã thêm xe {$vehicle->license_plate} thành công!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Vehicle $vehicle)
    {
        // Get filter parameters
        $type = $request->input('type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Load relationships with filters
        $vehicle->load([
            'driver',
            'owner',
            'incidents' => function($q) {
                $q->with(['patient', 'dispatcher'])->orderBy('date', 'desc')->limit(20);
            },
            'loanProfile.schedules',
            'loanProfile.interestAdjustments.creator',
        ]);

        // Get maintenance history with total cost
        $maintenancesQuery = $vehicle->vehicleMaintenances()
            ->with(['maintenanceService', 'partner', 'user', 'transaction'])
            ->orderBy('date', 'desc');
        
        $totalMaintenanceCost = (clone $maintenancesQuery)->sum('cost');
        $maintenances = $maintenancesQuery->paginate(10, ['*'], 'maintenance_page');

        // Build transactions query with filters
        $transactionsQuery = $vehicle->transactions()
            ->with(['incident.patient', 'recorder', 'vehicleMaintenance.maintenanceService', 'vehicleMaintenance.partner'])
            ->orderBy('date', 'desc');
        
        if ($type) {
            $transactionsQuery->where('type', $type);
        }
        
        if ($startDate) {
            $transactionsQuery->whereDate('date', '>=', $startDate);
        }
        
        if ($endDate) {
            $transactionsQuery->whereDate('date', '<=', $endDate);
        }

        // Get all filtered transactions
        $allTransactions = $transactionsQuery->get();

        // Group by incident_id (for incidents) or vehicle_maintenance_id (for maintenances)
        // Transactions without both will be grouped individually
        $groupedTransactions = $allTransactions->groupBy(function($transaction) {
            if ($transaction->incident_id) {
                return 'incident_' . $transaction->incident_id;
            } elseif ($transaction->vehicle_maintenance_id) {
                return 'maintenance_' . $transaction->vehicle_maintenance_id;
            } else {
                return 'other_' . $transaction->id;
            }
        })->map(function($group) use ($vehicle) {
            $totalRevenue = $group->where('type', 'thu')->sum('amount');
            $totalExpense = $group->where('type', 'chi')->sum('amount');
            $totalPlannedExpense = $group->where('type', 'du_kien_chi')->sum('amount');
            $netAmount = $totalRevenue - $totalExpense - $totalPlannedExpense;
            
            $hasOwner = $vehicle->hasOwner();
            $managementFee = ($hasOwner && $netAmount > 0) ? $netAmount * 0.15 : 0;
            $profitAfterFee = $netAmount - $managementFee;
            
            return [
                'incident' => $group->first()->incident,
                'vehicle' => $vehicle,
                'date' => $group->first()->date,
                'transactions' => $group,
                'total_revenue' => $totalRevenue,
                'total_expense' => $totalExpense,
                'total_planned_expense' => $totalPlannedExpense,
                'net_amount' => $netAmount,
                'has_owner' => $hasOwner,
                'management_fee' => $managementFee,
                'profit_after_fee' => $profitAfterFee,
            ];
        })->sortByDesc('date')->values();

        // Manual pagination
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $total = $groupedTransactions->count();
        $groupedTransactions = $groupedTransactions->forPage($currentPage, $perPage);

        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $groupedTransactions,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Statistics with filters
        $statsQuery = $vehicle->transactions();
        
        if ($startDate) {
            $statsQuery->whereDate('date', '>=', $startDate);
        }
        
        if ($endDate) {
            $statsQuery->whereDate('date', '<=', $endDate);
        }

        $stats = [
            'total_incidents' => $vehicle->incidents()->count(),
            'this_month_incidents' => $vehicle->incidents()->thisMonth()->count(),
            'total_revenue' => (clone $statsQuery)->revenue()->sum('amount'),
            'total_expense' => (clone $statsQuery)->expense()->sum('amount'),
            'total_planned_expense' => (clone $statsQuery)->plannedExpense()->sum('amount'),
            'month_revenue' => $vehicle->transactions()->revenue()->thisMonth()->sum('amount'),
            'month_expense' => $vehicle->transactions()->expense()->thisMonth()->sum('amount'),
            'month_planned_expense' => $vehicle->transactions()->plannedExpense()->thisMonth()->sum('amount'),
        ];

        // For vehicles with owner, separate owner maintenance costs
        $stats['has_owner'] = $vehicle->hasOwner();
        if ($stats['has_owner']) {
            // Get maintenance costs for owner's vehicle (deducted from vehicle profit, not company)
            $totalOwnerMaintenance = (clone $statsQuery)->expense()->where('category', 'bảo_trì_xe_chủ_riêng')->sum('amount');
            $monthOwnerMaintenance = $vehicle->transactions()->expense()->thisMonth()->where('category', 'bảo_trì_xe_chủ_riêng')->sum('amount');
            
            // Adjust expenses: remove owner maintenance from company expenses
            $stats['total_expense_company'] = $stats['total_expense'] - $totalOwnerMaintenance;
            $stats['month_expense_company'] = $stats['month_expense'] - $monthOwnerMaintenance;
            
            // Calculate net before owner costs
            $stats['total_net'] = $stats['total_revenue'] - $stats['total_expense_company'] - $stats['total_planned_expense'];
            $stats['month_net'] = $stats['month_revenue'] - $stats['month_expense_company'] - $stats['month_planned_expense'];
            
            // Calculate management fee (15% of profit before owner maintenance)
            $totalManagementFee = $stats['total_net'] > 0 ? $stats['total_net'] * 0.15 : 0;
            $monthManagementFee = $stats['month_net'] > 0 ? $stats['month_net'] * 0.15 : 0;
            
            $stats['total_management_fee'] = $totalManagementFee;
            $stats['month_management_fee'] = $monthManagementFee;
            
            // Profit after fee and owner maintenance
            $stats['total_profit_after_fee'] = $stats['total_net'] - $totalManagementFee - $totalOwnerMaintenance;
            $stats['month_profit_after_fee'] = $stats['month_net'] - $monthManagementFee - $monthOwnerMaintenance;
            
            // Track owner maintenance separately
            $stats['total_owner_maintenance'] = $totalOwnerMaintenance;
            $stats['month_owner_maintenance'] = $monthOwnerMaintenance;
        } else {
            // Company vehicle: include all expenses
            $stats['total_net'] = $stats['total_revenue'] - $stats['total_expense'] - $stats['total_planned_expense'];
            $stats['month_net'] = $stats['month_revenue'] - $stats['month_expense'] - $stats['month_planned_expense'];
        }

        return view('vehicles.show', compact('vehicle', 'stats', 'transactions', 'maintenances', 'totalMaintenanceCost'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate,' . $vehicle->id,
            'model' => 'nullable|string|max:100',
            'driver_name' => 'nullable|string|max:100',
            'driver_id' => 'nullable|exists:staff,id',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,maintenance',
            'note' => 'nullable|string',
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', "Đã cập nhật xe {$vehicle->license_plate} thành công!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $licensePlate = $vehicle->license_plate;
        
        // Check if vehicle has incidents
        if ($vehicle->incidents()->count() > 0) {
            return redirect()->route('vehicles.index')
                ->with('error', "Không thể xóa xe {$licensePlate} vì đã có chuyến đi liên quan!");
        }

        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', "Đã xóa xe {$licensePlate} thành công!");
    }

    /**
     * Export vehicle maintenances to Excel
     */
    public function exportMaintenancesExcel(Vehicle $vehicle)
    {
        $maintenances = $vehicle->vehicleMaintenances()
            ->with(['vehicle', 'maintenanceService', 'partner', 'user'])
            ->orderBy('date', 'desc')
            ->get();
        
        $totalCost = $maintenances->sum('cost');

        return Excel::download(
            new VehicleMaintenancesExport($maintenances, $totalCost), 
            'bao-tri-xe-' . $vehicle->license_plate . '-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export vehicle maintenances to PDF
     */
    public function exportMaintenancesPdf(Vehicle $vehicle)
    {
        $maintenances = $vehicle->vehicleMaintenances()
            ->with(['vehicle', 'maintenanceService', 'partner', 'user'])
            ->orderBy('date', 'desc')
            ->get();
        
        $totalCost = $maintenances->sum('cost');

        $pdf = Pdf::loadView('vehicle-maintenances.pdf', compact('maintenances', 'totalCost', 'vehicle'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('bao-tri-xe-' . $vehicle->license_plate . '-' . now()->format('Y-m-d') . '.pdf');
    }
}
