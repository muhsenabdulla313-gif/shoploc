<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Staff Dashboard')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Staff CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/staff.css') }}">

    @yield('styles')
</head>

<body>


    <div class="staffdb-wrap">
        <div class="staffdb-shell">

            <aside class="staffdb-sidebar">
                <div>
                    <div class="side-icons">
                        <a href="{{ route('staff.dashboard') }}"
                            class="side-btn {{ request()->routeIs('staff.dashboard') || request()->routeIs('staff.dashboard.index') ? 'active' : '' }}"
                            title="Dashboard">
                            <i class="fas fa-th-large"></i>
                        </a>
                        <a href="{{ route('staff.staff.members') }}"
                            class="side-btn {{ request()->routeIs('staff.staff.members') ? 'active' : '' }}"
                            title="Staff Members">
                            <i class="fas fa-users"></i>
                        </a>
                        <a href="{{ route('staff.products') }}"
                            class="side-btn {{ request()->routeIs('staff.staff.members') ? 'active' : '' }}"
                            title="Staff Members">
                            <i class="fas fa-users"></i>

                        </a>
                    </div>
                </div>

                <div class="side-icons">
                    <button class="side-btn" type="button" title="Help">
                        <i class="far fa-question-circle"></i>
                    </button>
                    <form id="logoutForm" action="{{ route('staff.logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="side-btn" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </aside>

            <main class="staffdb-main">
                <!-- Topbar -->
                <div class="staffdb-topbar">
                    <div class="page-title">
                        <h2>Welcome, {{ $staff->name ?? 'Staff Member' }}</h2>
                    </div>

                    <div class="top-actions">
                        <div class="userchip">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar">
                                    {{ strtoupper(substr($staff->name ?? 'S', 0, 1)) }}
                                </div>
                                <div class="meta">
                                    <p class="name mb-0">{{ $staff->name ?? 'Staff' }}</p>
                                    <p class="role mb-0">Staff Member</p>
                                </div>
                            </div>
                            <i class="far fa-bell" style="color:#7a7e95;"></i>
                        </div>
                    </div>
                </div>

@yield('content')

        </div>
    </div>
















    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');

        // Desktop: expand/collapse (uses .expanded)
        toggleBtn?.addEventListener('click', () => {
            sidebar.classList.toggle('expanded');
        });

        // Optional: close expanded on outside click in mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 992 &&
                sidebar.classList.contains('expanded') &&
                !sidebar.contains(e.target) &&
                e.target !== toggleBtn &&
                !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('expanded');
            }
        });
    </script>

    @yield('scripts')
</body>

</html>