@extends('layouts.auth')

@section('title', 'إعداد المصادقة بخطوتين')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                إعداد المصادقة بخطوتين
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                قم بتأمين حسابك بإضافة طبقة إضافية من الأمان
            </p>
        </div>
        
        <div class="bg-white shadow-lg rounded-lg p-6">
            <!-- QR Code Section -->
            <div class="text-center mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">امسح رمز QR</h3>
                <div class="flex justify-center mb-4">
                    <div class="bg-white p-4 rounded-lg border-2 border-gray-200">
                        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="w-48 h-48">
                    </div>
                </div>
                <p class="text-sm text-gray-600">
                    استخدم تطبيق المصادقة مثل Google Authenticator أو Authy
                </p>
            </div>
            
            <!-- Manual Entry -->
            <div class="border-t pt-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">أو أدخل الرمز يدوياً</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl font-mono tracking-widest">{{ $secret }}</span>
                        <button onclick="copySecret()" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Verification Form -->
            <form action="{{ route('two-factor.enable') }}" method="POST" class="space-y-6">
                @csrf
                
                @if (session('status'))
                    <div class="rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="mr-3">
                                <h3 class="text-sm font-medium text-green-800">
                                    نجح!
                                </h3>
                                <div class="mt-2 text-sm text-green-700">
                                    {{ session('status') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="mr-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    حدث خطأ
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        أدخل رمز التحقق المكون من 6 أرقان
                    </label>
                    <input id="code" name="code" type="text" required 
                           class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 text-center text-lg tracking-widest focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="000000"
                           maxlength="6"
                           pattern="[0-9]{6}"
                           autocomplete="one-time-code"
                           autofocus>
                </div>

                <div class="flex space-x-4 space-x-reverse">
                    <button type="submit" 
                            class="flex-1 group relative flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <span class="absolute right-0 inset-y-0 flex items-center pr-3">
                            <i class="fas fa-check group-hover:text-green-400 text-green-500"></i>
                        </span>
                        تفعيل المصادقة بخطوتين
                    </button>
                    
                    <a href="{{ route('dashboard') }}" 
                       class="flex-1 group relative flex justify-center py-2 px-4 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <span class="absolute right-0 inset-y-0 flex items-center pr-3">
                            <i class="fas fa-times group-hover:text-gray-400 text-gray-500"></i>
                        </span>
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Instructions -->
        <div class="bg-blue-50 rounded-lg p-6">
            <h3 class="text-lg font-medium text-blue-900 mb-4">
                <i class="fas fa-info-circle ml-2"></i>
                كيفية الإعداد
            </h3>
            <ol class="text-sm text-blue-800 space-y-2 text-right">
                <li class="flex items-start">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold ml-3 flex-shrink-0">1</span>
                    قم بتنزيل تطبيق المصادقة (Google Authenticator, Authy, أو Microsoft Authenticator)
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold ml-3 flex-shrink-0">2</span>
                    امسح رمز QR أو أدخل الرمز يدوياً في التطبيق
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold ml-3 flex-shrink-0">3</span>
                    أدخل الرمز المكون من 6 أرقان من التطبيق هنا
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold ml-3 flex-shrink-0">4</span>
                    احتفظ بالرموز الاحتياطية في مكان آمن
                </li>
            </ol>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('code');
    
    // Auto-focus and select
    codeInput.focus();
    
    // Only allow numbers
    codeInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Auto-submit when 6 digits entered
        if (this.value.length === 6) {
            this.form.submit();
        }
    });
    
    // Handle paste
    codeInput.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedData = (e.clipboardData || window.clipboardData).getData('text');
        const numbers = pastedData.replace(/[^0-9]/g, '').slice(0, 6);
        this.value = numbers;
        
        if (numbers.length === 6) {
            this.form.submit();
        }
    });
});

function copySecret() {
    const secret = '{{ $secret }}';
    navigator.clipboard.writeText(secret).then(function() {
        // Show feedback
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check text-green-600"></i>';
        setTimeout(() => {
            button.innerHTML = originalHTML;
        }, 2000);
    });
}
</script>
@endsection
