<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║  KIỂM TRA & ĐIỀU CHỈNH GIAO DỊCH CÔNG TY                            ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

echo "📊 HỆ THỐNG 4 TÀI KHOẢN:\n";
echo "  1. TỔNG THU: Thu + Nộp quỹ vào công ty\n";
echo "  2. TỔNG CHI: Chi từ lợi nhuận hoặc dự kiến chi\n";
echo "  3. LỢI NHUẬN: Thu - Chi - Dự kiến chi (nguồn có thể chi)\n";
echo "  4. DỰ KIẾN CHI: Trích từ lợi nhuận (nguồn có thể chi)\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// 1. Tổng thu (revenue)
echo "📈 1. TỔNG THU (Revenue):\n";
$revenueTransactions = Transaction::whereIn('type', ['thu', 'nop_quy'])
    ->where(function($q) {
        $q->where('category', '!=', 'vay_từ_công_ty')
          ->orWhereNull('category');
    })
    ->orderBy('date', 'desc')
    ->limit(10)
    ->get();

$totalRevenue = Transaction::whereIn('type', ['thu', 'nop_quy'])
    ->where(function($q) {
        $q->where('category', '!=', 'vay_từ_công_ty')
          ->orWhereNull('category');
    })
    ->sum('amount');

echo "  Tổng: " . number_format($totalRevenue, 0, ',', '.') . "đ\n";
echo "  Số giao dịch: " . Transaction::whereIn('type', ['thu', 'nop_quy'])->count() . "\n\n";

echo "  10 giao dịch thu gần nhất:\n";
foreach ($revenueTransactions as $tx) {
    echo "  • {$tx->code} | {$tx->type} | " . number_format($tx->amount, 0, ',', '.') . "đ | {$tx->date->format('d/m/Y')}\n";
    echo "    From: {$tx->from_account} → To: {$tx->to_account}\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// 2. Tổng chi (expense)
echo "📉 2. TỔNG CHI (Expense):\n";
$expenseTransactions = Transaction::where('type', 'chi')
    ->orderBy('date', 'desc')
    ->limit(10)
    ->get();

$totalExpense = Transaction::where('type', 'chi')->sum('amount');

echo "  Tổng: " . number_format($totalExpense, 0, ',', '.') . "đ\n";
echo "  Số giao dịch: " . Transaction::where('type', 'chi')->count() . "\n\n";

echo "  10 giao dịch chi gần nhất:\n";
foreach ($expenseTransactions as $tx) {
    $category = $tx->category ?? 'N/A';
    echo "  • {$tx->code} | {$category} | " . number_format($tx->amount, 0, ',', '.') . "đ | {$tx->date->format('d/m/Y')}\n";
    echo "    From: {$tx->from_account} → To: {$tx->to_account}\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// 3. Dự kiến chi (planned expense)
echo "📊 3. DỰ KIẾN CHI (Planned Expense):\n";
$plannedTransactions = Transaction::where('type', 'du_kien_chi')
    ->orderBy('date', 'desc')
    ->get();

$totalPlanned = Transaction::where('type', 'du_kien_chi')->sum('amount');

echo "  Tổng: " . number_format($totalPlanned, 0, ',', '.') . "đ\n";
echo "  Số giao dịch: " . $plannedTransactions->count() . "\n\n";

if ($plannedTransactions->count() > 0) {
    echo "  Tất cả giao dịch dự kiến chi:\n";
    foreach ($plannedTransactions as $tx) {
        echo "  • {$tx->code} | " . number_format($tx->amount, 0, ',', '.') . "đ | {$tx->date->format('d/m/Y')}\n";
        echo "    From: {$tx->from_account} → To: {$tx->to_account}\n";
        echo "    Note: " . ($tx->note ?? 'N/A') . "\n";
        
        // Kiểm tra account mapping
        if ($tx->from_account !== 'company_fund' || $tx->to_account !== 'company_reserved') {
            echo "    ⚠️  WARNING: Sai account mapping!\n";
            echo "        Expected: company_fund → company_reserved\n";
            echo "        Actual: {$tx->from_account} → {$tx->to_account}\n";
        }
    }
} else {
    echo "  ⚠️  Không có giao dịch dự kiến chi nào!\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// 4. Tính lợi nhuận
echo "💰 4. LỢI NHUẬN:\n";
$profit = $totalRevenue - $totalExpense - $totalPlanned;
echo "  Tổng thu: " . number_format($totalRevenue, 0, ',', '.') . "đ\n";
echo "  Tổng chi: " . number_format($totalExpense, 0, ',', '.') . "đ\n";
echo "  Dự kiến chi: " . number_format($totalPlanned, 0, ',', '.') . "đ\n";
echo "  ────────────────────────────────\n";
echo "  Lợi nhuận: " . number_format($profit, 0, ',', '.') . "đ\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// 5. Kiểm tra giao dịch cụ thể
echo "🔍 5. KIỂM TRA GIAO DỊCH GD20251122-0036:\n";
$specificTx = Transaction::where('code', 'GD20251122-0036')->first();

if ($specificTx) {
    echo "  Code: {$specificTx->code}\n";
    echo "  Type: {$specificTx->type}\n";
    echo "  Amount: " . number_format($specificTx->amount, 0, ',', '.') . "đ\n";
    echo "  Date: {$specificTx->date->format('d/m/Y')}\n";
    echo "  From: {$specificTx->from_account}\n";
    echo "  To: {$specificTx->to_account}\n";
    echo "  Category: " . ($specificTx->category ?? 'NULL') . "\n";
    echo "  Note: " . ($specificTx->note ?? 'NULL') . "\n\n";
    
    if ($specificTx->type === 'du_kien_chi') {
        echo "  ✅ Đúng loại: du_kien_chi\n";
    } else {
        echo "  ⚠️  SAI LOẠI! Nên là 'du_kien_chi'\n";
    }
    
    if ($specificTx->from_account === 'company_fund' && $specificTx->to_account === 'company_reserved') {
        echo "  ✅ Đúng account mapping: company_fund → company_reserved\n";
    } else {
        echo "  ⚠️  SAI ACCOUNT MAPPING!\n";
        echo "     Nên là: company_fund → company_reserved\n";
        echo "     Hiện tại: {$specificTx->from_account} → {$specificTx->to_account}\n";
    }
} else {
    echo "  ⚠️  Không tìm thấy giao dịch GD20251122-0036\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// 6. Kiểm tra các giao dịch có vấn đề
echo "⚠️  6. KIỂM TRA GIAO DỊCH CÓ VẤN ĐỀ:\n\n";

// Chi từ dự kiến nhưng không đúng category
$wrongCategoryExpense = Transaction::where('type', 'chi')
    ->where('category', 'chi_từ_dự_kiến')
    ->where('from_account', '!=', 'company_reserved')
    ->get();

if ($wrongCategoryExpense->count() > 0) {
    echo "  • Chi từ dự kiến nhưng from_account không phải company_reserved:\n";
    foreach ($wrongCategoryExpense as $tx) {
        echo "    - {$tx->code}: {$tx->from_account} → {$tx->to_account}\n";
    }
    echo "\n";
} else {
    echo "  ✅ Tất cả giao dịch 'chi từ dự kiến' đều đúng from_account\n";
}

// Dự kiến chi nhưng không đúng mapping
$wrongPlanned = Transaction::where('type', 'du_kien_chi')
    ->where(function($q) {
        $q->where('from_account', '!=', 'company_fund')
          ->orWhere('to_account', '!=', 'company_reserved');
    })
    ->get();

if ($wrongPlanned->count() > 0) {
    echo "  • Giao dịch dự kiến chi sai account mapping:\n";
    foreach ($wrongPlanned as $tx) {
        echo "    - {$tx->code}: {$tx->from_account} → {$tx->to_account}\n";
        echo "      (Nên là: company_fund → company_reserved)\n";
    }
    echo "\n";
} else {
    echo "  ✅ Tất cả giao dịch dự kiến chi đều đúng account mapping\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "✅ KIỂM TRA HOÀN TẤT!\n\n";

echo "💡 TÓM TẮT:\n";
echo "  • Tổng thu: " . number_format($totalRevenue, 0, ',', '.') . "đ\n";
echo "  • Tổng chi: " . number_format($totalExpense, 0, ',', '.') . "đ\n";
echo "  • Dự kiến chi: " . number_format($totalPlanned, 0, ',', '.') . "đ\n";
echo "  • Lợi nhuận: " . number_format($profit, 0, ',', '.') . "đ\n";
