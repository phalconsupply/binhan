<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bảng điều khiển
            </h2>
            <div class="text-sm text-gray-600">
                Xin chào, <span class="font-semibold">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                {{-- Total Vehicles --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Tổng số xe</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_vehicles'] }}</p>
                                <p class="text-xs text-green-600">{{ $stats['active_vehicles'] }} đang hoạt động</p>
                            </div>
                            <div class="text-blue-500">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Today Incidents --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Chuyến đi hôm nay</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['today_incidents'] }}</p>
                            </div>
                            <div class="text-green-500">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Today Revenue/Expense --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Thu/Chi hôm nay</p>
                                <p class="text-lg font-bold text-green-600">+{{ number_format($stats['today_revenue'], 0, ',', '.') }}đ</p>
                                <p class="text-lg font-bold text-red-600">-{{ number_format($stats['today_expense'], 0, ',', '.') }}đ</p>
                                <p class="text-sm font-semibold {{ $stats['today_net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    = {{ number_format($stats['today_net'], 0, ',', '.') }}đ
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Month Revenue/Expense --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Thu/Chi tháng này</p>
                                <p class="text-lg font-bold text-green-600">+{{ number_format($stats['month_revenue'], 0, ',', '.') }}đ</p>
                                <p class="text-lg font-bold text-red-600">-{{ number_format($stats['month_expense'], 0, ',', '.') }}đ</p>
                                <p class="text-sm font-semibold {{ $stats['month_net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    = {{ number_format($stats['month_net'], 0, ',', '.') }}đ
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Quick Entry Form --}}
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Ghi nhận nhanh</h3>
                            
                            <form method="POST" action="{{ route('dashboard.quick-entry') }}" class="space-y-4">
                                @csrf

                                {{-- Vehicle Select --}}
                                <div>
                                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Biển số xe <span class="text-red-500">*</span></label>
                                    <select id="vehicle_id" name="vehicle_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">-- Chọn xe --</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->license_plate }} @if($vehicle->driver_name) - {{ $vehicle->driver_name }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Date & Time --}}
                                <div>
                                    <label for="date" class="block text-sm font-medium text-gray-700">Ngày giờ <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" id="date" name="date" value="{{ old('date', now()->format('Y-m-d\TH:i')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Patient Info --}}
                                <div class="border-t pt-3">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Thông tin bệnh nhân (tùy chọn)</h4>
                                    
                                    <div class="space-y-2">
                                        <input type="text" name="patient_name" placeholder="Tên bệnh nhân" value="{{ old('patient_name') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        
                                        <input type="text" name="patient_phone" placeholder="Số điện thoại" value="{{ old('patient_phone') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="number" name="patient_birth_year" placeholder="Năm sinh" min="1900" max="{{ date('Y') }}" value="{{ old('patient_birth_year') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            
                                            <select name="patient_gender" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <option value="">Giới tính</option>
                                                <option value="male" {{ old('patient_gender') == 'male' ? 'selected' : '' }}>Nam</option>
                                                <option value="female" {{ old('patient_gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                                                <option value="other" {{ old('patient_gender') == 'other' ? 'selected' : '' }}>Khác</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Destination --}}
                                <div>
                                    <label for="destination" class="block text-sm font-medium text-gray-700">Điểm đến</label>
                                    <input type="text" id="destination" name="destination" value="{{ old('destination') }}" placeholder="Bệnh viện, phòng khám..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                {{-- Amount --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label for="amount_thu" class="block text-sm font-medium text-green-600">Thu (đ)</label>
                                        <input type="number" id="amount_thu" name="amount_thu" value="{{ old('amount_thu') }}" min="0" step="1000" placeholder="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    </div>
                                    <div>
                                        <label for="amount_chi" class="block text-sm font-medium text-red-600">Chi (đ)</label>
                                        <input type="number" id="amount_chi" name="amount_chi" value="{{ old('amount_chi') }}" min="0" step="1000" placeholder="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                    </div>
                                </div>

                                {{-- Payment Method --}}
                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Hình thức</label>
                                    <select id="payment_method" name="payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="cash" {{ old('payment_method', 'cash') == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                                        <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Chuyển khoản</option>
                                        <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                </div>

                                {{-- Note --}}
                                <div>
                                    <label for="note" class="block text-sm font-medium text-gray-700">Ghi chú</label>
                                    <textarea id="note" name="note" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('note') }}</textarea>
                                </div>

                                {{-- Submit --}}
                                <div>
                                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Ghi nhận
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Recent Activities --}}
                <div class="lg:col-span-2">
                    {{-- Today's Incidents --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Chuyến đi hôm nay ({{ $todayIncidents->count() }})</h3>
                            
                            @if($todayIncidents->isEmpty())
                                <p class="text-gray-500 text-sm">Chưa có chuyến đi nào hôm nay.</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($todayIncidents as $incident)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-semibold text-blue-600">{{ $incident->vehicle->license_plate }}</span>
                                                    @if($incident->patient)
                                                        <span class="text-gray-600">→ {{ $incident->patient->name }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $incident->date->format('H:i') }}
                                                    @if($incident->destination)
                                                        • {{ $incident->destination }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                @if($incident->transactions->count() > 0)
                                                    <div class="text-sm font-semibold text-green-600">
                                                        +{{ number_format($incident->total_revenue, 0, ',', '.') }}đ
                                                    </div>
                                                    @if($incident->total_expense > 0)
                                                        <div class="text-xs text-red-600">
                                                            -{{ number_format($incident->total_expense, 0, ',', '.') }}đ
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Recent Incidents --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Chuyến đi gần đây</h3>
                            
                            @if($recentIncidents->isEmpty())
                                <p class="text-gray-500 text-sm">Chưa có chuyến đi nào.</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($recentIncidents as $incident)
                                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-semibold text-blue-600">{{ $incident->vehicle->license_plate }}</span>
                                                    @if($incident->patient)
                                                        <span class="text-gray-600">→ {{ $incident->patient->name }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $incident->date->format('d/m/Y H:i') }}
                                                    @if($incident->destination)
                                                        • {{ $incident->destination }}
                                                    @endif
                                                    • Bởi {{ $incident->dispatcher->name }}
                                                </div>
                                            </div>
                                            <div class="text-right text-sm">
                                                @if($incident->transactions->count() > 0)
                                                    <span class="font-semibold {{ $incident->net_amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ number_format($incident->net_amount, 0, ',', '.') }}đ
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
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
