<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Transaction;
use App\Models\User;
use App\Models\StaffAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view staff')->only(['index', 'show']);
        $this->middleware('permission:create staff')->only(['create', 'store']);
        $this->middleware('permission:edit staff')->only(['edit', 'update']);
        $this->middleware('permission:delete staff')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Staff::with('user.roles')->latest();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Staff type filter
        if ($request->filled('staff_type')) {
            $query->where('staff_type', $request->staff_type);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $staff = $query->paginate(20);

        return view('staff.index', compact('staff'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('staff.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'staff_type' => 'required|in:medical_staff,driver,manager,investor,admin',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'id_card' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Auto-create department if provided and doesn't exist
            if (!empty($validated['department'])) {
                \App\Models\Department::firstOrCreate(
                    ['name' => $validated['department']],
                    ['is_active' => true]
                );
            }

            // Auto-create position if provided and doesn't exist
            if (!empty($validated['position'])) {
                \App\Models\Position::firstOrCreate(
                    ['name' => $validated['position']],
                    ['is_active' => true]
                );
            }

            // Create user account
            $user = User::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Assign role based on staff type
            $user->assignRole($validated['staff_type']);

            // Create staff profile (employee_code will be auto-generated)
            $staff = Staff::create([
                'user_id' => $user->id,
                'full_name' => $validated['full_name'],
                'staff_type' => $validated['staff_type'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'id_card' => $validated['id_card'],
                'birth_date' => $validated['birth_date'],
                'gender' => $validated['gender'],
                'address' => $validated['address'],
                'hire_date' => $validated['hire_date'],
                'department' => $validated['department'],
                'position' => $validated['position'],
                'base_salary' => $validated['base_salary'],
                'notes' => $validated['notes'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            // Check action parameter to determine redirect behavior
            $action = $request->input('action', 'save_close');
            
            if ($action === 'save_continue') {
                return redirect()->route('staff.create')
                    ->with('success', 'Đã tạo nhân sự thành công! Bạn có thể tiếp tục tạo nhân sự mới.');
            }

            return redirect()->route('staff.show', $staff)
                ->with('success', 'Đã tạo nhân sự thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff)
    {
        $staff->load('user.roles');
        return view('staff.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff)
    {
        return view('staff.edit', compact('staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'staff_type' => 'required|in:medical_staff,driver,manager,investor,admin',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . $staff->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'id_card' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Auto-create department if provided and doesn't exist
            if (!empty($validated['department'])) {
                \App\Models\Department::firstOrCreate(
                    ['name' => $validated['department']],
                    ['is_active' => true]
                );
            }

            // Auto-create position if provided and doesn't exist
            if (!empty($validated['position'])) {
                \App\Models\Position::firstOrCreate(
                    ['name' => $validated['position']],
                    ['is_active' => true]
                );
            }

            // Update user account
            $userData = [
                'name' => $validated['full_name'],
                'email' => $validated['email'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $staff->user->update($userData);

            // Update role if staff type changed
            if ($staff->staff_type !== $validated['staff_type']) {
                $staff->user->syncRoles([$validated['staff_type']]);
            }

            // Update staff profile (employee_code is not editable)
            $staff->update([
                'full_name' => $validated['full_name'],
                'staff_type' => $validated['staff_type'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'id_card' => $validated['id_card'],
                'birth_date' => $validated['birth_date'],
                'gender' => $validated['gender'],
                'address' => $validated['address'],
                'hire_date' => $validated['hire_date'],
                'department' => $validated['department'],
                'position' => $validated['position'],
                'base_salary' => $validated['base_salary'],
                'notes' => $validated['notes'],
                'is_active' => $validated['is_active'] ?? $staff->is_active,
            ]);

            DB::commit();

            return redirect()->route('staff.show', $staff)
                ->with('success', 'Đã cập nhật nhân sự thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {
        try {
            DB::beginTransaction();

            // Delete user account (will cascade delete staff profile)
            $staff->user->delete();

            DB::commit();

            return redirect()->route('staff.index')
                ->with('success', 'Đã xóa nhân sự thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Show staff earnings statistics
     */
    public function earnings(Request $request, Staff $staff)
    {
        // Query earnings transactions, excluding salary advances
        $query = Transaction::where('staff_id', $staff->id)
            ->where('type', 'chi')
            ->where(function($q) {
                $q->where('category', '!=', 'ứng_lương')
                  ->orWhereNull('category');
            })
            ->with(['incident.patient', 'vehicle']);

        // Date filter
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $earnings = $query->orderBy('date', 'desc')->paginate(50);

        // Calculate number of months for base salary calculation
        $monthsWorked = 0;
        if ($staff->hire_date) {
            $startDate = $request->filled('from_date') 
                ? max($staff->hire_date, \Carbon\Carbon::parse($request->from_date))
                : $staff->hire_date;
            
            $endDate = $request->filled('to_date')
                ? \Carbon\Carbon::parse($request->to_date)
                : now();
            
            $monthsWorked = $startDate->diffInMonths($endDate) + 1;
        }

        // Base salary total based on filtered period
        $baseSalaryTotal = $staff->base_salary ? ($staff->base_salary * $monthsWorked) : 0;

        // Wage earnings from transactions (CHI - THU), excluding salary advances
        $wageEarningsQueryBase = Transaction::where('staff_id', $staff->id);
        
        if ($request->filled('from_date')) {
            $wageEarningsQueryBase->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $wageEarningsQueryBase->whereDate('date', '<=', $request->to_date);
        }
        
        // Clone and add category filter for CHI (exclude salary advance categories)
        $chiQuery = clone $wageEarningsQueryBase;
        $chiTotal = $chiQuery->where('type', 'chi')
            ->where(function($query) {
                $query->whereNotIn('category', ['ứng_lương', 'ứng_lương_nợ'])
                      ->orWhereNull('category');
            })
            ->sum('amount');
        
        // Clone and add category filter for THU (exclude salary advance categories)
        $thuQuery = clone $wageEarningsQueryBase;
        $thuTotal = $thuQuery->where('type', 'thu')
            ->where(function($query) {
                $query->whereNotIn('category', ['ứng_lương', 'ứng_lương_nợ'])
                      ->orWhereNull('category');
            })
            ->sum('amount');
        
        $wageEarningsTotal = $chiTotal - $thuTotal;

        // Month earnings (CHI - THU), excluding salary advances
        $monthChi = Transaction::where('staff_id', $staff->id)
            ->where('type', 'chi')
            ->where(function($query) {
                $query->whereNotIn('category', ['ứng_lương', 'ứng_lương_nợ'])
                      ->orWhereNull('category');
            })
            ->thisMonth()
            ->sum('amount');
        
        $monthThu = Transaction::where('staff_id', $staff->id)
            ->where('type', 'thu')
            ->where(function($query) {
                $query->whereNotIn('category', ['ứng_lương', 'ứng_lương_nợ'])
                      ->orWhereNull('category');
            })
            ->thisMonth()
            ->sum('amount');
        
        $monthWageEarnings = $monthChi - $monthThu;
        
        $monthBaseSalary = $staff->base_salary ?? 0;

        // Get adjustments for current month (for display purposes only, already in transactions)
        $currentMonth = now()->startOfMonth();
        $monthAdjustments = StaffAdjustment::where('staff_id', $staff->id)
            ->forMonth($currentMonth)
            ->get();

        $monthAdjustmentAdditions = $monthAdjustments->where('type', 'addition')->where('status', 'applied')->sum('amount');
        $monthAdjustmentDeductions = $monthAdjustments->where('type', 'deduction')->where('status', 'applied')->sum('amount');

        // Calculate salary advances for current month
        $monthSalaryAdvances = \App\Models\SalaryAdvance::where('staff_id', $staff->id)
            ->forMonth($currentMonth)
            ->sum('from_earnings');

        // Statistics
        $stats = [
            'base_salary' => $staff->base_salary ?? 0,
            'base_salary_total' => $baseSalaryTotal,
            'wage_earnings_total' => $wageEarningsTotal,
            'total_earnings' => $baseSalaryTotal + $wageEarningsTotal, // wageEarningsTotal already includes adjustments via transactions
            'month_base_salary' => $monthBaseSalary,
            'month_wage_earnings' => $monthWageEarnings,
            'month_adjustments' => $monthAdjustmentAdditions - $monthAdjustmentDeductions, // For display only
            'month_salary_advances' => $monthSalaryAdvances, // Total advanced this month
            'month_total_earnings' => $monthBaseSalary + $monthWageEarnings - $monthSalaryAdvances, // Subtract advances
            'total_trips' => $staff->incidents()->count(),
            'months_worked' => $monthsWorked,
        ];

        // Get adjustments for current month with details
        $adjustments = StaffAdjustment::where('staff_id', $staff->id)
            ->forMonth($currentMonth)
            ->with(['creator', 'incident'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate adjustment totals for current month
        $adjustmentAdditions = $adjustments->where('type', 'addition')->sum('amount');
        $adjustmentDeductions = $adjustments->where('type', 'deduction')->sum('amount');
        $adjustmentNet = $adjustmentAdditions - $adjustmentDeductions;

        // Get pending debts from adjustments
        $pendingAdjustmentDebts = StaffAdjustment::where('staff_id', $staff->id)
            ->debt()
            ->with('creator')
            ->orderBy('created_at', 'asc')
            ->get();

        // Get pending debts from salary advances
        $pendingSalaryAdvanceDebts = \App\Models\SalaryAdvance::where('staff_id', $staff->id)
            ->where('debt_amount', '>', 0)
            ->with('approvedBy')
            ->orderBy('date', 'asc')
            ->get();

        // Merge all debts
        $pendingDebts = $pendingAdjustmentDebts->merge($pendingSalaryAdvanceDebts)->sortBy('created_at');
        $totalDebt = $pendingAdjustmentDebts->sum('debt_amount') + $pendingSalaryAdvanceDebts->sum('debt_amount');

        return view('staff.earnings', compact('staff', 'earnings', 'stats', 'adjustments', 'adjustmentAdditions', 'adjustmentDeductions', 'adjustmentNet', 'pendingDebts', 'totalDebt'));
    }

    /**
     * Store a new adjustment for staff earnings
     */
    public function storeAdjustment(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'type' => 'required|in:addition,deduction',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'reason' => 'required|string',
            'month' => 'required|date',
            'incident_id' => 'nullable|exists:incidents,id',
        ]);

        try {
            DB::beginTransaction();

            $month = \Carbon\Carbon::parse($validated['month'])->startOfMonth();
            $amount = $validated['amount'];
            $incidentId = $validated['incident_id'] ?? null;

            \Log::info('Creating adjustment', [
                'staff_id' => $staff->id,
                'type' => $validated['type'],
                'amount' => $amount,
                'incident_id' => $incidentId,
            ]);

            // Create adjustment record
            $adjustment = StaffAdjustment::create([
                'staff_id' => $staff->id,
                'created_by' => auth()->id(),
                'incident_id' => $incidentId,
                'type' => $validated['type'],
                'amount' => $amount,
                'month' => $month,
                'category' => $validated['category'],
                'reason' => $validated['reason'],
                'status' => 'pending',
                'debt_amount' => 0,
            ]);

            \Log::info('Adjustment created', ['adjustment_id' => $adjustment->id]);

            // Create transactions based on type
            $transactionIds = [];
            
            if ($validated['type'] === 'addition') {
                // For addition, create transaction(s)
                if ($incidentId) {
                    // Calculate how much can come from incident
                    $incident = \App\Models\Incident::find($incidentId);
                    $revenue = Transaction::where('incident_id', $incidentId)->where('type', 'thu')->sum('amount');
                    $expenses = Transaction::where('incident_id', $incidentId)->where('type', 'chi')->sum('amount');
                    $availableFromIncident = max(0, $revenue - $expenses);

                    if ($availableFromIncident >= $amount) {
                        // All from incident
                        $transaction = Transaction::create([
                            'incident_id' => $incidentId,
                            'vehicle_id' => $incident->vehicle_id,
                            'staff_id' => $staff->id,
                            'type' => 'chi',
                            'category' => 'điều_chỉnh_lương',
                            'amount' => $amount,
                            'date' => now(),
                            'note' => "Điều chỉnh: {$validated['category']} - {$staff->full_name} (#{$incident->id})",
                            'payment_method' => 'chuyển khoản',
                            'recorded_by' => auth()->id(),
                        ]);
                        $transactionIds[] = $transaction->id;
                        $adjustment->update([
                            'from_incident_amount' => $amount,
                            'from_company_amount' => 0,
                        ]);
                    } else {
                        // Split between incident and company
                        if ($availableFromIncident > 0) {
                            $transaction1 = Transaction::create([
                                'incident_id' => $incidentId,
                                'vehicle_id' => $incident->vehicle_id,
                                'staff_id' => $staff->id,
                                'type' => 'chi',
                                'category' => 'điều_chỉnh_lương',
                                'amount' => $availableFromIncident,
                                'date' => now(),
                                'note' => "Điều chỉnh: {$validated['category']} - {$staff->full_name} (#{$incident->id} - phần từ chuyến đi)",
                                'payment_method' => 'chuyển khoản',
                                'recorded_by' => auth()->id(),
                            ]);
                            $transactionIds[] = $transaction1->id;
                        }

                        $fromCompany = $amount - $availableFromIncident;
                        $transaction2 = Transaction::create([
                            'incident_id' => null,
                            'vehicle_id' => null,
                            'staff_id' => $staff->id,
                            'type' => 'chi',
                            'category' => 'điều_chỉnh_lương',
                            'amount' => $fromCompany,
                            'date' => now(),
                            'note' => "Điều chỉnh: {$validated['category']} - {$staff->full_name} (từ quỹ công ty)",
                            'payment_method' => 'chuyển khoản',
                            'recorded_by' => auth()->id(),
                        ]);
                        $transactionIds[] = $transaction2->id;
                        $adjustment->update([
                            'from_incident_amount' => $availableFromIncident,
                            'from_company_amount' => $fromCompany,
                        ]);
                    }
                } else {
                    // All from company
                    $transaction = Transaction::create([
                        'incident_id' => null,
                        'vehicle_id' => null,
                        'staff_id' => $staff->id,
                        'type' => 'chi',
                        'category' => 'điều_chỉnh_lương',
                        'amount' => $amount,
                        'date' => now(),
                        'note' => "Điều chỉnh: {$validated['category']} - {$staff->full_name} (từ quỹ công ty)",
                        'payment_method' => 'chuyển khoản',
                        'recorded_by' => auth()->id(),
                    ]);
                    $transactionIds[] = $transaction->id;
                    \Log::info('Transaction created from company', ['transaction_id' => $transaction->id]);
                    
                    $adjustment->update([
                        'from_incident_amount' => 0,
                        'from_company_amount' => $amount,
                    ]);
                }

                \Log::info('Transactions created', ['ids' => $transactionIds]);

                // Mark as applied
                $adjustment->update([
                    'status' => 'applied',
                    'applied_at' => now(),
                    'transaction_ids' => $transactionIds,
                ]);

                \Log::info('Adjustment marked as applied');

                // Try to pay off any pending debts
                $this->processDebtPayment($staff);
                
            } else {
                // For deduction, create transaction and check balance for debt
                $baseSalary = $staff->base_salary ?? 0;
                $wageEarnings = Transaction::where('staff_id', $staff->id)
                    ->where('type', 'chi')
                    ->whereYear('date', $month->year)
                    ->whereMonth('date', $month->month)
                    ->sum('amount');

                $otherAdjustments = StaffAdjustment::where('staff_id', $staff->id)
                    ->where('id', '!=', $adjustment->id)
                    ->forMonth($month)
                    ->get();

                $otherAdditions = $otherAdjustments->where('type', 'addition')->sum('amount');
                $otherDeductions = $otherAdjustments->where('type', 'deduction')->sum('amount');

                $availableBalance = $baseSalary + $wageEarnings + $otherAdditions - $otherDeductions;

                // Always create transaction for deduction (company receives money back)
                $transaction = Transaction::create([
                    'incident_id' => $incidentId,
                    'vehicle_id' => $incidentId ? \App\Models\Incident::find($incidentId)->vehicle_id : null,
                    'staff_id' => $staff->id,
                    'type' => 'thu',
                    'category' => 'điều_chỉnh_lương',
                    'amount' => $amount,
                    'date' => now(),
                    'note' => "Trừ tiền: {$validated['category']} - {$staff->full_name}" . ($incidentId ? " (Chuyến #{$incidentId})" : ""),
                    'payment_method' => 'chuyển khoản',
                    'recorded_by' => auth()->id(),
                ]);
                $transactionIds[] = $transaction->id;

                if ($availableBalance < $amount) {
                    // Not enough balance, create debt
                    $debtAmount = $amount - max(0, $availableBalance);
                    $adjustment->update([
                        'status' => 'debt',
                        'debt_amount' => $debtAmount,
                        'transaction_ids' => $transactionIds,
                    ]);
                } else {
                    // Enough balance, mark as applied
                    $adjustment->update([
                        'status' => 'applied',
                        'applied_at' => now(),
                        'transaction_ids' => $transactionIds,
                    ]);
                }
            }

            DB::commit();
            \Log::info('Transaction committed successfully');

            return redirect()->route('staff.earnings', $staff)
                ->with('success', 'Đã thêm điều chỉnh thu nhập thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating adjustment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Process debt payment when staff receives new income
     */
    private function processDebtPayment(Staff $staff)
    {
        // Get all pending debts from adjustments ordered by oldest first
        $adjustmentDebts = StaffAdjustment::where('staff_id', $staff->id)
            ->debt()
            ->orderBy('created_at', 'asc')
            ->get();

        // Get all pending debts from salary advances ordered by oldest first
        $advanceDebts = \App\Models\SalaryAdvance::where('staff_id', $staff->id)
            ->where('debt_amount', '>', 0)
            ->orderBy('date', 'asc')
            ->get();

        // Merge all debts
        $allDebts = $adjustmentDebts->merge($advanceDebts)->sortBy('created_at');

        if ($allDebts->isEmpty()) {
            return;
        }

        // Calculate available balance
        $currentMonth = now()->startOfMonth();
        
        $baseSalary = $staff->base_salary ?? 0;
        
        // Net transactions (CHI - THU), excluding salary advances
        $monthChi = Transaction::where('staff_id', $staff->id)
            ->where('type', 'chi')
            ->where(function($query) {
                $query->whereNotIn('category', ['ứng_lương', 'ứng_lương_nợ'])
                      ->orWhereNull('category');
            })
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->sum('amount');
        
        $monthThu = Transaction::where('staff_id', $staff->id)
            ->where('type', 'thu')
            ->where(function($query) {
                $query->whereNotIn('category', ['ứng_lương', 'ứng_lương_nợ'])
                      ->orWhereNull('category');
            })
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->sum('amount');
        
        $wageEarnings = $monthChi - $monthThu;

        $additions = StaffAdjustment::where('staff_id', $staff->id)
            ->where('type', 'addition')
            ->where('status', 'applied')
            ->forMonth($currentMonth)
            ->sum('amount');

        $appliedDeductions = StaffAdjustment::where('staff_id', $staff->id)
            ->where('type', 'deduction')
            ->where('status', 'applied')
            ->forMonth($currentMonth)
            ->sum('amount');

        $availableBalance = $baseSalary + $wageEarnings;

        // Pay off debts with available balance
        foreach ($allDebts as $debt) {
            if ($availableBalance <= 0) {
                break;
            }

            $debtAmount = $debt->debt_amount;

            if ($availableBalance >= $debtAmount) {
                // Can pay off entire debt
                $availableBalance -= $debtAmount;
                $debt->update([
                    'debt_amount' => 0,
                ]);
                
                // Mark adjustment as applied if it's from adjustments
                if ($debt instanceof StaffAdjustment) {
                    $debt->update([
                        'status' => 'applied',
                        'applied_at' => now(),
                    ]);
                }
            } else {
                // Partial payment
                $debt->update([
                    'debt_amount' => $debtAmount - $availableBalance,
                ]);
                $availableBalance = 0;
            }
        }
    }

    /**
     * Store salary advance request
     */
    public function storeSalaryAdvance(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $amount = $validated['amount'];
            $currentMonth = now()->startOfMonth();

            // Calculate available earnings
            $baseSalary = $staff->base_salary ?? 0;
            
            // Calculate net transactions (CHI - THU), excluding salary advances
            $monthChi = Transaction::where('staff_id', $staff->id)
                ->where('type', 'chi')
                ->where(function($query) {
                    $query->whereNotIn('category', ['ứng_lương', 'ứng_lương_nợ'])
                          ->orWhereNull('category');
                })
                ->thisMonth()
                ->sum('amount');
            
            $monthThu = Transaction::where('staff_id', $staff->id)
                ->where('type', 'thu')
                ->where(function($query) {
                    $query->whereNotIn('category', ['ứng_lương', 'ứng_lương_nợ'])
                          ->orWhereNull('category');
                })
                ->thisMonth()
                ->sum('amount');
            
            $wageEarnings = $monthChi - $monthThu;

            // Get existing adjustments
            $adjustments = StaffAdjustment::where('staff_id', $staff->id)
                ->where('status', 'applied')
                ->forMonth($currentMonth)
                ->get();

            $additions = $adjustments->where('type', 'addition')->sum('amount');
            $deductions = $adjustments->where('type', 'deduction')->sum('amount');

            // Calculate total available (already includes all transactions)
            $totalAvailable = $baseSalary + $wageEarnings;

            // Check existing advances and debts this month
            $existingAdvances = \App\Models\SalaryAdvance::where('staff_id', $staff->id)
                ->forMonth($currentMonth)
                ->sum('from_earnings');

            $existingDebts = StaffAdjustment::where('staff_id', $staff->id)
                ->where('debt_amount', '>', 0)
                ->sum('debt_amount');

            $availableForAdvance = max(0, $totalAvailable - $existingAdvances - $existingDebts);

            // Calculate how much comes from earnings vs company
            $fromEarnings = min($amount, $availableForAdvance);
            $fromCompany = $amount - $fromEarnings;

            $transactionIds = [];

            // If company needs to advance money (debt), create transaction to record it
            if ($fromCompany > 0) {
                $transaction = Transaction::create([
                    'incident_id' => null,
                    'vehicle_id' => null,
                    'staff_id' => $staff->id,
                    'type' => 'chi', // Company pays out (advance payment)
                    'category' => 'ứng_lương_nợ', // Debt advance - different from regular advances
                    'amount' => $fromCompany,
                    'date' => now(),
                    'note' => "Ứng lương (nợ công ty) - {$staff->full_name}",
                    'payment_method' => 'chuyển khoản',
                    'recorded_by' => auth()->id(),
                ]);
                $transactionIds[] = $transaction->id;
            }

            // Create advance record
            $advance = \App\Models\SalaryAdvance::create([
                'staff_id' => $staff->id,
                'amount' => $amount,
                'from_earnings' => $fromEarnings,
                'from_company' => $fromCompany,
                'debt_amount' => $fromCompany, // Company advance becomes debt
                'status' => 'approved',
                'note' => $validated['note'] ?? null,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'date' => now(),
                'transaction_ids' => $transactionIds,
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Đã ứng lương thành công!' . 
                    ($fromCompany > 0 ? " (Nợ công ty: " . number_format($fromCompany, 0, ',', '.') . "đ)" : ""));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error processing salary advance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update salary advance
     */
    public function updateSalaryAdvance(Request $request, \App\Models\SalaryAdvance $salaryAdvance)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $oldAmount = $salaryAdvance->amount;
            $newAmount = $validated['amount'];
            $staff = $salaryAdvance->staff;
            $currentMonth = \Carbon\Carbon::parse($salaryAdvance->date)->startOfMonth();

            // Recalculate available earnings (excluding this advance)
            $baseSalary = $staff->base_salary ?? 0;
            
            $monthChi = Transaction::where('staff_id', $staff->id)
                ->where('type', 'chi')
                ->whereYear('date', $currentMonth->year)
                ->whereMonth('date', $currentMonth->month)
                ->sum('amount');
            
            $monthThu = Transaction::where('staff_id', $staff->id)
                ->where('type', 'thu')
                ->whereYear('date', $currentMonth->year)
                ->whereMonth('date', $currentMonth->month)
                ->sum('amount');
            
            $wageEarnings = $monthChi - $monthThu;
            $totalAvailable = $baseSalary + $wageEarnings;

            // Get other advances (excluding this one)
            $otherAdvances = \App\Models\SalaryAdvance::where('staff_id', $staff->id)
                ->where('id', '!=', $salaryAdvance->id)
                ->whereYear('date', $currentMonth->year)
                ->whereMonth('date', $currentMonth->month)
                ->sum('from_earnings');

            $existingDebts = StaffAdjustment::where('staff_id', $staff->id)
                ->where('debt_amount', '>', 0)
                ->sum('debt_amount');

            $availableForAdvance = max(0, $totalAvailable - $otherAdvances - $existingDebts);

            // Recalculate split
            $fromEarnings = min($newAmount, $availableForAdvance);
            $fromCompany = $newAmount - $fromEarnings;

            // Handle transaction updates for company debt
            $oldTransactionIds = $salaryAdvance->transaction_ids ?? [];
            $newTransactionIds = [];

            if ($fromCompany > 0) {
                // Need company debt transaction
                if (!empty($oldTransactionIds)) {
                    // Update existing transaction
                    $existingTransaction = Transaction::find($oldTransactionIds[0]);
                    if ($existingTransaction) {
                        $existingTransaction->update([
                            'amount' => $fromCompany,
                            'note' => "Ứng lương (nợ công ty) - {$staff->full_name}",
                            'date' => now(),
                        ]);
                        $newTransactionIds[] = $existingTransaction->id;
                    } else {
                        // Old transaction deleted, create new one
                        $transaction = Transaction::create([
                            'incident_id' => null,
                            'vehicle_id' => null,
                            'staff_id' => $staff->id,
                            'type' => 'chi',
                            'category' => 'ứng_lương_nợ',
                            'amount' => $fromCompany,
                            'date' => now(),
                            'note' => "Ứng lương (nợ công ty) - {$staff->full_name}",
                            'payment_method' => 'chuyển khoản',
                            'recorded_by' => auth()->id(),
                        ]);
                        $newTransactionIds[] = $transaction->id;
                    }
                } else {
                    // No old transaction, create new one
                    $transaction = Transaction::create([
                        'incident_id' => null,
                        'vehicle_id' => null,
                        'staff_id' => $staff->id,
                        'type' => 'chi',
                        'category' => 'ứng_lương_nợ',
                        'amount' => $fromCompany,
                        'date' => now(),
                        'note' => "Ứng lương (nợ công ty) - {$staff->full_name}",
                        'payment_method' => 'chuyển khoản',
                        'recorded_by' => auth()->id(),
                    ]);
                    $newTransactionIds[] = $transaction->id;
                }
            } else {
                // No debt needed, delete old transaction if exists
                if (!empty($oldTransactionIds)) {
                    Transaction::whereIn('id', $oldTransactionIds)->delete();
                }
            }

            $salaryAdvance->update([
                'amount' => $newAmount,
                'from_earnings' => $fromEarnings,
                'from_company' => $fromCompany,
                'debt_amount' => $fromCompany,
                'note' => $validated['note'] ?? $salaryAdvance->note,
                'transaction_ids' => $newTransactionIds,
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Đã cập nhật ứng lương thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete salary advance
     */
    public function destroySalaryAdvance(\App\Models\SalaryAdvance $salaryAdvance)
    {
        try {
            DB::beginTransaction();

            $staff = $salaryAdvance->staff;
            $amount = $salaryAdvance->amount;
            
            // Delete related transactions (company debt transactions)
            if ($salaryAdvance->transaction_ids && !empty($salaryAdvance->transaction_ids)) {
                Transaction::whereIn('id', $salaryAdvance->transaction_ids)->delete();
            }
            
            $salaryAdvance->delete();

            DB::commit();

            return redirect()->back()
                ->with('success', "Đã hủy ứng lương " . number_format($amount, 0, ',', '.') . "đ thành công!");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}


