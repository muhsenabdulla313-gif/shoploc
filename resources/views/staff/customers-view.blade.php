@extends('staff.layout')

@section('title', 'Staff - View Customer')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Customer Details #{{ $id }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Email</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
        </div>
        <button type="button" class="btn btn-sm btn-primary">Edit Customer</button>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Customer Information
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Personal Information</h6>
                        <p><strong>Name:</strong> John Doe</p>
                        <p><strong>Email:</strong> john.doe@example.com</p>
                        <p><strong>Phone:</strong> +1 234 567 8900</p>
                        <p><strong>Date of Birth:</strong> 1990-05-15</p>
                        <p><strong>Gender:</strong> Male</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Account Information</h6>
                        <p><strong>Customer ID:</strong> #{{ $id }}</p>
                        <p><strong>Account Status:</strong> <span class="badge bg-success">Active</span></p>
                        <p><strong>Registration Date:</strong> 2022-12-01</p>
                        <p><strong>Last Login:</strong> 2023-01-20</p>
                        <p><strong>Newsletter:</strong> Subscribed</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                Address Information
            </div>
            <div class="card-body">
                <h6>Shipping Address</h6>
                <p>
                    John Doe<br>
                    123 Main Street<br>
                    New York, NY 10001<br>
                    United States<br>
                    Phone: +1 234 567 8900
                </p>
                
                <h6 class="mt-3">Billing Address</h6>
                <p>
                    John Doe<br>
                    123 Main Street<br>
                    New York, NY 10001<br>
                    United States<br>
                    Phone: +1 234 567 8900
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Customer Summary
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <img src="https://ui-avatars.com/api/?name=John+Doe&background=random" alt="Customer" class="rounded-circle" style="width: 100px; height: 100px;">
                </div>
                <h5>John Doe</h5>
                <p class="text-muted">Customer since Dec 1, 2022</p>
                
                <div class="row mt-4">
                    <div class="col-6">
                        <h6>Total Orders</h6>
                        <p class="display-6">12</p>
                    </div>
                    <div class="col-6">
                        <h6>Total Spent</h6>
                        <p class="display-6">$1,245</p>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary">Send Email</button>
                    <button class="btn btn-outline-success">Update Status</button>
                    <button class="btn btn-outline-warning">Add Note</button>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                Recent Activity
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Order #12345</div>
                            Completed on 2023-01-15
                        </div>
                        <span class="badge bg-success rounded-pill">$161.16</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Order #12344</div>
                            Completed on 2023-01-10
                        </div>
                        <span class="badge bg-success rounded-pill">$89.99</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Order #12343</div>
                            Completed on 2023-01-05
                        </div>
                        <span class="badge bg-success rounded-pill">$234.50</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection