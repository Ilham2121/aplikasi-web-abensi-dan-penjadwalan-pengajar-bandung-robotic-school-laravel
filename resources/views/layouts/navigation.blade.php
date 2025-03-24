<!-- ADMIN MENU -->
@if(Auth::user()->role == 'Admin')
<li class="nav-item {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.dashboard') }}">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span>
    </a>
</li>

<li class="nav-item {{ request()->is('admin/schools*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.schools.index') }}">
        <i class="fas fa-fw fa-school"></i>
        <span>Schools</span>
    </a>
</li>

<li class="nav-item {{ request()->is('admin/teachers*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.teachers.index') }}">
        <i class="fas fa-fw fa-chalkboard-teacher"></i>
        <span>Teachers</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->is('admin/schedules*') ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSchedules"
        aria-expanded="{{ request()->is('admin/schedules*') ? 'true' : 'false' }}" aria-controls="collapseSchedules">
        <i class="fas fa-fw fa-calendar-alt"></i>
        <span>Schedules</span>
    </a>
    <div id="collapseSchedules" class="collapse {{ request()->is('admin/schedules*') ? 'show' : '' }}" aria-labelledby="headingSchedules" data-bs-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item {{ request()->is('admin/schedules') ? 'active' : '' }}" href="{{ route('admin.schedules.index') }}">
                All Schedules
            </a>
            <a class="collapse-item {{ request()->is('admin/schedules/create*') ? 'active' : '' }}" href="{{ route('admin.schedules.create') }}">
                Create Schedule
            </a>
            <a class="collapse-item {{ request()->is('admin/schedules/weekly*') ? 'active' : '' }}" href="{{ route('admin.schedules.weekly') }}">
                Weekly View
            </a>
        </div>
    </div>
</li>

<li class="nav-item {{ request()->is('admin/attendances*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.attendances.index') }}">
        <i class="fas fa-fw fa-clipboard-check"></i>
        <span>Attendances</span>
    </a>
</li>
@endif

<!-- TEACHER MENU -->
@if(Auth::user()->role == 'Teacher')
<li class="nav-item{{ request()->is('teacher/dashboard*') ? ' active' : '' }}">
    <a class="nav-link" href="{{ route('teacher.dashboard') }}">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span>
    </a>
</li>

<li class="nav-item{{ request()->is('teacher/schedules*') ? ' active' : '' }}">
    <a class="nav-link" href="{{ route('teacher.schedules.index') }}">
        <i class="fas fa-fw fa-calendar-alt"></i>
        <span>My Schedules</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->is('teacher/attendances*') ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseAttendances"
        aria-expanded="{{ request()->is('teacher/attendances*') ? 'true' : 'false' }}" aria-controls="collapseAttendances">
        <i class="fas fa-fw fa-clipboard-check"></i>
        <span>Attendance</span>
    </a>
    <div id="collapseAttendances" class="collapse {{ request()->is('teacher/attendances*') ? 'show' : '' }}" aria-labelledby="headingAttendances" data-bs-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item {{ request()->is('teacher/attendances/today*') ? 'active' : '' }}" href="{{ route('teacher.attendances.today') }}">
                Today's Attendance
            </a>
            <a class="collapse-item {{ request()->is('teacher/attendances/history*') ? 'active' : '' }}" href="{{ route('teacher.attendances.history') }}">
                Attendance History
            </a>
        </div>
    </div>
</li>
@endif 