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
                        @if($stats['has_owner'])
                            <p class="text-xl font-bold {{ $stats['total_profit_after_fee'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                {{ number_format($stats['total_profit_after_fee'], 0, ',', '.') }}đ
                            </p>
                            <p class="text-xs {{ $stats['month_profit_after_fee'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                {{ number_format($stats['month_profit_after_fee'], 0, ',', '.') }}đ tháng này
                            </p>
                            <p class="text-xs text-orange-500 mt-1">
                                (Sau phí 15% & bảo trì)
                            </p>
                            @if($stats['total_owner_maintenance'] > 0)
                            <p class="text-xs text-gray-500 mt-1">
                                🔧 Bảo trì: {{ number_format($stats['total_owner_maintenance'], 0, ',', '.') }}đ
                            </p>
                            @endif
                        @else
                            <p class="text-xl font-bold {{ $stats['total_net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($stats['total_net'], 0, ',', '.') }}đ
                            </p>
                            <p class="text-xs {{ $stats['month_net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($stats['month_net'], 0, ',', '.') }}đ tháng này
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Loan Management Section --}}
            @can('manage vehicles')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">💰 Quản lý khoản vay</h3>
                        @if(!$vehicle->loanProfile)
                        <button onclick="openLoanModal()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            + Thêm khoản vay
                        </button>
                        @endif
                    </div>

                    @if($vehicle->loanProfile)
                        @php
                            $loan = $vehicle->loanProfile;
                            $progress = $loan->getProgressPercentage();
                            $totalPaid = $loan->getTotalPaidAmount();
                            $overdueCount = $loan->getOverdueCount();
                        @endphp

                        {{-- Loan Overview --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Số dư còn lại</p>
                                <p class="text-2xl font-bold text-blue-600">{{ number_format($loan->remaining_balance, 0, ',', '.') }}đ</p>
                                <p class="text-xs text-gray-500 mt-1">/ {{ number_format($loan->principal_amount, 0, ',', '.') }}đ</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Đã thanh toán</p>
                                <p class="text-2xl font-bold text-green-600">{{ number_format($totalPaid, 0, ',', '.') }}đ</p>
                                <p class="text-xs text-gray-500 mt-1">{{ number_format($progress, 1) }}% hoàn thành</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Lãi suất hiện tại</p>
                                <p class="text-2xl font-bold text-purple-600">{{ number_format($loan->getCurrentInterestRate(), 2) }}%</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $loan->term_months }} tháng</p>
                            </div>
                            <div class="bg-orange-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Trạng thái</p>
                                <p class="text-lg font-bold {{ $loan->status == 'active' ? 'text-orange-600' : 'text-gray-600' }}">
                                    {{ $loan->status == 'active' ? 'Đang hoạt động' : 'Đã đóng' }}
                                </p>
                                @if($overdueCount > 0)
                                <p class="text-xs text-red-600 mt-1">⚠ {{ $overdueCount }} kỳ quá hạn</p>
                                @endif
                            </div>
                        </div>

                        {{-- Loan Details --}}
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500">Ngân hàng</p>
                                    <p class="font-semibold">{{ $loan->bank_name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Số hợp đồng</p>
                                    <p class="font-semibold">{{ $loan->contract_number }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">CIF</p>
                                    <p class="font-semibold">{{ $loan->cif ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Ngày giải ngân</p>
                                    <p class="font-semibold">{{ $loan->disbursement_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Ngày trả hàng tháng</p>
                                    <p class="font-semibold">Ngày {{ $loan->payment_day }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Gốc hàng tháng</p>
                                    <p class="font-semibold text-blue-600">{{ number_format($loan->getMonthlyPrincipal(), 0, ',', '.') }}đ</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs text-gray-500">Ghi chú</p>
                                    <p class="text-sm">{{ $loan->note ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2 mb-6">
                            <button onclick="openEditLoanModal()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                ✏️ Sửa thông tin
                            </button>
                            <button onclick="openAdjustInterestModal()" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                                📊 Điều chỉnh lãi suất
                            </button>
                            @if($loan->status == 'active')
                            <button onclick="openPayOffModal()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                ✅ Trả nợ sớm
                            </button>
                            @endif
                            @if($loan->schedules()->where('status', 'paid')->count() == 0)
                            <button onclick="deleteLoan()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                🗑️ Xóa khoản vay
                            </button>
                            @endif
                        </div>

                        {{-- Repayment Schedule Table --}}
                        <div class="overflow-x-auto">
                            <h4 class="font-semibold mb-3">📅 Lịch trả nợ</h4>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kỳ</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ngày đến hạn</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Gốc</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Lãi</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Tổng</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Lãi suất</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ngày thanh toán</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($loan->schedules()->orderBy('period_no')->get() as $schedule)
                                    <tr class="{{ $schedule->status == 'overdue' ? 'bg-red-50' : '' }}">
                                        <td class="px-4 py-2 text-sm">{{ $schedule->period_no }}/{{ $loan->total_periods }}</td>
                                        <td class="px-4 py-2 text-sm">{{ \Carbon\Carbon::parse($schedule->due_date)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2 text-sm text-right">{{ number_format($schedule->principal, 0, ',', '.') }}đ</td>
                                        <td class="px-4 py-2 text-sm text-right">{{ number_format($schedule->interest, 0, ',', '.') }}đ</td>
                                        <td class="px-4 py-2 text-sm text-right font-semibold">{{ number_format($schedule->total, 0, ',', '.') }}đ</td>
                                        <td class="px-4 py-2 text-sm text-right">{{ number_format($schedule->interest_rate, 2) }}%</td>
                                        <td class="px-4 py-2 text-center">
                                            @if($schedule->status == 'paid')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Đã trả</span>
                                            @elseif($schedule->status == 'overdue')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    Quá hạn ({{ $schedule->overdue_days }} ngày)
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Chờ</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            {{ $schedule->paid_date ? \Carbon\Carbon::parse($schedule->paid_date)->format('d/m/Y') : '-' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Interest Adjustment History --}}
                        @if($loan->interestAdjustments()->count() > 0)
                        <div class="mt-6">
                            <h4 class="font-semibold mb-3">📈 Lịch sử điều chỉnh lãi suất</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ngày hiệu lực</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Lãi suất cũ</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Lãi suất mới</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ghi chú</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Người tạo</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($loan->interestAdjustments()->orderBy('effective_date', 'desc')->get() as $adjustment)
                                        <tr>
                                            <td class="px-4 py-2 text-sm">{{ \Carbon\Carbon::parse($adjustment->effective_date)->format('d/m/Y') }}</td>
                                            <td class="px-4 py-2 text-sm text-right">{{ number_format($adjustment->old_interest_rate, 2) }}%</td>
                                            <td class="px-4 py-2 text-sm text-right font-semibold text-purple-600">{{ number_format($adjustment->new_interest_rate, 2) }}%</td>
                                            <td class="px-4 py-2 text-sm">{{ $adjustment->note ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm">{{ $adjustment->creator->name ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                    @else
                        <p class="text-gray-500 text-center py-8">Chưa có khoản vay nào cho xe này</p>
                    @endif
                </div>
            </div>
            @endcan

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
                {{-- Maintenance History --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">🔧 Lịch sử bảo trì xe</h3>
                            <div class="flex items-center gap-2">
                                @can('manage vehicles')
                                <a href="{{ route('vehicle-maintenances.create', ['vehicle_id' => $vehicle->id]) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                    + Thêm bảo trì
                                </a>
                                @endcan
                            </div>
                        </div>

                        <!-- Statistics Section -->
                        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-600 mb-1">Tổng chi phí bảo trì</h4>
                                    <p class="text-2xl font-bold text-orange-600">{{ number_format($totalMaintenanceCost, 0, ',', '.') }} đ</p>
                                    <p class="text-xs text-gray-500 mt-1">Xe: {{ $vehicle->license_plate }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('vehicles.export-maintenances-pdf', $vehicle) }}" class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        In PDF
                                    </a>
                                    <a href="{{ route('vehicles.export-maintenances-excel', $vehicle) }}" class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Xuất Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        @if($maintenances->isEmpty())
                            <p class="text-gray-500 text-sm">Chưa có lịch sử bảo trì nào.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dịch vụ</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đối tác</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Chi phí</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Km</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Loại chi</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ghi chú</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($maintenances as $maintenance)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                {{ $maintenance->date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                {{ $maintenance->maintenanceService->name ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ $maintenance->partner->name ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-right text-red-600">
                                                {{ number_format($maintenance->cost, 0, ',', '.') }}đ
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-gray-600">
                                                {{ $maintenance->mileage ? number_format($maintenance->mileage, 0, ',', '.') : '-' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                                @if($maintenance->transaction)
                                                    @if($maintenance->transaction->category == 'bảo_trì_xe_chủ_riêng')
                                                        <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800" title="Chi phí trừ từ lợi nhuận xe chủ riêng">
                                                            🏠 Xe chủ riêng
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800" title="Chi phí trừ từ tài khoản công ty">
                                                            🏢 Công ty
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 text-xs">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                <div class="max-w-xs">
                                                    @if($maintenance->description)
                                                        <p class="text-gray-700 mb-1">{{ $maintenance->description }}</p>
                                                    @endif
                                                    @if($maintenance->note)
                                                        <p class="text-gray-500 text-xs">{{ $maintenance->note }}</p>
                                                    @endif
                                                    @if(!$maintenance->description && !$maintenance->note)
                                                        -
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                                @can('manage vehicles')
                                                <div class="flex items-center justify-center space-x-2">
                                                    <a href="{{ route('vehicle-maintenances.edit', $maintenance) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        Sửa
                                                    </a>
                                                    <form action="{{ route('vehicle-maintenances.destroy', $maintenance) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Xóa lịch sử bảo trì này?\n\nLưu ý: Giao dịch liên quan cũng sẽ bị xóa!')">
                                                            Xóa
                                                        </button>
                                                    </form>
                                                </div>
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            {{-- Maintenance Pagination --}}
                            <div class="mt-4">
                                {{ $maintenances->links() }}
                            </div>
                        @endif
                    </div>
                </div>

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
                                                
                                                {{-- Mã chuyến đi hoặc bảo trì --}}
                                                <div>
                                                    @if($group['incident'])
                                                        <a href="{{ route('incidents.show', $group['incident']) }}" class="text-base font-semibold text-blue-600 hover:text-blue-800" onclick="event.stopPropagation()">
                                                            Chuyến #{{ $group['incident']->id }}
                                                        </a>
                                                    @elseif($group['transactions']->first()->vehicleMaintenance)
                                                        <span class="text-base font-semibold text-green-600">
                                                            🔧 {{ $group['transactions']->first()->vehicleMaintenance->maintenanceService->name ?? 'Bảo trì' }}
                                                        </span>
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
                                                    @elseif($group['transactions']->first()->vehicleMaintenance)
                                                        @if($group['transactions']->first()->vehicleMaintenance->partner)
                                                            <span>•</span>
                                                            <span>{{ $group['transactions']->first()->vehicleMaintenance->partner->name }}</span>
                                                        @endif
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
                                                @if($group['has_owner'] && $group['management_fee'] > 0)
                                                <div class="text-right min-w-[100px]">
                                                    <div class="text-base font-semibold text-orange-600">
                                                        {{ number_format($group['management_fee'], 0, ',', '.') }}đ
                                                    </div>
                                                    <div class="text-xs text-gray-500">Phí 15%</div>
                                                </div>
                                                <div class="text-right min-w-[120px]">
                                                    <div class="text-lg font-bold text-blue-600">
                                                        +{{ number_format($group['profit_after_fee'], 0, ',', '.') }}đ
                                                    </div>
                                                    <div class="text-xs text-gray-500">Sau phí</div>
                                                </div>
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
                                                        <th class="py-2 text-left">Ngày giờ</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    @foreach($group['transactions'] as $transaction)
                                                    <tr class="hover:bg-gray-50 {{ $transaction->category == 'điều_chỉnh_lương' ? 'bg-blue-50' : '' }} {{ $transaction->vehicle_maintenance_id ? 'bg-green-50' : '' }}">
                                                        <td class="py-2">
                                                            <span class="px-2 py-1 text-xs rounded-full {{ $transaction->type == 'thu' ? 'bg-green-100 text-green-800' : ($transaction->type == 'du_kien_chi' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                                                {{ $transaction->type_label }}
                                                            </span>
                                                            @if($transaction->category == 'điều_chỉnh_lương')
                                                                <span class="ml-1 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                                    ⚙️ Điều chỉnh
                                                                </span>
                                                            @elseif($transaction->category == 'bảo_trì_xe_chủ_riêng')
                                                                <span class="ml-1 px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">
                                                                    🏠 Xe chủ riêng
                                                                </span>
                                                            @elseif($transaction->category == 'bảo_trì_xe')
                                                                <span class="ml-1 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                                    🏢 Công ty
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

        // Loan Management Functions
        function openLoanModal() {
            document.getElementById('loanModal').classList.remove('hidden');
        }

        function closeLoanModal() {
            document.getElementById('loanModal').classList.add('hidden');
        }

        function openEditLoanModal() {
            // Populate form with existing data
            @if($vehicle->loanProfile)
            document.getElementById('edit_cif').value = '{{ $vehicle->loanProfile->cif ?? '' }}';
            document.getElementById('edit_contract_number').value = '{{ $vehicle->loanProfile->contract_number }}';
            document.getElementById('edit_bank_name').value = '{{ $vehicle->loanProfile->bank_name }}';
            document.getElementById('edit_payment_day').value = '{{ $vehicle->loanProfile->payment_day }}';
            document.getElementById('edit_note').value = '{{ $vehicle->loanProfile->note ?? '' }}';
            @endif
            document.getElementById('editLoanModal').classList.remove('hidden');
        }

        function closeEditLoanModal() {
            document.getElementById('editLoanModal').classList.add('hidden');
        }

        function openAdjustInterestModal() {
            document.getElementById('adjustInterestModal').classList.remove('hidden');
        }

        function closeAdjustInterestModal() {
            document.getElementById('adjustInterestModal').classList.add('hidden');
        }

        function openPayOffModal() {
            @if($vehicle->loanProfile)
            const remaining = {{ $vehicle->loanProfile->schedules()->where('status', 'pending')->sum('total') }};
            document.getElementById('payoff_amount_display').textContent = new Intl.NumberFormat('vi-VN').format(remaining) + 'đ';
            @endif
            document.getElementById('payOffModal').classList.remove('hidden');
        }

        function closePayOffModal() {
            document.getElementById('payOffModal').classList.add('hidden');
        }

        function deleteLoan() {
            if (confirm('Bạn có chắc chắn muốn xóa khoản vay này?\n\nLưu ý: Chỉ có thể xóa khoản vay chưa có lịch sử thanh toán.')) {
                document.getElementById('deleteLoanForm').submit();
            }
        }
    </script>
    @endpush

    {{-- Create Loan Modal --}}
    <div id="loanModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Thêm khoản vay mới</h3>
                <button onclick="closeLoanModal()" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <form method="POST" action="{{ route('loans.store', $vehicle) }}">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CIF</label>
                        <input type="text" name="cif" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số hợp đồng <span class="text-red-500">*</span></label>
                        <input type="text" name="contract_number" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngân hàng <span class="text-red-500">*</span></label>
                        <input type="text" name="bank_name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số tiền gốc <span class="text-red-500">*</span></label>
                        <input type="number" name="principal_amount" required min="0" step="1000" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số tháng <span class="text-red-500">*</span></label>
                        <input type="number" name="term_months" required min="1" max="360" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày giải ngân <span class="text-red-500">*</span></label>
                        <input type="date" name="disbursement_date" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lãi suất (%/năm) <span class="text-red-500">*</span></label>
                        <input type="number" name="base_interest_rate" required min="0" max="100" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày trả hàng tháng <span class="text-red-500">*</span></label>
                        <input type="number" name="payment_day" required min="1" max="28" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                        <textarea name="note" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeLoanModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Tạo khoản vay</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Loan Modal --}}
    @if($vehicle->loanProfile)
    <div id="editLoanModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Sửa thông tin khoản vay</h3>
                <button onclick="closeEditLoanModal()" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <form method="POST" action="{{ route('loans.update', $vehicle->loanProfile) }}">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CIF</label>
                        <input type="text" name="cif" id="edit_cif" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số hợp đồng <span class="text-red-500">*</span></label>
                        <input type="text" name="contract_number" id="edit_contract_number" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngân hàng <span class="text-red-500">*</span></label>
                        <input type="text" name="bank_name" id="edit_bank_name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày trả hàng tháng <span class="text-red-500">*</span></label>
                        <input type="number" name="payment_day" id="edit_payment_day" required min="1" max="28" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                        <textarea name="note" id="edit_note" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeEditLoanModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Adjust Interest Modal --}}
    <div id="adjustInterestModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Điều chỉnh lãi suất</h3>
                <button onclick="closeAdjustInterestModal()" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <form method="POST" action="{{ route('loans.adjust-interest', $vehicle->loanProfile) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lãi suất hiện tại</label>
                        <input type="text" value="{{ number_format($vehicle->loanProfile->getCurrentInterestRate(), 2) }}%" readonly class="w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lãi suất mới (%/năm) <span class="text-red-500">*</span></label>
                        <input type="number" name="new_interest_rate" required min="0" max="100" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày hiệu lực <span class="text-red-500">*</span></label>
                        <input type="date" name="effective_date" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                        <textarea name="note" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeAdjustInterestModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">Điều chỉnh</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Pay Off Modal --}}
    <div id="payOffModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Trả nợ sớm</h3>
                <button onclick="closePayOffModal()" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <form method="POST" action="{{ route('loans.pay-off', $vehicle->loanProfile) }}">
                @csrf
                <div class="space-y-4">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm text-yellow-800 mb-2">⚠️ <strong>Lưu ý:</strong></p>
                        <ul class="text-sm text-yellow-700 list-disc list-inside space-y-1">
                            <li>Tất cả các kỳ chưa trả sẽ được đóng</li>
                            <li>Một giao dịch chi sẽ được tạo</li>
                            <li>Hành động này không thể hoàn tác</li>
                        </ul>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tổng số tiền cần trả</label>
                        <div class="text-2xl font-bold text-green-600" id="payoff_amount_display"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                        <textarea name="note" rows="3" placeholder="Lý do trả nợ sớm..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closePayOffModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Xác nhận trả nợ</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Loan Form (hidden) --}}
    <form id="deleteLoanForm" method="POST" action="{{ route('loans.destroy', $vehicle->loanProfile) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endif

</x-app-layout>
