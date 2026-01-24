@extends('layouts.app')

@section('title', 'User Preferences')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Preferences</h1>
                    <p class="text-gray-600">Customize your experience and notification settings</p>
                </div>
                <a href="{{ route('user.profile') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Profile
                </a>
            </div>
        </div>

        <!-- Preferences Form -->
        <form action="{{ route('user.preferences.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Notification Preferences -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Notification Preferences</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Email Notifications</h3>
                            <p class="text-sm text-gray-600">Receive email updates about your account activity</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_notifications" class="sr-only peer" {{ $preferences->email_notifications ?? true ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">SMS Notifications</h3>
                            <p class="text-sm text-gray-600">Receive text messages for important updates</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="sms_notifications" class="sr-only peer" {{ $preferences->sms_notifications ?? false ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Push Notifications</h3>
                            <p class="text-sm text-gray-600">Receive push notifications in your browser</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="push_notifications" class="sr-only peer" {{ $preferences->push_notifications ?? true ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Property Alerts -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Property Alerts</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">New Property Matches</h3>
                            <p class="text-sm text-gray-600">Get notified when new properties match your criteria</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="property_matches" class="sr-only peer" {{ $preferences->property_matches ?? true ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Price Changes</h3>
                            <p class="text-sm text-gray-600">Get notified when saved properties change price</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="price_changes" class="sr-only peer" {{ $preferences->price_changes ?? true ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Open House Notifications</h3>
                            <p class="text-sm text-gray-600">Get notified about open houses for saved properties</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="open_house_notifications" class="sr-only peer" {{ $preferences->open_house_notifications ?? false ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Communication Preferences -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Communication Preferences</h2>
                
                <div class="space-y-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Contact Method</label>
                        <select name="preferred_contact" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="email" {{ ($preferences->preferred_contact ?? 'email') === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="phone" {{ ($preferences->preferred_contact ?? 'email') === 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="sms" {{ ($preferences->preferred_contact ?? 'email') === 'sms' ? 'selected' : '' }}>SMS</option>
                            <option value="whatsapp" {{ ($preferences->preferred_contact ?? 'email') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        </select>
                    </div在水>
                    < perk
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Communication Frequency</label>
                        < evident
                        <select name="communication_frequency进行比较 frequency" class="w-full px-3 py caffeinated py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="immediately" {{ ($preferences->communication_frequency ?? 'immediately') === 'immediately' ? 'selected' : '' }}>Immediately</option>
                            <option value="daily" {{ ($preferences->communication_frequency ?? 'immediately') === 'daily' ? 4 'selected 
                            'selected'.
                            ' : '' }}>Daily Digest</option>
                            <option value="weekly" {{ ($preferences->communication_frequency ?? 'immediately') === 'weekly' ? 'selected' : '' }}>Weekly Summary</option>
                            <option value="monthly" {{ ($preferences->communication_frequency ?? 'immediately') === 'monthly' ? 'selected' : '' }}>Monthly Report</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Marketing Communications</h3>
                            <p class="text-sm text-gray-600">Receive marketing emails and promotional offers</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="marketing_communications" class="sr-only peer" {{ $preferences->marketing_communications ?? false ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Display Preferences -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Display Preferences</h2>
                
                <div class="space-y-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                        <select name="language" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="en" {{ ($preferences->language ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ ($preferences->language ?? 'en') === 'es' ? 'selected' : '' }}>Español</option>
                            <option value="fr" {{ ($preferences->language ?? 'en') === 'fr' ? 'selected' : '' }}>Français</option>
                            <option value="de" {{ ($preferences->language ?? 'en') === 'de' ? 'selected' : '' }}>Deutsch</option>
                            <option value="it" {{ ($preferences->language ?? 'en') === 'it' ? 'selected' : '' }}>Italiano</option>
                            <option value="pt" {{ ($preferences->language ?? 'en') === 'pt' ? 'selected' : '' }}>Português</option>
                            <option value="ar" {{ ($preferences->language ?? 'en') === 'ar' ? 'selected' : '' }}>العربية</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time Zone</label>
                        <select name="timezone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="UTC" {{ ($preferences->timezone ?? 'UTC') === 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ ($preferences->timezone ?? 'UTC') === 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                            <option value="America/Chicago" {{ ($preferences->timezone ?? 'UTC') === 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                            <option value="America/Denver" {{ ($preferences->timezone ?? 'UTC') === 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                            <option value="America/Los_Angeles" {{ ($preferences->timezone ?? 'UTC') === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                            <option value="Europe/London" {{ ($preferences->timezone ?? 'UTC') === 'Europe/London' ? 'selected' : '' }}>London</option>
                            <option value="Europe/Paris" {{ ($preferences->timezone ?? 'UTC') === 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                            <option value="Asia/Tokyo" {{ ($preferences->timezone ?? 'UTC') === 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select name="currency" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="USD" {{ ($preferences->currency ?? 'USD') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ ($preferences->currency ?? 'USD') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ ($preferences->currency ?? 'USD') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            <option value="CAD" {{ ($preferences->currency ?? 'USD') === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                            <option value="AUD" {{ ($preferences->currency ?? 'USD') === 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                            <option value="JPY" {{ ($preferences->currency ?? 'USD') === 'JPY' ? 'selected' : '' }}>JPY - Japanese Yen</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Dark Mode</h3>
                            <p class="text-sm text-gray-600">Use dark theme for the interface</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="dark_mode" class="sr-only peer" {{ $preferences->dark_mode ?? false ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Privacy Settings -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Privacy Settings</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Public Profile</h3>
                            <p class="text-sm text-gray-600">Make your profile visible to other users</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="public_profile" class="sr-only peer" {{ $preferences->public_profile ?? false ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Show Online Status</h3>
                            <p class="text-sm text-gray-600">Let others see when you're online</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="show_online_status" class="sr-only peer" {{ $preferences->show_online_status ?? true ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">Activity Tracking</h3>
                            <p class="text-sm text-gray-600">Allow us to track your activity for better recommendations</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="activity_tracking" class="sr-only peer" {{ $preferences->activity_tracking ?? true ? 'checked' : '' }}>
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
                    <button type="button" onclick="resetToDefaults()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Reset to Defaults
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Save Preferences
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function resetToDefaults() {
    if (confirm('Are you sure you want to reset all preferences to default values?')) {
        // Reset all checkboxes to default values
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = checkbox.name === 'email_notifications' || checkbox.name === 'push_notifications' || checkbox.name === 'property_matches' || checkbox.name === 'price_changes' || checkbox.name === 'show_online_status' || checkbox.name === 'activity_tracking';
        });
        
        // Reset selects to default values
        document.querySelector('select[name="preferred_contact"]').value = 'email';
        document.querySelector('select[name="communication_frequency"]').value = 'immediately';
        document.querySelector('select[name="language"]').value = 'en';
        document.querySelector('select[name="timezone"]').value = 'UTC';
        document.querySelector('select[name="currency"]').value = 'USD';
    }
}
</script>
@endsection
