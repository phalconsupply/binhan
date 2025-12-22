<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VehicleMaintenancesExport;
use App\Exports\VehicleTransactionsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'owner_or_permission:view vehicles'])->only(['index', 'show']);
        $this->middleware(['auth', 'owner_or_permission:create vehicles'])->only(['create', 'store']);
        $this->middleware(['auth', 'owner_or_permission:edit vehicles'])->only(['edit', 'update']);
        $this->middleware(['auth', 'owner_or_permission:delete vehicles'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vehicle::query();

        // Check if user is vehicle owner and limit to their vehicles
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            $query->whereIn('id', $ownedVehicleIds);
        }

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
        // Check if user is vehicle owner and has access to this vehicle
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($vehicle->id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền truy cập xe này.');
            }
        }

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
            'assets' => function($q) {
                $q->orderBy('equipped_date', 'desc');
            },
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
            $totalRevenue = $group->filter(function($t) { return $t->type === 'thu'; })->sum('amount');
            $totalExpense = $group->filter(function($t) { return $t->type === 'chi'; })->sum('amount');
            $totalPlannedExpense = $group->filter(function($t) { return $t->type === 'du_kien_chi'; })->sum('amount');
            $totalFundDeposit = $group->filter(function($t) { return $t->type === 'nop_quy'; })->sum('amount');
            
            // Nộp quỹ được cộng vào revenue nhưng KHÔNG tính phí 15%
            $netAmount = $totalRevenue - $totalExpense - $totalPlannedExpense + $totalFundDeposit;
            
            $hasOwner = $vehicle->hasOwner();
            
            // Chỉ tính management fee cho revenue thông thường, không tính cho Nộp quỹ
            $revenueForFee = $totalRevenue - $totalExpense - $totalPlannedExpense;
            $managementFee = ($hasOwner && $revenueForFee > 0) ? $revenueForFee * 0.15 : 0;
            $profitAfterFee = $netAmount - $managementFee;
            
            return [
                'incident' => $group->first()->incident,
                'vehicle' => $vehicle,
                'date' => $group->first()->date,
                'transactions' => $group,
                'total_revenue' => $totalRevenue,
                'total_expense' => $totalExpense,
                'total_planned_expense' => $totalPlannedExpense,
                'total_fund_deposit' => $totalFundDeposit,
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
            'total_fund_deposit' => (clone $statsQuery)->fundDeposit()->sum('amount'),
            'month_revenue' => $vehicle->transactions()->revenue()->thisMonth()->sum('amount'),
            'month_expense' => $vehicle->transactions()->expense()->thisMonth()->sum('amount'),
            'month_planned_expense' => $vehicle->transactions()->plannedExpense()->thisMonth()->sum('amount'),
            'month_fund_deposit' => $vehicle->transactions()->fundDeposit()->thisMonth()->sum('amount'),
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
            
            // Calculate net before owner costs (bao gồm fund deposit)
            $stats['total_net'] = $stats['total_revenue'] - $stats['total_expense_company'] - $stats['total_planned_expense'] + $stats['total_fund_deposit'];
            $stats['month_net'] = $stats['month_revenue'] - $stats['month_expense_company'] - $stats['month_planned_expense'] + $stats['month_fund_deposit'];
            
            // Calculate management fee (15% of profit BEFORE fund deposit, không tính phí cho Nộp quỹ)
            $totalNetBeforeFundDeposit = $stats['total_revenue'] - $stats['total_expense_company'] - $stats['total_planned_expense'];
            $monthNetBeforeFundDeposit = $stats['month_revenue'] - $stats['month_expense_company'] - $stats['month_planned_expense'];
            
            $totalManagementFee = $totalNetBeforeFundDeposit > 0 ? $totalNetBeforeFundDeposit * 0.15 : 0;
            $monthManagementFee = $monthNetBeforeFundDeposit > 0 ? $monthNetBeforeFundDeposit * 0.15 : 0;
            
            $stats['total_management_fee'] = $totalManagementFee;
            $stats['month_management_fee'] = $monthManagementFee;
            
            // Profit after fee and owner maintenance
            $stats['total_profit_after_fee'] = $stats['total_net'] - $totalManagementFee - $totalOwnerMaintenance;
            $stats['month_profit_after_fee'] = $stats['month_net'] - $monthManagementFee - $monthOwnerMaintenance;
            
            // Track owner maintenance separately
            $stats['total_owner_maintenance'] = $totalOwnerMaintenance;
            $stats['month_owner_maintenance'] = $monthOwnerMaintenance;
        } else {
            // Company vehicle: include all expenses and fund deposits
            $stats['total_net'] = $stats['total_revenue'] - $stats['total_expense'] - $stats['total_planned_expense'] + $stats['total_fund_deposit'];
            $stats['month_net'] = $stats['month_revenue'] - $stats['month_expense'] - $stats['month_planned_expense'] + $stats['month_fund_deposit'];
        }

        // Get recent incidents (4 most recent)
        $recentIncidents = $vehicle->incidents()
            ->with(['patient', 'dispatcher', 'transactions'])
            ->latest('date')
            ->take(4)
            ->get();

        return view('vehicles.show', compact('vehicle', 'stats', 'transactions', 'maintenances', 'totalMaintenanceCost', 'recentIncidents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        // Check if user is vehicle owner and has access to this vehicle
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($vehicle->id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền chỉnh sửa xe này.');
            }
        }

        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        // Check if user is vehicle owner and has access to this vehicle
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($vehicle->id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền cập nhật xe này.');
            }
        }

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
        // Check if user is vehicle owner and has access to this vehicle
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($vehicle->id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền xóa xe này.');
            }
        }

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
        // Check if user is vehicle owner and has access to this vehicle
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($vehicle->id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền xuất dữ liệu xe này.');
            }
        }

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
        // Check if user is vehicle owner and has access to this vehicle
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($vehicle->id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền xuất dữ liệu xe này.');
            }
        }

        $maintenances = $vehicle->vehicleMaintenances()
            ->with(['vehicle', 'maintenanceService', 'partner', 'user'])
            ->orderBy('date', 'desc')
            ->get();
        
        $totalCost = $maintenances->sum('cost');

        $pdf = Pdf::loadView('vehicle-maintenances.pdf', compact('maintenances', 'totalCost', 'vehicle'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('bao-tri-xe-' . $vehicle->license_plate . '-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export vehicle transactions to Excel
     */
    public function exportTransactions(Request $request, Vehicle $vehicle)
    {
        // Check if user is vehicle owner and only allow export their own vehicles
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($vehicle->id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền xuất dữ liệu xe này.');
            }
        }

        // Get filters from request
        $filters = [
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'transaction_type' => $request->input('transaction_type'),
        ];

        $fileName = 'giao-dich-' . $vehicle->license_plate . '-' . now()->format('Y-m-d-His') . '.xlsx';
        
        return Excel::download(new VehicleTransactionsExport($vehicle->id, $filters), $fileName);
    }
}

