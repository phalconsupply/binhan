<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Vehicle;
use App\Exceptions\InsufficientBalanceException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AccountBalanceService
{
    /**
     * Kiểm tra số dư tài khoản có đủ để thực hiện giao dịch không
     * 
     * @throws InsufficientBalanceException
     */
    public static function validateSufficientBalance(
        string $fromAccount,
        float $amount,
        bool $allowNegative = false
    ): void {
        // Skip validation for revenue accounts (customer, income)
        if (in_array($fromAccount, ['customer', 'income', 'external'])) {
            return;
        }

        $currentBalance = self::getCurrentBalance($fromAccount);

        if (!$allowNegative && $currentBalance < $amount) {
            throw new InsufficientBalanceException(
                $fromAccount,
                $currentBalance,
                $amount
            );
        }
    }

    /**
     * Xác định tài khoản nguồn và đích dựa trên loại giao dịch
     */
    public static function determineAccounts(Transaction $transaction): array
    {
        $fromAccount = null;
        $toAccount = null;

        switch ($transaction->type) {
            case 'thu':
                // Thu tiền từ khách hàng vào xe
                $fromAccount = 'customer';
                $toAccount = $transaction->vehicle_id ? "vehicle_{$transaction->vehicle_id}" : 'company_fund';
                break;

            case 'chi':
                // Chi tiền từ xe ra ngoài (nhân viên, đối tác)
                if ($transaction->category === 'chi_từ_dự_kiến') {
                    // Chi từ quỹ dự kiến của công ty
                    $fromAccount = 'company_reserved';
                } else {
                    // Chi từ tài khoản xe
                    $fromAccount = $transaction->vehicle_id ? "vehicle_{$transaction->vehicle_id}" : 'company_fund';
                }
                
                // Xác định tài khoản nhận
                if ($transaction->staff_id) {
                    $toAccount = "staff_{$transaction->staff_id}";
                } elseif ($transaction->category === 'xăng_xe' || $transaction->category === 'sửa_chữa') {
                    $toAccount = 'partner';
                } else {
                    $toAccount = 'external';
                }
                break;

            case 'du_kien_chi':
                // Dự kiến chi: Từ quỹ công ty → Quỹ dự kiến chi
                $fromAccount = 'company_fund';
                $toAccount = 'company_reserved';
                break;

            case 'nop_quy':
                // Nộp quỹ: Logic phụ thuộc xe có chủ hay không
                $fromAccount = 'income'; // Nguồn thu (từ lợi nhuận xe)
                
                if ($transaction->vehicle_id) {
                    $vehicle = Vehicle::find($transaction->vehicle_id);
                    
                    if ($vehicle && $vehicle->hasOwner()) {
                        // Xe CÓ chủ → Tiền vào số dư xe (không vào công ty)
                        $toAccount = "vehicle_{$transaction->vehicle_id}";
                    } else {
                        // Xe KHÔNG chủ → Tiền vào công ty
                        $toAccount = 'company_fund';
                    }
                } else {
                    // Không có xe → vào công ty
                    $toAccount = 'company_fund';
                }
                break;

            case 'vay_cong_ty':
                // Vay từ công ty: Công ty → Xe
                $fromAccount = 'company_fund';
                $toAccount = $transaction->vehicle_id ? "vehicle_{$transaction->vehicle_id}" : 'unknown';
                break;

            case 'tra_cong_ty':
                // Trả nợ công ty: Xe → Công ty
                $fromAccount = $transaction->vehicle_id ? "vehicle_{$transaction->vehicle_id}" : 'unknown';
                $toAccount = 'company_fund';
                break;
        }

        return [
            'from_account' => $fromAccount,
            'to_account' => $toAccount,
        ];
    }

    /**
     * Tính số dư của một tài khoản tại thời điểm trước transaction
     * CHÚ Ý: Tính theo DATE, không phải ID để hỗ trợ thêm GD quá khứ
     */
    public static function calculateBalance(string $accountName, $beforeTransactionId = null, $beforeDate = null): float
    {
        $query = Transaction::query();

        if ($beforeTransactionId) {
            // Lấy transaction hiện tại để biết date
            $currentTx = Transaction::find($beforeTransactionId);
            
            if ($currentTx) {
                // Chỉ tính các GD có:
                // - date < date của GD hiện tại
                // - HOẶC date = date của GD hiện tại NHƯNG id < id hiện tại
                $query->where(function($q) use ($currentTx) {
                    $q->where('date', '<', $currentTx->date)
                      ->orWhere(function($q2) use ($currentTx) {
                          $q2->where('date', '=', $currentTx->date)
                             ->where('id', '<', $currentTx->id);
                      });
                });
            } else {
                // Fallback: Nếu không tìm thấy transaction, dùng logic cũ
                $query->where('id', '<', $beforeTransactionId);
            }
        } elseif ($beforeDate) {
            // Tính số dư trước một ngày cụ thể
            $query->where('date', '<', $beforeDate);
        }

        $query->orderBy('date')->orderBy('id');

        $balance = 0;

        // Tính số dư dựa trên from_account và to_account
        $transactions = $query->get();

        foreach ($transactions as $tx) {
            // Nếu tài khoản là nguồn → trừ tiền
            if ($tx->from_account === $accountName) {
                $balance -= $tx->amount;
            }
            
            // Nếu tài khoản là đích → cộng tiền
            if ($tx->to_account === $accountName) {
                $balance += $tx->amount;
            }
        }

        return $balance;
    }

    /**
     * Cập nhật số dư cho transaction (với locking để tránh race condition)
     */
    public static function updateTransactionBalances(Transaction $transaction, bool $skipValidation = false): void
    {
        // Use cache lock để prevent race conditions
        $lockKey = "transaction_balance_update_{$transaction->id}";
        $lock = Cache::lock($lockKey, 10); // 10 seconds timeout

        if (!$lock->get()) {
            throw new \RuntimeException("Could not acquire lock for transaction balance update");
        }

        try {
            DB::transaction(function () use ($transaction, $skipValidation) {
                $accounts = self::determineAccounts($transaction);
                
                $fromAccount = $accounts['from_account'];
                $toAccount = $accounts['to_account'];

                // Validate sufficient balance before processing (chỉ với giao dịch chi tiền)
                // Skip validation khi recalculate để cho phép số dư âm trong lịch sử
                if (!$skipValidation && $fromAccount && !in_array($transaction->type, ['thu', 'nop_quy'])) {
                    self::validateSufficientBalance($fromAccount, $transaction->amount);
                }

                // Tính số dư trước giao dịch
                $fromBalanceBefore = $fromAccount ? self::calculateBalance($fromAccount, $transaction->id) : null;
                $toBalanceBefore = $toAccount ? self::calculateBalance($toAccount, $transaction->id) : null;

                // Tính số dư sau giao dịch
                $fromBalanceAfter = $fromBalanceBefore !== null ? $fromBalanceBefore - $transaction->amount : null;
                $toBalanceAfter = $toBalanceBefore !== null ? $toBalanceBefore + $transaction->amount : null;

                // Cập nhật transaction
                $transaction->updateQuietly([
                    'from_account' => $fromAccount,
                    'to_account' => $toAccount,
                    'from_balance_before' => $fromBalanceBefore,
                    'from_balance_after' => $fromBalanceAfter,
                    'to_balance_before' => $toBalanceBefore,
                    'to_balance_after' => $toBalanceAfter,
                ]);
            });
        } finally {
            $lock->release();
        }
    }

    /**
     * Lấy tên hiển thị của tài khoản
     */
    public static function getAccountDisplayName(string $accountName): string
    {
        if ($accountName === 'customer') {
            return '👤 Khách hàng';
        }

        if ($accountName === 'company_fund') {
            return '💰 Lợi nhuận công ty';
        }

        if ($accountName === 'company_reserved') {
            return '📊 Quỹ dự kiến chi';
        }

        if (str_starts_with($accountName, 'vehicle_')) {
            $vehicleId = str_replace('vehicle_', '', $accountName);
            $vehicle = Vehicle::find($vehicleId);
            return $vehicle ? "🚗 {$vehicle->license_plate}" : "🚗 Xe #{$vehicleId}";
        }

        if (str_starts_with($accountName, 'staff_')) {
            $staffId = str_replace('staff_', '', $accountName);
            $staff = \App\Models\Staff::find($staffId);
            return $staff ? "👤 {$staff->name}" : "👤 NV #{$staffId}";
        }

        if ($accountName === 'partner') {
            return '🤝 Đối tác';
        }

        if ($accountName === 'external') {
            return '💼 Bên ngoài';
        }

        return $accountName;
    }

    /**
     * Tái tính toán số dư cho tất cả transactions từ một ngày cụ thể
     * Dùng khi thêm/sửa/xóa GD quá khứ
     */
    public static function recalculateBalancesFromDate($fromDate): void
    {
        DB::transaction(function () use ($fromDate) {
            // Lấy tất cả transactions từ ngày đó trở về sau
            // SẮP XẾP THEO DATE, ID để đảm bảo đúng thứ tự thời gian
            $transactions = Transaction::where('date', '>=', $fromDate)
                ->orderBy('date')
                ->orderBy('id')
                ->get();

            foreach ($transactions as $transaction) {
                // Skip validation để cho phép số dư âm trong lịch sử
                self::updateTransactionBalances($transaction, true);
            }
        });
    }

    /**
     * Tái tính toán số dư cho tất cả transactions (dùng khi migrate data)
     */
    public static function recalculateAllBalances(): void
    {
        DB::transaction(function () {
            $transactions = Transaction::orderBy('date')->orderBy('id')->get();

            foreach ($transactions as $transaction) {
                // Skip validation để cho phép số dư âm trong lịch sử
                self::updateTransactionBalances($transaction, true);
            }
        });
    }

    /**
     * Lấy số dư hiện tại của tài khoản
     */
    public static function getCurrentBalance(string $accountName): float
    {
        return self::calculateBalance($accountName);
    }

    /**
     * Lấy tổng quan số dư các tài khoản
     */
    public static function getBalancesSummary(): array
    {
        $companyFund = self::getCurrentBalance('company_fund');
        $companyReserved = self::getCurrentBalance('company_reserved');

        $vehicles = Vehicle::all();
        $vehicleBalances = [];
        
        foreach ($vehicles as $vehicle) {
            $vehicleBalances[$vehicle->id] = [
                'vehicle' => $vehicle,
                'balance' => self::getCurrentBalance("vehicle_{$vehicle->id}"),
            ];
        }

        return [
            'company_fund' => $companyFund,
            'company_reserved' => $companyReserved,
            'company_available' => $companyFund - $companyReserved,
            'vehicles' => $vehicleBalances,
        ];
    }
}
