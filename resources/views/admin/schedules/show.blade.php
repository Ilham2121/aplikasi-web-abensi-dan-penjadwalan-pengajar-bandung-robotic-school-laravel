@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Schedule Details</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.schedules.index') }}">All Schedules</a></li>
                        <li class="breadcrumb-item active">Schedule Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Schedule Information</h5>
                        <div>
                            <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="btn btn-primary btn-sm me-2">
                                <i class="ri-pencil-line"></i> Edit
                            </a>
                            <a href="{{ route('admin.schedules.assign-teachers.form', $schedule->id) }}" class="btn btn-info btn-sm me-2">
                                <i class="ri-user-add-line"></i> Assign Teachers
                            </a>
                            <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this schedule?')">
                                    <i class="ri-delete-bin-line"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h5 class="text-muted fw-bold">School</h5>
                            <p class="mb-0">{{ $schedule->school->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h5 class="text-muted fw-bold">Schedule Type</h5>
                            <p class="mb-0">
                                <span class="badge {{ $schedule->schedule_type == 'regular' ? 'bg-primary' : 'bg-info' }}">
                                    {{ ucfirst($schedule->schedule_type) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h5 class="text-muted fw-bold">Day</h5>
                            <p class="mb-0">{{ $schedule->day }}</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h5 class="text-muted fw-bold">Time</h5>
                            <p class="mb-0">{{ date('H:i', strtotime($schedule->start_time)) }} - {{ date('H:i', strtotime($schedule->end_time)) }}</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h5 class="text-muted fw-bold">Semester</h5>
                            <p class="mb-0">{{ ucfirst($schedule->semester) }}</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h5 class="text-muted fw-bold">Academic Year</h5>
                            <p class="mb-0">{{ $schedule->academic_year }}</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h5 class="text-muted fw-bold">Semester Start Date</h5>
                            <p class="mb-0">{{ date('d F Y', strtotime($schedule->semester_start)) }}</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h5 class="text-muted fw-bold">Semester End Date</h5>
                            <p class="mb-0">{{ date('d F Y', strtotime($schedule->semester_end)) }}</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h5 class="text-muted fw-bold">Status</h5>
                            <p class="mb-0">
                                @if ($schedule->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Assigned Teachers</h5>
                </div>
                <div class="card-body">
                    @if ($schedule->teachers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schedule->teachers as $teacher)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.teachers.show', $teacher->id) }}">
                                                    {{ $teacher->user->name ?? 'N/A' }}
                                                </a>
                                            </td>
                                            <td>{{ $teacher->user->email ?? 'N/A' }}</td>
                                            <td>{{ $teacher->phone_number ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            No teachers assigned to this schedule.
                            <a href="{{ route('admin.schedules.assign-teachers.form', $schedule->id) }}" class="alert-link">
                                Assign teachers now
                            </a>.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 