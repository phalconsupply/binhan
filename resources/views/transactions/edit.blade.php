<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sửa giao dịch #{{ $transaction->id }}
            </h2>
            <a href="{{ route('transactions.index') }}" class="text-indigo-600 hover:text-indigo-900">
                ← Quay lại danh sách
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Type --}}
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">
                                Loại giao dịch <span class="text-red-500">*</span>
                            </label>
                            <select id="type" name="type" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="thu" {{ old('type', $transaction->type) == 'thu' ? 'selected' : '' }}>Thu</option>
                                <option value="chi" {{ old('type', $transaction->type) == 'chi' ? 'selected' : '' }}>Chi</option>
                                <option value="du_kien_chi" {{ old('type', $transaction->type) == 'du_kien_chi' ? 'selected' : '' }}>Dự kiến chi</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">💡 "Dự kiến chi" sẽ được trừ khỏi lợi nhuận và thống kê riêng là "khoản chưa chi"</p>
                        </div>

                        {{-- Vehicle --}}
                        <div>
                            <label for="vehicle_id" class="block text-sm font-medium text-gray-700">
                                Xe (tùy chọn)
                            </label>
                            <select id="vehicle_id" name="vehicle_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Không liên kết --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $transaction->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->license_plate }} @if($vehicle->driver_name) - {{ $vehicle->driver_name }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Incident (Optional) --}}
                        <div>
                            <label for="incident_id" class="block text-sm font-medium text-gray-700">
                                Chuyến đi (tùy chọn)
                            </label>
                            <select id="incident_id" name="incident_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Tìm kiếm chuyến đi (ID, tên bệnh nhân, biển số xe...) --</option>
                                @if($transaction->incident)
                                    <option value="{{ $transaction->incident->id }}" selected>
                                        #{{ $transaction->incident->id }} - {{ $transaction->incident->patient->name ?? 'N/A' }}
                                    </option>
                                @endif
                            </select>
                            <p class="mt-1 text-xs text-gray-500">💡 Gõ để tìm kiếm chuyến đi</p>
                        </div>

                        {{-- Amount --}}
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">
                                Số tiền <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="amount" name="amount" value="{{ old('amount', $transaction->amount) }}" required min="0" step="1"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Method --}}
                        <div>
                            <label for="method" class="block text-sm font-medium text-gray-700">
                                Phương thức <span class="text-red-500">*</span>
                            </label>
                            <select id="method" name="method" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="cash" {{ old('method', $transaction->method) == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                                <option value="bank" {{ old('method', $transaction->method) == 'bank' ? 'selected' : '' }}>Chuyển khoản</option>
                                <option value="other" {{ old('method', $transaction->method) == 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>

                        {{-- Date --}}
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">
                                Ngày giờ <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" id="date" name="date" value="{{ old('date', $transaction->date->format('Y-m-d\TH:i')) }}" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Note --}}
                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700">
                                Ghi chú
                            </label>
                            <textarea id="note" name="note" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('note', $transaction->note) }}</textarea>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Hủy
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .select2-dropdown {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #4f46e5;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#incident_id').select2({
                placeholder: 'Tìm kiếm chuyến đi (ID, tên bệnh nhân, biển số xe...)',
                allowClear: true,
                ajax: {
                    url: '{{ route("incidents.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
                templateResult: formatIncident,
                templateSelection: formatIncidentSelection
            });

            function formatIncident(incident) {
                if (incident.loading) {
                    return incident.text;
                }
                
                var $container = $(
                    '<div class="select2-result-incident clearfix">' +
                        '<div class="select2-result-incident__meta">' +
                            '<div class="select2-result-incident__title"><strong>#' + incident.id + '</strong> - ' + (incident.patient_name || 'N/A') + '</div>' +
                            '<div class="select2-result-incident__description text-sm text-gray-600">' +
                                '🚗 ' + (incident.vehicle_plate || 'N/A') + ' • ' +
                                '📅 ' + incident.date +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );

                return $container;
            }

            function formatIncidentSelection(incident) {
                if (incident.id) {
                    return '#' + incident.id + ' - ' + (incident.patient_name || incident.text);
                }
                return incident.text;
            }
        });
    </script>
    @endpush
</x-app-layout>
