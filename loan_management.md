# Loan Management Module Specification

## 1. Mục tiêu

Xây dựng module quản lý **lịch trả nợ cho xe trả góp**, cho phép: - Khai
báo khoản vay cho từng xe. - Tự động sinh lịch trả nợ theo tham số. - Tự
động trừ lợi nhuận hàng tháng theo lịch trả nợ. - Tự động tạo giao dịch
chi phí tương ứng vào module `/transactions`. - Hỗ trợ thay đổi lãi suất
theo từng đợt và vẫn bảo toàn lịch trả nợ đã phát sinh.

## 2. Các trường thông tin trong Form Khai Báo Khoản Vay (Loan Profile)

  -----------------------------------------------------------------------
  Trường             Kiểu nhập                    Ghi chú
  ------------------ ---------------------------- -----------------------
  **Xe trả nợ**      Select từ danh sách xe       Liên kết theo
                                                  `vehicle_id`

  **CIF**            Input text                   Mã hồ sơ khách hàng
                                                  ngân hàng

  **Hợp đồng số**    Input text                   Số hợp đồng tín dụng

  **Số tiền vay**    Number                       Tổng tiền giải ngân

  **Kỳ hạn (tháng)** Number                       Tổng số tháng vay

  **Số tháng của 1   Number                       VD: 1 = trả hàng tháng,
  kỳ trả nợ**                                     3 = trả theo quý

  **Số kỳ trả nợ**   Number                       Auto gợi ý = Kỳ hạn /
                                                  Số tháng/kỳ

  **Ngày giải ngân** Date                         Dùng để bắt đầu tính
                                                  lãi

  **Lãi suất theo    Number                       Lãi suất ban đầu
  hợp đồng (%)**                                  

  **Ngày trả nợ hàng Day (1--31)                  Ngày cố định trong
  tháng**                                         tháng

  **Lãi suất điều    Number                       Không bắt buộc
  chỉnh (%)**                                     

  **Ngày điều chỉnh  Date                         Nếu có nhiều lần điều
  lãi suất**                                      chỉnh → cần UI riêng
  -----------------------------------------------------------------------

## 3. Quy tắc Tính Lịch Trả Nợ (Repayment Schedule)

### 3.1. Tổng quan lịch trả nợ

Khi khai báo hồ sơ vay → hệ thống sẽ tính bảng lịch trả nợ gồm:

-   Kỳ số
-   Ngày phải trả
-   Số tiền gốc kỳ này
-   Số tiền lãi kỳ này
-   Tổng phải trả
-   Lãi suất áp dụng
-   Trạng thái (`pending|paid`)
-   Transaction ID (nếu đã trừ tiền)

### 3.2. Công thức tính

    Lãi kỳ = (Dư nợ hiện tại × Lãi suất theo năm / 12)
    Gốc kỳ = Tổng tiền vay / Số kỳ trả nợ
    Tổng kỳ = Gốc kỳ + Lãi kỳ

### 3.3. Lãi suất điều chỉnh

-   Chỉ áp dụng từ kỳ có ngày điều chỉnh.
-   Các kỳ trước giữ nguyên.
-   Lưu thông tin "đợt điều chỉnh" để hiển thị UI.

## 4. Tự Động Trừ Lợi Nhuận & Tạo Giao Dịch

### 4.1. Thời điểm thực thi

Cron job chạy **01:00 hàng ngày**.

### 4.2. Khi thực hiện trừ tiền

Hệ thống sẽ: 1. Trừ tiền từ tài khoản lợi nhuận của xe. 2. Ghi log lịch
trả nợ (`status = paid`). 3. Tạo giao dịch trong `/transactions`.

### 4.3. Không đủ tiền

-   Đánh dấu `overdue`.
-   Gửi cảnh báo.

## 5. UI/UX Bổ Sung Tại Trang Chi Tiết Xe

### 5.1. Bảng Lịch Trả Nợ

Gồm cột: Kỳ \| Ngày trả \| Gốc \| Lãi \| Tổng \| Lãi suất \| Trạng thái
\| Hành động

### 5.2. Lịch Sử Điều Chỉnh

Gồm: ngày áp dụng, lãi suất cũ → mới, ghi chú.

## 6. Cấu Trúc Bảng Dữ Liệu

### 6.1. loan_profiles

-   id\
-   vehicle_id\
-   CIF\
-   contract_number\
-   principal_amount\
-   term_months\
-   months_per_period\
-   total_periods\
-   disbursement_date\
-   base_interest_rate\
-   payment_day

### 6.2. loan_interest_adjustments

-   id\
-   loan_id\
-   new_interest_rate\
-   effective_date\
-   note

### 6.3. loan_repayment_schedule

-   id\
-   loan_id\
-   period_no\
-   due_date\
-   principal\
-   interest\
-   total\
-   interest_rate\
-   status\
-   transaction_id

## 7. API Endpoints

-   POST /loan-profile\
-   POST /loan-profile/{id}/adjust-interest\
-   GET /vehicle/{id}/loan-schedule

## 8. Cron Hàng Tháng

1.  Lấy kỳ đến hạn.\
2.  Trừ tiền lợi nhuận xe.\
3.  Tạo transaction.\
4.  Đánh dấu paid.
