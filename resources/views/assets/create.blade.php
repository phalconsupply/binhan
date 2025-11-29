<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ➕ Thêm Tài sản mới
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('assets.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Tên tài sản --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Tên tài sản <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-300 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Nhãn hiệu --}}
                            <div>
                                <label for="brand" class="block text-sm font-medium text-gray-700">Nhãn hiệu</label>
                                <input type="text" name="brand" id="brand" value="{{ old('brand') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('brand') border-red-300 @enderror">
                                @error('brand')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Ngày trang bị --}}
                            <div>
                                <label for="equipped_date" class="block text-sm font-medium text-gray-700">Ngày trang bị <span class="text-red-500">*</span></label>
                                <input type="date" name="equipped_date" id="equipped_date" value="{{ old('equipped_date', date('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('equipped_date') border-red-300 @enderror">
                                @error('equipped_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Số lượng --}}
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700">Số lượng <span class="text-red-500">*</span></label>
                                <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('quantity') border-red-300 @enderror">
                                @error('quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Loại sử dụng --}}
                            <div>
                                <label for="usage_type" class="block text-sm font-medium text-gray-700">Nơi sử dụng <span class="text-red-500">*</span></label>
                                <select name="usage_type" id="usage_type" required onchange="toggleUsageFields()"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('usage_type') border-red-300 @enderror">
                                    <option value="">-- Chọn loại --</option>
                                    <option value="vehicle" {{ old('usage_type', request('usage_type')) == 'vehicle' ? 'selected' : '' }}>🚗 Xe</option>
                                    <option value="staff" {{ old('usage_type', request('usage_type')) == 'staff' ? 'selected' : '' }}>👤 Cá nhân</option>
                                </select>
                                @error('usage_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Trạng thái --}}
                            <div>
                                <label for="is_active" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                                <select name="is_active" id="is_active"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>✓ Đang sử dụng</option>
                                    <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>✕ Ngừng sử dụng</option>
                                </select>
                            </div>

                            {{-- Xe (hiển thị khi chọn vehicle) --}}
                            <div id="vehicle_field" style="display: none;">
                                <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Xe <span class="text-red-500">*</span></label>
                                <select name="vehicle_id" id="vehicle_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('vehicle_id') border-red-300 @enderror">
                                    <option value="">-- Chọn xe --</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id', request('vehicle_id')) == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->license_plate }} @if($vehicle->model) - {{ $vehicle->model }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Nhân viên (hiển thị khi chọn staff) --}}
                            <div id="staff_field" style="display: none;">
                                <label for="staff_id" class="block text-sm font-medium text-gray-700">Nhân viên <span class="text-red-500">*</span></label>
                                <select name="staff_id" id="staff_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('staff_id') border-red-300 @enderror">
                                    <option value="">-- Chọn nhân viên --</option>
                                    @foreach($staffs as $staff)
                                        <option value="{{ $staff->id }}" {{ old('staff_id', request('staff_id')) == $staff->id ? 'selected' : '' }}>
                                            {{ $staff->full_name }} @if($staff->staff_type) - {{ $staff->staff_type }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('staff_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Ghi chú --}}
                        <div class="mt-6">
                            <label for="note" class="block text-sm font-medium text-gray-700">Ghi chú</label>
                            <textarea name="note" id="note" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('note') border-red-300 @enderror">{{ old('note') }}</textarea>
                            @error('note')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('assets.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Hủy
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                💾 Lưu tài sản
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleUsageFields() {
            const usageType = document.getElementById('usage_type').value;
            const vehicleField = document.getElementById('vehicle_field');
            const staffField = document.getElementById('staff_field');
            const vehicleSelect = document.getElementById('vehicle_id');
            const staffSelect = document.getElementById('staff_id');
            
            if (usageType === 'vehicle') {
                vehicleField.style.display = 'block';
                staffField.style.display = 'none';
                vehicleSelect.required = true;
                staffSelect.required = false;
                staffSelect.value = '';
            } else if (usageType === 'staff') {
                vehicleField.style.display = 'none';
                staffField.style.display = 'block';
                vehicleSelect.required = false;
                staffSelect.required = true;
                vehicleSelect.value = '';
            } else {
                vehicleField.style.display = 'none';
                staffField.style.display = 'none';
                vehicleSelect.required = false;
                staffSelect.required = false;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleUsageFields();
        });
    </script>
    @endpush
</x-app-layout>
