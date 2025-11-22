<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                💰 Thống kê thu nhập nhân viên: {{ $staff->full_name }}
            </h2>
            <a href="{{ route('staff.show', $staff) }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                ← Quay lại
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">💼 Lương cơ bản/tháng</p>
                        <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['base_salary'], 0, ',', '.') }}đ</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Tổng thu nhập</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_earnings'], 0, ',', '.') }}đ</p>
                        <p class="text-xs text-gray-400 mt-1">
                            Lương CB: {{ number_format($stats['base_salary_total'], 0, ',', '.') }}đ
                            + Tiền công: {{ number_format($stats['wage_earnings_total'], 0, ',', '.') }}đ
                        </p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Thu nhập tháng {{ now()->format('m/Y') }}</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['month_total_earnings'], 0, ',', '.') }}đ</p>
                        <p class="text-xs text-gray-400 mt-1">
                            CB: {{ number_format($stats['month_base_salary'], 0, ',', '.') }}đ
                            + TC: {{ number_format($stats['month_wage_earnings'], 0, ',', '.') }}đ
                            @if($stats['month_adjustments'] != 0)
                                {{ $stats['month_adjustments'] >= 0 ? '+' : '' }} ĐC: {{ number_format($stats['month_adjustments'], 0, ',', '.') }}đ
                            @endif
                            @if($stats['month_salary_advances'] > 0)
                                - Ứng: {{ number_format($stats['month_salary_advances'], 0, ',', '.') }}đ
                            @endif
                        </p>
                        <p class="text-xs text-green-600 mt-1">
                            📅 Ngày nhận: {{ now()->addMonth()->day(15)->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Tổng số chuyến</p>
                        <p class="text-2xl font-bold text-indigo-600">{{ $stats['total_trips'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            TB: {{ $stats['total_trips'] > 0 ? number_format($stats['wage_earnings_total'] / $stats['total_trips'], 0, ',', '.') : 0 }}đ/chuyến
                        </p>
                    </div>
                </div>
            </div>

            {{-- Adjustments & Debts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Add Adjustment Form --}}
                @can('manage settings')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">➕ Điều chỉnh thu nhập</h3>
                        
                        <form method="POST" action="{{ route('staff.adjustments.store', $staff) }}" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Loại điều chỉnh <span class="text-red-500">*</span></label>
                                <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Chọn --</option>
                                    <option value="addition" {{ old('type') == 'addition' ? 'selected' : '' }}>➕ Cộng tiền</option>
                                    <option value="deduction" {{ old('type') == 'deduction' ? 'selected' : '' }}>➖ Trừ tiền</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">Số tiền <span class="text-red-500">*</span></label>
                                <input type="number" id="amount" name="amount" value="{{ old('amount') }}" required step="1000" min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="VD: 500000">
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="month" class="block text-sm font-medium text-gray-700">Áp dụng cho tháng <span class="text-red-500">*</span></label>
                                <input type="month" id="month" name="month" value="{{ old('month', now()->format('Y-m')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('month')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">Hạng mục <span class="text-red-500">*</span></label>
                                <input type="text" id="category" name="category" value="{{ old('category') }}" required
                                    list="category-list"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="VD: Thưởng, Phạt, Tạm ứng...">
                                <datalist id="category-list">
                                    <option value="Thưởng">
                                    <option value="Phạt">
                                    <option value="Tạm ứng">
                                    <option value="Khấu trừ BHXH">
                                    <option value="Ứng lương">
                                    <option value="Bồi thường">
                                </datalist>
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-data="incidentSearch()">
                                <label for="incident_search" class="block text-sm font-medium text-gray-700">
                                    Chuyến đi liên quan (tùy chọn)
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           id="incident_search" 
                                           x-model="searchTerm"
                                           @input.debounce.300ms="search()"
                                           @focus="showResults = true"
                                           autocomplete="off"
                                           placeholder="Gõ để tìm: ID, tên bệnh nhân, biển số xe..."
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <input type="hidden" id="incident_id" name="incident_id" x-model="selectedId">
                                    
                                    <!-- Results dropdown -->
                                    <div x-show="showResults && results.length > 0" 
                                         @click.away="showResults = false"
                                         class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                        <template x-for="incident in results" :key="incident.id">
                                            <div @click="selectIncident(incident)" 
                                                 class="cursor-pointer select-none relative py-2 px-3 hover:bg-indigo-50">
                                                <div class="font-semibold text-gray-900">
                                                    #<span x-text="incident.id"></span> - <span x-text="incident.patient_name"></span>
                                                </div>
                                                <div class="text-sm text-gray-600">
                                                    🚗 <span x-text="incident.vehicle_plate"></span> • 📅 <span x-text="incident.date"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    💡 Gõ để tìm kiếm theo: Mã chuyến, tên bệnh nhân, biển số xe, ngày. Nếu chọn chuyến đi: Tiền sẽ trừ từ doanh thu chuyến đi. Nếu chuyến đi không đủ, phần còn lại lấy từ quỹ công ty.
                                </p>
                                @error('incident_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="reason" class="block text-sm font-medium text-gray-700">Lý do <span class="text-red-500">*</span></label>
                                <textarea id="reason" name="reason" rows="3" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Mô tả chi tiết lý do điều chỉnh...">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-md mb-3">
                                <p class="text-xs text-blue-800">
                                    ℹ️ <strong>Ghi nhận giao dịch:</strong><br>
                                    • Cộng tiền: Tạo transaction CHI (công ty chi trả cho nhân viên)<br>
                                    • Trừ tiền: Tạo transaction THU (công ty thu lại từ nhân viên)<br>
                                    • Tất cả đều được ghi trong Quản lý Giao dịch với tag "Điều chỉnh"
                                </p>
                            </div>

                            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                <p class="text-xs text-yellow-800">
                                    ⚠️ <strong>Lưu ý về nợ:</strong> Nếu trừ tiền mà số dư không đủ, hệ thống sẽ tự động tạo khoản nợ. 
                                    Khoản nợ sẽ được trừ tự động khi nhân viên có thu nhập mới.
                                </p>
                            </div>

                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Xác nhận điều chỉnh
                            </button>
                        </form>
                    </div>
                </div>
                @endcan

                {{-- Salary Advance Form --}}
                @can('manage settings')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">💰 Ứng lương</h3>
                        
                        <form method="POST" action="{{ route('staff.salary-advance.store', $staff) }}" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label for="advance_amount" class="block text-sm font-medium text-gray-700">Số tiền ứng <span class="text-red-500">*</span></label>
                                <input type="number" id="advance_amount" name="amount" value="{{ old('amount') }}" required step="1000" min="1000"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="VD: 1000000">
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="advance_note" class="block text-sm font-medium text-gray-700">Ghi chú</label>
                                <textarea id="advance_note" name="note" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Lý do ứng lương...">{{ old('note') }}</textarea>
                                @error('note')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-md">
                                <p class="text-xs text-blue-800">
                                    ℹ️ <strong>Cách hoạt động:</strong><br>
                                    • Hệ thống tự động tính số dư thu nhập hiện có<br>
                                    • Nếu đủ: Trừ từ thu nhập tháng này<br>
                                    • Nếu không đủ: Công ty bù phần thiếu (tạo nợ)<br>
                                    • Nợ sẽ tự động khấu trừ khi có thu nhập mới
                                </p>
                            </div>

                            <div class="p-3 bg-green-50 border border-green-200 rounded-md">
                                <p class="text-xs text-green-800">
                                    Thu nhập khả dụng tháng này: 
                                    <strong>{{ number_format($stats['month_total_earnings'], 0, ',', '.') }}đ</strong>
                                </p>
                            </div>

                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                💰 Ứng lương
                            </button>
                        </form>
                    </div>
                </div>
                @endcan

                {{-- Pending Debts --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">💳 Khoản nợ chưa thanh toán</h3>
                        
                        @if($totalDebt > 0)
                            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                                <p class="text-sm text-red-800 font-semibold">
                                    Tổng nợ: {{ number_format($totalDebt, 0, ',', '.') }}đ
                                </p>
                            </div>

                            <div class="space-y-3">
                                @foreach($pendingDebts as $debt)
                                    <div class="p-3 border border-gray-200 rounded-md {{ $debt instanceof \App\Models\SalaryAdvance ? 'bg-orange-50' : '' }}">
                                        <div class="flex justify-between items-start mb-2">
                                            @if($debt instanceof \App\Models\SalaryAdvance)
                                                <span class="text-sm font-semibold text-orange-600">💰 Ứng lương</span>
                                            @else
                                                <span class="text-sm font-semibold text-red-600">{{ $debt->category }}</span>
                                            @endif
                                            <span class="text-sm font-bold text-red-600">{{ number_format($debt->debt_amount, 0, ',', '.') }}đ</span>
                                        </div>
                                        
                                        @if($debt instanceof \App\Models\SalaryAdvance)
                                            <p class="text-xs text-gray-600 mb-1">{{ $debt->note ?? 'Ứng lương' }}</p>
                                            <div class="flex justify-between items-center text-xs text-gray-500">
                                                <span>Ngày {{ $debt->date->format('d/m/Y') }}</span>
                                                <span>Duyệt: {{ $debt->approvedBy->name ?? '-' }}</span>
                                            </div>
                                        @else
                                            <p class="text-xs text-gray-600 mb-1">{{ $debt->reason }}</p>
                                            <div class="flex justify-between items-center text-xs text-gray-500">
                                                <span>Tháng {{ $debt->month->format('m/Y') }}</span>
                                                <span>Bởi: {{ $debt->creator->name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500">✅ Không có khoản nợ</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Current Month Adjustments --}}
            @if($adjustments->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">📋 Điều chỉnh tháng {{ now()->format('m/Y') }}</h3>
                    
                    <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-md">
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <p class="text-xs text-gray-500">Cộng tiền</p>
                                <p class="text-lg font-bold text-green-600">+{{ number_format($adjustmentAdditions, 0, ',', '.') }}đ</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Trừ tiền</p>
                                <p class="text-lg font-bold text-red-600">-{{ number_format($adjustmentDeductions, 0, ',', '.') }}đ</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Tổng điều chỉnh</p>
                                <p class="text-lg font-bold {{ $adjustmentNet >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $adjustmentNet >= 0 ? '+' : '' }}{{ number_format($adjustmentNet, 0, ',', '.') }}đ
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hạng mục</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lý do</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nguồn tiền</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Số tiền</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người tạo</th>
                                    @can('manage settings')
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($adjustments as $adj)
                                    <tr class="hover:bg-gray-50" x-data="{ editing: false }">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $adj->type == 'addition' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $adj->type_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">{{ $adj->category }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            <div x-show="!editing">
                                                {{ Str::limit($adj->reason, 50) }}
                                            </div>
                                            <div x-show="editing" x-cloak>
                                                <textarea x-model="reason" class="w-full px-2 py-1 border rounded text-sm" rows="2">{{ $adj->reason }}</textarea>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-xs">
                                            @if($adj->incident_id)
                                                <a href="{{ route('incidents.show', $adj->incident_id) }}" class="text-blue-600 hover:text-blue-900">
                                                    🚑 Chuyến #{{ $adj->incident_id }}
                                                </a>
                                                @if($adj->type == 'addition')
                                                    @if($adj->from_incident_amount > 0)
                                                        <br><span class="text-green-600">↳ {{ number_format($adj->from_incident_amount, 0, ',', '.') }}đ</span>
                                                    @endif
                                                    @if($adj->from_company_amount > 0)
                                                        <br><span class="text-orange-600">↳ Công ty: {{ number_format($adj->from_company_amount, 0, ',', '.') }}đ</span>
                                                    @endif
                                                @endif
                                            @else
                                                @if($adj->type == 'addition')
                                                    <span class="text-orange-600">🏢 Quỹ công ty</span>
                                                @else
                                                    <span class="text-blue-600">💰 Thu về công ty</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right">
                                            <div x-show="!editing" class="font-semibold {{ $adj->type == 'addition' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $adj->type == 'addition' ? '+' : '-' }}{{ number_format($adj->amount, 0, ',', '.') }}đ
                                            </div>
                                            <div x-show="editing" x-cloak>
                                                <input type="number" x-model="amount" step="1000" class="w-32 px-2 py-1 border rounded text-sm text-right" value="{{ $adj->amount }}">
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $adj->status_color }}-100 text-{{ $adj->status_color }}-800">
                                                {{ $adj->status_label }}
                                            </span>
                                            @if($adj->debt_amount > 0)
                                                <p class="text-xs text-red-600 mt-1">Nợ: {{ number_format($adj->debt_amount, 0, ',', '.') }}đ</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $adj->creator->name }}
                                            <br>
                                            <span class="text-xs text-gray-400">{{ $adj->created_at->format('d/m/Y H:i') }}</span>
                                        </td>
                                        @can('manage settings')
                                        <td class="px-4 py-3 text-center">
                                            <div x-show="!editing" class="flex gap-2 justify-center">
                                                <button @click="editing = true; amount = {{ $adj->amount }}; reason = '{{ addslashes($adj->reason) }}'" 
                                                        class="text-blue-600 hover:text-blue-900" title="Sửa">
                                                    ✏️
                                                </button>
                                                <form action="{{ route('adjustment.destroy', $adj) }}" method="POST" 
                                                      onsubmit="return confirm('Xác nhận xóa điều chỉnh này?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Xóa">
                                                        🗑️
                                                    </button>
                                                </form>
                                            </div>
                                            <div x-show="editing" x-cloak class="flex gap-2 justify-center">
                                                <form :action="'{{ route('adjustment.update', ':id') }}'.replace(':id', {{ $adj->id }})" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="amount" :value="amount">
                                                    <input type="hidden" name="reason" :value="reason">
                                                    <button type="submit" class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                        💾 Lưu
                                                    </button>
                                                </form>
                                                <button @click="editing = false" class="px-2 py-1 bg-gray-400 text-white text-xs rounded hover:bg-gray-500">
                                                    ❌ Hủy
                                                </button>
                                            </div>
                                        </td>
                                        @endcan
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Salary Advances History --}}
            @php
                $salaryAdvances = $staff->salaryAdvances()
                    ->with('approvedBy')
                    ->orderBy('date', 'desc')
                    ->take(10)
                    ->get();
                $totalAdvances = $salaryAdvances->sum('amount');
                $totalDebtFromAdvances = $salaryAdvances->sum('debt_amount');
            @endphp

            @if($salaryAdvances->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">💰 Lịch sử ứng lương (10 gần nhất)</h3>
                    
                    <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-md">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <p class="text-xs text-gray-500">Tổng đã ứng</p>
                                <p class="text-lg font-bold text-blue-600">{{ number_format($totalAdvances, 0, ',', '.') }}đ</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Nợ ứng lương</p>
                                <p class="text-lg font-bold text-red-600">{{ number_format($totalDebtFromAdvances, 0, ',', '.') }}đ</p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Số tiền ứng</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Từ thu nhập</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Công ty bù</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nợ còn lại</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ghi chú</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người duyệt</th>
                                    @can('manage settings')
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($salaryAdvances as $advance)
                                    <tr class="hover:bg-gray-50" x-data="{ editing: false }">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            {{ $advance->date->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                            <span x-show="!editing" class="font-bold text-blue-600">
                                                {{ number_format($advance->amount, 0, ',', '.') }}đ
                                            </span>
                                            <input x-show="editing" type="number" x-model="amount_{{ $advance->id }}" 
                                                   class="w-32 px-2 py-1 text-sm border rounded" 
                                                   value="{{ $advance->amount }}" step="1000">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-green-600">
                                            {{ number_format($advance->from_earnings, 0, ',', '.') }}đ
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-orange-600">
                                            {{ number_format($advance->from_company, 0, ',', '.') }}đ
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                            @if($advance->debt_amount > 0)
                                                <span class="font-bold text-red-600">{{ number_format($advance->debt_amount, 0, ',', '.') }}đ</span>
                                            @else
                                                <span class="text-green-600">✓ Đã trả</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            <span x-show="!editing">{{ $advance->note ? Str::limit($advance->note, 40) : '-' }}</span>
                                            <input x-show="editing" type="text" x-model="note_{{ $advance->id }}" 
                                                   class="w-full px-2 py-1 text-sm border rounded" 
                                                   value="{{ $advance->note }}">
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            {{ $advance->approvedBy->name ?? '-' }}
                                        </td>
                                        @can('manage settings')
                                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                            <div x-show="!editing" class="flex justify-center gap-2">
                                                <button @click="editing = true; amount_{{ $advance->id }} = {{ $advance->amount }}; note_{{ $advance->id }} = '{{ $advance->note }}'" 
                                                        class="text-blue-600 hover:text-blue-900" title="Sửa">
                                                    ✏️
                                                </button>
                                                <form method="POST" action="{{ route('salary-advance.destroy', $advance) }}" 
                                                      onsubmit="return confirm('Bạn có chắc muốn hủy khoản ứng lương này?');" 
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hủy">
                                                        🗑️
                                                    </button>
                                                </form>
                                            </div>
                                            <div x-show="editing" class="flex justify-center gap-2">
                                                <form method="POST" action="{{ route('salary-advance.update', $advance) }}" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="amount" :value="amount_{{ $advance->id }}">
                                                    <input type="hidden" name="note" :value="note_{{ $advance->id }}">
                                                    <button type="submit" class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                        Lưu
                                                    </button>
                                                </form>
                                                <button @click="editing = false" class="px-2 py-1 bg-gray-300 text-gray-700 text-xs rounded hover:bg-gray-400">
                                                    Hủy
                                                </button>
                                            </div>
                                        </td>
                                        @endcan
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Filter --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($staff->base_salary)
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                            <p class="text-sm text-blue-800">
                                ℹ️ <strong>Lương cơ bản:</strong> {{ number_format($staff->base_salary, 0, ',', '.') }}đ/tháng
                                @if($stats['months_worked'] > 0)
                                    × {{ $stats['months_worked'] }} tháng
                                    @if(request('from_date') || request('to_date'))
                                        (theo bộ lọc)
                                    @endif
                                    = {{ number_format($stats['base_salary_total'], 0, ',', '.') }}đ
                                @endif
                            </p>
                            <p class="text-xs text-blue-600 mt-1">
                                💡 Lương cơ bản được tính tự động vào tổng thu nhập theo số tháng làm việc
                            </p>
                        </div>
                    @endif
                    
                    <form method="GET" action="{{ route('staff.earnings', $staff) }}" class="flex gap-4 items-end">
                        <div class="flex-1">
                            <label for="from_date" class="block text-sm font-medium text-gray-700">Từ ngày</label>
                            <input type="date" id="from_date" name="from_date" value="{{ request('from_date') }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="flex-1">
                            <label for="to_date" class="block text-sm font-medium text-gray-700">Đến ngày</label>
                            <input type="date" id="to_date" name="to_date" value="{{ request('to_date') }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Lọc
                        </button>
                        @if(request('from_date') || request('to_date'))
                            <a href="{{ route('staff.earnings', $staff) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Xóa lọc
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Earnings List --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-2">Lịch sử tiền công từ chuyến đi</h3>
                    <p class="text-sm text-gray-500 mb-4">📋 Danh sách này chỉ hiển thị tiền công từ các chuyến đi, không bao gồm lương cơ bản hàng tháng</p>
                    
                    @if($earnings->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chuyến đi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Xe</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vai trò</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tiền công</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($earnings as $transaction)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                {{ $transaction->date->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                @if($transaction->incident)
                                                    <a href="{{ route('incidents.show', $transaction->incident) }}" class="text-blue-600 hover:text-blue-900">
                                                        #{{ $transaction->incident->id }}
                                                        @if($transaction->incident->patient)
                                                            - {{ $transaction->incident->patient->name }}
                                                        @endif
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($transaction->vehicle)
                                                    <a href="{{ route('vehicles.show', $transaction->vehicle) }}" class="text-blue-600 hover:text-blue-900">
                                                        {{ $transaction->vehicle->license_plate }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($transaction->note)
                                                    @if(str_contains($transaction->note, 'lái xe'))
                                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Lái xe</span>
                                                    @elseif(str_contains($transaction->note, 'y tế'))
                                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Y tế</span>
                                                    @else
                                                        -
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-green-600">
                                                {{ number_format($transaction->amount, 0, ',', '.') }}đ
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                @if($transaction->incident)
                                                    <a href="{{ route('incidents.show', $transaction->incident) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        Xem chi tiết
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-sm font-semibold text-right">Tổng cộng:</td>
                                        <td class="px-6 py-4 text-sm font-bold text-right text-green-600">
                                            {{ number_format($earnings->sum('amount'), 0, ',', '.') }}đ
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $earnings->links() }}
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-8">Chưa có dữ liệu thu nhập</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function incidentSearch() {
            return {
                searchTerm: '',
                selectedId: '',
                results: [],
                showResults: false,
                
                async search() {
                    if (this.searchTerm.length < 1) {
                        this.results = [];
                        return;
                    }
                    
                    try {
                        const response = await fetch(`{{ route('incidents.search') }}?q=${encodeURIComponent(this.searchTerm)}`);
                        const data = await response.json();
                        this.results = data.results;
                        this.showResults = true;
                    } catch (error) {
                        console.error('Search error:', error);
                    }
                },
                
                selectIncident(incident) {
                    this.searchTerm = `#${incident.id} - ${incident.patient_name}`;
                    this.selectedId = incident.id;
                    this.showResults = false;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
