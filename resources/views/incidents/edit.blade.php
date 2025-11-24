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
                                                <input type="text" name="drivers[{{ $driverIndex }}][wage]" value="{{ $driver->pivot->actual_wage ?? 0 }}" placeholder="Tiền công (đ)" data-currency class="w-36 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
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
                                                <input type="text" name="medical_staff[{{ $medicalIndex }}][wage]" value="{{ $staff->pivot->actual_wage ?? 0 }}" placeholder="Tiền công (đ)" data-currency class="w-36 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
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
                                    <label for="partner_id" class="block text-sm font-medium text-gray-700">
                                        Đối tác
                                    </label>
                                    <select id="partner_id" name="partner_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">-- Không có --</option>
                                        @foreach(\App\Models\Partner::active()->where('type', 'collaborator')->orderBy('name')->get() as $partner)
                                            <option value="{{ $partner->id }}" data-commission="{{ $partner->commission_rate }}" {{ old('partner_id', $incident->partner_id) == $partner->id ? 'selected' : '' }}>
                                                {{ $partner->name }} @if($partner->commission_rate)(Hoa hồng: {{ $partner->commission_rate }}%)@endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="commission_amount" class="block text-sm font-medium text-gray-700">
                                        Tiền hoa hồng (đ)
                                    </label>
                                    <input type="text" id="commission_amount" name="commission_amount" value="{{ old('commission_amount', $incident->commission_amount) }}" data-currency
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
    </script>
</x-app-layout>
