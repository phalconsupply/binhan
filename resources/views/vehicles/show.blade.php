<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chi tiết xe: {{ $vehicle->license_plate }}
            </h2>
            <div class="space-x-2">
                @can('edit vehicles')
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Sửa
                </a>
                @endcan
                <a href="{{ route('vehicles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Quay lại
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Vehicle Info --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Thông tin xe</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Biển số</p>
                            <p class="text-lg font-semibold text-blue-600">{{ $vehicle->license_plate }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Trạng thái</p>
                            <span class="inline-flex px-2 text-xs leading-5 font-semibold rounded-full 
                                {{ $vehicle->status == 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $vehicle->status == 'inactive' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $vehicle->status == 'maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ $vehicle->status_label }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Mẫu xe</p>
                            <p class="text-base">{{ $vehicle->model ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tài xế</p>
                            <p class="text-base">{{ $vehicle->driver_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Lái xe chính</p>
                            <p class="text-base">
                                @if($vehicle->driver)
                                    <a href="{{ route('staff.show', $vehicle->driver) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $vehicle->driver->employee_code }} - {{ $vehicle->driver->full_name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Chủ xe</p>
                            <p class="text-base">
                                @if($vehicle->owner)
                                    <a href="{{ route('staff.show', $vehicle->owner) }}" class="text-orange-600 hover:text-orange-900 font-semibold">
                                        {{ $vehicle->owner->employee_code }} - {{ $vehicle->owner->full_name }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Chưa có</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Số điện thoại</p>
                            <p class="text-base">{{ $vehicle->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Ngày tạo</p>
                            <p class="text-base">{{ $vehicle->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($vehicle->note)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500">Ghi chú</p>
                            <p class="text-base">{{ $vehicle->note }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Tổng chuyến đi</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_incidents'] }}</p>
                        <p class="text-xs text-gray-500">{{ $stats['this_month_incidents'] }} chuyến tháng này</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Tổng thu</p>
                        <p class="text-xl font-bold text-green-600">{{ number_format($stats['total_revenue'], 0, ',', '.') }}đ</p>
                        <p class="text-xs text-gray-500">{{ number_format($stats['month_revenue'], 0, ',', '.') }}đ tháng này</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Tổng chi</p>
                        <p class="text-xl font-bold text-red-600">{{ number_format($stats['total_expense'], 0, ',', '.') }}đ</p>
                        <p class="text-xs text-gray-500">{{ number_format($stats['month_expense'], 0, ',', '.') }}đ tháng này</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Lợi nhuận</p>
                        <p class="text-xl font-bold {{ $stats['total_net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($stats['total_net'], 0, ',', '.') }}đ
                        </p>
                        <p class="text-xs {{ $stats['month_net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($stats['month_net'], 0, ',', '.') }}đ tháng này
                        </p>
                    </div>
                </div>
            </div>

            {{-- Filter Section --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">🔍 Lọc dữ liệu giao dịch</h3>
                    <form method="GET" action="{{ route('vehicles.show', $vehicle) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Loại giao dịch</label>
                            <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Tất cả</option>
                                <option value="thu" {{ request('type') == 'thu' ? 'selected' : '' }}>Thu</option>
                                <option value="chi" {{ request('type') == 'chi' ? 'selected' : '' }}>Chi</option>
                                <option value="du_kien_chi" {{ request('type') == 'du_kien_chi' ? 'selected' : '' }}>Dự kiến chi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 whitespace-nowrap">
                                Lọc
                            </button>
                            @if(request()->hasAny(['type', 'start_date', 'end_date']))
                            <a href="{{ route('vehicles.show', $vehicle) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 whitespace-nowrap">
                                Xóa lọc
                            </a>
                            @endif
                        </div>
                    </form>
                    
                    @if(request()->hasAny(['type', 'start_date', 'end_date']))
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <p class="text-sm text-blue-800">
                            <strong>Đang lọc:</strong>
                            @if(request('type'))
                                Loại: <span class="font-semibold">{{ request('type') == 'thu' ? 'Thu' : (request('type') == 'chi' ? 'Chi' : 'Dự kiến chi') }}</span>
                            @endif
                            @if(request('start_date'))
                                • Từ: <span class="font-semibold">{{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}</span>
                            @endif
                            @if(request('end_date'))
                                • Đến: <span class="font-semibold">{{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}</span>
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Recent Incidents --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Chuyến đi gần đây</h3>
                        @if($vehicle->incidents->isEmpty())
                            <p class="text-gray-500 text-sm">Chưa có chuyến đi nào.</p>
                        @else
                            <div class="space-y-3">
                                @foreach($vehicle->incidents as $incident)
                                <div class="border-l-4 {{ $incident->transactions->count() > 0 ? 'border-green-500' : 'border-gray-300' }} pl-4 py-2">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-800">
                                                @if($incident->patient)
                                                    {{ $incident->patient->name }}
                                                @else
                                                    Không có thông tin BN
                                                @endif
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ $incident->date->format('d/m/Y H:i') }}
                                                @if($incident->destination)
                                                    • {{ $incident->destination }}
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Bởi {{ $incident->dispatcher->name }}
                                            </p>
                                        </div>
                                        @if($incident->transactions->count() > 0)
                                        <div class="text-right">
                                            <p class="text-sm font-semibold {{ $incident->net_amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ number_format($incident->net_amount, 0, ',', '.') }}đ
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Recent Transactions --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Giao dịch 
                            @if(request()->hasAny(['type', 'start_date', 'end_date']))
                            <span class="text-sm font-normal text-gray-500">(đã lọc)</span>
                            @else
                            <span class="text-sm font-normal text-gray-500">(gần đây)</span>
                            @endif
                        </h3>
                        @if($transactions->isEmpty())
                            <p class="text-gray-500 text-sm">Không có giao dịch nào
                                @if(request()->hasAny(['type', 'start_date', 'end_date']))
                                    phù hợp với bộ lọc
                                @endif.
                            </p>
                        @else
                            <div class="space-y-3">
                                @foreach($transactions as $group)
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    {{-- Header - Click để mở rộng --}}
                                    <div class="bg-gray-50 px-4 py-3 cursor-pointer hover:bg-gray-100 transition" onclick="toggleDetail('detail-{{ $loop->index }}')">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                {{-- Icon mở rộng --}}
                                                <svg id="icon-{{ $loop->index }}" class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                                
                                                {{-- Mã chuyến đi --}}
                                                <div>
                                                    @if($group['incident'])
                                                        <a href="{{ route('incidents.show', $group['incident']) }}" class="text-base font-semibold text-blue-600 hover:text-blue-800" onclick="event.stopPropagation()">
                                                            Chuyến #{{ $group['incident']->id }}
                                                        </a>
                                                    @else
                                                        <span class="text-base font-semibold text-gray-600">Giao dịch khác</span>
                                                    @endif
                                                </div>

                                                {{-- Thông tin cơ bản --}}
                                                <div class="flex items-center space-x-3 text-sm text-gray-600">
                                                    <span>{{ $group['date']->format('d/m/Y') }}</span>
                                                    @if($group['incident'] && $group['incident']->patient)
                                                        <span>•</span>
                                                        <span>{{ $group['incident']->patient->name }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Tổng thu chi --}}
                                            <div class="flex items-center space-x-6 text-sm">
                                                <div class="text-right">
                                                    <div class="text-green-600 font-semibold">+{{ number_format($group['total_revenue'], 0, ',', '.') }}đ</div>
                                                    <div class="text-xs text-gray-500">Thu</div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-red-600 font-semibold">-{{ number_format($group['total_expense'], 0, ',', '.') }}đ</div>
                                                    <div class="text-xs text-gray-500">Chi</div>
                                                </div>
                                                @if($group['total_planned_expense'] > 0)
                                                <div class="text-right">
                                                    <div class="text-orange-600 font-semibold">-{{ number_format($group['total_planned_expense'], 0, ',', '.') }}đ</div>
                                                    <div class="text-xs text-gray-500">Dự kiến chi</div>
                                                </div>
                                                @endif
                                                <div class="text-right min-w-[120px]">
                                                    <div class="text-lg font-bold {{ $group['net_amount'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $group['net_amount'] >= 0 ? '+' : '' }}{{ number_format($group['net_amount'], 0, ',', '.') }}đ
                                                    </div>
                                                    <div class="text-xs text-gray-500">Lợi nhuận</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Chi tiết giao dịch - Ẩn mặc định --}}
                                    <div id="detail-{{ $loop->index }}" class="hidden bg-white">
                                        <div class="px-4 py-3 border-t border-gray-200">
                                            <table class="w-full text-sm">
                                                <thead class="text-xs text-gray-500 uppercase border-b">
                                                    <tr>
                                                        <th class="py-2 text-left">Loại</th>
                                                        <th class="py-2 text-left">Tên khoản</th>
                                                        <th class="py-2 text-right">Số tiền</th>
                                                        <th class="py-2 text-left">Phương thức</th>
                                                        <th class="py-2 text-left">Ngày giờ</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    @foreach($group['transactions'] as $transaction)
                                                    <tr class="hover:bg-gray-50 {{ $transaction->category == 'điều_chỉnh_lương' ? 'bg-blue-50' : '' }}">
                                                        <td class="py-2">
                                                            <span class="px-2 py-1 text-xs rounded-full {{ $transaction->type == 'thu' ? 'bg-green-100 text-green-800' : ($transaction->type == 'du_kien_chi' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                                                {{ $transaction->type_label }}
                                                            </span>
                                                            @if($transaction->category == 'điều_chỉnh_lương')
                                                                <span class="ml-1 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                                    ⚙️ Điều chỉnh
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="py-2">
                                                            {{ $transaction->note ?? '-' }}
                                                        </td>
                                                        <td class="py-2 text-right font-semibold {{ $transaction->type == 'thu' ? 'text-green-600' : ($transaction->type == 'du_kien_chi' ? 'text-orange-600' : 'text-red-600') }}">
                                                            {{ $transaction->type == 'thu' ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', '.') }}đ
                                                        </td>
                                                        <td class="py-2">{{ $transaction->method_label }}</td>
                                                        <td class="py-2 text-xs text-gray-500">{{ $transaction->date->format('d/m/Y H:i') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            {{-- Pagination --}}
                            <div class="mt-4">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Ghi chú</h3>
                            @can('create vehicles')
                            <button onclick="document.getElementById('vehicleNoteForm').classList.toggle('hidden')" class="text-sm text-indigo-600 hover:text-indigo-900">
                                + Thêm ghi chú
                            </button>
                            @endcan
                        </div>

                        {{-- Add Note Form --}}
                        @can('create vehicles')
                        <div id="vehicleNoteForm" class="hidden mb-4">
                            <form action="{{ route('notes.store') }}" method="POST" class="space-y-3">
                                @csrf
                                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                                
                                <div>
                                    <select name="severity" required class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="info">Thông tin</option>
                                        <option value="warning">Cảnh báo</option>
                                        <option value="critical">Quan trọng</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <textarea name="note" rows="3" required placeholder="Nhập ghi chú..."
                                              class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>
                                
                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="document.getElementById('vehicleNoteForm').classList.add('hidden')" 
                                            class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                        Hủy
                                    </button>
                                    <button type="submit" class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                        Lưu
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endcan

                        @if($vehicle->notes->isEmpty())
                            <p class="text-gray-500 text-sm">Chưa có ghi chú nào.</p>
                        @else
                            <div class="space-y-2">
                                @foreach($vehicle->notes as $note)
                                <div class="p-3 rounded border-l-4 {{ $note->severity == 'critical' ? 'bg-red-50 border-red-500' : ($note->severity == 'warning' ? 'bg-yellow-50 border-yellow-500' : 'bg-gray-50 border-blue-500') }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="text-sm">{{ $note->note }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $note->user->name }} • {{ $note->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                        @can('delete vehicles')
                                        <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-900 ml-2" 
                                                    onclick="return confirm('Xóa ghi chú này?')">
                                                Xóa
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleDetail(id) {
            const detail = document.getElementById(id);
            const iconId = id.replace('detail-', 'icon-');
            const icon = document.getElementById(iconId);
            
            if (detail.classList.contains('hidden')) {
                detail.classList.remove('hidden');
                icon.style.transform = 'rotate(90deg)';
            } else {
                detail.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
    @endpush
</x-app-layout>
