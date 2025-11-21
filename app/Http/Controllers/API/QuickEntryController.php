<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Patient;
use Illuminate\Http\Request;

class QuickEntryController extends Controller
{
    /**
     * Search vehicles by license plate (for typeahead)
     */
    public function searchVehicles(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $vehicles = Vehicle::where('license_plate', 'like', "%{$query}%")
            ->where('status', 'active')
            ->orderBy('license_plate')
            ->limit(10)
            ->get(['id', 'license_plate', 'driver_name', 'phone']);

        return response()->json($vehicles);
    }

    /**
     * Get vehicle details by ID
     */
    public function getVehicle($id)
    {
        $vehicle = Vehicle::with(['incidents' => function($q) {
            $q->orderBy('date', 'desc')->limit(5);
        }])->find($id);

        if (!$vehicle) {
            return response()->json(['error' => 'Vehicle not found'], 404);
        }

        return response()->json($vehicle);
    }

    /**
     * Search patients by name or phone (for typeahead)
     */
    public function searchPatients(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $patients = Patient::where(function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('phone', 'like', "%{$query}%");
        })
        ->orderBy('name')
        ->limit(10)
        ->get(['id', 'name', 'phone', 'birth_year', 'gender', 'address']);

        return response()->json($patients);
    }

    /**
     * Get patient details by ID
     */
    public function getPatient($id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        return response()->json($patient);
    }

    /**
     * Get quick statistics
     */
    public function getStats()
    {
        $stats = [
            'active_vehicles' => Vehicle::active()->count(),
            'today_incidents' => \App\Models\Incident::today()->count(),
            'today_revenue' => \App\Models\Transaction::revenue()->today()->sum('amount'),
            'today_expense' => \App\Models\Transaction::expense()->today()->sum('amount'),
        ];

        $stats['today_net'] = $stats['today_revenue'] - $stats['today_expense'];

        return response()->json($stats);
    }
}
