@extends('layouts.app')

@section('title', 'Agent Leads')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Lead Management</h1>
                    <p class="text-gray-600">Track and manage your potential clients</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="addLead()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Lead
                    </button>
                    <a href="{{ route('agent.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Lead Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Leads</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $leads->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-clock text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">New Leads</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $leads->where('status', 'new')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-handshake text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">In Progress</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $leads->where('status', 'in_progress')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Converted</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $leads->where('status', 'converted')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                    <input type="text" placeholder="Search leads..." class="px-3 py-2 border rounded-lg text-sm w-full md:w-64">
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Status</option>
                        <option>New</option>
                        <option>Contacted</option>
                        <option>In Progress</option>
                        <option>Converted</option>
                        <option>Closed</option>
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Sources</option>
                        <option>Website</option>
                        <option>Social Media</option>
                        <option>Referral</option>
                        <option>Walk-in</option>
                        <option>Phone</option>
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Priority</option>
                        <option>High</option>
                        <option>Medium</option>
                        <option>Low</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button class="bg-gray-600 text-white px-3 py-2 rounded hover:bg-gray-700 transition-colors text-sm">
                        <i class="fas fa-filter mr-1"></i>
                        Filter
                    </button>
                    <button class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 transition-colors text-sm">
                        <i class="fas fa-download mr-1"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Leads Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" class="rounded">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property Interest</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($leads as $lead)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="rounded">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-gray-200 rounded-full w-10 h-10 mr-3 flex items-center justify-center">
                                            @if($lead->avatar)
                                                <img src="{{ $lead->avatar }}" alt="" class="w-10 h-10 rounded-full object-cover">
                                            @else
                                                <i class="fas fa-user text-gray-400 text-sm"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $lead->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $lead->created_at->format('M j, Y') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <i class="fas fa-phone text-gray-400 mr-1"></i>
                                            {{ $lead->phone }}
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope text-gray-400 mr-1"></i>
                                            {{ $lead->email }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($lead->property_interest)
                                            <div class="font-medium">{{ $lead->property_interest->type }}</div>
                                            <div class="text-gray-500">${{ number_format($lead->property_interest->budget, 0) }} budget</div>
                                        @else
                                            <span class="text-gray-500">Not specified</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($lead->source) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($lead->status === 'new')
                                            bg-green-100 text-green-800
                                        @elseif($lead->status === 'in_progress')
                                            bg-yellow-100 text-yellow-800
                                        @elseif($lead->status === 'converted')
                                            bg-blue-100 text-blue-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($lead->priority === 'high')
                                            bg-red-100 text-red-800
                                        @elseif($lead->priority === 'medium')
                                            bg-yellow-100 text-yellow-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ ucfirst($lead->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex space-x-2">
                                        <button onclick="viewLead({{ $lead->id }})" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editLead({{ $lead->id }})" class="text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="contactLead({{ $lead->id }})" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-phone"></i>
                                        </button>
                                        <button onclick="deleteLead({{ $lead->id }})" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Leads Yet</h3>
                                    <p class="text-gray-500 mb-6">Start adding leads to build your client pipeline.</p>
                                    <button onclick="addLead()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add First Lead
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Lead Activity</h2>
            <div class="space-y-3">
                @forelse ($recentActivity as $activity)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="bg-blue-100 rounded-full p-2 mr-3">
                            <i class="fas fa-{{ $activity['icon'] }} text-blue-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">{{ $activity['message'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No recent activity</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Add Lead Modal -->
<div id="leadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full max-h-screen overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Add New Lead</h3>
        
        <form action="{{ route('agent.leads.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" name="phone" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Source</label>
                        <select name="source" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Source</option>
                            <option value="website">Website</option>
                            <option value="social_media">Social Media</option>
                            <option value="referral">Referral</option>
                            <option value="walk_in">Walk-in</option>
                            <option value="phone">Phone</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select name="priority" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Priority</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property Interest</label>
                    <select name="property_type_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Property Type</option>
                        @foreach ($propertyTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Budget Range</label>
                    <input type="text" name="budget" placeholder="e.g., $200,000 - $300,000"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeLeadModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Add Lead
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function addLead() {
    document.getElementById('leadModal').classList.remove('hidden');
}

function closeLeadModal() {
    document.getElementById('leadModal').classList.add('hidden');
}

function viewLead(leadId) {
    window.location.href = '/agent/leads/' + leadId;
}

function editLead(leadId) {
    window.location.href = '/agent/leads/' + leadId + '/edit';
}

function contactLead(leadId) {
    window.location.href = '/agent/leads/' + leadId + '/contact';
}

function deleteLead(leadId) {
    if (confirm('Are you sure you want to delete this lead?')) {
        fetch('/agent/leads/' + leadId, {
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
