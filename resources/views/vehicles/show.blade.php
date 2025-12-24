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

            {{-- Error Message --}}
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Có lỗi xảy ra:</strong>
                    <ul class="mt-2 ml-4 list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $stats['has_owner'] ? '4' : '5' }} gap-4 mb-6">
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
                        @if($stats['has_owner'])
                            <p class="text-xl font-bold text-green-600">{{ number_format($stats['total_revenue_display'], 0, ',', '.') }}đ</p>
                            <p class="text-xs text-gray-500">{{ number_format($stats['month_revenue_display'], 0, ',', '.') }}đ tháng này</p>
                            <p class="text-xs text-green-600 mt-1">(Thu + Nộp quỹ + Vay)</p>
                        @else
                            <p class="text-xl font-bold text-green-600">{{ number_format($stats['total_revenue'], 0, ',', '.') }}đ</p>
                            <p class="text-xs text-gray-500">{{ number_format($stats['month_revenue'], 0, ',', '.') }}đ tháng này</p>
                        @endif
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Tổng chi</p>
                        @if($stats['has_owner'])
                            <p class="text-xl font-bold text-red-600">{{ number_format($stats['total_expense_display'], 0, ',', '.') }}đ</p>
                            <p class="text-xs text-gray-500">{{ number_format($stats['month_expense_display'], 0, ',', '.') }}đ tháng này</p>
                            <p class="text-xs text-red-600 mt-1">(Chi + Trả nợ + Phí 15%)</p>
                        @else
                            <p class="text-xl font-bold text-red-600">{{ number_format($stats['total_expense'], 0, ',', '.') }}đ</p>
                            <p class="text-xs text-gray-500">{{ number_format($stats['month_expense'], 0, ',', '.') }}đ tháng này</p>
                        @endif
                    </div>
                </div>
                @if(!$stats['has_owner'])
                {{-- Chỉ hiển thị riêng cho xe công ty, xe có chủ đã gộp vào Tổng thu --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Nộp quỹ</p>
                        <p class="text-xl font-bold text-blue-600">{{ number_format($stats['total_fund_deposit'], 0, ',', '.') }}đ</p>
                        <p class="text-xs text-gray-500">{{ number_format($stats['month_fund_deposit'], 0, ',', '.') }}đ tháng này</p>
                        <p class="text-xs text-blue-500 mt-1">(Không tính phí 15%)</p>
                    </div>
                </div>
                @endif
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
                            <p class="text-xs text-green-500 mt-1">
                                (Thu - Chi)
                            </p>
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

            {{-- Borrowed Amount Alert (for vehicle owners) --}}
            @if($stats['has_owner'] && isset($stats['total_borrowed']) && $stats['total_borrowed'] > 0)
            <div class="bg-orange-50 border-l-4 border-orange-500 p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-orange-800">
                                Đang vay từ công ty: <span class="font-bold">{{ number_format($stats['total_borrowed'], 0, ',', '.') }}đ</span>
                            </h3>
                            <div class="mt-2 text-sm text-orange-700">
                                <p>Chủ xe đang mượn tiền từ công ty để chi trả. Số tiền này cần được hoàn trả lại.</p>
                                @if($stats['month_borrowed'] != 0)
                                <p class="mt-1">Tháng này: 
                                    <span class="{{ $stats['month_borrowed'] > 0 ? 'text-red-600' : 'text-green-600' }} font-semibold">
                                        {{ $stats['month_borrowed'] > 0 ? '+' : '' }}{{ number_format($stats['month_borrowed'], 0, ',', '.') }}đ
                                    </span>
                                </p>
                                @endif
                                @if($stats['total_profit_after_fee'] > 0)
                                <p class="mt-2 text-xs text-gray-600">
                                    💰 Số dư hiện tại: {{ number_format($stats['total_profit_after_fee'], 0, ',', '.') }}đ
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($stats['total_profit_after_fee'] > 0)
                    <div class="ml-4">
                        <button onclick="openRepayModal()" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            💳 Trả nợ
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Loan Management Section --}}
            @php
                $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())->where('staff_type', 'vehicle_owner')->exists();
                $canManageLoan = !$isVehicleOwner && auth()->user()->can('manage vehicles');
            @endphp
            
            @if($canManageLoan || $isVehicleOwner)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">💰 Quản lý khoản vay</h3>
                        @if(!$vehicle->loanProfile && $canManageLoan)
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
                        @if($canManageLoan)
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
                            <button onclick="deleteLoan()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                🗑️ Xóa khoản vay
                            </button>
                        </div>
                        @endif

                        {{-- Repayment Schedule Table --}}
                        <div class="overflow-x-auto">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-semibold">📅 Lịch trả nợ</h4>
                                @if($canManageLoan)
                                <form method="POST" action="{{ route('loans.process-repayments', $vehicle->loanProfile) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                        🔄 Cập nhật trạng thái
                                    </button>
                                </form>
                                @endif
                            </div>
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
                                            @if($canManageLoan)
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                                            @endif
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
                                            @if($canManageLoan)
                                            <td class="px-4 py-2 text-center">
                                                <form method="POST" action="{{ route('loans.delete-adjustment', $adjustment) }}" class="inline" onsubmit="return confirm('Xóa điều chỉnh lãi suất này? Lịch trả nợ sẽ được khôi phục về lãi suất cũ.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">🗑️ Xóa</button>
                                                </form>
                                            </td>
                                            @endif
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
            @endif

            {{-- Assets Section --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">🛠️ Tài sản được gán</h3>
                        @can('manage settings')
                        <a href="{{ route('assets.create', ['usage_type' => 'vehicle', 'vehicle_id' => $vehicle->id]) }}" class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                            + Thêm tài sản
                        </a>
                        @endcan
                    </div>

                    @if($vehicle->assets->isEmpty())
                        <p class="text-gray-500 text-center py-8">Chưa có tài sản nào được gán cho xe này</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên tài sản</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nhãn hiệu</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Số lượng</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày trang bị</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($vehicle->assets as $asset)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $asset->name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $asset->brand ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $asset->quantity }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $asset->equipped_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($asset->is_active)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    ✓ Đang dùng
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    ✕ Ngừng dùng
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <a href="{{ route('assets.show', $asset) }}" class="text-blue-600 hover:text-blue-900 mr-2">👁️ Xem</a>
                                            @can('manage settings')
                                            <a href="{{ route('assets.edit', $asset) }}" class="text-indigo-600 hover:text-indigo-900">✏️ Sửa</a>
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4 text-sm text-gray-600">
                            <strong>Tổng số tài sản:</strong> {{ $vehicle->assets->count() }} | 
                            <strong>Tổng số lượng:</strong> {{ $vehicle->assets->sum('quantity') }}
                        </div>
                    @endif
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

            <div class="space-y-6">
                {{-- Maintenance History --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                        @if($recentIncidents->isEmpty())
                            <p class="text-gray-500 text-sm">Chưa có chuyến đi nào.</p>
                        @else
                            <div class="space-y-3">
                                @foreach($recentIncidents as $incident)
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
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Giao dịch 
                                @if(request()->hasAny(['type', 'start_date', 'end_date']))
                                <span class="text-sm font-normal text-gray-500">(đã lọc)</span>
                                @else
                                <span class="text-sm font-normal text-gray-500">(gần đây)</span>
                                @endif
                            </h3>
                            
                            {{-- Export Button with Dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Xuất Excel
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                {{-- Export Dropdown --}}
                                <div x-show="open" @click.away="open = false" x-cloak
                                    class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                    <form action="{{ route('vehicles.export-transactions', $vehicle) }}" method="GET" class="p-4">
                                        <h4 class="font-semibold text-gray-800 mb-3">Tùy chọn xuất file</h4>
                                        
                                        {{-- Date Range --}}
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Khoảng thời gian</label>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <input type="date" name="date_from" value="{{ request('start_date') }}"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    <label class="text-xs text-gray-500">Từ ngày</label>
                                                </div>
                                                <div>
                                                    <input type="date" name="date_to" value="{{ request('end_date') }}"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    <label class="text-xs text-gray-500">Đến ngày</label>
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Để trống để xuất toàn bộ</p>
                                        </div>

                                        {{-- Transaction Type --}}
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Loại giao dịch</label>
                                            <select name="transaction_type" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                <option value="">Tất cả loại giao dịch</option>
                                                <option value="chuyen">Chuyến xe (có mã chuyến)</option>
                                                <option value="nop_quy">Nộp quỹ</option>
                                                <option value="khac">Giao dịch khác</option>
                                            </select>
                                        </div>

                                        {{-- Submit Button --}}
                                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Xuất file Excel
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
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
                                                @if(isset($group['total_fund_deposit']) && $group['total_fund_deposit'] > 0)
                                                <div class="text-right">
                                                    <div class="text-blue-600 font-semibold">+{{ number_format($group['total_fund_deposit'], 0, ',', '.') }}đ</div>
                                                    <div class="text-xs text-gray-500">Nộp quỹ</div>
                                                </div>
                                                @endif
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
                                                        <th class="py-2 text-left">Mã GD</th>
                                                        <th class="py-2 text-left">Loại</th>
                                                        <th class="py-2 text-left">Tên khoản</th>
                                                        <th class="py-2 text-right">Số tiền</th>
                                                        <th class="py-2 text-left">Phương thức</th>
                                                        <th class="py-2 text-left">Ngày giờ</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    @foreach($group['transactions'] as $transaction)
                                                    <tr class="hover:bg-gray-50 {{ $transaction->category == 'điều_chỉnh_lương' ? 'bg-blue-50' : '' }} {{ $transaction->vehicle_maintenance_id ? 'bg-green-50' : '' }} {{ $transaction->type == 'nop_quy' ? 'bg-blue-50' : '' }}">
                                                        <td class="py-2 text-gray-500 text-xs font-mono">
                                                            {{ $transaction->code ?? 'N/A' }}
                                                        </td>
                                                        <td class="py-2">
                                                            <span class="px-2 py-1 text-xs rounded-full {{ $transaction->type == 'thu' || $transaction->type == 'nop_quy' ? 'bg-green-100 text-green-800' : ($transaction->type == 'du_kien_chi' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
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
                                                            @elseif($transaction->type == 'nop_quy')
                                                                <span class="ml-1 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                                    💰 Không tính phí
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="py-2">
                                                            {{ $transaction->note ?? '-' }}
                                                        </td>
                                                        <td class="py-2 text-right font-semibold {{ $transaction->type == 'thu' || $transaction->type == 'nop_quy' ? 'text-green-600' : ($transaction->type == 'du_kien_chi' ? 'text-orange-600' : 'text-red-600') }}">
                                                            {{ $transaction->type == 'thu' || $transaction->type == 'nop_quy' ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', '.') }}đ
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
            const schedules = @json($vehicle->loanProfile->schedules->where('status', 'pending')->values());
            const totalRemaining = schedules.reduce((sum, schedule) => sum + parseFloat(schedule.total), 0);
            const principalRemaining = parseFloat('{{ $vehicle->loanProfile->remaining_balance }}');
            
            document.getElementById('remaining_principal_display').textContent = new Intl.NumberFormat('vi-VN').format(principalRemaining) + 'đ';
            document.getElementById('total_remaining_display').textContent = new Intl.NumberFormat('vi-VN').format(totalRemaining) + 'đ';
            document.getElementById('max_partial_display').textContent = new Intl.NumberFormat('vi-VN').format(principalRemaining) + 'đ';
            document.getElementById('partial_amount').max = principalRemaining;
            @endif
            document.getElementById('payOffModal').classList.remove('hidden');
        }

        function togglePaymentType() {
            const paymentType = document.querySelector('input[name="payment_type"]:checked').value;
            const partialSection = document.getElementById('partial_payment_section');
            const partialAmount = document.getElementById('partial_amount');
            
            if (paymentType === 'partial') {
                partialSection.classList.remove('hidden');
                partialAmount.required = true;
            } else {
                partialSection.classList.add('hidden');
                partialAmount.required = false;
                partialAmount.value = '';
            }
        }

        function closePayOffModal() {
            document.getElementById('payOffModal').classList.add('hidden');
        }

        function deleteLoan() {
            if (confirm('Bạn có chắc chắn muốn xóa khoản vay này?\n\n⚠️ Cảnh báo: Tất cả giao dịch trả nợ liên quan sẽ bị xóa và số tiền đã trả sẽ được hoàn lại vào lợi nhuận xe.\n\nHành động này không thể hoàn tác!')) {
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
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="first_period_interest_only" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-700">Kỳ đầu tiên chỉ trả lãi (không trả gốc)</span>
                        </label>
                        <p class="text-xs text-gray-500 ml-6 mt-1">Áp dụng khi ngày giải ngân gần với ngày trả nợ, kỳ đầu chỉ trả lãi để giảm gánh nặng thanh toán</p>
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
        <div class="relative top-10 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white mb-10" style="max-height: 90vh; overflow-y: auto;">
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
                        <textarea name="note" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4" style="display: flex !important; visibility: visible !important;">
                    <button type="button" onclick="closeAdjustInterestModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400" style="display: inline-block !important;">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-bold" style="display: inline-block !important; background-color: #16a34a !important; color: white !important; padding: 0.5rem 1rem !important; border-radius: 0.375rem !important;">✓ Điều chỉnh</button>
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
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800 mb-2">💰 <strong>Thông tin khoản vay:</strong></p>
                        <div class="text-sm text-blue-700 space-y-1">
                            <div>Số dư gốc còn lại: <span class="font-semibold" id="remaining_principal_display"></span></div>
                            <div>Tổng tiền cần trả (bao gồm lãi): <span class="font-semibold" id="total_remaining_display"></span></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hình thức trả nợ</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="payment_type" value="full" checked onchange="togglePaymentType()" class="mr-2">
                                <span>Trả hết (đóng khoản vay)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="payment_type" value="partial" onchange="togglePaymentType()" class="mr-2">
                                <span>Trả một phần tiền gốc</span>
                            </label>
                        </div>
                    </div>
                    <div id="partial_payment_section" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số tiền gốc muốn trả <span class="text-red-500">*</span></label>
                        <input type="number" name="partial_amount" id="partial_amount" min="0" step="1000" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Nhập số tiền...">
                        <p class="text-xs text-gray-500 mt-1">Tối đa: <span id="max_partial_display" class="font-semibold"></span> (số dư gốc còn lại)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                        <textarea name="note" rows="3" placeholder="Lý do trả nợ sớm..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-xs text-yellow-700">⚠️ Trả hết: Đóng toàn bộ khoản vay, xóa các kỳ chưa trả<br>⚠️ Trả một phần: Giảm tiền gốc, tái tính lịch trả nợ cho các kỳ chưa trả</p>
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

    {{-- Repay Company Modal --}}
    @if($stats['has_owner'] && isset($stats['total_borrowed']) && $stats['total_borrowed'] > 0)
    <div id="repayModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">💳 Trả nợ công ty</h3>
                <button type="button" onclick="closeRepayModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('vehicles.repay', $vehicle) }}">
                @csrf
                <div class="space-y-4">
                    <div class="bg-blue-50 rounded-lg p-3 space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Đang nợ:</span>
                            <span class="font-bold text-orange-600">{{ number_format($stats['total_borrowed'], 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Số dư hiện tại:</span>
                            <span class="font-bold text-green-600">{{ number_format($stats['total_profit_after_fee'], 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex justify-between text-sm pt-2 border-t border-blue-200">
                            <span class="text-gray-600">Có thể trả tối đa:</span>
                            <span class="font-bold text-blue-600">{{ number_format(min($stats['total_borrowed'], $stats['total_profit_after_fee']), 0, ',', '.') }}đ</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Chọn cách trả</label>
                        <div class="space-y-2">
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="repay_type" value="full" onchange="updateRepayAmount()" class="mr-3" checked>
                                <div class="flex-1">
                                    <span class="font-medium">Trả hết</span>
                                    <p class="text-xs text-gray-500">Trả toàn bộ số nợ</p>
                                </div>
                            </label>
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="repay_type" value="partial" onchange="updateRepayAmount()" class="mr-3">
                                <div class="flex-1">
                                    <span class="font-medium">Trả một phần</span>
                                    <p class="text-xs text-gray-500">Tự nhập số tiền muốn trả</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div id="partial_repay_section" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số tiền muốn trả <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" id="repay_amount" min="1000" step="1000" 
                               max="{{ min($stats['total_borrowed'], $stats['total_profit_after_fee']) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" 
                               placeholder="Nhập số tiền...">
                        <p class="text-xs text-gray-500 mt-1">Tối thiểu: 1.000đ | Tối đa: {{ number_format(min($stats['total_borrowed'], $stats['total_profit_after_fee']), 0, ',', '.') }}đ</p>
                    </div>
                    <input type="hidden" name="full_amount" value="{{ min($stats['total_borrowed'], $stats['total_profit_after_fee']) }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                        <textarea name="note" rows="2" placeholder="Lý do trả nợ..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                        <p class="text-xs text-green-700">
                            ✓ Tiền sẽ được trừ khỏi lợi nhuận chủ xe<br>
                            ✓ Tiền sẽ được cộng vào lợi nhuận công ty<br>
                            ✓ Giảm số nợ đang vay
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeRepayModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Xác nhận trả nợ</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRepayModal() {
            document.getElementById('repayModal').classList.remove('hidden');
            updateRepayAmount();
        }

        function closeRepayModal() {
            document.getElementById('repayModal').classList.add('hidden');
        }

        function updateRepayAmount() {
            const repayType = document.querySelector('input[name="repay_type"]:checked').value;
            const partialSection = document.getElementById('partial_repay_section');
            
            if (repayType === 'partial') {
                partialSection.classList.remove('hidden');
            } else {
                partialSection.classList.add('hidden');
            }
        }

        // Close modal when clicking outside
        document.getElementById('repayModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeRepayModal();
            }
        });
    </script>
    @endif

</x-app-layout>
