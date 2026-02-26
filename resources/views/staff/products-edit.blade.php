@extends('staff.layout')

@section('title', 'Staff - Edit Product')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Product #{{ $id }}</h1>
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
                        <input type="text" class="form-control" id="productName" placeholder="Enter product name" value="Premium T-Shirt">
                    </div>
                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="productDescription" rows="3" placeholder="Enter product description">High quality cotton t-shirt for everyday wear</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="productPrice" class="form-label">Price</label>
                            <input type="number" class="form-control" id="productPrice" placeholder="0.00" step="0.01" value="29.99">
                        </div>
                        <div class="col-md-6">
                            <label for="productStock" class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" id="productStock" placeholder="0" value="25">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="productCategory" class="form-label">Category</label>
                            <select class="form-select" id="productCategory">
                                <option value="men">Men</option>
                                <option value="women">Women</option>
                                <option value="kids">Kids</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="productSubcategory" class="form-label">Subcategory</label>
                            <input type="text" class="form-control" id="productSubcategory" placeholder="Enter subcategory" value="Shirts">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="productImage" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="productImage">
                        <div class="mt-2">
                            <img src="https://placehold.co/150x150" alt="Current Image" class="img-thumbnail" style="max-width: 150px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="productStatus" class="form-label">Status</label>
                        <select class="form-select" id="productStatus">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                    <a href="{{ route('staff.products') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Product Details
            </div>
            <div class="card-body">
                <h5>Current Status</h5>
                <p><strong>Created:</strong> 2023-01-15</p>
                <p><strong>Last Updated:</strong> 2023-01-20</p>
                <p><strong>Views:</strong> 1,245</p>
                <p><strong>Sales:</strong> 89</p>
            </div>
        </div>
    </div>
</div>
@endsection