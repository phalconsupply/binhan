<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view transactions'])->only(['index', 'show']);
        $this->middleware(['auth', 'permission:create transactions'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:edit transactions'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:delete transactions'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['vehicle', 'incident.patient', 'recorder']);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('vehicle', function($vq) use ($search) {
                    $vq->where('license_plate', 'like', "%{$search}%");
                })
                ->orWhereHas('incident', function($iq) use ($search) {
                    $iq->where('id', 'like', "%{$search}%");
                })
                ->orWhere('note', 'like', "%{$search}%")
                ->orWhere('amount', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Filter by vehicle
        if ($request->has('vehicle_id') && $request->vehicle_id != '') {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by method
        if ($request->has('method') && $request->method != '') {
            $query->where('method', $request->method);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Get all transactions
        $allTransactions = $query->orderBy('date', 'desc')->get();

        // Group by incident_id
        $groupedTransactions = $allTransactions->groupBy('incident_id')->map(function($group) {
            $totalRevenue = $group->where('type', 'thu')->sum('amount');
            $totalExpense = $group->where('type', 'chi')->sum('amount');
            $totalPlannedExpense = $group->where('type', 'du_kien_chi')->sum('amount');
            $netAmount = $totalRevenue - $totalExpense - $totalPlannedExpense;
            
            return [
                'incident' => $group->first()->incident,
                'vehicle' => $group->first()->vehicle,
                'date' => $group->first()->date,
                'transactions' => $group,
                'total_revenue' => $totalRevenue,
                'total_expense' => $totalExpense,
                'total_planned_expense' => $totalPlannedExpense,
                'net_amount' => $netAmount,
            ];
        })->sortByDesc('date')->values();

        // Manual pagination
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $total = $groupedTransactions->count();
        $groupedTransactions = $groupedTransactions->forPage($currentPage, $perPage);

        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $groupedTransactions,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Get vehicles for filter dropdown
        $vehicles = Vehicle::orderBy('license_plate')->get();

        // Statistics
        $stats = [
            'total_revenue' => Transaction::revenue()->sum('amount'),
            'total_expense' => Transaction::expense()->sum('amount'),
            'total_planned_expense' => Transaction::plannedExpense()->sum('amount'),
            'today_revenue' => Transaction::revenue()->today()->sum('amount'),
            'today_expense' => Transaction::expense()->today()->sum('amount'),
            'today_planned_expense' => Transaction::plannedExpense()->today()->sum('amount'),
            'month_revenue' => Transaction::revenue()->thisMonth()->sum('amount'),
            'month_expense' => Transaction::expense()->thisMonth()->sum('amount'),
            'month_planned_expense' => Transaction::plannedExpense()->thisMonth()->sum('amount'),
            'company_expense' => Transaction::expense()->whereNull('incident_id')->sum('amount'),
            'company_month_expense' => Transaction::expense()->whereNull('incident_id')->thisMonth()->sum('amount'),
            'company_planned_expense' => Transaction::plannedExpense()->whereNull('incident_id')->sum('amount'),
        ];
        $stats['total_net'] = $stats['total_revenue'] - $stats['total_expense'] - $stats['total_planned_expense'];
        $stats['today_net'] = $stats['today_revenue'] - $stats['today_expense'] - $stats['today_planned_expense'];
        $stats['month_net'] = $stats['month_revenue'] - $stats['month_expense'] - $stats['month_planned_expense'];

        return view('transactions.index', compact('transactions', 'vehicles', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $vehicles = Vehicle::active()->orderBy('license_plate')->get();
        $incidents = Incident::with(['vehicle', 'patient'])
            ->orderBy('date', 'desc')
            ->limit(50)
            ->get();

        // Pre-select incident if provided
        $selectedIncident = null;
        if ($request->has('incident_id')) {
            $selectedIncident = Incident::find($request->incident_id);
        }

        return view('transactions.create', compact('vehicles', 'incidents', 'selectedIncident'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'incident_id' => 'nullable|exists:incidents,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'type' => 'required|in:thu,chi,du_kien_chi',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,bank,other',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $validated['recorded_by'] = auth()->id();

        $transaction = Transaction::create($validated);

        return redirect()->route('transactions.index')
            ->with('success', "Đã ghi nhận {$transaction->type_label} " . number_format($transaction->amount, 0, ',', '.') . 'đ thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['vehicle', 'incident.patient', 'recorder']);

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        $transaction->load(['vehicle', 'incident']);
        
        $vehicles = Vehicle::orderBy('license_plate')->get();
        $incidents = Incident::with(['vehicle', 'patient'])
            ->orderBy('date', 'desc')
            ->limit(50)
            ->get();

        return view('transactions.edit', compact('transaction', 'vehicles', 'incidents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'incident_id' => 'nullable|exists:incidents,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'type' => 'required|in:thu,chi,du_kien_chi',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,bank,other',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Đã cập nhật giao dịch thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Đã xóa giao dịch thành công!');
    }

    /**
     * Remove all transactions for a specific incident.
     */
    public function destroyByIncident($incidentId)
    {
        // Check permission
        if (!auth()->user()->can('delete transactions')) {
            abort(403);
        }

        $count = Transaction::where('incident_id', $incidentId)->count();
        Transaction::where('incident_id', $incidentId)->delete();

        return redirect()->back()
            ->with('success', "Đã xóa {$count} giao dịch của chuyến đi này!");
    }

    /**
     * Distribute dividends to investors
     */
    public function distributeDividend(Request $request)
    {
        $validated = $request->validate([
            'distribution_percentage' => 'required|numeric|min:0|max:100',
            'investors' => 'required|array',
            'investors.*.staff_id' => 'required|exists:staff,id',
            'investors.*.amount' => 'required|numeric|min:0',
            'investors.*.equity_percentage' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $totalDistributed = 0;
            $date = now();

            // Create dividend transactions for each investor
            foreach ($validated['investors'] as $investorData) {
                $amount = $investorData['amount'];
                
                if ($amount <= 0) {
                    continue;
                }

                $staff = \App\Models\Staff::find($investorData['staff_id']);
                
                // Create CHI transaction (company pays dividend)
                Transaction::create([
                    'incident_id' => null,
                    'vehicle_id' => null,
                    'staff_id' => $staff->id,
                    'type' => 'chi',
                    'category' => 'cổ_tức',
                    'amount' => $amount,
                    'method' => 'bank',
                    'payment_method' => 'chuyển khoản',
                    'date' => $date,
                    'note' => "Chia cổ tức {$validated['distribution_percentage']}% - {$staff->full_name} (Vốn góp: {$investorData['equity_percentage']}%)" . 
                             ($validated['note'] ? " - {$validated['note']}" : ""),
                    'recorded_by' => auth()->id(),
                ]);

                $totalDistributed += $amount;
            }

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', "Đã chia cổ tức thành công! Tổng số tiền: " . number_format($totalDistributed, 0, ',', '.') . "đ cho " . count($validated['investors']) . " cổ đông.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }
}
