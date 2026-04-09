@extends('layouts.app')

@section('title', 'Sitemap Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Sitemap Management</h1>
                    <p class="text-gray-600">Manage and generate your website's XML sitemap</p>
                </div>
                <a href="{{ route('admin.seo.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-2"></i> Back to SEO
                </a>
            </div>

            <!-- Status Card -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Sitemap Status</h3>
                        <div class="flex items-center">
                            @if($sitemapExists)
                                <span class="flex items-center text-green-600 mr-4">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Sitemap exists
                                </span>
                                <span class="text-gray-500 text-sm">
                                    Last generated: {{ $lastGenerated }}
                                </span>
                            @else
                                <span class="flex items-center text-red-600">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Sitemap not found
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        @if($sitemapExists)
                            <a href="{{ url('sitemap.xml') }}" target="_blank" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                View Sitemap
                            </a>
                        @endif
                        
                        <form action="{{ route('admin.seo.generate-sitemap') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-sync-alt mr-2"></i>
                                Generate Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Configuration -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Included Content</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center p-4 bg-white border border-gray-200 rounded-lg">
                        <input type="checkbox" checked disabled class="h-4 w-4 text-blue-600 rounded border-gray-300">
                        <span class="ml-3 text-gray-700">Static Pages</span>
                    </div>
                    <div class="flex items-center p-4 bg-white border border-gray-200 rounded-lg">
                        <input type="checkbox" checked disabled class="h-4 w-4 text-blue-600 rounded border-gray-300">
                        <span class="ml-3 text-gray-700">Properties</span>
                    </div>
                    <div class="flex items-center p-4 bg-white border border-gray-200 rounded-lg">
                        <input type="checkbox" checked disabled class="h-4 w-4 text-blue-600 rounded border-gray-300">
                        <span class="ml-3 text-gray-700">Projects</span>
                    </div>
                    <div class="flex items-center p-4 bg-white border border-gray-200 rounded-lg">
                        <input type="checkbox" checked disabled class="h-4 w-4 text-blue-600 rounded border-gray-300">
                        <span class="ml-3 text-gray-700">Blog Posts</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
