@extends('layouts.app')

@section('title', 'Company Branches')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Company Branches</h1>
                    <p class="text-gray-600">Manage your company locations and branches</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="addBranch()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Branch
                    </button>
                    <a href="{{ route('company.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Branches Map -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Branch Locations</h2>
            <div class="bg-gray-200 rounded-lg h-96 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-map-marked-alt text-6xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">Interactive map showing all branch locations</p>
                </div>
            </div>
        </div>

        <!-- Branches List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Branches List</h2>
                    <div class="flex items-center space-x-3">
                        <input type="text" placeholder="Search branches..." class="px-3 py-2 border rounded-lg text-sm">
                        <select class="px-3 py-2 border rounded-lg text-sm">
                            <option>All Branches</option>
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($branches as $branch)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-blue-100 rounded-full p-2 mr-3">
                                            <i class="fas fa-building text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $branch->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $branch->type }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $branch->address }}</div>
                                    <div class="text-sm text-gray-500">{{ $branch->city }}, {{ $branch->state }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $branch->phone }}</div>
                                    <div class="text-sm text-gray-500">{{ $branch->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $branch->staff_count }} members</div>
                                    <div class="text-sm text-gray-500">{{ $branch->manager }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($branch->status === 'active')
                                            bg-green-100 text-green-800
                                        @else
                                            bg-red-100 text-red-800
                                        @endif
                                    ">
                                        {{ ucfirst($branch->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex space-x-2">
                                        <button onclick="viewBranch({{ $branch->id }})" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editBranch({{ $branch->id }})" class="text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteBranch({{ $branch->id }})" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Branches Yet</h3>
                                    <p class="text-gray-500 mb-6">Start by adding your first branch location.</p>
                                    <button onclick="addBranch()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add First Branch
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Branch Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-building text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Branches</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $branches->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Active Branches</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $branches->where('status', 'active')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Staff</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $branches (function($b) { return $b->staff_count; })->Convert.ToInt32())amp;gt;sum() }}</p>
,                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-map-marker-alt text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Cities</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $branches->pluck('city')->unique()->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Branch Modal -->
<div id="branchModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full max-h-screen overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Add New Branch</h3>
        
        <form action="{{ route('company.branches.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Branch Name</label>
                        <input type="text" name="name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Branch Type</label>
                        <select name="type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="main">Main Office</option>
                            <option value="branch">Branch Office</option>
                            <option value="satellite">Satellite Office</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <input type="text" name="address" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" name="city" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                        <input type="text" name="state" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ZIP Code</label>
                        <input type="text" name="zip_code" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" name="phone" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Manager Name</label>
                        <input type="text" name="manager"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Staff Count</label>
                        <input type="number" name="staff_count" min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeBranchModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Add Branch
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function addBranch() {
    document.getElementById('branchModal').classList.remove('hidden');
}

function closeBranchModal() {
    document.getElementById('branchModal').classList.add('hidden');
}

function viewBranch(branchId) {
    window.location.href = '/company/branches/' + branchId;
}

function editBranch(branchId) {
    window.location.href = '/company/branches/' + branchId + '/edit';
}

function deleteBranch(branchId) {
    if (confirm('Are you sure you want to delete this branch?')) {
        fetch('/company/branches/' + branchId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
</script>
@endsection
