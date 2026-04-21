@extends('staff.layout')

@section('title', 'Staff - Products')

@section('content')
 <div class="space-y-6">
        <!-- Header with Add Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="text-2xl font-bold text-gray-800">All Products</h2>
            <button type="button" onclick="openAddProductModal()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i> Add New Product
            </button>
        </div>



        <!-- Products Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-md object-cover"
                                                src="{{ $product->images->first() ? asset('storage/' . $product->images->first()->image) : 'https://via.placeholder.com/40x40' }}"
                                                alt="{{ $product->name }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $product->category->name ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($product->price, 2) }}
                                    @if($product->original_price && $product->original_price > $product->price)
                                        <span class="block text-sm text-red-500 line-through">
                                            ${{ number_format($product->original_price, 2) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $totalStock = $product->variants->sum('stock');
                                    @endphp

                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                                                                                                    @if($totalStock > 10) bg-green-100 text-green-800
                                                                                                                                                    @elseif($totalStock > 0) bg-yellow-100 text-yellow-800
                                                                                                                                                    @else bg-red-100 text-red-800 @endif">
                                        {{ $totalStock }} in stock
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                                                                                                                            @if($product->status == 'active') bg-green-100 text-green-800
                                                                                                                                                                            @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" onclick="viewProduct({{ $product->id }})"
                                        class="text-green-600 hover:text-green-900 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <button type="button" onclick="editProduct({{ $product->id }})"
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button type="button" onclick="deleteProduct({{ $product->id }})"
                                        class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No products found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($products) && $products->hasPages())
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <a href="#"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                        <a href="#"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ $products->firstItem() }}</span>
                                to <span class="font-medium">{{ $products->lastItem() }}</span>
                                of <span class="font-medium">{{ $products->total() }}</span> results
                            </p>
                        </div>
                        <div>
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto mx-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Add New Category</h3>
                <button type="button" onclick="openAddCategoryModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <form id="categoryForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                    <input type="text" id="categoryName" name="name" class="w-full px-3 py-2 border rounded mb-3"
                        placeholder="Category Name" required>

                    <select name="parent_id" id="parentCategory" class="w-full px-3 py-2 border rounded mb-3">
                        <option value="">Main Category</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCategoryModal()"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="productModal" class="fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-4">
        <div
            style="background:#f7f6f4; border-radius:20px; box-shadow:0 32px 80px rgba(0,0,0,0.4); width:100%; max-width:820px; max-height:92vh; overflow-y:auto; overflow-x:hidden;">

            <!-- HEADER -->
            <div
                style="display:flex; justify-content:space-between; align-items:center; padding:1.25rem 1.75rem; background:#0f0f0f; border-radius:20px 20px 0 0;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div
                        style="width:36px; height:36px; background:rgba(255,255,255,0.08); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-box-open" style="color:#fff; font-size:14px;"></i>
                    </div>
                    <div>
                        <h3 id="modalTitle"
                            style="font-family:'DM Serif Display',serif; font-size:1.2rem; font-weight:400; color:#fff; letter-spacing:0.01em;">
                            Add Product
                        </h3>
                        <p style="font-size:11px; color:rgba(255,255,255,0.4); margin-top:1px; letter-spacing:0.02em;">Fill
                            in the details to list a new item</p>
                    </div>
                </div>

                <button onclick="closeModal()"
                    style="width:32px; height:32px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:center; color:rgba(255,255,255,0.5); font-size:14px; transition:all 0.2s;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="productForm" enctype="multipart/form-data"
                style="padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">
                @csrf
                <input type="hidden" name="id" id="productId">

                <!-- BASIC INFO CARD -->
                <div style="background:#fff; padding:1.25rem; border-radius:14px; border:1px solid #ebebeb;">
                    <div
                        style="display:flex; align-items:center; gap:8px; margin-bottom:1rem; padding-bottom:0.75rem; border-bottom:1px solid #f2f2f2;">
                        <span
                            style="width:6px; height:6px; border-radius:50%; background:#0f0f0f; display:inline-block; flex-shrink:0;"></span>
                        <h4
                            style="font-size:11px; font-weight:600; color:#0f0f0f; letter-spacing:0.08em; text-transform:uppercase;">
                            Basic Information</h4>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">

                        <div style="display:flex; flex-direction:column; gap:5px;">
                            <label
                                style="font-size:11px; font-weight:500; color:#8a8880; letter-spacing:0.06em; text-transform:uppercase;">Product
                                Name</label>
                            <input type="text" name="name" placeholder="e.g. Silk Crew-Neck Tee"
                                style="width:100%; padding:0.6rem 0.875rem; background:#fafafa; border:1px solid #e8e8e8; border-radius:9px; font-size:13.5px; color:#1a1a1a;">
                        </div>

                        <div style="display:flex; flex-direction:column; gap:5px;">
                            <label
                                style="font-size:11px; font-weight:500; color:#8a8880; letter-spacing:0.06em; text-transform:uppercase;">Category</label>
                            <div style="display:flex; gap:8px; align-items:stretch;">
                                <select name="category_id" id="productCategory"
                                    style="flex:1; padding:0.6rem 0.875rem; background:#fafafa; border:1px solid #e8e8e8; border-radius:9px; font-size:13.5px; color:#1a1a1a; cursor:pointer;">
                                </select>

                                <button type="button" onclick="openAddCategoryModal()"
                                    style="width:38px; background:#0f0f0f; color:#fff; border:none; border-radius:9px; font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:background 0.15s;">
                                    +
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- PRICING CARD -->
                <div style="background:#fff; padding:1.25rem; border-radius:14px; border:1px solid #ebebeb;">
                    <div
                        style="display:flex; align-items:center; gap:8px; margin-bottom:1rem; padding-bottom:0.75rem; border-bottom:1px solid #f2f2f2;">
                        <span
                            style="width:6px; height:6px; border-radius:50%; background:#0f0f0f; display:inline-block; flex-shrink:0;"></span>
                        <h4
                            style="font-size:11px; font-weight:600; color:#0f0f0f; letter-spacing:0.08em; text-transform:uppercase;">
                            Pricing</h4>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">

                        <div style="display:flex; flex-direction:column; gap:5px;">
                            <label
                                style="font-size:11px; font-weight:500; color:#8a8880; letter-spacing:0.06em; text-transform:uppercase;">Price</label>
                            <input type="number" name="price" placeholder="0.00"
                                style="width:100%; padding:0.6rem 0.875rem; background:#fafafa; border:1px solid #e8e8e8; border-radius:9px; font-size:13.5px; color:#1a1a1a;">
                        </div>

                        <div style="display:flex; flex-direction:column; gap:5px;">
                            <label
                                style="font-size:11px; font-weight:500; color:#8a8880; letter-spacing:0.06em; text-transform:uppercase;">Original
                                Price</label>
                            <input type="number" name="original_price" placeholder="0.00"
                                style="width:100%; padding:0.6rem 0.875rem; background:#fafafa; border:1px solid #e8e8e8; border-radius:9px; font-size:13.5px; color:#1a1a1a;">
                        </div>

                        <div style="display:flex; flex-direction:column; gap:5px;">
                            <label
                                style="font-size:11px; font-weight:500; color:#8a8880; letter-spacing:0.06em; text-transform:uppercase;">Shipping</label>
                            <input type="number" name="shipping_charge" placeholder="0.00"
                                style="width:100%; padding:0.6rem 0.875rem; background:#fafafa; border:1px solid #e8e8e8; border-radius:9px; font-size:13.5px; color:#1a1a1a;">
                        </div>

                    </div>
                </div>

                <!-- COLOR IMAGES CARD -->
                <div style="background:#fff; padding:1.25rem; border-radius:14px; border:1px solid #ebebeb;">
                    <div
                        style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; padding-bottom:0.75rem; border-bottom:1px solid #f2f2f2;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span
                                style="width:6px; height:6px; border-radius:50%; background:#0f0f0f; display:inline-block;"></span>
                            <h4
                                style="font-size:11px; font-weight:600; color:#0f0f0f; letter-spacing:0.08em; text-transform:uppercase;">
                                Color Images</h4>
                        </div>

                        <button type="button" onclick="addColorImageRow()"
                            style="font-size:11px; font-weight:500; padding:5px 12px; border-radius:6px; cursor:pointer; border:1px solid #e0e0e0; background:#fff; color:#444; letter-spacing:0.02em; display:flex; align-items:center; gap:4px; transition:all 0.15s;">
                            + Add Color
                        </button>
                    </div>

                    <div id="colorImageContainer" style="display:flex; flex-direction:column; gap:8px;"></div>
                </div>

                <!-- VARIANTS CARD -->
                <div style="background:#fff; padding:1.25rem; border-radius:14px; border:1px solid #ebebeb;">
                    <div
                        style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; padding-bottom:0.75rem; border-bottom:1px solid #f2f2f2;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span
                                style="width:6px; height:6px; border-radius:50%; background:#0f0f0f; display:inline-block;"></span>
                            <h4
                                style="font-size:11px; font-weight:600; color:#0f0f0f; letter-spacing:0.08em; text-transform:uppercase;">
                                Variants</h4>
                        </div>

                        <button type="button" onclick="addVariantRow()"
                            style="font-size:11px; font-weight:500; padding:5px 12px; border-radius:6px; cursor:pointer; border:1px solid #e0e0e0; background:#fff; color:#444; letter-spacing:0.02em; display:flex; align-items:center; gap:4px; transition:all 0.15s;">
                            + Add Variant
                        </button>
                    </div>

                    <div id="variantContainer" style="display:flex; flex-direction:column; gap:8px;"></div>
                </div>

                <!-- STATUS + DESC -->
                <div
                    style="background:#fff; padding:1.25rem; border-radius:14px; border:1px solid #ebebeb; display:flex; flex-direction:column; gap:1rem;">

                    <div style="display:flex; flex-direction:column; gap:5px;">
                        <label
                            style="font-size:11px; font-weight:500; color:#8a8880; letter-spacing:0.06em; text-transform:uppercase;">Status</label>
                        <select name="status"
                            style="width:100%; padding:0.6rem 0.875rem; background:#fafafa; border:1px solid #e8e8e8; border-radius:9px; font-size:13.5px; color:#1a1a1a; cursor:pointer;">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:5px;">
                        <label
                            style="font-size:11px; font-weight:500; color:#8a8880; letter-spacing:0.06em; text-transform:uppercase;">Description</label>
                        <textarea name="description" rows="3"
                            placeholder="Describe the product — materials, fit, key features…"
                            style="width:100%; padding:0.6rem 0.875rem; background:#fafafa; border:1px solid #e8e8e8; border-radius:9px; font-size:13.5px; color:#1a1a1a; resize:vertical;"></textarea>
                    </div>

                </div>



                <!-- FOOTER -->
                <div style="display:flex; justify-content:space-between; align-items:center; padding-top:0.5rem;">
                    <span style="font-size:12px; color:#aaa;">All fields marked are required</span>
                    <div style="display:flex; gap:10px;">
                        <button type="button" onclick="closeModal()"
                            style="padding:0.625rem 1.25rem; border:1px solid #e0e0e0; border-radius:9px; background:transparent; color:#666; cursor:pointer; font-size:13.5px; font-weight:500; transition:all 0.15s;">
                            Cancel
                        </button>

                        <button type="submit"
                            style="padding:0.625rem 1.75rem; border-radius:9px; border:none; background:#0f0f0f; color:#fff; cursor:pointer; font-size:13.5px; font-weight:500; letter-spacing:0.02em; display:flex; align-items:center; gap:7px; transition:all 0.15s;">
                            <i class="fas fa-check" style="font-size:12px;"></i>
                            Save Product
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <div id="colorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[10000]">
        <div class="bg-white p-6 rounded-md w-full max-w-md">
            <h3 class="text-lg font-bold mb-3">Add Color</h3>

            <form id="colorForm">
                @csrf

                <input type="text" name="name" placeholder="Color Name" class="border p-2 w-full mb-3" required>

                <input type="color" name="code" class="w-full h-10 mb-3 cursor-pointer border rounded" value="#000000"
                    required>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeColorModal()">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection


@push('scripts')
    <script>

        let colorIndex = 0;
        let selectedColors = new Set();
        function addColorImageRow() {
            const container = document.getElementById('colorImageContainer');

            const html = `
                                            <div class="border p-3 rounded-md mb-3 color-row">

                                                <div class="flex gap-2 mb-2">
                                                    <select name="color_images[${colorIndex}][color_id]" 
                                                        class="color-select border p-2 flex-1"
                                                        onchange="updateSelectedColors()"></select>

                                                    <button type="button" onclick="openColorModal()" 
                                                        class="bg-green-600 text-white px-3 rounded-md">+</button>
                                                </div>

                                                <input type="file" name="color_images[${colorIndex}][images][]" multiple class="border p-2">

                                                <button type="button" onclick="removeColorRow(this)" 
                                                    class="bg-red-500 text-white px-2 mt-2">Remove</button>
                                            </div>
                                        `;

            container.insertAdjacentHTML('beforeend', html);

            const selects = container.querySelectorAll('.color-select');
            loadColors(selects[selects.length - 1]);

            colorIndex++;
        }

        function editProduct(id) {

    fetch(`/admin/products/${id}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'Error loading product');
                return;
            }

            const product = data.data;

            // ✅ BASIC
            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('productId').value = product.id;
            document.querySelector('[name="name"]').value = product.name || '';
            document.getElementById('productCategory').value = product.category_id || '';
            document.querySelector('[name="price"]').value = product.price || '';
            document.querySelector('[name="shipping_charge"]').value = product.shipping_charge || '';
            document.querySelector('[name="original_price"]').value = product.original_price || '';
            document.querySelector('[name="status"]').value = product.status || 'active';
            document.querySelector('[name="description"]').value = product.description || '';

            // ✅ CLEAR OLD
            document.getElementById('colorImageContainer').innerHTML = '';
            document.getElementById('variantContainer').innerHTML = '';

            // ✅ LOAD COLOR IMAGES
            if (product.color_images) {
                product.color_images.forEach(ci => {

                    addColorImageRow();

                    let lastRow = document.querySelectorAll('.color-row');
                    lastRow = lastRow[lastRow.length - 1];

                    const select = lastRow.querySelector('.color-select');
                    setTimeout(() => {
                        select.value = ci.color_id;
                    }, 200);
                });
            }

            // ✅ LOAD VARIANTS
            if (product.variants) {
                product.variants.forEach(v => {
                    addVariantRow(v.size, v.color_id, v.stock);
                });
            }

            openModal('productModal');
        })
        .catch(err => {
            console.error(err);
            alert('Error loading product');
        });
}

        function removeColorRow(btn) {
            btn.parentElement.remove();
            updateSelectedColors();
        }
        function updateSelectedColors() {
            selectedColors.clear();

            document.querySelectorAll('#colorImageContainer .color-select').forEach(select => {
                if (select.value) {
                    selectedColors.add(select.value);
                }
            });

            refreshVariantColorOptions();
        }
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function viewProduct(id) {
            fetch(`/admin/products/${id}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message || 'Error loading product');
                        return;
                    }

                    const product = data.data;

                    closeViewModal();


                    let colorImagesHtml = '';

                    if (product.color_images && product.color_images.length > 0) {
                        colorImagesHtml = product.color_images.map(ci => `
                                        <div class="mb-4">
                                            <div class="font-medium text-gray-700 mb-2">
                                                ${ci.color_name}
                                            </div>
                                            <div class="flex gap-2 flex-wrap">
                                                ${ci.images.map(img => `
                                                    <img src="/storage/${img}" 
                                                         class="w-20 h-20 object-cover rounded border">
                                                `).join('')}
                                            </div>
                                        </div>
                                    `).join('');
                    }

                    // ✅ VARIANTS TABLE
                    let variantsHtml = '';

                    if (product.variants && product.variants.length > 0) {
                        variantsHtml = `
                                        <table class="w-full text-sm border mt-3">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="p-2 border">Size</th>
                                                    <th class="p-2 border">Color</th>
                                                    <th class="p-2 border">Stock</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${product.variants.map(v => `
                                                    <tr>
                                                        <td class="p-2 border">${v.size}</td>
                                                        <td class="p-2 border">${v.color_name}</td>
                                                        <td class="p-2 border">${v.stock}</td>
                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    `;
                    }

                    const modalHtml = `
                                    <div id="viewProductModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                                      <div class="bg-white rounded-xl p-6 w-full max-w-5xl max-h-[90vh] overflow-y-auto">

                                        <div class="flex justify-between items-center mb-4">
                                          <h3 class="text-xl font-semibold">Product Details</h3>
                                          <button onclick="closeViewModal()" class="text-gray-500 hover:text-gray-700">
                                            <i class="fas fa-times text-2xl"></i>
                                          </button>
                                        </div>

                                        <div class="grid md:grid-cols-2 gap-6">

                                            <!-- LEFT -->
                                            <div>
                                                <h4 class="font-medium mb-2">Color Images</h4>
                                                ${colorImagesHtml || '<p class="text-gray-500">No images</p>'}
                                            </div>

                                            <!-- RIGHT -->
                                            <div>

                                                <div class="mb-3">
                                                    <div class="text-sm text-gray-500">Name</div>
                                                    <div class="font-semibold">${product.name}</div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4 mb-3">
                                                   <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <div class="text-sm text-gray-500">Category</div>
                    <div>${product.category || ''}</div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Subcategory</div>
                    <div>${product.subcategory || ''}</div>
                </div>
            </div>
                                                    <div>
                                                        <div class="text-sm text-gray-500">Price</div>
                                                        <div>$${parseFloat(product.price).toFixed(2)}</div>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4 mb-3">
                                                    <div>
                                                        <div class="text-sm text-gray-500">Original Price</div>
                                                        <div>${product.original_price ? '$' + parseFloat(product.original_price).toFixed(2) : 'N/A'}</div>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm text-gray-500">Status</div>
                                                        <div>${product.status}</div>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="text-sm text-gray-500">Description</div>
                                                    <div>${product.description || 'N/A'}</div>
                                                </div>

                                                <div class="mt-4">
                                                    <div class="text-sm text-gray-500 mb-1">Variants</div>
                                                    ${variantsHtml || '<p class="text-gray-500">No variants</p>'}
                                                </div>

                                                <div class="mt-5 text-right">
                                                    <button onclick="closeViewModal()"
                                                        class="px-4 py-2 border rounded-md hover:bg-gray-100">
                                                        Close
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                      </div>
                                    </div>
                                `;

                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                })
                .catch(err => {
                    console.error(err);
                    alert('Network error');
                });
        }
        function closeViewModal() {
            const modal = document.getElementById('viewProductModal');
            if (modal) modal.remove();
        }
        function openModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }

        function closeModalById(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }

        function loadParentCategories() {
            fetch('/admin/categories')
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('parentCategory');
                    select.innerHTML = '<option value="">Main Category</option>';

                    data.data.forEach(cat => {
                        const opt = document.createElement('option');
                        opt.value = cat.id;
                        opt.textContent = cat.name;
                        select.appendChild(opt);
                    });
                });
        } // CATEGORY MODAL
        function openAddCategoryModal() {
            document.getElementById('categoryForm')?.reset();
            loadParentCategories();

            closeModalById('productModal');
            openModal('categoryModal');
        }

        function closeCategoryModal() {
            closeModalById('categoryModal');
            openModal('productModal');
        }

        // PRODUCT MODAL
        function openAddProductModal() {
            const form = document.getElementById('productForm');
            form?.reset();

            document.getElementById('modalTitle').textContent = 'Add New Product';
            document.getElementById('productId').value = '';





            loadCategories(); // important
            openModal('productModal');
        }

        function closeModal() {
            closeModalById('productModal');
        }

        // LOAD CATEGORIES (ONLY ONE VERSION ✅)
        function loadCategories() {
            const categorySelect = document.getElementById('productCategory');
            if (!categorySelect) return;

            fetch('/admin/categories')
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;

                    categorySelect.innerHTML = '<option value="">Select Category</option>';

                    data.data.forEach(category => {
                        const parentOption = document.createElement('option');
                        parentOption.value = category.id;
                        parentOption.textContent = category.name;
                        categorySelect.appendChild(parentOption);

                        if (category.children) {
                            category.children.forEach(child => {
                                const childOpt = document.createElement('option');
                                childOpt.value = child.id;
                                childOpt.textContent = '-- ' + child.name;
                                categorySelect.appendChild(childOpt);
                            });
                        }
                    });
                })
                .catch(err => console.error('Category load error:', err));
        }

        // VARIANTS
        function addVariantRow(size = '', color_id = '', stock = '') {
            const container = document.getElementById('variantContainer');

            const html = `
                                        <div class="grid grid-cols-4 gap-3 border p-3 rounded-md variant-row">
                                            <input type="text" name="variants[size][]" value="${size}" placeholder="Size" class="px-2 py-1 border rounded-md">

                                            <select name="variants[color_id][]" class="px-2 py-1 border rounded-md variant-color"></select>

                                            <input type="number" name="variants[stock][]" value="${stock}" placeholder="Stock" class="px-2 py-1 border rounded-md">

                                            <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 text-white px-2 rounded-md">X</button>
                                        </div>
                                    `;

            container.insertAdjacentHTML('beforeend', html);

            const select = container.querySelectorAll('.variant-color');
            populateVariantColors(select[select.length - 1]);
        }



        function populateVariantColors(selectElement) {
            selectElement.innerHTML = '<option value="">Select Color</option>';

            document.querySelectorAll('#colorImageContainer .color-select').forEach(select => {
                if (select.value) {
                    const text = select.options[select.selectedIndex].text;

                    const opt = document.createElement('option');
                    opt.value = select.value;
                    opt.textContent = text;

                    selectElement.appendChild(opt);
                }
            });
        }
        function validateVariants() {
            const combinations = new Set();
            let valid = true;

            document.querySelectorAll('.variant-row').forEach(row => {
                const size = row.querySelector('input[name="variants[size][]"]').value;
                const color = row.querySelector('.variant-color').value;

                const key = size + '-' + color;

                if (combinations.has(key)) {
                    if (combinations.has(key)) {
                        alert('Duplicate variant: same size and color already added!');
                        return false;
                    }
                }

                combinations.add(key);
            });

            return valid;
        }
        function refreshVariantColorOptions() {
            document.querySelectorAll('.variant-color').forEach(select => {
                populateVariantColors(select);
            });
        }


       function loadColors(selectElement, callback = null) {
    fetch('/admin/colors')
        .then(res => res.json())
        .then(data => {
            selectElement.innerHTML = '<option value="">Select Color</option>';

            data.data.forEach(color => {
                const opt = document.createElement('option');
                opt.value = color.id;
                opt.textContent = color.name;
                selectElement.appendChild(opt);
            });

            if (callback) callback();
        });
}
        // SIZE PRICE GENERATION
        function generateSizePriceFields() {
            const sizesInput = document.getElementById('productSizes');
            const container = document.getElementById('sizePricesContainer');

            if (!sizesInput || !container) return;

            const sizes = sizesInput.value.split(',').map(s => s.trim()).filter(Boolean);

            if (!sizes.length) {
                container.innerHTML = '';
                return;
            }

            let html = '';

            sizes.forEach(size => {
                html += `
                                                                        <div class="grid grid-cols-2 gap-2 mb-2">
                                                                            <input type="number" name="size_prices[${size}][price]" placeholder="${size} price" class="border p-2 rounded">
                                                                            <input type="number" name="size_prices[${size}][original_price]" placeholder="${size} original" class="border p-2 rounded">
                                                                        </div>
                                                                    `;
            });

            container.innerHTML = html;
        }

        // DELETE
        function deleteProduct(id) {
            if (!confirm('Delete this product?')) return;

            fetch(`/admin/products/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': getCsrfToken() }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) location.reload();
                    else alert('Error deleting');
                });
        }
        function openColorModal() {
            openModal('colorModal');
        }

        function closeColorModal() {
            closeModalById('colorModal');
        }
        // DOM READY
        document.addEventListener('DOMContentLoaded', () => {

            closeModalById('productModal');
            closeModalById('categoryModal');

            loadCategories();




            // CATEGORY SUBMIT
            document.getElementById('categoryForm')?.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                fetch('/admin/categories', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) return alert('Error');

                        loadCategories();
                        alert('Category added');

                        closeModalById('categoryModal');
                        openModal('productModal');
                    });
            });


            document.addEventListener('submit', function (e) {
                if (e.target && e.target.id === 'colorForm') {
                    e.preventDefault();

                    const formData = new FormData(e.target);

                    fetch('/admin/colors', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) return alert('Error');

                            alert('Color added');

                            closeColorModal();

                            document.querySelectorAll('.color-select').forEach(select => {
    const currentValue = select.value; // save selected

    loadColors(select);

    setTimeout(() => {
        select.value = currentValue; // restore selected
        updateSelectedColors(); // VERY IMPORTANT
    }, 200);
});
                        });
                }
            });



            // PRODUCT SUBMIT
            document.getElementById('productForm')?.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!validateVariants()) return;

                const formData = new FormData(this);
                const id = document.getElementById('productId').value;

                if (id) formData.append('_method', 'PUT');

                fetch(id ? `/admin/products/${id}` : '/admin/products', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                })
                    .then(async r => {
                        const data = await r.json();

                        if (!data.success) {
                            console.error('FULL ERROR:', data);

                            if (data.errors) {
                                alert(JSON.stringify(data.errors, null, 2)); // validation errors
                            } else {
                                alert(data.message || data.error || 'Unknown error');
                            }
                            return;
                        }

                        alert('Saved successfully');
                        location.reload();
                    })
                    .catch(err => {
                        console.error('FETCH ERROR:', err);
                        alert('Something broke. Check console.');
                    });
            });

        });
    </script>
@endpush