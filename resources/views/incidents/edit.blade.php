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

                        {{-- Destination --}}
                        <div>
                            <label for="destination" class="block text-sm font-medium text-gray-700">
                                Điểm đến
                            </label>
                            <input type="text" id="destination" name="destination" value="{{ old('destination', $incident->destination) }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Summary --}}
                        <div>
                            <label for="summary" class="block text-sm font-medium text-gray-700">
                                Ghi chú / Tóm tắt
                            </label>
                            <textarea id="summary" name="summary" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('summary', $incident->summary) }}</textarea>
                        </div>

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
</x-app-layout>
