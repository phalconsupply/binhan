<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Thêm nhân sự mới
            </h2>
            <a href="{{ route('staff.index') }}" class="text-indigo-600 hover:text-indigo-900">
                ← Quay lại danh sách
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6" x-data="{ staffType: '{{ old('staff_type', '') }}' }">
                    <form method="POST" action="{{ route('staff.store') }}" class="space-y-6">
                        @csrf

                        {{-- Basic Info --}}
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin cơ bản</h3>
                            
                            <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                <p class="text-sm text-blue-800">
                                    ℹ️ Mã nhân viên sẽ được tự động sinh (VD: NV001, NV002, ...)
                                </p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="full_name" class="block text-sm font-medium text-gray-700">
                                        Họ và tên <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('full_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="staff_type" class="block text-sm font-medium text-gray-700">
                                        Loại nhân sự <span class="text-red-500">*</span>
                                    </label>
                                    <select id="staff_type" name="staff_type" required 
                                        x-model="staffType"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">-- Chọn loại --</option>
                                        <option value="medical_staff" {{ old('staff_type') == 'medical_staff' ? 'selected' : '' }}>Nhân viên y tế</option>
                                        <option value="driver" {{ old('staff_type') == 'driver' ? 'selected' : '' }}>Lái xe</option>
                                        <option value="manager" {{ old('staff_type') == 'manager' ? 'selected' : '' }}>Quản lý</option>
                                        <option value="investor" {{ old('staff_type') == 'investor' ? 'selected' : '' }}>Cổ đông</option>
                                        <option value="admin" {{ old('staff_type') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    @error('staff_type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div x-show="staffType === 'investor'" x-cloak>
                                    <label for="equity_percentage" class="block text-sm font-medium text-gray-700">
                                        Tỷ lệ vốn góp (%) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="equity_percentage" name="equity_percentage" value="{{ old('equity_percentage') }}" 
                                        step="0.01" min="0" max="100"
                                        :required="staffType === 'investor'"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="VD: 25.5">
                                    <p class="mt-1 text-xs text-gray-500">💡 Tỷ lệ vốn góp sẽ là căn cứ để chia lợi nhuận</p>
                                    @error('equity_percentage')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="position" class="block text-sm font-medium text-gray-700">
                                        Chức vụ
                                    </label>
                                    <input type="text" id="position" name="position" value="{{ old('position') }}" 
                                        list="position-list"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Nhập hoặc chọn chức vụ...">
                                    <datalist id="position-list">
                                        @foreach(\App\Models\Position::active()->orderBy('name')->get() as $pos)
                                            <option value="{{ $pos->name }}">
                                        @endforeach
                                    </datalist>
                                    @error('position')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Contact Info --}}
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin liên hệ</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">
                                        Số điện thoại
                                    </label>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">
                                        Địa chỉ
                                    </label>
                                    <textarea id="address" name="address" rows="2" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address') }}</textarea>
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Personal Info --}}
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin cá nhân</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="id_card" class="block text-sm font-medium text-gray-700">
                                        CMND/CCCD
                                    </label>
                                    <input type="text" id="id_card" name="id_card" value="{{ old('id_card') }}" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('id_card')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="birth_date" class="block text-sm font-medium text-gray-700">
                                        Ngày sinh
                                    </label>
                                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('birth_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700">
                                        Giới tính
                                    </label>
                                    <select id="gender" name="gender" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">-- Chọn --</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                    @error('gender')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Work Info --}}
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin công việc</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="hire_date" class="block text-sm font-medium text-gray-700">
                                        Ngày vào làm
                                    </label>
                                    <input type="date" id="hire_date" name="hire_date" value="{{ old('hire_date', now()->format('Y-m-d')) }}" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('hire_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="department" class="block text-sm font-medium text-gray-700">
                                        Phòng ban
                                    </label>
                                    <input type="text" id="department" name="department" value="{{ old('department') }}" 
                                        list="department-list"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Nhập hoặc chọn phòng ban...">
                                    <datalist id="department-list">
                                        @foreach(\App\Models\Department::active()->orderBy('name')->get() as $dept)
                                            <option value="{{ $dept->name }}">
                                        @endforeach
                                    </datalist>
                                    @error('department')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="base_salary" class="block text-sm font-medium text-gray-700">
                                        Lương cơ bản (tháng)
                                    </label>
                                    <input type="number" id="base_salary" name="base_salary" value="{{ old('base_salary') }}" 
                                        step="1000" min="0"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="VD: 5000000">
                                    <p class="mt-1 text-xs text-gray-500">💡 Thu nhập cố định hàng tháng, tự động tính vào earnings</p>
                                    @error('base_salary')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">
                                        Ghi chú
                                    </label>
                                    <textarea id="notes" name="notes" rows="3" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Account Info --}}
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Tài khoản đăng nhập</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">
                                        Mật khẩu <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" id="password" name="password" required 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                        Xác nhận mật khẩu <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" required 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Đang làm việc</span>
                            </label>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('staff.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Hủy
                            </a>
                            <button type="submit" name="action" value="save_continue" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Lưu và tiếp tục
                            </button>
                            <button type="submit" name="action" value="save_close" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Lưu và đóng
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
