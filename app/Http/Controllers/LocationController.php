<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage settings'])->except(['autocomplete']);
    }

    public function index(Request $request)
    {
        $query = Location::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        $locations = $query->orderBy('name')->paginate(15);

        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:from,to,both',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $location = Location::create($validated);

        return redirect()->route('locations.index')
            ->with('success', "Đã thêm địa điểm {$location->name} thành công!");
    }

    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:from,to,both',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $location->update($validated);

        return redirect()->route('locations.index')
            ->with('success', "Đã cập nhật địa điểm {$location->name} thành công!");
    }

    public function destroy(Location $location)
    {
        $name = $location->name;
        $location->delete();

        return redirect()->route('locations.index')
            ->with('success', "Đã xóa địa điểm {$name} thành công!");
    }

    public function autocomplete(Request $request)
    {
        $term = $request->input('term', '');
        $type = $request->input('type', 'both'); // 'from', 'to', or 'both'

        $query = Location::active();

        if ($type != 'both') {
            $query->where(function($q) use ($type) {
                $q->where('type', $type)->orWhere('type', 'both');
            });
        }

        if ($term) {
            $query->where('name', 'like', "%{$term}%");
        }

        $locations = $query->limit(10)->get(['id', 'name', 'address']);

        return response()->json($locations);
    }
}
