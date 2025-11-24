# System Settings Feature - Implementation Complete ✅

## Overview
Đã hoàn thành tính năng **Cấu hình Hệ thống** - cho phép quản trị viên thay đổi các cấu hình của hệ thống qua giao diện web mà không cần chỉnh sửa code hay file config.

## 📋 Thống Kê

- **Migration**: 1 table (`system_settings`)
- **Model**: SystemSetting với cache & helper methods
- **Seeder**: 57 cấu hình mặc định
- **Controller**: 5 methods (index, update, uploadFile, deleteFile, getValue)
- **View**: 1 trang với 7 tabs
- **Routes**: 5 routes được bảo vệ bởi permission `manage settings`
- **Helper**: 3 global functions (setting, setting_set, settings_clear_cache)

## 🎯 7 Nhóm Cấu Hình

### 1. Thông tin Công ty (10 settings)
- Tên công ty, tên viết tắt, slogan
- Email, hotline, địa chỉ
- Mã số thuế, website
- Mô tả công ty

### 2. Giao diện (7 settings)
- Logo chính, favicon
- Màu chủ đạo & màu phụ
- Hình nền trang login
- Font chữ (Inter, Roboto, Open Sans, etc.)
- Số bản ghi mỗi trang (10/15/25/50/100)

### 3. Ngôn ngữ & Định dạng (9 settings)
- Ngôn ngữ mặc định (Tiếng Việt/English)
- Múi giờ (Asia/Ho_Chi_Minh)
- Định dạng ngày/giờ
- Đơn vị tiền tệ (VND, ₫)
- Vị trí ký hiệu tiền
- Số chữ số thập phân
- Phân cách hàng nghìn

### 4. Nghiệp vụ (11 settings)
- Số km miễn phí: 10 km
- Giá mỗi km thêm: 15,000₫
- Thuế VAT: 10%
- Phí dịch vụ: 0%
- Giá tiền chờ: 50,000₫/giờ
- Thời gian chờ miễn phí: 30 phút
- Tự động tính tiền (checkbox)
- Yêu cầu phê duyệt (checkbox)
- Cho phép chỉnh sửa sau khi lưu
- Giờ bắt đầu/kết thúc ca (07:00 - 19:00)

### 5. Bảo mật (8 settings)
- Session timeout: 120 phút
- Số lần đăng nhập sai tối đa: 5
- Thời gian khóa tài khoản: 15 phút
- Độ dài mật khẩu tối thiểu: 8 ký tự
- Yêu cầu ký tự đặc biệt (checkbox)
- Yêu cầu số trong mật khẩu (checkbox)
- Yêu cầu chữ hoa (checkbox)
- Cho phép ghi nhớ đăng nhập (checkbox)

### 6. Backup & Bảo trì (5 settings)
- Tự động backup (checkbox)
- Tần suất backup (daily/weekly/monthly)
- Giữ backup: 30 ngày
- Chế độ bảo trì (checkbox)
- Thông báo bảo trì (textarea)

### 7. Hệ thống (7 settings)
- Debug mode (checkbox)
- Log level (debug/info/warning/error)
- Kích thước file upload tối đa: 10 MB
- Loại file cho phép upload
- Bật cache hệ thống (checkbox)
- Thời gian cache: 60 phút
- Số ngày giữ log: 90 ngày

## 🎨 Loại Input Được Hỗ Trợ

1. **text** - Văn bản ngắn
2. **email** - Email với validation
3. **url** - URL với validation
4. **number** - Số với validation
5. **textarea** - Văn bản dài
6. **checkbox** - Bật/tắt
7. **select** - Dropdown với options
8. **color** - Color picker với hex preview
9. **image** - Upload ảnh với preview
10. **file** - Upload file bất kỳ
11. **time** - Chọn giờ

## 🔧 Cách Sử Dụng

### 1. Truy Cập Trang Cấu Hình
- Menu: **⚙️ Cài đặt** → **⚙️ Cấu hình hệ thống**
- URL: `/settings`
- Permission: `manage settings` (Admin, Manager)

### 2. Sử Dụng Helper Function

```php
// Lấy giá trị cấu hình
$companyName = setting('company_name', 'Default Name');
$vatRate = setting('vat_rate', 10);

// Đặt giá trị cấu hình
setting_set('company_name', 'Binhan Ambulance');

// Xóa cache
settings_clear_cache();
```

### 3. Sử Dụng Model Methods

```php
use App\Models\SystemSetting;

// Lấy giá trị (có cache)
$value = SystemSetting::get('company_name', 'Default');

// Lấy tất cả settings
$allSettings = SystemSetting::getAllSettings();

// Lấy settings theo nhóm
$companySettings = SystemSetting::getGroupSettings('company');

// Xóa cache thủ công
SystemSetting::clearCache();
```

## 📦 Files Created/Modified

### New Files
1. `database/migrations/2025_11_24_071913_create_system_settings_table.php`
2. `app/Models/SystemSetting.php`
3. `database/seeders/SystemSettingSeeder.php`
4. `app/Http/Controllers/SystemSettingController.php`
5. `resources/views/settings/index.blade.php`
6. `app/Helpers/SettingHelper.php`
7. `SYSTEM-SETTINGS-PROPOSAL.md` (documentation)
8. `SYSTEM-SETTINGS-COMPLETED.md` (this file)

### Modified Files
1. `routes/web.php` - Added 5 routes
2. `resources/views/layouts/navigation.blade.php` - Added menu links (desktop + mobile)
3. `composer.json` - Auto-load helper
4. `database/seeders/DatabaseSeeder.php` - Added SystemSettingSeeder

## 🚀 Migration & Seeding

```bash
# Run migration
php artisan migrate

# Seed default settings
php artisan db:seed --class=SystemSettingSeeder

# Or fresh install
php artisan migrate:fresh --seed
```

## ⚡ Features

### Automatic Caching
- Cache TTL: 1 hour (3600 seconds)
- Automatically cleared on save/delete
- Cache key: `system_settings_all`
- Manual clear: `SystemSetting::clearCache()`

### File Uploads
- Supported: images (jpg, jpeg, png, ico)
- Max size: 2 MB
- Storage: `storage/app/public/settings/`
- Auto-delete old file on upload
- AJAX upload without page reload

### Validation
- Number fields: numeric validation
- Email fields: email format validation
- URL fields: URL format validation
- Required files: max 2MB, specific mimes

### Security
- Permission-based access (`manage settings`)
- CSRF protection on all forms
- File type validation
- Input sanitization

## 🎯 Testing Checklist

- [x] Migration runs successfully
- [x] Seeder creates 57 settings
- [x] Helper function works: `setting('company_name')`
- [x] Routes registered (5 routes)
- [x] Navigation links visible (desktop + mobile)
- [x] Permission `manage settings` exists
- [x] Cache system working
- [x] View compiles without errors
- [x] Composer autoload regenerated

## 📝 Notes

### Excluded Features (External Dependencies)
Per user request, the following were **NOT** implemented:
- ❌ Email/SMTP settings
- ❌ SMS Gateway
- ❌ Payment Gateway integrations
- ❌ Google Analytics
- ❌ Facebook Pixel
- ❌ Telegram/Zalo bot settings
- ❌ Google Maps API

### Included Features (Self-Contained)
- ✅ Company information
- ✅ Appearance (logos, colors)
- ✅ Language & Format
- ✅ Business rules
- ✅ Security settings
- ✅ Local backup settings
- ✅ System settings

## 🔗 Related Documentation
- [SYSTEM-SETTINGS-PROPOSAL.md](SYSTEM-SETTINGS-PROPOSAL.md) - Original proposal with 10 groups
- [start-guide.md](start-guide.md) - Project overview

## 🎓 Usage Examples

### Example 1: Display Company Info
```blade
<h1>{{ setting('company_name', 'My Company') }}</h1>
<p>{{ setting('company_slogan') }}</p>
<p>Email: {{ setting('company_email') }}</p>
<p>Phone: {{ setting('company_hotline') }}</p>
```

### Example 2: Calculate Fee with VAT
```php
$basePrice = 100000;
$vatRate = setting('vat_rate', 10) / 100;
$totalPrice = $basePrice * (1 + $vatRate);
```

### Example 3: Format Currency
```php
$amount = 123456;
$symbol = setting('currency_symbol', '₫');
$position = setting('currency_position', 'after');
$decimal = setting('decimal_places', 0);
$separator = setting('thousand_separator', ',');

$formatted = number_format($amount, $decimal, '.', $separator);
$display = $position === 'before' ? $symbol . $formatted : $formatted . $symbol;
// Output: 123,456₫
```

### Example 4: Check Business Rules
```php
if (setting('require_approval', false)) {
    // Send for approval
} else {
    // Auto-approve
}

if (setting('auto_calculate_fee', true)) {
    $distance = 20; // km
    $freeKm = setting('free_kilometers', 10);
    $pricePerKm = setting('price_per_km', 15000);
    
    $chargeableKm = max(0, $distance - $freeKm);
    $fee = $chargeableKm * $pricePerKm;
}
```

## 📊 Database Schema

```sql
CREATE TABLE system_settings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `group` VARCHAR(100) NOT NULL,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    value TEXT NULL,
    type VARCHAR(50) DEFAULT 'text',
    options TEXT NULL,
    description VARCHAR(255) NULL,
    `order` INT DEFAULT 0,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_group (`group`)
);
```

## ✅ Implementation Status

**Status**: COMPLETED ✅  
**Date**: November 24, 2025  
**Version**: 1.0.0  
**Laravel**: 10.49.1  
**PHP**: 8.2.12

---

**Ready for Production** 🚀

All features implemented, tested, and ready to use. Admin users can now access the System Settings page and configure the application through the web interface.
