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
- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Composer
- Node.js & NPM

### Cài đặt (Development):

```bash
# 1. Clone repository
git clone https://github.com/phalconsupply/binhan.git
cd binhan

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database trong .env
DB_DATABASE=binhan_db
DB_USERNAME=root
DB_PASSWORD=

# 5. Run migrations & seeders
php artisan migrate
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder

# 6. Build assets
npm run build

# 7. Start server
php artisan serve
```

### Login credentials (test users):
- **Admin:** admin@binhan.com / password
- **Dispatcher:** dispatcher@binhan.com / password
- **Accountant:** accountant@binhan.com / password
- **Driver:** driver@binhan.com / password

---

## 📚 Documentation

Đọc chi tiết trong các file sau:

- **[DEPLOYMENT-PLAN.md](./DEPLOYMENT-PLAN.md)** - Phương án triển khai đầy đủ (4 giai đoạn)
- **[QUICK-START.md](./QUICK-START.md)** - Hướng dẫn setup nhanh từ đầu
- **[start-guide](./start-guide)** - Tài liệu kỹ thuật gốc

---

## 🏗️ Kiến trúc

```
Laravel 10
├── Auth: Laravel Breeze
├── RBAC: Spatie Permission
├── Export: Maatwebsite/Excel + DomPDF
├── Audit: Spatie Activity Log
├── UI: Tailwind CSS + Alpine.js
└── Deploy: cPanel Shared Hosting
```

### Database Schema:
- `vehicles` - Thông tin xe
- `patients` - Thông tin bệnh nhân
- `incidents` - Các chuyến xe/sự cố
- `transactions` - Thu/chi
- `notes` - Ghi chú phát sinh
- `activity_log` - Audit trail

---

## 📦 Deployment (cPanel)

### Bước 1: Upload code
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
