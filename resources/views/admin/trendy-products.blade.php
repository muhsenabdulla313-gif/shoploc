@extends('admin.layout')

@section('title', 'Manage Trendy Products')
@section('page-title', 'Trendy Products')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Trendy Products</h1>
        <button id="open-create-modal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center">
            <i class="fas fa-plus mr-2"></i> Add Trendy Product
        </button>
    </div>

    <!-- Success/Error Messages -->
    <div id="message-container" class="mb-4"></div>

    <!-- Trendy Products Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trend Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="trendy-products-table-body" class="bg-white divide-y divide-gray-200">
                    <!-- Data will be loaded via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loading" class="hidden text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
        <p class="mt-2 text-gray-600">Loading trendy products...</p>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="trendy-product-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4">
        <div class="border-b px-6 py-4">
            <h3 id="modal-title" class="text-lg font-semibold text-gray-800">Add Trendy Product</h3>
        </div>
        
        <form id="trendy-product-form" class="p-6">
            @csrf
            <input type="hidden" id="product-id" name="product_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="product-select" class="block text-sm font-medium text-gray-700 mb-1">Select Product</label>
                    <select id="product-select" name="product_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Loading products...</option>
                    </select>
                    <p id="product-error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
                
                <div>
                    <label for="trend-type" class="block text-sm font-medium text-gray-700 mb-1">Trend Type</label>
                    <select id="trend-type" name="trend_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Trend Type</option>
                        <option value="hot-trend">Hot Trend</option>
                        <option value="best-seller">Best Seller</option>
                        <option value="featured">Featured</option>
                    </select>
                    <p id="trend-type-error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
                
                <div>
                    <label for="rating" class="block text-sm font-medium text-gray-700 mb-1">Rating (0-5)</label>
                    <input type="number" id="rating" name="rating" min="0" max="5" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p id="rating-error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" id="close-modal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" id="save-btn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="border-b px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-800">Confirm Deletion</h3>
        </div>
        
        <div class="p-6">
            <p id="confirm-message">Are you sure you want to delete this trendy product?</p>
        </div>
        
        <div class="border-t px-6 py-4 flex justify-end space-x-3">
            <button type="button" id="cancel-delete" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
            <button type="button" id="confirm-delete" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Delete</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const tbody = document.getElementById('trendy-products-table-body');
    const loading = document.getElementById('loading');
    const messageContainer = document.getElementById('message-container');
    const modal = document.getElementById('trendy-product-modal');
    const confirmModal = document.getElementById('confirm-modal');
    
    // Form elements
    const form = document.getElementById('trendy-product-form');
    const productIdInput = document.getElementById('product-id');
    const productSelect = document.getElementById('product-select');
    const trendTypeInput = document.getElementById('trend-type');
    const ratingInput = document.getElementById('rating');
    const modalTitle = document.getElementById('modal-title');
    const saveBtn = document.getElementById('save-btn');
    
    // Buttons
    const openCreateModalBtn = document.getElementById('open-create-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete');
    const confirmDeleteBtn = document.getElementById('confirm-delete');
    
    // Error elements
    const productError = document.getElementById('product-error');
    const trendTypeError = document.getElementById('trend-type-error');
    const ratingError = document.getElementById('rating-error');
    
    // Hidden variables for delete confirmation
    let productToDelete = null;
    
    // Load trendy products
    function loadTrendyProducts() {
        loading.classList.remove('hidden');
        tbody.innerHTML = '';
        
        fetch('{{ route("admin.trendy-products.list") }}')
            .then(response => response.json())
            .then(data => {
                loading.classList.add('hidden');
                
                if (data.success) {
                    if (data.data.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No trendy products found.</td>
                            </tr>
                        `;
                    } else {
                        data.data.forEach(product => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${product.id}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${product.name}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${product.category}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        ${product.trend_type === 'hot-trend' ? 'bg-red-100 text-red-800' : 
                                          product.trend_type === 'best-seller' ? 'bg-green-100 text-green-800' : 
                                          product.trend_type === 'featured' ? 'bg-yellow-100 text-yellow-800' : 
                                          'bg-gray-100 text-gray-800'}">
                                        ${product.trend_type ? product.trend_type.replace('-', ' ') : 'None'}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${product.rating ? parseFloat(product.rating).toFixed(1) : '0.0'}
                                    <div class="flex mt-1">
                                        ${renderStars(parseFloat(product.rating) || 0)}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₹${parseFloat(product.price).toFixed(2)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="editTrendyProduct(${product.id})" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button onclick="deleteTrendyProduct(${product.id}, '${product.name}')" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                    }
                } else {
                    showMessage('Error loading trendy products: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                loading.classList.add('hidden');
                console.error('Error:', error);
                showMessage('Error loading trendy products: ' + error.message, 'error');
            });
    }
    
    // Render star ratings
    function renderStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= Math.floor(rating)) {
                stars += '<span class="text-yellow-400">★</span>';
            } else if (i - 0.5 <= rating) {
                stars += '<span class="text-yellow-400">☆</span>'; // Half star
            } else {
                stars += '<span class="text-gray-300">☆</span>';
            }
        }
        return stars;
    }
    
    // Load all products for dropdown
    function loadAllProducts() {
        fetch('{{ route("admin.products.list") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    productSelect.innerHTML = '<option value="">Select a product</option>';
                    data.data.forEach(product => {
                        const option = document.createElement('option');
                        option.value = product.id;
                        option.textContent = `${product.name} (${product.category}) - ₹${parseFloat(product.price).toFixed(2)}`;
                        productSelect.appendChild(option);
                    });
                } else {
                    productSelect.innerHTML = '<option value="">Error loading products</option>';
                }
            })
            .catch(error => {
                console.error('Error loading products:', error);
                productSelect.innerHTML = '<option value="">Error loading products</option>';
            });
    }
    
    // Show message
    function showMessage(message, type = 'success') {
        messageContainer.innerHTML = `
            <div class="${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} 
                 border ${type === 'success' ? 'border-green-400' : 'border-red-400'} 
                 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">${type === 'success' ? 'Success!' : 'Error!'}</strong>
                <span class="block sm:inline">${message}</span>
            </div>
        `;
        
        // Auto hide message after 5 seconds
        setTimeout(() => {
            messageContainer.innerHTML = '';
        }, 5000);
    }
    
    // Open modal for creating a new trendy product
    openCreateModalBtn.addEventListener('click', function() {
        modalTitle.textContent = 'Add Trendy Product';
        form.reset();
        productIdInput.value = '';
        productSelect.disabled = false;
        
        // Clear errors
        clearErrors();
        
        modal.classList.remove('hidden');
    });
    
    // Close modal
    closeModalBtn.addEventListener('click', function() {
        modal.classList.add('hidden');
        clearErrors();
    });
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
            clearErrors();
        }
    });
    
    // Close confirmation modal when clicking outside
    confirmModal.addEventListener('click', function(e) {
        if (e.target === confirmModal) {
            confirmModal.classList.add('hidden');
        }
    });
    
    // Cancel delete
    cancelDeleteBtn.addEventListener('click', function() {
        confirmModal.classList.add('hidden');
        productToDelete = null;
    });
    
    // Save trendy product (form submission)
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        // Get the actual selected product value from the dropdown
        const productId = productSelect.value;
        const trendType = formData.get('trend_type');
        const rating = formData.get('rating');
        
        // Validate inputs
        let isValid = true;
        clearErrors();
        
        if (!productId || productId === '') {
            showError(productSelect, productError, 'Please select a product');
            isValid = false;
        }
        
        if (!trendType) {
            showError(trendTypeInput, trendTypeError, 'Please select a trend type');
            isValid = false;
        }
        
        if (rating && (parseFloat(rating) < 0 || parseFloat(rating) > 5)) {
            showError(ratingInput, ratingError, 'Rating must be between 0 and 5');
            isValid = false;
        }
        
        if (!isValid) return;
        
        // Disable save button to prevent double submission
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
        
        // Determine if this is a create or update
        const isUpdate = !!productIdInput.value;
        const url = isUpdate 
            ? `{{ route("admin.trendy-products.update", ":id") }}`.replace(':id', productIdInput.value)
            : '{{ route("admin.trendy-products.store") }}';
        
        // Prepare the data for the API call
        const requestData = {
            product_id: productId,
            trend_type: formData.get('trend_type'),
            rating: formData.get('rating'),
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };
        
        // For update, we need to use POST with _method override
        if (isUpdate) {
            requestData._method = 'PUT';
        }
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(requestData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message || (isUpdate ? 'Trendy product updated successfully!' : 'Trendy product added successfully!'));
                    modal.classList.add('hidden');
                    loadTrendyProducts(); // Reload the table
                } else {
                    // Show validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const input = document.getElementById(field.replace('_', '-'));
                            const errorElement = document.getElementById(field.replace('_', '-') + '-error');
                            if (input && errorElement) {
                                showError(input, errorElement, data.errors[field][0]);
                            }
                        });
                    } else {
                        showMessage(data.message || 'An error occurred', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while saving: ' + error.message, 'error');
            })
            .finally(() => {
                // Re-enable save button
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save';
            });
    });
    
    // Function to edit a trendy product (to be called from HTML)
    window.editTrendyProduct = function(productId) {
        // Fetch the product details
        fetch(`{{ route("admin.products.show", ":id") }}`.replace(':id', productId))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const product = data.data;
                    
                    modalTitle.textContent = 'Edit Trendy Product';
                    productIdInput.value = product.id;
                    productSelect.value = product.id;
                    trendTypeInput.value = product.trend_type || '';
                    ratingInput.value = product.rating ? parseFloat(product.rating).toFixed(1) : '';
                    productSelect.disabled = true; // Disable product selection when editing
                    
                    // Clear errors
                    clearErrors();
                    
                    modal.classList.remove('hidden');
                } else {
                    showMessage('Error loading product: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Error loading product: ' + error.message, 'error');
            });
    };
    
    // Function to delete a trendy product (to be called from HTML)
    window.deleteTrendyProduct = function(productId, productName) {
        productToDelete = productId;
        document.getElementById('confirm-message').textContent = 
            `Are you sure you want to remove "${productName}" from trendy products?`;
        confirmModal.classList.remove('hidden');
    };
    
    // Confirm delete
    confirmDeleteBtn.addEventListener('click', function() {
        if (!productToDelete) return;
        
        fetch(`{{ route("admin.trendy-products.remove", ":id") }}`.replace(':id', productToDelete), {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message || 'Trendy product removed successfully!');
                confirmModal.classList.add('hidden');
                loadTrendyProducts(); // Reload the table
                productToDelete = null;
            } else {
                showMessage(data.message || 'An error occurred', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('An error occurred while deleting: ' + error.message, 'error');
        });
    });
    
    // Helper functions
    function showError(input, errorElement, message) {
        input.classList.add('border-red-500');
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
    }
    
    function clearErrors() {
        [productSelect, trendTypeInput, ratingInput].forEach(input => {
            input.classList.remove('border-red-500');
        });
        [productError, trendTypeError, ratingError].forEach(error => {
            error.textContent = '';
            error.classList.add('hidden');
        });
    }
    
    // Initialize
    loadAllProducts();
    loadTrendyProducts();
});
</script>
@endpush