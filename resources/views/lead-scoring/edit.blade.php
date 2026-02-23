@extends('admin.layouts.admin')

@section('title', 'تعديل تقييم العميل')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تعديل تقييم العميل</h1>
            <p class="text-gray-600 mt-1">تحديث بيانات التقييم للعميل {{ $leadScore->lead->first_name }} {{ $leadScore->lead->last_name }}</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('lead-scoring.show', $leadScore->id) }}" class="text-gray-600 hover:text-gray-900 font-medium">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للتفاصيل
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl mx-auto">
    <form action="{{ route('lead-scoring.update', $leadScore->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Lead Selection (Disabled) -->
            <div>
                <label for="lead_id" class="block text-sm font-medium text-gray-700 mb-2">العميل المحتمل</label>
                <input type="text" value="{{ $leadScore->lead->first_name }} {{ $leadScore->lead->last_name }} ({{ $leadScore->lead->email ?? 'بدون بريد إلكتروني' }})" class="w-full rounded-lg border-gray-300 bg-gray-100 cursor-not-allowed text-gray-500" disabled>
            </div>

            <!-- Score Input -->
            <div>
                <label for="score" class="block text-sm font-medium text-gray-700 mb-2">التقييم (0-100)</label>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <input type="range" name="score" id="score_range" min="0" max="100" value="{{ $leadScore->score }}" 
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                           oninput="document.getElementById('score').value = this.value">
                    <input type="number" name="score" id="score" min="0" max="100" value="{{ $leadScore->score }}" 
                           class="w-20 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 text-center"
                           oninput="document.getElementById('score_range').value = this.value">
                </div>
                @error('score')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Factors -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">عوامل التقييم</label>
                <div id="factors_container" class="space-y-3">
                    @if(!empty($leadScore->factors) && is_array($leadScore->factors))
                        @foreach($leadScore->factors as $factor)
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <input type="text" name="factors[]" value="{{ $factor }}" placeholder="عامل التقييم" class="flex-1 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                                <button type="button" onclick="removeFactor(this)" class="text-red-500 hover:text-red-700 p-2">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <input type="text" name="factors[]" placeholder="عامل التقييم" class="flex-1 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <button type="button" onclick="removeFactor(this)" class="text-red-500 hover:text-red-700 p-2">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    @endif
                </div>
                <button type="button" onclick="addFactor()" class="mt-3 text-sm text-purple-600 hover:text-purple-700 font-medium flex items-center">
                    <i class="fas fa-plus ml-1"></i>
                    إضافة عامل آخر
                </button>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات إضافية</label>
                <textarea name="notes" id="notes" rows="4" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" placeholder="أي ملاحظات حول هذا التقييم...">{{ $leadScore->notes }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end space-x-3 space-x-reverse">
            <button type="button" onclick="window.history.back()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                إلغاء
            </button>
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium transition-colors shadow-sm">
                تحديث التقييم
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function addFactor() {
        const container = document.getElementById('factors_container');
        const div = document.createElement('div');
        div.className = 'flex items-center space-x-2 space-x-reverse';
        div.innerHTML = `
            <input type="text" name="factors[]" placeholder="عامل التقييم" class="flex-1 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            <button type="button" onclick="removeFactor(this)" class="text-red-500 hover:text-red-700 p-2">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(div);
    }

    function removeFactor(btn) {
        const container = document.getElementById('factors_container');
        if (container.children.length > 1) {
            btn.closest('div').remove();
        } else {
            // Clear the input instead of removing the last one
            btn.previousElementSibling.value = '';
        }
    }
</script>
@endpush
@endsection
