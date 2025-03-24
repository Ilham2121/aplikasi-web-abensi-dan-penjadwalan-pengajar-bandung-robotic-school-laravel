<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Teacher\ScheduleController as TeacherScheduleController;
use App\Http\Controllers\Teacher\AttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.change')->middleware('auth');
Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.update')->middleware('auth');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'adminDashboard'])->name('dashboard');
    
    // Schools
    Route::resource('schools', App\Http\Controllers\Admin\SchoolController::class);
    
    // Teachers
    Route::resource('teachers', App\Http\Controllers\Admin\TeacherController::class);

    // Schedules
    Route::get('/schedules/weekly-view', [App\Http\Controllers\Admin\ScheduleController::class, 'weeklyView'])->name('schedules.weekly');
    Route::get('/schedules/{schedule}/assign-teachers', [App\Http\Controllers\Admin\ScheduleController::class, 'showAssignTeachersForm'])->name('schedules.assign-teachers.form');
    Route::post('/schedules/{schedule}/assign-teachers', [App\Http\Controllers\Admin\ScheduleController::class, 'assignTeachers'])->name('schedules.assign-teachers');
    Route::resource('schedules', App\Http\Controllers\Admin\ScheduleController::class);
    
    // Attendances
    Route::get('/attendances', [App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/{attendance}', [App\Http\Controllers\Admin\AttendanceController::class, 'show'])->name('attendances.show');
    Route::post('/attendances/{attendance}/approve', [App\Http\Controllers\Admin\AttendanceController::class, 'approve'])->name('attendances.approve');
    Route::post('/attendances/{attendance}/reject', [App\Http\Controllers\Admin\AttendanceController::class, 'reject'])->name('attendances.reject');
});

// Teacher Routes
Route::middleware(['auth', 'role:Teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'teacherDashboard'])->name('dashboard');
    
    // Schedules
    Route::get('/schedules', [TeacherScheduleController::class, 'index'])->name('schedules.index');
    
    // Attendances
    Route::get('/attendances/today', [AttendanceController::class, 'today'])->name('attendances.today');
    Route::get('/attendances/create/{id}', [AttendanceController::class, 'create'])->name('attendances.create');
    Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('/attendances/history', [AttendanceController::class, 'history'])->name('attendances.history');
    Route::get('/attendances/{id}', [AttendanceController::class, 'show'])->name('attendances.show');
    Route::delete('/attendances/{id}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');
});
