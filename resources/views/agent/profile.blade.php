@extends('layouts.app')

@section('title', 'Agent Profile')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Agent Profile</h1>
                    <p class="text-gray-600">Manage your professional information and credentials</p>
                </div>
                <a href="{{ route('agent.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Profile Overview -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-start space-x-6">
                <!-- Profile Picture -->
                <div class="bg-gray-200 rounded-full w-32 h-32 flex items-center justify-center">
                    @if($agent->avatar)
                        <img src="{{ $agent->avatar }}" alt="{{ $agent->name }}" class="w-32 h-32 rounded-full object-cover">
                    @else
                        <i class="fas fa-user text-gray-400 text-4xl"></i>
                    @endif
                </div>
                
                <!-- Agent Details -->
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $agent->name }}</h2>
                            <p class="text-gray-600 mb-2">{{ $agent->title }}</p>
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <span><i class="fas fa-building mr-1"></i>{{ $agent->company->name }}</span>
                                <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $agent->location }}</span>
                                <span><i class="fas fa-phone mr-1"></i>{{ $agent->phone }}</span>
                                <span><i class="fas fa-envelope mr-1"></i>{{ $agent->email }}</span>
                            </div>
                        </div>
                        <button onclick="editProfile()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Profile
                        </button>
                    </div>
                    
                    <!-- Bio -->
                    <div class="mb-4">
                        <h3 class="font-medium text-gray-800 mb-2">About</h3>
                        <p class="text-gray-600">{{ $agent->bio ?? 'Professional real estate agent dedicated to helping clients find their dream properties.' }}</p>
                    </div>
                    
                    <!-- Specializations -->
                    @if($agent->specializations)
                        <div>
                            <h3 class="font-medium text-gray-800 mb-2">Specializations</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($agent->specializations as $specialization)
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                                        {{ $specialization }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Performance Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-home text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Properties Sold</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['properties_sold'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['total_revenue'] ?? 0, 0) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Active Clients</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['active_clients'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-star text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Rating</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['rating'] ?? 0 }}/5</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Licenses & Certifications -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Licenses & Certifications</h2>
            
            <div class="space-y-4">
                @forelse ($agent->licenses as $license)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-2 mr-3">
                                <i class="fas fa-certificate text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">{{ $license->type }}</h4>
                                <p class="text-sm text-gray-600">{{ $license->number }} - Expires: {{ $license->expires_at->format('M j, Y') }}</p>
                            </div>
                        </div>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Active</span>
                    </div>
                @empty
                    <p class="text-gray-500">No licenses added yet</p>
                @endforelse
            </div>
            
            <button onclick="addLicense()" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Add License
            </button>
        </div>

        <!-- Contact Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-phone text-gray-400 mr-3 w-5"></i>
                        <span class="text-gray-700">{{ $agent->phone }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-gray-400 mr-3 w-5"></i>
                        <span class="text-gray-700">{{ $agent->email }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-gray-400 mr-3 w-5"></i>
                        <span class="text-gray-700">{{ $agent->address }}</span>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fab fa-linkedin text-gray-400 mr-3 w-5"></i>
                        <span class="text-gray-700">{{ $agent->linkedin ?? 'Not provided' }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fab fa-facebook text-gray-400 mr-3 w-5"></i>
                        <span class="text-gray-700">{{ $agent->facebook ?? 'Not provided' }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-globe text-gray-400 mr-3 w-5"></i>
                        <span class="text-gray-700">{{ $agent->website ?? 'Not provided' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h2>
            
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

<!-- Edit Profile Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full max-h-screen overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Edit Profile</h3>
        
        <form action="{{ route('agent.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                    <input type="file" name="avatar" accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" value="{{ $agent->first_name }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" value="{{ $agent->last_name }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" name="title" value="{{ $agent->title }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                    <textarea name="bio" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $agent->bio ?? '' }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" name="phone" value="{{ $agent->phone }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <input type="text" name="address" value="{{ $agent->address }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">LinkedIn</label>
                        <input type="url" name="linkedin" value="{{ $agent->linkedin ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                        <input type="url" name="website" value="{{ $agent->website ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editProfile() {
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function addLicense() {
    window.location.href = '/agent/licenses/create';
}
</script>
@endsection
