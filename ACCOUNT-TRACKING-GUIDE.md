# HỆ THỐNG THEO DÕI LUỒNG TIỀN & SỐ DƯ TÀI KHOẢN

## 📋 Tổng quan

Hệ thống tracking luồng tiền giữa các tài khoản đã được triển khai, bao gồm:
- Tracking tài khoản nguồn và đích cho mỗi giao dịch
- Tính toán số dư trước và sau giao dịch
- Hiển thị luồng tiền rõ ràng trên giao diện
- Quản lý quỹ dự kiến chi riêng biệt

## 🏦 CÁC LOẠI TÀI KHOẢN

### 1. **Tài khoản Công ty**
- `company_fund` - Quỹ công ty (tổng)
- `company_reserved` - Quỹ dự kiến chi

### 2. **Tài khoản Xe**
- `vehicle_{id}` - Tài khoản riêng của từng xe
- Ví dụ: `vehicle_4` (Xe 49B-08879)

### 3. **Tài khoản Khác**
- `customer` - Khách hàng (nguồn thu)
- `staff_{id}` - Nhân viên
- `partner` - Đối tác
- `external` - Bên ngoài

## 💰 LOGIC LUỒNG TIỀN

### Thu (type = 'thu')
```
Từ: customer
Đến: vehicle_{id}
Mô tả: Khách trả tiền → Vào tài khoản xe
```

### Chi (type = 'chi')
```
Từ: vehicle_{id} HOẶC company_reserved (nếu chi từ dự kiến)
Đến: staff_{id}, partner, hoặc external
Mô tả: Xe/Dự kiến → Trả cho nhân viên/đối tác
```

### Nộp quỹ (type = 'nop_quy')
```
Từ: vehicle_{id}
Đến: company_fund
Mô tả: Xe → Nộp tiền vào quỹ công ty
```

### Vay công ty (type = 'vay_cong_ty')
```
Từ: company_fund
Đến: vehicle_{id}
Mô tả: Công ty → Cho xe vay
```

### Trả nợ (type = 'tra_cong_ty')
```
Từ: vehicle_{id}
Đến: company_fund
Mô tả: Xe → Trả nợ cho công ty
```

### Dự kiến chi (type = 'du_kien_chi')
```
Tạo:
  Từ: company_fund
  Đến: company_reserved
  Mô tả: Công ty → Giữ tiền để chi

Hủy:
  Từ: company_reserved
  Đến: company_fund
  Mô tả: Hoàn tiền về quỹ

Thực hiện (chi từ dự kiến):
  Từ: company_reserved
  Đến: staff/partner
  Mô tả: Dự kiến → Chi thực tế
```

## 📊 CẤU TRÚC DATABASE

### Bảng `transactions` - Các cột mới

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| `from_account` | VARCHAR(100) | Tài khoản nguồn |
| `to_account` | VARCHAR(100) | Tài khoản đích |
| `from_balance_before` | DECIMAL(15,2) | Số dư TK nguồn trước GD |
| `from_balance_after` | DECIMAL(15,2) | Số dư TK nguồn sau GD |
| `to_balance_before` | DECIMAL(15,2) | Số dư TK đích trước GD |
| `to_balance_after` | DECIMAL(15,2) | Số dư TK đích sau GD |

### Migration đã chạy
```bash
2025_12_25_174419_add_account_tracking_to_transactions_table.php
```

## 🔧 SERVICES & COMMANDS

### AccountBalanceService

Service chính để quản lý tài khoản:

```php
use App\Services\AccountBalanceService;

// Xác định tài khoản cho giao dịch
$accounts = AccountBalanceService::determineAccounts($transaction);

// Tính số dư tài khoản
$balance = AccountBalanceService::getCurrentBalance('company_fund');

// Lấy tổng quan số dư
$summary = AccountBalanceService::getBalancesSummary();

// Lấy tên hiển thị
$displayName = AccountBalanceService::getAccountDisplayName('vehicle_4');
// => "🚗 49B-08879"
```

### Command tái tính toán

```bash
php artisan transactions:recalculate-balances
```

Command này sẽ:
- Tái tính toán số dư cho TẤT CẢ giao dịch
- Hiển thị tổng quan số dư hiện tại
- Sử dụng khi migrate data hoặc fix lỗi

## 🎨 GIAO DIỆN

### 1. Trang /transactions

#### Số dư tài khoản (phía trên)
```
💰 Số dư tài khoản
┌─────────────────────────────────────────┐
│ 🏢 Quỹ công ty: 12,599,000đ            │
│ 📊 Quỹ dự kiến chi: 48,000,000đ        │
│ 💵 Khả dụng: -35,401,000đ              │
└─────────────────────────────────────────┘
```

#### Thông tin luồng tiền trong mỗi giao dịch
```
Ghi chú: Trả lương tài xế
Luồng: 🚗 49B-08879 → 👤 Nguyễn Văn A
Số dư sau: 5,200,000đ
```

### 2. Form tạo giao dịch

Khi chọn **"Chi"**, hiển thị thêm:
```
Nguồn chi *
┌──────────────────────────────────┐
│ -- Từ tài khoản xe --           │
│ 💰 Từ quỹ dự kiến chi            │
└──────────────────────────────────┘
💡 Nếu chọn "Từ quỹ dự kiến chi", 
   số tiền sẽ được trừ từ quỹ dự kiến chi
```

## 📝 CÁCH SỬ DỤNG

### 1. Tạo dự kiến chi

```
1. Vào /transactions/create
2. Chọn type: "Dự kiến chi"
3. Nhập số tiền: 5,000,000
4. Ghi chú: "Dự kiến chi sửa xe tháng 12"
5. Submit

=> Tạo giao dịch:
   Từ: company_fund
   Đến: company_reserved
   Số dư dự kiến: +5,000,000đ
```

### 2. Chi thực tế từ dự kiến

```
1. Vào /transactions/create
2. Chọn type: "Chi"
3. Chọn nguồn: "💰 Từ quỹ dự kiến chi"
4. Nhập số tiền: 3,000,000
5. Ghi chú: "Sửa xe thực tế"
6. Submit

=> Tạo giao dịch:
   Từ: company_reserved
   Đến: partner (hoặc staff)
   Số dư dự kiến: -3,000,000đ
```

### 3. Hủy dự kiến chi

```
1. Tìm giao dịch dự kiến chi
2. Xóa giao dịch

=> Tự động:
   Từ: company_reserved
   Đến: company_fund
   Hoàn tiền về quỹ
```

## 🔍 KIỂM TRA & TROUBLESHOOTING

### Kiểm tra số dư hiện tại

```bash
php artisan tinker
```

```php
use App\Services\AccountBalanceService;

// Số dư quỹ công ty
AccountBalanceService::getCurrentBalance('company_fund');

// Số dư dự kiến chi
AccountBalanceService::getCurrentBalance('company_reserved');

// Số dư xe
AccountBalanceService::getCurrentBalance('vehicle_4');

// Tổng quan
$summary = AccountBalanceService::getBalancesSummary();
print_r($summary);
```

### Tái tính toán nếu số dư sai

```bash
php artisan transactions:recalculate-balances
```

### Kiểm tra giao dịch có tracking chưa

```php
$transaction = Transaction::find(123);

echo "Từ: " . $transaction->from_account_display . "\n";
echo "Đến: " . $transaction->to_account_display . "\n";
echo "Số dư đích sau: " . number_format($transaction->to_balance_after) . "đ\n";
```

## ⚠️ LƯU Ý QUAN TRỌNG

### 1. Dự kiến chi
- ❌ **KHÔNG** ảnh hưởng số dư xe
- ✅ **CHỈ** dùng từ tài khoản công ty
- ✅ Tracking riêng trong `company_reserved`

### 2. Số dư xe
- ✅ Tính: Thu - Chi - Nộp quỹ + Vay - Trả nợ
- ❌ **KHÔNG** bao gồm dự kiến chi

### 3. Auto-tracking
- ✅ Tự động tính khi tạo giao dịch mới
- ✅ Tự động cập nhật from/to account
- ⚠️ Nếu edit giao dịch cũ, cần chạy lại `recalculate-balances`

## 📚 FILES LIÊN QUAN

### Migration
- `database/migrations/2025_12_25_174419_add_account_tracking_to_transactions_table.php`

### Service
- `app/Services/AccountBalanceService.php`

### Model
- `app/Models/Transaction.php` (đã update fillable, casts, accessors)

### Controller
- `app/Http/Controllers/TransactionController.php` (pass balances to view)

### View
- `resources/views/transactions/index.blade.php` (hiển thị số dư & luồng)
- `resources/views/transactions/create.blade.php` (form chọn nguồn)

### Command
- `app/Console/Commands/RecalculateTransactionBalances.php`

## 🎯 TÍNH NĂNG ĐÃ TRIỂN KHAI

- ✅ Tracking tài khoản nguồn/đích
- ✅ Tính số dư trước/sau giao dịch
- ✅ Hiển thị luồng tiền trên UI
- ✅ Quản lý quỹ dự kiến chi riêng
- ✅ Form chọn nguồn chi
- ✅ Command tái tính toán
- ✅ Auto-update khi tạo giao dịch mới
- ✅ Hiển thị tổng quan số dư tài khoản

## 📈 KẾ HOẠCH MỞ RỘNG

### Có thể bổ sung sau:
1. **Báo cáo luồng tiền** theo thời gian
2. **Biểu đồ** số dư tài khoản
3. **Cảnh báo** số dư âm
4. **Export** sổ quỹ
5. **Reconciliation** đối chiếu số dư

---

**Cập nhật:** 26/12/2025  
**Version:** 1.0  
**Status:** ✅ Production Ready
