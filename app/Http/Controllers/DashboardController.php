<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function adminDashboard()
    {
        $schoolCount = School::count();
        $teacherCount = Teacher::count();
        $scheduleCount = Schedule::count();
        
        // Get upcoming schedules for next 7 days
        $today = now()->format('l');
        $upcomingSchedules = Schedule::with(['school', 'teacher.user'])
            ->where('day', $today)
            ->get();
        
        // Get today's attendances
        $todayAttendances = Attendance::with(['schedule.teacher.user', 'schedule.school'])
            ->whereDate('created_at', today())
            ->latest()
            ->get();
        
        return view('admin.dashboard', compact('schoolCount', 'teacherCount', 'scheduleCount', 'upcomingSchedules', 'todayAttendances'));
    }
    
    public function teacherDashboard()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Teacher profile not found!');
        }
        
        // Get current teacher's upcoming schedules
        $today = now()->format('l');
        $upcomingSchedules = Schedule::whereHas('teachers', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('day', $today)
            ->with(['school'])
            ->get();
        
        // Get total schedules for current teacher
        $totalSchedules = Schedule::whereHas('teachers', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })->count();
        
        // Get monthly attendances
        $monthlyAttendances = Attendance::whereHas('schedule.teachers', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->with(['schedule.school'])
            ->latest()
            ->get();
        
        $attendanceCount = $monthlyAttendances->count();
        
        return view('teacher.dashboard', compact('upcomingSchedules', 'totalSchedules', 'monthlyAttendances', 'attendanceCount'));
    }
}
