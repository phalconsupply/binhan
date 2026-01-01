# GIẢI PHÁP TỐI ƯU: QUẢN LÝ SỬA/XÓA GIAO DỊCH

## 🎯 Nguyên tắc vàng

**KHÔNG BAO GIỜ XÓA THẬT (HARD DELETE)** - Luôn giữ audit trail

---

## 📋 3 PHƯƠNG ÁN CHÍNH

### 1️⃣ **REVERSAL (Đảo ngược) - KHUYẾN NGHỊ NHẤT**

**Khi nào dùng:**
- Giao dịch đã được ghi nhận nhưng SAI
- Cần hủy bỏ ảnh hưởng của giao dịch
- Muốn giữ lịch sử đầy đủ

**Cách hoạt động:**
```
Giao dịch gốc: Quỹ công ty → Bên ngoài (360,000đ) [CHI]
Reversal:       Bên ngoài → Quỹ công ty (360,000đ) [THU]
```

**Kết quả:**
- ✅ Số dư về như cũ (trước khi có giao dịch sai)
- ✅ Lịch sử đầy đủ (2 giao dịch: gốc + reversal)
- ✅ Audit trail hoàn chỉnh
- ✅ Dễ kiểm toán

**Command:**
```bash
# Preview trước
php artisan transaction:reverse GD20251218-0694 "Nhập sai số tiền" --preview

# Thực hiện
php artisan transaction:reverse GD20251218-0694 "Nhập sai số tiền"

# Kết quả:
# - Tạo giao dịch mới: REV20260102-0565-GD20251218-0694
# - Đánh dấu gốc: lifecycle_status = 'reversed'
# - Số dư tự động cập nhật đúng
```

**Ví dụ thực tế:**
```
Ban đầu:
  Quỹ công ty: -6,401,000đ
  Bên ngoài: 150,685,903đ

Sau reversal:
  Quỹ công ty: -6,041,000đ (hoàn lại 360k)
  Bên ngoài: 150,325,903đ (trừ lại 360k)
  
Database:
  Transaction #694: lifecycle_status = 'reversed'
  Transaction #565 (new): reverses_transaction_id = 694
```

---

### 2️⃣ **REPLACEMENT (Thay thế) - KHI CẦN SỬA**

**Khi nào dùng:**
- Giao dịch SAI nhiều thông tin
- Cần tạo giao dịch MỚI ĐÚNG

**Cách hoạt động:**
```
1. Tạo giao dịch mới (đúng)
2. Đánh dấu giao dịch cũ: lifecycle_status = 'replaced'
3. Link: old.replaced_by = new.id
```

**Ví dụ:**
```php
$lifecycleService = new TransactionLifecycleService();

$newTransaction = $lifecycleService->replaceTransaction(
    $oldTransaction,
    [
        'amount' => 400000,  // Đúng là 400k, không phải 360k
        'from_account_id' => 5,
        'to_account_id' => 3,
        'type' => 'chi',
        // ... các field khác
    ],
    "Sửa số tiền từ 360k thành 400k"
);
```

**Kết quả:**
- ✅ Giao dịch mới đúng được tạo
- ✅ Giao dịch cũ: is_active=false, lifecycle_status='replaced'
- ✅ Có link giữa cũ và mới
- ✅ Số dư tự động recalculate

---

### 3️⃣ **SOFT DELETE (Xóa mềm) - KHI NHẬP SAI HOÀN TOÀN**

**Khi nào dùng:**
- Giao dịch nhập SAI HOÀN TOÀN (không có thật)
- Test data cần xóa
- Duplicate entries

**Cách hoạt động:**
```
1. Đánh dấu deleted_at = now()
2. Set lifecycle_status = 'cancelled'
3. Recalculate toàn bộ (bỏ qua giao dịch đã xóa)
```

**Command:**
```php
$lifecycleService = new TransactionLifecycleService();
$lifecycleService->softDeleteTransaction(
    $transaction,
    "Giao dịch test, không có thật"
);
```

**Khôi phục nếu cần:**
```php
$lifecycleService->restoreTransaction($transactionId);
```

---

## 🔒 4️⃣ **LOCK TRANSACTION - BẢO VỆ DỮ LIỆU**

**Khi nào dùng:**
- Đã kiểm toán (audited)
- Đã đóng sổ kỳ kế toán
- Giao dịch quan trọng không được sửa

**Khóa 1 giao dịch:**
```php
$lifecycleService->lockTransaction(
    $transaction,
    "Đã kiểm toán, không được sửa"
);
```

**Khóa cả kỳ (tháng):**
```bash
php artisan period:lock 2025-11-01 2025-11-30 "Đóng sổ tháng 11/2025"
```

**Kết quả:**
- ❌ Không thể reverse
- ❌ Không thể replace
- ❌ Không thể soft delete
- ✅ Chỉ admin mới unlock được

---

## 📊 SO SÁNH CÁC PHƯƠNG ÁN

| Tính năng | Reversal | Replacement | Soft Delete | Hard Delete |
|-----------|----------|-------------|-------------|-------------|
| Giữ lịch sử | ✅✅✅ | ✅✅ | ✅ | ❌ |
| Audit trail | ✅✅✅ | ✅✅ | ✅ | ❌ |
| Số dư đúng | ✅ | ✅ | ✅ | ⚠️ Cần recalc |
| Phức tạp | Thấp | Trung bình | Thấp | Cao |
| An toàn | ✅✅✅ | ✅✅ | ✅ | ❌ |
| Khôi phục | ✅ | ✅ | ✅ | ❌ |

---

## 🔄 WORKFLOW THỰC TẾ

### Scenario 1: Phát hiện giao dịch sai số tiền

```bash
# Bước 1: Kiểm tra giao dịch
php artisan transaction:reverse GD20251218-0694 "Sai số tiền" --preview

# Bước 2: Reverse (hủy giao dịch sai)
php artisan transaction:reverse GD20251218-0694 "Nhập sai số tiền 360k"

# Bước 3: Tạo giao dịch MỚI ĐÚNG qua UI hoặc code
# Amount: 400,000đ (đúng)

# Bước 4: Verify
php artisan accounts:reconcile --all
```

### Scenario 2: Sửa thông tin giao dịch

```php
// Không sửa trực tiếp, mà dùng replacement
$lifecycleService = new TransactionLifecycleService();

$newTransaction = $lifecycleService->replaceTransaction(
    $oldTransaction,
    $newDataArray,
    "Sửa từ account A sang account B"
);
```

### Scenario 3: Xóa giao dịch test

```php
$lifecycleService->softDeleteTransaction(
    $transaction,
    "Test data, không sử dụng"
);
```

### Scenario 4: Đóng sổ tháng

```bash
# Khóa tất cả giao dịch tháng 12/2025
php artisan period:lock 2025-12-01 2025-12-31 "Đóng sổ tháng 12/2025"
```

---

## 💾 DATABASE STRUCTURE

```sql
transactions:
  - id
  - code
  - amount
  - from_account_id
  - to_account_id
  
  -- Soft delete
  - deleted_at
  
  -- Lifecycle
  - lifecycle_status (active/reversed/replaced/cancelled)
  
  -- Reversal tracking
  - reversed_by_transaction_id  (ID giao dịch đảo ngược này)
  - reverses_transaction_id     (ID giao dịch bị đảo ngược)
  
  -- Replacement tracking
  - replaced_by                  (ID giao dịch thay thế)
  
  -- Audit
  - modification_reason
  - modified_by
  - modified_at
  
  -- Lock
  - is_locked
  - locked_at
  - locked_by
```

---

## 🎯 LUỒNG XỬ LÝ HOÀN CHỈNH

```
USER: "Giao dịch #694 sai số tiền 360k, đúng là 400k"

BƯỚC 1: REVERSE giao dịch sai
  ├─ Transaction #694: lifecycle_status = 'reversed'
  ├─ Tạo Transaction #565 (reversal): reverses_transaction_id = 694
  ├─ Số dư quay về trạng thái trước #694
  └─ Journal entries cho cả 2 giao dịch

BƯỚC 2: TẠO giao dịch mới ĐÚNG
  ├─ Transaction #566: amount = 400,000đ
  ├─ from_account_id = 5 (Quỹ công ty)
  ├─ to_account_id = 3 (Bên ngoài)
  ├─ lifecycle_status = 'active'
  └─ note = "Giao dịch đúng, thay thế #694"

BƯỚC 3: VERIFY
  ├─ php artisan accounts:reconcile --all
  ├─ Kiểm tra số dư Quỹ công ty
  ├─ Kiểm tra journal entries balanced
  └─ ✅ Tất cả OK!

KẾT QUẢ:
  ✅ Lịch sử đầy đủ (3 giao dịch: sai, reversal, đúng)
  ✅ Số dư chính xác
  ✅ Audit trail hoàn chỉnh
  ✅ Biết tại sao thay đổi
```

---

## 🚨 CÁC LƯU Ý QUAN TRỌNG

### ❌ KHÔNG BAO GIỜ:
```sql
-- KHÔNG làm thế này!
DELETE FROM transactions WHERE id = 694;

-- KHÔNG sửa trực tiếp!
UPDATE transactions SET amount = 400000 WHERE id = 694;
```

### ✅ LUÔN LUÔN:
```php
// Dùng service
$lifecycleService = new TransactionLifecycleService();
$lifecycleService->reverseTransaction($tx, $reason);

// Sau đó verify
php artisan accounts:reconcile --all
```

### ⚠️ CHÚ Ý:
1. **Locked transactions** không thể reverse/replace/delete
2. **Reversed transactions** không nên delete (delete cả 2: gốc + reversal)
3. Sau mọi thay đổi: **PHẢI recalculate + verify**
4. **Lý do (reason)** bắt buộc - để audit

---

## 🛠️ COMMANDS QUAN TRỌNG

```bash
# Reverse transaction
php artisan transaction:reverse {code} {reason} [--preview]

# Lock period
php artisan period:lock {start} {end} {reason}

# Unlock transaction (admin only)
php artisan transaction:unlock {code}

# List locked transactions
php artisan transaction:list --locked

# List reversed transactions
php artisan transaction:list --reversed

# Verify sau mọi thay đổi
php artisan accounts:reconcile --all
php artisan transactions:recalculate-balances
php artisan transactions:generate-journal-entries --force
```

---

## 📈 LỢI ÍCH CỦA HỆ THỐNG MỚI

### So với hệ thống cũ:

| Hệ thống cũ | Hệ thống mới |
|-------------|--------------|
| ❌ Xóa = mất hẳn | ✅ Soft delete, có thể khôi phục |
| ❌ Sửa trực tiếp = mất lịch sử | ✅ Replacement = giữ lịch sử |
| ❌ Không biết ai sửa, sửa gì | ✅ Audit trail đầy đủ |
| ❌ Số dư sai không phát hiện | ✅ Reconcile command phát hiện ngay |
| ❌ Không thể kiểm toán | ✅ Đầy đủ thông tin kiểm toán |

---

## 📞 KẾT LUẬN

**Thứ tự ưu tiên khi cần sửa/xóa:**

1. **REVERSAL** (đảo ngược) - Dùng trong 90% trường hợp ✅
2. **REPLACEMENT** (thay thế) - Khi cần sửa nhiều thông tin ✅
3. **SOFT DELETE** - Chỉ khi nhập sai hoàn toàn ⚠️
4. **HARD DELETE** - KHÔNG BAO GIỜ ❌

**Nguyên tắc vàng:**
> "Trong kế toán, không có gì bị xóa. Chỉ có giao dịch đảo ngược."

---

**Migration:** `2026_01_02_100000_add_transaction_lifecycle_management.php`  
**Service:** `TransactionLifecycleService.php`  
**Commands:** `ReverseTransaction.php`, `LockPeriod.php`
