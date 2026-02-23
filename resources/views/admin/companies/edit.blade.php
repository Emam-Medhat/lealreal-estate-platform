@extends('layouts.app')

@section('title', 'Edit • ' . $company->name)

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

<style>
/* ── نفس الـ variables والـ global styles ── */
:root{
  --B:#2563eb;--Bd:#1d4ed8;--Bxd:#1e3a8a;--Bl:#3b82f6;--Bxl:#60a5fa;
  --B100:#dbeafe;--B50:#eff6ff;
  --ink:#0f172a;--ink2:#1e293b;--ink3:#334155;
  --mu:#64748b;--mul:#94a3b8;
  --bdr:#e2e8f0;--bdrf:#cbd5e1;
  --sur:#fff;--bg:#f8fafc;
  --G:#10b981;--Gbg:#d1fae5;--Gtx:#065f46;
  --R:#ef4444;--Rbg:#fee2e2;--Rtx:#991b1b;
  --A:#f59e0b;--Abg:#fef3c7;--Atx:#92400e;
  --rad:14px;--rads:9px;
  --shsm:0 1px 4px rgba(15,23,42,.08);
  --shmd:0 4px 18px rgba(15,23,42,.1);
  --shblue:0 8px 28px rgba(37,99,235,.3);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
.cc{font-family:'DM Sans',system-ui,sans-serif;background:var(--bg);min-height:100vh;color:var(--ink)}

/* Hero ── خفيف لصفحة Edit */
.cc-hero{background:linear-gradient(140deg,#0f172a 0%,#1e3a8a 48%,#2563eb 100%);position:relative;overflow:hidden}
.cc-hero::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.5) 50%,transparent)}
.cc-glow1{position:absolute;right:-100px;top:-70px;width:520px;height:520px;border-radius:50%;background:radial-gradient(circle,rgba(96,165,250,.18) 0%,transparent 70%)}
.cc-hero-in{position:relative;z-index:1;max-width:1160px;margin:0 auto;padding:40px 48px 24px}
.cc-h1{font-family:'Bricolage Grotesque',sans-serif;font-size:clamp(28px,4.2vw,48px);font-weight:800;color:#fff;line-height:1.05;letter-spacing:-.02em}
.cc-h1 em{font-style:normal;background:linear-gradient(135deg,#93c5fd,#60a5fa);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.cc-sub{font-size:14px;color:rgba(255,255,255,.65);margin-top:6px}
.cc-bc{display:flex;align-items:center;gap:8px;font-size:12px;color:rgba(255,255,255,.55);margin-bottom:14px}
.cc-bc a{color:rgba(255,255,255,.65);text-decoration:none;transition:color .15s}
.cc-bc a:hover{color:#fff}
.cc-bc i{font-size:9px}

/* Layout */
.cc-layout{max-width:1160px;margin:-32px auto 80px;padding:0 48px;position:relative}
.cc-panel{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--rad);box-shadow:var(--shmd);overflow:hidden;margin-bottom:24px}
.cc-ph{padding:20px 26px;border-bottom:1px solid var(--bdr);background:linear-gradient(135deg,var(--bg),var(--sur));display:flex;align-items:center;gap:14px}
.cc-ph-ic{width:44px;height:44px;border-radius:12px;background:var(--B50);border:1.5px solid var(--B100);display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--B);flex-shrink:0}
.cc-ph-t{font-family:'Bricolage Grotesque',sans-serif;font-size:19px;font-weight:800;color:var(--ink)}
.cc-pb{padding:26px}

/* Fields ── نفس الستايل السابق */
.fi,.fs,.ft{width:100%;padding:11px 14px;border:1.5px solid var(--bdr);border-radius:var(--rads);font-size:13.5px;color:var(--ink);background:var(--bg);outline:none;transition:all .2s}
.fi:hover,.fs:hover,.ft:hover{border-color:var(--bdrf);background:var(--sur)}
.fi:focus,.fs:focus,.ft:focus{border-color:var(--B);background:var(--sur);box-shadow:0 0 0 3px rgba(37,99,235,.12)}
.fi::placeholder,.ft::placeholder{color:var(--mul)}
.fi.err,.fs.err{border-color:var(--R);background:#fff9f9}
.ft{resize:vertical;min-height:100px}
.fs{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;padding-right:40px}
.ferr{display:flex;align-items:center;gap:6px;font-size:11.5px;color:var(--R);margin-top:5px}
.ferr i{font-size:10px}
.fhint{font-size:11.5px;color:var(--mul);margin-top:5px;display:flex;align-items:center;gap:6px}
.fhint i{font-size:10px}

/* Toggles */
.cc-tgl{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border:1.5px solid var(--bdr);border-radius:var(--rads);background:var(--bg);cursor:pointer;transition:all .2s}
.cc-tgl:hover{border-color:var(--bdrf);background:var(--sur)}
.cc-tgl.on{border-color:var(--B100);background:var(--B50)}
.cc-tgl-l{font-size:14px;font-weight:700;color:var(--ink3)}
.cc-tgl.on .cc-tgl-l{color:var(--B)}
.cc-sw{position:relative;width:44px;height:24px;flex-shrink:0}
.cc-sw input{opacity:0;width:0;height:0}
.cc-sw-t{position:absolute;inset:0;background:var(--bdrf);border-radius:12px;transition:background .2s}
.cc-sw input:checked~.cc-sw-t{background:var(--B)}
.cc-sw-t::after{content:'';position:absolute;top:3px;left:3px;width:18px;height:18px;background:#fff;border-radius:50%;box-shadow:0 1px 4px rgba(0,0,0,.18);transition:transform .24s cubic-bezier(.34,1.56,.64,1)}
.cc-sw input:checked~.cc-sw-t::after{transform:translateX(20px)}

/* Upload Zones */
.upz{border:2px dashed var(--bdrf);border-radius:var(--rads);padding:28px;text-align:center;cursor:pointer;transition:all .2s;background:var(--bg);position:relative}
.upz:hover,.upz.focus{border-color:var(--B);background:var(--B50)}
.upz-ic{width:52px;height:52px;border-radius:14px;background:var(--B50);border:1.5px solid var(--B100);display:flex;align-items:center;justify-content:center;font-size:20px;color:var(--B);margin:0 auto 12px}
.upz-t{font-size:14.5px;font-weight:700;color:var(--ink3);margin-bottom:4px}
.upz-s{font-size:12px;color:var(--mul);line-height:1.5}
.upz-prev{display:none;width:88px;height:88px;border-radius:12px;object-fit:cover;border:2px solid var(--B100);margin:16px auto 0}

/* Nav */
.cc-nav{display:flex;align-items:center;justify-content:space-between;padding:18px 26px;border-top:1px solid var(--bdr);background:var(--bg)}
.cc-btn{display:inline-flex;align-items:center;gap:8px;padding:11px 22px;border-radius:var(--rads);font-weight:700;font-size:14px;transition:all .18s;cursor:pointer}
.cc-btn-cancel{border:1.5px solid var(--bdr);color:var(--ink3);background:var(--sur)}
.cc-btn-cancel:hover{border-color:var(--B);color:var(--B)}
.cc-btn-save{background:var(--B);color:#fff;box-shadow:var(--shblue)}
.cc-btn-save:hover{background:var(--Bd);transform:translateY(-1px)}
</style>

<div class="cc">

  {{-- Hero --}}
  <div class="cc-hero">
    <div class="cc-glow1"></div>
    <div class="cc-hero-in">
      <div class="cc-bc">
        <a href="/"><i class="fas fa-home"></i></a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('admin.dashboard') }}">Admin</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('admin.companies.index') }}">Companies</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('admin.companies.show', $company->id) }}">{{ $company->name }}</a>
        <i class="fas fa-chevron-right"></i>
        <span>Edit</span>
      </div>
      <h1 class="cc-h1">Edit <em>{{ $company->name }}</em></h1>
      <p class="cc-sub">Update company profile, business details, media, verification status and API settings.</p>
    </div>
  </div>

  {{-- Form --}}
  <form action="{{ route('admin.companies.update', $company->id) }}" method="POST" enctype="multipart/form-data" class="cc-layout" novalidate>
    @csrf
    @method('PUT')

    @if(session('success'))
      <div class="cc-alert suc" style="margin-bottom:24px">
        <i class="fas fa-circle-check"></i>
        <div>{{ session('success') }}</div>
      </div>
    @endif

    @if($errors->any())
      <div class="cc-alert err" style="margin-bottom:24px">
        <i class="fas fa-circle-exclamation"></i>
        <div>
          <strong>Please fix these errors:</strong>
          <ul style="margin-top:8px; padding-left:20px; font-size:13px">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    {{-- Basic Information --}}
    <div class="cc-panel">
      <div class="cc-ph">
        <div class="cc-ph-ic"><i class="fas fa-building"></i></div>
        <div><div class="cc-ph-t">Basic Information</div></div>
      </div>
      <div class="cc-pb">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-20">

          <div class="md:col-span-2">
            <div class="fl">Company Name <span class="fl-r">*</span></div>
            <input type="text" name="name" value="{{ old('name', $company->name) }}" class="fi {{ $errors->has('name')?'err':'' }}" required>
            @error('name')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
          </div>

          <div>
            <div class="fl">Email <span class="fl-o">Optional</span></div>
            <input type="email" name="email" value="{{ old('email', $company->email) }}" class="fi">
            @error('email')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
          </div>

          <div>
            <div class="fl">Phone <span class="fl-o">Optional</span></div>
            <input type="tel" name="phone" value="{{ old('phone', $company->phone) }}" class="fi">
            @error('phone')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
          </div>

          <div>
            <div class="fl">Website <span class="fl-o">Optional</span></div>
            <input type="url" name="website" value="{{ old('website', $company->website) }}" class="fi" placeholder="https://example.com">
            @error('website')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
          </div>

          <div>
            <div class="fl">Company Type <span class="fl-r">*</span></div>
            <select name="type" class="fs {{ $errors->has('type')?'err':'' }}" required>
              <option value="">— Select type —</option>
              <option value="developer" {{ old('type', $company->type)=='developer'?'selected':'' }}>🏗️ Developer</option>
              <option value="brokerage" {{ old('type', $company->type)=='brokerage'?'selected':'' }}>🤝 Brokerage</option>
              <option value="property_management" {{ old('type', $company->type)=='property_management'?'selected':'' }}>🏢 Property Management</option>
              <option value="construction" {{ old('type', $company->type)=='construction'?'selected':'' }}>🔨 Construction</option>
              <option value="investment" {{ old('type', $company->type)=='investment'?'selected':'' }}>📈 Investment</option>
              <option value="consulting" {{ old('type', $company->type)=='consulting'?'selected':'' }}>💼 Consulting</option>
            </select>
            @error('type')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
          </div>

          <div>
            <div class="fl">Status <span class="fl-r">*</span></div>
            <select name="status" class="fs {{ $errors->has('status')?'err':'' }}" required>
              <option value="pending" {{ old('status', $company->status)=='pending'?'selected':'' }}> Pending</option>
              <option value="active" {{ old('status', $company->status)=='active'?'selected':'' }}> Active</option>
              <option value="suspended" {{ old('status', $company->status)=='suspended'?'selected':'' }}> Suspended</option>
              <option value="rejected" {{ old('status', $company->status)=='rejected'?'selected':'' }}> Rejected</option>
            </select>
            @error('status')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
          </div>

          <div class="md:col-span-2">
            <div class="fl">Description <span class="fl-o">Optional</span></div>
            <textarea name="description" class="ft" placeholder="Company overview and highlights…">{{ old('description', $company->description) }}</textarea>
            @error('description')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
            <div class="fhint"><i class="fas fa-circle-info"></i>Max 500 characters – shown on public profile</div>
          </div>

        </div>
      </div>
    </div>

    {{-- Business Details --}}
    <div class="cc-panel">
      <div class="cc-ph">
        <div class="cc-ph-ic"><i class="fas fa-chart-line"></i></div>
        <div><div class="cc-ph-t">Business Details</div></div>
      </div>
      <div class="cc-pb grid grid-cols-1 md:grid-cols-2 gap-20">

        <div>
          <div class="fl">Registration Number <span class="fl-o">Optional</span></div>
          <input type="text" name="registration_number" value="{{ old('registration_number', $company->registration_number) }}" class="fi">
          @error('registration_number')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

        <div>
          <div class="fl">Tax ID <span class="fl-o">Optional</span></div>
          <input type="text" name="tax_id" value="{{ old('tax_id', $company->tax_id) }}" class="fi">
          @error('tax_id')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

        <div>
          <div class="fl">Founded Date <span class="fl-o">Optional</span></div>
          <input type="date" name="founded_date" value="{{ old('founded_date', $company->founded_date) }}" class="fi">
          @error('founded_date')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

        <div>
          <div class="fl">Employee Count <span class="fl-r">*</span></div>
          <input type="number" name="employee_count" value="{{ old('employee_count', $company->employee_count) }}" class="fi" min="1" required>
          @error('employee_count')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

        <div>
          <div class="fl">Annual Revenue <span class="fl-o">Optional</span></div>
          <div class="fi-pfx">
            <span class="fi-pfx-s">$</span>
            <input type="number" name="annual_revenue" value="{{ old('annual_revenue', $company->annual_revenue) }}" step="0.01" class="fi" style="padding-left:28px">
            <span class="fi-sfx">USD</span>
          </div>
          @error('annual_revenue')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

        <div>
          <div class="fl">Subscription Plan <span class="fl-r">*</span></div>
          <select name="subscription_plan" class="fs {{ $errors->has('subscription_plan')?'err':'' }}" required>
            <option value="">— Select plan —</option>
            <option value="basic" {{ old('subscription_plan', $company->subscription_plan)=='basic'?'selected':'' }}>Basic</option>
            <option value="professional" {{ old('subscription_plan', $company->subscription_plan)=='professional'?'selected':'' }}>Professional</option>
            <option value="enterprise" {{ old('subscription_plan', $company->subscription_plan)=='enterprise'?'selected':'' }}>Enterprise</option>
          </select>
          @error('subscription_plan')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

      </div>
    </div>

    {{-- Address --}}
    <div class="cc-panel">
      <div class="cc-ph">
        <div class="cc-ph-ic"><i class="fas fa-map-marker-alt"></i></div>
        <div><div class="cc-ph-t">Address Information</div></div>
      </div>
      <div class="cc-pb grid grid-cols-1 md:grid-cols-2 gap-20">

        <div class="md:col-span-2">
          <div class="fl">Street Address <span class="fl-o">Optional</span></div>
          <input type="text" name="address" value="{{ old('address', $company->address) }}" class="fi">
          @error('address')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

        <div>
          <div class="fl">City <span class="fl-o">Optional</span></div>
          <input type="text" name="city" value="{{ old('city', $company->city) }}" class="fi">
          @error('city')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

        <div>
          <div class="fl">State / Province <span class="fl-o">Optional</span></div>
          <input type="text" name="state" value="{{ old('state', $company->state) }}" class="fi">
          @error('state')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

        <div>
          <div class="fl">Country <span class="fl-o">Optional</span></div>
          <input type="text" name="country" value="{{ old('country', $company->country ?? 'Saudi Arabia') }}" class="fi">
          @error('country')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

        <div>
          <div class="fl">Postal Code <span class="fl-o">Optional</span></div>
          <input type="text" name="postal_code" value="{{ old('postal_code', $company->postal_code) }}" class="fi">
          @error('postal_code')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

      </div>
    </div>

    {{-- Verification --}}
    <div class="cc-panel">
      <div class="cc-ph">
        <div class="cc-ph-ic"><i class="fas fa-shield-check"></i></div>
        <div><div class="cc-ph-t">Verification & Features</div></div>
      </div>
      <div class="cc-pb grid grid-cols-1 md:grid-cols-2 gap-20">

        <div class="md:col-span-2">
          <label class="cc-tgl {{ old('is_featured', $company->is_featured)?'on':'' }}">
            <div>
              <div class="cc-tgl-l"><i class="fas fa-star" style="color:var(--A);margin-right:8px"></i>Featured Company</div>
              <div class="cc-tgl-d">Highlight in search & featured sections</div>
            </div>
            <label class="cc-sw">
              <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $company->is_featured)?'checked':'' }}>
              <span class="cc-sw-t"></span>
            </label>
          </label>
        </div>

        <div class="md:col-span-2">
          <label class="cc-tgl {{ old('is_verified', $company->is_verified)?'on':'' }}">
            <div>
              <div class="cc-tgl-l"><i class="fas fa-circle-check" style="color:var(--B);margin-right:8px"></i>Verified Company</div>
              <div class="cc-tgl-d">Display verification badge on profile</div>
            </div>
            <label class="cc-sw">
              <input type="checkbox" name="is_verified" value="1" {{ old('is_verified', $company->is_verified)?'checked':'' }}>
              <span class="cc-sw-t"></span>
            </label>
          </label>
        </div>

        <div>
          <div class="fl">Verification Level <span class="fl-r">*</span></div>
          <select name="verification_level" class="fs {{ $errors->has('verification_level')?'err':'' }}" required>
            <option value="0" {{ old('verification_level', $company->verification_level)==0?'selected':'' }}>None</option>
            <option value="1" {{ old('verification_level', $company->verification_level)==1?'selected':'' }}>Basic</option>
            <option value="2" {{ old('verification_level', $company->verification_level)==2?'selected':'' }}>Standard</option>
            <option value="3" {{ old('verification_level', $company->verification_level)==3?'selected':'' }}>Premium</option>
          </select>
          @error('verification_level')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

        <div>
          <div class="fl">Rating <span class="fl-r">*</span></div>
          <input type="number" name="rating" step="0.1" min="0" max="5" value="{{ old('rating', $company->rating ?? 0) }}" class="fi" required>
          @error('rating')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

        <div>
          <div class="fl">Total Reviews <span class="fl-r">*</span></div>
          <input type="number" name="total_reviews" min="0" value="{{ old('total_reviews', $company->total_reviews ?? 0) }}" class="fi" required>
          @error('total_reviews')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

        <div>
          <div class="fl">Subscription Expiry <span class="fl-o">Optional</span></div>
          <input type="datetime-local" name="subscription_expires_at" value="{{ old('subscription_expires_at', $company->subscription_expires_at ? $company->subscription_expires_at->format('Y-m-d\TH:i') : '') }}" class="fi">
          @error('subscription_expires_at')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

      </div>
    </div>

    {{-- Media --}}
    <div class="cc-panel">
      <div class="cc-ph">
        <div class="cc-ph-ic"><i class="fas fa-photo-film"></i></div>
        <div><div class="cc-ph-t">Media Files</div></div>
      </div>
      <div class="cc-pb grid grid-cols-1 md:grid-cols-2 gap-24">

        <div>
          <div class="fl">Company Logo <span class="fl-o">Optional</span></div>
          <div class="upz" id="upz-logo">
            <input type="file" name="logo_url" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
            <div class="upz-ic"><i class="fas fa-building"></i></div>
            <div class="upz-t">Drop logo or click to browse</div>
            <div class="upz-s">PNG, JPG, SVG · max 2MB<br>Recommended: 200×200 px</div>
            @if($company->logo_url)
              <img src="{{ asset('storage/'.$company->logo_url) }}" class="upz-prev" style="display:block">
            @endif
          </div>
          @error('logo_url')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

        <div>
          <div class="fl">Cover Image <span class="fl-o">Optional</span></div>
          <div class="upz" id="upz-cover">
            <input type="file" name="cover_image_url" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
            <div class="upz-ic"><i class="fas fa-image"></i></div>
            <div class="upz-t">Drop cover or click to browse</div>
            <div class="upz-s">PNG, JPG · max 5MB<br>Recommended: 1200×400 px</div>
            @if($company->cover_image_url)
              <img src="{{ asset('storage/'.$company->cover_image_url) }}" class="upz-prev" style="display:block">
            @endif
          </div>
          @error('cover_image_url')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

      </div>
    </div>

    {{-- API --}}
    <div class="cc-panel">
      <div class="cc-ph">
        <div class="cc-ph-ic"><i class="fas fa-key"></i></div>
        <div><div class="cc-ph-t">API Settings</div></div>
      </div>
      <div class="cc-pb grid grid-cols-1 md:grid-cols-2 gap-20">

        <div>
          <div class="fl">API Key <span class="fl-o">Optional</span></div>
          <input type="text" name="api_key" value="{{ old('api_key', $company->api_key) }}" class="fi font-mono text-sm" placeholder="Leave empty to keep current">
          <div class="fhint"><i class="fas fa-circle-info"></i>Changing this will invalidate existing integrations</div>
          @error('api_key')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

        <div>
          <div class="fl">Webhook URL <span class="fl-o">Optional</span></div>
          <input type="url" name="webhook_url" value="{{ old('webhook_url', $company->webhook_url) }}" class="fi" placeholder="https://yourapp.com/webhooks">
          <div class="fhint"><i class="fas fa-circle-info"></i>For receiving real-time company events</div>
          @error('webhook_url')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        </div>

      </div>

      <div class="cc-nav">
        <a href="{{ route('admin.companies.show', $company->id) }}" class="cc-btn cc-btn-cancel">
          <i class="fas fa-arrow-left"></i> Cancel
        </a>
        <button type="submit" class="cc-btn cc-btn-save">
          <i class="fas fa-save"></i> Update Company
        </button>
      </div>
    </div>

  </form>

</div>

@endsection