# Hệ Thống Quản Lý Xe Cấp Cứu

![Laravel](https://img.shields.io/badge/Laravel-10.x-red)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue)
![License](https://img.shields.io/badge/license-MIT-green)

## 📋 Mô tả

Hệ thống quản lý xe cấp cứu, ghi nhận thu/chi, và quản lý thông tin bệnh nhân. Được thiết kế tối ưu cho **cPanel Shared Hosting**.

### Tính năng chính:
- ✅ **Nhập liệu nhanh** (Quick Entry) - < 30 giây/chuyến
- ✅ **Quản lý xe** theo biển số (trung tâm)
- ✅ **Ghi nhận thu/chi** tự động
- ✅ **Báo cáo** theo xe, theo ngày, theo nhân viên
- ✅ **Export Excel/PDF** 
- ✅ **Phân quyền** (Admin, Dispatcher, Accountant, Driver)
- ✅ **Audit Log** (theo dõi mọi thay đổi)
- ✅ **Mobile-first UI**

---

## 🚀 Quick Start

### Yêu cầu:
- PHP 8.2+
- MySQL 5.7+ / MariaDB 10.3+
- Composer
- Node.js 18+ & NPM

### ⚡ Cài đặt nhanh (1 lệnh):

**Windows:**
```bash
deploy.bat
```

**Linux/Mac:**
```bash
chmod +x deploy.sh && ./deploy.sh
```

### 📝 Cài đặt thủ công:

```bash
# 1. Clone repository
git clone https://github.com/phalconsupply/binhan.git
cd binhan

# 2. Copy environment file
cp .env.example .env

# 3. Configure database trong .env
DB_DATABASE=binhan_db
DB_USERNAME=root
DB_PASSWORD=

# 4. Run deployment script (tự động install dependencies, migrate, seed)
# Windows:
deploy.bat

# Linux/Mac:
./deploy.sh

# 5. Start server
php artisan serve
```

**Truy cập:** http://127.0.0.1:8000

### 🔐 Test Accounts:
- **Admin:** admin@binhan.com / password
- **Dispatcher:** dispatcher@binhan.com / password
- **Accountant:** accountant@binhan.com / password
- **Driver:** driver@binhan.com / password

---

## 📚 Documentation

### 📚 Documentation:

- **[DEPLOYMENT-CHECKLIST.md](./DEPLOYMENT-CHECKLIST.md)** - Checklist triển khai đầy đủ
- **[SETUP-NEW-MACHINE.md](./SETUP-NEW-MACHINE.md)** - Hướng dẫn setup máy mới
- **[TROUBLESHOOTING-ANALYSIS.md](./TROUBLESHOOTING-ANALYSIS.md)** - Phân tích lỗi thường gặp
- **[ROOT-CAUSE-SUMMARY.md](./ROOT-CAUSE-SUMMARY.md)** - Tóm tắt nguyên nhân lỗi

---

## 🏗️ Tech Stack

```
Laravel 10.49.1 (PHP 8.2+)
├── Auth: Laravel Breeze (Blade + Tailwind)
├── RBAC: Spatie Permission (8 roles, 28 permissions)
├── Export: Maatwebsite/Excel + DomPDF
├── Audit: Spatie Activity Log
├── UI: Tailwind CSS + Alpine.js
└── Deploy: Compatible with cPanel/VPS/Docker
```

### Database Schema (29 tables):
- `vehicles` - Quản lý xe cấp cứu
- `patients` - Thông tin bệnh nhân
- `incidents` - Chuyến đi/sự cố
- `transactions` - Thu/chi (với categories)
- `staff` - Nhân sự (lái xe, y tá, bác sĩ)
- `vehicle_maintenances` - Bảo trì xe
- `salary_advances` - Tạm ứng lương
- `notes` - Ghi chú
- `activity_log` - Audit trail
- `roles`, `permissions` - RBAC

---

## 🌐 Deployment Options

### Option 1: Development (Local)
```bash
php artisan serve
# Access: http://127.0.0.1:8000
```

### Option 2: VPS/Cloud (Production)

See **[DEPLOYMENT-CHECKLIST.md](./DEPLOYMENT-CHECKLIST.md)** for detailed steps.

Quick summary:
```bash
# 1. Clone & configure
git clone https://github.com/phalconsupply/binhan.git
cd binhan
cp .env.example .env
# Edit .env với thông tin database

# 2. Run deployment
./deploy.sh  # Linux/Mac
deploy.bat   # Windows

# 3. Set permissions (Linux only)
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 4. Configure Nginx/Apache (see DEPLOYMENT-CHECKLIST.md)
```

### Option 3: cPanel Shared Hosting

```bash
# Nén project (loại bỏ node_modules, vendor)
zip -r binhan.zip . -x "node_modules/*" "vendor/*" ".git/*"

# Upload lên cPanel qua File Manager
# Extract vào /home/username/binhan
```

### Bước 2: Cấu hình
```bash
# SSH vào server
cd ~/binhan

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Setup .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Bước 3: Setup Cron Job
```bash
* * * * * cd /home/username/binhan && php artisan schedule:run >> /dev/null 2>&1
```

### Bước 4: Point domain đến `/public`

Chi tiết đầy đủ xem trong **DEPLOYMENT-PLAN.md**

---

## 🔒 Security

- ✅ CSRF Protection (Laravel default)
- ✅ SQL Injection Prevention (Eloquent ORM)
- ✅ XSS Protection (Blade escaping)
- ✅ HTTPS Only (Force SSL)
- ✅ Role-based Access Control
- ✅ Audit Logging

---

## 🧪 Testing

```bash
# Run tests
php artisan test

# Run specific test
php artisan test --filter=VehicleTest
```

---

## 📊 Roadmap

### Phase 1 (Week 1) ✅
- [x] Quick Entry Form
- [x] Vehicle CRUD
- [x] Typeahead search

### Phase 2 (Week 2) 🚧
- [ ] History & Filters
- [ ] Reports (Daily, Vehicle, Cash Flow)
- [ ] Export Excel/PDF

### Phase 3 (Week 3) 📅
- [ ] RBAC Implementation
- [ ] Audit Logging
- [ ] Mobile Optimization

### Phase 4 (Week 4) 📅
- [ ] Deploy to cPanel
- [ ] User Training
- [ ] Go-live

---

## 🤝 Contributing

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📝 License

MIT License - xem file [LICENSE](./LICENSE) để biết thêm chi tiết.

---

## 👥 Team

- **Developer:** [Your Name]
- **Project Manager:** [PM Name]
- **Client:** Phalcon Supply

---

## 📞 Support

- **Issues:** https://github.com/phalconsupply/binhan/issues
- **Email:** support@phalconsupply.com

---

## 🙏 Acknowledgments

- Laravel Framework
- Spatie Packages
- Tailwind CSS
- Alpine.js

---

**Built with ❤️ for efficient ambulance fleet management**
