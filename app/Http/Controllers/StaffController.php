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
        $query = Transaction::where('staff_id', $staff->id)
            ->where('type', 'chi')
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

        // Wage earnings from transactions
        $wageEarningsTotal = Transaction::where('staff_id', $staff->id)
            ->where('type', 'chi');
        
        if ($request->filled('from_date')) {
            $wageEarningsTotal->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $wageEarningsTotal->whereDate('date', '<=', $request->to_date);
        }
        
        $wageEarningsTotal = $wageEarningsTotal->sum('amount');

        // Month earnings
        $monthWageEarnings = Transaction::where('staff_id', $staff->id)
            ->where('type', 'chi')
            ->thisMonth()
            ->sum('amount');
        
        $monthBaseSalary = $staff->base_salary ?? 0;

        // Get adjustments for current month
        $currentMonth = now()->startOfMonth();
        $monthAdjustments = StaffAdjustment::where('staff_id', $staff->id)
            ->forMonth($currentMonth)
            ->get();

        $monthAdjustmentAdditions = $monthAdjustments->where('type', 'addition')->where('status', 'applied')->sum('amount');
        $monthAdjustmentDeductions = $monthAdjustments->where('type', 'deduction')->where('status', 'applied')->sum('amount');

        // Statistics
        $stats = [
            'base_salary' => $staff->base_salary ?? 0,
            'base_salary_total' => $baseSalaryTotal,
            'wage_earnings_total' => $wageEarningsTotal,
            'total_earnings' => $baseSalaryTotal + $wageEarningsTotal,
            'month_base_salary' => $monthBaseSalary,
            'month_wage_earnings' => $monthWageEarnings,
            'month_adjustments' => $monthAdjustmentAdditions - $monthAdjustmentDeductions,
            'month_total_earnings' => $monthBaseSalary + $monthWageEarnings + $monthAdjustmentAdditions - $monthAdjustmentDeductions,
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

        // Get pending debts
        $pendingDebts = StaffAdjustment::where('staff_id', $staff->id)
            ->debt()
            ->with('creator')
            ->orderBy('created_at', 'asc')
            ->get();

        $totalDebt = $pendingDebts->sum('debt_amount');

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
                    ]);
                    $transactionIds[] = $transaction->id;
                    $adjustment->update([
                        'from_incident_amount' => 0,
                        'from_company_amount' => $amount,
                    ]);
                }

                // Mark as applied
                $adjustment->update([
                    'status' => 'applied',
                    'applied_at' => now(),
                    'transaction_ids' => $transactionIds,
                ]);

                // Try to pay off any pending debts
                $this->processDebtPayment($staff);
                
            } else {
                // For deduction, check balance and create debt if needed
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

                if ($availableBalance < $amount) {
                    // Not enough balance, create debt
                    $debtAmount = $amount - max(0, $availableBalance);
                    $adjustment->update([
                        'status' => 'debt',
                        'debt_amount' => $debtAmount,
                    ]);
                } else {
                    // Enough balance, mark as applied (no transaction created for deduction)
                    $adjustment->update([
                        'status' => 'applied',
                        'applied_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('staff.earnings', $staff)
                ->with('success', 'Đã thêm điều chỉnh thu nhập thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            
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
        // Get all pending debts ordered by oldest first
        $debts = StaffAdjustment::where('staff_id', $staff->id)
            ->debt()
            ->orderBy('created_at', 'asc')
            ->get();

        if ($debts->isEmpty()) {
            return;
        }

        // Calculate available balance from additions not yet used for debt payment
        $currentMonth = now()->startOfMonth();
        
        $baseSalary = $staff->base_salary ?? 0;
        $wageEarnings = Transaction::where('staff_id', $staff->id)
            ->where('type', 'chi')
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->sum('amount');

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

        $availableBalance = $baseSalary + $wageEarnings + $additions - $appliedDeductions;

        // Pay off debts with available balance
        foreach ($debts as $debt) {
            if ($availableBalance <= 0) {
                break;
            }

            if ($availableBalance >= $debt->debt_amount) {
                // Can pay off entire debt
                $availableBalance -= $debt->debt_amount;
                $debt->update([
                    'status' => 'applied',
                    'debt_amount' => 0,
                    'applied_at' => now(),
                ]);
            } else {
                // Partial payment
                $debt->update([
                    'debt_amount' => $debt->debt_amount - $availableBalance,
                ]);
                $availableBalance = 0;
            }
        }
    }
}
