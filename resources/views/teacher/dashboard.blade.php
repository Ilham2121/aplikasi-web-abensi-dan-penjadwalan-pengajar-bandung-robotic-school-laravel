@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Teacher Dashboard</h2>
        <div>
            <span class="text-muted">Today: {{ date('l, d F Y') }}</span>
        </div>
    </div>

    <!-- Stats cards -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Schedules (Current Semester)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSchedules }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Attendances This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attendanceCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Today's Schedules -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Schedules</h6>
                    <a href="{{ route('teacher.attendances.today') }}" class="btn btn-sm btn-primary">Today's Attendance</a>
                </div>
                <div class="card-body">
                    @if($upcomingSchedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Time</th>
                                        <th>School</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingSchedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->day }}</td>
                                        <td>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</td>
                                        <td>{{ $schedule->school->name }}</td>
                                        <td>
                                            <span class="badge {{ $schedule->schedule_type == 'regular' ? 'bg-primary' : 'bg-warning' }}">
                                                {{ ucfirst($schedule->schedule_type) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-gray-300 mb-2"></i>
                            <p>No upcoming schedules found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Attendances -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Attendances</h6>
                </div>
                <div class="card-body">
                    @if($monthlyAttendances->count() > 0)
                        <div class="list-group">
                            @foreach($monthlyAttendances->take(5) as $attendance)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $attendance->schedule->school->name }}</h6>
                                        <small>{{ $attendance->check_in_time->format('H:i') }}</small>
                                    </div>
                                    <p class="mb-1">{{ $attendance->attendance_date->format('d M Y') }} ({{ $attendance->schedule->day }})</p>
                                    <small>{{ $attendance->schedule->start_time->format('H:i') }} - {{ $attendance->schedule->end_time->format('H:i') }}</small>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($monthlyAttendances->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('teacher.attendances.history') }}" class="btn btn-sm btn-primary">View All</a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-2"></i>
                            <p>No attendance records for this month yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 