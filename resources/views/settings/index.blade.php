<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cấu hình Hệ thống') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('warning'))
                <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('warning') }}</span>
                    @if(session('errors'))
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach(session('errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Tabs Navigation -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                            @foreach($settingGroups as $groupKey => $groupName)
                                <button
                                    onclick="switchTab('{{ $groupKey }}')"
                                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                    data-tab="{{ $groupKey }}"
                                >
                                    {{ $groupName }}
                                    <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">
                                        {{ $settings[$groupKey]->count() }}
                                    </span>
                                </button>
                            @endforeach
                        </nav>
                    </div>

                    <!-- Settings Form -->
                    <form method="POST" action="{{ route('settings.update') }}" id="settings-form" onsubmit="saveCurrentTab()">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="active_tab" id="active-tab-input" value="">

                        @foreach($settingGroups as $groupKey => $groupName)
                            <div class="tab-content hidden" data-tab-content="{{ $groupKey }}">
                                <div class="space-y-6">
                                    @foreach($settings[$groupKey] as $setting)
                                        <div class="border-b border-gray-200 pb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ $setting->description }}
                                                @if($setting->is_public)
                                                    <span class="ml-2 text-xs text-gray-500">(Công khai)</span>
                                                @endif
                                            </label>

                                            @if($setting->type === 'text' || $setting->type === 'email' || $setting->type === 'url' || $setting->type === 'time')
                                                <input
                                                    type="{{ $setting->type }}"
                                                    name="settings[{{ $setting->key }}]"
                                                    value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                />

                                            @elseif($setting->type === 'number')
                                                <input
                                                    type="number"
                                                    name="settings[{{ $setting->key }}]"
                                                    value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                                    step="any"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                />

                                            @elseif($setting->type === 'textarea')
                                                <textarea
                                                    name="settings[{{ $setting->key }}]"
                                                    rows="3"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                >{{ old('settings.' . $setting->key, $setting->value) }}</textarea>

                                            @elseif($setting->type === 'checkbox')
                                                <div class="flex items-center mt-2">
                                                    <input
                                                        type="hidden"
                                                        name="settings[{{ $setting->key }}]"
                                                        value="0"
                                                    />
                                                    <input
                                                        type="checkbox"
                                                        name="settings[{{ $setting->key }}]"
                                                        value="1"
                                                        {{ old('settings.' . $setting->key, $setting->value) == '1' ? 'checked' : '' }}
                                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                    />
                                                    <span class="ml-2 text-sm text-gray-600">Bật</span>
                                                </div>

                                            @elseif($setting->type === 'select')
                                                <select
                                                    name="settings[{{ $setting->key }}]"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                >
                                                    @php
                                                        $options = is_array($setting->options) ? $setting->options : [];
                                                    @endphp
                                                    @foreach($options as $optKey => $optValue)
                                                        <option
                                                            value="{{ is_numeric($optKey) ? $optValue : $optKey }}"
                                                            {{ old('settings.' . $setting->key, $setting->value) == (is_numeric($optKey) ? $optValue : $optKey) ? 'selected' : '' }}
                                                        >
                                                            {{ $optValue }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                            @elseif($setting->type === 'color')
                                                <div class="flex items-center space-x-3">
                                                    <input
                                                        type="color"
                                                        name="settings[{{ $setting->key }}]"
                                                        id="color-{{ $setting->key }}"
                                                        value="{{ old('settings.' . $setting->key, $setting->value ?: '#000000') }}"
                                                        class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
                                                    />
                                                    <input
                                                        type="text"
                                                        id="color-text-{{ $setting->key }}"
                                                        value="{{ old('settings.' . $setting->key, $setting->value ?: '#000000') }}"
                                                        readonly
                                                        class="block w-32 rounded-md border-gray-300 bg-gray-50 shadow-sm sm:text-sm font-mono"
                                                    />
                                                </div>

                                            @elseif($setting->type === 'image' || $setting->type === 'file')
                                                <div class="mt-2">
                                                    @if($setting->value)
                                                        <div class="mb-3 flex items-center space-x-3">
                                                            @if($setting->type === 'image')
                                                                <div class="flex-shrink-0">
                                                                    <img src="{{ asset('storage/' . $setting->value) }}" alt="{{ $setting->description }}" style="max-width: 80px; max-height: 80px;" class="object-contain border rounded shadow-sm bg-gray-50">
                                                                </div>
                                                            @else
                                                                <a href="{{ asset('storage/' . $setting->value) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 underline">
                                                                    📎 Xem file hiện tại
                                                                </a>
                                                            @endif
                                                            <button
                                                                type="button"
                                                                onclick="deleteFile('{{ $setting->key }}')"
                                                                class="text-sm text-red-600 hover:text-red-900 font-medium"
                                                            >
                                                                Xóa
                                                            </button>
                                                        </div>
                                                    @endif
                                                    <div class="space-y-2">
                                                        <input
                                                            type="file"
                                                            id="file-{{ $setting->key }}"
                                                            accept="{{ $setting->type === 'image' ? 'image/*' : '*' }}"
                                                            onchange="uploadFile('{{ $setting->key }}')"
                                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                                        />
                                                        <span class="upload-status text-xs text-gray-500"></span>
                                                    </div>
                                                    <input type="hidden" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}">
                                                </div>
                                            @endif

                                            @if($setting->key)
                                                <p class="mt-1 text-xs text-gray-500">Key: {{ $setting->key }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-6 flex items-center justify-end space-x-3">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Hủy
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Lưu cấu hình
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active styles from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-indigo-500', 'text-indigo-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab content
            document.querySelector(`[data-tab-content="${tabName}"]`).classList.remove('hidden');

            // Add active styles to selected tab
            const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
            activeButton.classList.add('border-indigo-500', 'text-indigo-600');
            activeButton.classList.remove('border-transparent', 'text-gray-500');

            // Save to localStorage
            localStorage.setItem('settings_active_tab', tabName);
            document.getElementById('active-tab-input').value = tabName;
        }

        function saveCurrentTab() {
            const activeTab = localStorage.getItem('settings_active_tab') || 'company';
            document.getElementById('active-tab-input').value = activeTab;
        }

        function uploadFile(key) {
            const fileInput = document.getElementById(`file-${key}`);
            const file = fileInput.files[0];

            if (!file) return;

            // Show uploading message
            const uploadStatus = fileInput.parentElement.querySelector('.upload-status');
            if (uploadStatus) uploadStatus.textContent = 'Đang upload...';

            const formData = new FormData();
            formData.append('key', key);
            formData.append('file', file);

            fetch('{{ route('settings.upload') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Save current tab before reload
                    const currentTab = localStorage.getItem('settings_active_tab') || 'company';
                    alert('Upload thành công!');
                    window.location.href = '{{ route('settings.index') }}?tab=' + currentTab;
                } else {
                    alert('Lỗi: ' + data.message);
                    if (uploadStatus) uploadStatus.textContent = '';
                }
            })
            .catch(error => {
                alert('Lỗi khi upload: ' + error.message);
                if (uploadStatus) uploadStatus.textContent = '';
            });
        }

        function deleteFile(key) {
            if (!confirm('Bạn có chắc muốn xóa file này?')) {
                return;
            }

            fetch('{{ route('settings.delete-file') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ key: key })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Save current tab before reload
                    const currentTab = localStorage.getItem('settings_active_tab') || 'company';
                    alert('Đã xóa file thành công!');
                    window.location.href = '{{ route('settings.index') }}?tab=' + currentTab;
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                alert('Lỗi khi xóa file: ' + error.message);
            });
        }

        // Initialize tab on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Get tab from URL parameter or localStorage
            const urlParams = new URLSearchParams(window.location.search);
            const tabFromUrl = urlParams.get('tab');
            const savedTab = localStorage.getItem('settings_active_tab');
            const initialTab = tabFromUrl || savedTab || 'company';
            
            // Switch to the initial tab
            switchTab(initialTab);

            // Update color input text when color picker changes
            document.querySelectorAll('input[type="color"]').forEach(colorInput => {
                const key = colorInput.id.replace('color-', '');
                const textInput = document.getElementById('color-text-' + key);
                if (textInput) {
                    colorInput.addEventListener('input', function() {
                        textInput.value = this.value.toUpperCase();
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
