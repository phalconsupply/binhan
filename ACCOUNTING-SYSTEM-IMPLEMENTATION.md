# 🎯 HỆ THỐNG KẾ TOÁN 3 PHASES - IMPLEMENTATION GUIDE

## 📋 TỔNG QUAN

Đã hoàn thành cải tiến hệ thống quản lý giao dịch từ mô hình đơn giản sang hệ thống kế toán **Double-Entry Bookkeeping** đầy đủ theo 3 giai đoạn.

---

## ✅ PHASE 1: FIX CRITICAL ISSUES (Hoàn thành)

### 1.1 Balance Validation ✅
**Files created:**
- `app/Exceptions/InsufficientBalanceException.php`
- Updated `app/Services/AccountBalanceService.php`
- Updated `app/Http/Controllers/TransactionController.php`

**Tính năng:**
- Validate số dư trước khi chi tiền
- Throw exception với thông tin chi tiết
- Render error message user-friendly

**Cách sử dụng:**
```php
// Tự động validate trong TransactionController::store()
// Hoặc gọi thủ công:
AccountBalanceService::validateSufficientBalance('vehicle_4', 1000000);
```

### 1.2 Database Constraints ✅
**Files created:**
- `database/migrations/2026_01_01_100000_add_transaction_constraints.php`

**Constraints added:**
- `CHECK (amount > 0)` - Số tiền phải dương
- `UNIQUE (code)` - Mã giao dịch unique
- Indexes on `from_account`, `to_account` cho performance

**Chạy migration:**
```bash
php artisan migrate
```

### 1.3 Pessimistic Locking ✅
**Updated in:** `app/Services/AccountBalanceService.php`

**Tính năng:**
- Cache lock để prevent race conditions
- 10 seconds timeout
- Auto-release lock sau khi xong

**Implementation:**
```php
$lock = Cache::lock("transaction_balance_update_{$id}", 10);
if ($lock->get()) {
    try {
        // ... update balance ...
    } finally {
        $lock->release();
    }
}
```

### 1.4 Reconciliation Command ✅
**Files created:**
- `app/Console/Commands/ReconcileAccountBalances.php`

**Usage:**
```bash
# Check all accounts
php artisan accounts:reconcile --all

# Check specific account
php artisan accounts:reconcile company_fund

# Auto-fix discrepancies
php artisan accounts:reconcile --all --fix
```

---

## ✅ PHASE 2: NORMALIZE STRUCTURE (Hoàn thành)

### 2.1 Accounts Table ✅
**Files created:**
- `database/migrations/2026_01_01_110000_create_accounts_table.php`
- `app/Models/Account.php`
- `database/seeders/AccountSeeder.php`

**Schema:**
```sql
CREATE TABLE accounts (
    id BIGINT PRIMARY KEY,
    code VARCHAR(50) UNIQUE,     -- 'COMP-FUND', 'VEH-1', 'STAFF-5'
    name VARCHAR(100),            -- 'Quỹ công ty', 'Tài khoản xe 49B08879'
    type ENUM(asset, liability, equity, revenue, expense),
    category ENUM(company_fund, vehicle, staff, customer, ...),
    reference_id BIGINT,          -- vehicle_id, staff_id, etc
    reference_type VARCHAR(50),   -- 'Vehicle', 'Staff'
    parent_id BIGINT,             -- Hierarchical structure
    balance DECIMAL(15,2),        -- Denormalized current balance
    is_active BOOLEAN,
    system_account BOOLEAN        -- Cannot be deleted
)
```

**Seeding:**
```bash
php artisan db:seed --class=AccountSeeder
```

### 2.2 Transaction Foreign Keys ✅
**Files created:**
- `database/migrations/2026_01_01_120000_add_account_fk_to_transactions.php`

**New columns in transactions:**
- `from_account_id` (FK to accounts)
- `to_account_id` (FK to accounts)
- `status` (draft, pending, approved, rejected, completed)
- `approved_by`, `approved_at`
- `rejection_reason`

**Migration strategy:**
- Giữ cột `from_account`, `to_account` (string) cho backward compatibility
- Thêm `from_account_id`, `to_account_id` (FK)
- Migrate data dần dần
- Sau khi xong, có thể drop string columns

### 2.3 Data Migration Command ✅
**Files created:**
- `app/Console/Commands/MigrateTransactionsToAccounts.php`

**Usage:**
```bash
# Dry run (không thay đổi data)
php artisan accounts:migrate-transactions --dry-run

# Execute migration
php artisan accounts:migrate-transactions
```

---

## ✅ PHASE 3: DOUBLE-ENTRY FULL (Hoàn thành)

### 3.1 Transaction Lines Table ✅
**Files created:**
- `database/migrations/2026_01_01_130000_create_transaction_lines_table.php`
- `app/Models/TransactionLine.php`

**Schema:**
```sql
CREATE TABLE transaction_lines (
    id BIGINT PRIMARY KEY,
    transaction_id BIGINT FK,
    account_id BIGINT FK,
    debit DECIMAL(15,2) CHECK (debit >= 0),
    credit DECIMAL(15,2) CHECK (credit >= 0),
    description TEXT,
    line_number INT,
    CHECK (NOT (debit > 0 AND credit > 0))  -- Only one side
)
```

**Ví dụ Journal Entry:**
```
Transaction: Thu 1,000,000đ từ khách hàng vào xe 49B08879

Line 1: Debit  1,000,000đ - Account: Vehicle_4 (Asset tăng)
Line 2: Credit 1,000,000đ - Account: Customer (Revenue)
```

### 3.2 Double-Entry Service ✅
**Files created:**
- `app/Services/DoubleEntryService.php`

**Main methods:**
```php
// Convert transaction to journal entries
DoubleEntryService::createJournalEntries($transaction);

// Validate debits = credits
DoubleEntryService::validateBalance($transaction);

// Get trial balance
$trialBalance = DoubleEntryService::getTrialBalance();
```

**Accounting Rules:**
| Account Type | Debit | Credit |
|--------------|-------|--------|
| Asset (Tài sản) | Tăng (+) | Giảm (-) |
| Expense (Chi phí) | Tăng (+) | Giảm (-) |
| Liability (Nợ) | Giảm (-) | Tăng (+) |
| Equity (Vốn) | Giảm (-) | Tăng (+) |
| Revenue (Doanh thu) | Giảm (-) | Tăng (+) |

---

## 🚀 DEPLOYMENT CHECKLIST

### Step 1: Backup
```bash
# Backup database
php artisan backup:run

# Or manual backup
mysqldump -u root binhan > binhan_backup_$(date +%Y%m%d).sql
```

### Step 2: Run Migrations
```bash
php artisan migrate
```

### Step 3: Seed Accounts
```bash
php artisan db:seed --class=AccountSeeder
```

### Step 4: Migrate Existing Transactions
```bash
# Dry run first
php artisan accounts:migrate-transactions --dry-run

# If OK, execute
php artisan accounts:migrate-transactions
```

### Step 5: Reconcile Balances
```bash
php artisan accounts:reconcile --all

# If discrepancies found, auto-fix
php artisan accounts:reconcile --all --fix
```

### Step 6: Clear Cache
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

### Step 7: Test
```bash
# Test creating new transaction
# Should auto-validate balance
# Should auto-create journal entries
```

---

## 📊 REPORTING CAPABILITIES

Sau khi hoàn thành 3 phases, bạn có thể tạo:

### 1. Trial Balance
```php
$trialBalance = DoubleEntryService::getTrialBalance();
// Shows all accounts with debits, credits, and balance
```

### 2. Balance Sheet
```sql
SELECT 
    type,
    SUM(CASE WHEN type IN ('asset') THEN balance ELSE 0 END) as assets,
    SUM(CASE WHEN type IN ('liability') THEN balance ELSE 0 END) as liabilities,
    SUM(CASE WHEN type IN ('equity') THEN balance ELSE 0 END) as equity
FROM accounts
WHERE is_active = true
```

### 3. Profit & Loss Statement
```sql
SELECT 
    SUM(CASE WHEN type = 'revenue' THEN credit - debit ELSE 0 END) as total_revenue,
    SUM(CASE WHEN type = 'expense' THEN debit - credit ELSE 0 END) as total_expense
FROM transaction_lines
INNER JOIN accounts ON transaction_lines.account_id = accounts.id
WHERE accounts.type IN ('revenue', 'expense')
```

### 4. Cash Flow Statement
```sql
SELECT 
    t.date,
    t.code,
    a_from.name as from_account,
    a_to.name as to_account,
    t.amount
FROM transactions t
LEFT JOIN accounts a_from ON t.from_account_id = a_from.id
LEFT JOIN accounts a_to ON t.to_account_id = a_to.id
WHERE a_from.type = 'asset' OR a_to.type = 'asset'
ORDER BY t.date DESC
```

---

## ⚠️ IMPORTANT NOTES

### Backward Compatibility
- String-based account columns (`from_account`, `to_account`) vẫn giữ
- Cả 2 hệ thống (old & new) chạy song song trong giai đoạn chuyển tiếp
- Sau khi migrate xong và test OK, có thể drop string columns

### Performance Considerations
- Balance được denormalize trong `accounts` table
- Indexes đã thêm cho query performance
- Sử dụng cache lock thay vì database lock

### Data Integrity
- CHECK constraints ở DB level
- UNIQUE constraint trên transaction code
- Foreign keys enforce referential integrity
- Validation layer ở application level

### Testing
Trước khi deploy production:
1. ✅ Test với dry-run mode
2. ✅ Backup database
3. ✅ Test trên staging environment
4. ✅ Verify reconciliation
5. ✅ Load test với concurrent transactions

---

## 🎓 NEXT STEPS (Optional Enhancements)

1. **Web UI for Reconciliation**
   - Dashboard hiển thị balance discrepancies
   - One-click fix button

2. **Automated Tests**
   - Unit tests cho AccountBalanceService
   - Integration tests cho transaction creation
   - Load tests cho concurrent operations

3. **Audit Reports**
   - Monthly reconciliation reports
   - Balance change history
   - Transaction approval logs

4. **Multi-currency Support**
   - Exchange rates table
   - Currency conversion in journal entries

5. **Budget Management**
   - Budget vs Actual reports
   - Variance analysis
   - Alerts for budget overrun

---

## 📞 SUPPORT

Nếu gặp issue trong quá trình deployment:

1. Check logs: `storage/logs/laravel.log`
2. Run diagnostics: `php artisan accounts:reconcile --all`
3. Verify constraints: `SHOW CREATE TABLE transactions`
4. Test balance calculation: `AccountBalanceService::getCurrentBalance('company_fund')`

---

**Implementation Date:** January 1, 2026
**Version:** 1.0.0
**Status:** ✅ All 3 Phases Completed
