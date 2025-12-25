<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Incident;
use App\Models\Transaction;
use App\Models\Patient;
use App\Models\Note;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type', 'all');
        $user = auth()->user();

        if (empty($query)) {
            return view('search.index', [
                'query' => '',
                'results' => [],
                'type' => $type,
            ]);
        }

        $results = [];

        // Search vehicles - only if user has permission
        if (($type === 'all' || $type === 'vehicles') && $user->can('search vehicles')) {
            $results['vehicles'] = Vehicle::where('license_plate', 'like', "%{$query}%")
                ->orWhere('model', 'like', "%{$query}%")
                ->orWhere('driver_name', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->limit(10)
                ->get();
        }

        // Search patients - only if user has permission
        if (($type === 'all' || $type === 'patients') && $user->can('search patients')) {
            $results['patients'] = Patient::where('name', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->orWhere('address', 'like', "%{$query}%")
                ->limit(10)
                ->get();
        }

        // Search incidents - only if user has permission
        if (($type === 'all' || $type === 'incidents') && $user->can('search incidents')) {
            $results['incidents'] = Incident::with(['vehicle', 'patient', 'dispatcher'])
                ->where('destination', 'like', "%{$query}%")
                ->orWhere('summary', 'like', "%{$query}%")
                ->orWhereHas('vehicle', function($q) use ($query) {
                    $q->where('license_plate', 'like', "%{$query}%");
                })
                ->orWhereHas('patient', function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                })
                ->latest()
                ->limit(10)
                ->get();
        }

        // Search transactions - only if user has permission
        if (($type === 'all' || $type === 'transactions') && $user->can('search transactions')) {
            $results['transactions'] = Transaction::with(['vehicle', 'incident'])
                ->where('note', 'like', "%{$query}%")
                ->orWhereHas('vehicle', function($q) use ($query) {
                    $q->where('license_plate', 'like', "%{$query}%");
                })
                ->latest()
                ->limit(10)
                ->get();
        }

        // Search notes - only if user has permission
        if (($type === 'all' || $type === 'notes') && $user->can('search notes')) {
            $results['notes'] = Note::with(['user', 'vehicle', 'incident'])
                ->where('note', 'like', "%{$query}%")
                ->latest()
                ->limit(10)
                ->get();
        }

        return view('search.index', [
            'query' => $query,
            'results' => $results,
            'type' => $type,
        ]);
    }

    public function api(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type', 'all');
        $user = auth()->user();

        if (empty($query)) {
            return response()->json([]);
        }

        $results = [];

        // Search vehicles via API - only if user has permission
        if (($type === 'vehicles' || $type === 'all') && $user->can('search vehicles')) {
            $vehicles = Vehicle::where('license_plate', 'like', "%{$query}%")
                ->orWhere('driver_name', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function($vehicle) {
                    return [
                        'type' => 'vehicle',
                        'id' => $vehicle->id,
                        'title' => $vehicle->license_plate,
                        'subtitle' => $vehicle->driver_name ?? $vehicle->model,
                        'url' => route('vehicles.show', $vehicle),
                    ];
                });
            $results = array_merge($results, $vehicles->toArray());
        }

        // Search patients via API - only if user has permission
        if (($type === 'patients' || $type === 'all') && $user->can('search patients')) {
            $patients = Patient::where('name', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function($patient) {
                    return [
                        'type' => 'patient',
                        'id' => $patient->id,
                        'title' => $patient->name,
                        'subtitle' => $patient->phone,
                        'url' => route('patients.show', $patient),
                    ];
                });
            $results = array_merge($results, $patients->toArray());
        }

        // Search incidents via API - only if user has permission
        if (($type === 'incidents' || $type === 'all') && $user->can('search incidents')) {
            $incidents = Incident::with(['vehicle', 'patient'])
                ->where('destination', 'like', "%{$query}%")
                ->orWhereHas('vehicle', function($q) use ($query) {
                    $q->where('license_plate', 'like', "%{$query}%");
                })
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($incident) {
                    return [
                        'type' => 'incident',
                        'id' => $incident->id,
                        'title' => "Chuyến #{$incident->id} - {$incident->vehicle->license_plate}",
                        'subtitle' => $incident->destination ?? $incident->date->format('d/m/Y'),
                        'url' => route('incidents.show', $incident),
                    ];
                });
            $results = array_merge($results, $incidents->toArray());
        }

        return response()->json($results);
    }
}
