<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Thêm chuyến đi mới
            </h2>
            <a href="{{ route('incidents.index') }}" class="text-indigo-600 hover:text-indigo-900">
                ← Quay lại danh sách
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-2">Thông tin chuyến đi</h3>
                    <p class="text-xs text-gray-500 mb-4">📌 ID chuyến đi sẽ được tạo tự động sau khi lưu</p>
                    
                    <form id="incident-form" method="POST" action="{{ route('incidents.store') }}" class="space-y-4">
                        @csrf

                        {{-- 1. Thông tin xe --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-2">🚗 Thông tin xe</h4>
                            <label for="vehicle_id" class="block text-xs font-medium text-gray-700">Biển số xe <span class="text-red-500">*</span></label>
                            <select id="vehicle_id" name="vehicle_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">-- Chọn xe --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->license_plate }} @if($vehicle->driver_name) - {{ $vehicle->driver_name }} @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <label for="date" class="block text-xs font-medium text-gray-700 mt-2">Ngày giờ <span class="text-red-500">*</span></label>
                            <input type="datetime-local" id="date" name="date" value="{{ old('date', now()->format('Y-m-d\TH:i')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <input type="text" 
                                    id="from_location" 
                                    name="from_location" 
                                    list="from_locations_list"
                                    value="{{ old('from_location') }}" 
                                    placeholder="Nơi đi..."
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <datalist id="from_locations_list">
                                    @foreach(\App\Models\Location::active()->whereIn('type', ['from', 'both'])->orderBy('name')->get() as $location)
                                        <option value="{{ $location->name }}">
                                    @endforeach
                                </datalist>

                                <input type="text" 
                                    id="to_location" 
                                    name="to_location" 
                                    list="to_locations_list"
                                    value="{{ old('to_location') }}" 
                                    placeholder="Nơi đến..."
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <datalist id="to_locations_list">
                                    @foreach(\App\Models\Location::active()->whereIn('type', ['to', 'both'])->orderBy('name')->get() as $location)
                                        <option value="{{ $location->name }}">
                                    @endforeach
                                </datalist>
                            </div>
                        </div>

                        {{-- 2. Thông tin bệnh nhân --}}
                        <details class="border-t pt-3" open>
                            <summary class="text-sm font-semibold text-gray-800 cursor-pointer hover:text-gray-900">🏥 Thông tin bệnh nhân</summary>
                            <div class="mt-2 space-y-2">
                                <select id="patient_id" name="patient_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">-- Tạo mới --</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->name }} @if($patient->phone) - {{ $patient->phone }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div id="new_patient_fields">
                                    <input type="text" name="patient_name" placeholder="Tên bệnh nhân" value="{{ old('patient_name') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm capitalize">
                                    <input type="text" name="patient_phone" placeholder="Số điện thoại" value="{{ old('patient_phone') }}" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <div class="grid grid-cols-2 gap-2 mt-2">
                                        <input type="number" name="patient_birth_year" placeholder="Năm sinh" min="1900" max="{{ date('Y') }}" value="{{ old('patient_birth_year') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <select name="patient_gender" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="">Giới tính</option>
                                            <option value="male" {{ old('patient_gender') == 'male' ? 'selected' : '' }}>Nam</option>
                                            <option value="female" {{ old('patient_gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                                            <option value="other" {{ old('patient_gender') == 'other' ? 'selected' : '' }}>Khác</option>
                                        </select>
                                    </div>
                                    <textarea name="patient_address" rows="2" placeholder="Địa chỉ" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('patient_address') }}</textarea>
                                </div>
                            </div>
                        </details>

                        {{-- 3. Thông tin nhân sự --}}
                        <div class="border-t pt-3">
                            <h4 class="text-sm font-semibold text-gray-800 mb-2">👥 Thông tin nhân sự & Tiền công</h4>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Lái xe</label>
                                    <div id="drivers-container" class="space-y-2">
                                        <div class="driver-item flex gap-2">
                                            <select name="drivers[0][staff_id]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <option value="">-- Chọn lái xe --</option>
                                                @foreach(\App\Models\Staff::active()->where('staff_type', 'driver')->orderBy('full_name')->get() as $driver)
                                                    <option value="{{ $driver->id }}">{{ $driver->employee_code }} - {{ $driver->full_name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="drivers[0][wage]" placeholder="Tiền công" data-currency class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <button type="button" onclick="removeStaffRow(this)" class="px-2 text-red-600 hover:text-red-800">✕</button>
                                        </div>
                                    </div>
                                    <button type="button" onclick="addDriverRow()" class="mt-1 text-xs text-blue-600 hover:text-blue-800">+ Thêm</button>
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Nhân viên y tế</label>
                                    <div id="medical-staff-container" class="space-y-2">
                                        <div class="medical-staff-item flex gap-2">
                                            <select name="medical_staff[0][staff_id]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <option value="">-- Chọn NVYT --</option>
                                                @foreach(\App\Models\Staff::active()->where('staff_type', 'medical_staff')->orderBy('full_name')->get() as $staff)
                                                    <option value="{{ $staff->id }}">{{ $staff->employee_code }} - {{ $staff->full_name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="medical_staff[0][wage]" placeholder="Tiền công" data-currency class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <button type="button" onclick="removeStaffRow(this)" class="px-2 text-red-600 hover:text-red-800">✕</button>
                                        </div>
                                    </div>
                                    <button type="button" onclick="addMedicalStaffRow()" class="mt-1 text-xs text-blue-600 hover:text-blue-800">+ Thêm</button>
                                </div>
                            </div>
                        </div>

                        {{-- 4. Thông tin cộng tác viên --}}
                        <details class="border-t pt-3">
                            <summary class="text-sm font-semibold text-gray-800 cursor-pointer hover:text-gray-900">🤝 Thông tin cộng tác viên (tùy chọn)</summary>
                            <div class="mt-2 space-y-2">
                                <select id="partner_id" name="partner_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">-- Không có --</option>
                                    @foreach(\App\Models\Partner::collaborators()->active()->orderBy('name')->get() as $partner)
                                        <option value="{{ $partner->id }}" data-commission="{{ $partner->commission_rate }}" {{ old('partner_id') == $partner->id ? 'selected' : '' }}>
                                            {{ $partner->name }} @if($partner->commission_rate) ({{ $partner->commission_rate }}%) @endif
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" id="commission_amount" name="commission_amount" value="{{ old('commission_amount') }}" data-currency placeholder="Số tiền hoa hồng" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                        </details>

                        {{-- 5. Dịch vụ kèm theo --}}
                        <details class="border-t pt-3">
                            <summary class="text-sm font-semibold text-gray-800 cursor-pointer hover:text-gray-900 mb-2">🛎️ Dịch vụ kèm theo (tùy chọn)</summary>
                            <div class="mt-3 space-y-2">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs text-gray-600">Thêm dịch vụ đi kèm với chuyến xe này</span>
                                    <button type="button" id="add-additional-service-btn" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">
                                        + Thêm dịch vụ
                                    </button>
                                </div>
                                <div id="incident-services-container" class="space-y-2">
                                    <!-- Services will be added here dynamically -->
                                </div>
                            </div>
                        </details>

                        {{-- 6. Thông tin thu --}}
                        <div class="border-t pt-3">
                            <h4 class="text-sm font-semibold text-gray-800 mb-2">💰 Thông tin thu</h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-medium text-green-600">Số tiền thu</label>
                                    <button type="button" id="add-service-btn" class="text-xs text-green-600 hover:text-green-700">+ Thêm khoản thu</button>
                                </div>
                                <input 
                                    type="text" 
                                    id="amount_thu" 
                                    name="amount_thu" 
                                    value="{{ old('amount_thu') }}" 
                                    data-currency 
                                    placeholder="Số tiền thu" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <input type="text" 
                                    name="revenue_main_name" 
                                    value="{{ old('revenue_main_name', 'Thu chuyến đi') }}" 
                                    placeholder="Ghi chú" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                <div id="additional-services-container" class="space-y-1"></div>
                            </div>
                        </div>

                        {{-- 7. Thông tin chi --}}
                        <div class="border-t pt-3">
                            <h4 class="text-sm font-semibold text-gray-800 mb-2">💳 Thông tin chi</h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-medium text-red-600">Số tiền chi</label>
                                    <button type="button" id="add-expense-btn" class="text-xs text-red-600 hover:text-red-700">+ Thêm khoản chi</button>
                                </div>
                                <input 
                                    type="text" 
                                    id="amount_chi" 
                                    name="amount_chi" 
                                    value="{{ old('amount_chi') }}" 
                                    data-currency 
                                    placeholder="Số tiền chi" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <input type="text" 
                                    name="expense_main_name" 
                                    value="{{ old('expense_main_name', 'Chi phí chuyến đi') }}" 
                                    placeholder="Ghi chú" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-xs">
                                <div id="additional-expenses-container" class="space-y-1"></div>
                            </div>
                        </div>

                        {{-- 8. Thông tin bảo trì --}}
                        <details class="border-t pt-3">
                            <summary class="text-sm font-semibold text-gray-800 cursor-pointer hover:text-gray-900">🔧 Thông tin bảo trì (tùy chọn)</summary>
                            <div class="mt-2 space-y-2">
                                <select name="maintenance_partner_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                    <option value="">-- Chọn đối tác --</option>
                                    @foreach(\App\Models\Partner::active()->where('type', 'maintenance')->orderBy('name')->get() as $partner)
                                        <option value="{{ $partner->id }}" {{ old('maintenance_partner_id') == $partner->id ? 'selected' : '' }}>{{ $partner->name }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="maintenance_service" list="maintenance_services_list" placeholder="Loại dịch vụ" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                <datalist id="maintenance_services_list">
                                    @foreach(\App\Models\MaintenanceService::active()->orderBy('name')->get() as $service)
                                        <option value="{{ $service->name }}">
                                    @endforeach
                                </datalist>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="maintenance_cost" data-currency placeholder="Chi phí" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                    <input type="number" name="maintenance_mileage" min="0" placeholder="Số km" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                </div>
                                <textarea name="maintenance_note" rows="2" placeholder="Ghi chú" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm"></textarea>
                            </div>
                        </details>

                        {{-- 9. Ghi chú --}}
                        <div class="border-t pt-3">
                            <h4 class="text-sm font-semibold text-gray-800 mb-2">📝 Ghi chú</h4>
                            <div class="space-y-2">
                                <label for="payment_method" class="block text-xs font-medium text-gray-700">Hình thức thanh toán</label>
                                <select id="payment_method" name="payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="cash" {{ old('payment_method', 'cash') == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                                    <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Chuyển khoản</option>
                                    <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Khác</option>
                                </select>

                                <label for="summary" class="block text-xs font-medium text-gray-700 mt-2">Tóm tắt chuyến đi</label>
                                <textarea id="summary" name="summary" rows="2" placeholder="Mô tả ngắn gọn về chuyến đi..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('summary') }}</textarea>

                                <label for="tags" class="block text-xs font-medium text-gray-700 mt-2">Tags (phân cách bằng dấu phẩy)</label>
                                <input type="text" id="tags" name="tags" value="{{ old('tags') }}" placeholder="vd: khẩn cấp, đường xa, ..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Submit Buttons - Fixed at bottom --}}
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 flex items-center justify-end space-x-3 border-t">
                    <a href="{{ route('incidents.index') }}" class="px-6 py-2.5 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 font-medium">
                        ← Hủy
                    </a>
                    <button type="submit" form="incident-form" class="px-8 py-2.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium shadow-sm">
                        💾 Tạo chuyến đi
                    </button>
                </div>
            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Wage types from database
        const wageTypes = @json(\App\Models\WageType::active()->ordered()->pluck('name'));

        // Partner commission auto-calculation
        const partnerSelect = document.getElementById('partner_id');
        const commissionInput = document.getElementById('commission_amount');
        const revenueInput = document.getElementById('amount_thu');

        if (partnerSelect && commissionInput && revenueInput) {
            partnerSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const commissionRate = selectedOption.dataset.commission;
                const revenue = parseFloat(revenueInput.value) || 0;

                if (commissionRate && revenue > 0) {
                    const commissionAmount = (revenue * parseFloat(commissionRate)) / 100;
                    commissionInput.value = Math.round(commissionAmount);
                }
            });

            revenueInput.addEventListener('input', function() {
                if (partnerSelect.value) {
                    const selectedOption = partnerSelect.options[partnerSelect.selectedIndex];
                    const commissionRate = selectedOption.dataset.commission;
                    const revenue = parseFloat(this.value) || 0;

                    if (commissionRate && revenue > 0) {
                        const commissionAmount = (revenue * parseFloat(commissionRate)) / 100;
                        commissionInput.value = Math.round(commissionAmount);
                    }
                }
            });
        }

        // Additional Services
        let serviceCount = 0;
        const servicesContainer = document.getElementById('additional-services-container');
        const addServiceBtn = document.getElementById('add-service-btn');
        
        addServiceBtn.addEventListener('click', function() {
            serviceCount++;
            const serviceRow = document.createElement('div');
            serviceRow.className = 'grid grid-cols-2 gap-1';
            serviceRow.innerHTML = `
                <input type="text" 
                    name="additional_services[${serviceCount}][name]" 
                    placeholder="Tên dịch vụ" 
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                <div class="flex gap-1">
                    <input type="number" 
                        name="additional_services[${serviceCount}][amount]" 
                        min="0" 
                        step="1000" 
                        placeholder="Số tiền" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                    <button type="button" onclick="this.closest('.grid').remove()" class="text-red-500 hover:text-red-700 text-xs">✕</button>
                </div>
            `;
            servicesContainer.appendChild(serviceRow);
        });

        // Additional Expenses
        let expenseCount = 0;
        const expensesContainer = document.getElementById('additional-expenses-container');
        const addExpenseBtn = document.getElementById('add-expense-btn');
        
        addExpenseBtn.addEventListener('click', function() {
            expenseCount++;
            const expenseRow = document.createElement('div');
            expenseRow.className = 'grid grid-cols-2 gap-1';
            expenseRow.innerHTML = `
                <input type="text" 
                    name="additional_expenses[${expenseCount}][name]" 
                    placeholder="Tên khoản chi" 
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-xs">
                <div class="flex gap-1">
                    <input type="number" 
                        name="additional_expenses[${expenseCount}][amount]" 
                        min="0" 
                        step="1000" 
                        placeholder="Số tiền" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-xs">
                    <button type="button" onclick="this.closest('.grid').remove()" class="text-red-500 hover:text-red-700 text-xs">✕</button>
                </div>
            `;
            expensesContainer.appendChild(expenseRow);
        });

        // Staff management functions
        let driverIndex = 1;
        let medicalStaffIndex = 1;

        function addDriverRow() {
            const container = document.getElementById('drivers-container');
            const newRow = document.createElement('div');
            newRow.className = 'driver-item flex gap-2';
            
            newRow.innerHTML = `
                <select name="drivers[${driverIndex}][staff_id]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">-- Chọn lái xe --</option>
                    @foreach(\App\Models\Staff::active()->where('staff_type', 'driver')->orderBy('full_name')->get() as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->employee_code }} - {{ $driver->full_name }}</option>
                    @endforeach
                </select>
                <input type="text" name="drivers[${driverIndex}][wage]" placeholder="Tiền công" data-currency class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <button type="button" onclick="removeStaffRow(this)" class="px-2 text-red-600 hover:text-red-800">✕</button>
            `;
            container.appendChild(newRow);
            
            // Re-initialize currency inputs
            if (window.initCurrencyInputs) {
                window.initCurrencyInputs();
            }
            
            driverIndex++;
        }

        function addMedicalStaffRow() {
            const container = document.getElementById('medical-staff-container');
            const newRow = document.createElement('div');
            newRow.className = 'medical-staff-item flex gap-2';
            
            newRow.innerHTML = `
                <select name="medical_staff[${medicalStaffIndex}][staff_id]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">-- Chọn NVYT --</option>
                    @foreach(\App\Models\Staff::active()->where('staff_type', 'medical_staff')->orderBy('full_name')->get() as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->employee_code }} - {{ $staff->full_name }}</option>
                    @endforeach
                </select>
                <input type="text" name="medical_staff[${medicalStaffIndex}][wage]" placeholder="Tiền công" data-currency class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <button type="button" onclick="removeStaffRow(this)" class="px-2 text-red-600 hover:text-red-800">✕</button>
            `;
            container.appendChild(newRow);
            
            // Re-initialize currency inputs
            if (window.initCurrencyInputs) {
                window.initCurrencyInputs();
            }
            
            medicalStaffIndex++;
        }

        function removeStaffRow(button) {
            button.closest('.driver-item, .medical-staff-item').remove();
        }

        // === INCIDENT ADDITIONAL SERVICES ===
        let incidentServiceIndex = 0;
        const incidentServicesContainer = document.getElementById('incident-services-container');
        const addIncidentServiceBtn = document.getElementById('add-additional-service-btn');
        
        // Get additional services for datalist
        const availableServices = @json(\App\Models\AdditionalService::active()->get(['id', 'name', 'default_price']));
        
        addIncidentServiceBtn.addEventListener('click', function() {
            const serviceRow = document.createElement('div');
            serviceRow.className = 'flex gap-2 items-start p-3 bg-gray-50 rounded border border-gray-200';
            serviceRow.innerHTML = `
                <div class="flex-1">
                    <input type="text" 
                        name="incident_services[${incidentServiceIndex}][service_name]" 
                        list="incident_services_datalist"
                        placeholder="Tên dịch vụ..." 
                        onchange="fillServicePrice(this, ${incidentServiceIndex})"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div class="w-40">
                    <input type="text" 
                        name="incident_services[${incidentServiceIndex}][amount]" 
                        data-currency
                        placeholder="Số tiền" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div class="flex-1">
                    <input type="text" 
                        name="incident_services[${incidentServiceIndex}][note]" 
                        placeholder="Ghi chú (tùy chọn)" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            incidentServicesContainer.appendChild(serviceRow);
            
            // Re-initialize currency formatter for new input
            if (window.initCurrencyInputs) {
                window.initCurrencyInputs();
            }
            
            incidentServiceIndex++;
        });
        
        // Create datalist for incident services
        if (availableServices.length > 0) {
            const datalist = document.createElement('datalist');
            datalist.id = 'incident_services_datalist';
            availableServices.forEach(service => {
                const option = document.createElement('option');
                option.value = service.name;
                option.dataset.price = service.default_price;
                datalist.appendChild(option);
            });
            document.body.appendChild(datalist);
        }
        
        // Fill service price automatically when service is selected
        window.fillServicePrice = function(input, index) {
            const serviceName = input.value;
            const service = availableServices.find(s => s.name === serviceName);
            if (service && service.default_price) {
                const amountInput = document.querySelector(`input[name="incident_services[${index}][amount]"]`);
                if (amountInput) {
                    // Format with thousand separator
                    const formatted = Math.round(service.default_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    amountInput.value = formatted;
                    // Trigger input event to update any listeners
                    amountInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
        };

        // Patient selection toggle
        const patientSelect = document.getElementById('patient_id');
        const newPatientFields = document.getElementById('new_patient_fields');

        if (patientSelect && newPatientFields) {
            patientSelect.addEventListener('change', function() {
                if (this.value) {
                    newPatientFields.style.display = 'none';
                } else {
                    newPatientFields.style.display = 'block';
                }
            });

            // Initialize on load
            if (patientSelect.value) {
                newPatientFields.style.display = 'none';
            }
        }
    </script>
    @endpush
</x-app-layout>
