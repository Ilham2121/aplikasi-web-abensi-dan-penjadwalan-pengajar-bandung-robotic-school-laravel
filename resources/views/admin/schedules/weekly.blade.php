@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Weekly Schedule View</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.schedules.index') }}">All Schedules</a></li>
                        <li class="breadcrumb-item active">Weekly View</li>
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
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Filter Weekly Schedules</h5>
                        <div>
                            <a href="{{ route('admin.schedules.create') }}" class="btn btn-success btn-sm">
                                <i class="ri-add-line"></i> Add New Schedule
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.schedules.weekly') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="school_id" class="form-label">School</label>
                                <select class="form-select" id="school_id" name="school_id">
                                    <option value="">All Schools</option>
                                    @foreach ($schools as $school)
                                        <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                            {{ $school->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="semester" class="form-label">Semester</label>
                                <select class="form-select" id="semester" name="semester">
                                    <option value="">All Semesters</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester }}" {{ request('semester') == $semester ? 'selected' : '' }}>
                                            {{ ucfirst($semester) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="academic_year" class="form-label">Academic Year</label>
                                <select class="form-select" id="academic_year" name="academic_year">
                                    <option value="">All Years</option>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Weekly Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @foreach ($days as $day)
                            <h5 class="mt-4 mb-3 border-bottom pb-2">{{ $day }}</h5>
                            
                            @if ($schedulesByDay[$day]->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead>
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
                                                    <td>{{ $schedule->school->name ?? 'N/A' }}</td>
                                                    <td>{{ date('H:i', strtotime($schedule->start_time)) }} - {{ date('H:i', strtotime($schedule->end_time)) }}</td>
                                                    <td>
                                                        @if ($schedule->teachers->count() > 0)
                                                            <ul class="list-unstyled mb-0">
                                                                @foreach ($schedule->teachers as $teacher)
                                                                    <li>{{ $teacher->user->name ?? 'N/A' }}</li>
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
                                                            <a href="{{ route('admin.schedules.show', $schedule->id) }}" class="btn btn-info btn-sm">
                                                                <i class="ri-eye-line"></i>
                                                            </a>
                                                            <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="btn btn-primary btn-sm">
                                                                <i class="ri-pencil-line"></i>
                                                            </a>
                                                            <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this schedule?')">
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
                                <div class="alert alert-info">
                                    No schedules found for {{ $day }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 