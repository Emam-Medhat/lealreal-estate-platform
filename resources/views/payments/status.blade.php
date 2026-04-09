@extends('layouts.app')

@section('content')
    <div class="status-container py-24 text-center">
        <div class="max-w-md mx-auto px-4">
            @if($status === 'success')
                <div
                    class="success-icon w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">
                    <i class="fas fa-check"></i>
                </div>
                <h1 class="text-3xl font-bold mb-4">تمت عملية الدفع بنجاح!</h1>
                <p class="text-gray-600 mb-8">لقد تم تأكيد طلبك بنجاح. ستصلك رسالة بريد إلكتروني تحتوي على تفاصيل الفاتورة
                    والمستندات في غضون دقائق.</p>
                <div class="flex flex-col gap-4">
                    <a href="{{ route('dashboard') }}" class="bg-black text-white py-3 px-8 rounded-xl font-bold">انتقل إلى لوحة
                        التحكم</a>
                    <a href="{{ route('properties.index') }}" class="text-primary font-bold">تصفح عقارات أخرى</a>
                </div>
            @else
                <div
                    class="error-icon w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">
                    <i class="fas fa-times"></i>
                </div>
                <h1 class="text-3xl font-bold mb-4">عذراً، فشلت عملية الدفع</h1>
                <p class="text-gray-600 mb-8">لم نتمكن من إتمام عملية الدفع. يرجى التأكد من بيانات البطاقة أو الرصيد المتاح
                    والمحاولة مرة أخرى.</p>
                <div class="flex flex-col gap-4">
                    <a href="javascript:history.back()" class="bg-black text-white py-3 px-8 rounded-xl font-bold">المحاولة مرة
                        أخرى</a>
                    <a href="https://wa.me/201000000000" class="border border-gray-300 py-3 px-8 rounded-xl font-bold">تواصل مع
                        الدعم الفني</a>
                </div>
            @endif
        </div>
    </div>
@endsection