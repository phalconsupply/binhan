<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Báo cáo Chi tiết theo Khoa - Xem trước') }}
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
                            • Chọn <strong>Nơi đi (Khoa phòng)</strong> để xuất báo cáo<br>
                            • Chỉnh sửa <strong>Ghi chú</strong> trực tiếp trong bảng (tự động lưu sau 2 giây)<br>
                            • <strong>Bỏ chọn</strong> checkbox ở cột đầu để loại bỏ dòng không muốn xuất hiện trong báo cáo<br>
                            • Sử dụng <strong>Tuỳ chọn cột</strong> bên dưới để ẩn/hiện các cột trong báo cáo
                        </p>
                    </div>
                </div>
            </div>

            <!-- Location Selection -->
            <div class="bg-white shadow-sm sm:rounded-lg p-4 mb-4">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-700">Chọn Nơi đi (Khoa phòng):</h3>
                </div>
                <div class="flex flex-wrap gap-3">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="selectAllLocations" checked class="rounded border-gray-300 text-blue-600">
                        <span class="ml-2 text-sm font-semibold text-gray-700">Chọn tất cả</span>
                    </label>
                    @foreach($locations as $location)
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="location-checkbox rounded border-gray-300 text-blue-600" 
                               data-location="{{ $location->id }}" 
                               value="{{ $location->id }}" 
                               checked>
                        <span class="ml-2 text-sm text-gray-700">{{ $location->name }} ({{ $location->incidents_count }})</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Column Visibility Toggle -->
            <div class="bg-white shadow-sm sm:rounded-lg p-4 mb-4 print:hidden">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-700">Tuỳ chọn hiển thị cột:</h3>
                </div>
                <div class="flex flex-wrap gap-3">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="column-toggle rounded border-gray-300 text-blue-600" data-column="col-stt" checked>
                        <span class="ml-2 text-sm text-gray-700">STT</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="column-toggle rounded border-gray-300 text-blue-600" data-column="col-date" checked>
                        <span class="ml-2 text-sm text-gray-700">Ngày</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="column-toggle rounded border-gray-300 text-blue-600" data-column="col-patient" checked>
                        <span class="ml-2 text-sm text-gray-700">Họ tên người bệnh</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="column-toggle rounded border-gray-300 text-blue-600" data-column="col-from" checked>
                        <span class="ml-2 text-sm text-gray-700">Nơi đi</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="column-toggle rounded border-gray-300 text-blue-600" data-column="col-to" checked>
                        <span class="ml-2 text-sm text-gray-700">Nơi đến</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="column-toggle rounded border-gray-300 text-blue-600" data-column="col-driver" checked>
                        <span class="ml-2 text-sm text-gray-700">Lái xe</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="column-toggle rounded border-gray-300 text-blue-600" data-column="col-staff" checked>
                        <span class="ml-2 text-sm text-gray-700">Nhân viên Y tế</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="column-toggle rounded border-gray-300 text-blue-600" data-column="col-note" checked>
                        <span class="ml-2 text-sm text-gray-700">Ghi chú</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="column-toggle rounded border-gray-300 text-blue-600" data-column="col-commission" checked>
                        <span class="ml-2 text-sm text-gray-700">Hoa hồng</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="column-toggle rounded border-gray-300 text-blue-600" data-column="col-partner" checked>
                        <span class="ml-2 text-sm text-gray-700">Nơi nhận HH</span>
                    </label>
                </div>
            </div>

            <form id="reportForm" method="POST" action="{{ route('reports.locations.export-pdf') }}" class="bg-white shadow-sm sm:rounded-lg">
                @csrf
                <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                <input type="hidden" name="date_to" value="{{ $dateTo }}">

                <!-- Report Header -->
                <div class="p-6 border-b border-gray-200 text-center print:p-4">
                    <h1 class="text-2xl font-bold text-gray-900">BÁO CÁO CHI TIẾT THEO KHOA</h1>
                    <p class="text-sm text-gray-600 mt-2">
                        Thời gian: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
                    </p>
                </div>

                <!-- Report Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 4%;">
                                    <input type="checkbox" id="selectAllRows" checked class="rounded border-gray-300 text-blue-600" title="Chọn/Bỏ chọn tất cả">
                                </th>
                                <th class="col-stt px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 4%;">STT</th>
                                <th class="col-date px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 8%;">Ngày</th>
                                <th class="col-patient px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 14%;">Họ tên người bệnh</th>
                                <th class="col-from px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 12%;">Nơi đi</th>
                                <th class="col-to px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 12%;">Nơi đến</th>
                                <th class="col-driver px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 10%;">Lái xe</th>
                                <th class="col-staff px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 10%;">Nhân viên Y tế</th>
                                <th class="col-note px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 12%;">Ghi chú</th>
                                <th class="col-commission px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 7%;">Hoa hồng</th>
                                <th class="col-partner px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border border-gray-300" style="width: 10%;">Nơi nhận HH</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $globalIndex = 1; @endphp
                            @foreach($incidentsByLocation as $locationId => $locationIncidents)
                            @foreach($locationIncidents as $incident)
                            <tr class="hover:bg-gray-50 incident-row" data-location="{{ $locationId }}">
                                <td class="px-3 py-2 text-center border border-gray-300">
                                    <input type="checkbox" 
                                           name="include_incidents[]" 
                                           value="{{ $incident->id }}" 
                                           checked 
                                           class="row-checkbox rounded border-gray-300 text-blue-600"
                                           title="Bỏ chọn để loại bỏ khỏi báo cáo">
                                </td>
                                <td class="col-stt px-3 py-2 text-center text-sm border border-gray-300">{{ $globalIndex++ }}</td>
                                <td class="col-date px-3 py-2 text-sm border border-gray-300">{{ $incident->date->format('d/m/Y') }}</td>
                                <td class="col-patient px-3 py-2 text-sm border border-gray-300">{{ $incident->patient ? $incident->patient->name : '-' }}</td>
                                <td class="col-from px-3 py-2 text-sm border border-gray-300">{{ $incident->fromLocation ? $incident->fromLocation->name : '-' }}</td>
                                <td class="col-to px-3 py-2 text-sm border border-gray-300">{{ $incident->toLocation ? $incident->toLocation->name : '-' }}</td>
                                <td class="col-driver px-3 py-2 text-sm border border-gray-300">{{ $incident->drivers->pluck('name')->join(', ') ?: '-' }}</td>
                                <td class="col-staff px-3 py-2 text-sm border border-gray-300">{{ $incident->medicalStaff->pluck('name')->join(', ') ?: '-' }}</td>
                                <td class="col-note px-3 py-2 border border-gray-300">
                                    <input type="text" 
                                           name="notes[{{ $incident->id }}]" 
                                           value="{{ $incident->summary ?? '' }}"
                                           data-incident-id="{{ $incident->id }}"
                                           class="note-input w-full text-sm border-0 focus:ring-2 focus:ring-blue-500 rounded px-2 py-1"
                                           placeholder="Nhập ghi chú...">
                                    <span class="save-status text-xs text-gray-500 hidden"></span>
                                </td>
                                <td class="col-commission px-3 py-2 text-right text-sm border border-gray-300">{{ $incident->commission_amount ? number_format($incident->commission_amount, 0, ',', '.') : '-' }}</td>
                                <td class="col-partner px-3 py-2 text-sm border border-gray-300">{{ $incident->partner ? $incident->partner->name : '-' }}</td>
                            </tr>
                            @endforeach
                            @endforeach
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
                        <button type="button" onclick="submitReport()" class="px-6 py-2.5 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            📄 Xuất PDF
                        </button>
                        <button type="button" onclick="window.print()" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            🖨️ In báo cáo
                        </button>
                        <a href="{{ route('reports.index') }}" class="ml-auto px-6 py-2.5 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                            Quay lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Hide unchecked rows */
        .incident-row.row-unchecked {
            display: none;
        }
        
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
            .row-checkbox {
                display: none !important;
            }
            .incident-row {
                page-break-inside: avoid;
            }
            /* Hide unchecked rows when printing */
            .incident-row.row-unchecked {
                display: none !important;
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
        // Location filter functionality
        const locationCheckboxes = document.querySelectorAll('.location-checkbox');
        const selectAllLocations = document.getElementById('selectAllLocations');
        
        selectAllLocations.addEventListener('change', function() {
            locationCheckboxes.forEach(cb => {
                cb.checked = this.checked;
                filterRowsByLocation();
            });
        });

        locationCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                filterRowsByLocation();
                updateSelectAllLocationsState();
            });
        });

        function filterRowsByLocation() {
            const selectedLocations = Array.from(locationCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.getAttribute('data-location'));
            
            document.querySelectorAll('.incident-row').forEach(row => {
                const rowLocation = row.getAttribute('data-location');
                if (selectedLocations.includes(rowLocation)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Re-number STT
            renumberRows();
        }

        function renumberRows() {
            let visibleIndex = 1;
            document.querySelectorAll('.incident-row').forEach(row => {
                if (row.style.display !== 'none') {
                    const sttCell = row.querySelector('.col-stt');
                    if (sttCell) {
                        sttCell.textContent = visibleIndex++;
                    }
                }
            });
        }

        function updateSelectAllLocationsState() {
            const allChecked = Array.from(locationCheckboxes).every(cb => cb.checked);
            selectAllLocations.checked = allChecked;
        }

        // Column visibility toggle
        document.querySelectorAll('.column-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const columnClass = this.getAttribute('data-column');
                const elements = document.querySelectorAll('.' + columnClass);
                
                elements.forEach(el => {
                    if (this.checked) {
                        el.style.display = '';
                    } else {
                        el.style.display = 'none';
                    }
                });
            });
        });

        // Select/Deselect all rows checkbox functionality
        document.getElementById('selectAllRows').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => {
                const row = cb.closest('.incident-row');
                if (row.style.display !== 'none') {
                    cb.checked = this.checked;
                    updateRowStyle(cb);
                }
            });
        });

        // Update row style when checkbox changes
        document.querySelectorAll('.row-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateRowStyle(this);
                updateSelectAllRowsState();
            });
        });

        function updateRowStyle(checkbox) {
            const row = checkbox.closest('tr');
            if (checkbox.checked) {
                row.style.opacity = '1';
                row.style.backgroundColor = '';
                row.classList.remove('row-unchecked');
            } else {
                row.style.opacity = '0.4';
                row.style.backgroundColor = '#fee';
                row.classList.add('row-unchecked');
            }
        }

        function updateSelectAllRowsState() {
            const allCheckboxes = Array.from(document.querySelectorAll('.row-checkbox')).filter(cb => {
                const row = cb.closest('.incident-row');
                return row.style.display !== 'none';
            });
            const checkedCheckboxes = allCheckboxes.filter(cb => cb.checked);
            const selectAll = document.getElementById('selectAllRows');
            
            selectAll.checked = allCheckboxes.length === checkedCheckboxes.length;
        }

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

        // Submit report with only selected rows
        function submitReport() {
            const form = document.getElementById('reportForm');
            
            // Remove previously added hidden inputs (if any)
            form.querySelectorAll('input[name="include_incidents[]"][type="hidden"]').forEach(input => {
                input.remove();
            });
            
            // Disable all visible checkboxes to prevent them from being submitted
            document.querySelectorAll('.row-checkbox').forEach(checkbox => {
                checkbox.disabled = true;
            });
            
            // Add hidden inputs for only checked checkboxes that are visible (not filtered out)
            document.querySelectorAll('.row-checkbox:checked').forEach(checkbox => {
                const row = checkbox.closest('.incident-row');
                // Only include if row is visible
                if (row.style.display !== 'none') {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'include_incidents[]';
                    input.value = checkbox.value;
                    form.appendChild(input);
                }
            });
            
            // Add visible columns info
            const visibleColumns = [];
            document.querySelectorAll('.column-toggle:checked').forEach(toggle => {
                visibleColumns.push(toggle.getAttribute('data-column'));
            });
            
            const columnsInput = document.createElement('input');
            columnsInput.type = 'hidden';
            columnsInput.name = 'visible_columns';
            columnsInput.value = JSON.stringify(visibleColumns);
            form.appendChild(columnsInput);
            
            // Submit the form
            form.submit();
        }
    </script>
</x-app-layout>
