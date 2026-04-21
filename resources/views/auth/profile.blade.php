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

              <form id="profile-logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="button" class="pf-btn pf-btn-danger" onclick="confirmLogout()">
                  <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
              </form>


            </div>

          </div>
        </div>
      </div>

      <div class="pf-info-item">
        <label class="pf-label">My Addresses:</label>

        @foreach(Auth::user()->addresses as $addr)
          <div class="pf-info-value" style="margin-bottom:10px;">

            {{ $addr->address }}, {{ $addr->city }} - {{ $addr->zip }}

            <div style="margin-top:8px; display:flex; gap:6px;">

              <!-- EDIT BUTTON -->
              <a href="#" class="pf-btn pf-btn-ghost editAddressBtn" style="min-width:auto; padding:6px 10px;"
                data-id="{{ $addr->id }}" data-address="{{ $addr->address }}" data-city="{{ $addr->city }}"
                data-state="{{ $addr->state }}" data-zip="{{ $addr->zip }}" data-phone="{{ $addr->phone }}"
                data-bs-toggle="modal" data-bs-target="#editAddressModal">
                ✏️ Edit
              </a>

              <!-- DELETE BUTTON -->
              <form action="{{ route('address.delete', $addr->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="pf-btn pf-btn-danger" style="min-width:auto; padding:6px 10px;">
                  🗑 Delete
                </button>
              </form>

            </div>
          </div>
        @endforeach

        <!-- ADD BUTTON -->
        <a href="#" class="pf-btn pf-btn-ghost" data-bs-toggle="modal" data-bs-target="#addAddressModal">
          + Add Address
        </a>
      </div>
    </div>
  </div>


  <div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Add Address</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <form action="{{ route('address.store') }}" method="POST">
          @csrf

          <div class="modal-body">

            <div class="row">
              <div class="col-6">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control custom-input" required>
              </div>

              <div class="col-6">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control custom-input">
              </div>
            </div>

            <div class="mt-3">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control custom-input" required>
            </div>

            <div class="row mt-3">
              <div class="col-6">
                <label class="form-label">City</label>
                <input type="text" name="city" class="form-control custom-input" required>
              </div>

              <div class="col-6">
                <label class="form-label">State</label>
                <input type="text" name="state" class="form-control custom-input" required>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-6">
                <label class="form-label">Zip Code</label>
                <input type="text" name="zip" class="form-control custom-input" required>
              </div>

              <div class="col-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control custom-input" required>
              </div>
            </div>

          </div>

          <div class="modal-footer">
            <button class="btn btn-dark">Save</button>
          </div>

        </form>

      </div>
    </div>
  </div>

  <div class="modal fade" id="editAddressModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Edit Address</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <form id="editAddressForm" method="POST">
          @csrf

          <div class="modal-body">

            <div class="row">
              <div class="col-6">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" id="edit_first_name" class="form-control custom-input">
              </div>

              <div class="col-6">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" id="edit_last_name" class="form-control custom-input">
              </div>
            </div>

            <div class="mt-3">
              <label class="form-label">Address</label>
              <input type="text" name="address" id="edit_address" class="form-control custom-input">
            </div>

            <div class="row mt-3">
              <div class="col-6">
                <label class="form-label">City</label>
                <input type="text" name="city" id="edit_city" class="form-control custom-input">
              </div>

              <div class="col-6">
                <label class="form-label">State</label>
                <input type="text" name="state" id="edit_state" class="form-control custom-input">
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-6">
                <label class="form-label">Zip Code</label>
                <input type="text" name="zip" id="edit_zip" class="form-control custom-input">
              </div>

              <div class="col-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" id="edit_phone" class="form-control custom-input">
              </div>
            </div>

          </div>

          <div class="modal-footer">
            <button class="btn btn-dark">Update</button>
          </div>

        </form>

      </div>
    </div>
  </div>
@endsection

@push('scripts')



  <script>
    document.addEventListener("DOMContentLoaded", function () {
      document.querySelectorAll('.editAddressBtn').forEach(button => {
        button.addEventListener('click', function () {

          let id = this.getAttribute('data-id');

          document.getElementById('edit_first_name').value = this.getAttribute('data-first_name');
          document.getElementById('edit_last_name').value = this.getAttribute('data-last_name');
          document.getElementById('edit_address').value = this.getAttribute('data-address');
          document.getElementById('edit_city').value = this.getAttribute('data-city');
          document.getElementById('edit_state').value = this.getAttribute('data-state');
          document.getElementById('edit_zip').value = this.getAttribute('data-zip');
          document.getElementById('edit_phone').value = this.getAttribute('data-phone');

          document.getElementById('editAddressForm').action = "/update-address/" + id;
        });
      });
    });
  </script>
  <style>
    .custom-input {
      border-radius: 8px;
      padding: 10px;
      font-size: 14px;
      border: 1px solid #ddd;
      transition: 0.2s;
    }

    .custom-input:focus {
      border-color: #000;
      box-shadow: none;
    }

    .modal-content {
      border-radius: 12px;
    }

    .modal-header {
      border-bottom: 1px solid #eee;
    }

    .modal-footer {
      border-top: 1px solid #eee;
    }

    .pf-page {
      padding: 30px 14px 70px;
      background: #fff;
      min-height: calc(100vh - 120px);
    }

    .pf-wrap {
      max-width: 520px;
      margin: 0 auto;
    }

    .pf-card {
      background: #fff;
      border: 1px solid rgba(0, 0, 0, .08);
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 14px 35px rgba(0, 0, 0, .08);
    }

    .pf-head {
      padding: 18px 18px 12px;
      border-bottom: 1px solid rgba(0, 0, 0, .06);
    }

    .pf-title {
      margin: 0;
      font-weight: 900;
      font-size: 18px;
      color: #111;
    }

    .pf-body {
      padding: 18px;
    }

    .pf-label {
      display: block;
      font-size: 12px;
      font-weight: 800;
      color: #111;
      margin-bottom: 8px;
    }

    .pf-info-item {
      margin-bottom: 20px;
    }

    .pf-info-value {
      padding: 12px 16px;
      background: #f8f9fa;
      border-radius: 8px;
      border-left: 4px solid #2c1a2a;
      font-size: 16px;
    }

    .pf-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 10px;
    }

    .pf-btn {
      flex: 1;
      min-width: 140px;
      height: 40px;
      border-radius: 6px;
      font-weight: 900;
      font-size: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
      border: 0;
      cursor: pointer;
    }

    .pf-btn-ghost {
      background: #f3f4f6;
      color: #111;
      border: 1px solid rgba(0, 0, 0, .10);
    }

    .pf-btn-danger {
      background: #fff;
      color: #dc2626;
      border: 1px solid rgba(220, 38, 38, .25);
    }
  </style>
  <script>
    function confirmLogout() {
      if (confirm("Are you sure you want to logout?")) {
        document.getElementById('profile-logout-form').submit();
      }
    }
  </script>

  @include('footer')
@endpush