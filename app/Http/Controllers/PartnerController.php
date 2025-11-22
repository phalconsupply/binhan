<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage settings'])->except(['autocomplete']);
    }

    public function index(Request $request)
    {
        $query = Partner::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        $partners = $query->orderBy('name')->paginate(15);

        return view('partners.index', compact('partners'));
    }

    public function create()
    {
        return view('partners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:collaborator,maintenance',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'note' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $partner = Partner::create($validated);

        return redirect()->route('partners.index')
            ->with('success', "Đã thêm đối tác {$partner->name} thành công!");
    }

    public function edit(Partner $partner)
    {
        return view('partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:collaborator,maintenance',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'note' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $partner->update($validated);

        return redirect()->route('partners.index')
            ->with('success', "Đã cập nhật đối tác {$partner->name} thành công!");
    }

    public function destroy(Partner $partner)
    {
        $name = $partner->name;
        $partner->delete();

        return redirect()->route('partners.index')
            ->with('success', "Đã xóa đối tác {$name} thành công!");
    }

    public function autocomplete(Request $request)
    {
        $term = $request->input('term', '');
        $type = $request->input('type', ''); // 'collaborator' or 'maintenance'

        $query = Partner::active();

        if ($type) {
            $query->where('type', $type);
        }

        if ($term) {
            $query->where('name', 'like', "%{$term}%");
        }

        $partners = $query->limit(10)->get(['id', 'name', 'commission_rate', 'phone']);

        return response()->json($partners);
    }
}
