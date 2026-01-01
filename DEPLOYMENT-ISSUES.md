# 🚨 DEPLOYMENT ISSUES & SOLUTIONS

## ⚠️ VẤN ĐỀ PHÁT HIỆN

Sau khi deploy Phase 1 & 2, phát hiện:
- ✅ Migrations chạy thành công
- ✅ Accounts table created (31 accounts)
- ✅ Transaction FKs added
- ❌ **Balance discrepancy**: Calculated (-7.4M) ≠ Last Recorded (1.5M)

## 🔍 NGUYÊN NHÂN

Phase 1 implementation thêm validation cho **transactions mới**, nhưng **transactions cũ** (đã tồn tại) chưa được recalculate đúng cách vì:

1. Validation đang chặn recalculate khi balance âm
2. Một số transactions có thể missing from_account/to_account values

## ✅ GIẢI PHÁP ĐÃ THỰC HIỆN

### 1. Sửa AccountBalanceService
```php
// Thêm parameter $skipValidation
public static function updateTransactionBalances(
    Transaction $transaction, 
    bool $skipValidation = false
): void
```

### 2. Sửa RecalculateTransactionBalances Command
```php
// Pass skipValidation = true
AccountBalanceService::updateTransactionBalances($transaction, true);
```

### 3. Sửa ReconcileAccountBalances Command  
```php
// Pass skipValidation = true when recalculating
AccountBalanceService::updateTransactionBalances($transaction, true);
```

## 🎯 KHUYẾN NGHỊ DEPLOYMENT

### Option A: RESET & FRESH (Recommended cho local testing)
```bash
# 1. Rollback Phase 1 & 2 migrations
php artisan migrate:rollback --step=3

# 2. Delete test files
rm test-*.php

# 3. Re-run migrations từ đầu
php artisan migrate

# 4. Clear old transaction balance data
php artisan tinker
Transaction::query()->update([
    'from_account' => null,
    'to_account' => null,
    'from_balance_before' => null,
    'from_balance_after' => null,
    'to_balance_before' => null,
    'to_balance_after' => null
]);

# 5. Recalculate từ đầu với skipValidation
php artisan transactions:recalculate-balances

# 6. Verify
php artisan accounts:reconcile --all
```

### Option B: FIX IN PLACE (Cho production)
```bash
# 1. Backup trước
mysqldump -u root binhan_db > backup_before_fix.sql

# 2. Clear balance columns
php artisan tinker
Transaction::query()->update([
    'from_balance_before' => null,
    'from_balance_after' => null,
    'to_balance_before' => null,
    'to_balance_after' => null
]);

# 3. Re-run full recalculation
php artisan transactions:recalculate-balances

# 4. Check results
php artisan accounts:reconcile --all

# 5. Nếu vẫn fail, check missing from_account/to_account
php artisan tinker
Transaction::whereNull('from_account')->orWhereNull('to_account')->count();
```

## 🧪 TESTING VALIDATION

### Test 1: Create new transaction with insufficient balance
```bash
# Tạo giao dịch CHI từ vehicle có 0đ
# Should show error: "Số dư không đủ!"
```

### Test 2: Reconcile accounts
```bash
php artisan accounts:reconcile --all
# Should show: ✅ All accounts are balanced!
```

### Test 3: Check company fund
```bash
php artisan tinker
>>> use App\Services\AccountBalanceService;
>>> AccountBalanceService::getCurrentBalance('company_fund');
# Should match last transaction's balance
```

## 📊 KẾT QUẢ HIỆN TẠI

**Phase 1 & 2:**
- [x] Migrations run successfully  
- [x] Constraints added
- [x] Accounts table created
- [x] 564 transactions migrated to account FKs
- [ ] Balance reconciliation (pending fix)

**Số dư hiện tại:**
- Quỹ công ty: -7.401.000đ
- Quỹ dự kiến chi: 48.000.000đ  
- Khả dụng: -55.401.000đ

## 🔄 NEXT STEPS

1. **Quyết định deployment strategy:**
   - Option A (Fresh) - Cho local/dev
   - Option B (In-place) - Cho staging/production

2. **Sau khi fix xong balance:**
   ```bash
   # Phase 3 (optional): Double-entry
   php artisan migrate --path=database/migrations/2026_01_01_130000_create_transaction_lines_table.php
   ```

3. **Testing checklist:**
   - [ ] All accounts reconciled
   - [ ] Create new transaction works
   - [ ] Insufficient balance validation works
   - [ ] Concurrent transactions safe (with locking)

## ⚡ TEMPORARY WORKAROUND

Nếu muốn test validation ngay:
```php
// Trong TransactionController::store()
// Tạm comment validation để test các tính năng khác
// if ($fromAccount && !in_array($validated['type'], ['thu', 'nop_quy'])) {
//     AccountBalanceService::validateSufficientBalance($fromAccount, $validated['amount']);
// }
```

## 🎓 LESSONS LEARNED

1. **Migration strategy:** Nên có separate migration cho:
   - Schema changes (add columns)
   - Data migration (populate values)
   - Constraint enforcement (add validation)

2. **Testing:** Nên test từng phase riêng trước khi combine

3. **Rollback plan:** Luôn có backup và rollback script sẵn

---

**Status:** ⚠️ Pending balance reconciliation fix  
**Next Action:** Choose deployment strategy và execute  
**ETA:** 10-15 phút cho Option A, 5-10 phút cho Option B
