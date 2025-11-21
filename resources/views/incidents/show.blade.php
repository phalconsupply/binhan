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
                            <h3 class="text-lg font-semibold mb-4">Thông tin chuyến đi</h3>
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
                                    <p class="text-sm text-gray-500">Điểm đến</p>
                                    <p class="text-base">{{ $incident->destination ?? '-' }}</p>
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
                            </div>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Ghi chú</h3>
                            @if($incident->notes->isEmpty())
                                <p class="text-gray-500 text-sm">Chưa có ghi chú nào.</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($incident->notes as $note)
                                    <div class="p-3 bg-gray-50 rounded border-l-4 border-{{ $note->severity_color }}-500">
                                        <p class="text-sm">{{ $note->note }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $note->user->name }} • {{ $note->created_at->format('d/m/Y H:i') }}
                                        </p>
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
