<nav class="nav flex-column">
    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
    </a>
    
    <div class="sidebar-heading">
        Master Data
    </div>
    
    <a class="nav-link {{ request()->routeIs('admin.schools.*') ? 'active' : '' }}" href="{{ route('admin.schools.index') }}">
        <i class="fas fa-school me-2"></i> Schools
    </a>
    
    <a class="nav-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}" href="{{ route('admin.teachers.index') }}">
        <i class="fas fa-chalkboard-teacher me-2"></i> Teachers
    </a>
    
    <div class="sidebar-heading">
        Schedule Management
    </div>
    
    <a class="nav-link {{ request()->routeIs('admin.schedules.index') ? 'active' : '' }}" href="{{ route('admin.schedules.index') }}">
        <i class="fas fa-calendar-alt me-2"></i> All Schedules
    </a>
    
    <a class="nav-link {{ request()->routeIs('admin.schedules.weekly') ? 'active' : '' }}" href="{{ route('admin.schedules.weekly') }}">
        <i class="fas fa-calendar-week me-2"></i> Weekly View
    </a>
    
    <div class="sidebar-heading">
        Attendance Management
    </div>
    
    <a class="nav-link {{ request()->routeIs('admin.attendances.index') ? 'active' : '' }}" href="{{ route('admin.attendances.index') }}">
        <i class="fas fa-clipboard-check me-2"></i> Attendance Approval
    </a>
</nav> 