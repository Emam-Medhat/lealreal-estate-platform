@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                {{ __('إعداد المصادقة البيومترية') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                {{ __('أضف طبقة أمان إضافية لحسابك باستخدام البصمة أو التعرف على الوجه') }}
            </p>
        </div>
        
        @if (session('error'))
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="mr-3">
                        <p class="text-sm text-red-800">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="mr-3">
                        <p class="text-sm text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="space-y-6">
            <!-- Biometric Type Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    {{ __('اختر نوع المصادقة البيومترية') }}
                </label>
                <div class="space-y-2">
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="biometric_type" value="fingerprint" required
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <div class="mr-3">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ __('بصمة الإصبع') }}</div>
                            <div class="text-sm text-gray-500">{{ __('استخدم بصمة إصبعك لتسجيل الدخول السريع') }}</div>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="biometric_type" value="face" required
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <div class="mr-3">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ __('التعرف على الوجه') }}</div>
                            <div class="text-sm text-gray-500">{{ __('استخدم كاميرا الجهاز للتعرف على وجهك') }}</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Device Name -->
            <div>
                <label for="device_name" class="block text-sm font-medium text-gray-700">
                    {{ __('اسم الجهاز') }}
                </label>
                <input id="device_name" name="device_name" type="text" required
                       class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       placeholder="{{ __('مثال: جهازي الشخصي أو العمل') }}" value="{{ old('device_name') }}">
                @error('device_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Setup Button -->
            <button type="button" onclick="setupBiometric()" id="setup-btn"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('إعداد المصادقة البيومترية') }}
            </button>

            <!-- Hidden Form -->
            <form id="biometric-form" action="{{ route('biometric.setup') }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="biometric_type" id="hidden_biometric_type">
                <input type="hidden" name="device_name" id="hidden_device_name">
                <input type="hidden" name="biometric_data" id="biometric_data">
            </form>
        </div>

        <div class="text-center">
            <a href="{{ route('dashboard') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                {{ __('تخطي للآن') }}
            </a>
        </div>
    </div>
</div>

<script>
function setupBiometric() {
    const biometricType = document.querySelector('input[name="biometric_type"]:checked');
    const deviceName = document.getElementById('device_name').value;
    
    if (!biometricType) {
        alert('{{ __('الرجاء اختيار نوع المصادقة البيومترية') }}');
        return;
    }
    
    if (!deviceName) {
        alert('{{ __('الرجاء إدخال اسم الجهاز') }}');
        return;
    }
    
    if (!window.PublicKeyCredential) {
        alert('{{ __('المتصفح لا يدعم المصادقة البيومترية') }}');
        return;
    }
    
    const setupBtn = document.getElementById('setup-btn');
    setupBtn.disabled = true;
    setupBtn.innerHTML = '{{ __('جاري الإعداد...') }}';
    
    // Create biometric credential
    const createCredentialOptions = {
        publicKey: {
            challenge: new Uint8Array(32),
            rp: {
                name: "{{ config('app.name') }}",
                id: window.location.hostname
            },
            user: {
                id: new Uint8Array(16),
                name: "{{ Auth::user()->email }}",
                displayName: "{{ Auth::user()->name }}"
            },
            pubKeyCredParams: [
                { alg: -7, type: "public-key" },
                { alg: -257, type: "public-key" }
            ],
            authenticatorSelection: {
                authenticatorAttachment: biometricType.value === 'fingerprint' ? 'platform' : 'cross-platform',
                userVerification: biometricType.value === 'face' ? 'required' : 'preferred'
            },
            timeout: 60000
        }
    };
    
    navigator.credentials.create(createCredentialOptions)
        .then(credential => {
            // Store the credential data
            document.getElementById('hidden_biometric_type').value = biometricType.value;
            document.getElementById('hidden_device_name').value = deviceName;
            document.getElementById('biometric_data').value = btoa(JSON.stringify({
                id: credential.id,
                rawId: Array.from(new Uint8Array(credential.rawId)),
                response: {
                    clientDataJSON: Array.from(new Uint8Array(credential.response.clientDataJSON)),
                    attestationObject: Array.from(new Uint8Array(credential.response.attestationObject))
                }
            }));
            
            // Submit the form
            document.getElementById('biometric-form').submit();
        })
        .catch(error => {
            console.error('Biometric setup error:', error);
            setupBtn.disabled = false;
            setupBtn.innerHTML = '{{ __('إعداد المصادقة البيومترية') }}';
            alert('{{ __('فشل في إعداد المصادقة البيومترية') }}: ' + error.message);
        });
}
</script>
@endsection
