<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            @if($earnings)
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="w-full">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            💰 Thu nhập của tôi
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Danh sách các khoản tiền công đã nhận
                        </p>
                    </header>

                    <div class="mt-6">
                        @if($earnings->count() > 0)
                            {{-- Summary Stats --}}
                            @php
                                $totalEarnings = Transaction::where('staff_id', $staff->id)
                                    ->where('type', 'chi')
                                    ->sum('amount');
                                $thisMonthEarnings = Transaction::where('staff_id', $staff->id)
                                    ->where('type', 'chi')
                                    ->whereYear('date', date('Y'))
                                    ->whereMonth('date', date('m'))
                                    ->sum('amount');
                            @endphp
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <div class="text-sm text-gray-600">Tổng thu nhập</div>
                                    <div class="text-2xl font-bold text-blue-600">{{ number_format($totalEarnings, 0, ',', '.') }}đ</div>
                                </div>
                                <div class="bg-green-50 rounded-lg p-4">
                                    <div class="text-sm text-gray-600">Thu nhập tháng này</div>
                                    <div class="text-2xl font-bold text-green-600">{{ number_format($thisMonthEarnings, 0, ',', '.') }}đ</div>
                                </div>
                            </div>

                            {{-- Earnings Table --}}
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chuyến đi</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bệnh nhân</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Số tiền</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($earnings as $earning)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                {{ $earning->date ? $earning->date->format('d/m/Y') : 'N/A' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                @if($earning->incident)
                                                    <span class="text-blue-600">#{{ $earning->incident->id }}</span>
                                                    @if($earning->incident->vehicle)
                                                        - {{ $earning->incident->vehicle->license_plate }}
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                @if($earning->incident && $earning->incident->patient)
                                                    {{ $earning->incident->patient->name }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                {{ $earning->category }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-semibold text-green-600">
                                                {{ number_format($earning->amount, 0, ',', '.') }}đ
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ $earning->description ?: '-' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                {{ $earnings->links() }}
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">Chưa có dữ liệu thu nhập</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
