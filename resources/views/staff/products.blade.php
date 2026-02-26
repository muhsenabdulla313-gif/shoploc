@extends('staff.layout')

@section('title', 'Staff - Products')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Products</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Import</button>
        </div>
        <a href="{{ route('staff.products.create') }}" class="btn btn-sm btn-primary">Add Product</a>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search products...">
            <button class="btn btn-outline-secondary" type="button">Search</button>
        </div>
    </div>
    <div class="col-md-6 text-end">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                Filter by Category
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">All Categories</a></li>
                <li><a class="dropdown-item" href="#">Women</a></li>
                <li><a class="dropdown-item" href="#">Men</a></li>
                <li><a class="dropdown-item" href="#">Kids</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td><img src="https://placehold.co/50x50" alt="Product" class="img-thumbnail" style="width: 50px; height: 50px;"></td>
                <td>Premium T-Shirt</td>
                <td>Men</td>
                <td>$29.99</td>
                <td>3</td>
                <td><span class="badge bg-warning">Low Stock</span></td>
                <td>
                    <a href="{{ route('staff.products.edit', 1) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td><img src="https://placehold.co/50x50" alt="Product" class="img-thumbnail" style="width: 50px; height: 50px;"></td>
                <td>Designer Jeans</td>
                <td>Women</td>
                <td>$79.99</td>
                <td>8</td>
                <td><span class="badge bg-warning">Low Stock</span></td>
                <td>
                    <a href="{{ route('staff.products.edit', 2) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td><img src="https://placehold.co/50x50" alt="Product" class="img-thumbnail" style="width: 50px; height: 50px;"></td>
                <td>Casual Shoes</td>
                <td>Men</td>
                <td>$59.99</td>
                <td>12</td>
                <td><span class="badge bg-success">In Stock</span></td>
                <td>
                    <a href="{{ route('staff.products.edit', 3) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </td>
            </tr>
            <tr>
                <td>4</td>
                <td><img src="https://placehold.co/50x50" alt="Product" class="img-thumbnail" style="width: 50px; height: 50px;"></td>
                <td>Summer Dress</td>
                <td>Women</td>
                <td>$49.99</td>
                <td>25</td>
                <td><span class="badge bg-success">In Stock</span></td>
                <td>
                    <a href="{{ route('staff.products.edit', 4) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </td>
            </tr>
            <tr>
                <td>5</td>
                <td><img src="https://placehold.co/50x50" alt="Product" class="img-thumbnail" style="width: 50px; height: 50px;"></td>
                <td>Kids T-Shirt</td>
                <td>Kids</td>
                <td>$19.99</td>
                <td>0</td>
                <td><span class="badge bg-danger">Out of Stock</span></td>
                <td>
                    <a href="{{ route('staff.products.edit', 5) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center">
    <div class="text-muted">
        Showing 1 to 5 of 128 entries
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