@extends('layouts.app')

@section('title', 'Developer Profile')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Developer Profile</h1>
                    <p class="text-gray-600">Manage your company information and credentials</p>
                </div>
                <a href="{{ route('developer.dashboard') }}" class="text-gray-600 hover:text-gray-800">
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
                    @if($developer->logo)
                        <img src="{{ $developer->logo }}" alt="{{ $developer->name }}" class="w-32 h-32 rounded-lg object-cover">
                    @else
                        <i class="fas fa-building text-gray-400 text-4xl"></i>
                    @endif
                </div>
                
                <!-- Company Details -->
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $developer->name }}</h3>
                    <p class="text-gray-600 mb-4">{{ $developer->description }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">{{ $developer->email }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-phone text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">{{ $developer->phone }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-globe text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">{{ $developer->website }}</span>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">{{ $developer->address }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-industry text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">{{ $developer->industry }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">Founded {{ $developer->founded_year }}</span>
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
                        <i class="fas fa-building text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Projects</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_projects'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-home text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Units Completed</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['units_completed'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Value</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['total_value'], 0) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-award text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Awards</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['awards_count'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services & Expertise -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Services & Expertise</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium text-gray-800 mb-3">Development Services</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($developer->services ?? [] as $service)
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                                {{ $service }}
                            </span>
                        @endforeach
                    </div>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-800 mb-3">Property Types</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($developer->property_types ?? [] as $type)
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                                {{ $type }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Certifications & Licenses -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Certifications & Licenses</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium text-gray-800 mb-3">Certifications</h3>
                    <div class="space-y-2">
                        @forelse ($developer->certifications ?? [] as $certification)
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
                    <h3 class="font-medium text-gray-800 mb-3">Licenses</h3>
                    <div class="space-y-2">
                        @forelse ($developer->licenses ?? [] as $license)
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <i class="fas fa-file-contract text-green-600 mr-3"></i>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $license['number'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $license['type'] }} - Expires: {{ $license['expires'] }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">No licenses listed</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Projects -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Featured Projects</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($featuredProjects as $project)
                    <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-32 bg-gray-200">
                            @if($project->image)
                                <img src="{{ $project->image }}" alt="{{ $project->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-building text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h4 class="font-medium text-gray-800 mb-1">{{ $project->name }}</h4>
                            <p class="text-sm text-gray-600 mb-2">{{ $project->location }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-800">${{ number_format($project->total_value, 0) }}</span>
                                <span class="text-xs text-green-600">{{ $project->status }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Team -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Key Team Members</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($teamMembers as $member)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="bg-gray-200 rounded-full w-12 h-12 mr-3 flex items-center justify-center">
                            @if($member->avatar)
                                <img src="{{ $member->avatar }}" alt="" class="w-12 h-12 rounded-full object-cover">
                            @else
                                <i class="fas fa-user text-gray-400 text-sm"></i>
                            @endif
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $member->name }}</p>
                            <p class="text-sm text-gray-600">{{ $member->position }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full max-h-screen overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Edit Company Profile</h3>
        
        <form action="{{ route('developer.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Logo</label>
                    <input type="file" name="logo" accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                    <input type="text" name="name" value="{{ $developer->name }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $developer->description }}</textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ $developer->email }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" name="phone" value="{{ $developer->phone }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                    <input type="url" name="website" value="{{ $developer->website }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <input type="text" name="address" value="{{ $developer->address }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                    <input type="text" name="industry" value="{{ $developer->industry }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Founded Year</label>
                    <input type="number" name="founded_year" value="{{ $developer->founded_year }}"
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
