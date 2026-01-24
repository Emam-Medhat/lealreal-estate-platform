@extends('layouts.app')

@section('title', 'Account Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Account Settings</h1>
                    <p class="text-gray-600">Manage your account security and preferences</p>
                </div>
                <a href="{{ route('user.profile') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Profile
                </a>
            </div>
        </div>

        <!-- Settings Navigation -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex space-x-1">
                <button onclick="showTab('security')" class="tab-btn px-4 py-2 rounded-lg bg-blue-600 text-white" data-tab="security">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Security
                </button>
                <button onclick="showTab('privacy')" class="tab-btn px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300" data-tab="privacy">
                    <i class="fas fa-lock mr-2"></i>
                    Privacy
                </button>
                <button onclick="showTab('notifications')" class="tab-btn px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300" data-tab="notifications">
                    <i class="fas fa-bell mr-2"></i>
                    Notifications
                </button>
                <button onclick="showTab('danger')" class="tab-btn px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300" data-tab="danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Danger Zone
                </button>
            </div>
        </div>

        <!-- Security Settings -->
        <div id="security-tab" class="tab-content">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Security Settings</h2>
                
                <!-- Password Change -->
                <div class="mb-8">
                    <h3 class="font-medium text-gray-800 mb-4">Change Password</h3>
                    <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                <input type="password" name="current_password" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div></div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input type="password" name="password" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                <input type="password" name="password_confirmation" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Update Password
                        </button>
                    </form>
                </div>

                <!-- Two-Factor Authentication -->
                <div class="mb-8">
                    <h3 class="font-medium text-gray-800 mb-4">Two-Factor Authentication</h3>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">2FA Status</p>
                            <p class="text-sm text-gray-600">{{ Auth::user()->two_factor_enabled ? 'Enabled' : 'Disabled' }}</p>
                        </div>
                        <a href="{{ route('two-factor.setup') }}" class="bg-{{ Auth::user()->two_factor_enabled ? 'red' : 'blue' }}-600 text-white px-4 py-2 rounded-lg hover:bg-{{ Auth::user()->two_factor_enabled ? 'red' : 'blue' }}-700 transition-colors">
                            {{ Auth::user()->two_factor_enabled ? 'Disable' : 'Enable' }}
                        </a>
                    </div>
                </div>

                <!-- Login Sessions -->
                <div class="mb-8">
                    <h3 class="font-medium text-gray-800 mb-4">Active Sessions</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-4 border rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-blue-100 rounded-full p-2 mr-3">
                                    <i class="fas fa-desktop text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Current Session</p>
                                    <p class="text-sm text-gray-600">{{ request()->userAgent() }}</p>
                                    <p class="text-xs text-gray-500">Started {{ now()->diffForHumans() }}</p>
                                </div>
                            </div>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </div>
                    </div>
                    <button onclick="logoutAllSessions()" class="mt-4 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout All Sessions
                    </button>
                </div>

                <!-- Biometric Devices -->
                <div class="mb-8">
                    <h3 class="font-medium text-gray-800 mb-4">Biometric Authentication</h3>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">Biometric Devices</p>
                            <p class="text-sm text-gray-600">Manage fingerprint and facial recognition</p>
                        </div>
                        <a href="{{ route('biometric.devices') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Manage Devices
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Privacy Settings -->
        <div id="privacy-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Privacy Settings</h2>
                
                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Public Profile</h3>
                            <p class="text-sm text-gray-600">Make your profile visible to other users</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" {{ Auth::user()->public_profile ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Show Online Status</h3>
                            <p class="text-sm text-gray-600">Let others see when you're online</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" {{ Auth::user()->show_online_status ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Activity Tracking</h3>
                            <p class="text-sm text-gray-600">Allow us to track your activity for better recommendations</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" {{ Auth::user()->activity_tracking ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Data Sharing</h3>
                            <p class="text-sm text-gray-600">Share anonymized data to improve our services</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" {{ Auth::user()->data_sharing ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div id="notifications-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Notification Preferences</h2>
                
                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Email Notifications</h3>
                            <p class="text-sm text-gray-600">Receive email updates about your account activity</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" {{ Auth::user()->email_notifications ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">SMS Notifications</h3>
                            <p class="text-sm text-gray-600">Receive text messages for important updates</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" {{ Auth::user()->sms_notifications ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Push Notifications</h3>
                            <p class="text-sm text-gray-600">Receive push notifications in your browser</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" {{ Auth::user()->push_notifications ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Marketing Communications</h3>
                            <p class="text-sm text-gray-600">Receive marketing emails and promotional offers</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" {{ Auth::user()->marketing_communications ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div id="danger-tab" class="tab-content hidden">
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-red-800 mb-6">Danger Zone</h2>
                
                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border">
                        <div>
                            <h3 class="font-medium text-red-800">Download Your Data</h3>
                            <p class="text-sm text-red-600">Get a copy of all your data</p>
                        </div>
                        <button onclick="downloadData()" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                            Download Data
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border">
                        <div>
                            <h3 class="font-medium text-red-800">Disable Account</h3>
                            <p class="text-sm text-red-600">Temporarily disable your account</p>
                        </div>
                        <button onclick="disableAccount()" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                            Disable Account
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border">
                        <div>
                            <h3 class="font-medium text-red-800">Delete Account</h3>
                            <p class="text-sm text-red-600">Permanently delete your account and all data</p>
                        </div>
                        <button onclick="deleteAccount()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Update button styles
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    // Highlight active button
    const activeBtn = document.querySelector('[data-tab="' + tabName + '"]');
    activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
    activeBtn.classList.add('bg-blue-600', 'text-white');
}

function logoutAllSessions() {
    if (confirm('Are you sure you want to logout from all devices?')) {
        fetch('/logout-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/login';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function downloadData() {
    window.location.href = '/user/download-data';
}

function disableAccount() {
    if (confirm('Are you sure you want to disable your account? You can reactivate it later.')) {
        fetch('/user/disable', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/login';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function deleteAccount() {
    window.location.href = '/user/delete';
}
</script>
@endsection
