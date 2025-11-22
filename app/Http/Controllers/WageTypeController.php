<?php

namespace App\Http\Controllers;

use App\Models\WageType;
use Illuminate\Http\Request;

class WageTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $wageTypes = WageType::ordered()->get();
        return view('wage-types.index', compact('wageTypes'));
    }

    public function create()
    {
        return view('wage-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:wage_types,name',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = true;

        WageType::create($validated);

        return redirect()->route('wage-types.index')
            ->with('success', 'Đã thêm loại tiền công mới thành công!');
    }

    public function edit(WageType $wageType)
    {
        return view('wage-types.edit', compact('wageType'));
    }

    public function update(Request $request, WageType $wageType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:wage_types,name,' . $wageType->id,
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $wageType->update($validated);

        return redirect()->route('wage-types.index')
            ->with('success', 'Đã cập nhật loại tiền công thành công!');
    }

    public function destroy(WageType $wageType)
    {
        $wageType->delete();

        return redirect()->route('wage-types.index')
            ->with('success', 'Đã xóa loại tiền công thành công!');
    }
}
