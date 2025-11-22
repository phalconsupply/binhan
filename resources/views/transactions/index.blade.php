<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Quản lý Giao dịch
            </h2>
            @can('create transactions')
            <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm giao dịch
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Tổng thu</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_revenue'], 0, ',', '.') }}đ</p>
                    <p class="text-xs text-gray-500 mt-1">Tháng: {{ number_format($stats['month_revenue'], 0, ',', '.') }}đ</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Tổng chi</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($stats['total_expense'], 0, ',', '.') }}đ</p>
                    <p class="text-xs text-gray-500 mt-1">Tháng: {{ number_format($stats['month_expense'], 0, ',', '.') }}đ</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Chi từ công ty</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($stats['company_expense'], 0, ',', '.') }}đ</p>
                    <p class="text-xs text-gray-500 mt-1">Tháng: {{ number_format($stats['company_month_expense'], 0, ',', '.') }}đ</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Lợi nhuận</p>
                    <p class="text-2xl font-bold {{ $stats['total_net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($stats['total_net'], 0, ',', '.') }}đ
                    </p>
                    <p class="text-xs {{ $stats['month_net'] >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                        Tháng: {{ number_format($stats['month_net'], 0, ',', '.') }}đ
                    </p>
                </div>
            </div>

            {{-- Search & Filter --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('transactions.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Tất cả loại</option>
                                    <option value="thu" {{ request('type') == 'thu' ? 'selected' : '' }}>Thu</option>
                                    <option value="chi" {{ request('type') == 'chi' ? 'selected' : '' }}>Chi</option>
                                </select>
                            </div>
                            <div>
                                <select name="vehicle_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Tất cả xe</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->license_plate }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Tìm kiếm
                            </button>
                            @if(request()->hasAny(['search', 'type', 'vehicle_id', 'date_from', 'date_to']))
                            <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Xóa lọc
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Transactions Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($transactions->isEmpty())
                        <p class="text-gray-500 text-center py-8">Không tìm thấy giao dịch nào.</p>
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
                                                <span>•</span>
                                                <a href="{{ route('vehicles.show', $group['vehicle']) }}" class="text-blue-600 hover:text-blue-800 font-medium" onclick="event.stopPropagation()">
                                                    {{ $group['vehicle']->license_plate }}
                                                </a>
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
                                            <div class="text-right min-w-[120px]">
                                                <div class="text-lg font-bold {{ $group['net_amount'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $group['net_amount'] >= 0 ? '+' : '' }}{{ number_format($group['net_amount'], 0, ',', '.') }}đ
                                                </div>
                                                <div class="text-xs text-gray-500">Lợi nhuận</div>
                                            </div>
                                            
                                            {{-- Nút xóa hết --}}
                                            @if($group['incident'])
                                                @can('delete transactions')
                                                <form action="{{ route('transactions.destroyByIncident', $group['incident']->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa TẤT CẢ {{ $group['transactions']->count() }} giao dịch của chuyến này?')" onclick="event.stopPropagation()">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 text-xs bg-red-600 text-white rounded hover:bg-red-700 transition">
                                                        Xóa hết
                                                    </button>
                                                </form>
                                                @endcan
                                            @endif
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
                                                    <th class="py-2 text-right">Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach($group['transactions'] as $transaction)
                                                <tr class="hover:bg-gray-50 {{ $transaction->category == 'điều_chỉnh_lương' ? 'bg-blue-50' : '' }}">
                                                    <td class="py-2">
                                                        <span class="px-2 py-1 text-xs rounded-full {{ $transaction->type == 'thu' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
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
                                                        @if($transaction->category == 'điều_chỉnh_lương' && !$transaction->incident_id)
                                                            <span class="text-xs text-orange-600">(từ quỹ công ty)</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-2 text-right font-semibold {{ $transaction->type == 'thu' ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $transaction->type == 'thu' ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', '.') }}đ
                                                    </td>
                                                    <td class="py-2">{{ $transaction->method_label }}</td>
                                                    <td class="py-2 text-right space-x-2">
                                                        @can('edit transactions')
                                                        <a href="{{ route('transactions.edit', $transaction) }}" class="text-indigo-600 hover:text-indigo-900">Sửa</a>
                                                        @endcan
                                                        @can('delete transactions')
                                                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                                        </form>
                                                        @endcan
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @endif
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
