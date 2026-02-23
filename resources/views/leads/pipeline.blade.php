@extends('admin.layouts.admin')

@section('title', 'مسار المبيعات')

@push('styles')
<style>
:root{
  --blue:#0D6EFD; --blue-g:linear-gradient(135deg,#0D6EFD,#0a58ca);
  --purple:#6F42C1; --green:#198754; --orange:#FD7E14;
  --red:#DC3545; --yellow:#FFC107; --teal:#0DCAF0;
  --bg:#F0F4FF; --card:#fff; --card2:#F8FAFF;
  --bd:#E2E8F0; --bd2:#CBD5E1;
  --t1:#1E293B; --t2:#475569; --t3:#64748B; --t4:#94A3B8;
  --r1:10px; --r2:14px; --r3:18px;
  --sh:0 1px 4px rgba(0,0,0,.06),0 2px 10px rgba(0,0,0,.04);
  --sh-md:0 4px 20px rgba(0,0,0,.09),0 2px 8px rgba(0,0,0,.05);
  --sh-lg:0 10px 36px rgba(0,0,0,.11);
  --tr:all .22s cubic-bezier(.4,0,.2,1);
  --tr-sp:all .35s cubic-bezier(.34,1.3,.64,1);
  --ff:'Cairo',system-ui,sans-serif;
}

/* html,body{height:100%;overflow:hidden;} REMOVED to fix scrolling */
/* body{font-family:var(--ff);background:var(--bg);color:var(--t1);direction:rtl;display:flex;flex-direction:column;} */

/* Apply background and font to a wrapper or the main content instead of body if needed, 
   but since this extends a layout, we should respect the layout's body. 
   We can apply the background to the main wrapper. */

.pipeline-page-wrapper {
  font-family:var(--ff);
  color:var(--t1);
  direction:rtl;
  display:flex;
  flex-direction:column;
  min-height: 100%;
}

.pipeline-bg-overlay {
  position:fixed;inset:0;pointer-events:none;z-index:0;
  background:
    radial-gradient(ellipse 55% 35% at 85% 5%,rgba(13,110,253,.05) 0%,transparent 55%),
    radial-gradient(ellipse 45% 45% at 5% 90%,rgba(111,66,193,.04) 0%,transparent 55%);
}

/* ═══ TOP HEADER ═══ */
.page-header{
  background:linear-gradient(140deg,#1E3A8A 0%,#1D4ED8 55%,#4F46E5 100%);
  padding:0 28px; position:relative; overflow:hidden; flex-shrink:0;
}
.page-header::before{
  content:'';position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),
    linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);
  background-size:38px 38px;
}
.page-header::after{
  content:'';position:absolute;bottom:-2px;left:0;right:0;height:34px;
  background:var(--bg);clip-path:ellipse(52% 100% at 50% 100%);
}
.ph-inner{
  position:relative;z-index:1;
  display:flex;align-items:center;justify-content:space-between;
  gap:16px;flex-wrap:wrap;padding:22px 0 40px;
}
.ph-left{}
.ph-eyebrow{
  font-size:10px;font-weight:700;letter-spacing:.22em;
  text-transform:uppercase;color:rgba(255,255,255,.6);
  display:flex;align-items:center;gap:8px;margin-bottom:5px;
}
.ph-eyebrow::before{content:'';display:inline-block;width:16px;height:1.5px;background:rgba(255,255,255,.45);border-radius:2px;}
.ph-title{
  display:flex;align-items:center;gap:13px;
  font-size:clamp(19px,2.5vw,28px);font-weight:900;color:#fff;line-height:1.2;
}
.ph-ic{
  width:44px;height:44px;flex-shrink:0;
  background:rgba(255,255,255,.14);border:1.5px solid rgba(255,255,255,.22);
  border-radius:var(--r2);display:flex;align-items:center;justify-content:center;
  font-size:17px;box-shadow:0 4px 14px rgba(0,0,0,.2);
}
.ph-sub{font-size:12.5px;color:rgba(255,255,255,.68);margin-top:5px;margin-right:57px;}
.ph-right{display:flex;align-items:center;gap:10px;flex-shrink:0;}

.hbtn{
  display:inline-flex;align-items:center;gap:8px;
  padding:9px 18px;border-radius:var(--r2);
  font-family:var(--ff);font-size:13px;font-weight:700;
  cursor:pointer;border:none;transition:var(--tr-sp);
  text-decoration:none;white-space:nowrap;
}
.hbtn-ghost{
  background:rgba(255,255,255,.13);color:#fff;
  border:1.5px solid rgba(255,255,255,.22);
}
.hbtn-ghost:hover{background:rgba(255,255,255,.22);transform:translateY(-2px);}
.hbtn-orange{
  background:linear-gradient(135deg,#FD7E14,#ea6c00);color:#fff;
  box-shadow:0 4px 16px rgba(253,126,20,.35);
}
.hbtn-orange:hover{transform:translateY(-3px);box-shadow:0 10px 24px rgba(253,126,20,.42);}

/* ═══ STATS ROW ═══ */
.stats-bar{
  position:relative;z-index:1;
  display:grid;grid-template-columns:repeat(4,1fr);
  gap:12px;padding:0 28px;
  margin-top:-18px;margin-bottom:14px;flex-shrink:0;
}
.sc{
  background:var(--card);border:1px solid var(--bd);
  border-radius:var(--r2);padding:14px 16px;
  box-shadow:var(--sh);transition:var(--tr-sp);
  display:flex;align-items:center;justify-content:space-between;gap:12px;
  position:relative;overflow:hidden;
}
.sc::before{
  content:'';position:absolute;top:0;left:0;right:0;height:3px;
  border-radius:var(--r2) var(--r2) 0 0;transition:height .3s;
}
.sc:hover{transform:translateY(-4px);box-shadow:var(--sh-md);}
.sc:hover::before{height:4px;}
.sc-blue::before  {background:var(--blue-g);}
.sc-green::before {background:linear-gradient(135deg,#198754,#146c43);}
.sc-orange::before{background:linear-gradient(135deg,#FD7E14,#dc6a0c);}
.sc-purple::before{background:linear-gradient(135deg,#6F42C1,#59359a);}

.sc-left{}
.sc-label{font-size:10.5px;font-weight:700;color:var(--t4);letter-spacing:.04em;margin-bottom:4px;}
.sc-val{font-size:clamp(20px,2vw,28px);font-weight:900;color:var(--t1);line-height:1;}
.sc-icon{
  width:40px;height:40px;border-radius:var(--r1);
  display:flex;align-items:center;justify-content:center;
  font-size:16px;border:1px solid;flex-shrink:0;
  transition:transform .35s cubic-bezier(.34,1.3,.64,1);
}
.sc:hover .sc-icon{transform:scale(1.12) rotate(6deg);}
.si-blue  {background:rgba(13,110,253,.1); color:var(--blue);   border-color:rgba(13,110,253,.2);}
.si-green {background:rgba(25,135,84,.1);  color:var(--green);  border-color:rgba(25,135,84,.2);}
.si-orange{background:rgba(253,126,20,.1); color:var(--orange); border-color:rgba(253,126,20,.2);}
.si-purple{background:rgba(111,66,193,.1); color:var(--purple); border-color:rgba(111,66,193,.2);}

/* ═══ PIPELINE WRAPPER ═══ */
.pipeline-wrapper{
  position:relative;z-index:1;
  flex:1;min-height:0;
  display:flex;flex-direction:column;
  margin:0 28px 16px;
  background:var(--card);
  border:1px solid var(--bd);
  border-radius:var(--r3);
  box-shadow:var(--sh);
  overflow:hidden;
}

/* Pipeline toolbar */
.pip-toolbar{
  display:flex;align-items:center;justify-content:space-between;
  padding:12px 18px;border-bottom:1px solid var(--bd);
  background:var(--card);flex-shrink:0;gap:12px;flex-wrap:wrap;
}
.pip-toolbar-left{display:flex;align-items:center;gap:12px;flex-wrap:wrap;}
.pip-title{display:flex;align-items:center;gap:9px;font-size:14px;font-weight:800;color:var(--t1);}
.pip-title i{color:var(--blue);}
.pip-count{
  font-size:10.5px;font-weight:700;
  background:rgba(13,110,253,.08);color:var(--blue);
  border:1px solid rgba(13,110,253,.18);
  padding:3px 10px;border-radius:20px;
}

/* Search in toolbar */
.pip-search{position:relative;}
.pip-search i{position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--t4);font-size:12px;}
.pip-search input{
  padding:7px 32px 7px 12px;
  border:1.5px solid var(--bd);border-radius:var(--r1);
  font-family:var(--ff);font-size:12.5px;color:var(--t1);
  background:var(--card2);outline:none;width:200px;
  transition:border-color .2s,box-shadow .2s;
}
.pip-search input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(13,110,253,.1);}

/* View toggle */
.pip-toolbar-right{display:flex;align-items:center;gap:8px;}
.view-toggle{
  display:flex;background:var(--bg);border:1px solid var(--bd);
  border-radius:8px;padding:3px;gap:2px;
}
.vt-btn{
  width:30px;height:28px;border-radius:6px;border:none;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  font-size:12px;color:var(--t3);background:transparent;
  transition:var(--tr);
}
.vt-btn.active{background:var(--card);color:var(--blue);box-shadow:var(--sh);}
.vt-btn:hover:not(.active){color:var(--blue);}

.tb-icon-btn{
  width:32px;height:32px;border-radius:8px;border:1px solid var(--bd);
  background:var(--card);cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  font-size:13px;color:var(--t3);transition:var(--tr);
}
.tb-icon-btn:hover{border-color:rgba(13,110,253,.25);color:var(--blue);background:rgba(13,110,253,.04);}

/* Pipeline board */
.pip-board{
  flex:1;min-height:0;
  overflow-x:auto;overflow-y:hidden;
  padding:16px;
  background:var(--bg);
  display:flex;gap:14px;
  align-items:flex-start;
}

/* Custom scrollbar */
.pip-board::-webkit-scrollbar{height:5px;}
.pip-board::-webkit-scrollbar-track{background:transparent;}
.pip-board::-webkit-scrollbar-thumb{background:var(--bd2);border-radius:10px;}
.pip-board::-webkit-scrollbar-thumb:hover{background:var(--blue);}

/* ═══ KANBAN COLUMN ═══ */
.k-col{
  flex-shrink:0;width:280px;
  display:flex;flex-direction:column;
  background:var(--card);
  border:1px solid var(--bd);
  border-radius:var(--r2);
  box-shadow:var(--sh);
  max-height:100%;
  transition:var(--tr);
}
.k-col:hover{box-shadow:var(--sh-md);}
.k-col.drag-over{
  border-color:var(--blue);
  background:rgba(13,110,253,.03);
  box-shadow:0 0 0 2px rgba(13,110,253,.15);
}

/* Column header */
.k-head{
  padding:12px 14px 10px;
  border-bottom:1px solid var(--bd);
  border-radius:var(--r2) var(--r2) 0 0;
  flex-shrink:0;
  background:var(--card2);
}
.k-head-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;}
.k-head-left{display:flex;align-items:center;gap:8px;}
.k-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;}
.k-name{font-size:13px;font-weight:800;color:var(--t1);}
.k-badge{
  font-size:10px;font-weight:700;
  background:var(--bg);color:var(--t3);
  border:1px solid var(--bd);
  padding:2px 8px;border-radius:20px;
}
/* Progress bar */
.k-prog{height:3px;background:var(--bg);border-radius:4px;overflow:hidden;}
.k-prog-fill{height:100%;border-radius:4px;opacity:.55;width:100%;}

/* Column leads */
.k-leads{
  flex:1;overflow-y:auto;padding:10px;
  display:flex;flex-direction:column;gap:8px;
  min-height:80px;
}
.k-leads::-webkit-scrollbar{width:3px;}
.k-leads::-webkit-scrollbar-thumb{background:var(--bd2);border-radius:10px;}

/* ═══ LEAD CARD ═══ */
.lead-card{
  background:var(--card);
  border:1px solid var(--bd);
  border-radius:var(--r1);
  padding:12px 12px 10px 14px;
  cursor:grab;transition:var(--tr-sp);
  position:relative;overflow:hidden;
  box-shadow:0 1px 3px rgba(0,0,0,.05);
}
.lead-card:hover{
  transform:translateY(-2px);
  box-shadow:var(--sh-md);
  border-color:rgba(13,110,253,.2);
}
.lead-card.dragging{opacity:.5;transform:rotate(2deg);cursor:grabbing;}

/* Priority stripe on right (RTL) */
.lead-card::before{
  content:'';position:absolute;
  right:0;top:10px;bottom:10px;
  width:3px;border-radius:0 3px 3px 0;
}
.lc-high::before  {background:var(--red);}
.lc-medium::before{background:var(--yellow);}
.lc-low::before   {background:var(--green);}

.lc-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px;padding-right:8px;}
.lc-name{font-size:12.5px;font-weight:800;color:var(--t1);line-height:1.3;}
.lc-company{font-size:11px;color:var(--t4);display:flex;align-items:center;gap:5px;margin-bottom:8px;padding-right:8px;}
.lc-company i{font-size:10px;}

/* badges */
.lc-badges{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:8px;padding-right:8px;}
.lc-badge{
  display:inline-flex;align-items:center;gap:4px;
  font-size:9.5px;font-weight:700;
  padding:2px 8px;border-radius:20px;border:1px solid;
}
.lb-source{background:var(--bg);color:var(--t3);border-color:var(--bd);}
.lb-value {background:rgba(25,135,84,.08);color:var(--green);border-color:rgba(25,135,84,.18);}

.lc-foot{
  display:flex;align-items:center;justify-content:space-between;
  padding-top:8px;border-top:1px solid var(--bd);padding-right:8px;
}
.lc-avatar{
  width:24px;height:24px;border-radius:50%;
  background:linear-gradient(135deg,#0D6EFD,#6F42C1);
  display:flex;align-items:center;justify-content:center;
  font-size:10px;font-weight:700;color:#fff;flex-shrink:0;
  border:2px solid #fff;box-shadow:0 0 0 1px var(--bd);
}
.lc-avatar img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
.lc-time{font-size:10px;color:var(--t4);display:flex;align-items:center;gap:4px;}

/* Hover actions */
.lc-actions{
  position:absolute;top:8px;left:8px;
  display:flex;gap:3px;
  opacity:0;transition:opacity .2s;
  background:rgba(255,255,255,.92);
  backdrop-filter:blur(8px);
  border:1px solid var(--bd);
  border-radius:8px;padding:3px;
  box-shadow:var(--sh);
}
.lead-card:hover .lc-actions{opacity:1;}
.lc-act-btn{
  width:24px;height:24px;border-radius:6px;border:none;
  background:transparent;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  font-size:10px;transition:var(--tr);
}
.lca-edit {color:var(--blue);}
.lca-edit:hover{background:rgba(13,110,253,.1);}
.lca-del  {color:var(--red);}
.lca-del:hover{background:rgba(220,53,69,.1);}

/* ═══ EMPTY STATE ═══ */
.k-empty{
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:24px 16px;text-align:center;
  border:2px dashed var(--bd2);border-radius:var(--r1);
  background:var(--bg);
}
.k-empty i{font-size:22px;color:var(--t4);margin-bottom:8px;opacity:.6;}
.k-empty-txt{font-size:11.5px;color:var(--t4);margin-bottom:8px;}
.k-empty-btn{
  font-size:11.5px;font-weight:700;color:var(--blue);
  background:transparent;border:none;cursor:pointer;
  display:flex;align-items:center;gap:4px;font-family:var(--ff);
  transition:var(--tr);
}
.k-empty-btn:hover{color:var(--blue);text-decoration:underline;}

/* ═══ COLUMN FOOTER ═══ */
.k-foot{
  padding:8px 10px;border-top:1px solid var(--bd);
  border-radius:0 0 var(--r2) var(--r2);flex-shrink:0;
}
.k-add-btn{
  width:100%;padding:7px;border-radius:8px;border:none;
  background:transparent;cursor:pointer;font-family:var(--ff);
  font-size:12px;font-weight:700;color:var(--t3);
  display:flex;align-items:center;justify-content:center;gap:6px;
  transition:var(--tr);border:1px solid transparent;
}
.k-add-btn:hover{background:rgba(13,110,253,.06);color:var(--blue);border-color:rgba(13,110,253,.15);}
.k-add-btn i{font-size:11px;}

/* ═══ NOTIFICATION ═══ */
.notif{
  position:fixed;top:16px;left:16px;z-index:9999;
  display:flex;align-items:center;gap:10px;
  padding:10px 18px;border-radius:var(--r2);
  font-family:var(--ff);font-size:13px;font-weight:700;
  box-shadow:0 8px 32px rgba(0,0,0,.15);
  transition:transform .4s cubic-bezier(.34,1.2,.64,1),opacity .3s;
  transform:translateX(-130%);opacity:0;border:1px solid;
}
.notif.show{transform:translateX(0);opacity:1;}
.notif.success{background:var(--card);border-color:rgba(25,135,84,.3);color:var(--green);}
.notif.info   {background:var(--card);border-color:rgba(13,110,253,.3);color:var(--blue);}
.notif.error  {background:var(--card);border-color:rgba(220,53,69,.3); color:var(--red);}

/* RESPONSIVE */
@media(max-width:900px){
  .stats-bar{grid-template-columns:repeat(2,1fr);}
  .pipeline-wrapper,.stats-bar{margin-left:14px;margin-right:14px;}
  .page-header{padding:0 14px;}
}
@media(max-width:600px){
  .stats-bar{grid-template-columns:1fr 1fr;}
  .ph-right{display:none;}
}
</style>
@endpush

@section('content')

<div class="pipeline-page-wrapper">
    <div class="pipeline-bg-overlay"></div>

    <!-- NOTIFICATION -->
    <div class="notif" id="notif">
      <i class="fas fa-check-circle"></i>
      <span id="notif-msg">رسالة</span>
    </div>

    <!-- ═══ PAGE HEADER ═══ -->
    <div class="page-header">
  <div class="ph-inner">
    <div class="ph-left">
      <div class="ph-eyebrow">CRM / مبيعات</div>
      <h1 class="ph-title">
        <div class="ph-ic"><i class="fas fa-columns"></i></div>
        مسار المبيعات
      </h1>
      <p class="ph-sub">إدارة وتتبع مسار تحويل العملاء المحتملين</p>
    </div>
    <div class="ph-right">
      <button class="hbtn hbtn-ghost" onclick="refreshPipeline()">
        <i class="fas fa-sync-alt"></i> تحديث
      </button>
      <a href="#" class="hbtn hbtn-orange">
        <i class="fas fa-plus"></i> إضافة عميل
      </a>
    </div>
  </div>
</div>

<!-- ═══ STATS ROW ═══ -->
<div class="stats-bar">
  <div class="sc sc-blue">
    <div class="sc-left">
      <div class="sc-label">إجمالي العملاء</div>
      <div class="sc-val" style="color:var(--blue)">248</div>
    </div>
    <div class="sc-icon si-blue"><i class="fas fa-users"></i></div>
  </div>
  <div class="sc sc-green">
    <div class="sc-left">
      <div class="sc-label">العملاء المحوّلين</div>
      <div class="sc-val" style="color:var(--green)">64</div>
    </div>
    <div class="sc-icon si-green"><i class="fas fa-check-circle"></i></div>
  </div>
  <div class="sc sc-orange">
    <div class="sc-left">
      <div class="sc-label">القيمة الإجمالية</div>
      <div class="sc-val" style="color:var(--orange)">4.7M</div>
    </div>
    <div class="sc-icon si-orange"><i class="fas fa-dollar-sign"></i></div>
  </div>
  <div class="sc sc-purple">
    <div class="sc-left">
      <div class="sc-label">معدل التحويل</div>
      <div class="sc-val" style="color:var(--purple)">25.8%</div>
    </div>
    <div class="sc-icon si-purple"><i class="fas fa-chart-line"></i></div>
  </div>
</div>

<!-- ═══ PIPELINE WRAPPER ═══ -->
<div class="pipeline-wrapper">

  <!-- Toolbar -->
  <div class="pip-toolbar">
    <div class="pip-toolbar-left">
      <div class="pip-title">
        <i class="fas fa-th-large"></i>
        <span>لوحة المسار</span>
      </div>
      <div class="pip-count">248 عميل</div>
      <div class="pip-search">
        <i class="fas fa-search"></i>
        <input type="text" id="pip-search-inp" placeholder="بحث عن عميل..." oninput="filterCards()">
      </div>
    </div>
    <div class="pip-toolbar-right">
      <button class="tb-icon-btn" onclick="refreshPipeline()" title="تحديث">
        <i class="fas fa-sync-alt"></i>
      </button>
      <div class="view-toggle">
        <button class="vt-btn active" title="عرض الكانبان"><i class="fas fa-th-large"></i></button>
        <button class="vt-btn" title="عرض القائمة" onclick="showNotif('تم التبديل إلى عرض القائمة','info')"><i class="fas fa-list"></i></button>
      </div>
    </div>
  </div>

  <!-- BOARD -->
  <div class="pip-board" id="pip-board">

    <!-- COL 1 — جديد -->
    <div class="k-col" data-status="1">
      <div class="k-head">
        <div class="k-head-top">
          <div class="k-head-left">
            <div class="k-dot" style="background:#0D6EFD;box-shadow:0 0 0 3px rgba(13,110,253,.15)"></div>
            <div class="k-name">جديد</div>
          </div>
          <div class="k-badge">5</div>
        </div>
        <div class="k-prog"><div class="k-prog-fill" style="background:#0D6EFD"></div></div>
      </div>
      <div class="k-leads pipeline-leads">

        <!-- Card -->
        <div class="lead-card lc-high" draggable="true" data-lead="1" onclick="viewLead(1)">
          <div class="lc-actions">
            <button class="lc-act-btn lca-edit" onclick="event.stopPropagation();editLead(1)" title="تعديل"><i class="fas fa-pen"></i></button>
            <button class="lc-act-btn lca-del"  onclick="event.stopPropagation();deleteLead(1)" title="حذف"><i class="fas fa-trash"></i></button>
          </div>
          <div class="lc-top">
            <div class="lc-name">أحمد محمد السعيد</div>
          </div>
          <div class="lc-company"><i class="far fa-building"></i>شركة الأفق للتطوير</div>
          <div class="lc-badges">
            <span class="lc-badge lb-source"><i class="fas fa-globe" style="font-size:8px"></i>الموقع</span>
            <span class="lc-badge lb-value">850,000 ر.س</span>
          </div>
          <div class="lc-foot">
            <div class="lc-avatar">أ</div>
            <div class="lc-time"><i class="far fa-clock"></i>منذ ساعتين</div>
          </div>
        </div>

        <div class="lead-card lc-medium" draggable="true" data-lead="2" onclick="viewLead(2)">
          <div class="lc-actions">
            <button class="lc-act-btn lca-edit" onclick="event.stopPropagation();editLead(2)" title="تعديل"><i class="fas fa-pen"></i></button>
            <button class="lc-act-btn lca-del"  onclick="event.stopPropagation();deleteLead(2)" title="حذف"><i class="fas fa-trash"></i></button>
          </div>
          <div class="lc-top">
            <div class="lc-name">سارة عبدالله الحربي</div>
          </div>
          <div class="lc-badges">
            <span class="lc-badge lb-source"><i class="fas fa-hashtag" style="font-size:8px"></i>تواصل اجتماعي</span>
            <span class="lc-badge lb-value">320,000 ر.س</span>
          </div>
          <div class="lc-foot">
            <div class="lc-avatar">س</div>
            <div class="lc-time"><i class="far fa-clock"></i>منذ 5 ساعات</div>
          </div>
        </div>

        <div class="lead-card lc-low" draggable="true" data-lead="3" onclick="viewLead(3)">
          <div class="lc-actions">
            <button class="lc-act-btn lca-edit" onclick="event.stopPropagation();editLead(3)" title="تعديل"><i class="fas fa-pen"></i></button>
            <button class="lc-act-btn lca-del"  onclick="event.stopPropagation();deleteLead(3)" title="حذف"><i class="fas fa-trash"></i></button>
          </div>
          <div class="lc-top">
            <div class="lc-name">خالد عمر المنصوري</div>
          </div>
          <div class="lc-company"><i class="far fa-building"></i>مجموعة النخبة العقارية</div>
          <div class="lc-badges">
            <span class="lc-badge lb-source"><i class="fas fa-user-friends" style="font-size:8px"></i>توصية</span>
            <span class="lc-badge lb-value">1,200,000 ر.س</span>
          </div>
          <div class="lc-foot">
            <div class="lc-avatar">خ</div>
            <div class="lc-time"><i class="far fa-clock"></i>أمس</div>
          </div>
        </div>

        <div class="lead-card lc-high" draggable="true" data-lead="9" onclick="viewLead(9)">
          <div class="lc-actions">
            <button class="lc-act-btn lca-edit" onclick="event.stopPropagation();editLead(9)" title="تعديل"><i class="fas fa-pen"></i></button>
            <button class="lc-act-btn lca-del"  onclick="event.stopPropagation();deleteLead(9)" title="حذف"><i class="fas fa-trash"></i></button>
          </div>
          <div class="lc-top">
            <div class="lc-name">ريم فيصل الأحمدي</div>
          </div>
          <div class="lc-badges">
            <span class="lc-badge lb-source"><i class="fas fa-envelope" style="font-size:8px"></i>إيميل</span>
            <span class="lc-badge lb-value">560,000 ر.س</span>
          </div>
          <div class="lc-foot">
            <div class="lc-avatar" style="background:linear-gradient(135deg,#DB2777,#9333EA)">ر</div>
            <div class="lc-time"><i class="far fa-clock"></i>منذ 3 أيام</div>
          </div>
        </div>

        <div class="lead-card lc-medium" draggable="true" data-lead="10" onclick="viewLead(10)">
          <div class="lc-actions">
            <button class="lc-act-btn lca-edit" onclick="event.stopPropagation();editLead(10)" title="تعديل"><i class="fas fa-pen"></i></button>
            <button class="lc-act-btn lca-del"  onclick="event.stopPropagation();deleteLead(10)" title="حذف"><i class="fas fa-trash"></i></button>
          </div>
          <div class="lc-top">
            <div class="lc-name">بدر سلمان العتيبي</div>
          </div>
          <div class="lc-company"><i class="far fa-building"></i>شركة قمة للاستثمار</div>
          <div class="lc-badges">
            <span class="lc-badge lb-source"><i class="fas fa-phone" style="font-size:8px"></i>هاتف</span>
            <span class="lc-badge lb-value">980,000 ر.س</span>
          </div>
          <div class="lc-foot">
            <div class="lc-avatar" style="background:linear-gradient(135deg,#0891B2,#0D6EFD)">ب</div>
            <div class="lc-time"><i class="far fa-clock"></i>منذ 4 أيام</div>
          </div>
        </div>

      </div>
      <div class="k-foot">
        <button class="k-add-btn" onclick="addToStatus(1)"><i class="fas fa-plus"></i>إضافة بطاقة</button>
      </div>
    </div>

    <!-- COL 2 — تم التواصل -->
    <div class="k-col" data-status="2">
      <div class="k-head">
        <div class="k-head-top">
          <div class="k-head-left">
            <div class="k-dot" style="background:#FD7E14;box-shadow:0 0 0 3px rgba(253,126,20,.15)"></div>
            <div class="k-name">تم التواصل</div>
          </div>
          <div class="k-badge">3</div>
        </div>
        <div class="k-prog"><div class="k-prog-fill" style="background:#FD7E14"></div></div>
      </div>
      <div class="k-leads pipeline-leads">

        <div class="lead-card lc-high" draggable="true" data-lead="4" onclick="viewLead(4)">
          <div class="lc-actions">
            <button class="lc-act-btn lca-edit" onclick="event.stopPropagation();editLead(4)" title="تعديل"><i class="fas fa-pen"></i></button>
            <button class="lc-act-btn lca-del"  onclick="event.stopPropagation();deleteLead(4)" title="حذف"><i class="fas fa-trash"></i></button>
          </div>
          <div class="lc-top">
            <div class="lc-name">منى إبراهيم الزهراني</div>
          </div>
          <div class="lc-company"><i class="far fa-building"></i>مؤسسة الأمل للبناء</div>
          <div class="lc-badges">
            <span class="lc-badge lb-source"><i class="fas fa-phone" style="font-size:8px"></i>هاتف</span>
            <span class="lc-badge lb-value">430,000 ر.س</span>
          </div>
          <div class="lc-foot">
            <div class="lc-avatar" style="background:linear-gradient(135deg,#FD7E14,#DC2626)">م</div>
            <div class="lc-time"><i class="far fa-clock"></i>منذ يوم</div>
          </div>
        </div>

        <div class="lead-card lc-medium" draggable="true" data-lead="5" onclick="viewLead(5)">
          <div class="lc-actions">
            <button class="lc-act-btn lca-edit" onclick="event.stopPropagation();editLead(5)" title="تعديل"><i class="fas fa-pen"></i></button>
            <button class="lc-act-btn lca-del"  onclick="event.stopPropagation();deleteLead(5)" title="حذف"><i class="fas fa-trash"></i></button>
          </div>
          <div class="lc-top">
            <div class="lc-name">تركي عبدالرحمن الشمري</div>
          </div>
          <div class="lc-badges">
            <span class="lc-badge lb-source"><i class="fas fa-user-friends" style="font-size:8px"></i>توصية</span>
            <span class="lc-badge lb-value">2,100,000 ر.س</span>
          </div>
          <div class="lc-foot">
            <div class="lc-avatar" style="background:linear-gradient(135deg,#198754,#0D6EFD)">ت</div>
            <div class="lc-time"><i class="far fa-clock"></i>منذ يومين</div>
          </div>
        </div>

        <div class="lead-card lc-low" draggable="true" data-lead="11" onclick="viewLead(11)">
          <div class="lc-actions">
            <button class="lc-act-btn lca-edit" onclick="event.stopPropagation();editLead(11)" title="تعديل"><i class="fas fa-pen"></i></button>
            <button class="lc-act-btn lca-del"  onclick="event.stopPropagation();deleteLead(11)" title="حذف"><i class="fas fa-trash"></i></button>
          </div>
          <div class="lc-top">
            <div class="lc-name">نورة سعد القحطاني</div>
          </div>
          <div class="lc-badges">
            <span class="lc-badge lb-source"><i class="fas fa-globe" style="font-size:8px"></i>الموقع</span>
            <span class="lc-badge lb-value">740,000 ر.س</span>
          </div>
          <div class="lc-foot">
            <div class="lc-avatar" style="background:linear-gradient(135deg,#6F42C1,#DB2777)">ن</div>
            <div class="lc-time"><i class="far fa-clock"></i>منذ 3 أيام</div>
          </div>
        </div>

      </div>
      <div class="k-foot">
        <button class="k-add-btn" onclick="addToStatus(2)"><i class="fas fa-plus"></i>إضافة بطاقة</button>
      </div>
    </div>

    <!-- COL 3 — في المفاوضات -->
    <div class="k-col" data-status="3">
      <div class="k-head">
        <div class="k-head-top">
          <div class="k-head-left">
            <div class="k-dot" style="background:#6F42C1;box-shadow:0 0 0 3px rgba(111,66,193,.15)"></div>
            <div class="k-name">في المفاوضات</div>
          </div>
          <div class="k-badge">2</div>
        </div>
        <div class="k-prog"><div class="k-prog-fill" style="background:#6F42C1"></div></div>
      </div>
      <div class="k-leads pipeline-leads">

        <div class="lead-card lc-high" draggable="true" data-lead="6" onclick="viewLead(6)">
          <div class="lc-actions">
            <button class="lc-act-btn lca-edit" onclick="event.stopPropagation();editLead(6)" title="تعديل"><i class="fas fa-pen"></i></button>
            <button class="lc-act-btn lca-del"  onclick="event.stopPropagation();deleteLead(6)" title="حذف"><i class="fas fa-trash"></i></button>
          </div>
          <div class="lc-top">
            <div class="lc-name">عبدالعزيز فهد الرشيدي</div>
          </div>
          <div class="lc-company"><i class="far fa-building"></i>مجموعة الراجحي للعقارات</div>
          <div class="lc-badges">
            <span class="lc-badge lb-source"><i class="fas fa-envelope" style="font-size:8px"></i>إيميل</span>
            <span class="lc-badge lb-value">5,400,000 ر.س</span>
          </div>
          <div class="lc-foot">
            <div class="lc-avatar" style="background:linear-gradient(135deg,#6F42C1,#4F46E5)">ع</div>
            <div class="lc-time"><i class="far fa-clock"></i>منذ أسبوع</div>
          </div>
        </div>

        <div class="lead-card lc-medium" draggable="true" data-lead="12" onclick="viewLead(12)">
          <div class="lc-actions">
            <button class="lc-act-btn lca-edit" onclick="event.stopPropagation();editLead(12)" title="تعديل"><i class="fas fa-pen"></i></button>
            <button class="lc-act-btn lca-del"  onclick="event.stopPropagation();deleteLead(12)" title="حذف"><i class="fas fa-trash"></i></button>
          </div>
          <div class="lc-top">
            <div class="lc-name">هند علي الدوسري</div>
          </div>
          <div class="lc-badges">
            <span class="lc-badge lb-source"><i class="fas fa-hashtag" style="font-size:8px"></i>تواصل اجتماعي</span>
            <span class="lc-badge lb-value">1,800,000 ر.س</span>
          </div>
          <div class="lc-foot">
            <div class="lc-avatar" style="background:linear-gradient(135deg,#DB2777,#6F42C1)">هـ</div>
            <div class="lc-time"><i class="far fa-clock"></i>منذ 5 أيام</div>
          </div>
        </div>

      </div>
      <div class="k-foot">
        <button class="k-add-btn" onclick="addToStatus(3)"><i class="fas fa-plus"></i>إضافة بطاقة</button>
      </div>
    </div>

    <!-- COL 4 — عرض مقدم -->
    <div class="k-col" data-status="4">
      <div class="k-head">
        <div class="k-head-top">
          <div class="k-head-left">
            <div class="k-dot" style="background:#0DCAF0;box-shadow:0 0 0 3px rgba(13,202,240,.15)"></div>
            <div class="k-name">عرض مقدم</div>
          </div>
          <div class="k-badge">1</div>
        </div>
        <div class="k-prog"><div class="k-prog-fill" style="background:#0DCAF0"></div></div>
      </div>
      <div class="k-leads pipeline-leads">

        <div class="lead-card lc-high" draggable="true" data-lead="7" onclick="viewLead(7)">
          <div class="lc-actions">
            <button class="lc-act-btn lca-edit" onclick="event.stopPropagation();editLead(7)" title="تعديل"><i class="fas fa-pen"></i></button>
            <button class="lc-act-btn lca-del"  onclick="event.stopPropagation();deleteLead(7)" title="حذف"><i class="fas fa-trash"></i></button>
          </div>
          <div class="lc-top">
            <div class="lc-name">وليد حسن المالكي</div>
          </div>
          <div class="lc-company"><i class="far fa-building"></i>شركة بناء المستقبل</div>
          <div class="lc-badges">
            <span class="lc-badge lb-source"><i class="fas fa-user-friends" style="font-size:8px"></i>توصية</span>
            <span class="lc-badge lb-value">3,200,000 ر.س</span>
          </div>
          <div class="lc-foot">
            <div class="lc-avatar" style="background:linear-gradient(135deg,#0891B2,#0D6EFD)">و</div>
            <div class="lc-time"><i class="far fa-clock"></i>منذ 10 أيام</div>
          </div>
        </div>

      </div>
      <div class="k-foot">
        <button class="k-add-btn" onclick="addToStatus(4)"><i class="fas fa-plus"></i>إضافة بطاقة</button>
      </div>
    </div>

    <!-- COL 5 — محوّل -->
    <div class="k-col" data-status="5">
      <div class="k-head">
        <div class="k-head-top">
          <div class="k-head-left">
            <div class="k-dot" style="background:#198754;box-shadow:0 0 0 3px rgba(25,135,84,.15)"></div>
            <div class="k-name">محوّل ✓</div>
          </div>
          <div class="k-badge">2</div>
        </div>
        <div class="k-prog"><div class="k-prog-fill" style="background:#198754"></div></div>
      </div>
      <div class="k-leads pipeline-leads">

        <div class="lead-card lc-low" draggable="true" data-lead="8" onclick="viewLead(8)" style="border-color:rgba(25,135,84,.2);background:rgba(25,135,84,.02)">
          <div class="lc-actions">
            <button class="lc-act-btn lca-edit" onclick="event.stopPropagation();editLead(8)" title="تعديل"><i class="fas fa-pen"></i></button>
            <button class="lc-act-btn lca-del"  onclick="event.stopPropagation();deleteLead(8)" title="حذف"><i class="fas fa-trash"></i></button>
          </div>
          <div class="lc-top">
            <div class="lc-name">فارس علي الغامدي</div>
          </div>
          <div class="lc-company"><i class="far fa-building"></i>مؤسسة الفارس للتطوير</div>
          <div class="lc-badges">
            <span class="lc-badge" style="background:rgba(25,135,84,.1);color:var(--green);border-color:rgba(25,135,84,.2)"><i class="fas fa-check-circle" style="font-size:8px"></i>محوّل</span>
            <span class="lc-badge lb-value">2,600,000 ر.س</span>
          </div>
          <div class="lc-foot">
            <div class="lc-avatar" style="background:linear-gradient(135deg,#198754,#0891B2)">ف</div>
            <div class="lc-time"><i class="far fa-clock"></i>منذ أسبوعين</div>
          </div>
        </div>

        <div class="lead-card lc-low" draggable="true" data-lead="13" onclick="viewLead(13)" style="border-color:rgba(25,135,84,.2);background:rgba(25,135,84,.02)">
          <div class="lc-actions">
            <button class="lc-act-btn lca-edit" onclick="event.stopPropagation();editLead(13)" title="تعديل"><i class="fas fa-pen"></i></button>
            <button class="lc-act-btn lca-del"  onclick="event.stopPropagation();deleteLead(13)" title="حذف"><i class="fas fa-trash"></i></button>
          </div>
          <div class="lc-top">
            <div class="lc-name">لطيفة ناصر البلوي</div>
          </div>
          <div class="lc-badges">
            <span class="lc-badge" style="background:rgba(25,135,84,.1);color:var(--green);border-color:rgba(25,135,84,.2)"><i class="fas fa-check-circle" style="font-size:8px"></i>محوّل</span>
            <span class="lc-badge lb-value">1,450,000 ر.س</span>
          </div>
          <div class="lc-foot">
            <div class="lc-avatar" style="background:linear-gradient(135deg,#198754,#6F42C1)">ل</div>
            <div class="lc-time"><i class="far fa-clock"></i>منذ شهر</div>
          </div>
        </div>

      </div>
      <div class="k-foot">
        <button class="k-add-btn" onclick="addToStatus(5)"><i class="fas fa-plus"></i>إضافة بطاقة</button>
      </div>
    </div>

    <!-- COL 6 — خسارة -->
    <div class="k-col" data-status="6">
      <div class="k-head">
        <div class="k-head-top">
          <div class="k-head-left">
            <div class="k-dot" style="background:#DC3545;box-shadow:0 0 0 3px rgba(220,53,69,.15)"></div>
            <div class="k-name">خسارة</div>
          </div>
          <div class="k-badge">0</div>
        </div>
        <div class="k-prog"><div class="k-prog-fill" style="background:#DC3545"></div></div>
      </div>
      <div class="k-leads pipeline-leads">
        <div class="k-empty">
          <i class="fas fa-inbox"></i>
          <div class="k-empty-txt">لا توجد بطاقات</div>
          <button class="k-empty-btn" onclick="addToStatus(6)"><i class="fas fa-plus"></i>إضافة</button>
        </div>
      </div>
      <div class="k-foot">
        <button class="k-add-btn" onclick="addToStatus(6)"><i class="fas fa-plus"></i>إضافة بطاقة</button>
      </div>
    </div>

  </div><!-- /pip-board -->
</div><!-- /pipeline-wrapper -->

</div><!-- /pipeline-page-wrapper -->
@endsection

@push('scripts')
<script>
/* ═══ DRAG & DROP ═══ */
let dragCard=null;
document.querySelectorAll('.lead-card').forEach(card=>{
  card.addEventListener('dragstart',e=>{
    dragCard=card;
    card.classList.add('dragging');
    e.dataTransfer.effectAllowed='move';
    e.dataTransfer.setData('text/plain',card.dataset.lead);
  });
  card.addEventListener('dragend',()=>{
    card.classList.remove('dragging');
    document.querySelectorAll('.k-col').forEach(c=>c.classList.remove('drag-over'));
    dragCard=null;
  });
});

document.querySelectorAll('.k-leads').forEach(zone=>{
  zone.addEventListener('dragover',e=>{
    e.preventDefault();
    e.dataTransfer.dropEffect='move';
    zone.closest('.k-col').classList.add('drag-over');
  });
  zone.addEventListener('dragleave',e=>{
    if(!zone.contains(e.relatedTarget))
      zone.closest('.k-col').classList.remove('drag-over');
  });
  zone.addEventListener('drop',e=>{
    e.preventDefault();
    zone.closest('.k-col').classList.remove('drag-over');
    if(dragCard&&!zone.contains(dragCard)){
      // Remove empty state if present
      const empty=zone.querySelector('.k-empty');
      if(empty)empty.remove();
      zone.appendChild(dragCard);
      updateColCounts();
      showNotif('تم نقل البطاقة بنجاح','success');
    }
  });
});

function updateColCounts(){
  document.querySelectorAll('.k-col').forEach(col=>{
    const count=col.querySelectorAll('.lead-card').length;
    col.querySelector('.k-badge').textContent=count;
    // Show empty if no cards
    const leads=col.querySelector('.k-leads');
    if(count===0&&!leads.querySelector('.k-empty')){
      const statusId=col.dataset.status;
      leads.innerHTML=`<div class="k-empty"><i class="fas fa-inbox"></i><div class="k-empty-txt">لا توجد بطاقات</div><button class="k-empty-btn" onclick="addToStatus(${statusId})"><i class="fas fa-plus"></i>إضافة</button></div>`;
    }
  });
}

/* ═══ SEARCH FILTER ═══ */
function filterCards(){
  const q=document.getElementById('pip-search-inp').value.toLowerCase();
  document.querySelectorAll('.lead-card').forEach(c=>{
    const txt=c.textContent.toLowerCase();
    c.style.display=(!q||txt.includes(q))?'':'none';
  });
}

/* ═══ ACTIONS ═══ */
function refreshPipeline(){
  const btn=document.querySelector('.hbtn-ghost');
  if(btn){btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> جاري...';btn.disabled=true;}
  showNotif('جاري التحديث...','info');
  setTimeout(()=>{
    if(btn){btn.innerHTML='<i class="fas fa-sync-alt"></i> تحديث';btn.disabled=false;}
    showNotif('تم التحديث بنجاح','success');
  },1400);
}
function viewLead(id){showNotif(`فتح بيانات العميل #${id}`,'info');}
function editLead(id){showNotif(`تعديل العميل #${id}`,'info');}
function deleteLead(id){
  if(confirm('هل أنت متأكد من حذف هذا العميل؟')){
    const card=document.querySelector(`.lead-card[data-lead="${id}"]`);
    if(card){card.style.transition='opacity .3s';card.style.opacity='0';setTimeout(()=>{card.remove();updateColCounts();},300);}
    showNotif('تم حذف العميل','success');
  }
}
function addToStatus(id){showNotif(`إضافة عميل جديد للمرحلة #${id}`,'info');}

/* ═══ NOTIFICATION ═══ */
let ntTimer;
function showNotif(msg,type='success'){
  const n=document.getElementById('notif');
  const icons={success:'check-circle',info:'info-circle',error:'exclamation-circle'};
  n.className=`notif ${type} show`;
  n.innerHTML=`<i class="fas fa-${icons[type]}"></i><span>${msg}</span>`;
  clearTimeout(ntTimer);
  ntTimer=setTimeout(()=>n.classList.remove('show'),3000);
}
</script>
@endpush