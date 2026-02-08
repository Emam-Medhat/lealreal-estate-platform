@extends('layouts.app')

@section('title', $page->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $page->title }}</h1>
                    @if($page->excerpt)
                        <p class="text-gray-600">{{ $page->excerpt }}</p>
                    @endif
                </div>
                @if(auth()->check() && auth()->user()->hasRole('admin'))
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.pages.edit', $page) }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Page Content -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            @if($page->featured_image)
                <div class="mb-8">
                    <img src="{{ asset('storage/' . $page->featured_image) }}" 
                         alt="{{ $page->title }}" 
                         class="w-full h-64 object-cover rounded-lg">
                </div>
            @endif

            <div class="prose prose-lg max-w-none">
                {!! $page->content !!}
            </div>

            <!-- Page Meta -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <div>
                        @if($page->author)
                            <span>By {{ $page->author->name }}</span>
                        @endif
                        @if($page->published_at)
                            <span class="mx-2">•</span>
                            <span>Published {{ $page->published_at->format('M d, Y') }}</span>
                        @endif
                    </div>
                    <div>
                        @if($page->updated_at && $page->updated_at != $page->created_at)
                            <span>Last updated {{ $page->updated_at->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Pages (if any) -->
        @if(isset($relatedPages) && $relatedPages->count() > 0)
            <div class="mt-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Related Pages</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($relatedPages as $relatedPage)
                        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                <a href="{{ route('pages.show', $relatedPage) }}" 
                                   class="hover:text-blue-600 transition-colors">
                                    {{ $relatedPage->title }}
                                </a>
                            </h3>
                            @if($relatedPage->excerpt)
                                <p class="text-gray-600 text-sm">{{ Str::limit($relatedPage->excerpt, 100) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
