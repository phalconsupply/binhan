<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Báo cáo Chuyển viện Bình An - Xem trước') }}
            </h2>
            <a href="{{ route('reports.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                ← Quay lại Báo cáo
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- Info Banner -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Bạn có thể chỉnh sửa <strong>Ghi chú</strong> trực tiếp trong bảng bên dưới. Ghi chú sẽ được <strong>tự động lưu</strong> sau 2 giây khi bạn ngừng nhập.
                        </p>
                    </div>
                </div>
            </div>

            <form id="reportForm" method="POST" action="{{ route('reports.department.export-pdf-with-notes') }}" class="bg-white shadow-sm sm:rounded-lg">
                @csrf
                <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                <input type="hidden" name="date_to" value="{{ $dateTo }}">

                <!-- Report Header -->
                <div class="p-6 border-b border-gray-200 text-center print:p-4">
                    <h1 class="text-2xl font-bold text-gray-900">BÁO CÁO CHUYỂN VIỆN BÌNH AN</h1>
                    <p class="text-sm text-gray-600 mt-2">
                        Thông tin tháng báo cáo: Tháng {{ \Carbon\Carbon::parse($dateFrom)->format('m') }} Năm {{ \Carbon\Carbon::parse($dateFrom)->format('Y') }}
                    </p>
                </div>

                <!-- Report Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 5%;">STT</th>
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 8%;">Ngày</th>
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 15%;">Họ tên người bệnh</th>
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 12%;">Nơi đi</th>
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 12%;">Nơi đến</th>
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 11%;">Lái xe</th>
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 11%;">Nhân viên Y tế</th>
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 13%;">Ghi chú</th>
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 7%;">Hoa hồng</th>
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 10%;">Nơi nhận HH</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($incidents as $index => $incident)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 text-center text-sm border border-gray-300">{{ $index + 1 }}</td>
                                <td class="px-3 py-2 text-sm border border-gray-300">{{ $incident->date->format('d/m/Y') }}</td>
                                <td class="px-3 py-2 text-sm border border-gray-300">{{ $incident->patient ? $incident->patient->name : '-' }}</td>
                                <td class="px-3 py-2 text-sm border border-gray-300">{{ $incident->fromLocation ? $incident->fromLocation->name : '-' }}</td>
                                <td class="px-3 py-2 text-sm border border-gray-300">{{ $incident->toLocation ? $incident->toLocation->name : '-' }}</td>
                                <td class="px-3 py-2 text-sm border border-gray-300">{{ $incident->drivers->pluck('name')->join(', ') ?: '-' }}</td>
                                <td class="px-3 py-2 text-sm border border-gray-300">{{ $incident->medicalStaff->pluck('name')->join(', ') ?: '-' }}</td>
                                <td class="px-3 py-2 border border-gray-300">
                                    <input type="text" 
                                           name="notes[{{ $incident->id }}]" 
                                           value="{{ $incident->summary ?? '' }}"
                                           data-incident-id="{{ $incident->id }}"
                                           class="note-input w-full text-sm border-0 focus:ring-2 focus:ring-blue-500 rounded px-2 py-1"
                                           placeholder="Nhập ghi chú...">
                                    <span class="save-status text-xs text-gray-500 hidden"></span>
                                </td>
                                <td class="px-3 py-2 text-right text-sm border border-gray-300">{{ $incident->commission_amount ? number_format($incident->commission_amount, 0, ',', '.') : '-' }}</td>
                                <td class="px-3 py-2 text-sm border border-gray-300">{{ $incident->partner ? $incident->partner->name : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="px-6 py-8 text-center text-gray-500 border border-gray-300">
                                    Không có dữ liệu trong khoảng thời gian này
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Signature Section -->
                <div class="p-6 border-t border-gray-200 print:p-4">
                    <div class="flex justify-end">
                        <div class="text-center" style="min-width: 250px;">
                            <p class="text-sm font-medium mb-1">Lâm Đồng, ngày {{ now()->format('d') }} tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}</p>
                            <p class="text-sm font-bold mb-1">GIÁM ĐỐC</p>
                            <p class="text-sm" style="line-height: 1.8;">&nbsp;</p>
                            <p class="text-sm" style="line-height: 1.8;">&nbsp;</p>
                            <p class="text-sm" style="line-height: 1.8;">&nbsp;</p>
                            <p class="text-sm" style="line-height: 1.8;">&nbsp;</p>
                            <p class="text-sm font-bold">NGÔ QUYỀN</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="p-6 bg-gray-50 border-t border-gray-200 print:hidden">
                    <div class="mb-3">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="save_notes" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Lưu các thay đổi ghi chú vào hệ thống khi xuất PDF</span>
                        </label>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="px-6 py-2.5 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            📄 Xuất PDF
                        </button>
                        <button type="button" onclick="window.print()" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            🖨️ In báo cáo
                        </button>
                        <a href="{{ route('reports.export.incidents.excel', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" 
                           class="px-6 py-2.5 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors text-center">
                            📊 Xuất Excel
                        </a>
                        <a href="{{ route('reports.index') }}" class="ml-auto px-6 py-2.5 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                            Quay lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #reportForm, #reportForm * {
                visibility: visible;
            }
            #reportForm {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .print\:hidden {
                display: none !important;
            }
            input[type="text"] {
                border: none !important;
                background: transparent !important;
            }
            @page {
                size: A4 landscape;
                margin: 15mm;
            }
            table {
                font-size: 10pt;
            }
            th, td {
                padding: 4px 6px !important;
            }
        }
    </style>

    <script>
        // Auto-save notes with debounce
        let saveTimers = {};
        
        document.querySelectorAll('.note-input').forEach(input => {
            input.addEventListener('input', function() {
                const incidentId = this.getAttribute('data-incident-id');
                const statusSpan = this.parentElement.querySelector('.save-status');
                
                // Clear existing timer
                if (saveTimers[incidentId]) {
                    clearTimeout(saveTimers[incidentId]);
                }
                
                // Show saving status
                statusSpan.textContent = 'Đang lưu...';
                statusSpan.className = 'save-status text-xs text-blue-500';
                statusSpan.classList.remove('hidden');
                
                // Set new timer
                saveTimers[incidentId] = setTimeout(() => {
                    saveNote(incidentId, this.value, statusSpan);
                }, 2000);
            });
        });
        
        function saveNote(incidentId, note, statusSpan) {
            fetch('{{ route('reports.department.save-notes') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    incident_id: incidentId,
                    note: note
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusSpan.textContent = '✓ Đã lưu';
                    statusSpan.className = 'save-status text-xs text-green-600';
                    
                    // Hide status after 3 seconds
                    setTimeout(() => {
                        statusSpan.classList.add('hidden');
                    }, 3000);
                } else {
                    statusSpan.textContent = '✗ Lỗi';
                    statusSpan.className = 'save-status text-xs text-red-600';
                }
            })
            .catch(error => {
                console.error('Error saving note:', error);
                statusSpan.textContent = '✗ Lỗi kết nối';
                statusSpan.className = 'save-status text-xs text-red-600';
            });
        }
    </script>
</x-app-layout>
