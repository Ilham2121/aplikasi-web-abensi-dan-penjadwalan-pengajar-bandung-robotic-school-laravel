<nav class="nav flex-column">
    <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" href="{{ route('teacher.dashboard') }}">
        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
    </a>
    
    <div class="sidebar-heading">
        Teaching
    </div>
    
    <a class="nav-link {{ request()->routeIs('teacher.schedules.index') ? 'active' : '' }}" href="{{ route('teacher.schedules.index') }}">
        <i class="fas fa-calendar-alt me-2"></i> My Schedules
    </a>
    
    <a class="nav-link {{ request()->routeIs('teacher.attendances.today') ? 'active' : '' }}" href="{{ route('teacher.attendances.today') }}">
        <i class="fas fa-clipboard-list me-2"></i> Today's Attendance
    </a>
    
    <a class="nav-link {{ request()->routeIs('teacher.attendances.history') ? 'active' : '' }}" href="{{ route('teacher.attendances.history') }}">
        <i class="fas fa-history me-2"></i> Attendance History
    </a>
</nav> 