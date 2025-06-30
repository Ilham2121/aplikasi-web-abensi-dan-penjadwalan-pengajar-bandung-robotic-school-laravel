@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Dashboard Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 text-gray-800 font-weight-bold">Welcome Back, {{ Auth::user()->name }}!</h2>
            <p class="text-muted">Here's your teaching schedule and attendance overview.</p>
        </div>
        <div class="bg-light px-3 py-2 rounded-pill">
            <i class="fas fa-calendar-day me-2"></i>
            <span class="text-muted">{{ date('l, d F Y') }}</span>
        </div>
    </div>

    <!-- Stats cards -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="position-absolute bg-primary opacity-10" style="width: 8px; height: 100%; left: 0;"></div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Schedules (Current Semester)
                            </div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{ number_format($totalSchedules) }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-calendar fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="position-absolute bg-success opacity-10" style="width: 8px; height: 100%; left: 0;"></div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Attendances This Month
                            </div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{ number_format($attendanceCount) }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-clipboard-check fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upcoming Schedules -->
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-week me-2"></i>Upcoming Schedules
                    </h6>
                    <a href="{{ route('teacher.attendances.today') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-clipboard-list me-1"></i>Today's Attendance
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($upcomingSchedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4">Day</th>
                                        <th>Time</th>
                                        <th>School</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingSchedules as $schedule)
                                    <tr>
                                        <td class="px-4">{{ $schedule->day }}</td>
                                        <td>
                                            <i class="fas fa-clock text-muted me-1"></i>
                                            {{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}
                                        </td>
                                        <td>
                                            <i class="fas fa-school text-muted me-1"></i>
                                            {{ $schedule->school->name }}
                                        </td>
                                        <td>
                                            @php
                                                // Tentukan class badge berdasarkan tipe jadwal
                                                $scheduleType = $schedule->schedule_type ?? 'regular'; // Default 'regular'
                                                $typeClass = $scheduleType === 'regular' ? 'primary' : 'warning';
                                                $typeLabel = ucfirst($scheduleType); // Ubah "regular" jadi "Regular"
                                            @endphp
                                            
                                            <span class="badge bg-{{ $typeClass }} bg-opacity-10 text-{{ $typeClass }}">
                                                <i class="fas fa-tag me-1"></i>
                                                {{ $typeLabel }}
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                <i class="fas fa-check-circle me-1"></i>Active
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted mb-0">No upcoming schedules found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Attendances -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Recent Attendances
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($monthlyAttendances->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($monthlyAttendances->take(5) as $attendance)
                                <div class="list-group-item border-0 py-3">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 text-dark">{{ $attendance->schedule->school->name }}</h6>
                                            <p class="mb-1 text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $attendance->schedule->start_time->format('H:i') }} - {{ $attendance->schedule->end_time->format('H:i') }}
                                            </p>
                                        </div>
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            Present
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($monthlyAttendances->count() > 5)
                            <div class="text-center py-3 border-top">
                                <a href="{{ route('teacher.attendances.history') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-history me-1"></i>View All History
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted mb-0">No attendance records for this month yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .opacity-10 {
        opacity: 0.1;
    }
    
    .card {
        transition: transform 0.2s ease-in-out;
        border-radius: 0.75rem;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
    
    .card-header {
        border-top-left-radius: 0.75rem !important;
        border-top-right-radius: 0.75rem !important;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.5em 0.75em;
    }
    
    .bg-opacity-10 {
        opacity: 0.1;
    }
    
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    
    .btn-outline-primary:hover {
        background-color: #4e73df;
        border-color: #4e73df;
        color: white;
    }
</style>
@endpush
@endsection 
