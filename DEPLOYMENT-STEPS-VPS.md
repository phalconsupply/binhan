# 🚀 HƯỚNG DẪN DEPLOY LÊN VPS - CẬP NHẬT UI FILTER THÁNG

## 📊 TỔNG QUAN CẬP NHẬT

### Thay đổi trong bản cập nhật này:

**Commit 1: Transaction Lifecycle Management**
- ✅ Không cần migration (chỉ thêm field mới, không bắt buộc)
- ✅ Không ảnh hưởng giao dịch hiện tại

**Commit 2: Redesign Month Filter (Calendar Picker)**
- ✅ Chỉ thay đổi UI/Frontend
- ✅ Thay đổi terminology: "Quỹ công ty" → "Lợi nhuận công ty"
- ✅ Thêm filter tháng với calendar picker
- ✅ **KHÔNG CẦN UPDATE DATABASE**
- ✅ **KHÔNG CẦN UPDATE GIAO DỊCH CŨ**

### Files đã thay đổi:
```
✅ app/Http/Controllers/TransactionController.php    (+163 lines)
✅ app/Services/AccountBalanceService.php            (thay đổi display name)
✅ resources/views/staff/earnings.blade.php          (update terminology)
✅ resources/views/transactions/index.blade.php      (+438 lines - UI mới)
```

---

## 🔍 KIỂM TRA TRƯỚC KHI DEPLOY

### 1. Xác nhận đã push code
```bash
git log --oneline -3
# Kết quả mong đợi:
# 61bbe77 Redesign month filter: Calendar picker với navigation controls
# 3d4097e feat: Transaction Lifecycle Management System
# eace251 Thêm hệ thống theo dõi tài khoản...
```

✅ **ĐÃ HOÀN THÀNH** - Code đã được push lên GitHub

---

## 📦 CÁC BƯỚC DEPLOY LÊN VPS

### BƯỚC 1: SSH vào VPS
```bash
ssh root@your-vps-ip
# hoặc
ssh username@your-vps-ip
```

### BƯỚC 2: Chuyển đến thư mục project
```bash
cd /var/www/binhan
# hoặc đường dẫn cài đặt của bạn
```

### BƯỚC 3: Backup Database (Quan trọng!)
```bash
# Tạo backup trước khi cập nhật
php artisan backup:database

# Hoặc dùng mysqldump
mysqldump -u root -p binhan_db > backup_before_update_$(date +%Y%m%d_%H%M%S).sql
```

### BƯỚC 4: Pull code mới từ Git
```bash
git fetch origin
git pull origin main
```

Kết quả mong đợi:
```
Updating eace251..61bbe77
Fast-forward
 app/Http/Controllers/TransactionController.php | 163 +++++++--
 app/Services/AccountBalanceService.php         |   2 +-
 resources/views/staff/earnings.blade.php       |   4 +-
 resources/views/transactions/index.blade.php   | 438 ++++++++++++++++++++++---
 4 files changed, 524 insertions(+), 83 deletions(-)
```

### BƯỚC 5: Cập nhật dependencies (Nếu cần)
```bash
# Nếu có thay đổi composer
composer install --no-dev --optimize-autoloader

# Nếu có thay đổi npm
npm install
npm run build
```

**⚠️ CHÚ Ý:** Bản cập nhật này KHÔNG cần chạy `composer install` vì không có thay đổi dependencies.

### BƯỚC 6: Kiểm tra Migrations
```bash
# Xem danh sách migrations chưa chạy
php artisan migrate:status
```

**⚠️ QUAN TRỌNG:**
```bash
# Nếu có migration mới từ commit "Transaction Lifecycle Management":
php artisan migrate --force

# Migration này chỉ THÊM các field mới (nullable), KHÔNG ảnh hưởng dữ liệu cũ:
# - approved_at, approved_by (cho workflow approval)
# - reversed_by_transaction_id (cho transaction reversal)
# - locked_by, locked_at (cho transaction locking)
# - modified_by (cho audit trail)
```

✅ **Migration an toàn** - Chỉ thêm columns nullable, không xóa/thay đổi data

### BƯỚC 7: Clear Cache
```bash
# Clear tất cả cache
php artisan optimize:clear

# Hoặc clear từng loại:
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### BƯỚC 8: Set Permissions (Linux only)
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### BƯỚC 9: Restart Services
```bash
# Nginx
sudo systemctl restart nginx

# PHP-FPM (tùy version)
sudo systemctl restart php8.2-fpm

# Hoặc Apache
sudo systemctl restart apache2
```

---

## ✅ KIỂM TRA SAU KHI DEPLOY

### 1. Kiểm tra trang /transactions
- [ ] Trang load bình thường
- [ ] Thấy nút "📅 Chọn tháng" bên cạnh tiêu đề "📊 Tài khoản công ty"
- [ ] Click nút filter → Modal hiện ra với:
  - Header màu indigo "Chọn khoảng thời gian"
  - Mũi tên ◀ ▶ để chuyển năm
  - 2 quick filters: "Tất cả" | "Tháng này"
  - Grid tháng 3 cột × 4 hàng (Tháng 1 - Tháng 12)

### 2. Kiểm tra terminology
- [ ] "💰 Lợi nhuận công ty" (không còn "Quỹ công ty")
- [ ] "📊 Quỹ dự kiến chi" (giữ nguyên)
- [ ] Các màn hình staff/earnings cũng đã cập nhật

### 3. Test tính năng filter tháng
```bash
# Test bằng URL trực tiếp:
http://your-domain.com/transactions?quick_filter=current
http://your-domain.com/transactions?stat_year=2025&stat_months[]=2025-01
http://your-domain.com/transactions?stat_months[]=2025-01&stat_months[]=2025-02
```

- [ ] Filter "Tất cả" → Hiển thị tổng hợp + breakdown tháng hiện tại
- [ ] Filter "Tháng này" → Chỉ hiển thị thống kê tháng hiện tại
- [ ] Chọn 1 tháng → Hiển thị stats tháng đó
- [ ] Chọn nhiều tháng → Hiển thị bảng với từng tháng + tổng

### 4. Kiểm tra database
```bash
# Vào MySQL
mysql -u root -p binhan_db

# Kiểm tra không có lỗi
SELECT COUNT(*) FROM transactions;

# Kiểm tra migration mới (nếu chạy)
DESCRIBE transactions;
# Phải thấy: approved_at, approved_by, reversed_by_transaction_id, etc.
```

### 5. Check logs
```bash
# Xem logs Laravel
tail -f storage/logs/laravel.log

# Xem logs Nginx
sudo tail -f /var/log/nginx/error.log

# Xem logs PHP
sudo tail -f /var/log/php8.2-fpm.log
```

---

## ⚠️ CÂU HỎI: CÓ CẦN UPDATE GIAO DỊCH CŨ?

### ❌ **KHÔNG CẦN!** 

**Lý do:**

1. **Thay đổi Display Name:**
   ```php
   // Trong AccountBalanceService.php
   case 'company_fund':
       return '💰 Lợi nhuận công ty'; // Thay đổi từ "🏢 Quỹ công ty"
   ```
   - Đây là thay đổi **runtime display** chỉ
   - Không lưu trong database
   - Không cần update giao dịch cũ

2. **Thay đổi UI/Frontend:**
   - Calendar picker là UI component
   - Filter tháng là query parameter
   - Không ảnh hưởng data structure

3. **Logic tính toán:**
   - Công thức 4-account vẫn giữ nguyên
   - Scopes (revenue, expense) không thay đổi
   - Transaction types không thay đổi

### ✅ **Dữ liệu hiện tại hoàn toàn tương thích**

---

## 🔧 XỬ LÝ LỖI THƯỜNG GẶP

### Lỗi 1: "Class not found" sau khi pull
**Giải pháp:**
```bash
composer dump-autoload
php artisan optimize:clear
```

### Lỗi 2: Modal không hiện
**Nguyên nhân:** Cache CSS/JS cũ trong browser
**Giải pháp:**
```bash
# Hard refresh trên browser: Ctrl+Shift+R (Chrome/Firefox)
# Hoặc xóa cache browser
```

### Lỗi 3: "View not found"
**Giải pháp:**
```bash
php artisan view:clear
php artisan optimize:clear
```

### Lỗi 4: Migration đã chạy nhưng báo lỗi
**Kiểm tra:**
```bash
php artisan migrate:status
```
**Nếu migration trong trạng thái "Pending":**
```bash
php artisan migrate --force
```

### Lỗi 5: Permissions denied
**Giải pháp:**
```bash
# Fix permissions
sudo chown -R www-data:www-data /var/www/binhan
sudo chmod -R 775 storage bootstrap/cache

# Restart web server
sudo systemctl restart nginx php8.2-fpm
```

---

## 📝 ROLLBACK (Nếu cần)

Nếu gặp vấn đề nghiêm trọng, rollback về version trước:

```bash
# Quay về commit trước đó
git reset --hard eace251

# Restore database từ backup
mysql -u root -p binhan_db < backup_before_update_YYYYMMDD_HHMMSS.sql

# Clear cache
php artisan optimize:clear

# Restart services
sudo systemctl restart nginx php8.2-fpm
```

---

## 🎯 CHECKLIST TỔNG HỢP

### Pre-Deployment
- [x] Code đã push lên GitHub
- [x] Xác nhận không có breaking changes
- [x] Xác nhận không cần update data

### During Deployment
- [ ] SSH vào VPS
- [ ] Backup database
- [ ] Pull code mới
- [ ] Chạy migration (nếu cần)
- [ ] Clear cache
- [ ] Fix permissions
- [ ] Restart services

### Post-Deployment
- [ ] Kiểm tra trang /transactions load
- [ ] Test calendar picker modal
- [ ] Test filter tháng
- [ ] Verify terminology mới
- [ ] Check logs không có error
- [ ] Test với user thực

---

## 📞 SUPPORT

Nếu có vấn đề:
1. Check logs: `tail -f storage/logs/laravel.log`
2. Check nginx: `sudo tail -f /var/log/nginx/error.log`
3. Check migration status: `php artisan migrate:status`
4. Rollback nếu cần (xem section trên)

---

## ✨ KẾT LUẬN

**Deployment này rất đơn giản và an toàn:**
- ✅ Chỉ thay đổi UI/Display
- ✅ Không cần update database records
- ✅ Không có breaking changes
- ✅ Dễ dàng rollback nếu cần

**Thời gian ước tính:** 10-15 phút

**Downtime:** 0-2 phút (trong lúc restart services)

**Rủi ro:** Rất thấp (chỉ có UI changes)
