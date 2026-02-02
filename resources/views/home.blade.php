@extends('layouts.app')

@section('title', __('Home') . ' | ' . config('app.name', 'Real Estate Pro'))

@section('content')

    <!-- Advanced Glassmorphic Hero Section -->
    <section class="relative min-h-[90vh] flex items-center justify-center text-white overflow-hidden">
        <!-- Dynamic Background Layer -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" 
                 alt="{{ __('Luxury Properties Landscape') }}" 
                 class="w-full h-full object-cover scale-105 animate-pulse-slow">
            <div class="absolute inset-0 bg-gradient-to-tr from-slate-950 via-slate-900/80 to-transparent"></div>
        </div>

        <!-- Hero Content Interface -->
        <div class="relative z-10 container mx-auto px-6 py-20">
            <div class="max-w-5xl mx-auto">
                <!-- Animated Trust Indicator -->
                <div class="inline-flex items-center space-x-3 bg-white/10 backdrop-blur-xl rounded-full px-6 py-2.5 mb-10 border border-white/20 shadow-2xl animate-fade-in-down">
                    <div class="flex -space-x-2">
                        @for($i = 1; $i <= 3; $i++)
                            <img src="https://ui-avatars.com/api/?name=User+{{$i}}&background=random" class="w-8 h-8 rounded-full border-2 border-white/20 object-cover" alt="User Badge">
                        @endfor
                    </div>
                    <span class="text-sm font-semibold tracking-wide uppercase">{{ __('Trusted by 50K+ Clients Worldwide') }}</span>
                </div>

                <!-- Visionary Headline -->
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-black mb-8 leading-tight animate-fade-in">
                    {{ __('Find Your') }} <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-cyan-300 to-indigo-400">
                        {{ __('Architectural Dream') }}
                    </span>
                </h1>

                <!-- Compelling Value Proposition -->
                <p class="text-xl md:text-2xl text-slate-300 mb-14 max-w-3xl leading-relaxed font-light animate-fade-in delay-200">
                    {{ __('Experience a new standard of property acquisition. Premium estates, verified listings, and a seamless digital journey designed for the elite.') }}
                </p>

                <!-- Integrated Search Ecosystem (Glassmorphism) -->
                <div class="bg-white/5 backdrop-blur-3xl rounded-[2.5rem] border border-white/10 p-2 shadow-[0_20px_50px_rgba(0,0,0,0.5)] animate-fade-in-up delay-300">
                    <div class="p-4 md:p-6 lg:p-8">
                        <!-- Dynamic Tabs -->
                        <div class="flex space-x-2 mb-8 bg-black/20 p-1.5 rounded-2xl w-fit">
                            @php $tabs = ['Buy', 'Rent', 'Sell', 'Invest']; @endphp
                            @foreach($tabs as $idx => $tab)
                                <button class="px-8 py-3 rounded-xl transition-all duration-500 font-bold uppercase tracking-widest text-xs {{ $idx === 0 ? 'bg-blue-600 text-white shadow-xl shadow-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                    {{ __($tab) }}
                                </button>
                            @endforeach
                        </div>

                        <!-- High-Complexity Search Form -->
                        <form action="{{ route('properties.index') }}" method="GET" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <!-- Location Engine -->
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none transition-colors group-focus-within:text-blue-400 text-slate-400">
                                        <i class="fas fa-location-dot text-lg"></i>
                                    </div>
                                    <input type="text" name="location" placeholder="{{ __('Where do you want to live?') }}" 
                                           class="block w-full pl-16 pr-6 py-6 bg-white/10 border border-white/10 rounded-3xl text-white placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500/50 transition-all duration-300 text-lg">
                                </div>

                                <!-- AI-Suggested Categories -->
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-400 transition-colors">
                                        <i class="fas fa-house-chimney text-lg"></i>
                                    </div>
                                    <select name="property_type" class="block w-full pl-16 pr-6 py-6 bg-white/10 border border-white/10 rounded-3xl text-white appearance-none focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500/50 transition-all duration-300 text-lg">
                                        <option value="" class="bg-slate-900">{{ __('All Architecture') }}</option>
                                        <option value="villa" class="bg-slate-900">{{ __('Modern Villas') }}</option>
                                        <option value="apartment" class="bg-slate-900">{{ __('Penthouses') }}</option>
                                        <option value="house" class="bg-slate-900">{{ __('Single Houses') }}</option>
                                        <option value="office" class="bg-slate-900">{{ __('Smart Offices') }}</option>
                                    </select>
                                </div>

                                <!-- Premium Price Filtering -->
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-400 transition-colors">
                                        <i class="fas fa-coins text-lg"></i>
                                    </div>
                                    <input type="text" name="price_range" placeholder="{{ __('Investment Range') }}" 
                                           class="block w-full pl-16 pr-6 py-6 bg-white/10 border border-white/10 rounded-3xl text-white placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500/50 transition-all duration-300 text-lg">
                                </div>

                                <!-- Search Execution -->
                                <button type="submit" class="relative overflow-hidden group/btn bg-gradient-to-r from-blue-600 to-indigo-600 py-6 px-10 rounded-3xl font-black text-xl shadow-[0_15px_30px_rgba(37,99,235,0.4)] hover:shadow-[0_20px_40px_rgba(37,99,235,0.6)] transition-all duration-500 transform hover:-translate-y-1">
                                    <div class="absolute inset-0 w-full h-full bg-white/20 translate-x-[-100%] group-hover/btn:translate-x-[100%] transition-transform duration-1000"></div>
                                    <span class="relative flex items-center justify-center space-x-3">
                                        <i class="fas fa-magnifying-glass"></i>
                                        <span>{{ __('Discover') }}</span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Contextual Quick Filters -->
                <div class="mt-12 flex flex-wrap gap-8 animate-fade-in delay-500">
                    <a href="#" class="flex items-center space-x-3 text-slate-300 hover:text-white transition-all group">
                        <span class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center group-hover:bg-blue-600 group-hover:shadow-lg transition-all duration-500 border border-white/10">
                            <i class="fas fa-bolt-lightning text-sm text-yellow-400 group-hover:text-white"></i>
                        </span>
                        <span class="font-bold tracking-tight">{{ __('Market Hot Deals') }}</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 text-slate-300 hover:text-white transition-all group">
                        <span class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center group-hover:bg-indigo-600 group-hover:shadow-lg transition-all duration-500 border border-white/10">
                            <i class="fas fa-award text-sm text-blue-400 group-hover:text-white"></i>
                        </span>
                        <span class="font-bold tracking-tight">{{ __('Premium Collection') }}</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 text-slate-300 hover:text-white transition-all group">
                        <span class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center group-hover:bg-green-600 group-hover:shadow-lg transition-all duration-500 border border-white/10">
                            <i class="fas fa-map-marked-alt text-sm text-cyan-400 group-hover:text-white"></i>
                        </span>
                        <span class="font-bold tracking-tight">{{ __('Interactive Map Search') }}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Ambient Animation Components -->
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-600/30 blur-[150px] rounded-full"></div>
        <div class="absolute top-1/4 -left-24 w-64 h-64 bg-indigo-600/40 blur-[130px] rounded-full"></div>
    </section>

    <!-- Luxury Stats & Counter Section -->
    <section class="py-20 relative bg-slate-950 overflow-hidden">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-12 text-center items-center">
                @php
                    $stats = [
                        ['label' => 'Curated Properties', 'value' => 12500, 'icon' => 'fa-building-columns', 'color' => 'from-blue-400 to-blue-600'],
                        ['label' => 'Premium Transactions', 'value' => 8400, 'icon' => 'fa-handshake-angle', 'color' => 'from-indigo-400 to-indigo-600'],
                        ['label' => 'Elite Agents', 'value' => 1200, 'icon' => 'fa-user-tie', 'color' => 'from-cyan-400 to-cyan-600'],
                        ['label' => 'Global Partners', 'value' => 450, 'icon' => 'fa-globe-americas', 'color' => 'from-purple-400 to-purple-600'],
                    ];
                @endphp

                @foreach($stats as $stat)
                    <div class="relative group">
                        <div class="mb-6 mx-auto w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center transition-all duration-500 group-hover:scale-110 group-hover:border-blue-500/50">
                            <i class="fas {{ $stat['icon'] }} text-2xl bg-clip-text text-transparent bg-gradient-to-r {{ $stat['color'] }}"></i>
                        </div>
                        <div class="text-4xl lg:text-6xl font-black text-white mb-2 tracking-tighter" data-counter="{{ $stat['value'] }}">0</div>
                        <div class="text-slate-400 font-bold uppercase tracking-widest text-xs">{{ __($stat['label']) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured properties with Premium Grid -->
    <section class="py-32 bg-slate-50 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-blue-50/50 to-transparent pointer-events-none"></div>

        <div class="container mx-auto px-6 relative">
            <!-- Section Header Interface -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-20">
                <div class="max-w-3xl">
                    <div class="inline-block px-4 py-1.5 bg-blue-100 rounded-full text-blue-600 text-xs font-black uppercase tracking-widest mb-6">
                        {{ __('Exquisite Selection') }}
                    </div>
                    <h2 class="text-4xl md:text-6xl font-black text-slate-900 mb-8 leading-tight tracking-tight">
                        {{ __('Featured Masterpieces') }}
                    </h2>
                    <p class="text-lg text-slate-500 leading-relaxed font-medium">
                        {{ __('Dive into our handpicked collection of architectural marvels. Each property is a testament to luxury, comfort, and unparalleled design.') }}
                    </p>
                </div>
                <a href="{{ route('properties.index', ['featured' => 1]) }}" class="mt-10 md:mt-0 group/link flex items-center space-x-4 bg-white px-8 py-5 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-x-2 border border-slate-100">
                    <span class="font-black text-slate-900 uppercase tracking-widest text-sm underline decoration-blue-500 decoration-4 underline-offset-8">{{ __('Explore Full Catalog') }}</span>
                    <i class="fas fa-arrow-right-long text-blue-600 group-hover/link:translate-x-2 transition-transform duration-500"></i>
                </a>
            </div>

            <!-- Interactive Multi-Layout Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10">
                @forelse($featuredProperties ?? [] as $property)
                    <x-property-premium-card :property="$property" />
                @empty
                    <!-- Sophisticated Dummy Content for Redemption -->
                    @for($i = 1; $i <= 4; $i++)
                        <div class="group relative bg-white rounded-[2.5rem] overflow-hidden shadow-[0_15px_40px_-15px_rgba(0,0,0,0.1)] hover:shadow-[0_40px_70px_-20px_rgba(0,0,0,0.15)] transition-all duration-700 transform hover:-translate-y-4">
                            <!-- Card Visual Layer -->
                            <div class="relative h-80 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                     class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" alt="Estate">

                                <!-- Glassmorphic Badges -->
                                <div class="absolute top-6 left-6 flex flex-col space-y-2">
                                    <span class="bg-black/50 backdrop-blur-md text-white text-[10px] font-black px-4 py-2 rounded-full uppercase tracking-widest border border-white/10">
                                        {{ __('Featured') }}
                                    </span>
                                    @if($i % 2 == 0)
                                        <span class="bg-blue-600/90 backdrop-blur-md text-white text-[10px] font-black px-4 py-2 rounded-full uppercase tracking-widest">
                                            {{ __('Premium') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Wishlist Engine -->
                                <button class="absolute top-6 right-6 w-12 h-12 rounded-full bg-white/20 backdrop-blur-lg border border-white/30 flex items-center justify-center text-white hover:bg-white hover:text-red-500 transition-all duration-500 group/heart">
                                    <i class="fas fa-heart text-xl group-hover/heart:scale-125 transition-transform"></i>
                                </button>

                                <!-- Value Label -->
                                <div class="absolute bottom-6 left-6 right-6">
                                    <div class="bg-white/10 backdrop-blur-2xl border border-white/20 p-4 rounded-3xl flex justify-between items-center shadow-2xl">
                                        <div class="text-white">
                                            <div class="text-[10px] font-black uppercase tracking-widest opacity-70">{{ __('Investment') }}</div>
                                            <div class="text-2xl font-black">${{ number_format(1500000 + ($i * 250000)) }}</div>
                                        </div>
                                        <div class="bg-white rounded-2xl p-2.5 text-slate-900 shadow-xl">
                                            <i class="fas fa-crown"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Content Interface -->
                            <div class="p-8">
                                <h3 class="text-2xl font-black text-slate-900 mb-2 truncate group-hover:text-blue-600 transition-colors">Azure Estate {{ $i }}</h3>
                                <div class="flex items-center text-slate-400 font-bold text-sm mb-6">
                                    <i class="fas fa-location-dot mr-2 text-blue-500"></i>
                                    <span>Upper Manhattan, NYC</span>
                                </div>

                                <!-- Advanced Specs -->
                                <div class="grid grid-cols-3 gap-4 py-6 border-y border-slate-100 mb-8">
                                    <div class="text-center group/spec">
                                        <i class="fas fa-bed text-blue-500 mb-2 group-hover/spec:scale-125 transition-transform"></i>
                                        <div class="text-sm font-black text-slate-900">{{ 3 + $i }}</div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ __('Beds') }}</div>
                                    </div>
                                    <div class="text-center group/spec border-x border-slate-100 px-2">
                                        <i class="fas fa-bath text-blue-500 mb-2 group-hover/spec:scale-125 transition-transform"></i>
                                        <div class="text-sm font-black text-slate-900">{{ 2 + $i }}</div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ __('Baths') }}</div>
                                    </div>
                                    <div class="text-center group/spec">
                                        <i class="fas fa-ruler-combined text-blue-500 mb-2 group-hover/spec:scale-125 transition-transform"></i>
                                        <div class="text-sm font-black text-slate-900">{{ 3200 + ($i * 450) }}</div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ __('Sqft') }}</div>
                                    </div>
                                </div>

                                <!-- Action Architecture -->
                                <div class="flex items-center space-x-4">
                                    <a href="#" class="flex-1 bg-slate-900 text-white rounded-2xl py-4 font-black uppercase tracking-widest text-xs text-center border-2 border-slate-900 hover:bg-transparent hover:text-slate-900 transition-all duration-500">
                                        {{ __('View Manifest') }}
                                    </a>
                                    <button class="w-14 h-14 rounded-2xl border-2 border-slate-100 flex items-center justify-center text-slate-400 hover:border-blue-600 hover:text-blue-600 transition-all duration-500">
                                        <i class="fas fa-paper-plane text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>
        </div>
    </section>

    <!-- Visionary Categories Section -->
    <section class="py-32 bg-slate-950 relative overflow-hidden">
        <!-- Matrix Pattern Background -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#2563eb 1px, transparent 0); background-size: 40px 40px;"></div>

        <div class="container mx-auto px-6 relative">
            <div class="text-center max-w-4xl mx-auto mb-20">
                <h2 class="text-4xl md:text-6xl font-black text-white mb-8">{{ __('Architectural Categories') }}</h2>
                <p class="text-xl text-slate-400 leading-relaxed font-light">{{ __('Navigate through our curated ecosystem of property segments tailored for modern lifestyle needs.') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-8">
                @php
                    $segments = [
                        ['name' => 'Royal Penthouses', 'icon' => 'fa-building', 'color' => 'blue', 'listings' => '4,200+'],
                        ['name' => 'Modern Villas', 'icon' => 'fa-house-user', 'color' => 'indigo', 'listings' => '1,850+'],
                        ['name' => 'Smart Offices', 'icon' => 'fa-briefcase', 'color' => 'cyan', 'listings' => '920+'],
                        ['name' => 'Strategic Land', 'icon' => 'fa-mountain-sun', 'color' => 'purple', 'listings' => '540+'],
                        ['name' => 'Retail Spaces', 'icon' => 'fa-shop', 'color' => 'pink', 'listings' => '320+'],
                    ];
                @endphp

                @foreach($segments as $segment)
                    <a href="#" class="group relative bg-white/5 border border-white/10 rounded-[2.5rem] p-10 text-center hover:bg-white/10 hover:border-white/20 transition-all duration-1000 transform hover:-translate-y-6">
                        <!-- Icon Interaction -->
                        <div class="relative w-24 h-24 mx-auto mb-10 overflow-hidden">
                            <div class="absolute inset-0 bg-{{$segment['color']}}-600/20 blur-2xl rounded-full scale-0 group-hover:scale-150 transition-transform duration-1000"></div>
                            <div class="relative w-full h-full bg-white/5 border border-white/10 rounded-3xl flex items-center justify-center transition-all duration-700 group-hover:rotate-[360deg] group-hover:bg-{{$segment['color']}}-600 group-hover:shadow-[0_0_40px_rgba(37,99,235,0.4)]">
                                <i class="fas {{$segment['icon']}} text-3xl text-{{$segment['color']}}-400 group-hover:text-white transition-colors duration-700"></i>
                            </div>
                        </div>

                        <h3 class="text-xl font-black text-white mb-4 tracking-tight">{{ __($segment['name']) }}</h3>
                        <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 py-2 px-4 rounded-full bg-white/5 inline-block group-hover:bg-{{$segment['color']}}-600/20 group-hover:text-white transition-all">
                            {{ $segment['listings'] }} {{ __('Listings') }}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Strategic CTA Ecosystem -->
    <section class="py-32 relative overflow-hidden bg-white">
        <div class="container mx-auto px-6">
            <div class="bg-slate-900 rounded-[4rem] p-12 lg:p-24 relative overflow-hidden shadow-[0_40px_100px_-20px_rgba(15,23,42,0.4)]">
                <!-- Background Visual Layer -->
                <div class="absolute inset-0 opacity-10">
                    <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" 
                         class="w-full h-full object-cover" alt="Elite Office">
                </div>
                <div class="absolute -right-40 -top-40 w-96 h-96 bg-blue-600/20 blur-[150px] rounded-full"></div>

                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 lg:items-center gap-20">
                    <div>
                        <h2 class="text-4xl md:text-6xl font-black text-white mb-10 leading-tight">
                            {{ __('Elevate Your') }} <br>
                            <span class="text-blue-500">{{ __('Real Estate Assets') }}</span>
                        </h2>
                        <p class="text-xl text-slate-400 mb-14 leading-relaxed font-light">
                            {{ __('Whether you are looking to list a premium property or scale your portfolio, our visionary platform provides the ultimate ecosystem for success.') }}
                        </p>

                        <div class="flex flex-col sm:flex-row gap-6">
                            <a href="{{ route('register') }}" class="group/cta bg-white text-slate-900 px-12 py-5 rounded-2xl font-black uppercase tracking-widest text-[13px] hover:bg-blue-600 hover:text-white transition-all duration-500 flex items-center justify-center space-x-4 shadow-2xl hover:shadow-blue-500/40">
                                <span>{{ __('Ignite Journey') }}</span>
                                <i class="fas fa-chevron-right text-[10px] group-hover/cta:translate-x-2 transition-transform"></i>
                            </a>
                            <a href="{{ route('contact') }}" class="bg-white/10 backdrop-blur-xl border border-white/10 text-white px-12 py-5 rounded-2xl font-black uppercase tracking-widest text-[13px] hover:bg-white/20 transition-all duration-500 flex items-center justify-center">
                                {{ __('Consult Experts') }}
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @php
                            $features = [
                                ['title' => 'For Agents', 'desc' => 'Market intelligence & lead engine.', 'icon' => 'fa-users-gear', 'route' => 'register'],
                                ['title' => 'For Owners', 'desc' => 'Maximum exposure for high-value assets.', 'icon' => 'fa-house-circle-check', 'route' => 'register'],
                                ['title' => 'For Developers', 'desc' => 'Showcase visionary projects.', 'icon' => 'fa-city', 'route' => 'register'],
                                ['title' => 'For Investors', 'desc' => 'Data-driven portfolio analytics.', 'icon' => 'fa-chart-pie', 'route' => 'register'],
                            ];
                        @endphp

                        @foreach($features as $f)
                            <div class="bg-white/5 backdrop-blur-lg border border-white/5 p-8 rounded-[2.5rem] hover:border-blue-500/30 transition-all duration-700 group">
                                <div class="w-14 h-14 rounded-2xl bg-blue-600/20 flex items-center justify-center mb-6 group-hover:bg-blue-600 transition-all duration-500">
                                    <i class="fas {{$f['icon']}} text-xl text-blue-500 group-hover:text-white"></i>
                                </div>
                                <h3 class="text-white font-black text-lg mb-3 tracking-tight">{{ __($f['title']) }}</h3>
                                <p class="text-slate-400 text-sm font-medium mb-6 leading-relaxed">{{ __($f['desc']) }}</p>
                                <a href="{{ route($f['route']) }}" class="text-blue-500 text-[10px] font-black uppercase tracking-widest hover:text-white transition-colors flex items-center">
                                    {{ __('Discover More') }} <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('styles')
    <style>
        @keyframes pulse-slow {
            0%, 100% { transform: scale(1.05); }
            50% { transform: scale(1.1); }
        }
        .animate-pulse-slow { animation: pulse-slow 20s infinite ease-in-out; }

        .animate-fade-in { animation: fadeIn 1.2s forwards ease-out; opacity: 0; }
        .animate-fade-in-down { animation: fadeInDown 1.2s forwards ease-out; opacity: 0; }
        .animate-fade-in-up { animation: fadeInUp 1.2s forwards ease-out; opacity: 0; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-500 { animation-delay: 0.5s; }

        /* Counter Animation Initial State */
        [data-counter] { opacity: 0; transform: translateY(20px); transition: all 1s cubic-bezier(0.16, 1, 0.3, 1); }
        [data-counter].animated { opacity: 1; transform: translateY(0); }
    </style>
@endpush

@push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Advanced Counter with Locale Formatting
        function animateCounter(element, target) {
            let current = 0;
            const duration = 2000; 
            const frameRate = 1000 / 60;
            const totalFrames = Math.round(duration / frameRate);
            const increment = target / totalFrames;

            let frame = 0;
            const timer = setInterval(() => {
                frame++;
                current += increment;
                if (frame >= totalFrames) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current).toLocaleString();
            }, frameRate);
        }

        const counterElements = document.querySelectorAll('[data-counter]');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                    const target = parseInt(entry.target.getAttribute('data-counter'));
                    animateCounter(entry.target, target);
                    entry.target.classList.add('animated');
                }
            });
        }, { threshold: 0.5 });

        counterElements.forEach(el => observer.observe(el));
    });
    </script>
@endpush
