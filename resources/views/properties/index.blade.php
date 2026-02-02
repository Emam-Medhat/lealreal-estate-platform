@extends('layouts.app')

@section('title', __('Discover Properties') . ' | ' . config('app.name', 'Real Estate Pro'))

@section('content')
    <div class="bg-white min-h-screen">
        <!-- Sophisticated Filter Architecture -->
        <div class="bg-slate-900 border-b border-slate-800 sticky top-0 z-40 shadow-2xl">
            <div class="container mx-auto px-6 py-6 lg:py-8">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                    <div class="max-w-xl">
                        <h1 class="text-3xl md:text-5xl font-black text-white mb-2 tracking-tight">
                            {{ __('Discover Architecture') }}</h1>
                        <p class="text-slate-400 font-medium text-lg leading-relaxed">
                            {{ __('Explore our curated ecosystem of global real estate assets.') }}</p>
                    </div>

                    <!-- Quick Filter Interface -->
                    <div
                        class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-2 flex flex-wrap items-center gap-2">
                        @php $listingTypes = ['All', 'For Sale', 'For Rent', 'Invest']; @endphp
                        @foreach($listingTypes as $idx => $lt)
                            <button
                                class="px-6 py-2.5 rounded-2xl transition-all duration-300 font-bold uppercase tracking-widest text-[10px] {{ $idx === 0 ? 'bg-blue-600 text-white shadow-xl shadow-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                {{ __($lt) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Integrated Search Ecosystem -->
                <div class="mt-8">
                    <form method="GET" action="{{ route('optimized.properties.index') }}" id="premiumSearchForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div class="lg:col-span-2 relative group">
                                <div
                                    class="absolute inset-y-0 left-6 flex items-center pointer-events-none text-slate-500 transition-colors group-focus-within:text-blue-400">
                                    <i class="fas fa-magnifying-glass"></i>
                                </div>
                                <input type="text" name="q" value="{{ request('q') }}"
                                    class="w-full pl-14 pr-6 py-5 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-slate-500 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500/40 transition-all font-medium"
                                    placeholder="{{ __('Keywords, districts, or property codes...') }}">
                            </div>

                            <div class="relative group">
                                <select name="property_type"
                                    class="w-full pl-6 pr-12 py-5 bg-white/5 border border-white/10 rounded-2xl text-white appearance-none focus:outline-none focus:border-blue-500/40 transition-all font-bold text-xs uppercase tracking-widest">
                                    <option value="" class="bg-slate-900 font-black text-xs">{{ __('All Architecture') }}
                                    </option>
                                    @foreach($propertyTypes ?? [] as $type)
                                        <option value="{{ $type->slug }}" {{ request('property_type') == $type->slug ? 'selected' : '' }} class="bg-slate-900 font-black text-xs uppercase">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                <i
                                    class="fas fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none"></i>
                            </div>

                            <div class="relative group">
                                <select name="max_price"
                                    class="w-full pl-6 pr-12 py-5 bg-white/5 border border-white/10 rounded-2xl text-white appearance-none focus:outline-none focus:border-blue-500/40 transition-all font-bold text-xs uppercase tracking-widest">
                                    <option value="" class="bg-slate-900 font-black text-xs">{{ __('Max Investment') }}
                                    </option>
                                    @php $prices = [100000, 500000, 1000000, 2500000, 5000000]; @endphp
                                    @foreach($prices as $p)
                                        <option value="{{ $p }}" {{ request('max_price') == $p ? 'selected' : '' }}
                                            class="bg-slate-900 font-black text-xs">${{ number_format($p / 1000) }}k</option>
                                    @endforeach
                                </select>
                                <i
                                    class="fas fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none"></i>
                            </div>

                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-500 text-white font-black uppercase tracking-[0.2em] text-[11px] rounded-2xl py-5 transition-all shadow-xl shadow-blue-500/20 active:scale-95">
                                {{ __('Execute Search') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Results Interface -->
        <div class="container mx-auto px-6 py-12">
            <div class="flex items-center justify-between mb-12">
                <div class="flex items-center space-x-4">
                    <span
                        class="text-slate-400 font-black uppercase tracking-widest text-[10px]">{{ __('Metrics') }}:</span>
                    <span
                        class="bg-slate-100 text-slate-900 px-4 py-1.5 rounded-xl font-black text-xs border border-slate-200">
                        {{ $properties->total() }} {{ __('Manifests Found') }}
                    </span>
                </div>

                <div class="flex items-center space-x-6">
                    <div
                        class="hidden md:flex items-center space-x-2 bg-slate-50 p-1.5 rounded-2xl border border-slate-100">
                        <button
                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-white shadow-sm text-blue-600"><i
                                class="fas fa-grid-2"></i></button>
                        <button
                            class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-900"><i
                                class="fas fa-list"></i></button>
                    </div>
                    <select
                        class="bg-transparent border-none font-black uppercase tracking-widest text-[10px] text-slate-900 focus:ring-0 cursor-pointer">
                        <option>{{ __('Newest Listings') }}</option>
                        <option>{{ __('Price: High to Low') }}</option>
                        <option>{{ __('Price: Low to High') }}</option>
                    </select>
                </div>
            </div>

            <!-- Premium Property Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-12">
                @forelse($properties as $property)
                    <div
                        class="group relative bg-white rounded-[2.5rem] overflow-hidden shadow-[0_15px_40px_-15px_rgba(0,0,0,0.08)] hover:shadow-[0_45px_80px_-25px_rgba(37,99,235,0.15)] transition-all duration-700 transform hover:-translate-y-4 border border-slate-50">
                        <!-- Dynamic Media Layer -->
                        <div class="relative h-72 overflow-hidden">
                            <img src="{{ $property->media->first()->url ?? 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}"
                                class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                                alt="Estate">

                            <!-- Status Overlays -->
                            <div class="absolute top-6 left-6 flex items-center space-x-2">
                                <span
                                    class="bg-slate-900/80 backdrop-blur-md text-white text-[9px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest border border-white/10">
                                    {{ __($property->listing_type) }}
                                </span>
                                @if($property->featured)
                                    <span
                                        class="bg-blue-600 text-white text-[9px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest shadow-lg shadow-blue-500/30">
                                        {{ __('Exclusive') }}
                                    </span>
                                @endif
                            </div>

                            <!-- Wishlist Interface -->
                            <button
                                class="absolute top-6 right-6 w-11 h-11 rounded-full bg-white/20 backdrop-blur-lg border border-white/20 flex items-center justify-center text-white hover:bg-white hover:text-red-500 transition-all duration-500 group/heart">
                                <i class="fas fa-heart text-lg group-hover/heart:scale-110 transition-transform"></i>
                            </button>

                            <!-- Premium Label -->
                            <div class="absolute bottom-6 right-6">
                                <div
                                    class="w-10 h-10 bg-white shadow-2xl rounded-2xl flex items-center justify-center text-slate-900">
                                    <i class="fas fa-crown text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Content Interface -->
                        <div class="p-8">
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <h3
                                        class="text-xl font-black text-slate-900 mb-2 truncate max-w-[180px] group-hover:text-blue-600 transition-colors">
                                        {{ $property->title }}</h3>
                                    <div
                                        class="flex items-center text-slate-400 font-bold text-[11px] uppercase tracking-tighter">
                                        <i class="fas fa-location-dot mr-1.5 text-blue-500"></i>
                                        <span>{{ $property->location?->city ?? __('Prime District') }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xl font-black text-blue-600 tracking-tighter">
                                        ${{ number_format($property->price) }}</div>
                                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">
                                        {{ __('Invest Value') }}</div>
                                </div>
                            </div>

                            <!-- Metrics Architecture -->
                            <div class="grid grid-cols-3 gap-2 py-5 border-y border-slate-100 mb-8">
                                <div class="text-center">
                                    <div class="text-sm font-black text-slate-900">{{ $property->bedrooms ?? '0' }}</div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ __('Beds') }}
                                    </div>
                                </div>
                                <div class="text-center border-x border-slate-100">
                                    <div class="text-sm font-black text-slate-900">{{ $property->bathrooms ?? '0' }}</div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">
                                        {{ __('Baths') }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-sm font-black text-slate-900">{{ number_format($property->area ?? 0) }}
                                    </div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ __('Sqft') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Action Architecture -->
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('properties.show', $property) }}"
                                    class="flex-1 bg-slate-900 text-white rounded-2xl py-4 font-black uppercase tracking-widest text-[10px] text-center transition-all duration-500 hover:bg-blue-600 hover:shadow-xl hover:shadow-blue-500/20 active:scale-95">
                                    {{ __('View Manifest') }}
                                </a>
                                <button
                                    class="w-12 h-12 rounded-2xl border-2 border-slate-50 flex items-center justify-center text-slate-400 hover:border-blue-600 hover:text-blue-600 transition-all duration-500 bg-slate-50 hover:bg-white">
                                    <i class="fas fa-paper-plane text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-40">
                        <div class="max-w-md mx-auto text-center">
                            <div
                                class="w-32 h-32 bg-slate-50 rounded-[3rem] flex items-center justify-center mx-auto mb-10 border-2 border-dashed border-slate-200">
                                <i class="fas fa-map-marked text-slate-300 text-4xl"></i>
                            </div>
                            <h3 class="text-3xl font-black text-slate-900 mb-4 tracking-tight">{{ __('No Architecture Found') }}
                            </h3>
                            <p class="text-slate-500 font-medium text-lg leading-relaxed mb-10">
                                {{ __('We couldn\'t find any listings matching your sophisticated parameters. Try refining your horizon.') }}
                            </p>
                            <a href="{{ route('optimized.properties.index') }}"
                                class="inline-flex items-center space-x-3 text-blue-600 font-black uppercase tracking-widest text-xs hover:text-blue-500 group">
                                <span>{{ __('Clear All Vectors') }}</span>
                                <i
                                    class="fas fa-arrow-rotate-left group-hover:rotate-[-180deg] transition-transform duration-700"></i>
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Visionary Pagination -->
            <div class="mt-24 flex justify-center">
                <div class="bg-white px-8 py-3 rounded-full border border-slate-100 shadow-xl flex items-center space-x-6">
                    {{ $properties->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection