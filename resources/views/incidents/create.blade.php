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
                    <form method="POST" action="{{ route('incidents.store') }}" class="space-y-6">
                        @csrf

                        {{-- Vehicle --}}
                        <div>
                            <label for="vehicle_id" class="block text-sm font-medium text-gray-700">
                                Xe <span class="text-red-500">*</span>
                            </label>
                            <select id="vehicle_id" name="vehicle_id" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('vehicle_id') border-red-500 @enderror">
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
                        </div>

                        {{-- Date --}}
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">
                                Ngày giờ <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" id="date" name="date" value="{{ old('date', now()->format('Y-m-d\TH:i')) }}" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Patient Selection --}}
                        <div class="border-t pt-4">
                            <h3 class="text-md font-medium text-gray-700 mb-3">Thông tin bệnh nhân</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Chọn bệnh nhân có sẵn hoặc tạo mới
                                </label>
                                <select id="patient_id" name="patient_id" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Tạo mới --</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->name }} @if($patient->phone) - {{ $patient->phone }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="new_patient_fields" class="space-y-3">
                                <input type="text" name="patient_name" placeholder="Tên bệnh nhân" value="{{ old('patient_name') }}" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                
                                <input type="text" name="patient_phone" placeholder="Số điện thoại" value="{{ old('patient_phone') }}" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="number" name="patient_birth_year" placeholder="Năm sinh" min="1900" max="{{ date('Y') }}" value="{{ old('patient_birth_year') }}" 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    
                                    <select name="patient_gender" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Giới tính</option>
                                        <option value="male" {{ old('patient_gender') == 'male' ? 'selected' : '' }}>Nam</option>
                                        <option value="female" {{ old('patient_gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                                        <option value="other" {{ old('patient_gender') == 'other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                </div>

                                <textarea name="patient_address" rows="2" placeholder="Địa chỉ" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('patient_address') }}</textarea>
                            </div>
                        </div>

                        {{-- Destination --}}
                        <div>
                            <label for="destination" class="block text-sm font-medium text-gray-700">
                                Điểm đến
                            </label>
                            <input type="text" id="destination" name="destination" value="{{ old('destination') }}" 
                                placeholder="Bệnh viện, phòng khám..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Summary --}}
                        <div>
                            <label for="summary" class="block text-sm font-medium text-gray-700">
                                Ghi chú / Tóm tắt
                            </label>
                            <textarea id="summary" name="summary" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('summary') }}</textarea>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('incidents.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Hủy
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Tạo chuyến đi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('patient_id').addEventListener('change', function() {
            const newPatientFields = document.getElementById('new_patient_fields');
            if (this.value === '') {
                newPatientFields.style.display = 'block';
            } else {
                newPatientFields.style.display = 'none';
            }
        });
    </script>
    @endpush
</x-app-layout>
