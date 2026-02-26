@extends('admin.layout')

@section('title', 'Admin - Edit Billing Staff')
@section('page-title', 'Edit Billing Staff')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Edit Billing Staff</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.billing.staff.show', $billingStaff->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors flex items-center">
                <i class="fas fa-eye mr-2"></i>
                View
            </a>
            <a href="{{ route('admin.billing.staff') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <form action="{{ route('admin.billing.staff.update', $billingStaff->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Personal Information -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user mr-2 text-blue-500"></i>
                    Personal Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $billingStaff->name) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $billingStaff->email) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $billingStaff->phone) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                               required>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Address Information -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt mr-2 text-green-500"></i>
                    Address Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea id="address" name="address" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror">{{ old('address', $billingStaff->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="village" class="block text-sm font-medium text-gray-700 mb-2">Village</label>
                        <input type="text" id="village" name="village" value="{{ old('village', $billingStaff->village) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('village') border-red-500 @enderror">
                        @error('village')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="district" class="block text-sm font-medium text-gray-700 mb-2">District</label>
                        <input type="text" id="district" name="district" value="{{ old('district', $billingStaff->district) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('district') border-red-500 @enderror">
                        @error('district')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="pincode" class="block text-sm font-medium text-gray-700 mb-2">Pincode</label>
                        <input type="text" id="pincode" name="pincode" value="{{ old('pincode', $billingStaff->pincode) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pincode') border-red-500 @enderror">
                        @error('pincode')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Bank Information -->
            <div class="mb-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                    <i class="fas fa-university mr-2 text-blue-600"></i>
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">BANK DETAILS</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="bank_name" class="block text-sm font-medium text-blue-700 mb-2">Bank Name</label>
                        <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $billingStaff->bank_name) }}" 
                               class="w-full px-4 py-3 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white @error('bank_name') border-red-500 @enderror"
                               placeholder="Enter bank name">
                        @error('bank_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="bank_account_number" class="block text-sm font-medium text-blue-700 mb-2">Account Number</label>
                        <input type="text" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $billingStaff->bank_account_number) }}" 
                               class="w-full px-4 py-3 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white font-mono @error('bank_account_number') border-red-500 @enderror"
                               placeholder="Enter account number">
                        @error('bank_account_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="ifsc_code" class="block text-sm font-medium text-blue-700 mb-2">IFSC Code</label>
                        <input type="text" id="ifsc_code" name="ifsc_code" value="{{ old('ifsc_code', $billingStaff->ifsc_code) }}" 
                               class="w-full px-4 py-3 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white font-mono uppercase @error('ifsc_code') border-red-500 @enderror"
                               placeholder="Enter IFSC code">
                        @error('ifsc_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-4 p-4 bg-blue-100 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-700 flex items-start">
                        <i class="fas fa-info-circle mt-0.5 mr-2 flex-shrink-0"></i>
                        <span>Bank details are required for payment processing and salary disbursement. Please ensure all information is accurate.</span>
                    </p>
                </div>
            </div>
            
            <!-- Password (Optional) -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-lock mr-2 text-red-500"></i>
                    Account Security (Leave blank to keep current password)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-2">Leave these fields blank if you don't want to change the password.</p>
            </div>
            
            <!-- Submit Button -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.billing.staff.show', $billingStaff->id) }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md transition-colors flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Update Billing Staff
                </button>
            </div>
        </form>
    </div>
</div>
@endsection