@extends('staff.layout')

@section('title', 'Staff - Edit Customer')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Customer #{{ $id }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-success">Save Changes</button>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Customer Information
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" value="John">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" value="Doe">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="john.doe@example.com">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" value="+1 234 567 8900">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dateOfBirth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="dateOfBirth" value="1990-05-15">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender">
                            <option value="male" selected>Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Account Status</label>
                        <select class="form-select" id="status">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="newsletter" checked>
                        <label class="form-check-label" for="newsletter">
                            Subscribe to Newsletter
                        </label>
                    </div>
                    <button type="submit" class="btn btn-success">Update Customer</button>
                    <a href="{{ route('staff.customers') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                Address Information
            </div>
            <div class="card-body">
                <h6>Shipping Address</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="addressLine1" class="form-label">Address Line 1</label>
                            <input type="text" class="form-control" id="addressLine1" value="123 Main Street">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="addressLine2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="addressLine2" value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" value="New York">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control" id="state" value="NY">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="zipCode" class="form-label">ZIP Code</label>
                            <input type="text" class="form-control" id="zipCode" value="10001">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="country" class="form-label">Country</label>
                    <select class="form-select" id="country">
                        <option value="US" selected>United States</option>
                        <option value="CA">Canada</option>
                        <option value="GB">United Kingdom</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Customer Actions
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary">Send Email</button>
                    <button class="btn btn-outline-success">Update Status</button>
                    <button class="btn btn-outline-info">View Orders</button>
                    <button class="btn btn-outline-warning">Add Note</button>
                    <button class="btn btn-outline-danger">Suspend Account</button>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                Account Information
            </div>
            <div class="card-body">
                <p><strong>Customer ID:</strong> #{{ $id }}</p>
                <p><strong>Registration Date:</strong> 2022-12-01</p>
                <p><strong>Last Login:</strong> 2023-01-20</p>
                <p><strong>Total Orders:</strong> 12</p>
                <p><strong>Total Spent:</strong> $1,245.67</p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                Notes
            </div>
            <div class="card-body">
                <p class="text-muted">No notes available for this customer.</p>
            </div>
        </div>
    </div>
</div>
@endsection