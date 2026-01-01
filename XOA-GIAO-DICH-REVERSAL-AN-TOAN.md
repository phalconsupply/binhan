# ⚠️ XỬ LÝ AN TOÀN KHI XÓA GIAO DỊCH REVERSAL

## ❓ Vấn đề

Khi bạn có một cặp giao dịch reversal:
- **Giao dịch gốc** (ví dụ: `GD20251218-0694`) - Status: `reversed`
- **Giao dịch đảo ngược** (ví dụ: `REV20260101174800`) - Status: `active`

Và bạn muốn xóa giao dịch đảo ngược (`REV20260101174800`), điều gì sẽ xảy ra?

---

## 🔴 HẬU QUẢ KHI XÓA RIÊNG LẺ GIAO DỊCH REVERSAL

### 1. **Broken Relationship (Quan hệ bị hỏng)**
```
Giao dịch gốc GD20251218-0694:
  - lifecycle_status = 'reversed'
  - reversed_by_transaction_id = 810
  
Nhưng Transaction ID 810 (REV20260101174800) đã bị xóa!
=> Database integrity violation
```

### 2. **Số dư tài khoản SAI**
```
Trước khi xóa reversal:
  - Quỹ công ty: -13,182,000đ
  - Bên ngoài: 349,709,805đ
  
Sau khi xóa reversal:
  - Quỹ công ty: -13,542,000đ (giảm 360,000đ) ❌ SAI
  - Bên ngoài: 349,349,805đ (giảm 360,000đ) ❌ SAI
```

### 3. **Journal Entries không cân bằng**
- Reversal có 2 journal entries (Debit + Credit)
- Khi xóa → mất 2 entries → tổng Debit ≠ Credit
- Báo cáo tài chính sai

### 4. **Audit Trail bị phá vỡ**
- Không biết reversal ở đâu
- Không thể truy vết được lịch sử thay đổi
- Vi phạm nguyên tắc kế toán

---

## ✅ GIẢI PHÁP AN TOÀN

Hệ thống cung cấp 2 methods an toàn trong `TransactionLifecycleService`:

### **Option 1: Xóa CẢ 2 giao dịch (gốc + reversal)**

**Khi nào dùng:** Cả 2 giao dịch đều sai/không cần thiết

**Code:**
```php
use App\Services\TransactionLifecycleService;

$service = new TransactionLifecycleService();
$transaction = Transaction::where('code', 'GD20251218-0694')->first();

// Có thể truyền vào giao dịch gốc HOẶC reversal, đều work
$service->deleteReversalPair($transaction, 'Cả 2 giao dịch đều không cần thiết');
```

**Kết quả:**
- Cả 2 giao dịch đều bị soft delete
- Lifecycle status → `cancelled`
- Số dư tài khoản được recalculate đúng
- Có thể restore nếu cần

---

### **Option 2: Phục hồi giao dịch gốc (Undo Reversal)**

**Khi nào dùng:** Giao dịch gốc là ĐÚNG, không nên đã reverse

**Code:**
```php
use App\Services\TransactionLifecycleService;

$service = new TransactionLifecycleService();
$transaction = Transaction::where('code', 'GD20251218-0694')->first();

// Có thể truyền vào giao dịch gốc HOẶC reversal
$restored = $service->undoReversal($transaction, 'Giao dịch gốc là đúng');
```

**Kết quả:**
- Reversal bị soft delete
- Giao dịch gốc:
  - lifecycle_status → `active`
  - reversed_by_transaction_id → `NULL`
- Số dư tài khoản được recalculate
- Giao dịch gốc hoạt động bình thường trở lại

---

## 📊 SO SÁNH CÁC GIẢI PHÁP

| Tình huống | Giải pháp | Method | Kết quả |
|------------|-----------|--------|---------|
| Cả 2 giao dịch đều SAI | Xóa cả 2 | `deleteReversalPair()` | Cả 2 biến mất |
| Giao dịch gốc là ĐÚNG | Phục hồi gốc | `undoReversal()` | Gốc về active, reversal xóa |
| Giao dịch gốc là SAI | Giữ nguyên | Không làm gì | Cả 2 vẫn tồn tại |
| ❌ Xóa riêng reversal | **NGUY HIỂM** | ❌ Không dùng | Broken relationship, số dư sai |

---

## 🎯 DEMO

### File demo: `demo-handle-reversal-deletion.php`

```bash
php demo-handle-reversal-deletion.php
```

Demo này sẽ:
1. Hiển thị cặp giao dịch hiện tại
2. Cho bạn chọn Option 1 hoặc Option 2
3. Thực hiện và hiển thị kết quả
4. Verify trạng thái sau khi xử lý

---

## 🔒 BẢO VỆ CHỐNG XÓA NHẦM

### **Checks trong `deleteReversalPair()`:**
```php
// ✅ Kiểm tra có phải cặp reversal không
if (!$transaction->reverses_transaction_id && 
    $transaction->lifecycle_status !== 'reversed') {
    throw new \Exception("Giao dịch này không phải là một cặp reversal!");
}

// ✅ Kiểm tra cả 2 giao dịch tồn tại
if (!$original || !$reversal) {
    throw new \Exception("Không tìm thấy cặp giao dịch reversal!");
}

// ✅ Kiểm tra không bị lock
if ($original->is_locked || $reversal->is_locked) {
    throw new \Exception("Một trong hai giao dịch đã bị khóa!");
}
```

### **Checks trong `undoReversal()`:**
- Tương tự như `deleteReversalPair()`
- Thêm validation giao dịch gốc phải ở trạng thái `reversed`

---

## 📝 BEST PRACTICES

### ✅ DO (Nên làm):
1. **Luôn dùng methods có sẵn:**
   - `deleteReversalPair()` - Xóa an toàn
   - `undoReversal()` - Phục hồi an toàn
   
2. **Kiểm tra trước khi xóa:**
   ```bash
   php analyze-delete-reversal.php
   ```
   
3. **Test trên staging trước:**
   ```bash
   php demo-handle-reversal-deletion.php
   ```
   
4. **Backup trước khi thao tác quan trọng**

5. **Dùng soft delete (có thể restore)**

### ❌ DON'T (Không nên):
1. **Xóa riêng lẻ một trong hai giao dịch**
   ```php
   // ❌ NGUY HIỂM - Đừng làm thế này!
   $reversal->delete();
   ```

2. **Dùng forceDelete() (hard delete)**
   ```php
   // ❌ Không thể restore
   $transaction->forceDelete();
   ```

3. **Update manual lifecycle_status mà không xử lý cặp**
   ```php
   // ❌ Sẽ gây broken relationship
   $original->update(['lifecycle_status' => 'active']);
   ```

---

## 🧪 TESTING

### Test case 1: Xóa cả 2 giao dịch
```bash
php demo-handle-reversal-deletion.php
# Chọn Option 1
# Verify: Cả 2 đều deleted_at NOT NULL
```

### Test case 2: Undo reversal
```bash
php demo-handle-reversal-deletion.php
# Chọn Option 2
# Verify: Original active, Reversal deleted
```

### Test case 3: Kiểm tra số dư
```bash
php artisan accounts:reconcile --all
# Verify: Tất cả accounts cân bằng
```

---

## 🎓 KẾT LUẬN

**Quy tắc vàng:** Một cặp reversal (original + reversal) là một ĐƠN VỊ không thể tách rời.

**3 lựa chọn duy nhất:**
1. Giữ nguyên CẢ 2 (audit trail hoàn chỉnh)
2. Xóa CẢ 2 (đều không cần thiết)
3. Phục hồi giao dịch gốc (gốc đúng, reversal sai)

**❌ Không bao giờ:** Xóa riêng lẻ một trong hai!

---

## 📚 Tham khảo

- [GIAI-PHAP-SUA-XOA-GIAO-DICH.md](GIAI-PHAP-SUA-XOA-GIAO-DICH.md) - Giải pháp tổng quan
- [GIAI-THICH-HE-THONG-KE-TOAN.md](GIAI-THICH-HE-THONG-KE-TOAN.md) - Giải thích hệ thống
- `analyze-delete-reversal.php` - Phân tích tác động
- `demo-handle-reversal-deletion.php` - Demo thực tế

---

**⚠️ Cảnh báo cuối:** Vi phạm quy tắc này có thể dẫn đến:
- Số dư tài khoản SAI
- Báo cáo tài chính SAI
- Audit trail bị phá vỡ
- Database integrity violation

**✅ An toàn:** Luôn dùng `deleteReversalPair()` hoặc `undoReversal()`
