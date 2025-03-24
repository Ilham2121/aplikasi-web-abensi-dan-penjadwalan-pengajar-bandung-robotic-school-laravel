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
        $query = Attendance::with(['schedule.school', 'schedule.teacher']);
        
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
        
        $attendances = $query->orderBy('created_at', 'desc')->paginate(10);
        $schools = School::all();
        $teachers = Teacher::all();
        
        return view('admin.attendances.index', compact('attendances', 'schools', 'teachers'));
    }
    
    /**
     * Display the specified attendance.
     */
    public function show($id)
    {
        $attendance = Attendance::with(['schedule.school', 'schedule.teacher'])
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
