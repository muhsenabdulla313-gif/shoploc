<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Your Admin Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="sidebar fixed top-0 left-0 h-full bg-[var(--sidebar-bg)] text-white w-64 z-40">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    ShopLoc
                </h1>
            </div>

            <nav class="mt-6">
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-item block px-6 py-3 text-blue-400 hover:bg-[var(--hover-color)] transition-all duration-200">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>

                <a href="{{ route('admin.products') }}"
                   class="sidebar-item block px-6 py-3 text-gray-300 hover:text-white hover:bg-[var(--hover-color)] transition-all duration-200">
                    <i class="fas fa-box mr-3"></i>
                    Products
                </a>

                <a href="{{ route('admin.orders') }}"
                   class="sidebar-item block px-6 py-3 text-gray-300 hover:text-white hover:bg-[var(--hover-color)] transition-all duration-200">
                    <i class="fas fa-shopping-cart mr-3"></i>
                    Orders
                </a>

                <a href="{{ route('admin.users') }}"
                   class="sidebar-item block px-6 py-3 text-gray-300 hover:text-white hover:bg-[var(--hover-color)] transition-all duration-200">
                    <i class="fas fa-users mr-3"></i>
                    Users
                </a>

                <a href="{{ route('admin.staff.manage') }}"
                   class="sidebar-item block px-6 py-3 text-gray-300 hover:text-white hover:bg-[var(--hover-color)] transition-all duration-200">
                    <i class="fas fa-user-tie mr-3"></i>
                    Staff
                </a>

                <a href="{{ route('admin.billing.staff') }}"
                   class="sidebar-item block px-6 py-3 text-gray-300 hover:text-white hover:bg-[var(--hover-color)] transition-all duration-200">
                    <i class="fas fa-file-invoice-dollar mr-3"></i>
                    Billing Staff
                </a>

                <a href="/admin/trendy-products"
                   class="sidebar-item block px-6 py-3 text-gray-300 hover:text-white hover:bg-[var(--hover-color)] transition-all duration-200">
                    <i class="fas fa-fire mr-3"></i>
                    Trendy Products
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="ml-64 flex-1">
            <!-- Header -->
            <header class="bg-[var(--header-bg)] shadow-sm p-4 flex justify-between items-center sticky top-0 z-30">
                <div class="flex items-center">
                    <button id="sidebar-toggle" class="lg:hidden mr-4 text-gray-600" type="button">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-xl font-semibold">@yield('page-title', 'Dashboard')</h2>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <img
                            src="https://ui-avatars.com/api/?name={{ Auth::guard('staff')->user()->name ?? 'Admin' }}&background=4f46e5&color=fff"
                            alt="Profile"
                            class="w-8 h-8 rounded-full">
                        <span class="hidden md:inline text-gray-700">
                            {{ Auth::guard('staff')->user()->name ?? 'Admin' }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('staff.logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition-colors">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // Mobile sidebar toggle
        const toggleBtn = document.getElementById('sidebar-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                const sidebar = document.querySelector('.sidebar');
                if (!sidebar) return;

                sidebar.classList.toggle('w-16');
                sidebar.classList.toggle('w-64');
            });
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) alert.style.display = 'none';
        }, 5000);
    </script>

    {{-- ✅ THIS IS REQUIRED (for @push('scripts') in pages) --}}
    @stack('scripts')
</body>
</html>
