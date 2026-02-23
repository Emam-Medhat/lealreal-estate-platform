@extends('layouts.app')

@section('title', 'إنشاء مستند من القالب')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239CA3AF" fill-opacity="0.1"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Animated Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl shadow-2xl mb-6 transform rotate-3 hover:rotate-6 transition-transform duration-300">
                <i class="fas fa-file-alt text-white text-3xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text mb-4">
                إنشاء مستند
            </h1>
            <p class="text-xl text-gray-600 mb-2">قالب: {{ $template->name }}</p>
            <div class="flex items-center justify-center gap-2">
                <div class="h-1 w-20 bg-gradient-to-r from-blue-400 to-indigo-400 rounded"></div>
                <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
                <div class="h-1 w-20 bg-gradient-to-r from-indigo-400 to-blue-400 rounded"></div>
            </div>
        </div>

        <!-- Main Form Card with Glass Effect -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 p-8 relative overflow-hidden">
                <!-- Animated Background Elements -->
                <div class="absolute top-0 left-0 w-full h-full">
                    <div class="absolute top-4 left-4 w-32 h-32 bg-white/10 rounded-full blur-2xl animate-pulse"></div>
                    <div class="absolute bottom-4 right-4 w-24 h-24 bg-white/10 rounded-full blur-2xl animate-pulse delay-75"></div>
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
                </div>
                
                <div class="relative z-10">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl border-2 border-white/30">
                            <i class="fas fa-edit text-white text-2xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-white mr-4">بيانات المستند</h2>
                        <div class="ml-auto">
                            <span class="bg-white/20 backdrop-blur-sm text-white text-sm font-medium px-4 py-2 rounded-full border-2 border-white/30">
                                <i class="fas fa-magic ml-2"></i>
                                جاهز للإنشاء
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('document-templates.generate', $template) }}" method="POST" class="p-10">
                @csrf
                
                <!-- Document Title with Enhanced Styling -->
                <div class="mb-10">
                    <label for="title" class="block text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl mr-3">
                            <i class="fas fa-heading text-white text-sm"></i>
                        </div>
                        عنوان المستند
                        <span class="text-red-500 ml-2 animate-pulse">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="title"
                               name="title" 
                               required 
                               class="w-full px-6 py-5 text-xl border-2 border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white/50 backdrop-blur-sm"
                               placeholder="أدخل عنوان المستند هنا...">
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                            <i class="fas fa-file-alt text-gray-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Variables Section with Enhanced Design -->
                @if($template->variables && count($template->variables) > 0)
                    <div class="mb-10">
                        <div class="flex items-center mb-8">
                            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl mr-4 shadow-lg">
                                <i class="fas fa-list text-white text-lg"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mr-4">بيانات المتغيرات</h3>
                            <span class="bg-gradient-to-r from-purple-500 to-pink-600 text-white text-sm font-bold px-6 py-3 rounded-full shadow-lg animate-bounce">
                                {{ count($template->variables) }} متغيرات
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            @foreach($template->variables as $index => $variable)
                                <div class="group relative">
                                    <!-- Variable Card with Hover Effects -->
                                    <div class="bg-gradient-to-br from-gray-50 to-white p-8 rounded-2xl border-2 border-gray-200 hover:border-purple-300 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                                        <div class="flex items-center mb-6">
                                            <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl mr-4 shadow-md group-hover:scale-110 transition-transform duration-300">
                                                <span class="text-white font-bold text-lg">{{ $index + 1 }}</span>
                                            </div>
                                            <label class="text-lg font-bold text-gray-900 flex-1">
                                                {{ $variable['name'] }}
                                            </label>
                                            <span class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold px-3 py-2 rounded-full shadow-md">
                                                {{ $variable['type'] }}
                                            </span>
                                        </div>
                                        
                                        <!-- Enhanced Input Fields -->
                                        @if($variable['type'] == 'text')
                                            <div class="relative">
                                                <input type="text" 
                                                       name="data[{{ $variable['name'] }}]" 
                                                       class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 bg-white/70 backdrop-blur-sm"
                                                       placeholder="أدخل {{ $variable['name'] }} هنا...">
                                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                                    <i class="fas fa-keyboard text-gray-400"></i>
                                                </div>
                                            </div>
                                        @elseif($variable['type'] == 'date')
                                            <div class="relative">
                                                <input type="date" 
                                                       name="data[{{ $variable['name'] }}]" 
                                                       class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 bg-white/70 backdrop-blur-sm">
                                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                                    <i class="fas fa-calendar text-gray-400"></i>
                                                </div>
                                            </div>
                                        @elseif($variable['type'] == 'number')
                                            <div class="relative">
                                                <input type="number" 
                                                       name="data[{{ $variable['name'] }}]" 
                                                       class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 bg-white/70 backdrop-blur-sm"
                                                       placeholder="أدخل الرقم هنا...">
                                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                                    <i class="fas fa-hashtag text-gray-400"></i>
                                                </div>
                                            </div>
                                        @elseif($variable['type'] == 'select' && isset($variable['options']))
                                            <div class="relative">
                                                <select name="data[{{ $variable['name'] }}]" 
                                                        class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 bg-white/70 backdrop-blur-sm appearance-none">
                                                    <option value="">اختر من القائمة...</option>
                                                    @foreach($variable['options'] as $option)
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                                </div>
                                            </div>
                                        @else
                                            <div class="relative">
                                                <textarea name="data[{{ $variable['name'] }}]" 
                                                          rows="4"
                                                          class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 bg-white/70 backdrop-blur-sm resize-none"
                                                          placeholder="أدخل {{ $variable['name'] }} هنا..."></textarea>
                                                <div class="absolute left-4 top-4">
                                                    <i class="fas fa-align-left text-gray-400"></i>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <!-- Enhanced No Variables Alert -->
                    <div class="mb-10 bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-l-amber-400 p-8 rounded-2xl shadow-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-12 h-12 bg-amber-400 rounded-xl">
                                    <i class="fas fa-info-circle text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="mr-6">
                                <h3 class="text-lg font-bold text-amber-800 mb-2">لا توجد متغيرات محددة</h3>
                                <p class="text-amber-700">
                                    سيتم إنشاء المستند بالمحتوى الثابت الموجود في القالب.
                                </p>
                            </div>
                        </div>
                        <input type="hidden" name="data" value="[]">
                    </div>
                @endif

                <!-- Enhanced Submit Section -->
                <div class="flex justify-between items-center pt-10 border-t-2 border-gray-200">
                    <div class="flex items-center text-gray-600">
                        <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-xl mr-3">
                            <i class="fas fa-shield-alt text-green-600"></i>
                        </div>
                        <span class="text-sm font-medium">بياناتك آمنة ومشفرة</span>
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('document-templates.show', $template) }}" 
                           class="px-8 py-4 border-2 border-gray-300 text-gray-700 rounded-2xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 transform hover:-translate-y-1 flex items-center">
                            <i class="fas fa-times ml-2"></i>
                            إلغاء
                        </a>
                        <button type="submit" 
                                class="bg-gradient-to-r from-green-500 via-emerald-500 to-teal-600 text-white px-10 py-4 rounded-2xl hover:from-green-600 hover:via-emerald-600 hover:to-teal-700 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:-translate-y-2 flex items-center">
                            <i class="fas fa-file-pdf ml-3 text-xl"></i>
                            <span class="text-lg font-bold">توليد المستند</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Floating Elements -->
        <div class="fixed bottom-8 right-8 flex flex-col gap-4">
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-2xl p-4 border border-white/20">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-1">{{ count($template->variables ?? []) }}</div>
                    <div class="text-sm text-gray-600">متغيرات</div>
                </div>
            </div>
            <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-2xl p-4 border border-white/20 hover:bg-white transition-all duration-300">
                <i class="fas fa-arrow-up text-gray-600 text-xl"></i>
            </button>
        </div>
    </div>
</div>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.delay-75 {
    animation-delay: 75ms;
}

.delay-150 {
    animation-delay: 150ms;
}
</style>
@endsection
