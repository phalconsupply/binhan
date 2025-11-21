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
        $this->middleware(['auth', 'permission:view incidents'])->only(['index', 'show']);
        $this->middleware(['auth', 'permission:create incidents'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:edit incidents'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:delete incidents'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Incident::with(['vehicle', 'patient', 'dispatcher']);

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
            'patient_id' => 'nullable|exists:patients,id',
            'patient_name' => 'nullable|string|max:100',
            'patient_phone' => 'nullable|string|max:20',
            'patient_birth_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'patient_gender' => 'nullable|in:male,female,other',
            'patient_address' => 'nullable|string',
            'date' => 'required|date',
            'destination' => 'nullable|string|max:255',
            'summary' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Create or select patient
            $patientId = $validated['patient_id'] ?? null;
            
            if (!$patientId && !empty($validated['patient_name'])) {
                $patient = Patient::firstOrCreate(
                    [
                        'name' => $validated['patient_name'],
                        'phone' => $validated['patient_phone'] ?? null,
                    ],
                    [
                        'birth_year' => $validated['patient_birth_year'] ?? null,
                        'gender' => $validated['patient_gender'] ?? null,
                        'address' => $validated['patient_address'] ?? null,
                    ]
                );
                $patientId = $patient->id;
            }

            // Create incident
            $incident = Incident::create([
                'vehicle_id' => $validated['vehicle_id'],
                'patient_id' => $patientId,
                'date' => $validated['date'],
                'dispatch_by' => auth()->id(),
                'destination' => $validated['destination'],
                'summary' => $validated['summary'],
                'tags' => $validated['tags'] ?? null,
            ]);

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
        $incident->load([
            'vehicle',
            'patient',
            'dispatcher',
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

        return view('incidents.show', compact('incident', 'totals'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incident $incident)
    {
        $incident->load(['vehicle', 'patient']);
        
        $vehicles = Vehicle::orderBy('license_plate')->get();
        $patients = Patient::orderBy('name')->get();

        return view('incidents.edit', compact('incident', 'vehicles', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'patient_id' => 'nullable|exists:patients,id',
            'date' => 'required|date',
            'destination' => 'nullable|string|max:255',
            'summary' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        $incident->update($validated);

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Đã cập nhật chuyến đi thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incident)
    {
        // Check if incident has transactions
        if ($incident->transactions()->count() > 0) {
            return redirect()->route('incidents.index')
                ->with('error', 'Không thể xóa chuyến đi đã có giao dịch!');
        }

        $incident->delete();

        return redirect()->route('incidents.index')
            ->with('success', 'Đã xóa chuyến đi thành công!');
    }
}
