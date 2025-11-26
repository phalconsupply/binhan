# 💰 Hướng dẫn Quản lý Khoản vay

## Tổng quan

Module Quản lý Khoản vay cho phép theo dõi và xử lý các khoản vay mua xe, bao gồm:
- Tạo và quản lý thông tin khoản vay
- Lập lịch trả nợ tự động theo tháng
- Điều chỉnh lãi suất trong quá trình vay
- Trả nợ sớm
- Xử lý thanh toán tự động hàng ngày

## Các tính năng chính

### 1. Tạo khoản vay mới

**Đường dẫn:** Chi tiết xe → Nút "Thêm khoản vay"

**Thông tin cần nhập:**
- **CIF** (tùy chọn): Mã khách hàng tại ngân hàng
- **Số hợp đồng** (*): Số hợp đồng vay
- **Ngân hàng** (*): Tên ngân hàng
- **Số tiền gốc** (*): Số tiền vay ban đầu
- **Số tháng** (*): Thời hạn vay (1-360 tháng)
- **Ngày giải ngân** (*): Ngày nhận tiền từ ngân hàng
- **Lãi suất** (*): Lãi suất hàng năm (%)
- **Ngày trả hàng tháng** (*): Ngày trong tháng để trả nợ (1-28)
- **Ghi chú** (tùy chọn): Thông tin bổ sung

**Quy tắc:**
- Mỗi xe chỉ có thể có 1 khoản vay đang hoạt động
- Phải đóng khoản vay hiện tại trước khi tạo khoản mới
- Lịch trả nợ được tạo tự động sau khi lưu

### 2. Lịch trả nợ

**Cách tính:**
- **Kỳ hạn:** 1 tháng/kỳ (cố định)
- **Gốc hàng tháng:** Số tiền gốc / Số tháng
- **Lãi hàng tháng:** Số dư còn lại × (Lãi suất năm / 12)
- **Tổng thanh toán:** Gốc + Lãi

**Ví dụ:**
```
Số tiền gốc: 500,000,000đ
Thời hạn: 60 tháng
Lãi suất: 9%/năm

Gốc hàng tháng = 500,000,000 / 60 = 8,333,333đ

Kỳ 1:
- Số dư: 500,000,000đ
- Lãi = 500,000,000 × (9% / 12) = 3,750,000đ
- Tổng = 8,333,333 + 3,750,000 = 12,083,333đ

Kỳ 2:
- Số dư: 491,666,667đ (500M - 8.33M)
- Lãi = 491,666,667 × 0.75% = 3,687,500đ
- Tổng = 8,333,333 + 3,687,500 = 12,020,833đ
```

### 3. Xử lý thanh toán tự động

**Lệnh:** `php artisan loans:process-repayments`

**Lịch chạy:** Hàng ngày lúc 01:00 sáng

**Quy trình:**
1. Hệ thống tìm tất cả kỳ trả nợ đến hạn ngày hôm nay
2. Kiểm tra lợi nhuận khả dụng của xe
3. Tạo 2 giao dịch chi:
   - **trả_nợ_gốc**: Số tiền gốc
   - **trả_nợ_lãi**: Số tiền lãi
4. Cập nhật trạng thái kỳ trả nợ thành "Đã trả"
5. Cập nhật số dư còn lại của khoản vay

**Xử lý thiếu tiền:**
- Hệ thống vẫn tạo giao dịch chi
- Lợi nhuận xe sẽ âm
- ⚠️ Không chặn thanh toán

**Chạy thử nghiệm:**
```bash
php artisan loans:process-repayments --dry-run
```

### 4. Điều chỉnh lãi suất

**Khi nào cần:**
- Ngân hàng thay đổi lãi suất cho vay
- Thỏa thuận lại điều kiện vay

**Cách thực hiện:**
1. Nhấn "Điều chỉnh lãi suất"
2. Nhập lãi suất mới và ngày hiệu lực
3. Hệ thống sẽ:
   - Lưu lịch sử thay đổi
   - Tính lại lãi cho các kỳ chưa trả từ ngày hiệu lực
   - Giữ nguyên các kỳ đã trả

**Ví dụ:**
```
Ngày 15/01/2025: Điều chỉnh lãi suất từ 9% → 8.5%

Kỳ 5 (đến hạn 10/01/2025): Giữ nguyên lãi 9% (đã quá ngày hiệu lực)
Kỳ 6 (đến hạn 10/02/2025): Tính lại với lãi 8.5%
Kỳ 7 trở đi: Tính với lãi 8.5%
```

### 5. Trả nợ sớm

**Điều kiện:**
- Khoản vay đang ở trạng thái "active"
- Có quyền "manage vehicles"

**Quy trình:**
1. Nhấn "Trả nợ sớm"
2. Hệ thống hiển thị tổng số tiền cần trả
3. Xác nhận
4. Hệ thống sẽ:
   - Tạo giao dịch chi với category "trả_nợ_sớm"
   - Đóng tất cả các kỳ chưa trả
   - Chuyển trạng thái khoản vay thành "paid_off"

**Lưu ý:**
- Hành động này không thể hoàn tác
- Số tiền giao dịch = Tổng của tất cả các kỳ pending

### 6. Sửa thông tin khoản vay

**Các trường có thể sửa:**
- CIF
- Số hợp đồng
- Tên ngân hàng
- Ngày trả hàng tháng
- Ghi chú

**Các trường KHÔNG thể sửa:**
- Số tiền gốc
- Số tháng vay
- Ngày giải ngân
- Lãi suất gốc (dùng "Điều chỉnh lãi suất" thay thế)

### 7. Xóa khoản vay

**Điều kiện:**
- Chưa có kỳ nào được thanh toán
- Có quyền "manage vehicles"

**Khi xóa:**
- Tất cả lịch trả nợ bị xóa
- Không thể khôi phục

## Quyền hạn

- **Admin:** Toàn quyền (tạo, sửa, điều chỉnh, xóa)
- **Chủ xe:** Chỉ xem được thông tin khoản vay
- **Khác:** Không xem được module này

## Giao dịch liên quan

Module tạo các loại giao dịch sau:

| Category | Loại | Mô tả |
|----------|------|-------|
| `trả_nợ_gốc` | chi | Trả nợ gốc hàng tháng (tự động) |
| `trả_nợ_lãi` | chi | Trả lãi hàng tháng (tự động) |
| `trả_nợ_sớm` | chi | Trả nợ sớm toàn bộ số dư còn lại |

## Cấu hình Cron Job

**Trên server production (Ubuntu):**

```bash
# Mở crontab
crontab -e

# Thêm dòng sau
0 1 * * * cd /var/www/binhan && php artisan loans:process-repayments >> /var/www/binhan/storage/logs/loan-repayments.log 2>&1
```

**Giải thích:**
- `0 1 * * *`: Chạy lúc 01:00 sáng mỗi ngày
- Output được ghi vào `storage/logs/loan-repayments.log`

**Kiểm tra log:**
```bash
tail -f /var/www/binhan/storage/logs/loan-repayments.log
```

## Troubleshooting

### Lỗi: "Xe này đã có khoản vay đang hoạt động"

**Nguyên nhân:** Mỗi xe chỉ có 1 khoản vay active

**Giải pháp:**
1. Trả nợ sớm khoản vay hiện tại, HOẶC
2. Đợi đến khi khoản vay được thanh toán hết

### Lỗi: "Không thể xóa khoản vay đã có lịch sử thanh toán"

**Nguyên nhân:** Đã có ít nhất 1 kỳ được thanh toán

**Giải pháp:**
- Không thể xóa, chỉ có thể trả nợ sớm
- Đây là biện pháp bảo vệ dữ liệu

### Cron không chạy

**Kiểm tra:**
```bash
# Kiểm tra cron service
sudo systemctl status cron

# Chạy thử thủ công
php artisan loans:process-repayments --dry-run

# Kiểm tra quyền
ls -la /var/www/binhan/storage/logs/
```

### Lịch trả nợ không chính xác

**Nguyên nhân:** Có thể do điều chỉnh lãi suất

**Kiểm tra:**
1. Xem lịch sử điều chỉnh lãi suất
2. Đối chiếu ngày hiệu lực
3. Các kỳ sau ngày hiệu lực sẽ có lãi suất mới

## Database Schema

### loan_profiles
```sql
- id
- vehicle_id (FK)
- cif
- contract_number
- bank_name
- principal_amount (decimal)
- term_months (int)
- total_periods (int)
- disbursement_date
- base_interest_rate (decimal)
- payment_day (1-28)
- status (active/paid_off/cancelled)
- remaining_balance (decimal)
- note
- created_by, updated_by
- timestamps
```

### loan_repayment_schedules
```sql
- id
- loan_id (FK)
- period_no
- due_date
- principal (decimal)
- interest (decimal)
- total (decimal)
- interest_rate (decimal)
- status (pending/paid/overdue)
- paid_date
- paid_amount (decimal)
- overdue_days (int)
- late_fee (decimal)
- transaction_id (FK)
- timestamps

INDEX: (loan_id, period_no) UNIQUE
INDEX: (due_date, status)
```

### loan_interest_adjustments
```sql
- id
- loan_id (FK)
- old_interest_rate (decimal)
- new_interest_rate (decimal)
- effective_date
- note
- created_by
- timestamps

INDEX: (loan_id, effective_date)
```

## API Documentation

### Endpoints

**POST /vehicles/{vehicle}/loans**
- Tạo khoản vay mới
- Body: cif, contract_number, bank_name, principal_amount, term_months, disbursement_date, base_interest_rate, payment_day, note

**PUT /loans/{loan}**
- Cập nhật thông tin khoản vay
- Body: cif, contract_number, bank_name, payment_day, note

**POST /loans/{loan}/adjust-interest**
- Điều chỉnh lãi suất
- Body: new_interest_rate, effective_date, note

**POST /loans/{loan}/pay-off**
- Trả nợ sớm
- Body: note

**DELETE /loans/{loan}**
- Xóa khoản vay (chỉ khi chưa có thanh toán)

## Best Practices

1. **Backup trước khi điều chỉnh lãi suất lớn**
2. **Kiểm tra log cron job định kỳ**
3. **Xác nhận số liệu với ngân hàng mỗi tháng**
4. **Không xóa giao dịch tự động bằng tay**
5. **Chạy --dry-run trước khi chạy lệnh quan trọng**

## Phát triển tương lai

- [ ] Cho phép thanh toán thủ công từng kỳ
- [ ] Tính phí trả chậm tự động
- [ ] Export báo cáo Excel/PDF
- [ ] Email/SMS nhắc nhở trước hạn thanh toán
- [ ] Dashboard tổng quan tất cả các khoản vay
- [ ] API cho mobile app
