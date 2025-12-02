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
                                onchange="handleTypeChange(this.value)"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="thu" {{ old('type') == 'thu' ? 'selected' : '' }}>Thu</option>
                                <option value="chi" {{ old('type') == 'chi' ? 'selected' : '' }}>Chi</option>
                                <option value="du_kien_chi" {{ old('type') == 'du_kien_chi' ? 'selected' : '' }}>Dự kiến chi</option>
                                <option value="nop_quy" {{ old('type') == 'nop_quy' ? 'selected' : '' }}>Nộp quỹ</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500" id="type-hint">💡 "Dự kiến chi" sẽ được trừ khỏi lợi nhuận và thống kê riêng là "khoản chưa chi"</p>
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
                        <div id="incident-container" x-data="incidentSearch({{ $selectedIncident ? $selectedIncident->id : 'null' }}, '{{ $selectedIncident ? '#'.$selectedIncident->id.' - '.($selectedIncident->patient->name ?? 'N/A') : '' }}')">
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
                            <input type="text" id="amount" name="amount" value="{{ old('amount') }}" required data-currency
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
        
        function handleTypeChange(type) {
            const incidentContainer = document.getElementById('incident-container');
            const incidentInput = document.getElementById('incident_id');
            const typeHint = document.getElementById('type-hint');
            
            if (type === 'nop_quy') {
                // Ẩn chuyến đi khi chọn Nộp quỹ
                incidentContainer.style.display = 'none';
                incidentInput.value = '';
                typeHint.textContent = '💡 "Nộp quỹ" sẽ cộng tiền vào quỹ. Nếu chọn xe liên quan, tiền sẽ cộng vào số dư xe (không tính phí 15%). Nếu không chọn xe hoặc xe không có chủ, tiền sẽ cộng vào lợi nhuận công ty.';
            } else {
                // Hiện lại chuyến đi cho các loại khác
                incidentContainer.style.display = 'block';
                if (type === 'du_kien_chi') {
                    typeHint.textContent = '💡 "Dự kiến chi" sẽ được trừ khỏi lợi nhuận và thống kê riêng là "khoản chưa chi"';
                } else {
                    typeHint.textContent = '';
                }
            }
        }
        
        // Gọi khi load trang nếu đã có giá trị cũ
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            if (typeSelect && typeSelect.value === 'nop_quy') {
                handleTypeChange('nop_quy');
            }
        });
    </script>
    @endpush
</x-app-layout>
