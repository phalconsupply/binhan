<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chi tiết chuyến đi #{{ $incident->id }}
            </h2>
            <div class="space-x-2">
                @can('edit incidents')
                <a href="{{ route('incidents.edit', $incident) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Sửa
                </a>
                @endcan
                <a href="{{ route('incidents.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
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
                    {{-- Incident Details --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="mb-4 pb-4 border-b flex items-center justify-between">
                                <h3 class="text-lg font-semibold">Thông tin chuyến đi</h3>
                                <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-semibold rounded-full">
                                    Mã chuyến đi: #{{ $incident->id }}
                                </span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Ngày giờ</p>
                                    <p class="text-base font-semibold">{{ $incident->date->format('d/m/Y H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Xe</p>
                                    <a href="{{ route('vehicles.show', $incident->vehicle) }}" class="text-base font-semibold text-blue-600 hover:text-blue-900">
                                        {{ $incident->vehicle->license_plate }}
                                    </a>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Điều phối bởi</p>
                                    <p class="text-base">{{ $incident->dispatcher->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Lái xe</p>
                                    <p class="text-base">
                                        @if($incident->drivers->count() > 0)
                                            @foreach($incident->drivers as $driver)
                                                <a href="{{ route('staff.show', $driver) }}" class="text-blue-600 hover:text-blue-900">
                                                    {{ $driver->employee_code }} - {{ $driver->full_name }}
                                                </a>
                                                @if(!$loop->last), @endif
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Nhân viên y tế</p>
                                    <p class="text-base">
                                        @if($incident->medicalStaff->count() > 0)
                                            @foreach($incident->medicalStaff as $staff)
                                                <a href="{{ route('staff.show', $staff) }}" class="text-blue-600 hover:text-blue-900">
                                                    {{ $staff->employee_code }} - {{ $staff->full_name }}
                                                </a>
                                                @if(!$loop->last), @endif
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Nơi đi</p>
                                    <p class="text-base">{{ $incident->fromLocation->name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Nơi đến</p>
                                    <p class="text-base">{{ $incident->toLocation->name ?? '-' }}</p>
                                </div>
                                @if($incident->summary)
                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-500">Ghi chú</p>
                                    <p class="text-base">{{ $incident->summary }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Patient Info --}}
                    @if($incident->patient)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Thông tin bệnh nhân</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Họ tên</p>
                                    <p class="text-base font-semibold">{{ $incident->patient->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Số điện thoại</p>
                                    <p class="text-base">{{ $incident->patient->phone ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Năm sinh / Tuổi</p>
                                    <p class="text-base">
                                        @if($incident->patient->birth_year)
                                            {{ $incident->patient->birth_year }} ({{ $incident->patient->age }} tuổi)
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Giới tính</p>
                                    <p class="text-base">{{ $incident->patient->gender_label ?? '-' }}</p>
                                </div>
                                @if($incident->patient->address)
                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-500">Địa chỉ</p>
                                    <p class="text-base">{{ $incident->patient->address }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Additional Services --}}
                    @if($incident->additionalServices->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">🛎️ Dịch vụ kèm theo</h3>
                            <div class="space-y-2">
                                @foreach($incident->additionalServices as $service)
                                <div class="flex justify-between items-start p-3 bg-gray-50 rounded border border-gray-200">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-800">{{ $service->service_name }}</p>
                                        @if($service->note)
                                        <p class="text-xs text-gray-500 mt-1">{{ $service->note }}</p>
                                        @endif
                                        @if($service->additionalService)
                                        <p class="text-xs text-blue-600 mt-1">
                                            <span class="inline-flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Dịch vụ có sẵn
                                            </span>
                                        </p>
                                        @endif
                                    </div>
                                    <div class="text-right ml-4">
                                        <p class="text-lg font-bold text-green-600">
                                            {{ number_format($service->amount, 0, ',', '.') }}đ
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                                <div class="pt-2 border-t mt-3">
                                    <div class="flex justify-between items-center">
                                        <span class="font-semibold text-gray-700">Tổng dịch vụ:</span>
                                        <span class="text-xl font-bold text-green-600">
                                            {{ number_format($incident->additionalServices->sum('amount'), 0, ',', '.') }}đ
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Transactions --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Giao dịch</h3>
                                @can('create transactions')
                                <a href="{{ route('transactions.create', ['incident_id' => $incident->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    + Thêm giao dịch
                                </a>
                                @endcan
                            </div>
                            
                            @if($incident->transactions->isEmpty())
                                <p class="text-gray-500 text-sm">Chưa có giao dịch nào.</p>
                            @else
                                <div class="space-y-3">
                                    @foreach($incident->transactions as $transaction)
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
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Financial Summary --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Tổng hợp tài chính</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Thu:</span>
                                    <span class="font-semibold text-green-600">{{ number_format($totals['revenue'], 0, ',', '.') }}đ</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Chi:</span>
                                    <span class="font-semibold text-red-600">{{ number_format($totals['expense'], 0, ',', '.') }}đ</span>
                                </div>
                                <div class="flex justify-between pt-3 border-t">
                                    <span class="font-semibold">Lợi nhuận:</span>
                                    <span class="text-lg font-bold {{ $totals['net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($totals['net'], 0, ',', '.') }}đ
                                    </span>
                                </div>
                                @if($totals['has_owner'] && $totals['management_fee'] > 0)
                                <div class="flex justify-between pt-2 border-t border-dashed">
                                    <span class="text-sm text-gray-600">Phí quản lý (15%):</span>
                                    <span class="font-semibold text-orange-600">{{ number_format($totals['management_fee'], 0, ',', '.') }}đ</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t">
                                    <span class="font-semibold text-blue-600">Lợi nhuận sau phí:</span>
                                    <span class="text-lg font-bold {{ $totals['profit_after_fee'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                        {{ number_format($totals['profit_after_fee'], 0, ',', '.') }}đ
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Ghi chú</h3>
                                @can('create incidents')
                                <button onclick="document.getElementById('noteForm').classList.toggle('hidden')" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    + Thêm ghi chú
                                </button>
                                @endcan
                            </div>

                            {{-- Add Note Form --}}
                            @can('create incidents')
                            <div id="noteForm" class="hidden mb-4">
                                <form action="{{ route('notes.store') }}" method="POST" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="incident_id" value="{{ $incident->id }}">
                                    
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
                                        <button type="button" onclick="document.getElementById('noteForm').classList.add('hidden')" 
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

                            @if($incident->notes->isEmpty())
                                <p class="text-gray-500 text-sm">Chưa có ghi chú nào.</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($incident->notes as $note)
                                    <div class="p-3 rounded border-l-4 {{ $note->severity == 'critical' ? 'bg-red-50 border-red-500' : ($note->severity == 'warning' ? 'bg-yellow-50 border-yellow-500' : 'bg-gray-50 border-blue-500') }}">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="text-sm">{{ $note->note }}</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $note->user->name }} • {{ $note->created_at->format('d/m/Y H:i') }}
                                                </p>
                                            </div>
                                            @can('delete incidents')
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
    </div>
</x-app-layout>
