<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'day',
        'start_time',
        'end_time',
        'schedule_type',
        'semester',
        'academic_year',
        'semester_start',
        'semester_end',
        'is_active',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'semester_start' => 'date',
        'semester_end' => 'date',
        'is_active' => 'boolean',
    ];
    
    /**
     * Get the school that owns the schedule.
     */
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
    
    /**
     * Get the schedule assignments for the schedule.
     */
    public function scheduleTeachers()
    {
        return $this->hasMany(ScheduleTeacher::class);
    }
    
    /**
     * Get the teachers for the schedule.
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'schedule_teachers');
    }
    
    /**
     * Get the attendances for the schedule.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    
    /**
     * Scope a query to only include active schedules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope a query to only include current semester schedules.
     */
    public function scopeCurrentSemester($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('semester_start', '<=', $today)
                    ->where('semester_end', '>=', $today);
    }
    
    /**
     * Scope a query to get schedules for the next 7 days.
     */
    public function scopeNextSevenDays($query)
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $todayIndex = now()->dayOfWeek;
        
        // Convert Sunday (0) to 6 for our array
        $todayIndex = $todayIndex === 0 ? 6 : $todayIndex - 1;
        
        $relevantDays = [];
        for($i = 0; $i < 7; $i++) {
            $index = ($todayIndex + $i) % 7;
            $relevantDays[] = $days[$index];
        }
        
        return $query->whereIn('day', $relevantDays);
    }
}
