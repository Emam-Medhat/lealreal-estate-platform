@extends('admin.layouts.admin')

@section('title', 'نشاط رعاية جديد')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">نشاط رعاية جديد</h1>
            <p class="text-gray-600 mt-1">جدولة أو تنفيذ نشاط رعاية لعميل محتمل</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('lead-nurturing.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl mx-auto">
    <form action="{{ route('lead-nurturing.store') }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            <!-- Lead Selection -->
            <div>
                <label for="lead_id" class="block text-sm font-medium text-gray-700 mb-2">العميل المحتمل</label>
                <select name="lead_id" id="lead_id" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" required>
                    <option value="">اختر العميل...</option>
                    @foreach(\App\Models\Lead::orderBy('first_name')->take(50)->get() as $lead)
                        <option value="{{ $lead->id }}">{{ $lead->first_name }} {{ $lead->last_name }}</option>
                    @endforeach
                </select>
                @error('lead_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div>
                <label for="nurturing_type" class="block text-sm font-medium text-gray-700 mb-2">نوع النشاط</label>
                <select name="nurturing_type" id="nurturing_type" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" required>
                    <option value="email">بريد إلكتروني</option>
                    <option value="call">اتصال هاتفي</option>
                    <option value="sms">رسالة نصية</option>
                    <option value="meeting">اجتماع</option>
                </select>
            </div>

            <!-- Subject -->
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">الموضوع</label>
                <input type="text" name="subject" id="subject" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" required>
            </div>

            <!-- Message -->
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">الرسالة / التفاصيل</label>
                <textarea name="message" id="message" rows="4" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" required></textarea>
            </div>

            <!-- Schedule -->
            <div>
                <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-2">تاريخ الجدولة (اختياري)</label>
                <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                <p class="text-xs text-gray-500 mt-1">اتركه فارغاً للتنفيذ فوراً</p>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end space-x-3 space-x-reverse">
            <button type="button" onclick="window.history.back()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                إلغاء
            </button>
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium transition-colors shadow-sm">
                حفظ النشاط
            </button>
        </div>
    </form>
</div>
@endsection
