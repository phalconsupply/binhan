<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view vehicles'])->only(['index', 'show']);
        $this->middleware(['auth', 'permission:create vehicles'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:edit vehicles'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:delete vehicles'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vehicle::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('license_plate', 'like', "%{$search}%")
                  ->orWhere('driver_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $vehicles = $query->orderBy('license_plate')->paginate(15);

        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate',
            'model' => 'nullable|string|max:100',
            'driver_name' => 'nullable|string|max:100',
            'driver_id' => 'nullable|exists:staff,id',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,maintenance',
            'note' => 'nullable|string',
        ]);

        $vehicle = Vehicle::create($validated);

        return redirect()->route('vehicles.index')
            ->with('success', "Đã thêm xe {$vehicle->license_plate} thành công!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        // Load relationships
        $vehicle->load([
            'incidents' => function($q) {
                $q->with(['patient', 'dispatcher'])->orderBy('date', 'desc')->limit(20);
            },
            'transactions' => function($q) {
                $q->orderBy('date', 'desc')->limit(20);
            },
        ]);

        // Statistics
        $stats = [
            'total_incidents' => $vehicle->incidents()->count(),
            'this_month_incidents' => $vehicle->incidents()->thisMonth()->count(),
            'total_revenue' => $vehicle->transactions()->revenue()->sum('amount'),
            'total_expense' => $vehicle->transactions()->expense()->sum('amount'),
            'month_revenue' => $vehicle->transactions()->revenue()->thisMonth()->sum('amount'),
            'month_expense' => $vehicle->transactions()->expense()->thisMonth()->sum('amount'),
        ];

        $stats['total_net'] = $stats['total_revenue'] - $stats['total_expense'];
        $stats['month_net'] = $stats['month_revenue'] - $stats['month_expense'];

        return view('vehicles.show', compact('vehicle', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate,' . $vehicle->id,
            'model' => 'nullable|string|max:100',
            'driver_name' => 'nullable|string|max:100',
            'driver_id' => 'nullable|exists:staff,id',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,maintenance',
            'note' => 'nullable|string',
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', "Đã cập nhật xe {$vehicle->license_plate} thành công!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $licensePlate = $vehicle->license_plate;
        
        // Check if vehicle has incidents
        if ($vehicle->incidents()->count() > 0) {
            return redirect()->route('vehicles.index')
                ->with('error', "Không thể xóa xe {$licensePlate} vì đã có chuyến đi liên quan!");
        }

        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', "Đã xóa xe {$licensePlate} thành công!");
    }
}
