@extends('layouts.app')

@section('title', 'Investor Profile')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Investor Profile</h1>
                    <p class="text-gray-600">Manage your investment preferences and settings</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="editProfile()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Profile
                    </button>
                    <a href="{{ route('investor.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-start space-x-6">
                <!-- Profile Picture -->
                <div class="bg-gray-200 rounded-lg w-32 h-32 flex items-center justify-center">
                    @if($investor->avatar)
                        <img src="{{ $investor->avatar }}" alt="{{ $investor->name }}" class="w-32 h-32 rounded-lg object-cover">
                    @else
                        <i class="fas fa-user text-gray-400 text-4xl"></i>
                    @endif
                </div>
                
                <!-- Personal Details -->
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $investor->name }}</h3>
                    <p class="text-gray-600 mb-4">{{ $investor->bio }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">{{ $investor->email }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-phone text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">{{ $investor->phone }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">{{ $investor->location }}</span>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">Member since {{ $investor->created_at->format('M Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-shield-alt text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">Verified Investor</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-star text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">{{ $investor->rating }}/5 Rating</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Investment Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Invested</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['total_invested'], 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Returns</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['total_returns'], 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-percentage text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Avg ROI</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['avg_roi'] }}%</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-building text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Properties</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['property_count'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Investment Preferences -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Investment Preferences</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium text-gray-800 mb-3">Property Types</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($investor->preferred_property_types ?? [] as $type)
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                                {{ ucfirst($type) }}
                            </span>
                        @endforeach
                    </div>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-800 mb-3">Risk Tolerance</h3>
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Risk Level</span>
                                <span class="font-medium text-gray-800">{{ $investor->risk_tolerance }}/10</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $investor->risk_tolerance * 10 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-800 mb-3">Investment Range</h3>
                    <div class="text-gray-700">
                        <p>Min: ${{ number_format($investor->min_investment, 0) }}</p>
                        <p>Max: ${{ number_format($investor->max_investment, 0) }}</p>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-800 mb-3">Investment Goals</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($investor->investment_goals ?? [] as $goal)
                            <span class="bg-green-100 text-re-700"></span>
 which is incorrect. Letlant text-green-itt-700aggi-700 which is incorrectemple-1 rounded-full text-sm">
                                {{ ucfirst($goal) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Verification Status -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Verification Status</h2>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-2 mr-3">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800">Identity Verification</h4>
                            <p class="text-sm text-gray-600">Verified on {{ $investor->identity_verified_at }}</p>
                        </div>
                    </div>
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">Verified</span>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-2 mr-3">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800">Accredited Investor Status</h4>
                            <p class="text-sm text-gray-600">Verified on {{ $investor->accredited_verified_at }}</p>
                        </div>
                    </div>
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">Accredited</span>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-2 mr-3">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800">Bank Account Verification</h4>
                            <p class="text-sm text-gray-600">Verified on {{ $investor->bank_verified_at }}</p>
                        </div>
                    </div>
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">Verified</span>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h2>
            
            <div class="space-y-3">
                @forelse ($recentActivity as $activity)
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div class="bg-blue-100 rounded-full p-2 mr-3">
                            <i class="fas fa-{{ $activity['icon'] }} text-blue-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">{{ $activity['title'] }}</p>
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
        
        <form action="{{ route('investor.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                    <input type="file" name="avatar" accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ $investor->name }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                    <textarea name="bio" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $investor->bio }}</textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ $investor->email }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" name="phone" value="{{ $investor->phone }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" name="location" value="{{ $investor->location }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Investment</label>
                        <input type="number" name="min_investment" value="{{ $investor->min_investment }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Investment</label>
                        <input type="number" name="max_investment" value="{{ $investor->max_investment }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Risk Tolerance (1-10)</label>
                    <input type="number" name="risk_tolerance" min="1" max="10" value="{{ $investor->risk_tolerance }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
</script>
@endsection
