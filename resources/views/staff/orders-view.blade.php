@extends('staff.layout')

@section('title', 'Staff - View Order')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Order Details #{{ $id }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Print</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Email</button>
        </div>
        <button type="button" class="btn btn-sm btn-primary">Update Status</button>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Order Information
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Customer Details</h6>
                        <p><strong>Name:</strong> John Doe</p>
                        <p><strong>Email:</strong> john.doe@example.com</p>
                        <p><strong>Phone:</strong> +1 234 567 8900</p>
                        <p><strong>Address:</strong><br>
                            123 Main Street<br>
                            New York, NY 10001<br>
                            United States
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Order Details</h6>
                        <p><strong>Order ID:</strong> #{{ $id }}</p>
                        <p><strong>Date:</strong> 2023-01-15</p>
                        <p><strong>Status:</strong> <span class="badge bg-success">Completed</span></p>
                        <p><strong>Payment:</strong> <span class="badge bg-success">Paid</span></p>
                        <p><strong>Payment Method:</strong> Credit Card</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                Order Items
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Image</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Premium T-Shirt</td>
                                <td><img src="https://placehold.co/50x50" alt="Product" class="img-thumbnail" style="width: 50px; height: 50px;"></td>
                                <td>$29.99</td>
                                <td>2</td>
                                <td>$59.98</td>
                            </tr>
                            <tr>
                                <td>Designer Jeans</td>
                                <td><img src="https://placehold.co/50x50" alt="Product" class="img-thumbnail" style="width: 50px; height: 50px;"></td>
                                <td>$79.99</td>
                                <td>1</td>
                                <td>$79.99</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Subtotal:</th>
                                <td>$139.97</td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Shipping:</th>
                                <td>$9.99</td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Tax:</th>
                                <td>$11.20</td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Total:</th>
                                <td><strong>$161.16</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Order Status
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-success active">Completed</button>
                    <button class="btn btn-outline-info">Shipped</button>
                    <button class="btn btn-outline-warning">Processing</button>
                    <button class="btn btn-outline-secondary">Pending</button>
                    <button class="btn btn-outline-danger">Cancelled</button>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                Tracking Information
            </div>
            <div class="card-body">
                <p><strong>Tracking Number:</strong> TRK123456789</p>
                <p><strong>Carrier:</strong> UPS</p>
                <p><strong>Estimated Delivery:</strong> 2023-01-20</p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                Notes
            </div>
            <div class="card-body">
                <p class="text-muted">No special instructions provided by customer.</p>
            </div>
        </div>
    </div>
</div>
@endsection