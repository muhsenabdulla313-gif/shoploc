@extends('admin.layout')

@section('title', 'Admin - Offers')
@section('page-title', 'Offers Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h2 class="text-2xl font-bold text-gray-800">Manage Offers</h2>
        <button onclick="openAddOfferModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors flex items-center">
            <i class="fas fa-plus mr-2"></i> Add New Offer
        </button>
    </div>

    <!-- Offers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($offers as $offer)
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="relative">
                @if($offer->image)
                    <img src="{{ asset('storage/' . $offer->image) }}" alt="{{ $offer->alt_text }}" class="w-full h-48 object-cover">
                @else
                    <div class="bg-gray-200 border-2 border-dashed rounded-xl w-full h-48 flex items-center justify-center text-gray-500">
                        No Image
                    </div>
                @endif
                <div class="absolute top-2 right-2">
                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                        {{ $offer->active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-lg">{{ $offer->title }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ $offer->alt_text }}</p>
                <div class="mt-3 flex justify-between items-center">
                    <span class="text-xs text-gray-500">Start: {{ $offer->start_date->format('M d, Y') }}</span>
                    <span class="text-xs text-gray-500">End: {{ $offer->end_date->format('M d, Y') }}</span>
                </div>
                <div class="mt-4 flex space-x-2">
                    <button onclick="editOffer({{ $offer->id }})" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded-md text-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </button>
                    <button onclick="deleteOffer({{ $offer->id }})" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 rounded-md text-sm">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-8">
            <i class="fas fa-percentage text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No offers yet</h3>
            <p class="text-gray-500">Get started by adding a new offer.</p>
            <button onclick="openAddOfferModal()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                Add Offer
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- Add/Edit Offer Modal -->
<div id="offerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 id="offerModalTitle" class="text-xl font-semibold">Add New Offer</h3>
            <button onclick="closeOfferModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <form id="offerForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="offerId" name="id">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Offer Title</label>
                    <input type="text" id="offerTitle" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Offer Image</label>
                    <input type="file" id="offerImage" name="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div id="offerImagePreview" class="mt-2 hidden">
                        <img id="offerPreviewImg" src="" alt="Preview" class="h-32 w-32 object-cover rounded-md">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alternative Text</label>
                    <input type="text" id="offerAltText" name="alt_text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" id="offerStartDate" name="start_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" id="offerEndDate" name="end_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" id="offerActive" name="active" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Active Offer</span>
                    </label>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeOfferModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Offer</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddOfferModal() {
        document.getElementById('offerModalTitle').textContent = 'Add New Offer';
        document.getElementById('offerForm').reset();
        document.getElementById('offerId').value = '';
        document.getElementById('offerImagePreview').classList.add('hidden');
        document.getElementById('offerModal').classList.remove('hidden');
    }

    function closeOfferModal() {
        document.getElementById('offerModal').classList.add('hidden');
    }

    function editOffer(id) {
        // In a real implementation, you would fetch the offer data and populate the form
        document.getElementById('offerModalTitle').textContent = 'Edit Offer';
        document.getElementById('offerId').value = id;
        document.getElementById('offerModal').classList.remove('hidden');
    }

    function deleteOffer(id) {
        if (confirm('Are you sure you want to delete this offer?')) {
            // In a real implementation, you would send a DELETE request
            console.log('Deleting offer with ID:', id);
        }
    }

    // Image preview functionality
    document.getElementById('offerImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('offerPreviewImg').src = event.target.result;
                document.getElementById('offerImagePreview').classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    // Form submission handling
    document.getElementById('offerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const offerId = document.getElementById('offerId').value;
        
        // In a real implementation, you would send the form data to the server
        if (offerId) {
            // Update existing offer
            console.log('Updating offer with ID:', offerId);
        } else {
            // Create new offer
            console.log('Creating new offer');
        }
        
        closeOfferModal();
    });
</script>
@endsection