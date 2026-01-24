@extends('layouts.app')

@section('title', 'Biometric Devices')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Biometric Devices</h1>
                    <p class="text-gray-600">Manage your biometric authentication devices</p>
                </div>
                <button onclick="showAddDeviceModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Add Device
                </button>
            </div>
        </div>

        <!-- Registered Devices -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Registered Devices</h2>
            
            <div class="space-y-4">
                @forelse ($devices as $device)
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-3 mr-4">
                                <i class="fas fa-fingerprint text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-800">{{ $device->device_name }}</h3>
                                <p class="text-sm text-gray-600">{{ $device->device_type }}</p>
                                <div class="flex items-center space-x-4 mt-1">
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-calendar mr-1"></i>
                                        Registered {{ $device->created_at->diffForHumans() }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        Last used {{ $device->last_used_at ? $device->last_used_at->diffForHumans() : 'Never' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($device->is_active)
                                    bg-green-100 text-green-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ $device->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            
                            @if($device->is_active)
                                <button onclick="toggleDevice({{ $device->id }}, false)" class="text-yellow-600 hover:text-yellow-800" title="Disable">
                                    <i class="fas fa-pause"></i>
                                </button>
                            @else
                                <button onclick="toggleDevice({{ $device->id }}, true)" class="text-green-600 hover:text-green-800" title="Enable">
                                    <i class="fas fa-play"></i>
                                </button>
                            @endif
                            
                            <button onclick="deleteDevice({{ $device->id }})" class="text-red-600 hover:text-red-800" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-fingerprint text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No biometric devices registered</h3>
                        <p class="text-gray-500">Add a biometric device for secure authentication.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Security Settings -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Security Settings</h2>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h3 class="font-medium text-gray-800">Require Biometric for Sensitive Actions</h3>
                        <p class="text-sm text-gray-600">Require biometric verification for password changes, email updates, etc.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" {{ auth()->user()->biometric_required ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h3 class="font-medium text-gray-800">Auto-logout on Device Removal</h3>
                        <p class="text-sm text-gray-600">Automatically logout from all sessions when a device is removed</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" {{ auth()->user()->auto_logout_on_removal ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Device Information -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">About Biometric Authentication</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Biometric authentication provides:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Enhanced security with unique biological identifiers</li>
                            <li>Quick and convenient access without passwords</li>
                            <li>Protection against unauthorized access</li>
                        </ul>
                        <p class="mt-2">Supported devices include fingerprint scanners, facial recognition, and voice authentication.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Device Modal -->
<div id="addDeviceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Add Biometric Device</h3>
        
        <div class="space-y-4">
            <div class="text-center py-6">
                <div class="bg-blue-100 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-fingerprint text-blue-600 text-2xl"></i>
                </div>
                <h4 class="font-medium text-gray-800 mb-2">Register New Device</h4>
                <p class="text-sm text-gray-600 mb-4">Follow the steps below to register your biometric device:</p>
                
                <div class="text-left space-y-3">
                    <div class="flex items-start">
                        <div class="bg-blue-100 rounded-full p-1 mr-3 mt-1">
                            <span class="text-blue-600 text-xs font-bold">1</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-800">Place your finger on the sensor</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="bg-blue-100 rounded-full p-1 mr-3 mt-1">
                            <span class="text-blue-600 text-xs font-bold">2</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-800">Lift and repeat several times</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="bg-blue-100 rounded-full p-1 mr-3 mt-1">
                            <span class="text-blue-600 text-xs font-bold">3</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-800">Wait for registration to complete</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Device Name</label>
                <input type="text" id="deviceName" placeholder="e.g., Office Fingerprint" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Device Type</label>
                <select id="deviceType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="fingerprint">Fingerprint Scanner</option>
                    <option value="facial">Facial Recognition</option>
                    <option value="voice">Voice Authentication</option>
                    <option value="iris">Iris Scanner</option>
                </select>
            </div>
        </div>
        
        <div class="flex justify-end space-x-3">
            <button onclick="closeAddDeviceModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
            </button>
            <button onclick="registerDevice()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Register Device
            </button>
        </div>
    </div>
</div>

<script>
function showAddDeviceModal() {
    document.getElementById('addDeviceModal').classList.remove('hidden');
}

function closeAddDeviceModal() {
    document.getElementById('addDeviceModal').classList.add('hidden');
    document.getElementById('deviceName').value = '';
    document.getElementById('deviceType').value = 'fingerprint';
}

function registerDevice() {
    const deviceName = document.getElementById('deviceName').value;
    const deviceType = document.getElementById('deviceType').value;
    
    if (!deviceName) {
        alert('Please enter a device name');
        return;
    }
    
    // Simulate biometric registration
    const formData = new FormData();
    formData.append('device_name', deviceName);
    formData.append('device_type', deviceType);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    fetch('/biometric/register', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error registering device');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error registering device');
    });
}

function toggleDevice(deviceId, status) {
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('status', status);
    
    fetch('/biometric/devices/' + deviceId + '/toggle', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error updating device');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function deleteDevice(deviceId) {
    if (!confirm('Are you sure you want to delete this device? This action cannot be undone.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('_method', 'DELETE');
    
    fetch('/biometric/devices/' + deviceId, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error deleting device');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endsection
