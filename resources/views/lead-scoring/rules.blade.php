@extends('admin.layouts.admin')

@section('title', 'قواعد التقييم')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">قواعد التقييم</h1>
            <p class="text-gray-600 mt-1">تحديد القواعد الأوتوماتيكية لحساب نقاط العملاء المحتملين</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('lead-scoring.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <form action="{{ route('lead-scoring.rules.store') }}" method="POST">
        @csrf
        
        <div id="rules_container" class="space-y-6">
            @forelse($rules as $index => $rule)
                <div class="rule-card bg-gray-50 rounded-lg p-4 border border-gray-200 relative">
                    <button type="button" onclick="removeRule(this)" class="absolute top-4 left-4 text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">اسم القاعدة / العامل</label>
                            <input type="text" name="rules[{{ $index }}][factor]" value="{{ $rule['factor'] }}" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الوزن (النقاط)</label>
                            <input type="number" name="rules[{{ $index }}][weight]" value="{{ $rule['weight'] }}" min="0" max="100" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">الشروط</label>
                        <div class="conditions-container space-y-2 pl-4 border-l-2 border-purple-200">
                            @foreach($rule['conditions'] ?? [] as $condIndex => $condition)
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <select name="rules[{{ $index }}][conditions][{{ $condIndex }}][field]" class="rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
                                        <option value="email" {{ ($condition['field'] ?? '') == 'email' ? 'selected' : '' }}>البريد الإلكتروني</option>
                                        <option value="phone" {{ ($condition['field'] ?? '') == 'phone' ? 'selected' : '' }}>الهاتف</option>
                                        <option value="source_id" {{ ($condition['field'] ?? '') == 'source_id' ? 'selected' : '' }}>المصدر</option>
                                        <option value="status_id" {{ ($condition['field'] ?? '') == 'status_id' ? 'selected' : '' }}>الحالة</option>
                                        <option value="country" {{ ($condition['field'] ?? '') == 'country' ? 'selected' : '' }}>الدولة</option>
                                        <option value="city" {{ ($condition['field'] ?? '') == 'city' ? 'selected' : '' }}>المدينة</option>
                                    </select>
                                    
                                    <select name="rules[{{ $index }}][conditions][{{ $condIndex }}][operator]" class="rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
                                        <option value="equals" {{ ($condition['operator'] ?? '') == 'equals' ? 'selected' : '' }}>يساوي</option>
                                        <option value="contains" {{ ($condition['operator'] ?? '') == 'contains' ? 'selected' : '' }}>يحتوي على</option>
                                        <option value="greater_than" {{ ($condition['operator'] ?? '') == 'greater_than' ? 'selected' : '' }}>أكبر من</option>
                                        <option value="less_than" {{ ($condition['operator'] ?? '') == 'less_than' ? 'selected' : '' }}>أقل من</option>
                                    </select>
                                    
                                    <input type="text" name="rules[{{ $index }}][conditions][{{ $condIndex }}][value]" value="{{ $condition['value'] ?? '' }}" placeholder="القيمة" class="flex-1 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
                                    
                                    <button type="button" onclick="removeCondition(this)" class="text-red-400 hover:text-red-600 p-1">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" onclick="addCondition(this, {{ $index }})" class="text-xs text-purple-600 hover:text-purple-800 font-medium mt-1">
                            + إضافة شرط
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    <p>لا توجد قواعد معرفة حالياً</p>
                    <p class="text-sm mt-1">اضغط على زر "إضافة قاعدة جديدة" للبدء</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6 flex justify-between items-center">
            <button type="button" onclick="addRule()" class="px-4 py-2 border border-dashed border-purple-300 text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus ml-2"></i>
                إضافة قاعدة جديدة
            </button>
            
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium transition-colors shadow-sm">
                حفظ القواعد
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let ruleIndex = {{ count($rules) }};

    function addRule() {
        const container = document.getElementById('rules_container');
        // Clear empty state message if exists
        if (container.querySelector('.text-center')) {
            container.innerHTML = '';
        }

        const div = document.createElement('div');
        div.className = 'rule-card bg-gray-50 rounded-lg p-4 border border-gray-200 relative mb-6';
        div.innerHTML = `
            <button type="button" onclick="removeRule(this)" class="absolute top-4 left-4 text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">اسم القاعدة / العامل</label>
                    <input type="text" name="rules[${ruleIndex}][factor]" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الوزن (النقاط)</label>
                    <input type="number" name="rules[${ruleIndex}][weight]" min="0" max="100" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" required>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">الشروط</label>
                <div class="conditions-container space-y-2 pl-4 border-l-2 border-purple-200">
                    <!-- Conditions will be added here -->
                </div>
                <button type="button" onclick="addCondition(this, ${ruleIndex})" class="text-xs text-purple-600 hover:text-purple-800 font-medium mt-1">
                    + إضافة شرط
                </button>
            </div>
        `;
        container.appendChild(div);
        
        // Add one initial condition
        const btn = div.querySelector('button[onclick^="addCondition"]');
        addCondition(btn, ruleIndex);
        
        ruleIndex++;
    }

    function removeRule(btn) {
        if (confirm('هل أنت متأكد من حذف هذه القاعدة؟')) {
            btn.closest('.rule-card').remove();
            
            const container = document.getElementById('rules_container');
            if (container.children.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <p>لا توجد قواعد معرفة حالياً</p>
                        <p class="text-sm mt-1">اضغط على زر "إضافة قاعدة جديدة" للبدء</p>
                    </div>
                `;
            }
        }
    }

    function addCondition(btn, rIndex) {
        const container = btn.previousElementSibling;
        const cIndex = container.children.length;
        
        const div = document.createElement('div');
        div.className = 'flex items-center space-x-2 space-x-reverse';
        div.innerHTML = `
            <select name="rules[${rIndex}][conditions][${cIndex}][field]" class="rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
                <option value="email">البريد الإلكتروني</option>
                <option value="phone">الهاتف</option>
                <option value="source_id">المصدر</option>
                <option value="status_id">الحالة</option>
                <option value="country">الدولة</option>
                <option value="city">المدينة</option>
            </select>
            
            <select name="rules[${rIndex}][conditions][${cIndex}][operator]" class="rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
                <option value="equals">يساوي</option>
                <option value="contains">يحتوي على</option>
                <option value="greater_than">أكبر من</option>
                <option value="less_than">أقل من</option>
            </select>
            
            <input type="text" name="rules[${rIndex}][conditions][${cIndex}][value]" placeholder="القيمة" class="flex-1 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
            
            <button type="button" onclick="removeCondition(this)" class="text-red-400 hover:text-red-600 p-1">
                <i class="fas fa-trash-alt"></i>
            </button>
        `;
        container.appendChild(div);
    }

    function removeCondition(btn) {
        const container = btn.closest('.conditions-container');
        if (container.children.length > 1) {
            btn.closest('div').remove();
        } else {
            // Clear inputs instead of removing last condition
            const inputs = btn.closest('div').querySelectorAll('input');
            inputs.forEach(input => input.value = '');
        }
    }
</script>
@endpush
@endsection
