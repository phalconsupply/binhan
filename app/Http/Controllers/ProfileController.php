<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Staff;
use App\Models\Transaction;
use App\Models\StaffAdjustment;
use App\Models\SalaryAdvance;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Check if user is staff (driver, medical_staff, or manager)
        $staff = Staff::where('user_id', $user->id)->first();
        $earnings = null;
        $stats = [];
        $adjustments = collect();
        $pendingDebts = collect();
        $totalDebt = 0;
        
        if ($staff && in_array($staff->staff_type, ['driver', 'medical_staff', 'manager'])) {
            // Get earnings transactions (excluding salary advances)
            $earnings = Transaction::where('staff_id', $staff->id)
                ->where('type', 'chi')
                ->where(function($q) {
                    $q->where('category', '!=', 'ứng_lương')
                      ->orWhereNull('category');
                })
                ->with(['incident.vehicle', 'incident.patient'])
                ->orderBy('date', 'desc')
                ->paginate(20);
            
            // Calculate months worked
            $monthsWorked = 0;
            if ($staff->hire_date) {
                $monthsWorked = $staff->hire_date->diffInMonths(now()) + 1;
            }
            
            // Base salary total
            $baseSalaryTotal = $staff->base_salary ? ($staff->base_salary * $monthsWorked) : 0;
            
            // Wage earnings (CHI - THU), excluding salary advances
            $chiTotal = Transaction::where('staff_id', $staff->id)
                ->where('type', 'chi')
                ->where(function($query) {
                    $query->whereNotIn('category', ['ứng_lương', 'ứng_lương_nợ'])
                          ->orWhereNull('category');
                })
                ->sum('amount');
            
            $thuTotal = Transaction::where('staff_id', $staff->id)
                ->where('type', 'thu')
                ->where(function($query) {
                    $query->whereNotIn('category', ['ứng_lương', 'ứng_lương_nợ'])
                          ->orWhereNull('category');
                })
                ->sum('amount');
            
            $wageEarningsTotal = $chiTotal - $thuTotal;
            
            // Month earnings
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
            
            // Get adjustments for current month
            $currentMonth = now()->startOfMonth();
            $monthAdjustments = StaffAdjustment::where('staff_id', $staff->id)
                ->forMonth($currentMonth)
                ->get();
            
            $monthAdjustmentAdditions = $monthAdjustments->where('type', 'addition')->where('status', 'applied')->sum('amount');
            $monthAdjustmentDeductions = $monthAdjustments->where('type', 'deduction')->where('status', 'applied')->sum('amount');
            
            // Calculate salary advances for current month
            $monthSalaryAdvances = SalaryAdvance::where('staff_id', $staff->id)
                ->forMonth($currentMonth)
                ->sum('from_earnings');
            
            // Statistics
            $stats = [
                'base_salary' => $staff->base_salary ?? 0,
                'base_salary_total' => $baseSalaryTotal,
                'wage_earnings_total' => $wageEarningsTotal,
                'total_earnings' => $baseSalaryTotal + $wageEarningsTotal,
                'month_base_salary' => $monthBaseSalary,
                'month_wage_earnings' => $monthWageEarnings,
                'month_adjustments' => $monthAdjustmentAdditions - $monthAdjustmentDeductions,
                'month_salary_advances' => $monthSalaryAdvances,
                'month_total_earnings' => $monthBaseSalary + $monthWageEarnings - $monthSalaryAdvances,
                'total_trips' => $staff->incidents()->count(),
                'months_worked' => $monthsWorked,
            ];
            
            // Get adjustments with details
            $adjustments = StaffAdjustment::where('staff_id', $staff->id)
                ->forMonth($currentMonth)
                ->with(['creator', 'incident'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Get pending debts
            $pendingAdjustmentDebts = StaffAdjustment::where('staff_id', $staff->id)
                ->debt()
                ->with('creator')
                ->orderBy('created_at', 'asc')
                ->get();
            
            $pendingSalaryAdvanceDebts = SalaryAdvance::where('staff_id', $staff->id)
                ->debt()
                ->with('approvedBy')
                ->orderBy('date', 'asc')
                ->get();
            
            $pendingDebts = $pendingAdjustmentDebts->merge($pendingSalaryAdvanceDebts)
                ->sortBy('created_at');
            
            $totalDebt = $pendingAdjustmentDebts->sum('debt_amount') + 
                        $pendingSalaryAdvanceDebts->sum('debt_amount');
        }
        
        return view('profile.edit', [
            'user' => $user,
            'staff' => $staff,
            'earnings' => $earnings,
            'stats' => $stats,
            'adjustments' => $adjustments,
            'pendingDebts' => $pendingDebts,
            'totalDebt' => $totalDebt,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
