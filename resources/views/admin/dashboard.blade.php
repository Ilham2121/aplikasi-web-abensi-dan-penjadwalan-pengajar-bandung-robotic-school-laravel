@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid px-4">
    <!-- Dashboard Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 text-gray-800 font-weight-bold mb-1">Welcome Back, {{ Auth::user()->name }}! 👋</h2>
            <p class="text-muted mb-0">Here's what's happening in your school management system.</p>
        </div>
        <div class="bg-white shadow-sm px-4 py-2 rounded-pill">
            <i class="fas fa-calendar-day text-primary me-2"></i>
            <span class="text-gray-600 fw-medium">{{ date('l, d F Y') }}</span>
        </div>
    </div>

    <!-- Stats cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="position-absolute bg-primary" style="width: 4px; height: 100%; left: 0; opacity: 0.8;"></div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-2">Active Schools</div>
                            <div class="d-flex align-items-baseline">
                                <span class="h2 mb-0 fw-bold text-gray-800">{{ number_format($schoolCount) }}</span>
                                <span class="ms-2 text-success small">
                                    <i class="fas fa-arrow-up"></i> 12%
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-school fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="position-absolute bg-success" style="width: 4px; height: 100%; left: 0; opacity: 0.8;"></div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-success text-uppercase mb-2">Total Teachers</div>
                            <div class="d-flex align-items-baseline">
                                <span class="h2 mb-0 fw-bold text-gray-800">{{ number_format($teacherCount) }}</span>
                                <span class="ms-2 text-success small">
                                    <i class="fas fa-arrow-up"></i> 8%
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-chalkboard-teacher fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="position-absolute bg-info" style="width: 4px; height: 100%; left: 0; opacity: 0.8;"></div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-info text-uppercase mb-2">Active Schedules</div>
                            <div class="d-flex align-items-baseline">
                                <span class="h2 mb-0 fw-bold text-gray-800">{{ number_format($scheduleCount) }}</span>
                                <span class="ms-2 text-success small">
                                    <i class="fas fa-arrow-up"></i> 15%
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-calendar fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row g-4">
        <!-- Upcoming Schedules -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-calendar-week me-2"></i>Upcoming Schedules
                    </h6>
                    <a href="{{ route('admin.schedules.weekly') }}" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-calendar-alt me-1"></i>View Weekly Schedule
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="p-4">
                        @foreach ($days as $day)
                            <div class="mb-4">
                                <h6 class="mb-3 pb-2 border-bottom d-flex align-items-center">
                                    <i class="fas fa-calendar-day me-2 text-primary"></i>
                                    {{ $day }}
                                </h6>
                                
                                @if ($schedulesByDay[$day]->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>School</th>
                                                    <th>Time</th>
                                                    <th>Teachers</th>
                                                    <th>Semester</th>
                                                    <th>Academic Year</th>
                                                    <th>Type</th>
                                                    <th width="15%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($schedulesByDay[$day] as $schedule)
                                                    <tr>
                                                        <td class="fw-medium">{{ $schedule->school->name ?? 'N/A' }}</td>
                                                        <td>
                                                            <i class="far fa-clock text-muted me-1"></i>
                                                            {{ date('H:i', strtotime($schedule->start_time)) }} - {{ date('H:i', strtotime($schedule->end_time)) }}
                                                        </td>
                                                        <td>
                                                            @if ($schedule->teachers->count() > 0)
                                                                <ul class="list-unstyled mb-0">
                                                                    @foreach ($schedule->teachers as $teacher)
                                                                        <li>
                                                                            <i class="fas fa-user-tie text-muted me-1"></i>
                                                                            {{ $teacher->user->name ?? 'N/A' }}
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <span class="badge bg-warning">No teachers assigned</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ ucfirst($schedule->semester) }}</td>
                                                        <td>{{ $schedule->academic_year }}</td>
                                                        <td>
                                                            <span class="badge {{ $schedule->schedule_type == 'regular' ? 'bg-primary' : 'bg-info' }}">
                                                                {{ ucfirst($schedule->schedule_type) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <a href="{{ route('admin.schedules.show', $schedule->id) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="View Details">
                                                                    <i class="ri-eye-line"></i>
                                                                </a>
                                                                <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Edit">
                                                                    <i class="ri-pencil-line"></i>
                                                                </a>
                                                                <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')" data-bs-toggle="tooltip" title="Delete">
                                                                        <i class="ri-delete-bin-line"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No schedules found for {{ $day }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Attendance -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-clipboard-check me-2"></i>Today's Attendances
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($todayAttendances->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($todayAttendances as $attendance)
                                <div class="list-group-item border-0 py-3">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h6 class="mb-1 text-dark fw-medium">{{ $attendance->teacher->user->name }}</h6>
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="far fa-clock me-1"></i>{{ $attendance->check_in_time->format('H:i') }}
                                        </span>
                                    </div>
                                    <p class="mb-1 text-primary">
                                        <i class="fas fa-school me-1"></i>
                                        {{ $attendance->schedule->school->name }}
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $attendance->schedule->start_time->format('H:i') }} - {{ $attendance->schedule->end_time->format('H:i') }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted mb-0">No attendance records for today yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border-radius: 0.75rem;
        transition: transform 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-3px);
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
        white-space: nowrap;
    }
    
    .table td {
        font-size: 0.875rem;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.5em 0.75em;
    }
    
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .text-xs {
        font-size: 0.75rem;
    }
    
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
</style>
@endpush
@endsection
