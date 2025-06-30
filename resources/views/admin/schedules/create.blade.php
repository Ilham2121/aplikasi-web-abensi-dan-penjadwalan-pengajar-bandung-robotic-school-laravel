@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Create New Schedule</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.schedules.index') }}">All Schedules</a></li>
                        <li class="breadcrumb-item active">Create New Schedule</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
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
                    <h5 class="card-title mb-0">Schedule Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.schedules.store') }}" method="POST" id="scheduleForm">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="schedule_type">Schedule Type <span class="text-danger">*</span></label>
                                <select name="schedule_type" id="schedule_type" class="form-control">
                                    @foreach ($scheduleTypes as $type)
                                        <option value="{{ strtolower($type) }}" {{ strtolower(old('schedule_type')) == strtolower($type) ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="school_id" class="form-label">School <span class="text-danger">*</span></label>
                                <!-- Hidden input untuk menyimpan STMIK school ID -->
                                <input type="hidden" name="school_id" id="hidden_school_id" value="{{ $schools->firstWhere('name', 'STMIK')->id ?? '' }}">
                                <select class="form-select @error('school_id') is-invalid @enderror" 
                                        id="school_id" 
                                        {{ (old('schedule_type') == 'private') ? 'disabled' : '' }}>
                                    <option value="">Select School</option>
                                    @foreach ($schools as $school)
                                        <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                            {{ $school->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('school_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            
                            <div class="col-md-6">
                                <label for="day" class="form-label">Day <span class="text-danger">*</span></label>
                                <select class="form-select @error('day') is-invalid @enderror" id="day" name="day" required>
                                    <option value="">Select Day</option>
                                    @foreach ($days as $day)
                                        <option value="{{ $day }}" {{ old('day') == $day ? 'selected' : '' }}>
                                            {{ $day }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('day')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6 ">
                                <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                <select class="form-select @error('semester') is-invalid @enderror" id="semester" name="semester" required>
                                    <option value="">Select Semester</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester }}" {{ old('semester') == $semester ? 'selected' : '' }}>
                                            {{ ucfirst($semester) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('semester')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">                            
                            <div class="col-md-6">
                                <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <select class="form-select @error('academic_year') is-invalid @enderror" id="academic_year" name="academic_year" required>
                                    <option value="">Select Academic Year</option>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year }}" {{ old('academic_year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="semester_start" class="form-label">Semester Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('semester_start') is-invalid @enderror" id="semester_start" name="semester_start" value="{{ old('semester_start') }}" required>
                                @error('semester_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="semester_end" class="form-label">Semester End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('semester_end') is-invalid @enderror" id="semester_end" name="semester_end" value="{{ old('semester_end') }}" required>
                                @error('semester_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="teacher_ids" class="form-label">Assign Teachers <span class="text-danger">*</span></label>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach ($teachers as $teacher)
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="teacher_ids[]" 
                                                            value="{{ $teacher->id }}" id="teacher_{{ $teacher->id }}"
                                                            {{ (old('teacher_ids') && in_array($teacher->id, old('teacher_ids', []))) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="teacher_{{ $teacher->id }}">
                                                            {{ $teacher->user->name ?? 'N/A' }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('teacher_ids')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create Schedule</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



   
    

</div>

@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const scheduleType = document.getElementById('schedule_type');
    const schoolSelect = document.getElementById('school_id');
    const hiddenSchoolId = document.getElementById('hidden_school_id');
    const stmikSchoolId = '{{ $schools->firstWhere("name", "STMIK")->id ?? "" }}';

    function toggleSchoolSelect() {
        const selectedType = scheduleType.value.toLowerCase();
        console.log('Selected schedule_type:', selectedType);

        if (selectedType === 'private') {
            console.log('Disabling school select and setting STMIK ID:', stmikSchoolId);
            schoolSelect.disabled = true;
            hiddenSchoolId.value = stmikSchoolId; // Set hidden input value
            if (stmikSchoolId) {
                schoolSelect.value = stmikSchoolId;
            }
        } else {
            console.log('Enabling school select');
            schoolSelect.disabled = false;
            hiddenSchoolId.value = schoolSelect.value; // Update hidden input with select value
        }
    }

    // Jalankan saat load dan saat select berubah
    toggleSchoolSelect();
    scheduleType.addEventListener('change', toggleSchoolSelect);
    
    // Update hidden input when select changes
    schoolSelect.addEventListener('change', function() {
        if (!schoolSelect.disabled) {
            hiddenSchoolId.value = this.value;
        }
    });

    // Validasi waktu
    const endTime = document.getElementById('end_time');
    const startTime = document.getElementById('start_time');
    endTime.addEventListener('change', function () {
        if (startTime.value && endTime.value && startTime.value >= endTime.value) {
            alert('End time must be after start time');
            endTime.value = '';
        }
    });

    // Validasi tanggal semester
    const semesterStart = document.getElementById('semester_start');
    const semesterEnd = document.getElementById('semester_end');
    semesterEnd.addEventListener('change', function () {
        if (semesterStart.value && semesterEnd.value && semesterStart.value >= semesterEnd.value) {
            alert('Semester end date must be after start date');
            semesterEnd.value = '';
        }
    });
});
</script>
@endpush

