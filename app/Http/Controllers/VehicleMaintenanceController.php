<?php

namespace App\Http\Controllers;

use App\Models\VehicleMaintenance;
use App\Models\Vehicle;
use App\Models\MaintenanceService;
use App\Models\Partner;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VehicleMaintenancesExport;
use Barryvdh\DomPDF\Facade\Pdf;

class VehicleMaintenanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'owner_or_permission:manage vehicles']);
    }

    public function index(Request $request)
    {
        // Check if user is vehicle owner
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        $ownedVehicleIds = [];
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
        }

        $query = VehicleMaintenance::with(['vehicle', 'maintenanceService', 'partner', 'user']);

        // Scope to owner's vehicles if owner
        if ($isVehicleOwner && !empty($ownedVehicleIds)) {
            $query->whereIn('vehicle_id', $ownedVehicleIds);
        }

        if ($request->has('vehicle_id') && $request->vehicle_id) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('vehicle', function($q) use ($search) {
                $q->where('license_plate', 'like', "%{$search}%");
            });
        }

        // Calculate total cost based on current filters
        $totalCost = (clone $query)->sum('cost');

        $maintenances = $query->orderBy('date', 'desc')->paginate(15);
        $vehicles = Vehicle::orderBy('license_plate')->get();

        return view('vehicle-maintenances.index', compact('maintenances', 'vehicles', 'totalCost'));
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
            'maintenance_service_name' => 'nullable|string|max:255',
            'partner_id' => 'nullable|exists:partners,id',
            'partner_name' => 'nullable|string|max:255',
            'incident_id' => 'nullable|exists:incidents,id',
            'date' => 'required|date',
            'cost' => 'required|numeric|min:0',
            'mileage' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        // Create new maintenance service if name provided but no ID
        if (!$validated['maintenance_service_id'] && !empty($validated['maintenance_service_name'])) {
            $service = MaintenanceService::firstOrCreate(
                ['name' => $validated['maintenance_service_name']], // Search condition
                ['is_active' => true] // Additional attributes if creating
            );
            $validated['maintenance_service_id'] = $service->id;
        }

        // Create new partner if name provided but no ID
        if (!$validated['partner_id'] && !empty($validated['partner_name'])) {
            $partner = Partner::firstOrCreate(
                [
                    'name' => $validated['partner_name'],
                    'type' => 'maintenance'
                ], // Search condition
                ['is_active' => true] // Additional attributes if creating
            );
            $validated['partner_id'] = $partner->id;
        }

        $validated['user_id'] = auth()->id();

        $maintenance = VehicleMaintenance::create($validated);
        $vehicle = $maintenance->vehicle;

        // Create transaction for this maintenance
        $maintenance->createTransaction();

        $message = "Đã thêm lịch sử bảo trì cho xe {$vehicle->license_plate} thành công!";

        // Check action type
        if ($request->input('action') === 'save_and_continue') {
            return redirect()->route('vehicle-maintenances.create', ['vehicle_id' => $vehicle->id])
                ->with('success', $message);
        }

        return redirect()->route('vehicle-maintenances.index', ['vehicle_id' => $vehicle->id])
            ->with('success', $message);
    }

    public function edit(VehicleMaintenance $vehicleMaintenance)
    {
        // Check if user is vehicle owner and has access to this maintenance
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($vehicleMaintenance->vehicle_id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền chỉnh sửa bảo trì này.');
            }
        }

        $vehicles = Vehicle::orderBy('license_plate')->get();
        $services = MaintenanceService::active()->orderBy('name')->get();
        $partners = Partner::maintenancePartners()->active()->orderBy('name')->get();

        return view('vehicle-maintenances.edit', compact('vehicleMaintenance', 'vehicles', 'services', 'partners'));
    }

    public function update(Request $request, VehicleMaintenance $vehicleMaintenance)
    {
        // Check if user is vehicle owner and has access to this maintenance
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($vehicleMaintenance->vehicle_id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền cập nhật bảo trì này.');
            }
        }

        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_service_id' => 'nullable|exists:maintenance_services,id',
            'maintenance_service_name' => 'nullable|string|max:255',
            'partner_id' => 'nullable|exists:partners,id',
            'partner_name' => 'nullable|string|max:255',
            'incident_id' => 'nullable|exists:incidents,id',
            'date' => 'required|date',
            'cost' => 'required|numeric|min:0',
            'mileage' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        // Create new maintenance service if name provided but no ID
        if (!$validated['maintenance_service_id'] && !empty($validated['maintenance_service_name'])) {
            $service = MaintenanceService::firstOrCreate(
                ['name' => $validated['maintenance_service_name']], // Search condition
                ['is_active' => true] // Additional attributes if creating
            );
            $validated['maintenance_service_id'] = $service->id;
        }

        // Create new partner if name provided but no ID
        if (!$validated['partner_id'] && !empty($validated['partner_name'])) {
            $partner = Partner::firstOrCreate(
                [
                    'name' => $validated['partner_name'],
                    'type' => 'maintenance'
                ], // Search condition
                ['is_active' => true] // Additional attributes if creating
            );
            $validated['partner_id'] = $partner->id;
        }

        $vehicleMaintenance->update($validated);
        $vehicle = $vehicleMaintenance->vehicle;

        // Update or create transaction
        if ($vehicleMaintenance->transaction) {
            $transaction = $vehicleMaintenance->transaction;
            $hasOwner = $vehicle->hasOwner();
            
            $serviceName = $vehicleMaintenance->maintenanceService ? $vehicleMaintenance->maintenanceService->name : 'Bảo trì xe';
            $partnerName = $vehicleMaintenance->partner ? ' - ' . $vehicleMaintenance->partner->name : '';
            
            $note = "[Bảo trì] {$serviceName}{$partnerName}";
            if ($vehicleMaintenance->description) {
                $note .= " - {$vehicleMaintenance->description}";
            }

            $transaction->update([
                'vehicle_id' => $vehicleMaintenance->vehicle_id,
                'incident_id' => $vehicleMaintenance->incident_id,
                'category' => $hasOwner ? 'bảo_trì_xe_chủ_riêng' : 'bảo_trì_xe',
                'amount' => $vehicleMaintenance->cost,
                'note' => $note,
                'date' => $vehicleMaintenance->date,
            ]);
        } else {
            $vehicleMaintenance->createTransaction();
        }

        $message = "Đã cập nhật lịch sử bảo trì cho xe {$vehicle->license_plate} thành công!";

        // Check action type
        if ($request->input('action') === 'save_and_continue') {
            return redirect()->route('vehicle-maintenances.edit', $vehicleMaintenance)
                ->with('success', $message);
        }

        return redirect()->route('vehicle-maintenances.index', ['vehicle_id' => $vehicle->id])
            ->with('success', $message);
    }

    public function destroy(VehicleMaintenance $vehicleMaintenance)
    {
        // Check if user is vehicle owner and has access to this maintenance
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($vehicleMaintenance->vehicle_id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền xóa bảo trì này.');
            }
        }

        $vehicleId = $vehicleMaintenance->vehicle_id;
        
        // Delete associated transaction
        if ($vehicleMaintenance->transaction) {
            $vehicleMaintenance->transaction->delete();
        }
        
        $vehicleMaintenance->delete();

        return redirect()->route('vehicle-maintenances.index', ['vehicle_id' => $vehicleId])
            ->with('success', "Đã xóa lịch sử bảo trì thành công!");
    }

    /**
     * Search maintenance services for autocomplete
     */
    public function searchServices(Request $request)
    {
        $query = $request->input('q', '');
        
        $services = MaintenanceService::where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(function($service) {
                return [
                    'id' => $service->id,
                    'text' => $service->name
                ];
            });

        return response()->json([
            'results' => $services,
            'pagination' => ['more' => false]
        ]);
    }

    /**
     * Search partners for autocomplete
     */
    public function searchPartners(Request $request)
    {
        $query = $request->input('q', '');
        
        $partners = Partner::where('type', 'maintenance')
            ->where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(function($partner) {
                return [
                    'id' => $partner->id,
                    'text' => $partner->name
                ];
            });

        return response()->json([
            'results' => $partners,
            'pagination' => ['more' => false]
        ]);
    }

    /**
     * Export maintenances to Excel
     */
    public function exportExcel(Request $request)
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

        $maintenances = $query->orderBy('date', 'desc')->get();
        $totalCost = $maintenances->sum('cost');

        return Excel::download(
            new VehicleMaintenancesExport($maintenances, $totalCost), 
            'danh-sach-bao-tri-xe-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export maintenances to PDF
     */
    public function exportPdf(Request $request)
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

        $maintenances = $query->orderBy('date', 'desc')->get();
        $totalCost = $maintenances->sum('cost');

        $pdf = Pdf::loadView('vehicle-maintenances.pdf', compact('maintenances', 'totalCost'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('danh-sach-bao-tri-xe-' . now()->format('Y-m-d') . '.pdf');
    }
}
