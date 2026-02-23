@extends('admin.layouts.admin')

@section('title', 'لوحة تحكم الوحدات')
@section('page-title', 'لوحة تحكم الوحدات')

@section('content')
<div class="container mx-auto px-4 py-8">
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
                                <i class="fas fa-cubes text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-5xl font-black text-white mb-2 bg-clip-text text-transparent bg-gradient-to-r from-white to-yellow-200">لوحة تحكم الوحدات</h1>
                                <p class="text-white text-xl font-medium">إدارة وتفعيل الوحدات النظامية بأسلوب احترافي</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-6 mt-6">
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">الوحدات النشطة:</span>
                                <span class="text-lg font-black text-white ml-2">6</span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">متوسط الإصدار:</span>
                                <span class="text-lg font-black text-white ml-2">2.0</span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">صحة النظام:</span>
                                <span class="text-lg font-black text-white ml-2">100%</span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">التشغيل:</span>
                                <span class="text-lg font-black text-white ml-2">24/7</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-reverse space-x-4">
                        <button class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-2xl text-sm font-bold hover:bg-yellow-50 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-yellow-300/50">
                            <i class="fas fa-sync-alt ml-2"></i>
                            تحديث الوحدات
                        </button>
                        <button class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-2xl text-sm font-bold hover:from-yellow-500 hover:to-orange-600 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-yellow-400 transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-orange-500/50">
                            <i class="fas fa-download ml-2"></i>
                            تصدير الإعدادات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10" id="modulesGrid">
        <!-- Core Real Estate Module -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-green-500 to-emerald-400 rounded-2xl blur opacity-15 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-2xl shadow-xl border-2 border-green-200 p-6 transform hover:scale-105 transition-all duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-3 group-hover:rotate-12 transition-transform duration-500 border border-green-200">
                        <i class="fas fa-home text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-500 to-emerald-400"></i>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-emerald-400 rounded-lg px-3 py-1 text-white text-sm font shadow">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">نشط</span>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-black mb-2">Core Real Estate</h3>
                <p class="text-gray-600 text-sm mb-4">Property management, listings, and basic features</p>
                
                <div class="mb-4">
                    <p class="text-gray-700 font-semibold mb-2 text-sm">الروابط:</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">Properties</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">Users</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">Agents</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">+3 أكثر</span>
                    </div>
                </div>
                
                <div class="flex space-x-reverse space-x-2">
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg text-xs font-medium hover:from-blue-600 hover:to-cyan-500 transition-all duration-300 transform hover:scale-105 shadow view-module" data-module-key="core">
                        <i class="fas fa-eye ml-1"></i>
                        عرض
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-400 text-white rounded-lg text-xs font-medium hover:from-green-600 hover:to-emerald-500 transition-all duration-300 transform hover:scale-105 shadow configure-module" data-module-key="core">
                        <i class="fas fa-cog ml-1"></i>
                        إعدادات
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-red-400 to-red-500 text-white rounded-lg text-xs font-medium hover:from-red-500 hover:to-red-600 transition-all duration-300 transform hover:scale-105 shadow toggle-module" data-module-key="core" data-status="active">
                        <i class="fas fa-pause ml-1"></i>
                        إيقاف
                    </button>
                </div>
            </div>
        </div>

        <!-- Global Services Module -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-blue-500 to-cyan-400 rounded-2xl blur opacity-15 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-2xl shadow-xl border-2 border-blue-200 p-6 transform hover:scale-105 transition-all duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-3 group-hover:rotate-12 transition-transform duration-500 border border-blue-200">
                        <i class="fas fa-globe text-3xl text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-cyan-400"></i>
                    </div>
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-400 rounded-lg px-3 py-1 text-white text-sm font shadow">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">نشط</span>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-black mb-2">Global Services</h3>
                <p class="text-gray-600 text-sm mb-4">Multi-currency, multi-language, and international features</p>
                
                <div class="mb-4">
                    <p class="text-gray-700 font-semibold mb-2 text-sm">الروابط:</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Currency Exchange</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Multi-Language</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Gamification</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">+3 أكثر</span>
                    </div>
                </div>
                
                <div class="flex space-x-reverse space-x-2">
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg text-xs font-medium hover:from-blue-600 hover:to-cyan-500 transition-all duration-300 transform hover:scale-105 shadow view-module" data-module-key="global_services">
                        <i class="fas fa-eye ml-1"></i>
                        عرض
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-400 text-white rounded-lg text-xs font-medium hover:from-green-600 hover:to-emerald-500 transition-all duration-300 transform hover:scale-105 shadow configure-module" data-module-key="global_services">
                        <i class="fas fa-cog ml-1"></i>
                        إعدادات
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-red-400 to-red-500 text-white rounded-lg text-xs font-medium hover:from-red-500 hover:to-red-600 transition-all duration-300 transform hover:scale-105 shadow toggle-module" data-module-key="global_services" data-status="active">
                        <i class="fas fa-pause ml-1"></i>
                        إيقاف
                    </button>
                </div>
            </div>
        </div>

        <!-- Advanced Features Module -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-orange-500 to-red-400 rounded-2xl blur opacity-15 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-2xl shadow-xl border-2 border-orange-200 p-6 transform hover:scale-105 transition-all duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-3 group-hover:rotate-12 transition-transform duration-500 border border-orange-200">
                        <i class="fas fa-rocket text-3xl text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-400"></i>
                    </div>
                    <div class="bg-gradient-to-r from-orange-500 to-red-400 rounded-lg px-3 py-1 text-white text-sm font shadow">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200">نشط</span>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-black mb-2">Advanced Features</h3>
                <p class="text-gray-600 text-sm mb-4">IoT integration, security, and advanced analytics</p>
                
                <div class="mb-4">
                    <p class="text-gray-700 font-semibold mb-2 text-sm">الروابط:</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200">IoT Integration</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200">Advanced Security</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200">Analytics</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">+2 أكثر</span>
                    </div>
                </div>
                
                <div class="flex space-x-reverse space-x-2">
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg text-xs font-medium hover:from-blue-600 hover:to-cyan-500 transition-all duration-300 transform hover:scale-105 shadow view-module" data-module-key="advanced_features">
                        <i class="fas fa-eye ml-1"></i>
                        عرض
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-400 text-white rounded-lg text-xs font-medium hover:from-green-600 hover:to-emerald-500 transition-all duration-300 transform hover:scale-105 shadow configure-module" data-module-key="advanced_features">
                        <i class="fas fa-cog ml-1"></i>
                        إعدادات
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-red-400 to-red-500 text-white rounded-lg text-xs font-medium hover:from-red-500 hover:to-red-600 transition-all duration-300 transform hover:scale-105 shadow toggle-module" data-module-key="advanced_features" data-status="active">
                        <i class="fas fa-pause ml-1"></i>
                        إيقاف
                    </button>
                </div>
            </div>
        </div>

        <!-- Communication Module -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-purple-500 to-pink-400 rounded-2xl blur opacity-15 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-2xl shadow-xl border-2 border-purple-200 p-6 transform hover:scale-105 transition-all duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-3 group-hover:rotate-12 transition-transform duration-500 border border-purple-200">
                        <i class="fas fa-comments text-3xl text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-pink-400"></i>
                    </div>
                    <div class="bg-gradient-to-r from-purple-500 to-pink-400 rounded-lg px-3 py-1 text-white text-sm font shadow">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">نشط</span>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-black mb-2">Communication</h3>
                <p class="text-gray-600 text-sm mb-4">Real-time messaging, notifications, and collaboration</p>
                
                <div class="mb-4">
                    <p class="text-gray-700 font-semibold mb-2 text-sm">الروابط:</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">Messaging</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">Notifications</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">Video Calls</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">+2 أكثر</span>
                    </div>
                </div>
                
                <div class="flex space-x-reverse space-x-2">
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg text-xs font-medium hover:from-blue-600 hover:to-cyan-500 transition-all duration-300 transform hover:scale-105 shadow view-module" data-module-key="communication">
                        <i class="fas fa-eye ml-1"></i>
                        عرض
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-400 text-white rounded-lg text-xs font-medium hover:from-green-600 hover:to-emerald-500 transition-all duration-300 transform hover:scale-105 shadow configure-module" data-module-key="communication">
                        <i class="fas fa-cog ml-1"></i>
                        إعدادات
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-red-400 to-red-500 text-white rounded-lg text-xs font-medium hover:from-red-500 hover:to-red-600 transition-all duration-300 transform hover:scale-105 shadow toggle-module" data-module-key="communication" data-status="active">
                        <i class="fas fa-pause ml-1"></i>
                        إيقاف
                    </button>
                </div>
            </div>
        </div>

        <!-- Marketplace Module -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-red-500 to-pink-400 rounded-2xl blur opacity-15 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-2xl shadow-xl border-2 border-red-200 p-6 transform hover:scale-105 transition-all duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-xl p-3 group-hover:rotate-12 transition-transform duration-500 border border-red-200">
                        <i class="fas fa-store text-3xl text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-pink-400"></i>
                    </div>
                    <div class="bg-gradient-to-r from-red-500 to-pink-400 rounded-lg px-3 py-1 text-white text-sm font shadow">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">نشط</span>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-black mb-2">Marketplace</h3>
                <p class="text-gray-600 text-sm mb-4">Property marketplace, auctions, and trading platform</p>
                
                <div class="mb-4">
                    <p class="text-gray-700 font-semibold mb-2 text-sm">الروابط:</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">Marketplace</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">Auctions</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">Services</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">+2 أكثر</span>
                    </div>
                </div>
                
                <div class="flex space-x-reverse space-x-2">
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg text-xs font-medium hover:from-blue-600 hover:to-cyan-500 transition-all duration-300 transform hover:scale-105 shadow view-module" data-module-key="marketplace">
                        <i class="fas fa-eye ml-1"></i>
                        عرض
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-400 text-white rounded-lg text-xs font-medium hover:from-green-600 hover:to-emerald-500 transition-all duration-300 transform hover:scale-105 shadow configure-module" data-module-key="marketplace">
                        <i class="fas fa-cog ml-1"></i>
                        إعدادات
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-red-400 to-red-500 text-white rounded-lg text-xs font-medium hover:from-red-500 hover:to-red-600 transition-all duration-300 transform hover:scale-105 shadow toggle-module" data-module-key="marketplace" data-status="active">
                        <i class="fas fa-pause ml-1"></i>
                        إيقاف
                    </button>
                </div>
            </div>
        </div>

        <!-- Admin Tools Module -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-gray-500 to-gray-700 rounded-2xl blur opacity-15 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-2xl shadow-xl border-2 border-gray-300 p-6 transform hover:scale-105 transition-all duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-3 group-hover:rotate-12 transition-transform duration-500 border border-gray-300">
                        <i class="fas fa-cogs text-3xl text-transparent bg-clip-text bg-gradient-to-r from-gray-500 to-gray-700"></i>
                    </div>
                    <div class="bg-gradient-to-r from-gray-500 to-gray-700 rounded-lg px-3 py-1 text-white text-sm font shadow">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-300">نشط</span>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-black mb-2">Admin Tools</h3>
                <p class="text-gray-600 text-sm mb-4">Administration, configuration, and system management</p>
                
                <div class="mb-4">
                    <p class="text-gray-700 font-semibold mb-2 text-sm">الروابط:</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-300">Admin Panel</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-300">System Settings</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-300">System Logs</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-300">+2 أكثر</span>
                    </div>
                </div>
                
                <div class="flex space-x-reverse space-x-2">
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg text-xs font-medium hover:from-blue-600 hover:to-cyan-500 transition-all duration-300 transform hover:scale-105 shadow view-module" data-module-key="admin_tools">
                        <i class="fas fa-eye ml-1"></i>
                        عرض
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-400 text-white rounded-lg text-xs font-medium hover:from-green-600 hover:to-emerald-500 transition-all duration-300 transform hover:scale-105 shadow configure-module" data-module-key="admin_tools">
                        <i class="fas fa-cog ml-1"></i>
                        إعدادات
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-red-400 to-red-500 text-white rounded-lg text-xs font-medium hover:from-red-500 hover:to-red-600 transition-all duration-300 transform hover:scale-105 shadow toggle-module" data-module-key="admin_tools" data-status="active">
                        <i class="fas fa-pause ml-1"></i>
                        إيقاف
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Details Modal -->
    <div class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" id="moduleDetailsModal">
        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-4xl transform scale-95 opacity-0 transition-all duration-300">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">تفاصيلات الوحدة</h3>
                <button class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeModal('moduleDetailsModal')">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 border-2 border-gray-200" id="moduleDetailsContent">
                <!-- Module details will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const apiRoutes = {
    list: "{{ route('modules.list') }}",
    details: "{{ route('modules.details', ['moduleKey' => ':key']) }}",
    toggle: "{{ route('modules.toggle', ['moduleKey' => ':key']) }}",
    configure: "{{ route('modules.configure', ['moduleKey' => ':key']) }}"
};

$(document).ready(function() {
    loadModules();
});

function loadModules() {
    $('#modulesGrid').html(`
        <div class="col-12 text-center">
            <div class="bg-white rounded-2xl shadow-xl p-12 border-2 border-gray-200">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full mb-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-4 border-white border-t-transparent"></div>
                </div>
                <p class="text-xl text-black font-bold">جاري تحميل الوحدات...</p>
            </div>
        </div>
    `);
    
    $.ajax({
        url: apiRoutes.list,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                renderModules(response.modules);
            } else {
                showMockModules();
            }
        },
        error: function() {
            showMockModules();
        }
    });
}

function renderModules(modules) {
    let html = '';
    Object.keys(modules).forEach(key => {
        const module = modules[key];
        html += createModuleCard(key, module);
    });
    $('#modulesGrid').html(html);
}

function createModuleCard(moduleKey, module) {
    const colorMap = {
        'primary': 'from-green-600 to-emerald-500',
        'success': 'from-blue-600 to-cyan-500', 
        'warning': 'from-orange-600 to-red-500',
        'info': 'from-purple-600 to-pink-500',
        'danger': 'from-red-600 to-pink-500',
        'dark': 'from-gray-600 to-gray-800'
    };
    
    const gradient = colorMap[module.color] || colorMap['primary'];
    const statusText = module.status === 'active' ? 'نشط' : 'غير نشط';
    const statusColor = module.status === 'active' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200';
    
    return `
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r ${gradient} rounded-2xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-2xl shadow-xl border-2 border-${module.color}-200 p-6 transform hover:scale-105 transition-all duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-${module.color}-50 to-${module.color}-100 rounded-xl p-3 group-hover:rotate-12 transition-transform duration-500 border-2 border-${module.color}-200">
                        <i class="${module.icon} text-3xl text-transparent bg-clip-text bg-gradient-to-r ${gradient}"></i>
                    </div>
                    <div class="bg-gradient-to-r ${gradient} rounded-lg px-3 py-1 text-white text-sm font-bold shadow-lg">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold ${statusColor} border-2 border-${module.color}-200">${statusText}</span>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-black mb-2">${module.name}</h3>
                <p class="text-gray-600 text-sm mb-4">${module.description}</p>
                
                <div class="mb-4">
                    <p class="text-gray-700 font-semibold mb-2 text-sm">الروابط:</p>
                    <div class="flex flex-wrap gap-2">
                        ${Object.keys(module.routes).slice(0, 3).map(route => 
                            `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-${module.color}-50 to-${module.color}-100 text-black border-2 border-${module.color}-200">${module.routes[route]}</span>`
                        ).join('')}
                        ${Object.keys(module.routes).length > 3 ? 
                            `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-black border-2 border-gray-200">+${Object.keys(module.routes).length - 3} أكثر</span>` : ''}
                    </div>
                </div>
                
                <div class="flex space-x-reverse space-x-2">
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-lg text-xs font-bold hover:from-blue-700 hover:to-cyan-600 transition-all duration-300 transform hover:scale-105 shadow-lg view-module" data-module-key="${moduleKey}">
                        <i class="fas fa-eye ml-1"></i>
                        عرض
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-500 text-white rounded-lg text-xs font-bold hover:from-green-700 hover:to-emerald-600 transition-all duration-300 transform hover:scale-105 shadow-lg configure-module" data-module-key="${moduleKey}">
                        <i class="fas fa-cog ml-1"></i>
                        إعدادات
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg text-xs font-bold hover:from-red-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105 shadow-lg toggle-module" data-module-key="${moduleKey}" data-status="${module.status}">
                        <i class="fas fa-${module.status === 'active' ? 'pause' : 'play'} ml-1"></i>
                        ${module.status === 'active' ? 'إيقاف' : 'تشغيل'}
                    </button>
                </div>
            </div>
        </div>
    `;
}

function showMockModules() {
    const mockModules = {
        'core': {
            'name': 'Core Real Estate',
            'description': 'Property management, listings, and basic features',
            'icon': 'fas fa-home',
            'color': 'primary',
            'status': 'active',
            'version': '1.0.0',
            'routes': {
                'properties': 'Properties',
                'users': 'Users',
                'agents': 'Agents',
                'companies': 'Companies',
                'leads': 'Leads',
                'investments': 'Investments'
            }
        },
        'global_services': {
            'name': 'Global Services',
            'description': 'Multi-currency, multi-language, and international features',
            'icon': 'fas fa-globe',
            'color': 'success',
            'status': 'active',
            'version': '2.0.0',
            'routes': {
                'currency': 'Currency Exchange',
                'language': 'Multi-Language',
                'gamification': 'Gamification',
                'blockchain': 'Blockchain & Web3',
                'ai_services': 'AI Services',
                'enterprise': 'Enterprise Solutions'
            }
        },
        'advanced_features': {
            'name': 'Advanced Features',
            'description': 'IoT integration, security, and advanced analytics',
            'icon': 'fas fa-rocket',
            'color': 'warning',
            'status': 'active',
            'version': '1.5.0',
            'routes': {
                'iot': 'IoT Integration',
                'security': 'Advanced Security',
                'analytics': 'Analytics',
                'automation': 'Automation',
                'ai': 'AI Integration'
            }
        },
        'communication': {
            'name': 'Communication',
            'description': 'Real-time messaging, notifications, and collaboration',
            'icon': 'fas fa-comments',
            'color': 'info',
            'status': 'active',
            'version': '2.1.0',
            'routes': {
                'messaging': 'Messaging',
                'notifications': 'Notifications',
                'video': 'Video Calls',
                'forums': 'Forums',
                'chat': 'Live Chat'
            }
        },
        'marketplace': {
            'name': 'Marketplace',
            'description': 'Property marketplace, auctions, and trading platform',
            'icon': 'fas fa-store',
            'color': 'danger',
            'status': 'active',
            'version': '1.8.0',
            'routes': {
                'marketplace': 'Marketplace',
                'auctions': 'Auctions',
                'services': 'Services',
                'reviews': 'Reviews',
                'deals': 'Hot Deals'
            }
        },
        'admin_tools': {
            'name': 'Admin Tools',
            'description': 'Administration, configuration, and system management',
            'icon': 'fas fa-cogs',
            'color': 'dark',
            'status': 'active',
            'version': '3.0.0',
            'routes': {
                'admin': 'Admin Panel',
                'settings': 'System Settings',
                'logs': 'System Logs',
                'backups': 'Backups',
                'monitoring': 'System Monitoring'
            }
        }
    };
    
    renderModules(mockModules);
}

$(document).on('click', '.view-module', function() {
    const moduleKey = $(this).data('module-key');
    viewModuleDetails(moduleKey);
});

$(document).on('click', '.configure-module', function() {
    const moduleKey = $(this).data('module-key');
    configureModule(moduleKey);
});

$(document).on('click', '.toggle-module', function() {
    const moduleKey = $(this).data('module-key');
    const currentStatus = $(this).data('status');
    toggleModule(moduleKey, currentStatus);
});

function viewModuleDetails(moduleKey) {
    $('#moduleDetailsContent').html(`
        <div class="text-center py-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full mb-4">
                <div class="animate-spin rounded-full h-8 w-8 border-4 border-white border-t-transparent"></div>
            </div>
            <p class="text-xl text-black font-bold">جاري تحميل التفاصيل...</p>
        </div>
    `);
    
    const modal = document.getElementById('moduleDetailsModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        modal.querySelector('.bg-white').classList.remove('scale-95', 'opacity-0');
        modal.querySelector('.bg-white').classList.add('scale-100', 'opacity-100');
    }, 10);
    
    $.ajax({
        url: apiRoutes.details.replace(':key', moduleKey),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                renderModuleDetails(response.module);
            } else {
                showMockModuleDetails(moduleKey);
            }
        },
        error: function() {
            showMockModuleDetails(moduleKey);
        }
    });
}

function renderModuleDetails(module) {
    const html = `
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-reverse space-x-4">
                    <div class="bg-gradient-to-br from-${module.color}-50 to-${module.color}-100 rounded-2xl p-4 border-2 border-${module.color}-200">
                        <i class="${module.icon} text-4xl text-transparent bg-clip-text bg-gradient-to-r from-${module.color}-600 to-${module.color}-500"></i>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold text-black">${module.name}</h4>
                        <p class="text-black text-lg">${module.description}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-${module.color}-100 text-${module.color}-800 border-2 border-${module.color}-200">
                        الإصدار: ${module.version}
                    </span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl p-6 border-2 border-gray-200">
                    <h5 class="text-lg font-bold text-black mb-4">الروابط المتاحة</h5>
                    <div class="space-y-2">
                        ${Object.keys(module.routes).map(route => 
                            `<div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border-2 border-gray-200">
                                <span class="text-black font-medium">${module.routes[route]}</span>
                                <i class="fas fa-arrow-left text-gray-400"></i>
                            </div>`
                        ).join('')}
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl p-6 border-2 border-gray-200">
                    <h5 class="text-lg font-bold text-black mb-4">الإحصائيات</h5>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-black">عدد المستخدمين</span>
                            <span class="text-black font-bold">${Math.floor(Math.random() * 10000)}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-black">العمليات اليومية</span>
                            <span class="text-black font-bold">${Math.floor(Math.random() * 1000)}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-black">معدل الاستخدام</span>
                            <span class="text-black font-bold">${Math.floor(Math.random() * 100)}%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-6 border-2 border-gray-200">
                <h5 class="text-lg font-bold text-black mb-4">النشاط الأخير</h5>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border-2 border-gray-200">
                        <span class="text-black">تحديث الوحدة</span>
                        <span class="text-black text-sm">منذ ساعتين</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border-2 border-gray-200">
                        <span class="text-black">صيانة دورية</span>
                        <span class="text-black text-sm">منذ 5 ساعات</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border-2 border-gray-200">
                        <span class="text-black">نسخ احتياطي</span>
                        <span class="text-black text-sm">منذ يوم</span>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#moduleDetailsContent').html(html);
}

function showMockModuleDetails(moduleKey) {
    const mockModuleDetails = {
        'core': {
            'name': 'Core Real Estate',
            'description': 'Property management, listings, and basic features',
            'icon': 'fas fa-home',
            'color': 'primary',
            'status': 'active',
            'version': '1.0.0',
            'routes': {
                'properties': 'Properties',
                'users': 'Users',
                'agents': 'Agents',
                'companies': 'Companies',
                'leads': 'Leads',
                'investments': 'Investments'
            }
        },
        'global_services': {
            'name': 'Global Services',
            'description': 'Multi-currency, multi-language, and international features',
            'icon': 'fas fa-globe',
            'color': 'success',
            'status': 'active',
            'version': '2.0.0',
            'routes': {
                'currency': 'Currency Exchange',
                'language': 'Multi-Language',
                'gamification': 'Gamification',
                'blockchain': 'Blockchain & Web3',
                'ai_services': 'AI Services',
                'enterprise': 'Enterprise Solutions'
            }
        }
    };
    
    const module = mockModuleDetails[moduleKey] || mockModuleDetails['core'];
    renderModuleDetails(module);
}

function configureModule(moduleKey) {
    showNotification(`جاري فتح إعدادات الوحدة: ${moduleKey}`, 'info');
    setTimeout(() => {
        window.location.href = apiRoutes.configure.replace(':key', moduleKey);
    }, 1000);
}

function toggleModule(moduleKey, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const statusText = newStatus === 'active' ? 'تفعيل' : 'إيقاف';
    
    const button = $(`.toggle-module[data-module-key="${moduleKey}"]`);
    const icon = button.find('i');
    const text = button.contents().filter(function() { return this.nodeType === 3; }).last();
    
    if (newStatus === 'active') {
        icon.removeClass('fa-play').addClass('fa-pause');
        text.replaceWith(' إيقاف');
        button.data('status', 'active');
    } else {
        icon.removeClass('fa-pause').addClass('fa-play');
        text.replaceWith(' تشغيل');
        button.data('status', 'inactive');
    }
    
    showNotification(`تم ${statusText} الوحدة: ${moduleKey}`, 'success');
    
    $.ajax({
        url: apiRoutes.toggle.replace(':key', moduleKey),
        method: 'POST',
        data: {
            status: newStatus,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showNotification(`تم ${statusText} الوحدة بنجاح`, 'success');
            } else {
                showNotification('فشل في تحديث حالة الوحدة', 'error');
            }
        },
        error: function() {
            showNotification('فشل في الاتصال بالخادم', 'error');
        }
    });
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.querySelector('.bg-white').classList.add('scale-95', 'opacity-0');
    modal.querySelector('.bg-white').classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

function showNotification(message, type = 'info') {
    const colors = {
        success: 'from-green-600 to-emerald-500',
        error: 'from-red-600 to-red-700',
        info: 'from-blue-600 to-cyan-500',
        warning: 'from-orange-600 to-red-500'
    };
    
    const notification = $(`
        <div class="fixed top-4 right-4 z-50 transform translate-x-full transition-transform duration-300">
            <div class="bg-gradient-to-r ${colors[type]} text-white rounded-2xl shadow-2xl p-4 flex items-center space-x-reverse space-x-3 min-w-[300px]">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} text-xl"></i>
                <span class="font-medium">${message}</span>
            </div>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(() => {
        notification.removeClass('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.addClass('translate-x-full');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

$(document).on('click', '#moduleDetailsModal', function(e) {
    if (e.target === this) {
        closeModal('moduleDetailsModal');
    }
});
</script>
@endpush
