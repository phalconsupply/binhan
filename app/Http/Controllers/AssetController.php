<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Vehicle;
use App\Models\Staff;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage settings']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Asset::with(['vehicle', 'staff', 'creator']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%");
            });
        }

        // Filter by usage type
        if ($request->filled('usage_type')) {
            $query->where('usage_type', $request->usage_type);
        }

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by staff
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $assets = $query->orderBy('equipped_date', 'desc')->paginate(20);

        // Get filter options
        $vehicles = Vehicle::orderBy('license_plate')->get();
        $staffs = Staff::orderBy('full_name')->get();

        // Statistics
        $statistics = [
            'total' => Asset::count(),
            'active' => Asset::active()->count(),
            'vehicle_assets' => Asset::forVehicle()->count(),
            'staff_assets' => Asset::forStaff()->count(),
            'total_quantity' => Asset::sum('quantity'),
        ];

        return view('assets.index', compact('assets', 'vehicles', 'staffs', 'statistics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::active()->orderBy('license_plate')->get();
        $staffs = Staff::where('is_active', true)->orderBy('full_name')->get();

        return view('assets.create', compact('vehicles', 'staffs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipped_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'brand' => 'nullable|string|max:255',
            'usage_type' => 'required|in:vehicle,staff',
            'vehicle_id' => 'required_if:usage_type,vehicle|nullable|exists:vehicles,id',
            'staff_id' => 'required_if:usage_type,staff|nullable|exists:staff,id',
            'note' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Clear unused relation
        if ($validated['usage_type'] === 'vehicle') {
            $validated['staff_id'] = null;
        } else {
            $validated['vehicle_id'] = null;
        }

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $asset = Asset::create($validated);

        activity()
            ->performedOn($asset)
            ->causedBy(auth()->user())
            ->log('Tạo tài sản mới: ' . $asset->name);

        return redirect()->route('assets.index')
            ->with('success', 'Tạo tài sản thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        $asset->load(['vehicle', 'staff', 'creator', 'updater']);

        return view('assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        $vehicles = Vehicle::orderBy('license_plate')->get();
        $staffs = Staff::orderBy('full_name')->get();

        return view('assets.edit', compact('asset', 'vehicles', 'staffs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipped_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'brand' => 'nullable|string|max:255',
            'usage_type' => 'required|in:vehicle,staff',
            'vehicle_id' => 'required_if:usage_type,vehicle|nullable|exists:vehicles,id',
            'staff_id' => 'required_if:usage_type,staff|nullable|exists:staff,id',
            'note' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Clear unused relation
        if ($validated['usage_type'] === 'vehicle') {
            $validated['staff_id'] = null;
        } else {
            $validated['vehicle_id'] = null;
        }

        $validated['updated_by'] = auth()->id();

        $asset->update($validated);

        activity()
            ->performedOn($asset)
            ->causedBy(auth()->user())
            ->log('Cập nhật tài sản: ' . $asset->name);

        return redirect()->route('assets.index')
            ->with('success', 'Cập nhật tài sản thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        $assetName = $asset->name;
        
        activity()
            ->performedOn($asset)
            ->causedBy(auth()->user())
            ->log('Xóa tài sản: ' . $assetName);

        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', "Đã xóa tài sản: {$assetName}");
    }
}
