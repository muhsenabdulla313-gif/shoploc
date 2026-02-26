<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Billing Payments</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    
    <!-- External CSS -->
    <link rel="stylesheet" href="/assets/css/billing-style.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-file-invoice me-2"></i>Billing Panel</h4>
        </div>
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('billing') || request()->is('billing/dashboard') ? 'active' : '' }}" href="/billing">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('billing/orders*') ? 'active' : '' }}" href="/billing/orders">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('billing/invoices*') ? 'active' : '' }}" href="/billing/invoices">
                        <i class="fas fa-file-invoice"></i> Invoices
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('billing/reports*') ? 'active' : '' }}" href="/billing/reports">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('billing/payments*') ? 'active' : '' }}" href="/billing/payments">
                        <i class="fas fa-credit-card"></i> Payments
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <nav class="navbar navbar-expand navbar-billing">
            <div class="container-fluid">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="d-flex align-items-center ms-auto">
                    <div class="user-menu">
                        <button class="btn btn-sm">
                            <i class="fas fa-user-circle fa-2x" style="color: var(--primary-color);"></i>
                        </button>
                        <div class="user-dropdown">
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-user me-2"></i> Profile
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-cog me-2"></i> Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('billing.logout') }}" class="dropdown-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('billing.logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content Area -->
        <div class="content-wrapper">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Payments</h2>
                        <button class="btn btn-primary">Process Payment</button>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Payment ID</th>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>#PAY-001</td>
                                            <td>#1001</td>
                                            <td>John Doe</td>
                                            <td>2023-06-15</td>
                                            <td>$125.00</td>
                                            <td>Credit Card</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">View</button>
                                                <button class="btn btn-sm btn-outline-secondary">Refund</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#PAY-002</td>
                                            <td>#1002</td>
                                            <td>Jane Smith</td>
                                            <td>2023-06-14</td>
                                            <td>$89.99</td>
                                            <td>PayPal</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">View</button>
                                                <button class="btn btn-sm btn-outline-secondary">Refund</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#PAY-003</td>
                                            <td>#1003</td>
                                            <td>Robert Johnson</td>
                                            <td>2023-06-14</td>
                                            <td>$210.50</td>
                                            <td>Bank Transfer</td>
                                            <td><span class="badge bg-warning">Pending</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">View</button>
                                                <button class="btn btn-sm btn-outline-secondary">Refund</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#PAY-004</td>
                                            <td>#1004</td>
                                            <td>Emily Davis</td>
                                            <td>2023-06-13</td>
                                            <td>$67.25</td>
                                            <td>Credit Card</td>
                                            <td><span class="badge bg-danger">Failed</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">View</button>
                                                <button class="btn btn-sm btn-outline-secondary">Retry</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#PAY-005</td>
                                            <td>#1005</td>
                                            <td>Michael Wilson</td>
                                            <td>2023-06-12</td>
                                            <td>$154.75</td>
                                            <td>UPI</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">View</button>
                                                <button class="btn btn-sm btn-outline-secondary">Refund</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Toggle sidebar
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('show');
            mainContent.classList.toggle('expanded');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.getElementById('toggleSidebar');
            
            if (window.innerWidth < 992) {
                if (!sidebar.contains(event.target) && event.target !== toggleButton && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    document.getElementById('mainContent').classList.remove('expanded');
                }
            }
        });
    </script>
    
    <!-- External JS -->
    <script src="/assets/js/billing-script.js"></script>
</body>
</html>