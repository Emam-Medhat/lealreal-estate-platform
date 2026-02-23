@extends('layouts.app')
@section('title', 'Create Company')
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
@keyframes shake{
  0%,100%{transform:translateX(0)}
  25%{transform:translateX(-5px)}
  75%{transform:translateX(5px)}
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
.cc{font-family:'DM Sans',system-ui,sans-serif;background:var(--bg);min-height:100vh;color:var(--ink)}

/* ── HERO ── */
.cc-hero{background:linear-gradient(140deg,#0f172a 0%,#1e3a8a 48%,#2563eb 100%);position:relative;overflow:hidden}
.cc-hero::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.5) 50%,transparent)}
.cc-hero::after{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);background-size:44px 44px;pointer-events:none}
.cc-glow1{position:absolute;right:-80px;top:-60px;width:480px;height:480px;border-radius:50%;background:radial-gradient(circle,rgba(96,165,250,.2) 0%,transparent 70%);pointer-events:none}
.cc-glow2{position:absolute;left:-60px;bottom:-40px;width:320px;height:320px;border-radius:50%;background:radial-gradient(circle,rgba(139,92,246,.15) 0%,transparent 70%);pointer-events:none}
.cc-hero-in{position:relative;z-index:1;max-width:1160px;margin:0 auto;padding:46px 48px 94px;display:flex;align-items:flex-start;justify-content:space-between;gap:32px;animation:slideInLeft 0.8s ease-out}
.cc-bc{display:flex;align-items:center;gap:6px;font-size:11.5px;color:rgba(255,255,255,.45);margin-bottom:16px;animation:fadeInUp 0.6s ease-out 0.2s both}
.cc-h1{font-family:'Bricolage Grotesque',sans-serif;font-size:clamp(26px,3.2vw,46px);font-weight:800;color:#fff;line-height:1.08;letter-spacing:-.025em;margin-bottom:8px;animation:fadeInUp 0.6s ease-out 0.3s both}
.cc-sub{font-size:13.5px;color:rgba(255,255,255,.5);line-height:1.65;max-width:380px;animation:fadeInUp 0.6s ease-out 0.4s both}
.cc-hero-r{display:flex;flex-direction:column;align-items:flex-end;gap:14px;padding-top:4px;animation:slideInRight 0.8s ease-out}
.cc-back{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.18);border-radius:var(--rads);color:rgba(255,255,255,.75);font-size:12.5px;font-weight:700;text-decoration:none;transition:all .18s;animation:fadeInUp 0.6s ease-out 0.5s both}
.cc-back:hover{background:rgba(255,255,255,.18);border-color:rgba(255,255,255,.4);color:#fff}
.cc-hsteps{display:flex;flex-direction:column;gap:7px}
.cc-hs{display:flex;align-items:center;gap:10px;padding:8px 15px 8px 9px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:50px;font-size:12px;font-weight:600;color:rgba(255,255,255,.5);transition:all .2s;animation:fadeInUp 0.6s ease-out;transform-origin:center}
.cc-hs.done{background:rgba(74,222,128,.12);border-color:rgba(74,222,128,.25);color:rgba(255,255,255,.8)}
.cc-hs.cur{background:rgba(37,99,235,.3);border-color:rgba(96,165,250,.5);color:#fff}
.cc-hs-n{width:22px;height:22px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;background:rgba(255,255,255,.1);color:rgba(255,255,255,.6);transition:all .25s}
.cc-hs.done .cc-hs-n{background:var(--G);color:#fff}
.cc-hs.cur .cc-hs-n{background:var(--B);color:#fff;box-shadow:0 0 0 3px rgba(37,99,235,.3)}
.cc-wave{position:absolute;bottom:-2px;left:0;right:0;height:72px;pointer-events:none}

/* ── RAIL ── */
.cc-rail-w{position:sticky;top:0;z-index:200;background:var(--sur);border-bottom:1px solid var(--bdr);box-shadow:0 2px 12px rgba(15,23,42,.07);animation:fadeInUp 0.6s ease-out 0.6s both}
.cc-rail{max-width:1160px;margin:0 auto;padding:0 48px;display:flex;align-items:stretch}
.cc-rt{flex:1;display:flex;align-items:center;gap:11px;padding:14px 0;position:relative;cursor:pointer;transition:all .2s;animation:fadeInUp 0.6s ease-out 0.7s both}
.cc-rt:not(:last-child)::after{content:'';position:absolute;right:0;top:50%;transform:translateY(-50%);width:1px;height:36%;background:var(--bdr)}
.cc-rt-n{width:32px;height:32px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;border:2px solid var(--bdr);color:var(--mul);background:var(--bg);transition:all .25s}
.cc-rt-l{font-size:11px;font-weight:800;letter-spacing:.07em;text-transform:uppercase;color:var(--mul);transition:color .2s}
.cc-rt-d{font-size:10.5px;color:var(--mul);margin-top:1px}
.cc-rt.done .cc-rt-n{background:var(--G);border-color:var(--G);color:#fff}
.cc-rt.done .cc-rt-l{color:var(--G)}
.cc-rt.cur .cc-rt-n{background:var(--B);border-color:var(--B);color:#fff;box-shadow:0 0 0 4px var(--B50)}
.cc-rt.cur .cc-rt-l{color:var(--B)}.cc-rt.cur .cc-rt-d{color:var(--mu)}
.cc-prog{height:3px;background:var(--bdr);position:relative;overflow:hidden}
.cc-prog-f{position:absolute;left:0;top:0;height:100%;background:linear-gradient(90deg,var(--B),var(--Bxl));border-radius:2px;transition:width .5s cubic-bezier(.4,0,.2,1)}

/* ── LAYOUT ── */
.cc-layout{max-width:1160px;margin:0 auto;padding:0 48px 64px;margin-top:-46px;position:relative;display:grid;grid-template-columns:1fr 290px;gap:22px;align-items:start}

/* ── PANELS ── */
.cc-panel{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--rad);box-shadow:var(--shmd);overflow:hidden;display:none;animation:panelIn .28s ease both}
.cc-panel.show{display:block}
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
.ft{resize:vertical;min-height:96px;line-height:1.6}
.fs{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 13px center;padding-right:38px;cursor:pointer}
.fi-w{position:relative;display:flex;align-items:center}
.fi-w i{position:absolute;left:13px;font-size:13px;color:var(--mul);pointer-events:none;transition:color .2s}
.fi-w .fi{padding-left:38px}
.fi-w:focus-within i{color:var(--B)}
.fi-pfx{position:relative;display:flex;align-items:center}
.fi-pfx-s{position:absolute;left:13px;font-size:14px;color:var(--mu);pointer-events:none}
.fi-pfx .fi{padding-left:22px}
.fi-sfx{position:absolute;right:13px;font-size:11px;font-weight:700;color:var(--mul);pointer-events:none}
.ferr{display:flex;align-items:center;gap:5px;font-size:11.5px;color:var(--R);margin-top:4px;font-weight:500;animation:fadeInUp 0.3s ease-out}
.ferr i{font-size:10px}
.fhint{font-size:11px;color:var(--mu);margin-top:4px;display:flex;align-items:flex-start;gap:5px}
.fhint i{font-size:10px;margin-top:2px;flex-shrink:0}

/* ── TOGGLES ── */
.cc-tgl{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 16px;border:1.5px solid var(--bdr);border-radius:var(--rads);background:var(--bg);cursor:pointer;transition:all .2s}
.cc-tgl:hover{border-color:var(--bdrf);background:var(--sur)}
.cc-tgl.on{border-color:var(--B100);background:var(--B50)}
.cc-tgl-l{font-size:13.5px;font-weight:700;color:var(--ink3)}
.cc-tgl.on .cc-tgl-l{color:var(--B)}
.cc-tgl-d{font-size:11.5px;color:var(--mul);margin-top:2px}
.cc-sw{position:relative;width:42px;height:23px;flex-shrink:0}
.cc-sw input{opacity:0;width:0;height:0;position:absolute}
.cc-sw-t{position:absolute;inset:0;background:var(--bdrf);border-radius:12px;cursor:pointer;transition:background .2s}
.cc-sw input:checked~.cc-sw-t{background:var(--B)}
.cc-sw-t::after{content:'';position:absolute;top:3px;left:3px;width:17px;height:17px;background:#fff;border-radius:50%;box-shadow:0 1px 4px rgba(0,0,0,.2);transition:transform .2s cubic-bezier(.34,1.56,.64,1)}
.cc-sw input:checked~.cc-sw-t::after{transform:translateX(19px)}

/* ── VERIFICATION LEVELS ── */
.vl-g{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
.vl-r{display:none}
.vl-l{display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;border:1.5px solid var(--bdr);border-radius:var(--rads);cursor:pointer;transition:all .18s;text-align:center;font-size:11px;font-weight:700;color:var(--mu);background:var(--bg)}
.vl-l i{font-size:20px;color:var(--mul);transition:color .18s}
.vl-r:checked+.vl-l{border-color:var(--B);background:var(--B50);color:var(--B)}
.vl-r:checked+.vl-l i{color:var(--B)}

/* ── PLAN CARDS ── */
.pl-g{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.pl-r{display:none}
.pl-l{display:flex;flex-direction:column;gap:10px;padding:16px;border:1.5px solid var(--bdr);border-radius:var(--rads);cursor:pointer;transition:all .18s;background:var(--bg);position:relative;overflow:hidden}
.pl-l::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--bdr);transition:background .18s}
.pl-name{font-size:13px;font-weight:800;color:var(--ink3)}
.pl-price{font-family:'Bricolage Grotesque',sans-serif;font-size:22px;font-weight:800;color:var(--ink);line-height:1}
.pl-price small{font-family:'DM Sans',sans-serif;font-size:11px;color:var(--mu);font-weight:400}
.pl-feats{display:flex;flex-direction:column;gap:4px}
.pl-feat{display:flex;align-items:center;gap:6px;font-size:11.5px;color:var(--mu)}
.pl-feat i{font-size:9px}
.pl-badge{position:absolute;top:9px;right:9px;font-size:9px;font-weight:800;letter-spacing:.06em;padding:2px 8px;border-radius:20px;text-transform:uppercase}
.pl-r:checked+.pl-l{border-color:var(--B);background:var(--B50);box-shadow:0 4px 16px rgba(37,99,235,.15)}
.pl-r:checked+.pl-l::before{background:var(--B)}
.pl-r:checked+.pl-l .pl-name{color:var(--B)}
.pb .pl-l::before{background:var(--mul)}
.pp .pl-badge{background:var(--B100);color:var(--Bd)}
.pe .pl-l::before{background:var(--V)}
.pe .pl-badge{background:var(--Vbg);color:var(--Vtx)}

/* ── STARS ── */
.star-row{display:flex;align-items:center;gap:4px}
.star-b{font-size:24px;color:var(--bdr);background:none;border:none;cursor:pointer;line-height:1;transition:color .15s;padding:0}
.star-b.lit{color:var(--A)}
.star-v{font-size:14px;font-weight:800;color:var(--A);margin-left:6px}

/* ── UPLOAD ZONES ── */
.upz{border:2px dashed var(--bdrf);border-radius:var(--rads);padding:26px;text-align:center;cursor:pointer;transition:all .2s;background:var(--bg);position:relative;overflow:hidden}
.upz input{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%}
.upz:hover,.upz.dov{border-color:var(--B);background:var(--B50)}
.upz-ic{width:46px;height:46px;border-radius:12px;background:var(--B50);border:1.5px solid var(--B100);display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--B);margin:0 auto 10px}
.upz-t{font-size:13.5px;font-weight:700;color:var(--ink3);margin-bottom:3px}
.upz-s{font-size:11.5px;color:var(--mul);line-height:1.5}
.upz-prev{display:none;width:72px;height:72px;border-radius:10px;object-fit:cover;border:2px solid var(--B100);margin:12px auto 0}

/* ── API ROW ── */
.api-row{display:flex;gap:8px}
.api-row .fi{font-family:'Courier New',monospace;font-size:12.5px;letter-spacing:.04em;flex:1}
.api-gen{padding:0 16px;background:var(--B);color:#fff;border:none;border-radius:var(--rads);font-family:'DM Sans',sans-serif;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;transition:background .15s;flex-shrink:0}
.api-gen:hover{background:var(--Bd)}

/* ── NAV ── */
.cc-nav{display:flex;align-items:center;justify-content:space-between;padding:18px 26px;border-top:1px solid var(--bdr);background:var(--bg);gap:12px}
.nav-pill{font-size:11.5px;font-weight:700;color:var(--mu);background:var(--sur);border:1px solid var(--bdr);border-radius:20px;padding:4px 12px}
.nav-prev{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border:1.5px solid var(--bdr);border-radius:var(--rads);font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;color:var(--ink3);cursor:pointer;background:var(--sur);transition:all .18s;animation:fadeInUp 0.4s ease-out}
.nav-prev:hover{border-color:var(--B);color:var(--B);transform:translateY(-2px)}
.nav-next{display:inline-flex;align-items:center;gap:8px;padding:10px 26px;background:var(--B);color:#fff;border:none;border-radius:var(--rads);font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;cursor:pointer;box-shadow:var(--shblue);transition:all .2s;animation:fadeInUp 0.4s ease-out}
.nav-next:hover{background:var(--Bd);transform:translateY(-2px);box-shadow:0 12px 32px rgba(37,99,235,.4)}
.nav-sub{display:inline-flex;align-items:center;gap:10px;padding:12px 32px;background:linear-gradient(135deg,var(--Bd),var(--B),var(--Bl));color:#fff;border:none;border-radius:var(--rads);font-family:'DM Sans',sans-serif;font-size:14px;font-weight:800;cursor:pointer;box-shadow:var(--shblue);transition:all .2s;animation:fadeInUp 0.4s ease-out}
.nav-sub:hover{transform:translateY(-3px);box-shadow:0 20px 48px rgba(37,99,235,.5)}

/* ── SIDEBAR ── */
.cc-sb{display:flex;flex-direction:column;gap:16px}
.cc-sbc{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--rad);box-shadow:var(--shsm);overflow:hidden;animation:slideInRight 0.6s ease-out}
.cc-sbh{padding:13px 17px;border-bottom:1px solid var(--bdr);font-size:10.5px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:var(--mu);background:var(--bg);display:flex;align-items:center;gap:7px}
.cc-sbh i{color:var(--B);font-size:11px}
.cc-sbb{padding:16px 17px}

/* preview */
.pv-logo{width:56px;height:56px;border-radius:13px;background:var(--B50);border:1.5px solid var(--B100);display:flex;align-items:center;justify-content:center;font-family:'Bricolage Grotesque',sans-serif;font-size:19px;font-weight:800;color:var(--B);margin-bottom:12px;overflow:hidden;position:relative;animation:pulse 3s infinite}
.pv-logo img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:none}
.pv-name{font-family:'Bricolage Grotesque',sans-serif;font-size:17px;font-weight:800;color:var(--ink);margin-bottom:2px;word-break:break-word;animation:fadeInUp 0.4s ease-out 0.2s both}
.pv-type{font-size:11.5px;color:var(--mu);margin-bottom:10px;animation:fadeInUp 0.4s ease-out 0.3s both}
.pv-st{display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;padding:3px 10px;border-radius:20px;animation:fadeInUp 0.4s ease-out 0.4s both}
.pv-st.active{background:var(--Gbg);color:var(--Gtx)}.pv-st.pending{background:var(--Abg);color:var(--Atx)}
.pv-st.suspended{background:var(--Rbg);color:var(--Rtx)}.pv-st.rejected{background:#f3f4f6;color:#6b7280}
.pv-flds{margin-top:14px;display:flex;flex-direction:column;gap:7px;animation:fadeInUp 0.4s ease-out 0.5s both}
.pv-row{display:flex;align-items:center;gap:8px;font-size:12px;color:var(--mu);animation:fadeInUp 0.4s ease-out 0.6s both}
.pv-row i{width:14px;text-align:center;font-size:10px;color:var(--mul);flex-shrink:0}
.pv-row span{color:var(--ink3);font-weight:500}

/* checklist */
.ck-list{display:flex;flex-direction:column;gap:5px}
.ck-item{display:flex;align-items:center;gap:9px;font-size:12px;color:var(--mul);padding:6px 0;border-bottom:1px solid var(--bdr);transition:all .2s;animation:fadeInUp 0.4s ease-out}
.ck-item:last-child{border:none;padding-bottom:0}
.ck-item.done{color:var(--Gtx);transform:scale(1.02)}
.ck-ic{width:20px;height:20px;border-radius:50%;border:1.5px solid var(--bdr);display:flex;align-items:center;justify-content:center;font-size:9px;color:var(--mul);flex-shrink:0;background:var(--bg);transition:all .2s}
.ck-item.done .ck-ic{background:var(--Gbg);border-color:var(--G);color:var(--G);transform:scale(1.1)}

/* alerts */
.cc-alert{display:flex;align-items:flex-start;gap:10px;padding:13px 17px;border-radius:var(--rads);font-size:13px;margin-bottom:18px;animation:slideInLeft 0.4s ease-out}
.cc-alert.suc{background:var(--Gbg);border:1px solid #a7f3d0;color:var(--Gtx)}
.cc-alert.err{background:var(--Rbg);border:1px solid #fca5a5;color:var(--Rtx)}
.cc-alert i{font-size:15px;flex-shrink:0;margin-top:1px}
.cc-alert ul{list-style:disc inside;margin-top:5px;font-size:12px}
.tip-item{display:flex;gap:9px;font-size:12px;color:var(--mu);line-height:1.55;animation:fadeInUp 0.4s ease-out 0.2s both}
.tip-item i{color:var(--B);margin-top:2px;flex-shrink:0}

@media(max-width:940px){
  .cc-layout{grid-template-columns:1fr;padding:0 18px 48px}.cc-sb{display:none}
  .cc-hero-in{padding:36px 20px 84px;flex-direction:column}.cc-rail{padding:0 16px}
  .fg2{grid-template-columns:1fr}.pl-g{grid-template-columns:1fr}.vl-g{grid-template-columns:repeat(2,1fr)}
}
</style>

<div class="cc">

{{-- HERO --}}
<div class="cc-hero">
  <div class="cc-glow1"></div><div class="cc-glow2"></div>
  <div class="cc-hero-in">
    <div>
      <div class="cc-bc">
        <a href="/"><i class="fas fa-home"></i></a><i class="fas fa-chevron-right"></i>
        <a href="{{ route('admin.dashboard') }}">Admin</a><i class="fas fa-chevron-right"></i>
        <a href="{{ route('admin.companies.index') }}">Companies</a><i class="fas fa-chevron-right"></i>
        <span>Create New</span>
      </div>
      <h1 class="cc-h1">Add New<br><em>Company</em></h1>
      <p class="cc-sub">Register a real estate company in 5 simple steps — from basic info to media and API settings.</p>
    </div>
    <div class="cc-hero-r">
      <a href="{{ route('admin.companies.index') }}" class="cc-back"><i class="fas fa-arrow-left"></i> Back to Companies</a>
      <div class="cc-hsteps">
        @foreach([['1','fa-building','Basic Info'],['2','fa-chart-line','Business'],['3','fa-map-marker-alt','Address'],['4','fa-shield-check','Verification'],['5','fa-photo-film','Media & API']] as [$n,$ic,$l])
        <div class="cc-hs {{ $n==='1'?'cur':'' }}" id="hs{{$n}}">
          <div class="cc-hs-n" id="hsn{{$n}}"><i class="fas {{$ic}}" style="font-size:9px"></i></div>
          {{$l}}
        </div>
        @endforeach
      </div>
    </div>
  </div>
  <svg class="cc-wave" viewBox="0 0 1440 72" preserveAspectRatio="none" fill="#f8fafc">
    <path d="M0,72 C480,0 960,72 1440,0 L1440,72 L0,72Z"/>
  </svg>
</div>

{{-- RAIL --}}
<div class="cc-rail-w">
  <div class="cc-rail">
    @foreach([['1','Basic Info','Name & contact'],['2','Business','Reg & plan'],['3','Address','Location'],['4','Verification','Status & rating'],['5','Media & API','Logo & keys']] as [$n,$l,$d])
    <div class="cc-rt {{ $n==='1'?'cur':'' }}" id="rt{{$n}}" onclick="goStep({{$n}})">
      <div class="cc-rt-n" id="rtn{{$n}}">{{$n}}</div>
      <div><div class="cc-rt-l">{{$l}}</div><div class="cc-rt-d">{{$d}}</div></div>
    </div>
    @endforeach
  </div>
  <div class="cc-prog"><div class="cc-prog-f" id="pf" style="width:20%"></div></div>
</div>

{{-- FORM --}}
<form action="{{ route('admin.companies.store') }}" method="POST" enctype="multipart/form-data" id="ccForm" novalidate>
@csrf
<div class="cc-layout">
<div>

  {{-- alerts --}}
  @if(session('success'))<div class="cc-alert suc"><i class="fas fa-circle-check"></i><div>{{ session('success') }}</div></div>@endif
  @if($errors->any())<div class="cc-alert err"><i class="fas fa-circle-exclamation"></i><div><strong>Please fix these errors:</strong><ul>@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div></div>@endif

  {{-- ═══ STEP 1 ═══ --}}
  <div class="cc-panel show" id="step1">
    <div class="cc-ph"><div class="cc-ph-ic"><i class="fas fa-building"></i></div><div><div class="cc-ph-t">Basic Information</div><div class="cc-ph-s">Name, contact details and description</div></div></div>
    <div class="cc-pb"><div class="fg fg2">

      <div class="fspan">
        <div class="fl">Company Name <span class="fl-r">*</span></div>
        <div class="fi-w"><i class="fas fa-building"></i>
          <input type="text" name="name" id="fn" value="{{ old('name') }}" class="fi {{ $errors->has('name')?'err':'' }}" placeholder="e.g. Al Madar Real Estate Group" required oninput="lu()">
        </div>
        @error('name')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div>
        <div class="fl">Email <span class="fl-o">Optional</span></div>
        <div class="fi-w"><i class="fas fa-envelope"></i>
          <input type="email" name="email" id="fe" value="{{ old('email') }}" class="fi" placeholder="company@example.com" oninput="lu()">
        </div>
        @error('email')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div>
        <div class="fl">Phone <span class="fl-o">Optional</span></div>
        <div class="fi-w"><i class="fas fa-phone"></i>
          <input type="tel" name="phone" id="fph" value="{{ old('phone') }}" class="fi" placeholder="+20 100 000 0000" oninput="lu()">
        </div>
        @error('phone')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div>
        <div class="fl">Website <span class="fl-o">Optional</span></div>
        <div class="fi-w"><i class="fas fa-globe"></i>
          <input type="url" name="website" value="{{ old('website') }}" class="fi" placeholder="https://example.com">
        </div>
        @error('website')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div>
        <div class="fl">Company Type <span class="fl-r">*</span></div>
        <select name="type" id="ft" class="fs {{ $errors->has('type')?'err':'' }}" required onchange="lu()">
          <option value="">— Select type —</option>
          <option value="developer" {{ old('type')=='developer'?'selected':'' }}>🏗️ Developer</option>
          <option value="brokerage" {{ old('type')=='brokerage'?'selected':'' }}>🤝 Brokerage</option>
          <option value="property_management" {{ old('type')=='property_management'?'selected':'' }}>🏢 Property Management</option>
          <option value="construction" {{ old('type')=='construction'?'selected':'' }}>🔨 Construction</option>
          <option value="investment" {{ old('type')=='investment'?'selected':'' }}>📈 Investment</option>
          <option value="consulting" {{ old('type')=='consulting'?'selected':'' }}>💼 Consulting</option>
        </select>
        @error('type')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div>
        <div class="fl">Status <span class="fl-r">*</span></div>
        <select name="status" id="fs" class="fs {{ $errors->has('status')?'err':'' }}" required onchange="lu()">
          <option value="pending" {{ old('status')=='pending'?'selected':'' }}>⏳ Pending</option>
          <option value="active" {{ old('status')=='active'?'selected':'' }}>✅ Active</option>
          <option value="suspended" {{ old('status')=='suspended'?'selected':'' }}>🚫 Suspended</option>
          <option value="rejected" {{ old('status')=='rejected'?'selected':'' }}>❌ Rejected</option>
        </select>
        @error('status')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div class="fspan">
        <div class="fl">Description <span class="fl-o">Optional</span></div>
        <textarea name="description" class="ft" placeholder="Brief company overview, specialization and highlights…">{{ old('description') }}</textarea>
        @error('description')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
        <div class="fhint"><i class="fas fa-circle-info"></i>Shown on the public company profile. Max 500 characters.</div>
      </div>

    </div></div>
    <div class="cc-nav"><span class="nav-pill">Step 1 / 5</span><button type="button" class="nav-next" onclick="goStep(2)">Business Details <i class="fas fa-arrow-right"></i></button></div>
  </div>

  {{-- ═══ STEP 2 ═══ --}}
  <div class="cc-panel" id="step2">
    <div class="cc-ph"><div class="cc-ph-ic"><i class="fas fa-chart-line"></i></div><div><div class="cc-ph-t">Business Details</div><div class="cc-ph-s">Registration, revenue and subscription plan</div></div></div>
    <div class="cc-pb"><div class="fg fg2">

      <div>
        <div class="fl">Registration Number <span class="fl-o">Optional</span></div>
        <div class="fi-w"><i class="fas fa-hashtag"></i><input type="text" name="registration_number" value="{{ old('registration_number') }}" class="fi" placeholder="e.g. CR-123456789"></div>
        @error('registration_number')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div>
        <div class="fl">Tax ID <span class="fl-o">Optional</span></div>
        <div class="fi-w"><i class="fas fa-file-invoice"></i><input type="text" name="tax_id" value="{{ old('tax_id') }}" class="fi" placeholder="e.g. 300-000-000"></div>
        @error('tax_id')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div>
        <div class="fl">Founded Date <span class="fl-o">Optional</span></div>
        <input type="date" name="founded_date" value="{{ old('founded_date') }}" class="fi">
        @error('founded_date')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div>
        <div class="fl">Employee Count <span class="fl-r">*</span></div>
        <div class="fi-w"><i class="fas fa-users"></i><input type="number" name="employee_count" value="{{ old('employee_count') }}" class="fi" placeholder="e.g. 150" min="1" required></div>
        @error('employee_count')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div>
        <div class="fl">Annual Revenue <span class="fl-o">Optional</span></div>
        <div class="fi-pfx" style="position:relative;display:flex;align-items:center">
          <span class="fi-pfx-s">$</span>
          <input type="number" name="annual_revenue" value="{{ old('annual_revenue') }}" step="0.01" class="fi" placeholder="0.00" style="padding-left:22px">
          <span class="fi-sfx">USD</span>
        </div>
        @error('annual_revenue')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div>
        <div class="fl">Subscription Expiry <span class="fl-o">Optional</span></div>
        <input type="datetime-local" name="subscription_expires_at" value="{{ old('subscription_expires_at') }}" class="fi">
        @error('subscription_expires_at')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div class="fspan">
        <div class="fl">Subscription Plan <span class="fl-r">*</span></div>
        <div class="pl-g">
          <label class="pb"><input type="radio" name="subscription_plan" value="basic" class="pl-r" {{ old('subscription_plan','basic')=='basic'?'checked':'' }} onchange="ck('ck-plan',true)">
            <div class="pl-l"><div class="pl-name">Basic</div><div class="pl-price">Free <small>/ mo</small></div>
              <div class="pl-feats"><div class="pl-feat"><i class="fas fa-check" style="color:var(--G)"></i>10 listings</div><div class="pl-feat"><i class="fas fa-check" style="color:var(--G)"></i>Analytics</div><div class="pl-feat"><i class="fas fa-times" style="color:var(--mul)"></i>No API</div></div>
            </div></label>
          <label class="pp"><input type="radio" name="subscription_plan" value="professional" class="pl-r" {{ old('subscription_plan')=='professional'?'checked':'' }} onchange="ck('ck-plan',true)">
            <div class="pl-l"><span class="pl-badge">Popular</span><div class="pl-name">Professional</div><div class="pl-price">$99 <small>/ mo</small></div>
              <div class="pl-feats"><div class="pl-feat"><i class="fas fa-check" style="color:var(--G)"></i>Unlimited listings</div><div class="pl-feat"><i class="fas fa-check" style="color:var(--G)"></i>Advanced analytics</div><div class="pl-feat"><i class="fas fa-check" style="color:var(--G)"></i>API access</div></div>
            </div></label>
          <label class="pe"><input type="radio" name="subscription_plan" value="enterprise" class="pl-r" {{ old('subscription_plan')=='enterprise'?'checked':'' }} onchange="ck('ck-plan',true)">
            <div class="pl-l"><span class="pl-badge">Enterprise</span><div class="pl-name">Enterprise</div><div class="pl-price">$299 <small>/ mo</small></div>
              <div class="pl-feats"><div class="pl-feat"><i class="fas fa-check" style="color:var(--G)"></i>Everything in Pro</div><div class="pl-feat"><i class="fas fa-check" style="color:var(--G)"></i>Dedicated support</div><div class="pl-feat"><i class="fas fa-check" style="color:var(--G)"></i>Custom integrations</div></div>
            </div></label>
        </div>
        @error('subscription_plan')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

    </div></div>
    <div class="cc-nav"><button type="button" class="nav-prev" onclick="goStep(1)"><i class="fas fa-arrow-left"></i> Basic Info</button><span class="nav-pill">Step 2 / 5</span><button type="button" class="nav-next" onclick="goStep(3)">Address <i class="fas fa-arrow-right"></i></button></div>
  </div>

  {{-- ═══ STEP 3 ═══ --}}
  <div class="cc-panel" id="step3">
    <div class="cc-ph"><div class="cc-ph-ic"><i class="fas fa-map-marker-alt"></i></div><div><div class="cc-ph-t">Address Information</div><div class="cc-ph-s">Physical location and postal details</div></div></div>
    <div class="cc-pb"><div class="fg fg2">

      <div class="fspan">
        <div class="fl">Street Address <span class="fl-o">Optional</span></div>
        <div class="fi-w"><i class="fas fa-road"></i><input type="text" name="address" value="{{ old('address') }}" class="fi" placeholder="e.g. 14 Gamal Abd El Nasser St., Floor 3"></div>
        @error('address')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>
      <div><div class="fl">City <span class="fl-o">Optional</span></div><div class="fi-w"><i class="fas fa-city"></i><input type="text" name="city" value="{{ old('city') }}" class="fi" placeholder="e.g. Cairo"></div>@error('city')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror</div>
      <div><div class="fl">State / Province <span class="fl-o">Optional</span></div><div class="fi-w"><i class="fas fa-map"></i><input type="text" name="state" value="{{ old('state') }}" class="fi" placeholder="e.g. Cairo Governorate"></div>@error('state')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror</div>
      <div><div class="fl">Country <span class="fl-o">Optional</span></div><div class="fi-w"><i class="fas fa-flag"></i><input type="text" name="country" value="{{ old('country') ?? 'Saudi Arabia' }}" class="fi"></div>@error('country')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror</div>
      <div><div class="fl">Postal Code <span class="fl-o">Optional</span></div><div class="fi-w"><i class="fas fa-envelope"></i><input type="text" name="postal_code" value="{{ old('postal_code') }}" class="fi" placeholder="e.g. 11511"></div>@error('postal_code')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror</div>

    </div></div>
    <div class="cc-nav"><button type="button" class="nav-prev" onclick="goStep(2)"><i class="fas fa-arrow-left"></i> Business</button><span class="nav-pill">Step 3 / 5</span><button type="button" class="nav-next" onclick="goStep(4)">Verification <i class="fas fa-arrow-right"></i></button></div>
  </div>

  {{-- ═══ STEP 4 ═══ --}}
  <div class="cc-panel" id="step4">
    <div class="cc-ph"><div class="cc-ph-ic"><i class="fas fa-shield-check"></i></div><div><div class="cc-ph-t">Verification & Features</div><div class="cc-ph-s">Trust level, rating and special badges</div></div></div>
    <div class="cc-pb"><div class="fg fg2">

      <div class="fspan">
        <label class="cc-tgl" id="tg1" onclick="tglRow(this,'is_featured')">
          <div><div class="cc-tgl-l"><i class="fas fa-star" style="color:var(--A);margin-right:6px"></i>Featured Company</div><div class="cc-tgl-d">Highlighted in search results and featured sections</div></div>
          <label class="cc-sw"><input type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured')?'checked':'' }} onclick="event.stopPropagation()"><div class="cc-sw-t"></div></label>
        </label>
      </div>

      <div class="fspan">
        <label class="cc-tgl" id="tg2" onclick="tglRow(this,'is_verified')">
          <div><div class="cc-tgl-l"><i class="fas fa-circle-check" style="color:var(--B);margin-right:6px"></i>Verified Company</div><div class="cc-tgl-d">Shows a verification badge on the company profile</div></div>
          <label class="cc-sw"><input type="checkbox" name="is_verified" value="1" id="is_verified" {{ old('is_verified')?'checked':'' }} onclick="event.stopPropagation()"><div class="cc-sw-t"></div></label>
        </label>
      </div>

      <div class="fspan">
        <div class="fl">Verification Level <span class="fl-r">*</span></div>
        <div class="vl-g">
          @foreach([[0,'fa-ban','None'],[1,'fa-shield','Basic'],[2,'fa-shield-halved','Standard'],[3,'fa-shield-check','Premium']] as [$v,$ic,$lb])
          <div><input type="radio" name="verification_level" value="{{ $v }}" id="vl{{$v}}" class="vl-r" {{ old('verification_level','0')==$v?'checked':'' }}><label for="vl{{$v}}" class="vl-l"><i class="fas {{$ic}}"></i>{{$lb}}</label></div>
          @endforeach
        </div>
        @error('verification_level')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div>
        <div class="fl">Initial Rating <span class="fl-r">*</span></div>
        <div class="star-row" id="starRow">
          @for($i=1;$i<=5;$i++)<button type="button" class="star-b" data-v="{{$i}}" onclick="setR({{$i}})">★</button>@endfor
          <span class="star-v" id="sd">0</span>
        </div>
        <input type="hidden" name="rating" id="rh" value="{{ old('rating',0) }}">
        @error('rating')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div>
        <div class="fl">Total Reviews <span class="fl-r">*</span></div>
        <div class="fi-w"><i class="fas fa-comments"></i><input type="number" name="total_reviews" value="{{ old('total_reviews',0) }}" class="fi" min="0" required></div>
        @error('total_reviews')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

    </div></div>
    <div class="cc-nav"><button type="button" class="nav-prev" onclick="goStep(3)"><i class="fas fa-arrow-left"></i> Address</button><span class="nav-pill">Step 4 / 5</span><button type="button" class="nav-next" onclick="goStep(5)">Media & API <i class="fas fa-arrow-right"></i></button></div>
  </div>

  {{-- ═══ STEP 5 ═══ --}}
  <div class="cc-panel" id="step5">
    <div class="cc-ph"><div class="cc-ph-ic"><i class="fas fa-photo-film"></i></div><div><div class="cc-ph-t">Media & API Settings</div><div class="cc-ph-s">Logo, cover image and API configuration</div></div></div>
    <div class="cc-pb"><div class="fg fg2">

      <div>
        <div class="fl">Company Logo <span class="fl-o">Optional</span></div>
        <div class="upz" id="lz" ondragover="dov(event,this)" ondragleave="dlv(this)" ondrop="drp(event,this,'lp','li')">
          <input type="file" name="logo_url" id="li" accept="image/*" onchange="prf(this,'lp','lz','pvi','pvt')">
          <div class="upz-ic"><i class="fas fa-building"></i></div>
          <div class="upz-t">Drop logo here or click to browse</div>
          <div class="upz-s">PNG, JPG, SVG · max 2MB<br>Recommended: 200×200px</div>
          <img id="lp" class="upz-prev">
        </div>
        @error('logo_url')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div>
        <div class="fl">Cover Image <span class="fl-o">Optional</span></div>
        <div class="upz" id="cz" ondragover="dov(event,this)" ondragleave="dlv(this)" ondrop="drp(event,this,'cp','ci')">
          <input type="file" name="cover_image_url" id="ci" accept="image/*" onchange="prf(this,'cp','cz',null,null)">
          <div class="upz-ic"><i class="fas fa-image"></i></div>
          <div class="upz-t">Drop cover image or click to browse</div>
          <div class="upz-s">PNG, JPG · max 5MB<br>Recommended: 1200×400px</div>
          <img id="cp" class="upz-prev">
        </div>
        @error('cover_image_url')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div class="fspan">
        <div class="fl">API Key <span class="fl-o">Optional</span></div>
        <div class="api-row">
          <input type="text" name="api_key" id="ak" value="{{ old('api_key') }}" class="fi" placeholder="Auto-generated if left empty">
          <button type="button" class="api-gen" onclick="genK()"><i class="fas fa-rotate"></i> Generate</button>
        </div>
        <div class="fhint"><i class="fas fa-circle-info"></i>Leave blank to auto-generate a secure key on creation.</div>
        @error('api_key')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

      <div class="fspan">
        <div class="fl">Webhook URL <span class="fl-o">Optional</span></div>
        <div class="fi-w"><i class="fas fa-webhook"></i><input type="url" name="webhook_url" value="{{ old('webhook_url') }}" class="fi" placeholder="https://yourapp.com/webhooks/company"></div>
        <div class="fhint"><i class="fas fa-circle-info"></i>Receive real-time event notifications.</div>
        @error('webhook_url')<div class="ferr"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>@enderror
      </div>

    </div></div>
    <div class="cc-nav"><button type="button" class="nav-prev" onclick="goStep(4)"><i class="fas fa-arrow-left"></i> Verification</button><span class="nav-pill">Step 5 / 5</span><button type="submit" class="nav-sub"><i class="fas fa-building"></i> Create Company</button></div>
  </div>

</div>

{{-- SIDEBAR --}}
<div class="cc-sb">
  <div class="cc-sbc">
    <div class="cc-sbh"><i class="fas fa-eye"></i> Live Preview</div>
    <div class="cc-sbb">
      <div class="pv-logo" id="pvLogo"><img id="pvi" src="" alt=""><span id="pvt">CO</span></div>
      <div class="pv-name" id="pvN">Company Name</div>
      <div class="pv-type" id="pvT">Type not selected</div>
      <span class="pv-st pending" id="pvS">⏳ Pending</span>
      <div class="pv-flds">
        <div class="pv-row"><i class="fas fa-envelope"></i><span id="pvE">—</span></div>
        <div class="pv-row"><i class="fas fa-phone"></i><span id="pvP">—</span></div>
      </div>
    </div>
  </div>

  <div class="cc-sbc">
    <div class="cc-sbh"><i class="fas fa-list-check"></i> Completion</div>
    <div class="cc-sbb">
      <div class="ck-list">
        <div class="ck-item" id="ck-name"><div class="ck-ic"><i class="fas fa-check"></i></div>Company name</div>
        <div class="ck-item" id="ck-type"><div class="ck-ic"><i class="fas fa-check"></i></div>Company type</div>
        <div class="ck-item" id="ck-email"><div class="ck-ic"><i class="fas fa-check"></i></div>Email address</div>
        <div class="ck-item" id="ck-plan"><div class="ck-ic"><i class="fas fa-check"></i></div>Subscription plan</div>
        <div class="ck-item" id="ck-logo"><div class="ck-ic"><i class="fas fa-check"></i></div>Logo uploaded</div>
      </div>
    </div>
  </div>

  <div class="cc-sbc">
    <div class="cc-sbh"><i class="fas fa-lightbulb"></i> Tips</div>
    <div class="cc-sbb" style="display:flex;flex-direction:column;gap:12px">
      <div class="tip-item"><i class="fas fa-star"></i>Use "Featured" for VIP companies to boost their visibility across the platform.</div>
      <div class="tip-item"><i class="fas fa-shield-check"></i>Verified companies receive 3× more inquiries from clients.</div>
      <div class="tip-item"><i class="fas fa-image"></i>A cover image increases profile views by up to 60%.</div>
    </div>
  </div>
</div>

</div>
</form>
</div>

<script>
let cur=1;
const T=5;
function goStep(n){
  document.getElementById('step'+cur).classList.remove('show');
  for(let i=1;i<=T;i++){
    const rt=document.getElementById('rt'+i),rtn=document.getElementById('rtn'+i);
    const hs=document.getElementById('hs'+i),hsn=document.getElementById('hsn'+i);
    rt.classList.remove('cur','done');hs.classList.remove('cur','done');
    if(i<n){rt.classList.add('done');hs.classList.add('done');rtn.innerHTML='<i class="fas fa-check" style="font-size:10px"></i>'}
    else if(i===n){rt.classList.add('cur');hs.classList.add('cur');rtn.textContent=i}
    else rtn.textContent=i;
  }
  cur=n;
  document.getElementById('step'+n).classList.add('show');
  document.getElementById('pf').style.width=(n/T*100)+'%';
  const rw=document.querySelector('.cc-rail-w');
  if(rw)window.scrollTo({top:rw.offsetTop-20,behavior:'smooth'});
}

function lu(){
  const n=(document.getElementById('fn')?.value||'').trim();
  const ty=document.getElementById('ft')?.value||'';
  const st=document.getElementById('fs')?.value||'pending';
  const em=document.getElementById('fe')?.value||'';
  const ph=document.getElementById('fph')?.value||'';
  sv('pvN',n||'Company Name');sv('pvt',n?n.substring(0,2).toUpperCase():'CO');
  sv('pvT',ty?ty.replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase()):'Type not selected');
  sv('pvE',em||'—');sv('pvP',ph||'—');
  const el=document.getElementById('pvS');
  if(el){el.className='pv-st '+(st||'pending');const L={active:'✅ Active',pending:'⏳ Pending',suspended:'🚫 Suspended',rejected:'❌ Rejected'};el.textContent=L[st]||'⏳ Pending'}
  ck('ck-name',n.length>1);ck('ck-type',!!ty);ck('ck-email',em.includes('@'));
}

function sv(id,v){const e=document.getElementById(id);if(e)e.textContent=v}

function ck(id,done){
  const e=document.getElementById(id);if(!e)return;
  e.classList.toggle('done',done);
}

function tglRow(row,cbId){
  const cb=document.getElementById(cbId);if(cb)cb.checked=!cb.checked;
  row.classList.toggle('on',cb&&cb.checked);
}

function setR(v){
  document.getElementById('rh').value=v;document.getElementById('sd').textContent=v+'.0';
  document.querySelectorAll('.star-b').forEach((b,i)=>b.classList.toggle('lit',i<v));
}

function prf(inp,prevId,zoneId,pvImgId,pvTxtId){
  if(!inp.files?.[0])return;
  const r=new FileReader();
  r.onload=e=>{
    const pv=document.getElementById(prevId),zn=document.getElementById(zoneId);
    if(pv){pv.src=e.target.result;pv.style.display='block'}
    if(zn)zn.style.borderColor='var(--B)';
    if(pvImgId){
      const img=document.getElementById(pvImgId),txt=document.getElementById(pvTxtId);
      if(img){img.src=e.target.result;img.style.display='block'}
      if(txt)txt.style.display='none';
    }
    ck('ck-logo',true);
  };
  r.readAsDataURL(inp.files[0]);
}

function dov(e,el){e.preventDefault();el.classList.add('dov')}
function dlv(el){el.classList.remove('dov')}
function drp(e,el,pId,iId){
  e.preventDefault();el.classList.remove('dov');
  const inp=document.getElementById(iId);
  if(inp&&e.dataTransfer?.files?.length){inp.files=e.dataTransfer.files;prf(inp,pId,el.id,'pvi','pvt')}
}

function genK(){
  const c='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let k='rk_live_';for(let i=0;i<32;i++)k+=c[Math.floor(Math.random()*c.length)];
  const inp=document.getElementById('ak');if(inp)inp.value=k;
}

document.addEventListener('DOMContentLoaded',()=>{
  lu();setR(parseInt('{{ old("rating",0) }}')||0);
  ['is_featured','is_verified'].forEach(id=>{
    const cb=document.getElementById(id);
    if(cb?.checked){const r=cb.closest('.cc-tgl');if(r)r.classList.add('on')}
  });
});
</script>
@endsection