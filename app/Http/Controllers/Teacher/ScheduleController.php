<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Teacher']);
    }
    
    /**
     * Display all schedules for the logged in teacher
     */
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        
        $query = $teacher->schedules()
            ->with(['school'])
            ->active()
            ->currentSemester();
        
        // Filter by day
        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }
        
        // Filter by type (regular/private)
        if ($request->filled('type')) {
            $query->where('schedule_type', $request->type);
        }
        
        // Filter by school
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }
        
        $schedules = $query->orderBy('day')
            ->orderBy('start_time')
            ->paginate(10);
        
        // Get unique schools for filter dropdown
        $schools = $teacher->schedules()
            ->select('school_id')
            ->with('school')
            ->get()
            ->pluck('school')
            ->unique('id');
        
        return view('teacher.schedules.index', compact('schedules', 'schools'));
    }
}
