@extends('admin.layouts.admin')

@section('title', 'قاعدة أتمتة جديدة')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">إنشاء قاعدة أتمتة جديدة</h1>
            <p class="text-gray-600 mt-1">تعريف الشروط والإجراءات التلقائية</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('lead-nurturing.automation') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                <i class="fas fa-arrow-right ml-2"></i>
                إلغاء والعودة
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl mx-auto">
    <form action="{{ route('lead-nurturing.automation.store') }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم القاعدة</label>
                <input type="text" name="name" id="name" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" required placeholder="مثال: رسالة ترحيبية">
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" id="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" placeholder="وصف لما تفعله هذه القاعدة..."></textarea>
            </div>

            <!-- Trigger (Simplified) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحدث المحفز</label>
                <select name="triggers[0][type]" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    <option value="status_change">تغيير الحالة</option>
                    <option value="new_lead">عميل جديد</option>
                    <option value="lead_score">تغيير التقييم</option>
                </select>
                <input type="hidden" name="triggers[0][conditions][]" value="">
            </div>

            <!-- Action (Simplified) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الإجراء</label>
                <select name="actions[0][type]" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    <option value="send_email">إرسال بريد إلكتروني</option>
                    <option value="create_task">إنشاء مهمة</option>
                    <option value="assign_lead">تعيين العميل</option>
                </select>
                <input type="hidden" name="actions[0][parameters][]" value="">
            </div>

            <!-- Active Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                <label for="is_active" class="mr-2 block text-sm text-gray-900">تفعيل هذه القاعدة فوراً</label>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end space-x-3 space-x-reverse">
            <button type="button" onclick="window.history.back()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                إلغاء
            </button>
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium transition-colors shadow-sm">
                حفظ القاعدة
            </button>
        </div>
    </form>
</div>
@endsection
