@extends('layouts.app')

@section('title', 'Agent ' . $agent->user->name . ' - Real Estate Pro')

@section('content')

{{-- ════════════════════════════════════════════════════
     AGENT PROFILE — "MODERN SAPPHIRE LUXURY"
     تصميم: أزرق ملكي + ذهبي + خلفيات نظيفة
════════════════════════════════════════════════════ --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Outfit:wght@300;400;500;600;700;800&display=swap');

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

.ap-page { min-height:100vh; background:var(--cream); font-family:var(--font-b); color:var(--ink-2); padding-bottom:80px; }

/* HERO */
.ap-hero { position:relative; height:440px; background:var(--ink); overflow:hidden; }
.ap-hero-bg { position:absolute; inset:0; background: radial-gradient(circle at 80% 20%, rgba(37,99,235,0.25) 0%, transparent 50%), radial-gradient(circle at 20% 80%, rgba(13,148,136,0.2) 0%, transparent 50%), linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); }
.ap-hero-mesh { position:absolute; inset:0; opacity:.06; background-image: radial-gradient(rgba(255,255,255,0.7) 1px, transparent 1px); background-size:32px 32px; }
.ap-hero-bar { position:absolute; top:0; right:0; width:50%; height:100%; background:linear-gradient(90deg, transparent, rgba(37,99,235,0.06)); clip-path:polygon(20% 0, 100% 0, 100% 100%, 0% 100%); }
.ap-hero-deco { position:absolute; bottom:-20px; left:-10px; font-family:var(--font-h); font-size:clamp(100px,14vw,180px); font-style:italic; color:rgba(255,255,255,.025); line-height:1; pointer-events:none; user-select:none; white-space:nowrap; }
.ap-hero-dots { position:absolute; inset:0; overflow:hidden; pointer-events:none; }
.ap-hero-dots::before { content:''; position:absolute; width:300px; height:300px; top:-80px; right:10%; background:radial-gradient(circle, rgba(201,147,58,.18) 0%, transparent 70%); border-radius:50%; animation:float-a 8s ease-in-out infinite; }
.ap-hero-dots::after  { content:''; position:absolute; width:200px; height:200px; bottom:-50px; left:15%; background:radial-gradient(circle, rgba(27,107,107,.2) 0%, transparent 70%); border-radius:50%; animation:float-b 10s ease-in-out infinite; }
@keyframes float-a { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(-20px) scale(1.05)} }
@keyframes float-b { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(15px) scale(.95)} }
.ap-hero-inner { position:relative; z-index:10; height:100%; display:flex; flex-direction:column; padding:28px 40px 0; max-width:1400px; margin:0 auto; }
.ap-bread { display:inline-flex; align-items:center; gap:8px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); backdrop-filter:blur(8px); padding:6px 16px; border-radius:30px; font-size:12px; color:rgba(255,255,255,.55); align-self:flex-start; }
.ap-bread a { color:rgba(255,255,255,.55); text-decoration:none; transition:color .15s; }
.ap-bread a:hover { color:rgba(255,255,255,.9); }
.ap-bread i { font-size:8px; opacity:.5; }
.ap-bread .cur { color:rgba(255,255,255,.9); font-weight:600; }
.ap-hero-tag { margin-top:auto; margin-bottom:48px; }
.ap-hero-tag h1 { font-family:var(--font-h); font-size:clamp(32px,4vw,52px); color:var(--white); line-height:1.15; font-weight:400; }
.ap-hero-tag h1 em { font-style:italic; background:linear-gradient(120deg, var(--gold-l), var(--tc-l)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
.ap-hero-tag p { font-size:15px; color:rgba(255,255,255,.5); margin-top:10px; max-width:460px; line-height:1.7; }

/* CONTAINER & GRID */
.ap-container { max-width:1400px; margin:0 auto; padding:0 32px; }
.ap-grid { display:grid; grid-template-columns:320px 1fr; gap:32px; margin-top:-140px; align-items:start; position:relative; z-index:20; }
@media(max-width:1024px){ .ap-grid{grid-template-columns:1fr;margin-top:-40px} .ap-hero-tag{display:none} .ap-hero{height:200px} }

/* SIDEBAR CARD */
.ap-card { background:var(--white); border-radius:var(--r-xl); overflow:hidden; box-shadow:0 4px 6px rgba(15,23,42,.04),0 20px 60px rgba(15,23,42,.1); position:sticky; top:88px; border:1px solid rgba(37,99,235,.08); }
.ap-card-head { height:120px; background:linear-gradient(135deg, var(--tc-d) 0%, var(--tc) 50%, var(--tc-l) 100%); position:relative; overflow:hidden; }
.ap-card-head::before { content:''; position:absolute; inset:0; background:repeating-linear-gradient(45deg, transparent, transparent 20px, rgba(255,255,255,.04) 20px, rgba(255,255,255,.04) 21px); }
.ap-card-head::after { content:''; position:absolute; bottom:0; left:0; right:0; height:50px; background:linear-gradient(to bottom, transparent, var(--white)); }
.ap-avatar-wrap { position:relative; width:110px; height:110px; margin:0 auto -60px; }
.ap-avatar-ring { position:absolute; inset:-4px; border-radius:50%; background:linear-gradient(135deg,var(--gold-l),var(--tc),var(--teal)); animation:ring-spin 6s linear infinite; opacity:.7; }
@keyframes ring-spin { to{transform:rotate(360deg)} }
.ap-avatar-ring-2 { position:absolute; inset:-2px; border-radius:50%; background:var(--white); }
.ap-avatar { position:relative; z-index:1; width:100%; height:100%; border-radius:50%; overflow:hidden; border:3px solid var(--white); box-shadow:0 8px 24px rgba(15,23,42,.2); }
.ap-avatar img { width:100%; height:100%; object-fit:cover; transition:transform .4s var(--ease); }
.ap-avatar-wrap:hover .ap-avatar img { transform:scale(1.07); }
.ap-avatar-default { width:100%; height:100%; background:var(--sand); display:flex; align-items:center; justify-content:center; color:var(--ink-3); font-size:42px; }
.ap-verified { position:absolute; bottom:4px; right:4px; z-index:2; width:26px; height:26px; background:var(--teal); border-radius:50%; border:2.5px solid var(--white); display:flex; align-items:center; justify-content:center; color:var(--white); font-size:10px; box-shadow:0 2px 8px rgba(13,148,136,.4); }
.ap-card-body { padding:0 28px 28px; text-align:center; }
.ap-agent-name { font-family:var(--font-h); font-size:24px; color:var(--ink); font-weight:400; margin-bottom:4px; margin-top:72px; }
.ap-agent-spec { font-size:13px; font-weight:600; color:var(--tc); letter-spacing:.04em; margin-bottom:10px; }
.ap-agent-co { display:inline-flex; align-items:center; gap:6px; background:var(--sand); color:var(--ink-3); font-size:12px; font-weight:600; padding:5px 14px; border-radius:20px; border:1px solid var(--sand-d); margin-bottom:18px; }
.ap-agent-co i { font-size:10px; color:var(--tc); }
.ap-rating { display:flex; flex-direction:column; align-items:center; padding:16px 0; margin:4px 0 20px; border-top:1px solid var(--sand); border-bottom:1px solid var(--sand); }
.ap-stars { display:flex; gap:3px; margin-bottom:4px; }
.ap-stars i { font-size:16px; color:var(--sand-d); }
.ap-stars i.on { color:var(--gold); }
.ap-rating-num { font-size:28px; font-weight:800; color:var(--ink); font-family:var(--font-h); line-height:1; }
.ap-rating-lbl { font-size:11.5px; color:var(--ink-4); margin-top:2px; }
.ap-btn-primary { display:flex; align-items:center; justify-content:center; gap:10px; width:100%; padding:13px 20px; border-radius:var(--r-md); font-weight:700; font-size:14px; text-decoration:none; transition:all .22s var(--ease); margin-bottom:10px; font-family:var(--font-b); cursor:pointer; border:none; letter-spacing:.02em; }
.ap-btn-tc { background:linear-gradient(135deg,var(--tc),var(--tc-l)); color:var(--white); box-shadow:0 4px 16px rgba(37,99,235,.3); }
.ap-btn-tc:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(37,99,235,.4); }
.ap-btn-wa { background:linear-gradient(135deg,#1a7c3e,#25a259); color:var(--white); box-shadow:0 4px 16px rgba(37,162,89,.25); }
.ap-btn-wa:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(37,162,89,.35); }
.ap-btn-outline { background:transparent; color:var(--ink-2); border:1.5px solid var(--sand-d); }
.ap-btn-outline:hover { border-color:var(--tc); color:var(--tc); background:var(--tc-ll); }
.ap-btn-icon { width:34px; height:34px; background:rgba(255,255,255,.15); border-radius:var(--r-sm); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:13px; }
.ap-socials { display:flex; justify-content:center; gap:8px; padding:16px 0 0; border-top:1px solid var(--sand); margin-top:6px; }
.ap-social-btn { width:36px; height:36px; border-radius:10px; background:var(--sand); border:1px solid var(--sand-d); display:flex; align-items:center; justify-content:center; color:var(--ink-3); font-size:13px; text-decoration:none; transition:all .18s var(--ease); }
.ap-social-btn:hover { background:var(--tc); color:var(--white); border-color:var(--tc); transform:translateY(-2px); }
.ap-contact-card { background:var(--white); border-radius:var(--r-lg); border:1px solid rgba(37,99,235,.08); padding:22px 24px; margin-top:20px; box-shadow:0 2px 20px rgba(15,23,42,.06); }
.ap-contact-card h4 { font-size:10px; font-weight:800; letter-spacing:.18em; text-transform:uppercase; color:var(--ink-4); margin-bottom:16px; }
.ap-contact-item { display:flex; align-items:flex-start; gap:12px; margin-bottom:14px; font-size:13px; color:var(--ink-2); }
.ap-contact-item:last-child { margin-bottom:0; }
.ap-contact-ic { width:32px; height:32px; border-radius:var(--r-sm); display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0; margin-top:1px; }
.ci-tc   { background:var(--tc-ll); color:var(--tc); }
.ci-teal { background:#d4eeee;      color:var(--teal); }
.ci-gold { background:#f5e9d5;      color:var(--gold); }

/* MAIN */
.ap-main { display:flex; flex-direction:column; gap:28px; }
.ap-section { background:var(--white); border-radius:var(--r-xl); padding:36px 40px; box-shadow:0 2px 6px rgba(15,23,42,.04),0 12px 40px rgba(15,23,42,.06); border:1px solid rgba(37,99,235,.06); position:relative; overflow:hidden; }
@media(max-width:768px){ .ap-section{padding:24px} }
.ap-section-h { display:flex; align-items:center; gap:14px; margin-bottom:28px; }
.ap-section-ic { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.sic-tc   { background:var(--tc-ll); color:var(--tc); }
.sic-gold { background:#f5e9d5;      color:var(--gold); }
.sic-teal { background:#d4eeee;      color:var(--teal); }
.ap-section-title { font-family:var(--font-h); font-size:24px; color:var(--ink); font-weight:400; line-height:1.2; }
.ap-section-sub { font-size:13px; color:var(--ink-4); margin-top:2px; }

/* STATS */
.ap-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
@media(max-width:768px){ .ap-stats{grid-template-columns:1fr 1fr} }
.ap-stat { background:var(--cream); border:1px solid var(--sand-d); border-radius:var(--r-lg); padding:22px 16px; text-align:center; position:relative; overflow:hidden; transition:all .22s var(--ease); }
.ap-stat::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:3px 3px 0 0; transition:opacity .22s; opacity:0; }
.ap-stat:hover { border-color:transparent; box-shadow:0 8px 30px rgba(15,23,42,.1); transform:translateY(-3px); }
.ap-stat:hover::before { opacity:1; }
.ap-stat-s1::before { background:linear-gradient(90deg,var(--tc),var(--tc-l)); }
.ap-stat-s2::before { background:linear-gradient(90deg,var(--gold),var(--gold-l)); }
.ap-stat-s3::before { background:linear-gradient(90deg,var(--teal),var(--teal-l)); }
.ap-stat-s4::before { background:linear-gradient(90deg,var(--tc-d),var(--tc)); }
.ap-stat-icon { width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:20px; margin:0 auto 12px; transition:transform .22s var(--ease); }
.ap-stat:hover .ap-stat-icon { transform:rotate(6deg) scale(1.08); }
.si-tc   { background:var(--tc-ll); color:var(--tc); }
.si-gold { background:#f5e9d5;      color:var(--gold); }
.si-teal { background:#d4eeee;      color:var(--teal); }
.si-ink  { background:var(--sand);  color:var(--ink-2); }
.ap-stat-val { font-family:var(--font-h); font-size:38px; font-weight:400; color:var(--ink); line-height:1; margin-bottom:4px; }
.ap-stat-lbl { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--ink-4); }

/* ABOUT */
.ap-about-quote { position:absolute; top:20px; right:28px; font-size:100px; color:rgba(37,99,235,.06); font-family:Georgia,serif; line-height:1; pointer-events:none; }
.ap-about-body { font-size:15.5px; line-height:1.8; color:var(--ink-3); max-width:700px; }
.ap-no-content { font-style:italic; color:var(--ink-4); font-size:14px; }
.ap-langs { display:flex; flex-wrap:wrap; gap:8px; margin-top:24px; padding-top:22px; border-top:1px solid var(--sand); }
.ap-lang-label { font-size:10px; font-weight:800; letter-spacing:.15em; text-transform:uppercase; color:var(--ink-4); width:100%; margin-bottom:4px; }
.ap-lang { display:inline-flex; align-items:center; gap:7px; padding:7px 16px; background:var(--cream); border:1px solid var(--sand-d); border-radius:30px; font-size:13px; font-weight:600; color:var(--ink-2); transition:all .18s; cursor:default; }
.ap-lang:hover { background:var(--tc-ll); border-color:rgba(37,99,235,.25); color:var(--tc); }
.ap-lang i { font-size:10px; color:var(--tc); opacity:.7; }

/* PROPERTIES */
.ap-listings-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
.ap-view-all { display:flex; align-items:center; gap:8px; font-size:13px; font-weight:700; color:var(--tc); text-decoration:none; transition:gap .2s; }
.ap-view-all:hover { gap:12px; color:var(--tc-d); }
.ap-view-all-arrow { width:30px; height:30px; border-radius:50%; background:var(--tc-ll); display:flex; align-items:center; justify-content:center; font-size:10px; transition:background .18s; }
.ap-view-all:hover .ap-view-all-arrow { background:var(--tc); color:var(--white); }
.ap-props-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:20px; }
@media(max-width:768px){ .ap-props-grid{grid-template-columns:1fr} }
.ap-prop { background:var(--cream); border-radius:var(--r-lg); overflow:hidden; border:1px solid var(--sand-d); transition:all .25s var(--ease); position:relative; }
.ap-prop:hover { border-color:rgba(37,99,235,.2); box-shadow:0 12px 40px rgba(15,23,42,.12); transform:translateY(-4px); }
.ap-prop-img { height:210px; overflow:hidden; position:relative; }
.ap-prop-img img { width:100%; height:100%; object-fit:cover; transition:transform .5s var(--ease); }
.ap-prop:hover .ap-prop-img img { transform:scale(1.06); }
.ap-prop-overlay { position:absolute; inset:0; background:linear-gradient(to top, rgba(15,23,42,.6) 0%, rgba(15,23,42,.05) 50%, transparent 100%); transition:opacity .25s; }
.ap-prop:hover .ap-prop-overlay { opacity:.7; }
.ap-prop-badges { position:absolute; top:12px; left:12px; display:flex; gap:6px; z-index:2; }
.ap-prop-badge { font-size:10px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; padding:4px 10px; border-radius:6px; backdrop-filter:blur(6px); }
.pb-status { background:var(--tc); color:var(--white); }
.pb-type   { background:rgba(15,23,42,.6);    color:rgba(255,255,255,.9); }
.ap-prop-price { position:absolute; bottom:12px; left:12px; z-index:2; color:var(--white); }
.ap-prop-price-val { font-family:var(--font-h); font-size:22px; line-height:1; }
.ap-prop-price-cur { font-size:11px; opacity:.8; }
.ap-prop-photos { position:absolute; bottom:12px; right:12px; z-index:2; background:rgba(255,255,255,.18); backdrop-filter:blur(6px); color:var(--white); font-size:11px; font-weight:600; padding:4px 10px; border-radius:6px; display:flex; align-items:center; gap:5px; }
.ap-prop-body { padding:18px; }
.ap-prop-title { font-family:var(--font-h); font-size:17px; color:var(--ink); font-weight:400; margin-bottom:6px; line-height:1.3; }
.ap-prop-title a { text-decoration:none; color:inherit; transition:color .18s; }
.ap-prop:hover .ap-prop-title a { color:var(--tc); }
.ap-prop-loc { display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--ink-3); margin-bottom:14px; }
.ap-prop-loc i { color:var(--tc); font-size:11px; }
.ap-prop-meta { display:grid; grid-template-columns:repeat(3,1fr); gap:4px; padding-top:14px; border-top:1px solid var(--sand-d); }
.ap-prop-meta-item { text-align:center; padding:6px 0; }
.ap-prop-meta-item + .ap-prop-meta-item { border-left:1px solid var(--sand-d); }
.ap-prop-meta-val { display:block; font-weight:700; font-size:15px; color:var(--ink); }
.ap-prop-meta-lbl { display:block; font-size:10.5px; color:var(--ink-4); margin-top:1px; }
.ap-empty { grid-column:1/-1; text-align:center; padding:50px 20px; border:2px dashed var(--sand-d); border-radius:var(--r-lg); background:var(--cream); }
.ap-empty-icon { width:64px; height:64px; background:var(--sand); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; font-size:24px; color:var(--ink-4); }
.ap-empty h3 { font-size:16px; font-weight:700; color:var(--ink-2); margin-bottom:6px; }
.ap-empty p  { font-size:13.5px; color:var(--ink-4); }

/* REVIEWS */
.ap-reviews-top { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:28px; }
.ap-write-btn { display:inline-flex; align-items:center; gap:8px; padding:11px 22px; background:var(--ink); color:var(--white); border:none; border-radius:var(--r-md); font-family:var(--font-b); font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; transition:all .2s var(--ease); white-space:nowrap; box-shadow:0 4px 16px rgba(15,23,42,.2); }
.ap-write-btn:hover { background:var(--tc-d); transform:translateY(-1px); box-shadow:0 8px 24px rgba(15,23,42,.25); }
.ap-write-btn i { font-size:11px; }
.ap-review { background:var(--cream); border:1px solid var(--sand-d); border-radius:var(--r-lg); padding:24px; transition:all .2s var(--ease); margin-bottom:14px; position:relative; }
.ap-review:last-child { margin-bottom:0; }
.ap-review:hover { border-color:rgba(37,99,235,.18); background:var(--white); box-shadow:0 6px 24px rgba(15,23,42,.07); }
.ap-review-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
.ap-review-user { display:flex; align-items:center; gap:12px; }
.ap-review-av { width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg,var(--tc),var(--gold)); display:flex; align-items:center; justify-content:center; color:var(--white); font-size:16px; font-weight:800; flex-shrink:0; }
.ap-review-name { font-size:15px; font-weight:700; color:var(--ink); }
.ap-review-date { font-size:11.5px; color:var(--ink-4); margin-top:2px; }
.ap-review-stars { display:flex; gap:2px; background:var(--white); padding:6px 12px; border-radius:8px; border:1px solid var(--sand-d); }
.ap-review-stars i { font-size:12px; color:var(--sand-d); }
.ap-review-stars i.on { color:var(--gold); }
.ap-review-quote-ic { position:absolute; top:16px; right:70px; font-size:60px; color:rgba(37,99,235,.05); font-family:Georgia,serif; line-height:1; pointer-events:none; }
.ap-review-title   { font-size:15px; font-weight:700; color:var(--ink-2); margin-bottom:6px; }
.ap-review-content { font-size:14px; color:var(--ink-3); line-height:1.75; }
.ap-review-empty { text-align:center; padding:48px 20px; background:var(--cream); border:2px dashed var(--sand-d); border-radius:var(--r-lg); }
.ap-review-empty i { font-size:28px; color:var(--ink-4); opacity:.4; display:block; margin-bottom:12px; }
.ap-review-empty p { font-size:14px; color:var(--ink-4); }

/* MODAL */
.rm-overlay { position:fixed; inset:0; background:rgba(15,23,42,.85); backdrop-filter:blur(6px); z-index:9990; display:flex; align-items:center; justify-content:center; padding:20px; opacity:0; visibility:hidden; transition:opacity .28s var(--ease),visibility .28s; }
.rm-overlay.open { opacity:1; visibility:visible; }
.rm-box { background:var(--white); border-radius:var(--r-xl); width:100%; max-width:520px; overflow:hidden; transform:translateY(20px) scale(.97); transition:transform .28s var(--ease); position:relative; box-shadow:0 30px 80px rgba(15,23,42,.35); }
.rm-overlay.open .rm-box { transform:translateY(0) scale(1); }
.rm-top { height:5px; background:linear-gradient(90deg,var(--tc-d),var(--tc),var(--tc-l),var(--gold)); }
.rm-head { display:flex; align-items:flex-start; justify-content:space-between; padding:28px 32px 20px; border-bottom:1px solid var(--sand); }
.rm-head-title { font-family:var(--font-h); font-size:24px; color:var(--ink); font-weight:400; }
.rm-head-sub { font-size:13px; color:var(--ink-4); margin-top:3px; }
.rm-close { width:36px; height:36px; border-radius:10px; background:var(--sand); border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--ink-3); transition:all .18s; font-size:14px; flex-shrink:0; }
.rm-close:hover { background:var(--tc); color:var(--white); }
.rm-body { padding:28px 32px; }
.rm-stars-wrap { text-align:center; margin-bottom:28px; }
.rm-stars-label { font-size:11px; font-weight:800; letter-spacing:.15em; text-transform:uppercase; color:var(--ink-4); margin-bottom:14px; }
.rm-stars { display:flex; justify-content:center; gap:6px; }
.rm-star-btn { background:none; border:none; cursor:pointer; padding:4px; transition:transform .15s; }
.rm-star-btn:hover { transform:scale(1.15); }
.rm-star-btn i { font-size:36px; color:var(--sand-d); transition:color .15s; }
.rm-star-btn i.on { color:var(--gold); }
.rm-star-txt { font-size:12px; font-weight:600; color:var(--tc); min-height:20px; margin-top:10px; opacity:0; transition:opacity .2s; }
.rm-star-txt.show { opacity:1; }
.rm-field { margin-bottom:18px; }
.rm-field label { display:block; font-size:11.5px; font-weight:700; color:var(--ink-3); margin-bottom:7px; letter-spacing:.04em; text-transform:uppercase; }
.rm-field input, .rm-field textarea { width:100%; background:var(--cream); border:1.5px solid var(--sand-d); border-radius:var(--r-md); padding:12px 16px; font-family:var(--font-b); font-size:14px; color:var(--ink); outline:none; transition:border-color .18s,box-shadow .18s,background .18s; }
.rm-field input:focus, .rm-field textarea:focus { border-color:var(--tc); background:var(--white); box-shadow:0 0 0 3px rgba(37,99,235,.1); }
.rm-field textarea { resize:none; }
.rm-actions { display:flex; gap:12px; margin-top:24px; }
.rm-cancel { flex:1; padding:13px; background:var(--cream); border:1.5px solid var(--sand-d); border-radius:var(--r-md); font-family:var(--font-b); font-size:13.5px; font-weight:700; color:var(--ink-2); cursor:pointer; transition:all .18s; }
.rm-cancel:hover { border-color:var(--tc); color:var(--tc); }
.rm-submit { flex:2; padding:13px; background:linear-gradient(135deg,var(--tc),var(--tc-l)); border:none; border-radius:var(--r-md); font-family:var(--font-b); font-size:13.5px; font-weight:700; color:var(--white); cursor:pointer; transition:all .2s; box-shadow:0 4px 16px rgba(37,99,235,.3); }
.rm-submit:hover { transform:translateY(-1px); box-shadow:0 8px 24px rgba(37,99,235,.4); }
</style>

<div class="ap-page">
  <div class="ap-hero">
    <div class="ap-hero-bg"></div>
    <div class="ap-hero-mesh"></div>
    <div class="ap-hero-bar"></div>
    <div class="ap-hero-dots"></div>
    <div class="ap-hero-deco">Estate</div>
    <div class="ap-hero-inner">
      <nav class="ap-bread">
        <a href="/">Home</a><i class="fas fa-chevron-right"></i>
        <a href="{{ route('agents.directory') }}">Agents</a><i class="fas fa-chevron-right"></i>
        <span class="cur">{{ $agent->user->name }}</span>
      </nav>
      <div class="ap-hero-tag">
        <h1>Exceptional Service,<br><em>Outstanding Results.</em></h1>
        <p>Partner with a trusted real estate professional dedicated to finding your dream property.</p>
      </div>
    </div>
  </div>

  <div class="ap-container">
    <div class="ap-grid">

      {{-- SIDEBAR --}}
      <div>
        <div class="ap-card">
          <div class="ap-card-head"></div>
          <div class="ap-card-body">
            <div class="ap-avatar-wrap">
              <div class="ap-avatar-ring"></div>
              <div class="ap-avatar-ring-2"></div>
              <div class="ap-avatar">
                @if($agent->profile && $agent->profile->photo)
                  <img src="{{ Storage::url($agent->profile->photo) }}" alt="{{ $agent->user->name }}">
                @else
                  <div class="ap-avatar-default"><i class="fas fa-user"></i></div>
                @endif
              </div>
              @if($agent->is_verified)
                <div class="ap-verified" title="Verified"><i class="fas fa-check"></i></div>
              @endif
            </div>
            <div class="ap-agent-name">{{ $agent->user->name }}</div>
            <div class="ap-agent-spec">{{ $agent->specialization ?? 'Real Estate Specialist' }}</div>
            @if($agent->company)
              <div class="ap-agent-co"><i class="fas fa-building"></i> {{ $agent->company->name }}</div>
            @endif
            <div class="ap-rating">
              <div class="ap-stars">
                @for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i<=round($stats['average_rating']) ? 'on' : '' }}"></i>@endfor
              </div>
              <div class="ap-rating-num">{{ number_format($stats['average_rating'],1) }}</div>
              <div class="ap-rating-lbl">{{ $stats['total_reviews'] }} Client Reviews</div>
            </div>
            @if($agent->profile && $agent->profile->phone)
              <a href="tel:{{ $agent->profile->phone }}" class="ap-btn-primary ap-btn-tc">
                <span class="ap-btn-icon"><i class="fas fa-phone"></i></span> Call Now
              </a>
              <a href="https://wa.me/{{ preg_replace('/[^0-9]/','', $agent->profile->phone) }}" target="_blank" class="ap-btn-primary ap-btn-wa">
                <span class="ap-btn-icon"><i class="fab fa-whatsapp text-lg"></i></span> WhatsApp
              </a>
            @else
              <button disabled class="ap-btn-primary" style="background:var(--sand);color:var(--ink-4);cursor:not-allowed;"><i class="fas fa-phone-slash"></i> Contact Unavailable</button>
            @endif
            @if($agent->profile && $agent->profile->email)
              <a href="mailto:{{ $agent->profile->email }}" class="ap-btn-primary ap-btn-outline"><i class="far fa-envelope"></i> Email Agent</a>
            @endif
            @if($agent->profile && $agent->profile->social_media)
            <div class="ap-socials">
              @foreach($agent->profile->social_media as $p => $l)
                @if($l)<a href="{{ $l }}" target="_blank" class="ap-social-btn"><i class="fab fa-{{ $p }}"></i></a>@endif
              @endforeach
            </div>
            @endif
          </div>
        </div>

        <div class="ap-contact-card">
          <h4>Contact Details</h4>
          @if($agent->profile && $agent->profile->address)
            <div class="ap-contact-item"><div class="ap-contact-ic ci-tc"><i class="fas fa-map-marker-alt"></i></div><span>{{ $agent->profile->address }}</span></div>
          @endif
          @if($agent->license_number)
            <div class="ap-contact-item"><div class="ap-contact-ic ci-teal"><i class="fas fa-id-card"></i></div><span>License: <strong>{{ $agent->license_number }}</strong></span></div>
          @endif
          <div class="ap-contact-item"><div class="ap-contact-ic ci-gold"><i class="fas fa-calendar-alt"></i></div><span>Joined {{ $agent->created_at->format('M Y') }}</span></div>
        </div>
      </div>

      {{-- MAIN --}}
      <div class="ap-main">

        {{-- Stats --}}
        <div class="ap-stats">
          <div class="ap-stat ap-stat-s1"><div class="ap-stat-icon si-tc"><i class="fas fa-home"></i></div><div class="ap-stat-val">{{ $stats['total_properties'] }}</div><div class="ap-stat-lbl">Properties</div></div>
          <div class="ap-stat ap-stat-s2"><div class="ap-stat-icon si-gold"><i class="fas fa-handshake"></i></div><div class="ap-stat-val">{{ $stats['sold_properties'] }}</div><div class="ap-stat-lbl">Sold</div></div>
          <div class="ap-stat ap-stat-s3"><div class="ap-stat-icon si-teal"><i class="fas fa-award"></i></div><div class="ap-stat-val">{{ $stats['experience_years'] }}</div><div class="ap-stat-lbl">Years Exp.</div></div>
          <div class="ap-stat ap-stat-s4"><div class="ap-stat-icon si-ink"><i class="fas fa-star"></i></div><div class="ap-stat-val">{{ number_format($stats['average_rating'],1) }}</div><div class="ap-stat-lbl">Rating</div></div>
        </div>

        {{-- About --}}
        <div class="ap-section">
          <div class="ap-about-quote">"</div>
          <div class="ap-section-h">
            <div class="ap-section-ic sic-tc"><i class="fas fa-user-tie"></i></div>
            <div><div class="ap-section-title">About {{ $agent->user->name }}</div></div>
          </div>
          <div class="ap-about-body">
            @if($agent->profile && $agent->profile->bio)
              {!! nl2br(e($agent->profile->bio)) !!}
            @else
              <p class="ap-no-content">No biography available for this agent.</p>
            @endif
          </div>
          @if($agent->profile && $agent->profile->languages)
            <div class="ap-langs">
              <div class="ap-lang-label">Languages Spoken</div>
              @foreach($agent->profile->languages as $lang)
                <span class="ap-lang"><i class="fas fa-comment-alt"></i> {{ $lang }}</span>
              @endforeach
            </div>
          @endif
        </div>

        {{-- Listings --}}
        <div class="ap-section">
          <div class="ap-listings-head">
            <div class="ap-section-h" style="margin-bottom:0;">
              <div class="ap-section-ic sic-gold"><i class="fas fa-building"></i></div>
              <div><div class="ap-section-title">Featured Listings</div></div>
            </div>
            <a href="{{ route('properties.index', ['agent'=>$agent->id]) }}" class="ap-view-all">
              View All <span class="ap-view-all-arrow"><i class="fas fa-arrow-right"></i></span>
            </a>
          </div>
          <div class="ap-props-grid">
            @forelse($agent->properties()->where('status','active')->with(['media','location','price','propertyType'])->limit(4)->get() as $property)
              <div class="ap-prop">
                <div class="ap-prop-img">
                  <img src="{{ $property->media->first() ? Storage::url($property->media->first()->file_path) : asset('images/property-placeholder.jpg') }}" alt="{{ $property->title }}">
                  <div class="ap-prop-overlay"></div>
                  <div class="ap-prop-badges">
                    <span class="ap-prop-badge pb-status">{{ $property->status }}</span>
                    <span class="ap-prop-badge pb-type">{{ $property->propertyType->name ?? 'Property' }}</span>
                  </div>
                  <div class="ap-prop-price">
                    <div class="ap-prop-price-val">{{ number_format($property->price->price ?? 0) }}</div>
                    <div class="ap-prop-price-cur">{{ $property->price->currency ?? 'USD' }}</div>
                  </div>
                  <div class="ap-prop-photos"><i class="fas fa-camera"></i> {{ $property->media->count() }}</div>
                </div>
                <div class="ap-prop-body">
                  <div class="ap-prop-title"><a href="{{ route('properties.show', $property->id) }}">{{ $property->title }}</a></div>
                  <div class="ap-prop-loc"><i class="fas fa-map-marker-alt"></i><span>{{ $property->location->address ?? 'Address not available' }}</span></div>
                  <div class="ap-prop-meta">
                    <div class="ap-prop-meta-item"><span class="ap-prop-meta-val">{{ $property->bedrooms ?? 0 }}</span><span class="ap-prop-meta-lbl">Beds</span></div>
                    <div class="ap-prop-meta-item"><span class="ap-prop-meta-val">{{ $property->bathrooms ?? 0 }}</span><span class="ap-prop-meta-lbl">Baths</span></div>
                    <div class="ap-prop-meta-item"><span class="ap-prop-meta-val">{{ $property->area ?? 0 }}</span><span class="ap-prop-meta-lbl">m²</span></div>
                  </div>
                </div>
              </div>
            @empty
              <div class="ap-empty">
                <div class="ap-empty-icon"><i class="far fa-building"></i></div>
                <h3>No active listings</h3>
                <p>This agent doesn't have any active properties at the moment.</p>
              </div>
            @endforelse
          </div>
        </div>

        {{-- Reviews --}}
        <div class="ap-section">
          <div class="ap-reviews-top">
            <div class="ap-section-h" style="margin-bottom:0;">
              <div class="ap-section-ic sic-teal"><i class="fas fa-star"></i></div>
              <div>
                <div class="ap-section-title">Client Experiences</div>
                <div class="ap-section-sub">What people say about {{ $agent->user->name }}</div>
              </div>
            </div>
            @auth
              <button onclick="openReviewModal()" class="ap-write-btn"><i class="fas fa-pen"></i> Write a Review</button>
            @else
              <a href="{{ route('login') }}" class="ap-write-btn" style="background:var(--sand);color:var(--ink-2);box-shadow:none;">Login to Review</a>
            @endauth
          </div>

          @forelse($agent->reviews as $review)
            <div class="ap-review">
              <div class="ap-review-quote-ic">"</div>
              <div class="ap-review-header">
                <div class="ap-review-user">
                  <div class="ap-review-av">{{ substr($review->user->name,0,1) }}</div>
                  <div>
                    <div class="ap-review-name">{{ $review->user->name }}</div>
                    <div class="ap-review-date">{{ $review->created_at->format('M d, Y') }}</div>
                  </div>
                </div>
                <div class="ap-review-stars">
                  @for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i<=$review->rating ? 'on' : '' }}"></i>@endfor
                </div>
              </div>
              <div class="ap-review-title">{{ $review->title }}</div>
              <div class="ap-review-content">"{{ $review->content }}"</div>
            </div>
          @empty
            <div class="ap-review-empty">
              <i class="far fa-comment-dots"></i>
              <p>No reviews yet. Be the first to share your experience!</p>
            </div>
          @endforelse

          @if($agent->reviews->count() > 5)
            <div style="text-align:center;margin-top:20px;">
              <button style="color:var(--tc);font-weight:700;background:none;border:none;cursor:pointer;font-size:14px;font-family:var(--font-b);">Show More Reviews <i class="fas fa-chevron-down"></i></button>
            </div>
          @endif
        </div>

      </div>
    </div>
  </div>
</div>

{{-- MODAL --}}
@auth
<div class="rm-overlay" id="reviewModal">
  <div class="rm-box">
    <div class="rm-top"></div>
    <div class="rm-head">
      <div>
        <div class="rm-head-title">Share Your Experience</div>
        <div class="rm-head-sub">Your feedback helps others make better decisions.</div>
      </div>
      <button class="rm-close" onclick="closeReviewModal()"><i class="fas fa-times"></i></button>
    </div>
    <div class="rm-body">
      <form action="{{ route('reviews.store') }}" method="POST">
        @csrf
        <input type="hidden" name="type" value="agent">
        <input type="hidden" name="reviewable_id" value="{{ $agent->id }}">
        <input type="hidden" name="rating" id="ratingInput">
        <div class="rm-stars-wrap">
          <div class="rm-stars-label">How would you rate your experience?</div>
          <div class="rm-stars" id="rmStars">
            @for($i=1;$i<=5;$i++)
              <button type="button" class="rm-star-btn" onclick="setRating({{$i}})"><i class="fas fa-star" data-r="{{$i}}"></i></button>
            @endfor
          </div>
          <div class="rm-star-txt" id="rmStarTxt">Select a rating</div>
        </div>
        <div class="rm-field"><label>Review Title</label><input type="text" name="title" required placeholder="e.g. Professional and dedicated agent"></div>
        <div class="rm-field"><label>Your Review</label><textarea name="content" rows="4" required placeholder="Tell us about your experience…"></textarea></div>
        <div class="rm-actions">
          <button type="button" class="rm-cancel" onclick="closeReviewModal()">Cancel</button>
          <button type="submit" class="rm-submit"><i class="fas fa-paper-plane" style="margin-left:6px;"></i> Submit Review</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endauth

<script>
const ratingLabels = ['Poor','Fair','Good','Very Good','Excellent'];
function openReviewModal()  { document.getElementById('reviewModal').classList.add('open'); document.body.style.overflow='hidden'; }
function closeReviewModal() { document.getElementById('reviewModal').classList.remove('open'); document.body.style.overflow=''; }
function setRating(r) {
  document.getElementById('ratingInput').value = r;
  document.querySelectorAll('#rmStars i').forEach((s,i) => s.classList.toggle('on', i < r));
  const t = document.getElementById('rmStarTxt');
  t.textContent = ratingLabels[r-1];
  t.classList.add('show');
}
document.querySelectorAll('.rm-star-btn').forEach((btn,i) => {
  btn.addEventListener('mouseenter', () => {
    document.querySelectorAll('#rmStars i').forEach((s,j) => s.classList.toggle('on', j <= i));
  });
  btn.addEventListener('mouseleave', () => {
    const cur = parseInt(document.getElementById('ratingInput')?.value || 0);
    document.querySelectorAll('#rmStars i').forEach((s,j) => s.classList.toggle('on', j < cur));
  });
});
document.getElementById('reviewModal')?.addEventListener('click', e => { if(e.target===e.currentTarget) closeReviewModal(); });
</script>
@endsection