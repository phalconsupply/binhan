<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chỉnh sửa Bệnh nhân') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('patients.update', $patient) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tên -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Họ tên <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $patient->name) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Số điện thoại -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Số điện thoại <span class="text-red-500">*</span></label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $patient->phone) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Ngày sinh -->
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Ngày sinh <span class="text-red-500">*</span></label>
                                <input type="date" name="date_of_birth" id="date_of_birth" 
                                       value="{{ old('date_of_birth', $patient->date_of_birth->format('Y-m-d')) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('date_of_birth')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Giới tính -->
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700">Giới tính <span class="text-red-500">*</span></label>
                                <select name="gender" id="gender" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Chọn giới tính</option>
                                    <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                                    <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                                    <option value="other" {{ old('gender', $patient->gender) == 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Liên hệ khẩn cấp -->
                            <div>
                                <label for="emergency_contact" class="block text-sm font-medium text-gray-700">SĐT khẩn cấp</label>
                                <input type="text" name="emergency_contact" id="emergency_contact" 
                                       value="{{ old('emergency_contact', $patient->emergency_contact) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('emergency_contact')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Địa chỉ -->
                        <div class="mt-6">
                            <label for="address" class="block text-sm font-medium text-gray-700">Địa chỉ</label>
                            <textarea name="address" id="address" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $patient->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ghi chú y tế -->
                        <div class="mt-6">
                            <label for="medical_notes" class="block text-sm font-medium text-gray-700">Ghi chú y tế</label>
                            <textarea name="medical_notes" id="medical_notes" rows="3"
                                      placeholder="Tiền sử bệnh, dị ứng thuốc, ghi chú đặc biệt..."
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('medical_notes', $patient->medical_notes) }}</textarea>
                            @error('medical_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('patients.show', $patient) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                Hủy
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
