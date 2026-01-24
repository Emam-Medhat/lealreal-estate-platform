@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">My Profile</h1>
                        <p class="text-gray-600">Manage your personal information and account settings</p>
                    </div>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Profile Information</h2>
                    <a href="{{ route('user.profile.edit') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Profile
                    </a>
                </div>

                <div class="flex items-center mb-6">
                    <div class="bg-gray-200 rounded-full w-20 h-20 mr-6 flex items-center justify-center">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover">
                        @else
                            <i class="fas fa-user text-gray-400 text-2xl"></i>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800">{{ $user->name }}</h3>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        <p class="text-sm text-gray-500">Member since {{ $user->created_at->format('M j, Y') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-800 mb-3">Personal Information</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Full Name:</span>
                                <span class="font-medium">{{ $user->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email:</span>
                                <span class="font-medium">{{ $user->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Phone:</span>
                                <span class="font-medium">{{ $user->phone ?? 'Not provided' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date of Birth:</span>
                                <span
                                    class="font-medium">{{ $user->date_of_birth ? $user->date_of_birth->format('M j, Y') : 'Not provided' }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-800 mb-3">Location</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Country:</span>
                                <span class="font-medium">{{ $user->country ?? 'Not provided' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">City:</span>
                                <span class="font-medium">{{ $user->city ?? 'Not provided' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">State/Province:</span>
                                <span class="font-medium">{{ $user->state ?? 'Not provided' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">ZIP Code:</span>
                                <span class="font-medium">{{ $user->zip_code ?? 'Not provided' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Settings -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Account Settings</h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Email Notifications</h3>
                            <p class="text-sm text-gray-600">Receive email updates about your account activity</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" {{ $user->email_notifications ? 'checked' : '' }}>
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                            </div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">SMS Notifications</h3>
                            <p class="text-sm text-gray-600">Receive text messages for important updates</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" {{ $user->sms_notifications ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray lax-gray-oken after:border after entertainer roundedekk rounded-full datum after:h articulation和尚 after: Boundary after: 
                                after:h-葵 after:w- UL after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Two-Factor Authentication</h3>
                            <p class="text-sm text-gray-600">Add an extra layer of security to your account</p>
                        </div>
                        <a href="{{ route('two-factor.setup') }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            {{ $user->two_factor_enabled ? 'Manage' : 'Enable' }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Security -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Security</h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Change Password</h3>
                            <p class="text-sm text-gray-600">Update your account password</p>
                        </div>
                        <a href="{{ route('password.change') }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            Change Password
                        </a>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Login Activity</h3>
                            <p class="text-sm text-gray-600">View your recent login history</p>
                        </div>
                        <a href="{{ route('user.activity') }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            View Activity
                        </a>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Biometric Devices</h3>
                            <p class="text-sm text-gray-600">Manage your biometric authentication devices</p>
                        </div>
                        <a href="{{ route('biometric.devices') }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            Manage Devices
                        </a>
                    </div>
                </div>
            </div>

            <!-- Connected Accounts -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Connected Accounts</h2>
                    <a href="{{ route('social.accounts') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Manage →
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-red-100 rounded-full p-2 mr-3">
                                <i class="fab fa-google text-red-600"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-800">Google</h3>
                                <p class="text-sm text-gray-600">
                                    {{ $user->socialAccounts()->where('provider', 'google')->exists() ? 'Connected' : 'Not connected' }}
                                </p>
                            </div>
                        </div>
                        @if($user->socialAccounts()->where('provider', 'google')->exists())
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Connected
                            </span>
                        @else
                            <a href="{{ route('social.redirect', 'google') }}"
                                class="text-blue-600 hover:text-blue-800 text-sm">
                                Connect
                            </a>
                        @endif
                    </div>

                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-2 mr-3">
                                <i class="fab fa-facebook text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-800">Facebook</h3>
                                <p class="text-sm text-gray-600">
                                    {{ $user->socialAccounts()->where('provider', 'facebook')->exists() ? 'Connected' : 'Not connected' }}
                                </p>
                            </div>
                        </div>
                        @if($user->socialAccounts()->where('provider', 'facebook')->exists())
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Connected
                            </span>
                        @else
                            <a href="{{ route('social.redirect', 'facebook') }}"
                                class="text-blue-600 hover:text-blue-800 text-sm">
                                Connect
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-red-800 mb-4">Danger Zone</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-red-800">Delete Account</h3>
                            <p class="text-sm text-red-600">Permanently delete your account and all data</p>
                        </div>
                        <button onclick="showDeleteModal()"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Delete Account</h3>

            <div class="mb-6">
                <p class="text-gray-600 mb-4">Are you sure you want to delete your account? This action cannot be undone and
                    will permanently delete:</p>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li>Your profile information</li>
                    <li>Saved properties and preferences</li>
                    <li>Transaction history</li>
                    <li>All account data</li>
                </ul>
            </div>

            <form action="{{ route('user.delete') }}" method="POST" onsubmit="return confirmDelete()">
                @csrf
                @method('DELETE')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type "DELETE" to confirm</label>
                    <input type="text" name="confirm_text" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function confirmDelete() {
            const confirmText = document.querySelector('input[name="confirm_text"]').value;
            if (confirmText !== 'DELETE') {
                alert('Please type "DELETE" to confirm account deletion');
                return false;
            }
            return confirm('Are you absolutely sure? This action cannot be undone.');
        }
    </script>
@endsection