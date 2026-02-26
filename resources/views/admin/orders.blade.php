@extends('admin.layout')

@section('title', 'Admin - Orders')
@section('page-title', 'Orders Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h2 class="text-2xl font-bold text-gray-800">All Orders</h2>
        <div class="flex gap-2">
            <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
                <option value="completed">Completed</option>
            </select>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                Export
            </button>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $order->id }}</td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->first_name }} {{ $order->last_name }}
                            <div class="text-gray-500">{{ $order->email }}</div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ₹{{ number_format($order->total_amount, 2) }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($order->status == 'completed') bg-green-100 text-green-800
                                @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                                @elseif($order->status == 'delivered') bg-indigo-100 text-indigo-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($order->payment_status == 'paid') bg-green-100 text-green-800
                                @elseif($order->payment_status == 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <!-- ✅ View -->
                            <a href="{{ route('admin.orders.details', $order->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>

                            <!-- ✅ Delete -->
                            <button onclick="deleteOrder({{ $order->id }})" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            No orders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($orders) && $orders->hasPages())
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $orders->firstItem() }}</span>
                        to <span class="font-medium">{{ $orders->lastItem() }}</span>
                        of <span class="font-medium">{{ $orders->total() }}</span> results
                    </p>
                </div>
                <div>
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    function deleteOrder(id) {
        if (confirm('Are you sure you want to delete this order?')) {
            fetch(`/admin/orders/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) location.reload();
                else alert('Failed to delete order');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the order');
            });
        }
    }

    // Status filter
    document.getElementById('statusFilter').addEventListener('change', function() {
        const selectedStatus = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr:not(.empty-row)');

        rows.forEach(row => {
            const statusCell = row.cells[4];
            if (!statusCell) return;

            const statusSpan = statusCell.querySelector('span');
            if (!statusSpan) return;

            const statusText = statusSpan.textContent.trim().toLowerCase();

            if (selectedStatus === '' || statusText === selectedStatus) row.style.display = '';
            else row.style.display = 'none';
        });
    });
</script>
@endsection
