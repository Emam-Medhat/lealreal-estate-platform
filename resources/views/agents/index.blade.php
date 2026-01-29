@extends('layouts.app')

@section('title', 'Agent Management - Real Estate Pro')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Agent Management</h1>
                <p class="text-gray-600">Manage and monitor all real estate agents in the system</p>
            </div>
            <a href="{{ route('agents.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Add New Agent
            </a>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
            <form action="{{ route('agents.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Agents</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email or license..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gray-900 text-white py-2 rounded-lg font-semibold hover:bg-gray-800 transition-colors">
                        Filter Results
                    </button>
                </div>
            </form>
        </div>

        <!-- Agents Table -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Agent</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Contact</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Company</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Properties</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($agents as $agent)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold mr-3 overflow-hidden">
                                            @if($agent->profile && $agent->profile->photo)
                                                <img src="{{ Storage::url($agent->profile->photo) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                {{ substr($agent->user->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $agent->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $agent->license_number ?? 'No License' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $agent->user->email }}</div>
                                    <div class="text-xs text-gray-500">{{ $agent->profile->phone ?? 'No phone' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $agent->company->name ?? 'Independent' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $agent->properties_count ?? $agent->properties->count() }} Listings
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'active' => 'bg-green-100 text-green-800',
                                            'inactive' => 'bg-red-100 text-red-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                        ];
                                        $statusClass = $statusClasses[$agent->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ ucfirst($agent->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('agents.show', $agent) }}" class="text-blue-600 hover:text-blue-900" title="View Profile">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('agents.edit', $agent) }}" class="text-gray-600 hover:text-gray-900" title="Edit Agent">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('agents.destroy', $agent) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this agent?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete Agent">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No agents found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($agents->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $agents->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
