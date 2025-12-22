<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Vehicle;
use App\Models\Patient;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncidentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'owner_or_permission:view incidents'])->only(['index', 'show']);
        $this->middleware(['auth', 'owner_or_permission:create incidents'])->only(['create', 'store']);
        $this->middleware(['auth', 'owner_or_permission:edit incidents'])->only(['edit', 'update']);
        $this->middleware(['auth', 'owner_or_permission:delete incidents'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Incident::with(['vehicle', 'patient', 'dispatcher']);

        // Check if user is vehicle owner and limit to their vehicles
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            $query->whereIn('vehicle_id', $ownedVehicleIds);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('vehicle', function($vq) use ($search) {
                    $vq->where('license_plate', 'like', "%{$search}%");
                })
                ->orWhereHas('patient', function($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhere('destination', 'like', "%{$search}%")
                ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        // Filter by vehicle
        if ($request->has('vehicle_id') && $request->vehicle_id != '') {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Default: Most recent first
        $query->orderBy('date', 'desc');

        $incidents = $query->paginate(20);

        // Get vehicles for filter dropdown
        $vehicles = Vehicle::orderBy('license_plate')->get();

        // Statistics
        $stats = [
            'total' => Incident::count(),
            'today' => Incident::today()->count(),
            'this_week' => Incident::thisWeek()->count(),
            'this_month' => Incident::thisMonth()->count(),
        ];

        return view('incidents.index', compact('incidents', 'vehicles', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::active()->orderBy('license_plate')->get();
        $patients = Patient::orderBy('name')->get();

        return view('incidents.create', compact('vehicles', 'patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'drivers' => 'nullable|array',
            'drivers.*.staff_id' => 'nullable|exists:staff,id',
            'drivers.*.wages' => 'nullable|array',
            'drivers.*.wages.*.type' => 'nullable|string',
            'drivers.*.wages.*.amount' => 'nullable|numeric|min:0',
            'medical_staff' => 'nullable|array',
            'medical_staff.*.staff_id' => 'nullable|exists:staff,id',
            'medical_staff.*.wages' => 'nullable|array',
            'medical_staff.*.wages.*.type' => 'nullable|string',
            'medical_staff.*.wages.*.amount' => 'nullable|numeric|min:0',
            'patient_id' => 'nullable|exists:patients,id',
            'patient_name' => 'nullable|string|max:100',
            'patient_phone' => 'nullable|string|max:20',
            'patient_birth_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'patient_gender' => 'nullable|in:male,female,other',
            'patient_address' => 'nullable|string',
            'date' => 'required|date',
            'from_location' => 'nullable|string|max:255',
            'to_location' => 'nullable|string|max:255',
            'partner_id' => 'nullable|exists:partners,id',
            'commission_amount' => 'nullable|numeric|min:0',
            'revenue_main_name' => 'nullable|string|max:255',
            'amount_thu' => 'nullable|numeric|min:0',
            'expense_main_name' => 'nullable|string|max:255',
            'amount_chi' => 'nullable|numeric|min:0',
            'additional_services' => 'nullable|array',
            'additional_services.*.name' => 'required|string|max:255',
            'additional_services.*.amount' => 'required|numeric|min:0',
            'incident_services' => 'nullable|array',
            'incident_services.*.service_name' => 'required|string|max:255',
            'incident_services.*.amount' => 'required|numeric|min:0',
            'incident_services.*.note' => 'nullable|string',
            'additional_expenses' => 'nullable|array',
            'additional_expenses.*.name' => 'required|string|max:255',
            'additional_expenses.*.amount' => 'required|numeric|min:0',
            'maintenance_partner_id' => 'nullable|exists:partners,id',
            'maintenance_service' => 'nullable|string|max:255',
            'maintenance_cost' => 'nullable|numeric|min:0',
            'maintenance_mileage' => 'nullable|integer|min:0',
            'maintenance_note' => 'nullable|string',
            'payment_method' => 'required|in:cash,bank,other',
            'summary' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Find or create locations based on names (case-insensitive)
            $fromLocationId = null;
            $toLocationId = null;
            
            if (!empty($validated['from_location'])) {
                $normalizedName = trim($validated['from_location']);
                
                // Search case-insensitive first
                $location = \App\Models\Location::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)])
                    ->first();
                
                if (!$location) {
                    // Create new if not found
                    $location = \App\Models\Location::create([
                        'name' => $normalizedName,
                        'type' => 'from',
                        'is_active' => true
                    ]);
                }
                
                $fromLocationId = $location->id;
            }
            
            if (!empty($validated['to_location'])) {
                $normalizedName = trim($validated['to_location']);
                
                // Search case-insensitive first
                $location = \App\Models\Location::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)])
                    ->first();
                
                if (!$location) {
                    // Create new if not found
                    $location = \App\Models\Location::create([
                        'name' => $normalizedName,
                        'type' => 'to',
                        'is_active' => true
                    ]);
                }
                
                $toLocationId = $location->id;
            }

            // Create or select patient (case-insensitive, handle NULL phone)
            $patientId = $validated['patient_id'] ?? null;
            
            if (!$patientId && !empty($validated['patient_name'])) {
                $normalizedName = trim($validated['patient_name']);
                $normalizedPhone = !empty($validated['patient_phone']) ? trim($validated['patient_phone']) : null;
                
                // Search case-insensitive
                $patientQuery = Patient::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)]);
                
                // Match phone if provided
                if ($normalizedPhone) {
                    $patientQuery->where('phone', $normalizedPhone);
                } else {
                    // If no phone provided, find any patient with this name
                    $patientQuery->whereNull('phone');
                }
                
                $patient = $patientQuery->first();
                
                if (!$patient) {
                    // Create new patient
                    $patient = Patient::create([
                        'name' => $normalizedName,
                        'phone' => $normalizedPhone,
                        'birth_year' => $validated['patient_birth_year'] ?? null,
                        'gender' => $validated['patient_gender'] ?? null,
                        'address' => $validated['patient_address'] ?? null,
                    ]);
                }
                
                $patientId = $patient->id;
            }

            // Create incident
            $incident = Incident::create([
                'vehicle_id' => $validated['vehicle_id'],
                'patient_id' => $patientId,
                'date' => $validated['date'],
                'dispatch_by' => auth()->id(),
                'from_location_id' => $fromLocationId,
                'to_location_id' => $toLocationId,
                'partner_id' => $validated['partner_id'] ?? null,
                'commission_amount' => $validated['commission_amount'] ?? null,
                'summary' => $validated['summary'],
                'tags' => $validated['tags'] ?? null,
            ]);

            // Attach staff to incident (drivers and medical staff) with wages
            if (!empty($validated['drivers'])) {
                foreach ($validated['drivers'] as $driver) {
                    if (!empty($driver['staff_id'])) {
                        $wages = $driver['wages'] ?? [];
                        $totalWage = 0;
                        $wageDetails = [];
                        
                        // Calculate total and build details array
                        foreach ($wages as $wage) {
                            if (!empty($wage['amount']) && $wage['amount'] > 0) {
                                $totalWage += $wage['amount'];
                                $wageDetails[] = [
                                    'type' => $wage['type'] ?? 'Công',
                                    'amount' => $wage['amount']
                                ];
                            }
                        }
                        
                        $incident->staff()->attach($driver['staff_id'], [
                            'role' => 'driver',
                            'wage_amount' => $totalWage,
                            'wage_details' => !empty($wageDetails) ? json_encode($wageDetails) : null
                        ]);

                        // Create wage transactions for each type
                        if (!empty($wageDetails)) {
                            $staffMember = \App\Models\Staff::find($driver['staff_id']);
                            foreach ($wageDetails as $detail) {
                                Transaction::create([
                                    'incident_id' => $incident->id,
                                    'vehicle_id' => $validated['vehicle_id'],
                                    'staff_id' => $driver['staff_id'],
                                    'type' => 'chi',
                                    'amount' => $detail['amount'],
                                    'method' => $validated['payment_method'],
                                    'recorded_by' => auth()->id(),
                                    'date' => $validated['date'],
                                    'note' => $detail['type'] . ' lái xe: ' . ($staffMember ? $staffMember->full_name : ''),
                                ]);
                            }
                        }
                    }
                }
            }
            
            if (!empty($validated['medical_staff'])) {
                foreach ($validated['medical_staff'] as $staff) {
                    if (!empty($staff['staff_id'])) {
                        $wages = $staff['wages'] ?? [];
                        $totalWage = 0;
                        $wageDetails = [];
                        
                        // Calculate total and build details array
                        foreach ($wages as $wage) {
                            if (!empty($wage['amount']) && $wage['amount'] > 0) {
                                $totalWage += $wage['amount'];
                                $wageDetails[] = [
                                    'type' => $wage['type'] ?? 'Công',
                                    'amount' => $wage['amount']
                                ];
                            }
                        }
                        
                        $incident->staff()->attach($staff['staff_id'], [
                            'role' => 'medical_staff',
                            'wage_amount' => $totalWage,
                            'wage_details' => !empty($wageDetails) ? json_encode($wageDetails) : null
                        ]);

                        // Create wage transactions for each type
                        if (!empty($wageDetails)) {
                            $staffMember = \App\Models\Staff::find($staff['staff_id']);
                            foreach ($wageDetails as $detail) {
                                Transaction::create([
                                    'incident_id' => $incident->id,
                                    'vehicle_id' => $validated['vehicle_id'],
                                    'staff_id' => $staff['staff_id'],
                                    'type' => 'chi',
                                    'amount' => $detail['amount'],
                                    'method' => $validated['payment_method'],
                                    'recorded_by' => auth()->id(),
                                    'date' => $validated['date'],
                                    'note' => $detail['type'] . ' NVYT: ' . ($staffMember ? $staffMember->full_name : ''),
                                ]);
                            }
                        }
                    }
                }
            }

            // Create main revenue transaction
            if (!empty($validated['amount_thu']) && $validated['amount_thu'] > 0) {
                Transaction::create([
                    'incident_id' => $incident->id,
                    'vehicle_id' => $validated['vehicle_id'],
                    'type' => 'thu',
                    'amount' => $validated['amount_thu'],
                    'method' => $validated['payment_method'],
                    'recorded_by' => auth()->id(),
                    'date' => $validated['date'],
                    'note' => $validated['revenue_main_name'] ?? 'Thu chuyến đi',
                ]);
            }

            // Create additional services revenue (legacy support)
            if (!empty($validated['additional_services'])) {
                foreach ($validated['additional_services'] as $service) {
                    if (!empty($service['name']) && !empty($service['amount'])) {
                        $additionalService = \App\Models\AdditionalService::where('name', $service['name'])->first();
                        
                        \App\Models\IncidentAdditionalService::create([
                            'incident_id' => $incident->id,
                            'additional_service_id' => $additionalService->id ?? null,
                            'service_name' => $service['name'],
                            'amount' => $service['amount'],
                        ]);

                        Transaction::create([
                            'incident_id' => $incident->id,
                            'vehicle_id' => $validated['vehicle_id'],
                            'type' => 'thu',
                            'amount' => $service['amount'],
                            'method' => $validated['payment_method'],
                            'recorded_by' => auth()->id(),
                            'date' => $validated['date'],
                            'note' => 'Thu dịch vụ: ' . $service['name'],
                        ]);
                    }
                }
            }

            // Create incident services (new dedicated section)
            if (!empty($validated['incident_services'])) {
                foreach ($validated['incident_services'] as $service) {
                    if (!empty($service['service_name']) && !empty($service['amount'])) {
                        // Find matching additional service
                        $additionalService = \App\Models\AdditionalService::where('name', $service['service_name'])->first();
                        
                        \App\Models\IncidentAdditionalService::create([
                            'incident_id' => $incident->id,
                            'additional_service_id' => $additionalService->id ?? null,
                            'service_name' => $service['service_name'],
                            'amount' => $service['amount'],
                            'note' => $service['note'] ?? null,
                        ]);

                        Transaction::create([
                            'incident_id' => $incident->id,
                            'vehicle_id' => $validated['vehicle_id'],
                            'type' => 'thu',
                            'amount' => $service['amount'],
                            'method' => $validated['payment_method'],
                            'recorded_by' => auth()->id(),
                            'date' => $validated['date'],
                            'note' => 'Dịch vụ: ' . $service['service_name'],
                        ]);
                    }
                }
            }

            // Create main expense transaction
            if (!empty($validated['amount_chi']) && $validated['amount_chi'] > 0) {
                Transaction::create([
                    'incident_id' => $incident->id,
                    'vehicle_id' => $validated['vehicle_id'],
                    'type' => 'chi',
                    'amount' => $validated['amount_chi'],
                    'method' => $validated['payment_method'],
                    'recorded_by' => auth()->id(),
                    'date' => $validated['date'],
                    'note' => $validated['expense_main_name'] ?? 'Chi phí chuyến đi',
                ]);
            }

            // Create additional expenses
            if (!empty($validated['additional_expenses'])) {
                foreach ($validated['additional_expenses'] as $expense) {
                    if (!empty($expense['name']) && !empty($expense['amount'])) {
                        Transaction::create([
                            'incident_id' => $incident->id,
                            'vehicle_id' => $validated['vehicle_id'],
                            'type' => 'chi',
                            'amount' => $expense['amount'],
                            'method' => $validated['payment_method'],
                            'recorded_by' => auth()->id(),
                            'date' => $validated['date'],
                            'note' => 'Chi phí: ' . $expense['name'],
                        ]);
                    }
                }
            }

            // Create maintenance record if provided
            if (!empty($validated['maintenance_service']) && !empty($validated['maintenance_cost'])) {
                $maintenanceService = \App\Models\MaintenanceService::firstOrCreate(
                    ['name' => $validated['maintenance_service']],
                    ['is_active' => true]
                );

                \App\Models\VehicleMaintenance::create([
                    'vehicle_id' => $validated['vehicle_id'],
                    'incident_id' => $incident->id,
                    'partner_id' => $validated['maintenance_partner_id'] ?? null,
                    'maintenance_service_id' => $maintenanceService->id,
                    'date' => $validated['date'],
                    'mileage' => $validated['maintenance_mileage'] ?? null,
                    'cost' => $validated['maintenance_cost'],
                    'note' => $validated['maintenance_note'] ?? 'Bảo trì phát sinh trong chuyến đi',
                    'user_id' => auth()->id(),
                ]);

                Transaction::create([
                    'incident_id' => $incident->id,
                    'vehicle_id' => $validated['vehicle_id'],
                    'type' => 'chi',
                    'amount' => $validated['maintenance_cost'],
                    'method' => $validated['payment_method'],
                    'recorded_by' => auth()->id(),
                    'date' => $validated['date'],
                    'note' => 'Bảo trì: ' . $validated['maintenance_service'],
                ]);
            }

            // Create commission expense transaction if partner exists
            if (!empty($validated['partner_id']) && !empty($validated['commission_amount']) && $validated['commission_amount'] > 0) {
                $partner = \App\Models\Partner::find($validated['partner_id']);
                Transaction::create([
                    'incident_id' => $incident->id,
                    'vehicle_id' => $validated['vehicle_id'],
                    'type' => 'chi',
                    'amount' => $validated['commission_amount'],
                    'method' => $validated['payment_method'],
                    'recorded_by' => auth()->id(),
                    'date' => $validated['date'],
                    'note' => 'Hoa hồng: ' . ($partner ? $partner->name : 'Đối tác'),
                ]);
            }

            DB::commit();

            return redirect()->route('incidents.show', $incident)
                ->with('success', 'Đã tạo chuyến đi thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Incident $incident)
    {
        // Check if user is vehicle owner and has access to this incident
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($incident->vehicle_id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền xem chuyến đi này.');
            }
        }

        $incident->load([
            'vehicle',
            'patient',
            'dispatcher',
            'staff',
            'drivers',
            'medicalStaff',
            'transactions' => function($q) {
                $q->with('recorder')->orderBy('date', 'desc');
            },
            'notes' => function($q) {
                $q->with('user')->orderBy('created_at', 'desc');
            }
        ]);

        // Calculate totals
        $totals = [
            'revenue' => $incident->transactions()->where('type', 'thu')->sum('amount'),
            'expense' => $incident->transactions()->where('type', 'chi')->sum('amount'),
        ];
        $totals['net'] = $totals['revenue'] - $totals['expense'];
        
        // Calculate management fee (15%) for vehicles with owners
        $totals['has_owner'] = $incident->vehicle && $incident->vehicle->hasOwner();
        if ($totals['has_owner'] && $totals['net'] > 0) {
            $totals['management_fee'] = $totals['net'] * 0.15;
            $totals['profit_after_fee'] = $totals['net'] - $totals['management_fee'];
        } else {
            $totals['management_fee'] = 0;
            $totals['profit_after_fee'] = $totals['net'];
        }

        return view('incidents.show', compact('incident', 'totals'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incident $incident)
    {
        // Check if user is vehicle owner and has access to this incident
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($incident->vehicle_id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền chỉnh sửa chuyến đi này.');
            }
        }

        $incident->load(['vehicle', 'patient', 'drivers', 'medicalStaff', 'transactions']);
        
        // Sync wage amounts from actual transactions for display accuracy
        // This ensures edit form shows real wage data, not stale pivot table data
        foreach ($incident->drivers as $driver) {
            $actualWage = $incident->transactions()
                ->where('staff_id', $driver->id)
                ->where('type', 'chi')
                ->sum('amount');
            $driver->pivot->actual_wage = $actualWage;
        }
        
        foreach ($incident->medicalStaff as $staff) {
            $actualWage = $incident->transactions()
                ->where('staff_id', $staff->id)
                ->where('type', 'chi')
                ->sum('amount');
            $staff->pivot->actual_wage = $actualWage;
        }
        
        $vehicles = Vehicle::orderBy('license_plate')->get();
        $patients = Patient::orderBy('name')->get();

        return view('incidents.edit', compact('incident', 'vehicles', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Incident $incident)
    {
        // Check if user is vehicle owner and has access to this incident
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($incident->vehicle_id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền cập nhật chuyến đi này.');
            }
        }

        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'drivers' => 'nullable|array',
            'drivers.*.staff_id' => 'nullable|exists:staff,id',
            'drivers.*.wage' => 'nullable|numeric|min:0',
            'medical_staff' => 'nullable|array',
            'medical_staff.*.staff_id' => 'nullable|exists:staff,id',
            'medical_staff.*.wage' => 'nullable|numeric|min:0',
            'patient_id' => 'nullable|exists:patients,id',
            'date' => 'required|date',
            'from_location' => 'nullable|string|max:255',
            'to_location' => 'nullable|string|max:255',
            'partner_id' => 'nullable|exists:partners,id',
            'commission_amount' => 'nullable|numeric|min:0',
            'existing_services' => 'nullable|array',
            'existing_services.*.id' => 'required|exists:incident_additional_services,id',
            'existing_services.*.service_name' => 'required|string|max:255',
            'existing_services.*.amount' => 'required|numeric|min:0',
            'existing_services.*.note' => 'nullable|string',
            'new_services' => 'nullable|array',
            'new_services.*.service_name' => 'required|string|max:255',
            'new_services.*.amount' => 'required|numeric|min:0',
            'new_services.*.note' => 'nullable|string',
            'services_to_delete' => 'nullable|string',
            'summary' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Find or create locations based on names (case-insensitive)
            $fromLocationId = null;
            $toLocationId = null;
            
            if (!empty($validated['from_location'])) {
                $normalizedName = trim($validated['from_location']);
                
                // Search case-insensitive first
                $location = \App\Models\Location::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)])
                    ->first();
                
                if (!$location) {
                    // Create new if not found
                    $location = \App\Models\Location::create([
                        'name' => $normalizedName,
                        'type' => 'from',
                        'is_active' => true
                    ]);
                }
                
                $fromLocationId = $location->id;
            }
            
            if (!empty($validated['to_location'])) {
                $normalizedName = trim($validated['to_location']);
                
                // Search case-insensitive first
                $location = \App\Models\Location::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)])
                    ->first();
                
                if (!$location) {
                    // Create new if not found
                    $location = \App\Models\Location::create([
                        'name' => $normalizedName,
                        'type' => 'to',
                        'is_active' => true
                    ]);
                }
                
                $toLocationId = $location->id;
            }

            $incident->update([
                'vehicle_id' => $validated['vehicle_id'],
                'patient_id' => $validated['patient_id'],
                'date' => $validated['date'],
                'from_location_id' => $fromLocationId,
                'to_location_id' => $toLocationId,
                'partner_id' => $validated['partner_id'] ?? null,
                'commission_amount' => $validated['commission_amount'] ?? null,
                'summary' => $validated['summary'],
                'tags' => $validated['tags'] ?? null,
            ]);

            // Sync staff assignments
            // Remove all existing staff
            $incident->staff()->detach();
            
            // Delete ALL old wage transactions for this incident (by staff_id)
            // This ensures we don't create duplicates regardless of note format
            Transaction::where('incident_id', $incident->id)
                ->whereNotNull('staff_id')
                ->delete();
            
            // Attach drivers with wages
            if (!empty($validated['drivers'])) {
                foreach ($validated['drivers'] as $driver) {
                    // Skip if staff_id is empty (unselected dropdown)
                    if (empty($driver['staff_id'])) {
                        continue;
                    }
                    
                    $wageAmount = !empty($driver['wage']) ? (float)$driver['wage'] : null;
                    
                    $incident->staff()->attach($driver['staff_id'], [
                        'role' => 'driver',
                        'wage_amount' => $wageAmount
                    ]);
                    
                    // Create wage transaction if wage is provided
                    if ($wageAmount && $wageAmount > 0) {
                        $staff = \App\Models\Staff::find($driver['staff_id']);
                        Transaction::create([
                            'incident_id' => $incident->id,
                            'vehicle_id' => $validated['vehicle_id'],
                            'staff_id' => $driver['staff_id'],
                            'type' => 'chi',
                            'amount' => $wageAmount,
                            'method' => 'cash',
                            'recorded_by' => auth()->id(),
                            'date' => $validated['date'],
                            'note' => 'Tiền công lái xe: ' . ($staff ? $staff->full_name : 'Lái xe'),
                        ]);
                    }
                }
            }
            
            // Attach medical staff with wages
            if (!empty($validated['medical_staff'])) {
                foreach ($validated['medical_staff'] as $medicalStaff) {
                    // Skip if staff_id is empty (unselected dropdown)
                    if (empty($medicalStaff['staff_id'])) {
                        continue;
                    }
                    
                    $wageAmount = !empty($medicalStaff['wage']) ? (float)$medicalStaff['wage'] : null;
                    
                    $incident->staff()->attach($medicalStaff['staff_id'], [
                        'role' => 'medical_staff',
                        'wage_amount' => $wageAmount
                    ]);
                    
                    // Create wage transaction if wage is provided
                    if ($wageAmount && $wageAmount > 0) {
                        $staff = \App\Models\Staff::find($medicalStaff['staff_id']);
                        Transaction::create([
                            'incident_id' => $incident->id,
                            'vehicle_id' => $validated['vehicle_id'],
                            'staff_id' => $medicalStaff['staff_id'],
                            'type' => 'chi',
                            'amount' => $wageAmount,
                            'method' => 'cash',
                            'recorded_by' => auth()->id(),
                            'date' => $validated['date'],
                            'note' => 'Tiền công nhân viên y tế: ' . ($staff ? $staff->full_name : 'Nhân viên y tế'),
                        ]);
                    }
                }
            }

            // Update commission transaction
            // First, delete old commission transaction if exists
            Transaction::where('incident_id', $incident->id)
                ->where('note', 'LIKE', 'Hoa hồng:%')
                ->delete();

            // Create new commission transaction if partner exists
            if (!empty($validated['partner_id']) && !empty($validated['commission_amount']) && $validated['commission_amount'] > 0) {
                $partner = \App\Models\Partner::find($validated['partner_id']);
                Transaction::create([
                    'incident_id' => $incident->id,
                    'vehicle_id' => $validated['vehicle_id'],
                    'type' => 'chi',
                    'amount' => $validated['commission_amount'],
                    'method' => 'cash', // Default method for commission
                    'recorded_by' => auth()->id(),
                    'date' => $validated['date'],
                    'note' => 'Hoa hồng: ' . ($partner ? $partner->name : 'Đối tác'),
                ]);
            }

            // === UPDATE INCIDENT SERVICES ===
            
            // 1. Delete marked services and their transactions
            if (!empty($validated['services_to_delete'])) {
                $servicesToDelete = json_decode($validated['services_to_delete'], true);
                if (is_array($servicesToDelete)) {
                    foreach ($servicesToDelete as $serviceId) {
                        $service = \App\Models\IncidentAdditionalService::find($serviceId);
                        if ($service && $service->incident_id == $incident->id) {
                            // Delete related transaction
                            Transaction::where('incident_id', $incident->id)
                                ->where('note', 'LIKE', '%Dịch vụ: ' . $service->service_name . '%')
                                ->delete();
                            
                            // Delete service
                            $service->delete();
                        }
                    }
                }
            }
            
            // 2. Update existing services
            if (!empty($validated['existing_services'])) {
                foreach ($validated['existing_services'] as $serviceData) {
                    $service = \App\Models\IncidentAdditionalService::find($serviceData['id']);
                    if ($service && $service->incident_id == $incident->id) {
                        $oldName = $service->service_name;
                        $oldAmount = $service->amount;
                        
                        // Update service
                        $additionalService = \App\Models\AdditionalService::where('name', $serviceData['service_name'])->first();
                        $service->update([
                            'additional_service_id' => $additionalService->id ?? $service->additional_service_id,
                            'service_name' => $serviceData['service_name'],
                            'amount' => $serviceData['amount'],
                            'note' => $serviceData['note'] ?? null,
                        ]);
                        
                        // Update or recreate transaction if amount/name changed
                        if ($oldAmount != $serviceData['amount'] || $oldName != $serviceData['service_name']) {
                            Transaction::where('incident_id', $incident->id)
                                ->where('note', 'LIKE', '%Dịch vụ: ' . $oldName . '%')
                                ->delete();
                            
                            Transaction::create([
                                'incident_id' => $incident->id,
                                'vehicle_id' => $validated['vehicle_id'],
                                'type' => 'thu',
                                'amount' => $serviceData['amount'],
                                'method' => 'cash',
                                'recorded_by' => auth()->id(),
                                'date' => $validated['date'],
                                'note' => 'Dịch vụ: ' . $serviceData['service_name'],
                            ]);
                        }
                    }
                }
            }
            
            // 3. Add new services
            if (!empty($validated['new_services'])) {
                foreach ($validated['new_services'] as $serviceData) {
                    if (!empty($serviceData['service_name']) && !empty($serviceData['amount'])) {
                        $additionalService = \App\Models\AdditionalService::where('name', $serviceData['service_name'])->first();
                        
                        \App\Models\IncidentAdditionalService::create([
                            'incident_id' => $incident->id,
                            'additional_service_id' => $additionalService->id ?? null,
                            'service_name' => $serviceData['service_name'],
                            'amount' => $serviceData['amount'],
                            'note' => $serviceData['note'] ?? null,
                        ]);
                        
                        Transaction::create([
                            'incident_id' => $incident->id,
                            'vehicle_id' => $validated['vehicle_id'],
                            'type' => 'thu',
                            'amount' => $serviceData['amount'],
                            'method' => 'cash',
                            'recorded_by' => auth()->id(),
                            'date' => $validated['date'],
                            'note' => 'Dịch vụ: ' . $serviceData['service_name'],
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('incidents.show', $incident)
                ->with('success', 'Đã cập nhật chuyến đi thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incident)
    {
        // Check if user is vehicle owner and has access to this incident
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            if (!in_array($incident->vehicle_id, $ownedVehicleIds)) {
                abort(403, 'Bạn không có quyền xóa chuyến đi này.');
            }
        }

        // Check if incident has transactions
        if ($incident->transactions()->count() > 0) {
            return redirect()->route('incidents.index')
                ->with('error', 'Không thể xóa chuyến đi đã có giao dịch!');
        }

        $incident->delete();

        return redirect()->route('incidents.index')
            ->with('success', 'Đã xóa chuyến đi thành công!');
    }

    /**
     * Search incidents for Select2 autocomplete
     */
    public function search(Request $request)
    {
        $searchTerm = $request->input('q');
        $page = $request->input('page', 1);
        $perPage = 20;

        $query = Incident::with(['patient', 'vehicle'])
            ->where(function($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                  ->orWhereHas('patient', function($pq) use ($searchTerm) {
                      $pq->where('name', 'like', "%{$searchTerm}%")
                         ->orWhere('phone', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('vehicle', function($vq) use ($searchTerm) {
                      $vq->where('license_plate', 'like', "%{$searchTerm}%");
                  })
                  ->orWhere('destination', 'like', "%{$searchTerm}%");
            })
            ->orderBy('date', 'desc');

        $total = $query->count();
        $incidents = $query->skip(($page - 1) * $perPage)
                           ->take($perPage)
                           ->get();

        $results = $incidents->map(function($incident) {
            return [
                'id' => $incident->id,
                'text' => "#" . $incident->id . " - " . ($incident->patient->name ?? 'N/A'),
                'patient_name' => $incident->patient->name ?? 'N/A',
                'vehicle_plate' => $incident->vehicle->license_plate ?? 'N/A',
                'date' => $incident->date->format('d/m/Y'),
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }

    /**
     * Export incidents to Excel
     */
    public function export(Request $request)
    {
        // Check if user is vehicle owner and only allow export their own vehicles
        $isVehicleOwner = \App\Models\Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        // Get filters from current page request (same as index filters)
        $filters = [
            'search' => $request->input('search'),
            'vehicle_id' => $request->input('vehicle_id'),
            'status' => $request->input('status'),
            'date' => $request->input('date'),
        ];

        // If vehicle owner, limit to their vehicles
        if ($isVehicleOwner) {
            $ownedVehicleIds = \App\Models\Staff::where('user_id', auth()->id())
                ->where('staff_type', 'vehicle_owner')
                ->pluck('vehicle_id')
                ->filter()
                ->toArray();
            
            // If a specific vehicle is selected, verify ownership
            if (!empty($filters['vehicle_id'])) {
                if (!in_array($filters['vehicle_id'], $ownedVehicleIds)) {
                    abort(403, 'Bạn không có quyền xuất dữ liệu xe này.');
                }
            } else {
                // Set filter to owned vehicles if not specified
                $filters['owned_vehicle_ids'] = $ownedVehicleIds;
            }
        }

        $fileName = 'danh-sach-chuyen-di-' . now()->format('Y-m-d-His') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\IncidentsIndexExport($filters), $fileName);
    }
}

