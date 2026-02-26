@extends('admin.layout')

@section('title', 'Admin - View Billing Staff')
@section('page-title', 'View Billing Staff')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Billing Staff Details</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.billing.staff.edit', $billingStaff->id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md transition-colors flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <a href="{{ route('admin.billing.staff') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Staff Details -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6">
            <!-- Profile Header -->
            <div class="flex items-start gap-6 mb-8 pb-6 border-b border-gray-200">
                <div class="flex-shrink-0">
                    <img class="h-20 w-20 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($billingStaff->name) }}&background=10b981&color=fff&size=128" alt="{{ $billingStaff->name }}">
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $billingStaff->name }}</h3>
                    <p class="text-gray-600 mt-1">Billing Staff Member #{{ $billingStaff->id }}</p>
                    <div class="flex items-center mt-2 text-sm text-gray-500">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Joined on {{ $billingStaff->created_at->format('F j, Y') }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Personal Information -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-500"></i>
                        Personal Information
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Full Name</label>
                            <p class="mt-1 text-gray-900">{{ $billingStaff->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email Address</label>
                            <p class="mt-1 text-gray-900">{{ $billingStaff->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone Number</label>
                            <p class="mt-1 text-gray-900">{{ $billingStaff->phone ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2 text-green-500"></i>
                        Address Information
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Address</label>
                            <p class="mt-1 text-gray-900">{{ $billingStaff->address ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Village</label>
                            <p class="mt-1 text-gray-900">{{ $billingStaff->village ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">District</label>
                            <p class="mt-1 text-gray-900">{{ $billingStaff->district ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Pincode</label>
                            <p class="mt-1 text-gray-900">{{ $billingStaff->pincode ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Bank Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                        <i class="fas fa-university mr-2 text-blue-600"></i>
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">BANK DETAILS</span>
                    </h4>
                    <div class="space-y-4">
                        <div class="bg-white p-4 rounded-lg border border-blue-100">
                            <label class="block text-sm font-medium text-blue-700 mb-1">Bank Name</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $billingStaff->bank_name ?? 'Not provided' }}</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-blue-100">
                            <label class="block text-sm font-medium text-blue-700 mb-1">Account Number</label>
                            <p class="text-lg font-semibold text-gray-900 font-mono">{{ $billingStaff->bank_account_number ?? 'Not provided' }}</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-blue-100">
                            <label class="block text-sm font-medium text-blue-700 mb-1">IFSC Code</label>
                            <p class="text-lg font-semibold text-gray-900 font-mono uppercase">{{ $billingStaff->ifsc_code ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-lock mr-2 text-red-500"></i>
                        Account Information
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Account Status</label>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Active
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                            <p class="mt-1 text-gray-900">{{ $billingStaff->updated_at->format('F j, Y g:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Member Since</label>
                            <p class="mt-1 text-gray-900">{{ $billingStaff->created_at->format('F j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection