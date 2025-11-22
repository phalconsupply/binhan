<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sửa giao dịch #{{ $transaction->id }}
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
                    <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Type --}}
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">
                                Loại giao dịch <span class="text-red-500">*</span>
                            </label>
                            <select id="type" name="type" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="thu" {{ old('type', $transaction->type) == 'thu' ? 'selected' : '' }}>Thu</option>
                                <option value="chi" {{ old('type', $transaction->type) == 'chi' ? 'selected' : '' }}>Chi</option>
                                <option value="du_kien_chi" {{ old('type', $transaction->type) == 'du_kien_chi' ? 'selected' : '' }}>Dự kiến chi</option>
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
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $transaction->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->license_plate }} @if($vehicle->driver_name) - {{ $vehicle->driver_name }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Incident (Optional) --}}
                        <div x-data="incidentSearch({{ $transaction->incident_id ?? 'null' }}, '{{ $transaction->incident ? '#'.$transaction->incident->id.' - '.($transaction->incident->patient->name ?? 'N/A') : '' }}')">
                            <label for="incident_search" class="block text-sm font-medium text-gray-700">
                                Chuyến đi (tùy chọn)
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="incident_search" 
                                       x-model="searchTerm"
                                       @input.debounce.300ms="search()"
                                       @focus="showResults = true"
                                       autocomplete="off"
                                       placeholder="Gõ để tìm: ID, tên bệnh nhân, biển số xe..."
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <input type="hidden" id="incident_id" name="incident_id" x-model="selectedId">
                                
                                <!-- Results dropdown -->
                                <div x-show="showResults && results.length > 0" 
                                     @click.away="showResults = false"
                                     class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                    <template x-for="incident in results" :key="incident.id">
                                        <div @click="selectIncident(incident)" 
                                             class="cursor-pointer select-none relative py-2 px-3 hover:bg-indigo-50">
                                            <div class="font-semibold text-gray-900">
                                                #<span x-text="incident.id"></span> - <span x-text="incident.patient_name"></span>
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                🚗 <span x-text="incident.vehicle_plate"></span> • 📅 <span x-text="incident.date"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">💡 Gõ để tìm kiếm chuyến đi</p>
                        </div>

                        {{-- Amount --}}
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">
                                Số tiền <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="amount" name="amount" value="{{ old('amount', $transaction->amount) }}" required min="0" step="1"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Method --}}
                        <div>
                            <label for="method" class="block text-sm font-medium text-gray-700">
                                Phương thức <span class="text-red-500">*</span>
                            </label>
                            <select id="method" name="method" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="cash" {{ old('method', $transaction->method) == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                                <option value="bank" {{ old('method', $transaction->method) == 'bank' ? 'selected' : '' }}>Chuyển khoản</option>
                                <option value="other" {{ old('method', $transaction->method) == 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>

                        {{-- Date --}}
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">
                                Ngày giờ <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" id="date" name="date" value="{{ old('date', $transaction->date->format('Y-m-d\TH:i')) }}" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Note --}}
                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700">
                                Ghi chú
                            </label>
                            <textarea id="note" name="note" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('note', $transaction->note) }}</textarea>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
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

    @push('scripts')
    <script>
        function incidentSearch(initialId = null, initialText = '') {
            return {
                searchTerm: initialText,
                selectedId: initialId || '',
                results: [],
                showResults: false,
                
                async search() {
                    if (this.searchTerm.length < 1) {
                        this.results = [];
                        return;
                    }
                    
                    try {
                        const response = await fetch(`{{ route('incidents.search') }}?q=${encodeURIComponent(this.searchTerm)}`);
                        const data = await response.json();
                        this.results = data.results;
                        this.showResults = true;
                    } catch (error) {
                        console.error('Search error:', error);
                    }
                },
                
                selectIncident(incident) {
                    this.searchTerm = `#${incident.id} - ${incident.patient_name}`;
                    this.selectedId = incident.id;
                    this.showResults = false;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
