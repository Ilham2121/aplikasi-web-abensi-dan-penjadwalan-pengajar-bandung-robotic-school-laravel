@extends('layouts.app')

@section('title', 'My Schedules')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Teaching Schedules</h2>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Schedule List</h6>
            <div class="ml-auto">
                <form action="{{ route('teacher.schedules.index') }}" method="GET" class="form-inline">
                    <div class="input-group mr-2 mb-2">
                        <select name="day" class="form-select">
                            <option value="">-- All Days --</option>
                            <option value="Senin" {{ request('day') == 'Senin' ? 'selected' : '' }}>Senin</option>
                            <option value="Selasa" {{ request('day') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                            <option value="Rabu" {{ request('day') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                            <option value="Kamis" {{ request('day') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                            <option value="Jumat" {{ request('day') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                            <option value="Sabtu" {{ request('day') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                            <option value="Minggu" {{ request('day') == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                        </select>
                    </div>
                    
                    <div class="input-group mr-2 mb-2">
                        <select name="type" class="form-select">
                            <option value="">-- All Types --</option>
                            <option value="regular" {{ request('type') == 'regular' ? 'selected' : '' }}>Regular</option>
                            <option value="private" {{ request('type') == 'private' ? 'selected' : '' }}>Private</option>
                        </select>
                    </div>
                    
                    <div class="input-group mr-2 mb-2">
                        <select name="school_id" class="form-select">
                            <option value="">-- All Schools --</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-sm btn-primary mb-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    
                    @if(request()->anyFilled(['day', 'type', 'school_id']))
                        <a href="{{ route('teacher.schedules.index') }}" class="btn btn-sm btn-secondary mb-2 ms-1">
                            <i class="fas fa-times me-1"></i> Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Time</th>
                            <th>School</th>
                            <th>Schedule Type</th>
                            <th>Academic Year</th>
                            <th>Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                            <tr>
                                <td>{{ $schedule->day }}</td>
                                <td>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</td>
                                <td>{{ $schedule->school->name }}</td>
                                <td>
                                    <span class="badge {{ $schedule->schedule_type == 'regular' ? 'bg-primary' : 'bg-warning' }}">
                                        {{ ucfirst($schedule->schedule_type) }}
                                    </span>
                                </td>
                                <td>{{ $schedule->academic_year }}</td>
                                <td>{{ ucfirst($schedule->semester) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No schedules found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $schedules->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 