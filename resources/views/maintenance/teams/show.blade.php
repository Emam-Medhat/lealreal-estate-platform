@extends('admin.layouts.admin')

@section('title', 'تفاصيل فريق الصيانة')

@section('content')
@if($team)
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تفاصيل الفريق</h1>
            <p class="text-gray-600 mt-1">معلومات فريق الصيانة وأعضائه</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('maintenance.teams.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-arrow-right"></i>
                <span>عودة</span>
            </a>
            <a href="{{ route('maintenance.teams.edit', $team) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-edit"></i>
                <span>تعديل</span>
            </a>
        </div>
    </div>
</div>

<!-- Team Information -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">معلومات الفريق</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="flex items-center space-x-4 space-x-reverse mb-6">
                    <div class="bg-blue-100 rounded-full p-4">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $team->name }}</h3>
                        <p class="text-gray-500">{{ $team->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-info-circle text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الوصف</p>
                            <p class="text-gray-900">{{ $team->description ?: 'غير محدد' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-tools text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">التخصص</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $team->specialization_label }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-flag text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الحالة</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($team->is_active)
                                    bg-green-100 text-green-800
                                @else
                                    bg-red-100 text-red-800
                                @endif">
                                {{ $team->status_label }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-user-tie text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">قائد الفريق</p>
                            <p class="text-gray-900">{{ $team->leader_name }}</p>
                            @if($team->leader_email)
                                <p class="text-sm text-gray-500">{{ $team->leader_email }}</p>
                            @endif
                            @if($team->leader_phone)
                                <p class="text-sm text-gray-500">{{ $team->leader_phone }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-tasks text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الحد الأقصى للوظائف</p>
                            <p class="text-gray-900">{{ $team->max_concurrent_jobs }} وظيفة</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-clock text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">ساعات العمل</p>
                            @if($team->working_hours)
                                <p class="text-gray-900">
                                    {{ json_decode($team->working_hours)->start }} - 
                                    {{ json_decode($team->working_hours)->end }}
                                </p>
                            @else
                                <p class="text-gray-900">غير محدد</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-users text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">عدد الأعضاء</p>
                            <p class="text-gray-900">{{ $team->members ? $team->members->count() : 0 }} عضو</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($team->notes)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-sticky-note text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">ملاحظات</p>
                        <p class="text-gray-900">{{ $team->notes }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Team Members -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">أعضاء الفريق</h2>
        <form method="POST" action="{{ route('maintenance.teams.add-member', $team) }}" class="flex items-center space-x-2 space-x-reverse">
            @csrf
            <select name="user_id" required class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">اختر عضو</option>
                @foreach(\App\Models\User::where('email_verified_at', '!=', null)->get() as $user)
                    @if(!$team->members || !$team->members->contains($user->id))
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endif
                @endforeach
            </select>
            <select name="role" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="member">عضو</option>
                <option value="supervisor">مشرف</option>
            </select>
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                <i class="fas fa-plus ml-1"></i>
                إضافة
            </button>
        </form>
    </div>
    <div class="p-6">
        @if($team->members && $team->members->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الدور</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الانضمام</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($team->members as $member)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $member->name }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900">{{ $member->email }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($member->pivot->role == 'supervisor')
                                            bg-purple-100 text-purple-800
                                        @else
                                            bg-blue-100 text-blue-800
                                        @endif">
                                        @if($member->pivot->role == 'supervisor')
                                            مشرف
                                        @else
                                            عضو
                                        @endif
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900">{{ $member->pivot->joined_at->format('Y-m-d') }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <form method="POST" action="{{ route('maintenance.teams.remove-member', [$team, $member]) }}" 
                                          class="inline"
                                          onsubmit="return confirm('هل أنت متأكد من إزالة هذا العضو؟');">
                                        @csrf
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 transition-colors duration-150"
                                                title="إزالة">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <div class="bg-gray-100 rounded-full p-4 mb-4 inline-block">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد أعضاء</h3>
                <p class="text-gray-500 mb-4">لم يتم إضافة أي أعضاء لهذا الفريق بعد</p>
                <p class="text-sm text-gray-400">استخدم النموذج أعلاه لإضافة أعضاء جدد</p>
            </div>
        @endif
    </div>
</div>

<!-- Recent Work Orders -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">أوامر العمل الأخيرة</h2>
    </div>
    <div class="p-6">
        @if($team->workOrders && $team->workOrders->count() > 0)
            <div class="space-y-4">
                @foreach($team->workOrders as $workOrder)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $workOrder->title }}</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($workOrder->status == 'pending')
                                            bg-yellow-100 text-yellow-800
                                        @elseif($workOrder->status == 'in_progress')
                                            bg-indigo-100 text-indigo-800
                                        @elseif($workOrder->status == 'completed')
                                            bg-green-100 text-green-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $workOrder->status_label }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($workOrder->priority == 'high')
                                            bg-red-100 text-red-800
                                        @elseif($workOrder->priority == 'medium')
                                            bg-blue-100 text-blue-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $workOrder->priority_label }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">{{ $workOrder->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <a href="{{ route('maintenance.workorders.show', $workOrder) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors duration-150"
                                   title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('maintenance.teams.workload', $team) }}" 
                   class="text-blue-600 hover:text-blue-800 font-medium">
                    عرض جميع أوامر العمل →
                </a>
            </div>
        @else
            <div class="text-center py-8">
                <div class="bg-gray-100 rounded-full p-4 mb-4 inline-block">
                    <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد أوامر عمل</h3>
                <p class="text-gray-500 mb-4">لم يتم تكليف هذا الفريق بأي أوامر عمل بعد</p>
            </div>
        @endif
    </div>
</div>

<!-- Actions -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">الإجراءات</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('maintenance.teams.edit', $team) }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-edit ml-2"></i>
            تعديل الفريق
        </a>
        
        <a href="{{ route('maintenance.teams.workload', $team) }}" 
           class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-chart-bar ml-2"></i>
            عبء العمل
        </a>
        
        <form method="POST" action="{{ route('maintenance.teams.toggle-status', $team) }}" 
              class="inline"
              onsubmit="return confirm('هل أنت متأكد من {{ $team->is_active ? 'إلغاء تفعيل' : 'تفعيل' }} الفريق؟');">
            @csrf
            <button type="submit" 
                    class="bg-{{ $team->is_active ? 'yellow' : 'green' }}-500 hover:bg-{{ $team->is_active ? 'yellow' : 'green' }}-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-power-off ml-2"></i>
                {{ $team->is_active ? 'إلغاء تفعيل' : 'تفعيل' }}
            </button>
        </form>
        
        <form method="POST" action="{{ route('maintenance.teams.destroy', $team) }}" 
              class="inline"
              onsubmit="return confirm('هل أنت متأكد من حذف هذا الفريق؟ هذا الإجراء لا يمكن التراجع عنه.');">
            @csrf
            <button type="submit" 
                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-trash ml-2"></i>
                حذف الفريق
            </button>
        </form>
        
        <a href="{{ route('maintenance.teams.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للقائمة
        </a>
    </div>
</div>

@else
<!-- Team Not Found -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
    <div class="flex flex-col items-center">
        <div class="bg-gray-100 rounded-full p-4 mb-4">
            <i class="fas fa-users text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">الفريق غير موجود</h3>
        <p class="text-gray-500 mb-4">لم يتم العثور على الفريق المطلوب</p>
        <a href="{{ route('maintenance.teams.index') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للفرق
        </a>
    </div>
</div>
@endif
@endsection
