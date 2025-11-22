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
            'drivers' => 'nullable|array',
            'drivers.*.staff_id' => 'nullable|exists:staff,id',
            'drivers.*.wage' => 'nullable|numeric|min:0',
            'medical_staff' => 'nullable|array',
            'medical_staff.*.staff_id' => 'nullable|exists:staff,id',
            'medical_staff.*.wage' => 'nullable|numeric|min:0',
            'patient_name' => 'nullable|string|max:100',
            'patient_phone' => 'nullable|string|max:20',
            'patient_birth_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'patient_gender' => 'nullable|in:male,female,other',
            'from_location' => 'nullable|string|max:255',
            'to_location' => 'nullable|string|max:255',
            'date' => 'required|date',
            'partner_id' => 'nullable|exists:partners,id',
            'commission_amount' => 'nullable|numeric|min:0',
            'revenue_main_name' => 'nullable|string|max:255',
            'amount_thu' => 'nullable|numeric|min:0',
            'expense_main_name' => 'nullable|string|max:255',
            'amount_chi' => 'nullable|numeric|min:0',
            'additional_services' => 'nullable|array',
            'additional_services.*.name' => 'required|string|max:255',
            'additional_services.*.amount' => 'required|numeric|min:0',
            'additional_expenses' => 'nullable|array',
            'additional_expenses.*.name' => 'required|string|max:255',
            'additional_expenses.*.amount' => 'required|numeric|min:0',
            'maintenance_partner_id' => 'nullable|exists:partners,id',
            'maintenance_service' => 'nullable|string|max:255',
            'maintenance_cost' => 'nullable|numeric|min:0',
            'maintenance_mileage' => 'nullable|integer|min:0',
            'maintenance_note' => 'nullable|string',
            'payment_method' => 'required|in:cash,bank,other',
            'note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Find or create locations based on names
            $fromLocationId = null;
            $toLocationId = null;
            
            if (!empty($validated['from_location'])) {
                $location = \App\Models\Location::firstOrCreate(
                    ['name' => $validated['from_location']],
                    ['type' => 'from', 'is_active' => true]
                );
                $fromLocationId = $location->id;
            }
            
            if (!empty($validated['to_location'])) {
                $location = \App\Models\Location::firstOrCreate(
                    ['name' => $validated['to_location']],
                    ['type' => 'to', 'is_active' => true]
                );
                $toLocationId = $location->id;
            }

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
                'from_location_id' => $fromLocationId,
                'to_location_id' => $toLocationId,
                'partner_id' => $validated['partner_id'] ?? null,
                'commission_amount' => $validated['commission_amount'] ?? null,
                'summary' => $validated['note'],
            ]);

            // Attach staff to incident (drivers and medical staff) with wages
            if (!empty($validated['drivers'])) {
                foreach ($validated['drivers'] as $driver) {
                    if (!empty($driver['staff_id'])) {
                        $incident->staff()->attach($driver['staff_id'], [
                            'role' => 'driver',
                            'wage_amount' => $driver['wage'] ?? null
                        ]);

                        // Create wage transaction if wage is provided
                        if (!empty($driver['wage']) && $driver['wage'] > 0) {
                            $staffMember = \App\Models\Staff::find($driver['staff_id']);
                            Transaction::create([
                                'incident_id' => $incident->id,
                                'vehicle_id' => $validated['vehicle_id'],
                                'staff_id' => $driver['staff_id'],
                                'type' => 'chi',
                                'amount' => $driver['wage'],
                                'method' => $validated['payment_method'],
                                'recorded_by' => auth()->id(),
                                'date' => $validated['date'],
                                'note' => 'Tiền công lái xe: ' . ($staffMember ? $staffMember->full_name : ''),
                            ]);
                        }
                    }
                }
            }
            
            if (!empty($validated['medical_staff'])) {
                foreach ($validated['medical_staff'] as $staff) {
                    if (!empty($staff['staff_id'])) {
                        $incident->staff()->attach($staff['staff_id'], [
                            'role' => 'medical_staff',
                            'wage_amount' => $staff['wage'] ?? null
                        ]);

                        // Create wage transaction if wage is provided
                        if (!empty($staff['wage']) && $staff['wage'] > 0) {
                            $staffMember = \App\Models\Staff::find($staff['staff_id']);
                            Transaction::create([
                                'incident_id' => $incident->id,
                                'vehicle_id' => $validated['vehicle_id'],
                                'staff_id' => $staff['staff_id'],
                                'type' => 'chi',
                                'amount' => $staff['wage'],
                                'method' => $validated['payment_method'],
                                'recorded_by' => auth()->id(),
                                'date' => $validated['date'],
                                'note' => 'Tiền công nhân viên y tế: ' . ($staffMember ? $staffMember->full_name : ''),
                            ]);
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

            // Create additional services revenue
            if (!empty($validated['additional_services'])) {
                foreach ($validated['additional_services'] as $service) {
                    if (!empty($service['name']) && !empty($service['amount'])) {
                        // Try to find matching service
                        $additionalService = \App\Models\AdditionalService::where('name', $service['name'])->first();
                        
                        // Save to incident_additional_services
                        \App\Models\IncidentAdditionalService::create([
                            'incident_id' => $incident->id,
                            'additional_service_id' => $additionalService->id ?? null,
                            'service_name' => $service['name'],
                            'amount' => $service['amount'],
                        ]);

                        // Create transaction for this service
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
                // Find or create maintenance service
                $maintenanceService = \App\Models\MaintenanceService::firstOrCreate(
                    ['name' => $validated['maintenance_service']],
                    ['is_active' => true]
                );

                // Create maintenance record
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

                // Create expense transaction for maintenance
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
