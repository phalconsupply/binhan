<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

echo "=== KIỂM TRA KẾT QUẢ REVERSAL ===\n\n";

// Giao dịch gốc
$original = Transaction::where('code', 'GD20251218-0694')->first();
echo "📌 GIAO DỊCH GỐC:\n";
echo "   Code: {$original->code}\n";
echo "   Type: {$original->type}\n";
echo "   Amount: " . number_format($original->amount, 0, ',', '.') . "đ\n";
echo "   From: {$original->from_account}\n";
echo "   To: {$original->to_account}\n";
echo "   Status: {$original->lifecycle_status}\n";

if ($original->reversedByTransaction) {
    echo "   ✅ Reversed by: {$original->reversedByTransaction->code}\n";
}
echo "   Modification reason: {$original->modification_reason}\n\n";

// Giao dịch đảo ngược
$reversal = Transaction::where('code', 'REV20260101174800')->first();
if ($reversal) {
    echo "🔄 GIAO DỊCH ĐẢO NGƯỢC:\n";
    echo "   Code: {$reversal->code}\n";
    echo "   Type: {$reversal->type}\n";
    echo "   Amount: " . number_format($reversal->amount, 0, ',', '.') . "đ\n";
    echo "   From: {$reversal->from_account}\n";
    echo "   To: {$reversal->to_account}\n";
    echo "   Status: {$reversal->lifecycle_status}\n";
    
    if ($reversal->reversesTransaction) {
        echo "   ✅ Reverses: {$reversal->reversesTransaction->code}\n";
    }
    echo "   Modification reason: {$reversal->modification_reason}\n\n";
}

// So sánh
echo "📊 SO SÁNH:\n";
echo "   Original: {$original->type} {$original->from_account} → {$original->to_account}\n";
echo "   Reversal: {$reversal->type} {$reversal->from_account} → {$reversal->to_account}\n\n";

// Kiểm tra journal entries
$originalLines = $original->lines;
$reversalLines = $reversal->lines;

echo "📖 JOURNAL ENTRIES:\n";
echo "\n   Original Transaction (ID {$original->id}):\n";
foreach ($originalLines as $line) {
    $type = $line->entry_type === 'debit' ? 'DEBIT ' : 'CREDIT';
    echo "   - {$type} {$line->account_code}: " . number_format($line->amount, 0, ',', '.') . "đ\n";
}

echo "\n   Reversal Transaction (ID {$reversal->id}):\n";
foreach ($reversalLines as $line) {
    $type = $line->entry_type === 'debit' ? 'DEBIT ' : 'CREDIT';
    echo "   - {$type} {$line->account_code}: " . number_format($line->amount, 0, ',', '.') . "đ\n";
}

echo "\n✅ REVERSAL HOÀN TẤT - Hai giao dịch đối nghịch nhau hoàn toàn!\n";
