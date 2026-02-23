@extends('layouts.app')

@section('title', 'قوالب الوثائق')

@push('styles')
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --blue:#0D6EFD; --blue-g:linear-gradient(135deg,#0D6EFD,#0a58ca);
  --purple:#6F42C1; --green:#198754; --orange:#FD7E14;
  --red:#DC3545; --yellow:#FFC107; --teal:#0DCAF0;
  --bg:#F0F4FF; --card:#fff; --card2:#F8FAFF;
  --bd:#E2E8F0; --bd2:#CBD5E1; --bd-blue:rgba(13,110,253,.25);
  --t1:#1E293B; --t2:#475569; --t3:#64748B; --t4:#94A3B8;
  --r1:10px; --r2:14px; --r3:18px; --r4:24px;
  --sh:0 1px 4px rgba(0,0,0,.06),0 2px 10px rgba(0,0,0,.04);
  --sh-md:0 4px 20px rgba(0,0,0,.09),0 2px 8px rgba(0,0,0,.05);
  --sh-lg:0 10px 40px rgba(0,0,0,.11);
  --tr:all .25s cubic-bezier(.4,0,.2,1);
  --tr-sp:all .35s cubic-bezier(.34,1.3,.64,1);
  --ff:'Cairo',system-ui,sans-serif;
}
body{font-family:var(--ff);background:var(--bg);color:var(--t1);direction:rtl;min-height:100vh;}
body::before{
  content:'';position:fixed;inset:0;pointer-events:none;z-index:0;
  background:
    radial-gradient(ellipse 55% 35% at 85% 5%,rgba(13,110,253,.05) 0%,transparent 55%),
    radial-gradient(ellipse 45% 45% at 5% 90%,rgba(111,66,193,.04) 0%,transparent 55%);
}

/* ═══ PAGE HEADER ═══ */
.page-header{
  background:linear-gradient(140deg,#1E3A8A 0%,#1D4ED8 55%,#4F46E5 100%);
  padding:0 32px; position:relative; overflow:hidden;
}
.page-header::before{
  content:'';position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),
    linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);
  background-size:40px 40px;
}
.page-header::after{
  content:'';position:absolute;bottom:-2px;left:0;right:0;height:44px;
  background:var(--bg); clip-path:ellipse(52% 100% at 50% 100%);
}
.ph-inner{
  position:relative;z-index:1;
  max-width:1400px;margin:0 auto;
  display:flex;align-items:center;justify-content:space-between;
  gap:20px;flex-wrap:wrap;padding:34px 0 56px;
}
.ph-eyebrow{
  font-size:10px;font-weight:700;letter-spacing:.22em;
  text-transform:uppercase;color:rgba(255,255,255,.6);
  display:flex;align-items:center;gap:8px;margin-bottom:7px;
}
.ph-eyebrow::before{content:'';display:inline-block;width:18px;height:1.5px;background:rgba(255,255,255,.45);border-radius:2px;}
.ph-title{
  display:flex;align-items:center;gap:14px;
  font-size:clamp(21px,3vw,32px);font-weight:900;color:#fff;line-height:1.2;
}
.ph-title-ic{
  width:48px;height:48px;flex-shrink:0;
  background:rgba(255,255,255,.14);border:1.5px solid rgba(255,255,255,.22);
  border-radius:var(--r2);display:flex;align-items:center;justify-content:center;
  font-size:19px;box-shadow:0 4px 16px rgba(0,0,0,.2);
}
.ph-sub{font-size:13px;color:rgba(255,255,255,.7);margin-top:7px;margin-right:62px;}
.ph-actions{display:flex;gap:10px;flex-shrink:0;}
.hbtn{
  display:inline-flex;align-items:center;gap:8px;
  padding:11px 22px;border-radius:var(--r2);
  font-family:var(--ff);font-size:13.5px;font-weight:700;
  cursor:pointer;border:none;transition:var(--tr-sp);
  text-decoration:none;white-space:nowrap;
}
.hbtn-green{
  background:linear-gradient(135deg,#198754,#146c43);color:#fff;
  box-shadow:0 4px 16px rgba(25,135,84,.3);
}
.hbtn-green:hover{transform:translateY(-3px);box-shadow:0 10px 26px rgba(25,135,84,.38);}

/* ═══ CONTENT ═══ */
.content{position:relative;z-index:1;max-width:1400px;margin:0 auto;padding:22px 32px 60px;}

/* ═══ SEARCH & FILTER PANEL ═══ */
.filter-panel{
  background:var(--card);border:1px solid var(--bd);
  border-radius:var(--r3);box-shadow:var(--sh);
  padding:20px 22px;margin-bottom:22px;
}
.filter-form{display:grid;grid-template-columns:1fr 1fr auto auto;gap:12px;align-items:end;}
.field label{display:block;font-size:11.5px;font-weight:700;color:var(--t3);margin-bottom:5px;letter-spacing:.04em;}
.field input,.field select{
  width:100%;padding:9px 13px;
  border:1.5px solid var(--bd);border-radius:var(--r1);
  font-family:var(--ff);font-size:13px;color:var(--t1);
  background:var(--card2);outline:none;
  transition:border-color .2s,box-shadow .2s;
}
.field input:focus,.field select:focus{
  border-color:var(--blue);
  box-shadow:0 0 0 3px rgba(13,110,253,.1);
}
.btn-search{
  padding:9px 20px;background:var(--blue-g);color:#fff;
  border:none;border-radius:var(--r1);
  font-family:var(--ff);font-size:13px;font-weight:700;
  cursor:pointer;display:flex;align-items:center;gap:7px;
  white-space:nowrap;transition:var(--tr-sp);
  box-shadow:0 4px 14px rgba(13,110,253,.25);
}
.btn-search:hover{transform:translateY(-2px);box-shadow:0 8px 22px rgba(13,110,253,.35);}
.btn-reset{
  padding:9px 16px;background:var(--bg);color:var(--t3);
  border:1.5px solid var(--bd);border-radius:var(--r1);
  font-family:var(--ff);font-size:13px;font-weight:700;
  cursor:pointer;display:flex;align-items:center;gap:7px;
  white-space:nowrap;transition:var(--tr);
}
.btn-reset:hover{border-color:var(--bd2);color:var(--t2);}

/* ═══ SECTION HEADER ═══ */
.sec-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;}
.sec-left{display:flex;align-items:center;gap:12px;}
.sec-accent{width:3px;height:24px;background:var(--blue-g);border-radius:2px;flex-shrink:0;}
.sec-title{font-size:17px;font-weight:800;color:var(--t1);}
.sec-count{
  font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;
  color:var(--t4);background:var(--card);padding:3px 11px;
  border-radius:20px;border:1px solid var(--bd);
}

/* ═══ TEMPLATES GRID ═══ */
.tpl-grid{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:16px;
}

/* ═══ TEMPLATE CARD ═══ */
.tpl-card{
  background:var(--card);border:1px solid var(--bd);
  border-radius:var(--r3);box-shadow:var(--sh);
  overflow:hidden;transition:var(--tr-sp);
  display:flex;flex-direction:column;
  position:relative;
}
.tpl-card::before{
  content:'';position:absolute;
  top:0;left:0;right:0;height:3px;
  opacity:0;transition:opacity .3s;
  border-radius:var(--r3) var(--r3) 0 0;
}
.tpl-card:hover{transform:translateY(-5px);box-shadow:var(--sh-lg);border-color:var(--bd2);}
.tpl-card:hover::before{opacity:1;}

/* card color variants */
.tpl-c-blue::before  {background:var(--blue-g);}
.tpl-c-blue:hover    {border-color:rgba(13,110,253,.2);}
.tpl-c-purple::before{background:linear-gradient(135deg,#6F42C1,#59359a);}
.tpl-c-purple:hover  {border-color:rgba(111,66,193,.2);}
.tpl-c-green::before {background:linear-gradient(135deg,#198754,#146c43);}
.tpl-c-green:hover   {border-color:rgba(25,135,84,.2);}
.tpl-c-orange::before{background:linear-gradient(135deg,#FD7E14,#dc6a0c);}
.tpl-c-orange:hover  {border-color:rgba(253,126,20,.2);}

/* Card body */
.tpl-body{padding:20px 20px 16px;flex:1;}

.tpl-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;gap:10px;}
.tpl-ic-wrap{display:flex;align-items:center;gap:12px;}
.tpl-ic{
  width:44px;height:44px;border-radius:var(--r2);
  display:flex;align-items:center;justify-content:center;
  font-size:17px;flex-shrink:0;border:1px solid;
  transition:transform .35s cubic-bezier(.34,1.3,.64,1);
}
.tpl-card:hover .tpl-ic{transform:scale(1.1) rotate(5deg);}
.ti-blue  {background:rgba(13,110,253,.1); color:var(--blue);   border-color:rgba(13,110,253,.2);}
.ti-purple{background:rgba(111,66,193,.1); color:var(--purple); border-color:rgba(111,66,193,.2);}
.ti-green {background:rgba(25,135,84,.1);  color:var(--green);  border-color:rgba(25,135,84,.2);}
.ti-orange{background:rgba(253,126,20,.1); color:var(--orange); border-color:rgba(253,126,20,.2);}

.tpl-name{font-size:14px;font-weight:800;color:var(--t1);line-height:1.3;margin-bottom:3px;}
.tpl-cat{
  font-size:10.5px;font-weight:700;
  color:var(--t4);letter-spacing:.06em;text-transform:uppercase;
}

/* Status badge */
.status-badge{
  display:inline-flex;align-items:center;gap:5px;
  padding:3px 10px;border-radius:20px;
  font-size:10.5px;font-weight:700;flex-shrink:0;
}
.sb-active  {background:rgba(25,135,84,.1);  color:var(--green); border:1px solid rgba(25,135,84,.2);}
.sb-inactive{background:rgba(100,116,139,.1);color:var(--t3);    border:1px solid var(--bd);}
.sb-dot{width:5px;height:5px;border-radius:50%;}
.sb-active .sb-dot  {background:var(--green);animation:sbping 2s ease-in-out infinite;}
.sb-inactive .sb-dot{background:var(--t4);}
@keyframes sbping{0%,100%{opacity:1}50%{opacity:.3}}

/* Description */
.tpl-desc{font-size:12.5px;color:var(--t3);line-height:1.65;margin-bottom:14px;}

/* Meta pills */
.tpl-meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:16px;}
.meta-pill{
  display:inline-flex;align-items:center;gap:5px;
  padding:4px 10px;border-radius:20px;
  font-size:11px;font-weight:600;color:var(--t3);
  background:var(--bg);border:1px solid var(--bd);
}
.meta-pill i{font-size:10px;color:var(--t4);}

/* Date */
.tpl-date{font-size:11px;color:var(--t4);display:flex;align-items:center;gap:5px;}

/* Card footer */
.tpl-foot{
  display:flex;align-items:center;justify-content:space-between;
  padding:12px 20px;border-top:1px solid var(--bd);
  background:var(--card2);
}
.tpl-actions{display:flex;align-items:center;gap:4px;}
.tpl-action-btn{
  width:32px;height:32px;border-radius:8px;
  display:flex;align-items:center;justify-content:center;
  font-size:13px;cursor:pointer;transition:var(--tr);
  text-decoration:none;border:1px solid transparent;
  background:transparent;font-family:var(--ff);
}
/* action button colors */
.tab-view  {color:var(--blue);  }
.tab-view:hover  {background:rgba(13,110,253,.1);border-color:rgba(13,110,253,.2);}
.tab-prev  {color:var(--green); }
.tab-prev:hover  {background:rgba(25,135,84,.1); border-color:rgba(25,135,84,.2);}
.tab-edit  {color:var(--orange);}
.tab-edit:hover  {background:rgba(253,126,20,.1);border-color:rgba(253,126,20,.2);}
.tab-copy  {color:var(--purple);}
.tab-copy:hover  {background:rgba(111,66,193,.1);border-color:rgba(111,66,193,.2);}
.tab-del   {color:var(--red);   }
.tab-del:hover   {background:rgba(220,53,69,.1); border-color:rgba(220,53,69,.2);}

/* Tooltip on hover */
.tpl-action-btn{position:relative;}
.tpl-action-btn::after{
  content:attr(data-tip);
  position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);
  background:#1E293B;color:#fff;font-size:10px;font-weight:600;
  padding:4px 8px;border-radius:6px;white-space:nowrap;
  opacity:0;pointer-events:none;transition:opacity .18s;
}
.tpl-action-btn:hover::after{opacity:1;}

/* ═══ EMPTY STATE ═══ */
.empty-state{
  grid-column:1/-1;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:72px 20px;text-align:center;
}
.empty-ic{
  width:80px;height:80px;border-radius:var(--r4);
  background:rgba(13,110,253,.07);border:2px dashed rgba(13,110,253,.2);
  display:flex;align-items:center;justify-content:center;
  font-size:30px;color:var(--t4);margin-bottom:18px;
}
.empty-title{font-size:17px;font-weight:800;color:var(--t2);margin-bottom:6px;}
.empty-sub{font-size:13px;color:var(--t4);margin-bottom:22px;}

/* ═══ PAGINATION ═══ */
.pagination{display:flex;align-items:center;justify-content:center;gap:6px;margin-top:28px;}
.pg-btn{
  width:36px;height:36px;border-radius:var(--r1);
  display:flex;align-items:center;justify-content:center;
  font-size:13px;font-weight:700;font-family:var(--ff);
  cursor:pointer;border:1.5px solid var(--bd);
  background:var(--card);color:var(--t3);transition:var(--tr);
  text-decoration:none;
}
.pg-btn:hover{border-color:var(--blue);color:var(--blue);background:rgba(13,110,253,.06);}
.pg-btn.active{background:var(--blue-g);color:#fff;border-color:var(--blue);box-shadow:0 4px 14px rgba(13,110,253,.25);}
.pg-btn.disabled{opacity:.4;pointer-events:none;}

/* ═══ CATEGORY LEGEND ═══ */
.cat-filter{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.cat-pill{
  display:inline-flex;align-items:center;gap:6px;
  padding:5px 14px;border-radius:30px;
  font-size:12px;font-weight:700;cursor:pointer;
  border:1.5px solid var(--bd);background:var(--card);
  color:var(--t3);transition:var(--tr);
}
.cat-pill:hover{border-color:rgba(13,110,253,.3);color:var(--blue);}
.cat-pill.active-all{background:var(--blue-g);color:#fff;border-color:var(--blue);}
.cat-pill.active-contract{background:linear-gradient(135deg,#0D6EFD,#0a58ca);color:#fff;border-color:var(--blue);}
.cat-pill.active-legal{background:linear-gradient(135deg,#6F42C1,#59359a);color:#fff;border-color:var(--purple);}
.cat-pill.active-financial{background:linear-gradient(135deg,#198754,#146c43);color:#fff;border-color:var(--green);}
.cat-pill.active-other{background:linear-gradient(135deg,#FD7E14,#dc6a0c);color:#fff;border-color:var(--orange);}

/* ═══ ANIMATIONS ═══ */
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.anim{opacity:0;animation:fadeUp .5s ease forwards;}
.a1{animation-delay:.04s}.a2{animation-delay:.08s}.a3{animation-delay:.12s}
.a4{animation-delay:.16s}.a5{animation-delay:.2s}.a6{animation-delay:.24s}
.a7{animation-delay:.28s}.a8{animation-delay:.32s}.a9{animation-delay:.36s}

::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-track{background:var(--bg)}
::-webkit-scrollbar-thumb{background:var(--bd2);border-radius:10px}
::-webkit-scrollbar-thumb:hover{background:var(--blue)}

/* RESPONSIVE */
@media(max-width:1100px){.tpl-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:768px){
  .tpl-grid{grid-template-columns:1fr}
  .filter-form{grid-template-columns:1fr 1fr}
  .content,.page-header{padding-left:16px;padding-right:16px}
  .ph-inner{padding:28px 0 52px}
}
@media(max-width:500px){.filter-form{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<!-- ═══ HEADER ═══ -->
<div class="page-header">
  <div class="ph-inner">
    <div>
      <div class="ph-eyebrow">الوثائق / Documents</div>
      <h1 class="ph-title">
        <div class="ph-title-ic"><i class="fas fa-file-alt"></i></div>
        قوالب الوثائق
      </h1>
      <p class="ph-sub">إدارة وتنظيم قوالب الوثائق والعقود</p>
    </div>
    <div class="ph-actions">
      <a href="{{ route('document-templates.create') }}" class="hbtn hbtn-green">
        <i class="fas fa-plus"></i> إنشاء قالب جديد
      </a>
    </div>
  </div>
</div>

<!-- ═══ CONTENT ═══ -->
<div class="content">

  <!-- FILTER PANEL -->
  <div class="filter-panel anim a1">
    <form class="filter-form" method="GET">
      <div class="field" style="grid-column:span 2">
        <label>البحث في القوالب</label>
        <input type="text" name="search" placeholder="ابحث عن اسم قالب أو وصفه...">
      </div>
      <div class="field">
        <label>الفئة</label>
        <select name="category">
          <option value="">جميع الفئات</option>
          <option value="contract">عقود</option>
          <option value="legal">قانوني</option>
          <option value="financial">مالي</option>
          <option value="other">أخرى</option>
        </select>
      </div>
      <div style="display:flex;gap:8px;align-items:flex-end">
        <button type="submit" class="btn-search" style="flex:1">
          <i class="fas fa-search"></i> بحث
        </button>
        <button type="button" class="btn-reset" onclick="location.href=location.pathname">
          <i class="fas fa-undo"></i>
        </button>
      </div>
    </form>
  </div>

  <!-- SECTION HEADER -->
  <div class="sec-head anim a2">
    <div class="sec-left">
      <div class="sec-accent"></div>
      <div class="sec-title">القوالب المتاحة</div>
      <div class="sec-count">6 قالب</div>
    </div>
    <div class="cat-filter">
      <span class="cat-pill active-all">الكل</span>
      <span class="cat-pill"><i class="fas fa-file-signature" style="font-size:10px;color:var(--blue)"></i>عقود</span>
      <span class="cat-pill"><i class="fas fa-gavel" style="font-size:10px;color:var(--purple)"></i>قانوني</span>
      <span class="cat-pill"><i class="fas fa-dollar-sign" style="font-size:10px;color:var(--green)"></i>مالي</span>
      <span class="cat-pill"><i class="fas fa-ellipsis-h" style="font-size:10px;color:var(--orange)"></i>أخرى</span>
    </div>
  </div>

  <!-- TEMPLATES GRID -->
  <div class="tpl-grid">

    <!-- Card 1 -->
    <div class="tpl-card tpl-c-blue anim a3">
      <div class="tpl-body">
        <div class="tpl-top">
          <div class="tpl-ic-wrap">
            <div class="tpl-ic ti-blue"><i class="fas fa-file-contract"></i></div>
            <div>
              <div class="tpl-name">عقد بيع عقار</div>
              <div class="tpl-cat">عقود</div>
            </div>
          </div>
          <span class="status-badge sb-active"><span class="sb-dot"></span>نشط</span>
        </div>
        <p class="tpl-desc">نموذج عقد بيع عقار شامل يتضمن جميع الشروط والأحكام القانونية المطلوبة لنقل ملكية العقار بشكل رسمي وقانوني.</p>
        <div class="tpl-meta">
          <span class="meta-pill"><i class="fas fa-layer-group"></i>3 إصدارات</span>
          <span class="meta-pill"><i class="fas fa-file"></i>12 مستند</span>
          <span class="meta-pill"><i class="fas fa-tag"></i>عقود</span>
        </div>
      </div>
      <div class="tpl-foot">
        <div class="tpl-date"><i class="far fa-calendar"></i>2024-01-15</div>
        <div class="tpl-actions">
          <a href="#" class="tpl-action-btn tab-view" data-tip="عرض"><i class="fas fa-eye"></i></a>
          <a href="#" class="tpl-action-btn tab-prev" data-tip="معاينة"><i class="fas fa-search"></i></a>
          <a href="#" class="tpl-action-btn tab-edit" data-tip="تعديل"><i class="fas fa-edit"></i></a>
          <button class="tpl-action-btn tab-copy" data-tip="نسخ"><i class="fas fa-copy"></i></button>
        </div>
      </div>
    </div>

    <!-- Card 2 -->
    <div class="tpl-card tpl-c-purple anim a4">
      <div class="tpl-body">
        <div class="tpl-top">
          <div class="tpl-ic-wrap">
            <div class="tpl-ic ti-purple"><i class="fas fa-gavel"></i></div>
            <div>
              <div class="tpl-name">توكيل رسمي</div>
              <div class="tpl-cat">قانوني</div>
            </div>
          </div>
          <span class="status-badge sb-active"><span class="sb-dot"></span>نشط</span>
        </div>
        <p class="tpl-desc">نموذج توكيل رسمي قانوني يُمكّن الطرف الموكِّل من تفويض صلاحياته لشخص آخر للقيام بالإجراءات القانونية نيابةً عنه.</p>
        <div class="tpl-meta">
          <span class="meta-pill"><i class="fas fa-layer-group"></i>2 إصدارات</span>
          <span class="meta-pill"><i class="fas fa-file"></i>8 مستندات</span>
          <span class="meta-pill"><i class="fas fa-tag"></i>قانوني</span>
        </div>
      </div>
      <div class="tpl-foot">
        <div class="tpl-date"><i class="far fa-calendar"></i>2024-01-10</div>
        <div class="tpl-actions">
          <a href="#" class="tpl-action-btn tab-view" data-tip="عرض"><i class="fas fa-eye"></i></a>
          <a href="#" class="tpl-action-btn tab-prev" data-tip="معاينة"><i class="fas fa-search"></i></a>
          <a href="#" class="tpl-action-btn tab-edit" data-tip="تعديل"><i class="fas fa-edit"></i></a>
          <button class="tpl-action-btn tab-copy" data-tip="نسخ"><i class="fas fa-copy"></i></button>
        </div>
      </div>
    </div>

    <!-- Card 3 -->
    <div class="tpl-card tpl-c-green anim a5">
      <div class="tpl-body">
        <div class="tpl-top">
          <div class="tpl-ic-wrap">
            <div class="tpl-ic ti-green"><i class="fas fa-file-invoice-dollar"></i></div>
            <div>
              <div class="tpl-name">عقد إيجار سكني</div>
              <div class="tpl-cat">مالي</div>
            </div>
          </div>
          <span class="status-badge sb-active"><span class="sb-dot"></span>نشط</span>
        </div>
        <p class="tpl-desc">نموذج عقد إيجار سكني متكامل يتضمن حقوق وواجبات المالك والمستأجر، مع بنود واضحة للمدة والأجرة وشروط الإنهاء.</p>
        <div class="tpl-meta">
          <span class="meta-pill"><i class="fas fa-layer-group"></i>5 إصدارات</span>
          <span class="meta-pill"><i class="fas fa-file"></i>24 مستند</span>
          <span class="meta-pill"><i class="fas fa-tag"></i>مالي</span>
        </div>
      </div>
      <div class="tpl-foot">
        <div class="tpl-date"><i class="far fa-calendar"></i>2024-01-08</div>
        <div class="tpl-actions">
          <a href="#" class="tpl-action-btn tab-view" data-tip="عرض"><i class="fas fa-eye"></i></a>
          <a href="#" class="tpl-action-btn tab-prev" data-tip="معاينة"><i class="fas fa-search"></i></a>
          <a href="#" class="tpl-action-btn tab-edit" data-tip="تعديل"><i class="fas fa-edit"></i></a>
          <button class="tpl-action-btn tab-copy" data-tip="نسخ"><i class="fas fa-copy"></i></button>
        </div>
      </div>
    </div>

    <!-- Card 4 -->
    <div class="tpl-card tpl-c-orange anim a6">
      <div class="tpl-body">
        <div class="tpl-top">
          <div class="tpl-ic-wrap">
            <div class="tpl-ic ti-orange"><i class="fas fa-handshake"></i></div>
            <div>
              <div class="tpl-name">اتفاقية شراكة</div>
              <div class="tpl-cat">عقود</div>
            </div>
          </div>
          <span class="status-badge sb-inactive"><span class="sb-dot"></span>غير نشط</span>
        </div>
        <p class="tpl-desc">نموذج اتفاقية شراكة تجارية يُنظّم العلاقة بين الشركاء، ويحدد حصص الملكية والمسؤوليات وآلية توزيع الأرباح والخسائر.</p>
        <div class="tpl-meta">
          <span class="meta-pill"><i class="fas fa-layer-group"></i>1 إصدار</span>
          <span class="meta-pill"><i class="fas fa-file"></i>3 مستندات</span>
          <span class="meta-pill"><i class="fas fa-tag"></i>عقود</span>
        </div>
      </div>
      <div class="tpl-foot">
        <div class="tpl-date"><i class="far fa-calendar"></i>2023-12-20</div>
        <div class="tpl-actions">
          <a href="#" class="tpl-action-btn tab-view" data-tip="عرض"><i class="fas fa-eye"></i></a>
          <a href="#" class="tpl-action-btn tab-prev" data-tip="معاينة"><i class="fas fa-search"></i></a>
          <a href="#" class="tpl-action-btn tab-edit" data-tip="تعديل"><i class="fas fa-edit"></i></a>
          <button class="tpl-action-btn tab-copy" data-tip="نسخ"><i class="fas fa-copy"></i></button>
        </div>
      </div>
    </div>

    <!-- Card 5 -->
    <div class="tpl-card tpl-c-blue anim a7">
      <div class="tpl-body">
        <div class="tpl-top">
          <div class="tpl-ic-wrap">
            <div class="tpl-ic ti-blue"><i class="fas fa-building"></i></div>
            <div>
              <div class="tpl-name">عقد تطوير عقاري</div>
              <div class="tpl-cat">عقود</div>
            </div>
          </div>
          <span class="status-badge sb-active"><span class="sb-dot"></span>نشط</span>
        </div>
        <p class="tpl-desc">نموذج عقد تطوير عقاري متكامل يُنظّم عملية بناء وتطوير المشاريع العقارية، مع جداول زمنية ومعايير جودة واضحة.</p>
        <div class="tpl-meta">
          <span class="meta-pill"><i class="fas fa-layer-group"></i>4 إصدارات</span>
          <span class="meta-pill"><i class="fas fa-file"></i>17 مستند</span>
          <span class="meta-pill"><i class="fas fa-tag"></i>عقود</span>
        </div>
      </div>
      <div class="tpl-foot">
        <div class="tpl-date"><i class="far fa-calendar"></i>2024-01-02</div>
        <div class="tpl-actions">
          <a href="#" class="tpl-action-btn tab-view" data-tip="عرض"><i class="fas fa-eye"></i></a>
          <a href="#" class="tpl-action-btn tab-prev" data-tip="معاينة"><i class="fas fa-search"></i></a>
          <a href="#" class="tpl-action-btn tab-edit" data-tip="تعديل"><i class="fas fa-edit"></i></a>
          <button class="tpl-action-btn tab-copy" data-tip="نسخ"><i class="fas fa-copy"></i></button>
        </div>
      </div>
    </div>

    <!-- Card 6 -->
    <div class="tpl-card tpl-c-purple anim a8">
      <div class="tpl-body">
        <div class="tpl-top">
          <div class="tpl-ic-wrap">
            <div class="tpl-ic ti-purple"><i class="fas fa-balance-scale"></i></div>
            <div>
              <div class="tpl-name">اتفاقية سرية</div>
              <div class="tpl-cat">قانوني</div>
            </div>
          </div>
          <span class="status-badge sb-active"><span class="sb-dot"></span>نشط</span>
        </div>
        <p class="tpl-desc">نموذج اتفاقية عدم إفصاح وسرية المعلومات (NDA) يحمي المعلومات التجارية الحساسة المتبادلة بين الأطراف المتعاقدة.</p>
        <div class="tpl-meta">
          <span class="meta-pill"><i class="fas fa-layer-group"></i>2 إصدارات</span>
          <span class="meta-pill"><i class="fas fa-file"></i>9 مستندات</span>
          <span class="meta-pill"><i class="fas fa-tag"></i>قانوني</span>
        </div>
      </div>
      <div class="tpl-foot">
        <div class="tpl-date"><i class="far fa-calendar"></i>2024-01-05</div>
        <div class="tpl-actions">
          <a href="#" class="tpl-action-btn tab-view" data-tip="عرض"><i class="fas fa-eye"></i></a>
          <a href="#" class="tpl-action-btn tab-prev" data-tip="معاينة"><i class="fas fa-search"></i></a>
          <a href="#" class="tpl-action-btn tab-edit" data-tip="تعديل"><i class="fas fa-edit"></i></a>
          <button class="tpl-action-btn tab-copy" data-tip="نسخ"><i class="fas fa-copy"></i></button>
        </div>
      </div>
    </div>

  </div><!-- /tpl-grid -->

  <!-- PAGINATION -->
  <div class="pagination anim a9">
    <a href="#" class="pg-btn disabled"><i class="fas fa-chevron-right"></i></a>
    <a href="#" class="pg-btn active">1</a>
    <a href="#" class="pg-btn">2</a>
    <a href="#" class="pg-btn">3</a>
    <span style="color:var(--t4);font-size:13px;font-weight:600;padding:0 4px">···</span>
    <a href="#" class="pg-btn">8</a>
    <a href="#" class="pg-btn"><i class="fas fa-chevron-left"></i></a>
  </div>

</div><!-- /content -->

@endsection

@push('scripts')
<script>
// Category filter pills
document.querySelectorAll('.cat-pill').forEach(pill=>{
  pill.addEventListener('click',()=>{
    document.querySelectorAll('.cat-pill').forEach(p=>{
      p.className='cat-pill';
    });
    // Set active class based on text
    const txt=pill.textContent.trim();
    const map={'الكل':'active-all','عقود':'active-contract','قانوني':'active-legal','مالي':'active-financial','أخرى':'active-other'};
    for(const [k,v] of Object.entries(map)){
      if(txt.includes(k)){pill.classList.add(v);break;}
    }
  });
});
</script>
@endpush