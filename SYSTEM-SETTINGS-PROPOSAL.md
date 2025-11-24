# ĐỀ XUẤT TÍNH NĂNG "CẤU HÌNH HỆ THỐNG"

## 📋 TỔNG QUAN

Tính năng cho phép Admin cấu hình các thông số hệ thống mà không cần chỉnh sửa code.

---

## 🎯 CÁC NHÓM CẤU HÌNH

### 1. **THÔNG TIN CÔNG TY** (Company Information)
```
- Tên công ty (Company Name)
- Tên viết tắt (Short Name)
- Slogan (Tagline)
- Mô tả (Description)
- Email liên hệ (Contact Email)
- Số điện thoại (Phone)
- Hotline
- Website URL
- Địa chỉ (Address)
- Thành phố/Tỉnh (City/Province)
- Quốc gia (Country)
- Mã số thuế (Tax Code)
- Giấy phép kinh doanh (Business License)
```

### 2. **GIAO DIỆN HỆ THỐNG** (System Appearance)
```
- Logo chính (Main Logo) - Upload
- Logo nhỏ (Small Logo/Favicon) - Upload
- Favicon - Upload
- Background đăng nhập (Login Background) - Upload
- Màu chủ đạo (Primary Color) - Color picker
- Màu phụ (Secondary Color)
- Màu accent (Accent Color)
- Font chữ (Font Family)
- Kích thước font (Font Size)
```

### 3. **NGÔN NGỮ & ĐỊNH DẠNG** (Language & Format)
```
- Ngôn ngữ mặc định (Default Language): vi, en
- Múi giờ (Timezone): Asia/Ho_Chi_Minh
- Định dạng ngày (Date Format): DD/MM/YYYY, MM/DD/YYYY
- Định dạng giờ (Time Format): 24h, 12h
- Đơn vị tiền tệ (Currency): VND
- Ký hiệu tiền tệ (Currency Symbol): ₫
- Vị trí ký hiệu (Currency Position): Before, After
- Số thập phân (Decimal Places): 0, 2
- Phân cách hàng nghìn (Thousand Separator): , hoặc .
```

### 4. **EMAIL** (Email Configuration)
```
- Email gửi đi (From Email)
- Tên người gửi (From Name)
- SMTP Host
- SMTP Port
- SMTP Username
- SMTP Password
- SMTP Encryption: SSL, TLS
- Email nhận thông báo (Admin Email)
- Bật/tắt email thông báo (Enable Email Notifications)
```

### 5. **BẢO MẬT** (Security Settings)
```
- Thời gian timeout session (Session Timeout): phút
- Số lần đăng nhập sai tối đa (Max Login Attempts)
- Thời gian khóa tài khoản (Lockout Duration): phút
- Bắt buộc đổi mật khẩu (Force Password Change): ngày
- Độ dài mật khẩu tối thiểu (Min Password Length)
- Yêu cầu ký tự đặc biệt (Require Special Characters)
- Yêu cầu số (Require Numbers)
- Yêu cầu chữ hoa (Require Uppercase)
- Xác thực 2 bước (Two-Factor Authentication): Enable/Disable
- Cho phép nhớ đăng nhập (Remember Me): Enable/Disable
```

### 6. **NGHIỆP VỤ** (Business Settings)
```
- Giờ bắt đầu ca (Shift Start Time)
- Giờ kết thúc ca (Shift End Time)
- Số km miễn phí (Free Kilometers)
- Giá mỗi km thêm (Price Per Extra Km)
- Thuế VAT (VAT Rate): %
- Phí dịch vụ (Service Fee): %
- Thời gian chờ miễn phí (Free Waiting Time): phút
- Giá tiền chờ (Waiting Fee Per Hour)
- Tự động tính tiền (Auto Calculate Fee): Yes/No
- Yêu cầu phê duyệt chuyến (Require Approval): Yes/No
- Cho phép chỉnh sửa sau (Allow Edit After Save): Yes/No
```

### 7. **THÔNG BÁO** (Notification Settings)
```
- Thông báo chuyến mới (New Incident Notification): Yes/No
- Thông báo thu/chi (Transaction Notification): Yes/No
- Thông báo bảo trì xe (Maintenance Notification): Yes/No
- Thông báo quá hạn (Overdue Notification): Yes/No
- Email thông báo hàng ngày (Daily Report Email): Yes/No
- Giờ gửi báo cáo (Report Time): HH:MM
- Telegram/Zalo Bot Token (nếu có)
```

### 8. **BACKUP & BẢO TRÌ** (Backup & Maintenance)
```
- Tự động backup (Auto Backup): Yes/No
- Tần suất backup (Backup Frequency): Daily, Weekly
- Giữ lại backup (Retain Backups): số ngày
- Chế độ bảo trì (Maintenance Mode): On/Off
- Thông báo bảo trì (Maintenance Message)
```

### 9. **TÍCH HỢP** (Integrations)
```
- Google Analytics ID
- Facebook Pixel ID
- Google Maps API Key
- Zalo OA ID
- Telegram Bot Token
- SMS Gateway API
- Payment Gateway (Momo, VNPay, ZaloPay)
```

### 10. **HỆ THỐNG** (System Settings)
```
- Chế độ debug (Debug Mode): On/Off
- Log Level: debug, info, warning, error
- Kích thước file upload tối đa (Max Upload Size): MB
- Loại file cho phép (Allowed File Types)
- Cache: Enable/Disable
- Cache Duration: phút
- Số bản ghi mỗi trang (Records Per Page)
- Số ngày giữ log (Log Retention Days)
```

---

## 💾 CẤU TRÚC DATABASE

```sql
CREATE TABLE system_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `group` VARCHAR(100) NOT NULL,        -- company, appearance, language, etc.
    `key` VARCHAR(100) NOT NULL,          -- company_name, logo_path, etc.
    `value` TEXT,                          -- Giá trị setting
    `type` VARCHAR(50) DEFAULT 'text',    -- text, textarea, file, color, select, etc.
    `options` TEXT,                        -- JSON options cho select/radio
    `description` VARCHAR(255),            -- Mô tả setting
    `order` INT DEFAULT 0,                 -- Thứ tự hiển thị
    `is_public` BOOLEAN DEFAULT FALSE,     -- Có cho phép truy cập public không
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_key (`key`)
);
```

---

## 🎨 GIAO DIỆN

### Layout:
```
┌─────────────────────────────────────┐
│  CẤU HÌNH HỆ THỐNG                  │
├─────────────────────────────────────┤
│  [Tabs Navigation]                  │
│  - Thông tin công ty                │
│  - Giao diện                        │
│  - Ngôn ngữ                         │
│  - Email                            │
│  - Bảo mật                          │
│  - Nghiệp vụ                        │
│  - Thông báo                        │
│  - Backup                           │
│  - Tích hợp                         │
│  - Hệ thống                         │
├─────────────────────────────────────┤
│  [Form fields theo tab đã chọn]     │
│                                     │
│  [Lưu cấu hình] [Khôi phục mặc định]│
└─────────────────────────────────────┘
```

---

## 🔧 TÍNH NĂNG BỔ SUNG

1. **Import/Export Settings**: Xuất/nhập cấu hình dạng JSON
2. **Settings History**: Lưu lịch sử thay đổi
3. **Validation**: Validate từng field (email format, URL, số dương, v.v.)
4. **Preview**: Xem trước thay đổi trước khi lưu (cho logo, màu sắc)
5. **Reset to Default**: Khôi phục giá trị mặc định
6. **Cache Settings**: Cache để truy cập nhanh
7. **Permission Control**: Chỉ admin mới được chỉnh sửa

---

## 📊 HELPER FUNCTIONS

```php
// Lấy setting
setting('company_name', 'Default Company');

// Set setting
setting(['company_name' => 'Binhan']);

// Lấy nhóm settings
settings('company'); // Lấy tất cả settings trong nhóm company

// Xóa cache
settings_cache_clear();
```

---

## ✅ ƯU TIÊN TRIỂN KHAI

**Phase 1 (Quan trọng nhất):**
- Thông tin công ty
- Giao diện (logo, favicon, colors)
- Ngôn ngữ & định dạng
- Nghiệp vụ (giá cả, thuế)

**Phase 2:**
- Email
- Bảo mật
- Thông báo

**Phase 3:**
- Backup
- Tích hợp
- Hệ thống

---

## 🎯 ĐỀ XUẤT BẮT ĐẦU

Tôi đề xuất bắt đầu với **Phase 1** - các settings quan trọng nhất:

### 1. Thông tin công ty (8 fields)
- company_name
- company_short_name
- company_email
- company_phone
- company_address
- company_tax_code
- company_description
- company_website

### 2. Giao diện (6 fields)
- site_logo (upload)
- site_favicon (upload)
- primary_color
- login_background (upload)
- font_family
- theme_mode (light/dark)

### 3. Định dạng (6 fields)
- timezone
- date_format
- time_format
- currency
- currency_symbol
- decimal_places

### 4. Nghiệp vụ (6 fields)
- free_kilometers
- price_per_km
- vat_rate
- service_fee
- auto_calculate_fee
- require_approval

---

**Bạn có muốn tôi triển khai Phase 1 không?**

Hoặc bạn muốn điều chỉnh/thêm bớt settings nào?
