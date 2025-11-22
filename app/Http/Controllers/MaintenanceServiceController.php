<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceService;
use Illuminate\Http\Request;

class MaintenanceServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage settings'])->except(['autocomplete']);
    }

    public function index(Request $request)
    {
        $query = MaintenanceService::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('name')->paginate(15);

        return view('maintenance-services.index', compact('services'));
    }

    public function create()
    {
        return view('maintenance-services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $service = MaintenanceService::create($validated);

        return redirect()->route('maintenance-services.index')
            ->with('success', "Đã thêm loại dịch vụ {$service->name} thành công!");
    }

    public function edit(MaintenanceService $maintenanceService)
    {
        return view('maintenance-services.edit', compact('maintenanceService'));
    }

    public function update(Request $request, MaintenanceService $maintenanceService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $maintenanceService->update($validated);

        return redirect()->route('maintenance-services.index')
            ->with('success', "Đã cập nhật loại dịch vụ {$maintenanceService->name} thành công!");
    }

    public function destroy(MaintenanceService $maintenanceService)
    {
        $name = $maintenanceService->name;
        $maintenanceService->delete();

        return redirect()->route('maintenance-services.index')
            ->with('success', "Đã xóa loại dịch vụ {$name} thành công!");
    }

    public function autocomplete(Request $request)
    {
        $term = $request->input('term', '');

        $query = MaintenanceService::active();

        if ($term) {
            $query->where('name', 'like', "%{$term}%");
        }

        $services = $query->limit(10)->get(['id', 'name']);

        return response()->json($services);
    }
}
