@extends('layouts.app')
@section('title', 'العقارات')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --nv:#0F1B2D;--nv2:#162236;--nv3:#1E2F45;
  --gd:#C9A84C;--gd2:#E8C97A;
  --gd-g:linear-gradient(135deg,#C9A84C 0%,#E8C97A 55%,#C9A84C 100%);
  --bl:#1B5EF7;--bl2:#0D4EE0;
  --bl-g:linear-gradient(135deg,#1B5EF7,#0D4EE0);
  --gr:#059669;--or:#EA580C;--rd:#DC2626;
  --t1:#0F1B2D;--t2:#2D3F55;--t3:#64748B;--t4:#94A3B8;
  --bg:#F3F1ED;--bg2:#EDE9E2;--cd:#fff;
  --bd:#E5E0D8;--bd2:#D6D0C4;
  --r1:8px;--r2:14px;--r3:20px;--r4:28px;
  --sh:0 1px 3px rgba(15,27,45,.06),0 4px 16px rgba(15,27,45,.06);
  --shm:0 4px 20px rgba(15,27,45,.1),0 1px 6px rgba(15,27,45,.05);
  --shl:0 20px 60px rgba(15,27,45,.14),0 4px 16px rgba(15,27,45,.07);
  --shx:0 40px 100px rgba(15,27,45,.18),0 8px 24px rgba(15,27,45,.08);
  --shg:0 8px 28px rgba(201,168,76,.3),0 2px 8px rgba(201,168,76,.15);
  --tr:all .25s cubic-bezier(.4,0,.2,1);
  --trs:all .4s cubic-bezier(.34,1.1,.64,1);
  --ff:'Cairo',system-ui,sans-serif;
  --ffd:'Playfair Display',Georgia,serif;
}
html{scroll-behavior:smooth}
body{font-family:var(--ff);background:var(--bg);color:var(--t1);direction:rtl;overflow-x:hidden}
body::before{
  content:'';position:fixed;inset:0;pointer-events:none;z-index:0;
  background:
    radial-gradient(ellipse 80% 60% at 100% -5%,rgba(201,168,76,.07) 0%,transparent 55%),
    radial-gradient(ellipse 60% 50% at -5% 90%,rgba(27,94,247,.05) 0%,transparent 55%);
}

/* ═══ HERO ═══ */
.hero{position:relative;overflow:hidden;background:var(--nv);min-height:470px;display:flex;flex-direction:column}
.hero::before{
  content:'';position:absolute;inset:0;pointer-events:none;
  background-image:
    linear-gradient(rgba(201,168,76,.05) 1px,transparent 1px),
    linear-gradient(90deg,rgba(201,168,76,.05) 1px,transparent 1px);
  background-size:72px 72px;
  mask-image:radial-gradient(ellipse 90% 70% at 50% 0%,black 0%,transparent 75%);
}
.hero::after{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(201,168,76,.6) 50%,transparent)}
.h-gr{position:absolute;top:-20%;right:-10%;width:650px;height:650px;border-radius:50%;background:radial-gradient(circle,rgba(201,168,76,.14) 0%,transparent 60%);pointer-events:none}
.h-gl{position:absolute;bottom:-25%;left:-5%;width:480px;height:480px;border-radius:50%;background:radial-gradient(circle,rgba(27,94,247,.1) 0%,transparent 60%);pointer-events:none}

.h-in{position:relative;z-index:2;max-width:1440px;margin:0 auto;width:100%;padding:52px 56px 0;flex:1;display:flex;flex-direction:column}
.h-bc{display:flex;align-items:center;gap:10px;font-size:10.5px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:44px}
.h-bc::before{content:'';display:inline-block;width:18px;height:1.5px;background:var(--gd);border-radius:2px;opacity:.7}
.h-bc a{color:rgba(255,255,255,.4);text-decoration:none;transition:color .2s}
.h-bc a:hover{color:var(--gd2)}
.h-bc-s{color:rgba(255,255,255,.2)}.h-bc-c{color:var(--gd)}

.h-body{flex:1;display:flex;align-items:flex-start;justify-content:space-between;gap:48px}
.h-left{max-width:540px}
.h-ey{display:inline-flex;align-items:center;gap:10px;font-size:10px;font-weight:800;letter-spacing:.22em;text-transform:uppercase;color:var(--gd);margin-bottom:20px}
.h-ey::after{content:'';width:36px;height:1px;background:var(--gd);opacity:.55}
.h-h1{font-family:var(--ffd);font-size:clamp(36px,4.5vw,64px);font-weight:700;color:#fff;line-height:1.08;letter-spacing:-.01em;margin-bottom:18px}
.h-h1 em{font-style:italic;background:var(--gd-g);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.h-sub{font-size:15px;color:rgba(255,255,255,.55);line-height:1.75;margin-bottom:38px;max-width:400px}

.h-stats{display:flex;gap:14px;flex-wrap:wrap}
.hst{display:flex;flex-direction:column;gap:4px;padding:14px 22px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09);border-radius:var(--r2);backdrop-filter:blur(12px);transition:var(--tr)}
.hst:hover{background:rgba(255,255,255,.09);border-color:rgba(201,168,76,.25);transform:translateY(-2px)}
.hst-n{font-family:var(--ffd);font-size:26px;font-weight:700;color:#fff;line-height:1}
.hst-l{font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,.4)}

.h-right{flex-shrink:0;display:flex;gap:12px;align-items:flex-start;padding-top:4px;opacity:0;animation:hRIn .9s .3s ease forwards}
@keyframes hRIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}
.h-col{display:flex;flex-direction:column;gap:12px}
.h-col:last-child{margin-top:28px}
.hth{border-radius:var(--r2);overflow:hidden;position:relative;box-shadow:0 8px 28px rgba(0,0,0,.45)}
.hth img{display:block;object-fit:cover;width:100%;height:100%;transition:transform .5s ease}
.hth:hover img{transform:scale(1.05)}
.ht-a{width:175px;height:135px}.ht-b{width:175px;height:195px}
.ht-c{width:155px;height:175px}.ht-d{width:155px;height:130px}
.hth:first-child::after{content:'';position:absolute;inset:0;border:2px solid rgba(201,168,76,.45);border-radius:var(--r2)}

/* ═══ SEARCH ═══ */
.sw{position:relative;z-index:10;background:var(--nv);padding-bottom:52px}
.si{max-width:1440px;margin:0 auto;padding:0 56px}
.sb{
  background:var(--cd);border-radius:var(--r3);box-shadow:var(--shx);
  border:1px solid var(--bd);overflow:hidden;
  display:grid;grid-template-columns:1fr 1px 1fr 1px 1fr 1px 1fr auto;align-items:stretch;
}
.sdv{width:1px;background:var(--bd);align-self:stretch}
.sf{padding:16px 24px;display:flex;flex-direction:column;gap:6px}
.sf-l{font-size:9.5px;font-weight:800;letter-spacing:.18em;text-transform:uppercase;color:var(--t4)}
.sf-i{border:none;outline:none;font-family:var(--ff);font-size:13.5px;font-weight:700;color:var(--t1);background:transparent;appearance:none;-webkit-appearance:none;width:100%;cursor:pointer}
.sf-i::placeholder{color:var(--t4);font-weight:400}
.sf-i option{font-family:var(--ff)}
.sbtn{background:var(--gd-g);color:var(--nv);border:none;padding:0 36px;font-family:var(--ff);font-size:14px;font-weight:900;cursor:pointer;display:flex;align-items:center;gap:9px;transition:var(--trs);box-shadow:var(--shg);white-space:nowrap}
.sbtn:hover{box-shadow:0 16px 44px rgba(201,168,76,.42)}
.sbtn i{font-size:15px}

/* ═══ MAIN ═══ */
.main{position:relative;z-index:1;max-width:1440px;margin:0 auto;padding:48px 56px 80px}

/* Results bar */
.rb{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:36px}
.rb-l{display:flex;align-items:center;gap:20px;flex-wrap:wrap}
.rc{font-size:14.5px;font-weight:600;color:var(--t2)}
.rc strong{font-size:20px;font-weight:900;color:var(--t1)}

.fps{display:flex;gap:8px;flex-wrap:wrap}
.fp{display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:40px;font-size:11.5px;font-weight:700;border:1.5px solid var(--bd2);background:var(--cd);color:var(--t3);cursor:pointer;transition:var(--tr);white-space:nowrap}
.fp:hover{border-color:rgba(27,94,247,.3);color:var(--bl)}
.fp.on{background:var(--bl-g);color:#fff;border-color:var(--bl);box-shadow:0 4px 14px rgba(27,94,247,.22)}
.fp.gd.on{background:var(--gd-g);color:var(--nv);border-color:var(--gd);box-shadow:var(--shg)}
.fp i{font-size:9px}

.rb-r{display:flex;align-items:center;gap:10px}
.ssel{padding:8px 16px;border:1.5px solid var(--bd2);border-radius:var(--r1);font-family:var(--ff);font-size:12.5px;color:var(--t2);background:var(--cd);outline:none;cursor:pointer;transition:border-color .2s}
.ssel:focus{border-color:var(--bl)}
.vbs{display:flex;background:var(--cd);border:1.5px solid var(--bd2);border-radius:var(--r1);overflow:hidden}
.vb{width:36px;height:36px;border:none;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--t4);transition:var(--tr)}
.vb.on{background:var(--nv);color:#fff}
.vb:hover:not(.on){color:var(--t2)}

.shd{display:flex;align-items:center;gap:14px;margin-bottom:24px}
.shd-ln{flex:1;height:1px;background:var(--bd)}
.shd-lb{font-size:9.5px;font-weight:800;letter-spacing:.22em;text-transform:uppercase;color:var(--t4);white-space:nowrap}

/* ═══ PROPERTY GRID ═══ */
.pg{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}

.pc{
  background:var(--cd);border-radius:var(--r3);overflow:hidden;
  box-shadow:var(--sh);border:1px solid var(--bd);
  display:flex;flex-direction:column;position:relative;
  transition:var(--trs);
  opacity:0;animation:cuP .55s ease forwards;
}
.pc:nth-child(1){animation-delay:.04s}.pc:nth-child(2){animation-delay:.09s}.pc:nth-child(3){animation-delay:.14s}
.pc:nth-child(4){animation-delay:.19s}.pc:nth-child(5){animation-delay:.24s}.pc:nth-child(6){animation-delay:.29s}
.pc:nth-child(7){animation-delay:.34s}.pc:nth-child(8){animation-delay:.39s}.pc:nth-child(9){animation-delay:.44s}
@keyframes cuP{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}
.pc:hover{transform:translateY(-8px);box-shadow:var(--shl);border-color:rgba(27,94,247,.1)}

/* Image */
.pcim{position:relative;height:228px;overflow:hidden;background:var(--nv3);flex-shrink:0}
.pcim img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .6s cubic-bezier(.4,0,.2,1)}
.pc:hover .pcim img{transform:scale(1.07)}
.pcim::after{content:'';position:absolute;inset:0;background:linear-gradient(180deg,transparent 45%,rgba(15,27,45,.6) 100%);opacity:0;transition:opacity .3s}
.pc:hover .pcim::after{opacity:1}

/* Badges */
.pc-bgs{position:absolute;top:12px;right:12px;z-index:3;display:flex;gap:6px;flex-wrap:wrap}
.pbg{display:inline-flex;align-items:center;gap:4px;padding:4px 11px;border-radius:30px;font-size:10.5px;font-weight:800;letter-spacing:.04em;backdrop-filter:blur(10px)}
.pbg-sale{background:rgba(27,94,247,.9);color:#fff}
.pbg-rent{background:rgba(5,150,105,.9);color:#fff}
.pbg-new{background:rgba(234,88,12,.9);color:#fff}
.pbg-feat{background:rgba(201,168,76,.95);color:var(--nv)}

/* Wishlist */
.wb{position:absolute;top:12px;left:12px;z-index:4;width:33px;height:33px;border-radius:50%;background:rgba(255,255,255,.88);backdrop-filter:blur(10px);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--t3);transition:var(--trs);box-shadow:0 2px 8px rgba(0,0,0,.14)}
.wb:hover{transform:scale(1.12);color:var(--rd)}
.wb.liked{color:var(--rd);background:rgba(220,38,38,.1)}

/* Photo count */
.pcnt{position:absolute;bottom:12px;left:12px;z-index:4;background:rgba(0,0,0,.52);backdrop-filter:blur(8px);border-radius:20px;padding:3px 11px;font-size:10px;font-weight:700;color:#fff;display:flex;align-items:center;gap:4px}

/* Hover price */
.hpx{position:absolute;bottom:12px;right:12px;z-index:4;background:rgba(15,27,45,.9);backdrop-filter:blur(12px);border:1px solid rgba(201,168,76,.3);border-radius:var(--r1);padding:6px 13px;opacity:0;transform:translateY(5px);transition:opacity .25s,transform .25s}
.pc:hover .hpx{opacity:1;transform:translateY(0)}
.hpxv{font-size:13.5px;font-weight:900;color:#fff;display:flex;align-items:baseline;gap:4px}
.hpxc{font-size:10px;color:rgba(255,255,255,.55)}

/* Card body */
.pc-bd{padding:18px 18px 16px;flex:1;display:flex;flex-direction:column}
.pc-tt{font-size:14.5px;font-weight:800;color:var(--t1);margin-bottom:6px;line-height:1.35;transition:color .2s;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.pc:hover .pc-tt{color:var(--bl)}
.pc-lc{display:flex;align-items:center;gap:5px;font-size:11.5px;color:var(--t3);margin-bottom:14px}
.pc-lc i{color:var(--gd);font-size:10px}

/* Features */
.pcft{display:grid;grid-template-columns:repeat(3,1fr);border:1px solid var(--bd);border-radius:var(--r2);overflow:hidden;margin-bottom:16px}
.pci{display:flex;flex-direction:column;align-items:center;padding:9px 6px;gap:3px;border-right:1px solid var(--bd);transition:background .2s}
.pci:last-child{border-right:none}
.pc:hover .pci{background:rgba(27,94,247,.03)}
.pci-ic{font-size:15px;color:var(--bl)}
.pci-v{font-size:13px;font-weight:800;color:var(--t1)}
.pci-l{font-size:9px;font-weight:700;color:var(--t4);letter-spacing:.04em}

/* Price */
.pc-pr{font-size:21px;font-weight:900;color:var(--t1);margin-bottom:14px;line-height:1;display:flex;align-items:baseline;gap:6px}
.pc-pr small{font-size:12px;font-weight:600;color:var(--t4)}

/* Footer */
.pc-ft{display:flex;gap:8px;align-items:center;padding-top:14px;border-top:1px solid var(--bd);margin-top:auto}
.bdt{flex:1;padding:10px;border-radius:var(--r1);background:var(--bl-g);color:#fff;border:none;font-family:var(--ff);font-size:12.5px;font-weight:700;display:flex;align-items:center;justify-content:center;gap:6px;transition:var(--trs);cursor:pointer;text-decoration:none;box-shadow:0 4px 12px rgba(27,94,247,.2)}
.bdt:hover{transform:translateY(-2px);box-shadow:0 8px 22px rgba(27,94,247,.32)}
.bsm{width:38px;height:38px;border-radius:var(--r1);border:none;cursor:pointer;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:14px;transition:var(--trs)}
.bcl{background:rgba(5,150,105,.08);color:var(--gr);border:1px solid rgba(5,150,105,.2)}
.bcl:hover{background:var(--gr);color:#fff;transform:scale(1.08)}
.bsh{background:var(--bg2);color:var(--t3);border:1px solid var(--bd)}
.bsh:hover{background:rgba(234,88,12,.08);color:var(--or);border-color:rgba(234,88,12,.2);transform:scale(1.08)}

/* Empty state */
.empt{grid-column:1/-1;display:flex;flex-direction:column;align-items:center;padding:100px 20px;text-align:center}
.em-ic{width:96px;height:96px;border-radius:var(--r4);background:var(--bg2);border:2px dashed var(--bd2);display:flex;align-items:center;justify-content:center;font-size:36px;color:var(--t4);margin-bottom:24px}
.em-t{font-size:22px;font-weight:800;color:var(--t1);margin-bottom:8px}
.em-s{font-size:14px;color:var(--t3);margin-bottom:28px;line-height:1.6}
.brs{padding:11px 28px;border-radius:var(--r2);background:var(--bl-g);color:#fff;border:none;font-family:var(--ff);font-size:13.5px;font-weight:700;cursor:pointer;transition:var(--trs);text-decoration:none;box-shadow:0 4px 14px rgba(27,94,247,.22);display:inline-flex;align-items:center;gap:8px}
.brs:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(27,94,247,.32)}

/* Pagination */
.pgw{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:52px}

/* Toast */
.tst{position:fixed;bottom:28px;left:28px;z-index:9999;display:flex;align-items:center;gap:10px;padding:13px 22px;border-radius:var(--r2);font-family:var(--ff);font-size:13px;font-weight:700;box-shadow:var(--shl);border:1px solid;transition:transform .4s cubic-bezier(.34,1.2,.64,1),opacity .3s;transform:translateY(120%);opacity:0}
.tst.show{transform:translateY(0);opacity:1}
.tst.heart{background:var(--cd);border-color:rgba(220,38,38,.25);color:var(--rd)}
.tst.info{background:var(--cd);border-color:rgba(27,94,247,.25);color:var(--bl)}

@keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
.h-h1{animation:fadeUp .7s ease forwards}
.h-sub{animation:fadeUp .7s .1s ease both}
.h-stats{animation:fadeUp .7s .2s ease both}

::-webkit-scrollbar{width:5px}::-webkit-scrollbar-track{background:var(--bg)}
::-webkit-scrollbar-thumb{background:var(--bd2);border-radius:10px}::-webkit-scrollbar-thumb:hover{background:var(--gd)}

@media(max-width:1200px){
  .pg{grid-template-columns:repeat(3,1fr)}
  .h-right{display:none}
  .sb{grid-template-columns:1fr 1fr}
  .sdv{display:none}
  .sf{border-bottom:1px solid var(--bd)}
}
@media(max-width:900px){
  .h-in,.main,.si{padding-left:20px;padding-right:20px}
  .sb{grid-template-columns:1fr}
  .pg{grid-template-columns:1fr}
  .h-in{padding-top:36px}
}
@media(max-width:600px){.rb{flex-direction:column;align-items:flex-start}.fps{display:none}}
</style>
@endpush

@section('content')

<div class="tst" id="tst"></div>

{{-- ═══ HERO ═══ --}}
<div class="hero">
  <div class="h-gr"></div><div class="h-gl"></div>
  <div class="h-in">
    <div class="h-bc">
      <a href="{{ url('/') }}">الرئيسية</a>
      <span class="h-bc-s">/</span>
      <span class="h-bc-c">العقارات</span>
    </div>
    <div class="h-body">
      <div class="h-left">
        <div class="h-ey">اكتشف ما يناسبك</div>
        <h1 class="h-h1">اعثر على<br><em>منزل أحلامك</em></h1>
        <p class="h-sub">آلاف العقارات المميزة في أفضل المواقع، بأسعار تناسب جميع الميزانيات</p>
        <div class="h-stats">
          <div class="hst"><div class="hst-n">{{ $properties->total() }}+</div><div class="hst-l">عقار متاح</div></div>
          <div class="hst"><div class="hst-n">180+</div><div class="hst-l">مدينة</div></div>
          <div class="hst"><div class="hst-n">12K+</div><div class="hst-l">عميل راضٍ</div></div>
        </div>
      </div>
      <div class="h-right">
        <div class="h-col">
          <div class="hth ht-a"><img src="https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=400&q=80" alt=""></div>
          <div class="hth ht-b"><img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=400&q=80" alt=""></div>
        </div>
        <div class="h-col">
          <div class="hth ht-c"><img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=400&q=80" alt=""></div>
          <div class="hth ht-d"><img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&q=80" alt=""></div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ═══ SEARCH ═══ --}}
<div class="sw">
  <div class="si">
    <form method="GET" action="{{ route('properties.index') }}">
      <div class="sb">
        <div class="sf"><span class="sf-l">المدينة</span><input class="sf-i" type="text" name="city" value="{{ request('city') }}" placeholder="الرياض، جدة، الدمام…"></div>
        <div class="sdv"></div>
        <div class="sf">
          <span class="sf-l">نوع العقار</span>
          <select class="sf-i" name="property_type">
            <option value="">جميع الأنواع</option>
            <option value="apartment" {{ request('property_type')=='apartment'?'selected':'' }}>شقة</option>
            <option value="villa"     {{ request('property_type')=='villa'    ?'selected':'' }}>فيلا</option>
            <option value="land"      {{ request('property_type')=='land'     ?'selected':'' }}>أرض</option>
            <option value="office"    {{ request('property_type')=='office'   ?'selected':'' }}>مكتب</option>
            <option value="warehouse" {{ request('property_type')=='warehouse'?'selected':'' }}>مستودع</option>
          </select>
        </div>
        <div class="sdv"></div>
        <div class="sf">
          <span class="sf-l">الغرض</span>
          <select class="sf-i" name="listing_type">
            <option value="">للبيع والإيجار</option>
            <option value="sale" {{ request('listing_type')=='sale'?'selected':'' }}>للبيع</option>
            <option value="rent" {{ request('listing_type')=='rent'?'selected':'' }}>للإيجار</option>
          </select>
        </div>
        <div class="sdv"></div>
        <div class="sf">
          <span class="sf-l">نطاق السعر (ريال)</span>
          <div style="display:flex;gap:8px;align-items:center">
            <input class="sf-i" type="number" name="min_price" value="{{ request('min_price') }}" placeholder="من" style="width:45%">
            <span style="color:var(--t4);font-weight:700;font-size:11px">—</span>
            <input class="sf-i" type="number" name="max_price" value="{{ request('max_price') }}" placeholder="إلى" style="width:45%">
          </div>
        </div>
        <button type="submit" class="sbtn"><i class="fas fa-search"></i> بحث</button>
      </div>
    </form>
  </div>
</div>

{{-- ═══ MAIN ═══ --}}
<div class="main">

  <div class="rb">
    <div class="rb-l">
      <div class="rc">تم العثور على <strong>{{ $properties->total() }}</strong> عقار</div>
      <div class="fps">
        <span class="fp gd on" onclick="tp(this)"><i class="fas fa-star"></i> مميزة</span>
        <span class="fp on"    onclick="tp(this)">للبيع</span>
        <span class="fp"       onclick="tp(this)">للإيجار</span>
        <span class="fp"       onclick="tp(this)"><i class="fas fa-bed"></i> 3+ غرف</span>
        <span class="fp"       onclick="tp(this)">شقق</span>
        <span class="fp"       onclick="tp(this)">فيلات</span>
      </div>
    </div>
    <div class="rb-r">
      <select class="ssel">
        <option>الأحدث أولاً</option><option>الأقل سعراً</option>
        <option>الأعلى سعراً</option><option>الأكبر مساحةً</option><option>الأكثر مشاهدةً</option>
      </select>
      <div class="vbs">
        <button class="vb on"><i class="fas fa-th"></i></button>
        <button class="vb"><i class="fas fa-list"></i></button>
      </div>
    </div>
  </div>

  <div class="shd"><span class="shd-lb">جميع العقارات</span><div class="shd-ln"></div></div>

  {{-- ════════ PROPERTY GRID — 100% DYNAMIC ════════ --}}
  <div class="pg" id="pg">
    @forelse($properties as $property)
    <div class="pc">
      {{-- Image block --}}
      <div class="pcim">
        <img
          src="{{ $property->image ?? $property->primary_image_url ?? 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=600&q=80' }}"
          alt="{{ $property->title ?? 'عقار' }}"
          loading="lazy"
          onerror="this.src='https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=600&q=80'"
        >
        {{-- Badges --}}
        <div class="pc-bgs">
          @if($property->is_featured ?? false)
            <span class="pbg pbg-feat"><i class="fas fa-star"></i> مميز</span>
          @endif
          <span class="pbg pbg-{{ $property->listing_type == 'sale' ? 'sale' : 'rent' }}">
            {{ $property->listing_type == 'sale' ? 'للبيع' : 'للإيجار' }}
          </span>
          @if(isset($property->created_at) && $property->created_at->isCurrentMonth())
            <span class="pbg pbg-new"><i class="fas fa-bolt"></i> جديد</span>
          @endif
        </div>
        {{-- Wishlist btn --}}
        <button class="wb" onclick="tw(this)" data-id="{{ $property->id }}">
          <i class="far fa-heart"></i>
        </button>
        {{-- Photo count --}}
        <div class="pcnt"><i class="fas fa-camera"></i> {{ $property->images_count ?? 1 }}</div>
        {{-- Price hover overlay --}}
        <div class="hpx">
          <div class="hpxv">{{ number_format($property->price) }}<span class="hpxc">ريال</span></div>
        </div>
      </div>

      {{-- Body --}}
      <div class="pc-bd">
        <div class="pc-tt">{{ $property->title }}</div>
        <div class="pc-lc">
          <i class="fas fa-map-marker-alt"></i>
          {{ $property->city }}{{ $property->address ? '، '.$property->address : '' }}
        </div>
        {{-- Features --}}
        <div class="pcft">
          <div class="pci"><div class="pci-ic"><i class="fas fa-bed"></i></div><div class="pci-v">{{ $property->bedrooms ?? '—' }}</div><div class="pci-l">غرف</div></div>
          <div class="pci"><div class="pci-ic"><i class="fas fa-bath"></i></div><div class="pci-v">{{ $property->bathrooms ?? '—' }}</div><div class="pci-l">حمام</div></div>
          <div class="pci"><div class="pci-ic"><i class="fas fa-expand-arrows-alt"></i></div><div class="pci-v">{{ $property->area ?? '—' }}</div><div class="pci-l">م²</div></div>
        </div>
        {{-- Price --}}
        <div class="pc-pr">
          {{ number_format($property->price) }}
          <small>ريال{{ $property->listing_type == 'rent' ? ' / سنوياً' : '' }}</small>
        </div>
        {{-- Actions --}}
        <div class="pc-ft">
          <a href="{{ route('properties.show', $property->id) }}" class="bdt">
            <i class="fas fa-eye"></i> عرض التفاصيل
          </a>
          <button class="bsm bcl" title="اتصل"><i class="fas fa-phone"></i></button>
          <button class="bsm bsh" title="مشاركة"><i class="fas fa-share-alt"></i></button>
        </div>
      </div>
    </div>
    @empty
    <div class="empt">
      <div class="em-ic"><i class="fas fa-search"></i></div>
      <div class="em-t">لا توجد عقارات مطابقة</div>
      <div class="em-s">حاول تغيير معايير البحث أو تصفح جميع العقارات المتاحة</div>
      <a href="{{ route('properties.index') }}" class="brs">
        <i class="fas fa-rotate-left"></i> عرض جميع العقارات
      </a>
    </div>
    @endforelse
  </div>

  {{-- Pagination --}}
  @if($properties->hasPages())
  <div class="pgw">{{ $properties->withQueryString()->links() }}</div>
  @endif

</div>

@endsection

@push('scripts')
<script>
/* Wishlist */
function tw(btn){
  const on=btn.classList.toggle('liked');
  btn.querySelector('i').className=on?'fas fa-heart':'far fa-heart';
  st(on?'تمت الإضافة إلى المفضلة ❤️':'تمت الإزالة من المفضلة','heart');
}
/* Pills */
function tp(el){el.classList.toggle('on')}
/* Toast */
let _t;
function st(msg,type){
  const el=document.getElementById('tst');
  el.className=`tst ${type} show`;
  el.innerHTML=`<i class="fas fa-${type==='heart'?'heart':'check-circle'}"></i>${msg}`;
  clearTimeout(_t);_t=setTimeout(()=>el.classList.remove('show'),3000);
}
/* Scroll reveal */
const io=new IntersectionObserver(en=>{
  en.forEach(e=>{if(e.isIntersecting){e.target.style.animationPlayState='running';io.unobserve(e.target)}});
},{threshold:.08});
document.querySelectorAll('.pc').forEach(c=>{c.style.animationPlayState='paused';io.observe(c)});
</script>
@endpush