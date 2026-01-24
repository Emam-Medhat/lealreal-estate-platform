@extends('layouts.app')

@section('title', 'قوالب العقود')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">قوالب العقود</h1>
        <a href="{{ route('contract-templates.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
            <i class="fas fa-plus ml-2"></i>إنشاء قالب جديد
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن قالب عقد..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الفئات</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 w-full">
                    <i class="fas fa-search ml-2"></i>بحث
                </button>
            </div>
        </form>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="bg-green-100 text-green-600 rounded-full p-3 ml-3">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $template->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $template->category->name }}</p>
                            </div>
                        </div>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $template->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    
                    <p class="text-gray-600 mb-4">{{ Str::limit($template->description, 100) }}</p>
                    
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <span class="ml-4">
                            <i class="fas fa-layer-group ml-1"></i>
                            {{ $template->terms_count ?? 0 }} بند
                        </span>
                        <span class="ml-4">
                            <i class="fas fa-file ml-1"></i>
                            {{ $template->contracts_count ?? 0 }} عقد
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-calendar ml-1"></i>
                            {{ $template->updated_at->format('Y-m-d') }}
                        </div>
                        <div class="flex space-x-2 space-x-reverse">
                            <a href="{{ route('contract-templates.show', $template) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('contract-templates.preview', $template) }}" class="text-green-600 hover:text-green-900">
                                <i class="fas fa-search"></i>
                            </a>
                            <a href="{{ route('contract-templates.edit', $template) }}" class="text-yellow-600 hover:text-yellow-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('contract-templates.duplicate', $template) }}" class="text-purple-600 hover:text-purple-900">
                                <i class="fas fa-copy"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-file-contract text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد قوالب عقود</h3>
                <p class="text-gray-600 mb-4">لم يتم إنشاء أي قوالب عقود بعد</p>
                <a href="{{ route('contract-templates.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    <i class="fas fa-plus ml-2"></i>إنشاء قالب جديد
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($templates->hasPages())
        <div class="mt-6">
            {{ $templates->links() }}
        </div>
    @endif
</div>
@endsection
