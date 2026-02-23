@extends('admin.layouts.admin')

@section('title', 'تفاصيل رعاية العميل')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">سجل رعاية العميل</h1>
            <p class="text-gray-600 mt-1">عرض أنشطة الرعاية للعميل {{ $lead->first_name }} {{ $lead->last_name }}</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('lead-nurturing.create', ['lead_id' => $lead->id]) }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus ml-2"></i>
                نشاط جديد
            </a>
            <a href="{{ route('lead-nurturing.index') }}" class="text-gray-600 hover:text-gray-900 font-medium ml-4">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Lead Info -->
    <div class="col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">معلومات العميل</h2>
            <div class="space-y-4">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="bg-purple-100 rounded-full p-3">
                        <i class="fas fa-user text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $lead->first_name }} {{ $lead->last_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $lead->email }}</p>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">الهاتف:</span>
                        <span class="text-gray-900">{{ $lead->phone ?? 'غير متوفر' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">المصدر:</span>
                        <span class="text-gray-900">{{ $lead->source->name ?? 'غير محدد' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">الحالة:</span>
                        <span class="px-2 py-0.5 rounded-full bg-gray-100 text-xs">{{ $lead->status->name ?? 'غير محدد' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">آخر تواصل:</span>
                        <span class="text-gray-900">{{ $lead->last_contact_at ? $lead->last_contact_at->format('Y-m-d H:i') : 'أبداً' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-2">سجل الأنشطة</h2>
            
            <div class="relative border-r border-gray-200 mr-3">
                @forelse($lead->activities as $activity)
                    <div class="mb-8 mr-6 relative">
                        <div class="absolute -right-9 top-1 bg-white border-2 border-purple-500 rounded-full w-4 h-4"></div>
                        <div class="flex justify-between items-start mb-1">
                            <h3 class="font-bold text-gray-900 text-sm">{{ $activity->type }}</h3>
                            <span class="text-xs text-gray-500">{{ $activity->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <p class="text-gray-600 text-sm bg-gray-50 p-3 rounded-lg border border-gray-100">
                            {{ $activity->description }}
                        </p>
                        <div class="text-xs text-gray-400 mt-1">
                            بواسطة: {{ $activity->user->name ?? 'System' }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <p>لا توجد أنشطة مسجلة لهذا العميل</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
