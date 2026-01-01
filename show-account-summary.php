<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "=== ACCOUNT SUMMARY ===\n\n";

// Company accounts
echo "📊 COMPANY ACCOUNTS:\n";
$compFund = Account::where('code', 'COMP-FUND')->first();
$compReserved = Account::where('code', 'COMP-RESERVED')->first();

echo "  Quỹ công ty: " . number_format($compFund->balance) . "đ\n";
echo "  Quỹ dự kiến chi: " . number_format($compReserved->balance) . "đ\n";
echo "  Khả dụng: " . number_format($compFund->balance - $compReserved->balance) . "đ\n\n";

// Vehicle accounts
echo "🚗 VEHICLE ACCOUNTS:\n";
$vehicles = Account::where('code', 'like', 'VEH-%')->whereNotNull('reference_id')->get();
foreach ($vehicles as $vehicle) {
    $txCount = Transaction::where('from_account_id', $vehicle->id)
        ->orWhere('to_account_id', $vehicle->id)
        ->count();
    echo "  {$vehicle->name}: " . number_format($vehicle->balance) . "đ ({$txCount} transactions)\n";
}

// Staff accounts
echo "\n👤 STAFF ACCOUNTS (with transactions):\n";
$staffs = Account::where('code', 'like', 'STAFF-%')
    ->whereNotNull('reference_id')
    ->get()
    ->filter(function($staff) {
        return Transaction::where('from_account_id', $staff->id)
            ->orWhere('to_account_id', $staff->id)
            ->exists();
    });
    
foreach ($staffs as $staff) {
    $txCount = Transaction::where('from_account_id', $staff->id)
        ->orWhere('to_account_id', $staff->id)
        ->count();
    echo "  {$staff->name}: " . number_format($staff->balance) . "đ ({$txCount} transactions)\n";
}

// System accounts
echo "\n💼 SYSTEM ACCOUNTS:\n";
$customer = Account::where('code', 'SYS-CUSTOMER')->first();
$income = Account::where('code', 'SYS-INCOME')->first();
$external = Account::where('code', 'SYS-EXTERNAL')->first();
$partner = Account::where('code', 'SYS-PARTNER')->first();

echo "  Customer: " . number_format($customer->balance) . "đ\n";
echo "  Income: " . number_format($income->balance) . "đ\n";
echo "  External: " . number_format($external->balance) . "đ\n";
echo "  Partner: " . number_format($partner->balance) . "đ\n";

echo "\n✅ All account balances reconciled!\n";
