@extends('admin.layouts.admin')

@section('title', 'التوقيعات الرقمية')
@section('page-title', 'التوقيعات الرقمية')

@section('content')
    <!-- Page Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-3xl shadow-2xl p-8 text-white relative overflow-hidden">
            <!-- Animated Background Pattern -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -mr-48 -mt-48 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full -ml-32 -mb-32 animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/2 w-48 h-48 bg-yellow-300 rounded-full -ml-24 -mt-24 animate-pulse" style="animation-delay: 2s;"></div>
            </div>
            
            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 opacity-10">
                <div class="h-full w-full" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
            </div>
            
            <div class="relative z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="bg-white bg-opacity-25 rounded-2xl p-4 ml-4 backdrop-blur-sm border border-white border-opacity-40 shadow-xl transform hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-signature text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-5xl font-black text-white mb-2 bg-clip-text text-transparent bg-gradient-to-r from-white to-yellow-200">التوقيعات الرقمية</h1>
                                <p class="text-white text-xl font-medium">إدارة التوقيعات الرقمية بأسلوب احترافي</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-6 mt-6">
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">إجمالي التوقيعات:</span>
                                <span class="text-lg font-black text-white ml-2">{{ $signatures->total() }}</span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">في الانتظار:</span>
                                <span class="text-lg font-black text-white ml-2">{{ $signatures->where('status', 'pending')->count() }}</span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">الموقّعة:</span>
                                <span class="text-lg font-black text-white ml-2">{{ $signatures->where('status', 'signed')->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-reverse space-x-4">
                        <a href="/document-signatures/create" class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-2xl text-sm font-bold hover:bg-yellow-50 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-yellow-300/50">
                            <i class="fas fa-plus ml-2"></i>
                            طلب توقيع جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Search and Filters -->

    <div class="bg-white rounded-lg shadow-md p-4 mb-6">

        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div class="md:col-span-2">

                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن توقيع..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

            </div>

            <div>

                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                    <option value="">جميع الحالات</option>

                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>

                    <option value="signed" {{ request('status') == 'signed' ? 'selected' : '' }}>موقّع</option>

                    <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>ملغي</option>

                </select>

            </div>

            <div>

                <select name="signature_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                    <option value="">جميع الأنواعف</option>

                    <option value="digital" {{ request('signature_type') == 'digital' ? 'selected' : '' }}>رقمي</option>

                    <option value="electronic" {{ request('signature_type') == 'electronic' ? 'selected' : '' }}>إلكتروني</option>

                    <option value="handwritten" {{ request('signature_type') == 'handwritten' ? 'selected' : '' }}>يدوي</option>

                    <option value="stamp" {{ request('signature_type') == 'stamp' ? 'selected' : '' }}>ختم</option>

                </select>

            </div>

            <div>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 w-full">

                    <i class="fas fa-search ml-2"></i>بحث

                </button>

            </div>

        </form>

    </div>



    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
        <!-- Pending Signatures -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-yellow-500 to-orange-400 rounded-2xl blur opacity-15 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-2xl shadow-xl border-2 border-yellow-200 p-6 transform hover:scale-105 transition-all duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl p-3 group-hover:rotate-12 transition-transform duration-500 border border-yellow-200">
                        <i class="fas fa-clock text-3xl text-transparent bg-clip-text bg-gradient-to-r from-yellow-500 to-orange-400"></i>
                    </div>
                    <div class="bg-gradient-to-r from-yellow-500 to-orange-400 rounded-lg px-3 py-1 text-white text-sm font shadow">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">في الانتظار</span>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-black mb-2">التوقيعات المنتظرة</h3>
                <p class="text-gray-600 text-sm mb-4">التوقيعات اللي محتاجة موافقة</p>
                
                <div class="mb-4">
                    <p class="text-gray-700 font-semibold mb-2 text-sm">الإحصائيات:</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">{{ $signatures->where('status', 'pending')->count() }} توقيع</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">جاري المراجعة</span>
                    </div>
                </div>
                
                <div class="flex space-x-reverse space-x-2">
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg text-xs font-medium hover:from-blue-600 hover:to-cyan-500 transition-all duration-300 transform hover:scale-105 shadow">
                        <i class="fas fa-eye ml-1"></i>
                        عرض
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-400 text-white rounded-lg text-xs font-medium hover:from-green-600 hover:to-emerald-500 transition-all duration-300 transform hover:scale-105 shadow">
                        <i class="fas fa-cog ml-1"></i>
                        إعدادات
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Signed Signatures -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-green-500 to-emerald-400 rounded-2xl blur opacity-15 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-2xl shadow-xl border-2 border-green-200 p-6 transform hover:scale-105 transition-all duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-3 group-hover:rotate-12 transition-transform duration-500 border border-green-200">
                        <i class="fas fa-signature text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-500 to-emerald-400"></i>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-emerald-400 rounded-lg px-3 py-1 text-white text-sm font shadow">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">موقّعة</span>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-black mb-2">التوقيعات المكتملة</h3>
                <p class="text-gray-600 text-sm mb-4">التوقيعات اللي اتمتت بنجاح</p>
                
                <div class="mb-4">
                    <p class="text-gray-700 font-semibold mb-2 text-sm">الإحصائيات:</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">{{ $signatures->where('status', 'signed')->count() }} توقيع</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">مكتملة</span>
                    </div>
                </div>
                
                <div class="flex space-x-reverse space-x-2">
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg text-xs font-medium hover:from-blue-600 hover:to-cyan-500 transition-all duration-300 transform hover:scale-105 shadow">
                        <i class="fas fa-eye ml-1"></i>
                        عرض
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-400 text-white rounded-lg text-xs font-medium hover:from-green-600 hover:to-emerald-500 transition-all duration-300 transform hover:scale-105 shadow">
                        <i class="fas fa-download ml-1"></i>
                        تحميل
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Revoked Signatures -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-red-500 to-pink-400 rounded-2xl blur opacity-15 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-2xl shadow-xl border-2 border-red-200 p-6 transform hover:scale-105 transition-all duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-xl p-3 group-hover:rotate-12 transition-transform duration-500 border border-red-200">
                        <i class="fas fa-times-circle text-3xl text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-pink-400"></i>
                    </div>
                    <div class="bg-gradient-to-r from-red-500 to-pink-400 rounded-lg px-3 py-1 text-white text-sm font shadow">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">ملغية</span>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-black mb-2">التوقيعات الملغية</h3>
                <p class="text-gray-600 text-sm mb-4">التوقيعات اللي تم إلغاؤها</p>
                
                <div class="mb-4">
                    <p class="text-gray-700 font-semibold mb-2 text-sm">الإحصائيات:</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">{{ $signatures->where('status', 'revoked')->count() }} توقيع</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">ملغاة</span>
                    </div>
                </div>
                
                <div class="flex space-x-reverse space-x-2">
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg text-xs font-medium hover:from-blue-600 hover:to-cyan-500 transition-all duration-300 transform hover:scale-105 shadow">
                        <i class="fas fa-eye ml-1"></i>
                        عرض
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-red-400 to-red-500 text-white rounded-lg text-xs font-medium hover:from-red-500 hover:to-red-600 transition-all duration-300 transform hover:scale-105 shadow">
                        <i class="fas fa-trash ml-1"></i>
                        حذف
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Total Statistics -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-blue-500 to-cyan-400 rounded-2xl blur opacity-15 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-2xl shadow-xl border-2 border-blue-200 p-6 transform hover:scale-105 transition-all duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-3 group-hover:rotate-12 transition-transform duration-500 border border-blue-200">
                        <i class="fas fa-chart-bar text-3xl text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-cyan-400"></i>
                    </div>
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-400 rounded-lg px-3 py-1 text-white text-sm font shadow">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">الإجمالي</span>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-black mb-2">إجمالي التوقيعات</h3>
                <p class="text-gray-600 text-sm mb-4">جميع التوقيعات في النظام</p>
                
                <div class="mb-4">
                    <p class="text-gray-700 font-semibold mb-2 text-sm">الإحصائيات:</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">{{ $signatures->total() }} توقيع</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">الكل</span>
                    </div>
                </div>
                
                <div class="flex space-x-reverse space-x-2">
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg text-xs font-medium hover:from-blue-600 hover:to-cyan-500 transition-all duration-300 transform hover:scale-105 shadow">
                        <i class="fas fa-chart-line ml-1"></i>
                        تحليل
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-400 text-white rounded-lg text-xs font-medium hover:from-green-600 hover:to-emerald-500 transition-all duration-300 transform hover:scale-105 shadow">
                        <i class="fas fa-download ml-1"></i>
                        تصدير
                    </button>
                </div>
            </div>
        </div>
    </div>



    <!-- Signatures Table -->

    <div class="bg-white rounded-lg shadow-md overflow-hidden">

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوثيقة</th>

                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الموقّع</th>

                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>

                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع التوقيع</th>

                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>

                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التوقيع</th>

                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>

                    </tr>

                </thead>

                <tbody class="bg-white divide-y divide-gray-200">

                    @forelse($signatures as $signature)

                        <tr>

                            <td class="px-6 py-4 whitespace-nowrap">

                                <div class="text-sm font-medium text-gray-900">{{ $signature->document->title }}</div>

                                <div class="text-sm text-gray-500">{{ $signature->document->file_name }}</div>

                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">

                                {{ $signature->signer_name }}

                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">

                                {{ $signature->signer_email }}

                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">

                                @switch($signature->signature_type)

                                    @case('digital')

                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">

                                            رقمي

                                        </span>

                                        @break

                                    @case('electronic')

                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">

                                            إلكتروني

                                        </span>

                                        @break

                                    @case('handwritten')

                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">

                                            يدوي

                                        </span>

                                        @break

                                    @case('stamp')

                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">

                                            ختم

                                        </span>

                                        @break

                                @endswitch

                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">

                                @switch($signature->status)

                                    @case('pending')

                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">

                                            في الانتظار

                                        </span>

                                        @break

                                    @case('signed')

                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">

                                            موقّع

                                        </span>

                                        @break

                                    @case('revoked')

                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">

                                            ملغي

                                        </span>

                                        @break

                                @endswitch

                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">

                                {{ $signature->signed_at ? $signature->signed_at->format('Y-m-d H:i') : '---' }}

                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">

                                <a href="/document-signatures/{{ $signature->id }}" class="text-blue-600 hover:text-blue-900 ml-2">

                                    <i class="fas fa-eye"></i>

                                </a>

                                @if($signature->status === 'pending')

                                    <a href="/document-signatures/{{ $signature->id }}/verify" class="text-green-600 hover:text-green-900 ml-2">

                                        <i class="fas fa-check"></i>

                                    </a>

                                    <a href="/document-signatures/{{ $signature->id }}/revoke" class="text-red-600 hover:text-red-900 ml-2">

                                        <i class="fas fa-times"></i>

                                    </a>

                                @endif

                                @if($signature->status === 'signed')

                                    <a href="/document-signatures/{{ $signature->id }}/download" class="text-gray-600 hover:text-gray-900 ml-2">

                                        <i class="fas fa-download"></i>

                                    </a>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">

                                لا توجد توقيعات

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        

        <!-- Pagination -->

        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">

            {{ $signatures->links() }}

        </div>

    </div>



    <!-- Bulk Actions -->

    <div class="bg-white rounded-lg shadow-md p-6 mt-6">

        <h2 class="text-lg font-semibold mb-4">إجراءات جماعية</h2>

        

        <div class="flex flex-wrap gap-3">

            <button onclick="showBulkSignModal()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">

                <i class="fas fa-users ml-2"></i>توقيع جماعي

            </button>

            <button onclick="exportSignatures()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">

                <i class="fas fa-download ml-2"></i>تصدير التوقيعات

            </button>

            <button onclick="sendReminders()" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">

                <i class="fas fa-bell ml-2"></i>إرسال تذكيرات

            </button>

        </div>

    </div>

</div>



<!-- Bulk Sign Modal -->

<div id="bulkSignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full z-50">

    <div class="flex min-h-full items-center justify-center p-4">

        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">

            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">

                <h3 class="text-lg font-semibold text-gray-900">توقيع جماعي</h3>

                <button onclick="hideBulkSignModal()" class="text-gray-400 hover:text-gray-600">

                    <i class="fas fa-times"></i>

                </button>

            </div>

            

            <div class="p-6">

                <form id="bulkSignForm">

                    <div class="mb-4">

                        <label class="block text-sm font-medium text-gray-700 mb-2">اختر التوقيعات</label>

                        <div class="space-y-2 max-h-60 overflow-y-auto">

                            @foreach($signatures->where('status', 'pending') as $signature)

                                <div class="flex items-center p-2 border rounded">

                                    <input type="checkbox" name="signature_ids[]" value="{{ $signature->id }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                                    <label class="mr-3 text-sm text-gray-700">

                                        {{ $signature->signer_name }} - {{ $signature->document->title }}

                                    </label>

                                </div>

                            @endforeach

                        </div>

                    </div>

                    

                    <div class="mb-4">

                        <label class="block text-sm font-medium text-gray-700 mb-2">رسالة مخصصة (اختياري)</label>

                        <textarea name="message" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل رسالة مخصصة للموقيعين..."></textarea>

                    </div>

                    

                    <div class="flex justify-end space-x-4 space-x-reverse">

                        <button type="button" onclick="hideBulkSignModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">

                            إلغاء

                        </button>

                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">

                            <i class="fas fa-users ml-2"></i>توقيع المحدد

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

    </div>
@endsection

@section('scripts')

<script>

function showBulkSignModal() {

    document.getElementById('bulkSignModal').classList.remove('hidden');

}



function hideBulkSignModal() {

    document.getElementById('bulkSignModal').classList.add('hidden');

}



function exportSignatures() {

    window.location.href = '/document-signatures/export';

}



function sendReminders() {

    // Implementation for sending reminders

    alert('سيتم إرسال التذكيرات إلى جميع التوقيعات في الانتظار');

}



// Bulk sign form submission

document.getElementById('bulkSignForm').addEventListener('submit', function(e) {

    e.preventDefault();

    

    const formData = new FormData(this);

    const signatureIds = formData.getAll('signature_ids[]');

    

    if (signatureIds.length === 0) {

        alert('يرجى اختيار على الأقل توقيع واحد');

        return;

    }

    

    // Submit bulk sign request

    fetch('/document-signatures/bulk', {

        method: 'POST',

        body: formData,

        headers: {

            'X-CSRF-TOKEN': '{{ csrf_token() }}',

            'Content-Type': 'application/x-www-form-urlencoded',

        },

    })

    .then(response => response.json())

    .then(data => {

        if (data.success) {

            hideBulkSignModal();

            location.reload();

        } else {

            alert('حدث خطأء: ' + data.message);

        }

    })

    .catch(error => {

        console.error('Error:', error);

        alert('حدث خطأء أثناء إرسال الطلب');

    });

});

</script>

@endsection

