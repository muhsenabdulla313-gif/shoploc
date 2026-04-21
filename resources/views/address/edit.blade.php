@extends('layout.master')

@section('body')
<div class="pf-page">
  <div class="pf-wrap">
    <div class="pf-card">
      <div class="pf-head">
        <h2 class="pf-title">Edit Address</h2>
      </div>

      <div class="pf-body">
        <form action="{{ route('address.update', $address->id) }}" method="POST">
          @csrf

          <div class="pf-info-item">
            <label class="pf-label">Address</label>
            <input type="text" name="address" value="{{ $address->address }}" class="form-control">
          </div>

          <div class="pf-info-item">
            <label class="pf-label">City</label>
            <input type="text" name="city" value="{{ $address->city }}" class="form-control">
          </div>

          <div class="pf-info-item">
            <label class="pf-label">State</label>
            <input type="text" name="state" value="{{ $address->state }}" class="form-control">
          </div>

          <div class="pf-info-item">
            <label class="pf-label">Zip</label>
            <input type="text" name="zip" value="{{ $address->zip }}" class="form-control">
          </div>

          <div class="pf-info-item">
            <label class="pf-label">Phone</label>
            <input type="text" name="phone" value="{{ $address->phone }}" class="form-control">
          </div>

          <button class="pf-btn pf-btn-ghost">Update Address</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection