<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Quản lý nhân sự
            </h2>
            <div class="flex items-center space-x-2">
                @can('view staff')
                <a href="{{ route('staff.payroll') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    💰 Bảng lương
                </a>
                @endcan
                @can('create staff')
                <a href="{{ route('staff.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    + Thêm nhân sự
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

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Search & Filter --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('staff.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, mã NV, SĐT, email..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <select name="staff_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Tất cả loại</option>
                                    <option value="medical_staff" {{ request('staff_type') == 'medical_staff' ? 'selected' : '' }}>Nhân viên y tế</option>
                                    <option value="driver" {{ request('staff_type') == 'driver' ? 'selected' : '' }}>Lái xe</option>
                                    <option value="manager" {{ request('staff_type') == 'manager' ? 'selected' : '' }}>Quản lý</option>
                                    <option value="investor" {{ request('staff_type') == 'investor' ? 'selected' : '' }}>Cổ đông</option>
                                    <option value="vehicle_owner" {{ request('staff_type') == 'vehicle_owner' ? 'selected' : '' }}>Chủ xe</option>
                                    <option value="admin" {{ request('staff_type') == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            <div>
                                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang làm việc</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Đã nghỉ việc</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Tìm kiếm
                                </button>
                                @if(request()->hasAny(['search', 'staff_type', 'status']))
                                <a href="{{ route('staff.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    Xóa lọc
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Staff Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($staff->isEmpty())
                        <p class="text-gray-500 text-center py-8">Không tìm thấy nhân sự nào.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã NV</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Họ tên</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chức vụ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SĐT</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($staff as $member)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $member->employee_code ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('staff.show', $member) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                                {{ $member->full_name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $member->staff_type == 'admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $member->staff_type == 'manager' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $member->staff_type == 'medical_staff' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $member->staff_type == 'driver' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $member->staff_type == 'investor' ? 'bg-pink-100 text-pink-800' : '' }}
                                                {{ $member->staff_type == 'vehicle_owner' ? 'bg-orange-100 text-orange-800' : '' }}">
                                                {{ $member->staff_type_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $member->position ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $member->phone ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $member->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $member->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $member->is_active ? 'Đang làm' : 'Đã nghỉ' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <a href="{{ route('staff.show', $member) }}" class="text-blue-600 hover:text-blue-900">Xem</a>
                                            @if(in_array($member->staff_type, ['driver', 'medical_staff', 'manager']))
                                            <a href="{{ route('staff.earnings', $member) }}" class="text-green-600 hover:text-green-900">Thu nhập</a>
                                            @endif
                                            @can('edit staff')
                                            <a href="{{ route('staff.edit', $member) }}" class="text-indigo-600 hover:text-indigo-900">Sửa</a>
                                            @endcan
                                            @can('delete staff')
                                            <form action="{{ route('staff.destroy', $member) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân sự này?');">
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

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $staff->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
