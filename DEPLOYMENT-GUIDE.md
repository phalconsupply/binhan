# 📘 HƯỚNG DẪN TRIỂN KHAI DỰ ÁN BINHAN AMBULANCE

> **Phiên bản:** 1.0.0  
> **Cập nhật:** 24/11/2025  
> **Hệ thống:** Quản lý Xe Cấp Cứu

---

## 📋 YÊU CẦU HỆ THỐNG

### Phần mềm bắt buộc:
- **PHP:** >= 8.2.12
- **MySQL:** >= 8.0
- **Composer:** >= 2.x
- **Node.js:** >= 18.x (cho Vite)
- **Git:** >= 2.x
- **Web Server:** Apache/Nginx

### Extensions PHP bắt buộc:
```
- php-curl
- php-dom
- php-fileinfo
- php-filter
- php-hash
- php-mbstring
- php-openssl
- php-pcre
- php-pdo
- php-session
- php-tokenizer
- php-xml
- php-zip
- php-gd (cho xử lý ảnh)
- php-intl
```

---

## 🚀 BƯỚC 1: CLONE DỰ ÁN

```bash
# Clone repository từ GitHub
git clone https://github.com/phalconsupply/binhan.git
cd binhan

# Kiểm tra branch
git branch
# Nên thấy: * main
```

---

## 🔧 BƯỚC 2: CÀI ĐẶT DEPENDENCIES

### 2.1. Cài đặt PHP Dependencies (Composer)

```bash
composer install
```

**Nếu gặp lỗi memory:**
```bash
php -d memory_limit=-1 C:\xampp\php\composer.phar install
```

### 2.2. Cài đặt Node Dependencies (NPM)

```bash
npm install
```

hoặc sử dụng Yarn:
```bash
yarn install
```

---

## ⚙️ BƯỚC 3: CẤU HÌNH MÔI TRƯỜNG

### 3.1. Tạo file .env

```bash
# Copy file .env.example thành .env
copy .env.example .env
```

### 3.2. Cấu hình Database trong file .env

```env
APP_NAME="Binhan Ambulance"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost/binhan/public

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=binhan_db
DB_USERNAME=root
DB_PASSWORD=

# Session & Cache
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Queue
QUEUE_CONNECTION=sync

# Storage
FILESYSTEM_DISK=public

# Media Library
MEDIA_DISK=public
```

### 3.3. Generate Application Key

```bash
php artisan key:generate
```

---

## 💾 BƯỚC 4: TẠO VÀ CẤU HÌNH DATABASE

### 4.1. Tạo Database

**Cách 1: Qua phpMyAdmin**
1. Mở http://localhost/phpmyadmin
2. Tạo database mới tên `binhan_db`
3. Chọn Collation: `utf8mb4_unicode_ci`

**Cách 2: Qua Command Line**
```bash
# Windows (XAMPP)
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE binhan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Linux/Mac
mysql -u root -p -e "CREATE DATABASE binhan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 4.2. Chạy Migrations

```bash
# Chạy tất cả migrations
php artisan migrate
```

**Nếu muốn fresh install (xóa dữ liệu cũ):**
```bash
php artisan migrate:fresh
```

### 4.3. Seed Dữ Liệu Mẫu

```bash
# Chạy tất cả seeders
php artisan db:seed

# Hoặc chỉ chạy seeder cụ thể
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=PositionSeeder
php artisan db:seed --class=DepartmentSeeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=SystemSettingSeeder
```

**Hoặc migrate + seed cùng lúc:**
```bash
php artisan migrate:fresh --seed
```

---

## 📁 BƯỚC 5: CẤU HÌNH STORAGE

### 5.1. Tạo Symbolic Link

```bash
# Tạo symlink từ public/storage -> storage/app/public
php artisan storage:link
```

**Nếu gặp lỗi trên Windows, chạy CMD/PowerShell as Administrator:**
```bash
php artisan storage:link
```

### 5.2. Tạo Thư Mục Storage (nếu chưa có)

```bash
# Windows
mkdir storage\app\public\settings
mkdir storage\app\public\media
mkdir storage\framework\cache
mkdir storage\framework\sessions
mkdir storage\framework\views
mkdir storage\logs

# Linux/Mac
mkdir -p storage/app/public/settings
mkdir -p storage/app/public/media
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
```

### 5.3. Set Permissions (Linux/Mac)

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

---

## 🎨 BƯỚC 6: BUILD FRONTEND ASSETS

### 6.1. Development Build (cho môi trường local)

```bash
npm run dev
```

### 6.2. Production Build (cho production)

```bash
npm run build
```

**Lưu ý:** Nếu chạy `npm run dev`, cần giữ terminal đang chạy. Dùng `npm run build` để build 1 lần.

---

## 🌐 BƯỚC 7: CẤU HÌNH WEB SERVER

### Cấu hình cho XAMPP (Windows)

**Không cần cấu hình gì thêm!** Chỉ cần truy cập:
```
http://localhost/binhan/public
```

### Cấu hình Apache Virtual Host (Optional - cho domain đẹp hơn)

**File:** `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

```apache
<VirtualHost *:80>
    ServerName binhan.local
    DocumentRoot "C:/xampp/htdocs/binhan/public"
    
    <Directory "C:/xampp/htdocs/binhan/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**File:** `C:\Windows\System32\drivers\etc\hosts` (Run as Admin)
```
127.0.0.1    binhan.local
```

Restart Apache, sau đó truy cập: `http://binhan.local`

### Cấu hình Nginx (Linux)

**File:** `/etc/nginx/sites-available/binhan`

```nginx
server {
    listen 80;
    server_name binhan.local;
    root /var/www/binhan/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/binhan /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 🔐 BƯỚC 8: ĐĂNG NHẬP HỆ THỐNG

### Tài khoản mặc định (sau khi seed):

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@binhan.com | password |
| **Dispatcher** | dispatcher@binhan.com | password |
| **Accountant** | accountant@binhan.com | password |
| **Driver** | driver@binhan.com | password |

**⚠️ QUAN TRỌNG:** Đổi mật khẩu ngay sau khi đăng nhập lần đầu!

---

## ✅ BƯỚC 9: KIỂM TRA HỆ THỐNG

### 9.1. Kiểm tra Laravel

```bash
# Kiểm tra thông tin hệ thống
php artisan about

# Kiểm tra routes
php artisan route:list

# Test setting helper
php artisan tinker
>>> setting('company_name')
=> "Binhan Ambulance"
>>> exit
```

### 9.2. Kiểm tra Database

```bash
# Kiểm tra kết nối DB
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit
```

### 9.3. Checklist Tính Năng

Đăng nhập và kiểm tra các trang:

- [ ] **Dashboard** (`/dashboard`) - Hiển thị thống kê và form ghi nhận nhanh
- [ ] **Cấu hình Hệ thống** (`/settings`) - 7 tabs với 58 cấu hình
- [ ] **Quản lý File & Media** (`/media`) - Upload và quản lý file
- [ ] **Vehicles** (`/vehicles`) - Quản lý xe cấp cứu
- [ ] **Incidents** (`/incidents`) - Quản lý chuyến đi
- [ ] **Patients** (`/patients`) - Quản lý bệnh nhân
- [ ] **Staff** (`/staff`) - Quản lý nhân viên
- [ ] **Reports** (`/reports`) - Báo cáo thống kê

### 9.4. Kiểm tra Logo & Favicon

1. Vào `/settings` → Tab **Giao diện**
2. Upload logo (PNG/JPG, tối đa 2MB)
3. Upload favicon (ICO/PNG 32x32)
4. Kiểm tra logo hiển thị ở navigation
5. Kiểm tra favicon hiển thị trên tab trình duyệt

---

## 🔧 BƯỚC 10: CẤU HÌNH BỔ SUNG

### 10.1. Clear Cache

```bash
# Clear tất cả cache
php artisan optimize:clear

# Hoặc clear từng loại
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan permission:cache-reset
```

### 10.2. Optimize cho Production

```bash
# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoload
composer install --optimize-autoloader --no-dev
```

### 10.3. Setup Queue Worker (Optional)

**Nếu muốn xử lý jobs bất đồng bộ:**

```bash
# Chạy queue worker
php artisan queue:work

# Hoặc dùng supervisor (Linux)
sudo apt install supervisor
```

**File:** `/etc/supervisor/conf.d/binhan-worker.conf`
```ini
[program:binhan-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/binhan/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/binhan/storage/logs/worker.log
```

---

## 📊 CẤU TRÚC DỮ LIỆU MẪU

### Roles & Permissions (28 permissions, 8 roles)

**Permissions:**
- `view vehicles`, `create vehicles`, `edit vehicles`, `delete vehicles`
- `view incidents`, `create incidents`, `edit incidents`, `delete incidents`
- `view patients`, `create patients`, `edit patients`, `delete patients`
- `view transactions`, `create transactions`, `edit transactions`, `delete transactions`
- `view staff`, `create staff`, `edit staff`, `delete staff`
- `view reports`, `view audits`
- `manage settings`, `manage vehicles`

**Roles:**
- **Admin** - Full access (all permissions)
- **Manager** - Most permissions except system settings
- **Dispatcher** - View & manage incidents, vehicles, patients
- **Accountant** - View & manage transactions, reports
- **Medical Staff** - View incidents & patients
- **Driver** - View assigned incidents
- **Maintenance** - View & manage vehicle maintenance
- **Viewer** - Read-only access

### System Settings (58 settings, 7 groups)

1. **Company Info** (10) - Tên, email, địa chỉ, hotline, website, MST
2. **Appearance** (8) - Logo, favicon, colors, font, records per page
3. **Language & Format** (9) - Timezone, date/time format, currency
4. **Business** (11) - Pricing, VAT, free km, shift times, approval rules
5. **Security** (8) - Session timeout, password rules, login attempts
6. **Maintenance** (5) - Auto backup, maintenance mode
7. **System** (7) - Debug mode, log level, upload limits, cache

---

## 🐛 TROUBLESHOOTING

### Lỗi: "Permission denied" (Linux)

```bash
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Lỗi: "No such file or directory" khi chạy npm

```bash
# Xóa node_modules và cài lại
rm -rf node_modules
npm install
```

### Lỗi: "SQLSTATE[HY000] [2002] Connection refused"

- Kiểm tra MySQL đang chạy: `mysql -V`
- Kiểm tra `.env` có đúng cấu hình DB không
- Restart MySQL service

### Lỗi: "Vite manifest not found"

```bash
# Build lại assets
npm run build
```

### Lỗi: "Class 'SystemSetting' not found"

```bash
# Regenerate autoload
composer dump-autoload
php artisan optimize:clear
```

### Lỗi: "Storage link already exists"

**Windows:**
```bash
# Xóa link cũ và tạo lại
rmdir public\storage
php artisan storage:link
```

**Linux:**
```bash
rm public/storage
php artisan storage:link
```

### Lỗi: "419 Page Expired" khi submit form

- Clear cache: `php artisan optimize:clear`
- Kiểm tra `APP_KEY` trong `.env` đã được generate
- Xóa cookies trình duyệt

---

## 📝 CẬP NHẬT HỆ THỐNG

### Pull code mới từ Git

```bash
# Stash local changes (nếu có)
git stash

# Pull latest code
git pull origin main

# Apply stashed changes (nếu cần)
git stash pop

# Update dependencies
composer install
npm install

# Run migrations
php artisan migrate

# Clear cache
php artisan optimize:clear

# Rebuild assets
npm run build
```

---

## 🔒 BẢO MẬT

### Cho Production Server:

1. **Đổi APP_ENV=production trong .env**
```env
APP_ENV=production
APP_DEBUG=false
```

2. **Đổi tất cả mật khẩu mặc định**
```bash
php artisan tinker
>>> $user = User::where('email', 'admin@binhan.com')->first();
>>> $user->password = Hash::make('new-secure-password');
>>> $user->save();
```

3. **Disable debug mode**
4. **Setup HTTPS (SSL Certificate)**
5. **Setup Firewall**
6. **Regular backups**

---

## 📞 HỖ TRỢ

### Tài liệu:
- [Laravel Documentation](https://laravel.com/docs/10.x)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6)
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary/v11)

### File tham khảo trong dự án:
- `start-guide.md` - Hướng dẫn tổng quan
- `SYSTEM-SETTINGS-COMPLETED.md` - Tài liệu System Settings
- `SYSTEM-SETTINGS-PROPOSAL.md` - Đề xuất ban đầu

---

## ✅ CHECKLIST TRIỂN KHAI HOÀN CHỈNH

- [ ] PHP 8.2+ đã cài đặt
- [ ] MySQL 8.0+ đã cài đặt và đang chạy
- [ ] Composer dependencies đã install
- [ ] Node.js packages đã install
- [ ] File `.env` đã cấu hình đúng
- [ ] `APP_KEY` đã generate
- [ ] Database `binhan_db` đã tạo
- [ ] Migrations đã chạy thành công
- [ ] Seeders đã chạy thành công
- [ ] Storage symlink đã tạo
- [ ] Frontend assets đã build
- [ ] Đăng nhập được bằng tài khoản admin
- [ ] Upload logo/favicon thành công
- [ ] Tất cả trang đều load không lỗi
- [ ] Cache đã clear
- [ ] Mật khẩu mặc định đã đổi (production)

---

**🎉 Chúc mừng! Hệ thống đã sẵn sàng sử dụng.**

Nếu gặp vấn đề, vui lòng kiểm tra phần **Troubleshooting** hoặc xem logs tại:
- `storage/logs/laravel.log`
- Apache/Nginx error logs
