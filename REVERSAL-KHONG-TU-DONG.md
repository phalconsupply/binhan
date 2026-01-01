# LÀM RÕ: REVERSAL KHÔNG TỰ ĐỘNG - NGƯỜI DÙNG CHỌN

## ❌ HIỂU NHẦM PHỔ BIẾN

```
User nghĩ sai:
┌─────────────────────────────────────────────────┐
│ Tôi click nút [XÓA] giao dịch GD20251218-0694  │
│           ↓                                     │
│ Hệ thống TỰ ĐỘNG tạo giao dịch đảo ngược       │
│           ↓                                     │
│ Có 2 giao dịch: gốc + reversal                 │
└─────────────────────────────────────────────────┘
```

## ✅ THỰC TẾ

```
Thực tế:
┌─────────────────────────────────────────────────────────┐
│ Người dùng phát hiện giao dịch GD20251218-0694         │
│           ↓                                             │
│ Người dùng CHỌN 1 trong 4 hành động:                   │
│  [1] REVERSAL     (tạo đảo ngược)                      │
│  [2] SOFT DELETE  (xóa mềm)                            │
│  [3] REPLACEMENT  (thay thế)                           │
│  [4] KHÔNG LÀM GÌ (giữ nguyên)                         │
│           ↓                                             │
│ Hệ thống thực hiện ĐÚNG THEO lựa chọn                  │
└─────────────────────────────────────────────────────────┘
```

---

## 🎯 4 LỰA CHỌN CHI TIẾT

### LỰA CHỌN 1: REVERSAL (Đảo ngược)

**Khi nào:** Giao dịch đã ghi ĐÚNG nhưng cần HỦY BỎ

**Ví dụ thực tế:**
```
Tình huống: 
- Ngày 18/12: Chi 360,000đ mua hàng từ nhà cung cấp A
- Ngày 20/12: Nhà cung cấp A hủy đơn hàng, hoàn tiền
- Cần ghi lại việc hoàn tiền này

Sai lầm: Xóa giao dịch ngày 18/12
→ Mất audit trail, không biết đã có giao dịch này

Đúng: Reversal giao dịch ngày 18/12
→ Giữ nguyên giao dịch gốc + tạo giao dịch đảo ngược
→ Audit trail đầy đủ: "Đã chi → đã hoàn"
```

**Workflow:**
```
User:     Vào giao dịch GD20251218-0694
          ↓
User:     Click nút [ĐẢO NGƯỢC] (KHÔNG phải nút [XÓA])
          ↓
User:     Nhập lý do: "Nhà cung cấp hủy đơn, hoàn tiền"
          ↓
User:     Click [XÁC NHẬN]
          ↓
System:   Tạo giao dịch REV20260101174800
          - Type: THU (ngược lại)
          - Amount: 360,000đ
          - From: Bên ngoài → Quỹ công ty
          - Note: "ĐẢONGU: GD20251218-0694 - Nhà cung cấp hủy đơn"
          ↓
System:   Cập nhật GD20251218-0694
          - lifecycle_status = "reversed"
          - reversed_by_transaction_id = 810
          ↓
Result:   Database có 2 giao dịch:
          - GD20251218-0694: CHI 360k (reversed)
          - REV20260101174800: THU 360k (active)
          Tổng impact: 0đ
```

**Command:**
```bash
php artisan transaction:reverse GD20251218-0694 "Nhà cung cấp hủy đơn"
```

---

### LỰA CHỌN 2: SOFT DELETE (Xóa mềm)

**Khi nào:** Giao dịch NHẬP NHẦM, CHƯA XẢY RA thực tế

**Ví dụ thực tế:**
```
Tình huống:
- Nhân viên nhập giao dịch: Chi 360,000đ
- Nhưng thực tế KHÔNG CÓ giao dịch này
- Nhân viên nhập nhầm hoặc duplicate

Sai lầm: Dùng Reversal
→ Tạo 2 giao dịch cho việc KHÔNG TỒN TẠI
→ Làm phức tạp sổ sách

Đúng: Soft Delete
→ Ẩn giao dịch nhập nhầm
→ Có thể restore nếu cần
```

**Workflow:**
```
User:     Vào giao dịch GD20251218-0694
          ↓
User:     Click nút [XÓA]
          ↓
User:     Nhập lý do: "Nhập nhầm, không có giao dịch thực tế"
          ↓
User:     Click [XÁC NHẬN]
          ↓
System:   Cập nhật GD20251218-0694
          - lifecycle_status = "cancelled"
          - deleted_at = 2026-01-02 18:00:00
          ↓
System:   KHÔNG TẠO giao dịch đảo ngược
          ↓
System:   Ẩn khỏi danh sách giao dịch
          ↓
Result:   - Giao dịch bị ẩn (soft deleted)
          - Có thể restore nếu phát hiện xóa nhầm
          - Số dư như chưa có giao dịch này
```

**Code:**
```php
$service->softDeleteTransaction($transaction, "Nhập nhầm");
```

---

### LỰA CHỌN 3: REPLACEMENT (Thay thế)

**Khi nào:** Giao dịch CÓ XẢY RA nhưng GHI SAI thông tin

**Ví dụ thực tế:**
```
Tình huống:
- Ghi: Chi 360,000đ cho nhà cung cấp A
- Hóa đơn thực tế: 320,000đ
- Cần sửa số tiền cho đúng

Sai lầm: Xóa + tạo mới
→ Mất liên kết giữa giao dịch cũ và mới
→ Không biết đã sửa gì

Đúng: Replacement
→ Giữ giao dịch cũ (marked as replaced)
→ Tạo giao dịch mới (active)
→ Biết được lịch sử sửa
```

**Workflow:**
```
User:     Vào giao dịch GD20251218-0694
          ↓
User:     Click nút [THAY THẾ]
          ↓
User:     Nhập dữ liệu đúng:
          - Amount: 320,000đ (thay vì 360,000đ)
          ↓
User:     Nhập lý do: "Sửa theo hóa đơn thực tế"
          ↓
User:     Click [XÁC NHẬN]
          ↓
System:   Tạo giao dịch mới GD20260102-0815
          - Type: CHI
          - Amount: 320,000đ
          - lifecycle_status = "active"
          ↓
System:   Cập nhật GD20251218-0694
          - lifecycle_status = "replaced"
          - replaced_by = 815
          ↓
Result:   - Giao dịch cũ: ẩn khỏi báo cáo
          - Giao dịch mới: dùng cho báo cáo
          - Biết được đã sửa gì
```

**Code:**
```php
$service->replaceTransaction($old, [
    'amount' => 320000
], "Sửa theo hóa đơn thực tế");
```

---

### LỰA CHỌN 4: KHÔNG LÀM GÌ

**Khi nào:** Giao dịch ĐÚNG, không có vấn đề

**Workflow:**
```
User:     Kiểm tra giao dịch GD20251218-0694
          ↓
User:     Xác nhận giao dịch đúng
          ↓
User:     Đóng màn hình
          ↓
Result:   Không có thay đổi
```

---

## 📊 BẢNG SO SÁNH

| Tiêu chí | Reversal | Soft Delete | Replacement | Không làm gì |
|----------|----------|-------------|-------------|--------------|
| **Giao dịch đã xảy ra?** | ✅ Có | ❌ Không | ✅ Có | ✅ Có |
| **Cần hủy bỏ?** | ✅ Có | ✅ Có | ❌ Không (chỉ sửa) | ❌ Không |
| **Tạo giao dịch mới?** | ✅ Đảo ngược | ❌ Không | ✅ Giao dịch đúng | ❌ Không |
| **Giữ giao dịch gốc?** | ✅ Có (reversed) | ⚠️ Có (cancelled) | ✅ Có (replaced) | ✅ Có (active) |
| **Audit trail?** | ✅ Đầy đủ | ⚠️ Có (nhưng ẩn) | ✅ Đầy đủ | ✅ Đầy đủ |
| **Tổng impact** | 0đ | -(số tiền gốc) | Khác gốc | Giữ nguyên |

---

## 🎯 DECISION TREE

```
Bạn cần xử lý giao dịch GD20251218-0694?
│
├─ Giao dịch ĐÚNG?
│  └─ YES → KHÔNG LÀM GÌ
│
├─ Giao dịch ĐÃ XẢY RA trong thực tế?
│  │
│  ├─ YES → Cần hủy bỏ?
│  │  │
│  │  ├─ YES → REVERSAL (tạo đảo ngược)
│  │  │
│  │  └─ NO → Có thông tin sai?
│  │     │
│  │     ├─ YES → REPLACEMENT (thay thế)
│  │     │
│  │     └─ NO → KHÔNG LÀM GÌ
│  │
│  └─ NO (nhập nhầm) → SOFT DELETE (xóa mềm)
```

---

## 💡 VÍ DỤ CỤ THỂ

### Tình huống 1: Hủy hóa đơn
```
Sự kiện:
18/12: Chi 360k mua hàng từ nhà cung cấp A ✅ GHI
20/12: NCC A hủy đơn, hoàn 360k

Xử lý:
→ REVERSAL giao dịch 18/12
→ Lý do: "NCC hủy đơn, hoàn tiền"
→ Kết quả:
  - GD 18/12: CHI 360k (reversed)
  - REV 20/12: THU 360k (active)
  - Tổng: 0đ
  - Audit: Biết đã chi rồi hoàn
```

### Tình huống 2: Nhập nhầm
```
Sự kiện:
18/12: Nhân viên nhập: Chi 360k
Thực tế: KHÔNG CÓ giao dịch này (duplicate)

Xử lý:
→ SOFT DELETE giao dịch 18/12
→ Lý do: "Duplicate, nhập nhầm"
→ Kết quả:
  - GD 18/12: Ẩn (deleted_at set)
  - Không có reversal
  - Số dư như chưa có GD này
```

### Tình huống 3: Sai số tiền
```
Sự kiện:
18/12: Ghi Chi 360k cho NCC A
Hóa đơn thực: 320k (sai 40k)

Xử lý:
→ REPLACEMENT giao dịch 18/12
→ Dữ liệu mới: amount = 320k
→ Lý do: "Sửa theo HĐ thực tế"
→ Kết quả:
  - GD cũ 18/12: CHI 360k (replaced, ẩn)
  - GD mới 02/01: CHI 320k (active, hiện)
  - Biết đã sửa từ 360k → 320k
```

---

## ⚠️ LƯU Ý QUAN TRỌNG

### 1. Reversal ≠ Xóa
```
[ĐẢO NGƯỢC] button:  Tạo giao dịch đối nghịch, giữ nguyên gốc
[XÓA] button:        Ẩn giao dịch, không tạo đối nghịch

→ Là 2 HÀNH ĐỘNG HOÀN TOÀN KHÁC NHAU!
```

### 2. Hệ thống KHÔNG tự động
```
❌ Sai: Click [XÓA] → Hệ thống tạo reversal
✅ Đúng: Click [ĐẢO NGƯỢC] → Hệ thống tạo reversal
```

### 3. UI cần có 2 nút riêng
```
Màn hình giao dịch cần có:
┌─────────────────────────────────┐
│ [ĐẢO NGƯỢC]  [THAY THẾ]  [XÓA] │
└─────────────────────────────────┘
   ↓              ↓          ↓
Reversal    Replacement  Soft Delete
```

---

## 🔧 IMPLEMENTATION CHO UI

### Button [ĐẢO NGƯỢC]
```javascript
// Vue/Blade
<button @click="reverseTransaction">
  ĐẢO NGƯỢC
</button>

// Action
async reverseTransaction() {
  const reason = prompt("Lý do đảo ngược:");
  if (!reason) return;
  
  await axios.post('/api/transactions/reverse', {
    transaction_id: this.transaction.id,
    reason: reason
  });
  
  alert("Đã tạo giao dịch đảo ngược!");
}
```

### Button [XÓA]
```javascript
<button @click="deleteTransaction">
  XÓA
</button>

async deleteTransaction() {
  const reason = prompt("Lý do xóa:");
  if (!reason) return;
  
  if (!confirm("Xác nhận XÓA (không tạo reversal)?")) {
    return;
  }
  
  await axios.post('/api/transactions/soft-delete', {
    transaction_id: this.transaction.id,
    reason: reason
  });
  
  alert("Đã xóa giao dịch!");
}
```

### Button [THAY THẾ]
```javascript
<button @click="showReplaceForm">
  THAY THẾ
</button>

async replaceTransaction(newData) {
  const reason = prompt("Lý do thay thế:");
  if (!reason) return;
  
  await axios.post('/api/transactions/replace', {
    transaction_id: this.transaction.id,
    new_data: newData,
    reason: reason
  });
  
  alert("Đã tạo giao dịch thay thế!");
}
```

---

## ✅ KẾT LUẬN

**5 điểm cốt lõi:**

1. **HỆ THỐNG KHÔNG TỰ ĐỘNG** - Người dùng chủ động chọn
2. **REVERSAL ≠ XÓA** - Là 2 hành động hoàn toàn khác nhau
3. **MỖI TÌNH HUỐNG CÓ GIẢI PHÁP RIÊNG** - Không có one-size-fits-all
4. **UI PHẢI RÕ RÀNG** - 3 buttons riêng biệt cho 3 actions
5. **AUDIT TRAIL LÀ QUAN TRỌNG** - Giữ lại lịch sử thay đổi

**Workflow đúng:**
```
User phát hiện vấn đề
   ↓
User phân tích tình huống
   ↓
User CHỌN action phù hợp
   ↓
User thực hiện action
   ↓
System xử lý THEO LỰA CHỌN của user
```

**❌ KHÔNG BAO GIỜ tự động tạo reversal khi user click [XÓA]!**
