    @extends('admin.layout')

    @section('title', 'Admin - Staff Management')
    @section('page-title', 'Staff Management')

    @section('content')
    <div class="space-y-6">
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

        <div class="bg-white rounded-xl p-6 shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Staff Members</h2>
                <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-plus mr-1"></i> Add Staff Member
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>

                            {{-- Location removed from table --}}
                            {{-- <th class="px-6 py-3 ...">Location</th> --}}

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchases Referred</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Earnings</th>

                            {{-- ✅ Referral Code removed --}}
                            {{-- <th class="px-6 py-3 ...">Referral Code</th> --}}

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($staffMembers as $staff)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full"
                                            src="https://ui-avatars.com/api/?name={{ urlencode($staff->name) }}&background=4f46e5&color=fff"
                                            alt="{{ $staff->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $staff->name }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $staff->email }}</td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $staff->purchases_referred ?? 0 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₹{{ number_format($staff->total_earnings ?? 0, 2) }}</td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $staff->created_at->format('M d, Y') }}</td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="#"
                                class="text-green-600 hover:text-green-900 mr-4 view-btn"
                                title="View" aria-label="View"
                                data-id="{{ $staff->id }}"
                                data-name="{{ $staff->name }}"
                                data-email="{{ $staff->email }}"
                                data-phone="{{ $staff->phone }}"
                                data-purchases-referred="{{ $staff->purchases_referred ?? 0 }}"
                                data-total-earnings="{{ $staff->total_earnings ?? 0 }}"
                                data-referral-code="{{ $staff->referral_code }}"
                                data-bank-account-number="{{ $staff->bank_account_number }}"
                                data-ifsc-code="{{ $staff->ifsc_code }}"
                                data-bank-name="{{ $staff->bank_name }}"
                                data-city="{{ $staff->city }}"
                                data-village="{{ $staff->village }}"
                                data-address="{{ $staff->address }}"
                                data-district="{{ $staff->district }}"
                                data-pincode="{{ $staff->pincode }}"
                                data-created-at="{{ $staff->created_at->format('M d, Y') }}">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="#"
                                class="text-blue-600 hover:text-blue-900 mr-4 edit-btn"
                                title="Edit" aria-label="Edit"
                                data-id="{{ $staff->id }}"
                                data-name="{{ $staff->name }}"
                                data-email="{{ $staff->email }}"
                                data-phone="{{ $staff->phone }}"
                                data-bank-account-number="{{ $staff->bank_account_number }}"
                                data-ifsc-code="{{ $staff->ifsc_code }}"
                                data-bank-name="{{ $staff->bank_name }}"
                                data-city="{{ $staff->city }}"
                                data-village="{{ $staff->village }}"
                                data-address="{{ $staff->address }}"
                                data-district="{{ $staff->district }}"
                                data-pincode="{{ $staff->pincode }}">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <a href="#"
                                class="text-purple-600 hover:text-purple-900 mr-4 message-btn"
                                title="Send Message" aria-label="Send Message"
                                data-id="{{ $staff->id }}"
                                data-name="{{ $staff->name }}"
                                data-email="{{ $staff->email }}">
                                    <i class="fas fa-envelope"></i>
                                </a>

                                <a href="#"
                                class="text-red-600 hover:text-red-900 delete-btn"
                                title="Delete" aria-label="Delete"
                                data-id="{{ $staff->id }}">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            {{-- ✅ colspan changed: Name, Email, Purchases, Earnings, Referral Code, Created, Actions = 7 --}}
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No staff members found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $staffMembers->links() }}
            </div>
        </div>

        <!-- ✅ Navigation Section -->
        <div class="bg-white rounded-xl p-6 shadow-md">
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.staff.messages.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center">
                    <i class="fas fa-envelope mr-2"></i> View All Staff Messages
                </a>
            </div>
        </div>

        <!-- ✅ Bulk Message Section -->
        <div class="bg-white rounded-xl p-6 shadow-md mt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Send Message to Staff Members</h3>

            <form method="POST" action="{{ route('admin.staff.bulk-message') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                        <input type="text" name="subject" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter message subject">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                        <textarea name="message" rows="4" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter your message here..."></textarea>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Recipients</h4>
                        <div class="text-sm text-gray-600">
                            This message will be sent to all <span class="font-medium">{{ $staffMembers->total() }}</span> staff members
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 flex items-center">
                            <i class="fas fa-paper-plane mr-2"></i>Send to All Staff
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ✅ Add Staff Modal -->
    <div id="addStaffModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto p-4">
        <div class="min-h-full flex items-start justify-center py-6">
            <div class="bg-white rounded-xl p-6 w-full max-w-2xl mx-auto shadow-lg max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4 sticky top-0 bg-white pt-1">
                    <h3 class="text-lg font-bold text-gray-800">Add Staff Member</h3>
                    <button onclick="closeAddModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.staff.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                                <input type="text" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account Number</label>
                                <input type="text" name="bank_account_number" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">IFSC Code</label>
                                <input type="text" name="ifsc_code" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                                <input type="text" name="bank_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" name="city" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Village</label>
                                <input type="text" name="village" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                                <input type="text" name="district" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pincode</label>
                                <input type="text" name="pincode" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                                <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                                <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" onclick="closeAddModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Add Staff
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ✅ View Staff Modal (Referral Code removed) -->
    <div id="viewStaffModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto p-4">
        <div class="min-h-full flex items-start justify-center py-6">
            <div class="bg-white rounded-xl p-6 w-full max-w-2xl mx-auto shadow-lg max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4 sticky top-0 bg-white pt-1">
                    <h3 class="text-lg font-bold text-gray-800">Staff Member Details</h3>
                    <button onclick="closeViewModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <div id="viewName" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <div id="viewEmail" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <div id="viewPhone" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 font-medium"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Referral Code</label>
                            <div id="viewReferralCode" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 font-medium"></div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">Location</h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <div id="viewCity" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Village</label>
                                <div id="viewVillage" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                                <div id="viewDistrict" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pincode</label>
                                <div id="viewPincode" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <div id="viewAddress" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <h4 class="text-md font-semibold text-green-800 mb-3">Referral Performance</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Purchases Referred</label>
                                <div id="viewPurchasesReferred" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 font-medium"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total Earnings</label>
                                <div id="viewTotalEarnings" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 font-medium"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h4 class="text-md font-semibold text-blue-800 mb-3">Bank Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                                <div id="viewBankAccountNumber" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 font-medium"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">IFSC Code</label>
                                <div id="viewIfscCode" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 font-medium"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                                <div id="viewBankName" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 font-medium"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Created At</label>
                        <div id="viewCreatedAt" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 font-medium"></div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeViewModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Edit Staff Modal -->
    <div id="editStaffModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto p-4">
        <div class="min-h-full flex items-start justify-center py-6">
            <div class="bg-white rounded-xl p-6 w-full max-w-2xl mx-auto shadow-lg max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4 sticky top-0 bg-white pt-1">
                    <h3 class="text-lg font-bold text-gray-800">Edit Staff Member</h3>
                    <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="editStaffForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input type="text" name="name" id="editName" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" name="email" id="editEmail" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                                <input type="text" name="phone" id="editPhone" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account Number</label>
                                <input type="text" name="bank_account_number" id="editBankAccountNumber" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">IFSC Code</label>
                                <input type="text" name="ifsc_code" id="editIfscCode" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                                <input type="text" name="bank_name" id="editBankName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" name="city" id="editCity" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Village</label>
                                <input type="text" name="village" id="editVillage" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="address" id="editAddress" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                                <input type="text" name="district" id="editDistrict" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pincode</label>
                                <input type="text" name="pincode" id="editPincode" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">New Password (optional)</label>
                                <input type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Update Staff
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ✅ Send Message Modal -->
    <div id="messageStaffModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto p-4">
        <div class="min-h-full flex items-start justify-center py-6">
            <div class="bg-white rounded-xl p-6 w-full max-w-2xl mx-auto shadow-lg max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4 sticky top-0 bg-white pt-1">
                    <h3 class="text-lg font-bold text-gray-800">Send Message to Staff</h3>
                    <button onclick="closeMessageModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="sendMessageForm" method="POST" action="{{ route('admin.staff.message') }}">
                    @csrf
                    <input type="hidden" name="staff_id" id="messageStaffId">

                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">To:</label>
                                    <div id="messageStaffName" class="font-medium text-gray-900"></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                                    <div id="messageStaffEmail" class="text-gray-700"></div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                            <input type="text" name="subject" id="messageSubject" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter message subject">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                            <textarea name="message" id="messageContent" rows="6" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Enter your message here..."></textarea>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" onclick="closeMessageModal()"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                                <i class="fas fa-paper-plane mr-2"></i>Send Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ✅ View Message Modal -->
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
                        <div id="viewMessageContent" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 min-h-[100px]"></div>
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
        function openAddModal() { document.getElementById('addStaffModal').classList.remove('hidden'); }
        function closeAddModal() { document.getElementById('addStaffModal').classList.add('hidden'); }
        function closeViewModal() { document.getElementById('viewStaffModal').classList.add('hidden'); }
        function closeEditModal() { document.getElementById('editStaffModal').classList.add('hidden'); }
        function closeMessageModal() { document.getElementById('messageStaffModal').classList.add('hidden'); }
        function closeViewMessageModal() { document.getElementById('viewMessageModal').classList.add('hidden'); }

        // View
        document.querySelectorAll('.view-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');
                const phone = this.getAttribute('data-phone');
                const purchasesReferred = this.getAttribute('data-purchases-referred');
                const totalEarnings = this.getAttribute('data-total-earnings');
                const referralCode = this.getAttribute('data-referral-code') || this.closest('tr').querySelector('.referral-code-value').textContent.trim();

                const bankAccountNumber = this.getAttribute('data-bank-account-number') || '-';
                const ifscCode = this.getAttribute('data-ifsc-code') || '-';
                const bankName = this.getAttribute('data-bank-name') || '-';

                const city = this.getAttribute('data-city') || '-';
                const village = this.getAttribute('data-village') || '-';
                const address = this.getAttribute('data-address') || '-';
                const district = this.getAttribute('data-district') || '-';
                const pincode = this.getAttribute('data-pincode') || '-';

                const createdAt = this.getAttribute('data-created-at');

                document.getElementById('viewName').textContent = name;
                document.getElementById('viewEmail').textContent = email;
                document.getElementById('viewPhone').textContent = phone;
                document.getElementById('viewReferralCode').textContent = referralCode;
                document.getElementById('viewCreatedAt').textContent = createdAt;

                document.getElementById('viewBankAccountNumber').textContent = bankAccountNumber;
                document.getElementById('viewIfscCode').textContent = ifscCode;
                document.getElementById('viewBankName').textContent = bankName;

                document.getElementById('viewCity').textContent = city;
                document.getElementById('viewVillage').textContent = village;
                document.getElementById('viewAddress').textContent = address;
                document.getElementById('viewDistrict').textContent = district;
                document.getElementById('viewPincode').textContent = pincode;

                document.getElementById('viewPurchasesReferred').textContent = purchasesReferred;
                document.getElementById('viewTotalEarnings').textContent = '₹' + parseFloat(totalEarnings || 0).toFixed(2);

                document.getElementById('viewStaffModal').classList.remove('hidden');
            });
        });

        // Edit
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');

                document.getElementById('editName').value = this.getAttribute('data-name') || '';
                document.getElementById('editEmail').value = this.getAttribute('data-email') || '';
                document.getElementById('editPhone').value = this.getAttribute('data-phone') || '';

                document.getElementById('editBankAccountNumber').value = this.getAttribute('data-bank-account-number') || '';
                document.getElementById('editIfscCode').value = this.getAttribute('data-ifsc-code') || '';
                document.getElementById('editBankName').value = this.getAttribute('data-bank-name') || '';

                document.getElementById('editCity').value = this.getAttribute('data-city') || '';
                document.getElementById('editVillage').value = this.getAttribute('data-village') || '';
                document.getElementById('editAddress').value = this.getAttribute('data-address') || '';
                document.getElementById('editDistrict').value = this.getAttribute('data-district') || '';
                document.getElementById('editPincode').value = this.getAttribute('data-pincode') || '';

                document.getElementById('editStaffForm').action = `/admin/staff/${id}`;
                document.getElementById('editStaffModal').classList.remove('hidden');
            });
        });

        // Delete
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');

                if (confirm('Are you sure you want to delete this staff member?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/staff/${id}`;

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Message
        document.querySelectorAll('.message-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                document.getElementById('messageStaffId').value = this.getAttribute('data-id');
                document.getElementById('messageStaffName').textContent = this.getAttribute('data-name');
                document.getElementById('messageStaffEmail').textContent = this.getAttribute('data-email');
                document.getElementById('messageSubject').value = '';
                document.getElementById('messageContent').value = '';

                document.getElementById('messageStaffModal').classList.remove('hidden');
            });
        });

        // overlay click close
        document.getElementById('viewStaffModal')?.addEventListener('click', function(e) { if (e.target === this) closeViewModal(); });
        document.getElementById('addStaffModal')?.addEventListener('click', function(e) { if (e.target === this) closeAddModal(); });
        document.getElementById('editStaffModal')?.addEventListener('click', function(e) { if (e.target === this) closeEditModal(); });
        document.getElementById('messageStaffModal')?.addEventListener('click', function(e) { if (e.target === this) closeMessageModal(); });
        document.getElementById('viewMessageModal')?.addEventListener('click', function(e) { if (e.target === this) closeViewMessageModal(); });
    </script>
    @endsection
