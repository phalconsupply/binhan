<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountingPeriod;
use App\Models\Transaction;
use Carbon\Carbon;

class AccountingPeriodController extends Controller
{
    public function index()
    {
        // Lấy 12 tháng gần nhất
        $periods = collect();
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $period = AccountingPeriod::forDate($date);
            
            // Đếm số giao dịch trong tháng
            $txCount = Transaction::whereYear('date', $period->year)
                ->whereMonth('date', $period->month)
                ->count();
            
            $period->transaction_count = $txCount;
            $periods->push($period);
        }
        
        return view('accounting-periods.index', compact('periods'));
    }

    public function close($id)
    {
        try {
            $period = AccountingPeriod::findOrFail($id);
            
            if ($period->status === 'locked') {
                return back()->with('error', 'Không thể đóng kỳ đã khóa.');
            }
            
            $period->close();
            
            return back()->with('success', "Đã đóng kỳ {$period->display_name} thành công!");
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function lock($id)
    {
        try {
            $period = AccountingPeriod::findOrFail($id);
            $period->lock();
            
            return back()->with('success', "Đã khóa kỳ {$period->display_name} thành công!");
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function reopen($id)
    {
        try {
            $period = AccountingPeriod::findOrFail($id);
            
            if ($period->status === 'locked') {
                return back()->with('error', 'Không thể mở lại kỳ đã khóa. Phải unlock trước.');
            }
            
            $period->reopen();
            
            return back()->with('success', "Đã mở lại kỳ {$period->display_name} thành công!");
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function unlock($id)
    {
        try {
            $period = AccountingPeriod::findOrFail($id);
            $period->unlock();
            
            return back()->with('success', "Đã mở khóa kỳ {$period->display_name} thành công!");
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
