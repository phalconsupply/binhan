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
                                <option value="vay_cong_ty" {{ old('type') == 'vay_cong_ty' ? 'selected' : '' }}>Vay công ty</option>
                                <option value="tra_cong_ty" {{ old('type') == 'tra_cong_ty' ? 'selected' : '' }}>Trả nợ công ty</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500" id="type-hint">💡 "Dự kiến chi" sẽ được trừ khỏi lợi nhuận và thống kê riêng là "khoản chưa chi"</p>
                        </div>

                        {{-- Source Account for "Chi" transactions --}}
                        <div id="source-account-container" style="display: none;">
                            <label for="category" class="block text-sm font-medium text-gray-700">
                                Nguồn chi <span class="text-red-500">*</span>
                            </label>
                            <select id="category" name="category" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">🚗 Từ tài khoản xe (chỉ xe có chủ)</option>
                <option value="chi_từ_công_ty">🏢 Từ số dư công ty (áp dụng cho tất cả xe)</option>
                <option value="chi_từ_dự_kiến">💰 Từ quỹ dự kiến chi (áp dụng cho tất cả xe)</option>
            </select>
            <p class="mt-1 text-xs text-gray-500">
                💡 <strong>Tài khoản xe:</strong> Chỉ dùng cho xe có chủ sở hữu<br>
                💡 <strong>Số dư công ty:</strong> Chi trực tiếp từ lợi nhuận công ty<br>
                💡 <strong>Quỹ dự kiến chi:</strong> Chi từ quỹ đã dự trù trước
                            </label>
                            <select id="vehicle_id" name="vehicle_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Không liên kết --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" 
                                            data-has-owner="{{ $vehicle->hasOwner() ? '1' : '0' }}"
                                            {{ old('vehicle_id', $selectedIncident?->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->license_plate }} @if($vehicle->driver_name) - {{ $vehicle->driver_name }} @endif{{ $vehicle->hasOwner() ? '' : ' (Không có chủ)' }}
                                    </option>
                                @endforeach
                            </select>
                            <p id="vehicle-hint" class="mt-1 text-xs text-gray-500" style="display: none;"></p>
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
        
        function handleCategoryChange(category) {
            const vehicleSelect = document.getElementById('vehicle_id');
            const vehicleHint = document.getElementById('vehicle-hint');
            const options = vehicleSelect.querySelectorAll('option');
            
            if (category === '') { // Chi từ tài khoản xe
                // Chỉ hiển thị xe có owner
                let hasOwnerVehicles = false;
                options.forEach(option => {
                    if (option.value === '') {
                        option.style.display = 'block';
                        return;
                    }
                    const hasOwner = option.getAttribute('data-has-owner') === '1';
                    option.style.display = hasOwner ? 'block' : 'none';
                    if (hasOwner) hasOwnerVehicles = true;
                    
                    // Bỏ chọn nếu xe hiện tại không có owner
                    if (!hasOwner && option.selected) {
                        vehicleSelect.value = '';
                    }
                });
                vehicleHint.textContent = '⚠️ Chỉ hiển thị xe có chủ sở hữu';
                vehicleHint.style.display = 'block';
                vehicleHint.className = 'mt-1 text-xs text-orange-600 font-medium';
            } else {
                // Hiển thị tất cả xe
                options.forEach(option => {
                    option.style.display = 'block';
                });
                vehicleHint.style.display = 'none';
            }
        }
        
        function handleTypeChange(type) {
            const incidentContainer = document.getElementById('incident-container');
            const incidentInput = document.getElementById('incident_id');
            const typeHint = document.getElementById('type-hint');
            const vehicleSelect = document.getElementById('vehicle_id');
            const sourceAccountContainer = document.getElementById('source-account-container');
            const categorySelect = document.getElementById('category');
            
            // Show source account selection only for "chi" type
            if (type === 'chi') {
                sourceAccountContainer.style.display = 'block';
                // Trigger filter based on current category
                if (categorySelect) {
                    handleCategoryChange(categorySelect.value);
                }
            } else {
                sourceAccountContainer.style.display = 'none';
                // Reset vehicle filter
                const vehicleOptions = vehicleSelect.querySelectorAll('option');
                vehicleOptions.forEach(option => {
                    option.style.display = 'block';
                });
                const vehicleHint = document.getElementById('vehicle-hint');
                if (vehicleHint) vehicleHint.style.display = 'none';
            }
            
            if (type === 'nop_quy') {
                // Ẩn chuyến đi khi chọn Nộp quỹ
                incidentContainer.style.display = 'none';
                incidentInput.value = '';
                typeHint.textContent = '💡 "Nộp quỹ" sẽ cộng tiền vào quỹ. Nếu chọn xe liên quan, tiền sẽ cộng vào số dư xe (không tính phí 15%). Nếu không chọn xe hoặc xe không có chủ, tiền sẽ cộng vào lợi nhuận công ty.';
                if (vehicleSelect) vehicleSelect.removeAttribute('required');
            } else if (type === 'vay_cong_ty') {
                // Ẩn chuyến đi và BẮT BUỘC chọn xe khi vay
                incidentContainer.style.display = 'none';
                incidentInput.value = '';
                typeHint.textContent = '💡 "Vay công ty" sẽ tạo 2 giao dịch: Chi từ công ty (trừ lợi nhuận công ty) và Thu cho xe (không tính phí 15%). Phải chọn xe!';
                if (vehicleSelect) vehicleSelect.setAttribute('required', 'required');
            } else if (type === 'tra_cong_ty') {
                // Ẩn chuyến đi khi trả nợ
                incidentContainer.style.display = 'none';
                incidentInput.value = '';
                typeHint.textContent = '💡 "Trả nợ công ty" sẽ trừ tiền từ xe và cộng vào lợi nhuận công ty. Phải chọn xe!';
                if (vehicleSelect) vehicleSelect.setAttribute('required', 'required');
            } else {
                // Hiện lại chuyến đi cho các loại khác
                incidentContainer.style.display = 'block';
                if (vehicleSelect) vehicleSelect.removeAttribute('required');
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
            const categorySelect = document.getElementById('category');
            
            if (typeSelect && (typeSelect.value === 'nop_quy' || typeSelect.value === 'vay_cong_ty' || typeSelect.value === 'tra_cong_ty')) {
                handleTypeChange(typeSelect.value);
            }
            
            // Add category change listener
            if (categorySelect) {
                categorySelect.addEventListener('change', function() {
                    handleCategoryChange(this.value);
                });
                
                // Trigger on load if type is 'chi'
                if (typeSelect && typeSelect.value === 'chi') {
                    handleCategoryChange(categorySelect.value);
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
