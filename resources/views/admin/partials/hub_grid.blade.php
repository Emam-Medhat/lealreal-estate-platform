@php
    // Category keywords for highly relevant expressive images
    $catKeywords = [
        'core' => 'technology,server,database',
        'financial' => 'finance,chart,business',
        'ai' => 'artificial,intelligence,data',
        'blockchain' => 'crypto,network,blockchain',
        'properties' => 'realestate,architecture,city',
        'advanced' => 'metaverse,data,iot',
        'growth' => 'growth,marketing,team',
        'operations' => 'office,desk,planning'
    ];
@endphp

@push('styles')
<style>
    .cat-section {
        margin-bottom: 3.5rem;
    }

    .cat-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        direction: rtl;
    }

    .cat-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .cat-title-group {
        display: flex;
        flex-direction: column;
    }

    .cat-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }

    .cat-subtitle {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
        direction: rtl;
    }

    /* Premium Module Card */
    .hub-mod-card {
        background: #ffffff;
        border: 1px solid rgba(229, 231, 235, 0.8);
        border-radius: 20px;
        padding: 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        position: relative;
        overflow: hidden;
    }

    .hub-mod-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        border-color: rgba(99, 102, 241, 0.2);
    }

    /* Card Image Section */
    .mod-card-image {
        width: 100%;
        height: 160px;
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid rgba(229, 231, 235, 0.5);
    }

    .mod-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .hub-mod-card:hover .mod-card-image img {
        transform: scale(1.1);
    }

    .mod-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.4) 100%);
    }

    .mod-card-body {
        padding: 24px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .mod-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .mod-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: #f8fafc;
        color: #6366f1;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }

    .hub-mod-card:hover .mod-icon-wrapper {
        transform: scale(1.1) rotate(5deg);
        background: #eef2ff;
        color: #4f46e5;
    }

    .status-badge {
        font-size: 0.65rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 8px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .status-active {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #dcfce7;
    }

    .status-planned {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #f1f5f9;
    }

    .mod-info h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .mod-info p {
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .mod-features {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 24px;
    }

    .feature-tag {
        font-size: 0.7rem;
        font-weight: 700;
        color: #6366f1;
        background: #f5f3ff;
        padding: 4px 10px;
        border-radius: 8px;
        border: 1px solid #ede9fe;
    }

    .mod-card-footer {
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .mod-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 700;
        transition: all 0.3s ease;
        text-decoration: none !important;
    }

    .btn-active {
        background: #1e293b;
        color: white !important;
    }

    .btn-active:hover {
        background: #4f46e5;
        transform: scale(1.05);
    }

    .btn-soon {
        background: #f8fafc;
        color: #94a3b8 !important;
        border: 1px solid #f1f5f9;
        cursor: not-allowed;
    }

    .team-avatars {
        display: flex;
        align-items: center;
    }

    .avatar-mini {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid white;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
        font-weight: 800;
        color: #64748b;
        margin-right: -8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .avatar-mini:first-child {
        margin-right: 0;
    }
</style>
@endpush

@foreach($data['categories'] as $category)
<div class="cat-section category-section" data-category="{{ $category['id'] }}">

    {{-- Category Header --}}
    <div class="cat-header">
        <div class="cat-icon-box" style="background: {{ $category['color'] ?? '#6366f1' }}">
            <i class="fas {{ $category['icon'] ?? 'fa-layer-group' }}"></i>
        </div>
        <div class="cat-title-group">
            <h2 class="cat-title">{{ $category['name'] }}</h2>
            <span class="cat-subtitle">{{ $category['id'] }} system</span>
        </div>
    </div>

    {{-- Modules Grid --}}
    <div class="modules-grid">
        @foreach($category['modules'] as $module)
        @php
            $route    = $module['route'] ?? null;
            $hasRoute = !empty($route) && Route::has($route);
            $status   = $hasRoute ? (app()->getLocale() == 'ar' ? 'نشط' : 'Active') : (app()->getLocale() == 'ar' ? 'قريباً' : 'Soon');
            
            // Dynamic Expressive Image generation using LoremFlickr + Category Keywords
            $seed = crc32($module['name']); // Stable image per module
            $keyword = $catKeywords[$category['id']] ?? 'business,technology';
            $imgUrl = "https://loremflickr.com/500/250/{$keyword}/all?lock={$seed}";
        @endphp

        <div class="hub-mod-card module-card">
            {{-- Top Image with Graceful SVG Fallback --}}
            <div class="mod-card-image" style="background: {{ $category['color'] ?? '#6366f1' }};">
                <img src="{{ $imgUrl }}" alt="{{ $module['name'] }}" 
                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';" 
                     style="width: 100%; height: 100%; object-fit: cover;">
                
                {{-- Fallback Gradient (Shows Only If Image Fails) --}}
                <div class="fallback-gradient" style="display: none; width: 100%; height: 100%; background: linear-gradient(135deg, {{ $category['color'] ?? '#6366f1' }}cc, {{ $category['color'] ?? '#6366f1' }}); align-items: center; justify-content: center; position: absolute; top:0; left:0;">
                    <i class="fas {{ $module['icon'] ?? 'fa-cube' }}" style="font-size: 80px; color: rgba(255,255,255,0.4);"></i>
                </div>
                
                <div class="mod-card-overlay"></div>
            </div>

            <div class="mod-card-body">
                {{-- Header --}}
                <div class="mod-header">
                    <div class="mod-icon-wrapper">
                        <i class="fas {{ $module['icon'] ?? 'fa-cube' }}"></i>
                    </div>
                    <span class="status-badge {{ $hasRoute ? 'status-active' : 'status-planned' }}">
                        {{ $status }}
                    </span>
                </div>

                {{-- Info --}}
                <div class="mod-info">
                    <h3>{{ $module['name'] }}</h3>
                    <p>{{ $module['description'] }}</p>

                    <div class="mod-features">
                        @foreach($module['features'] as $feature)
                        <span class="feature-tag">{{ $feature }}</span>
                        @endforeach
                    </div>
                </div>

                {{-- Footer --}}
                <div class="mod-card-footer">
                    <div class="team-avatars">
                        <div class="avatar-mini" style="background: #e0e7ff; color: #4338ca;">AI</div>
                        <div class="avatar-mini" style="background: #ecfeff; color: #0891b2;">RD</div>
                    </div>

                    @if($hasRoute)
                    <a href="{{ route($route) }}" class="mod-action-btn btn-active">
                        {{ app()->getLocale() === 'ar' ? 'دخول' : 'Enter' }}
                        <i class="fas fa-arrow-left text-[10px] mr-1"></i>
                    </a>
                    @else
                    <span class="mod-action-btn btn-soon">
                        {{ app()->getLocale() === 'ar' ? 'قريباً' : 'Soon' }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach
