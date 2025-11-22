<?php

namespace App\Http\Controllers;

use App\Models\VehicleMaintenance;
use App\Models\Vehicle;
use App\Models\MaintenanceService;
use App\Models\Partner;
use Illuminate\Http\Request;

class VehicleMaintenanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage vehicles']);
    }

    public function index(Request $request)
    {
        $query = VehicleMaintenance::with(['vehicle', 'maintenanceService', 'partner', 'user']);

        if ($request->has('vehicle_id') && $request->vehicle_id) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('vehicle', function($q) use ($search) {
                $q->where('license_plate', 'like', "%{$search}%");
            });
        }

        $maintenances = $query->orderBy('date', 'desc')->paginate(15);
        $vehicles = Vehicle::orderBy('license_plate')->get();

        return view('vehicle-maintenances.index', compact('maintenances', 'vehicles'));
    }

    public function create(Request $request)
    {
        $vehicles = Vehicle::orderBy('license_plate')->get();
        $services = MaintenanceService::active()->orderBy('name')->get();
        $partners = Partner::maintenancePartners()->active()->orderBy('name')->get();
        
        $vehicleId = $request->query('vehicle_id');

        return view('vehicle-maintenances.create', compact('vehicles', 'services', 'partners', 'vehicleId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_service_id' => 'nullable|exists:maintenance_services,id',
            'partner_id' => 'nullable|exists:partners,id',
            'incident_id' => 'nullable|exists:incidents,id',
            'date' => 'required|date',
            'cost' => 'required|numeric|min:0',
            'mileage' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();

        $maintenance = VehicleMaintenance::create($validated);
        $vehicle = $maintenance->vehicle;

        return redirect()->route('vehicle-maintenances.index', ['vehicle_id' => $vehicle->id])
            ->with('success', "Đã thêm lịch sử bảo trì cho xe {$vehicle->license_plate} thành công!");
    }

    public function edit(VehicleMaintenance $vehicleMaintenance)
    {
        $vehicles = Vehicle::orderBy('license_plate')->get();
        $services = MaintenanceService::active()->orderBy('name')->get();
        $partners = Partner::maintenancePartners()->active()->orderBy('name')->get();

        return view('vehicle-maintenances.edit', compact('vehicleMaintenance', 'vehicles', 'services', 'partners'));
    }

    public function update(Request $request, VehicleMaintenance $vehicleMaintenance)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_service_id' => 'nullable|exists:maintenance_services,id',
            'partner_id' => 'nullable|exists:partners,id',
            'incident_id' => 'nullable|exists:incidents,id',
            'date' => 'required|date',
            'cost' => 'required|numeric|min:0',
            'mileage' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $vehicleMaintenance->update($validated);
        $vehicle = $vehicleMaintenance->vehicle;

        return redirect()->route('vehicle-maintenances.index', ['vehicle_id' => $vehicle->id])
            ->with('success', "Đã cập nhật lịch sử bảo trì cho xe {$vehicle->license_plate} thành công!");
    }

    public function destroy(VehicleMaintenance $vehicleMaintenance)
    {
        $vehicleId = $vehicleMaintenance->vehicle_id;
        $vehicleMaintenance->delete();

        return redirect()->route('vehicle-maintenances.index', ['vehicle_id' => $vehicleId])
            ->with('success', "Đã xóa lịch sử bảo trì thành công!");
    }
}
