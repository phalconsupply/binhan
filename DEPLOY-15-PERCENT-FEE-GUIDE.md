# 🚀 HƯỚNG DẪN DEPLOY HỆ THỐNG PHÍ 15%

## 📋 TỔNG QUAN

Phiên bản mới chuyển **Phí 15%** từ tính toán ảo sang **giao dịch thực tế**:
- Mỗi chuyến đi của xe có chủ → Tự động tạo GD chi phí 15%
- Phí được lưu với `category='phí_công_ty_15%'`
- AccountBalance và UI Lợi nhuận giờ khớp nhau 100%

---

## ⚠️ QUAN TRỌNG

**Phải backup database trước khi thực hiện!**

---

## 🔧 CÁC BƯỚC DEPLOY TRÊN VPS

### **Bước 1: Backup Database**

```bash
# SSH vào VPS
ssh user@your-vps-ip

# Backup database
cd /var/www/binhan  # hoặc đường dẫn project của bạn
php artisan backup:database

# Hoặc backup thủ công
mysqldump -u username -p binhan > backup_before_15percent_$(date +%Y%m%d_%H%M%S).sql
```

### **Bước 2: Pull Code Mới**

```bash
cd /var/www/binhan
git pull origin main
```

**Kiểm tra commit mới:**
- Commit: `c2d5f90`
- Message: "feat: Convert 15% company fee to real transactions"

### **Bước 3: Clear Cache**

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### **Bước 4: Chạy Script Tạo Phí 15% Cho Chuyến Đi Cũ**

⚠️ **QUAN TRỌNG**: Script này sẽ tạo giao dịch phí 15% cho TẤT CẢ chuyến đi cũ của xe có chủ.

```bash
# Chạy script
php create-15-percent-fee-for-old-incidents.php
```

**Script sẽ:**
1. Duyệt qua tất cả xe có chủ
2. Với mỗi chuyến đi (incident):
   - Tính lợi nhuận chuyến đi = Thu - Chi
   - Nếu lợi nhuận > 0 → Tạo GD phí 15% = Lợi nhuận × 15%
3. Hiển thị log chi tiết
4. Tổng kết số lượng GD đã tạo

**Output mẫu:**
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🚗 XE: 49B08879 (ID: 4)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

   ✅ Incident #28 (17/11/2025): Thu=4.500.000, Chi=3.260.000, Lợi=1.240.000 → Phí=186.000
   ✅ Incident #42 (21/11/2025): Thu=4.500.000, Chi=3.140.000, Lợi=1.360.000 → Phí=204.000
   ...

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📊 TỔNG KẾT:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✅ Đã tạo:     76 giao dịch phí 15%
⏭️  Bỏ qua:    0 incident (đã có phí hoặc lỗ)
📋 Tổng:       76 incidents

🎉 HOÀN TẤT!
```

### **Bước 5: Xác Minh Kết Quả**

```bash
# Kiểm tra số lượng giao dịch phí 15%
php artisan tinker
>>> \App\Models\Transaction::where('category', 'phí_công_ty_15%')->count();
# Kết quả: Số lượng GD phí 15% đã tạo

# Kiểm tra tổng số tiền phí 15% của 1 xe
>>> \App\Models\Transaction::where('vehicle_id', 4)->where('category', 'phí_công_ty_15%')->sum('amount');
# Kết quả: Tổng phí 15% của xe ID=4

# Exit
>>> exit
```

### **Bước 6: Test Trên UI**

1. Truy cập trang chi tiết xe có chủ
2. Kiểm tra:
   - **Lợi nhuận toàn thời gian** đã giảm (vì có phí 15%)
   - **Số dư** bây giờ khớp với lợi nhuận
3. Xem danh sách giao dịch:
   - Tìm các GD có category = "phí_công_ty_15%"
   - Note: "Phí công ty 15% - Chuyến đi #xxx"

### **Bước 7: Test Tạo Chuyến Đi Mới**

1. Tạo 1 chuyến đi mới cho xe có chủ
2. Sau khi lưu, kiểm tra:
   - Có tự động tạo GD phí 15% không?
   - Số dư có cập nhật đúng không?

---

## 📊 SO SÁNH TRƯỚC & SAU

### **TRƯỚC (Phí 15% ảo):**
```
Thu hiển thị:     133.500.000đ
Chi hiển thị:     104.825.425đ (chi thực + phí ảo)
Lợi nhuận UI:     28.674.575đ

AccountBalance:   36.669.575đ
Chênh lệch:       7.995.000đ ❌ (Phí ảo không được trừ)
```

### **SAU (Phí 15% thực tế):**
```
Thu hiển thị:     133.500.000đ
Chi hiển thị:     133.500.000đ (bao gồm phí thực tế)
Lợi nhuận UI:     0đ

AccountBalance:   0đ
Chênh lệch:       0đ ✅ (Khớp hoàn toàn!)
```

---

## 🔍 TROUBLESHOOTING

### **Lỗi: "Column owner_id not found"**
- **Nguyên nhân**: Model Vehicle không có column `owner_id`
- **Giải pháp**: Script đã sửa, dùng `hasOwner()` method

### **Lỗi: "Data truncated for column 'method'"**
- **Nguyên nhân**: Enum `method` không có giá trị `transfer`
- **Giải pháp**: Script đã sửa, dùng `bank` thay thế

### **Script chạy xong nhưng không thấy phí 15%**
- Kiểm tra: `SELECT * FROM transactions WHERE category = 'phí_công_ty_15%'`
- Nếu không có → Kiểm tra log lỗi trong script

### **Số dư vẫn không khớp**
- Làm mới cache: `php artisan cache:clear`
- Kiểm tra xem có GD nào bị sai `from_account`/`to_account` không

---

## ✅ CHECKLIST HOÀN TẤT

- [ ] Đã backup database
- [ ] Đã pull code mới (commit c2d5f90)
- [ ] Đã clear cache
- [ ] Đã chạy script tạo phí 15%
- [ ] Xác nhận script chạy thành công (có log tổng kết)
- [ ] Kiểm tra UI: Lợi nhuận đã giảm
- [ ] Kiểm tra UI: Số dư khớp với lợi nhuận
- [ ] Test tạo chuyến đi mới: Phí 15% tự động
- [ ] Thông báo team về thay đổi

---

## 📞 LƯU Ý

1. **Backup quan trọng**: Phòng khi có sự cố cần rollback
2. **Chạy ngoài giờ cao điểm**: Script có thể mất vài phút
3. **Không tắt script giữa chừng**: Dữ liệu có thể bị thiếu
4. **Check log cẩn thận**: Đảm bảo không có lỗi
5. **Thông báo user**: Số liệu sẽ thay đổi sau deploy

---

## 🎯 KẾT QUẢ MONG ĐỢI

Sau khi deploy:
- ✅ Mỗi chuyến đi có GD phí 15% rõ ràng
- ✅ Số dư = Lợi nhuận (không còn chênh lệch)
- ✅ Báo cáo tài chính chính xác hơn
- ✅ Dễ dàng kiểm toán, theo dõi phí công ty
- ✅ Hệ thống nhất quán với quy định kế toán

---

## 🔄 ROLLBACK (Nếu cần)

```bash
# 1. Restore database
mysql -u username -p binhan < backup_before_15percent_YYYYMMDD_HHMMSS.sql

# 2. Rollback code
git reset --hard 7e91904  # Commit trước khi có phí 15%

# 3. Clear cache
php artisan cache:clear
```

---

## 📚 TÀI LIỆU LIÊN QUAN

- [ACCOUNTING-PERIOD-LOCK-GUIDE.md](./ACCOUNTING-PERIOD-LOCK-GUIDE.md)
- Commit: c2d5f90 - "feat: Convert 15% company fee to real transactions"
- Script: [create-15-percent-fee-for-old-incidents.php](./create-15-percent-fee-for-old-incidents.php)
