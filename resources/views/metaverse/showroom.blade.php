@extends('layouts.app')

@section('title', 'صالات العرض الافتراضية')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg p-8 mb-8 text-white">
        <h1 class="text-4xl font-bold mb-4">صالات العرض الافتراضية</h1>
        <p class="text-xl opacity-90">استكشف صالات العرض التفاعلية في العوالم الافتراضية</p>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['total_showrooms'] }}</div>
                <div class="text-sm opacity-90">صالات العرض</div>
            </div>
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['active_showrooms'] }}</div>
                <div class="text-sm opacity-90">نشطة</div>
            </div>
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['total_visitors'] }}</div>
                <div class="text-sm opacity-90">زوار حاليين</div>
            </div>
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['total_events'] }}</div>
                <div class="text-sm opacity-90">فعاليات</div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold mb-4">البحث والتصفية</h2>
        <form method="GET" action="{{ route('metaverse.showrooms.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">العالم الافتراضي</label>
                <select name="world_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">كل العوالم</option>
                    @foreach($worlds as $world)
                        <option value="{{ $world->id }}" {{ request('world_id') == $world->id ? 'selected' : '' }}>
                            {{ $world->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع صالة العرض</label>
                <select name="showroom_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">كل الأنواع</option>
                    <option value="residential" {{ request('showroom_type') == 'residential' ? 'selected' : '' }}>سكني</option>
                    <option value="commercial" {{ request('showroom_type') == 'commercial' ? 'selected' : '' }}>تجاري</option>
                    <option value="mixed" {{ request('showroom_type') == 'mixed' ? 'selected' : '' }}>مختلط</option>
                    <option value="exhibition" {{ request('showroom_type') == 'exhibition' ? 'selected' : '' }}>معرض</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">مستوى الوصول</label>
                <select name="access_level" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">كل المستويات</option>
                    <option value="public" {{ request('access_level') == 'public' ? 'selected' : '' }}>عام</option>
                    <option value="private" {{ request('access_level') == 'private' ? 'selected' : '' }}>خاص</option>
                    <option value="restricted" {{ request('access_level') == 'restricted' ? 'selected' : '' }}>مقيد</option>
                    <option value="premium" {{ request('access_level') == 'premium' ? 'selected' : '' }}>مميز</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">السعة</label>
                <select name="capacity" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">كل السعات</option>
                    <option value="small" {{ request('capacity') == 'small' ? 'selected' : '' }}>صغيرة (1-10)</option>
                    <option value="medium" {{ request('capacity') == 'medium' ? 'selected' : '' }}>متوسطة (11-50)</option>
                    <option value="large" {{ request('capacity') == 'large' ? 'selected' : '' }}>كبيرة (51-100)</option>
                    <option value="xlarge" {{ request('capacity') == 'xlarge' ? 'selected' : '' }}>كبيرة جداً (100+)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الميزات</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ request('is_active') ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm">نشطة</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="has_events" value="1" {{ request('has_events') ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm">لها فعاليات</span>
                    </label>
                </div>
            </div>
            
            <div class="md:col-span-4">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                    بحث
                </button>
                <a href="{{ route('metaverse.showrooms.index') }}" class="ml-2 text-gray-600 hover:text-gray-800">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- Showrooms Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @forelse($showrooms as $showroom)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <!-- Showroom Image -->
                <div class="relative h-48 bg-gray-200">
                    <img src="{{ $showroom->getThumbnailUrl() }}" alt="{{ $showroom->title }}" class="w-full h-full object-cover">
                    @if($showroom->is_active)
                        <div class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs">
                            نشطة
                        </div>
                    @endif
                    @if($showroom->current_visitors > 0)
                        <div class="absolute top-2 left-2 bg-blue-500 text-white px-2 py-1 rounded-full text-xs">
                            {{ $showroom->current_visitors }} زائر
                        </div>
                    @endif
                </div>
                
                <!-- Showroom Info -->
                <div class="p-4">
                    <h3 class="font-bold text-lg mb-2">{{ $showroom->title }}</h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $showroom->description }}</p>
                    
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-sm text-gray-500">
                            {{ $showroom->virtualWorld->name }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $showroom->getShowroomTypeTextAttribute() }}
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-sm text-gray-500">
                            السعة: {{ $showroom->current_visitors }}/{{ $showroom->max_visitors }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $showroom->getAccessLevelTextAttribute() }}
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                        <div>👁 {{ $showroom->view_count }}</div>
                        <div>👥 {{ $showroom->visit_count }}</div>
                        <div>📅 {{ $showroom->event_count }}</div>
                    </div>
                    
                    <!-- Capacity Progress Bar -->
                    <div class="mb-4">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>السعة المستخدمة</span>
                            <span>{{ $showroom->getCapacityUsageAttribute() }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $showroom->getCapacityUsageAttribute() }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Upcoming Events -->
                    @if($showroom->upcomingEvents->count() > 0)
                        <div class="mb-4">
                            <div class="text-xs text-gray-500 mb-1">الفعاليات القادمة:</div>
                            <div class="space-y-1">
                                @foreach($showroom->upcomingEvents->take(2) as $event)
                                    <div class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                        {{ $event->title }} - {{ $event->start_time->format('M j, H:i') }}
                                    </div>
                                @endforeach
                                @if($showroom->upcomingEvents->count() > 2)
                                    <div class="text-gray-400 text-xs">+{{ $showroom->upcomingEvents->count() - 2 }} أخرى</div>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    <div class="flex gap-2">
                        <a href="{{ route('metaverse.showrooms.show', $showroom) }}" 
                           class="flex-1 bg-indigo-600 text-white text-center py-2 rounded-md hover:bg-indigo-700 transition-colors">
                            عرض التفاصيل
                        </a>
                        @if($showroom->is_active && !$showroom->getIsFullAttribute())
                            <button onclick="enterShowroom({{ $showroom->id }})" 
                                    class="flex-1 bg-green-600 text-white text-center py-2 rounded-md hover:bg-green-700 transition-colors">
                                دخول
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-500 text-lg">لم يتم العثور على صالات عرض</div>
                <p class="text-gray-400 mt-2">حاول تعديل معايير البحث</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($showrooms->hasPages())
        <div class="flex justify-center">
            {{ $showrooms->links() }}
        </div>
    @endif
</div>

<!-- Quick Actions Floating Button -->
<div class="fixed bottom-8 right-8 flex flex-col gap-2">
    <a href="{{ route('metaverse.showrooms.create') }}" 
       class="bg-indigo-600 text-white p-4 rounded-full shadow-lg hover:bg-indigo-700 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
    </a>
    <a href="{{ route('metaverse.marketplace.index') }}" 
       class="bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
    </a>
</div>

<script>
// Enter showroom function
function enterShowroom(showroomId) {
    fetch(`/metaverse/showrooms/${showroomId}/enter`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to virtual space
            window.location.href = data.redirect_url;
        } else {
            alert(data.message || 'فشل الدخول إلى صالة العرض');
        }
    })
    .catch(error => {
        console.error('Error entering showroom:', error);
        alert('حدث خطأ أثناء الدخول إلى صالة العرض');
    });
}

// Real-time updates
setInterval(() => {
    // Update showroom visitor counts
    fetch('/api/metaverse/showrooms/stats')
        .then(response => response.json())
        .then(data => {
            // Update visitor counts in the UI
            document.querySelectorAll('.visitor-count').forEach(element => {
                const showroomId = element.dataset.showroomId;
                const visitorCount = data.visitor_counts[showroomId];
                if (visitorCount !== undefined) {
                    element.textContent = visitorCount;
                }
            });
        });
}, 30000); // Update every 30 seconds

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const form = e.target.closest('form');
                form.submit();
            }, 500);
        });
    }
});
</script>
@endsection
