@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center mb-4">
                <a href="{{ route('user.profile') }}" class="text-gray-600 hover:text-gray-800 mr-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Profile</h1>
                    <p class="text-gray-600">Update your personal information and preferences</p>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm p-6">
            @csrf
            @method('PUT')
            
            <!-- Profile Picture -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Profile Picture</h2>
                <div class="flex items-center space-x-6">
                    <div class="bg-gray-200 rounded-full w-24 h-24 flex items-center justify-center">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover">
                        @else
                            <i class="fas fa-user text-gray-400 text-3xl"></i>
                        @endif
                    </div>
                    <div>
                        <div class="mb-4">
                            <label for="avatar" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                                <i class="fas fa-upload mr-2"></i>
                                Upload New Picture
                            </label>
                            <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*" onchange="previewAvatar(this)">
                        </div>
                        <p class="text-sm text-gray-500">JPG, PNG or GIF. Max size 2MB</p>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ $user->name }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ $user->email }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ $user->phone }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" 
                            value="{{ $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Location Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <select id="country" name="country" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Country</option>
                            <option value="US" {{ $user->country === 'US' ? 'selected' : '' }}>United States</option>
                            <option value="CA" {{ $user->country === 'CA' ? 'selected' : '' }}>Canada</option>
                            <option value="UK" {{ $user->country === 'UK' ? 'selected' : '' }}>United Kingdom</option>
                            <!-- Add more countries as needed -->
                        </select>
                    </div>
                    
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" id="city" name="city" value="{{ $user->city }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State/Province</label>
                        <input type="text" id="state" name="state" value="{{ $user->state }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-2">ZIP/Postal Code</label>
                        <input type="text" id="zip_code" name="zip_code" value="{{ $user->zip_code }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Bio -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">About Me</h2>
                <div class="mb-4">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                    <textarea id="bio" name="bio" rows="4" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Tell us about yourself...">{{ $user->bio ?? '' }}</textarea>
                </div>
                <p class="text-sm text-gray-500">{{ 500 - (strlen($user->bio ?? '')) }} characters remaining</p>
            </div>

            <!-- Preferences -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Preferences</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-gray-800">Email Notifications</h3>
                            <p class="text-sm text-gray-600">Receive email updates about your account activity</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_notifications" class="sr-only peer" {{ $user->email_notifications ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-gray-800">SMS Notifications</h3>
                            <p class="text-sm text-gray-600">Receive text messages for important updates</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="sms_notifications" class="sr-only peer" {{ $user->sms_notifications ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-gray-800">Public Profile</h3>
                            <p class="text-sm text-gray-600">Make your profile visible to other users</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="public_profile" class="sr-only peer" {{ $user->public_profile ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <a href="{{ route('user.profile') }}" class="text-gray-600 hover:text-gray-800">
                    Cancel
                </a>
                <div class="flex space-x-3">
                    <button type="button" onclick="resetForm()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Reset
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const avatarImg = document.querySelector('.bg-gray-200.rounded-full img');
            if (avatarImg) {
                avatarImg.src = e.target.result;
            } else {
                const container = document.querySelector('.bg-gray-200.rounded-full');
                container.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="w-24 h-24 rounded-full object-cover">';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        location.reload();
    }
}

// Character counter for bio
document.getElementById('bio').addEventListener('input', function() {
    const remaining = 500 - this.value.length;
    const counter = this.parentElement.nextElementSibling;
    counter.textContent = remaining + ' characters remaining';
    
    if (remaining < 50) {
        counter.classList.add('text-red-600');
        counter.classList.remove('text-gray-500');
    } else {
        counter.classList.remove('text-red-600');
        counter.classList.add('text-gray-500');
    }
});
</script>
@endsection
