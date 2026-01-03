<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            @if($earnings)
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="w-full">
                    <header class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">
                                💰 Thu nhập của tôi
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Thống kê chi tiết thu nhập và các khoản điều chỉnh
                            </p>
                        </div>
                        
                        {{-- Month Filter --}}
                        <form method="GET" action="{{ route('profile.edit') }}" class="flex items-center gap-2">
                            <label for="month_filter" class="text-sm text-gray-700 whitespace-nowrap">Chọn tháng:</label>
                            <input type="month" 
                                   id="month_filter" 
                                   name="month" 
                                   value="{{ $selectedMonth }}"
                                   max="{{ now()->format('Y-m') }}"
                                   class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                   onchange="this.form.submit()">
                        </form>
                    </header>

                    {{-- Summary Cards --}}
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600">💼 Lương cơ bản/tháng</div>
                            <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['base_salary'], 0, ',', '.') }}đ</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600">Tổng thu nhập</div>
                            <div class="text-2xl font-bold text-green-600">{{ number_format($stats['total_earnings'], 0, ',', '.') }}đ</div>
                            <div class="text-xs text-gray-500 mt-1">
                                CB: {{ number_format($stats['base_salary_total'], 0, ',', '.') }}đ
                                + TC: {{ number_format($stats['wage_earnings_total'], 0, ',', '.') }}đ
                            </div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600">Thu nhập tháng {{ $monthDate->format('m/Y') }}</div>
                            <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['month_total_earnings'], 0, ',', '.') }}đ</div>
                            <div class="text-xs text-gray-500 mt-1">
                                CB: {{ number_format($stats['month_base_salary'], 0, ',', '.') }}đ
                                + TC: {{ number_format($stats['month_wage_earnings'], 0, ',', '.') }}đ
                                @if($stats['month_adjustments'] != 0)
                                    {{ $stats['month_adjustments'] >= 0 ? '+' : '' }} ĐC: {{ number_format($stats['month_adjustments'], 0, ',', '.') }}đ
                                @endif
                                @if($stats['month_salary_advances'] > 0)
                                    - Ứng: {{ number_format($stats['month_salary_advances'], 0, ',', '.') }}đ
                                @endif
                            </div>
                            <div class="text-xs text-green-600 mt-1">
                                📅 Ngày nhận: {{ now()->addMonth()->day(15)->format('d/m/Y') }}
                            </div>
                        </div>
                        <div class="bg-indigo-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600">Tổng số chuyến</div>
                            <div class="text-2xl font-bold text-indigo-600">{{ $stats['total_trips'] }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                TB: {{ $stats['total_trips'] > 0 ? number_format($stats['wage_earnings_total'] / $stats['total_trips'], 0, ',', '.') : 0 }}đ/chuyến
                            </div>
                        </div>
                    </div>

                    {{-- Pending Debts --}}
                    @if($totalDebt > 0)
                    <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <h3 class="text-md font-semibold text-red-800 mb-3">💳 Khoản nợ chưa thanh toán</h3>
                        
                        <div class="mb-3 p-2 bg-white rounded">
                            <p class="text-sm text-red-600 font-semibold">
                                Tổng nợ: {{ number_format($totalDebt, 0, ',', '.') }}đ
                            </p>
                        </div>

                        <div class="space-y-2">
                            @foreach($pendingDebts as $debt)
                                <div class="p-3 bg-white border border-red-100 rounded-md">
                                    <div class="flex justify-between items-start mb-1">
                                        @if($debt instanceof \App\Models\SalaryAdvance)
                                            <span class="text-sm font-semibold text-orange-600">💰 Ứng lương</span>
                                        @else
                                            <span class="text-sm font-semibold text-red-600">{{ $debt->category }}</span>
                                        @endif
                                        <span class="text-sm font-bold text-red-600">{{ number_format($debt->debt_amount, 0, ',', '.') }}đ</span>
                                    </div>
                                    
                                    @if($debt instanceof \App\Models\SalaryAdvance)
                                        <p class="text-xs text-gray-600">{{ $debt->note ?? 'Ứng lương' }}</p>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Ngày {{ $debt->date->format('d/m/Y') }}
                                        </div>
                                    @else
                                        <p class="text-xs text-gray-600">{{ $debt->reason }}</p>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Tháng {{ $debt->month->format('m/Y') }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Selected Month Adjustments --}}
                    @if($adjustments->isNotEmpty())
                    <div class="mt-6 bg-white border border-gray-200 rounded-lg p-4">
                        <h3 class="text-md font-semibold mb-3">📋 Điều chỉnh tháng {{ $monthDate->format('m/Y') }}</h3>
                        
                        @php
                            $adjustmentAdditions = $adjustments->where('type', 'addition')->sum('amount');
                            $adjustmentDeductions = $adjustments->where('type', 'deduction')->sum('amount');
                            $adjustmentNet = $adjustmentAdditions - $adjustmentDeductions;
                        @endphp
                        
                        <div class="mb-3 p-3 bg-gray-50 border border-gray-200 rounded-md">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <p class="text-xs text-gray-500">Cộng tiền</p>
                                    <p class="text-md font-bold text-green-600">+{{ number_format($adjustmentAdditions, 0, ',', '.') }}đ</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Trừ tiền</p>
                                    <p class="text-md font-bold text-red-600">-{{ number_format($adjustmentDeductions, 0, ',', '.') }}đ</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Tổng điều chỉnh</p>
                                    <p class="text-md font-bold {{ $adjustmentNet >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $adjustmentNet >= 0 ? '+' : '' }}{{ number_format($adjustmentNet, 0, ',', '.') }}đ
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            @foreach($adjustments as $adjustment)
                                <div class="p-2 border border-gray-100 rounded text-sm {{ $adjustment->type == 'addition' ? 'bg-green-50' : 'bg-red-50' }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <span class="font-semibold {{ $adjustment->type == 'addition' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $adjustment->category }}
                                            </span>
                                            @if($adjustment->incident)
                                                <span class="text-xs text-gray-500">- Chuyến #{{ $adjustment->incident->id }}</span>
                                            @endif
                                            <p class="text-xs text-gray-600 mt-1">{{ $adjustment->reason }}</p>
                                        </div>
                                        <span class="font-bold {{ $adjustment->type == 'addition' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $adjustment->type == 'addition' ? '+' : '-' }}{{ number_format($adjustment->amount, 0, ',', '.') }}đ
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Earnings Table --}}
                    <div class="mt-6">
                        <h3 class="text-md font-semibold mb-3">📝 Danh sách các khoản tiền công</h3>
                        
                        @if($earnings && $earnings->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chuyến đi</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bệnh nhân</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Số tiền</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($earnings as $earning)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                {{ $earning->date ? $earning->date->format('d/m/Y') : 'N/A' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                @if($earning->incident)
                                                    <span class="text-blue-600">#{{ $earning->incident->id }}</span>
                                                    @if($earning->incident->vehicle)
                                                        - {{ $earning->incident->vehicle->license_plate }}
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                @if($earning->incident && $earning->incident->patient)
                                                    {{ $earning->incident->patient->name }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                {{ $earning->category }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-semibold text-green-600">
                                                {{ number_format($earning->amount, 0, ',', '.') }}đ
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ $earning->description ?: '-' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                {{ $earnings->links() }}
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">Chưa có dữ liệu thu nhập</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            @can('delete staff')
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            @endcan
        </div>
    </div>
</x-app-layout>
