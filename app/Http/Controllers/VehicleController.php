<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VehicleMaintenancesExport;
use App\Exports\VehicleTransactionsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        // Exclude maintenance transactions as they're already shown in the maintenance table
        $transactionsQuery = $vehicle->transactions()
            ->with(['incident.patient', 'recorder', 'vehicleMaintenance.maintenanceService', 'vehicleMaintenance.partner'])
            ->whereNull('vehicle_maintenance_id')  // Exclude maintenance transactions
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
        // All other transactions go into a single "other" group
        $groupedTransactions = $allTransactions->groupBy(function($transaction) {
            if ($transaction->incident_id) {
                return 'incident_' . $transaction->incident_id;
            } elseif ($transaction->vehicle_maintenance_id) {
                return 'maintenance_' . $transaction->vehicle_maintenance_id;
            } else {
                // All other transactions (vay_cong_ty, tra_cong_ty, nop_quy, thu, chi, etc.) 
                // are grouped together into one single group
                return 'other';
            }
        })->map(function($group) use ($vehicle) {
            // Determine which transaction types are revenue (positive) vs expense (negative)
            $revenueTypes = ['thu', 'vay_cong_ty', 'nop_quy'];
            $expenseTypes = ['chi', 'tra_cong_ty', 'du_kien_chi'];
            
            $totalRevenue = $group->filter(function($t) use ($revenueTypes) { 
                return in_array($t->type, $revenueTypes);
            })->sum('amount');
            
            $totalExpense = $group->filter(function($t) use ($expenseTypes) { 
                return in_array($t->type, $expenseTypes);
            })->sum('amount');
            
            $totalPlannedExpense = $group->filter(function($t) { return $t->type === 'du_kien_chi'; })->sum('amount');
            $totalFundDeposit = $group->filter(function($t) { return $t->type === 'nop_quy'; })->sum('amount');
            $totalBorrowed = $group->filter(function($t) { return $t->type === 'vay_cong_ty'; })->sum('amount');
            
            // Nộp quỹ được cộng vào revenue nhưng KHÔNG tính phí 15%
            $netAmount = $totalRevenue - $totalExpense;
            
            $hasOwner = $vehicle->hasOwner();
            
            // Chỉ tính management fee cho revenue thực sự (thu), không tính cho Nộp quỹ, Vay công ty và thu từ vay
            $realRevenue = $group->filter(function($t) { 
                return $t->type === 'thu' && $t->category !== 'vay_từ_công_ty'; 
            })->sum('amount');
            $realExpense = $group->filter(function($t) { return $t->type === 'chi'; })->sum('amount');
            $revenueForFee = $realRevenue - $realExpense - $totalPlannedExpense;
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
            
            // BƯỚC 1: Tổng thu = thu + nộp quỹ + vay công ty (TẤT CẢ tiền vào)
            $totalBorrowed = (clone $statsQuery)->borrowFromCompany()->sum('amount');
            $monthBorrowed = $vehicle->transactions()->borrowFromCompany()->thisMonth()->sum('amount');
            
            $stats['total_revenue_display'] = $stats['total_revenue'] + $stats['total_fund_deposit'] + $totalBorrowed;
            $stats['month_revenue_display'] = $stats['month_revenue'] + $stats['month_fund_deposit'] + $monthBorrowed;
            
            // BƯỚC 2: Tổng chi = chi + trả nợ + phí 15% cho công ty (TẤT CẢ tiền ra)
            $totalReturned = (clone $statsQuery)->returnToCompany()->sum('amount');
            $monthReturned = $vehicle->transactions()->returnToCompany()->thisMonth()->sum('amount');
            
            // Tính phí 15% cho công ty (từ tổng thu, LOẠI TRỪ thu từ vay)
            $totalRevenueForFee = (clone $statsQuery)->revenue()
                ->where(function($q) {
                    $q->where('category', '!=', 'vay_từ_công_ty')
                      ->orWhereNull('category');
                })->sum('amount');
            $monthRevenueForFee = $vehicle->transactions()->revenue()->thisMonth()
                ->where(function($q) {
                    $q->where('category', '!=', 'vay_từ_công_ty')
                      ->orWhereNull('category');
                })->sum('amount');
                
            $companyFee = $totalRevenueForFee * 0.15;
            $monthCompanyFee = $monthRevenueForFee * 0.15;
            
            $stats['total_expense_display'] = $stats['total_expense'] + $totalReturned + $companyFee;
            $stats['month_expense_display'] = $stats['month_expense'] + $monthReturned + $monthCompanyFee;
            
            // Track company fee separately
            $stats['total_company_fee'] = $companyFee;
            $stats['month_company_fee'] = $monthCompanyFee;
            
            // Track net debt for reference
            $stats['total_borrowed'] = $totalBorrowed - $totalReturned;
            $stats['month_borrowed'] = $monthBorrowed - $monthReturned;
            
            // BƯỚC 3: Lợi nhuận = Tổng thu - Tổng chi (logic đơn giản: tiền vào - tiền ra)
            $stats['total_profit_after_fee'] = $stats['total_revenue_display'] - $stats['total_expense_display'];
            $stats['month_profit_after_fee'] = $stats['month_revenue_display'] - $stats['month_expense_display'];
            
            // Track owner maintenance separately (for display purposes)
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

    /**
     * Process repayment to company for vehicle owner
     */
    public function repayCompany(Request $request, Vehicle $vehicle)
    {
        // Validate vehicle has owner
        if (!$vehicle->hasOwner()) {
            return redirect()->back()->with('error', 'Xe này không có chủ xe.');
        }

        // Check permission
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
                abort(403, 'Bạn không có quyền thao tác trên xe này.');
            }
        } elseif (!auth()->user()->can('manage vehicles')) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        // Calculate current debt
        $totalBorrowed = $vehicle->transactions()->borrowFromCompany()->sum('amount');
        $totalReturned = $vehicle->transactions()->returnToCompany()->sum('amount');
        $currentDebt = $totalBorrowed - $totalReturned;

        if ($currentDebt <= 0) {
            return redirect()->back()->with('error', 'Xe này không có nợ công ty.');
        }

        // Calculate current balance
        $totalRevenue = $vehicle->transactions()->revenue()->sum('amount');
        $totalFundDeposit = $vehicle->transactions()->fundDeposit()->sum('amount');
        $totalExpense = $vehicle->transactions()->expense()->sum('amount');
        $currentBalance = $totalRevenue + $totalFundDeposit - $totalExpense + $totalBorrowed - $totalReturned;

        if ($currentBalance <= 0) {
            return redirect()->back()->with('error', 'Số dư lợi nhuận không đủ để trả nợ.');
        }

        // Determine repay amount
        $repayType = $request->input('repay_type', 'full');
        
        if ($repayType === 'full') {
            $repayAmount = min($currentDebt, $currentBalance);
        } else {
            $repayAmount = $request->input('amount', 0);
            
            // Validate amount
            if ($repayAmount <= 0) {
                return redirect()->back()->with('error', 'Số tiền trả phải lớn hơn 0.');
            }
            
            if ($repayAmount > $currentDebt) {
                return redirect()->back()->with('error', 'Số tiền trả không được vượt quá số nợ hiện tại.');
            }
            
            if ($repayAmount > $currentBalance) {
                return redirect()->back()->with('error', 'Số dư lợi nhuận không đủ để trả số tiền này.');
            }
        }

        try {
            DB::beginTransaction();

            // Create return transaction (deduct from vehicle owner)
            $returnTransaction = \App\Models\Transaction::create([
                'vehicle_id' => $vehicle->id,
                'type' => 'tra_cong_ty',
                'amount' => $repayAmount,
                'category' => 'hoàn_trả',
                'note' => $request->input('note', 'Trả nợ công ty') . ($repayType === 'full' ? ' (trả hết)' : ''),
                'date' => now(),
                'recorded_by' => auth()->id(),
                'method' => 'bank',
            ]);

            // Create company revenue transaction (add to company fund)
            $revenueTransaction = \App\Models\Transaction::create([
                'vehicle_id' => null, // No vehicle_id = company fund
                'type' => 'thu',
                'amount' => $repayAmount,
                'category' => null,
                'note' => 'Thu từ xe ' . $vehicle->license_plate . ' trả nợ (GD #' . $returnTransaction->id . ')',
                'date' => now(),
                'recorded_by' => auth()->id(),
                'method' => 'bank',
            ]);

            Log::info('Manual debt repayment', [
                'vehicle_id' => $vehicle->id,
                'license_plate' => $vehicle->license_plate,
                'repay_amount' => $repayAmount,
                'return_transaction_id' => $returnTransaction->id,
                'revenue_transaction_id' => $revenueTransaction->id,
            ]);

            DB::commit();

            $remainingDebt = $currentDebt - $repayAmount;
            $message = 'Đã trả nợ thành công ' . number_format($repayAmount, 0, ',', '.') . 'đ. ';
            
            if ($remainingDebt > 0) {
                $message .= 'Còn nợ: ' . number_format($remainingDebt, 0, ',', '.') . 'đ.';
            } else {
                $message .= 'Đã trả hết nợ công ty! 🎉';
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in repayCompany: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi trả nợ: ' . $e->getMessage());
        }
    }
}

