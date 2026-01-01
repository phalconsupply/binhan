<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionLifecycleService
{
    /**
     * Đảo ngược giao dịch (tạo giao dịch ngược lại)
     * Đây là cách an toàn nhất - giữ nguyên lịch sử
     */
    public function reverseTransaction(Transaction $transaction, string $reason): Transaction
    {
        if ($transaction->lifecycle_status === 'reversed') {
            throw new \Exception("Giao dịch này đã được đảo ngược rồi!");
        }
        
        if ($transaction->lifecycle_status === 'cancelled') {
            throw new \Exception("Không thể đảo ngược giao dịch đã hủy!");
        }
        
        if ($transaction->is_locked) {
            throw new \Exception("Giao dịch đã bị khóa, không thể đảo ngược!");
        }
        
        return DB::transaction(function () use ($transaction, $reason) {
            // Tạo giao dịch đảo ngược (swap from/to)
            $reversalTransaction = Transaction::create([
                'code' => $this->generateReversalCode($transaction->code),
                'incident_id' => $transaction->incident_id,
                'vehicle_id' => $transaction->vehicle_id,
                'vehicle_maintenance_id' => $transaction->vehicle_maintenance_id,
                'staff_id' => $transaction->staff_id,
                'type' => $transaction->type === 'thu' ? 'chi' : 'thu', // Đảo ngược loại
                'category' => $transaction->category,
                'transaction_category' => $transaction->transaction_category,
                'amount' => $transaction->amount,
                'method' => $transaction->method,
                'payment_method' => $transaction->payment_method,
                'note' => "ĐẢONGU: {$transaction->code} - Lý do: {$reason}",
                'recorded_by' => Auth::id() ?? $transaction->recorded_by ?? 1,
                'date' => now(),
                'is_active' => true,
                
                // SWAP from/to accounts (đảo ngược luồng tiền)
                'from_account_id' => $transaction->to_account_id,
                'to_account_id' => $transaction->from_account_id,
                'from_account' => $transaction->to_account,
                'to_account' => $transaction->from_account,
                
                // Lifecycle
                'lifecycle_status' => 'active',
                'reverses_transaction_id' => $transaction->id,
                'modification_reason' => $reason,
                'modified_by' => Auth::id() ?? $transaction->recorded_by ?? 1,
                'modified_at' => now(),
            ]);
            
            // Cập nhật số dư cho reversal transaction
            AccountBalanceService::updateTransactionBalances($reversalTransaction);
            
            // Tạo journal entries cho reversal
            $doubleEntryService = new DoubleEntryService();
            $doubleEntryService->createJournalEntries($reversalTransaction);
            
            // Đánh dấu giao dịch gốc là đã đảo ngược
            $transaction->update([
                'lifecycle_status' => 'reversed',
                'reversed_by_transaction_id' => $reversalTransaction->id,
                'modification_reason' => $reason,
                'modified_by' => Auth::id(),
                'modified_at' => now(),
            ]);
            
            return $reversalTransaction;
        });
    }
    
    /**
     * Soft delete - đánh dấu xóa nhưng không xóa thật
     * Dùng cho các giao dịch nhập sai hoàn toàn
     */
    public function softDeleteTransaction(Transaction $transaction, string $reason): void
    {
        if ($transaction->is_locked) {
            throw new \Exception("Giao dịch đã bị khóa, không thể xóa!");
        }
        
        if ($transaction->lifecycle_status === 'reversed') {
            throw new \Exception("Giao dịch đã được đảo ngược, không nên xóa. Hãy xóa cả 2 giao dịch (gốc + reversal)!");
        }
        
        // Use manual updates instead of nested transaction
        $transaction->update([
            'lifecycle_status' => 'cancelled',
            'modification_reason' => $reason,
            'modified_by' => Auth::id() ?? 1,
            'modified_at' => now(),
        ]);
        
        $transaction->delete(); // Soft delete
        
        // Recalculate balances (bỏ qua giao dịch đã xóa)
        // Note: This may need to be run separately in production
        // AccountBalanceService::recalculateAllBalances();
    }
    
    /**
     * Thay thế giao dịch - tạo giao dịch mới đúng, đánh dấu giao dịch cũ
     */
    public function replaceTransaction(Transaction $oldTransaction, array $newData, string $reason): Transaction
    {
        if ($oldTransaction->is_locked) {
            throw new \Exception("Giao dịch đã bị khóa, không thể thay thế!");
        }
        
        return DB::transaction(function () use ($oldTransaction, $newData, $reason) {
            // Tạo giao dịch mới
            $newTransaction = Transaction::create(array_merge($newData, [
                'recorded_by' => Auth::id(),
                'date' => now(),
                'lifecycle_status' => 'active',
                'modification_reason' => "Thay thế {$oldTransaction->code}: {$reason}",
                'modified_by' => Auth::id(),
                'modified_at' => now(),
            ]));
            
            // Cập nhật số dư và journal entries cho giao dịch mới
            AccountBalanceService::updateTransactionBalances($newTransaction);
            
            $doubleEntryService = new DoubleEntryService();
            $doubleEntryService->createJournalEntries($newTransaction);
            
            // Đánh dấu giao dịch cũ
            $oldTransaction->update([
                'lifecycle_status' => 'replaced',
                'replaced_by' => $newTransaction->id,
                'modification_reason' => $reason,
                'modified_by' => Auth::id(),
                'modified_at' => now(),
                'is_active' => false,
            ]);
            
            // Recalculate để loại bỏ ảnh hưởng của giao dịch cũ
            AccountBalanceService::recalculateAllBalances();
            
            return $newTransaction;
        });
    }
    
    /**
     * Khóa giao dịch - không cho sửa/xóa nữa
     * Dùng cho các giao dịch đã được audit hoặc đã đóng sổ
     */
    public function lockTransaction(Transaction $transaction, string $reason = null): void
    {
        $transaction->update([
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => Auth::id(),
            'modification_reason' => $reason ?? 'Khóa giao dịch để bảo vệ dữ liệu',
        ]);
    }
    
    /**
     * Mở khóa giao dịch
     */
    public function unlockTransaction(Transaction $transaction): void
    {
        $transaction->update([
            'is_locked' => false,
            'locked_at' => null,
            'locked_by' => null,
        ]);
    }
    
    /**
     * Khóa tất cả giao dịch trong khoảng thời gian (đóng sổ)
     */
    public function lockPeriod(\DateTime $startDate, \DateTime $endDate, string $reason): int
    {
        return Transaction::whereBetween('date', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->update([
                'is_locked' => true,
                'locked_at' => now(),
                'locked_by' => Auth::id(),
                'modification_reason' => $reason,
            ]);
    }
    
    /**
     * Generate mã giao dịch đảo ngược
     */
    protected function generateReversalCode(string $originalCode): string
    {
        $date = now()->format('Ymd');
        $time = now()->format('His');
        
        return "REV{$date}{$time}";
    }
    
    /**
     * Regenerate journal entries excluding soft deleted transactions
     */
    protected function regenerateJournalEntriesExcludingDeleted(): void
    {
        // Delete all journal entries
        TransactionLine::truncate();
        
        // Recreate only for active transactions
        $transactions = Transaction::whereNull('deleted_at')
            ->whereNotNull('from_account_id')
            ->whereNotNull('to_account_id')
            ->orderBy('id')
            ->get();
        
        $doubleEntryService = new DoubleEntryService();
        
        foreach ($transactions as $transaction) {
            $doubleEntryService->createJournalEntries($transaction);
        }
    }
    
    /**
     * Restore soft deleted transaction
     */
    public function restoreTransaction(int $transactionId): Transaction
    {
        $transaction = Transaction::withTrashed()->findOrFail($transactionId);
        
        if (!$transaction->trashed()) {
            throw new \Exception("Giao dịch này chưa bị xóa!");
        }
        
        DB::transaction(function () use ($transaction) {
            $transaction->restore();
            
            $transaction->update([
                'lifecycle_status' => 'active',
                'modification_reason' => 'Khôi phục giao dịch',
                'modified_by' => Auth::id(),
                'modified_at' => now(),
            ]);
            
            // Recalculate để tính lại số dư
            AccountBalanceService::recalculateAllBalances();
            
            // Regenerate journal entries
            $doubleEntryService = new DoubleEntryService();
            $doubleEntryService->createJournalEntries($transaction);
        });
        
        return $transaction;
    }
    
    /**
     * Xóa CẢ CẶP giao dịch reversal (gốc + đảo ngược) an toàn
     * Dùng khi cả 2 giao dịch đều không cần thiết
     */
    public function deleteReversalPair(Transaction $transaction, string $reason): void
    {
        // Tìm cặp giao dịch
        if ($transaction->lifecycle_status === 'reversed') {
            // Đây là giao dịch gốc đã bị reverse
            $original = $transaction;
            $reversal = Transaction::find($original->reversed_by_transaction_id);
        } elseif ($transaction->reverses_transaction_id) {
            // Đây là giao dịch reversal
            $reversal = $transaction;
            $original = Transaction::find($reversal->reverses_transaction_id);
        } else {
            throw new \Exception("Giao dịch này không phải là một cặp reversal!");
        }
        
        if (!$original || !$reversal) {
            throw new \Exception("Không tìm thấy cặp giao dịch reversal!");
        }
        
        if ($original->is_locked || $reversal->is_locked) {
            throw new \Exception("Một trong hai giao dịch đã bị khóa, không thể xóa!");
        }
        
        // Xóa cả 2
        $original->update([
            'lifecycle_status' => 'cancelled',
            'modification_reason' => $reason,
            'modified_by' => Auth::id() ?? 1,
            'modified_at' => now(),
        ]);
        $original->delete();
        
        $reversal->update([
            'lifecycle_status' => 'cancelled',
            'modification_reason' => $reason,
            'modified_by' => Auth::id() ?? 1,
            'modified_at' => now(),
        ]);
        $reversal->delete();
    }
    
    /**
     * Phục hồi giao dịch gốc (hủy reversal)
     * Dùng khi giao dịch gốc là ĐÚNG, không nên đã reverse
     */
    public function undoReversal(Transaction $transaction, string $reason): Transaction
    {
        $original = null;
        $reversal = null;
        
        // Tìm cặp giao dịch
        if ($transaction->lifecycle_status === 'reversed') {
            $original = $transaction;
            $reversal = Transaction::find($original->reversed_by_transaction_id);
        } elseif ($transaction->reverses_transaction_id) {
            $reversal = $transaction;
            $original = Transaction::find($reversal->reverses_transaction_id);
        } else {
            throw new \Exception("Giao dịch này không phải là một cặp reversal!");
        }
        
        if (!$original || !$reversal) {
            throw new \Exception("Không tìm thấy cặp giao dịch reversal!");
        }
        
        if ($original->is_locked || $reversal->is_locked) {
            throw new \Exception("Một trong hai giao dịch đã bị khóa, không thể undo!");
        }
        
        // Xóa reversal
        $reversal->update([
            'lifecycle_status' => 'cancelled',
            'modification_reason' => "Undo reversal: {$reason}",
            'modified_by' => Auth::id() ?? 1,
            'modified_at' => now(),
        ]);
        $reversal->delete();
        
        // Phục hồi giao dịch gốc
        $original->update([
            'lifecycle_status' => 'active',
            'reversed_by_transaction_id' => null,
            'modification_reason' => "Restored: {$reason}",
            'modified_by' => Auth::id() ?? 1,
            'modified_at' => now(),
        ]);
        
        return $original;
    }
