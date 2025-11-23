# 🐛 BUG FIX: Double Wage Transactions

## 📋 Vấn đề

**Triệu chứng**: Incident #10 có tiền công nhân viên bị tính **double** (chi 2 lần cho cùng 1 giao dịch)

**Ví dụ**:
- Lê Phong (Lái xe): 450.000đ → Chi thực tế: **900.000đ** (2 transactions)
- Nguyễn Quốc Vũ (NVYT): 450.000đ → Chi thực tế: **900.000đ** (2 transactions)

## 🔍 Nguyên nhân

**Root Cause**: Pattern matching không khớp giữa `store()` và `update()` methods

### Chi tiết:

1. **Khi tạo mới incident (store())**:
   - Transaction được tạo với note: 
     - `"Công lái xe: Lê Phong"`
     - `"Công NVYT: Nguyễn Quốc Vũ"`

2. **Khi edit incident (update())**:
   - Code cố xóa transactions cũ với pattern: `'LIKE Tiền công:%'`
   - ❌ Pattern **KHÔNG MATCH** với note từ store()
   - ✅ Code tạo transaction mới với note: `"Tiền công: Lê Phong"`
   - **Kết quả**: 2 transactions cùng tồn tại → DOUBLE!

### Code cũ (có bug):

```php
// IncidentController::update() - DÒNG 595-598
Transaction::where('incident_id', $incident->id)
    ->whereNotNull('staff_id')
    ->where('note', 'LIKE', 'Tiền công:%')  // ❌ Không match "Công lái xe:"
    ->delete();
```

## ✅ Giải pháp

### 1. Fix logic delete trong update()

**Thay đổi**: Xóa tất cả wage transactions theo `staff_id`, không dùng pattern matching

```php
// IncidentController::update() - DÒNG 595-598 (SAU KHI FIX)
Transaction::where('incident_id', $incident->id)
    ->whereNotNull('staff_id')
    ->delete();  // ✓ Xóa tất cả wage transactions, bất kể note format
```

**Lý do**: Đảm bảo xóa được tất cả wage transactions cũ, bất kể format note (từ store hay update trước đó)

### 2. Chuẩn hóa note format

Thống nhất format note giữa store() và update():

```php
// Driver wage
'note' => 'Tiền công lái xe: ' . ($staff ? $staff->full_name : 'Lái xe')

// Medical staff wage  
'note' => 'Tiền công nhân viên y tế: ' . ($staff ? $staff->full_name : 'Nhân viên y tế')
```

**Thay đổi**:
- ✅ Dùng `full_name` thay vì `name` (consistent)
- ✅ Note format chi tiết hơn cho dễ đọc

### 3. Fix existing data

Script: `fix-double-wage-transactions.php`

**Kết quả**:
- Tìm thấy: 2 staff với double transactions
- Đã xóa: 2 duplicate transactions (#81, #82)
- Giữ lại: 2 original transactions (#76, #77)

## 🧪 Verification

### Trước khi fix:

```
Incident #10:
  Lê Phong: 2 transactions (900.000đ total, expected 450.000đ) ❌
  Nguyễn Quốc Vũ: 2 transactions (900.000đ total, expected 450.000đ) ❌
```

### Sau khi fix:

```
Incident #10:
  Lê Phong: 1 transaction (450.000đ) ✓ MATCH
  Nguyễn Quốc Vũ: 1 transaction (450.000đ) ✓ MATCH
```

### System Audit:

```
✓ Double Wage Transactions: 0
✓ Mismatched Wages: 0
✓ Orphaned Transactions: 0
✓ Missing Transactions: 0
✓ System is healthy!
```

## 📂 Files Changed

1. **app/Http/Controllers/IncidentController.php**
   - Line 595-598: Fix delete logic (remove pattern matching)
   - Line 620-633: Standardize driver wage note format
   - Line 650-663: Standardize medical staff wage note format

2. **fix-double-wage-transactions.php** (NEW)
   - Script to fix existing double transactions in database

3. **audit-transaction-integrity.php** (NEW)
   - Comprehensive system audit script

4. **analyze-incident-10.php** (NEW)
   - Detailed analysis script for debugging

## 🎯 Impact

### Before Fix:
- ❌ Incident #10: Overpaying 900.000đ (2x actual wages)
- ❌ Financial reports incorrect
- ❌ Staff payroll calculations wrong
- ❌ Vehicle profit calculations wrong

### After Fix:
- ✅ Incident #10: Correct wages
- ✅ No more double creation on edit
- ✅ All financial calculations accurate
- ✅ System integrity verified

## 🔮 Future Improvements

**Short-term**: ✅ DONE
- Fix delete logic
- Clean existing data
- Standardize note format

**Medium-term**: Use transaction_category (as planned in IMPLEMENTATION-GUIDE.md)
- Add `transaction_category` field for precise filtering
- Implement soft delete with audit trail
- Better historical tracking

**Long-term**:
- Add validation to prevent duplicates at database level
- Add automated tests for incident edit scenarios
- Dashboard to monitor transaction integrity

## 📝 Related Documents

- `IMPLEMENTATION-GUIDE.md`: Complete solution với transaction_category
- `INCIDENT-TRANSACTION-SOLUTION.md`: Analysis of transaction management issues

## ✅ Checklist

- [x] Identify root cause (pattern matching issue)
- [x] Fix delete logic in update()
- [x] Standardize note format
- [x] Clean existing double transactions (2 deleted)
- [x] Verify incident #10 is fixed
- [x] Audit entire system (0 issues found)
- [x] Document fix
- [x] Ready to commit

---

**Date Fixed**: November 24, 2025  
**Fixed By**: GitHub Copilot  
**Verified**: ✅ System audit passed
