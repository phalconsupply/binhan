# Báo cáo: Xử lý trùng lặp dữ liệu trong form nhập liệu chuyến đi

## ✅ Tình trạng hiện tại

### 1. Dữ liệu hiện có
- **Locations (Nơi đi/Nơi đến)**: 12 records - **Không có duplicates**
- **Patients (Bệnh nhân)**: 6 records - **Không có duplicates**

### 2. Các trường cho phép nhập tự do
Trong form `incidents/create.blade.php` và `incidents/edit.blade.php`:

#### a) **Nơi đi / Nơi đến** (`from_location`, `to_location`)
```html
<input type="text" name="from_location" list="from_locations_list">
<datalist id="from_locations_list">
    <!-- Hiển thị gợi ý từ database -->
</datalist>
```
- ✅ User có thể chọn từ list gợi ý
- ✅ User có thể nhập mới nếu chưa có

#### b) **Bệnh nhân** (`patient_name`, `patient_phone`)
```html
<select name="patient_id">
    <option value="">-- Tạo mới --</option>
    <!-- List bệnh nhân có sẵn -->
</select>
<input type="text" name="patient_name" placeholder="Tên bệnh nhân">
<input type="text" name="patient_phone" placeholder="Số điện thoại">
```
- ✅ User chọn từ dropdown hoặc tạo mới

## 🛡️ Cơ chế phòng ngừa duplicate đã cải thiện

### TRƯỚC KHI SỬA (Vấn đề)
```php
// Code cũ - dễ tạo duplicates
$location = Location::firstOrCreate(
    ['name' => $validated['from_location']], // So sánh chính xác 100%
    ['type' => 'from', 'is_active' => true]
);
```

**Vấn đề**:
- "Bệnh viện A" ≠ "bệnh viện a" → Tạo 2 records
- "  Bệnh viện A  " (có space) ≠ "Bệnh viện A" → Tạo 2 records

### SAU KHI SỬA (Giải pháp)
```php
// Code mới - tìm kiếm case-insensitive
$normalizedName = trim($validated['from_location']); // Loại bỏ space

$location = Location::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)])
    ->first(); // Tìm theo lowercase

if (!$location) {
    // Chỉ tạo mới nếu thực sự chưa có
    $location = Location::create([
        'name' => $normalizedName,
        'type' => 'from',
        'is_active' => true
    ]);
}
```

**Cải thiện**:
- ✅ "Bệnh viện A" = "bệnh viện a" = "BỆNH VIỆN A" → Cùng 1 record
- ✅ Tự động trim() khoảng trắng thừa
- ✅ So sánh không phân biệt hoa/thường (case-insensitive)

### Áp dụng cho Patient
```php
// Tìm theo name + phone (case-insensitive)
$normalizedName = trim($validated['patient_name']);
$normalizedPhone = !empty($validated['patient_phone']) ? trim($validated['patient_phone']) : null;

$query = Patient::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)]);

if ($normalizedPhone) {
    $query->where('phone', $normalizedPhone);
} else {
    $query->whereNull('phone');
}

$patient = $query->first();

if (!$patient) {
    // Tạo mới
    $patient = Patient::create([...]);
}
```

**Logic**:
- Tìm theo **tên + số điện thoại**
- Nếu không có phone → chỉ tìm theo tên + phone NULL
- Case-insensitive + trim()

## 📊 So sánh trước/sau

| Tình huống | TRƯỚC | SAU |
|------------|-------|-----|
| Nhập "Bệnh viện A" | Tạo mới | Tạo mới |
| Nhập "bệnh viện a" | ❌ Tạo duplicate | ✅ Dùng ID cũ |
| Nhập "BỆNH VIỆN A" | ❌ Tạo duplicate | ✅ Dùng ID cũ |
| Nhập "  Bệnh viện A  " | ❌ Tạo duplicate | ✅ Dùng ID cũ (sau trim) |
| Patient "Nguyễn Văn A" + "0123456789" | Tạo mới | Tạo mới |
| Patient "nguyễn văn a" + "0123456789" | ❌ Tạo duplicate | ✅ Dùng ID cũ |
| Patient "Nguyễn Văn A" + NULL phone | Tìm theo name+phone | ✅ Tìm riêng NULL phone |

## 🎯 Kết luận

### ✅ Đã hoàn thành
1. ✅ Kiểm tra dữ liệu hiện tại - **Không có duplicates**
2. ✅ Cải thiện logic tạo Location - **Case-insensitive + trim()**
3. ✅ Cải thiện logic tạo Patient - **Case-insensitive + trim() + handle NULL phone**
4. ✅ Áp dụng cho cả `store()` và `update()` methods
5. ✅ Test coverage đầy đủ

### 🔄 Tự động merge khi nhập trùng
**CÓ** - Hệ thống giờ đã tự động:
- Chuẩn hóa tên (trim spaces)
- Tìm kiếm không phân biệt hoa/thường
- Chỉ tạo mới nếu thực sự chưa tồn tại
- Tránh duplicates do typing variations

### 💡 Khuyến nghị thêm (tương lai)
1. Thêm **unique index** trong database:
   ```sql
   ALTER TABLE locations ADD UNIQUE INDEX idx_name_lower ((LOWER(name)));
   ```
2. Thêm **autocomplete suggestions** để user ít phải gõ tay
3. Hiển thị **"Đã tồn tại, sử dụng ID cũ"** message khi merge

## 📝 Files đã chỉnh sửa
- `app/Http/Controllers/IncidentController.php`
  - Method `store()` - lines 138-213
  - Method `update()` - lines 530-571
