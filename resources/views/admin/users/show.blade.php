@extends('layouts.dashboard')

@section('title', 'عرض المستخدم: ' . ($user->first_name ?? 'تفاصيل المستخدم'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg shadow-lg p-6 mb-6 text-white">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div
                            class="h-20 w-20 rounded-full bg-white bg-opacity-20 flex items-center justify-center text-3xl font-bold mr-6">
                            @if($user->profile_photo_path)
                                <img src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}"
                                    class="h-20 w-20 rounded-full object-cover">
                            @else
                                {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold mb-1">
                                {{ ($user->first_name ?? '') . ' ' . ($user->last_name ?? '') }}</h1>
                            <p class="text-blue-100">{{ $user->email }} - <span
                                    class="capitalize">{{ $user->user_type ?? 'User' }}</span></p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.users.edit', $user) }}"
                            class="bg-white text-blue-600 px-6 py-2 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                            <i class="fas fa-edit ml-1"></i> تعديل
                        </a>
                        <a href="{{ route('admin.users.index') }}"
                            class="bg-blue-800 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-900 transition-colors">
                            <i class="fas fa-arrow-right ml-1"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Basic Information -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-bold text-gray-800">بيانات الحساب</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="text-xs text-gray-500 block">الاسم بالكامل</label>
                                    <p class="text-gray-900 font-medium">
                                        {{ ($user->first_name ?? '') . ' ' . ($user->last_name ?? '') }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 block">البريد الإلكتروني</label>
                                    <p class="text-gray-900 font-medium">{{ $user->email }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 block">الهاتف</label>
                                    <p class="text-gray-900 font-medium">{{ $user->phone ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 block">نوع المستخدم</label>
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $user->user_type === 'admin' ? 'bg-purple-100 text-purple-800' :
        ($user->user_type === 'agent' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst($user->user_type ?? 'user') }}
                                    </span>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 block">الحالة</label>
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($user->status ?? 'active') }}
                                    </span>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 block">تاريخ الانضمام</label>
                                    <p class="text-gray-900 font-medium">{{ $user->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 block">آخر نشاط</label>
                                    <p class="text-gray-900 font-medium">
                                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'لا يوجد سجل' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Stats and Activity -->
                <div class="lg:col-span-2">
                    <!-- Activity Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-white p-6 rounded-lg shadow-sm border-r-4 border-blue-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">إجمالي العقارات</p>
                                    <h4 class="text-2xl font-bold">{{ $user->properties_count ?? 0 }}</h4>
                                </div>
                                <div class="bg-blue-100 p-3 rounded-full text-blue-600">
                                    <i class="fas fa-home"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-sm border-r-4 border-green-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">العملاء النشطون</p>
                                    <h4 class="text-2xl font-bold">{{ $user->leads_count ?? 0 }}</h4>
                                </div>
                                <div class="bg-green-100 p-3 rounded-full text-green-600">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-sm border-r-4 border-amber-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">التواصلات</p>
                                    <h4 class="text-2xl font-bold">{{ $user->activities_count ?? 0 }}</h4>
                                </div>
                                <div class="bg-amber-100 p-3 rounded-full text-amber-600">
                                    <i class="fas fa-comments"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                            <h3 class="text-lg font-bold text-gray-800">الأنشطة الأخيرة</h3>
                        </div>
                        <div class="p-0">
                            <div class="divide-y divide-gray-100">
                                @forelse($user->activities ?? [] as $activity)
                                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="bg-blue-50 text-blue-600 p-2 rounded-full ml-4">
                                                    <i class="fas fa-info-circle"></i>
                                                </div>
                                                <div>
                                                    <p class="text-gray-900 font-medium">
                                                        {{ $activity->action ?? 'نشاط غير معروف' }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $activity->created_at->format('Y-m-d H:i') }}</p>
                                                </div>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $activity->description ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-6 py-10 text-center text-gray-500">
                                        <i class="fas fa-history text-4xl mb-3 opacity-20"></i>
                                        <p>لا توجد أنشطة مسجلة لهذا المستخدم</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        @if(isset($user->activities) && count($user->activities) > 0)
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-center">
                                <a href="#" class="text-blue-600 font-medium text-sm hover:underline">عرض كل الأنشطة</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection