<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                💰 Bảng lương nhân viên
            </h2>
            <a href="{{ route('staff.index') }}" class="text-indigo-600 hover:text-indigo-900">
                ← Quay lại danh sách
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Year Selector --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Chọn năm</h3>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('staff.payroll', ['year' => $year - 1]) }}" 
                               class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                                ← {{ $year - 1 }}
                            </a>
                            <span class="text-2xl font-bold text-indigo-600">{{ $year }}</span>
                            <a href="{{ route('staff.payroll', ['year' => $year + 1]) }}" 
                               class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                                {{ $year + 1 }} →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Calendar Grid --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @php
                            $months = [
                                1 => 'Tháng 1', 2 => 'Tháng 2', 3 => 'Tháng 3', 4 => 'Tháng 4',
                                5 => 'Tháng 5', 6 => 'Tháng 6', 7 => 'Tháng 7', 8 => 'Tháng 8',
                                9 => 'Tháng 9', 10 => 'Tháng 10', 11 => 'Tháng 11', 12 => 'Tháng 12'
                            ];
                        @endphp

                        @foreach($months as $monthNum => $monthName)
                            @php
                                $isCurrentMonth = ($monthNum == $currentMonth && $year == $currentYear);
                                $isPastMonth = ($year < $currentYear) || ($year == $currentYear && $monthNum < $currentMonth);
                                $isFutureMonth = ($year > $currentYear) || ($year == $currentYear && $monthNum > $currentMonth);
                            @endphp

                            <a href="{{ route('staff.payroll.detail', ['year' => $year, 'month' => $monthNum]) }}" 
                               class="block p-6 border-2 rounded-lg transition-all duration-200 hover:shadow-lg
                                      {{ $isCurrentMonth ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200' : '' }}
                                      {{ $isPastMonth && !$isCurrentMonth ? 'border-gray-300 bg-gray-50 hover:border-gray-400' : '' }}
                                      {{ $isFutureMonth ? 'border-gray-200 bg-white hover:border-indigo-300' : '' }}
                                      {{ !$isCurrentMonth && !$isPastMonth && !$isFutureMonth ? 'border-gray-300 hover:border-indigo-400' : '' }}">
                                
                                <div class="text-center">
                                    {{-- Month Icon --}}
                                    <div class="text-4xl mb-2">
                                        @if($isCurrentMonth)
                                            ⭐
                                        @elseif($isPastMonth)
                                            📋
                                        @else
                                            📅
                                        @endif
                                    </div>

                                    {{-- Month Name --}}
                                    <h3 class="text-lg font-semibold mb-1
                                        {{ $isCurrentMonth ? 'text-indigo-700' : 'text-gray-800' }}">
                                        {{ $monthName }}
                                    </h3>

                                    {{-- Year --}}
                                    <p class="text-sm text-gray-500">{{ $year }}</p>

                                    {{-- Status Badge --}}
                                    @if($isCurrentMonth)
                                        <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold rounded-full bg-indigo-600 text-white">
                                            Tháng hiện tại
                                        </span>
                                    @elseif($isPastMonth)
                                        <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold rounded-full bg-gray-600 text-white">
                                            Đã qua
                                        </span>
                                    @else
                                        <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold rounded-full bg-green-600 text-white">
                                            Sắp tới
                                        </span>
                                    @endif

                                    {{-- Action --}}
                                    <div class="mt-3 text-sm font-medium
                                        {{ $isCurrentMonth ? 'text-indigo-600' : 'text-gray-600' }}">
                                        Xem bảng lương →
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    ℹ️ <strong>Hướng dẫn:</strong><br>
                    • Click vào tháng để xem chi tiết bảng lương của nhân viên có lương cơ bản<br>
                    • Tháng hiện tại được highlight màu xanh dương với icon ⭐<br>
                    • Bảng lương bao gồm: Lương cơ bản, phụ cấp, thưởng, phạt, tiền công từ chuyến đi, ứng lương
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
