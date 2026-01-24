@extends('layouts.auth')

@section('title', 'التحقق بخطوتين')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                التحقق بخطوتين
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                يرجى إدخال رمز التحقق المكون من 6 أرقان من تطبيق المصادقة
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="{{ route('two-factor.verify') }}" method="POST">
            @csrf
            
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
            
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="code" class="sr-only">رمز التحقق</label>
                    <input id="code" name="code" type="text" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 text-center text-lg tracking-widest focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="000000"
                           maxlength="6"
                           pattern="[0-9]{6}"
                           autocomplete="one-time-code"
                           autofocus>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-lock group-hover:text-blue-400 text-blue-500"></i>
                    </span>
                    تحقق
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    هل تواجه مشكلة؟ 
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                        تواصل مع الدعم
                    </a>
                </p>
            </div>
        </form>
        
        <div class="mt-6 border-t border-gray-200 pt-6">
            <div class="text-center">
                <h3 class="text-sm font-medium text-gray-900 mb-2">نصائح سريعة:</h3>
                <ul class="text-xs text-gray-600 space-y-1 text-right">
                    <li>• تأكد من فتح تطبيق المصادقة الصحيح</li>
                    <li>• تحقق من الوقت على هاتفك</li>
                    <li>• إذا لم يعمل الرمز، انتظر الرمز التالي</li>
                    <li>• يمكنك استخدام رموز النسخ الاحتياطي</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('code');
    
    // Auto-focus and select
    codeInput.focus();
    codeInput.select();
    
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
</script>
@endsection
