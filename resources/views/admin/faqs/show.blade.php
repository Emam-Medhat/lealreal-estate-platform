@extends('admin.layouts.admin')

@section('title', 'FAQ Details')
@section('page-title', 'FAQ Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.faqs.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-arrow-right text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">FAQ Details</h1>
                    <p class="text-sm text-gray-500">View question and answer details</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this FAQ?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-50 text-red-600 px-4 py-2 rounded-lg hover:bg-red-100 transition-colors flex items-center gap-2 border border-red-200">
                        <i class="fas fa-trash"></i>
                        <span>Delete</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Question Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-question-circle text-indigo-500"></i>
                            Question
                        </h3>
                    </div>
                    <div class="p-6">
                        <p class="text-lg text-gray-800 font-medium leading-relaxed">{{ $faq->question }}</p>
                    </div>
                </div>

                <!-- Answer Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-comment-alt text-green-500"></i>
                            Answer
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="prose max-w-none text-gray-600 leading-relaxed whitespace-pre-wrap">{{ $faq->answer }}</div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="font-semibold text-gray-800">Status & Visibility</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                            <span class="text-sm text-gray-500">Status</span>
                            @if($faq->is_active)
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Active
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                    Inactive
                                </span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                            <span class="text-sm text-gray-500">Featured</span>
                            @if($faq->is_featured)
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 flex items-center gap-1">
                                    <i class="fas fa-star text-xs"></i>
                                    Featured
                                </span>
                            @else
                                <span class="text-sm text-gray-400">Standard</span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                            <span class="text-sm text-gray-500">Sort Order</span>
                            <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded text-gray-600">{{ $faq->order }}</span>
                        </div>
                    </div>
                </div>

                <!-- Category Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="font-semibold text-gray-800">Classification</h3>
                    </div>
                    <div class="p-4">
                        <div class="mb-1 text-xs text-gray-500 uppercase tracking-wider">Category</div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span class="font-medium text-gray-800">{{ $faq->category }}</span>
                        </div>
                    </div>
                </div>

                <!-- Metadata Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="font-semibold text-gray-800">Metadata</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Created At</div>
                            <div class="text-sm text-gray-800 flex items-center gap-2">
                                <i class="far fa-calendar text-gray-400"></i>
                                <span dir="ltr">{{ $faq->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Last Updated</div>
                            <div class="text-sm text-gray-800 flex items-center gap-2">
                                <i class="far fa-clock text-gray-400"></i>
                                <span dir="ltr">{{ $faq->updated_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
