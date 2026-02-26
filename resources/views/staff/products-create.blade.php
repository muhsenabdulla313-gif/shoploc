@extends('staff.layout')

@section('title', 'Staff - Create Product')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create New Product</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Product Information
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productName" placeholder="Enter product name">
                    </div>
                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="productDescription" rows="3" placeholder="Enter product description"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="productPrice" class="form-label">Price</label>
                            <input type="number" class="form-control" id="productPrice" placeholder="0.00" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label for="productStock" class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" id="productStock" placeholder="0">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="productCategory" class="form-label">Category</label>
                            <select class="form-select" id="productCategory">
                                <option selected>Select category</option>
                                <option value="men">Men</option>
                                <option value="women">Women</option>
                                <option value="kids">Kids</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="productSubcategory" class="form-label">Subcategory</label>
                            <input type="text" class="form-control" id="productSubcategory" placeholder="Enter subcategory">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="productImage" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="productImage">
                    </div>
                    <div class="mb-3">
                        <label for="productStatus" class="form-label">Status</label>
                        <select class="form-select" id="productStatus">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Product</button>
                    <a href="{{ route('staff.products') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Additional Information
            </div>
            <div class="card-body">
                <h5>Product Guidelines</h5>
                <ul>
                    <li>Product name should be unique</li>
                    <li>Price should be in USD</li>
                    <li>Stock quantity should be a positive number</li>
                    <li>Image size should not exceed 2MB</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection