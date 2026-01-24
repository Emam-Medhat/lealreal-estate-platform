@extends('layouts.app')

@section('title', 'Company Profile')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Company Profile</h1>
                    <p class="text-gray-600">Manage your company information and branding</p>
                </div>
                <a href="{{ route('company.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Company Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Company Information</h2>
                <button onclick="editProfile()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Profile
                </button>
            </div>
            
            <div class="flex items-start space-x-6">
                <!-- Company Logo -->
                <div class="bg-gray-200 rounded-lg w-32 h-32 flex items-center justify-center">
                    @if($company->logo)
                        <img src="{{ $company->logo }}" alt="{{ $company->name }}" class="w-32 h-32 rounded-lg object-cover">
                    @else
                        <i class="fas fa-building text-gray-400 text-4xl"></i>
                    @endif
                </div>
                
                <!-- Company Details -->
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $company->name }}</h3>
                    <p class="text-gray-600 mb-4">{{ $company->description }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">{{ $company->email }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-phone text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">{{ $company->phone }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-globe text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">{{ $company->website }}</span>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">{{ $company->address }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-industry text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">{{ $company->industry }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-users text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">{{ $company->employees_count }} Employees</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-home text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Properties</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['properties'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-users text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Team Members</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['team_members'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-map-marked-alt text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Branches</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['branches'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Revenue</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['revenue'] ?? 0, 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services & Specializations -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Services & Specializations</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium text-gray-800 mb-3">Services</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($company->services ?? [] as $service)
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                                {{ $service }}
                            </span>
                        @endforeach
                    </div>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-800 mb-3">Specializations</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($company->specializations ?? [] as $specialization)
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                                {{ $specialization }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Certifications & Awards -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Certifications & Awards</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium text-gray-800 mb-3">Certifications</h3>
                    <div class="space-y-2">
                        @forelse ($company->certifications ?? [] as $certification)
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <i class="fas fa-certificate text-blue-600 mr-3"></i>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $certification['name'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $certification['issuer'] }} - {{ $certification['year'] }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">No certifications listed</p>
                        @endforelse
                    </div>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-800 mb-3">Awards</h3>
                    <div class="space-y-2">
                        @forelse ($company->awards ?? [] as $award)
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <i class="fas fa-trophy text-yellow-600 mr-3"></i>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $award['name'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $award['organization'] }} - {{ $award['year'] }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">No awards listed</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Social Media</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @if($company->social_media['facebook'] ?? null)
                    <a href="{{ $company->social_media['facebook'] }}" class="flex items-center justify-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <i class="fab fa-facebook text-blue-600 text-2xl"></i>
                    </a>
                @endif
                
                @if($company->social_media['twitter'] ?? null)
                    <a href="{{ $company->social_media['twitter'] }}" class="flex items-center justify-center p-4 bg-sky-50 rounded-lg hover:bg-sky-100 transition-colors">
                        <i class="fab fa-twitter text-sky-600 text-2xl"></i>
                    </a>
                @endif
                
                @if($company->social_media['linkedin'] ?? null)
                    <a href="{{ $company->social_media['linkedin'] }}" class="flex items-center justify-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <i class="fab fa-linkedin text-blue-700 text-2xl"></i>
                    </a>
                @endif
                
                @if($company->social_media['instagram'] ?? null)
                    <a href="{{ $company->social_media['instagram'] }}" class="flex items-center justify-center p-4 bg-pink-50 rounded-lg hover:bg-pink-100 transition-colors">
                        <i class="fab fa-instagram text-pink-600 text-2xl"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full max-h-screen overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Edit Company Profile</h3>
        
        <form action="{{ route('company.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                    <input type="text" name="name" value="{{ $company->name }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $company->description }}</textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ $company->email }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" name="phone" value="{{ $company->phone }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                    <input type="url" name="website" value="{{ $company->website }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <input type="text" name="address" value="{{ $company->address }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                    <input type="file" name="logo" accept="image/*"
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
