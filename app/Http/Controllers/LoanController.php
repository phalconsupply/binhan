<?php

namespace App\Http\Controllers;

use App\Models\LoanProfile;
use App\Models\LoanInterestAdjustment;
use App\Models\Vehicle;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage vehicles']);
    }

    /**
     * Store a newly created loan profile
     */
    public function store(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'cif' => 'nullable|string|max:50',
            'contract_number' => 'required|string|max:100',
            'bank_name' => 'required|string|max:100',
            'principal_amount' => 'required|numeric|min:0',
            'term_months' => 'required|integer|min:1|max:360',
            'disbursement_date' => 'required|date',
            'base_interest_rate' => 'required|numeric|min:0|max:100',
            'payment_day' => 'required|integer|min:1|max:28',
            'first_period_interest_only' => 'nullable|boolean',
            'note' => 'nullable|string',
        ]);

        // Check if vehicle already has an active loan
        $existingLoan = LoanProfile::where('vehicle_id', $vehicle->id)
            ->where('status', 'active')
            ->first();

        if ($existingLoan) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Xe này đã có khoản vay đang hoạt động. Vui lòng đóng khoản vay hiện tại trước khi tạo khoản mới.');
        }

        try {
            DB::beginTransaction();

            // Add vehicle_id and calculate total periods
            $validated['vehicle_id'] = $vehicle->id;
            $validated['total_periods'] = $validated['term_months'];
            $validated['created_by'] = auth()->id();

            // Create loan profile
            $loan = LoanProfile::create($validated);

            // Generate repayment schedule
            $loan->generateRepaymentSchedule();

            // Log activity
            Log::info('Loan profile created', [
                'loan_id' => $loan->id,
                'vehicle_id' => $loan->vehicle_id,
                'principal_amount' => $loan->principal_amount,
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('vehicles.show', $loan->vehicle_id)
                ->with('success', 'Đã tạo khoản vay và lịch trả nợ thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create loan profile', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Update loan profile
     */
    public function update(Request $request, LoanProfile $loan)
    {
        $validated = $request->validate([
            'cif' => 'nullable|string|max:50',
            'contract_number' => 'required|string|max:100',
            'bank_name' => 'required|string|max:100',
            'payment_day' => 'required|integer|min:1|max:28',
            'note' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();

        $loan->update($validated);

        Log::info('Loan profile updated', [
            'loan_id' => $loan->id,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('vehicles.show', $loan->vehicle_id)
            ->with('success', 'Đã cập nhật thông tin khoản vay!');
    }

    /**
     * Adjust interest rate
     */
    public function adjustInterest(Request $request, LoanProfile $loan)
    {
        $validated = $request->validate([
            'new_interest_rate' => 'required|numeric|min:0|max:100',
            'effective_date' => 'required|date|after:' . $loan->disbursement_date,
            'note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Create interest adjustment record
            LoanInterestAdjustment::create([
                'loan_id' => $loan->id,
                'old_interest_rate' => $loan->getCurrentInterestRate(),
                'new_interest_rate' => $validated['new_interest_rate'],
                'effective_date' => $validated['effective_date'],
                'note' => $validated['note'],
                'created_by' => auth()->id(),
            ]);

            // Regenerate schedule for periods from effective date onwards
            $this->regenerateScheduleFromDate($loan, $validated['effective_date']);

            Log::info('Loan interest rate adjusted', [
                'loan_id' => $loan->id,
                'old_rate' => $loan->getCurrentInterestRate(),
                'new_rate' => $validated['new_interest_rate'],
                'effective_date' => $validated['effective_date'],
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('vehicles.show', $loan->vehicle_id)
                ->with('success', 'Đã điều chỉnh lãi suất thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to adjust interest rate', [
                'loan_id' => $loan->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Mark loan as paid off (early payment)
     */
    public function payOff(Request $request, LoanProfile $loan)
    {
        $validated = $request->validate([
            'payment_type' => 'required|in:full,partial',
            'partial_amount' => 'required_if:payment_type,partial|nullable|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $vehicle = $loan->vehicle;
            $paymentType = $validated['payment_type'];

            if ($paymentType === 'full') {
                // Full payment - close the loan
                $remainingAmount = $loan->schedules()
                    ->where('status', 'pending')
                    ->sum('total');

                // Create transaction for full payment
                Transaction::create([
                    'vehicle_id' => $loan->vehicle_id,
                    'type' => 'chi',
                    'category' => 'trả_nợ_sớm',
                    'amount' => $remainingAmount,
                    'method' => 'bank',
                    'recorded_by' => auth()->id(),
                    'date' => now(),
                    'note' => 'Trả nợ sớm (đóng khoản vay) xe ' . $vehicle->license_plate . ' - ' . $loan->bank_name . ($validated['note'] ? ' - ' . $validated['note'] : ''),
                ]);

                // Mark loan as paid off
                $loan->markAsPaidOff();

                Log::info('Loan paid off early (full)', [
                    'loan_id' => $loan->id,
                    'vehicle_id' => $loan->vehicle_id,
                    'remaining_amount' => $remainingAmount,
                    'user_id' => auth()->id(),
                ]);

                DB::commit();

                return redirect()->route('vehicles.show', $loan->vehicle_id)
                    ->with('success', 'Đã đóng khoản vay hoàn toàn! Tổng số tiền: ' . number_format($remainingAmount, 0, ',', '.') . 'đ');

            } else {
                // Partial payment - reduce principal and recalculate schedule
                $partialAmount = $validated['partial_amount'];

                if ($partialAmount > $loan->remaining_balance) {
                    return redirect()->back()
                        ->with('error', 'Số tiền trả vượt quá số dư gốc còn lại!');
                }

                // Create transaction for partial payment
                Transaction::create([
                    'vehicle_id' => $loan->vehicle_id,
                    'type' => 'chi',
                    'category' => 'trả_nợ_gốc',
                    'amount' => $partialAmount,
                    'method' => 'bank',
                    'recorded_by' => auth()->id(),
                    'date' => now(),
                    'note' => 'Trả nợ gốc sớm (một phần) xe ' . $vehicle->license_plate . ' - ' . $loan->bank_name . ($validated['note'] ? ' - ' . $validated['note'] : ''),
                ]);

                // Update remaining balance
                $loan->remaining_balance -= $partialAmount;
                $loan->save();

                // Recalculate monthly principal for remaining periods
                $pendingSchedules = $loan->schedules()
                    ->where('status', 'pending')
                    ->orderBy('period_no')
                    ->get();

                $remainingPeriods = $pendingSchedules->count();
                $newMonthlyPrincipal = $loan->remaining_balance / $remainingPeriods;

                // Update each pending schedule
                foreach ($pendingSchedules as $schedule) {
                    $newPrincipal = $newMonthlyPrincipal;
                    $newTotal = $newPrincipal + $schedule->interest;

                    $schedule->update([
                        'principal' => $newPrincipal,
                        'total' => $newTotal,
                    ]);
                }

                Log::info('Loan partial payment', [
                    'loan_id' => $loan->id,
                    'vehicle_id' => $loan->vehicle_id,
                    'partial_amount' => $partialAmount,
                    'new_remaining_balance' => $loan->remaining_balance,
                    'user_id' => auth()->id(),
                ]);

                DB::commit();

                return redirect()->route('vehicles.show', $loan->vehicle_id)
                    ->with('success', 'Đã trả nợ gốc ' . number_format($partialAmount, 0, ',', '.') . 'đ. Lịch trả nợ đã được cập nhật!');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process payment', [
                'loan_id' => $loan->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Cancel/Delete loan profile
     */
    public function destroy(LoanProfile $loan)
    {
        try {
            DB::beginTransaction();

            $vehicleId = $loan->vehicle_id;

            // Check if any payments have been made
            $paidCount = $loan->schedules()->where('status', 'paid')->count();
            
            if ($paidCount > 0) {
                return redirect()->back()
                    ->with('error', 'Không thể xóa khoản vay đã có lịch sử thanh toán!');
            }

            // Delete schedules and loan
            $loan->schedules()->delete();
            $loan->delete();

            Log::info('Loan profile deleted', [
                'loan_id' => $loan->id,
                'vehicle_id' => $vehicleId,
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('vehicles.show', $vehicleId)
                ->with('success', 'Đã xóa khoản vay!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete loan', [
                'loan_id' => $loan->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate schedule from a specific date (when interest rate changes)
     */
    protected function regenerateScheduleFromDate(LoanProfile $loan, $effectiveDate)
    {
        $schedules = $loan->schedules()
            ->where('due_date', '>=', $effectiveDate)
            ->where('status', 'pending')
            ->orderBy('period_no')
            ->get();

        foreach ($schedules as $schedule) {
            // Recalculate interest with new rate
            $interestRate = $loan->getInterestRateForDate($schedule->due_date);
            
            // Calculate remaining balance at this period
            $paidPeriods = $loan->schedules()->where('period_no', '<', $schedule->period_no)->where('status', 'paid')->count();
            $remainingBalance = $loan->principal_amount - ($loan->getMonthlyPrincipal() * $paidPeriods);
            
            // Recalculate interest
            $interest = $remainingBalance * ($interestRate / 100 / 12);
            $total = $schedule->principal + $interest;

            $schedule->update([
                'interest' => $interest,
                'total' => $total,
                'interest_rate' => $interestRate,
            ]);
        }
    }

    /**
     * Process repayments for a specific loan
     */
    public function processRepayments(Request $request, LoanProfile $loan)
    {
        try {
            $today = now()->startOfDay();
            $processedCount = 0;
            $insufficientFundsCount = 0;

            // Get all schedules due today or overdue that are NOT yet paid
            $dueSchedules = $loan->schedules()
                ->where(function($query) {
                    $query->where('status', 'pending')
                          ->orWhere('status', 'overdue');
                })
                ->where('due_date', '<=', now()->startOfDay())
                ->whereNull('paid_date')
                ->orderBy('due_date')
                ->get();

            if ($dueSchedules->isEmpty()) {
                return redirect()->back()->with('info', 'Không có kỳ nào đến hạn cần xử lý.');
            }

            DB::beginTransaction();

            foreach ($dueSchedules as $schedule) {
                // Skip if already has transaction_id (already processed)
                if ($schedule->transaction_id) {
                    continue;
                }

                $vehicle = $loan->vehicle;

                // Calculate available balance
                $vehicleProfit = $vehicle->transactions()
                    ->where('type', 'thu')
                    ->sum('amount');

                $vehicleExpenses = $vehicle->transactions()
                    ->where('type', 'chi')
                    ->sum('amount');

                $currentBalance = $vehicleProfit - $vehicleExpenses;

                // Create principal transaction if amount > 0
                $principalTransaction = null;
                if ($schedule->principal > 0) {
                    $principalTransaction = Transaction::create([
                        'vehicle_id' => $vehicle->id,
                        'type' => 'chi',
                        'category' => 'trả_nợ_gốc',
                        'amount' => $schedule->principal,
                        'method' => 'other',
                        'recorded_by' => auth()->id(),
                        'date' => $today,
                        'note' => "Trả nợ gốc kỳ {$schedule->period_no}/{$loan->total_periods} - {$loan->bank_name}",
                    ]);
                }

                // Create interest transaction
                Transaction::create([
                    'vehicle_id' => $vehicle->id,
                    'type' => 'chi',
                    'category' => 'trả_nợ_lãi',
                    'amount' => $schedule->interest,
                    'method' => 'other',
                    'recorded_by' => auth()->id(),
                    'date' => $today,
                    'note' => "Trả lãi kỳ {$schedule->period_no}/{$loan->total_periods} - {$loan->bank_name} (lãi suất {$schedule->interest_rate}%)",
                ]);

                // Mark schedule as paid with transaction reference
                $schedule->markAsPaid($principalTransaction ? $principalTransaction->id : null);

                // Update remaining balance only if principal was paid
                if ($schedule->principal > 0) {
                    $loan->remaining_balance -= $schedule->principal;
                    $loan->save();
                }

                $processedCount++;

                // Check if insufficient funds
                if ($currentBalance < $schedule->total) {
                    $insufficientFundsCount++;
                }
            }

            DB::commit();

            if ($processedCount == 0) {
                return redirect()->back()->with('info', 'Tất cả các kỳ đến hạn đã được xử lý trước đó.');
            }

            $message = "Đã xử lý {$processedCount} kỳ thanh toán.";
            if ($insufficientFundsCount > 0) {
                $message .= " Cảnh báo: {$insufficientFundsCount} kỳ không đủ số dư.";
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Process repayments error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xử lý thanh toán: ' . $e->getMessage());
        }
    }
}
