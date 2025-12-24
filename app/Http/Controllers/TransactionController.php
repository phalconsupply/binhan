<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'owner_or_permission:view transactions'])->only(['index', 'show']);
        $this->middleware(['auth', 'owner_or_permission:create transactions'])->only(['create', 'store']);
        $this->middleware(['auth', 'owner_or_permission:edit transactions'])->only(['edit', 'update']);
        $this->middleware(['auth', 'owner_or_permission:delete transactions'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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

        $query = Transaction::with(['vehicle', 'incident.patient', 'recorder', 'vehicleMaintenance.maintenanceService']);

        // Scope to owner's vehicles if owner
        if ($isVehicleOwner && !empty($ownedVehicleIds)) {
            $query->whereIn('vehicle_id', $ownedVehicleIds);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('vehicle', function($vq) use ($search) {
                    $vq->where('license_plate', 'like', "%{$search}%");
                })
                ->orWhereHas('incident', function($iq) use ($search) {
                    $iq->where('id', 'like', "%{$search}%");
                })
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('note', 'like', "%{$search}%")
                ->orWhere('amount', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Filter by vehicle
        if ($request->has('vehicle_id') && $request->vehicle_id != '') {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by method
        if ($request->has('method') && $request->method != '') {
            $query->where('method', $request->method);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Get all transactions
        $allTransactions = $query->orderBy('date', 'desc')->get();

        // Separate dividend transactions
        $dividendTransactions = $allTransactions->filter(function($transaction) {
            return str_contains($transaction->note ?? '', 'Chia cổ tức');
        });
        
        // Separate maintenance transactions (by vehicle_maintenance_id relationship)
        $maintenanceTransactions = $allTransactions->filter(function($transaction) {
            return !empty($transaction->vehicle_maintenance_id);
        });
        
        // Separate fund deposit transactions (nop_quy only)
        $fundDepositTransactions = $allTransactions->filter(function($transaction) {
            return $transaction->type === 'nop_quy';
        });
        
        // Regular transactions include auto-repayments (tra_cong_ty from fund deposits)
        $regularTransactions = $allTransactions->reject(function($transaction) {
            return str_contains($transaction->note ?? '', 'Chia cổ tức') ||
                   !empty($transaction->vehicle_maintenance_id) ||
                   $transaction->type === 'nop_quy';
        });

        // Group regular transactions by incident_id
        $groupedTransactions = $regularTransactions->groupBy('incident_id')->map(function($group) {
            // Define transaction types
            $revenueTypes = ['thu', 'vay_cong_ty', 'nop_quy'];
            $expenseTypes = ['chi', 'tra_cong_ty', 'du_kien_chi'];
            
            $totalRevenue = $group->filter(function($t) use ($revenueTypes) {
                return in_array($t->type, $revenueTypes);
            })->sum('amount');
            
            $totalExpense = $group->filter(function($t) use ($expenseTypes) {
                return in_array($t->type, $expenseTypes);
            })->sum('amount');
            
            $totalPlannedExpense = $group->where('type', 'du_kien_chi')->sum('amount');
            $netAmount = $totalRevenue - $totalExpense;
            
            $vehicle = $group->first()->vehicle;
            $hasOwner = $vehicle && $vehicle->hasOwner();
            
            // Only calculate management fee on real revenue (thu), not on borrowed or deposited funds
            $realRevenue = $group->where('type', 'thu')->sum('amount');
            $realExpense = $group->where('type', 'chi')->sum('amount');
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
                'net_amount' => $netAmount,
                'has_owner' => $hasOwner,
                'management_fee' => $managementFee,
                'profit_after_fee' => $profitAfterFee,
                'is_dividend' => false,
                'is_maintenance' => false,
                'is_fund_deposit' => false,
                'is_other' => false,
            ];
        })->sortByDesc('date');

        // Separate incident groups and "other transactions" group
        $incidentGroups = $groupedTransactions->filter(function($group) {
            return $group['incident'] !== null;
        })->values();

        $otherGroup = $groupedTransactions->filter(function($group) {
            return $group['incident'] === null;
        })->first();

        // Build final sorted groups
        $finalGroups = collect();

        // 1. Dividend group (if exists)
        if ($dividendTransactions->isNotEmpty()) {
            $totalExpense = $dividendTransactions->sum('amount');
            $dividendGroup = [
                'incident' => null,
                'vehicle' => null,
                'date' => $dividendTransactions->first()->date,
                'transactions' => $dividendTransactions,
                'total_revenue' => 0,
                'total_expense' => $totalExpense,
                'total_planned_expense' => 0,
                'net_amount' => -$totalExpense,
                'has_owner' => false,
                'management_fee' => 0,
                'profit_after_fee' => 0,
                'is_dividend' => true,
                'is_maintenance' => false,
                'is_fund_deposit' => false,
                'is_other' => false,
            ];
            $finalGroups->push($dividendGroup);
        }

        // 2. Maintenance group (if exists)
        if ($maintenanceTransactions->isNotEmpty()) {
            $totalExpense = $maintenanceTransactions->sum('amount');
            $maintenanceGroup = [
                'incident' => null,
                'vehicle' => null,
                'date' => $maintenanceTransactions->first()->date,
                'transactions' => $maintenanceTransactions,
                'total_revenue' => 0,
                'total_expense' => $totalExpense,
                'total_planned_expense' => 0,
                'net_amount' => -$totalExpense,
                'has_owner' => false,
                'management_fee' => 0,
                'profit_after_fee' => 0,
                'is_dividend' => false,
                'is_maintenance' => true,
                'is_fund_deposit' => false,
                'is_other' => false,
            ];
            $finalGroups->push($maintenanceGroup);
        }

        // 3. Fund Deposit group (if exists) - Chỉ nộp quỹ
        if ($fundDepositTransactions->isNotEmpty()) {
            $totalRevenue = $fundDepositTransactions->sum('amount');
            $fundDepositGroup = [
                'incident' => null,
                'vehicle' => null,
                'date' => $fundDepositTransactions->first()->date,
                'transactions' => $fundDepositTransactions,
                'total_revenue' => $totalRevenue,
                'total_expense' => 0,
                'total_planned_expense' => 0,
                'net_amount' => $totalRevenue,
                'has_owner' => false,
                'management_fee' => 0, // Nộp quỹ KHÔNG tính phí 15%
                'profit_after_fee' => $totalRevenue,
                'is_dividend' => false,
                'is_maintenance' => false,
                'is_fund_deposit' => true,
                'is_other' => false,
            ];
            $finalGroups->push($fundDepositGroup);
        }

        // 4. Other transactions group (if exists)
        if ($otherGroup) {
            $otherGroup['is_other'] = true;
            $otherGroup['is_fund_deposit'] = false;
            $finalGroups->push($otherGroup);
        }

        // 5. Incident groups (sorted by date desc)
        $finalGroups = $finalGroups->merge($incidentGroups);

        $groupedTransactions = $finalGroups->values();

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

        // Get vehicles for filter dropdown (scoped to owner's vehicles if owner)
        if ($isVehicleOwner && !empty($ownedVehicleIds)) {
            $vehicles = Vehicle::whereIn('id', $ownedVehicleIds)->orderBy('license_plate')->get();
        } else {
            $vehicles = Vehicle::orderBy('license_plate')->get();
        }

        // Statistics (scoped to owner's vehicles if owner)
        $statsQuery = Transaction::query();
        if ($isVehicleOwner && !empty($ownedVehicleIds)) {
            $statsQuery->whereIn('vehicle_id', $ownedVehicleIds);
        }

        $totalRevenue = (clone $statsQuery)->revenue()->sum('amount');
        $totalExpense = (clone $statsQuery)->expense()->sum('amount');
        $totalPlannedExpense = (clone $statsQuery)->plannedExpense()->sum('amount');
        $monthRevenue = (clone $statsQuery)->revenue()->thisMonth()->sum('amount');
        $monthExpense = (clone $statsQuery)->expense()->thisMonth()->sum('amount');
        $monthPlannedExpense = (clone $statsQuery)->plannedExpense()->thisMonth()->sum('amount');
        
        // Calculate "Chi từ công ty" for vehicle owners
        // NEW LOGIC: Chi từ công ty = Actual borrowed amount from company (vay_cong_ty - tra_cong_ty)
        // FALLBACK: If no borrow transactions exist, calculate deficit (Chi - Thu)
        if ($isVehicleOwner) {
            $totalBorrowed = (clone $statsQuery)->borrowFromCompany()->sum('amount');
            $totalReturned = (clone $statsQuery)->returnToCompany()->sum('amount');
            $currentDebt = $totalBorrowed - $totalReturned;
            
            // If there are actual borrow transactions, use them; otherwise calculate deficit
            if ($totalBorrowed > 0) {
                $companyExpense = $currentDebt;
                $monthBorrowed = (clone $statsQuery)->borrowFromCompany()->thisMonth()->sum('amount');
                $monthReturned = (clone $statsQuery)->returnToCompany()->thisMonth()->sum('amount');
                $companyMonthExpense = $monthBorrowed - $monthReturned;
            } else {
                // Legacy calculation: amount company had to cover when owner didn't have enough
                $companyExpense = max(0, $totalExpense - $totalRevenue);
                $companyMonthExpense = max(0, $monthExpense - $monthRevenue);
            }
            $companyPlannedExpense = (clone $statsQuery)->plannedExpense()->whereNull('incident_id')->sum('amount');
        } else {
            // For admin: show expenses without incident_id (company's own expenses)
            $companyExpense = (clone $statsQuery)->expense()->whereNull('incident_id')->sum('amount');
            $companyMonthExpense = (clone $statsQuery)->expense()->whereNull('incident_id')->thisMonth()->sum('amount');
            $companyPlannedExpense = (clone $statsQuery)->plannedExpense()->whereNull('incident_id')->sum('amount');
        }
        
        $stats = [
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'total_planned_expense' => $totalPlannedExpense,
            'today_revenue' => (clone $statsQuery)->revenue()->today()->sum('amount'),
            'today_expense' => (clone $statsQuery)->expense()->today()->sum('amount'),
            'today_planned_expense' => (clone $statsQuery)->plannedExpense()->today()->sum('amount'),
            'month_revenue' => $monthRevenue,
            'month_expense' => $monthExpense,
            'month_planned_expense' => $monthPlannedExpense,
            'company_expense' => $companyExpense,
            'company_month_expense' => $companyMonthExpense,
            'company_planned_expense' => $companyPlannedExpense,
        ];
        
        // Calculate profit (different for owner vs company)
        if ($isVehicleOwner) {
            // For vehicle owner: simple calculation
            // Owner's profit = Total Revenue - Total Expense (excluding owner maintenance) - Total Planned Expense + Fund Deposit
            // Need to exclude "bảo_trì_xe_chủ_riêng" from expense calculation (deducted separately, not from company)
            
            // Get owner maintenance costs (these are deducted from vehicle profit, not company expense)
            $totalOwnerMaintenance = (clone $statsQuery)->expense()->where('category', 'bảo_trì_xe_chủ_riêng')->sum('amount');
            $todayOwnerMaintenance = (clone $statsQuery)->expense()->today()->where('category', 'bảo_trì_xe_chủ_riêng')->sum('amount');
            $monthOwnerMaintenance = (clone $statsQuery)->expense()->thisMonth()->where('category', 'bảo_trì_xe_chủ_riêng')->sum('amount');
            
            // Calculate company expenses (excluding owner maintenance)
            $totalExpenseCompany = $stats['total_expense'] - $totalOwnerMaintenance;
            $todayExpenseCompany = $stats['today_expense'] - $todayOwnerMaintenance;
            $monthExpenseCompany = $stats['month_expense'] - $monthOwnerMaintenance;
            
            // Include fund deposits for the owner's vehicle
            $totalFundDeposit = (clone $statsQuery)->fundDeposit()->sum('amount');
            $todayFundDeposit = (clone $statsQuery)->fundDeposit()->today()->sum('amount');
            $monthFundDeposit = (clone $statsQuery)->fundDeposit()->whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount');
            
            // Calculate net profit (before management fee and owner maintenance)
            $totalNet = $stats['total_revenue'] - $totalExpenseCompany - $stats['total_planned_expense'] + $totalFundDeposit;
            $todayNet = $stats['today_revenue'] - $todayExpenseCompany - $stats['today_planned_expense'] + $todayFundDeposit;
            $monthNet = $stats['month_revenue'] - $monthExpenseCompany - $stats['month_planned_expense'] + $monthFundDeposit;
            
            // Calculate management fee (15% of net BEFORE fund deposit)
            $totalNetBeforeFundDeposit = $stats['total_revenue'] - $totalExpenseCompany - $stats['total_planned_expense'];
            $todayNetBeforeFundDeposit = $stats['today_revenue'] - $todayExpenseCompany - $stats['today_planned_expense'];
            $monthNetBeforeFundDeposit = $stats['month_revenue'] - $monthExpenseCompany - $stats['month_planned_expense'];
            
            $totalManagementFee = $totalNetBeforeFundDeposit > 0 ? $totalNetBeforeFundDeposit * 0.15 : 0;
            $todayManagementFee = $todayNetBeforeFundDeposit > 0 ? $todayNetBeforeFundDeposit * 0.15 : 0;
            $monthManagementFee = $monthNetBeforeFundDeposit > 0 ? $monthNetBeforeFundDeposit * 0.15 : 0;
            
            // Final profit after management fee and owner maintenance
            $companyProfit = $totalNet - $totalManagementFee - $totalOwnerMaintenance;
            $companyTodayProfit = $todayNet - $todayManagementFee - $todayOwnerMaintenance;
            $companyMonthProfit = $monthNet - $monthManagementFee - $monthOwnerMaintenance;
        } else {
            // For company/admin: Simplified calculation
            // Lợi nhuận = Tổng thu - Tổng chi - Dự kiến chi (tất cả giao dịch)
            
            $companyProfit = $totalRevenue - $totalExpense - $totalPlannedExpense;
            $companyTodayProfit = $stats['today_revenue'] - $stats['today_expense'] - $stats['today_planned_expense'];
            $companyMonthProfit = $monthRevenue - $monthExpense - $monthPlannedExpense;
        }
        
        $stats['total_net'] = $companyProfit;
        $stats['today_net'] = $companyTodayProfit;
        $stats['month_net'] = $companyMonthProfit;

        return view('transactions.index', compact('transactions', 'vehicles', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $vehicles = Vehicle::active()->orderBy('license_plate')->get();
        $incidents = Incident::with(['vehicle', 'patient'])
            ->orderBy('date', 'desc')
            ->limit(50)
            ->get();

        // Pre-select incident if provided
        $selectedIncident = null;
        if ($request->has('incident_id')) {
            $selectedIncident = Incident::find($request->incident_id);
        }

        return view('transactions.create', compact('vehicles', 'incidents', 'selectedIncident'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'incident_id' => 'nullable|exists:incidents,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'type' => 'required|in:thu,chi,du_kien_chi,nop_quy',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,bank,other',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        // Nếu là Nộp quỹ, không cho phép chọn incident_id
        if ($validated['type'] === 'nop_quy') {
            $validated['incident_id'] = null;
            $validated['category'] = 'nop_quy';
            
            // Thêm ghi chú tự động nếu chưa có
            if (empty($validated['note'])) {
                if (!empty($validated['vehicle_id'])) {
                    $vehicle = Vehicle::find($validated['vehicle_id']);
                    $validated['note'] = 'Nộp quỹ - ' . $vehicle->license_plate;
                } else {
                    $validated['note'] = 'Nộp quỹ - Tổng công ty';
                }
            }
        }

        $validated['recorded_by'] = auth()->id();

        $transaction = Transaction::create($validated);

        return redirect()->route('transactions.index')
            ->with('success', "Đã ghi nhận {$transaction->type_label} " . number_format($transaction->amount, 0, ',', '.') . 'đ thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        // Check if user is vehicle owner and has access to this transaction
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($transaction->vehicle_id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền xem giao dịch này.');
            }
        }

        $transaction->load(['vehicle', 'incident.patient', 'recorder']);

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        // Check if user is vehicle owner and has access to this transaction
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($transaction->vehicle_id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền chỉnh sửa giao dịch này.');
            }
        }

        $transaction->load(['vehicle', 'incident']);
        
        $vehicles = Vehicle::orderBy('license_plate')->get();
        $incidents = Incident::with(['vehicle', 'patient'])
            ->orderBy('date', 'desc')
            ->limit(50)
            ->get();

        return view('transactions.edit', compact('transaction', 'vehicles', 'incidents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        // Check if user is vehicle owner and has access to this transaction
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($transaction->vehicle_id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền cập nhật giao dịch này.');
            }
        }

        $validated = $request->validate([
            'incident_id' => 'nullable|exists:incidents,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'type' => 'required|in:thu,chi,du_kien_chi',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,bank,other',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Đã cập nhật giao dịch thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        // Check if user is vehicle owner and has access to this transaction
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($transaction->vehicle_id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền xóa giao dịch này.');
            }
        }

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Đã xóa giao dịch thành công!');
    }

    /**
     * Remove all transactions for a specific incident.
     */
    public function destroyByIncident($incidentId)
    {
        // Check permission
        if (!auth()->user()->can('delete transactions')) {
            abort(403);
        }

        $count = Transaction::where('incident_id', $incidentId)->count();
        Transaction::where('incident_id', $incidentId)->delete();

        return redirect()->back()
            ->with('success', "Đã xóa {$count} giao dịch của chuyến đi này!");
    }

    /**
     * Distribute dividends to investors
     */
    public function distributeDividend(Request $request)
    {
        $validated = $request->validate([
            'distribution_percentage' => 'required|numeric|min:0|max:100',
            'investors' => 'required|array',
            'investors.*.staff_id' => 'required|exists:staff,id',
            'investors.*.amount' => 'required|numeric|min:0',
            'investors.*.equity_percentage' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $totalDistributed = 0;
            $date = now();

            // Create dividend transactions for each investor
            foreach ($validated['investors'] as $investorData) {
                $amount = $investorData['amount'];
                
                if ($amount <= 0) {
                    continue;
                }

                $staff = \App\Models\Staff::find($investorData['staff_id']);
                
                // Create CHI transaction (company pays dividend)
                Transaction::create([
                    'incident_id' => null,
                    'vehicle_id' => null,
                    'staff_id' => $staff->id,
                    'type' => 'chi',
                    'category' => 'cổ_tức',
                    'amount' => $amount,
                    'method' => 'bank',
                    'payment_method' => 'chuyển khoản',
                    'date' => $date,
                    'note' => "Chia cổ tức {$validated['distribution_percentage']}% - {$staff->full_name} (Vốn góp: {$investorData['equity_percentage']}%)" . 
                             ($validated['note'] ? " - {$validated['note']}" : ""),
                    'recorded_by' => auth()->id(),
                ]);

                $totalDistributed += $amount;
            }

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', "Đã chia cổ tức thành công! Tổng số tiền: " . number_format($totalDistributed, 0, ',', '.') . "đ cho " . count($validated['investors']) . " cổ đông.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }
}
