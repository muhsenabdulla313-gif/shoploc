@extends('staff.layout')

@section('title', 'Staff - Customers')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Customers</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Import</button>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search customers...">
            <button class="btn btn-outline-secondary" type="button">Search</button>
        </div>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary btn-sm">New</button>
            <button type="button" class="btn btn-outline-secondary btn-sm">Active</button>
            <button type="button" class="btn btn-outline-secondary btn-sm">Inactive</button>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Location</th>
                <th>Orders</th>
                <th>Total Spent</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>John Doe</td>
                <td>john.doe@example.com</td>
                <td>+1 234 567 8900</td>
                <td>New York, USA</td>
                <td>12</td>
                <td>$1,245.99</td>
                <td><span class="badge bg-success">Active</span></td>
                <td>
                    <a href="{{ route('staff.customers.view', 1) }}" class="btn btn-sm btn-outline-primary">View</a>
                    <a href="{{ route('staff.customers.edit', 1) }}" class="btn btn-sm btn-outline-info">Edit</a>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Jane Smith</td>
                <td>jane.smith@example.com</td>
                <td>+1 234 567 8901</td>
                <td>Los Angeles, USA</td>
                <td>8</td>
                <td>$890.50</td>
                <td><span class="badge bg-success">Active</span></td>
                <td>
                    <a href="{{ route('staff.customers.view', 2) }}" class="btn btn-sm btn-outline-primary">View</a>
                    <a href="{{ route('staff.customers.edit', 2) }}" class="btn btn-sm btn-outline-info">Edit</a>
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td>Robert Johnson</td>
                <td>robert.j@example.com</td>
                <td>+1 234 567 8902</td>
                <td>Chicago, USA</td>
                <td>5</td>
                <td>$543.25</td>
                <td><span class="badge bg-success">Active</span></td>
                <td>
                    <a href="{{ route('staff.customers.view', 3) }}" class="btn btn-sm btn-outline-primary">View</a>
                    <a href="{{ route('staff.customers.edit', 3) }}" class="btn btn-sm btn-outline-info">Edit</a>
                </td>
            </tr>
            <tr>
                <td>4</td>
                <td>Emily Davis</td>
                <td>emily.d@example.com</td>
                <td>+1 234 567 8903</td>
                <td>Houston, USA</td>
                <td>3</td>
                <td>$321.75</td>
                <td><span class="badge bg-success">Active</span></td>
                <td>
                    <a href="{{ route('staff.customers.view', 4) }}" class="btn btn-sm btn-outline-primary">View</a>
                    <a href="{{ route('staff.customers.edit', 4) }}" class="btn btn-sm btn-outline-info">Edit</a>
                </td>
            </tr>
            <tr>
                <td>5</td>
                <td>Michael Wilson</td>
                <td>michael.w@example.com</td>
                <td>+1 234 567 8904</td>
                <td>Phoenix, USA</td>
                <td>1</td>
                <td>$67.80</td>
                <td><span class="badge bg-warning">Inactive</span></td>
                <td>
                    <a href="{{ route('staff.customers.view', 5) }}" class="btn btn-sm btn-outline-primary">View</a>
                    <a href="{{ route('staff.customers.edit', 5) }}" class="btn btn-sm btn-outline-info">Edit</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center">
    <div class="text-muted">
        Showing 1 to 5 of 1,200 entries
    </div>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">Previous</a>
            </li>
            <li class="page-item active">
                <a class="page-link" href="#">1</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">2</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">3</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">Next</a>
            </li>
        </ul>
    </nav>
</div>
@endsection