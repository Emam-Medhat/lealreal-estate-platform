@extends('admin.layouts.admin')

@section('title', 'توصيات الأداء (Recommendations)')
@section('page-title', 'توصيات التحسين')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">توصيات تحسين الأداء</h2>
            <p class="text-gray-600 mt-2">تحليلات ذكية ومقترحات لتحسين سرعة وكفاءة النظام.</p>
        </div>

        @if(empty($recommendations))
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
                <div class="bg-green-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-green-500 text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">النظام يعمل بكفاءة عالية</h3>
                <p class="text-gray-600">لم يتم العثور على مشاكل أداء حرجة في الوقت الحالي.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.performance.dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                        العودة للوحة الأداء
                    </a>
                </div>
            </div>
        @else
            <!-- Recommendations List -->
            <div class="grid grid-cols-1 gap-6 mb-8">
                @foreach($recommendations as $rec)
                    <div class="bg-white rounded-2xl shadow-md border-l-4 {{ $rec['severity'] == 'high' ? 'border-red-500' : ($rec['severity'] == 'medium' ? 'border-amber-500' : 'border-blue-500') }} p-6 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 ml-4">
                                @if($rec['severity'] == 'high')
                                    <div class="bg-red-100 p-3 rounded-full">
                                        <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                                    </div>
                                @elseif($rec['severity'] == 'medium')
                                    <div class="bg-amber-100 p-3 rounded-full">
                                        <i class="fas fa-exclamation-triangle text-amber-600 text-xl"></i>
                                    </div>
                                @else
                                    <div class="bg-blue-100 p-3 rounded-full">
                                        <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-bold text-gray-800">{{ $rec['message'] }}</h3>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $rec['severity'] == 'high' ? 'bg-red-100 text-red-700' : ($rec['severity'] == 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                        {{ ucfirst($rec['severity']) }} Priority
                                    </span>
                                </div>
                                <p class="text-gray-600 mb-4">{{ $rec['action'] }}</p>
                                
                                <div class="bg-gray-50 rounded-lg p-3 text-sm border border-gray-100">
                                    <span class="font-bold text-gray-700">التصنيف:</span> 
                                    <span class="text-gray-600 ml-2">{{ ucfirst($rec['type']) }} Optimization</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-6">أدوات التحسين</h3>
            <div class="flex flex-wrap gap-4">
                <form action="{{ route('admin.performance.clear_cache') }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-xl transition-all duration-300 flex items-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <i class="fas fa-broom ml-2"></i>
                        تنظيف الكاش
                    </button>
                </form>
                
                <a href="{{ route('admin.performance.queries') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2.5 rounded-xl transition-all duration-300 flex items-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-search ml-2"></i>
                    تحليل الاستعلامات
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
