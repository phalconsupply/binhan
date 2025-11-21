<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage patients')->except(['index', 'show']);
        $this->middleware('permission:view patients')->only(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = Patient::with(['incidents' => function($q) {
            $q->latest()->limit(5);
        }]);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Lọc theo giới tính
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Sắp xếp
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        
        if ($sort === 'incidents_count') {
            $query->withCount('incidents')->orderBy('incidents_count', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $patients = $query->paginate(15);

        // Thống kê
        $statistics = [
            'total' => Patient::count(),
            'male' => Patient::where('gender', 'male')->count(),
            'female' => Patient::where('gender', 'female')->count(),
            'with_incidents' => Patient::has('incidents')->count(),
        ];

        return view('patients.index', compact('patients', 'statistics'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:20',
            'medical_notes' => 'nullable|string',
        ]);

        $patient = Patient::create($validated);

        activity()
            ->performedOn($patient)
            ->causedBy(auth()->user())
            ->log('Tạo bệnh nhân mới');

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Tạo bệnh nhân thành công!');
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'incidents' => function($q) {
                $q->with(['vehicle', 'dispatcher'])->latest();
            }
        ]);

        // Thống kê cho bệnh nhân
        $statistics = [
            'total_incidents' => $patient->incidents->count(),
            'total_spent' => $patient->incidents->sum('total_revenue'),
            'recent_incidents' => $patient->incidents->take(5),
            'last_incident_date' => $patient->incidents->first()?->date,
        ];

        return view('patients.show', compact('patient', 'statistics'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:20',
            'medical_notes' => 'nullable|string',
        ]);

        $patient->update($validated);

        activity()
            ->performedOn($patient)
            ->causedBy(auth()->user())
            ->log('Cập nhật thông tin bệnh nhân');

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Cập nhật bệnh nhân thành công!');
    }

    public function destroy(Patient $patient)
    {
        // Kiểm tra xem bệnh nhân có chuyến đi nào không
        if ($patient->incidents()->count() > 0) {
            return back()->with('error', 'Không thể xóa bệnh nhân đã có chuyến đi. Vui lòng xóa các chuyến đi trước.');
        }

        activity()
            ->performedOn($patient)
            ->causedBy(auth()->user())
            ->log('Xóa bệnh nhân');

        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Xóa bệnh nhân thành công!');
    }
}
