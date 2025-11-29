<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🛠️ Chi tiết Tài sản
            </h2>
            <div class="space-x-2">
                @can('manage settings')
                <a href="{{ route('assets.edit', $asset) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    ✏️ Chỉnh sửa
                </a>
                @endcan
                <a href="{{ route('assets.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                    ← Quay lại
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Asset Info --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">📋 Thông tin tài sản</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500">Tên tài sản</p>
                            <p class="text-base font-medium text-gray-900">{{ $asset->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Nhãn hiệu</p>
                            <p class="text-base font-medium text-gray-900">{{ $asset->brand ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Ngày trang bị</p>
                            <p class="text-base font-medium text-gray-900">{{ $asset->equipped_date->format('d/m/Y') }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Số lượng</p>
                            <p class="text-base font-medium text-gray-900">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $asset->quantity }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Loại</p>
                            <p class="text-base font-medium text-gray-900">
                                @if($asset->usage_type === 'vehicle')
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                        🚗 Tài sản xe
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                                        👤 Tài sản cá nhân
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Nơi sử dụng</p>
                            <p class="text-base font-medium text-gray-900">
                                @if($asset->usage_type === 'vehicle' && $asset->vehicle)
                                    <a href="{{ route('vehicles.show', $asset->vehicle) }}" class="text-blue-600 hover:text-blue-800">
                                        Xe {{ $asset->vehicle->license_plate }}
                                    </a>
                                @elseif($asset->usage_type === 'staff' && $asset->staff)
                                    <a href="{{ route('staff.show', $asset->staff) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $asset->staff->full_name }}
                                    </a>
                                @else
                                    <span class="text-gray-500">Chưa phân bổ</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Trạng thái</p>
                            <p class="text-base font-medium">
                                @if($asset->is_active)
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                        ✓ Đang sử dụng
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                        ✕ Ngừng sử dụng
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Thời gian sử dụng</p>
                            <p class="text-base font-medium text-gray-900">
                                {{ $asset->equipped_date->diffForHumans() }}
                            </p>
                        </div>

                        @if($asset->note)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500">Ghi chú</p>
                            <p class="text-base text-gray-900 whitespace-pre-line">{{ $asset->note }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Metadata --}}
                <div class="p-6 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">ℹ️ Thông tin hệ thống</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Người tạo</p>
                            <p class="text-gray-900">{{ $asset->creator->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Ngày tạo</p>
                            <p class="text-gray-900">{{ $asset->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($asset->updater)
                        <div>
                            <p class="text-gray-500">Người cập nhật</p>
                            <p class="text-gray-900">{{ $asset->updater->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Cập nhật lần cuối</p>
                            <p class="text-gray-900">{{ $asset->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Delete Button --}}
            @can('manage settings')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-red-600 mb-3">⚠️ Xóa tài sản</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Xóa tài sản sẽ không thể khôi phục. Hãy chắc chắn trước khi thực hiện.
                </p>
                <form action="{{ route('assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài sản này?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        🗑️ Xóa tài sản
                    </button>
                </form>
            </div>
            @endcan
        </div>
    </div>
</x-app-layout>
