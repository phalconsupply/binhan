# PHƯƠNG ÁN TRIỂN KHAI - HỆ THỐNG QUẢN LÝ XE CẤP CỨU

## 📋 MỤC TIÊU & PHẠM VI

**Mục tiêu:** Xây dựng hệ thống quản lý xe cấp cứu, ghi nhận thu/chi, bệnh nhân  
**Môi trường production:** cPanel Shared Hosting  
**Timeline:** 3-4 tuần (MVP)  
**Team:** 1-2 developers

---

## 🚨 ĐIỀU CHỈNH QUAN TRỌNG SO VỚI START-GUIDE

### ❌ LOẠI BỎ (không tương thích shared hosting):
- ~~Livewire realtime~~ → Giữ Livewire cơ bản HOẶC dùng AJAX thuần
- ~~Redis + Horizon~~ → Database Queue + Cron Job
- ~~Queue Workers~~ → Sync processing cho file nhỏ
- ~~Docker~~ → Deploy trực tiếp qua cPanel

### ✅ SỬ DỤNG:
- **Laravel 10.x** (PHP 8.1+)
- **MySQL/MariaDB** (có sẵn trên shared hosting)
- **Alpine.js** cho interactivity nhẹ
- **Vanilla AJAX/Fetch API** hoặc Axios
- **Maatwebsite/Excel** (export đơn giản)
- **DomPDF** (không cần wkhtmltopdf)
- **Laravel Breeze** (auth đơn giản)
- **Spatie Permission** (RBAC)

---

## 🏗️ KIẾN TRÚC HỆ THỐNG (CPANEL VERSION)

```
┌─────────────────────────────────────────┐
│          BROWSER (Mobile-first)         │
└─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────┐
│    Laravel App (PHP-FPM via cPanel)     │
│  ┌─────────────────────────────────┐   │
│  │  Routes (web.php)               │   │
│  │  Controllers (Resource/API)     │   │
│  │  Blade Templates + Alpine.js    │   │
│  │  AJAX Endpoints                 │   │
│  └─────────────────────────────────┘   │
└─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────┐
│          MySQL Database                 │
│  vehicles | incidents | transactions    │
│  patients | users | notes | audits      │
└─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────┐
│       Storage (local filesystem)        │
│  exports/ | uploads/ | logs/            │
└─────────────────────────────────────────┘
```

---

## 📁 CẤU TRÚC PROJECT

```
binhan/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php
│   │   │   ├── VehicleController.php
│   │   │   ├── IncidentController.php
│   │   │   ├── TransactionController.php
│   │   │   ├── PatientController.php
│   │   │   ├── ReportController.php
│   │   │   └── API/
│   │   │       └── QuickEntryController.php (AJAX endpoints)
│   │   └── Middleware/
│   │       └── CheckRole.php
│   ├── Models/
│   │   ├── Vehicle.php
│   │   ├── Incident.php
│   │   ├── Transaction.php
│   │   ├── Patient.php
│   │   └── Note.php
│   ├── Services/
│   │   ├── ExportService.php
│   │   └── ReportService.php
│   └── Traits/
│       └── Auditable.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_create_vehicles_table.php
│   │   ├── 2024_01_02_create_patients_table.php
│   │   ├── 2024_01_03_create_incidents_table.php
│   │   ├── 2024_01_04_create_transactions_table.php
│   │   ├── 2024_01_05_create_notes_table.php
│   │   └── 2024_01_06_create_audits_table.php
│   ├── seeders/
│   │   ├── RoleSeeder.php
│   │   └── DemoDataSeeder.php
│   └── factories/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php (Alpine.js, Tailwind)
│   │   ├── dashboard.blade.php (Quick Entry Form)
│   │   ├── vehicles/
│   │   ├── incidents/
│   │   ├── transactions/
│   │   └── reports/
│   └── js/
│       ├── app.js
│       └── components/
│           ├── typeahead.js
│           └── quick-entry.js
├── public/
│   ├── .htaccess (URL rewrite cho cPanel)
│   └── index.php
├── routes/
│   ├── web.php
│   └── api.php
├── storage/
│   ├── app/
│   │   ├── exports/
│   │   └── uploads/
│   └── logs/
├── .env.example
├── composer.json
└── package.json
```

---

## 🗄️ DATABASE SCHEMA (Hoàn chỉnh)

### 1. vehicles
```sql
id                  BIGINT UNSIGNED PRIMARY KEY
license_plate       VARCHAR(20) UNIQUE NOT NULL (index)
model               VARCHAR(100) NULL
driver_name         VARCHAR(100) NULL
phone               VARCHAR(20) NULL
status              ENUM('active','inactive','maintenance') DEFAULT 'active'
note                TEXT NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

### 2. patients
```sql
id                  BIGINT UNSIGNED PRIMARY KEY
name                VARCHAR(100) NOT NULL (index)
birth_year          INT NULL
phone               VARCHAR(20) NULL (index)
gender              ENUM('male','female','other') NULL
address             TEXT NULL
notes               TEXT NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

### 3. incidents
```sql
id                  BIGINT UNSIGNED PRIMARY KEY
vehicle_id          BIGINT UNSIGNED (FK → vehicles) (index)
patient_id          BIGINT UNSIGNED NULL (FK → patients) (index)
date                DATETIME NOT NULL (index)
dispatch_by         BIGINT UNSIGNED (FK → users)
destination         VARCHAR(255) NULL
summary             TEXT NULL
tags                JSON NULL (ví dụ: ["HS Lâm sàng", "HSTC"])
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_vehicle_date (vehicle_id, date)
INDEX idx_date (date)
```

### 4. transactions
```sql
id                  BIGINT UNSIGNED PRIMARY KEY
incident_id         BIGINT UNSIGNED NULL (FK → incidents) (index)
vehicle_id          BIGINT UNSIGNED (FK → vehicles) (index)
type                ENUM('thu','chi') NOT NULL
amount              DECIMAL(15,2) NOT NULL
method              ENUM('cash','bank','other') DEFAULT 'cash'
note                TEXT NULL
recorded_by         BIGINT UNSIGNED (FK → users)
date                DATETIME NOT NULL (index)
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_vehicle_type_date (vehicle_id, type, date)
INDEX idx_type_date (type, date)
```

### 5. notes
```sql
id                  BIGINT UNSIGNED PRIMARY KEY
incident_id         BIGINT UNSIGNED NULL (FK → incidents)
vehicle_id          BIGINT UNSIGNED NULL (FK → vehicles)
user_id             BIGINT UNSIGNED (FK → users)
note                TEXT NOT NULL
severity            ENUM('info','warning','critical') DEFAULT 'info'
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

### 6. audits (dùng spatie/laravel-activitylog)
```sql
id                  BIGINT UNSIGNED PRIMARY KEY
log_name            VARCHAR(255) NULL
description         TEXT NOT NULL
subject_type        VARCHAR(255) NULL (polymorphic)
subject_id          BIGINT UNSIGNED NULL
causer_type         VARCHAR(255) NULL
causer_id           BIGINT UNSIGNED NULL
properties          JSON NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_subject (subject_type, subject_id)
INDEX idx_causer (causer_type, causer_id)
```

---

## 🎯 ROADMAP TRIỂN KHAI (4 GIAI ĐOẠN)

### 📦 **GIAI ĐOẠN 0: SETUP & CƠ SỞ HẠ TẦNG** (2-3 ngày)

#### Mục tiêu:
- Cài đặt Laravel local + cấu hình cPanel tương tự
- Thiết lập database, auth, RBAC

#### Checklist:
- [ ] Cài Laravel 10.x: `composer create-project laravel/laravel binhan`
- [ ] Cấu hình `.env` (MySQL, APP_URL, timezone)
- [ ] Cài packages:
  ```bash
  composer require laravel/breeze --dev
  php artisan breeze:install blade
  composer require spatie/laravel-permission
  composer require maatwebsite/excel
  composer require barryvdh/laravel-dompdf
  composer require spatie/laravel-activitylog
  ```
- [ ] Setup Tailwind CSS + Alpine.js (đã có với Breeze)
- [ ] Tạo database migrations (6 bảng)
- [ ] Chạy migrations: `php artisan migrate`
- [ ] Tạo RoleSeeder (4 roles: admin, dispatcher, accountant, driver)
- [ ] Chạy seeders: `php artisan db:seed --class=RoleSeeder`
- [ ] Test auth: đăng ký/đăng nhập user
- [ ] Assign role cho user test

#### Output:
- Laravel app chạy local (http://localhost/binhan/public)
- Database có 6 bảng + auth tables
- User test với role admin

---

### 🚀 **GIAI ĐOẠN 1: QUICK ENTRY FORM + VEHICLE MODULE** (4-5 ngày)

#### Mục tiêu:
- Xây dựng form nhập liệu nhanh (trung tâm)
- CRUD vehicles
- AJAX typeahead cho biển số xe

#### Features:

##### 1.1 Dashboard với Quick Entry Form
**File:** `resources/views/dashboard.blade.php`

**Layout:**
```html
┌──────────────────────────────────────────┐
│  [Logo] Hệ thống Quản lý Xe Cấp cứu     │
│  [Đăng xuất] [Username - Role]           │
├──────────────────────────────────────────┤
│  📝 NHẬP LIỆU NHANH                      │
│  ┌────────────────────────────────────┐ │
│  │ Biển số xe: [_______] (typeahead) │ │
│  │ Ngày giờ: [2024-01-15 14:30]      │ │
│  │ Bệnh nhân: [_______] (typeahead)  │ │
│  │ Điểm đến: [_______]               │ │
│  │ Khoản thu: [_______] VNĐ          │ │
│  │ Khoản chi: [_______] VNĐ          │ │
│  │ Phương thức: [⚪cash ⚪bank]      │ │
│  │ Ghi chú: [________________]       │ │
│  │ Tags: [☑HS Lâm sàng ☐HSTC]       │ │
│  │                                    │ │
│  │ [Lưu & Tiếp tục] [Chỉ lưu]       │ │
│  └────────────────────────────────────┘ │
│                                          │
│  📊 TỔNG QUAN HÔM NAY                   │
│  Số chuyến: 5 | Thu: 2,500,000 VNĐ     │
│  Chi: 800,000 VNĐ | Tồn: 1,700,000 VNĐ │
└──────────────────────────────────────────┘
```

**AJAX Endpoint:**
- POST `/api/quick-entry` (lưu incident + transaction)
- GET `/api/vehicles/search?q={query}` (typeahead)
- GET `/api/patients/search?q={query}`

**Validation rules:**
- `license_plate`: required, max:20
- `date`: required, date
- `amount_thu/chi`: numeric, min:0
- `patient_name`: nullable, max:100

##### 1.2 Vehicle Management
**Pages:**
- `/vehicles` - Danh sách xe (DataTable)
- `/vehicles/create` - Thêm xe mới
- `/vehicles/{id}/edit` - Sửa xe
- `/vehicles/{id}` - Chi tiết xe + lịch sử chuyến

**Controller:** `VehicleController` (Resource)

**Chức năng đặc biệt:**
- Tính tổng thu/chi theo xe
- Hiển thị 10 chuyến gần nhất
- Export lịch sử theo xe (Excel)

#### Checklist:
- [ ] Tạo Models + Relationships (Vehicle, Incident, Transaction, Patient)
- [ ] Tạo Controllers (VehicleController, QuickEntryController)
- [ ] Tạo Blade views (dashboard, vehicles/*)
- [ ] Implement typeahead JS (Alpine.js component)
- [ ] API routes cho AJAX
- [ ] Validation + error handling
- [ ] Test nhập liệu: tạo 5 chuyến mẫu
- [ ] Mobile responsive check

#### Output:
- Form nhập liệu nhanh hoạt động
- Quản lý xe đầy đủ
- Typeahead biển số xe smooth

---

### 📊 **GIAI ĐOẠN 2: LỊCH SỬ, REPORTS & EXPORT** (4-5 ngày)

#### Mục tiêu:
- Xem lịch sử chi tiết (incidents, transactions)
- Báo cáo thu/chi theo xe, theo ngày, theo nhân viên
- Export Excel/PDF

#### Features:

##### 2.1 Incident History
**Route:** `/incidents`

**Filters:**
- Biển số xe (dropdown)
- Khoảng ngày (date range picker)
- Nhân viên dispatch (dropdown)
- Bệnh nhân (search)

**Table columns:**
```
| Ngày giờ | Biển số | Bệnh nhân | Điểm đến | Thu | Chi | Người ghi | Actions |
```

**Actions:** View detail, Edit (trong 24h), Delete (admin only)

##### 2.2 Transaction Management
**Route:** `/transactions`

**Filters:**
- Loại (thu/chi)
- Phương thức (cash/bank)
- Biển số xe
- Khoảng ngày

**Tính năng:**
- Inline edit amount/note (AJAX)
- Xác nhận delete với modal
- Audit log mỗi thay đổi

##### 2.3 Reports Module
**Routes:**
- `/reports/daily` - Báo cáo ngày
- `/reports/vehicle-summary` - Tổng hợp theo xe
- `/reports/cash-flow` - Dòng tiền (cash flow)

**Báo cáo hàng ngày:**
```
┌─────────────────────────────────────────┐
│ BÁO CÁO NGÀY: 15/01/2024               │
├─────────────────────────────────────────┤
│ Tổng chuyến: 12                         │
│ Tổng thu: 5,200,000 VNĐ                │
│ Tổng chi: 1,800,000 VNĐ                │
│ Tồn quỹ: +3,400,000 VNĐ                │
│                                         │
│ Chi tiết theo xe:                       │
│ ┌──────────┬───────┬─────────┬────────┐│
│ │ Biển số  │ Chuyến│ Thu     │ Chi    ││
│ ├──────────┼───────┼─────────┼────────┤│
│ │ 51A-12345│   3   │ 1,500K  │  500K  ││
│ │ 51B-67890│   5   │ 2,000K  │  800K  ││
│ └──────────┴───────┴─────────┴────────┘│
│                                         │
│ [Export Excel] [Export PDF] [In]       │
└─────────────────────────────────────────┘
```

##### 2.4 Export Service
**Class:** `App\Services\ExportService`

**Methods:**
- `exportIncidents($filters)` → Excel
- `exportDailyReport($date)` → PDF
- `exportVehicleHistory($vehicleId)` → Excel giống mẫu cũ

**Giới hạn:** Max 1000 rows mỗi lần (tránh timeout shared hosting)

#### Checklist:
- [ ] Tạo IncidentController với filters
- [ ] Tạo TransactionController với inline edit
- [ ] Tạo ReportController (3 loại báo cáo)
- [ ] Implement ExportService (Maatwebsite + DomPDF)
- [ ] Tạo Blade views cho reports
- [ ] Tạo PDF template (Blade → PDF)
- [ ] Test export với data lớn (500 rows)
- [ ] Optimize queries (eager loading, indexes)

#### Output:
- Xem lịch sử incidents/transactions với filters
- 3 loại báo cáo đầy đủ
- Export Excel/PDF hoạt động

---

### 🔒 **GIAI ĐOẠN 3: RBAC, AUDIT & POLISH** (3-4 ngày)

#### Mục tiêu:
- Phân quyền theo role
- Audit log mọi thay đổi
- Optimize UI/UX mobile
- Import Excel (optional)

#### Features:

##### 3.1 Role-Based Access Control (RBAC)

**Roles & Permissions:**

| Role        | Permissions                                                      |
|-------------|------------------------------------------------------------------|
| **Admin**   | Full access (tất cả CRUD + delete + settings)                   |
| **Dispatcher** | Create/edit incidents (trong 24h), view reports             |
| **Accountant** | Edit transactions, view/export reports, cannot delete      |
| **Driver**  | View own vehicle history only (read-only)                       |

**Implementation:**
- Middleware: `CheckRole::class`
- Blade directives: `@role('admin')`, `@can('edit-incident')`
- Route protection:
  ```php
  Route::middleware(['auth', 'role:admin'])->group(function() {
      Route::delete('/vehicles/{id}', ...);
  });
  ```

##### 3.2 Audit Log (spatie/laravel-activitylog)

**Tracking:**
- Vehicles: created, updated, deleted
- Incidents: created, updated, deleted
- Transactions: created, updated, deleted (lưu old/new values)

**View Audit:**
- Route: `/audits` (admin only)
- Columns: `User | Action | Model | Old Value | New Value | Date`

**Example log:**
```
User: Nguyễn Văn A (dispatcher)
Action: Updated Transaction #123
Old: amount = 500,000
New: amount = 600,000
Date: 2024-01-15 14:30:25
```

##### 3.3 Mobile Optimization

**Checklist:**
- [ ] Responsive form (input size 16px+, button 44px+)
- [ ] Touch-friendly typeahead dropdown
- [ ] Sticky header trên mobile
- [ ] Bottom navigation (optional)
- [ ] Test trên iPhone SE, Android nhỏ

##### 3.4 Import Excel (Optional)

**Route:** `/import/excel`

**Process:**
1. Upload file Excel (mẫu cũ)
2. Map columns: Biển số → license_plate, Bệnh nhân → patient_name...
3. Preview 5 rows đầu
4. Confirm import
5. Background job (database queue) xử lý từng row
6. Hiển thị kết quả: 50 success, 2 failed (với lỗi)

**Validation:**
- Check duplicate license_plate
- Validate date format
- Skip empty rows

#### Checklist:
- [ ] Setup permissions (create, edit, delete cho từng model)
- [ ] Tạo RoleSeeder với permissions
- [ ] Apply middleware cho routes
- [ ] Tích hợp spatie/activitylog (config + trait)
- [ ] Tạo trang /audits
- [ ] Mobile testing (Chrome DevTools)
- [ ] Import Excel (nếu cần)
- [ ] Write user documentation (doc/USER-GUIDE.md)

#### Output:
- Phân quyền hoạt động đúng
- Audit log đầy đủ
- UI mobile-friendly
- Import Excel (nếu làm)

---

## 🚀 GIAI ĐOẠN 4: DEPLOYMENT LÊN CPANEL

### 4.1 Chuẩn bị môi trường cPanel

**Yêu cầu hosting:**
- PHP 8.1+ (với extensions: mbstring, xml, pdo_mysql, zip, gd)
- MySQL 5.7+ hoặc MariaDB 10.3+
- SSL certificate (Let's Encrypt free)
- Cron Jobs (cho schedule:run)
- File Manager hoặc FTP access

### 4.2 Deploy Laravel lên cPanel

#### Bước 1: Nén project local
```bash
# Loại bỏ files không cần
rm -rf node_modules vendor storage/logs/* .git

# Tạo file zip
zip -r binhan.zip . -x "node_modules/*" "vendor/*" ".git/*"
```

#### Bước 2: Upload lên cPanel

**Cấu trúc thư mục trên cPanel:**
```
/home/username/
├── public_html/               # Document root
│   ├── index.php              # Symlink đến ../binhan/public/index.php
│   ├── .htaccess
│   ├── css/
│   ├── js/
│   └── ...                    # Các file trong public/
└── binhan/                    # Laravel app (ngoài public_html)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── .env
    ├── artisan
    └── composer.json
```

**Upload qua cPanel File Manager:**
1. Upload `binhan.zip` vào `/home/username/`
2. Extract zip
3. Move files từ `public/` sang `public_html/`

#### Bước 3: Cấu hình .env
```env
APP_NAME="Quản lý Xe Cấp cứu"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_binhan
DB_USERNAME=username_binhan_user
DB_PASSWORD=your_secure_password

QUEUE_CONNECTION=database
CACHE_DRIVER=file
SESSION_DRIVER=file

# Timezone
APP_TIMEZONE=Asia/Ho_Chi_Minh
```

#### Bước 4: Chạy commands qua SSH hoặc cPanel Terminal
```bash
cd ~/binhan

# Install dependencies
composer install --no-dev --optimize-autoloader

# Tạo APP_KEY
php artisan key:generate

# Chạy migrations
php artisan migrate --force

# Seed roles
php artisan db:seed --class=RoleSeeder --force

# Clear cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Link storage
php artisan storage:link

# Set permissions
chmod -R 755 storage bootstrap/cache
```

#### Bước 5: Cấu hình .htaccess (public_html)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Redirect đến /public nếu chưa chuyển file
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ /public/$1 [L]

    # Hoặc nếu đã move file:
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [L]
</IfModule>
```

#### Bước 6: Setup Cron Job (cho queue & schedule)
**cPanel → Cron Jobs → Add:**
```bash
# Chạy mỗi phút (Laravel scheduler)
* * * * * cd /home/username/binhan && php artisan schedule:run >> /dev/null 2>&1

# Chạy queue worker (nếu dùng database queue)
* * * * * cd /home/username/binhan && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

#### Bước 7: SSL Certificate
**cPanel → SSL/TLS Status:**
- Enable AutoSSL (Let's Encrypt)
- Hoặc upload certificate riêng

**Force HTTPS trong .htaccess:**
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 4.3 Testing sau deploy

**Checklist:**
- [ ] Truy cập domain → trang login hiện
- [ ] Đăng nhập → dashboard hoạt động
- [ ] Nhập liệu nhanh → lưu được vào DB
- [ ] Typeahead hoạt động (AJAX)
- [ ] Export Excel → download được
- [ ] Export PDF → render đúng
- [ ] Mobile responsive → test trên điện thoại
- [ ] Audit log ghi nhận thay đổi
- [ ] Permissions hoạt động đúng

### 4.4 Backup Strategy

**Tự động (cPanel):**
- Enable cPanel Backup (hàng tuần/hàng tháng)

**Manual:**
```bash
# Database backup (chạy cron hàng ngày)
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# Upload backup lên cloud (Google Drive, Dropbox...)
# Hoặc download về local qua FTP
```

**Laravel backup (optional):**
```bash
# Cài spatie/laravel-backup
composer require spatie/laravel-backup

# Setup trong config/backup.php
# Chạy cron: php artisan backup:run
```

---

## 🛠️ CÔNG CỤ & PACKAGE CẦN DÙNG

### Backend
```json
{
  "require": {
    "php": "^8.1",
    "laravel/framework": "^10.0",
    "laravel/breeze": "^1.26",
    "spatie/laravel-permission": "^6.0",
    "maatwebsite/excel": "^3.1",
    "barryvdh/laravel-dompdf": "^2.0",
    "spatie/laravel-activitylog": "^4.7"
  }
}
```

### Frontend
```json
{
  "devDependencies": {
    "alpinejs": "^3.13",
    "tailwindcss": "^3.3",
    "@tailwindcss/forms": "^0.5",
    "axios": "^1.6"
  }
}
```

### Optional (cho import/export nâng cao)
- `phpoffice/phpspreadsheet` (included với maatwebsite/excel)
- `intervention/image` (nếu upload ảnh)

---

## 📝 CHECKLIST TỔNG HỢP

### Phase 0: Setup ✅
- [ ] Laravel 10 installed
- [ ] Database configured
- [ ] Auth (Breeze) working
- [ ] Roles seeded (4 roles)
- [ ] Migrations run (6 tables)

### Phase 1: Core Features ✅
- [ ] Quick Entry Form hoạt động
- [ ] Vehicle CRUD
- [ ] Typeahead biển số xe/bệnh nhân
- [ ] Lưu incident + transaction
- [ ] Dashboard stats

### Phase 2: History & Reports ✅
- [ ] Incident history với filters
- [ ] Transaction management
- [ ] 3 loại reports (daily, vehicle, cash-flow)
- [ ] Export Excel/PDF

### Phase 3: Polish ✅
- [ ] RBAC implemented
- [ ] Audit log tracking
- [ ] Mobile optimized
- [ ] Import Excel (optional)
- [ ] User guide written

### Phase 4: Deployment ✅
- [ ] Uploaded to cPanel
- [ ] .env configured
- [ ] Migrations run on production
- [ ] Cron jobs setup
- [ ] SSL enabled
- [ ] Backup configured
- [ ] Testing passed

---

## 🚨 LƯU Ý QUAN TRỌNG

### 1. Shared Hosting Limitations

**Timeout:**
- PHP max_execution_time thường là 30-60s
- Export > 1000 rows có thể timeout
→ **Giải pháp:** Giới hạn export, hoặc chunk data

**Memory:**
- memory_limit thường 128MB-512MB
→ **Giải pháp:** Optimize queries, không load hết data 1 lúc

**No background workers:**
- Không chạy `php artisan queue:work` daemon
→ **Giải pháp:** Dùng cron job chạy `queue:work --stop-when-empty`

### 2. Performance Optimization

**Database:**
- Đánh index đúng (license_plate, date, vehicle_id)
- Eager loading relationships (`with()`)
- Paginate kết quả (20-50 rows/page)

**Caching:**
```php
// Cache reports 5 phút
Cache::remember('daily_report_'.date('Y-m-d'), 300, function() {
    return Report::generateDaily();
});
```

**Query Optimization:**
```php
// ❌ Tránh N+1 queries
$incidents = Incident::all();
foreach($incidents as $inc) {
    echo $inc->vehicle->license_plate; // N queries!
}

// ✅ Eager loading
$incidents = Incident::with('vehicle')->get();
```

### 3. Security Best Practices

- [ ] `.env` không commit vào Git
- [ ] `APP_DEBUG=false` trên production
- [ ] Validate tất cả input
- [ ] CSRF protection (Laravel default)
- [ ] SQL Injection prevention (Eloquent default)
- [ ] XSS protection (`{{ }}` Blade escaping)
- [ ] HTTPS only
- [ ] Rate limiting cho API (`throttle:60,1`)

### 4. User Training

**Tài liệu cần chuẩn bị:**
- [ ] Hướng dẫn nhập liệu nhanh (video 2-3 phút)
- [ ] Hướng dẫn xem báo cáo
- [ ] FAQ: Làm gì khi quên mật khẩu, sửa sai nhập liệu...
- [ ] Hotline/support email

---

## 📊 TIẾN ĐỘ THEO TUẦN

### Tuần 1
- ✅ Setup project (2 ngày)
- ✅ Quick Entry + Vehicle Module (5 ngày)

### Tuần 2
- ✅ History & Reports (4 ngày)
- ✅ Export Excel/PDF (1 ngày)
- ✅ Testing (2 ngày)

### Tuần 3
- ✅ RBAC + Audit (3 ngày)
- ✅ Mobile polish (2 ngày)
- ✅ Deploy to cPanel (2 ngày)

### Tuần 4 (Buffer)
- ✅ Bug fixing
- ✅ User training
- ✅ Documentation
- ✅ Go-live

---

## 🎉 TIÊU CHÍ THÀNH CÔNG

### Functional:
1. ✅ Nhập liệu nhanh < 30 giây/chuyến
2. ✅ Typeahead biển số xe phản hồi < 500ms
3. ✅ Export báo cáo < 5 giây (100 rows)
4. ✅ Mobile hoạt động mượt (iPhone 12+, Android 10+)
5. ✅ Phân quyền chính xác (dispatcher không xóa được)

### Non-functional:
1. ✅ Uptime > 99% (shared hosting)
2. ✅ Page load < 2s (mobile 4G)
3. ✅ Database backup hàng ngày
4. ✅ Zero data loss

---

## 📞 HỖ TRỢ & MAINTENANCE

### Post-launch:
- **Tuần 1-2:** Daily check logs, fix urgent bugs
- **Tháng 1:** Gather user feedback, minor improvements
- **Tháng 2+:** Monthly updates, feature requests

### Monitoring:
- cPanel Error Logs (`storage/logs/laravel.log`)
- MySQL slow query log
- Google Analytics (optional)

### Contact:
- Dev Team: [email]
- Hosting Support: [cPanel provider]

---

## ✅ KẾT LUẬN

Phương án này:
- ✅ **Tương thích 100% với cPanel shared hosting**
- ✅ **Loại bỏ các công nghệ không khả thi** (Redis, Horizon, Docker)
- ✅ **Giữ core features** (quick entry, reports, export)
- ✅ **Đơn giản, dễ maintain** (vanilla Laravel + Alpine.js)
- ✅ **Timeline thực tế** (3-4 tuần MVP)
- ✅ **Scalable** (dễ nâng cấp lên VPS sau này)

**READY TO START!** 🚀
