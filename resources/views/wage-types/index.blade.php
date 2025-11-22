<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Quản lý Loại Tiền Công
            </h2>
            <a href="{{ route('wage-types.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                + Thêm loại mới
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Quản lý các loại tiền công sẽ hiển thị trong dropdown khi nhập tiền công cho nhân viên. Bạn có thể thêm, sửa, xóa hoặc sắp xếp thứ tự hiển thị.
                    </p>

                    @if($wageTypes->isEmpty())
                        <p class="text-gray-500 text-center py-8">Chưa có loại tiền công nào.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thứ tự</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên loại tiền</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($wageTypes as $wageType)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $wageType->sort_order }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-semibold text-gray-900">{{ $wageType->name }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $wageType->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $wageType->is_active ? 'Đang dùng' : 'Tắt' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <a href="{{ route('wage-types.edit', $wageType) }}" class="text-indigo-600 hover:text-indigo-900">Sửa</a>
                                            <form action="{{ route('wage-types.destroy', $wageType) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa loại tiền công này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">💡 Hướng dẫn sử dụng:</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• <strong>Thứ tự</strong>: Số càng nhỏ sẽ hiển thị càng đầu trong dropdown</li>
                    <li>• <strong>Trạng thái</strong>: Chỉ các loại "Đang dùng" mới hiển thị khi nhập liệu</li>
                    <li>• <strong>Thêm mới</strong>: Các loại tiền phổ biến như "Phụ cấp", "Ăn ca", "Xăng xe", "Ca đêm"...</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
