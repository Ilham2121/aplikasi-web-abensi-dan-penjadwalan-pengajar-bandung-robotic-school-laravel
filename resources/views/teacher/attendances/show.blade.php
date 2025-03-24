@extends('layouts.app')

@section('title', 'Attendance Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Attendance Details</h2>
        <div>
            <span class="text-muted mr-3">{{ $attendance->created_at->format('l, d F Y') }}</span>
            <a href="{{ route('teacher.attendances.history') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to History
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Attendance Information</h6>
                    <span class="badge {{ $attendance->status == 'approved' ? 'bg-success' : ($attendance->status == 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                        {{ ucfirst($attendance->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">School</h5>
                            <p>{{ $attendance->schedule->school->name }}</p>

                            <h5 class="font-weight-bold mt-3">Day</h5>
                            <p>{{ $attendance->schedule->day }}</p>

                            <h5 class="font-weight-bold mt-3">Time</h5>
                            <p>{{ $attendance->schedule->start_time->format('H:i') }} - {{ $attendance->schedule->end_time->format('H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Type</h5>
                            <p>
                                <span class="badge {{ $attendance->schedule->schedule_type == 'regular' ? 'bg-primary' : 'bg-warning' }}">
                                    {{ ucfirst($attendance->schedule->schedule_type) }}
                                </span>
                            </p>

                            <h5 class="font-weight-bold mt-3">Academic Year</h5>
                            <p>{{ $attendance->schedule->academic_year }}</p>

                            <h5 class="font-weight-bold mt-3">Semester</h5>
                            <p>{{ ucfirst($attendance->schedule->semester) }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="font-weight-bold">Notes</h5>
                        <p class="border p-3 rounded bg-light">{{ $attendance->note ?? 'No notes provided.' }}</p>
                    </div>

                    @if($attendance->status == 'rejected' && $attendance->rejection_reason)
                    <div class="mb-4">
                        <h5 class="font-weight-bold text-danger">Rejection Reason</h5>
                        <p class="border p-3 rounded bg-light text-danger">{{ $attendance->rejection_reason }}</p>
                    </div>
                    @endif

                    <div>
                        <h5 class="font-weight-bold">Submitted At</h5>
                        <p>{{ $attendance->created_at->format('d F Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Attendance Photo</h6>
                </div>
                <div class="card-body text-center">
                    <a href="{{ asset('storage/' . $attendance->photo) }}" target="_blank">
                        <img src="{{ asset('storage/' . $attendance->photo) }}" class="img-fluid rounded mb-3" alt="Attendance Photo">
                    </a>
                    <p class="text-muted">Click on the image to view full size</p>
                </div>
            </div>

            @if($attendance->status == 'pending')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.attendances.destroy', $attendance->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to delete this attendance record?')">
                            <i class="fas fa-trash me-1"></i> Delete Attendance
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 