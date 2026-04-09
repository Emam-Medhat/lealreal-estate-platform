@extends('layouts.app')

@section('title', 'تعديل التقييم')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">تعديل التقييم</h1>
        <p class="text-gray-600">قم بتحديث تقييمك</p>
    </div>

    <!-- Reviewable Info -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">عنصر التقييم</h3>
        <div class="flex items-center">
            <div class="flex-shrink-0 h-16 w-16 bg-gray-200 rounded-lg flex items-center justify-center">
                @if($review->reviewable instanceof \App\Models\Property)
                    <i class="fas fa-home text-gray-600 text-2xl"></i>
                @elseif($review->reviewable instanceof \App\Models\Agent)
                    <i class="fas fa-user-tie text-gray-600 text-2xl"></i>
                @else
                    <i class="fas fa-star text-gray-600 text-2xl"></i>
                @endif
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-900">
                    @if($review->reviewable instanceof \App\Models\Property)
                        عقار: {{ $review->reviewable->title ?? $review->reviewable->name }}
                    @elseif($review->reviewable instanceof \App\Models\Agent)
                        وكيل: {{ $review->reviewable->name }}
                    @else
                        {{ $review->reviewable->name ?? 'عنصر' }}
                    @endif
                </p>
                <p class="text-xs text-gray-500">معرف: {{ $review->reviewable_id }}</p>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('reviews.update', $review->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        عنوان التقييم <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $review->title) }}" 
                           required maxlength="255"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Rating -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        التقييم <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <input type="hidden" name="rating" id="rating-input" value="{{ old('rating', $review->rating) }}" required>
                        <div id="rating-stars" class="flex space-x-1 space-x-reverse">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="star-btn text-3xl 
                                    @if($i <= $review->rating) text-yellow-400 @else text-gray-300 @endif
                                    hover:text-yellow-500 transition-colors"
                                        data-rating="{{ $i }}">
                                    <i class="fas fa-star"></i>
                                </button>
                            @endfor
                        </div>
                        <span id="rating-text" class="text-lg font-medium text-gray-700">
                            {{ $review->rating }} {{ $review->getRatingText() }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">اختر تقييمك من 1 إلى 5 نجوم</p>
                </div>

                <!-- Recommendation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        هل توصي بهذا العنصر؟
                    </label>
                    <div class="flex space-x-4 space-x-reverse">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="recommendation" value="1" 
                                   @if(old('recommendation', $review->recommendation) == 1) checked @endif
                                   class="ml-2">
                            <span class="text-green-600"><i class="fas fa-thumbs-up ml-1"></i>نعم</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="recommendation" value="0" 
                                   @if(old('recommendation', $review->recommendation) == 0) checked @endif
                                   class="ml-2">
                            <span class="text-red-600"><i class="fas fa-thumbs-down ml-1"></i>لا</span>
                        </label>
                    </div>
                </div>

                <!-- Content -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        تفاصيل التقييم <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" rows="6" required minlength="10"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('content', $review->content) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">يجب أن يكون على الأقل 10 أحرف</p>
                </div>

                <!-- Pros -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        الإيجابيات (اختياري)
                    </label>
                    <textarea name="pros" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('pros', $review->pros) }}</textarea>
                </div>

                <!-- Cons -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        السلبيات (اختياري)
                    </label>
                    <textarea name="cons" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('cons', $review->cons) }}</textarea>
                </div>

                <!-- Anonymous Option -->
                <div class="md:col-span-2">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_anonymous" value="1" 
                               @if(old('is_anonymous', $review->is_anonymous)) checked @endif
                               class="ml-2">
                        <span class="text-gray-700">نشر التقييم بشكل مجهول</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1">لن يتم عرض اسمك مع التقييم</p>
                </div>
            </div>

            <!-- Status Info -->
            @if(auth()->user()->hasRole('admin'))
                <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                    <h4 class="text-sm font-semibold text-yellow-900 mb-2">
                        <i class="fas fa-info-circle ml-2"></i>معلومات الحالة
                    </h4>
                    <div class="text-sm text-yellow-800">
                        <p>الحالة الحالية: <span class="font-semibold">{{ $review->getStatusText() }}</span></p>
                        @if($review->approved_at)
                            <p>تاريخ الموافقة: {{ $review->approved_at->locale('ar')->translatedFormat('d F Y') }}</p>
                        @endif
                        @if($review->rejected_at)
                            <p>تاريخ الرفض: {{ $review->rejected_at->locale('ar')->translatedFormat('d F Y') }}</p>
                            @if($review->rejection_reason)
                                <p>سبب الرفض: {{ $review->rejection_reason }}</p>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

            <!-- Guidelines -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h4 class="text-sm font-semibold text-blue-900 mb-2">
                    <i class="fas fa-info-circle ml-2"></i>إرشادات التقييم
                </h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• كن صادقاً وموضوعياً في تقييمك</li>
                    <li>• ركز على تجربتك الشخصية</li>
                    <li>• تجنب استخدام لغة مسيئة أو غير لائقة</li>
                    <li>• لا تشارك معلومات شخصية أو حساسة</li>
                </ul>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex justify-between">
                <div class="flex space-x-2 space-x-reverse">
                    <a href="{{ route('reviews.show', $review->id) }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-arrow-right ml-2"></i>
                        إلغاء
                    </a>
                    @if(auth()->user()->hasRole('admin'))
                        <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                                    onclick="return confirm('هل أنت متأكد من حذف هذا التقييم؟')">
                                <i class="fas fa-trash ml-2"></i>
                                حذف
                            </button>
                        </form>
                    @endif
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save ml-2"></i>
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating-input');
    const ratingText = document.getElementById('rating-text');
    
    const ratingTexts = {
        1: '1 سيء جداً',
        2: '2 سيء',
        3: '3 متوسط',
        4: '4 جيد',
        5: '5 ممتاز'
    };

    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingInput.value = rating;
            ratingText.textContent = ratingTexts[rating];
            updateStars(rating);
        });

        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            updateStars(rating);
        });
    });

    document.getElementById('rating-stars').addEventListener('mouseleave', function() {
        updateStars(parseInt(ratingInput.value));
    });

    function updateStars(rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('text-yellow-400');
                star.classList.remove('text-gray-300');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }

    // Initialize
    updateStars(parseInt(ratingInput.value));
});
</script>
@endsection
