@extends('layouts.app')

@section('title', 'School Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>School Details</h2>
        <div>
            <a href="{{ route('admin.schools.edit', $school->id) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('admin.schools.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Schools
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ $school->name }}</h6>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="font-weight-bold">School Name</h5>
                    <p>{{ $school->name }}</p>
                </div>
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Contact Person</h5>
                    <p>{{ $school->contact_person }}</p>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Phone Number</h5>
                    <p>{{ $school->phone_number }}</p>
                </div>
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Created At</h5>
                    <p>{{ $school->created_at->format('d F Y H:i') }}</p>
                </div>
            </div>
            
            <div class="mb-4">
                <h5 class="font-weight-bold">Address</h5>
                <p>{{ $school->address }}</p>
            </div>
            
            <div class="mb-4">
                <h5 class="font-weight-bold">Actions</h5>
                <div class="d-flex">
                    <a href="{{ route('admin.schools.edit', $school->id) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-1"></i> Edit School
                    </a>
                    <form action="{{ route('admin.schools.destroy', $school->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this school?')">
                            <i class="fas fa-trash me-1"></i> Delete School
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 