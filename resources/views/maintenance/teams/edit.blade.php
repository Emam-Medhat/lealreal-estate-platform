@extends('admin.layouts.admin')

@section('title', 'تعديل فريق الصيانة')

@section('content')
@if($team)
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تعديل فريق الصيانة</h1>
            <p class="text-gray-600 mt-1">تعديل بيانات فريق الصيانة</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('maintenance.teams.show', $team) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-arrow-right"></i>
                <span>عودة</span>
            </a>
        </div>
    </div>
</div>

<!-- Edit Form -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">معلومات الفريق</h2>
    </div>
    
    <form method="POST" action="{{ route('maintenance.teams.update', $team) }}" class="p-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div class="space-y-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم الفريق *</label>
                    <input type="text" id="name" name="name" required
                           value="{{ old('name', $team->name) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="أدخل اسم الفريق">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="team_code" class="block text-sm font-medium text-gray-700 mb-2">كود الفريق</label>
                    <input type="text" id="team_code" name="team_code"
                           value="{{ old('team_code', $team->team_code) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="مثال: MT-001">
                    @error('team_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">وصف الفريق</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="أدخل وصفاً للفريق ومهامه">{{ old('description', $team->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Leadership -->
        <div class="space-y-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">القيادة</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="team_leader_id" class="block text-sm font-medium text-gray-700 mb-2">قائد الفريق</label>
                    <select id="team_leader_id" name="team_leader_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر قائد الفريق</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" 
                                    {{ old('team_leader_id', $team->team_leader_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('team_leader_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">التخصص</label>
                    <select id="specialization" name="specialization"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر التخصص</option>
                        <option value="general" {{ old('specialization', $team->specialization) == 'general' ? 'selected' : '' }}>صيانة عامة</option>
                        <option value="electrical" {{ old('specialization', $team->specialization) == 'electrical' ? 'selected' : '' }}>كهرباء</option>
                        <option value="plumbing" {{ old('specialization', $team->specialization) == 'plumbing' ? 'selected' : '' }}>سباكة</option>
                        <option value="hvac" {{ old('specialization', $team->specialization) == 'hvac' ? 'selected' : '' }}>تكييف وتبريد</option>
                        <option value="structural" {{ old('specialization', $team->specialization) == 'structural' ? 'selected' : '' }}>هيكلية</option>
                        <option value="painting" {{ old('specialization', $team->specialization) == 'painting' ? 'selected' : '' }}>دهان</option>
                        <option value="landscaping" {{ old('specialization', $team->specialization) == 'landscaping' ? 'selected' : '' }}>تنسيق حدائق</option>
                    </select>
                    @error('specialization')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="space-y-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات الاتصال</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" id="contact_email" name="contact_email"
                           value="{{ old('contact_email', $team->leader_email) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="أدخل البريد الإلكتروني">
                    @error('contact_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">هاتف الاتصال</label>
                    <input type="tel" id="contact_phone" name="contact_phone"
                           value="{{ old('contact_phone', $team->leader_phone) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="أدخل رقم الهاتف">
                    @error('contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Working Hours -->
        <div class="space-y-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">ساعات العمل</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="working_hours_start" class="block text-sm font-medium text-gray-700 mb-2">وقت البدء</label>
                    <input type="time" id="working_hours_start" name="working_hours_start"
                           value="{{ old('working_hours_start', $team->working_hours ? json_decode($team->working_hours)->start : '08:00') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('working_hours_start')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="working_hours_end" class="block text-sm font-medium text-gray-700 mb-2">وقت الانتهاء</label>
                    <input type="time" id="working_hours_end" name="working_hours_end"
                           value="{{ old('working_hours_end', $team->working_hours ? json_decode($team->working_hours)->end : '17:00') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('working_hours_end')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="max_concurrent_jobs" class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى للوظائف</label>
                    <input type="number" id="max_concurrent_jobs" name="max_concurrent_jobs"
                           value="{{ old('max_concurrent_jobs', $team->max_concurrent_jobs) }}"
                           min="1" max="20"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('max_concurrent_jobs')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Team Members -->
        <div class="space-y-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">أعضاء الفريق</h3>
            
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">إضافة عضو جديد</label>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <select name="new_member_id" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">اختر عضو</option>
                            @foreach($users as $user)
                                @if(!$team->members || !$team->members->contains($user->id))
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endif
                            @endforeach
                        </select>
                        <select name="new_member_role" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="member">عضو</option>
                            <option value="supervisor">مشرف</option>
                        </select>
                        <button type="button" onclick="addMember()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                            <i class="fas fa-plus ml-1"></i>
                            إضافة
                        </button>
                    </div>
                </div>
                
                @if($team->members && $team->members->count() > 0)
                    <div class="space-y-2" id="members_list">
                        @foreach($team->members as $member)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $member->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $member->email }}</p>
                                    </div>
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
                                </div>
                                <button type="button" onclick="removeMember({{ $member->id }})" 
                                        class="text-red-600 hover:text-red-800 transition-colors duration-150"
                                        title="إزالة">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-gray-500">
                        <i class="fas fa-users text-gray-400 text-2xl mb-2"></i>
                        <p>لا يوجد أعضاء حالياً</p>
                    </div>
                @endif
                
                <!-- Hidden input to store members data -->
                <input type="hidden" name="members_data" id="members_data" value='@if($team->members){{ $team->members->map(function($member) { return ['id' => $member->id, 'role' => $member->pivot->role]; })->toJson() }}@else[]@endif'>
            </div>
        </div>
        
        <!-- Additional Information -->
        <div class="space-y-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات إضافية</h3>
            
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                <textarea id="notes" name="notes" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="أي ملاحظات إضافية عن الفريق">{{ old('notes', $team->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Status -->
        <div class="space-y-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">الحالة</h3>
            
            <div class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $team->is_active) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="mr-2 block text-sm text-gray-700">
                    فريق نشط
                </label>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <a href="{{ route('maintenance.teams.show', $team) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-times ml-2"></i>
                إلغاء
            </a>
            
            <div class="flex items-center space-x-3 space-x-reverse">
                <button type="reset" 
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-redo ml-2"></i>
                    إعادة تعيين
                </button>
                
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-save ml-2"></i>
                    حفظ التغييرات
                </button>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript for member management -->
<script>
let membersData = JSON.parse(document.getElementById('members_data').value || '[]');

function addMember() {
    const select = document.querySelector('select[name="new_member_id"]');
    const roleSelect = document.querySelector('select[name="new_member_role"]');
    const userId = select.value;
    const role = roleSelect.value;
    
    if (!userId) {
        alert('الرجاء اختيار عضو');
        return;
    }
    
    // Add to members data
    membersData.push({ id: parseInt(userId), role: role });
    
    // Update hidden input
    document.getElementById('members_data').value = JSON.stringify(membersData);
    
    // Remove from select
    select.remove(select.selectedIndex);
    
    // Update UI
    updateMembersList();
}

function removeMember(userId) {
    membersData = membersData.filter(member => member.id !== userId);
    document.getElementById('members_data').value = JSON.stringify(membersData);
    updateMembersList();
}

function updateMembersList() {
    const container = document.getElementById('members_list');
    if (membersData.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4 text-gray-500">
                <i class="fas fa-users text-gray-400 text-2xl mb-2"></i>
                <p>لا يوجد أعضاء حالياً</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = membersData.map(member => `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center space-x-3 space-x-reverse">
                <div>
                    <p class="font-medium text-gray-900">${member.name || 'User ' + member.id}</p>
                    <p class="text-sm text-gray-500">${member.email || 'ID: ' + member.id}</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${member.role === 'supervisor' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'}">
                    ${member.role === 'supervisor' ? 'مشرف' : 'عضو'}
                </span>
            </div>
            <button type="button" onclick="removeMember(${member.id})" 
                    class="text-red-600 hover:text-red-800 transition-colors duration-150"
                    title="إزالة">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `).join('');
}

// Validate working hours
document.addEventListener('DOMContentLoaded', function() {
    const startTimeInput = document.getElementById('working_hours_start');
    const endTimeInput = document.getElementById('working_hours_end');
    
    function validateWorkingHours() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        
        if (startTime && endTime && startTime >= endTime) {
            endTimeInput.setCustomValidity('وقت الانتهاء يجب أن يكون بعد وقت البدء');
        } else {
            endTimeInput.setCustomValidity('');
        }
    }
    
    startTimeInput.addEventListener('change', validateWorkingHours);
    endTimeInput.addEventListener('change', validateWorkingHours);
});
</script>

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
