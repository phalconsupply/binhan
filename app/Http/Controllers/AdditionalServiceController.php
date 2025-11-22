<?php

namespace App\Http\Controllers;

use App\Models\AdditionalService;
use Illuminate\Http\Request;

class AdditionalServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage settings'])->except(['autocomplete']);
    }

    public function index(Request $request)
    {
        $query = AdditionalService::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('name')->paginate(15);

        return view('additional-services.index', compact('services'));
    }

    public function create()
    {
        return view('additional-services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $service = AdditionalService::create($validated);

        return redirect()->route('additional-services.index')
            ->with('success', "Đã thêm dịch vụ {$service->name} thành công!");
    }

    public function edit(AdditionalService $additionalService)
    {
        return view('additional-services.edit', compact('additionalService'));
    }

    public function update(Request $request, AdditionalService $additionalService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $additionalService->update($validated);

        return redirect()->route('additional-services.index')
            ->with('success', "Đã cập nhật dịch vụ {$additionalService->name} thành công!");
    }

    public function destroy(AdditionalService $additionalService)
    {
        $name = $additionalService->name;
        $additionalService->delete();

        return redirect()->route('additional-services.index')
            ->with('success', "Đã xóa dịch vụ {$name} thành công!");
    }

    public function autocomplete(Request $request)
    {
        $term = $request->input('term', '');

        $query = AdditionalService::active();

        if ($term) {
            $query->where('name', 'like', "%{$term}%");
        }

        $services = $query->limit(10)->get(['id', 'name', 'default_price']);

        return response()->json($services);
    }
}
