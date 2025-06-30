@extends('layouts.app')



    @section('title', 'Attendance History')
    
    @section('content')
    <div class="container-fluid">
        <!-- Header Section with Glassmorphism -->
        <div class="card bg-transparent shadow-none mb-4" style="backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.1);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="text-gradient">Attendance History</h2>
                        <p class="text-muted mb-0">Monitor and manage teacher attendance records</p>
                    </div>
                </div>
            </div>
        </div>
        
        
       <!-- Filter Section -->
<div class="card shadow-lg border-0 mb-4 overflow-hidden">
    <div class="card-header bg-gradient-primary text-white py-3">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Attendance Records</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.attendances.filter') }}" method="GET" class="row g-3">
            <!-- Teacher Name Filter -->
            <div class="col-md-3">
                <label for="teacher_name" class="form-label">Teacher Name</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-user text-muted"></i>
                    </span>
                    <input type="text" 
                           class="form-control border-start-0" 
                           id="teacher_name" 
                           name="teacher_name" 
                           placeholder="Search teacher..."
                           value="{{ request('teacher_name') }}">
                </div>
            </div>

            <!-- School Filter -->
            <div class="col-md-3">
                <label for="school" class="form-label">School</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-school text-muted"></i>
                    </span>
                    <select class="form-select border-start-0" id="school" name="school">
                        <option value="">All Schools</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ request('school') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Date From Filter -->
            <div class="col-md-3">
                <label for="date_from" class="form-label">Date From</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-calendar-alt text-muted"></i>
                    </span>
                    <input type="date" 
                           class="form-control border-start-0" 
                           id="date_from" 
                           name="date_from"
                           value="{{ request('date_from') }}">
                </div>
            </div>

            <!-- Date To Filter -->
            <div class="col-md-3">
                <label for="date_to" class="form-label">Date To</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-calendar-alt text-muted"></i>
                    </span>
                    <input type="date" 
                           class="form-control border-start-0" 
                           id="date_to" 
                           name="date_to"
                           value="{{ request('date_to') }}">
                </div>
            </div>

            <!-- Status Filter -->
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-tag text-muted"></i>
                    </span>
                    <select class="form-select border-start-0" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.attendances.filter') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-1"></i> Reset Filters
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
    
        <!-- Attendance Table Section -->
        <div class="card shadow-lg border-0 overflow-hidden">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clipboard-list me-2"></i>Attendance Records
                </h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>Print
                    </button>
                    <button class="btn btn-sm btn-outline-success">
                        <i class="fas fa-file-excel me-1"></i>Export
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="py-3">Teacher</th>
                                <th class="py-3">School</th>
                                <th class="py-3">Time</th>
                                <th class="py-3">Type</th>
                                <th class="py-3">Status</th>
                                <th class="py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attendances as $attendance)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">{{ $attendance->created_at->format('d M Y') }}</span>
                                        <small class="text-muted">{{ $attendance->created_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        @if($attendance->teacher->photo)
                                            <img src="{{ asset('storage/' . $attendance->teacher->photo) }}" 
                                                 class="rounded-circle me-2" 
                                                 width="40" height="40"
                                                 alt="Teacher photo">
                                        @else
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary me-2 d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $attendance->teacher->user->name }}</div>
                                            <small class="text-muted">{{ $attendance->teacher->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">{{ $attendance->schedule->school->name }}</td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-clock text-muted me-2"></i>
                                        {{ $attendance->schedule->start_time->format('H:i') }} - {{ $attendance->schedule->end_time->format('H:i') }}
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="badge rounded-pill {{ $attendance->schedule->schedule_type == 'regular' ? 'bg-primary' : 'bg-warning' }}">
                                        {{ ucfirst($attendance->schedule->schedule_type) }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="badge rounded-pill {{ 
                                        $attendance->status == 'approved' ? 'bg-success' : 
                                        ($attendance->status == 'rejected' ? 'bg-danger' : 'bg-warning') 
                                    }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.attendances.show', $attendance->id) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           data-bs-toggle="tooltip"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-folder-open text-muted fa-3x mb-3"></i>
                                        <h5 class="text-muted mb-2">No Attendance Records Found</h5>
                                        <p class="text-muted mb-0">Try adjusting your search or filter criteria</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $attendances->firstItem() ?? 0 }} to {{ $attendances->lastItem() ?? 0 }} 
                        of {{ $attendances->total() ?? 0 }} entries
                    </div>
                    {{ $attendances->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
    
    @push('styles')
    <style>
        .text-gradient {
            background: linear-gradient(45deg, #2b2d42, #8d99ae);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .table > :not(caption) > * > * {
            padding: 1rem;
        }
        
        .btn-group .btn {
            border-radius: 0;
        }
        
        .btn-group .btn:first-child {
            border-top-left-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
        }
        
        .btn-group .btn:last-child {
            border-top-right-radius: 0.25rem;
            border-bottom-right-radius: 0.25rem;
        }
    </style>
    @endpush
    
    @push('scripts')
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
    @endpush
@endsection 
