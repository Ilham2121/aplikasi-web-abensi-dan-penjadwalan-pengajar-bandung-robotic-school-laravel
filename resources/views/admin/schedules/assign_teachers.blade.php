@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Assign Teachers to Schedule</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.schedules.index') }}">All Schedules</a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.schedules.show', $schedule->id) }}">Schedule Details</a>
                        </li>
                        <li class="breadcrumb-item active">Assign Teachers</li>
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
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Schedule Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <h5 class="text-muted fw-bold">School</h5>
                            <p class="mb-0">{{ $schedule->school->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3 mb-4">
                            <h5 class="text-muted fw-bold">Day</h5>
                            <p class="mb-0">{{ $schedule->day }}</p>
                        </div>
                        <div class="col-md-3 mb-4">
                            <h5 class="text-muted fw-bold">Time</h5>
                            <p class="mb-0">{{ date('H:i', strtotime($schedule->start_time)) }} - {{ date('H:i', strtotime($schedule->end_time)) }}</p>
                        </div>
                        <div class="col-md-3 mb-4">
                            <h5 class="text-muted fw-bold">Type</h5>
                            <p class="mb-0">
                                <span class="badge {{ $schedule->schedule_type == 'regular' ? 'bg-primary' : 'bg-info' }}">
                                    {{ ucfirst($schedule->schedule_type) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Assign Teachers</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.schedules.assign-teachers', $schedule->id) }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                @error('teacher_ids')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="select-all">
                                                <label class="form-check-label fw-bold" for="select-all">
                                                    Select/Deselect All
                                                </label>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            @foreach ($teachers as $teacher)
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input teacher-checkbox" type="checkbox" name="teacher_ids[]" 
                                                            value="{{ $teacher->id }}" id="teacher_{{ $teacher->id }}"
                                                            {{ (old('teacher_ids') && in_array($teacher->id, old('teacher_ids', []))) || 
                                                                (in_array($teacher->id, $assignedTeacherIds)) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="teacher_{{ $teacher->id }}">
                                                            {{ $teacher->user->name ?? 'N/A' }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('admin.schedules.show', $schedule->id) }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Assignments</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select/Deselect All functionality
        const selectAllCheckbox = document.getElementById('select-all');
        const teacherCheckboxes = document.querySelectorAll('.teacher-checkbox');
        
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            
            teacherCheckboxes.forEach(function(checkbox) {
                checkbox.checked = isChecked;
            });
        });
        
        // Update "Select All" checkbox state based on individual checkboxes
        function updateSelectAllCheckbox() {
            const checkboxesCount = teacherCheckboxes.length;
            const checkedCount = document.querySelectorAll('.teacher-checkbox:checked').length;
            
            selectAllCheckbox.checked = checkboxesCount === checkedCount;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < checkboxesCount;
        }
        
        // Add event listeners to all teacher checkboxes
        teacherCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', updateSelectAllCheckbox);
        });
        
        // Initialize the select all checkbox state
        updateSelectAllCheckbox();
    });
</script>
@endsection 