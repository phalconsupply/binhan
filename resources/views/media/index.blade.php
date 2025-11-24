<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quản lý File & Media') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Upload Section -->
                    <div class="mb-6 border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-500 transition-colors" id="upload-zone">
                        <form id="upload-form" enctype="multipart/form-data">
                            @csrf
                            <input type="file" id="file-input" name="file" class="hidden" multiple accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx">
                            
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            
                            <div class="mt-4">
                                <button type="button" onclick="document.getElementById('file-input').click()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Chọn file
                                </button>
                                <p class="mt-2 text-sm text-gray-500">hoặc kéo thả file vào đây</p>
                                <p class="text-xs text-gray-400 mt-1">Tối đa 10MB - Hỗ trợ: ảnh, PDF, Word, Excel</p>
                            </div>

                            <div class="mt-4">
                                <label class="text-sm text-gray-700 mr-2">Thư mục:</label>
                                <select name="collection" id="collection-select" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="default">Khác</option>
                                    <option value="avatars">Avatar</option>
                                    <option value="logos">Logo</option>
                                    <option value="images">Hình ảnh</option>
                                    <option value="documents">Tài liệu</option>
                                </select>
                            </div>
                        </form>
                        
                        <div id="upload-progress" class="mt-4 hidden">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="progress-bar" class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2" id="upload-status">Đang upload...</p>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            @foreach($collections as $key => $label)
                                <a href="{{ route('media.index', ['collection' => $key]) }}" 
                                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $collection === $key ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </nav>
                    </div>

                    <!-- Media Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4" id="media-grid">
                        @forelse($media as $item)
                            <div class="media-item group relative border rounded-lg p-2 hover:shadow-lg transition-shadow" data-id="{{ $item->id }}">
                                <div class="aspect-square bg-gray-100 rounded overflow-hidden mb-2">
                                    @if(str_starts_with($item->mime_type, 'image/'))
                                        <img src="{{ $item->getUrl() }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            @if(str_starts_with($item->mime_type, 'application/pdf'))
                                                <svg class="w-16 h-16 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 18h12V6h-4V2H4v16zm-2 1V0h12l4 4v16H2v-1z"/>
                                                </svg>
                                            @elseif(str_contains($item->mime_type, 'word'))
                                                <svg class="w-16 h-16 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 18h12V6h-4V2H4v16zm-2 1V0h12l4 4v16H2v-1z"/>
                                                </svg>
                                            @elseif(str_contains($item->mime_type, 'sheet') || str_contains($item->mime_type, 'excel'))
                                                <svg class="w-16 h-16 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 18h12V6h-4V2H4v16zm-2 1V0h12l4 4v16H2v-1z"/>
                                                </svg>
                                            @else
                                                <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 18h12V6h-4V2H4v16zm-2 1V0h12l4 4v16H2v-1z"/>
                                                </svg>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                
                                <p class="text-xs text-gray-700 truncate" title="{{ $item->file_name }}">{{ $item->file_name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->human_readable_size }}</p>
                                
                                <!-- Action buttons (visible on hover) -->
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity flex space-x-1">
                                    <button onclick="viewMedia({{ $item->id }})" class="p-1 bg-white rounded shadow hover:bg-gray-100" title="Xem">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <a href="{{ route('media.download', $item->id) }}" class="p-1 bg-white rounded shadow hover:bg-gray-100" title="Tải về">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </a>
                                    <button onclick="deleteMedia({{ $item->id }})" class="p-1 bg-white rounded shadow hover:bg-red-100" title="Xóa">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Chưa có file nào</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if(method_exists($media, 'links'))
                        <div class="mt-6">
                            {{ $media->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Media Detail Modal -->
    <div id="media-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Thông tin file</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="media-detail-content"></div>
        </div>
    </div>

    @push('scripts')
    <script>
        // File input change handler
        document.getElementById('file-input').addEventListener('change', function(e) {
            if (this.files.length > 0) {
                uploadFiles(this.files);
            }
        });

        // Drag and drop handlers
        const uploadZone = document.getElementById('upload-zone');
        
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('border-indigo-500', 'bg-indigo-50');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('border-indigo-500', 'bg-indigo-50');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('border-indigo-500', 'bg-indigo-50');
            
            if (e.dataTransfer.files.length > 0) {
                uploadFiles(e.dataTransfer.files);
            }
        });

        // Upload files
        function uploadFiles(files) {
            const collection = document.getElementById('collection-select').value;
            const progress = document.getElementById('upload-progress');
            const progressBar = document.getElementById('progress-bar');
            const uploadStatus = document.getElementById('upload-status');
            
            progress.classList.remove('hidden');
            
            Array.from(files).forEach((file, index) => {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('collection', collection);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route('media.upload') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const percent = ((index + 1) / files.length) * 100;
                        progressBar.style.width = percent + '%';
                        uploadStatus.textContent = `Đã upload ${index + 1}/${files.length} file`;
                        
                        if (index === files.length - 1) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        }
                    } else {
                        alert('Lỗi upload: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Lỗi upload: ' + error.message);
                });
            });
        }

        // View media details
        function viewMedia(id) {
            fetch(`/media/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const media = data.media;
                        let preview = '';
                        
                        if (media.mime_type.startsWith('image/')) {
                            preview = `<img src="${media.url}" class="w-full rounded mb-4">`;
                        }
                        
                        document.getElementById('media-detail-content').innerHTML = `
                            ${preview}
                            <dl class="space-y-2">
                                <div><dt class="font-semibold inline">Tên file:</dt> <dd class="inline">${media.file_name}</dd></div>
                                <div><dt class="font-semibold inline">Loại:</dt> <dd class="inline">${media.mime_type}</dd></div>
                                <div><dt class="font-semibold inline">Kích thước:</dt> <dd class="inline">${media.size}</dd></div>
                                <div><dt class="font-semibold inline">Thư mục:</dt> <dd class="inline">${media.collection}</dd></div>
                                <div><dt class="font-semibold inline">Người upload:</dt> <dd class="inline">${media.uploader}</dd></div>
                                <div><dt class="font-semibold inline">Ngày tạo:</dt> <dd class="inline">${media.created_at}</dd></div>
                                <div><dt class="font-semibold inline">URL:</dt> <dd class="inline text-xs break-all"><a href="${media.url}" target="_blank" class="text-indigo-600 hover:underline">${media.url}</a></dd></div>
                            </dl>
                        `;
                        document.getElementById('media-modal').classList.remove('hidden');
                    }
                });
        }

        // Delete media
        function deleteMedia(id) {
            if (!confirm('Bạn có chắc muốn xóa file này?')) {
                return;
            }

            fetch(`/media/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`[data-id="${id}"]`).remove();
                    alert('Đã xóa file thành công!');
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                alert('Lỗi: ' + error.message);
            });
        }

        // Close modal
        function closeModal() {
            document.getElementById('media-modal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>
