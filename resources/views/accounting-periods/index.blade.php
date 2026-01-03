<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📅 Quản lý kỳ kế toán
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div id="success-alert" class="mb-4 bg-green-500 border border-green-600 text-white px-4 py-3 rounded-lg shadow-lg relative flex items-center justify-between animate-slide-in">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button onclick="document.getElementById('success-alert').remove()" class="ml-4 text-white hover:text-green-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
                <script>
                    setTimeout(() => {
                        const alert = document.getElementById('success-alert');
                        if (alert) {
                            alert.style.transition = 'all 0.5s ease-out';
                            alert.style.opacity = '0';
                            alert.style.transform = 'translateY(-20px)';
                            setTimeout(() => alert.remove(), 500);
                        }
                    }, 5000);
                </script>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Thông tin hệ thống --}}
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Hệ thống khóa kỳ kế toán:</strong> Khi đóng/khóa một kỳ, không thể thêm, sửa, xóa giao dịch trong kỳ đó.
                            Điều này đảm bảo tính toàn vẹn của dữ liệu sau khi chốt sổ.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Danh sách periods --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">12 tháng gần nhất</h3>
                        <p class="text-sm text-gray-600">Quản lý trạng thái các kỳ kế toán</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kỳ
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Trạng thái
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Số giao dịch
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Người thao tác
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Thời gian
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Hành động
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($periods as $period)
                                <tr class="{{ $period->status === 'locked' ? 'bg-red-50' : ($period->status === 'closed' ? 'bg-yellow-50' : '') }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $period->display_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $period->status === 'locked' ? 'bg-red-100 text-red-800' : 
                                               ($period->status === 'closed' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ $period->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($period->transaction_count) }} giao dịch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($period->status === 'locked' && $period->lockedByUser)
                                            Khóa bởi: {{ $period->lockedByUser->name }}
                                        @elseif($period->status === 'closed' && $period->closedByUser)
                                            Đóng bởi: {{ $period->closedByUser->name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($period->locked_at)
                                            {{ $period->locked_at->format('d/m/Y H:i') }}
                                        @elseif($period->closed_at)
                                            {{ $period->closed_at->format('d/m/Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($period->status === 'open')
                                            <form action="{{ route('accounting-periods.close', $period) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-900" onclick="return confirm('Đóng kỳ {{ $period->display_name }}?')">
                                                    Đóng kỳ
                                                </button>
                                            </form>
                                        @elseif($period->status === 'closed')
                                            <form action="{{ route('accounting-periods.lock', $period) }}" method="POST" class="inline mr-2">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Khóa kỳ {{ $period->display_name }}? Sau khi khóa sẽ KHÔNG THỂ sửa được!')">
                                                    Khóa kỳ
                                                </button>
                                            </form>
                                            <form action="{{ route('accounting-periods.reopen', $period) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Mở lại kỳ {{ $period->display_name }}?')">
                                                    Mở lại
                                                </button>
                                            </form>
                                        @elseif($period->status === 'locked')
                                            @can('manage settings')
                                            <form action="{{ route('accounting-periods.unlock', $period) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="text-blue-600 hover:text-blue-900" onclick="return confirm('MỞ KHÓA kỳ {{ $period->display_name }}? Chỉ admin mới được phép!')">
                                                    Mở khóa
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-gray-400">Đã khóa</span>
                                            @endcan
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Chú thích --}}
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Các trạng thái:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 mr-2">
                                    🔓 Đang mở
                                </span>
                                <p class="text-gray-600 mt-1">Có thể thêm, sửa, xóa giao dịch tự do</p>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 mr-2">
                                    🔒 Đã đóng
                                </span>
                                <p class="text-gray-600 mt-1">Không thể sửa, có thể mở lại hoặc khóa</p>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 mr-2">
                                    🔐 Đã khóa
                                </span>
                                <p class="text-gray-600 mt-1">Chỉ admin mới mở khóa được</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
