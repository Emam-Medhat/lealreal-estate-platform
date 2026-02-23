@extends('layouts.app')

@section('title', 'تفاصيل NFT')

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
  --rad:16px;--rads:12px;
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
.nft-show{font-family:'DM Sans',system-ui,sans-serif;background:linear-gradient(135deg,var(--bg) 0%,var(--B50) 100%);min-height:100vh;color:var(--ink)}

/* ── HERO ── */
.nft-hero{background:linear-gradient(140deg,var(--ink) 0%,var(--ink2) 48%,var(--B) 100%);position:relative;overflow:hidden}
.nft-hero::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.5) 50%,transparent)}
.nft-hero::after{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);background-size:44px 44px;pointer-events:none}
.nft-glow1{position:absolute;right:-80px;top:-60px;width:480px;height:480px;border-radius:50%;background:radial-gradient(circle,rgba(96,165,250,.2) 0%,transparent 70%);pointer-events:none;animation:float 6s ease-in-out infinite}
.nft-glow2{position:absolute;left:-60px;bottom:-40px;width:320px;height:320px;border-radius:50%;background:radial-gradient(circle,rgba(139,92,246,.15) 0%,transparent 70%);pointer-events:none;animation:float 8s ease-in-out infinite reverse}
.nft-hero-in{position:relative;z-index:1;max-width:1160px;margin:0 auto;padding:46px 48px 94px;animation:slideInLeft 0.8s ease-out}
.nft-bc{display:flex;align-items:center;gap:6px;font-size:11.5px;color:rgba(255,255,255,.45);margin-bottom:16px;animation:fadeInUp 0.6s ease-out 0.2s both}
.nft-bc a{color:rgba(255,255,255,.45);text-decoration:none;transition:color .15s}.nft-bc a:hover{color:rgba(255,255,255,.85)}
.nft-bc i{font-size:8px}.nft-bc span{color:rgba(255,255,255,.9);font-weight:700}
.nft-h1{font-family:'Bricolage Grotesque',sans-serif;font-size:clamp(26px,3.2vw,46px);font-weight:800;color:#fff;line-height:1.08;letter-spacing:-.025em;margin-bottom:8px;animation:fadeInUp 0.6s ease-out 0.3s both}
.nft-sub{font-size:13.5px;color:rgba(255,255,255,.5);line-height:1.65;max-width:380px;animation:fadeInUp 0.6s ease-out 0.4s both}

/* ── MAIN CONTENT ── */
.nft-main{max-width:1160px;margin:0 auto;padding:0 48px 64px;margin-top:-46px;position:relative;z-index:10}
.nft-grid{display:grid;grid-template-columns:1fr 1fr;gap:32px;align-items:start}

/* ── NFT CARD ── */
.nft-card{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--rad);box-shadow:var(--shlg);overflow:hidden;animation:fadeInUp 0.6s ease-out 0.5s both}
.nft-img-w{position:relative;aspect-ratio:1;overflow:hidden;background:linear-gradient(135deg,var(--B50),var(--B100))}
.nft-img{width:100%;height:100%;object-fit:cover;transition:transform 0.5s cubic-bezier(0.4,0,0.2,1)}
.nft-card:hover .nft-img{transform:scale(1.05)}
.nft-img-ph{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:12px;color:var(--mu)}
.nft-img-ph i{font-size:48px;opacity:0.3}
.nft-badge{position:absolute;top:16px;right:16px;background:rgba(255,255,255,.95);backdrop-filter:blur(10px);padding:6px 12px;border-radius:20px;font-size:11.5px;font-weight:700;color:var(--ink);box-shadow:var(--shmd);animation:pulse 2s infinite}
.nft-badge.available{background:var(--Gbg);color:var(--Gtx)}
.nft-badge.sold{background:var(--Rbg);color:var(--Rtx)}

/* ── INFO CARD ── */
.nft-info{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--rad);box-shadow:var(--shlg);overflow:hidden;animation:slideInRight 0.8s ease-out}
.nft-info-h{padding:24px 28px;border-bottom:1px solid var(--bdr);background:linear-gradient(135deg,var(--bg),var(--sur))}
.nft-info-t{font-family:'Bricolage Grotesque',sans-serif;font-size:24px;font-weight:800;color:var(--ink);margin-bottom:4px}
.nft-info-s{font-size:14px;color:var(--mu);line-height:1.5}
.nft-info-b{padding:28px}
.nft-info-row{display:flex;align-items:center;justify-content:space-between;padding:16px 0;border-bottom:1px solid var(--bdr);animation:fadeInUp 0.4s ease-out}
.nft-info-row:last-child{border:none}
.nft-info-l{display:flex;flex-direction:column;gap:2px}
.nft-info-lb{font-size:11.5px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:var(--ink3)}
.nft-info-lv{font-size:14px;color:var(--ink);font-weight:500}
.nft-info-r{text-align:right}
.nft-price{font-size:24px;font-weight:800;color:var(--B);font-family:'Bricolage Grotesque',sans-serif}
.nft-actions{display:flex;gap:12px;padding-top:24px;animation:fadeInUp 0.6s ease-out}
.nft-btn{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:var(--rads);font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;text-decoration:none;cursor:pointer;transition:all .2s;border:none}
.nft-btn-back{background:var(--bg);color:var(--ink3);border:1.5px solid var(--bdr)}
.nft-btn-back:hover{border-color:var(--B);color:var(--B);transform:translateY(-2px)}
.nft-btn-buy{background:var(--B);color:#fff;box-shadow:var(--shblue)}
.nft-btn-buy:hover{background:var(--Bd);transform:translateY(-2px);box-shadow:0 12px 32px rgba(37,99,235,.4)}

/* ── ATTRIBUTES ── */
.nft-attrs{margin-top:24px;padding-top:24px;border-top:1px solid var(--bdr)}
.nft-attrs-h{font-size:12px;font-weight:800;letter-spacing:.05em;text-transform:uppercase;color:var(--ink3);margin-bottom:16px}
.nft-attr-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
.nft-attr{background:var(--bg);border:1px solid var(--bdr);border-radius:var(--rads);padding:12px;text-align:center;transition:all .2s}
.nft-attr:hover{border-color:var(--B);transform:translateY(-2px)}
.nft-attr-t{font-size:10.5px;color:var(--mu);margin-bottom:4px}
.nft-attr-v{font-size:14px;font-weight:700;color:var(--ink)}

@media(max-width:768px){
  .nft-main{padding:0 20px 40px}.nft-grid{grid-template-columns:1fr;gap:20px}
  .nft-hero-in{padding:36px 20px 84px}.nft-info-h{padding:20px}.nft-info-b{padding:20px}
  .nft-attr-grid{grid-template-columns:1fr}
}
</style>

<div class="nft-show">

{{-- HERO --}}
<div class="nft-hero">
  <div class="nft-glow1"></div>
  <div class="nft-glow2"></div>
  <div class="nft-hero-in">
    <div class="nft-bc">
      <a href="{{ route('blockchain.nfts.index') }}"><i class="fas fa-arrow-right"></i></a>
      <span>NFTs</span>
      <i class="fas fa-chevron-left"></i>
      <span>تفاصيل</span>
    </div>
    <h1 class="nft-h1">{{ $nft->name }}</h1>
    <p class="nft-sub">استكشف تفاصيل هذه القطعة الرقمية الفريدة</p>
  </div>
</div>

{{-- MAIN CONTENT --}}
<div class="nft-main">
  <div class="nft-grid">
    
    {{-- NFT IMAGE --}}
    <div class="nft-card">
      <div class="nft-img-w">
        @if($nft->image_url)
          <img src="{{ $nft->image_url }}" alt="{{ $nft->name }}" class="nft-img">
        @else
          <div class="nft-img-ph">
            <i class="fas fa-cube"></i>
            <span>لا توجد صورة</span>
          </div>
        @endif
        <div class="nft-badge {{ $nft->status }}">
          {{ $nft->status == 'available' ? 'متاح' : 'تم البيع' }}
        </div>
      </div>
    </div>
    
    {{-- NFT INFO --}}
    <div class="nft-info">
      <div class="nft-info-h">
        <h2 class="nft-info-t">{{ $nft->name }}</h2>
        <p class="nft-info-s">{{ $nft->description }}</p>
      </div>
      
      <div class="nft-info-b">
        
        {{-- Token ID --}}
        <div class="nft-info-row">
          <div class="nft-info-l">
            <span class="nft-info-lb">Token ID</span>
            <span class="nft-info-lv">{{ $nft->token_id ?? $nft->id }}</span>
          </div>
        </div>
        
        {{-- Contract Address --}}
        <div class="nft-info-row">
          <div class="nft-info-l">
            <span class="nft-info-lb">Contract Address</span>
            <span class="nft-info-lv">{{ $nft->contract_address }}</span>
          </div>
        </div>
        
        {{-- Price --}}
        <div class="nft-info-row">
          <div class="nft-info-l">
            <span class="nft-info-lb">السعر</span>
            <span class="nft-info-lv">{{ $nft->price ?? 'N/A' }} {{ $nft->currency ?? 'ETH' }}</span>
          </div>
          <div class="nft-info-r">
            <span class="nft-price">{{ $nft->price ?? '0' }}</span>
          </div>
        </div>
        
        {{-- Owner --}}
        @if($nft->owner)
        <div class="nft-info-row">
          <div class="nft-info-l">
            <span class="nft-info-lb">المالك</span>
            <span class="nft-info-lv">{{ $nft->owner->name ?? $nft->owner_address }}</span>
          </div>
        </div>
        @endif
        
        {{-- Actions --}}
        <div class="nft-actions">
          <a href="{{ route('blockchain.nfts.index') }}" class="nft-btn nft-btn-back">
            <i class="fas fa-arrow-right"></i>
            العودة
          </a>
          
          @if($nft->status == 'available')
            <button class="nft-btn nft-btn-buy" onclick="buyNFT({{ $nft->id }})">
              <i class="fas fa-shopping-cart"></i>
              شراء
            </button>
          @endif
        </div>
        
        {{-- Attributes --}}
        <div class="nft-attrs">
          <h3 class="nft-attrs-h">الخصائص</h3>
          <div class="nft-attr-grid">
            <div class="nft-attr">
              <div class="nft-attr-t">الحالة</div>
              <div class="nft-attr-v">{{ $nft->status }}</div>
            </div>
            <div class="nft-attr">
              <div class="nft-attr-t">العملة</div>
              <div class="nft-attr-v">{{ $nft->currency ?? 'ETH' }}</div>
            </div>
          </div>
        </div>
        
      </div>
    </div>
    
  </div>
</div>

</div>

<script>
function buyNFT(id) {
    if(confirm('هل أنت متأكد من شراء هذا NFT؟')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/nfts/' + id + '/buy';
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
