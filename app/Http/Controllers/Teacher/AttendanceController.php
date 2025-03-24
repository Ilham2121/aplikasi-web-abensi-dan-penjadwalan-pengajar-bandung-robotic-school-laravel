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
        $today = Carbon::now()->format('l');

        $todaySchedules = Schedule::with('school')
            ->whereHas('teachers', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('day', $today)
            ->orderBy('start_time')
            ->get();

        // Get attendance status for today's schedules
        foreach ($todaySchedules as $schedule) {
            $attendance = Attendance::where('schedule_id', $schedule->id)
                ->whereDate('created_at', Carbon::today())
                ->first();
            
            $schedule->attendance_status = $attendance ? $attendance->status : null;
            $schedule->attendance_id = $attendance ? $attendance->id : null;
        }

        return view('teacher.attendances.today', compact('todaySchedules'));
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
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
        
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

        // Check if attendance already exists for today
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
        $attendance->photo = $photoPath;
        $attendance->note = $request->note;
        $attendance->status = 'pending';
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
