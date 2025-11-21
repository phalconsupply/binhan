<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Quản lý Bệnh nhân') }}
            </h2>
            @can('manage patients')
            <a href="{{ route('patients.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Thêm bệnh nhân
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Thống kê -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Tổng bệnh nhân</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $statistics['total'] }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Nam</div>
                        <div class="text-2xl font-bold text-blue-600">{{ $statistics['male'] }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Nữ</div>
                        <div class="text-2xl font-bold text-pink-600">{{ $statistics['female'] }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Có chuyến đi</div>
                        <div class="text-2xl font-bold text-green-600">{{ $statistics['with_incidents'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Bộ lọc -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('patients.index') }}" class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Tên, SĐT, địa chỉ..."
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="min-w-[150px]">
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
                            <select name="gender" id="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Tất cả</option>
                                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                        <div class="min-w-[150px]">
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp</label>
                            <select name="sort" id="sort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Tên</option>
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                                <option value="incidents_count" {{ request('sort') == 'incidents_count' ? 'selected' : '' }}>Số chuyến</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Lọc
                        </button>
                        <a href="{{ route('patients.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Reset
                        </a>
                    </form>
                </div>
            </div>

            <!-- Danh sách bệnh nhân -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($patients->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bệnh nhân</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Liên hệ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tuổi/Giới tính</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số chuyến</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chuyến gần nhất</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($patients as $patient)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $patient->name }}</div>
                                        @if($patient->address)
                                        <div class="text-sm text-gray-500">{{ Str::limit($patient->address, 30) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $patient->phone }}</div>
                                        @if($patient->emergency_contact)
                                        <div class="text-sm text-gray-500">SOS: {{ $patient->emergency_contact }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $patient->age }} tuổi / {{ $patient->gender_label }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $patient->incidents->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($patient->incidents->first())
                                            {{ $patient->incidents->first()->date->diffForHumans() }}
                                        @else
                                            Chưa có
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('patients.show', $patient) }}" class="text-blue-600 hover:text-blue-900 mr-3">Chi tiết</a>
                                        @can('manage patients')
                                        <a href="{{ route('patients.edit', $patient) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Sửa</a>
                                        <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" 
                                                    onclick="return confirm('Bạn có chắc muốn xóa bệnh nhân này?')">Xóa</button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $patients->links() }}
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-4">Không tìm thấy bệnh nhân nào.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
