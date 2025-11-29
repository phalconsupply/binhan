<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Báo cáo & Thống kê') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Bộ lọc ngày -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                            <input type="date" name="date_from" id="date_from" 
                                   value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                            <input type="date" name="date_to" id="date_to" 
                                   value="{{ request('date_to', now()->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Lọc
                        </button>
                    </form>
                </div>
            </div>

            <!-- Thống kê tổng quan -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Tổng chuyến đi</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $statistics['total_incidents'] }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Tổng thu</div>
                        <div class="text-2xl font-bold text-green-600">{{ number_format($statistics['total_revenue'], 0, ',', '.') }}đ</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Tổng chi</div>
                        <div class="text-2xl font-bold text-red-600">{{ number_format($statistics['total_expense'], 0, ',', '.') }}đ</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Lợi nhuận</div>
                        <div class="text-2xl font-bold {{ $statistics['net_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($statistics['net_profit'], 0, ',', '.') }}đ
                        </div>
                    </div>
                </div>
            </div>

            <!-- Xuất báo cáo -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Xuất báo cáo</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Báo cáo phòng điều dưỡng -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Báo cáo phòng điều dưỡng</h4>
                            <p class="text-sm text-gray-600 mb-4">Báo cáo chuyển viện</p>
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('reports.department.preview', ['date_from' => request('date_from'), 'date_to' => request('date_to')]) }}" 
                                   class="px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 text-center">
                                    📋 Xem trước & Chỉnh sửa
                                </a>
                            </div>
                        </div>

                        <!-- Báo cáo chi tiết theo khoa -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Báo cáo chi tiết theo khoa</h4>
                            <p class="text-sm text-gray-600 mb-4">Chi tiết theo từng khoa phòng</p>
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('reports.locations.preview', ['date_from' => request('date_from'), 'date_to' => request('date_to')]) }}" 
                                   class="px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 text-center">
                                    📋 Xem trước & Chọn khoa
                                </a>
                            </div>
                        </div>

                        <!-- Báo cáo xe -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Báo cáo hiệu suất xe</h4>
                            <p class="text-sm text-gray-600 mb-4">Thống kê chi tiết theo từng xe</p>
                            <div class="flex gap-2">
                                <a href="{{ route('reports.export.vehicles.excel', ['date_from' => request('date_from'), 'date_to' => request('date_to')]) }}" 
                                   class="flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 text-center">
                                    Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hiệu suất theo xe -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Hiệu suất theo xe</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Xe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số chuyến</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng thu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng chi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lợi nhuận</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($vehiclePerformance as $vehicle)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $vehicle->license_plate }}</div>
                                        <div class="text-sm text-gray-500">{{ $vehicle->type }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $vehicle->incidents_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                        {{ number_format($vehicle->total_revenue, 0, ',', '.') }}đ
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                        {{ number_format($vehicle->total_expense, 0, ',', '.') }}đ
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $vehicle->net_profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($vehicle->net_profit, 0, ',', '.') }}đ
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Biểu đồ doanh thu hàng ngày -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Doanh thu & Chi phí hàng ngày</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số chuyến</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lợi nhuận</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($dailyRevenue as $day)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $day->count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                        {{ number_format($day->revenue, 0, ',', '.') }}đ
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                        {{ number_format($day->expense, 0, ',', '.') }}đ
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $day->net >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($day->net, 0, ',', '.') }}đ
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bệnh nhân thường xuyên -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Bệnh nhân sử dụng dịch vụ nhiều nhất</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bệnh nhân</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số điện thoại</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số chuyến</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng chi tiêu</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($topPatients as $patient)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $patient->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $patient->gender_label }} - {{ $patient->age }} tuổi</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $patient->phone }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $patient->incidents_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                        {{ number_format($patient->total_spent, 0, ',', '.') }}đ
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
