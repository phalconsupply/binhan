<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Services\TransactionLifecycleService;

echo "=== DEMO XỬ LÝ AN TOÀN KHI XÓA GIAO DỊCH REVERSAL ===\n\n";

$service = new TransactionLifecycleService();

// Lấy cặp giao dịch
$reversal = Transaction::where('code', 'REV20260101174800')->first();
$original = Transaction::where('code', 'GD20251218-0694')->first();

if (!$reversal || !$original) {
    echo "❌ Không tìm thấy cặp giao dịch\n";
    exit;
}

echo "📊 CẶP GIAO DỊCH HIỆN TẠI:\n";
echo "   Original: {$original->code} (status: {$original->lifecycle_status})\n";
echo "   Reversal: {$reversal->code} (status: {$reversal->lifecycle_status})\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "🎯 OPTION 1: XÓA CẢ 2 GIAO DỊCH\n";
echo "Khi nào dùng: Cả 2 giao dịch đều sai/không cần thiết\n";
echo "Kết quả: Cả 2 biến mất, hệ thống như chưa có giao dịch này\n\n";

echo "Code mẫu:\n";
echo "```php\n";
echo "\$service->deleteReversalPair(\$original, 'Cả 2 giao dịch đều không cần thiết');\n";
echo "// hoặc\n";
echo "\$service->deleteReversalPair(\$reversal, 'Cả 2 giao dịch đều không cần thiết');\n";
echo "```\n\n";

echo "Thực hiện? [y/N]: ";
$choice1 = trim(fgets(STDIN));

if (strtolower($choice1) === 'y') {
    try {
        $service->deleteReversalPair($original, 'Demo: Xóa cả cặp reversal');
        echo "✅ Đã xóa cả 2 giao dịch thành công!\n";
        
        $checkOriginal = Transaction::withTrashed()->find($original->id);
        $checkReversal = Transaction::withTrashed()->find($reversal->id);
        
        echo "\n📊 Trạng thái sau khi xóa:\n";
        echo "   Original {$checkOriginal->code}:\n";
        echo "   - Lifecycle: {$checkOriginal->lifecycle_status}\n";
        echo "   - Deleted at: " . ($checkOriginal->deleted_at ? $checkOriginal->deleted_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
        echo "   - Is trashed: " . ($checkOriginal->trashed() ? 'YES' : 'NO') . "\n\n";
        
        echo "   Reversal {$checkReversal->code}:\n";
        echo "   - Lifecycle: {$checkReversal->lifecycle_status}\n";
        echo "   - Deleted at: " . ($checkReversal->deleted_at ? $checkReversal->deleted_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
        echo "   - Is trashed: " . ($checkReversal->trashed() ? 'YES' : 'NO') . "\n\n";
        
        echo "✅ Cả 2 giao dịch đều đã bị soft delete và có thể restore nếu cần.\n";
        exit;
        
    } catch (\Exception $e) {
        echo "❌ Error: {$e->getMessage()}\n\n";
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "🎯 OPTION 2: PHỤC HỒI GIAO DỊCH GỐC (Undo Reversal)\n";
echo "Khi nào dùng: Giao dịch gốc là ĐÚNG, không nên đã reverse\n";
echo "Kết quả: Xóa reversal, giao dịch gốc quay về 'active'\n\n";

echo "Code mẫu:\n";
echo "```php\n";
echo "\$service->undoReversal(\$original, 'Giao dịch gốc là đúng');\n";
echo "// hoặc\n";
echo "\$service->undoReversal(\$reversal, 'Giao dịch gốc là đúng');\n";
echo "```\n\n";

echo "Thực hiện? [y/N]: ";
$choice2 = trim(fgets(STDIN));

if (strtolower($choice2) === 'y') {
    try {
        $restored = $service->undoReversal($original, 'Demo: Phục hồi giao dịch gốc');
        echo "✅ Đã phục hồi giao dịch gốc thành công!\n";
        
        $checkOriginal = Transaction::find($restored->id);
        $checkReversal = Transaction::withTrashed()->find($reversal->id);
        
        echo "\n📊 Trạng thái sau khi undo:\n";
        echo "   Original {$checkOriginal->code}:\n";
        echo "   - Lifecycle: {$checkOriginal->lifecycle_status}\n";
        echo "   - Reversed by: " . ($checkOriginal->reversed_by_transaction_id ?: 'NULL') . "\n";
        echo "   - Is active: YES\n\n";
        
        echo "   Reversal {$checkReversal->code}:\n";
        echo "   - Lifecycle: {$checkReversal->lifecycle_status}\n";
        echo "   - Deleted at: " . ($checkReversal->deleted_at ? $checkReversal->deleted_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
        echo "   - Is trashed: " . ($checkReversal->trashed() ? 'YES' : 'NO') . "\n\n";
        
        echo "✅ Giao dịch gốc đã được phục hồi, reversal đã bị xóa.\n";
        exit;
        
    } catch (\Exception $e) {
        echo "❌ Error: {$e->getMessage()}\n\n";
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "🎯 OPTION 3: REVERSE THE REVERSAL\n";
echo "Khi nào dùng: Muốn phục hồi giao dịch gốc với đầy đủ audit trail\n";
echo "Kết quả: Tạo reversal của reversal (giống giao dịch gốc)\n\n";

echo "Code mẫu:\n";
echo "```php\n";
echo "// Đầu tiên, undo reversal để giao dịch gốc về 'active'\n";
echo "\$service->undoReversal(\$original, 'Prepare for re-reversal');\n";
echo "// Sau đó nếu cần, có thể tạo reversal mới\n";
echo "```\n\n";

echo "⚠️  CẢNH BÁO: ĐỪNG XÓA RIÊNG LẺ!\n";
echo "   Nếu chỉ xóa reversal mà không xử lý giao dịch gốc:\n";
echo "   - Giao dịch gốc vẫn ở trạng thái 'reversed'\n";
echo "   - Nhưng không có reversal để cân bằng\n";
echo "   - Số dư tài khoản SAI\n";
echo "   - Broken relationship (reversed_by_transaction_id trỏ vào ID không tồn tại)\n\n";

echo "✅ KẾT LUẬN:\n";
echo "   Luôn dùng một trong các methods:\n";
echo "   1. deleteReversalPair() - Xóa cả 2\n";
echo "   2. undoReversal() - Phục hồi giao dịch gốc\n";
echo "   3. Không xóa gì cả - Giữ nguyên cặp reversal\n\n";

echo "   ❌ KHÔNG BAO GIỜ: Xóa riêng một trong hai giao dịch!\n";
