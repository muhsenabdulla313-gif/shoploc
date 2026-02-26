@extends('staff.layout')

@section('title', 'Staff Dashboard')

@section('styles')
<style>
    :root{
        --bg:#f6f4ff;
        --card:#ffffff;
        --muted:#8b8fa3;
        --text:#1f2430;
        --line:#ece9ff;
        --primary:#7c5cff;
        --primary-2:#9b7bff;
        --pill:#eee9ff;
        --shadow: 0 12px 30px rgba(30, 20, 90, .08);
        --shadow-sm: 0 8px 18px rgba(30, 20, 90, .06);
        --radius:16px;
        --radius-lg:22px;
    }

    /* Page base */
    .staffdb-wrap{
        background: var(--bg);
        min-height: calc(100vh - 0px);
        padding: 22px;
    }

    /* Layout (Sidebar + Main) */
    .staffdb-shell{
        display:flex;
        gap:18px;
        align-items:stretch;
    }

    .staffdb-sidebar{
        width:84px;
        min-width:84px;
        background: rgba(255,255,255,.55);
        border:1px solid rgba(124,92,255,.08);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        padding: 14px 10px;
        display:flex;
        flex-direction:column;
        justify-content:space-between;
        backdrop-filter: blur(8px);
    }

    .brand-mini{
        display:flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        padding:10px 6px;
    }
    .brand-dot{
        width:12px;height:12px;border-radius:4px;
        background: linear-gradient(135deg, var(--primary), var(--primary-2));
        box-shadow: 0 10px 20px rgba(124,92,255,.25);
    }

    .side-icons{
        display:flex;
        flex-direction:column;
        gap:10px;
        margin-top:10px;
    }
    .side-btn{
        width:52px;height:52px;
        border-radius:16px;
        display:flex;
        align-items:center;
        justify-content:center;
        color:#6f6a8f;
        background: transparent;
        border:1px solid transparent;
        transition: .2s ease;
        cursor:pointer;
    }
    .side-btn:hover{
        background: rgba(124,92,255,.08);
        color: var(--primary);
        border-color: rgba(124,92,255,.12);
    }
    .side-btn.active{
        background: linear-gradient(135deg, rgba(124,92,255,.16), rgba(155,123,255,.10));
        color: var(--primary);
        border-color: rgba(124,92,255,.16);
    }

    /* Main area */
    .staffdb-main{
        flex:1;
        background: transparent;
        border-radius: var(--radius-lg);
    }

    /* Topbar */
    .staffdb-topbar{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:14px;
        padding: 6px 6px 14px 6px;
    }

    .page-title{
        display:flex;
        align-items:center;
        gap:10px;
    }
    .page-title h2{
        margin:0;
        font-weight:800;
        color: var(--text);
        font-size: 1.35rem;
        letter-spacing: .2px;
    }

    .top-actions{
        display:flex;
        align-items:center;
        gap:12px;
        flex-wrap: wrap;
        justify-content:flex-end;
    }

    .userchip{
        display:flex;
        align-items:center;
        gap:10px;
        background: rgba(255,255,255,.75);
        border:1px solid rgba(124,92,255,.10);
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        padding: 8px 12px;
    }
    .userchip .avatar{
        width:34px;height:34px;border-radius:12px;
        background: linear-gradient(135deg, rgba(124,92,255,.25), rgba(155,123,255,.18));
        display:flex;align-items:center;justify-content:center;
        color: var(--primary);
        font-weight:800;
    }
    .userchip .meta{
        line-height:1.15;
    }
    .userchip .meta .name{
        font-weight:700;
        color: var(--text);
        font-size:.92rem;
        margin:0;
    }
    .userchip .meta .role{
        font-size:.78rem;
        color: var(--muted);
        margin:0;
    }

    /* Panel card */
    .panel{
        background: rgba(255,255,255,.70);
        border:1px solid rgba(124,92,255,.08);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        padding: 16px;
        backdrop-filter: blur(10px);
    }

    /* Small helper cards */
    .mini-cards{
        display:grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap:12px;
        margin: 0 0 14px;
    }
    .mini{
        background: rgba(255,255,255,.75);
        border:1px solid rgba(124,92,255,.08);
        border-radius: 18px;
        padding: 14px;
        box-shadow: var(--shadow-sm);
    }
    .mini .v{ font-size:1.4rem; font-weight:900; color: var(--text); }
    .mini .l{ font-size:.78rem; font-weight:800; color: var(--muted); margin-top:2px; }

    /* Table */
    .staff-table{
        width:100%;
        border-collapse: separate;
        border-spacing: 0 12px;
    }

    .staff-table thead th{
        color:#7a7e95;
        font-size:.82rem;
        font-weight:800;
        letter-spacing:.2px;
        padding: 6px 10px;
    }

    .staff-row{
        background: var(--card);
        border:1px solid rgba(124,92,255,.08);
        box-shadow: var(--shadow-sm);
        border-radius: 18px;
        overflow:hidden;
    }
    .staff-row td{
        padding: 14px 12px;
        color: var(--text);
        font-weight:600;
        vertical-align: middle;
    }

    /* Responsive */
    @media (max-width: 992px){
        .staffdb-shell{ flex-direction: column; }
        .staffdb-sidebar{
            width:100%;
            min-width: auto;
            flex-direction: row;
            align-items:center;
            gap:10px;
        }
        .side-icons{
            flex-direction: row;
            flex-wrap: wrap;
            margin-top:0;
        }
        .staffdb-topbar{ flex-direction: column; align-items: stretch; }
        .mini-cards{ grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
@php
    // ✅ IMPORTANT:
    // Admin side-il purchases_referred / total_earnings update aavunnath pole,
    // Staff dashboard-ilum same columns direct display cheyyunna fix aanu.
    $purchasesReferred = $staff->purchases_referred ?? 0;
    $totalEarnings     = $staff->total_earnings ?? 0;

    // ✅ Referral code field name: your staff dashboard uses `referral_code`
    // (screenshotil link `STAFF0EE6B1D7` varunnath ok.)
    $refCode = $staff->referral_code ?? null;

    $refLink = $refCode ? url('/?ref=' . $refCode) : null;
@endphp

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
                                @if($staff->city)
                                    <div class="col-md-6 mb-2">
                                        <strong>City:</strong> {{ $staff->city }}
                                    </div>
                                @endif

                                @if($staff->village)
                                    <div class="col-md-6 mb-2">
                                        <strong>Village:</strong> {{ $staff->village }}
                                    </div>
                                @endif

                                @if($staff->district)
                                    <div class="col-md-6 mb-2">
                                        <strong>District:</strong> {{ $staff->district }}
                                    </div>
                                @endif

                                @if($staff->pincode)
                                    <div class="col-md-6 mb-2">
                                        <strong>Pincode:</strong> {{ $staff->pincode }}
                                    </div>
                                @endif

                                @if($staff->address)
                                    <div class="col-md-12 mb-2">
                                        <strong>Address:</strong> {{ $staff->address }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            <!-- ✅ Stats Cards (FIXED) -->
            <div class="mini-cards">
                <div class="mini">
                    <div class="v">{{ $purchasesReferred }}</div>
                    <div class="l">Purchases Referred</div>
                </div>
                <div class="mini">
                    <div class="v">₹{{ number_format($totalEarnings, 2) }}</div>
                    <div class="l">Total Earnings</div>
                </div>
                <div class="mini">
                    <div class="v">{{ $refCode ?? '—' }}</div>
                    <div class="l">Referral Code</div>
                </div>
            </div>

            <!-- Referral Link Section -->
            <section class="panel mb-4">
                <div class="panel-head">
                    <h3 class="mb-0">Your Referral Link</h3>
                </div>

                <div class="p-3">
                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
                        <div class="flex-grow-1">
                            <input
                                type="text"
                                id="referralLink"
                                class="form-control"
                                value="{{ $refLink ?? 'Generating referral link...' }}"
                                readonly
                                style="background: rgba(255,255,255,.85); border:1px solid rgba(124,92,255,.12); border-radius: 14px; padding: 12px 16px; font-weight:600; color:var(--text);"
                            >
                        </div>
                        <button
                            class="btn btn-primary"
                            type="button"
                            onclick="copyReferralLink()"
                            style="background: linear-gradient(135deg, var(--primary), var(--primary-2)); border:none; padding: 12px 24px; border-radius: 14px; font-weight:700; box-shadow: 0 8px 20px rgba(124,92,255,.25);"
                        >
                            <i class="fas fa-copy me-2"></i>Copy Link
                        </button>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">Share this link to earn referral points when others sign up or make purchases</small>
                    </div>
                </div>
            </section>

            <!-- Messages from Admin -->
            <section class="panel mb-4">
                <div class="panel-head">
                    <h3 class="mb-0">Messages from Admin</h3>
                </div>

                <div class="p-3">
                    @if(isset($adminMessages) && count($adminMessages) > 0)
                        <div class="space-y-4">
                            @foreach($adminMessages as $message)
                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-5 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope-open-text text-blue-500 mr-3 text-lg"></i>
                                            <h4 class="font-bold text-gray-800 text-lg">{{ $message['subject'] ?? 'No Subject' }}</h4>
                                        </div>
                                        <span class="text-sm text-gray-600 bg-blue-100 px-3 py-1 rounded-full">
                                            {{ isset($message['created_at']) ? \Carbon\Carbon::parse($message['created_at'])->setTimezone('Asia/Kolkata')->format('M d, Y g:i A') : 'Date unknown' }}
                                        </span>
                                    </div>
                                    <p class="text-gray-700 mb-4 pl-8">{{ $message['message'] ?? 'No message content' }}</p>
                                    <div class="flex items-center text-sm text-blue-700 bg-blue-100 px-4 py-2 rounded-lg w-fit">
                                        <i class="fas fa-user-shield mr-2"></i>
                                        <span class="font-medium">From:</span> Admin Team
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-dashed border-blue-200">
                            <i class="fas fa-envelope text-blue-400 text-5xl mb-4"></i>
                            <h4 class="font-bold text-gray-700 mb-2">No messages yet</h4>
                            <p class="text-gray-600 mb-1">Stay tuned for important announcements</p>
                            <p class="text-sm text-gray-500">Messages from admin will appear here</p>
                        </div>
                    @endif
                </div>
            </section>

            <!-- Recent Activity Panel -->
            <section class="panel">
                <div class="panel-head">
                    <h3 class="mb-0">Recent Activity</h3>
                </div>

                <div class="p-3">
                    <div class="table-responsive">
                        <table class="staff-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Activity</th>
                                    <th>Type</th>
                                    <th>Earnings</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities ?? [] as $activity)
                                    <tr class="staff-row">
                                        <td>{{ $activity['date'] ?? 'N/A' }}</td>
                                        <td>{{ $activity['description'] ?? 'N/A' }}</td>
                                        <td><span class="badge bg-primary">{{ $activity['type'] ?? 'N/A' }}</span></td>
                                        <td>₹{{ number_format($activity['earnings'] ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr class="staff-row">
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            No recent activity
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
    function copyReferralLink() {
        const referralLinkInput = document.getElementById('referralLink');

        if (!referralLinkInput || !referralLinkInput.value || referralLinkInput.value.includes('Generating')) {
            alert('Referral link not ready yet.');
            return;
        }

        referralLinkInput.select();

        navigator.clipboard.writeText(referralLinkInput.value)
            .then(() => {
                const btn = document.querySelector('[onclick="copyReferralLink()"]');
                if (!btn) return;

                const original = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Copied!';
                btn.style.background = 'linear-gradient(135deg, #28a745, #20c997)';

                setTimeout(() => {
                    btn.innerHTML = original;
                    btn.style.background = 'linear-gradient(135deg, var(--primary), var(--primary-2))';
                }, 2000);
            })
            .catch(err => {
                console.error('Failed to copy: ', err);
                alert('Failed to copy referral link. Please try again.');
            });
    }
</script>
@endsection
