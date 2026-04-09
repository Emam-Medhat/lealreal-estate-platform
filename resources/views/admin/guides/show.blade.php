@extends('layouts.app')

@section('title', $guide->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $guide->title }}</h1>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                            {{ $guide->difficulty }}
                        </span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                            {{ $guide->status }}
                        </span>
                        @if($guide->category)
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                                {{ $guide->category }}
                            </span>
                        @endif
                    </div>
                    @if($guide->excerpt)
                        <p class="text-gray-600 mt-2">{{ $guide->excerpt }}</p>
                    @endif
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.guides.edit', $guide) }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <form action="{{ route('admin.guides.destroy', $guide) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this guide?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Guide Content -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            @if($guide->featured_image)
                <div class="mb-8">
                    <img src="{{ asset('storage/' . $guide->featured_image) }}" 
                         alt="{{ $guide->title }}" 
                         class="w-full h-64 object-cover rounded-lg">
                </div>
            @endif

            <div class="prose prose-lg max-w-none">
                {!! $guide->content !!}
            </div>
        </div>

        <!-- Guide Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Category:</span>
                        <span class="text-sm text-gray-600">{{ $guide->category ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Difficulty:</span>
                        <span class="text-sm text-gray-600">{{ $guide->difficulty }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Reading Time:</span>
                        <span class="text-sm text-gray-600">{{ $guide->reading_time ?? 0 }} minutes</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Estimated Time:</span>
                        <span class="text-sm text-gray-600">{{ $guide->estimated_time ?? 0 }} minutes</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Status:</span>
                        <span class="text-sm px-2 py-1 rounded-full
                            @if($guide->status === 'published') bg-green-100 text-green-800
                            @elseif($guide->status === 'draft') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $guide->status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Author & Dates -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Author & Dates</h3>
                
                <div class="space-y-3">
                    @if($guide->author)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Author:</span>
                            <span class="text-sm text-gray-600">{{ $guide->author->name }}</span>
                        </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Created:</span>
                        <span class="text-sm text-gray-600">{{ $guide->created_at->format('M d, Y g:i A') }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Published:</span>
                        <span class="text-sm text-gray-600">
                            {{ $guide->published_at ? $guide->published_at->format('M d, Y g:i A') : 'Not published' }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Last Updated:</span>
                        <span class="text-sm text-gray-600">{{ $guide->updated_at->format('M d, Y g:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        @if($guide->prerequisites || $guide->learning_objectives)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Learning Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($guide->prerequisites)
                        <div>
                            <h4 class="text-md font-medium text-gray-700 mb-2">Prerequisites</h4>
                            <div class="text-sm text-gray-600">
                                {!! nl2br(htmlspecialchars($guide->prerequisites)) !!}
                            </div>
                        </div>
                    @endif
                    
                    @if($guide->learning_objectives)
                        <div>
                            <h4 class="text-md font-medium text-gray-700 mb-2">Learning Objectives</h4>
                            <div class="text-sm text-gray-600">
                                {!! nl2br(htmlspecialchars($guide->learning_objectives)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- SEO Information -->
        @if($guide->meta_title || $guide->meta_description)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">SEO Information</h3>
                
                <div class="space-y-3">
                    @if($guide->meta_title)
                        <div>
                            <span class="text-sm font-medium text-gray-700">Meta Title:</span>
                            <div class="text-sm text-gray-600 mt-1">{{ $guide->meta_title }}</div>
                        </div>
                    @endif
                    
                    @if($guide->meta_description)
                        <div>
                            <span class="text-sm font-medium text-gray-700">Meta Description:</span>
                            <div class="text-sm text-gray-600 mt-1">{{ $guide->meta_description }}</div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Statistics -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistics</h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $guide->views ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Views</div>
                </div>
                
                <div class="p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">
                        {{ $guide->is_featured ? 'Yes' : 'No' }}
                    </div>
                    <div class="text-sm text-gray-600">Featured</div>
                </div>
                
                <div class="p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ $guide->reading_time ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Reading Time (min)</div>
                </div>
                
                <div class="p-4 bg-orange-50 rounded-lg">
                    <div class="text-2xl font-bold text-orange-600">{{ $guide->estimated_time ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Estimated Time (min)</div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between mt-6">
            <a href="{{ route('admin.guides.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Guides
            </a>
        </div>
    </div>
</div>
@endsection
