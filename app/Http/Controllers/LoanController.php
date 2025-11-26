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
            'note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $vehicle = $loan->vehicle;

            // Calculate total remaining amount
            $remainingAmount = $loan->schedules()
                ->where('status', 'pending')
                ->sum('total');

            // Create transaction for early payment
            $transaction = Transaction::create([
                'vehicle_id' => $loan->vehicle_id,
                'type' => 'chi',
                'category' => 'trả_nợ_sớm',
                'amount' => $remainingAmount,
                'method' => 'bank',
                'recorded_by' => auth()->id(),
                'date' => now(),
                'note' => 'Trả nợ sớm xe ' . $vehicle->license_plate . ' - ' . $loan->bank_name . ($validated['note'] ? ' - ' . $validated['note'] : ''),
            ]);

            // Mark loan as paid off
            $loan->markAsPaidOff();

            Log::info('Loan paid off early', [
                'loan_id' => $loan->id,
                'vehicle_id' => $loan->vehicle_id,
                'remaining_amount' => $remainingAmount,
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('vehicles.show', $loan->vehicle_id)
                ->with('success', 'Đã đóng khoản vay trước hạn! Tổng số tiền: ' . number_format($remainingAmount, 0, ',', '.') . 'đ');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to pay off loan', [
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
}
