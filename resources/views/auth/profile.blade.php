@extends('layout.master')

@section('body')
<div class="pf-page">
  <div class="pf-wrap">
    <div class="pf-card">
      <div class="pf-head">
        <h2 class="pf-title">My Profile</h2>
      </div>

      <div class="pf-body">
        <div class="pf-info-display">
          <div class="pf-info-item">
            <label class="pf-label">Username:</label>
            <div class="pf-info-value">{{ $user->name }}</div>
          </div>

          <div class="pf-info-item">
            <label class="pf-label">E-mail:</label>
            <div class="pf-info-value">{{ $user->email }}</div>
          </div>

          <div class="pf-info-item">
            <label class="pf-label">Member Since:</label>
            <div class="pf-info-value">{{ $user->created_at->format('F j, Y') }}</div>
          </div>

          <div class="pf-actions">
            <a href="/my-orders" class="pf-btn pf-btn-ghost">
              <i class="fa-solid fa-box"></i> My Orders
            </a>

            <button type="button" class="pf-btn pf-btn-danger" onclick="confirmDeleteAccount()">
              <i class="fa-solid fa-trash"></i> Delete Account
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<style>
.pf-page{ padding:30px 14px 70px; background:#fff; min-height:calc(100vh - 120px); }
.pf-wrap{ max-width:520px; margin:0 auto; }

.pf-card{ background:#fff; border:1px solid rgba(0,0,0,.08); border-radius:10px; overflow:hidden; box-shadow:0 14px 35px rgba(0,0,0,.08); }
.pf-head{ padding:18px 18px 12px; border-bottom:1px solid rgba(0,0,0,.06); }
.pf-title{ margin:0; font-weight:900; font-size:18px; color:#111; }
.pf-body{ padding:18px; }

.pf-label{ display:block; font-size:12px; font-weight:800; color:#111; margin-bottom:8px; }
.pf-info-item{ margin-bottom:20px; }
.pf-info-value{ padding:12px 16px; background:#f8f9fa; border-radius:8px; border-left:4px solid #2c1a2a; font-size:16px; }

.pf-actions{ display:flex; gap:10px; flex-wrap:wrap; margin-top:10px; }
.pf-btn{ flex:1; min-width:140px; height:40px; border-radius:6px; font-weight:900; font-size:12px; display:flex; align-items:center; justify-content:center; gap:8px; text-decoration:none; border:0; cursor:pointer; }

.pf-btn-ghost{ background:#f3f4f6; color:#111; border:1px solid rgba(0,0,0,.10); }
.pf-btn-danger{ background:#fff; color:#dc2626; border:1px solid rgba(220,38,38,.25); }
</style>

<script>
function confirmDeleteAccount(){
  if(confirm('Are you sure you want to delete your account?')){
    const form=document.createElement('form');
    form.method='POST';
    form.action='/profile/delete';

    const token=document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.innerHTML = `
      <input type="hidden" name="_token" value="${token}">
      <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
  }
}
</script>
@include('footer')
@endpush
