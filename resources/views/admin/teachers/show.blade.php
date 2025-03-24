@extends('layouts.app')

@section('title', 'Teacher Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Teacher Details</h2>
        <div>
            <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Teachers
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile</h6>
                </div>
                <div class="card-body text-center">
                    @if($teacher->photo)
                        <img src="{{ asset('storage/' . $teacher->photo) }}" alt="{{ $teacher->user->name }}" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
                    @else
                        <img src="{{ asset('images/default-user.png') }}" alt="Default Profile" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
                    @endif
                    
                    <h4 class="font-weight-bold mb-0">{{ $teacher->user->name }}</h4>
                    <p class="text-muted">Teacher</p>
                    
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit Profile
                        </a>
                        <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100 mt-2" onclick="return confirm('Are you sure you want to delete this teacher? This will also delete their user account.')">
                                <i class="fas fa-trash me-1"></i> Delete Teacher
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Full Name</div>
                        <div class="col-md-8">{{ $teacher->user->name }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Email</div>
                        <div class="col-md-8">{{ $teacher->user->email }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Phone Number</div>
                        <div class="col-md-8">{{ $teacher->phone_number }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Address</div>
                        <div class="col-md-8">{{ $teacher->address }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Account Status</div>
                        <div class="col-md-8">
                            <span class="badge {{ $teacher->user->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $teacher->user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 font-weight-bold">Joined Date</div>
                        <div class="col-md-8">{{ $teacher->created_at->format('d F Y') }}</div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Teaching Schedules</h6>
                </div>
                <div class="card-body">
                    @if($teacher->schedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Time</th>
                                        <th>School</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teacher->schedules as $schedule)
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
                        <p class="text-center text-muted mb-0">No teaching schedules assigned.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 