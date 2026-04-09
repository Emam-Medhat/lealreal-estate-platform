@extends('layouts.app')

@section('title', 'Find Real Estate Agents - ' . config('app.name', 'EWAN'))

@section('content')
<style>
    :root {
        /* REFRESHED PALETTE: Royal Blue & Gold */
        --tc:     #2563eb; /* Royal Blue */
        --tc-l:   #3b82f6;
        --tc-ll:  #eff6ff;
        --tc-d:   #1e40af;
        
        --sand:   #f1f5f9; /* Cool Gray Light */
        --sand-d: #cbd5e1; /* Cool Gray Darker */
        --cream:  #f8fafc; /* Very Light Blue-Gray */
        
        --ink:    #0f172a; /* Deep Slate */
        --ink-2:  #334155;
        --ink-3:  #475569;
        --ink-4:  #94a3b8;
        
        --gold:   #f59e0b; /* Amber */
        --gold-l: #fbbf24;
        
        --teal:   #0d9488; /* Teal */
        --teal-l: #14b8a6;
        
        --white:  #ffffff;
        
        --font-h: 'DM Serif Display', Georgia, serif;
        --font-b: 'Outfit', system-ui, sans-serif;
        
        --r-sm:  8px; --r-md: 14px; --r-lg: 20px; --r-xl: 28px;
        --ease: cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* BASE */
    .ad-page { font-family:var(--font-b); background:var(--cream); color:var(--ink); padding-top:80px; min-height:100vh; }
    
    /* HERO */
    .ad-hero { position:relative; padding:80px 0 120px; background:var(--ink); overflow:hidden; text-align:center; }
    .ad-hero-bg { position:absolute; inset:0; background: radial-gradient(circle at 80% 20%, rgba(37,99,235,0.25) 0%, transparent 50%), radial-gradient(circle at 20% 80%, rgba(13,148,136,0.2) 0%, transparent 50%), linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); }
    .ad-hero-mesh { position:absolute; inset:0; opacity:.06; background-image: radial-gradient(rgba(255,255,255,0.7) 1px, transparent 1px); background-size:32px 32px; }
    
    .ad-hero-content { position:relative; z-index:2; max-width:800px; margin:0 auto; padding:0 20px; }
    .ad-icon-box { width:80px; height:80px; background:rgba(255,255,255,.1); backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,.2); border-radius:24px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:24px; color:var(--white); font-size:32px; box-shadow:0 12px 30px rgba(0,0,0,.2); }
    .ad-title { font-family:var(--font-h); font-size:48px; color:var(--white); margin-bottom:16px; line-height:1.1; }
    .ad-subtitle { font-size:18px; color:var(--ink-4); margin-bottom:40px; line-height:1.6; max-width:600px; margin-left:auto; margin-right:auto; }
    
    /* SEARCH BAR */
    .ad-search-wrap { background:var(--white); padding:10px; border-radius:var(--r-xl); box-shadow:0 20px 60px rgba(0,0,0,.2); display:flex; gap:10px; max-width:700px; margin:0 auto; border:1px solid rgba(255,255,255,.1); position:relative; }
    .ad-search-input { flex:1; border:none; background:transparent; padding:0 24px; font-family:var(--font-b); font-size:16px; color:var(--ink); outline:none; }
    .ad-search-btn { background:var(--tc); color:var(--white); border:none; padding:14px 32px; border-radius:var(--r-lg); font-weight:700; font-size:15px; cursor:pointer; transition:all .2s; display:flex; align-items:center; gap:8px; }
    .ad-search-btn:hover { background:var(--tc-d); transform:translateY(-1px); box-shadow:0 8px 20px rgba(37,99,235,.3); }

    /* FILTERS */
    .ad-filters { margin-top:-40px; position:relative; z-index:10; padding:0 20px; margin-bottom:60px; }
    .ad-filters-box { background:var(--white); border-radius:var(--r-lg); padding:24px; box-shadow:0 12px 40px rgba(15,23,42,.06); border:1px solid var(--sand-d); display:flex; flex-wrap:wrap; gap:20px; align-items:flex-end; max-width:1100px; margin:0 auto; }
    .ad-f-group { flex:1; min-width:200px; }
    .ad-f-label { display:block; font-size:11px; font-weight:800; text-transform:uppercase; color:var(--ink-4); letter-spacing:.05em; margin-bottom:8px; }
    .ad-f-select, .ad-f-input { width:100%; padding:12px 16px; background:var(--cream); border:1px solid var(--sand-d); border-radius:var(--r-md); font-family:var(--font-b); font-size:14px; color:var(--ink); outline:none; transition:all .2s; appearance:none; }
    .ad-f-select:focus, .ad-f-input:focus { border-color:var(--tc); background:var(--white); box-shadow:0 0 0 3px rgba(37,99,235,.1); }
    .ad-f-btn { padding:0 32px; background:linear-gradient(135deg, var(--tc), var(--tc-l)); color:var(--white); border:none; border-radius:var(--r-md); font-weight:700; cursor:pointer; transition:all .2s; height:48px; box-shadow:0 4px 12px rgba(37,99,235,.25); }
    .ad-f-btn:hover { transform:translateY(-1px); box-shadow:0 8px 20px rgba(37,99,235,.35); }
    .ad-f-clear { padding:0 24px; background:var(--white); color:var(--ink-3); border:1px solid var(--sand-d); border-radius:var(--r-md); font-weight:600; cursor:pointer; transition:all .2s; height:48px; text-decoration:none; display:inline-flex; align-items:center; }
    .ad-f-clear:hover { background:var(--sand); color:var(--ink); border-color:var(--ink-4); }

    /* GRID */
    .ad-grid-sec { padding-bottom:80px; }
    .ad-container { max-width:1200px; margin:0 auto; padding:0 20px; }
    .ad-stats-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:32px; flex-wrap:wrap; gap:16px; padding:0 10px; }
    .ad-count { font-size:16px; font-weight:600; color:var(--ink-3); }
    .ad-count span { color:var(--tc); font-weight:800; font-size:18px; }
    .ad-add-btn { background:var(--white); color:var(--tc); border:1px solid var(--tc-l); padding:10px 24px; border-radius:var(--r-md); text-decoration:none; font-weight:700; font-size:14px; display:inline-flex; align-items:center; gap:8px; transition:all .2s; }
    .ad-add-btn:hover { background:var(--tc-ll); transform:translateY(-1px); }

    .ad-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:28px; }
    
    /* CARD */
    .ad-card { background:var(--white); border-radius:var(--r-lg); border:1px solid rgba(255,255,255,.5); box-shadow:0 4px 6px -1px rgba(0,0,0,.02), 0 2px 4px -1px rgba(0,0,0,.02); overflow:hidden; transition:all .3s var(--ease); position:relative; display:flex; flex-direction:column; }
    .ad-card:hover { transform:translateY(-8px); box-shadow:0 20px 40px -4px rgba(15,23,42,.08), 0 8px 16px -4px rgba(15,23,42,.04); border-color:rgba(37,99,235,.15); }
    
    .ad-c-head { height:110px; background:linear-gradient(135deg, var(--ink) 0%, #1e293b 100%); position:relative; }
    .ad-c-head::before { content:''; position:absolute; inset:0; background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px); background-size:20px 20px; opacity:0.2; }
    .ad-c-head::after { content:''; position:absolute; bottom:0; left:0; right:0; height:40px; background:linear-gradient(to bottom, transparent, var(--white)); opacity:.1; }
    
    .ad-c-body { padding:0 28px 28px; margin-top:-55px; flex:1; display:flex; flex-direction:column; position:relative; z-index:2; }
    .ad-c-top { display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:12px; }
    .ad-avatar { width:110px; height:110px; border-radius:50%; border:4px solid var(--white); background:var(--sand); object-fit:cover; box-shadow:0 8px 20px rgba(0,0,0,.08); transition:transform .3s; }
    .ad-card:hover .ad-avatar { transform:scale(1.05); }
    .ad-verified { width:26px; height:26px; background:var(--tc); color:var(--white); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; border:2.5px solid var(--white); position:absolute; bottom:4px; right:4px; box-shadow:0 2px 6px rgba(37,99,235,.3); }
    
    .ad-name { font-family:var(--font-h); font-size:24px; color:var(--ink); margin-bottom:2px; line-height:1.2; letter-spacing:-0.01em; }
    .ad-co { font-size:13px; font-weight:600; color:var(--tc); margin-bottom:14px; display:inline-block; background:var(--tc-ll); padding:4px 10px; border-radius:20px; }
    .ad-bio { font-size:14px; color:var(--ink-3); line-height:1.6; margin-bottom:20px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:44px; }
    
    .ad-tags { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:24px; }
    .ad-tag { font-size:11px; font-weight:600; color:var(--ink-2); background:var(--sand); padding:5px 12px; border-radius:20px; border:1px solid var(--sand-d); }
    
    .ad-stats { display:flex; gap:0; margin-top:auto; padding:16px 0; border-top:1px solid var(--sand); border-bottom:1px solid var(--sand); margin-bottom:20px; }
    .ad-stat { flex:1; text-align:center; position:relative; }
    .ad-stat:not(:last-child)::after { content:''; position:absolute; right:0; top:10%; height:80%; width:1px; background:var(--sand-d); }
    .ad-stat-val { font-size:18px; font-weight:800; color:var(--ink); display:block; font-family:var(--font-h); }
    .ad-stat-lbl { font-size:10px; color:var(--ink-4); text-transform:uppercase; font-weight:700; letter-spacing:.05em; margin-top:2px; display:block; }
    
    .ad-actions { display:flex; gap:10px; }
    .ad-btn-view { flex:1; background:linear-gradient(135deg, var(--tc), var(--tc-l)); color:var(--white); text-decoration:none; display:flex; align-items:center; justify-content:center; padding:12px; border-radius:var(--r-md); font-weight:700; font-size:14px; transition:all .2s; box-shadow:0 4px 12px rgba(37,99,235,.2); }
    .ad-btn-view:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(37,99,235,.3); color:var(--white); }
    .ad-btn-icon { width:46px; background:var(--white); color:var(--ink-3); border:1px solid var(--sand-d); border-radius:var(--r-md); cursor:pointer; transition:all .2s; display:flex; align-items:center; justify-content:center; font-size:16px; }
    .ad-btn-icon:hover { border-color:var(--tc); color:var(--tc); background:var(--tc-ll); }

    .ad-empty { text-align:center; padding:80px 20px; grid-column:1/-1; }
    .ad-empty-icon { font-size:48px; color:var(--ink-4); margin-bottom:20px; opacity:.5; }
    .ad-empty h3 { font-family:var(--font-h); font-size:24px; color:var(--ink); margin-bottom:8px; }
    .ad-empty p { color:var(--ink-3); max-width:400px; margin:0 auto 24px; }
    
    /* PAGINATION */
    .ad-pagination { margin-top:60px; display:flex; justify-content:center; }
    .pagination { display:flex; gap:6px; list-style:none; padding:0; }
    .page-item .page-link { width:40px; height:40px; display:flex; align-items:center; justify-content:center; border-radius:10px; background:var(--white); color:var(--ink); text-decoration:none; font-weight:700; border:1px solid var(--sand-d); transition:all .2s; }
    .page-item.active .page-link { background:var(--tc); color:var(--white); border-color:var(--tc); }
    .page-item.disabled .page-link { opacity:.5; pointer-events:none; }
</style>

<div class="ad-page">
    <!-- Hero Section -->
    <div class="ad-hero">
        <div class="ad-hero-bg"></div>
        <div class="ad-hero-mesh"></div>
        <div class="ad-hero-content">
            <div class="ad-icon-box">
                <i class="fas fa-users-viewfinder"></i>
            </div>
            <h1 class="ad-title">Elite Agent Directory</h1>
            <p class="ad-subtitle">Connect with top-tier real estate professionals who can help you navigate the market with confidence and expertise on the EWAN platform.</p>
            
            <form action="{{ route('agents.directory') }}" method="GET" class="ad-search-wrap">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, license, or location..." class="ad-search-input">
                <button type="submit" class="ad-search-btn">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="ad-filters">
        <form action="{{ route('agents.directory') }}" method="GET" class="ad-filters-box">
            <input type="hidden" name="search" value="{{ request('search') }}">
            
            <div class="ad-f-group">
                <label class="ad-f-label"><i class="fas fa-briefcase"></i> Specialization</label>
                <select name="specialization" class="ad-f-select">
                    <option value="">All Specializations</option>
                    @foreach($specializations as $specialization)
                        <option value="{{ $specialization }}" {{ request('specialization') == $specialization ? 'selected' : '' }}>
                            {{ ucfirst($specialization) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="ad-f-group">
                <label class="ad-f-label"><i class="fas fa-map-pin"></i> Location</label>
                <input type="text" name="location" value="{{ request('location') }}" placeholder="City, Zip..." class="ad-f-input">
            </div>

            <div class="ad-f-group">
                <label class="ad-f-label"><i class="fas fa-star"></i> Rating</label>
                <select name="rating" class="ad-f-select">
                    <option value="">Any Rating</option>
                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                </select>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="ad-f-btn">Apply</button>
                @if(request()->hasAny(['specialization', 'location', 'rating', 'search']))
                    <a href="{{ route('agents.directory') }}" class="ad-f-clear">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Grid Section -->
    <div class="ad-grid-sec">
        <div class="ad-container">
            <div class="ad-stats-bar">
                <div class="ad-count">
                    Found <span>{{ $agents->total() ?? 0 }}</span> Professionals
                </div>
                <a href="{{ route('agents.create') }}" class="ad-add-btn">
                    <i class="fas fa-plus-circle"></i> Join as Agent
                </a>
            </div>

            @if($agents->count() > 0)
                <div class="ad-grid">
                    @foreach($agents as $agent)
                        <div class="ad-card">
                            <div class="ad-c-head"></div>
                            <div class="ad-c-body">
                                <div class="ad-c-top">
                                    <div style="position:relative;">
                                        @if($agent->user->avatar_url)
                                            <img src="{{ $agent->user->avatar_url }}" alt="{{ $agent->user->name }}" class="ad-avatar">
                                        @else
                                            <div class="ad-avatar" style="display:flex; align-items:center; justify-content:center; background:var(--ink-4); color:var(--white); font-size:32px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                        <div class="ad-verified"><i class="fas fa-check"></i></div>
                                    </div>
                                    <div style="text-align:right;">
                                        @if($agent->reviews()->count() > 0)
                                            <div style="color:var(--gold); font-size:14px; font-weight:700;">
                                                <i class="fas fa-star"></i> {{ number_format($agent->reviews()->avg('rating') ?? 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <h3 class="ad-name">{{ $agent->user->name }}</h3>
                                @if($agent->company)
                                    <span class="ad-co">{{ $agent->company->name }}</span>
                                @endif
                                
                                <div class="ad-bio">
                                    {{ $agent->profile && $agent->profile->bio ? Str::limit($agent->profile->bio, 90) : 'Experienced real estate professional dedicated to helping clients achieve their property goals.' }}
                                </div>

                                @if($agent->profile && $agent->profile->specializations)
                                    <div class="ad-tags">
                                        @foreach(array_slice($agent->profile->specializations, 0, 3) as $spec)
                                            <span class="ad-tag">{{ ucfirst($spec) }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="ad-stats">
                                    <div class="ad-stat">
                                        <span class="ad-stat-val">{{ $agent->properties()->where('status', 'sold')->count() }}</span>
                                        <span class="ad-stat-lbl">Sold</span>
                                    </div>
                                    <div class="ad-stat">
                                        <span class="ad-stat-val">{{ $agent->properties()->count() }}</span>
                                        <span class="ad-stat-lbl">Listed</span>
                                    </div>
                                    <div class="ad-stat">
                                        <span class="ad-stat-val">{{ $agent->experience_years ?? '5+' }}</span>
                                        <span class="ad-stat-lbl">Years</span>
                                    </div>
                                </div>

                                <div class="ad-actions">
                                    <a href="{{ route('agents.show', $agent) }}" class="ad-btn-view">View Profile</a>
                                    <button class="ad-btn-icon"><i class="fas fa-envelope"></i></button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="ad-pagination">
                    {{ $agents->links() }}
                </div>
            @else
                <div class="ad-empty">
                    <div class="ad-empty-icon"><i class="fas fa-search"></i></div>
                    <h3>No Agents Found</h3>
                    <p>We couldn't find any agents matching your criteria. Try adjusting your filters.</p>
                    <a href="{{ route('agents.directory') }}" class="ad-f-btn" style="display:inline-block; text-decoration:none;">Clear Filters</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
