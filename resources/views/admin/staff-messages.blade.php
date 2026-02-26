@extends('admin.layout')

@section('title', 'Admin - Staff Messages')
@section('page-title', 'Staff Messages')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Staff Messages</h2>
        <a href="{{ route('admin.staff.manage') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Staff
        </a>
    </div>

    <!-- Messages Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($messages as $message)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 truncate max-w-xs">{{ $message->subject }}</div>
                            <div class="text-sm text-gray-500 mt-1 truncate max-w-xs">{{ Str::limit($message->message, 100) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($message->staff)
                                <div>{{ $message->staff->name }}</div>
                                <div class="text-gray-500">{{ $message->staff->email }}</div>
                            @else
                                <span class="text-gray-500">All Staff</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $message->created_at->format('M d, Y g:i A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewMessage({{ $message->id }})" data-subject="{{ $message->subject }}" data-recipient="{{ $message->staff->name ?? 'All Staff' }}" data-email="{{ $message->staff->email ?? 'N/A' }}" data-message="{{ $message->message }}" data-date="{{ $message->created_at->format('M d, Y g:i A') }}" 
                                    class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="deleteMessage({{ $message->id }})" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-envelope text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">No messages found</h3>
                                <p class="text-gray-500">No messages have been sent to staff members yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if(isset($messages) && $messages->hasPages())
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                {{ $messages->links('vendor.pagination.simple-default') }}
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $messages->firstItem() }}</span> to <span class="font-medium">{{ $messages->lastItem() }}</span> of <span class="font-medium">{{ $messages->total() }}</span> results
                    </p>
                </div>
                <div>
                    {{ $messages->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- View Message Modal -->
<div id="viewMessageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto p-4">
    <div class="min-h-full flex items-start justify-center py-6">
        <div class="bg-white rounded-xl p-6 w-full max-w-2xl mx-auto shadow-lg max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4 sticky top-0 bg-white pt-1">
                <h3 class="text-lg font-bold text-gray-800">View Message</h3>
                <button onclick="closeViewMessageModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <div id="viewMessageSubject" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 font-medium"></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recipient</label>
                    <div id="viewMessageRecipient" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100"></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <div id="viewMessageDate" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100"></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <div id="viewMessageContent" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 min-h-[150px]"></div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeViewMessageModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function viewMessage(id) {
        const button = event.currentTarget;
        const subject = button.getAttribute('data-subject');
        const recipient = button.getAttribute('data-recipient');
        const email = button.getAttribute('data-email');
        const message = button.getAttribute('data-message');
        const date = button.getAttribute('data-date');
        
        document.getElementById('viewMessageSubject').textContent = subject;
        document.getElementById('viewMessageRecipient').textContent = recipient + ' (' + email + ')';
        document.getElementById('viewMessageDate').textContent = date;
        document.getElementById('viewMessageContent').textContent = message;
        document.getElementById('viewMessageModal').classList.remove('hidden');
    }

    function closeViewMessageModal() {
        document.getElementById('viewMessageModal').classList.add('hidden');
    }

    function deleteMessage(id) {
        if (confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
            fetch(`/admin/staff-messages/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Message deleted successfully!');
                    location.reload();
                } else {
                    alert('Error deleting message: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting message. Please try again.');
            });
        }
    }

    // Close modal when clicking outside
    document.getElementById('viewMessageModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeViewMessageModal();
    });
</script>
@endsection