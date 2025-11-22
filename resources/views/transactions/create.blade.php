<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Thêm giao dịch mới
            </h2>
            <a href="{{ route('transactions.index') }}" class="text-indigo-600 hover:text-indigo-900">
                ← Quay lại danh sách
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('transactions.store') }}" class="space-y-6">
                        @csrf

                        {{-- Type --}}
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">
                                Loại giao dịch <span class="text-red-500">*</span>
                            </label>
                            <select id="type" name="type" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="thu" {{ old('type') == 'thu' ? 'selected' : '' }}>Thu</option>
                                <option value="chi" {{ old('type') == 'chi' ? 'selected' : '' }}>Chi</option>
                                <option value="du_kien_chi" {{ old('type') == 'du_kien_chi' ? 'selected' : '' }}>Dự kiến chi</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">💡 "Dự kiến chi" sẽ được trừ khỏi lợi nhuận và thống kê riêng là "khoản chưa chi"</p>
                        </div>

                        {{-- Vehicle --}}
                        <div>
                            <label for="vehicle_id" class="block text-sm font-medium text-gray-700">
                                Xe (tùy chọn)
                            </label>
                            <select id="vehicle_id" name="vehicle_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Không liên kết --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $selectedIncident?->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->license_plate }} @if($vehicle->driver_name) - {{ $vehicle->driver_name }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Incident (Optional) --}}
                        <div>
                            <label for="incident_id" class="block text-sm font-medium text-gray-700">
                                Chuyến đi (tùy chọn)
                            </label>
                            <select id="incident_id" name="incident_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Không liên kết --</option>
                                @foreach($incidents as $incident)
                                    <option value="{{ $incident->id }}" {{ old('incident_id', $selectedIncident?->id) == $incident->id ? 'selected' : '' }}>
                                        {{ $incident->date->format('d/m/Y H:i') }} - {{ $incident->vehicle->license_plate }}
                                        @if($incident->patient) - {{ $incident->patient->name }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Amount --}}
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">
                                Số tiền <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="amount" name="amount" value="{{ old('amount') }}" required min="0" step="1"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Method --}}
                        <div>
                            <label for="method" class="block text-sm font-medium text-gray-700">
                                Phương thức <span class="text-red-500">*</span>
                            </label>
                            <select id="method" name="method" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="cash" {{ old('method', 'cash') == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                                <option value="bank" {{ old('method') == 'bank' ? 'selected' : '' }}>Chuyển khoản</option>
                                <option value="other" {{ old('method') == 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>

                        {{-- Date --}}
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">
                                Ngày giờ <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" id="date" name="date" value="{{ old('date', now()->format('Y-m-d\TH:i')) }}" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Note --}}
                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700">
                                Ghi chú
                            </label>
                            <textarea id="note" name="note" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('note') }}</textarea>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Hủy
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Thêm giao dịch
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
