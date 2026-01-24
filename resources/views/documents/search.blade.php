@extends('layouts.app')

@section('title', 'بحث في الوثائق')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">بحث في الوثائق</h1>
        <a href="{{ route('documents.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
            <i class="fas fa-arrow-right ml-2"></i>عودة
        </a>
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('documents.search') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">بحث</label>
                    <div class="relative">
                        <input type="text" 
                               name="query" 
                               value="{{ $query ?? '' }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-10" 
                               placeholder="ابحث عن عنوان، وصف، أو وسوم...">
                        <button type="submit" class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الفئة</label>
                    <select name="category_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">جميع الفئات</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">مستوى السرية</label>
                    <select name="confidentiality_level" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">الكل</option>
                        <option value="public" {{ request('confidentiality_level') == 'public' ? 'selected' : '' }}>عام</option>
                        <option value="internal" {{ request('confidentiality_level') == 'internal' ? 'selected' : '' }}>داخلي</option>
                        <option value="confidential" {{ request('confidentiality_level') == 'confidential' ? 'selected' : '' }}>سري</option>
                        <option value="restricted" {{ request('confidentiality_level') == 'restricted' ? 'selected' : '' }}>مقيد</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">نوع الملف</label>
                    <select name="file_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">الكل</option>
                        <option value="pdf" {{ request('file_type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="doc" {{ request('file_type') == 'doc' ? 'selected' : '' }}>Word</option>
                        <option value="xls" {{ request('file_type') == 'xls' ? 'selected' : '' }}>Excel</option>
                        <option value="ppt" {{ request('file_type') == 'ppt' ? 'selected' : '' }}>PowerPoint</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="is_active" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">الكل</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الترتيب</label>
                    <select name="sort" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>الأحدث أولاً</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>الأقدم أولاً</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>بالاسم</option>
                        <option value="size" {{ request('sort') == 'size' ? 'selected' : '' }}>بالحجم</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="button" onclick="clearFilters()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 ml-2">
                    <i class="fas fa-times ml-2"></i>مسح الفلاتر
                </button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    <i class="fas fa-search ml-2"></i>بحث
                </button>
            </div>
        </form>
    </div>

    <!-- Search Results -->
    @if(isset($query) || request()->hasAny(['category_id', 'confidentiality_level', 'file_type', 'is_active']))
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">نتائج البحث</h2>
                    <div class="text-sm text-gray-600">
                        تم العثور على {{ $documents->total() }} نتيجة
                    </div>
                </div>
            </div>
            
            @if($documents->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($documents as $document)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <h3 class="text-lg font-medium text-gray-900">{{ $document->title }}</h3>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $document->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} mr-3">
                                            {{ $document->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                        @switch($document->confidentiality_level)
                                            @case('public')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 mr-2">عام</span>
                                                @break
                                            @case('internal')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mr-2">داخلي</span>
                                                @break
                                            @case('confidential')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 mr-2">سري</span>
                                                @break
                                            @case('restricted')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 mr-2">مقيد</span>
                                                @break
                                        @endswitch
                                    </div>
                                    
                                    @if($document->description)
                                        <p class="text-gray-600 mb-2">{{ Str::limit($document->description, 150) }}</p>
                                    @endif
                                    
                                    <div class="flex items-center text-sm text-gray-500 space-x-4 space-x-reverse">
                                        <span>
                                            <i class="fas fa-folder ml-1"></i>
                                            {{ $document->category->name ?? 'غير مصنف' }}
                                        </span>
                                        <span>
                                            <i class="fas fa-file ml-1"></i>
                                            {{ $document->file_type }}
                                        </span>
                                        <span>
                                            <i class="fas fa-database ml-1"></i>
                                            {{ number_format($document->file_size / 1024, 2) }} KB
                                        </span>
                                        <span>
                                            <i class="fas fa-calendar ml-1"></i>
                                            {{ $document->created_at->format('Y-m-d') }}
                                        </span>
                                    </div>
                                    
                                    @if($document->tags && count($document->tags) > 0)
                                        <div class="mt-2">
                                            @foreach($document->tags as $tag)
                                                <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full ml-2 mb-1">
                                                    {{ $tag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex items-center space-x-2 space-x-reverse mr-4">
                                    <a href="{{ route('documents.show', $document) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('documents.preview', $document) }}" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-search"></i>
                                    </a>
                                    <a href="{{ route('documents.download', $document) }}" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    {{ $documents->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد نتائج</h3>
                    <p class="text-gray-600">لم يتم العثور على وثائق تطابق معايير البحث</p>
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">ابحث في الوثائق</h3>
            <p class="text-gray-600">استخدم نموذج البحث أعلاه للعثور على الوثائق التي تبحث عنها</p>
        </div>
    @endif
</div>

@section('scripts')
<script>
function clearFilters() {
    window.location.href = '{{ route('documents.search') }}';
}
</script>
@endsection
