@extends('layouts.app')

@section('title', 'Today\'s Attendance')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Today's Teaching Schedule</h2>
        <span class="text-muted">{{ date('l, d F Y') }}</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">My Schedule for Today</h6>
        </div>
        <div class="card-body">
            @if($todaySchedules->count() > 0)
            
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>School</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todaySchedules as $schedule)
                                <tr>
                                    <td>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</td>
                                    <td>{{ $schedule->school->name }}</td>
                                    <td>
                                        <span class="badge {{ $schedule->schedule_type == 'regular' ? 'bg-primary' : 'bg-warning' }}">
                                            {{ ucfirst($schedule->schedule_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($schedule->attendance_status )
                                        
                                            <span class="badge {{ $schedule->attendance_status == 1 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $schedule->attendance_status == 1 ? 'Hadir' : 'Tidak Hadir' }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning">Status Belum Diperbarui</span>
                                        @endif

                                    </td>
                                    <td>
                                        @if($schedule->attendance_status)
                                            <a href="{{ route('teacher.attendances.show', $schedule->attendance_id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        @else
                                            <a href="{{ route('teacher.attendances.create', $schedule->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-clipboard-check me-1"></i> Submit Attendance
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-4x text-gray-300 mb-3"></i>
                    <p class="mb-0">You don't have any teaching schedules today.</p>
                    <p>Enjoy your day!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 