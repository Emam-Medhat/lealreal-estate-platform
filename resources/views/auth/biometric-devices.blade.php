@extends('layouts.app')

@section('title', __('auth.biometric_devices'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ __('auth.biometric_devices') }}</h1>
                        <p class="text-gray-600">{{ __('auth.manage_biometric') }}</p>
                    </div>
                    <button onclick="showAddDeviceModal()"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        {{ __('auth.add_device') }}
                    </button>
                </div>
            </div>

            <!-- Registered Devices -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('auth.registered_devices') }}</h2>

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
                                            {{ __('common.registered') }} {{ $device->created_at->diffForHumans() }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ __('common.last_used') }}
                                            {{ $device->last_used_at ? $device->last_used_at->diffForHumans() : __('common.never') }}
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
                                    {{ $device->is_active ? __('auth.active') : __('auth.inactive') }}
                                </span>

                                @if($device->is_active)
                                    <button onclick="toggleDevice({{ $device->id }}, false)"
                                        class="text-yellow-600 hover:text-yellow-800" title="Disable">
                                        <i class="fas fa-pause"></i>
                                    </button>
                                @else
                                    <button onclick="toggleDevice({{ $device->id }}, true)"
                                        class="text-green-600 hover:text-green-800" title="Enable">
                                        <i class="fas fa-play"></i>
                                    </button>
                                @endif

                                <button onclick="deleteDevice({{ $device->id }})" class="text-red-600 hover:text-red-800"
                                    title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-fingerprint text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('auth.no_devices') }}</h3>
                            <p class="text-gray-500">{{ __('auth.add_device_desc') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Security Settings -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('auth.security_settings') }}</h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">{{ __('auth.require_biometric_sensitive') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('auth.require_biometric_desc') }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" {{ auth()->user()->biometric_required ? 'checked' : '' }}>
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                            </div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-800">{{ __('auth.auto_logout_removal') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('auth.auto_logout_removal_desc') }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" {{ auth()->user()->auto_logout_on_removal ? 'checked' : '' }}>
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                            </div>
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
                        <h3 class="text-sm font-medium text-blue-800">{{ __('auth.about_biometric') }}</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>{{ __('auth.about_biometric_desc') }}</p>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>{{ __('auth.biological_identifiers') }}</li>
                                <li>{{ __('auth.quick_access') }}</li>
                                <li>{{ __('auth.protection_unauthorized') }}</li>
                            </ul>
                            <p class="mt-2">{{ __('auth.supported_devices_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Device Modal -->
    <div id="addDeviceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('auth.add_biometric_device') }}</h3>

            <div class="space-y-4">
                <div class="text-center py-6">
                    <div class="bg-blue-100 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-fingerprint text-blue-600 text-2xl"></i>
                    </div>
                    <h4 class="font-medium text-gray-800 mb-2">{{ __('auth.register_new_device') }}</h4>
                    <p class="text-sm text-gray-600 mb-4">{{ __('auth.register_steps_desc') }}</p>

                    <div class="text-left space-y-3">
                        <div class="flex items-start">
                            <div class="bg-blue-100 rounded-full p-1 mr-3 mt-1">
                                <span class="text-blue-600 text-xs font-bold">1</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-800">{{ __('auth.step_sensor') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-blue-100 rounded-full p-1 mr-3 mt-1">
                                <span class="text-blue-600 text-xs font-bold">2</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-800">{{ __('auth.step_repeat') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-blue-100 rounded-full p-1 mr-3 mt-1">
                                <span class="text-blue-600 text-xs font-bold">3</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-800">{{ __('auth.step_wait') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('auth.device_name') }}</label>
                    <input type="text" id="deviceName" placeholder="{{ __('auth.device_name_placeholder') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('auth.device_type') }}</label>
                    <select id="deviceType"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="fingerprint">{{ __('auth.fingerprint_scanner') }}</option>
                        <option value="facial">{{ __('auth.facial_recognition') }}</option>
                        <option value="voice">{{ __('auth.voice_authentication') }}</option>
                        <option value="iris">{{ __('auth.iris_scanner') }}</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button onclick="closeAddDeviceModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    {{ __('common.cancel') }}
                </button>
                <button onclick="registerDevice()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    {{ __('auth.register_device') }}
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