<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the attendances.
     */
    public function index(Request $request)
{
    $query = Attendance::with(['schedule.school', 'schedule.teachers']);
    
    // Apply filters
    if ($request->filled('school_id')) {
        $query->whereHas('schedule', function ($q) use ($request) {
            $q->where('school_id', $request->school_id);
        });
    }
    
    if ($request->filled('teacher_id')) {
        $query->whereHas('schedule', function ($q) use ($request) {
            $q->where('teacher_id', $request->teacher_id);
        });
    }
    
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }
    
    // Get paginated attendances with sorting
    $attendances = $query->orderBy('created_at', 'desc')->paginate(10);
    
    // Get all schools for filter dropdown
    $schools = School::all();
    
    // Get all teachers for filter dropdown
    $teachers = Teacher::all();
    
    // Get attendance statistics
    $statistics = [
        'approved' => $query->clone()->where('attendance_status', 'approved')->count(),
        'pending' => $query->clone()->where('attendance_status', 'pending')->count(),
        'rejected' => $query->clone()->where('attendance_status', 'rejected')->count(),
    ];
    
    return view('admin.attendances.index', compact(
        'attendances',
        'schools',
        'teachers',
        'statistics'
    ));
}

public function filter(Request $request)
{
    // Pastikan menerima Request sebagai parameter
    $query = Attendance::with(['schedule.school', 'schedule.teachers.user']);

    // Filter by teacher name
    if ($request->filled('teacher_name')) {
        $query->whereHas('schedule.teachers', function ($q) use ($request) {
            $q->whereHas('user', function ($u) use ($request) {
                $u->where('name', 'like', '%' . $request->teacher_name . '%');
            });
        });
    }

    // Filter by school
    if ($request->filled('school')) {
        $query->whereHas('schedule', function ($q) use ($request) {
            $q->where('school_id', $request->school);
        });
    }

    // Filter by date range
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Get attendance statistics (gunakan query baru untuk statistik)
    $statistics = [
        'approved' => Attendance::where('attendance_status', 'approved')->count(),
        'pending' => Attendance::where('attendance_status', 'pending')->count(),
        'rejected' => Attendance::where('attendance_status', 'rejected')->count(),
    ];

    // Get paginated results
    $attendances = $query->orderBy('created_at', 'desc')->paginate(10);

    // Get all schools for filter dropdown
    $schools = School::all();

    return view('admin.attendances.index', compact(
        'attendances',
        'schools',
        'statistics'
    ));
}

    
    
    /**
     * Display the specified attendance.
     */
    public function show($id)
    {
        $attendance = Attendance::with(['schedule.school', 'schedule.teachers'])
            ->findOrFail($id);
  
        return view('admin.attendances.show', compact('attendance'));
    }
    
    /**
     * Approve the specified attendance.
     */
    public function approve($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->status = 'approved';
        $attendance->save();
        
        return redirect()->route('admin.attendances.show', $attendance->id)
            ->with('success', 'Attendance has been approved successfully.');
    }
    
    /**
     * Reject the specified attendance.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        $attendance = Attendance::findOrFail($id);
        $attendance->status = 'rejected';
        $attendance->rejection_reason = $request->rejection_reason;
        $attendance->save();
        
        return redirect()->route('admin.attendances.show', $attendance->id)
            ->with('success', 'Attendance has been rejected successfully.');
    }
}
