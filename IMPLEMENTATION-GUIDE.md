# 🎯 GIẢI PHÁP HOÀN CHỈNH: Quản lý Edit Incident & Transactions

## ✅ ĐÃ HOÀN THÀNH

### 1. Database Migration
```sql
✅ transaction_category VARCHAR(50) - Phân loại giao dịch
✅ is_active BOOLEAN DEFAULT TRUE - Soft delete
✅ replaced_by INT - Link đến transaction mới
✅ edited_at TIMESTAMP - Thời gian sửa
✅ edited_by INT - Người sửa
```

### 2. Transaction Model Updated
```php
✅ Thêm fillable fields
✅ Thêm casts cho boolean và datetime
✅ Thêm relationships: editor(), replacedByTransaction()
✅ Thêm scope: active()
```

## 🔧 CODE CẦN CẬP NHẬT

### File: `app/Http/Controllers/IncidentController.php`

#### 1. Constants cho Transaction Categories
```php
// Thêm ở đầu class
class IncidentController extends Controller
{
    // Transaction categories
    const CATEGORY_REVENUE_MAIN = 'thu_chinh';
    const CATEGORY_EXPENSE_MAIN = 'chi_chinh';
    const CATEGORY_DRIVER_WAGE = 'tien_cong_lai_xe';
    const CATEGORY_MEDICAL_WAGE = 'tien_cong_nvyt';
    const CATEGORY_COMMISSION = 'hoa_hong';
    const CATEGORY_MAINTENANCE = 'bao_tri';
    const CATEGORY_SERVICE_ADDITIONAL = 'dich_vu_bo_sung';
    const CATEGORY_EXPENSE_ADDITIONAL = 'chi_phi_bo_sung';
```

#### 2. Helper Method: Soft Delete Transaction
```php
/**
 * Soft delete a transaction by replacing it
 */
private function replaceTransaction($oldTransaction, $newTransactionData)
{
    $newTransaction = Transaction::create($newTransactionData);
    
    $oldTransaction->update([
        'is_active' => false,
        'replaced_by' => $newTransaction->id,
        'edited_at' => now(),
        'edited_by' => auth()->id()
    ]);
    
    return $newTransaction;
}
```

#### 3. Update store() method - Thêm transaction_category

**Tìm và thay thế tất cả `Transaction::create()` trong store():**

```php
// DRIVER WAGES
Transaction::create([
    'incident_id' => $incident->id,
    'vehicle_id' => $validated['vehicle_id'],
    'staff_id' => $driver['staff_id'],
    'type' => 'chi',
    'transaction_category' => self::CATEGORY_DRIVER_WAGE, // ← THÊM
    'amount' => $detail['amount'],
    'method' => $validated['payment_method'],
    'recorded_by' => auth()->id(),
    'date' => $validated['date'],
    'note' => $detail['type'] . ' lái xe: ' . ($staffMember ? $staffMember->full_name : ''),
]);

// MEDICAL STAFF WAGES
Transaction::create([
    'incident_id' => $incident->id,
    'vehicle_id' => $validated['vehicle_id'],
    'staff_id' => $staff['staff_id'],
    'type' => 'chi',
    'transaction_category' => self::CATEGORY_MEDICAL_WAGE, // ← THÊM
    'amount' => $detail['amount'],
    'method' => $validated['payment_method'],
    'recorded_by' => auth()->id(),
    'date' => $validated['date'],
    'note' => $detail['type'] . ' NVYT: ' . ($staffMember ? $staffMember->full_name : ''),
]);

// MAIN REVENUE
Transaction::create([
    'incident_id' => $incident->id,
    'vehicle_id' => $validated['vehicle_id'],
    'type' => 'thu',
    'transaction_category' => self::CATEGORY_REVENUE_MAIN, // ← THÊM
    'amount' => $validated['amount_thu'],
    'method' => $validated['payment_method'],
    'recorded_by' => auth()->id(),
    'date' => $validated['date'],
    'note' => $validated['revenue_main_name'] ?? 'Thu chuyến đi',
]);

// MAIN EXPENSE
Transaction::create([
    'incident_id' => $incident->id,
    'vehicle_id' => $validated['vehicle_id'],
    'type' => 'chi',
    'transaction_category' => self::CATEGORY_EXPENSE_MAIN, // ← THÊM
    'amount' => $validated['amount_chi'],
    'method' => $validated['payment_method'],
    'recorded_by' => auth()->id(),
    'date' => $validated['date'],
    'note' => $validated['expense_main_name'] ?? 'Chi phí',
]);

// ADDITIONAL SERVICES
Transaction::create([
    'incident_id' => $incident->id,
    'vehicle_id' => $validated['vehicle_id'],
    'type' => 'thu',
    'transaction_category' => self::CATEGORY_SERVICE_ADDITIONAL, // ← THÊM
    'amount' => $service['amount'],
    'method' => $validated['payment_method'],
    'recorded_by' => auth()->id(),
    'date' => $validated['date'],
    'note' => $service['name'],
]);

// ADDITIONAL EXPENSES
Transaction::create([
    'incident_id' => $incident->id,
    'vehicle_id' => $validated['vehicle_id'],
    'type' => 'chi',
    'transaction_category' => self::CATEGORY_EXPENSE_ADDITIONAL, // ← THÊM
    'amount' => $expense['amount'],
    'method' => $validated['payment_method'],
    'recorded_by' => auth()->id(),
    'date' => $validated['date'],
    'note' => $expense['name'],
]);

// COMMISSION
Transaction::create([
    'incident_id' => $incident->id,
    'vehicle_id' => $validated['vehicle_id'],
    'type' => 'chi',
    'transaction_category' => self::CATEGORY_COMMISSION, // ← THÊM
    'amount' => $validated['commission_amount'],
    'method' => $validated['payment_method'],
    'recorded_by' => auth()->id(),
    'date' => $validated['date'],
    'note' => 'Hoa hồng: ' . ($partner ? $partner->name : 'Đối tác'),
]);

// MAINTENANCE
Transaction::create([
    'incident_id' => $incident->id,
    'vehicle_id' => $validated['vehicle_id'],
    'type' => 'chi',
    'transaction_category' => self::CATEGORY_MAINTENANCE, // ← THÊM
    'amount' => $validated['maintenance_cost'],
    'method' => $validated['payment_method'],
    'recorded_by' => auth()->id(),
    'date' => $validated['date'],
    'note' => 'Bảo trì: ' . ($service ? $service->name : ''),
]);
```

#### 4. HOÀN TOÀN VIẾT LẠI update() method

```php
public function update(Request $request, Incident $incident)
{
    $validated = $request->validate([...]);

    try {
        DB::beginTransaction();

        // 1. Update locations (case-insensitive)
        $fromLocationId = null;
        $toLocationId = null;
        
        if (!empty($validated['from_location'])) {
            $normalizedName = trim($validated['from_location']);
            $location = \App\Models\Location::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)])
                ->first();
            
            if (!$location) {
                $location = \App\Models\Location::create([
                    'name' => $normalizedName,
                    'type' => 'from',
                    'is_active' => true
                ]);
            }
            
            $fromLocationId = $location->id;
        }
        
        if (!empty($validated['to_location'])) {
            $normalizedName = trim($validated['to_location']);
            $location = \App\Models\Location::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)])
                ->first();
            
            if (!$location) {
                $location = \App\Models\Location::create([
                    'name' => $normalizedName,
                    'type' => 'to',
                    'is_active' => true
                ]);
            }
            
            $toLocationId = $location->id;
        }

        // 2. Update incident basic info
        $incident->update([
            'vehicle_id' => $validated['vehicle_id'],
            'patient_id' => $validated['patient_id'],
            'date' => $validated['date'],
            'from_location_id' => $fromLocationId,
            'to_location_id' => $toLocationId,
            'partner_id' => $validated['partner_id'] ?? null,
            'commission_amount' => $validated['commission_amount'] ?? null,
            'summary' => $validated['summary'],
            'tags' => $validated['tags'] ?? null,
        ]);

        // 3. SYNC STAFF & WAGES (SOFT DELETE OLD + CREATE NEW)
        
        // Remove all existing staff
        $incident->staff()->detach();
        
        // Soft delete old wage transactions
        Transaction::where('incident_id', $incident->id)
            ->active()
            ->whereIn('transaction_category', [self::CATEGORY_DRIVER_WAGE, self::CATEGORY_MEDICAL_WAGE])
            ->update([
                'is_active' => false,
                'edited_at' => now(),
                'edited_by' => auth()->id()
            ]);
        
        // Recreate drivers with wages
        if (!empty($validated['drivers'])) {
            foreach ($validated['drivers'] as $driver) {
                if (!empty($driver['staff_id'])) {
                    $wages = $driver['wages'] ?? [];
                    $totalWage = 0;
                    $wageDetails = [];
                    
                    foreach ($wages as $wage) {
                        if (!empty($wage['amount']) && $wage['amount'] > 0) {
                            $totalWage += $wage['amount'];
                            $wageDetails[] = [
                                'type' => $wage['type'] ?? 'Công',
                                'amount' => $wage['amount']
                            ];
                        }
                    }
                    
                    $incident->staff()->attach($driver['staff_id'], [
                        'role' => 'driver',
                        'wage_amount' => $totalWage,
                        'wage_details' => !empty($wageDetails) ? json_encode($wageDetails) : null
                    ]);

                    // Create new wage transactions
                    if (!empty($wageDetails)) {
                        $staffMember = \App\Models\Staff::find($driver['staff_id']);
                        foreach ($wageDetails as $detail) {
                            Transaction::create([
                                'incident_id' => $incident->id,
                                'vehicle_id' => $validated['vehicle_id'],
                                'staff_id' => $driver['staff_id'],
                                'type' => 'chi',
                                'transaction_category' => self::CATEGORY_DRIVER_WAGE,
                                'amount' => $detail['amount'],
                                'method' => $validated['payment_method'],
                                'recorded_by' => auth()->id(),
                                'date' => $validated['date'],
                                'note' => $detail['type'] . ' lái xe: ' . ($staffMember ? $staffMember->full_name : ''),
                            ]);
                        }
                    }
                }
            }
        }
        
        // Recreate medical staff with wages  
        if (!empty($validated['medical_staff'])) {
            foreach ($validated['medical_staff'] as $staff) {
                if (!empty($staff['staff_id'])) {
                    $wages = $staff['wages'] ?? [];
                    $totalWage = 0;
                    $wageDetails = [];
                    
                    foreach ($wages as $wage) {
                        if (!empty($wage['amount']) && $wage['amount'] > 0) {
                            $totalWage += $wage['amount'];
                            $wageDetails[] = [
                                'type' => $wage['type'] ?? 'Công',
                                'amount' => $wage['amount']
                            ];
                        }
                    }
                    
                    $incident->staff()->attach($staff['staff_id'], [
                        'role' => 'medical_staff',
                        'wage_amount' => $totalWage,
                        'wage_details' => !empty($wageDetails) ? json_encode($wageDetails) : null
                    ]);

                    // Create new wage transactions
                    if (!empty($wageDetails)) {
                        $staffMember = \App\Models\Staff::find($staff['staff_id']);
                        foreach ($wageDetails as $detail) {
                            Transaction::create([
                                'incident_id' => $incident->id,
                                'vehicle_id' => $validated['vehicle_id'],
                                'staff_id' => $staff['staff_id'],
                                'type' => 'chi',
                                'transaction_category' => self::CATEGORY_MEDICAL_WAGE,
                                'amount' => $detail['amount'],
                                'method' => $validated['payment_method'],
                                'recorded_by' => auth()->id(),
                                'date' => $validated['date'],
                                'note' => $detail['type'] . ' NVYT: ' . ($staffMember ? $staffMember->full_name : ''),
                            ]);
                        }
                    }
                }
            }
        }

        // 4. UPDATE MAIN REVENUE (SOFT DELETE + CREATE)
        $oldRevenue = Transaction::where('incident_id', $incident->id)
            ->active()
            ->where('transaction_category', self::CATEGORY_REVENUE_MAIN)
            ->first();
        
        if (!empty($validated['amount_thu']) && $validated['amount_thu'] > 0) {
            $newRevenueData = [
                'incident_id' => $incident->id,
                'vehicle_id' => $validated['vehicle_id'],
                'type' => 'thu',
                'transaction_category' => self::CATEGORY_REVENUE_MAIN,
                'amount' => $validated['amount_thu'],
                'method' => $validated['payment_method'],
                'recorded_by' => auth()->id(),
                'date' => $validated['date'],
                'note' => $validated['revenue_main_name'] ?? 'Thu chuyến đi',
            ];
            
            if ($oldRevenue) {
                $this->replaceTransaction($oldRevenue, $newRevenueData);
            } else {
                Transaction::create($newRevenueData);
            }
        } elseif ($oldRevenue) {
            // If no revenue but old exists, soft delete
            $oldRevenue->update([
                'is_active' => false,
                'edited_at' => now(),
                'edited_by' => auth()->id()
            ]);
        }

        // 5. UPDATE MAIN EXPENSE (SOFT DELETE + CREATE)
        $oldExpense = Transaction::where('incident_id', $incident->id)
            ->active()
            ->where('transaction_category', self::CATEGORY_EXPENSE_MAIN)
            ->first();
        
        if (!empty($validated['amount_chi']) && $validated['amount_chi'] > 0) {
            $newExpenseData = [
                'incident_id' => $incident->id,
                'vehicle_id' => $validated['vehicle_id'],
                'type' => 'chi',
                'transaction_category' => self::CATEGORY_EXPENSE_MAIN,
                'amount' => $validated['amount_chi'],
                'method' => $validated['payment_method'],
                'recorded_by' => auth()->id(),
                'date' => $validated['date'],
                'note' => $validated['expense_main_name'] ?? 'Chi phí',
            ];
            
            if ($oldExpense) {
                $this->replaceTransaction($oldExpense, $newExpenseData);
            } else {
                Transaction::create($newExpenseData);
            }
        } elseif ($oldExpense) {
            $oldExpense->update([
                'is_active' => false,
                'edited_at' => now(),
                'edited_by' => auth()->id()
            ]);
        }

        // 6. UPDATE COMMISSION (SOFT DELETE + CREATE)
        $oldCommission = Transaction::where('incident_id', $incident->id)
            ->active()
            ->where('transaction_category', self::CATEGORY_COMMISSION)
            ->first();

        if (!empty($validated['partner_id']) && !empty($validated['commission_amount']) && $validated['commission_amount'] > 0) {
            $partner = \App\Models\Partner::find($validated['partner_id']);
            $newCommissionData = [
                'incident_id' => $incident->id,
                'vehicle_id' => $validated['vehicle_id'],
                'type' => 'chi',
                'transaction_category' => self::CATEGORY_COMMISSION,
                'amount' => $validated['commission_amount'],
                'method' => 'cash',
                'recorded_by' => auth()->id(),
                'date' => $validated['date'],
                'note' => 'Hoa hồng: ' . ($partner ? $partner->name : 'Đối tác'),
            ];
            
            if ($oldCommission) {
                $this->replaceTransaction($oldCommission, $newCommissionData);
            } else {
                Transaction::create($newCommissionData);
            }
        } elseif ($oldCommission) {
            $oldCommission->update([
                'is_active' => false,
                'edited_at' => now(),
                'edited_by' => auth()->id()
            ]);
        }

        // 7. UPDATE ADDITIONAL SERVICES (DELETE ALL OLD + CREATE NEW)
        Transaction::where('incident_id', $incident->id)
            ->active()
            ->where('transaction_category', self::CATEGORY_SERVICE_ADDITIONAL)
            ->update([
                'is_active' => false,
                'edited_at' => now(),
                'edited_by' => auth()->id()
            ]);
        
        if (!empty($validated['additional_services'])) {
            foreach ($validated['additional_services'] as $service) {
                if (!empty($service['name']) && !empty($service['amount'])) {
                    Transaction::create([
                        'incident_id' => $incident->id,
                        'vehicle_id' => $validated['vehicle_id'],
                        'type' => 'thu',
                        'transaction_category' => self::CATEGORY_SERVICE_ADDITIONAL,
                        'amount' => $service['amount'],
                        'method' => $validated['payment_method'],
                        'recorded_by' => auth()->id(),
                        'date' => $validated['date'],
                        'note' => $service['name'],
                    ]);
                }
            }
        }

        // 8. UPDATE ADDITIONAL EXPENSES (DELETE ALL OLD + CREATE NEW)
        Transaction::where('incident_id', $incident->id)
            ->active()
            ->where('transaction_category', self::CATEGORY_EXPENSE_ADDITIONAL)
            ->update([
                'is_active' => false,
                'edited_at' => now(),
                'edited_by' => auth()->id()
            ]);
        
        if (!empty($validated['additional_expenses'])) {
            foreach ($validated['additional_expenses'] as $expense) {
                if (!empty($expense['name']) && !empty($expense['amount'])) {
                    Transaction::create([
                        'incident_id' => $incident->id,
                        'vehicle_id' => $validated['vehicle_id'],
                        'type' => 'chi',
                        'transaction_category' => self::CATEGORY_EXPENSE_ADDITIONAL,
                        'amount' => $expense['amount'],
                        'method' => $validated['payment_method'],
                        'recorded_by' => auth()->id(),
                        'date' => $validated['date'],
                        'note' => $expense['name'],
                    ]);
                }
            }
        }

        // 9. UPDATE MAINTENANCE (SOFT DELETE + CREATE)
        // (Tương tự như revenue/expense)

        DB::commit();

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Đã cập nhật chuyến đi thành công!');

    } catch (\Exception $e) {
        DB::rollBack();
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    }
}
```

## 🔄 CẬP NHẬT QUERIES - CHỈ LẤY ACTIVE TRANSACTIONS

Tất cả queries lấy transactions cần thêm `->active()`:

```php
// BEFORE
$vehicle->transactions()->sum('amount');

// AFTER
$vehicle->transactions()->active()->sum('amount');

// BEFORE  
Transaction::where('incident_id', $id)->get();

// AFTER
Transaction::where('incident_id', $id)->active()->get();
```

## 📊 BACKFILL DATA - GẮN CATEGORY CHO TRANSACTIONS CŨ

Tạo script `backfill-transaction-categories.php`:

```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

DB::beginTransaction();

try {
    // Update wage transactions
    DB::table('transactions')
        ->whereNotNull('staff_id')
        ->whereNotNull('incident_id')
        ->where(function($q) {
            $q->where('note', 'LIKE', '%lái xe%')
              ->orWhere('note', 'LIKE', '%Tiền công:%');
        })
        ->update(['transaction_category' => 'tien_cong_lai_xe']);
    
    DB::table('transactions')
        ->whereNotNull('staff_id')
        ->whereNotNull('incident_id')
        ->where(function($q) {
            $q->where('note', 'LIKE', '%NVYT%')
              ->orWhere('note', 'LIKE', '%y tế%');
        })
        ->update(['transaction_category' => 'tien_cong_nvyt']);
    
    // Update commission
    DB::table('transactions')
        ->whereNotNull('incident_id')
        ->where('note', 'LIKE', 'Hoa hồng:%')
        ->update(['transaction_category' => 'hoa_hong']);
    
    // Update maintenance
    DB::table('transactions')
        ->whereNotNull('vehicle_maintenance_id')
        ->update(['transaction_category' => 'bao_tri']);
    
    // Update main revenue
    DB::table('transactions')
        ->whereNotNull('incident_id')
        ->where('type', 'thu')
        ->whereNull('staff_id')
        ->where(function($q) {
            $q->where('note', 'Thu chuyến đi')
              ->orWhere('note', 'LIKE', 'Thu%');
        })
        ->update(['transaction_category' => 'thu_chinh']);
    
    DB::commit();
    echo "✅ Backfill completed!\n";
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
}
```

## ✅ CHECKLIST TRIỂN KHAI

- [x] Database migration
- [x] Update Transaction model
- [ ] Add constants to IncidentController
- [ ] Add replaceTransaction() helper method
- [ ] Update store() - add transaction_category
- [ ] Rewrite update() - full implementation
- [ ] Backfill existing data
- [ ] Update all queries to use ->active()
- [ ] Test edit scenarios
- [ ] Document for team

