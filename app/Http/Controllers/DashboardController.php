<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function adminDashboard(Request $request)
{
    // Query jadwal dengan relasi yang diperlukan
    $query = Schedule::with(['school', 'teachers.user']);

    // Apply filters jika ada
    if ($request->filled('school_id')) {
        $query->where('school_id', $request->school_id);
    }

    if ($request->filled('semester')) {
        $query->where('semester', $request->semester);
    }

    if ($request->filled('academic_year')) {
        $query->where('academic_year', $request->academic_year);
    }

    // Ambil semua jadwal setelah difilter
    $allSchedules = $query->get();

    // Konversi nama hari ke bahasa Indonesia
    $hariIndonesia = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
    ];

    // Susunan hari dalam bahasa Indonesia
    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    // Kelompokkan jadwal berdasarkan hari
    $schedulesByDay = [];
    foreach ($days as $day) {
        $schedulesByDay[$day] = $allSchedules->filter(function ($schedule) use ($day) {
            return $schedule->day === $day;
        })->sortBy('start_time');
    }

    // Data untuk filter
    $schools = School::all();
    $academicYears = Schedule::distinct()->pluck('academic_year');
    $semesters = Schedule::distinct()->pluck('semester');

    // Statistik jumlah sekolah, guru, dan jadwal
    $schoolCount = School::count();
    $teacherCount = Teacher::count();
    $scheduleCount = Schedule::count();

    // Ambil nama hari ini dalam bahasa Indonesia
    $today = $hariIndonesia[now()->format('l')];

    // Jadwal yang akan datang (hari ini)
    $upcomingSchedules = Schedule::with(['school', 'teachers.user'])
        ->where('day', $today)
        ->get();

    // Kehadiran hari ini
    $todayAttendances = Attendance::with(['schedule.teachers.user', 'schedule.school'])
        ->whereDate('created_at', today())
        ->latest()
        ->get();

    // Return ke view
    return view('admin.dashboard', compact(
        'schoolCount',
        'teacherCount',
        'scheduleCount',
        'upcomingSchedules',
        'todayAttendances',
        'schools',
        'academicYears',
        'semesters',
        'schedulesByDay',
        'days'
    ));
}
    
    public function teacherDashboard()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Teacher profile not found!');
        }
        
        // Get current teacher's upcoming schedules
        $currentTime = Carbon::now(); // Waktu sekarang
    
        // Konversi nama hari dari Inggris ke Indonesia
        $hariInggris = Carbon::now()->format('l'); // Nama hari dalam bahasa Inggris
        $hariIndonesia = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        
        $today = $hariIndonesia[$hariInggris]; // Ubah ke bahasa Indonesia

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
