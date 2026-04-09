@extends('layouts.app')

@section('title', 'Company Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Company Settings</h1>
                    <p class="text-gray-600">Configure your company preferences and integrations</p>
                </div>
                <a href="{{ route('company.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Settings Navigation -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex space-x-1">
                <button onclick="showTab('general')" class="tab-btn px-4 py-2 rounded-lg bg-blue-600 text-white" data-tab="general">
                    <i class="fas fa-cog mr-2"></i>
                    General
                </button>
                <button onclick="showTab('notifications')" class="tab-btn px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300" data-tab="notifications">
                    <i class="fas fa-bell mr-2"></i>
                    Notifications
                </button>
                <button onclick="showTab('integrations')" class="tab-btn px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300" data-tab="integrations">
                    <i class="fas fa-plug mr-2"></i>
                    Integrations
                </button>
                <button onclick="showTab('billing')" class="tab-btn px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300" data-tab="billing">
                    <i class="fas fa-credit-card mr-2"></i>
                    Billing
                </button>
                <button onclick="showTab('security')" class="tab-btn px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300" data-tab="security">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Security
                </button>
            </div>
        </div>

        <!-- General Settings -->
        <div id="general-tab" class="tab-content">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">General Settings</h2>
                
                <form action="{{ route('company.settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Company Information -->
                    <div>
                        <h3 class="font-medium text-gray-800 mb-4">Company Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                                <input type="text" name="company_name" value="{{ $settings['company_name'] }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Company Email</label>
                                <input type="email" name="company_email" value="{{ $settings['company_email'] }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Company Phone</label>
                                <input type="tel" name="company_phone" value="{{ $settings['company_phone'] }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Company Website</label>
                                <input type="url" name="company_website" value="{{ $settings['company_website'] }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Address -->
                    <div>
                        <h3 class="font-medium text-gray-800 mb-4">Address</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
                                <input type="text" name="address" value="{{ $settings['address'] }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <input type="text" name="city" value="{{ $settings['city'] }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                                <input type="text" name="state" value="{{ $settings['state'] }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">ZIP Code</label>
                                <input type="text" name="zip_code" value="{{ $settings['zip_code'] }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                <input type="text" name="country" value="{{ $settings['country'] }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Business Hours -->
                    <div>
                        <h3 class="font-medium text-gray-800 mb-4">Business Hours</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Monday - Friday</label>
                                <input type="text" name="weekdays_hours" value="{{ $settings['weekdays_hours'] }}" placeholder="9:00 AM - 6:00 PM"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Saturday</label>
                                <input type="text" name="saturday_hours" value="{{ $settings['saturday_hours'] }}" placeholder="10:00 AM - 4:00 PM"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sunday</label>
                                <input type="text" name="sunday_hours" value="{{ $settings['sunday_hours'] }}" placeholder="Closed"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Notification Settings -->
        <div id="notifications-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Notification Settings</h2>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="font-medium text-gray-800 mb-4">Email Notifications</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-800">New Lead Notifications</h4>
                                    <p class="text-sm text-gray-600">Get notified when new leads are generated</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" {{ $settings['email_new_leads'] ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-800">Property Status Updates</h4>
                                    <p class="text-sm text-gray-600">Notifications for property status changes</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" {{ $settings['email_property_updates'] ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-800">Team Performance Reports</h4>
                                    <p class="text-sm text-gray-600">Weekly performance summaries</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" {{ $settings['email_performance_reports'] ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-medium text-gray-800 mb-4">SMS Notifications</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-800">Urgent Leads</h4>
                                    <p class="text-sm text-gray-600">SMS for high-priority leads</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" {{ $settings['sms_urgent_leads'] ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Integrations -->
        <div id="integrations-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Integrations</h2>
                
                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-3 mr-4">
                                <i class="fab fa-google text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-800">Google Analytics</h3>
                                <p class="text-sm text-gray-600">Track website traffic and user behavior</p>
                            </div>
                        </div>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Connect
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-green-100 rounded-full p-3 mr-4">
                                <i class="fab fa-mailchimp text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-800">Mailchimp</h3>
                                <p class="text-sm text-gray-600">Email marketing automation</p>
                            </div>
                        </div>
                        <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            Connect
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-purple-100 rounded-full p-3 mr-4">
                                <i class="fab fa-slack text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-800">Slack</h3>
                                <p class="text-sm text-gray-600">Team communication and notifications</p>
                            </div>
                        </div>
                        <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            Connect
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing -->
        <div id="billing-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Billing Settings</h2>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="font-medium text-gray-800 mb-4">Current Plan</h3>
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-gray-800">Professional Plan</h4>
                                    <p class="text-sm text-gray-600">$299/month - Up to 50 users</p>
                                </div>
                                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Upgrade Plan
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-medium text-gray-800 mb-4">Payment Methods</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-4 border rounded-lg">
                                <div class="flex items-center">
                                    <div class="bg-gray-200 rounded p-2 mr-3">
                                        <i class="fab fa-cc-visa text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Visa ending in 4242</p>
                                        <p class="text-sm text-gray-600">Expires 12/24</p>
                                    </div>
                                </div>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Default</span>
                            </div>
                        </div>
                        <button class="mt-4 bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                            Add Payment Method
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security -->
        <div id="security-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Security Settings</h2>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="font-medium text-gray-800 mb-4">Two-Factor Authentication</h3>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800">Enable 2FA for all users</p>
                                <p class="text-sm text-gray-600">Require two-factor authentication for company accounts</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" {{ $settings['require_2fa'] ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-medium text-gray-800 mb-4">Password Policy</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-800">Minimum Password Length</p>
                                    <p class="text-sm text-gray-600">Require passwords to be at least 8 characters</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
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
</script>
@endsection
