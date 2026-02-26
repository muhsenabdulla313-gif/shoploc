@extends('admin.layout')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Message -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-6 text-white">
        <h1 class="text-2xl font-bold mb-2">Welcome Back, {{ Auth::guard('staff')->user()->name ?? 'Admin' }}!</h1>
        <p class="opacity-90">Here's what's happening with your store today.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="stat-card bg-white rounded-xl p-6 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Products</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalProducts }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-500 text-sm font-medium">
                    <i class="fas fa-arrow-up"></i> 12%
                </span>
                <span class="text-gray-500 text-sm ml-2">from last month</span>
            </div>
        </div>

        <div class="stat-card bg-white rounded-xl p-6 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Users</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalUsers }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-500 text-sm font-medium">
                    <i class="fas fa-arrow-up"></i> 8%
                </span>
                <span class="text-gray-500 text-sm ml-2">from last month</span>
            </div>
        </div>

        <div class="stat-card bg-white rounded-xl p-6 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending Orders</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $pendingOrders }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-shopping-cart text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-red-500 text-sm font-medium">
                    <i class="fas fa-arrow-down"></i> 3%
                </span>
                <span class="text-gray-500 text-sm ml-2">from last month</span>
            </div>
        </div>


    </div>

    <!-- Charts and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white rounded-xl p-6 shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Orders</h3>
            <div class="space-y-4">
                @forelse($recentOrders as $order)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium">#{{ $order->id }}</p>
                                <p class="text-sm text-gray-600">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">${{ number_format($order->total_amount, 2) }}</p>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($order->status == 'completed') bg-green-100 text-green-800
                                    @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600">{{ $order->first_name }} {{ $order->last_name }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No recent orders</p>
                @endforelse
            </div>
            <a href="{{ route('admin.orders') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800 font-medium">
                View All Orders <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <!-- Top Selling Products -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-fire text-orange-500 mr-3"></i>
                    Top Selling Products
                </h3>
                <span class="px-3 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">
                    HOT
                </span>
            </div>
            
            <div class="space-y-4">
                @forelse($topSellingProducts as $index => $product)
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-all duration-200 hover:border-orange-200">
                        <div class="flex items-center flex-1">
                            <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center mr-4">
                                <span class="text-orange-600 font-bold text-lg">{{ $index + 1 }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 truncate">{{ $product->product_name }}</p>
                                <div class="flex items-center mt-1">
                                    <span class="text-sm text-gray-600 mr-3">
                                        <i class="fas fa-shopping-cart text-green-500 mr-1"></i>
                                        Sold: {{ $product->total_quantity }} units
                                    </span>
                                    @if($product->badge)
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 font-medium">
                                            {{ $product->badge }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="ml-4 text-right">
                            <div class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium">No top selling products yet</p>
                        <p class="text-gray-400 text-sm mt-1">Start selling to see popular items here</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.products') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    View All Products
                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection