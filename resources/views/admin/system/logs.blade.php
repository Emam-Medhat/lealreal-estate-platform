@extends('admin.layouts.admin')

@section('title', 'السجلات')
@section('page-title', 'السجلات')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* ═══════════════════════════════════════════
   SYSTEM LOGS PAGE — Light Theme
   Palette: Blue #0D6EFD · Purple #6F42C1
            Green #198754 · Orange #FD7E14
            Red #DC3545 · Teal #0DCAF0
   Font: Cairo
═══════════════════════════════════════════ */
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
  --blue:    #0D6EFD;  --blue-g:  linear-gradient(135deg,#0D6EFD,#0a58ca);
  --purple:  #6F42C1;  --purple-g:linear-gradient(135deg,#6F42C1,#59359a);
  --green:   #198754;  --orange:  #FD7E14;
  --red:     #DC3545;  --teal:    #0DCAF0;
  --yellow:  #FFC107;  --indigo:  #4F46E5;

  /* log-level colors */
  --lvl-debug:    #6B7280;
  --lvl-info:     #0D6EFD;
  --lvl-warning:  #FD7E14;
  --lvl-error:    #DC3545;
  --lvl-critical: #991B1B;

  /* surfaces */
  --bg:    #F0F4FF;
  --card:  #FFFFFF;
  --card2: #F8FAFF;
  --bd:    #E2E8F0;
  --bd2:   #CBD5E1;

  /* text */
  --t1: #1E293B;
  --t2: #475569;
  --t3: #64748B;
  --t4: #94A3B8;

  --r1:10px; --r2:14px; --r3:18px; --r4:24px;
  --sh:   0 1px 4px rgba(0,0,0,.06),0 2px 10px rgba(0,0,0,.04);
  --sh-md:0 4px 20px rgba(0,0,0,.08),0 2px 8px rgba(0,0,0,.04);
  --sh-lg:0 10px 40px rgba(0,0,0,.1);
  --tr: all .25s cubic-bezier(.4,0,.2,1);
  --tr-sp: all .35s cubic-bezier(.34,1.3,.64,1);
  --ff: 'Cairo', system-ui, sans-serif;
}

body {
  font-family: var(--ff);
  background: var(--bg);
  color: var(--t1);
  direction: rtl;
  min-height: 100vh;
}
body::before {
  content:''; position:fixed; inset:0; pointer-events:none;
  background:
    radial-gradient(ellipse 55% 35% at 85% 5%,rgba(13,110,253,.05) 0%,transparent 55%),
    radial-gradient(ellipse 45% 45% at 5% 90%,rgba(111,66,193,.04) 0%,transparent 55%);
  z-index:0;
}

/* ═══ PAGE HEADER ═══ */
.page-header {
  background: linear-gradient(140deg, #1E3A8A 0%, #1D4ED8 55%, #4F46E5 100%);
  padding: 0 32px;
  position: relative;
  overflow: hidden;
}
.page-header::after {
  content:''; position:absolute;
  bottom:-2px; left:0; right:0; height:48px;
  background: var(--bg);
  clip-path: ellipse(52% 100% at 50% 100%);
}
/* subtle grid overlay */
.page-header::before {
  content:''; position:absolute; inset:0;
  background-image:
    linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
  background-size: 40px 40px;
}

.ph-inner {
  position: relative; z-index:1;
  max-width: 1400px; margin: 0 auto;
  display: flex; align-items: center; justify-content: space-between;
  gap: 20px; flex-wrap: wrap;
  padding: 36px 0 60px;
}
.ph-left {}
.ph-eyebrow {
  font-size: 10px; font-weight:700; letter-spacing:.22em;
  text-transform:uppercase; color:rgba(255,255,255,.6);
  display:flex; align-items:center; gap:8px; margin-bottom:8px;
}
.ph-eyebrow::before {
  content:''; display:inline-block;
  width:18px; height:1.5px;
  background:rgba(255,255,255,.5); border-radius:2px;
}
.ph-title {
  display:flex; align-items:center; gap:14px;
  font-size: clamp(22px,3vw,34px);
  font-weight: 900; color:#fff; line-height:1.2;
}
.ph-title-ic {
  width:50px; height:50px; flex-shrink:0;
  background:rgba(255,255,255,.14);
  border:1.5px solid rgba(255,255,255,.22);
  border-radius: var(--r2);
  display:flex; align-items:center; justify-content:center;
  font-size:20px;
  box-shadow: 0 4px 16px rgba(0,0,0,.2);
}
.ph-sub {
  font-size:13.5px; color:rgba(255,255,255,.7);
  margin-top:8px; margin-right:64px;
}
.ph-actions { display:flex; gap:10px; flex-shrink:0; }

.hbtn {
  display:inline-flex; align-items:center; gap:8px;
  padding:11px 22px; border-radius:var(--r2);
  font-family:var(--ff); font-size:13.5px; font-weight:700;
  cursor:pointer; border:none; transition:var(--tr-sp);
  text-decoration:none; white-space:nowrap;
}
.hbtn-white {
  background:#fff; color:var(--t1);
  box-shadow:0 4px 18px rgba(0,0,0,.18);
}
.hbtn-white:hover { transform:translateY(-3px); box-shadow:0 10px 28px rgba(0,0,0,.22); }
.hbtn-red {
  background:rgba(220,53,69,.88); color:#fff;
  border:1.5px solid rgba(255,255,255,.18);
}
.hbtn-red:hover { background:var(--red); transform:translateY(-3px); box-shadow:0 8px 22px rgba(220,53,69,.35); }

/* ═══ CONTENT WRAPPER ═══ */
.content {
  position:relative; z-index:1;
  max-width:1400px; margin:0 auto;
  padding:24px 32px 60px;
}

/* ═══ STATS ROW ═══ */
.stats-row {
  display:grid;
  grid-template-columns:repeat(5,1fr);
  gap:14px;
  margin-bottom:22px;
}
.sc {
  background:var(--card); border:1px solid var(--bd);
  border-radius:var(--r3); padding:20px 18px;
  box-shadow:var(--sh); transition:var(--tr-sp);
  position:relative; overflow:hidden; cursor:default;
}
.sc:hover { transform:translateY(-5px); box-shadow:var(--sh-md); }
.sc::before {
  content:''; position:absolute;
  top:0; left:0; right:0; height:3px;
  border-radius:var(--r3) var(--r3) 0 0;
  transition:height .3s;
}
.sc:hover::before { height:4px; }
.sc-debug::before   { background:var(--lvl-debug); }
.sc-info::before    { background:var(--lvl-info); }
.sc-warning::before { background:var(--lvl-warning); }
.sc-error::before   { background:var(--lvl-error); }
.sc-critical::before{ background:var(--lvl-critical); }

.sc-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
.sc-label { font-size:10px; font-weight:700; letter-spacing:.18em; text-transform:uppercase; color:var(--t4); }
.sc-icon {
  width:38px; height:38px; border-radius:var(--r1);
  display:flex; align-items:center; justify-content:center;
  font-size:15px; border:1px solid; flex-shrink:0;
  transition:transform .3s;
}
.sc:hover .sc-icon { transform:scale(1.1) rotate(6deg); }
.si-debug   { background:rgba(107,114,128,.1); color:var(--lvl-debug);    border-color:rgba(107,114,128,.22); }
.si-info    { background:rgba(13,110,253,.1);  color:var(--lvl-info);     border-color:rgba(13,110,253,.22); }
.si-warning { background:rgba(253,126,20,.1);  color:var(--lvl-warning);  border-color:rgba(253,126,20,.22); }
.si-error   { background:rgba(220,53,69,.1);   color:var(--lvl-error);    border-color:rgba(220,53,69,.22); }
.si-critical{ background:rgba(153,27,27,.1);   color:var(--lvl-critical); border-color:rgba(153,27,27,.22); }

.sc-val { font-size:clamp(26px,2.5vw,36px); font-weight:900; color:var(--t1); line-height:1; margin-bottom:8px; }
.sc-meta { display:flex; align-items:center; gap:7px; font-size:11px; color:var(--t4); }
.sc-dot {
  width:6px; height:6px; border-radius:50%; flex-shrink:0;
  animation:blink 2.2s ease-in-out infinite;
}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.25}}
.sd-debug   { background:var(--lvl-debug); }
.sd-info    { background:var(--lvl-info);    box-shadow:0 0 0 3px rgba(13,110,253,.15); }
.sd-warning { background:var(--lvl-warning); box-shadow:0 0 0 3px rgba(253,126,20,.15); }
.sd-error   { background:var(--lvl-error);   box-shadow:0 0 0 3px rgba(220,53,69,.15); }
.sd-critical{ background:var(--lvl-critical);box-shadow:0 0 0 3px rgba(153,27,27,.15); }

/* ═══ GENERIC PANEL ═══ */
.panel {
  background:var(--card); border:1px solid var(--bd);
  border-radius:var(--r3); box-shadow:var(--sh);
  overflow:hidden; transition:box-shadow .25s;
}
.panel:hover { box-shadow:var(--sh-md); }

.ph {
  display:flex; align-items:center; justify-content:space-between;
  padding:16px 22px; border-bottom:1px solid var(--bd);
  gap:12px;
}
.ph-l { display:flex; align-items:center; gap:12px; }
.p-ic {
  width:38px; height:38px; border-radius:var(--r1);
  display:flex; align-items:center; justify-content:center;
  font-size:15px; flex-shrink:0;
}
.pic-blue  { background:rgba(13,110,253,.1);  color:var(--blue); }
.pic-purple{ background:rgba(111,66,193,.1);  color:var(--purple); }
.pic-orange{ background:rgba(253,126,20,.1);  color:var(--orange); }
.pic-teal  { background:rgba(13,202,240,.12); color:#0891B2; }
.pic-indigo{ background:rgba(79,70,229,.1);   color:var(--indigo); }
.pic-green { background:rgba(25,135,84,.1);   color:var(--green); }
.pic-red   { background:rgba(220,53,69,.1);   color:var(--red); }

.p-title { font-size:15px; font-weight:800; color:var(--t1); }
.p-sub   { font-size:11px; color:var(--t4); margin-top:2px; }
.pb { padding:20px 22px; }

/* ═══ CHARTS ROW ═══ */
.charts-row {
  display:grid; grid-template-columns:1.55fr 1fr;
  gap:16px; margin-bottom:18px;
}
.chart-box { position:relative; height:260px; }

/* ═══ FILTER PANEL ═══ */
.filter-grid {
  display:grid;
  grid-template-columns:1fr 1fr 1fr 1.6fr;
  gap:14px; align-items:end;
}
.fld label {
  display:block; font-size:11.5px; font-weight:700;
  color:var(--t3); margin-bottom:5px; letter-spacing:.04em;
}
.fld select,
.fld input[type="datetime-local"],
.fld input[type="text"] {
  width:100%; padding:9px 13px;
  border:1.5px solid var(--bd); border-radius:var(--r1);
  font-family:var(--ff); font-size:13px; color:var(--t1);
  background:var(--card2); outline:none;
  transition:border-color .2s, box-shadow .2s;
}
.fld select:focus,
.fld input:focus {
  border-color:var(--blue);
  box-shadow:0 0 0 3px rgba(13,110,253,.1);
}
.fld-row { display:flex; gap:8px; }
.fld-row input { flex:1; }

.btn-apply {
  padding:9px 18px; background:var(--blue-g);
  color:#fff; border:none; border-radius:var(--r1);
  font-family:var(--ff); font-size:13px; font-weight:700;
  cursor:pointer; display:flex; align-items:center; gap:7px;
  white-space:nowrap; transition:var(--tr-sp);
  box-shadow:0 4px 14px rgba(13,110,253,.25);
}
.btn-apply:hover { transform:translateY(-2px); box-shadow:0 8px 22px rgba(13,110,253,.35); }

/* ═══ LOG ENTRIES ═══ */
.log-list { display:flex; flex-direction:column; gap:10px; }

.log-card {
  border-radius:var(--r2); border:1px solid var(--bd);
  background:var(--card); overflow:hidden;
  transition:var(--tr); position:relative;
}
.log-card:hover { box-shadow:var(--sh-md); border-color:var(--bd2); }

/* Left accent bar (RTL = right border) */
.log-card.lc-critical { border-right:4px solid var(--lvl-critical); }
.log-card.lc-error    { border-right:4px solid var(--lvl-error); }
.log-card.lc-warning  { border-right:4px solid var(--lvl-warning); }
.log-card.lc-info     { border-right:4px solid var(--lvl-info); }
.log-card.lc-debug    { border-right:4px solid var(--lvl-debug); }

.log-head {
  display:flex; align-items:flex-start; justify-content:space-between;
  padding:14px 18px; gap:12px; cursor:pointer;
  transition:background .18s;
}
.log-head:hover { background:var(--card2); }
.log-head-l { display:flex; align-items:flex-start; gap:12px; flex:1; min-width:0; }

.log-av {
  width:36px; height:36px; border-radius:10px;
  display:flex; align-items:center; justify-content:center;
  font-size:14px; flex-shrink:0; margin-top:1px;
}
.la-critical { background:rgba(153,27,27,.1);   color:var(--lvl-critical); }
.la-error    { background:rgba(220,53,69,.1);   color:var(--lvl-error); }
.la-warning  { background:rgba(253,126,20,.1);  color:var(--lvl-warning); }
.la-info     { background:rgba(13,110,253,.1);  color:var(--lvl-info); }
.la-debug    { background:rgba(107,114,128,.1); color:var(--lvl-debug); }

.log-title { font-size:13.5px; font-weight:800; color:var(--t1); margin-bottom:5px; }
.log-meta { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }

.log-badge {
  display:inline-flex; align-items:center; gap:4px;
  font-size:9.5px; font-weight:700; letter-spacing:.1em;
  text-transform:uppercase; padding:2px 9px;
  border-radius:20px; border:1px solid;
}
.lb-critical { background:rgba(153,27,27,.1);   color:var(--lvl-critical); border-color:rgba(153,27,27,.22); }
.lb-error    { background:rgba(220,53,69,.1);   color:var(--lvl-error);    border-color:rgba(220,53,69,.22); }
.lb-warning  { background:rgba(253,126,20,.1);  color:var(--lvl-warning);  border-color:rgba(253,126,20,.22); }
.lb-info     { background:rgba(13,110,253,.1);  color:var(--lvl-info);     border-color:rgba(13,110,253,.22); }
.lb-debug    { background:rgba(107,114,128,.1); color:var(--lvl-debug);    border-color:rgba(107,114,128,.22); }

.log-time { font-size:11px; color:var(--t4); display:flex; align-items:center; gap:4px; }
.log-file { font-size:10.5px; color:var(--t4); font-family:'Courier New',monospace; }

.log-toggle {
  width:28px; height:28px; border-radius:8px;
  background:var(--bg); border:1px solid var(--bd);
  display:flex; align-items:center; justify-content:center;
  font-size:10px; color:var(--t3); flex-shrink:0;
  cursor:pointer; transition:var(--tr);
}
.log-toggle:hover { background:rgba(13,110,253,.08); color:var(--blue); border-color:rgba(13,110,253,.2); }
.log-toggle.open  { background:rgba(13,110,253,.08); color:var(--blue); transform:rotate(180deg); }

/* Code block */
.log-code {
  background:#1E293B; color:#CBD5E1;
  padding:16px 20px;
  font-family:'Courier New', monospace;
  font-size:11.5px; line-height:1.75;
  overflow-x:auto; border-top:1px solid rgba(255,255,255,.05);
  display:none;
}
.log-code.show { display:block; }
.c-hl   { background:rgba(96,165,250,.18); color:#93C5FD; padding:1px 5px; border-radius:4px; }
.c-path { color:#86EFAC; }
.c-crit { color:#FCA5A5; }
.c-err  { color:#FCA5A5; }
.c-warn { color:#FCD34D; }
.c-info { color:#93C5FD; }
.c-dbg  { color:#D1D5DB; }

/* ═══ LOG FILES TABLE ═══ */
.tbl { width:100%; border-collapse:collapse; }
.tbl thead th {
  text-align:right; font-size:10.5px; font-weight:700;
  letter-spacing:.12em; text-transform:uppercase;
  color:var(--t4); padding:11px 18px;
  border-bottom:2px solid var(--bd); background:var(--card2);
}
.tbl tbody td {
  padding:14px 18px; border-bottom:1px solid var(--bd);
  font-size:13px; color:var(--t2); vertical-align:middle;
}
.tbl tbody tr:last-child td { border-bottom:none; }
.tbl tbody tr:hover td { background:var(--card2); }

.tbl-file { display:flex; align-items:center; gap:12px; }
.tbl-fic {
  width:36px; height:36px; border-radius:10px;
  display:flex; align-items:center; justify-content:center;
  font-size:14px; flex-shrink:0;
}
.tfi-gray  { background:rgba(107,114,128,.1); color:var(--lvl-debug); }
.tfi-red   { background:rgba(220,53,69,.1);   color:var(--red); }
.tfi-blue  { background:rgba(13,110,253,.1);  color:var(--blue); }
.tfi-green { background:rgba(25,135,84,.1);   color:var(--green); }

.tbl-fname { font-size:13px; font-weight:700; color:var(--t1); }
.tbl-fdesc { font-size:11px; color:var(--t4); margin-top:1px; }

.pill-size {
  display:inline-flex; align-items:center;
  padding:3px 11px; background:var(--bg);
  border:1px solid var(--bd); border-radius:20px;
  font-size:11.5px; font-weight:600; color:var(--t3);
}
.pill-lines {
  display:inline-flex; align-items:center;
  padding:3px 11px;
  background:rgba(13,110,253,.06); border:1px solid rgba(13,110,253,.15);
  border-radius:20px; font-size:11.5px; font-weight:600; color:var(--blue);
}

.btn-view {
  display:inline-flex; align-items:center; gap:6px;
  padding:6px 14px; border-radius:8px;
  background:rgba(79,70,229,.08); color:var(--indigo);
  border:1px solid rgba(79,70,229,.18);
  font-family:var(--ff); font-size:12px; font-weight:700;
  cursor:pointer; transition:var(--tr);
}
.btn-view:hover { background:rgba(79,70,229,.15); transform:translateY(-1px); }

.btn-dl {
  display:inline-flex; align-items:center; gap:7px;
  padding:8px 18px; border-radius:var(--r1);
  background:rgba(13,202,240,.1); color:#0891B2;
  border:1px solid rgba(13,202,240,.25);
  font-family:var(--ff); font-size:12.5px; font-weight:700;
  cursor:pointer; transition:var(--tr);
}
.btn-dl:hover { background:rgba(13,202,240,.2); transform:translateY(-1px); }

/* ═══ LIVE BADGE ═══ */
.live-badge {
  display:inline-flex; align-items:center; gap:7px;
  padding:4px 12px; border-radius:20px; font-size:10px;
  font-weight:700; letter-spacing:.08em; text-transform:uppercase;
  background:rgba(25,135,84,.08); border:1px solid rgba(25,135,84,.2);
  color:var(--green);
}
.live-dot {
  width:6px; height:6px; border-radius:50%; background:var(--green);
  animation:liveping 2s ease-in-out infinite;
}
@keyframes liveping{0%,100%{box-shadow:0 0 0 0 rgba(25,135,84,.4)}60%{box-shadow:0 0 0 6px rgba(25,135,84,0)}}

/* ═══ NOTIFICATION ═══ */
.notif {
  position:fixed; top:20px; left:20px; z-index:9999;
  display:flex; align-items:center; gap:10px;
  padding:12px 20px; border-radius:var(--r2);
  font-family:var(--ff); font-size:13.5px; font-weight:700;
  box-shadow:0 8px 32px rgba(0,0,0,.16);
  transition:transform .4s cubic-bezier(.34,1.2,.64,1), opacity .3s;
  transform:translateX(-130%); opacity:0;
  border:1px solid;
}
.notif.show { transform:translateX(0); opacity:1; }
.notif.success{ background:var(--card); border-color:rgba(25,135,84,.3);   color:var(--green); }
.notif.info   { background:var(--card); border-color:rgba(13,110,253,.3);  color:var(--blue); }
.notif.error  { background:var(--card); border-color:rgba(220,53,69,.3);   color:var(--red); }

/* ═══ SPACING UTILS ═══ */
.mb-18 { margin-bottom:18px; }
.mb-22 { margin-bottom:22px; }

/* ═══ ANIMATIONS ═══ */
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.anim{opacity:0;animation:fadeUp .5s ease forwards;}
.a1{animation-delay:.05s}.a2{animation-delay:.1s}.a3{animation-delay:.15s}
.a4{animation-delay:.2s}.a5{animation-delay:.25s}.a6{animation-delay:.3s}

/* ═══ SCROLLBAR ═══ */
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-track{background:var(--bg)}
::-webkit-scrollbar-thumb{background:var(--bd2);border-radius:10px}
::-webkit-scrollbar-thumb:hover{background:var(--blue)}

/* ═══ RESPONSIVE ═══ */
@media(max-width:1200px){
  .stats-row{grid-template-columns:repeat(3,1fr)}
  .charts-row{grid-template-columns:1fr}
  .filter-grid{grid-template-columns:1fr 1fr}
}
@media(max-width:900px){
  .stats-row{grid-template-columns:repeat(2,1fr)}
  .filter-grid{grid-template-columns:1fr}
  .content{padding:18px 16px 40px}
  .ph-inner{padding:28px 0 52px}
  .page-header{padding:0 16px}
}
@media(max-width:600px){
  .stats-row{grid-template-columns:1fr 1fr}
  .ph-actions{flex-wrap:wrap;gap:8px}
}

/* ═══ LOG MODAL ═══ */
.log-modal {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center;
  opacity: 0; pointer-events: none; transition: opacity .3s;
}
.log-modal.show { opacity: 1; pointer-events: auto; }
.lm-content {
  width: 90%; max-width: 1000px; height: 85vh;
  background: #1E293B; border-radius: 16px;
  box-shadow: 0 20px 50px rgba(0,0,0,0.3);
  display: flex; flex-direction: column; overflow: hidden;
  transform: translateY(20px); transition: transform .3s;
}
.log-modal.show .lm-content { transform: translateY(0); }
.lm-header {
  padding: 16px 24px; background: rgba(255,255,255,0.05);
  border-bottom: 1px solid rgba(255,255,255,0.1);
  display: flex; align-items: center; justify-content: space-between;
}
.lm-title { color: #fff; font-weight: 700; display: flex; align-items: center; gap: 10px; }
.lm-close {
  background: transparent; border: none; color: rgba(255,255,255,0.6);
  font-size: 18px; cursor: pointer; transition: color .2s;
}
.lm-close:hover { color: #fff; }
.lm-body { flex: 1; overflow: auto; padding: 0; }
.lm-code {
  margin: 0; padding: 20px; color: #CBD5E1; font-family: 'Courier New', monospace;
  font-size: 13px; line-height: 1.6; white-space: pre-wrap;
}
</style>
@endpush

@section('content')

<!-- NOTIFICATION -->
<div class="notif" id="notif">
  <i class="fas fa-check-circle"></i>
  <span id="notif-msg">رسالة</span>
</div>

<!-- ═══ PAGE HEADER ═══ -->
<div class="page-header">
  <div class="ph-inner">
    <div class="ph-left">
      <div class="ph-eyebrow">النظام / System</div>
      <h1 class="ph-title">
        <div class="ph-title-ic"><i class="fas fa-file-alt"></i></div>
        السجلات
      </h1>
      <p class="ph-sub">مراقبة وتحليل سجلات النظام في الوقت الحقيقي</p>
    </div>
    <div class="ph-actions">
      <button class="hbtn hbtn-white" id="refresh-btn" onclick="refreshLogs()">
        <i class="fas fa-sync-alt"></i> تحديث
      </button>
      <button class="hbtn hbtn-red" id="clear-btn" onclick="clearLogs()">
        <i class="fas fa-trash"></i> مسح الكل
      </button>
    </div>
  </div>
</div>

<!-- ═══ CONTENT ═══ -->
<div class="content">

  <!-- STAT CARDS -->
  <div class="stats-row mb-22">

    <div class="sc sc-debug anim a1">
      <div class="sc-top">
        <div class="sc-label">Debug</div>
        <div class="sc-icon si-debug"><i class="fas fa-bug"></i></div>
      </div>
      <div class="sc-val">234</div>
      <div class="sc-meta"><span class="sc-dot sd-debug"></span>غير حرج</div>
    </div>

    <div class="sc sc-info anim a2">
      <div class="sc-top">
        <div class="sc-label">Info</div>
        <div class="sc-icon si-info"><i class="fas fa-info-circle"></i></div>
      </div>
      <div class="sc-val">1,456</div>
      <div class="sc-meta"><span class="sc-dot sd-info"></span>معلوماتي</div>
    </div>

    <div class="sc sc-warning anim a3">
      <div class="sc-top">
        <div class="sc-label">Warning</div>
        <div class="sc-icon si-warning"><i class="fas fa-exclamation-triangle"></i></div>
      </div>
      <div class="sc-val">89</div>
      <div class="sc-meta"><span class="sc-dot sd-warning"></span>تحذير</div>
    </div>

    <div class="sc sc-error anim a4">
      <div class="sc-top">
        <div class="sc-label">Error</div>
        <div class="sc-icon si-error"><i class="fas fa-times-circle"></i></div>
      </div>
      <div class="sc-val">23</div>
      <div class="sc-meta"><span class="sc-dot sd-error"></span>خطأ</div>
    </div>

    <div class="sc sc-critical anim a5">
      <div class="sc-top">
        <div class="sc-label">Critical</div>
        <div class="sc-icon si-critical"><i class="fas fa-skull-crossbones"></i></div>
      </div>
      <div class="sc-val">3</div>
      <div class="sc-meta"><span class="sc-dot sd-critical"></span>حرج جداً</div>
    </div>

  </div>

  <!-- CHARTS ROW -->
  <div class="charts-row mb-18 anim a3">

    <div class="panel">
      <div class="ph">
        <div class="ph-l">
          <div class="p-ic pic-blue"><i class="fas fa-chart-line"></i></div>
          <div>
            <div class="p-title">اتجاه السجلات</div>
            <div class="p-sub">آخر 24 ساعة — يتحدث تلقائياً</div>
          </div>
        </div>
        <div class="live-badge"><span class="live-dot"></span>Live</div>
      </div>
      <div class="pb"><div class="chart-box"><canvas id="trendChart"></canvas></div></div>
    </div>

    <div class="panel">
      <div class="ph">
        <div class="ph-l">
          <div class="p-ic pic-purple"><i class="fas fa-chart-pie"></i></div>
          <div>
            <div class="p-title">توزيع المستويات</div>
            <div class="p-sub">نسبة كل مستوى</div>
          </div>
        </div>
      </div>
      <div class="pb"><div class="chart-box"><canvas id="pieChart"></canvas></div></div>
    </div>

  </div>

  <!-- FILTER PANEL -->
  <div class="panel mb-18 anim a4">
    <div class="ph">
      <div class="ph-l">
        <div class="p-ic pic-orange"><i class="fas fa-filter"></i></div>
        <div>
          <div class="p-title">فلترة السجلات</div>
          <div class="p-sub">تصفية وبحث متقدم</div>
        </div>
      </div>
    </div>
    <div class="pb">
      <div class="filter-grid">
        <div class="fld">
          <label>مستوى السجل</label>
          <select id="level-filter">
            <option value="">الكل</option>
            <option value="debug">Debug</option>
            <option value="info">Info</option>
            <option value="warning">Warning</option>
            <option value="error">Error</option>
            <option value="critical">Critical</option>
          </select>
        </div>
        <div class="fld">
          <label>من التاريخ</label>
          <input type="datetime-local" id="date-from">
        </div>
        <div class="fld">
          <label>إلى التاريخ</label>
          <input type="datetime-local" id="date-to">
        </div>
        <div class="fld">
          <label>بحث في السجلات</label>
          <div class="fld-row">
            <input type="text" id="search-input" placeholder="اكتب كلمة للبحث...">
            <button class="btn-apply" onclick="applyFilters()">
              <i class="fas fa-search"></i> تطبيق
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- LOG ENTRIES -->
  <div class="panel mb-18 anim a5">
    <div class="ph">
      <div class="ph-l">
        <div class="p-ic pic-teal"><i class="fas fa-list-ul"></i></div>
        <div>
          <div class="p-title">السجلات الحديثة</div>
          <div class="p-sub">آخر الأحداث المسجلة على النظام</div>
        </div>
      </div>
      <button class="btn-dl" onclick="downloadLogs()">
        <i class="fas fa-download"></i> تحميل
      </button>
    </div>
    <div class="pb">
      <div class="log-list" id="logs-container">

        <!-- CRITICAL -->
        <div class="log-card lc-critical">
          <div class="log-head" onclick="toggleLog(this)">
            <div class="log-head-l">
              <div class="log-av la-critical"><i class="fas fa-skull-crossbones"></i></div>
              <div style="flex:1;min-width:0">
                <div class="log-title">Database Connection Failed</div>
                <div class="log-meta">
                  <span class="log-badge lb-critical">Critical</span>
                  <span class="log-time"><i class="far fa-clock"></i>2024-01-15 14:23:45</span>
                  <span class="log-file">App/Models/User.php:123</span>
                </div>
              </div>
            </div>
            <div class="log-toggle"><i class="fas fa-chevron-down"></i></div>
          </div>
          <div class="log-code">
[2024-01-15 14:23:45] <span class="c-crit">CRITICAL</span>: Database connection failed
SQLSTATE[HY000] [2002] Connection refused
in <span class="c-path">App/Models/User.php</span> line <span class="c-hl">123</span>

Stack trace:
#0 App/Models/User.php(123): Illuminate\Database\Connection->connect()
#1 App/Http/Controllers/UserController.php(45): App\Models\User::find()
#2 {main}</div>
        </div>

        <!-- ERROR -->
        <div class="log-card lc-error">
          <div class="log-head" onclick="toggleLog(this)">
            <div class="log-head-l">
              <div class="log-av la-error"><i class="fas fa-times-circle"></i></div>
              <div style="flex:1;min-width:0">
                <div class="log-title">File Not Found</div>
                <div class="log-meta">
                  <span class="log-badge lb-error">Error</span>
                  <span class="log-time"><i class="far fa-clock"></i>2024-01-15 14:22:30</span>
                  <span class="log-file">App/Http/Controllers/FileController.php:67</span>
                </div>
              </div>
            </div>
            <div class="log-toggle"><i class="fas fa-chevron-down"></i></div>
          </div>
          <div class="log-code">
[2024-01-15 14:22:30] <span class="c-err">ERROR</span>: File not found at path
/var/www/storage/uploads/<span class="c-hl">document.pdf</span>
in <span class="c-path">App/Http/Controllers/FileController.php</span> line <span class="c-hl">67</span>

Exception: FileNotFoundException
  at /var/www/html/app/Http/Controllers/FileController.php:67</div>
        </div>

        <!-- WARNING -->
        <div class="log-card lc-warning">
          <div class="log-head" onclick="toggleLog(this)">
            <div class="log-head-l">
              <div class="log-av la-warning"><i class="fas fa-exclamation-triangle"></i></div>
              <div style="flex:1;min-width:0">
                <div class="log-title">High Memory Usage Detected</div>
                <div class="log-meta">
                  <span class="log-badge lb-warning">Warning</span>
                  <span class="log-time"><i class="far fa-clock"></i>2024-01-15 14:20:15</span>
                  <span class="log-file">App/Services/ImageProcessor.php:89</span>
                </div>
              </div>
            </div>
            <div class="log-toggle"><i class="fas fa-chevron-down"></i></div>
          </div>
          <div class="log-code">
[2024-01-15 14:20:15] <span class="c-warn">WARNING</span>: High memory usage detected
Current usage: <span class="c-hl">85%</span> of available memory (1.7GB / 2GB)
Threshold: 80%
in <span class="c-path">App/Services/ImageProcessor.php</span> line <span class="c-hl">89</span>

Recommendation: Consider increasing memory_limit in php.ini</div>
        </div>

        <!-- INFO -->
        <div class="log-card lc-info">
          <div class="log-head" onclick="toggleLog(this)">
            <div class="log-head-l">
              <div class="log-av la-info"><i class="fas fa-info-circle"></i></div>
              <div style="flex:1;min-width:0">
                <div class="log-title">User Login Successful</div>
                <div class="log-meta">
                  <span class="log-badge lb-info">Info</span>
                  <span class="log-time"><i class="far fa-clock"></i>2024-01-15 14:18:22</span>
                  <span class="log-file">App/Http/Controllers/AuthController.php:156</span>
                </div>
              </div>
            </div>
            <div class="log-toggle"><i class="fas fa-chevron-down"></i></div>
          </div>
          <div class="log-code">
[2024-01-15 14:18:22] <span class="c-info">INFO</span>: User login successful
User ID: <span class="c-hl">1234</span>   Email: user@example.com
IP Address: 192.168.1.100   Browser: Chrome/120
Session ID: sess_abc123xyz
in <span class="c-path">App/Http/Controllers/AuthController.php</span> line <span class="c-hl">156</span></div>
        </div>

        <!-- DEBUG -->
        <div class="log-card lc-debug">
          <div class="log-head" onclick="toggleLog(this)">
            <div class="log-head-l">
              <div class="log-av la-debug"><i class="fas fa-bug"></i></div>
              <div style="flex:1;min-width:0">
                <div class="log-title">API Request Processed</div>
                <div class="log-meta">
                  <span class="log-badge lb-debug">Debug</span>
                  <span class="log-time"><i class="far fa-clock"></i>2024-01-15 14:15:10</span>
                  <span class="log-file">App/Http/Controllers/APIController.php:234</span>
                </div>
              </div>
            </div>
            <div class="log-toggle"><i class="fas fa-chevron-down"></i></div>
          </div>
          <div class="log-code">
[2024-01-15 14:15:10] <span class="c-dbg">DEBUG</span>: API request processed
Endpoint: <span class="c-hl">/api/v1/properties</span>   Method: GET
Response Time: <span class="c-hl">120ms</span>   Status: 200 OK
Query Count: 4   Cache: HIT
in <span class="c-path">App/Http/Controllers/APIController.php</span> line <span class="c-hl">234</span></div>
        </div>

      </div><!-- /log-list -->
    </div>
  </div>

  <!-- LOG FILES TABLE -->
  <div class="panel anim a6">
    <div class="ph">
      <div class="ph-l">
        <div class="p-ic pic-indigo"><i class="fas fa-folder-open"></i></div>
        <div>
          <div class="p-title">ملفات السجلات</div>
          <div class="p-sub">جميع ملفات اللوج المتاحة</div>
        </div>
      </div>
    </div>
    <div style="overflow-x:auto">
      <table class="tbl">
        <thead>
          <tr>
            <th>الملف</th>
            <th>الحجم</th>
            <th>السطور</th>
            <th>آخر تعديل</th>
            <th>الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <div class="tbl-file">
                <div class="tbl-fic tfi-gray"><i class="fas fa-file-alt"></i></div>
                <div><div class="tbl-fname">laravel.log</div><div class="tbl-fdesc">السجل الرئيسي</div></div>
              </div>
            </td>
            <td><span class="pill-size">45.2 MB</span></td>
            <td><span class="pill-lines">234,567</span></td>
            <td style="font-size:12px;color:var(--t4)"><i class="far fa-clock" style="margin-left:5px"></i>منذ 5 دقائق</td>
            <td><button class="btn-view" onclick="viewFile('laravel.log')"><i class="fas fa-eye"></i> عرض</button></td>
          </tr>
          <tr>
            <td>
              <div class="tbl-file">
                <div class="tbl-fic tfi-red"><i class="fas fa-file-alt"></i></div>
                <div><div class="tbl-fname">error.log</div><div class="tbl-fdesc">سجل الأخطاء</div></div>
              </div>
            </td>
            <td><span class="pill-size">12.3 MB</span></td>
            <td><span class="pill-lines">45,678</span></td>
            <td style="font-size:12px;color:var(--t4)"><i class="far fa-clock" style="margin-left:5px"></i>منذ ساعة</td>
            <td><button class="btn-view" onclick="viewFile('error.log')"><i class="fas fa-eye"></i> عرض</button></td>
          </tr>
          <tr>
            <td>
              <div class="tbl-file">
                <div class="tbl-fic tfi-blue"><i class="fas fa-file-alt"></i></div>
                <div><div class="tbl-fname">access.log</div><div class="tbl-fdesc">سجل الوصول</div></div>
              </div>
            </td>
            <td><span class="pill-size">89.7 MB</span></td>
            <td><span class="pill-lines">567,890</span></td>
            <td style="font-size:12px;color:var(--t4)"><i class="far fa-clock" style="margin-left:5px"></i>منذ يوم</td>
            <td><button class="btn-view" onclick="viewFile('access.log')"><i class="fas fa-eye"></i> عرض</button></td>
          </tr>
          <tr>
            <td>
              <div class="tbl-file">
                <div class="tbl-fic tfi-green"><i class="fas fa-file-alt"></i></div>
                <div><div class="tbl-fname">query.log</div><div class="tbl-fdesc">سجل قاعدة البيانات</div></div>
              </div>
            </td>
            <td><span class="pill-size">23.1 MB</span></td>
            <td><span class="pill-lines">98,234</span></td>
            <td style="font-size:12px;color:var(--t4)"><i class="far fa-clock" style="margin-left:5px"></i>منذ 3 ساعات</td>
            <td><button class="btn-view" onclick="viewFile('query.log')"><i class="fas fa-eye"></i> عرض</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div><!-- /content -->

<!-- LOG VIEWER MODAL -->
<div id="logModal" class="log-modal">
  <div class="lm-content">
    <div class="lm-header">
      <div class="lm-title">
        <i class="fas fa-file-code"></i>
        <span id="lm-filename">filename.log</span>
      </div>
      <button class="lm-close" onclick="closeLogModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div class="lm-body">
      <pre id="lm-code" class="lm-code">Loading...</pre>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
/* ═══ CHARTS ═══ */
let tChart, pChart;

function genLabels(n){
  const out=[],now=new Date();
  for(let i=n-1;i>=0;i--){
    const t=new Date(now-i*3600000);
    out.push(t.toLocaleTimeString('ar',{hour:'2-digit',minute:'2-digit'}));
  }
  return out;
}
function rnd(n,mn,mx){return Array.from({length:n},()=>Math.floor(Math.random()*(mx-mn+1))+mn);}

const chartDefaults={
  responsive:true, maintainAspectRatio:false,
  plugins:{
    legend:{position:'bottom',labels:{color:'#64748B',padding:18,font:{size:12,family:'Cairo'}}},
    tooltip:{backgroundColor:'#1E293B',titleColor:'#fff',bodyColor:'#CBD5E1',
             borderColor:'#334155',borderWidth:1,cornerRadius:10,padding:12}
  }
};

function initCharts(){
  /* Trend */
  const ctxTrend = document.getElementById('trendChart').getContext('2d');
  const gradientBlue = ctxTrend.createLinearGradient(0, 0, 0, 300);
  gradientBlue.addColorStop(0, 'rgba(13, 110, 253, 0.25)');
  gradientBlue.addColorStop(1, 'rgba(13, 110, 253, 0)');

  const gradientOrange = ctxTrend.createLinearGradient(0, 0, 0, 300);
  gradientOrange.addColorStop(0, 'rgba(253, 126, 20, 0.25)');
  gradientOrange.addColorStop(1, 'rgba(253, 126, 20, 0)');

  const gradientRed = ctxTrend.createLinearGradient(0, 0, 0, 300);
  gradientRed.addColorStop(0, 'rgba(220, 53, 69, 0.25)');
  gradientRed.addColorStop(1, 'rgba(220, 53, 69, 0)');

  tChart=new Chart(ctxTrend,{
    type:'line',
    data:{
      labels:genLabels(24),
      datasets:[
        {
          label:'Info',
          data:rnd(24,40,80),
          borderColor:'#0D6EFD',
          backgroundColor: gradientBlue,
          borderWidth:2,
          fill:true,
          tension:0.4,
          pointRadius:0,
          pointHoverRadius:6,
          pointBackgroundColor: '#0D6EFD',
          pointBorderColor: '#fff',
          pointBorderWidth: 2
        },
        {
          label:'Warning',
          data:rnd(24,2,12),
          borderColor:'#FD7E14',
          backgroundColor: gradientOrange,
          borderWidth:2,
          fill:true,
          tension:0.4,
          pointRadius:0,
          pointHoverRadius:6,
          pointBackgroundColor: '#FD7E14',
          pointBorderColor: '#fff',
          pointBorderWidth: 2
        },
        {
          label:'Error',
          data:rnd(24,0,6),
          borderColor:'#DC3545',
          backgroundColor: gradientRed,
          borderWidth:2,
          fill:true,
          tension:0.4,
          pointRadius:0,
          pointHoverRadius:6,
          pointBackgroundColor: '#DC3545',
          pointBorderColor: '#fff',
          pointBorderWidth: 2
        },
      ]
    },
    options:{
      ...chartDefaults,
      interaction:{mode:'index',intersect:false},
      scales:{
        x:{
          grid:{display:false, drawBorder: false},
          ticks:{color:'#94A3B8',font:{size:11, family: 'Cairo'}, maxTicksLimit: 8}
        },
        y:{
          beginAtZero:true,
          border: {display: false},
          grid:{color:'#F1F5F9', borderDash: [5, 5]},
          ticks:{color:'#94A3B8',font:{size:11, family: 'Cairo'}, padding: 10}
        }
      },
      plugins: {
        ...chartDefaults.plugins,
        tooltip: {
          ...chartDefaults.plugins.tooltip,
          mode: 'index',
          intersect: false,
          callbacks: {
            label: function(context) {
              let label = context.dataset.label || '';
              if (label) {
                label += ': ';
              }
              if (context.parsed.y !== null) {
                label += context.parsed.y + ' سجل';
              }
              return label;
            }
          }
        }
      }
    }
  });

  /* Pie */
  pChart=new Chart(document.getElementById('pieChart'),{
    type:'doughnut',
    data:{
      labels:['Debug','Info','Warning','Error','Critical'],
      datasets:[{
        data:[234,1456,89,23,3],
        backgroundColor:['#9CA3AF','#0D6EFD','#FD7E14','#DC3545','#7F1D1D'],
        borderWidth:3,borderColor:'#fff',hoverOffset:8
      }]
    },
    options:{
      ...chartDefaults,
      cutout:'68%',
      plugins:{
        ...chartDefaults.plugins,
        tooltip:{...chartDefaults.plugins.tooltip,
          callbacks:{label:c=>` ${c.label}: ${c.parsed.toLocaleString()} سجل`}}
      }
    }
  });
}

function updateTrend(){
  if(!tChart)return;
  const now=new Date().toLocaleTimeString('ar',{hour:'2-digit',minute:'2-digit'});
  tChart.data.labels.shift(); tChart.data.labels.push(now);
  tChart.data.datasets.forEach((ds,i)=>{
    ds.data.shift();
    ds.data.push(i===0?rnd(1,40,80)[0]:i===1?rnd(1,2,12)[0]:rnd(1,0,6)[0]);
  });
  tChart.update('none');
}

/* ═══ LOG TOGGLE ═══ */
function toggleLog(head){
  const code=head.nextElementSibling;
  const btn=head.querySelector('.log-toggle');
  const open=code.classList.toggle('show');
  btn.classList.toggle('open',open);
}

/* ═══ ACTIONS ═══ */
function refreshLogs(){
  const btn=document.getElementById('refresh-btn');
  const orig=btn.innerHTML; btn.disabled=true;
  btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';
  updateTrend();
  setTimeout(()=>{
    btn.innerHTML=orig; btn.disabled=false;
    showNotif('تم تحديث السجلات بنجاح','success');
  },1400);
}

function clearLogs(){
  const btn=document.getElementById('clear-btn');
  const orig=btn.innerHTML; btn.disabled=true;
  btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> جاري المسح...';
  setTimeout(()=>{
    document.getElementById('logs-container').innerHTML=`
      <div style="text-align:center;padding:52px 20px">
        <i class="fas fa-check-circle" style="font-size:44px;color:var(--green);margin-bottom:14px;display:block"></i>
        <div style="font-size:16px;font-weight:700;color:var(--t2);margin-bottom:6px">تم مسح جميع السجلات</div>
        <div style="font-size:12.5px;color:var(--t4)">لا توجد سجلات حالياً</div>
      </div>`;
    btn.innerHTML=orig; btn.disabled=false;
    showNotif('تم مسح جميع السجلات بنجاح','success');
  },1800);
}

function applyFilters(){
  showNotif('جاري تطبيق الفلاتر...','info');
  setTimeout(()=>showNotif('تم تطبيق الفلاتر بنجاح','success'),1400);
}

function downloadLogs(){
  showNotif('جاري تحميل السجلات...','info');
  setTimeout(()=>showNotif('تم تحميل السجلات','success'),2000);
}

function viewFile(f){
  showNotif(`جاري فتح: ${f}`,'info');
  const m = document.getElementById('logModal');
  const t = document.getElementById('lm-filename');
  const c = document.getElementById('lm-code');
  
  t.innerText = f;
  c.innerText = 'جاري التحميل...';
  m.classList.add('show');
  
  fetch(`{{ url('admin/system/logs/view') }}/${f}`)
    .then(r => r.json())
    .then(d => {
      if(d.error) c.innerText = 'خطأ: ' + d.error;
      else c.innerText = d.content || 'الملف فارغ';
    })
    .catch(e => {
      console.error(e);
      c.innerText = 'حدث خطأ أثناء تحميل الملف';
    });
}

function closeLogModal(){
  document.getElementById('logModal').classList.remove('show');
}

/* ═══ NOTIFICATION ═══ */
let ntTimer;
function showNotif(msg,type='success'){
  const n=document.getElementById('notif');
  const icons={success:'check-circle',info:'info-circle',error:'exclamation-circle'};
  n.className=`notif ${type} show`;
  n.innerHTML=`<i class="fas fa-${icons[type]}"></i><span>${msg}</span>`;
  clearTimeout(ntTimer);
  ntTimer=setTimeout(()=>n.classList.remove('show'),3200);
}

/* ═══ INIT ═══ */
document.addEventListener('DOMContentLoaded',()=>{
  initCharts();
  setInterval(updateTrend,9000);
});
</script>
@endpush