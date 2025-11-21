<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quản lý Ghi chú') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Thống kê -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Tổng ghi chú</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $statistics['total'] }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Thông tin</div>
                        <div class="text-2xl font-bold text-blue-600">{{ $statistics['info'] }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Cảnh báo</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ $statistics['warning'] }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Quan trọng</div>
                        <div class="text-2xl font-bold text-red-600">{{ $statistics['critical'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Bộ lọc -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('notes.index') }}" class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Nội dung ghi chú..."
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="min-w-[150px]">
                            <label for="severity" class="block text-sm font-medium text-gray-700 mb-1">Mức độ</label>
                            <select name="severity" id="severity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Tất cả</option>
                                <option value="info" {{ request('severity') == 'info' ? 'selected' : '' }}>Thông tin</option>
                                <option value="warning" {{ request('severity') == 'warning' ? 'selected' : '' }}>Cảnh báo</option>
                                <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Quan trọng</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Lọc
                        </button>
                        <a href="{{ route('notes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Reset
                        </a>
                    </form>
                </div>
            </div>

            <!-- Danh sách ghi chú -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($notes->count() > 0)
                    <div class="space-y-4">
                        @foreach($notes as $note)
                        <div class="border border-gray-200 rounded-lg p-4 {{ $note->severity == 'critical' ? 'border-red-300 bg-red-50' : ($note->severity == 'warning' ? 'border-yellow-300 bg-yellow-50' : '') }}">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $note->severity_color }}">
                                        {{ $note->severity_label }}
                                    </span>
                                    @if($note->vehicle)
                                    <a href="{{ route('vehicles.show', $note->vehicle) }}" class="text-sm text-blue-600 hover:underline">
                                        Xe: {{ $note->vehicle->license_plate }}
                                    </a>
                                    @endif
                                    @if($note->incident)
                                    <a href="{{ route('incidents.show', $note->incident) }}" class="text-sm text-blue-600 hover:underline">
                                        Chuyến #{{ $note->incident->id }}
                                    </a>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $note->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="text-sm text-gray-900 mb-2">{{ $note->note }}</div>
                            <div class="flex justify-between items-center">
                                <div class="text-xs text-gray-500">
                                    Bởi: {{ $note->user->name }}
                                </div>
                                @can('delete incidents')
                                <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-900" 
                                            onclick="return confirm('Bạn có chắc muốn xóa ghi chú này?')">
                                        Xóa
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $notes->links() }}
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-4">Không có ghi chú nào.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
