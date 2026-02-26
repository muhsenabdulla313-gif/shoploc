@extends('staff.layout')

@section('title', 'Staff - Edit Order')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Order #{{ $id }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-success">Save Changes</button>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Order Information
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Customer Details</h6>
                            <div class="mb-3">
                                <label for="customerName" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="customerName" value="John Doe">
                            </div>
                            <div class="mb-3">
                                <label for="customerEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="customerEmail" value="john.doe@example.com">
                            </div>
                            <div class="mb-3">
                                <label for="customerPhone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="customerPhone" value="+1 234 567 8900">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Order Details</h6>
                            <div class="mb-3">
                                <label for="orderStatus" class="form-label">Status</label>
                                <select class="form-select" id="orderStatus">
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered" selected>Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="paymentStatus" class="form-label">Payment Status</label>
                                <select class="form-select" id="paymentStatus">
                                    <option value="pending">Pending</option>
                                    <option value="paid" selected>Paid</option>
                                    <option value="refunded">Refunded</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="orderDate" class="form-label">Order Date</label>
                                <input type="date" class="form-control" id="orderDate" value="2023-01-15">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="shippingAddress" class="form-label">Shipping Address</label>
                        <textarea class="form-control" id="shippingAddress" rows="3">123 Main Street
New York, NY 10001
United States</textarea>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between">
                <span>Order Items</span>
                <button type="button" class="btn btn-sm btn-outline-primary">Add Item</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Premium T-Shirt</td>
                                <td>$29.99</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="2" min="1" style="width: 80px;">
                                </td>
                                <td>$59.98</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger">Remove</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Designer Jeans</td>
                                <td>$79.99</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="1" min="1" style="width: 80px;">
                                </td>
                                <td>$79.99</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger">Remove</button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Subtotal:</th>
                                <td>$139.97</td>
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Shipping:</th>
                                <td>$9.99</td>
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Tax:</th>
                                <td>$11.20</td>
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <td><strong>$161.16</strong></td>
                                <td></td>
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
                Order Actions
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary">Update Order</button>
                    <button class="btn btn-outline-success">Send Notification</button>
                    <button class="btn btn-outline-info">Print Invoice</button>
                    <button class="btn btn-outline-warning">Add Note</button>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                Order History
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Order Placed</div>
                            2023-01-15 10:30 AM
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Payment Received</div>
                            2023-01-15 10:35 AM
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Order Completed</div>
                            2023-01-15 11:00 AM
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection