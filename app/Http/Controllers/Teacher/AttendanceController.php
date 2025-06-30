<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\School;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Teacher']);
    }
    
    /**
     * Display today's schedules for attendance.
     */
    public function today()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
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
        
        // Ambil jadwal untuk hari ini
        $todaySchedules = Schedule::with('school') 
            ->whereHas('teachers', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('day', $today) // Filter hanya jadwal untuk hari ini dalam bahasa Indonesia
            ->orderBy('start_time')
            ->get();
    
        // Loop untuk menambahkan status kehadiran dan apakah jadwal sedang berlangsung
        foreach ($todaySchedules as $schedule) {
            $attendance = Attendance::where('schedule_id', $schedule->id)
                ->whereDate('created_at', Carbon::today())
                ->first();
    
            // Cek apakah jadwal sedang berlangsung berdasarkan waktu saat ini
            $startTime = Carbon::parse($schedule->start_time);
            $endTime = Carbon::parse($schedule->end_time);
            $schedule->is_active = $currentTime->between($startTime, $endTime);
    
            // Tambahkan informasi kehadiran
            $schedule->attendance_status = $attendance ? $attendance->attendance_status : null;
            $schedule->attendance_id = $attendance ? $attendance->id : null;
        }
    
        return view('teacher.attendances.today', compact('todaySchedules', 'currentTime', 'today'));
    }
    


    /**
     * Show the form for creating a new attendance record.
     */
    public function create($id)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
        
        $schedule = Schedule::with('school')
            ->whereHas('teachers', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('id', $id)
            ->firstOrFail();

        // Check if attendance already exists for today
        $existingAttendance = Attendance::where('schedule_id', $schedule->id)
            ->whereDate('created_at', Carbon::today())
            ->first();

        if ($existingAttendance) {
            return redirect()->route('teacher.attendances.today')
                ->with('error', 'You have already submitted attendance for this schedule today.');
        }

        return view('teacher.attendances.create', compact('schedule'));
    }

    /**
     * Store a newly created attendance record.
     */
    public function store(Request $request)
    {
        // Set locale untuk waktu di PHP
        setlocale(LC_TIME, 'id_ID.UTF-8');
        
        // Set locale untuk Carbon
        Carbon::setLocale('id');
    
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
    
        $currentTime = Carbon::now()->format('H:i:s'); // Format HH:MM:SS
        $today = Carbon::now()->translatedFormat('l');// Format waktu sekarang (hari)

        
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'photo' => 'required|image|max:2048',
            'note' => 'nullable|string|max:500',
        ]);
    
        $schedule = Schedule::whereHas('teachers', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('id', $request->schedule_id)
            ->firstOrFail();
    
        // Cek apakah ada jadwal untuk hari ini yang cocok dengan waktu
        $todaySchedules = Schedule::with(['school', 'teachers'])
            ->where('day', $today) // Cek apakah hari ini sesuai dengan enum di DB
            ->whereTime('start_time', '<=', $currentTime) // Mulai sebelum atau sama dengan waktu sekarang
            ->whereTime('end_time', '>=', $currentTime) // Berakhir setelah atau sama dengan waktu sekarang
            ->orderBy('start_time')
            ->get();
    
       
        // Proses lainnya
        $existingAttendance = Attendance::where('schedule_id', $schedule->id)
            ->whereDate('created_at', Carbon::today())
            ->first();
    
        if ($existingAttendance) {
            return redirect()->route('teacher.attendances.today')
                ->with('error', 'You have already submitted attendance for this schedule today.');
        }
    
        // Upload the photo
        $photoPath = $request->file('photo')->store('attendances', 'public');
    
        // Create attendance record
        $attendance = new Attendance();
        $attendance->schedule_id = $schedule->id;
        $attendance->teacher_id = $teacher->id;
        $attendance->attendance_date = Carbon::today()->format('Y-m-d');
        $attendance->check_in_time = Carbon::now()->format('H:i:s');
        $attendance->photo = $photoPath;
        $attendance->note = $request->note;
        $status = $attendance->attendance_status = 1;  

        $attendance->save();
    
        return redirect()->route('teacher.attendances.today')
            ->with('success', 'Attendance has been submitted successfully and is pending approval.');
    }
    

    /**
     * Display attendance history.
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
        
        $query = Attendance::with(['schedule.school'])
            ->whereHas('schedule.teachers', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            });

        // Apply filters
        if ($request->filled('school')) {
            $query->whereHas('schedule', function ($q) use ($request) {
                $q->where('school_id', $request->school);
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

        return view('teacher.attendances.history', compact('attendances', 'schools'));
    }

    /**
     * Display the specified attendance record.
     */
    public function show($id)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
        
        $attendance = Attendance::with('schedule.school')
            ->whereHas('schedule.teachers', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->findOrFail($id);

        return view('teacher.attendances.show', compact('attendance'));
    }

    /**
     * Remove the specified attendance record.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
        
        $attendance = Attendance::with('schedule')
            ->whereHas('schedule.teachers', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->where('status', 'pending')
            ->findOrFail($id);

        // Delete the photo from storage
        if ($attendance->photo) {
            Storage::disk('public')->delete($attendance->photo);
        }

        $attendance->delete();

        return redirect()->route('teacher.attendances.history')
            ->with('success', 'Attendance record has been deleted successfully.');
    }
}
