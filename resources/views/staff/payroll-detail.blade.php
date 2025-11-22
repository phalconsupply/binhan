<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                💰 Bảng lương tháng {{ $month }}/{{ $year }}
            </h2>
            <div class="flex items-center space-x-2">
                <button onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    🖨️ In bảng lương
                </button>
                <a href="{{ route('staff.payroll', ['year' => $year]) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    ← Quay lại
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                @php
                    $totalBaseSalary = collect($payrollData)->sum('base_salary');
                    $totalAdditions = collect($payrollData)->sum('additions');
                    $totalDeductions = collect($payrollData)->sum('deductions');
                    $totalPayout = collect($payrollData)->sum('total');
                @endphp

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Tổng lương cơ bản</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($totalBaseSalary, 0, ',', '.') }}đ</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Tổng cộng thêm</p>
                    <p class="text-2xl font-bold text-green-600">+{{ number_format($totalAdditions, 0, ',', '.') }}đ</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Tổng trừ</p>
                    <p class="text-2xl font-bold text-red-600">-{{ number_format($totalDeductions, 0, ',', '.') }}đ</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Tổng chi trả</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ number_format($totalPayout, 0, ',', '.') }}đ</p>
                </div>
            </div>

            {{-- Payroll Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 flex justify-between items-center">
                        <h3 class="text-lg font-semibold">Chi tiết bảng lương</h3>
                        <span class="text-sm text-gray-500">Tổng: {{ count($payrollData) }} nhân viên</span>
                    </div>

                    @if(count($payrollData) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">STT</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nhân viên</th>
                                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mã NV</th>
                                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Chức vụ</th>
                                        <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase">Lương CB</th>
                                        <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase">Tiền công</th>
                                        <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase">Cộng</th>
                                        <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase">Trừ</th>
                                        <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ứng</th>
                                        <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase">Thực lãnh</th>
                                        <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase">Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($payrollData as $index => $data)
                                        {{-- Wrap each staff's rows in a tbody with Alpine data --}}
                                        </tbody>
                                        <tbody x-data="{ showDetail: false }" class="bg-white divide-y divide-gray-200">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $index + 1 }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm">
                                                <a href="{{ route('staff.show', $data['staff']) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                                    {{ $data['staff']->full_name }}
                                                </a>
                                            </td>
                                            <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $data['staff']->employee_code ?? '-' }}</td>
                                            <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $data['staff']->position ?? '-' }}</td>
                                            <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold text-blue-600">
                                                {{ number_format($data['base_salary'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-2 py-2 whitespace-nowrap text-right text-xs text-green-600">
                                                {{ number_format($data['earnings'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-2 py-2 whitespace-nowrap text-right text-xs text-green-600">
                                                +{{ number_format($data['additions'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-2 py-2 whitespace-nowrap text-right text-xs text-red-600">
                                                -{{ number_format($data['deductions'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-2 py-2 whitespace-nowrap text-right text-xs text-orange-600">
                                                -{{ number_format($data['advances'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-2 py-2 whitespace-nowrap text-right text-base font-bold {{ $data['total'] >= 0 ? 'text-indigo-600' : 'text-red-600' }}">
                                                {{ number_format($data['total'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-2 py-2 whitespace-nowrap text-center">
                                                <button @click="showDetail = !showDetail" 
                                                        class="px-2 py-1 text-xs rounded-md transition-colors"
                                                        :class="showDetail ? 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                                                    <span x-show="!showDetail">📋 Chi tiết</span>
                                                    <span x-show="showDetail">▲ Thu gọn</span>
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        {{-- Detail Row --}}
                                        <tr x-show="showDetail" x-cloak class="bg-gray-50">
                                            <td colspan="11" class="px-4 py-4">
                                                <div class="space-y-4">
                                                    <h4 class="font-bold text-gray-800 text-base border-b pb-2">📊 Lịch sử thu chi tháng {{ $month }}/{{ $year }}</h4>
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                        {{-- Left Column: Income --}}
                                                        <div class="space-y-4">
                                                            {{-- Base Salary --}}
                                                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                                                <h5 class="font-semibold text-blue-800 mb-2 flex items-center">
                                                                    💵 Lương cơ bản
                                                                </h5>
                                                                <div class="flex justify-between items-center">
                                                                    <span class="text-sm text-gray-600">Lương tháng cố định</span>
                                                                    <span class="font-bold text-blue-600 text-lg">+{{ number_format($data['base_salary'], 0, ',', '.') }}đ</span>
                                                                </div>
                                                            </div>

                                                            {{-- Earnings from Trips --}}
                                                            @if($data['earning_records']->count() > 0 || $data['earning_transactions']->count() > 0)
                                                                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                                                    <h5 class="font-semibold text-green-800 mb-3 flex items-center justify-between">
                                                                        <span>🚗 Tiền công từ chuyến đi</span>
                                                                        <span class="text-sm font-normal">({{ $data['earning_records']->count() + $data['earning_transactions']->count() }} chuyến)</span>
                                                                    </h5>
                                                                    <div class="space-y-2 max-h-60 overflow-y-auto">
                                                                        {{-- Earnings from incident_staff --}}
                                                                        @foreach($data['earning_records'] as $record)
                                                                            <div class="flex justify-between items-start text-sm bg-white p-2 rounded border border-green-100">
                                                                                <div class="flex-1">
                                                                                    <div class="font-medium text-gray-800">
                                                                                        <a href="{{ route('incidents.show', $record->incident_id) }}" class="text-indigo-600 hover:text-indigo-800" target="_blank">
                                                                                            #{{ $record->incident_id }} - {{ $record->patient_name }}
                                                                                        </a>
                                                                                    </div>
                                                                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($record->incident_date)->format('d/m/Y') }}</div>
                                                                                    @if($record->destination)
                                                                                        <div class="text-xs text-gray-600 mt-1">→ {{ Str::limit($record->destination, 50) }}</div>
                                                                                    @endif
                                                                                    <div class="text-xs text-blue-600 mt-1">{{ $record->role }}</div>
                                                                                </div>
                                                                                <span class="font-semibold text-green-600 ml-2 whitespace-nowrap">+{{ number_format($record->wage_amount, 0, ',', '.') }}đ</span>
                                                                            </div>
                                                                        @endforeach

                                                                        {{-- Earnings from transactions (legacy) --}}
                                                                        @foreach($data['earning_transactions'] as $trans)
                                                                            <div class="flex justify-between items-start text-sm bg-white p-2 rounded border border-green-100">
                                                                                <div class="flex-1">
                                                                                    <div class="font-medium text-gray-800">
                                                                                        @if($trans->incident)
                                                                                            <a href="{{ route('incidents.show', $trans->incident) }}" class="text-indigo-600 hover:text-indigo-800" target="_blank">
                                                                                                #{{ $trans->incident->id }} - {{ $trans->incident->patient_name }}
                                                                                            </a>
                                                                                        @else
                                                                                            {{ $trans->category ?? 'Tiền công' }}
                                                                                        @endif
                                                                                    </div>
                                                                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($trans->date)->format('d/m/Y') }}</div>
                                                                                    @if($trans->description)
                                                                                        <div class="text-xs text-gray-600 mt-1">{{ Str::limit($trans->description, 50) }}</div>
                                                                                    @endif
                                                                                </div>
                                                                                <span class="font-semibold text-green-600 ml-2 whitespace-nowrap">+{{ number_format($trans->amount, 0, ',', '.') }}đ</span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="mt-2 pt-2 border-t border-green-300 flex justify-between font-bold">
                                                                        <span>Tổng tiền công:</span>
                                                                        <span class="text-green-700">+{{ number_format($data['earnings'], 0, ',', '.') }}đ</span>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            {{-- Additions Detail --}}
                                                            @if($data['adjustments']->where('type', 'addition')->count() > 0)
                                                                <div class="bg-teal-50 rounded-lg p-4 border border-teal-200">
                                                                    <h5 class="font-semibold text-teal-800 mb-3 flex items-center justify-between">
                                                                        <span>📈 Cộng thêm</span>
                                                                        <span class="text-sm font-normal">({{ $data['adjustments']->where('type', 'addition')->count() }} khoản)</span>
                                                                    </h5>
                                                                    <div class="space-y-2">
                                                                        @foreach($data['adjustments']->where('type', 'addition') as $adj)
                                                                            <div class="flex justify-between items-start text-sm bg-white p-2 rounded border border-teal-100">
                                                                                <div class="flex-1">
                                                                                    <div class="font-medium text-gray-800">{{ $adj->category }}</div>
                                                                                    @if($adj->reason)
                                                                                        <div class="text-xs text-gray-600 mt-1">{{ $adj->reason }}</div>
                                                                                    @endif
                                                                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($adj->month)->format('d/m/Y') }}</div>
                                                                                </div>
                                                                                <span class="font-semibold text-teal-600 ml-2 whitespace-nowrap">+{{ number_format($adj->amount, 0, ',', '.') }}đ</span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="mt-2 pt-2 border-t border-teal-300 flex justify-between font-bold">
                                                                        <span>Tổng cộng thêm:</span>
                                                                        <span class="text-teal-700">+{{ number_format($data['additions'], 0, ',', '.') }}đ</span>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        {{-- Right Column: Deductions --}}
                                                        <div class="space-y-4">
                                                            {{-- Deductions Detail --}}
                                                            @if($data['adjustments']->where('type', 'deduction')->count() > 0)
                                                                <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                                                                    <h5 class="font-semibold text-red-800 mb-3 flex items-center justify-between">
                                                                        <span>📉 Các khoản trừ</span>
                                                                        <span class="text-sm font-normal">({{ $data['adjustments']->where('type', 'deduction')->count() }} khoản)</span>
                                                                    </h5>
                                                                    <div class="space-y-2">
                                                                        @foreach($data['adjustments']->where('type', 'deduction') as $adj)
                                                                            <div class="flex justify-between items-start text-sm bg-white p-2 rounded border border-red-100">
                                                                                <div class="flex-1">
                                                                                    <div class="font-medium text-gray-800">{{ $adj->category }}</div>
                                                                                    @if($adj->reason)
                                                                                        <div class="text-xs text-gray-600 mt-1">{{ $adj->reason }}</div>
                                                                                    @endif
                                                                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($adj->month)->format('d/m/Y') }}</div>
                                                                                </div>
                                                                                <span class="font-semibold text-red-600 ml-2 whitespace-nowrap">-{{ number_format($adj->amount, 0, ',', '.') }}đ</span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="mt-2 pt-2 border-t border-red-300 flex justify-between font-bold">
                                                                        <span>Tổng trừ:</span>
                                                                        <span class="text-red-700">-{{ number_format($data['deductions'], 0, ',', '.') }}đ</span>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            {{-- Advances Detail --}}
                                                            @if($data['advance_records']->count() > 0)
                                                                <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                                                                    <h5 class="font-semibold text-orange-800 mb-3 flex items-center justify-between">
                                                                        <span>💰 Ứng lương</span>
                                                                        <span class="text-sm font-normal">({{ $data['advance_records']->count() }} lần)</span>
                                                                    </h5>
                                                                    <div class="space-y-2">
                                                                        @foreach($data['advance_records'] as $advance)
                                                                            <div class="flex justify-between items-start text-sm bg-white p-2 rounded border border-orange-100">
                                                                                <div class="flex-1">
                                                                                    <div class="font-medium text-gray-800">Ứng lương</div>
                                                                                    @if($advance->reason)
                                                                                        <div class="text-xs text-gray-600 mt-1">{{ $advance->reason }}</div>
                                                                                    @endif
                                                                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($advance->date)->format('d/m/Y') }}</div>
                                                                                </div>
                                                                                <span class="font-semibold text-orange-600 ml-2 whitespace-nowrap">-{{ number_format($advance->amount, 0, ',', '.') }}đ</span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="mt-2 pt-2 border-t border-orange-300 flex justify-between font-bold">
                                                                        <span>Tổng ứng:</span>
                                                                        <span class="text-orange-700">-{{ number_format($data['advances'], 0, ',', '.') }}đ</span>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            {{-- Summary Calculation --}}
                                                            <div class="bg-indigo-50 rounded-lg p-4 border-2 border-indigo-300">
                                                                <h5 class="font-bold text-indigo-900 mb-3">💵 Tổng kết</h5>
                                                                <div class="space-y-2 text-sm">
                                                                    <div class="flex justify-between">
                                                                        <span>Lương cơ bản:</span>
                                                                        <span class="font-semibold text-blue-600">{{ number_format($data['base_salary'], 0, ',', '.') }}đ</span>
                                                                    </div>
                                                                    <div class="flex justify-between">
                                                                        <span>Tiền công chuyến:</span>
                                                                        <span class="font-semibold text-green-600">+{{ number_format($data['earnings'], 0, ',', '.') }}đ</span>
                                                                    </div>
                                                                    <div class="flex justify-between">
                                                                        <span>Cộng thêm:</span>
                                                                        <span class="font-semibold text-teal-600">+{{ number_format($data['additions'], 0, ',', '.') }}đ</span>
                                                                    </div>
                                                                    <div class="flex justify-between">
                                                                        <span>Trừ:</span>
                                                                        <span class="font-semibold text-red-600">-{{ number_format($data['deductions'], 0, ',', '.') }}đ</span>
                                                                    </div>
                                                                    <div class="flex justify-between">
                                                                        <span>Ứng lương:</span>
                                                                        <span class="font-semibold text-orange-600">-{{ number_format($data['advances'], 0, ',', '.') }}đ</span>
                                                                    </div>
                                                                    <div class="border-t-2 border-indigo-400 pt-2 mt-2 flex justify-between">
                                                                        <span class="font-bold text-base">Thực lãnh:</span>
                                                                        <span class="font-bold text-lg {{ $data['total'] >= 0 ? 'text-indigo-700' : 'text-red-700' }}">
                                                                            {{ number_format($data['total'], 0, ',', '.') }}đ
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    @endforeach
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tfoot class="bg-gray-100 font-bold">
                                    <tr>
                                        <td colspan="4" class="px-2 py-2 text-right text-sm">TỔNG CỘNG:</td>
                                        <td class="px-2 py-2 text-right text-xs text-blue-600">{{ number_format($totalBaseSalary, 0, ',', '.') }}</td>
                                        <td class="px-2 py-2 text-right text-xs text-green-600">{{ number_format(collect($payrollData)->sum('earnings'), 0, ',', '.') }}</td>
                                        <td class="px-2 py-2 text-right text-xs text-green-600">+{{ number_format($totalAdditions, 0, ',', '.') }}</td>
                                        <td class="px-2 py-2 text-right text-xs text-red-600">-{{ number_format($totalDeductions, 0, ',', '.') }}</td>
                                        <td class="px-2 py-2 text-right text-xs text-orange-600">-{{ number_format(collect($payrollData)->sum('advances'), 0, ',', '.') }}</td>
                                        <td class="px-2 py-2 text-right text-indigo-600 text-base">{{ number_format($totalPayout, 0, ',', '.') }}</td>
                                        <td class="px-2 py-2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">Không có nhân viên nào có lương cơ bản trong tháng này</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info Box --}}
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4 print:hidden">
                <p class="text-sm text-blue-800">
                    ℹ️ <strong>Công thức tính:</strong><br>
                    <code>Thực lãnh = Lương cơ bản + Tiền công từ chuyến + Cộng thêm - Trừ - Ứng lương</code><br><br>
                    • <strong>Lương cơ bản:</strong> Lương tháng của nhân viên<br>
                    • <strong>Tiền công:</strong> Thu nhập từ các chuyến đi trong tháng<br>
                    • <strong>Cộng thêm:</strong> Thưởng, phụ cấp, điều chỉnh tăng<br>
                    • <strong>Trừ:</strong> Phạt, khấu trừ, điều chỉnh giảm<br>
                    • <strong>Ứng lương:</strong> Số tiền đã ứng trong tháng
                </p>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }
        
        @media print {
            .print\:hidden {
                display: none !important;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
    @endpush
</x-app-layout>
