<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quản lý Phân quyền') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- Info Banner -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Click vào ô để bật/tắt quyền. <span class="font-semibold">✔</span> = Có quyền, <span class="font-semibold">·</span> = Không có quyền
                        </p>
                    </div>
                </div>
            </div>

            <!-- Permission Matrix -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="sticky left-0 z-10 bg-gray-50 px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-300 min-w-[200px]">
                                        Permission
                                    </th>
                                    @foreach($roles as $role)
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-300 min-w-[120px]">
                                        {{ $role->name }}
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($permissions as $permission)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="sticky left-0 z-10 bg-white hover:bg-gray-50 px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r border-gray-300 font-medium">
                                        {{ $permission->name }}
                                    </td>
                                    @foreach($roles as $role)
                                    <td class="px-4 py-3 text-center border-r border-gray-300">
                                        <button 
                                            type="button"
                                            onclick="togglePermission('{{ $role->name }}', '{{ $permission->name }}', this)"
                                            class="permission-toggle inline-flex items-center justify-center w-8 h-8 rounded transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                                {{ $matrix[$permission->name][$role->name] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}"
                                            data-has-permission="{{ $matrix[$permission->name][$role->name] ? '1' : '0' }}"
                                            title="{{ $matrix[$permission->name][$role->name] ? 'Click to revoke' : 'Click to grant' }}"
                                        >
                                            <span class="text-lg font-bold">
                                                {{ $matrix[$permission->name][$role->name] ? '✔' : '·' }}
                                            </span>
                                        </button>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Stats -->
                    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600">Total Roles</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $roles->count() }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600">Total Permissions</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $permissions->count() }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600">Last Updated</div>
                            <div class="text-sm font-medium text-gray-900">{{ now()->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600">Guard</div>
                            <div class="text-sm font-medium text-gray-900">web</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <div class="flex items-center gap-6 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded bg-green-100 text-green-700 font-bold">✔</span>
                            <span>Có quyền</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded bg-gray-100 text-gray-400 font-bold">·</span>
                            <span>Không có quyền</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 hidden bg-white rounded-lg shadow-lg border border-gray-200 p-4 min-w-[300px] z-50 transition-all duration-300">
        <div class="flex items-start gap-3">
            <div id="toast-icon" class="flex-shrink-0">
                <!-- Icon will be inserted by JS -->
            </div>
            <div class="flex-1">
                <p id="toast-message" class="text-sm font-medium text-gray-900"></p>
            </div>
            <button onclick="hideToast()" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
        function togglePermission(role, permission, button) {
            // Disable button during request
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');
            
            fetch('{{ route('role-permissions.toggle') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    role: role,
                    permission: permission
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button state
                    const hasPermission = data.hasPermission;
                    button.dataset.hasPermission = hasPermission ? '1' : '0';
                    
                    // Update button styling
                    if (hasPermission) {
                        button.classList.remove('bg-gray-100', 'text-gray-400', 'hover:bg-gray-200');
                        button.classList.add('bg-green-100', 'text-green-700', 'hover:bg-green-200');
                        button.querySelector('span').textContent = '✔';
                        button.title = 'Click to revoke';
                    } else {
                        button.classList.remove('bg-green-100', 'text-green-700', 'hover:bg-green-200');
                        button.classList.add('bg-gray-100', 'text-gray-400', 'hover:bg-gray-200');
                        button.querySelector('span').textContent = '·';
                        button.title = 'Click to grant';
                    }
                    
                    // Show success toast
                    showToast('success', data.message);
                } else {
                    showToast('error', 'Failed to update permission');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'An error occurred while updating permission');
            })
            .finally(() => {
                // Re-enable button
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        }

        function showToast(type, message) {
            const toast = document.getElementById('toast');
            const toastIcon = document.getElementById('toast-icon');
            const toastMessage = document.getElementById('toast-message');
            
            // Set icon based on type
            if (type === 'success') {
                toastIcon.innerHTML = '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
            } else {
                toastIcon.innerHTML = '<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
            }
            
            toastMessage.textContent = message;
            toast.classList.remove('hidden');
            
            // Auto hide after 3 seconds
            setTimeout(() => {
                hideToast();
            }, 3000);
        }

        function hideToast() {
            const toast = document.getElementById('toast');
            toast.classList.add('hidden');
        }

        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideToast();
            }
        });
    </script>
    @endpush
</x-app-layout>
