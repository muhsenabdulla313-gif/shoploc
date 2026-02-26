@extends('admin.layout')

@section('title', 'Admin - Order Details')
@section('page-title', 'Order Details')

@section('content')
<div class="space-y-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Success! </strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Error! </strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Order Header -->
    <div class="bg-white rounded-xl p-6 shadow-md">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Order #{{ $order->id }}</h2>
                <p class="text-gray-600">Placed on {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="px-3 py-1 rounded-full text-sm font-medium 
                    @if($order->status == 'completed') bg-green-100 text-green-800
                    @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                    @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                    @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($order->status) }}
                </span>
                <span class="px-3 py-1 rounded-full text-sm font-medium 
                    @if($order->payment_status == 'paid') bg-green-100 text-green-800
                    @elseif($order->payment_status == 'pending') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl p-6 shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h3>
            <div class="space-y-2">
                <p><span class="font-medium">Name:</span> {{ $order->first_name }} {{ $order->last_name }}</p>
                <p><span class="font-medium">Email:</span> {{ $order->email }}</p>
                <p><span class="font-medium">Phone:</span> {{ $order->phone }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Shipping Address</h3>
            <div class="space-y-2">
                <p>{{ $order->address }}</p>
                <p>{{ $order->city }}, {{ $order->state }} {{ $order->zip }}</p>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-xl p-6 shadow-md">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Items</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($item->product_image)
                                        <img class="h-10 w-10 rounded-md object-cover" src="{{ asset('storage/' . $item->product_image) }}" alt="{{ $item->product_name }}">
                                    @else
                                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-10 h-10" />
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->color ?: '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->size ?: '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($item->price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="bg-white rounded-xl p-6 shadow-md">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-800 mb-3">Payment Information</h4>
                <div class="space-y-2">
                    <p><span class="font-medium">Method:</span> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                    <p><span class="font-medium">Status:</span> {{ ucfirst($order->payment_status) }}</p>
                </div>
            </div>
            <div>
                <h4 class="font-medium text-gray-800 mb-3">Order Totals</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Discount:</span>
                        <span>-${{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total:</span>
                        <span>${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Actions -->
    <div class="bg-white rounded-xl p-6 shadow-md">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Actions</h3>
        <div class="flex flex-wrap gap-3">
            @if($order->status !== 'cancelled')
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="inline">
                    @csrf
                    @method('PUT')
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="ml-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">Update Status</button>
                </form>
                
                @if($order->status !== 'completed' && $order->status !== 'cancelled')
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel Order</button>
                </form>
                @endif
            @endif
            
            <form method="POST" action="{{ route('admin.orders.delete', $order->id) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md ml-2" onclick="return confirm('Are you sure you want to delete this order?')">Delete Order</button>
            </form>
        </div>
    </div>
</div>
@endsection