@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Quản lý giao dịch của tôi</h1>
        <p class="text-gray-600">Tổng hợp giao dịch từ {{ count($vehicleStats) }} xe</p>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div class="w-full">
                    <p class="text-sm text-gray-600 mb-1">Tổng thu</p>
                    <p class="text-xs text-green-600 mb-3">(Thu + Nộp quỹ)</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($stats['month_revenue_display'], 0, ',', '.') }}đ</p>
                    <p class="text-xs text-gray-400 mt-1">Tháng này</p>
                    <p class="text-sm text-gray-500 mt-2">Tổng: {{ number_format($stats['total_revenue_display'], 0, ',', '.') }}đ</p>
                </div>
            </div>
        </div>

        <!-- Total Expense -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div class="w-full">
                    <p class="text-sm text-gray-600 mb-1">Tổng chi</p>
                    <p class="text-xs text-red-600 mb-3">(Chi + Phí 15%)</p>
                    <p class="text-3xl font-bold text-red-600">{{ number_format($stats['month_expense_display'], 0, ',', '.') }}đ</p>
                    <p class="text-xs text-gray-400 mt-1">Tháng này</p>
                    <p class="text-sm text-gray-500 mt-2">Tổng: {{ number_format($stats['total_expense_display'], 0, ',', '.') }}đ</p>
                </div>
            </div>
        </div>

        <!-- Outstanding Debt -->
        <div class="bg-white rounded-lg shadow p-4 border-l-4 {{ $stats['total_debt'] > 0 ? 'border-orange-500' : 'border-green-500' }}">
            <div class="flex items-center justify-between">
                <div class="w-full">
                    <p class="text-sm text-gray-600 mb-4">Khoản đang vay</p>
                    <p class="text-3xl font-bold {{ $stats['month_debt'] > 0 ? 'text-orange-600' : 'text-green-600' }}">{{ number_format($stats['month_debt'], 0, ',', '.') }}đ</p>
                    <p class="text-xs text-gray-400 mt-1">Tháng này</p>
                    <p class="text-sm text-gray-500 mt-2">Tổng: {{ number_format($stats['total_debt'], 0, ',', '.') }}đ</p>
                </div>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div class="w-full">
                    <p class="text-sm text-gray-600 mb-1">Lợi nhuận</p>
                    <p class="text-xs text-blue-600 mb-3">(Thu - Chi - Vay)</p>
                    <p class="text-3xl font-bold {{ $stats['month_profit'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">{{ number_format($stats['month_profit'], 0, ',', '.') }}đ</p>
                    <p class="text-xs text-gray-400 mt-1">Tháng này</p>
                    <p class="text-sm text-gray-500 mt-2">Tổng: {{ number_format($stats['total_profit'], 0, ',', '.') }}đ</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle Stats -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Thống kê theo xe</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Biển số xe</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tổng thu</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tổng chi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Khoản vay</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Lợi nhuận</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tháng này</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($vehicleStats as $vStats)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('vehicles.show', $vStats['vehicle']->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                {{ $vStats['vehicle']->license_plate }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-green-600">
                            {{ number_format($vStats['total_revenue_display'], 0, ',', '.') }}đ
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-red-600">
                            {{ number_format($vStats['total_expense_display'], 0, ',', '.') }}đ
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right {{ $vStats['total_debt'] > 0 ? 'text-orange-600' : 'text-gray-400' }}">
                            {{ number_format($vStats['total_debt'], 0, ',', '.') }}đ
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium {{ $vStats['total_profit'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            {{ number_format($vStats['total_profit'], 0, ',', '.') }}đ
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-xs">
                                <div class="text-green-600">+{{ number_format($vStats['month_revenue_display'], 0, ',', '.') }}</div>
                                <div class="text-red-600">-{{ number_format($vStats['month_expense_display'], 0, ',', '.') }}</div>
                                <div class="font-medium {{ $vStats['month_profit'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                    = {{ number_format($vStats['month_profit'], 0, ',', '.') }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Transactions by Vehicle -->
    @foreach($transactionsByVehicle as $vehicleData)
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b bg-blue-50">
            <h2 class="text-lg font-semibold text-gray-800">
                Xe {{ $vehicleData['vehicle']->license_plate }}
            </h2>
        </div>

        {{-- Chuyến đi --}}
        @if($vehicleData['incidents']->count() > 0)
        <div class="px-6 py-4 border-b transaction-table-container" data-table-id="incidents-{{ $vehicleData['vehicle']->id }}">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-md font-semibold text-gray-700">Chuyến đi ({{ $vehicleData['incidents']->total() }})</h3>
                <a href="{{ route('owner.transactions.export-incidents', $vehicleData['vehicle']->id) }}" 
                   class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Excel
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ngày</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Chuyến</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">GD</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Thu</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Chi</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Phí 15%</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Lợi nhuận</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($vehicleData['incidents'] as $group)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($group['date'])->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-2 text-sm">
                                <a href="{{ route('incidents.show', $group['incident']->id) }}" class="text-blue-600 hover:underline font-medium">
                                    #{{ $group['incident']->id }}
                                </a>
                                @if($group['incident']->patient)
                                    <div class="text-xs text-gray-500">{{ $group['incident']->patient->name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">{{ $group['count'] }}</span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium text-green-600">
                                {{ number_format($group['total_revenue'], 0, ',', '.') }}đ
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium text-red-600">
                                {{ number_format($group['total_expense'], 0, ',', '.') }}đ
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium text-orange-600">
                                {{ number_format($group['management_fee'] ?? 0, 0, ',', '.') }}đ
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-bold {{ ($group['profit_after_fee'] ?? $group['net_amount']) >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                {{ number_format($group['profit_after_fee'] ?? $group['net_amount'], 0, ',', '.') }}đ
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($vehicleData['incidents']->hasPages())
            <div class="mt-4 transaction-table-pagination">
                {{ $vehicleData['incidents']->appends(request()->except('incidents_page'))->links() }}
            </div>
            @endif
        </div>
        @endif

        {{-- Bảo trì xe --}}
        @if($vehicleData['maintenances']->count() > 0)
        <div class="px-6 py-4 border-b transaction-table-container" data-table-id="maintenances-{{ $vehicleData['vehicle']->id }}">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-md font-semibold text-gray-700">Bảo trì xe ({{ $vehicleData['maintenances']->total() }})</h3>
                <a href="{{ route('owner.transactions.export-maintenances', $vehicleData['vehicle']->id) }}" 
                   class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Excel
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ngày</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dịch vụ</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Đối tác</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">GD</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Chi phí</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($vehicleData['maintenances'] as $group)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($group['date'])->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-2 text-sm text-green-600 font-medium">
                                🔧 {{ $group['maintenance']->maintenanceService->name ?? 'Bảo trì' }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-600">
                                {{ $group['maintenance']->partner->name ?? '-' }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">{{ $group['count'] }}</span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium text-red-600">
                                {{ number_format($group['total_expense'], 0, ',', '.') }}đ
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($vehicleData['maintenances']->hasPages())
            <div class="mt-4 transaction-table-pagination">
                {{ $vehicleData['maintenances']->appends(request()->except('maintenances_page'))->links() }}
            </div>
            @endif
        </div>
        @endif

        {{-- Giao dịch khác --}}
        @if($vehicleData['others']->count() > 0)
        <div class="px-6 py-4 transaction-table-container" data-table-id="others-{{ $vehicleData['vehicle']->id }}">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-md font-semibold text-gray-700">Giao dịch khác ({{ $vehicleData['others']->total() }})</h3>
                <a href="{{ route('owner.transactions.export-others', $vehicleData['vehicle']->id) }}" 
                   class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Excel
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ngày</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mã GD</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ghi chú</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Thu</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Chi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($vehicleData['others'] as $group)
                        @php
                            $transaction = $group['transactions']->first();
                            $isRevenue = in_array($transaction->type, ['thu', 'vay_cong_ty', 'nop_quy']);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-mono text-gray-600">
                                {{ $transaction->transaction_code }}
                            </td>
                            <td class="px-4 py-2 text-sm">
                                @if($transaction->type == 'vay_cong_ty')
                                    <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">Vay công ty</span>
                                @elseif($transaction->type == 'tra_cong_ty')
                                    <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">Trả công ty</span>
                                @elseif($transaction->type == 'nop_quy')
                                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Nộp quỹ</span>
                                @elseif($transaction->type == 'thu')
                                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Thu khác</span>
                                @elseif($transaction->type == 'chi')
                                    <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Chi khác</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">{{ $transaction->type }}</span>
                                @endif
                                @if($transaction->category)
                                    <div class="text-xs text-gray-500 mt-1">{{ $transaction->category }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-600">
                                {{ $transaction->notes ?? '-' }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium {{ $isRevenue ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $isRevenue ? number_format($transaction->amount, 0, ',', '.') . 'đ' : '-' }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium {{ !$isRevenue ? 'text-red-600' : 'text-gray-400' }}">
                                {{ !$isRevenue ? number_format($transaction->amount, 0, ',', '.') . 'đ' : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($vehicleData['others']->hasPages())
            <div class="mt-4 transaction-table-pagination">
                {{ $vehicleData['others']->appends(request()->except('others_page'))->links() }}
            </div>
            @endif
        </div>
        @endif
    </div>
    @endforeach
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle pagination clicks for each table
    document.querySelectorAll('.transaction-table-pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = this.getAttribute('href');
            const tableContainer = this.closest('.transaction-table-container');
            const tableId = tableContainer.dataset.tableId;
            
            // Show loading state
            tableContainer.style.opacity = '0.5';
            
            // Fetch new data
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Parse the HTML response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Find the matching table container in the response
                const newTableContainer = doc.querySelector(`[data-table-id="${tableId}"]`);
                
                if (newTableContainer) {
                    // Replace the table content
                    tableContainer.innerHTML = newTableContainer.innerHTML;
                    
                    // Scroll to the table
                    tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                
                // Reset opacity
                tableContainer.style.opacity = '1';
            })
            .catch(error => {
                console.error('Error loading data:', error);
                tableContainer.style.opacity = '1';
            });
        });
    });
});
</script>
@endpush
@endsection
