@extends('layout.master')

@section('body')
<div class="pf-page">
  <div class="pf-wrap">
    <div class="pf-card">
      <div class="pf-head">
        <h2 class="pf-title">Add Address</h2>
      </div>

      <div class="pf-body">
        <form action="{{ route('address.store') }}" method="POST">
          @csrf

          <div class="pf-info-item">
            <label class="pf-label">Address</label>
            <input type="text" name="address" class="form-control" required>
          </div>

          <div class="pf-info-item">
            <label class="pf-label">City</label>
            <input type="text" name="city" class="form-control" required>
          </div>

          <div class="pf-info-item">
            <label class="pf-label">State</label>
            <input type="text" name="state" class="form-control" required>
          </div>

          <div class="pf-info-item">
            <label class="pf-label">Zip</label>
            <input type="text" name="zip" class="form-control" required>
          </div>

          <div class="pf-info-item">
            <label class="pf-label">Phone</label>
            <input type="text" name="phone" class="form-control" required>
          </div>

          <button class="pf-btn pf-btn-ghost">Save Address</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection