# 🔐 HỆ THỐNG KHÓA KỲ KẾ TOÁN

## 📋 TỔNG QUAN

Hệ thống khóa kỳ kế toán giúp bảo vệ dữ liệu sau khi chốt sổ, ngăn chặn sửa đổi giao dịch trong quá khứ và đảm bảo tính toàn vẹn của báo cáo tài chính.

---

## ✨ TÍNH NĂNG CHÍNH

### 1. **BA TRẠNG THÁI KỲ KẾ TOÁN**

| Trạng thái | Icon | Mô tả | Hành động |
|------------|------|-------|-----------|
| **Đang mở** | 🔓 | Có thể thêm/sửa/xóa GD tự do | Đóng kỳ |
| **Đã đóng** | 🔒 | KHÔNG thể thêm/sửa/xóa GD | Khóa kỳ / Mở lại |
| **Đã khóa** | 🔐 | Chỉ admin mới mở khóa được | Mở khóa (admin only) |

### 2. **AUTO RECALCULATION**

Khi thêm/sửa/xóa giao dịch với ngày trong quá khứ, hệ thống **TỰ ĐỘNG**:
- Tính lại số dư TẤT CẢ giao dịch sau ngày đó
- Sắp xếp theo DATE (không phải ID!)
- Đảm bảo số dư luôn chính xác theo timeline

---

## 🎯 HƯỚNG DẪN SỬ DỤNG

### **Bước 1: Truy cập quản lý kỳ**
```
Dashboard → Menu → "Kỳ kế toán"
Hoặc: /accounting-periods
```

### **Bước 2: Xem danh sách kỳ**
- Hiển thị 12 tháng gần nhất
- Màu nền:
  - Trắng: Đang mở
  - Vàng nhạt: Đã đóng
  - Đỏ nhạt: Đã khóa

### **Bước 3: Đóng kỳ (cuối tháng)**
```
1. Click "Đóng kỳ" ở tháng hiện tại
2. Xác nhận
3. Kỳ chuyển sang trạng thái "Đã đóng"
4. KHÔNG thể thêm/sửa/xóa GD trong tháng đó nữa
```

### **Bước 4: Khóa kỳ (sau khi kiểm toán/báo cáo thuế)**
```
1. Đảm bảo đã đóng kỳ
2. Click "Khóa kỳ"
3. Xác nhận
4. Chỉ admin mới mở khóa được
```

---

## 🔄 LUỒNG XỬ LÝ GIAO DỊCH QUÁ KHỨ

### **Tình huống: Thêm GD thiếu của tháng trước**

**Ví dụ:**
```
Hôm nay: 03/01/2026
Phát hiện thiếu GD chi 500K ngày 10/12/2025
```

**Quy trình:**

1. **Kiểm tra trạng thái kỳ 12/2025:**
   - ✅ Nếu ĐANG MỞ: Cho phép thêm → Auto recalculate
   - ❌ Nếu ĐÃ ĐÓNG: Từ chối
   - ❌ Nếu ĐÃ KHÓA: Từ chối

2. **Nếu cho phép thêm:**
   ```
   - Lưu GD: 10/12/2025 - Chi 500K
   - Trigger: RecalculateBalancesJob(fromDate: 10/12/2025)
   - Recalculate TẤT CẢ GD từ 10/12 → hiện tại
   - Số dư được cập nhật chính xác theo timeline
   ```

3. **Kết quả:**
   ```
   Timeline đúng:
   - 05/12: GD#1 → Balance: 10M
   - 10/12: GD#NEW (mới thêm) → Balance: 9.5M ✓
   - 15/12: GD#2 → Balance: 7.5M ✓ (đã recalculate)
   - 01/01: GD#3 → Balance: 4.5M ✓ (đã recalculate)
   ```

---

## ⚠️ LƯU Ý QUAN TRỌNG

### **1. KHÔNG THỂ THÊM GD VÀO KỲ ĐÃ ĐÓNG/KHÓA**

```php
// Hệ thống sẽ throw exception:
"Không thể thêm giao dịch vào kỳ kế toán đã khóa. 
 Tháng 12/2025 đã được khóa sổ."
```

### **2. RECALCULATION CHỈ CHẠY KHI CẦN**

- Thêm GD trong ngày: KHÔNG recalculate
- Thêm GD quá khứ (>1 ngày): CÓ recalculate

### **3. PERFORMANCE VỚI NHIỀU GIAO DỊCH**

- Recalculation chạy qua Queue (background job)
- User không phải chờ đợi
- Log trong `storage/logs/laravel.log`

### **4. QUYỀN HẠN**

- **Manager**: Đóng kỳ, Khóa kỳ, Mở lại kỳ
- **Admin**: Tất cả + Mở khóa kỳ đã khóa

---

## 📊 KỊ

CH BẢN THỰC TẾ

### **Kịch bản 1: Cuối tháng đóng sổ**

```
Ngày 31/12/2025:
1. Kiểm tra tất cả GD tháng 12
2. Đóng kỳ 12/2025
3. Không ai có thể sửa GD tháng 12 nữa
4. Xuất báo cáo tháng 12
```

### **Kịch bản 2: Phát hiện thiếu GD sau khi đóng sổ**

```
Ngày 05/01/2026:
Phát hiện thiếu GD chi 1M ngày 28/12/2025

❌ CÁCH SAI:
   - Thêm GD 28/12 → Bị từ chối (kỳ đã đóng)

✅ CÁCH ĐÚNG:
   Option 1: Mở lại kỳ 12/2025 → Thêm GD → Đóng lại
   Option 2: Tạo bút toán điều chỉnh tháng 01/2026
```

### **Kịch bản 3: Sau báo cáo thuế**

```
Ngày 15/01/2026:
1. Đã nộp báo cáo thuế tháng 12
2. Khóa kỳ 12/2025
3. Chỉ admin mới có thể mở khóa (đặc biệt cần thiết)
```

---

## 🔧 TROUBLESHOOTING

### **Lỗi: "Không thể thêm giao dịch vào kỳ đã khóa"**

**Nguyên nhân:** Tháng đã bị khóa

**Giải pháp:**
1. Kiểm tra trạng thái kỳ tại `/accounting-periods`
2. Nếu cần thêm GD:
   - Manager: Không làm được, liên hệ admin
   - Admin: Mở khóa → Thêm GD → Khóa lại

### **Lỗi: Số dư không khớp sau khi thêm GD quá khứ**

**Nguyên nhân:** Recalculation job chưa chạy xong

**Giải pháp:**
1. Chờ 1-2 phút
2. Refresh trang
3. Check log: `storage/logs/laravel.log`

---

## 🎓 BEST PRACTICES

1. **Đóng kỳ ngay sau cuối tháng**
   - Tránh sửa đổi sau khi đã tổng hợp

2. **Khóa kỳ sau khi báo cáo thuế**
   - Đảm bảo dữ liệu không thay đổi

3. **Kiểm tra kỹ trước khi khóa**
   - Khóa rồi chỉ admin mới mở được

4. **Backup trước khi mở khóa**
   - Phòng trường hợp sửa nhầm

---

## 🚀 DEMO

### **Test recalculation:**

```bash
# 1. Thêm GD với ngày hiện tại
POST /transactions
{
    "date": "2026-01-03",
    "amount": 1000000
}
# → KHÔNG trigger recalculation

# 2. Thêm GD với ngày quá khứ
POST /transactions
{
    "date": "2025-12-10",
    "amount": 500000
}
# → Trigger RecalculateBalancesJob(fromDate: 2025-12-10)
# → Tất cả GD từ 10/12 được tính lại

# 3. Check log
tail -f storage/logs/laravel.log | grep Recalculate
```

---

## 📞 HỖ TRỢ

Nếu gặp vấn đề, check:
1. `/accounting-periods` - Trạng thái các kỳ
2. `storage/logs/laravel.log` - Log recalculation
3. Database: `accounting_periods` table

---

## 🎉 TÓM TẮT

✅ **Đã implement:**
- Bảng `accounting_periods`
- Model với 3 trạng thái: open/closed/locked
- Auto recalculation theo DATE
- Background job xử lý
- UI quản lý đầy đủ
- Permission control

✅ **Lợi ích:**
- Bảo vệ dữ liệu đã chốt sổ
- Linh hoạt thêm GD quá khứ (kỳ chưa khóa)
- Số dư luôn chính xác 100%
- Tuân thủ quy định kế toán

🎯 **Mục tiêu đạt được:**
> "Khi thêm GD quá khứ, hệ thống TỰ ĐỘNG tính lại số dư toàn bộ từ ngày đó đến hiện tại, đảm bảo số dư chính xác theo timeline!"
