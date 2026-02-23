@extends('admin.layouts.admin')

@section('title', 'الأسئلة الشائعة')
@section('page-title', 'الأسئلة الشائعة')

@section('content')
    <!-- Page Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-3xl shadow-2xl p-8 text-white relative overflow-hidden">
            <!-- Animated Background Pattern -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -mr-48 -mt-48 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full -ml-32 -mb-32 animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/2 w-48 h-48 bg-yellow-300 rounded-full -ml-24 -mt-24 animate-pulse" style="animation-delay: 2s;"></div>
            </div>
            
            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 opacity-10">
                <div class="h-full w-full" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
            </div>
            
            <div class="relative z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="bg-white bg-opacity-25 rounded-2xl p-4 ml-4 backdrop-blur-sm border border-white border-opacity-40 shadow-xl transform hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-question-circle text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-5xl font-black text-white mb-2 bg-clip-text text-transparent bg-gradient-to-r from-white to-yellow-200">الأسئلة الشائعة</h1>
                                <p class="text-white text-xl font-medium">ابحث عن إجابات للأسئلة المتكررة في العقارات</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-6 mt-6">
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">إجمالي الأسئلة:</span>
                                <span class="text-lg font-black text-white ml-2">{{ $faqs->count() }}</span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">التصنيفات:</span>
                                <span class="text-lg font-black text-white ml-2">{{ $categories->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-reverse space-x-4">
                        <button class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-2xl text-sm font-bold hover:bg-yellow-50 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-yellow-300/50">
                            <i class="fas fa-plus ml-2"></i>
                            سؤال جديد
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Enhanced Search -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
                <div class="relative">
                    <input type="text" id="faqSearch" placeholder="ابحث في الأسئلة الشائعة..." 
                           class="w-full px-6 py-4 pl-14 border border-gray-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:border-transparent text-lg font-medium">
                    <i class="fas fa-search absolute left-6 top-5 text-gray-400 text-xl"></i>
                </div>
            </div>

            <!-- Enhanced Category Filter -->
            @if($categories->count() > 0)
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-layer-group ml-3 text-indigo-600"></i>
                        التصنيفات
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        <button onclick="filterCategory('')" 
                                class="category-btn px-6 py-3 rounded-full text-sm font-bold {{ !request('category') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-all duration-300 transform hover:scale-105">
                            جميع التصنيفات
                        </button>
                        @foreach($categories as $category)
                            <button onclick="filterCategory('{{ $category }}')" 
                                    class="category-btn px-6 py-3 rounded-full text-sm font-bold {{ request('category') === $category ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-all duration-300 transform hover:scale-105">
                                {{ ucfirst($category) }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Enhanced FAQs -->
            <div class="space-y-6" id="faqContainer">
                @foreach($faqs as $faq)
                    <div class="faq-item bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:scale-102" data-category="{{ $faq->category ?? 'general' }}">
                        <button onclick="toggleFAQ({{ $faq->id }})" 
                                class="w-full px-8 py-6 text-right flex items-center justify-between hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all duration-300">
                            <div class="flex items-center space-x-4 flex-1">
                                @if($faq->is_featured)
                                    <span class="text-yellow-500 text-xl">
                                        <i class="fas fa-star"></i>
                                    </span>
                                @endif
                                <h3 class="font-bold text-gray-900 text-lg">{{ $faq->question }}</h3>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300 text-xl" id="icon-{{ $faq->id }}"></i>
                        </button>
                        <div class="hidden px-8 py-6 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200" id="answer-{{ $faq->id }}">
                            <div class="prose max-w-none text-gray-700 text-lg leading-relaxed">
                                {!! $faq->answer !!}
                            </div>
                            <div class="mt-6 flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 font-medium">
                                        <i class="fas fa-tag ml-2"></i>
                                        {{ ucfirst($faq->category ?? 'General') }}
                                    </span>
                                    <span class="inline-flex items-center text-gray-600">
                                        <i class="fas fa-eye ml-2"></i>
                                        {{ $faq->views }} مشاهدة
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($faqs->count() === 0)
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-16 text-center">
                    <div class="flex flex-col items-center">
                        <div class="bg-gradient-to-br from-gray-100 to-gray-200 p-12 rounded-3xl mb-6">
                            <i class="fas fa-question-circle text-gray-400 text-7xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-700 mb-3">لا توجد أسئلة شائعة</h3>
                        <p class="text-gray-500 text-lg mb-8">حاول تعديل البحث أو معايير التصفية</p>
                        <button class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-bold hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-indigo-500/50">
                            <i class="fas fa-plus ml-3"></i>
                            إضافة سؤال جديد
                        </button>
                    </div>
                </div>
            @endif
            </div>

        <!-- Enhanced Sidebar -->
        <div class="space-y-8">
            <!-- Quick Links -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-link ml-3 text-indigo-600"></i>
                    روابط سريعة
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('blog.index') }}" class="flex items-center p-4 rounded-xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-300 group">
                        <div class="bg-blue-100 p-3 rounded-xl ml-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-blog text-blue-600 text-xl"></i>
                        </div>
                        <span class="text-gray-700 font-medium">المدونة</span>
                    </a>
                    <a href="{{ route('guides.index') }}" class="flex items-center p-4 rounded-xl hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-all duration-300 group">
                        <div class="bg-green-100 p-3 rounded-xl ml-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-book text-green-600 text-xl"></i>
                        </div>
                        <span class="text-gray-700 font-medium">الأدلة</span>
                    </a>
                    <a href="{{ route('news.index') }}" class="flex items-center p-4 rounded-xl hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 transition-all duration-300 group">
                        <div class="bg-purple-100 p-3 rounded-xl ml-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-newspaper text-purple-600 text-xl"></i>
                        </div>
                        <span class="text-gray-700 font-medium">الأخبار</span>
                    </a>
                    <a href="{{ route('contact') }}" class="flex items-center p-4 rounded-xl hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all duration-300 group">
                        <div class="bg-indigo-100 p-3 rounded-xl ml-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-envelope text-indigo-600 text-xl"></i>
                        </div>
                        <span class="text-gray-700 font-medium">الدعم الفني</span>
                    </a>
                </div>
            </div>

            <!-- Popular Questions -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-fire ml-3 text-orange-500"></i>
                    الأسئلة الشائعة
                </h3>
                <div class="space-y-3">
                    @foreach($faqs->where('is_featured', true)->take(5) as $popular)
                        <button onclick="scrollToFAQ({{ $popular->id }})" 
                                class="w-full text-right p-4 rounded-xl hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all duration-300 group">
                            <div class="flex items-start">
                                <i class="fas fa-star text-yellow-500 ml-3 mt-1"></i>
                                <h4 class="text-sm font-bold text-gray-900">{{ Str::limit($popular->question, 80) }}</h4>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Enhanced Still Need Help -->
            <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 text-white rounded-2xl shadow-2xl p-8 relative overflow-hidden">
                <!-- Animated Background -->
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white rounded-full -mr-16 -mt-16 animate-pulse"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white rounded-full -ml-12 -mb-12 animate-pulse" style="animation-delay: 1s;"></div>
                </div>
                
                <div class="relative z-10">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-headset ml-3"></i>
                        هل تحتاج مساعدة؟
                    </h3>
                    <p class="text-indigo-100 mb-6 text-lg">لا يمكن العثور على ما تبحث عنه؟ فريق الدعم هنا للمساعدة</p>
                    <div class="space-y-3">
                        <a href="{{ route('contact') }}" 
                           class="block w-full px-6 py-4 bg-white text-indigo-600 text-center rounded-xl font-bold hover:bg-yellow-50 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-envelope ml-2"></i>
                            تواصل مع الدعم
                        </a>
                        <button class="w-full px-6 py-4 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-40 text-white rounded-xl font-bold hover:bg-opacity-30 transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-comments ml-2"></i>
                            محادثة مباشرة
                        </button>
                    </div>
                </div>
            </div>

            <!-- Enhanced Stats -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-chart-bar ml-3 text-indigo-600"></i>
                    إحصائيات الأسئلة
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                        <span class="text-gray-700 font-medium">إجمالي الأسئلة</span>
                        <span class="font-bold text-blue-600 text-lg">{{ $faqs->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl">
                        <span class="text-gray-700 font-medium">التصنيفات</span>
                        <span class="font-bold text-green-600 text-lg">{{ $categories->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl">
                        <span class="text-gray-700 font-medium">إجمالي المشاهدات</span>
                        <span class="font-bold text-purple-600 text-lg">{{ $faqs->sum('views') }}</span>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFAQ(id) {
    const answer = document.getElementById('answer-' + id);
    const icon = document.getElementById('icon-' + id);
    
    if (answer.classList.contains('hidden')) {
        answer.classList.remove('hidden');
        icon.classList.add('rotate-180');
        
        // Increment view count (you'd need to implement this via AJAX)
        incrementViewCount(id);
    } else {
        answer.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}

function filterCategory(category) {
    const items = document.querySelectorAll('.faq-item');
    const buttons = document.querySelectorAll('.category-btn');
    
    // Update button states
    buttons.forEach(btn => {
        if (btn.textContent.toLowerCase().includes(category.toLowerCase()) || 
            (category === '' && btn.textContent.includes('All'))) {
            btn.classList.remove('bg-gray-100', 'text-gray-700');
            btn.classList.add('bg-indigo-600', 'text-white');
        } else {
            btn.classList.remove('bg-indigo-600', 'text-white');
            btn.classList.add('bg-gray-100', 'text-gray-700');
        }
    });
    
    // Filter items
    items.forEach(item => {
        if (category === '' || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function scrollToFAQ(id) {
    const element = document.querySelector('[data-id="' + id + '"]');
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
        toggleFAQ(id);
    }
}

function incrementViewCount(id) {
    fetch('/faqs/' + id + '/increment-view', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    });
}

// Search functionality
document.getElementById('faqSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const items = document.querySelectorAll('.faq-item');
    
    items.forEach(item => {
        const question = item.querySelector('h3').textContent.toLowerCase();
        const answer = item.querySelector('.prose').textContent.toLowerCase();
        
        if (question.includes(searchTerm) || answer.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>

<style>
.rotate-180 {
    transform: rotate(180deg);
}
</style>
@endsection
