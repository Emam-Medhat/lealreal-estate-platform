@extends('layouts.app')

@section('title', 'FAQs - Real Estate Pro')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">Frequently Asked Questions</h1>
                <p class="text-xl text-indigo-100">Find answers to common real estate questions</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Search -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <div class="relative">
                        <input type="text" id="faqSearch" placeholder="Search FAQs..." 
                               class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
                    </div>
                </div>

                <!-- Category Filter -->
                @if($categories->count() > 0)
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="filterCategory('')" 
                                    class="category-btn px-4 py-2 rounded-full text-sm {{ !request('category') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                                All Categories
                            </button>
                            @foreach($categories as $category)
                                <button onclick="filterCategory('{{ $category }}')" 
                                        class="category-btn px-4 py-2 rounded-full text-sm {{ request('category') === $category ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                                    {{ ucfirst($category) }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- FAQs -->
                <div class="space-y-4" id="faqContainer">
                    @foreach($faqs as $faq)
                        <div class="faq-item bg-white rounded-xl shadow-lg overflow-hidden" data-category="{{ $faq->category ?? 'general' }}">
                            <button onclick="toggleFAQ({{ $faq->id }})" 
                                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <div class="flex items-center space-x-3">
                                    @if($faq->is_featured)
                                        <span class="text-yellow-500">
                                            <i class="fas fa-star"></i>
                                        </span>
                                    @endif
                                    <h3 class="font-semibold text-gray-900">{{ $faq->question }}</h3>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform" id="icon-{{ $faq->id }}"></i>
                            </button>
                            <div class="hidden px-6 py-4 border-t border-gray-100" id="answer-{{ $faq->id }}">
                                <div class="prose max-w-none text-gray-700">
                                    {!! $faq->answer !!}
                                </div>
                                <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                                    <span>Category: {{ ucfirst($faq->category ?? 'General') }}</span>
                                    <span>{{ $faq->views }} views</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($faqs->count() === 0)
                    <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                        <i class="fas fa-question-circle text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No FAQs found</h3>
                        <p class="text-gray-600">Try adjusting your search or filter criteria</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Links -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h3>
                    <div class="space-y-3">
                        <a href="{{ route('blog.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-blog text-blue-600 mr-3"></i>
                            <span class="text-gray-700">Blog</span>
                        </a>
                        <a href="{{ route('guides.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-book text-green-600 mr-3"></i>
                            <span class="text-gray-700">Guides</span>
                        </a>
                        <a href="{{ route('news.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-newspaper text-purple-600 mr-3"></i>
                            <span class="text-gray-700">News</span>
                        </a>
                        <a href="{{ route('contact') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-envelope text-indigo-600 mr-3"></i>
                            <span class="text-gray-700">Contact Support</span>
                        </a>
                    </div>
                </div>

                <!-- Popular Questions -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Popular Questions</h3>
                    <div class="space-y-3">
                        @foreach($faqs->where('is_featured', true)->take(5) as $popular)
                            <button onclick="scrollToFAQ({{ $popular->id }})" 
                                    class="w-full text-left p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <h4 class="text-sm font-medium text-gray-900">{{ Str::limit($popular->question, 60) }}</h4>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Still Need Help -->
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-3">Still Need Help?</h3>
                    <p class="text-indigo-100 mb-4">Can't find what you're looking for? Our support team is here to help.</p>
                    <div class="space-y-3">
                        <a href="{{ route('contact') }}" 
                           class="block w-full px-4 py-2 bg-white text-indigo-600 text-center rounded-lg font-semibold hover:bg-indigo-50 transition-colors">
                            <i class="fas fa-envelope mr-2"></i>Contact Support
                        </a>
                        <button class="w-full px-4 py-2 bg-indigo-700 text-white rounded-lg font-semibold hover:bg-indigo-900 transition-colors">
                            <i class="fas fa-comments mr-2"></i>Live Chat
                        </button>
                    </div>
                </div>

                <!-- Stats -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">FAQ Stats</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total Questions</span>
                            <span class="font-semibold text-gray-900">{{ $faqs->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Categories</span>
                            <span class="font-semibold text-gray-900">{{ $categories->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total Views</span>
                            <span class="font-semibold text-gray-900">{{ $faqs->sum('views') }}</span>
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
