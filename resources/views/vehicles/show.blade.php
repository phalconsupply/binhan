<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chi tiết xe: {{ $vehicle->license_plate }}
            </h2>
            <div class="space-x-2">
                @can('edit vehicles')
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Sửa
                </a>
                @endcan
                <a href="{{ route('vehicles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Quay lại
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Vehicle Info --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Thông tin xe</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Biển số</p>
                            <p class="text-lg font-semibold text-blue-600">{{ $vehicle->license_plate }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Trạng thái</p>
                            <span class="inline-flex px-2 text-xs leading-5 font-semibold rounded-full 
                                {{ $vehicle->status == 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $vehicle->status == 'inactive' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $vehicle->status == 'maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ $vehicle->status_label }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Mẫu xe</p>
                            <p class="text-base">{{ $vehicle->model ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tài xế</p>
                            <p class="text-base">{{ $vehicle->driver_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Lái xe chính</p>
                            <p class="text-base">
                                @if($vehicle->driver)
                                    <a href="{{ route('staff.show', $vehicle->driver) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $vehicle->driver->employee_code }} - {{ $vehicle->driver->full_name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Chủ xe</p>
                            <p class="text-base">
                                @if($vehicle->owner)
                                    <a href="{{ route('staff.show', $vehicle->owner) }}" class="text-orange-600 hover:text-orange-900 font-semibold">
                                        {{ $vehicle->owner->employee_code }} - {{ $vehicle->owner->full_name }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Chưa có</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Số điện thoại</p>
                            <p class="text-base">{{ $vehicle->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Ngày tạo</p>
                            <p class="text-base">{{ $vehicle->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($vehicle->note)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500">Ghi chú</p>
                            <p class="text-base">{{ $vehicle->note }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Tổng chuyến đi</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_incidents'] }}</p>
                        <p class="text-xs text-gray-500">{{ $stats['this_month_incidents'] }} chuyến tháng này</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Tổng thu</p>
                        <p class="text-xl font-bold text-green-600">{{ number_format($stats['total_revenue'], 0, ',', '.') }}đ</p>
                        <p class="text-xs text-gray-500">{{ number_format($stats['month_revenue'], 0, ',', '.') }}đ tháng này</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Tổng chi</p>
                        <p class="text-xl font-bold text-red-600">{{ number_format($stats['total_expense'], 0, ',', '.') }}đ</p>
                        <p class="text-xs text-gray-500">{{ number_format($stats['month_expense'], 0, ',', '.') }}đ tháng này</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Lợi nhuận</p>
                        <p class="text-xl font-bold {{ $stats['total_net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($stats['total_net'], 0, ',', '.') }}đ
                        </p>
                        <p class="text-xs {{ $stats['month_net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($stats['month_net'], 0, ',', '.') }}đ tháng này
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Recent Incidents --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Chuyến đi gần đây</h3>
                        @if($vehicle->incidents->isEmpty())
                            <p class="text-gray-500 text-sm">Chưa có chuyến đi nào.</p>
                        @else
                            <div class="space-y-3">
                                @foreach($vehicle->incidents as $incident)
                                <div class="border-l-4 {{ $incident->transactions->count() > 0 ? 'border-green-500' : 'border-gray-300' }} pl-4 py-2">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-800">
                                                @if($incident->patient)
                                                    {{ $incident->patient->name }}
                                                @else
                                                    Không có thông tin BN
                                                @endif
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ $incident->date->format('d/m/Y H:i') }}
                                                @if($incident->destination)
                                                    • {{ $incident->destination }}
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Bởi {{ $incident->dispatcher->name }}
                                            </p>
                                        </div>
                                        @if($incident->transactions->count() > 0)
                                        <div class="text-right">
                                            <p class="text-sm font-semibold {{ $incident->net_amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ number_format($incident->net_amount, 0, ',', '.') }}đ
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Recent Transactions --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Giao dịch gần đây</h3>
                        @if($vehicle->transactions->isEmpty())
                            <p class="text-gray-500 text-sm">Chưa có giao dịch nào.</p>
                        @else
                            <div class="space-y-3">
                                @foreach($vehicle->transactions as $transaction)
                                <div class="flex justify-between items-center border-b pb-2">
                                    <div class="flex-1">
                                        <p class="font-semibold {{ $transaction->type == 'thu' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $transaction->type_label }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $transaction->date->format('d/m/Y H:i') }} • {{ $transaction->method_label }}
                                        </p>
                                        @if($transaction->note)
                                        <p class="text-xs text-gray-600 mt-1">{{ $transaction->note }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold {{ $transaction->type == 'thu' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $transaction->type == 'thu' ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', '.') }}đ
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Ghi chú</h3>
                            @can('create vehicles')
                            <button onclick="document.getElementById('vehicleNoteForm').classList.toggle('hidden')" class="text-sm text-indigo-600 hover:text-indigo-900">
                                + Thêm ghi chú
                            </button>
                            @endcan
                        </div>

                        {{-- Add Note Form --}}
                        @can('create vehicles')
                        <div id="vehicleNoteForm" class="hidden mb-4">
                            <form action="{{ route('notes.store') }}" method="POST" class="space-y-3">
                                @csrf
                                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                                
                                <div>
                                    <select name="severity" required class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="info">Thông tin</option>
                                        <option value="warning">Cảnh báo</option>
                                        <option value="critical">Quan trọng</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <textarea name="note" rows="3" required placeholder="Nhập ghi chú..."
                                              class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>
                                
                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="document.getElementById('vehicleNoteForm').classList.add('hidden')" 
                                            class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                        Hủy
                                    </button>
                                    <button type="submit" class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                        Lưu
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endcan

                        @if($vehicle->notes->isEmpty())
                            <p class="text-gray-500 text-sm">Chưa có ghi chú nào.</p>
                        @else
                            <div class="space-y-2">
                                @foreach($vehicle->notes as $note)
                                <div class="p-3 rounded border-l-4 {{ $note->severity == 'critical' ? 'bg-red-50 border-red-500' : ($note->severity == 'warning' ? 'bg-yellow-50 border-yellow-500' : 'bg-gray-50 border-blue-500') }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="text-sm">{{ $note->note }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $note->user->name }} • {{ $note->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                        @can('delete vehicles')
                                        <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-900 ml-2" 
                                                    onclick="return confirm('Xóa ghi chú này?')">
                                                Xóa
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
