# LOGIC VAY TIỀN TỪ CÔNG TY CHO XE CÓ CHỦ

## Tổng quan

Khi xe có chủ cần chi tiền mà số dư lợi nhuận không đủ, hệ thống sẽ tự động tạo giao dịch "vay từ công ty" để chủ xe có thể thực hiện giao dịch chi. Khoản vay này cần được hoàn trả lại sau.

## Các loại giao dịch mới

### 1. VAY TỪ CÔNG TY (`vay_cong_ty`)
- **Mục đích**: Ghi nhận khoản tiền công ty cho chủ xe vay tạm
- **Tác động**: 
  - ➕ Cộng vào tài khoản lợi nhuận chủ xe
  - ➖ Trừ khỏi tài khoản công ty
- **Khi nào dùng**: Khi số dư lợi nhuận < số tiền cần chi

### 2. TRẢ CHO CÔNG TY (`tra_cong_ty`)
- **Mục đích**: Ghi nhận việc hoàn trả nợ cho công ty
- **Tác động**:
  - ➖ Trừ khỏi tài khoản lợi nhuận chủ xe
  - ➕ Cộng vào tài khoản công ty
- **Khi nào dùng**: Khi có lợi nhuận dương và muốn trả nợ

## Cấu trúc dữ liệu

### Giao dịch vay
```php
Transaction::create([
    'vehicle_id' => $vehicle->id,
    'type' => 'vay_cong_ty',
    'amount' => 5000000, // Số tiền vay
    'category' => 'vay_tạm_ứng',
    'note' => 'Vay từ công ty để chi trả',
    'date' => now(),
    'recorded_by' => auth()->id(),
]);
```

### Giao dịch trả
```php
Transaction::create([
    'vehicle_id' => $vehicle->id,
    'type' => 'tra_cong_ty',
    'amount' => 3000000, // Số tiền trả
    'category' => 'hoàn_trả',
    'note' => 'Trả nợ công ty',
    'date' => now(),
    'recorded_by' => auth()->id(),
]);
```

## Công thức tính toán

### Số dư lợi nhuận
```
Số dư = (Tổng thu + Nộp quỹ + Vay công ty) - (Tổng chi + Trả công ty)
```

### Số tiền đang vay
```
Đang vay = Tổng vay - Tổng trả
```

## Hiển thị trong View

### Cảnh báo nợ công ty
Khi `total_borrowed > 0`, hiển thị cảnh báo màu cam:

```blade
@if($stats['has_owner'] && $stats['total_borrowed'] > 0)
<div class="bg-orange-50 border-l-4 border-orange-500 p-4">
    ⚠️ Đang vay từ công ty: {{ number_format($stats['total_borrowed']) }}đ
    Chủ xe đang mượn tiền từ công ty để chi trả.
</div>
@endif
```

### Thống kê trong Controller
```php
$totalBorrowed = $vehicle->transactions()->borrowFromCompany()->sum('amount');
$totalReturned = $vehicle->transactions()->returnToCompany()->sum('amount');
$stats['total_borrowed'] = $totalBorrowed - $totalReturned;
```

## Quy trình xử lý

### Khi tạo giao dịch chi:

1. **Kiểm tra số dư**
   ```php
   $currentBalance = $vehicle->getCurrentBalance();
   $amountNeeded = $expenseAmount;
   ```

2. **Nếu thiếu tiền, tạo giao dịch vay**
   ```php
   if ($currentBalance < $amountNeeded) {
       $borrowAmount = $amountNeeded - $currentBalance;
       
       Transaction::create([
           'vehicle_id' => $vehicle->id,
           'type' => 'vay_cong_ty',
           'amount' => $borrowAmount,
           'category' => 'vay_tạm_ứng',
           'note' => 'Vay từ công ty để chi: ' . $description,
           'date' => now(),
           'recorded_by' => auth()->id(),
       ]);
   }
   ```

3. **Sau đó mới tạo giao dịch chi**
   ```php
   Transaction::create([
       'vehicle_id' => $vehicle->id,
       'type' => 'chi',
       'amount' => $amountNeeded,
       // ... các field khác
   ]);
   ```

## Báo cáo và Thống kê

### Danh sách xe đang vay tiền
```php
$vehiclesWithDebt = Vehicle::whereHas('owner')
    ->get()
    ->filter(function($vehicle) {
        $borrowed = $vehicle->transactions()->borrowFromCompany()->sum('amount');
        $returned = $vehicle->transactions()->returnToCompany()->sum('amount');
        return ($borrowed - $returned) > 0;
    })
    ->map(function($vehicle) {
        $borrowed = $vehicle->transactions()->borrowFromCompany()->sum('amount');
        $returned = $vehicle->transactions()->returnToCompany()->sum('amount');
        return [
            'vehicle' => $vehicle,
            'debt' => $borrowed - $returned,
        ];
    });
```

### Tổng nợ công ty phải thu về
```php
$totalCompanyDebt = DB::table('transactions')
    ->where('type', 'vay_cong_ty')
    ->sum('amount')
    - DB::table('transactions')
    ->where('type', 'tra_cong_ty')
    ->sum('amount');
```

## Lưu ý quan trọng

1. ✅ **Tự động**: Logic vay nên tự động khi tạo giao dịch chi
2. ✅ **Cảnh báo**: Luôn hiển thị rõ số tiền đang vay
3. ✅ **Lịch sử**: Lưu đầy đủ lịch sử vay-trả
4. ✅ **Báo cáo**: Tạo báo cáo riêng cho các khoản vay
5. ⚠️ **Giới hạn**: Cân nhắc đặt hạn mức vay tối đa
6. ⚠️ **Lãi suất**: Có thể tính lãi suất cho khoản vay nếu cần

## Ví dụ thực tế

### Tình huống 1: Vay để chi bảo trì
```
Lợi nhuận hiện tại: 5.000.000đ
Cần chi bảo trì:    8.000.000đ
=> Vay:             3.000.000đ
```

### Tình huống 2: Có lợi nhuận, trả nợ
```
Lợi nhuận mới:     10.000.000đ
Đang nợ:            3.000.000đ
=> Trả:             3.000.000đ
Còn lại:            7.000.000đ
```

## Migration đã tạo

File: `2025_12_24_100000_add_vay_cong_ty_type_to_transactions_table.php`

Đã thêm 2 giá trị mới vào ENUM `type`:
- `vay_cong_ty`: Vay từ công ty
- `tra_cong_ty`: Trả cho công ty

## Model Updates

File: `app/Models/Transaction.php`

Đã thêm:
- Scope: `borrowFromCompany()`, `returnToCompany()`
- Label: 'Vay công ty', 'Trả công ty'

## Controller Updates

File: `app/Http/Controllers/VehicleController.php`

Đã thêm tính toán `total_borrowed` và `month_borrowed`

## View Updates

File: `resources/views/vehicles/show.blade.php`

Đã thêm cảnh báo hiển thị số tiền đang vay từ công ty
