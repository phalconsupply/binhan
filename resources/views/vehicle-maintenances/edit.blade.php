<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chỉnh sửa bảo trì xe
            </h2>
            <a href="{{ route('vehicle-maintenances.index') }}" class="text-indigo-600 hover:text-indigo-900">
                ← Quay lại danh sách
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('vehicle-maintenances.update', $vehicleMaintenance) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="vehicle_id" class="block text-sm font-medium text-gray-700">
                                Xe <span class="text-red-500">*</span>
                            </label>
                            <select id="vehicle_id" name="vehicle_id" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Chọn xe --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $vehicleMaintenance->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->license_plate }} - {{ $vehicle->driver_name ?? 'Chưa có tài xế' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700">
                                    Ngày bảo trì <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="date" name="date" value="{{ old('date', $vehicleMaintenance->date->format('Y-m-d')) }}" required 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="cost" class="block text-sm font-medium text-gray-700">
                                    Chi phí (đ) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="cost" name="cost" value="{{ number_format(old('cost', $vehicleMaintenance->cost), 0, ',', '.') }}" required data-currency 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Ví dụ: 518298">
                                @error('cost')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div x-data="serviceAutocomplete('{{ old('maintenance_service_id', $vehicleMaintenance->maintenance_service_id) }}', '{{ $vehicleMaintenance->maintenanceService?->name ?? '' }}')">
                            <label for="service_search" class="block text-sm font-medium text-gray-700">
                                Loại dịch vụ bảo trì
                            </label>
                            <div class="relative mt-1">
                                <input type="text" 
                                    id="service_search"
                                    x-model="searchTerm"
                                    @input.debounce.300ms="search()"
                                    @focus="showResults = true"
                                    @keydown.escape="showResults = false"
                                    placeholder="Nhập tên dịch vụ..."
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                
                                <input type="hidden" name="maintenance_service_id" x-model="selectedId">
                                <input type="hidden" name="maintenance_service_name" x-model="searchTerm">
                                
                                <div x-show="showResults && results.length > 0" 
                                     @click.away="showResults = false"
                                     class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                    
                                    <template x-for="result in results" :key="result.id">
                                        <div @click="selectService(result)" 
                                             class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white">
                                            <span x-text="result.text"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                💡 Tự động thêm mới nếu chưa có trong danh sách
                            </p>
                            @error('maintenance_service_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="partnerAutocomplete('{{ old('partner_id', $vehicleMaintenance->partner_id) }}', '{{ $vehicleMaintenance->partner?->name ?? '' }}')">
                            <label for="partner_search" class="block text-sm font-medium text-gray-700">
                                Đối tác bảo trì
                            </label>
                            <div class="relative mt-1">
                                <input type="text" 
                                    id="partner_search"
                                    x-model="searchTerm"
                                    @input.debounce.300ms="search()"
                                    @focus="showResults = true"
                                    @keydown.escape="showResults = false"
                                    placeholder="Nhập tên đối tác..."
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                
                                <input type="hidden" name="partner_id" x-model="selectedId">
                                <input type="hidden" name="partner_name" x-model="searchTerm">
                                
                                <div x-show="showResults && results.length > 0" 
                                     @click.away="showResults = false"
                                     class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                    
                                    <template x-for="result in results" :key="result.id">
                                        <div @click="selectPartner(result)" 
                                             class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white">
                                            <span x-text="result.text"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                💡 Tự động thêm mới nếu chưa có trong danh sách
                            </p>
                            @error('partner_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mileage" class="block text-sm font-medium text-gray-700">
                                Số km
                            </label>
                            <input type="number" id="mileage" name="mileage" value="{{ old('mileage', $vehicleMaintenance->mileage) }}" min="0" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('mileage')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Mô tả công việc
                            </label>
                            <textarea id="description" name="description" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $vehicleMaintenance->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700">
                                Ghi chú
                            </label>
                            <textarea id="note" name="note" rows="2" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('note', $vehicleMaintenance->note) }}</textarea>
                            @error('note')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <input type="hidden" name="action" id="form_action" value="save_and_exit">
                        
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('vehicle-maintenances.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Hủy
                            </a>
                            <button type="submit" onclick="document.getElementById('form_action').value='save_and_continue'" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                💾 Lưu và tiếp tục
                            </button>
                            <button type="submit" onclick="document.getElementById('form_action').value='save_and_exit'" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                ✓ Lưu và thoát
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function serviceAutocomplete(initialId = '', initialText = '') {
            return {
                searchTerm: initialText,
                selectedId: initialId,
                results: [],
                showResults: false,
                
                async search() {
                    if (this.searchTerm.length < 1) {
                        this.results = [];
                        return;
                    }
                    
                    try {
                        const response = await fetch(`{{ route('vehicle-maintenances.search.services') }}?q=${encodeURIComponent(this.searchTerm)}`);
                        const data = await response.json();
                        this.results = data.results;
                    } catch (error) {
                        console.error('Search error:', error);
                    }
                },
                
                selectService(service) {
                    this.searchTerm = service.text;
                    this.selectedId = service.id;
                    this.showResults = false;
                }
            }
        }
        
        function partnerAutocomplete(initialId = '', initialText = '') {
            return {
                searchTerm: initialText,
                selectedId: initialId,
                results: [],
                showResults: false,
                
                async search() {
                    if (this.searchTerm.length < 1) {
                        this.results = [];
                        return;
                    }
                    
                    try {
                        const response = await fetch(`{{ route('vehicle-maintenances.search.partners') }}?q=${encodeURIComponent(this.searchTerm)}`);
                        const data = await response.json();
                        this.results = data.results;
                    } catch (error) {
                        console.error('Search error:', error);
                    }
                },
                
                selectPartner(partner) {
                    this.searchTerm = partner.text;
                    this.selectedId = partner.id;
                    this.showResults = false;
                }
            }
        }
    </script>
</x-app-layout>
