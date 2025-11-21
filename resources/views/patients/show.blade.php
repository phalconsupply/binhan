<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Chi tiết Bệnh nhân') }}
            </h2>
            <div class="flex space-x-2">
                @can('manage patients')
                <a href="{{ route('patients.edit', $patient) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Chỉnh sửa
                </a>
                @endcan
                <a href="{{ route('patients.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    Quay lại
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Thông tin bệnh nhân -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin cá nhân</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm text-gray-500">Họ tên</div>
                            <div class="text-base font-medium text-gray-900">{{ $patient->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Số điện thoại</div>
                            <div class="text-base font-medium text-gray-900">{{ $patient->phone }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Ngày sinh / Tuổi</div>
                            <div class="text-base font-medium text-gray-900">
                                {{ $patient->date_of_birth->format('d/m/Y') }} ({{ $patient->age }} tuổi)
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Giới tính</div>
                            <div class="text-base font-medium text-gray-900">{{ $patient->gender_label }}</div>
                        </div>
                        @if($patient->emergency_contact)
                        <div>
                            <div class="text-sm text-gray-500">SĐT khẩn cấp</div>
                            <div class="text-base font-medium text-gray-900">{{ $patient->emergency_contact }}</div>
                        </div>
                        @endif
                        @if($patient->address)
                        <div class="md:col-span-2">
                            <div class="text-sm text-gray-500">Địa chỉ</div>
                            <div class="text-base font-medium text-gray-900">{{ $patient->address }}</div>
                        </div>
                        @endif
                        @if($patient->medical_notes)
                        <div class="md:col-span-2">
                            <div class="text-sm text-gray-500">Ghi chú y tế</div>
                            <div class="text-base font-medium text-gray-900 whitespace-pre-line">{{ $patient->medical_notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Thống kê -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Tổng chuyến đi</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $statistics['total_incidents'] }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Tổng chi tiêu</div>
                        <div class="text-2xl font-bold text-green-600">{{ number_format($statistics['total_spent'], 0, ',', '.') }}đ</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Chi tiêu trung bình</div>
                        <div class="text-2xl font-bold text-blue-600">
                            {{ $statistics['total_incidents'] > 0 ? number_format($statistics['total_spent'] / $statistics['total_incidents'], 0, ',', '.') : 0 }}đ
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Chuyến gần nhất</div>
                        <div class="text-base font-medium text-gray-900">
                            @if($statistics['last_incident_date'])
                                {{ $statistics['last_incident_date']->diffForHumans() }}
                            @else
                                Chưa có
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lịch sử chuyến đi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Lịch sử chuyến đi</h3>
                    @if($patient->incidents->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Xe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Điểm đến</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Điều phối</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chi phí</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($patient->incidents as $incident)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $incident->date->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $incident->vehicle->license_plate }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $incident->destination ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $incident->dispatcher->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                        {{ number_format($incident->total_revenue, 0, ',', '.') }}đ
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('incidents.show', $incident) }}" class="text-blue-600 hover:text-blue-900">Chi tiết</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-4">Chưa có chuyến đi nào.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
