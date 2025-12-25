<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tìm kiếm') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('search.index') }}">
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <input type="text" name="q" value="{{ $query }}" 
                                       placeholder="Tìm kiếm xe, bệnh nhân, chuyến đi..."
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       autofocus>
                            </div>
                            <div class="w-48">
                                <select name="type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="all" {{ $type == 'all' ? 'selected' : '' }}>Tất cả</option>
                                    @can('search vehicles')
                                    <option value="vehicles" {{ $type == 'vehicles' ? 'selected' : '' }}>Xe</option>
                                    @endcan
                                    @can('search patients')
                                    <option value="patients" {{ $type == 'patients' ? 'selected' : '' }}>Bệnh nhân</option>
                                    @endcan
                                    @can('search incidents')
                                    <option value="incidents" {{ $type == 'incidents' ? 'selected' : '' }}>Chuyến đi</option>
                                    @endcan
                                    @can('search transactions')
                                    <option value="transactions" {{ $type == 'transactions' ? 'selected' : '' }}>Giao dịch</option>
                                    @endcan
                                    @can('search notes')
                                    <option value="notes" {{ $type == 'notes' ? 'selected' : '' }}>Ghi chú</option>
                                    @endcan
                                </select>
                            </div>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Tìm
                            </button>
                        </div>
                    </form>

                    <!-- Permission Info -->
                    @if(!auth()->user()->can('search vehicles') || !auth()->user()->can('search patients') || !auth()->user()->can('search incidents') || !auth()->user()->can('search transactions') || !auth()->user()->can('search notes'))
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <p class="text-sm text-blue-700">
                            <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            Bạn có quyền tìm kiếm: 
                            @can('search vehicles')<span class="font-semibold">Xe</span>@endcan
                            @can('search patients')@can('search vehicles'), @endcan<span class="font-semibold">Bệnh nhân</span>@endcan
                            @can('search incidents')@if(auth()->user()->can('search vehicles') || auth()->user()->can('search patients')), @endif<span class="font-semibold">Chuyến đi</span>@endcan
                            @can('search transactions')@if(auth()->user()->can('search vehicles') || auth()->user()->can('search patients') || auth()->user()->can('search incidents')), @endif<span class="font-semibold">Giao dịch</span>@endcan
                            @can('search notes')@if(auth()->user()->can('search vehicles') || auth()->user()->can('search patients') || auth()->user()->can('search incidents') || auth()->user()->can('search transactions')), @endif<span class="font-semibold">Ghi chú</span>@endcan
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            @if($query)
            <!-- Search Results -->
            <div class="space-y-6">
                <!-- Vehicles -->
                @can('search vehicles')
                @if(isset($results['vehicles']) && $results['vehicles']->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Xe cấp cứu ({{ $results['vehicles']->count() }})</h3>
                        <div class="space-y-2">
                            @foreach($results['vehicles'] as $vehicle)
                            <a href="{{ route('vehicles.show', $vehicle) }}" class="block p-4 border rounded hover:bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold text-blue-600">{{ $vehicle->license_plate }}</p>
                                        <p class="text-sm text-gray-600">{{ $vehicle->model ?? '-' }} • {{ $vehicle->driver_name ?? 'Chưa có tài xế' }}</p>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold rounded {{ $vehicle->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $vehicle->status_label }}
                                    </span>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @endcan

                <!-- Patients -->
                @can('search patients')
                @if(isset($results['patients']) && $results['patients']->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Bệnh nhân ({{ $results['patients']->count() }})</h3>
                        <div class="space-y-2">
                            @foreach($results['patients'] as $patient)
                            <a href="{{ route('patients.show', $patient) }}" class="block p-4 border rounded hover:bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold text-blue-600">{{ $patient->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $patient->phone }} • {{ $patient->age }} tuổi • {{ $patient->gender_label }}</p>
                                        @if($patient->address)
                                        <p class="text-sm text-gray-500">{{ Str::limit($patient->address, 60) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @endcan

                <!-- Incidents -->
                @can('search incidents')
                @if(isset($results['incidents']) && $results['incidents']->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Chuyến đi ({{ $results['incidents']->count() }})</h3>
                        <div class="space-y-2">
                            @foreach($results['incidents'] as $incident)
                            <a href="{{ route('incidents.show', $incident) }}" class="block p-4 border rounded hover:bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold text-blue-600">Chuyến #{{ $incident->id }} - {{ $incident->vehicle->license_plate }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ $incident->date->format('d/m/Y H:i') }}
                                            @if($incident->patient)
                                                • {{ $incident->patient->name }}
                                            @endif
                                            @if($incident->destination)
                                                • {{ $incident->destination }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold {{ $incident->net_amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($incident->net_amount, 0, ',', '.') }}đ
                                        </p>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @endcan

                <!-- Transactions -->
                @can('search transactions')
                @if(isset($results['transactions']) && $results['transactions']->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Giao dịch ({{ $results['transactions']->count() }})</h3>
                        <div class="space-y-2">
                            @foreach($results['transactions'] as $transaction)
                            <a href="{{ route('transactions.show', $transaction) }}" class="block p-4 border rounded hover:bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold {{ $transaction->type == 'thu' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $transaction->type_label }} - {{ $transaction->vehicle->license_plate }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{ $transaction->date->format('d/m/Y H:i') }} • {{ $transaction->method_label }}
                                            @if($transaction->note)
                                                • {{ Str::limit($transaction->note, 50) }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold {{ $transaction->type == 'thu' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $transaction->type == 'thu' ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', '.') }}đ
                                        </p>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @endcan

                <!-- Notes -->
                @can('search notes')
                @if(isset($results['notes']) && $results['notes']->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Ghi chú ({{ $results['notes']->count() }})</h3>
                        <div class="space-y-2">
                            @foreach($results['notes'] as $note)
                            <div class="p-4 border rounded {{ $note->severity == 'critical' ? 'border-red-300 bg-red-50' : ($note->severity == 'warning' ? 'border-yellow-300 bg-yellow-50' : '') }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $note->severity_color }}">
                                                {{ $note->severity_label }}
                                            </span>
                                            @if($note->vehicle)
                                            <a href="{{ route('vehicles.show', $note->vehicle) }}" class="text-sm text-blue-600 hover:underline">
                                                {{ $note->vehicle->license_plate }}
                                            </a>
                                            @endif
                                            @if($note->incident)
                                            <a href="{{ route('incidents.show', $note->incident) }}" class="text-sm text-blue-600 hover:underline">
                                                Chuyến #{{ $note->incident->id }}
                                            </a>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-900">{{ $note->note }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $note->user->name }} • {{ $note->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @endcan

                @if(empty($results) || collect($results)->sum(fn($r) => $r->count()) == 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        Không tìm thấy kết quả nào cho "{{ $query }}"
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center text-gray-500">
                    Nhập từ khóa để tìm kiếm
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
