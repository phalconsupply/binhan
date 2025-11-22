<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chi tiết nhân sự: {{ $staff->full_name }}
            </h2>
            <div class="space-x-2">
                @if(in_array($staff->staff_type, ['driver', 'medical_staff']))
                <a href="{{ route('staff.earnings', $staff) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    💰 Xem thu nhập
                </a>
                @endif
                @can('edit staff')
                <a href="{{ route('staff.edit', $staff) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Sửa
                </a>
                @endcan
                <a href="{{ route('staff.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Quay lại
                </a>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Info --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Basic Info --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Thông tin cơ bản</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Mã nhân viên</p>
                                    <p class="text-base font-semibold">{{ $staff->employee_code ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Họ và tên</p>
                                    <p class="text-base font-semibold">{{ $staff->full_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Loại nhân sự</p>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $staff->staff_type == 'admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $staff->staff_type == 'manager' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $staff->staff_type == 'medical_staff' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $staff->staff_type == 'driver' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $staff->staff_type == 'investor' ? 'bg-pink-100 text-pink-800' : '' }}">
                                        {{ $staff->staff_type_label }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Chức vụ</p>
                                    <p class="text-base">{{ $staff->position ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Phòng ban</p>
                                    <p class="text-base">{{ $staff->department ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Trạng thái</p>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $staff->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $staff->is_active ? 'Đang làm việc' : 'Đã nghỉ việc' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Info --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Thông tin liên hệ</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Số điện thoại</p>
                                    <p class="text-base">{{ $staff->phone ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="text-base">{{ $staff->email }}</p>
                                </div>
                                @if($staff->address)
                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-500">Địa chỉ</p>
                                    <p class="text-base">{{ $staff->address }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Personal Info --}}
                    @if($staff->id_card || $staff->birth_date || $staff->gender)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Thông tin cá nhân</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @if($staff->id_card)
                                <div>
                                    <p class="text-sm text-gray-500">CMND/CCCD</p>
                                    <p class="text-base">{{ $staff->id_card }}</p>
                                </div>
                                @endif
                                @if($staff->birth_date)
                                <div>
                                    <p class="text-sm text-gray-500">Ngày sinh</p>
                                    <p class="text-base">{{ $staff->birth_date->format('d/m/Y') }}</p>
                                </div>
                                @endif
                                @if($staff->gender)
                                <div>
                                    <p class="text-sm text-gray-500">Giới tính</p>
                                    <p class="text-base">{{ $staff->gender_label }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Notes --}}
                    @if($staff->notes)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Ghi chú</h3>
                            <p class="text-base">{{ $staff->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Work Info --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Thông tin công việc</h3>
                            @if($staff->hire_date)
                            <div class="mb-3">
                                <p class="text-sm text-gray-500">Ngày vào làm</p>
                                <p class="text-base font-semibold">{{ $staff->hire_date->format('d/m/Y') }}</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-500">Tài khoản đăng nhập</p>
                                <p class="text-base">{{ $staff->user->email }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Permissions Info --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Phân quyền</h3>
                            <div class="space-y-2">
                                @foreach($staff->user->roles as $role)
                                <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-800 text-sm rounded-full">
                                    {{ ucfirst($role->name) }}
                                </span>
                                @endforeach
                            </div>
                            
                            @if($staff->staff_type == 'admin' || $staff->staff_type == 'manager')
                            <div class="mt-4 p-3 bg-blue-50 rounded-md">
                                <p class="text-xs text-blue-800">✓ Full quyền quản lý</p>
                            </div>
                            @elseif($staff->staff_type == 'investor')
                            <div class="mt-4 p-3 bg-pink-50 rounded-md">
                                <p class="text-xs text-pink-800">👁️ Chỉ xem (không sửa/xóa)</p>
                            </div>
                            @elseif($staff->staff_type == 'medical_staff' || $staff->staff_type == 'driver')
                            <div class="mt-4 p-3 bg-green-50 rounded-md">
                                <p class="text-xs text-green-800">✓ Ghi nhận nhanh<br>👁️ Xem danh sách chuyến đi</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
