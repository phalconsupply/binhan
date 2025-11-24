# Hướng dẫn Setup Dự án trên Máy Mới

## Bước 1: Clone dự án
```bash
git clone https://github.com/phalconsupply/binhan.git
cd binhan
```

## Bước 2: Cài đặt dependencies
```bash
composer install
npm install
```

## Bước 3: Cấu hình môi trường
```bash
# Copy file .env.example
cp .env.example .env

# Generate application key
php artisan key:generate
```

## Bước 4: Cấu hình Database
Sửa file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=binhan_db
DB_USERNAME=root
DB_PASSWORD=
```

## Bước 5: Tạo database
```sql
CREATE DATABASE binhan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Bước 6: Chạy migrations và seeders
```bash
php artisan migrate:fresh --seed
```

**⚠️ LƯU Ý QUAN TRỌNG:** Lệnh này sẽ:
- Xóa toàn bộ database cũ
- Tạo lại tất cả tables
- Chạy seeders để tạo:
  - 8 roles với đầy đủ permissions
  - Positions, Departments
  - Users mặc định

## Bước 7: Build frontend assets
```bash
npm run build
```

## Bước 8: Start server
```bash
php artisan serve
```

Truy cập: http://127.0.0.1:8000

## Bước 9: Login với tài khoản mặc định

### Admin (Full quyền):
- Email: `admin@binhan.com`
- Password: `password`

### Các tài khoản khác:
- Dispatcher: `dispatcher@binhan.com` / `password`
- Accountant: `accountant@binhan.com` / `password`
- Driver: `driver@binhan.com` / `password`

---

## Khi Pull Code Mới

Nếu đã setup rồi, chỉ cần:

```bash
# 1. Pull code
git pull origin main

# 2. Update dependencies (nếu có thay đổi)
composer install
npm install

# 3. Chạy migrations mới (nếu có)
php artisan migrate

# 4. Rebuild assets (nếu có thay đổi CSS/JS)
npm run build

# 5. Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan permission:cache-reset
```

⚠️ **KHÔNG chạy `migrate:fresh --seed` khi pull code** vì sẽ **XÓA HẾT DATA**!

---

## Troubleshooting

### Vấn đề: Menu không hiển thị đầy đủ
```bash
php artisan permission:cache-reset
php artisan cache:clear
# Đăng xuất và đăng nhập lại
```

### Vấn đề: UI không giống dev
```bash
npm run build
php artisan view:clear
# Hard refresh browser (Ctrl + Shift + R)
```

### Vấn đề: Permission denied / Role không đúng
Kiểm tra database:
```sql
-- Xem roles của user
SELECT u.email, r.name 
FROM users u 
JOIN model_has_roles mhr ON u.id = mhr.model_id 
JOIN roles r ON mhr.role_id = r.id;

-- Xem permissions của role
SELECT r.name as role_name, p.name as permission_name
FROM roles r
JOIN role_has_permissions rhp ON r.id = rhp.role_id
JOIN permissions p ON rhp.permission_id = p.id
WHERE r.name = 'admin';
```

Nếu thiếu permissions, chạy lại seeder:
```bash
php artisan db:seed --class=RoleSeeder
php artisan permission:cache-reset
```
