<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Quản lý Bệnh nhân') }}
            </h2>
            @can('manage patients')
            <a href="{{ route('patients.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Thêm bệnh nhân
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Thống kê -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Tổng bệnh nhân</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $statistics['total'] }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Nam</div>
                        <div class="text-2xl font-bold text-blue-600">{{ $statistics['male'] }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Nữ</div>
                        <div class="text-2xl font-bold text-pink-600">{{ $statistics['female'] }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Có chuyến đi</div>
                        <div class="text-2xl font-bold text-green-600">{{ $statistics['with_incidents'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Bộ lọc -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('patients.index') }}" class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Tên, SĐT, địa chỉ..."
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="min-w-[150px]">
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
                            <select name="gender" id="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Tất cả</option>
                                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                        <div class="min-w-[150px]">
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp</label>
                            <select name="sort" id="sort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="id" {{ request('sort') == 'id' ? 'selected' : '' }}>ID Chuyến</option>
                                <option value="date" {{ request('sort') == 'date' ? 'selected' : '' }}>Ngày</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Lọc
                        </button>
                        <a href="{{ route('patients.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Reset
                        </a>
                    </form>
                </div>
            </div>

            <!-- Danh sách bệnh nhân -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($incidents->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Chuyến</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bệnh nhân</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Liên hệ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tuổi/Giới tính</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày chuyến</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($incidents as $incident)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('incidents.show', $incident) }}" 
                                           class="text-blue-600 hover:text-blue-900 font-bold text-lg">
                                            #{{ $incident->id }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $incident->patient->name }}</div>
                                        @if($incident->patient->address)
                                        <div class="text-sm text-gray-500">{{ Str::limit($incident->patient->address, 30) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $incident->patient->phone }}</div>
                                        @if($incident->patient->emergency_contact)
                                        <div class="text-sm text-gray-500">SOS: {{ $incident->patient->emergency_contact }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $incident->patient->age }} tuổi / {{ $incident->patient->gender_label }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $incident->date->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="toggleDetail({{ $incident->id }})" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Chi tiết
                                        </button>
                                    </td>
                                </tr>
                                <!-- Chi tiết chuyến đi (collapsed by default) -->
                                <tr id="detail-{{ $incident->id }}" class="hidden bg-gray-50">
                                    <td colspan="6" class="px-6 py-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Thông tin chuyến đi -->
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    Thông tin chuyến
                                                </h4>
                                                <dl class="space-y-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <dt class="text-gray-600">Xe:</dt>
                                                        <dd class="font-medium text-gray-900">{{ $incident->vehicle->license_plate ?? 'N/A' }}</dd>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <dt class="text-gray-600">Điểm đi:</dt>
                                                        <dd class="font-medium text-gray-900">{{ $incident->fromLocation->name ?? $incident->destination }}</dd>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <dt class="text-gray-600">Điểm đến:</dt>
                                                        <dd class="font-medium text-gray-900">{{ $incident->toLocation->name ?? $incident->destination }}</dd>
                                                    </div>
                                                    @if($incident->partner)
                                                    <div class="flex justify-between">
                                                        <dt class="text-gray-600">Đối tác:</dt>
                                                        <dd class="font-medium text-gray-900">{{ $incident->partner->name }}</dd>
                                                    </div>
                                                    @endif
                                                    @if($incident->dispatcher)
                                                    <div class="flex justify-between">
                                                        <dt class="text-gray-600">Điều phối:</dt>
                                                        <dd class="font-medium text-gray-900">{{ $incident->dispatcher->name }}</dd>
                                                    </div>
                                                    @endif
                                                </dl>
                                            </div>

                                            <!-- Thông tin tài chính -->
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Tài chính
                                                </h4>
                                                <dl class="space-y-2 text-sm">
                                                    @if($incident->total_revenue)
                                                    <div class="flex justify-between">
                                                        <dt class="text-gray-600">Tổng thu:</dt>
                                                        <dd class="font-bold text-green-600">{{ number_format($incident->total_revenue) }}₫</dd>
                                                    </div>
                                                    @endif
                                                    @if($incident->commission_amount)
                                                    <div class="flex justify-between">
                                                        <dt class="text-gray-600">Hoa hồng:</dt>
                                                        <dd class="font-medium text-gray-900">{{ number_format($incident->commission_amount) }}₫</dd>
                                                    </div>
                                                    @endif
                                                    @if($incident->total_expense)
                                                    <div class="flex justify-between">
                                                        <dt class="text-gray-600">Chi phí:</dt>
                                                        <dd class="font-medium text-red-600">{{ number_format($incident->total_expense) }}₫</dd>
                                                    </div>
                                                    @endif
                                                    @if($incident->net_profit)
                                                    <div class="flex justify-between pt-2 border-t border-gray-200">
                                                        <dt class="font-semibold text-gray-900">Lợi nhuận:</dt>
                                                        <dd class="font-bold text-blue-600">{{ number_format($incident->net_profit) }}₫</dd>
                                                    </div>
                                                    @endif
                                                </dl>
                                            </div>
                                        </div>
                                        
                                        <!-- Ghi chú -->
                                        @if($incident->summary)
                                        <div class="mt-4 bg-white p-4 rounded-lg shadow-sm">
                                            <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                </svg>
                                                Ghi chú
                                            </h4>
                                            <p class="text-sm text-gray-700">{{ $incident->summary }}</p>
                                        </div>
                                        @endif

                                        <!-- Nút sửa bệnh nhân -->
                                        <div class="mt-4 flex justify-end">
                                            <a href="{{ route('patients.edit', $incident->patient) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Sửa thông tin bệnh nhân
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $incidents->links() }}
                    </div>
                    
                    <script>
                        function toggleDetail(id) {
                            const detailRow = document.getElementById('detail-' + id);
                            if (detailRow.classList.contains('hidden')) {
                                detailRow.classList.remove('hidden');
                            } else {
                                detailRow.classList.add('hidden');
                            }
                        }
                    </script>
                    @else
                    <p class="text-gray-500 text-center py-4">Không tìm thấy chuyến đi nào.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
