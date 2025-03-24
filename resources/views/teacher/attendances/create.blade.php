@extends('layouts.app')

@section('title', 'Submit Attendance')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Submit Attendance</h2>
        <span class="text-muted">{{ date('l, d F Y') }}</span>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Attendance Form</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.attendances.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label">School</label>
                            <input type="text" class="form-control" value="{{ $schedule->school->name }}" readonly>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Day</label>
                                <input type="text" class="form-control" value="{{ $schedule->day }}" readonly>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Time</label>
                                <input type="text" class="form-control" value="{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="photo" class="form-label">Attendance Photo <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*" required>
                            <div class="form-text">Take a photo as proof of your attendance. Max size: 2MB</div>
                            
                            @error('photo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="note" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3" placeholder="Add any notes or remarks about today's teaching session">{{ old('note') }}</textarea>
                            
                            @error('note')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('teacher.attendances.today') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Submit Attendance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Schedule Information</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>School:</span>
                            <span class="fw-bold">{{ $schedule->school->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Day:</span>
                            <span class="fw-bold">{{ $schedule->day }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Time:</span>
                            <span class="fw-bold">{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Type:</span>
                            <span class="badge {{ $schedule->schedule_type == 'regular' ? 'bg-primary' : 'bg-warning' }}">
                                {{ ucfirst($schedule->schedule_type) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Academic Year:</span>
                            <span class="fw-bold">{{ $schedule->academic_year }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Semester:</span>
                            <span class="fw-bold">{{ ucfirst($schedule->semester) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 