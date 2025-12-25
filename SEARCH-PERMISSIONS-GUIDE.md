# Hướng dẫn Phân quyền Tìm kiếm (Search Permissions)

## 📋 Tổng quan

Hệ thống phân quyền tìm kiếm đã được bổ sung vào trang `/search` cho phép quản trị viên kiểm soát chi tiết những gì mỗi loại user có thể tìm kiếm.

## 🔐 Các Quyền Tìm kiếm (Search Permissions)

Đã thêm 5 quyền mới vào hệ thống:

1. **search vehicles** - Tìm kiếm xe cấp cứu
2. **search patients** - Tìm kiếm bệnh nhân  
3. **search incidents** - Tìm kiếm chuyến đi
4. **search transactions** - Tìm kiếm giao dịch
5. **search notes** - Tìm kiếm ghi chú

## 👥 Phân quyền theo Role

### Admin
- ✅ Tất cả quyền tìm kiếm (kế thừa tất cả permissions)

### Dispatcher
- ✅ search vehicles
- ✅ search patients
- ✅ search incidents
- ✅ search transactions
- ✅ search notes

### Accountant
- ✅ search vehicles
- ✅ search patients
- ✅ search incidents
- ✅ search transactions
- ❌ search notes

### Driver
- ✅ search vehicles
- ✅ search incidents
- ❌ search patients
- ❌ search transactions
- ❌ search notes

### Medical Staff
- ✅ search vehicles
- ✅ search patients
- ✅ search incidents
- ❌ search transactions
- ❌ search notes

### Manager
- ✅ search vehicles
- ✅ search patients
- ✅ search incidents
- ✅ search transactions
- ✅ search notes

### Investor
- ❌ Không có quyền tìm kiếm (chỉ xem báo cáo)

### Vehicle Owner
- ✅ search vehicles
- ✅ search incidents
- ❌ search patients
- ❌ search transactions
- ❌ search notes

## 🎯 Cách sử dụng

### 1. Quản lý quyền tại `/role-permissions`

Truy cập trang quản lý phân quyền:
```
https://your-domain.com/role-permissions
```

Tại đây bạn có thể:
- Xem ma trận phân quyền đầy đủ
- Bật/tắt từng quyền cho từng role
- Thấy ngay kết quả thay đổi

### 2. Trang tìm kiếm `/search`

Khi user truy cập trang tìm kiếm:

**Dropdown "Loại tìm kiếm"** chỉ hiển thị các loại mà user có quyền:
```php
// Ví dụ: Driver chỉ thấy:
- Tất cả
- Xe
- Chuyến đi
```

**Thông báo quyền** hiển thị ở dưới form tìm kiếm nếu user không có đủ quyền

**Kết quả tìm kiếm** chỉ hiển thị các loại mà user có quyền

## 🔧 Cài đặt & Migration

Đã thực hiện:
```bash
# 1. Thêm permissions vào RoleSeeder
php artisan db:seed --class=RoleSeeder

# 2. Clear cache
php artisan permission:cache-reset
```

## 📝 Code Implementation

### Controller: GlobalSearchController
```php
// Kiểm tra quyền trước khi tìm kiếm
if ($user->can('search vehicles')) {
    $results['vehicles'] = Vehicle::where(...)->get();
}
```

### View: search/index.blade.php
```blade
{{-- Chỉ hiển thị option nếu có quyền --}}
@can('search vehicles')
    <option value="vehicles">Xe</option>
@endcan
```

### Seeder: RoleSeeder.php
```php
$permissions = [
    // ... existing permissions
    'search vehicles',
    'search incidents',
    'search transactions',
    'search patients',
    'search notes',
];
```

## 🧪 Testing

### Test quyền của từng role:

1. **Login với role Driver**
   - Vào `/search`
   - Kiểm tra chỉ thấy: "Tất cả", "Xe", "Chuyến đi"
   - Tìm kiếm chỉ trả về xe và chuyến đi

2. **Login với role Dispatcher**
   - Vào `/search`
   - Kiểm tra thấy đầy đủ tất cả options
   - Tìm kiếm trả về tất cả loại kết quả

3. **Login với role Investor**
   - Vào `/search`
   - Không thấy bất kỳ kết quả nào

## ⚙️ Tùy chỉnh quyền

Để thay đổi quyền cho một role:

### Cách 1: Qua giao diện web
1. Truy cập `/role-permissions`
2. Click vào ô tương ứng với role và permission
3. Thay đổi được lưu ngay lập tức

### Cách 2: Qua code
```php
use Spatie\Permission\Models\Role;

$role = Role::findByName('driver');
$role->givePermissionTo('search transactions');
// hoặc
$role->revokePermissionTo('search vehicles');
```

### Cách 3: Sửa RoleSeeder
Sửa file `database/seeders/RoleSeeder.php` và chạy lại:
```bash
php artisan db:seed --class=RoleSeeder
php artisan permission:cache-reset
```

## 📊 Database Schema

Permissions được lưu trong các bảng:
- `permissions` - Danh sách quyền
- `roles` - Danh sách role
- `role_has_permissions` - Mapping role-permission
- `model_has_permissions` - Permission trực tiếp cho user
- `model_has_roles` - Gán role cho user

## 🔍 Troubleshooting

### Không thấy kết quả tìm kiếm
```bash
# Kiểm tra permission cache
php artisan permission:cache-reset

# Kiểm tra user có quyền không
php artisan tinker
>>> $user = User::find(1);
>>> $user->can('search vehicles'); // should return true/false
```

### Permission không được apply
```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan permission:cache-reset
```

### Thêm quyền cho user cụ thể
```php
$user = User::find(1);
$user->givePermissionTo('search notes');
```

## 📚 Tài liệu liên quan

- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [Laravel Authorization](https://laravel.com/docs/authorization)
- File cấu hình: `config/permission.php`

---

**Cập nhật lần cuối:** 25/12/2024
**Phiên bản:** 1.0
