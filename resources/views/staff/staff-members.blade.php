@extends('staff.layout')

@section('title', 'Staff Members')

@section('content')
<div class="staffdb-wrap">
    <div class="staffdb-shell">
        <!-- Sidebar -->
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
                </div>
            </div>

            <div class="side-icons">
                <button class="side-btn" type="button" title="Help">
                    <i class="far fa-question-circle"></i>
                </button>

                <form id="logoutForm" action="{{ route('staff.logout') }}" method="POST" style="display:inline;">
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
                    <h2>All Staff Members</h2>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="mini-cards">
                <div class="mini">
                    <div class="v">{{ count($staffMembers) }}</div>
                    <div class="l">Total Staff</div>
                </div>
            </div>

            <!-- Staff Members Table -->
            <section class="panel">
                <div class="panel-head">
                    <h3 class="mb-0">Staff Directory</h3>
                </div>
                            
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="staff-table" id="staffTable">
                            <thead>
                                <tr>
                                    <th class="th-pad">Name</th>
                                    <th>Email</th>
                                    <th>Purchases Referred</th>
                                    <th>Total Earnings</th>
                                </tr>
                            </thead>
            
                            <tbody>
                                @forelse($staffMembers as $staffMember)
                                    <tr class="staff-row">
                                        <td data-label="Name" class="td-pad">
                                            <div class="namecell">
                                                <div class="photo">
                                                    {{ strtoupper(substr($staffMember->name ?? 'S', 0, 1)) }}
                                                </div>
                                                <div class="meta">
                                                    <p class="nm mb-0">{{ $staffMember->name ?? 'N/A' }}</p>
                                                    <p class="em mb-0">{{ $staffMember->email ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </td>
            
                                        <td data-label="Email" class="td-text">{{ $staffMember->email ?? 'N/A' }}</td>
                                        <td data-label="Purchases Referred" class="td-text">
                                            {{ $staffMember->referrals_count ?? 0 }}
                                        </td>
                                        <td data-label="Total Earnings" class="td-text">
                                            ₹{{ number_format($staffMember->total_earnings ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="staff-row empty-row">
                                        <td colspan="4" class="empty-cell">
                                            <div class="empty-state">
                                                <i class="fas fa-users"></i>
                                                <p class="title">No staff members found</p>
                                                <small>There are no staff members in the system yet</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
            
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // No search functionality needed
</script>
@endsection
