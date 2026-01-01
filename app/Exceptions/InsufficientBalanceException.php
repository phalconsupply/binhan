<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    protected $accountName;
    protected $currentBalance;
    protected $requiredAmount;

    public function __construct(
        string $accountName,
        float $currentBalance,
        float $requiredAmount,
        string $message = null
    ) {
        $this->accountName = $accountName;
        $this->currentBalance = $currentBalance;
        $this->requiredAmount = $requiredAmount;

        $defaultMessage = sprintf(
            "Số dư không đủ! Tài khoản '%s' có số dư %s đ nhưng cần %s đ",
            $accountName,
            number_format($currentBalance, 0, ',', '.'),
            number_format($requiredAmount, 0, ',', '.')
        );

        parent::__construct($message ?? $defaultMessage);
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }

    public function getCurrentBalance(): float
    {
        return $this->currentBalance;
    }

    public function getRequiredAmount(): float
    {
        return $this->requiredAmount;
    }

    /**
     * Render exception as user-friendly error
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $this->getMessage(),
                'account' => $this->accountName,
                'current_balance' => $this->currentBalance,
                'required_amount' => $this->requiredAmount,
                'deficit' => $this->requiredAmount - $this->currentBalance,
            ], 422);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $this->getMessage());
    }
}
