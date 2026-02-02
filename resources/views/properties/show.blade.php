@extends('layouts.app')

@section('title', $property->title . ' | ' . config('app.name', 'Real Estate Pro'))

@section('content')
    <div class="bg-white min-h-screen">
        <!-- Immersive Property Introduction -->
        <section class="relative h-[70vh] md:h-[80vh] overflow-hidden group">
            <!-- Main Cinematic Visual -->
            @php
                $images = $property->media->where('media_type', 'image')->pluck('url')->toArray();
                $mainImage = count($images) > 0 ? $images[0] : 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
            @endphp
            <img src="{{ $mainImage }}"
                class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"
                alt="{{ $property->title }}">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>

            <!-- Premium Navigation Overlay -->
            <div class="absolute top-10 left-10 z-20">
                <a href="{{ route('optimized.properties.index') }}"
                    class="group/back flex items-center space-x-4 bg-white/10 backdrop-blur-xl border border-white/20 px-6 py-3 rounded-2xl text-white font-black uppercase tracking-[0.2em] text-[10px] hover:bg-white hover:text-slate-900 transition-all duration-500">
                    <i class="fas fa-arrow-left-long group-hover/back:-translate-x-2 transition-transform"></i>
                    <span>{{ __('Return to Catalog') }}</span>
                </a>
            </div>

            <!-- Dynamic Header Info Overlay -->
            <div class="absolute bottom-20 left-10 md:left-20 right-10 md:right-20 z-10">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-10">
                    <div class="max-w-4xl">
                        <div class="flex items-center space-x-3 mb-6">
                            <span
                                class="bg-blue-600 text-white text-[10px] font-black px-6 py-2 rounded-full uppercase tracking-widest shadow-2xl shadow-blue-500/40">
                                {{ __($property->listing_type) }}
                            </span>
                            <span
                                class="bg-white/10 backdrop-blur-md text-white text-[10px] font-black px-6 py-2 rounded-full uppercase tracking-widest border border-white/20">
                                {{ $property->propertyType?->name ?? __('Exclusive Residence') }}
                            </span>
                        </div>
                        <h1
                            class="text-5xl md:text-7xl lg:text-8xl font-black text-white mb-6 leading-tight tracking-tighter">
                            {{ $property->title }}</h1>
                        <div class="flex flex-wrap items-center gap-8 text-slate-300 font-bold text-lg">
                            <div class="flex items-center">
                                <i class="fas fa-location-dot mr-3 text-blue-500 text-xl"></i>
                                <span>{{ $property->location?->full_address ?? $property->address }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-qrcode mr-3 text-blue-500"></i>
                                <span
                                    class="uppercase tracking-widest text-sm underline decoration-blue-500/50 decoration-2 underline-offset-4">{{ $property->property_code ?? 'PRP-2024' }}</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white/5 backdrop-blur-3xl border border-white/10 p-10 rounded-[3rem] text-right shadow-2xl">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-3">
                            {{ __('Investment Listing') }}</div>
                        <div class="text-5xl md:text-7xl font-black text-white tracking-tighter">
                            ${{ number_format($property->price) }}</div>
                        <div class="text-blue-400 font-bold text-sm mt-2 uppercase tracking-widest">
                            {{ __('Negotiable Assets') }}</div>
                    </div>
                </div>
            </div>

            <!-- Gallery Controller -->
            <div class="absolute bottom-10 right-10 z-20 flex space-x-4">
                <button
                    class="w-16 h-16 rounded-full bg-white/10 backdrop-blur-xl border border-white/20 flex items-center justify-center text-white hover:bg-white hover:text-slate-900 transition-all duration-500 shadow-2xl">
                    <i class="fas fa-images text-xl"></i>
                </button>
                <button
                    class="w-16 h-16 rounded-full bg-white/10 backdrop-blur-xl border border-white/20 flex items-center justify-center text-white hover:bg-white hover:text-slate-900 transition-all duration-500 shadow-2xl">
                    <i class="fas fa-video text-xl"></i>
                </button>
            </div>
        </section>

        <!-- Structural Detail Interface -->
        <div class="container mx-auto px-6 py-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-20">
                <!-- Global Property Specifications (Main Column) -->
                <div class="lg:col-span-8 space-y-24">

                    <!-- Advanced Metrics Panel -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                        @php
                            $specs = [
                                ['label' => 'Total Bedrooms', 'val' => $property->bedrooms ?? 0, 'icon' => 'fa-bed', 'color' => 'blue'],
                                ['label' => 'Bathrooms', 'val' => $property->bathrooms ?? 0, 'icon' => 'fa-bath', 'color' => 'indigo'],
                                ['label' => 'Living Area', 'val' => number_format($property->area ?? 0) . ' ' . ($property->area_unit ?? 'sqft'), 'icon' => 'fa-ruler-combined', 'color' => 'cyan'],
                                ['label' => 'Total Floors', 'val' => '02', 'icon' => 'fa-stairs', 'color' => 'purple'],
                            ];
                        @endphp
                        @foreach($specs as $spec)
                            <div
                                class="bg-slate-50 border border-slate-100 p-8 rounded-[2.5rem] group hover:bg-white hover:shadow-2xl hover:shadow-slate-200 transition-all duration-700">
                                <div
                                    class="w-14 h-14 bg-{{$spec['color']}}-600/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-{{$spec['color']}}-600 transition-all duration-500">
                                    <i
                                        class="fas {{$spec['icon']}} text-xl text-{{$spec['color']}}-600 group-hover:text-white"></i>
                                </div>
                                <div class="text-2xl font-black text-slate-950 mb-1">{{ $spec['val'] }}</div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    {{ __($spec['label']) }}</div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Architectural Description -->
                    <div class="relative">
                        <div
                            class="inline-block px-4 py-1 bg-slate-100 rounded-full text-slate-900 text-[10px] font-black uppercase tracking-widest mb-8">
                            {{ __('Manifest Details') }}
                        </div>
                        <h2 class="text-4xl font-black text-slate-900 mb-10 tracking-tight">
                            {{ __('Visionary Design Statement') }}</h2>
                        <div class="text-xl text-slate-500 leading-[2.2] font-medium space-y-8">
                            {!! nl2br(e($property->description)) !!}
                        </div>
                    </div>

                    <!-- Luxury Amenities Ecosystem -->
                    <div>
                        <h2 class="text-4xl font-black text-slate-900 mb-12 tracking-tight">{{ __('Curated Amenities') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($property->propertyAmenities as $amenity)
                                <div
                                    class="flex items-center space-x-6 bg-slate-50 p-6 rounded-3xl border border-slate-100 hover:border-blue-500/30 transition-all group">
                                    <div
                                        class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm group-hover:bg-blue-600 transition-all duration-500">
                                        <i
                                            class="{{ $amenity->icon ?? 'fas fa-check-circle' }} text-blue-600 group-hover:text-white transition-colors"></i>
                                    </div>
                                    <span
                                        class="text-slate-900 font-bold uppercase tracking-widest text-xs">{{ $amenity->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Interactive Location Interface -->
                    @if($property->location?->latitude && $property->location?->longitude)
                        <div>
                            <div class="flex items-center justify-between mb-12">
                                <h2 class="text-4xl font-black text-slate-900 tracking-tight">{{ __('Strategic Location') }}
                                </h2>
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $property->location->latitude }},{{ $property->location->longitude }}"
                                    target="_blank"
                                    class="text-blue-600 font-black uppercase tracking-widest text-[10px] underline decoration-blue-500/50 decoration-4 underline-offset-8">
                                    {{ __('Open Satellite View') }}
                                </a>
                            </div>
                            <div id="premiumMap"
                                class="h-[500px] rounded-[3rem] border-8 border-slate-50 shadow-2xl overflow-hidden relative"
                                data-lat="{{ $property->location->latitude }}" data-lng="{{ $property->location->longitude }}">
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Conversion Ecosystem (Sidebar) -->
                <div class="lg:col-span-4">
                    <div class="sticky top-40 space-y-10">

                        <!-- Agent Manifest Card -->
                        <div
                            class="bg-slate-900 rounded-[3rem] p-10 shadow-[0_40px_80px_-20px_rgba(15,23,42,0.3)] relative overflow-hidden group">
                            <div
                                class="absolute -right-10 -top-10 w-40 h-40 bg-blue-600/20 blur-3xl rounded-full transition-all group-hover:scale-150 duration-1000">
                            </div>

                            <div class="relative z-10">
                                <h3
                                    class="text-white font-black text-[10px] uppercase tracking-[0.3em] mb-10 flex items-center space-x-3">
                                    <span class="w-8 h-[2px] bg-blue-600"></span>
                                    <span>{{ __('Acquisition Specialist') }}</span>
                                </h3>

                                @if($property->agent)
                                    <div class="flex items-center space-x-6 mb-12">
                                        <div class="relative">
                                            <img src="{{ $property->agent->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . $property->agent->name }}"
                                                class="w-24 h-24 rounded-[2rem] object-cover border-4 border-white/5"
                                                alt="Agent">
                                            <div
                                                class="absolute -bottom-2 -right-2 bg-green-500 w-6 h-6 rounded-full border-4 border-slate-900">
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="text-2xl font-black text-white mb-1">{{ $property->agent->name }}</h4>
                                            <div class="text-blue-400 font-bold uppercase tracking-widest text-[10px]">
                                                {{ __('Elite Certified Agent') }}</div>
                                        </div>
                                    </div>

                                    <div class="space-y-4 mb-12">
                                        <a href="tel:{{ $property->agent->phone }}"
                                            class="group/call w-full bg-white/5 border border-white/10 text-white rounded-2xl py-6 px-8 flex items-center justify-between hover:bg-white hover:text-slate-900 transition-all duration-500">
                                            <span
                                                class="font-black uppercase tracking-widest text-xs">{{ __('Voice Call') }}</span>
                                            <i class="fas fa-phone-volume group-hover/call:rotate-12 transition-transform"></i>
                                        </a>
                                        <a href="sms:{{ $property->agent->phone }}"
                                            class="group/text w-full bg-white/5 border border-white/10 text-white rounded-2xl py-6 px-8 flex items-center justify-between hover:bg-white hover:text-slate-900 transition-all duration-500">
                                            <span
                                                class="font-black uppercase tracking-widest text-xs">{{ __('Direct Message') }}</span>
                                            <i
                                                class="fas fa-comment-dots group-hover/text:translate-x-1 transition-transform"></i>
                                        </a>
                                    </div>

                                    <button
                                        class="w-full bg-blue-600 text-white rounded-2xl py-6 font-black uppercase tracking-widest text-xs shadow-xl shadow-blue-500/40 hover:bg-blue-500 transition-all active:scale-95">
                                        {{ __('Request Private Viewing') }}
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Assets Utility -->
                        <div class="bg-slate-50 rounded-[2.5rem] border border-slate-100 p-10 space-y-8">
                            <h4 class="text-slate-900 font-black text-sm uppercase tracking-widest mb-4">
                                {{ __('Asset Utilities') }}</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <button
                                    class="bg-white p-6 rounded-3xl border border-slate-100 text-slate-500 hover:text-blue-600 hover:border-blue-500/30 transition-all shadow-sm flex flex-col items-center">
                                    <i class="fas fa-file-pdf mb-3 text-xl"></i>
                                    <span class="text-[9px] font-black uppercase tracking-widest">{{ __('Exposé') }}</span>
                                </button>
                                <button
                                    class="bg-white p-6 rounded-3xl border border-slate-100 text-slate-500 hover:text-red-500 hover:border-red-500/30 transition-all shadow-sm flex flex-col items-center">
                                    <i class="fas fa-heart mb-3 text-xl"></i>
                                    <span
                                        class="text-[9px] font-black uppercase tracking-widest">{{ __('Manifest') }}</span>
                                </button>
                            </div>
                            <button
                                class="w-full py-5 rounded-2xl border-2 border-dashed border-slate-200 text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] hover:bg-slate-100 hover:text-slate-900 transition-all">
                                <i class="fas fa-share-nodes mr-3"></i>{{ __('Share Digital Invite') }}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapEl = document.getElementById('premiumMap');
            if (mapEl) {
                const lat = parseFloat(mapEl.dataset.lat);
                const lng = parseFloat(mapEl.dataset.lng);
                const map = L.map('premiumMap', { zoomControl: false }).setView([lat, lng], 15);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '© OpenStreetMap CARTO'
                }).addTo(map);

                const icon = L.divIcon({
                    html: '<div class="w-10 h-10 bg-blue-600 rounded-2xl border-4 border-white shadow-2xl flex items-center justify-center text-white rotate-45"><i class="fas fa-home -rotate-45 text-xs"></i></div>',
                    className: 'custom-div-icon',
                    iconSize: [40, 40],
                    iconAnchor: [20, 20]
                });

                L.marker([lat, lng], { icon: icon }).addTo(map);
            }
        });
    </script>
@endpush