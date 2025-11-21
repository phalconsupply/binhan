<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Incident;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create incidents|create vehicles')->only(['store']);
        $this->middleware('permission:edit incidents|edit vehicles')->only(['update']);
        $this->middleware('permission:delete incidents|delete vehicles')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Note::with(['user', 'incident', 'vehicle'])
            ->latest();

        // Filter by severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by incident
        if ($request->filled('incident_id')) {
            $query->where('incident_id', $request->incident_id);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('note', 'like', "%{$request->search}%");
        }

        $notes = $query->paginate(20);

        // Statistics
        $statistics = [
            'total' => Note::count(),
            'info' => Note::where('severity', 'info')->count(),
            'warning' => Note::where('severity', 'warning')->count(),
            'critical' => Note::where('severity', 'critical')->count(),
        ];

        return view('notes.index', compact('notes', 'statistics'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'incident_id' => 'nullable|exists:incidents,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'note' => 'required|string',
            'severity' => 'required|in:info,warning,critical',
        ]);

        $validated['user_id'] = auth()->id();

        $note = Note::create($validated);

        activity()
            ->performedOn($note)
            ->causedBy(auth()->user())
            ->log('Tạo ghi chú mới');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ghi chú đã được tạo!',
                'note' => $note->load('user')
            ]);
        }

        $redirect = null;
        if ($note->incident_id) {
            $redirect = route('incidents.show', $note->incident_id);
        } elseif ($note->vehicle_id) {
            $redirect = route('vehicles.show', $note->vehicle_id);
        } else {
            $redirect = route('notes.index');
        }

        return redirect($redirect)
            ->with('success', 'Ghi chú đã được tạo!');
    }

    public function update(Request $request, Note $note)
    {
        $validated = $request->validate([
            'note' => 'required|string',
            'severity' => 'required|in:info,warning,critical',
        ]);

        $note->update($validated);

        activity()
            ->performedOn($note)
            ->causedBy(auth()->user())
            ->log('Cập nhật ghi chú');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ghi chú đã được cập nhật!',
                'note' => $note->load('user')
            ]);
        }

        return back()->with('success', 'Ghi chú đã được cập nhật!');
    }

    public function destroy(Request $request, Note $note)
    {
        activity()
            ->performedOn($note)
            ->causedBy(auth()->user())
            ->log('Xóa ghi chú');

        $note->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ghi chú đã được xóa!'
            ]);
        }

        return back()->with('success', 'Ghi chú đã được xóa!');
    }
}
