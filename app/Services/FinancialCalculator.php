<?php

namespace App\Services;

use App\Models\Incident;
use App\Models\Transaction;
use Illuminate\Support\Carbon;

class FinancialCalculator
{
    /**
     * Calculate company statistics with proper management fee handling
     * 
     * @param array $ownedVehicleIds Optional array of vehicle IDs for vehicle owners
     * @param string|null $dateFrom Optional start date
     * @param string|null $dateTo Optional end date
     * @return array
     */
    public static function calculateStatistics($ownedVehicleIds = [], $dateFrom = null, $dateTo = null)
    {
        $isVehicleOwner = !empty($ownedVehicleIds);
        
        // Build base queries
        $transactionsQuery = Transaction::query();
        $incidentsQuery = Incident::with(['vehicle.owner', 'transactions']);
        
        // Apply vehicle owner scope
        if ($isVehicleOwner) {
            $transactionsQuery->whereIn('vehicle_id', $ownedVehicleIds);
            $incidentsQuery->whereIn('vehicle_id', $ownedVehicleIds);
        }
        
        // Apply date range if provided
        if ($dateFrom && $dateTo) {
            $transactionsQuery->whereBetween('date', [$dateFrom, $dateTo]);
            $incidentsQuery->whereBetween('date', [$dateFrom, $dateTo]);
        }
        
        // Get basic transaction sums
        $totalRevenue = (clone $transactionsQuery)->revenue()->sum('amount');
        $totalExpense = (clone $transactionsQuery)->expense()->sum('amount');
        $totalPlannedExpense = (clone $transactionsQuery)->plannedExpense()->sum('amount');
        
        // Calculate profit based on user type
        if ($isVehicleOwner) {
            // For vehicle owners: simple calculation (they see their full vehicle P&L)
            $totalProfit = $totalRevenue - $totalExpense - $totalPlannedExpense;
        } else {
            // For company: calculate with management fee consideration
            $totalProfit = self::calculateCompanyProfit($incidentsQuery->get());
        }
        
        return [
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'total_planned_expense' => $totalPlannedExpense,
            'total_profit' => $totalProfit,
        ];
    }
    
    /**
     * Calculate today's statistics
     */
    public static function calculateTodayStatistics($ownedVehicleIds = [])
    {
        $isVehicleOwner = !empty($ownedVehicleIds);
        
        $transactionsQuery = Transaction::today();
        $incidentsQuery = Incident::with(['vehicle.owner', 'transactions'])->today();
        
        if ($isVehicleOwner) {
            $transactionsQuery->whereIn('vehicle_id', $ownedVehicleIds);
            $incidentsQuery->whereIn('vehicle_id', $ownedVehicleIds);
        }
        
        $todayRevenue = (clone $transactionsQuery)->revenue()->sum('amount');
        $todayExpense = (clone $transactionsQuery)->expense()->sum('amount');
        $todayPlannedExpense = (clone $transactionsQuery)->plannedExpense()->sum('amount');
        
        if ($isVehicleOwner) {
            $todayProfit = $todayRevenue - $todayExpense - $todayPlannedExpense;
        } else {
            $todayProfit = self::calculateCompanyProfit($incidentsQuery->get());
        }
        
        return [
            'today_revenue' => $todayRevenue,
            'today_expense' => $todayExpense,
            'today_planned_expense' => $todayPlannedExpense,
            'today_profit' => $todayProfit,
        ];
    }
    
    /**
     * Calculate this month's statistics
     */
    public static function calculateMonthStatistics($ownedVehicleIds = [])
    {
        $isVehicleOwner = !empty($ownedVehicleIds);
        
        $transactionsQuery = Transaction::thisMonth();
        $incidentsQuery = Incident::with(['vehicle.owner', 'transactions'])->thisMonth();
        
        if ($isVehicleOwner) {
            $transactionsQuery->whereIn('vehicle_id', $ownedVehicleIds);
            $incidentsQuery->whereIn('vehicle_id', $ownedVehicleIds);
        }
        
        $monthRevenue = (clone $transactionsQuery)->revenue()->sum('amount');
        $monthExpense = (clone $transactionsQuery)->expense()->sum('amount');
        $monthPlannedExpense = (clone $transactionsQuery)->plannedExpense()->sum('amount');
        
        if ($isVehicleOwner) {
            $monthProfit = $monthRevenue - $monthExpense - $monthPlannedExpense;
        } else {
            $monthProfit = self::calculateCompanyProfit($incidentsQuery->get());
        }
        
        return [
            'month_revenue' => $monthRevenue,
            'month_expense' => $monthExpense,
            'month_planned_expense' => $monthPlannedExpense,
            'month_profit' => $monthProfit,
        ];
    }
    
    /**
     * Calculate company profit considering management fee (15% for vehicles with owners)
     * Only counts positive profits from incidents
     * 
     * @param \Illuminate\Database\Eloquent\Collection $incidents
     * @return float
     */
    private static function calculateCompanyProfit($incidents)
    {
        $companyProfit = 0;
        
        foreach ($incidents as $incident) {
            $incidentRevenue = $incident->transactions()->revenue()->sum('amount');
            $incidentExpense = $incident->transactions()->expense()->sum('amount');
            $incidentPlannedExpense = $incident->transactions()->plannedExpense()->sum('amount');
            $incidentNet = $incidentRevenue - $incidentExpense - $incidentPlannedExpense;
            
            // Only count positive profits
            if ($incidentNet > 0) {
                if ($incident->vehicle && $incident->vehicle->hasOwner()) {
                    // Vehicle with owner: company gets 15% management fee
                    $companyProfit += $incidentNet * 0.15;
                } else {
                    // Vehicle without owner: company gets full profit
                    $companyProfit += $incidentNet;
                }
            }
        }
        
        return $companyProfit;
    }
}
