@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900/20 to-slate-900 text-white overflow-hidden relative font-sans">
        <!-- Enhanced Animated Background -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-0 -left-64 w-96 h-96 bg-gradient-to-r from-blue-600/30 to-cyan-600/30 rounded-full mix-blend-screen filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute top-0 -right-64 w-96 h-96 bg-gradient-to-r from-purple-600/30 to-pink-600/30 rounded-full mix-blend-screen filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-32 left-1/2 w-96 h-96 bg-gradient-to-r from-indigo-600/30 to-blue-600/30 rounded-full mix-blend-screen filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
            <div class="absolute top-1/2 left-1/4 w-72 h-72 bg-gradient-to-r from-teal-600/20 to-green-600/20 rounded-full mix-blend-screen filter blur-3xl opacity-20 animate-blob animation-delay-6000"></div>
        </div>

        <!-- Grid Pattern Overlay -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.03"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>

        <div class="container mx-auto px-4 py-20 lg:py-32 relative z-10 rtl text-center" dir="rtl">
            <!-- Enhanced Hero Section -->
            <div class="mb-16">
                <div class="inline-flex items-center gap-2 px-4 py-2 mb-8 text-sm font-semibold tracking-wide text-blue-300 bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-full border border-blue-400/20 backdrop-blur-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-400"></span>
                    </span>
                    جيل جديد من البحث العقاري بالذكاء الاصطناعي
                </div>
                
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black mb-8 leading-tight">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-white via-blue-100 to-purple-200">
                        ابحث كما تفكر
                    </span>
                    <br>
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">
                        تماماً
                    </span>
                </h1>
                
                <p class="text-lg md:text-xl text-slate-300 max-w-3xl mx-auto leading-relaxed mb-8">
                    استخدم لغتك الطبيعية للبحث عن منزلك القادم. لا حاجة للفلاتر المعقدة، فقط أخبرنا بما تحلم به ودع الذكاء الاصطناعي يجد لك الأنسب.
                </p>

                <!-- Stats Pills -->
                <div class="flex flex-wrap justify-center gap-4 mb-12">
                    <div class="px-4 py-2 bg-white/5 backdrop-blur-sm border border-white/10 rounded-full text-sm">
                        <span class="text-blue-400 font-bold">50,000+</span>
                        <span class="text-slate-400 mr-2">عقار</span>
                    </div>
                    <div class="px-4 py-2 bg-white/5 backdrop-blur-sm border border-white/10 rounded-full text-sm">
                        <span class="text-purple-400 font-bold">98%</span>
                        <span class="text-slate-400 mr-2">دقة البحث</span>
                    </div>
                    <div class="px-4 py-2 bg-white/5 backdrop-blur-sm border border-white/10 rounded-full text-sm">
                        <span class="text-green-400 font-bold">0.5ث</span>
                        <span class="text-slate-400 mr-2">وقت الاستجابة</span>
                    </div>
                </div>
            </div>

            <!-- Enhanced Smart Search Bar -->
            <div class="max-w-5xl mx-auto relative group mb-12">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-3xl blur-lg opacity-25 group-hover:opacity-40 transition-all duration-500 group-hover:duration-300"></div>
                <div class="relative bg-slate-800/80 backdrop-blur-xl rounded-3xl p-3 shadow-2xl border border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="flex-1 relative">
                            <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" id="smartInput" 
                                placeholder="جرّب: شقة 3 غرف في التجمع الخامس تحت 5 مليون جنيه بحديقة..."
                                class="w-full bg-transparent border-none focus:ring-0 text-lg md:text-xl py-5 pr-12 pl-6 text-white placeholder-slate-400 rtl">
                        </div>
                        <button id="searchBtn"
                            class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white px-6 md:px-8 py-4 md:py-5 rounded-2xl font-bold text-base md:text-lg shadow-lg flex items-center gap-3 transition-all transform hover:scale-[1.02] active:scale-95 min-w-fit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span class="hidden sm:inline">ابحث بالذكاء</span>
                            <span class="sm:hidden">ابحث</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Enhanced Suggestions -->
            <div class="flex flex-wrap justify-center items-center gap-3 text-sm">
                <span class="text-slate-400 font-medium">جرب:</span>
                <button onclick="fillSearch(this.innerText)"
                    class="px-4 py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded-full text-slate-300 transition-all hover:scale-105 backdrop-blur-sm">
                    فيلا للبيع في الشيخ زايد بالتقسيط
                </button>
                <button onclick="fillSearch(this.innerText)"
                    class="px-4 py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded-full text-slate-300 transition-all hover:scale-105 backdrop-blur-sm">
                    شقة مفروشة للايجار بالمعادي
                </button>
                <button onclick="fillSearch(this.innerText)"
                    class="px-4 py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded-full text-slate-300 transition-all hover:scale-105 backdrop-blur-sm">
                    مكتب إداري في العاصمة الإدارية
                </button>
            </div>

            <!-- Enhanced Results Area -->
            <div id="resultsArea" class="mt-16 hidden">
                <!-- Loading State -->
                <div id="loading" class="flex flex-col items-center gap-6 py-16">
                    <div class="relative">
                        <div class="w-16 h-16 border-4 border-blue-500/20 border-t-blue-500 rounded-full animate-spin"></div>
                        <div class="absolute inset-0 w-16 h-16 border-4 border-purple-500/20 border-t-purple-500 rounded-full animate-spin animation-delay-150"></div>
                    </div>
                    <div class="text-center">
                        <p class="text-slate-300 text-lg font-medium animate-pulse">جاري تحليل طلبك...</p>
                        <p class="text-slate-500 text-sm mt-2">يفهم الذكاء الاصطناعي احتياجاتك بدقة</p>
                    </div>
                </div>

                <!-- Parsed Filters -->
                <div id="parsedFilters" class="mb-12 bg-gradient-to-r from-blue-500/10 to-purple-500/10 border border-blue-400/20 rounded-3xl p-8 text-right hidden backdrop-blur-sm">
                    <h3 class="text-blue-300 font-bold mb-6 flex items-center gap-3 text-lg">
                        <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" />
                            </svg>
                        </div>
                        التفاصيل المستخرجة بالذكاء الاصطناعي:
                    </h3>
                    <div id="filterPills" class="flex flex-wrap gap-3 rtl justify-start"></div>
                </div>

                <!-- Property Grid -->
                <div id="propertyGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 text-right">
                    <!-- Cards will be injected here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillSearch(text) {
            const input = document.getElementById('smartInput');
            input.value = text;
            // Add a subtle animation
            input.classList.add('ring-2', 'ring-blue-400', 'ring-opacity-50');
            setTimeout(() => {
                input.classList.remove('ring-2', 'ring-blue-400', 'ring-opacity-50');
            }, 500);
            doSearch();
        }

        async function doSearch() {
            const text = document.getElementById('smartInput').value.trim();
            if (!text) return;

            const resultsArea = document.getElementById('resultsArea');
            const loading = document.getElementById('loading');
            const propertyGrid = document.getElementById('propertyGrid');
            const parsedFilters = document.getElementById('parsedFilters');
            const filterPills = document.getElementById('filterPills');
            const searchBtn = document.getElementById('searchBtn');

            // Show results area with animation
            resultsArea.classList.remove('hidden');
            resultsArea.style.opacity = '0';
            resultsArea.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                resultsArea.style.transition = 'all 0.5s ease-out';
                resultsArea.style.opacity = '1';
                resultsArea.style.transform = 'translateY(0)';
            }, 100);

            loading.classList.remove('hidden');
            propertyGrid.innerHTML = '';
            parsedFilters.classList.add('hidden');
            
            // Disable search button during loading
            searchBtn.disabled = true;
            searchBtn.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                const response = await fetch('/api/ai/search', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ text })
                });
                
                if (!response.ok) throw new Error('Search failed');
                
                const res = await response.json();

                // Simulate processing delay for better UX
                await new Promise(resolve => setTimeout(resolve, 800));
                
                loading.classList.add('hidden');

                if (res.success) {
                    // Show parsed filters with animation
                    renderFilters(res.raw, filterPills, parsedFilters);

                    // Render properties with staggered animation
                    if (res.data && res.data.data && res.data.data.length > 0) {
                        propertyGrid.innerHTML = '';
                        res.data.data.forEach((prop, index) => {
                            setTimeout(() => {
                                propertyGrid.innerHTML += renderCard(prop);
                                // Add entrance animation to new card
                                const newCard = propertyGrid.lastElementChild;
                                newCard.style.opacity = '0';
                                newCard.style.transform = 'translateY(20px)';
                                setTimeout(() => {
                                    newCard.style.transition = 'all 0.5s ease-out';
                                    newCard.style.opacity = '1';
                                    newCard.style.transform = 'translateY(0)';
                                }, 50);
                            }, index * 150);
                        });
                    } else {
                        propertyGrid.innerHTML = `
                            <div class="col-span-full py-20 text-center">
                                <div class="w-20 h-20 bg-slate-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold text-slate-300 mb-2">لم نجد نتائج مطابقة</h3>
                                <p class="text-slate-500 max-w-md mx-auto">حاول تغيير بعض الكلمات أو استخدم عبارات مختلفة للوصف الذي تبحث عنه</p>
                            </div>
                        `;
                    }
                } else {
                    throw new Error(res.message || 'Search failed');
                }
            } catch (e) {
                loading.classList.add('hidden');
                propertyGrid.innerHTML = `
                    <div class="col-span-full py-20 text-center">
                        <div class="w-20 h-20 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-red-400 mb-2">حدث خطأ أثناء البحث</h3>
                        <p class="text-slate-500 max-w-md mx-auto">${e.message}</p>
                        <button onclick="doSearch()" class="mt-4 px-6 py-2 bg-blue-600 hover:bg-blue-500 rounded-lg transition-colors">
                            حاول مرة أخرى
                        </button>
                    </div>
                `;
            } finally {
                // Re-enable search button
                searchBtn.disabled = false;
                searchBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        function renderFilters(raw, container, wrapper) {
            container.innerHTML = '';
            wrapper.classList.remove('hidden');

            const labels = {
                property_type: 'نوع العقار',
                city: 'المدينة',
                district: 'الحي',
                min_price: 'السعر الأدنى',
                max_price: 'السعر الأعلى',
                bedrooms: 'الغرف',
                bathrooms: 'الحمامات',
                listing_type: 'نوع الإعلان',
                furnished: 'التشطيب',
                area: 'المساحة'
            };

            const icons = {
                property_type: '🏠',
                city: '📍',
                district: '🏘️',
                min_price: '💰',
                max_price: '💵',
                bedrooms: '🛏️',
                bathrooms: '🚿',
                listing_type: '📋',
                furnished: '🏡',
                area: '📐'
            };

            Object.entries(raw).forEach(([key, val]) => {
                if (val && labels[key]) {
                    const displayValue = typeof val === 'boolean' ? (val ? 'مؤثث' : 'غير مؤثث') : val;
                    container.innerHTML += `
                        <span class="px-4 py-2 bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-blue-300 border border-blue-400/30 rounded-xl text-sm font-medium backdrop-blur-sm hover:scale-105 transition-transform">
                            <span class="ml-1">${icons[key] || '🔹'}</span>
                            ${labels[key]}: ${displayValue}
                        </span>
                    `;
                }
            });
        }

        function renderCard(p) {
            const imageUrl = p.thumbnail || p.media?.[0]?.file_path || 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80';
            const price = p.price?.price || p.price || 0;
            const formattedPrice = new Intl.NumberFormat('ar-EG').format(price);
            
            return `
                <div class="group bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 rounded-2xl overflow-hidden hover:border-blue-500/50 transition-all duration-300 hover:shadow-2xl hover:shadow-blue-500/20 hover:-translate-y-1">
                    <div class="h-52 bg-slate-700 relative overflow-hidden">
                        <img src="${imageUrl}" 
                             alt="${p.title || 'عقار'}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                             onerror="this.src='https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute top-4 right-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-xs font-bold px-3 py-1.5 rounded-full capitalize">
                            ${p.listing_type || 'sale'}
                        </div>
                        ${p.featured ? '<div class="absolute top-4 left-4 bg-yellow-500 text-black text-xs font-bold px-2 py-1 rounded-full">⭐ مميز</div>' : ''}
                    </div>
                    <div class="p-6">
                        <h4 class="font-bold text-lg mb-3 text-slate-100 line-clamp-2 group-hover:text-blue-300 transition-colors">
                            ${p.title || 'عقار فاخر'}
                        </h4>
                        <div class="flex items-center gap-2 text-slate-400 text-sm mb-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>${p.location?.city || 'موقع غير محدد'} - ${p.location?.district || ''}</span>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-slate-700">
                            <div>
                                <span class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                                    ${formattedPrice}
                                </span>
                                <span class="text-slate-500 text-sm mr-1">جنيه</span>
                            </div>
                            <div class="flex gap-4 text-slate-400 text-sm">
                                ${p.details?.bedrooms ? `
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                                        </svg>
                                        ${p.details.bedrooms}
                                    </span>
                                ` : ''}
                                ${p.details?.bathrooms ? `
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                                        </svg>
                                        ${p.details.bathrooms}
                                    </span>
                                ` : ''}
                                ${p.area ? `
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                        </svg>
                                        ${p.area}م²
                                    </span>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Event listeners
        document.getElementById('searchBtn').addEventListener('click', doSearch);
        document.getElementById('smartInput').addEventListener('keypress', (e) => { 
            if (e.key === 'Enter') doSearch(); 
        });

        // Add input animation
        document.getElementById('smartInput').addEventListener('focus', function() {
            this.parentElement.parentElement.classList.add('ring-2', 'ring-blue-400', 'ring-opacity-30');
        });

        document.getElementById('smartInput').addEventListener('blur', function() {
            this.parentElement.parentElement.classList.remove('ring-2', 'ring-blue-400', 'ring-opacity-30');
        });
    </script>

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-150 {
            animation-delay: 150ms;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        .animation-delay-6000 {
            animation-delay: 6s;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.5);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.7);
        }
    </style>
@endsection