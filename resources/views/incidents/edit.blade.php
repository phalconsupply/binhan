<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sửa chuyến đi #{{ $incident->id }}
            </h2>
            <a href="{{ route('incidents.show', $incident) }}" class="text-indigo-600 hover:text-indigo-900">
                ← Quay lại chi tiết
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('incidents.update', $incident) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Vehicle --}}
                        <div>
                            <label for="vehicle_id" class="block text-sm font-medium text-gray-700">
                                Xe <span class="text-red-500">*</span>
                            </label>
                            <select id="vehicle_id" name="vehicle_id" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $incident->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->license_plate }} @if($vehicle->driver_name) - {{ $vehicle->driver_name }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date --}}
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">
                                Ngày giờ <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" id="date" name="date" value="{{ old('date', $incident->date->format('Y-m-d\TH:i')) }}" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Patient --}}
                        <div>
                            <label for="patient_id" class="block text-sm font-medium text-gray-700">
                                Bệnh nhân
                            </label>
                            <select id="patient_id" name="patient_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Không có --</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id', $incident->patient_id) == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->name }} @if($patient->phone) - {{ $patient->phone }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Locations with autocomplete --}}
                        <div class="border-t pt-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">📍 Địa điểm đón/trả</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="from_location" class="block text-sm font-medium text-gray-700">Nơi đi</label>
                                    <input type="text" 
                                        id="from_location" 
                                        name="from_location" 
                                        list="from_locations_list"
                                        value="{{ old('from_location', $incident->fromLocation->name ?? '') }}" 
                                        placeholder="Nhập hoặc chọn nơi đi..."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <datalist id="from_locations_list">
                                        @foreach(\App\Models\Location::active()->whereIn('type', ['from', 'both'])->orderBy('name')->get() as $location)
                                            <option value="{{ $location->name }}">
                                        @endforeach
                                    </datalist>
                                </div>

                                <div>
                                    <label for="to_location" class="block text-sm font-medium text-gray-700">Nơi đến</label>
                                    <input type="text" 
                                        id="to_location" 
                                        name="to_location" 
                                        list="to_locations_list"
                                        value="{{ old('to_location', $incident->toLocation->name ?? '') }}" 
                                        placeholder="Nhập hoặc chọn nơi đến..."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <datalist id="to_locations_list">
                                        @foreach(\App\Models\Location::active()->whereIn('type', ['to', 'both'])->orderBy('name')->get() as $location)
                                            <option value="{{ $location->name }}">
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>
                        </div>

                        {{-- Staff Assignment with Wages --}}
                        <div class="border-t pt-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">👥 Nhân sự & Tiền công</h3>
                            <p class="text-xs text-gray-500 mb-3">💡 Sử dụng nút 🗑️ để xóa nhân viên. Để thay đổi tiền công, nhập số tiền mới và lưu.</p>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Lái xe</label>
                                    <div id="drivers-container-edit" class="space-y-2">
                                        @php $driverIndex = 0; @endphp
                                        @foreach($incident->drivers as $driver)
                                            <div class="driver-item flex gap-2 items-center bg-gray-50 p-2 rounded">
                                                <input type="hidden" name="drivers[{{ $driverIndex }}][staff_id]" value="{{ $driver->id }}">
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium">{{ $driver->employee_code }} - {{ $driver->full_name }}</span>
                                                    @if($driver->pivot->actual_wage ?? 0 > 0)
                                                        <span class="text-xs text-green-600 ml-2">({{ number_format($driver->pivot->actual_wage, 0, ',', '.') }}đ)</span>
                                                    @endif
                                                </div>
                                                <input type="text" name="drivers[{{ $driverIndex }}][wage]" value="{{ number_format($driver->pivot->actual_wage ?? 0, 0, ',', '.') }}" placeholder="Tiền công (đ)" data-currency class="w-36 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <button type="button" onclick="removeStaff(this)" class="px-2 py-1 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded" title="Xóa nhân viên">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            @php $driverIndex++; @endphp
                                        @endforeach
                                        <div class="driver-item flex gap-2">
                                            <select name="drivers[{{ $driverIndex }}][staff_id]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <option value="">-- Thêm lái xe mới --</option>
                                                @foreach(\App\Models\Staff::active()->where('staff_type', 'driver')->orderBy('full_name')->get() as $driver)
                                                    <option value="{{ $driver->id }}">{{ $driver->employee_code }} - {{ $driver->full_name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="drivers[{{ $driverIndex }}][wage]" placeholder="Tiền công (đ)" data-currency class="w-36 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nhân viên y tế</label>
                                    <div id="medical-staff-container-edit" class="space-y-2">
                                        @php $medicalIndex = 0; @endphp
                                        @foreach($incident->medicalStaff as $staff)
                                            <div class="medical-staff-item flex gap-2 items-center bg-gray-50 p-2 rounded">
                                                <input type="hidden" name="medical_staff[{{ $medicalIndex }}][staff_id]" value="{{ $staff->id }}">
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium">{{ $staff->employee_code }} - {{ $staff->full_name }}</span>
                                                    @if($staff->pivot->actual_wage ?? 0 > 0)
                                                        <span class="text-xs text-green-600 ml-2">({{ number_format($staff->pivot->actual_wage, 0, ',', '.') }}đ)</span>
                                                    @endif
                                                </div>
                                                <input type="text" name="medical_staff[{{ $medicalIndex }}][wage]" value="{{ number_format($staff->pivot->actual_wage ?? 0, 0, ',', '.') }}" placeholder="Tiền công (đ)" data-currency class="w-36 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <button type="button" onclick="removeStaff(this)" class="px-2 py-1 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded" title="Xóa nhân viên">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            @php $medicalIndex++; @endphp
                                        @endforeach
                                        <div class="medical-staff-item flex gap-2">
                                            <select name="medical_staff[{{ $medicalIndex }}][staff_id]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <option value="">-- Thêm nhân viên y tế mới --</option>
                                                @foreach(\App\Models\Staff::active()->where('staff_type', 'medical_staff')->orderBy('full_name')->get() as $staff)
                                                    <option value="{{ $staff->id }}">{{ $staff->employee_code }} - {{ $staff->full_name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="medical_staff[{{ $medicalIndex }}][wage]" placeholder="Tiền công (đ)" data-currency class="w-36 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Services --}}
                        <div class="border-t pt-4">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-sm font-medium text-gray-700">🛎️ Dịch vụ kèm theo</h3>
                                <button type="button" id="add-edit-service-btn" class="px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">
                                    + Thêm dịch vụ
                                </button>
                            </div>
                            
                            <div id="edit-services-container" class="space-y-2">
                                @foreach($incident->additionalServices as $index => $service)
                                <div class="flex gap-2 items-start p-3 bg-gray-50 rounded border border-gray-200">
                                    <input type="hidden" name="existing_services[{{ $index }}][id]" value="{{ $service->id }}">
                                    <div class="flex-1">
                                        <input type="text" 
                                            name="existing_services[{{ $index }}][service_name]" 
                                            value="{{ $service->service_name }}"
                                            list="edit_services_datalist"
                                            placeholder="Tên dịch vụ..." 
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div class="w-40">
                                        <input type="text" 
                                            name="existing_services[{{ $index }}][amount]" 
                                            value="{{ number_format($service->amount, 0, ',', '.') }}"
                                            data-currency
                                            placeholder="Số tiền" 
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div class="flex-1">
                                        <input type="text" 
                                            name="existing_services[{{ $index }}][note]" 
                                            value="{{ $service->note }}"
                                            placeholder="Ghi chú" 
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <button type="button" onclick="markServiceForDeletion(this, {{ $service->id }})" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            
                            <!-- Hidden field for services to delete -->
                            <input type="hidden" name="services_to_delete" id="services_to_delete" value="">
                        </div>

                        {{-- Partner & Commission --}}
                        <div class="border-t pt-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">🤝 Thông tin đối tác & Hoa hồng</h3>
                            
                            <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                <p class="text-sm text-blue-800">
                                    💡 <strong>Ghi chú:</strong> Khi bạn nhập thông tin hoa hồng và lưu, hệ thống sẽ tự động tạo/cập nhật giao dịch chi "Hoa hồng" trong menu Giao dịch.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="referrer_select" class="block text-sm font-medium text-gray-700">
                                        Người giới thiệu
                                    </label>
                                    <select id="referrer_select" name="referrer_select"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">-- Không có --</option>
                                        <optgroup label="👥 Nhân viên công ty">
                                            @foreach(\App\Models\Staff::active()->orderBy('full_name')->get() as $staff)
                                                <option value="staff_{{ $staff->id }}" 
                                                    data-commission="{{ $staff->commission_rate ?? 0 }}"
                                                    {{ (old('referrer_type', $incident->referrer_type) == 'App\Models\Staff' && old('referrer_id', $incident->referrer_id) == $staff->id) ? 'selected' : '' }}>
                                                    {{ $staff->full_name }} ({{ ucfirst($staff->staff_type) }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="🤝 Cộng tác viên">
                                            @foreach(\App\Models\Partner::active()->where('type', 'collaborator')->orderBy('name')->get() as $partner)
                                                <option value="partner_{{ $partner->id }}" 
                                                    data-commission="{{ $partner->commission_rate }}"
                                                    {{ (old('referrer_type', $incident->referrer_type) == 'App\Models\Partner' && old('referrer_id', $incident->referrer_id) == $partner->id) ? 'selected' : '' }}>
                                                    {{ $partner->name }} @if($partner->commission_rate)({{ $partner->commission_rate }}%)@endif
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                    
                                    <!-- Hidden fields for backend -->
                                    <input type="hidden" id="referrer_type" name="referrer_type" value="{{ old('referrer_type', $incident->referrer_type) }}">
                                    <input type="hidden" id="referrer_id" name="referrer_id" value="{{ old('referrer_id', $incident->referrer_id) }}">
                                </div>

                                <div>
                                    <label for="commission_amount" class="block text-sm font-medium text-gray-700">
                                        Tiền hoa hồng (đ)
                                    </label>
                                    <input type="text" id="commission_amount" name="commission_amount" value="{{ number_format(old('commission_amount', $incident->commission_amount ?? 0), 0, ',', '.') }}" data-currency
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Nhập tiền hoa hồng">
                                </div>
                            </div>
                        </div>

                        {{-- Summary --}}
                        <div class="border-t pt-4">
                            <label for="summary" class="block text-sm font-medium text-gray-700">
                                Ghi chú / Tóm tắt
                            </label>
                            <textarea id="summary" name="summary" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('summary', $incident->summary) }}</textarea>
                        </div>

                        {{-- Transactions Summary (Read-only) --}}
                        @if($incident->transactions->count() > 0)
                        <div class="border-t pt-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">💰 Tổng quan giao dịch</h3>
                            <div class="bg-gray-50 p-4 rounded-md">
                                <div class="grid grid-cols-3 gap-4 mb-3">
                                    <div>
                                        <p class="text-xs text-gray-500">Tổng thu</p>
                                        <p class="text-lg font-bold text-green-600">
                                            +{{ number_format($incident->transactions()->where('type', 'thu')->sum('amount'), 0, ',', '.') }}đ
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Tổng chi</p>
                                        <p class="text-lg font-bold text-red-600">
                                            -{{ number_format($incident->transactions()->where('type', 'chi')->sum('amount'), 0, ',', '.') }}đ
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Lợi nhuận</p>
                                        <p class="text-lg font-bold {{ $incident->net_amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($incident->net_amount, 0, ',', '.') }}đ
                                        </p>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500">
                                    <a href="{{ route('transactions.index', ['search' => $incident->id]) }}" class="text-indigo-600 hover:text-indigo-900">
                                        → Xem chi tiết giao dịch
                                    </a>
                                </p>
                            </div>
                        </div>
                        @endif

                        {{-- Buttons --}}
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('incidents.show', $incident) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Hủy
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function removeStaff(button) {
            if (confirm('Bạn có chắc muốn xóa nhân viên này khỏi chuyến đi? Giao dịch tiền công (nếu có) cũng sẽ bị xóa.')) {
                const staffItem = button.closest('.driver-item, .medical-staff-item');
                staffItem.remove();
            }
        }

        // === INCIDENT SERVICES MANAGEMENT ===
        let editServiceIndex = {{ $incident->additionalServices->count() }};
        const editServicesContainer = document.getElementById('edit-services-container');
        const addEditServiceBtn = document.getElementById('add-edit-service-btn');
        const servicesToDeleteInput = document.getElementById('services_to_delete');
        let servicesToDelete = [];
        
        // Get available services
        const availableServices = @json(\App\Models\AdditionalService::active()->get(['id', 'name', 'default_price']));
        
        // Create datalist for services
        if (availableServices.length > 0) {
            const datalist = document.createElement('datalist');
            datalist.id = 'edit_services_datalist';
            availableServices.forEach(service => {
                const option = document.createElement('option');
                option.value = service.name;
                option.dataset.price = service.default_price;
                datalist.appendChild(option);
            });
            document.body.appendChild(datalist);
        }
        
        addEditServiceBtn.addEventListener('click', function() {
            const serviceRow = document.createElement('div');
            serviceRow.className = 'flex gap-2 items-start p-3 bg-gray-50 rounded border border-gray-200';
            serviceRow.innerHTML = `
                <div class="flex-1">
                    <input type="text" 
                        name="new_services[${editServiceIndex}][service_name]" 
                        list="edit_services_datalist"
                        placeholder="Tên dịch vụ..." 
                        onchange="fillEditServicePrice(this, ${editServiceIndex})"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div class="w-40">
                    <input type="text" 
                        name="new_services[${editServiceIndex}][amount]" 
                        data-currency
                        placeholder="Số tiền" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div class="flex-1">
                    <input type="text" 
                        name="new_services[${editServiceIndex}][note]" 
                        placeholder="Ghi chú" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            editServicesContainer.appendChild(serviceRow);
            
            // Re-initialize currency formatter
            if (window.initCurrencyInputs) {
                window.initCurrencyInputs();
            }
            
            editServiceIndex++;
        });
        
        // Fill service price for new services
        window.fillEditServicePrice = function(input, index) {
            const serviceName = input.value;
            const service = availableServices.find(s => s.name === serviceName);
            if (service && service.default_price) {
                const amountInput = document.querySelector(`input[name="new_services[${index}][amount]"]`);
                if (amountInput) {
                    const formatted = Math.round(service.default_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    amountInput.value = formatted;
                    amountInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
        };
        
        // Mark service for deletion
        window.markServiceForDeletion = function(button, serviceId) {
            if (confirm('Bạn có chắc muốn xóa dịch vụ này? Giao dịch liên quan cũng sẽ bị xóa.')) {
                const serviceRow = button.closest('.flex');
                serviceRow.style.opacity = '0.5';
                serviceRow.style.textDecoration = 'line-through';
                
                // Add to delete list
                servicesToDelete.push(serviceId);
                servicesToDeleteInput.value = JSON.stringify(servicesToDelete);
                
                // Disable inputs
                serviceRow.querySelectorAll('input').forEach(input => {
                    input.disabled = true;
                });
                
                // Change button to undo
                button.innerHTML = '<span class="text-xs">↶ Hoàn tác</span>';
                button.onclick = function() {
                    undoServiceDeletion(button, serviceId, serviceRow);
                };
            }
        };
        
        window.undoServiceDeletion = function(button, serviceId, serviceRow) {
            serviceRow.style.opacity = '1';
            serviceRow.style.textDecoration = 'none';
            
            // Remove from delete list
            servicesToDelete = servicesToDelete.filter(id => id !== serviceId);
            servicesToDeleteInput.value = JSON.stringify(servicesToDelete);
            
            // Enable inputs
            serviceRow.querySelectorAll('input').forEach(input => {
                input.disabled = false;
            });
            
            // Restore delete button
            button.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            `;
            button.onclick = function() {
                markServiceForDeletion(button, serviceId);
            };
        };
        
        // Referrer selection handler
        const referrerSelect = document.getElementById('referrer_select');
        const referrerTypeInput = document.getElementById('referrer_type');
        const referrerIdInput = document.getElementById('referrer_id');
        const commissionInput = document.getElementById('commission_amount');
        
        if (referrerSelect) {
            referrerSelect.addEventListener('change', function() {
                const value = this.value;
                
                if (value) {
                    // Split "staff_123" or "partner_456"
                    const [type, id] = value.split('_');
                    referrerTypeInput.value = type === 'staff' ? 'App\\Models\\Staff' : 'App\\Models\\Partner';
                    referrerIdInput.value = id;
                } else {
                    referrerTypeInput.value = '';
                    referrerIdInput.value = '';
                }
            });
        }
    </script>
</x-app-layout>
