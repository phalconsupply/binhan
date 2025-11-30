<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bảng điều khiển
            </h2>
            <div class="text-sm text-gray-600">
                Xin chào, <span class="font-semibold">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Statistics Cards --}}
            @cannot('view reports')
            {{-- Ẩn thống kê cho driver và roles không có quyền view reports --}}
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                {{-- Total Vehicles --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Tổng số xe</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_vehicles'] }}</p>
                                <p class="text-xs text-green-600">{{ $stats['active_vehicles'] }} đang hoạt động</p>
                            </div>
                            <div class="text-blue-500">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Today Incidents --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Chuyến đi hôm nay</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['today_incidents'] }}</p>
                            </div>
                            <div class="text-green-500">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Today Revenue/Expense --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Thu/Chi hôm nay</p>
                                <p class="text-lg font-bold text-green-600">+{{ number_format($stats['today_revenue'], 0, ',', '.') }}đ</p>
                                <p class="text-lg font-bold text-red-600">-{{ number_format($stats['today_expense'], 0, ',', '.') }}đ</p>
                                @if($stats['today_planned_expense'] > 0)
                                <p class="text-lg font-bold text-orange-600">-{{ number_format($stats['today_planned_expense'], 0, ',', '.') }}đ <span class="text-xs">(dự kiến)</span></p>
                                @endif
                                <p class="text-sm font-semibold {{ $stats['today_net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    = {{ number_format($stats['today_net'], 0, ',', '.') }}đ
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Month Revenue/Expense --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Thu/Chi tháng này</p>
                                <p class="text-lg font-bold text-green-600">+{{ number_format($stats['month_revenue'], 0, ',', '.') }}đ</p>
                                <p class="text-lg font-bold text-red-600">-{{ number_format($stats['month_expense'], 0, ',', '.') }}đ</p>
                                @if($stats['month_planned_expense'] > 0)
                                <p class="text-lg font-bold text-orange-600">-{{ number_format($stats['month_planned_expense'], 0, ',', '.') }}đ <span class="text-xs">(dự kiến)</span></p>
                                @endif
                                <p class="text-sm font-semibold {{ $stats['month_net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    = {{ number_format($stats['month_net'], 0, ',', '.') }}đ
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcannot

            {{-- Quick Entry Form - Full Width --}}
            @php
                $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())->where('staff_type', 'vehicle_owner')->exists();
            @endphp
            @if(!$isVehicleOwner)
            <div class="mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">Ghi nhận nhanh</h3>
                        <p class="text-xs text-gray-500 mb-4">📌 ID chuyến đi sẽ được tạo tự động sau khi lưu</p>
                        
                        {{-- Display Errors --}}
                        @if ($errors->any())
                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <strong class="font-bold">Có lỗi xảy ra!</strong>
                                <ul class="mt-2 list-disc list-inside text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('dashboard.quick-entry') }}" class="space-y-4">
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
                                        <input type="text" name="patient_name" placeholder="Tên bệnh nhân" value="{{ old('patient_name') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm capitalize">
                                        <input type="text" name="patient_phone" placeholder="Số điện thoại" value="{{ old('patient_phone') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="number" name="patient_birth_year" placeholder="Năm sinh" min="1900" max="{{ date('Y') }}" value="{{ old('patient_birth_year') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <select name="patient_gender" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <option value="">Giới tính</option>
                                                <option value="male" {{ old('patient_gender') == 'male' ? 'selected' : '' }}>Nam</option>
                                                <option value="female" {{ old('patient_gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                                                <option value="other" {{ old('patient_gender') == 'other' ? 'selected' : '' }}>Khác</option>
                                            </select>
                                        </div>
                                        <textarea name="patient_address" rows="2" placeholder="Địa chỉ" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('patient_address') }}</textarea>
                                    </div>
                                </details>

                                {{-- 3. Thông tin nhân sự --}}
                                <div class="border-t pt-3">
                                    <h4 class="text-sm font-semibold text-gray-800 mb-2">👥 Thông tin nhân sự & Tiền công</h4>
                                    
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Lái xe</label>
                                            <div id="drivers-container" class="space-y-2">
                                                <div class="driver-item flex flex-col sm:flex-row gap-2">
                                                    <select name="drivers[0][staff_id]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                        <option value="">-- Chọn lái xe --</option>
                                                        @foreach(\App\Models\Staff::active()->where('staff_type', 'driver')->orderBy('full_name')->get() as $driver)
                                                            <option value="{{ $driver->id }}">{{ $driver->employee_code }} - {{ $driver->full_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="flex gap-2 sm:flex-1">
                                                        <input type="text" name="drivers[0][wage]" placeholder="Tiền công" data-currency class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                        <button type="button" onclick="removeStaffRow(this)" class="px-3 sm:px-2 text-red-600 hover:text-red-800">✕</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" onclick="addDriverRow()" class="mt-1 text-xs text-blue-600 hover:text-blue-800">+ Thêm</button>
                                        </div>

                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Nhân viên y tế</label>
                                            <div id="medical-staff-container" class="space-y-2">
                                                <div class="medical-staff-item flex flex-col sm:flex-row gap-2">
                                                    <select name="medical_staff[0][staff_id]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                        <option value="">-- Chọn NVYT --</option>
                                                        @foreach(\App\Models\Staff::active()->where('staff_type', 'medical_staff')->orderBy('full_name')->get() as $staff)
                                                            <option value="{{ $staff->id }}">{{ $staff->employee_code }} - {{ $staff->full_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="flex gap-2 sm:flex-1">
                                                        <input type="text" name="medical_staff[0][wage]" placeholder="Tiền công" data-currency class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                        <button type="button" onclick="removeStaffRow(this)" class="px-3 sm:px-2 text-red-600 hover:text-red-800">✕</button>
                                                    </div>
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
                                    <summary class="text-sm font-semibold text-gray-800 cursor-pointer hover:text-gray-900">🛎️ Dịch vụ kèm theo (tùy chọn)</summary>
                                    <div class="mt-3 space-y-2">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-xs text-gray-600">Thêm các dịch vụ phát sinh trong chuyến đi</p>
                                            <button type="button" id="add-incident-service-btn" class="text-xs px-2 py-1 bg-blue-100 text-blue-600 rounded hover:bg-blue-200">+ Thêm dịch vụ</button>
                                        </div>
                                        <div id="incident-services-container" class="space-y-2"></div>
                                    </div>
                                </details>

                                {{-- 6. Thông tin thu chi --}}
                                <div class="border-t pt-3">
                                    <h4 class="text-sm font-semibold text-gray-800 mb-2">💰 Thông tin thu - chi</h4>
                                    <div class="grid grid-cols-2 gap-3">
                                        {{-- Revenue --}}
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <label class="block text-xs font-medium text-green-600">Thu</label>
                                                <button type="button" id="add-service-btn" class="text-xs text-green-600 hover:text-green-700">+</button>
                                            </div>
                                            <input type="text" 
                                                id="amount_thu" 
                                                name="amount_thu" 
                                                value="{{ old('amount_thu') }}" 
                                                data-currency 
                                                placeholder="Số tiền thu" 
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                            <input type="text" 
                                                name="revenue_main_name" 
                                                value="{{ old('revenue_main_name', 'Thu chuyến đi') }}" 
                                                placeholder="Ghi chú" 
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                            <div id="additional-services-container" class="mt-2 space-y-1"></div>
                                        </div>

                                        {{-- Expense --}}
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <label class="block text-xs font-medium text-red-600">Chi</label>
                                                <button type="button" id="add-expense-btn" class="text-xs text-red-600 hover:text-red-700">+</button>
                                            </div>
                                            <input type="text" 
                                                id="amount_chi" 
                                                name="amount_chi" 
                                                value="{{ old('amount_chi') }}" 
                                                data-currency 
                                                placeholder="Số tiền chi" 
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm">
                                            <input type="text" 
                                                name="expense_main_name" 
                                                value="{{ old('expense_main_name', 'Chi phí chuyến đi') }}" 
                                                placeholder="Ghi chú" 
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-xs">
                                            <div id="additional-expenses-container" class="mt-2 space-y-1"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 7. Thông tin bảo trì --}}
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

                                {{-- 8. Ghi chú --}}
                                <div class="border-t pt-3">
                                    <h4 class="text-sm font-semibold text-gray-800 mb-2">📝 Ghi chú</h4>
                                    <div class="space-y-2">
                                        <label for="payment_method" class="block text-xs font-medium text-gray-700">Hình thức thanh toán</label>
                                        <select id="payment_method" name="payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="cash" {{ old('payment_method', 'cash') == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                                            <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Chuyển khoản</option>
                                            <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Khác</option>
                                        </select>

                                        <label for="note" class="block text-xs font-medium text-gray-700 mt-2">Ghi chú chung</label>
                                        <textarea id="note" name="note" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('note') }}</textarea>
                                    </div>
                                </div>

                                {{-- Submit --}}
                                <div class="border-t pt-3 flex justify-end">
                                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                        💾 Ghi nhận chuyến đi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Recent Activities - Two Columns --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Today's Incidents --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Chuyến đi hôm nay ({{ $todayIncidents->count() }})</h3>
                            
                        @if($todayIncidents->isEmpty())
                            <p class="text-gray-500 text-sm">Chưa có chuyến đi nào hôm nay.</p>
                        @else
                            <div class="space-y-2">
                                @foreach($todayIncidents as $incident)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-semibold text-blue-600">{{ $incident->vehicle->license_plate }}</span>
                                                @if($incident->patient)
                                                    <span class="text-gray-600">→ {{ $incident->patient->name }}</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $incident->date->format('H:i') }}
                                                @if($incident->destination)
                                                    • {{ $incident->destination }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if($incident->transactions->count() > 0)
                                                <div class="text-sm font-semibold text-green-600">
                                                    +{{ number_format($incident->total_revenue, 0, ',', '.') }}đ
                                                </div>
                                                @if($incident->total_expense > 0)
                                                    <div class="text-xs text-red-600">
                                                        -{{ number_format($incident->total_expense, 0, ',', '.') }}đ
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Recent Incidents --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Chuyến đi gần đây</h3>
                            
                        @if($recentIncidents->isEmpty())
                            <p class="text-gray-500 text-sm">Chưa có chuyến đi nào.</p>
                        @else
                            <div class="space-y-2">
                                @foreach($recentIncidents as $incident)
                                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-semibold text-blue-600">{{ $incident->vehicle->license_plate }}</span>
                                                @if($incident->patient)
                                                    <span class="text-gray-600">→ {{ $incident->patient->name }}</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $incident->date->format('d/m/Y H:i') }}
                                                @if($incident->destination)
                                                    • {{ $incident->destination }}
                                                @endif
                                                • Bởi {{ $incident->dispatcher->name }}
                                            </div>
                                        </div>
                                        <div class="text-right text-sm">
                                            @if($incident->transactions->count() > 0)
                                                <span class="font-semibold {{ $incident->net_amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ number_format($incident->net_amount, 0, ',', '.') }}đ
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for dynamic fields --}}
    @push('scripts')
    <script>
        // Incident Services (Dịch vụ kèm theo)
        let incidentServiceCount = 0;
        const incidentServicesContainer = document.getElementById('incident-services-container');
        const addIncidentServiceBtn = document.getElementById('add-incident-service-btn');
        
        // Get additional services for autocomplete
        const incidentServices = @json(\App\Models\AdditionalService::active()->orderBy('name')->get(['id', 'name', 'default_price']));
        
        addIncidentServiceBtn.addEventListener('click', function() {
            incidentServiceCount++;
            const serviceRow = document.createElement('div');
            serviceRow.className = 'incident-service-item flex flex-col sm:flex-row gap-2';
            serviceRow.innerHTML = `
                <input type="text" 
                    name="incident_services[${incidentServiceCount}][service_name]" 
                    list="incident_services_datalist"
                    placeholder="Tên dịch vụ..." 
                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    onchange="autoFillIncidentServicePrice(this, ${incidentServiceCount})">
                <div class="flex gap-2 sm:flex-1">
                    <input type="text" 
                        name="incident_services[${incidentServiceCount}][amount]" 
                        data-currency 
                        placeholder="Số tiền" 
                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <input type="text" 
                        name="incident_services[${incidentServiceCount}][note]" 
                        placeholder="Ghi chú" 
                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <button type="button" onclick="removeIncidentServiceRow(this)" class="px-3 sm:px-2 text-red-600 hover:text-red-800">✕</button>
                </div>
            `;
            incidentServicesContainer.appendChild(serviceRow);
            
            // Reinitialize currency inputs for the new row
            if (window.initCurrencyInputs) {
                window.initCurrencyInputs();
            }
        });
        
        // Create datalist for incident services
        if (incidentServices.length > 0) {
            const datalist = document.createElement('datalist');
            datalist.id = 'incident_services_datalist';
            incidentServices.forEach(service => {
                const option = document.createElement('option');
                option.value = service.name;
                option.dataset.price = service.default_price || '';
                datalist.appendChild(option);
            });
            document.body.appendChild(datalist);
        }
        
        // Auto-fill price when service is selected
        window.autoFillIncidentServicePrice = function(input, index) {
            const serviceName = input.value;
            const service = incidentServices.find(s => s.name === serviceName);
            if (service && service.default_price) {
                const amountInput = document.querySelector(`input[name="incident_services[${index}][amount]"]`);
                if (amountInput && !amountInput.value) {
                    amountInput.value = window.formatCurrency ? window.formatCurrency(service.default_price) : service.default_price;
                }
            }
        };

        // Additional Services
        let serviceCount = 0;
        const servicesContainer = document.getElementById('additional-services-container');
        const addServiceBtn = document.getElementById('add-service-btn');
        
        // Get additional services for datalist
        const additionalServices = @json(\App\Models\AdditionalService::active()->orderBy('name')->pluck('name'));
        
        addServiceBtn.addEventListener('click', function() {
            serviceCount++;
            const serviceRow = document.createElement('div');
            serviceRow.className = 'flex gap-2 items-start';
            serviceRow.innerHTML = `
                <div class="flex-1">
                    <input type="text" 
                        name="additional_services[${serviceCount}][name]" 
                        list="services_datalist"
                        placeholder="Tên dịch vụ..." 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                </div>
                <div class="w-28">
                    <input type="text" 
                        name="additional_services[${serviceCount}][amount]" 
                        data-currency 
                        placeholder="Số tiền" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
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
            expenseRow.className = 'flex gap-2 items-start';
            expenseRow.innerHTML = `
                <div class="flex-1">
                    <input type="text" 
                        name="additional_expenses[${expenseCount}][name]" 
                        placeholder="Tên khoản chi..." 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm">
                </div>
                <div class="w-28">
                    <input type="text" 
                        name="additional_expenses[${expenseCount}][amount]" 
                        data-currency 
                        placeholder="Số tiền" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm">
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            expensesContainer.appendChild(expenseRow);
        });

        // Create datalist for additional services
        if (additionalServices.length > 0) {
            const datalist = document.createElement('datalist');
            datalist.id = 'services_datalist';
            additionalServices.forEach(service => {
                const option = document.createElement('option');
                option.value = service;
                datalist.appendChild(option);
            });
            document.body.appendChild(datalist);
        }

        // Staff management functions
        let driverIndex = 1;
        let medicalStaffIndex = 1;

        function addDriverRow() {
            const container = document.getElementById('drivers-container');
            const newRow = document.createElement('div');
            newRow.className = 'driver-item flex flex-col sm:flex-row gap-2';
            newRow.innerHTML = `
                <select name="drivers[${driverIndex}][staff_id]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">-- Chọn lái xe --</option>
                    @foreach(\App\Models\Staff::active()->where('staff_type', 'driver')->orderBy('full_name')->get() as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->employee_code }} - {{ $driver->full_name }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2 sm:flex-1">
                    <input type="text" name="drivers[${driverIndex}][wage]" placeholder="Tiền công" data-currency class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <button type="button" onclick="removeStaffRow(this)" class="px-3 sm:px-2 text-red-600 hover:text-red-800">✕</button>
                </div>
            `;
            container.appendChild(newRow);
            driverIndex++;
        }

        function addMedicalStaffRow() {
            const container = document.getElementById('medical-staff-container');
            const newRow = document.createElement('div');
            newRow.className = 'medical-staff-item flex flex-col sm:flex-row gap-2';
            newRow.innerHTML = `
                <select name="medical_staff[${medicalStaffIndex}][staff_id]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">-- Chọn NVYT --</option>
                    @foreach(\App\Models\Staff::active()->where('staff_type', 'medical_staff')->orderBy('full_name')->get() as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->employee_code }} - {{ $staff->full_name }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2 sm:flex-1">
                    <input type="text" name="medical_staff[${medicalStaffIndex}][wage]" placeholder="Tiền công" data-currency class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <button type="button" onclick="removeStaffRow(this)" class="px-3 sm:px-2 text-red-600 hover:text-red-800">✕</button>
                </div>
            `;
            container.appendChild(newRow);
            medicalStaffIndex++;
        }

        function removeStaffRow(button) {
            button.closest('.driver-item, .medical-staff-item').remove();
        }

        function removeIncidentServiceRow(button) {
            button.closest('.incident-service-item').remove();
        }
    </script>
    @endpush
</x-app-layout>


