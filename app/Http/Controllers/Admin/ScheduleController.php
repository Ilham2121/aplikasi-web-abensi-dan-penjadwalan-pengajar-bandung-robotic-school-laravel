<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Schedule::with(['school', 'teachers.user']);
        
        // Apply filters
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }
        
        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }
        
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }
        
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        
        if ($request->filled('schedule_type')) {
            $query->where('schedule_type', $request->schedule_type);
        }
        
        $schedules = $query->orderBy('day')->orderBy('start_time')->paginate(15);
        $schools = School::all();
        
        // Get unique academic years and semesters for filters
        $academicYears = Schedule::select('academic_year')->distinct()->pluck('academic_year');
        $semesters = Schedule::select('semester')->distinct()->pluck('semester');
        
        return view('admin.schedules.index', compact('schedules', 'schools', 'academicYears', 'semesters'));
    }

    /**
     * Show the form for creating a new resource.
     */

     private function getEnumValues($table, $column)
{
    $enumValues = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = ?", [$column]);
    $enum = $enumValues[0]->Type;

    preg_match_all("/'([^']+)'/", $enum, $matches);

    return $matches[1]; // Mengembalikan array nilai enum
}
    public function create()
    {
        $schools = School::all();
        $teachers = Teacher::with('user')->get();
        
        // Days of week options
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        $scheduleTypes = $this->getEnumValues('schedules', 'schedule_type');
       
        // Semester options
        $semesters = ['ganjil', 'genap'];
        
        // Generate academic year options (current year - 1 to current year + 2)
        $currentYear = date('Y');
        $academicYears = [];
        for ($i = -1; $i <= 2; $i++) {
            $year = $currentYear + $i;
            $academicYears[] = $year . '/' . ($year + 1);
        }
        
        return view('admin.schedules.create', compact('schools', 'teachers', 'days', 'scheduleTypes', 'semesters', 'academicYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
   
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'day' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'schedule_type' => 'required|in:regular,private',
            'semester' => 'required|in:ganjil,genap',
            'academic_year' => 'required|string',
            'semester_start' => 'required|date',
            'semester_end' => 'required|date|after:semester_start',
            'teacher_ids' => 'required|array',
            'teacher_ids.*' => 'exists:teachers,id',
        ]);
        DB::beginTransaction();
       
        
        try {
            // Create schedule
            $schedule = Schedule::create([
                'school_id' => $request->school_id,
                'day' => $request->day,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'schedule_type' => $request->schedule_type,
                'semester' => $request->semester,
                'academic_year' => $request->academic_year,
                'semester_start' => $request->semester_start,
                'semester_end' => $request->semester_end,
                'is_active' => true,
            ]);
          
            // Attach teachers to schedule
            $schedule->teachers()->attach($request->teacher_ids);
            
            DB::commit();
            
            return redirect()->route('admin.schedules.index')
                ->with('success', 'Schedule created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create schedule: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = Schedule::with(['school', 'teachers.user'])->findOrFail($id);
        return view('admin.schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $schedule = Schedule::with(['teachers'])->findOrFail($id);
        $schools = School::all();
        $teachers = Teacher::with('user')->get();
        
        // Days of week options
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        // Schedule type options
        $scheduleTypes = ['regular', 'substitute'];
        
        // Semester options
        $semesters = ['ganjil', 'genap'];
        
        // Generate academic year options (current year - 1 to current year + 2)
        $currentYear = date('Y');
        $academicYears = [];
        for ($i = -1; $i <= 2; $i++) {
            $year = $currentYear + $i;
            $academicYears[] = $year . '/' . ($year + 1);
        }
        
        // Get assigned teacher IDs
        $assignedTeacherIds = $schedule->teachers->pluck('id')->toArray();
        
        return view('admin.schedules.edit', compact('schedule', 'schools', 'teachers', 'days', 'scheduleTypes', 'semesters', 'academicYears', 'assignedTeacherIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'day' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'schedule_type' => 'required|in:regular,substitute',
            'semester' => 'required|in:ganjil,genap',
            'academic_year' => 'required|string',
            'semester_start' => 'required|date',
            'semester_end' => 'required|date|after:semester_start',
            'teacher_ids' => 'required|array',
            'teacher_ids.*' => 'exists:teachers,id',
        ]);
        
        DB::beginTransaction();
        
        try {
            $schedule = Schedule::findOrFail($id);
            
            // Update schedule
            $schedule->update([
                'school_id' => $request->school_id,
                'day' => $request->day,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'schedule_type' => $request->schedule_type,
                'semester' => $request->semester,
                'academic_year' => $request->academic_year,
                'semester_start' => $request->semester_start,
                'semester_end' => $request->semester_end,
            ]);
            
            // Sync teachers
            $schedule->teachers()->sync($request->teacher_ids);
            
            DB::commit();
            
            return redirect()->route('admin.schedules.index')
                ->with('success', 'Schedule updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update schedule: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            
            // Detach all teachers first
            $schedule->teachers()->detach();
            
            // Delete schedule
            $schedule->delete();
            
            return redirect()->route('admin.schedules.index')
                ->with('success', 'Schedule deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.schedules.index')
                ->with('error', 'Failed to delete schedule: ' . $e->getMessage());
        }
    }
    
    /**
     * Display weekly view of schedules.
     */
    public function weeklyView(Request $request)
    {
        $query = Schedule::with(['school', 'teachers.user']);
        
        // Apply filters
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }
        
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }
        
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        
        // Get all schedules
        $allSchedules = $query->get();
        
        // Group schedules by day
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $schedulesByDay = [];
        
        foreach ($days as $day) {
            $schedulesByDay[$day] = $allSchedules->filter(function($schedule) use ($day) {
                return $schedule->day === $day;
            })->sortBy('start_time');
        }
        
        $schools = School::all();
        
        // Get unique academic years and semesters for filters
        $academicYears = Schedule::select('academic_year')->distinct()->pluck('academic_year');
        $semesters = Schedule::select('semester')->distinct()->pluck('semester');
        
        return view('admin.schedules.weekly', compact('schedulesByDay', 'days', 'schools', 'academicYears', 'semesters'));
    }
    
    /**
     * Show form to assign teachers to a schedule.
     */
    public function showAssignTeachersForm($id)
    {
        $schedule = Schedule::with(['teachers'])->findOrFail($id);
        $teachers = Teacher::with('user')->get();
        
        // Get assigned teacher IDs
        $assignedTeacherIds = $schedule->teachers->pluck('id')->toArray();
        
        return view('admin.schedules.assign_teachers', compact('schedule', 'teachers', 'assignedTeacherIds'));
    }
    
    /**
     * Assign teachers to a schedule.
     */
    public function assignTeachers(Request $request, $id)
    {
        $request->validate([
            'teacher_ids' => 'required|array',
            'teacher_ids.*' => 'exists:teachers,id',
        ]);
        
        try {
            $schedule = Schedule::findOrFail($id);
            
            // Sync teachers
            $schedule->teachers()->sync($request->teacher_ids);
            
            return redirect()->route('admin.schedules.show', $id)
                ->with('success', 'Teachers assigned successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to assign teachers: ' . $e->getMessage())
                ->withInput();
        }
    }
}

