@extends('layouts.app')

@section('title', 'Edit Company - ' . $company->name)

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

<style>
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
  --shlg:0 12px 32px rgba(15,23,42,.15);
  --shblue:0 8px 28px rgba(37,99,235,.3);
}

/* ── ANIMATIONS ── */
@keyframes fadeInUp{
  from{opacity:0;transform:translateY(30px)}
  to{opacity:1;transform:translateY(0)}
}
@keyframes slideInLeft{
  from{opacity:0;transform:translateX(-50px)}
  to{opacity:1;transform:translateX(0)}
}
@keyframes slideInRight{
  from{opacity:0;transform:translateX(50px)}
  to{opacity:1;transform:translateX(0)}
}
@keyframes pulse{
  0%,100%{transform:scale(1)}
  50%{transform:scale(1.05)}
}
@keyframes float{
  0%,100%{transform:translateY(0)}
  50%{transform:translateY(-10px)}
}
@keyframes glow{
  0%,100%{box-shadow:0 0 5px rgba(37,99,235,.3)}
  50%{box-shadow:0 0 20px rgba(37,99,235,.6)}
}

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
.cc{font-family:'DM Sans',system-ui,sans-serif;background:var(--bg);min-height:100vh;color:var(--ink)}

/* ── HERO ── */
.cc-hero{background:linear-gradient(140deg,var(--ink) 0%,var(--ink2) 48%,var(--B) 100%);position:relative;overflow:hidden}
.cc-hero::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.5) 50%,transparent)}
.cc-hero::after{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);background-size:44px 44px;pointer-events:none}
.cc-glow1{position:absolute;right:-80px;top:-60px;width:480px;height:480px;border-radius:50%;background:radial-gradient(circle,rgba(96,165,250,.2) 0%,transparent 70%);pointer-events:none;animation:float 6s ease-in-out infinite}
.cc-glow2{position:absolute;left:-60px;bottom:-40px;width:320px;height:320px;border-radius:50%;background:radial-gradient(circle,rgba(139,92,246,.15) 0%,transparent 70%);pointer-events:none;animation:float 8s ease-in-out infinite reverse}
.cc-hero-in{position:relative;z-index:1;max-width:1160px;margin:0 auto;padding:46px 48px 94px;animation:slideInLeft 0.8s ease-out}
.cc-bc{display:flex;align-items:center;gap:6px;font-size:11.5px;color:rgba(255,255,255,.45);margin-bottom:16px;animation:fadeInUp 0.6s ease-out 0.2s both}
.cc-bc a{color:rgba(255,255,255,.45);text-decoration:none;transition:color .15s}.cc-bc a:hover{color:rgba(255,255,255,.85)}
.cc-bc i{font-size:8px}.cc-bc span{color:rgba(255,255,255,.9);font-weight:700}
.cc-h1{font-family:'Bricolage Grotesque',sans-serif;font-size:clamp(26px,3.2vw,46px);font-weight:800;color:#fff;line-height:1.08;letter-spacing:-.025em;margin-bottom:8px;animation:fadeInUp 0.6s ease-out 0.3s both}
.cc-sub{font-size:13.5px;color:rgba(255,255,255,.5);line-height:1.65;max-width:380px;animation:fadeInUp 0.6s ease-out 0.4s both}
.cc-hero-r{display:flex;flex-direction:column;align-items:flex-end;gap:14px;padding-top:4px;animation:slideInRight 0.8s ease-out}
.cc-back{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.18);border-radius:var(--rads);color:rgba(255,255,255,.75);font-size:12.5px;font-weight:700;text-decoration:none;transition:all .18s;animation:fadeInUp 0.6s ease-out 0.5s both}
.cc-back:hover{background:rgba(255,255,255,.18);border-color:rgba(255,255,255,.4);color:#fff}

/* ── MAIN CONTENT ── */
.cc-main{max-width:1160px;margin:0 auto;padding:0 48px 64px;margin-top:-46px;position:relative;z-index:10;animation:fadeInUp 0.6s ease-out 0.6s both}
.cc-layout{display:grid;grid-template-columns:1fr 290px;gap:32px;align-items:start}
@media(max-width:1024px){.cc-layout{grid-template-columns:1fr}}

/* ── PANELS ── */
.cc-panel{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--rad);box-shadow:var(--shlg);overflow:hidden;margin-bottom:24px;animation:panelIn .28s ease both}
@keyframes panelIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.cc-ph{padding:20px 26px;border-bottom:1px solid var(--bdr);display:flex;align-items:center;gap:14px;background:linear-gradient(135deg,var(--bg),var(--sur));animation:fadeInUp 0.4s ease-out}
.cc-ph-ic{width:40px;height:40px;border-radius:10px;background:var(--B50);border:1.5px solid var(--B100);display:flex;align-items:center;justify-content:center;font-size:15px;color:var(--B);flex-shrink:0;animation:pulse 2s infinite}
.cc-ph-t{font-family:'Bricolage Grotesque',sans-serif;font-size:17px;font-weight:800;color:var(--ink)}
.cc-ph-s{font-size:12px;color:var(--mu);margin-top:2px}
.cc-pb{padding:26px;animation:fadeInUp 0.4s ease-out 0.1s both}

/* ── FIELDS ── */
.fg{display:grid;gap:18px}.fg2{grid-template-columns:1fr 1fr}.fspan{grid-column:1/-1}
.fl{display:flex;align-items:center;gap:6px;font-size:11px;font-weight:800;letter-spacing:.05em;text-transform:uppercase;color:var(--ink3);margin-bottom:7px}
.fl-r{color:var(--R);font-size:14px;line-height:1}
.fl-o{font-size:9.5px;font-weight:700;letter-spacing:.06em;background:var(--bg);border:1px solid var(--bdr);color:var(--mul);padding:1px 6px;border-radius:4px;text-transform:uppercase}
.fi,.fs,.ft{width:100%;padding:11px 14px;border:1.5px solid var(--bdr);border-radius:var(--rads);font-family:'DM Sans',sans-serif;font-size:13.5px;color:var(--ink);background:var(--bg);outline:none;transition:all .2s;appearance:none;animation:fadeInUp 0.4s ease-out}
.fi:hover,.fs:hover,.ft:hover{border-color:var(--bdrf);background:var(--sur);transform:translateY(-1px)}
.fi:focus,.fs:focus,.ft:focus{border-color:var(--B);background:var(--sur);box-shadow:0 0 0 3px rgba(37,99,235,.1);transform:translateY(-2px)}
.fi::placeholder,.ft::placeholder{color:var(--mul)}
.fi.err,.fs.err{border-color:var(--R);background:#fff9f9;animation:shake 0.5s ease-in-out}
@keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-5px)}75%{transform:translateX(5px)}}
.ft{resize:vertical;min-height:96px;line-height:1.6}
.fs{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 13px center;padding-right:38px;cursor:pointer}
.fi-w{position:relative;display:flex;align-items:center}
.fi-w i{position:absolute;left:13px;font-size:13px;color:var(--mu);pointer-events:none;transition:color .2s}
.fi-w .fi{padding-left:38px}
.fi-w:focus-within i{color:var(--B)}
.ferr{display:flex;align-items:center;gap:5px;font-size:11.5px;color:var(--R);margin-top:4px;font-weight:500;animation:fadeInUp 0.3s ease-out}
.ferr i{font-size:10px}
.fhint{font-size:11px;color:var(--mu);margin-top:4px;display:flex;align-items:flex-start;gap:5px}
.fhint i{color:var(--B);margin-top:2px;flex-shrink:0}

/* ── FILE UPLOAD ── */
.file-up{border:2px dashed var(--bdr);border-radius:var(--rads);padding:24px;text-align:center;transition:all .2s;cursor:pointer;animation:fadeInUp 0.4s ease-out}
.file-up:hover{border-color:var(--B);background:var(--B50)}
.file-up-ic{width:48px;height:48px;border-radius:50%;background:var(--B50);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:20px;color:var(--B)}
.file-up-t{font-size:14px;font-weight:700;color:var(--ink);margin-bottom:4px}
.file-up-s{font-size:12px;color:var(--mu)}
.file-input{display:none}
.file-preview{margin-top:16px;display:flex;align-items:center;gap:12px;padding:12px;background:var(--bg);border-radius:var(--rads)}
.file-preview-img{width:48px;height:48px;border-radius:8px;object-fit:cover}
.file-preview-info{flex:1}
.file-preview-name{font-size:13px;font-weight:600;color:var(--ink)}
.file-preview-size{font-size:11px;color:var(--mu)}
.file-preview-remove{padding:6px 12px;background:var(--R);color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;transition:all .2s}
.file-preview-remove:hover{background:#dc2626}

/* ── ACTIONS ── */
.cc-actions{display:flex;align-items:center;justify-content:space-between;padding:18px 26px;border-top:1px solid var(--bdr);background:var(--bg);animation:fadeInUp 0.6s ease-out}
.cc-btn{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:var(--rads);font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;text-decoration:none;cursor:pointer;transition:all .18s}
.cc-btn-cancel{border:1.5px solid var(--bdr);background:var(--sur);color:var(--ink3)}
.cc-btn-cancel:hover{border-color:var(--B);color:var(--B);transform:translateY(-1px)}
.cc-btn-submit{background:var(--B);color:#fff;box-shadow:var(--shblue)}
.cc-btn-submit:hover{background:var(--Bd);transform:translateY(-2px);box-shadow:0 12px 32px rgba(37,99,235,.4)}
</style>

<div class="cc">

{{-- HERO --}}
<div class="cc-hero">
  <div class="cc-glow1"></div>
  <div class="cc-glow2"></div>
  <div class="cc-hero-in">
    <div>
      <div class="cc-bc">
        <a href="/"><i class="fas fa-home"></i></a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('companies.index') }}">Companies</a>
        <i class="fas fa-chevron-right"></i>
        <span>Edit</span>
      </div>
      <h1 class="cc-h1">Edit <em>{{ $company->name }}</em></h1>
      <p class="cc-sub">Update your company information and branding</p>
    </div>
    <div class="cc-hero-r">
      <a href="{{ route('companies.show', $company) }}" class="cc-back">
        <i class="fas fa-eye"></i>
        View Profile
      </a>
    </div>
  </div>
</div>

{{-- MAIN CONTENT --}}
<div class="cc-main">
  <form action="{{ route('companies.update', $company) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="cc-layout">
      <div>

        {{-- BASIC INFO --}}
        <div class="cc-panel">
          <div class="cc-ph">
            <div class="cc-ph-ic"><i class="fas fa-building"></i></div>
            <div>
              <div class="cc-ph-t">Basic Information</div>
              <div class="cc-ph-s">Company details and registration</div>
            </div>
          </div>
          <div class="cc-pb">
            <div class="fg fg2">
              <div class="fspan">
                <div class="fl">
                  Company Name
                  <span class="fl-r">*</span>
                </div>
                <input type="text" name="name" id="name" required value="{{ old('name', $company->name) }}" class="fi @error('name') err @enderror">
                @error('name') <div class="ferr"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
              </div>
              
              <div>
                <div class="fl">Company Type</div>
                <select name="type" id="type" required class="fs">
                  <option value="">Select type</option>
                  <option value="agency" {{ old('type', $company->type) == 'agency' ? 'selected' : '' }}>Agency</option>
                  <option value="developer" {{ old('type', $company->type) == 'developer' ? 'selected' : '' }}>Developer</option>
                  <option value="contractor" {{ old('type', $company->type) == 'contractor' ? 'selected' : '' }}>Contractor</option>
                </select>
              </div>
              
              <div>
                <div class="fl">Registration Number</div>
                <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number', $company->registration_number) }}" class="fi">
              </div>
            </div>
          </div>
        </div>

        {{-- CONTACT INFO --}}
        <div class="cc-panel">
          <div class="cc-ph">
            <div class="cc-ph-ic"><i class="fas fa-address-card"></i></div>
            <div>
              <div class="cc-ph-t">Contact Information</div>
              <div class="cc-ph-s">How clients can reach you</div>
            </div>
          </div>
          <div class="cc-pb">
            <div class="fg fg2">
              <div>
                <div class="fl">
                  Email Address
                  <span class="fl-r">*</span>
                </div>
                <div class="fi-w">
                  <i class="fas fa-envelope"></i>
                  <input type="email" name="email" id="email" required value="{{ old('email', $company->email) }}" class="fi @error('email') err @enderror">
                </div>
                @error('email') <div class="ferr"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
              </div>
              
              <div>
                <div class="fl">Phone Number</div>
                <div class="fi-w">
                  <i class="fas fa-phone"></i>
                  <input type="text" name="phone" id="phone" value="{{ old('phone', $company->phone) }}" class="fi">
                </div>
              </div>
              
              <div class="fspan">
                <div class="fl">Website URL</div>
                <div class="fi-w">
                  <i class="fas fa-globe"></i>
                  <input type="url" name="website" id="website" value="{{ old('website', $company->website) }}" placeholder="https://" class="fi">
                </div>
              </div>
              
              <div class="fspan">
                <div class="fl">Office Address</div>
                <div class="fi-w">
                  <i class="fas fa-map-marker-alt"></i>
                  <input type="text" name="address" id="address" value="{{ old('address', $company->address) }}" class="fi">
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- BRANDING --}}
        <div class="cc-panel">
          <div class="cc-ph">
            <div class="cc-ph-ic"><i class="fas fa-palette"></i></div>
            <div>
              <div class="cc-ph-t">Branding & Description</div>
              <div class="cc-ph-s">Visual identity and company story</div>
            </div>
          </div>
          <div class="cc-pb">
            <div class="fg">
              <div class="fspan">
                <div class="fl">Company Logo</div>
                <div class="file-up" onclick="document.getElementById('logo').click()">
                  <div class="file-up-ic">
                    @if($company->logo_url)
                      <img src="{{ asset('storage/' . $company->logo_url) }}" alt="" class="w-full h-full object-cover rounded-full">
                    @else
                      <i class="fas fa-image"></i>
                    @endif
                  </div>
                  <div class="file-up-t">Click to upload logo</div>
                  <div class="file-up-s">Square PNG or JPG, at least 400x400px</div>
                  <input type="file" name="logo" id="logo" accept="image/*" class="file-input">
                </div>
              </div>
              
              <div class="fspan">
                <div class="fl">About the Company</div>
                <textarea name="description" id="description" rows="5" class="ft" placeholder="Describe your company, services, and expertise...">{{ old('description', $company->description ?? ($company->profile->description ?? '')) }}</textarea>
                <div class="fhint">
                  <i class="fas fa-lightbulb"></i>
                  <span>Include your services, expertise, and what makes your company unique</span>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      {{-- SIDEBAR --}}
      <div>
        <div class="cc-panel">
          <div class="cc-ph">
            <div class="cc-ph-ic"><i class="fas fa-info-circle"></i></div>
            <div>
              <div class="cc-ph-t">Quick Tips</div>
            </div>
          </div>
          <div class="cc-pb">
            <div class="space-y-3">
              <div class="fhint">
                <i class="fas fa-check-circle" style="color: var(--G)"></i>
                <span>Keep your company name professional and memorable</span>
              </div>
              <div class="fhint">
                <i class="fas fa-check-circle" style="color: var(--G)"></i>
                <span>Use a high-quality logo for better branding</span>
              </div>
              <div class="fhint">
                <i class="fas fa-check-circle" style="color: var(--G)"></i>
                <span>Provide detailed contact information</span>
              </div>
              <div class="fhint">
                <i class="fas fa-check-circle" style="color: var(--G)"></i>
                <span>Write a compelling description</span>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    {{-- ACTIONS --}}
    <div class="cc-actions">
      <a href="{{ route('companies.show', $company) }}" class="cc-btn cc-btn-cancel">
        <i class="fas fa-times"></i>
        Cancel
      </a>
      <button type="submit" class="cc-btn cc-btn-submit">
        <i class="fas fa-save"></i>
        Save Changes
      </button>
    </div>

  </form>
</div>

</div>
@endsection
