<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'phone_number',
        'address',
        'photo',
    ];
    
    /**
     * Get the user that owns the teacher.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the schedule assignments for the teacher.
     */
    public function scheduleTeachers()
    {
        return $this->hasMany(ScheduleTeacher::class);
    }
    
    /**
     * Get the schedules for the teacher.
     */
    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'schedule_teachers');
    }
    
    /**
     * Get the attendances for the teacher.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
