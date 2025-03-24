@extends('layouts.app')

@section('title', 'Attendance Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Attendance Details</h2>
        <div>
            <span class="text-muted mr-3">{{ $attendance->created_at->format('l, d F Y') }}</span>
            <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Attendance List
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

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
                            <h5 class="font-weight-bold">Teacher</h5>
                            <p>{{ $attendance->schedule->teacher->user->name }}</p>

                            <h5 class="font-weight-bold mt-3">School</h5>
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
                    <form action="{{ route('admin.attendances.approve', $attendance->id) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-1"></i> Approve Attendance
                        </button>
                    </form>
                    
                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-times me-1"></i> Reject Attendance
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.attendances.reject', $attendance->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('rejection_reason') is-invalid @enderror" id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
                        
                        @error('rejection_reason')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 