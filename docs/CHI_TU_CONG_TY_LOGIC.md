# LOGIC "CHI TỪ CÔNG TY" - GIẢI THÍCH

## Tình huống hiện tại

**Xe 49B08879 (ID: 4)**
- Thu: 0đ
- Chi: 31.911.425đ (bảo trì)
- Số dư: -31.911.425đ
- Giao dịch vay công ty: 0đ

## Vấn đề

Khi xe có chủ chi tiền mà không có thu, ai trả tiền?
→ **Công ty phải trả trước** cho chủ xe

## Hai cách ghi nhận

### Cách 1: KHÔNG tạo giao dịch vay (Legacy - Hiện tại)
```
Chi từ công ty = max(0, Chi - Thu)
                = max(0, 31.911.425 - 0)
                = 31.911.425đ
```

**Đặc điểm:**
- ✅ Đơn giản, không cần tạo giao dịch phức tạp
- ✅ Hiển thị rõ số tiền công ty phải chi ra
- ❌ Không có giao dịch vay chính thức
- ❌ Khó theo dõi lịch sử vay-trả

### Cách 2: CÓ tạo giao dịch vay (Mới)
```
Bước 1: Tự động tạo giao dịch vay khi chi
  - Type: vay_cong_ty
  - Amount: 31.911.425đ
  
Bước 2: Tạo giao dịch chi
  - Type: chi
  - Amount: 31.911.425đ
  
Chi từ công ty = Vay - Trả
                = 31.911.425 - 0
                = 31.911.425đ
```

**Đặc điểm:**
- ✅ Ghi nhận chính thức khoản vay
- ✅ Dễ theo dõi lịch sử vay-trả
- ✅ Có thể tính lãi suất nếu cần
- ❌ Phức tạp hơn
- ❌ Cần logic tự động tạo giao dịch vay

## Logic đã cập nhật trong TransactionController

```php
if ($isVehicleOwner) {
    $totalBorrowed = $statsQuery->borrowFromCompany()->sum('amount');
    $totalReturned = $statsQuery->returnToCompany()->sum('amount');
    $currentDebt = $totalBorrowed - $totalReturned;
    
    // Nếu có giao dịch vay, dùng số liệu thực
    if ($totalBorrowed > 0) {
        $companyExpense = $currentDebt;
    } else {
        // Nếu không có, tính deficit (legacy)
        $companyExpense = max(0, $totalExpense - $totalRevenue);
    }
}
```

**Ưu điểm:**
- ✅ Tương thích ngược: Xe không có giao dịch vay → dùng logic cũ
- ✅ Xe có giao dịch vay → dùng logic mới (chính xác hơn)
- ✅ Không cần migration hay sửa dữ liệu cũ

## Khuyến nghị

### Nếu muốn đơn giản (Cách 1):
- Giữ nguyên logic hiện tại
- Hiển thị "Chi từ công ty" = Chi - Thu (nếu âm)
- **Phù hợp với tình huống hiện tại của xe 49B08879**

### Nếu muốn chính xác (Cách 2):
- Triển khai logic tự động tạo giao dịch vay
- Khi tạo giao dịch chi:
  1. Kiểm tra số dư
  2. Nếu thiếu → tự động tạo giao dịch vay
  3. Sau đó mới tạo giao dịch chi
- **Cần code thêm logic vào TransactionController@store**

## Kết luận cho trường hợp xe 49B08879

**Tình trạng:**
- Xe chỉ có chi (bảo trì) = 31.911.425đ
- Không có thu = 0đ
- Không có giao dịch vay

**Hiển thị:**
- ✅ "Chi từ công ty" = 31.911.425đ (đúng)
- Đây là số tiền công ty đã chi ra cho chủ xe
- Chủ xe đang "nợ" công ty số tiền này

**Cách xử lý:**
1. **Tạo giao dịch vay thủ công** (nếu muốn ghi nhận chính thức):
   ```php
   Transaction::create([
       'vehicle_id' => 4,
       'type' => 'vay_cong_ty',
       'amount' => 31911425,
       'note' => 'Vay công ty để chi bảo trì',
       'date' => now(),
   ]);
   ```

2. **Hoặc giữ nguyên** và hiểu rằng:
   - "Chi từ công ty" = số tiền công ty đã ứng trước
   - Khi có thu sau → chủ xe trả nợ

## File đã cập nhật

- [TransactionController.php](app/Http/Controllers/TransactionController.php) - Logic tính "Chi từ công ty"
- [VehicleController.php](app/Http/Controllers/VehicleController.php) - Logic trả nợ
- [Transaction.php](app/Models/Transaction.php) - Scope borrowFromCompany, returnToCompany
