@extends('layouts.app')
@section('title', $company->name . ' • Details')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

<style>
/* ── نفس الـ :root والـ global styles من صفحة الإنشاء ── */
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
  --V:#8b5cf6;--Vbg:#ede9fe;--Vtx:#5b21b6;
  --rad:14px;--rads:9px;
  --shsm:0 1px 4px rgba(15,23,42,.08);
  --shmd:0 4px 18px rgba(15,23,42,.1);
  --shblue:0 8px 28px rgba(37,99,235,.3);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
.cc{font-family:'DM Sans',system-ui,sans-serif;background:var(--bg);min-height:100vh;color:var(--ink)}

/* Hero & Wave (مُعدّل ليكون أخف هنا) */
.cc-hero{background:linear-gradient(140deg,#0f172a 0%,#1e3a8a 48%,#2563eb 100%);position:relative;overflow:hidden;padding-bottom:60px}
.cc-hero::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.5) 50%,transparent)}
.cc-glow1{position:absolute;right:-120px;top:-80px;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(96,165,250,.18) 0%,transparent 70%)}
.cc-hero-in{position:relative;z-index:1;max-width:1160px;margin:0 auto;padding:40px 48px 20px}
.cc-h1{font-family:'Bricolage Grotesque',sans-serif;font-size:clamp(28px,4vw,52px);font-weight:800;color:#fff;line-height:1.05;letter-spacing:-.02em}
.cc-h1 em{font-style:normal;background:linear-gradient(135deg,#93c5fd,#60a5fa);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.cc-sub{font-size:14px;color:rgba(255,255,255,.6);margin-top:8px;max-width:520px}
.cc-wave{position:absolute;bottom:0;left:0;right:0;height:80px;pointer-events:none}

/* Breadcrumb */
.cc-bc{display:flex;align-items:center;gap:8px;font-size:12px;color:rgba(255,255,255,.5);margin-bottom:16px}
.cc-bc a{color:rgba(255,255,255,.6);text-decoration:none;transition:color .15s}
.cc-bc a:hover{color:#fff}
.cc-bc i{font-size:9px}

/* Layout */
.cc-layout{max-width:1160px;margin:0 auto;padding:0 48px 80px;position:relative}
.cc-grid{display:grid;grid-template-columns:1fr 1fr;gap:32px;align-items:start}
@media(max-width:1024px){.cc-grid{grid-template-columns:1fr}}

/* Cards & Panels */
.cc-card{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--rad);box-shadow:var(--shmd);overflow:hidden}
.cc-ch{padding:20px 24px;border-bottom:1px solid var(--bdr);background:linear-gradient(135deg,var(--bg),var(--sur));display:flex;align-items:center;gap:12px}
.cc-ch-ic{width:44px;height:44px;border-radius:12px;background:var(--B50);border:1.5px solid var(--B100);display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--B);flex-shrink:0}
.cc-ch-t{font-family:'Bricolage Grotesque',sans-serif;font-size:18px;font-weight:800;color:var(--ink)}
.cc-cb{padding:24px}

/* Status Badges */
.st-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:50px;font-size:12.5px;font-weight:700}
.st-active{background:var(--Gbg);color:var(--Gtx)}
.st-pending{background:var(--Abg);color:var(--Atx)}
.st-suspended{background:var(--Rbg);color:var(--Rtx)}
.st-rejected{background:#f3f4f6;color:#6b7280}
.st-verified{background:var(--Gbg);color:var(--Gtx)}
.st-featured{background:var(--Abg);color:var(--Atx)}

/* Info Rows */
.info-row{display:flex;align-items:flex-start;gap:12px;padding:12px 0;border-bottom:1px solid var(--bdrf)}
.info-row:last-child{border-bottom:none}
.info-lbl{font-size:12.5px;font-weight:700;color:var(--mu);min-width:140px;flex-shrink:0}
.info-val{font-size:13.5px;color:var(--ink3);font-weight:500;flex:1}
.info-val a{color:var(--B);text-decoration:none}
.info-val a:hover{text-decoration:underline}

/* Stats Grid */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:16px;text-align:center}
.stat-item{padding:16px;border-radius:var(--rads);background:var(--B50);border:1px solid var(--B100)}
.stat-num{font-family:'Bricolage Grotesque',sans-serif;font-size:28px;font-weight:800;color:var(--ink)}
.stat-lbl{font-size:12px;color:var(--mu);margin-top:4px}

/* Actions */
.cc-actions{display:flex;justify-content:space-between;align-items:center;padding:18px 24px;border-top:1px solid var(--bdr);background:var(--bg)}
.cc-btn{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:var(--rads);font-weight:700;cursor:pointer;transition:all .18s}
.cc-btn-edit{background:var(--B);color:#fff;box-shadow:var(--shblue)}
.cc-btn-edit:hover{background:var(--Bd);transform:translateY(-1px)}
.cc-btn-back{border:1.5px solid var(--bdr);background:var(--sur);color:var(--ink3)}
.cc-btn-back:hover{border-color:var(--B);color:var(--B)}
.cc-btn-del{background:var(--R);color:#fff}
.cc-btn-del:hover{background:#dc2626}

/* Cover */
.cover-img{width:100%;height:260px;object-fit:cover;border-radius:var(--rad);border:1px solid var(--bdr)}
</style>

<div class="cc">

  {{-- Hero --}}
  <div class="cc-hero">
    <div class="cc-glow1"></div>
    <div class="cc-hero-in">
      <div>
        <div class="cc-bc">
          <a href="/"><i class="fas fa-home"></i></a>
          <i class="fas fa-chevron-right"></i>
          <a href="{{ route('admin.dashboard') }}">Admin</a>
          <i class="fas fa-chevron-right"></i>
          <a href="{{ route('admin.companies.index') }}">Companies</a>
          <i class="fas fa-chevron-right"></i>
          <span>Details</span>
        </div>
        <h1 class="cc-h1">{{ $company->name }} <em>Profile</em></h1>
        <p class="cc-sub">Complete overview and management options for this real estate company.</p>
      </div>
    </div>
    <svg class="cc-wave" viewBox="0 0 1440 80" preserveAspectRatio="none" fill="#f8fafc">
      <path d="M0,80 C480,0 960,80 1440,0 L1440,80 L0,80Z"/>
    </svg>
  </div>

  {{-- Main Content --}}
  <div class="cc-layout">
    <div class="cc-grid">

      {{-- Left Column --}}
      <div class="space-y-6">

        {{-- Profile Card --}}
        <div class="cc-card">
          <div class="cc-ch">
            <div class="cc-ch-ic"><i class="fas fa-building"></i></div>
            <div><div class="cc-ch-t">Company Profile</div></div>
          </div>
          <div class="cc-cb">
            <div class="text-center pb-6">
              @if($company->logo_url)
                <img class="w-28 h-28 rounded-full mx-auto object-cover border-4 border-white shadow-md" src="{{ asset('storage/' . $company->logo_url) }}" alt="{{ $company->name }}">
              @else
                <div class="w-28 h-28 rounded-full bg-gradient-to-br from-blue-50 to-blue-100 mx-auto flex items-center justify-center shadow-inner border-4 border-white">
                  <span class="text-4xl font-extrabold text-blue-600 tracking-tight">
                    {{ strtoupper(substr(trim($company->name), 0, 2)) }}
                  </span>
                </div>
              @endif

              <h2 class="mt-5 text-2xl font-extrabold" style="color: var(--ink)">{{ $company->name }}</h2>

              <div class="mt-5 flex flex-wrap justify-center gap-3">
                <span class="st-badge bg-blue-50 text-blue-800">
                  {{ ucfirst(str_replace('_',' ', $company->type)) }}
                </span>

                <span class="st-badge 
                  @if($company->status=='active') st-active
                  @elseif($company->status=='pending') st-pending
                  @elseif($company->status=='suspended') st-suspended
                  @else st-rejected @endif">
                  {{ ucfirst($company->status) }}
                </span>

                @if($company->is_verified)
                  <span class="st-badge st-verified"><i class="fas fa-check"></i> Verified</span>
                @endif

                @if($company->is_featured)
                  <span class="st-badge st-featured"><i class="fas fa-star"></i> Featured</span>
                @endif
              </div>
            </div>

            <div class="stats-grid mt-4">
              <div class="stat-item">
                <div class="stat-num">{{ $company->profile->employee_count ?? $company->employee_count ?? 0 }}</div>
                <div class="stat-lbl">Employees</div>
              </div>
              <div class="stat-item">
                <div class="stat-num">{{ number_format($company->rating ?? 0, 1) }}</div>
                <div class="stat-lbl">Rating</div>
              </div>
              <div class="stat-item">
                <div class="stat-num">{{ $company->total_reviews ?? 0 }}</div>
                <div class="stat-lbl">Reviews</div>
              </div>
            </div>
          </div>
        </div>

        {{-- Contact Information --}}
        <div class="cc-card">
          <div class="cc-ch">
            <div class="cc-ch-ic"><i class="fas fa-address-card"></i></div>
            <div><div class="cc-ch-t">Contact Information</div></div>
          </div>
          <div class="cc-cb">
            <div class="space-y-1">
              <div class="info-row">
                <div class="info-lbl">Email</div>
                <div class="info-val">{{ $company->email ?? '—' }}</div>
              </div>
              <div class="info-row">
                <div class="info-lbl">Phone</div>
                <div class="info-val">{{ $company->phone ?? '—' }}</div>
              </div>
              <div class="info-row">
                <div class="info-lbl">Website</div>
                <div class="info-val">
                  @if($company->website)
                    <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
                  @else
                    —
                  @endif
                </div>
              </div>
              <div class="info-row">
                <div class="info-lbl">Founded</div>
                <div class="info-val">{{ $company->profile->founded_date ? \Carbon\Carbon::parse($company->profile->founded_date)->format('M Y') : ($company->founded_date ? \Carbon\Carbon::parse($company->founded_date)->format('M Y') : '—') }}</div>
              </div>
            </div>
          </div>
        </div>

        {{-- Business Details --}}
        <div class="cc-card">
          <div class="cc-ch">
            <div class="cc-ch-ic"><i class="fas fa-chart-line"></i></div>
            <div><div class="cc-ch-t">Business Details</div></div>
          </div>
          <div class="cc-cb">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="info-row">
                <div class="info-lbl">Reg. Number</div>
                <div class="info-val">{{ $company->registration_number ?? '—' }}</div>
              </div>
              <div class="info-row">
                <div class="info-lbl">Tax ID</div>
                <div class="info-val">{{ $company->tax_id ?? '—' }}</div>
              </div>
              <div class="info-row">
                <div class="info-lbl">Annual Revenue</div>
                <div class="info-val">${{ number_format($company->annual_revenue ?? 0, 2) }} USD</div>
              </div>
              <div class="info-row">
                <div class="info-lbl">Plan</div>
                <div class="info-val font-medium">{{ ucfirst($company->subscription_plan ?? '—') }}</div>
              </div>
            </div>
            @if($company->description ?? $company->profile->description)
              <div class="mt-6 pt-5 border-t" style="border-color: var(--bdrf)">
                <div class="info-lbl mb-2">Description</div>
                <p class="text-[13.5px] leading-relaxed" style="color: var(--ink3)" class="whitespace-pre-line">{{ $company->description ?? $company->profile->description }}</p>
              </div>
            @endif
          </div>
        </div>

        {{-- Cover Image --}}
        @if($company->cover_image_url)
          <div class="cc-card">
            <div class="cc-ch">
              <div class="cc-ch-ic"><i class="fas fa-image"></i></div>
              <div><div class="cc-ch-t">Cover Image</div></div>
            </div>
            <div class="cc-cb p-3">
              <img src="{{ asset('storage/' . $company->cover_image_url) }}" alt="Cover" class="cover-img">
            </div>
          </div>
        @endif

      </div>

      {{-- Right Column --}}
      <div class="space-y-6">

        {{-- Address --}}
        <div class="cc-card">
          <div class="cc-ch">
            <div class="cc-ch-ic"><i class="fas fa-map-marker-alt"></i></div>
            <div><div class="cc-ch-t">Address</div></div>
          </div>
          <div class="cc-cb">
            <div class="space-y-1">
              <div class="info-row">
                <div class="info-lbl">Street</div>
                <div class="info-val">{{ $company->profile->address ?? $company->address ?? '—' }}</div>
              </div>
              <div class="info-row md:grid md:grid-cols-2">
                <div>
                  <div class="info-lbl">City</div>
                  <div class="info-val">{{ $company->profile->city ?? $company->city ?? '—' }}</div>
                </div>
                <div>
                  <div class="info-lbl">State / Province</div>
                  <div class="info-val">{{ $company->profile->state ?? $company->state ?? '—' }}</div>
                </div>
              </div>
              <div class="info-row md:grid md:grid-cols-2">
                <div>
                  <div class="info-lbl">Country</div>
                  <div class="info-val">{{ $company->profile->country ?? $company->country ?? '—' }}</div>
                </div>
                <div>
                  <div class="info-lbl">Postal Code</div>
                  <div class="info-val">{{ $company->profile->postal_code ?? $company->postal_code ?? '—' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Verification & Features --}}
        <div class="cc-card">
          <div class="cc-ch">
            <div class="cc-ch-ic"><i class="fas fa-shield-check"></i></div>
            <div><div class="cc-ch-t">Verification & Features</div></div>
          </div>
          <div class="cc-cb">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <div class="info-lbl">Verification Level</div>
                <div class="info-val font-medium mt-1">
                  @switch($company->verification_level)
                    @case(0) Not Verified @break
                    @case(1) Basic @break
                    @case(2) Standard @break
                    @case(3) Premium @break
                    @default —
                  @endswitch
                </div>
              </div>
              <div>
                <div class="info-lbl">Total Reviews</div>
                <div class="info-val font-medium mt-1">{{ $company->total_reviews ?? 0 }}</div>
              </div>
              <div>
                <div class="info-lbl">Subscription Expires</div>
                <div class="info-val font-medium mt-1">
                  {{ $company->subscription_expires_at ? \Carbon\Carbon::parse($company->subscription_expires_at)->format('M j, Y') : '—' }}
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- API Settings --}}
        <div class="cc-card">
          <div class="cc-ch">
            <div class="cc-ch-ic"><i class="fas fa-key"></i></div>
            <div><div class="cc-ch-t">API & Webhook</div></div>
          </div>
          <div class="cc-cb">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <div class="info-lbl">API Key</div>
                <div class="info-val font-mono text-sm mt-1 break-all">
                  {{ $company->api_key ?? 'Not Generated' }}
                </div>
              </div>
              <div>
                <div class="info-lbl">Webhook URL</div>
                <div class="info-val mt-1">
                  @if($company->webhook_url)
                    <a href="{{ $company->webhook_url }}" target="_blank" style="color: var(--B)" class="hover:underline break-all">{{ $company->webhook_url }}</a>
                  @else
                    —
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Quick Actions --}}
        <div class="cc-card">
          <div class="cc-ch">
            <div class="cc-ch-ic"><i class="fas fa-cog"></i></div>
            <div><div class="cc-ch-t">Quick Actions</div></div>
          </div>
          <div class="cc-cb space-y-4">
            <a href="{{ route('admin.companies.edit', $company->id) }}" class="cc-btn cc-btn-edit w-full justify-center">
              <i class="fas fa-edit"></i> Edit Company
            </a>
            <a href="{{ route('admin.companies.index') }}" class="cc-btn cc-btn-back w-full justify-center">
              <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <form action="{{ route('admin.companies.destroy', $company->id) }}" method="POST" class="inline w-full">
              @csrf @method('DELETE')
              <button type="submit" class="cc-btn cc-btn-del w-full justify-center" onclick="return confirm('Delete this company permanently?')">
                <i class="fas fa-trash-alt"></i> Delete Company
              </button>
            </form>
          </div>
        </div>

        {{-- Activity --}}
        <div class="cc-card">
          <div class="cc-ch">
            <div class="cc-ch-ic"><i class="fas fa-clock"></i></div>
            <div><div class="cc-ch-t">Activity</div></div>
          </div>
          <div class="cc-cb text-sm space-y-3">
            <div class="info-row">
              <div class="info-lbl">Created</div>
              <div class="info-val">{{ $company->created_at?->format('M j, Y • g:i A') ?? '—' }}</div>
            </div>
            <div class="info-row">
              <div class="info-lbl">Last Update</div>
              <div class="info-val">{{ $company->updated_at?->format('M j, Y • g:i A') ?? '—' }}</div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>

</div>

@endsection