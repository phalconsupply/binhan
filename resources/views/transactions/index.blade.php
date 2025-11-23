<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Quản lý Giao dịch
            </h2>
            <div class="flex gap-2">
                @can('manage settings')
                <button onclick="window.dispatchEvent(new CustomEvent('open-dividend-modal'))" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Chia cổ tức
                </button>
                @endcan
                @can('create transactions')
                <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Thêm giao dịch
                </a>
                @endcan
            </div>
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
                    <p class="text-sm text-gray-500">Dự kiến chi</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($stats['total_planned_expense'], 0, ',', '.') }}đ</p>
                    <p class="text-xs text-gray-500 mt-1">Tháng: {{ number_format($stats['month_planned_expense'], 0, ',', '.') }}đ</p>
                    <p class="text-xs text-gray-500">Công ty: {{ number_format($stats['company_planned_expense'], 0, ',', '.') }}đ</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Chi từ công ty</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['company_expense'], 0, ',', '.') }}đ</p>
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
                                    <option value="du_kien_chi" {{ request('type') == 'du_kien_chi' ? 'selected' : '' }}>Dự kiến chi</option>
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
                                                @if($group['vehicle'])
                                                    <span>•</span>
                                                    <a href="{{ route('vehicles.show', $group['vehicle']) }}" class="text-blue-600 hover:text-blue-800 font-medium" onclick="event.stopPropagation()">
                                                        {{ $group['vehicle']->license_plate }}
                                                    </a>
                                                @else
                                                    <span>•</span>
                                                    <span class="text-gray-500">🏢 Quỹ công ty</span>
                                                @endif
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
                                            @if($group['has_owner'] && $group['management_fee'] > 0)
                                            <div class="text-right min-w-[120px]">
                                                <div class="text-base font-semibold text-orange-600">
                                                    {{ number_format($group['management_fee'], 0, ',', '.') }}đ
                                                </div>
                                                <div class="text-xs text-gray-500">Phí 15%</div>
                                            </div>
                                            <div class="text-right min-w-[120px]">
                                                <div class="text-lg font-bold text-blue-600">
                                                    +{{ number_format($group['profit_after_fee'], 0, ',', '.') }}đ
                                                </div>
                                                <div class="text-xs text-gray-500">Cho chủ xe</div>
                                            </div>
                                            @endif
                                            
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

                                {{-- Chi tiết giao dịch - Hiển thị mặc định cho "Giao dịch khác", ẩn cho incidents --}}
                                <div id="detail-{{ $loop->index }}" class="{{ $group['incident'] ? 'hidden' : '' }} bg-white">
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

        // Initialize icons for expanded groups (e.g., "Giao dịch khác")
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[id^="detail-"]').forEach(function(detail) {
                if (!detail.classList.contains('hidden')) {
                    const iconId = detail.id.replace('detail-', 'icon-');
                    const icon = document.getElementById(iconId);
                    if (icon) {
                        icon.style.transform = 'rotate(90deg)';
                    }
                }
            });
        });
    </script>
    @endpush

    {{-- Dividend Distribution Modal --}}
    <div x-data="dividendModal()" x-show="showModal" @open-dividend-modal.window="openModal()" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">💰 Chia cổ tức</h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('transactions.distribute-dividend') }}">
                        @csrf
                        
                        {{-- Company Profit --}}
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-gray-600">Số dư lợi nhuận công ty hiện tại</p>
                                    <p class="text-3xl font-bold text-blue-600">
                                        {{ number_format($stats['total_net'], 0, ',', '.') }}đ
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Tháng này</p>
                                    <p class="text-lg font-semibold {{ $stats['month_net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($stats['month_net'], 0, ',', '.') }}đ
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Distribution Percentage --}}
                        <div class="mb-6">
                            <label for="distribution_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                                Tỷ lệ chia (% lợi nhuận) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="distribution_percentage" name="distribution_percentage" 
                                   x-model="percentage" @input="calculateDividends()" 
                                   required min="0" max="100" step="0.01"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="VD: 50 (chia 50% lợi nhuận)">
                            <p class="mt-1 text-xs text-gray-500">💡 Nhập % lợi nhuận muốn chia. VD: 50 = chia 50% lợi nhuận</p>
                        </div>

                        {{-- Total Distribution Amount --}}
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md">
                            <p class="text-sm text-gray-600">Tổng số tiền chia</p>
                            <p class="text-2xl font-bold text-green-600" x-text="formatMoney(totalAmount)"></p>
                        </div>

                        {{-- Investors List --}}
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Danh sách cổ đông</h4>
                            
                            @php
                                $investors = \App\Models\Staff::where('staff_type', 'investor')
                                    ->whereNotNull('equity_percentage')
                                    ->where('is_active', true)
                                    ->get();
                                $totalEquity = $investors->sum('equity_percentage');
                            @endphp

                            @if($investors->isEmpty())
                                <div class="text-center py-8 text-gray-500">
                                    <p>Chưa có cổ đông nào trong hệ thống</p>
                                    <a href="{{ route('staff.create') }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                        + Thêm cổ đông
                                    </a>
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cổ đông</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tỷ lệ vốn góp</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Số tiền nhận</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($investors as $investor)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10 bg-pink-100 rounded-full flex items-center justify-center">
                                                            <span class="text-pink-600 font-semibold">{{ substr($investor->full_name, 0, 1) }}</span>
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm font-medium text-gray-900">{{ $investor->full_name }}</p>
                                                            <p class="text-xs text-gray-500">{{ $investor->employee_code }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-pink-100 text-pink-800">
                                                        {{ number_format($investor->equity_percentage, 2) }}%
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <p class="text-sm font-bold text-green-600" 
                                                       x-text="formatMoney(calculateInvestorAmount({{ $investor->equity_percentage }}, {{ $totalEquity }}))">
                                                    </p>
                                                    <input type="hidden" name="investors[{{ $investor->id }}][staff_id]" value="{{ $investor->id }}">
                                                    <input type="hidden" 
                                                           name="investors[{{ $investor->id }}][amount]" 
                                                           :value="calculateInvestorAmount({{ $investor->equity_percentage }}, {{ $totalEquity }})">
                                                    <input type="hidden" 
                                                           name="investors[{{ $investor->id }}][equity_percentage]" 
                                                           value="{{ $investor->equity_percentage }}">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <td colspan="2" class="px-4 py-3 text-sm font-semibold text-gray-700">
                                                    Tổng vốn góp: {{ number_format($totalEquity, 2) }}%
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <p class="text-sm font-bold text-green-600" x-text="formatMoney(totalAmount)"></p>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                @if($totalEquity != 100)
                                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                    <p class="text-sm text-yellow-800">
                                        ⚠️ Tổng tỷ lệ vốn góp hiện tại là {{ number_format($totalEquity, 2) }}%, 
                                        {{ $totalEquity < 100 ? 'thiếu' : 'thừa' }} {{ abs(100 - $totalEquity) }}%
                                    </p>
                                </div>
                                @endif
                            @endif
                        </div>

                        {{-- Note --}}
                        <div class="mb-6">
                            <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                                Ghi chú
                            </label>
                            <textarea id="note" name="note" rows="3" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Ghi chú về đợt chia cổ tức này..."></textarea>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-end space-x-3">
                            <button type="button" @click="showModal = false" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Hủy
                            </button>
                            @if(!$investors->isEmpty())
                            <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                                onclick="return confirm('Xác nhận chia cổ tức? Các giao dịch sẽ được ghi nhận và không thể hoàn tác.');">
                                Xác nhận chia cổ tức
                            </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function dividendModal() {
            return {
                showModal: false,
                percentage: 0,
                totalAmount: 0,
                companyProfit: {{ $stats['total_net'] }},
                
                openModal() {
                    this.showModal = true;
                    this.percentage = 0;
                    this.totalAmount = 0;
                },
                
                calculateDividends() {
                    this.totalAmount = (this.companyProfit * this.percentage) / 100;
                },
                
                calculateInvestorAmount(equityPercentage, totalEquity) {
                    if (totalEquity === 0) return 0;
                    return (this.totalAmount * equityPercentage) / totalEquity;
                },
                
                formatMoney(amount) {
                    if (!amount || isNaN(amount)) return '0đ';
                    return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + 'đ';
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
