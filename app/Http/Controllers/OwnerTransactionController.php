<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class OwnerTransactionController extends Controller
{
    /**
     * Display transactions summary for vehicle owner
     * Shows aggregated stats from all owned vehicles
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get owner's vehicles using Staff model
        $ownedVehicleIds = \App\Models\Staff::where('user_id', $user->id)
            ->where('staff_type', 'vehicle_owner')
            ->pluck('vehicle_id')
            ->filter()
            ->toArray();
        
        if (empty($ownedVehicleIds)) {
            return redirect()->route('dashboard')->with('error', 'Bạn chưa được gán xe nào');
        }
        
        // Get vehicles
        $vehicles = Vehicle::whereIn('id', $ownedVehicleIds)
            ->with('owner')
            ->orderBy('license_plate')
            ->get();
        
        // Initialize aggregated stats
        $totalRevenueDisplay = 0;
        $monthRevenueDisplay = 0;
        $totalExpenseDisplay = 0;
        $monthExpenseDisplay = 0;
        $totalCompanyFee = 0;
        $monthCompanyFee = 0;
        $totalDebt = 0;
        $monthDebt = 0;
        $totalProfit = 0;
        $monthProfit = 0;
        
        $vehicleStats = [];
        
        // Calculate stats for each vehicle using VehicleController logic
        foreach ($vehicles as $vehicle) {
            $stats = VehicleController::calculateVehicleStats($vehicle);
            
            // Store vehicle stats
            $vehicleStats[] = [
                'vehicle' => $vehicle,
                'total_revenue_display' => $stats['total_revenue_display'] ?? 0,
                'month_revenue_display' => $stats['month_revenue_display'] ?? 0,
                'total_expense_display' => $stats['total_expense_display'] ?? 0,
                'month_expense_display' => $stats['month_expense_display'] ?? 0,
                'total_company_fee' => $stats['total_company_fee'] ?? 0,
                'month_company_fee' => $stats['month_company_fee'] ?? 0,
                'total_debt' => $stats['total_borrowed'] ?? 0,
                'month_debt' => $stats['month_borrowed'] ?? 0,
                'total_profit' => $stats['total_profit_after_fee'] ?? 0,
                'month_profit' => $stats['month_profit_after_fee'] ?? 0,
            ];
            
            // Aggregate totals
            $totalRevenueDisplay += $stats['total_revenue_display'] ?? 0;
            $monthRevenueDisplay += $stats['month_revenue_display'] ?? 0;
            $totalExpenseDisplay += $stats['total_expense_display'] ?? 0;
            $monthExpenseDisplay += $stats['month_expense_display'] ?? 0;
            $totalCompanyFee += $stats['total_company_fee'] ?? 0;
            $monthCompanyFee += $stats['month_company_fee'] ?? 0;
            $totalDebt += $stats['total_borrowed'] ?? 0;
            $monthDebt += $stats['month_borrowed'] ?? 0;
            $totalProfit += $stats['total_profit_after_fee'] ?? 0;
            $monthProfit += $stats['month_profit_after_fee'] ?? 0;
        }
        
        // Summary stats
        $stats = [
            'total_revenue_display' => $totalRevenueDisplay,
            'month_revenue_display' => $monthRevenueDisplay,
            'total_expense_display' => $totalExpenseDisplay,
            'month_expense_display' => $monthExpenseDisplay,
            'total_company_fee' => $totalCompanyFee,
            'month_company_fee' => $monthCompanyFee,
            'total_debt' => $totalDebt,
            'month_debt' => $monthDebt,
            'total_profit' => $totalProfit,
            'month_profit' => $monthProfit,
        ];
        
        // Get recent transactions from all owned vehicles and group them by type
        $vehicleIds = $vehicles->pluck('id');
        $allTransactions = Transaction::with(['vehicle', 'incident.patient', 'vehicleMaintenance.maintenanceService', 'vehicleMaintenance.partner'])
            ->whereIn('vehicle_id', $vehicleIds)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Pagination parameters
        $perPage = 15;
        $incidentsPage = $request->get('incidents_page', 1);
        $maintenancesPage = $request->get('maintenances_page', 1);
        $othersPage = $request->get('others_page', 1);
        
        // Group transactions by vehicle and type
        $transactionsByVehicle = [];
        foreach ($vehicles as $vehicle) {
            $vehicleTransactions = $allTransactions->where('vehicle_id', $vehicle->id);
            
            if ($vehicleTransactions->isEmpty()) {
                continue;
            }
            
            // Group by incident, maintenance, or each individual other transaction
            $grouped = $vehicleTransactions->groupBy(function($transaction) {
                if ($transaction->incident_id) {
                    return 'incident_' . $transaction->incident_id;
                } elseif ($transaction->vehicle_maintenance_id) {
                    return 'maintenance_' . $transaction->vehicle_maintenance_id;
                } else {
                    // Each "other" transaction is its own group
                    return 'other_' . $transaction->id;
                }
            })->map(function($group) use ($vehicle) {
                $revenueTypes = ['thu', 'vay_cong_ty', 'nop_quy'];
                $expenseTypes = ['chi', 'tra_cong_ty', 'du_kien_chi'];
                
                $totalRevenue = $group->filter(function($t) use ($revenueTypes) { 
                    return in_array($t->type, $revenueTypes);
                })->sum('amount');
                
                $totalExpense = $group->filter(function($t) use ($expenseTypes) { 
                    return in_array($t->type, $expenseTypes);
                })->sum('amount');
                
                $totalPlannedExpense = $group->filter(function($t) { return $t->type === 'du_kien_chi'; })->sum('amount');
                $totalFundDeposit = $group->filter(function($t) { return $t->type === 'nop_quy'; })->sum('amount');
                
                $netAmount = $totalRevenue - $totalExpense;
                
                // Calculate management fee (15% on profit for incidents)
                $hasOwner = $vehicle->hasOwner();
                $managementFee = 0;
                $profitAfterFee = $netAmount;
                
                $firstTransaction = $group->first();
                if ($firstTransaction->incident_id && $hasOwner) {
                    // For incidents: calculate 15% fee on profit (revenue - expense - planned)
                    $realRevenue = $group->filter(function($t) { 
                        return $t->type === 'thu' && $t->category !== 'vay_từ_công_ty'; 
                    })->sum('amount');
                    $realExpense = $group->filter(function($t) { return $t->type === 'chi'; })->sum('amount');
                    $revenueForFee = $realRevenue - $realExpense - $totalPlannedExpense;
                    $managementFee = ($revenueForFee > 0) ? $revenueForFee * 0.15 : 0;
                    $profitAfterFee = $netAmount - $managementFee;
                }
                
                // Determine group type
                if ($firstTransaction->incident_id) {
                    $groupType = 'incident';
                } elseif ($firstTransaction->vehicle_maintenance_id) {
                    $groupType = 'maintenance';
                } else {
                    $groupType = 'other';
                }
                
                return [
                    'type' => $groupType,
                    'incident' => $firstTransaction->incident,
                    'maintenance' => $firstTransaction->vehicleMaintenance,
                    'vehicle' => $vehicle,
                    'date' => $firstTransaction->date,
                    'transactions' => $group,
                    'total_revenue' => $totalRevenue,
                    'total_expense' => $totalExpense,
                    'total_planned_expense' => $totalPlannedExpense,
                    'total_fund_deposit' => $totalFundDeposit,
                    'net_amount' => $netAmount,
                    'management_fee' => $managementFee,
                    'profit_after_fee' => $profitAfterFee,
                    'count' => $group->count(),
                ];
            })->sortByDesc('date')->values();
            
            // Separate by type
            $incidents = $grouped->where('type', 'incident')->values();
            $maintenances = $grouped->where('type', 'maintenance')->values();
            $others = $grouped->where('type', 'other')->values();
            
            // Paginate each type
            $incidentsTotal = $incidents->count();
            $incidentsPaginated = $incidents->forPage($incidentsPage, $perPage);
            $incidentsPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $incidentsPaginated,
                $incidentsTotal,
                $perPage,
                $incidentsPage,
                ['path' => $request->url(), 'query' => array_merge($request->query(), ['incidents_page' => $incidentsPage]), 'pageName' => 'incidents_page']
            );
            
            $maintenancesTotal = $maintenances->count();
            $maintenancesPaginated = $maintenances->forPage($maintenancesPage, $perPage);
            $maintenancesPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $maintenancesPaginated,
                $maintenancesTotal,
                $perPage,
                $maintenancesPage,
                ['path' => $request->url(), 'query' => array_merge($request->query(), ['maintenances_page' => $maintenancesPage]), 'pageName' => 'maintenances_page']
            );
            
            $othersTotal = $others->count();
            $othersPaginated = $others->forPage($othersPage, $perPage);
            $othersPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $othersPaginated,
                $othersTotal,
                $perPage,
                $othersPage,
                ['path' => $request->url(), 'query' => array_merge($request->query(), ['others_page' => $othersPage]), 'pageName' => 'others_page']
            );
            
            $transactionsByVehicle[$vehicle->id] = [
                'vehicle' => $vehicle,
                'incidents' => $incidentsPaginator,
                'maintenances' => $maintenancesPaginator,
                'others' => $othersPaginator,
            ];
        }
        
        return view('owner.transactions', compact('vehicleStats', 'stats', 'transactionsByVehicle', 'vehicles'));
    }
}
