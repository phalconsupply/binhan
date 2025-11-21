<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Incident;
use App\Models\Transaction;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $stats = [
            'total_vehicles' => Vehicle::count(),
            'active_vehicles' => Vehicle::active()->count(),
            'today_incidents' => Incident::today()->count(),
            'today_revenue' => Transaction::revenue()->today()->sum('amount'),
            'today_expense' => Transaction::expense()->today()->sum('amount'),
            'month_revenue' => Transaction::revenue()->thisMonth()->sum('amount'),
            'month_expense' => Transaction::expense()->thisMonth()->sum('amount'),
        ];

        $stats['today_net'] = $stats['today_revenue'] - $stats['today_expense'];
        $stats['month_net'] = $stats['month_revenue'] - $stats['month_expense'];

        // Recent incidents (last 10)
        $recentIncidents = Incident::with(['vehicle', 'patient', 'dispatcher'])
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        // Today's incidents
        $todayIncidents = Incident::with(['vehicle', 'patient'])
            ->today()
            ->orderBy('date', 'desc')
            ->get();

        // Active vehicles for dropdown
        $vehicles = Vehicle::active()
            ->orderBy('license_plate')
            ->get();

        return view('dashboard', compact('stats', 'recentIncidents', 'todayIncidents', 'vehicles'));
    }

    public function quickEntry(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'patient_name' => 'nullable|string|max:100',
            'patient_phone' => 'nullable|string|max:20',
            'patient_birth_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'patient_gender' => 'nullable|in:male,female,other',
            'destination' => 'nullable|string|max:255',
            'date' => 'required|date',
            'amount_thu' => 'nullable|numeric|min:0',
            'amount_chi' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,bank,other',
            'note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Create or find patient if provided
            $patientId = null;
            if ($validated['patient_name']) {
                $patient = Patient::firstOrCreate(
                    [
                        'name' => $validated['patient_name'],
                        'phone' => $validated['patient_phone'],
                    ],
                    [
                        'birth_year' => $validated['patient_birth_year'] ?? null,
                        'gender' => $validated['patient_gender'] ?? null,
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
                'summary' => $validated['note'],
            ]);

            // Create transactions if amounts provided
            if (!empty($validated['amount_thu']) && $validated['amount_thu'] > 0) {
                Transaction::create([
                    'incident_id' => $incident->id,
                    'vehicle_id' => $validated['vehicle_id'],
                    'type' => 'thu',
                    'amount' => $validated['amount_thu'],
                    'method' => $validated['payment_method'],
                    'recorded_by' => auth()->id(),
                    'date' => $validated['date'],
                    'note' => 'Thu từ chuyến đi',
                ]);
            }

            if (!empty($validated['amount_chi']) && $validated['amount_chi'] > 0) {
                Transaction::create([
                    'incident_id' => $incident->id,
                    'vehicle_id' => $validated['vehicle_id'],
                    'type' => 'chi',
                    'amount' => $validated['amount_chi'],
                    'method' => $validated['payment_method'],
                    'recorded_by' => auth()->id(),
                    'date' => $validated['date'],
                    'note' => 'Chi phí chuyến đi',
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard')
                ->with('success', 'Đã ghi nhận chuyến đi thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
