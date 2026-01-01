<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class DoubleEntryService
{
    /**
     * Convert existing transaction to double-entry format
     * 
     * Rules:
     * - Debit (Nợ): Increase in Assets/Expenses, Decrease in Liabilities/Revenue
     * - Credit (Có): Decrease in Assets/Expenses, Increase in Liabilities/Revenue
     */
    public static function createJournalEntries(Transaction $transaction): void
    {
        // Get accounts
        $fromAccount = $transaction->from_account_id 
            ? Account::find($transaction->from_account_id)
            : null;
        
        $toAccount = $transaction->to_account_id
            ? Account::find($transaction->to_account_id)
            : null;

        if (!$fromAccount || !$toAccount) {
            throw new \Exception("Both from_account_id and to_account_id must be set");
        }

        $amount = $transaction->amount;

        DB::transaction(function () use ($transaction, $fromAccount, $toAccount, $amount) {
            // Delete existing lines if any
            TransactionLine::where('transaction_id', $transaction->id)->delete();

            // Create journal entries based on account types
            $entries = self::determineJournalEntries($fromAccount, $toAccount, $amount, $transaction);

            foreach ($entries as $index => $entry) {
                TransactionLine::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'],
                    'credit' => $entry['credit'],
                    'description' => $entry['description'],
                    'line_number' => $index + 1,
                ]);

                // Update account balance
                self::updateAccountBalance($entry['account_id'], $entry['debit'], $entry['credit']);
            }
        });
    }

    /**
     * Determine journal entries based on account types and transaction type
     */
    protected static function determineJournalEntries(
        Account $fromAccount,
        Account $toAccount,
        float $amount,
        Transaction $transaction
    ): array {
        $entries = [];

        // Logic: Money flows from "fromAccount" to "toAccount"
        // This means:
        // - fromAccount loses money (credit if asset, debit if liability/revenue)
        // - toAccount gains money (debit if asset, credit if liability/revenue)

        switch ($transaction->type) {
            case 'thu': // Revenue
                // Debit: Asset (Vehicle/Company account receives cash)
                // Credit: Revenue (Customer pays)
                $entries[] = [
                    'account_id' => $toAccount->id,
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => "Thu tiền vào {$toAccount->name}",
                ];
                $entries[] = [
                    'account_id' => $fromAccount->id,
                    'debit' => 0,
                    'credit' => $amount,
                    'description' => "Thu từ {$fromAccount->name}",
                ];
                break;

            case 'chi': // Expense
                // Debit: Expense account
                // Credit: Asset (Vehicle/Company account pays cash)
                $entries[] = [
                    'account_id' => $toAccount->id,
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => "Chi tiền cho {$toAccount->name}",
                ];
                $entries[] = [
                    'account_id' => $fromAccount->id,
                    'debit' => 0,
                    'credit' => $amount,
                    'description' => "Chi từ {$fromAccount->name}",
                ];
                break;

            case 'du_kien_chi': // Planned expense (internal transfer)
                // Debit: Reserved fund (asset sub-account)
                // Credit: Company fund (main asset account)
                $entries[] = [
                    'account_id' => $toAccount->id,
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => "Dự kiến chi - chuyển vào quỹ dự kiến",
                ];
                $entries[] = [
                    'account_id' => $fromAccount->id,
                    'debit' => 0,
                    'credit' => $amount,
                    'description' => "Dự kiến chi - trừ quỹ công ty",
                ];
                break;

            case 'nop_quy': // Fund deposit
                // Debit: Company fund or vehicle account
                // Credit: Income/Revenue
                $entries[] = [
                    'account_id' => $toAccount->id,
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => "Nộp quỹ vào {$toAccount->name}",
                ];
                $entries[] = [
                    'account_id' => $fromAccount->id,
                    'debit' => 0,
                    'credit' => $amount,
                    'description' => "Nộp quỹ từ lợi nhuận",
                ];
                break;

            case 'vay_cong_ty': // Borrow from company
                // Debit: Vehicle asset account (receives cash)
                // Credit: Company fund (pays cash)
                $entries[] = [
                    'account_id' => $toAccount->id,
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => "Vay tiền từ công ty",
                ];
                $entries[] = [
                    'account_id' => $fromAccount->id,
                    'debit' => 0,
                    'credit' => $amount,
                    'description' => "Cho vay xe",
                ];
                break;

            case 'tra_cong_ty': // Repay to company
                // Debit: Company fund (receives repayment)
                // Credit: Vehicle account (pays back)
                $entries[] = [
                    'account_id' => $toAccount->id,
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => "Nhận trả nợ từ xe",
                ];
                $entries[] = [
                    'account_id' => $fromAccount->id,
                    'debit' => 0,
                    'credit' => $amount,
                    'description' => "Trả nợ công ty",
                ];
                break;

            default:
                throw new \Exception("Unknown transaction type: {$transaction->type}");
        }

        return $entries;
    }

    /**
     * Update account balance (denormalized for performance)
     */
    protected static function updateAccountBalance(int $accountId, float $debit, float $credit): void
    {
        $account = Account::findOrFail($accountId);

        // For Asset and Expense accounts: Debit increases, Credit decreases
        // For Liability, Equity, and Revenue accounts: Debit decreases, Credit increases
        if (in_array($account->type, ['asset', 'expense'])) {
            $account->balance += ($debit - $credit);
        } else {
            $account->balance += ($credit - $debit);
        }

        $account->save();
    }

    /**
     * Validate that debits equal credits
     */
    public static function validateBalance(Transaction $transaction): bool
    {
        $totalDebits = TransactionLine::where('transaction_id', $transaction->id)
            ->sum('debit');
        
        $totalCredits = TransactionLine::where('transaction_id', $transaction->id)
            ->sum('credit');

        return abs($totalDebits - $totalCredits) < 0.01; // Allow 1 cent tolerance
    }

    /**
     * Get trial balance (for all accounts)
     */
    public static function getTrialBalance(): array
    {
        $accounts = Account::with(['transactionLines'])->get();

        $trialBalance = [];

        foreach ($accounts as $account) {
            $totalDebits = TransactionLine::forAccount($account->id)->sum('debit');
            $totalCredits = TransactionLine::forAccount($account->id)->sum('credit');

            $trialBalance[] = [
                'account' => $account,
                'debits' => $totalDebits,
                'credits' => $totalCredits,
                'balance' => $totalDebits - $totalCredits,
            ];
        }

        return $trialBalance;
    }
}
