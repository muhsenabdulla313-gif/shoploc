@extends('staff.layout')

@section('title', 'Profile')

@section('content')
<div class="staffdb-wrap">
    <div class="staffdb-shell">
        <!-- Sidebar -->
        <aside class="staffdb-sidebar">
            <div>
                <div class="side-icons">
                    <a href="{{ route('staff.dashboard') }}" class="side-btn {{ request()->routeIs('staff.dashboard') || request()->routeIs('staff.dashboard.index') ? 'active' : '' }}" title="Dashboard">
                        <i class="fas fa-th-large"></i>
                    </a>
                    <a href="{{ route('staff.staff.members') }}" class="side-btn {{ request()->routeIs('staff.staff.members') ? 'active' : '' }}" title="Staff Members">
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

        <!-- Main -->
        <main class="staffdb-main">
            <!-- Topbar -->
            <div class="staffdb-topbar">
                <div class="page-title">
                    <h2>Your Profile</h2>
                </div>
            </div>

            <!-- Your Information Section -->
            <section class="panel mb-4">
                <div class="panel-head">
                    <h3 class="mb-0">Your Information</h3>
                </div>
                
                <div class="p-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Name:</strong> {{ $staff->name ?? 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Email:</strong> {{ $staff->email ?? 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Phone:</strong> {{ $staff->phone ?? 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Join Date:</strong> {{ $staff->created_at ? \Carbon\Carbon::parse($staff->created_at)->format('d M Y') : 'N/A' }}
                        </div>
                    </div>
                    
                    @if($staff->bank_account_number || $staff->bank_name || $staff->ifsc_code)
                    <div class="border-top pt-3 mt-3">
                        <h5>Bank Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Account Number:</strong> {{ $staff->bank_account_number ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Bank Name:</strong> {{ $staff->bank_name ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>IFSC Code:</strong> {{ $staff->ifsc_code ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($staff->city || $staff->village || $staff->address || $staff->district || $staff->pincode)
                    <div class="border-top pt-3 mt-3">
                        <h5>Location Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>City:</strong> {{ $staff->city ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Village:</strong> {{ $staff->village ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>District:</strong> {{ $staff->district ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Pincode:</strong> {{ $staff->pincode ?? 'N/A' }}
                            </div>
                            <div class="col-md-12 mb-2">
                                <strong>Address:</strong> {{ $staff->address ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </section>

            <!-- Change Password Section -->
            <section class="panel">
                <div class="panel-head">
                    <h3 class="mb-0">Change Password</h3>
                </div>
                
                <div class="p-3">
                    <form method="POST" action="{{ route('staff.update.password') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
            </section>
        </main>
    </div>
</div>
@endsection