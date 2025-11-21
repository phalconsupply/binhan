# QUICK START CHECKLIST - Bắt đầu ngay!

## 🚀 BƯỚC 1: TẠO PROJECT LARAVEL (10 phút)

```powershell
# Di chuyển vào thư mục xampp/htdocs
cd c:\xampp\htdocs\binhan

# Tạo Laravel project mới (nếu chưa có)
composer create-project laravel/laravel . "10.*"

# Hoặc nếu đã có Laravel, kiểm tra version
php artisan --version
```

**Expected output:** `Laravel Framework 10.x.x`

---

## 📦 BƯỚC 2: CÀI ĐẶT PACKAGES (15 phút)

```powershell
# Auth package
composer require laravel/breeze --dev
php artisan breeze:install blade

# RBAC
composer require spatie/laravel-permission

# Export Excel
composer require maatwebsite/excel

# PDF Export
composer require barryvdh/laravel-dompdf

# Audit Logging
composer require spatie/laravel-activitylog

# Install NPM dependencies
npm install
npm run build
```

---

## 🗄️ BƯỚC 3: SETUP DATABASE (5 phút)

### 3.1 Tạo database trong phpMyAdmin
1. Mở http://localhost/phpmyadmin
2. Tạo database mới: `binhan_db`
3. Collation: `utf8mb4_unicode_ci`

### 3.2 Cấu hình .env
```env
APP_NAME="Quản lý Xe Cấp cứu"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/binhan

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=binhan_db
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
CACHE_DRIVER=file
SESSION_DRIVER=file

APP_TIMEZONE=Asia/Ho_Chi_Minh
```

### 3.3 Generate APP_KEY
```powershell
php artisan key:generate
```

---

## 🏗️ BƯỚC 4: TẠO MIGRATIONS (20 phút)

### 4.1 Chạy migration auth (Laravel Breeze)
```powershell
php artisan migrate
```

### 4.2 Tạo migrations cho project

```powershell
# Vehicles table
php artisan make:migration create_vehicles_table

# Patients table
php artisan make:migration create_patients_table

# Incidents table
php artisan make:migration create_incidents_table

# Transactions table
php artisan make:migration create_transactions_table

# Notes table
php artisan make:migration create_notes_table
```

### 4.3 Copy nội dung migration (xem file riêng bên dưới)

---

## 📝 BƯỚC 5: MIGRATION FILES CONTENT

### File: `database/migrations/xxxx_create_vehicles_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('license_plate', 20)->unique();
            $table->string('model', 100)->nullable();
            $table->string('driver_name', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->text('note')->nullable();
            $table->timestamps();
            
            $table->index('license_plate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
```

### File: `database/migrations/xxxx_create_patients_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->integer('birth_year')->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('name');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
```

### File: `database/migrations/xxxx_create_incidents_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('date');
            $table->foreignId('dispatch_by')->constrained('users');
            $table->string('destination')->nullable();
            $table->text('summary')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            
            $table->index('vehicle_id');
            $table->index('patient_id');
            $table->index('date');
            $table->index(['vehicle_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
```

### File: `database/migrations/xxxx_create_transactions_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['thu', 'chi']);
            $table->decimal('amount', 15, 2);
            $table->enum('method', ['cash', 'bank', 'other'])->default('cash');
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->dateTime('date');
            $table->timestamps();
            
            $table->index('incident_id');
            $table->index('vehicle_id');
            $table->index('type');
            $table->index('date');
            $table->index(['vehicle_id', 'type', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
```

### File: `database/migrations/xxxx_create_notes_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('note');
            $table->enum('severity', ['info', 'warning', 'critical'])->default('info');
            $table->timestamps();
            
            $table->index('incident_id');
            $table->index('vehicle_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
```

---

## ▶️ BƯỚC 6: CHẠY MIGRATIONS (2 phút)

```powershell
php artisan migrate
```

**Kiểm tra trong phpMyAdmin:** Phải có các bảng:
- users, password_resets, failed_jobs (Laravel default)
- vehicles, patients, incidents, transactions, notes

---

## 👤 BƯỚC 7: SETUP ROLES & PERMISSIONS (15 phút)

### 7.1 Publish Spatie Permission config
```powershell
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### 7.2 Tạo RoleSeeder
```powershell
php artisan make:seeder RoleSeeder
```

### File: `database/seeders/RoleSeeder.php`
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view vehicles',
            'create vehicles',
            'edit vehicles',
            'delete vehicles',
            
            'view incidents',
            'create incidents',
            'edit incidents',
            'delete incidents',
            
            'view transactions',
            'create transactions',
            'edit transactions',
            'delete transactions',
            
            'view reports',
            'export reports',
            
            'view audits',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin - full access
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // Dispatcher - create/edit incidents & view reports
        $dispatcher = Role::create(['name' => 'dispatcher']);
        $dispatcher->givePermissionTo([
            'view vehicles',
            'view incidents',
            'create incidents',
            'edit incidents',
            'view transactions',
            'create transactions',
            'view reports',
        ]);

        // Accountant - manage transactions & export reports
        $accountant = Role::create(['name' => 'accountant']);
        $accountant->givePermissionTo([
            'view vehicles',
            'view incidents',
            'view transactions',
            'create transactions',
            'edit transactions',
            'view reports',
            'export reports',
        ]);

        // Driver - view only own vehicle
        $driver = Role::create(['name' => 'driver']);
        $driver->givePermissionTo([
            'view vehicles',
            'view incidents',
            'view transactions',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}
```

### 7.3 Chạy RoleSeeder
```powershell
php artisan db:seed --class=RoleSeeder
```

---

## 🧪 BƯỚC 8: TẠO USER TEST (5 phút)

### 8.1 Tạo UserSeeder
```powershell
php artisan make:seeder UserSeeder
```

### File: `database/seeders/UserSeeder.php`
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@binhan.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Dispatcher user
        $dispatcher = User::create([
            'name' => 'Điều phối viên',
            'email' => 'dispatcher@binhan.com',
            'password' => Hash::make('password'),
        ]);
        $dispatcher->assignRole('dispatcher');

        // Accountant user
        $accountant = User::create([
            'name' => 'Kế toán',
            'email' => 'accountant@binhan.com',
            'password' => Hash::make('password'),
        ]);
        $accountant->assignRole('accountant');

        // Driver user
        $driver = User::create([
            'name' => 'Tài xế',
            'email' => 'driver@binhan.com',
            'password' => Hash::make('password'),
        ]);
        $driver->assignRole('driver');

        $this->command->info('Test users created!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@binhan.com / password');
        $this->command->info('Dispatcher: dispatcher@binhan.com / password');
        $this->command->info('Accountant: accountant@binhan.com / password');
        $this->command->info('Driver: driver@binhan.com / password');
    }
}
```

### 8.2 Chạy UserSeeder
```powershell
php artisan db:seed --class=UserSeeder
```

---

## 🎨 BƯỚC 9: SETUP ACTIVITY LOG (5 phút)

```powershell
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-config"
php artisan migrate
```

---

## ✅ BƯỚC 10: KIỂM TRA (5 phút)

### 10.1 Start server
```powershell
php artisan serve
```

### 10.2 Truy cập
- URL: http://localhost:8000
- Hoặc: http://localhost/binhan/public

### 10.3 Test login
- Email: `admin@binhan.com`
- Password: `password`

**Kết quả mong đợi:** Đăng nhập thành công → Dashboard

---

## 📊 KIỂM TRA DATABASE

### Trong phpMyAdmin, kiểm tra:
- [ ] Bảng `vehicles` có cột `license_plate` (unique)
- [ ] Bảng `incidents` có foreign key `vehicle_id`
- [ ] Bảng `transactions` có enum `type` (thu/chi)
- [ ] Bảng `roles` có 4 roles: admin, dispatcher, accountant, driver
- [ ] Bảng `users` có 4 users test
- [ ] Bảng `activity_log` tồn tại (cho audit)

---

## 🎯 TIẾP THEO: BẮT ĐẦU CODE!

### Phase 1 sẽ làm:
1. ✅ **Models & Relationships** (Vehicle, Incident, Transaction, Patient)
2. ✅ **Dashboard với Quick Entry Form**
3. ✅ **AJAX Typeahead** cho biển số xe
4. ✅ **Vehicle CRUD**

### File cần tạo tiếp theo:
```
app/Models/Vehicle.php
app/Models/Incident.php
app/Models/Transaction.php
app/Models/Patient.php
app/Models/Note.php

app/Http/Controllers/DashboardController.php
app/Http/Controllers/VehicleController.php
app/Http/Controllers/API/QuickEntryController.php

resources/views/dashboard.blade.php
resources/views/vehicles/index.blade.php
resources/views/vehicles/create.blade.php
resources/views/vehicles/edit.blade.php
resources/views/vehicles/show.blade.php
```

---

## 🆘 TROUBLESHOOTING

### Lỗi: "Class 'Laravel\Breeze\...' not found"
```powershell
composer dump-autoload
php artisan clear-compiled
```

### Lỗi: "SQLSTATE[HY000] [1045] Access denied"
- Kiểm tra `.env`: `DB_USERNAME`, `DB_PASSWORD`
- Restart MySQL trong XAMPP Control Panel

### Lỗi: "Vite manifest not found"
```powershell
npm install
npm run build
```

### Lỗi: "Permission denied" trên storage/
```powershell
# Windows (run as Admin)
icacls "storage" /grant "Users:(OI)(CI)F" /T
icacls "bootstrap\cache" /grant "Users:(OI)(CI)F" /T
```

---

## 📞 SUPPORT

Nếu gặp vấn đề, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Apache error log: `c:\xampp\apache\logs\error.log`
3. PHP version: `php -v` (cần 8.1+)

**✅ DONE! Bạn đã sẵn sàng bắt đầu code Phase 1!** 🚀
