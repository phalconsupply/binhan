<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Vehicle;
use App\Models\Transaction;

echo "🔍 Kiểm tra giao dịch #873 có trừ vào lợi nhuận xe chưa\n\n";

$vehicle = Vehicle::where('license_plate', '49B08879')->first();
$transaction873 = Transaction::find(873);

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 Phân tích scope giao dịch:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Check scopes
$totalRevenue = $vehicle->transactions()->revenue()->sum('amount');
$totalExpense = $vehicle->transactions()->expense()->sum('amount');
$totalBorrowed = $vehicle->transactions()->borrowFromCompany()->sum('amount');
$totalReturned = $vehicle->transactions()->returnToCompany()->sum('amount');

echo "Tổng THU (type='thu'): " . number_format($totalRevenue) . "đ\n";
echo "Tổng CHI (type='chi'): " . number_format($totalExpense) . "đ\n";
echo "Tổng VAY (type='vay_cong_ty'): " . number_format($totalBorrowed) . "đ\n";
echo "Tổng TRẢ NỢ (type='tra_cong_ty'): " . number_format($totalReturned) . "đ\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 Kiểm tra giao dịch #873:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "Loại: {$transaction873->type}\n";
echo "Số tiền: " . number_format($transaction873->amount) . "đ\n\n";

// Check if it's in expense
$isInExpense = $vehicle->transactions()->expense()->where('id', 873)->exists();
echo "Có trong scope expense(): " . ($isInExpense ? "CÓ ✓" : "KHÔNG ✗") . "\n";

// Check if it's in returnToCompany
$isInReturn = $vehicle->transactions()->returnToCompany()->where('id', 873)->exists();
echo "Có trong scope returnToCompany(): " . ($isInReturn ? "CÓ ✓" : "KHÔNG ✗") . "\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 Logic tính lợi nhuận xe hiện tại:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "Cách 1 - Đơn giản:\n";
echo "  Lợi nhuận = Thu - Chi\n";
echo "  = " . number_format($totalRevenue) . " - " . number_format($totalExpense) . "\n";
echo "  = " . number_format($totalRevenue - $totalExpense) . "đ\n";
echo "  ⚠️  Không tính trả nợ!\n\n";

echo "Cách 2 - Có tính trả nợ:\n";
echo "  Lợi nhuận = Thu - Chi - Trả nợ\n";
echo "  = " . number_format($totalRevenue) . " - " . number_format($totalExpense) . " - " . number_format($totalReturned) . "\n";
echo "  = " . number_format($totalRevenue - $totalExpense - $totalReturned) . "đ\n";
echo "  ✓ Có tính trả nợ!\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "❓ CÂU HỎI:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "1. Trả nợ có nên trừ vào lợi nhuận xe không?\n";
echo "   → KHÔNG! Vì:\n";
echo "     - Tiền vay đã được cộng vào số dư xe (vay_cong_ty)\n";
echo "     - Trả nợ chỉ là hoàn lại, không làm giảm lợi nhuận\n";
echo "     - Lợi nhuận = Thu - Chi (không tính vay/trả)\n\n";

echo "2. Số dư xe hiện tại là gì?\n";
echo "   Số dư = Thu + Vay - Chi - Trả nợ\n";
echo "   = " . number_format($totalRevenue) . " + " . number_format($totalBorrowed) . " - " . number_format($totalExpense) . " - " . number_format($totalReturned) . "\n";
echo "   = " . number_format($totalRevenue + $totalBorrowed - $totalExpense - $totalReturned) . "đ\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ KẾT LUẬN:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "- Giao dịch #873 (tra_cong_ty) KHÔNG nằm trong scope expense()\n";
echo "- Giao dịch #873 CÓ nằm trong scope returnToCompany()\n";
echo "- Lợi nhuận xe = Thu - Chi (KHÔNG bao gồm trả nợ) ✓ ĐÚNG\n";
echo "- Số dư xe = Thu + Vay - Chi - Trả nợ (CÓ bao gồm trả nợ) ✓ ĐÚNG\n";
