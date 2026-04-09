@extends('layouts.app')

@section('title', 'Properties Management')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,400;0,600;1,400;1,600&display=swap" rel="stylesheet">

<style>
/* ═══════════════════════════════════════════
   ROOT TOKENS  — الألوان من الكود الأصلي
═══════════════════════════════════════════ */
:root {
  --blue:       #2563eb;   /* blue-600 */
  --blue-dark:  #1d4ed8;   /* blue-700 */
  --blue-light: #3b82f6;   /* blue-500 */
  --blue-xlight:#eff6ff;   /* blue-50  */
  --blue-100:   #dbeafe;
  --blue-200:   #bfdbfe;

  --gray-50:    #f9fafb;
  --gray-100:   #f3f4f6;
  --gray-200:   #e5e7eb;
  --gray-300:   #d1d5db;
  --gray-400:   #9ca3af;
  --gray-500:   #6b7280;
  --gray-600:   #4b5563;
  --gray-700:   #374151;
  --gray-800:   #1f2937;
  --gray-900:   #111827;

  --green-100:  #dcfce7;
  --green-700:  #15803d;
  --yellow-100: #fef9c3;
  --yellow-700: #a16207;
  --red-100:    #fee2e2;
  --red-700:    #b91c1c;
  --indigo-100: #e0e7ff;
  --indigo-700: #4338ca;

  --white: #ffffff;
  --sidebar-w: 310px;
  --radius: 12px;
  --radius-sm: 8px;
  --shadow-sm: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.06);
  --shadow-md: 0 4px 16px rgba(0,0,0,.10);
  --shadow-lg: 0 10px 40px rgba(0,0,0,.14);
  --shadow-blue: 0 8px 32px rgba(37,99,235,.25);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.pm-wrap {
  font-family: 'Plus Jakarta Sans', sans-serif;
  background: var(--gray-50);
  min-height: 100vh;
  color: var(--gray-900);
}

/* ═══════════════════════════════════════════
   HERO HEADER
═══════════════════════════════════════════ */
.pm-hero {
  position: relative;
  background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 40%, #2563eb 70%, #3b82f6 100%);
  padding: 0;
  overflow: hidden;
}

/* geometric SVG shapes */
.pm-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(circle at 80% 20%, rgba(255,255,255,.12) 0%, transparent 50%),
    radial-gradient(circle at 10% 80%, rgba(255,255,255,.08) 0%, transparent 40%);
  pointer-events: none;
}
.pm-hero::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  right: 0;
  height: 60px;
  background: var(--gray-50);
  clip-path: ellipse(55% 100% at 50% 100%);
}

.pm-hero__shapes {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
}
.pm-hero__shapes svg {
  position: absolute;
}
.shape-1 { top: -40px; right: -40px; width: 400px; opacity: .06; }
.shape-2 { bottom: 20px; left: -60px; width: 300px; opacity: .05; }
.shape-3 { top: 50%; left: 40%; transform: translate(-50%,-50%); width: 600px; opacity: .04; }

.pm-hero__inner {
  position: relative;
  max-width: 1600px;
  margin: 0 auto;
  padding: 52px 48px 96px;
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 40px;
  align-items: start;
}

.pm-hero__left { }

.pm-hero__breadcrumb {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: rgba(255,255,255,.6);
  letter-spacing: .04em;
  margin-bottom: 20px;
}
.pm-hero__breadcrumb i { font-size: 10px; }
.pm-hero__breadcrumb a { color: rgba(255,255,255,.6); text-decoration: none; }
.pm-hero__breadcrumb a:hover { color: #fff; }
.pm-hero__breadcrumb span { color: rgba(255,255,255,.9); }

.pm-hero__title {
  font-family: 'Lora', serif;
  font-size: clamp(32px, 3.5vw, 54px);
  font-weight: 600;
  color: #ffffff;
  line-height: 1.12;
  margin-bottom: 10px;
  letter-spacing: -.02em;
}
.pm-hero__title em {
  font-style: italic;
  color: rgba(255,255,255,.85);
  font-weight: 400;
}

.pm-hero__sub {
  font-size: 15px;
  color: rgba(255,255,255,.65);
  font-weight: 400;
  line-height: 1.6;
  max-width: 520px;
  margin-bottom: 36px;
}

/* ── BIG Search Bar ── */
.pm-search-bar {
  display: flex;
  align-items: stretch;
  background: rgba(255,255,255,.15);
  backdrop-filter: blur(16px);
  border: 1.5px solid rgba(255,255,255,.3);
  border-radius: var(--radius);
  overflow: hidden;
  max-width: 680px;
  transition: border-color .2s, background .2s;
  box-shadow: 0 8px 32px rgba(0,0,0,.15);
}
.pm-search-bar:focus-within {
  border-color: rgba(255,255,255,.7);
  background: rgba(255,255,255,.2);
}

.pm-search-bar__icon {
  display: flex;
  align-items: center;
  padding: 0 20px;
  color: rgba(255,255,255,.7);
  font-size: 16px;
  flex-shrink: 0;
}
.pm-search-bar input {
  flex: 1;
  background: transparent;
  border: none;
  outline: none;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 15px;
  color: #ffffff;
  padding: 18px 12px;
  caret-color: #fff;
}
.pm-search-bar input::placeholder { color: rgba(255,255,255,.5); }

.pm-search-bar__divider {
  width: 1px;
  background: rgba(255,255,255,.25);
  margin: 12px 0;
}

.pm-search-bar__select {
  background: transparent;
  border: none;
  outline: none;
  color: rgba(255,255,255,.8);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 13px;
  padding: 0 20px;
  cursor: pointer;
  appearance: none;
  min-width: 120px;
}
.pm-search-bar__select option { background: var(--blue-dark); color: #fff; }

.pm-search-bar__btn {
  background: #ffffff;
  border: none;
  color: var(--blue-dark);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: .04em;
  padding: 0 32px;
  cursor: pointer;
  transition: background .2s;
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
  flex-shrink: 0;
  border-radius: 0 calc(var(--radius) - 2px) calc(var(--radius) - 2px) 0;
}
.pm-search-bar__btn:hover { background: var(--blue-xlight); }

/* ── Quick Filter Tabs ── */
.pm-hero__tabs {
  display: flex;
  gap: 8px;
  margin-top: 20px;
  flex-wrap: wrap;
}
.pm-hero__tab {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 7px 16px;
  background: rgba(255,255,255,.12);
  border: 1px solid rgba(255,255,255,.2);
  border-radius: 50px;
  color: rgba(255,255,255,.75);
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all .2s;
  font-family: 'Plus Jakarta Sans', sans-serif;
  letter-spacing: .02em;
}
.pm-hero__tab:hover, .pm-hero__tab.active {
  background: rgba(255,255,255,.25);
  border-color: rgba(255,255,255,.5);
  color: #fff;
}
.pm-hero__tab i { font-size: 11px; }

/* ── Stats (right side) ── */
.pm-hero__stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  padding-top: 68px;
}
.pm-hero__stat {
  background: rgba(255,255,255,.12);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,.2);
  border-radius: var(--radius);
  padding: 20px 22px;
  text-align: center;
  min-width: 110px;
}
.pm-hero__stat-val {
  font-family: 'Lora', serif;
  font-size: 32px;
  font-weight: 600;
  color: #fff;
  line-height: 1;
  margin-bottom: 4px;
}
.pm-hero__stat-val sup { font-size: 16px; vertical-align: super; }
.pm-hero__stat-lbl {
  font-size: 11px;
  color: rgba(255,255,255,.6);
  letter-spacing: .08em;
  text-transform: uppercase;
}

/* Add button */
.pm-add-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: #ffffff;
  color: var(--blue-dark);
  border: none;
  border-radius: var(--radius-sm);
  padding: 14px 28px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  text-decoration: none;
  box-shadow: var(--shadow-blue);
  transition: transform .2s, box-shadow .2s;
  white-space: nowrap;
  margin-top: 68px;
}
.pm-add-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(37,99,235,.35); }
.pm-add-btn i {
  width: 28px; height: 28px;
  background: var(--blue);
  color: #fff;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  flex-shrink: 0;
}

/* ═══════════════════════════════════════════
   MAIN LAYOUT
═══════════════════════════════════════════ */
.pm-layout {
  max-width: 1600px;
  margin: 0 auto;
  padding: 36px 48px 60px;
  display: flex;
  gap: 28px;
  align-items: flex-start;
}

/* ═══════════════════════════════════════════
   SIDEBAR
═══════════════════════════════════════════ */
.pm-sidebar {
  width: var(--sidebar-w);
  flex-shrink: 0;
  position: sticky;
  top: 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.sb-card {
  background: var(--white);
  border-radius: var(--radius);
  border: 1px solid var(--gray-200);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}

.sb-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  cursor: pointer;
  user-select: none;
  border-bottom: 1px solid var(--gray-100);
  transition: background .15s;
}
.sb-head:hover { background: var(--gray-50); }
.sb-head__label {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
  font-weight: 700;
  color: var(--gray-800);
  letter-spacing: .01em;
}
.sb-head__label-icon {
  width: 28px; height: 28px;
  background: var(--blue-xlight);
  border-radius: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--blue);
  font-size: 12px;
  flex-shrink: 0;
}
.sb-head__chevron {
  color: var(--gray-400);
  font-size: 11px;
  transition: transform .25s;
}
.sb-head.collapsed .sb-head__chevron { transform: rotate(-90deg); }

.sb-body { padding: 20px; }

/* Price Range Slider */
.price-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 18px;
  gap: 8px;
}
.price-input-wrap {
  flex: 1;
  background: var(--gray-50);
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius-sm);
  padding: 8px 12px;
  transition: border-color .2s;
}
.price-input-wrap:focus-within { border-color: var(--blue); }
.price-input-wrap label {
  display: block;
  font-size: 10px;
  font-weight: 600;
  color: var(--gray-400);
  letter-spacing: .1em;
  text-transform: uppercase;
  margin-bottom: 3px;
}
.price-input-wrap input {
  width: 100%;
  background: transparent;
  border: none;
  outline: none;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 15px;
  font-weight: 700;
  color: var(--gray-800);
}
.price-sep { font-size: 18px; color: var(--gray-300); padding-bottom: 8px; flex-shrink: 0; }

.dual-range {
  position: relative;
  height: 6px;
  background: var(--gray-200);
  border-radius: 3px;
  margin: 0 4px 8px;
}
.dual-range__fill {
  position: absolute;
  height: 100%;
  background: linear-gradient(90deg, var(--blue-light), var(--blue));
  border-radius: 3px;
  left: 0%;
  right: 0%;
  transition: left .05s, right .05s;
}
.dual-range input[type=range] {
  position: absolute;
  width: 100%;
  top: 50%;
  transform: translateY(-50%);
  appearance: none;
  background: transparent;
  height: 20px;
  cursor: pointer;
  pointer-events: none;
}
.dual-range input[type=range]::-webkit-slider-thumb {
  appearance: none;
  width: 18px; height: 18px;
  border-radius: 50%;
  background: var(--white);
  border: 2.5px solid var(--blue);
  box-shadow: 0 2px 8px rgba(37,99,235,.3);
  cursor: grab;
  pointer-events: all;
  transition: box-shadow .2s;
}
.dual-range input[type=range]::-webkit-slider-thumb:hover {
  box-shadow: 0 0 0 6px rgba(37,99,235,.15);
}
.dual-range input[type=range]:active::-webkit-slider-thumb { cursor: grabbing; }

/* Chips */
.chips-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}
.chip-item {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 9px 10px;
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius-sm);
  font-size: 12px;
  font-weight: 500;
  color: var(--gray-600);
  cursor: pointer;
  transition: all .15s;
  text-align: center;
  background: transparent;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.chip-item i { font-size: 11px; color: var(--gray-400); transition: color .15s; }
.chip-item:hover { border-color: var(--blue-200); background: var(--blue-xlight); color: var(--blue); }
.chip-item:hover i { color: var(--blue); }
.chip-item.active {
  background: var(--blue);
  border-color: var(--blue);
  color: #fff;
  box-shadow: 0 4px 12px rgba(37,99,235,.25);
}
.chip-item.active i { color: rgba(255,255,255,.8); }
.chip-item.active-ghost {
  background: var(--blue-xlight);
  border-color: var(--blue-200);
  color: var(--blue);
}
.chip-item.active-ghost i { color: var(--blue); }

/* Status chips */
.status-row {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
.status-chip {
  flex: 1;
  min-width: 70px;
  text-align: center;
  padding: 8px 6px;
  border-radius: 50px;
  font-size: 11.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all .15s;
  border: 1.5px solid transparent;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.sc-all    { background: var(--gray-100); color: var(--gray-600); border-color: var(--gray-200); }
.sc-active { background: var(--green-100); color: var(--green-700); border-color: #bbf7d0; }
.sc-pending{ background: var(--yellow-100); color: var(--yellow-700); border-color: #fef08a; }
.sc-sold   { background: var(--gray-100); color: var(--gray-500); border-color: var(--gray-200); }
.sc-rented { background: var(--indigo-100); color: var(--indigo-700); border-color: #c7d2fe; }
.status-chip.sel { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.12); }
.sc-all.sel    { background: var(--blue); color: #fff; border-color: var(--blue); box-shadow: 0 4px 12px rgba(37,99,235,.3); }

/* Beds */
.beds-row { display: flex; gap: 6px; }
.bed-btn {
  flex: 1;
  padding: 10px 4px;
  text-align: center;
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius-sm);
  font-size: 13px;
  font-weight: 600;
  color: var(--gray-500);
  cursor: pointer;
  transition: all .15s;
  background: transparent;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.bed-btn:hover { border-color: var(--blue-200); color: var(--blue); background: var(--blue-xlight); }
.bed-btn.active {
  background: var(--blue);
  border-color: var(--blue);
  color: #fff;
  box-shadow: 0 4px 12px rgba(37,99,235,.25);
}

/* Selects */
.sb-select {
  width: 100%;
  padding: 10px 40px 10px 14px;
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius-sm);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 13px;
  font-weight: 500;
  color: var(--gray-700);
  background-color: var(--gray-50);
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  appearance: none;
  outline: none;
  cursor: pointer;
  transition: border-color .2s;
  margin-bottom: 10px;
}
.sb-select:focus { border-color: var(--blue); background-color: var(--white); }
.sb-select:last-child { margin-bottom: 0; }

/* Amenity checkboxes */
.amenity-list { display: flex; flex-direction: column; gap: 2px; }
.amenity-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 10px;
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: background .15s;
}
.amenity-row:hover { background: var(--gray-50); }
.amenity-row.checked { background: var(--blue-xlight); }
.amenity-check {
  width: 18px; height: 18px;
  border: 2px solid var(--gray-300);
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all .15s;
  font-size: 9px;
  color: transparent;
}
.amenity-row.checked .amenity-check {
  background: var(--blue);
  border-color: var(--blue);
  color: #fff;
}
.amenity-icon-wrap {
  width: 30px; height: 30px;
  background: var(--gray-100);
  border-radius: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  color: var(--gray-500);
  flex-shrink: 0;
  transition: all .15s;
}
.amenity-row.checked .amenity-icon-wrap { background: var(--blue-200); color: var(--blue-dark); }
.amenity-name { flex: 1; font-size: 13px; font-weight: 500; color: var(--gray-700); }
.amenity-cnt {
  font-size: 11px;
  font-weight: 600;
  background: var(--gray-100);
  color: var(--gray-500);
  padding: 2px 7px;
  border-radius: 20px;
}
.amenity-row.checked .amenity-cnt { background: var(--blue-100); color: var(--blue-dark); }

/* Area range */
.area-row { display: flex; gap: 8px; align-items: center; }
.area-input {
  flex: 1;
  padding: 10px 12px;
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius-sm);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 13px;
  color: var(--gray-700);
  background: var(--gray-50);
  outline: none;
  transition: border-color .2s;
}
.area-input:focus { border-color: var(--blue); background: var(--white); }
.area-sep { font-size: 13px; color: var(--gray-400); font-weight: 500; flex-shrink: 0; }

/* Sidebar buttons */
.sb-apply {
  width: 100%;
  padding: 14px;
  background: var(--blue);
  color: #fff;
  border: none;
  border-radius: var(--radius);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: background .2s, transform .15s, box-shadow .2s;
  box-shadow: 0 4px 16px rgba(37,99,235,.3);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  letter-spacing: .01em;
}
.sb-apply:hover {
  background: var(--blue-dark);
  transform: translateY(-1px);
  box-shadow: 0 8px 24px rgba(37,99,235,.4);
}
.sb-clear {
  width: 100%;
  padding: 10px;
  background: transparent;
  color: var(--gray-500);
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all .15s;
  margin-top: 8px;
}
.sb-clear:hover { background: var(--gray-100); color: var(--gray-700); border-color: var(--gray-300); }

/* ═══════════════════════════════════════════
   MAIN CONTENT
═══════════════════════════════════════════ */
.pm-main { flex: 1; min-width: 0; }

/* Toolbar */
.pm-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
  gap: 16px;
  flex-wrap: wrap;
  background: var(--white);
  border: 1px solid var(--gray-200);
  border-radius: var(--radius);
  padding: 14px 20px;
  box-shadow: var(--shadow-sm);
}

.pm-toolbar__left {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}
.pm-toolbar__count {
  font-size: 15px;
  font-weight: 700;
  color: var(--gray-800);
}
.pm-toolbar__count span { color: var(--blue); }
.pm-toolbar__count small {
  font-weight: 400;
  color: var(--gray-400);
  font-size: 13px;
}

/* Active filter tags */
.pm-active-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  align-items: center;
}
.pm-tag {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 10px 4px 12px;
  background: var(--blue-xlight);
  border: 1px solid var(--blue-200);
  border-radius: 50px;
  font-size: 11.5px;
  font-weight: 600;
  color: var(--blue-dark);
}
.pm-tag button {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--blue);
  font-size: 13px;
  padding: 0;
  line-height: 1;
  opacity: .6;
  margin-left: 2px;
  display: flex;
  align-items: center;
}
.pm-tag button:hover { opacity: 1; }

.pm-toolbar__right { display: flex; align-items: center; gap: 10px; }

.pm-sort {
  padding: 9px 38px 9px 14px;
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius-sm);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 13px;
  font-weight: 500;
  color: var(--gray-700);
  background-color: var(--white);
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  appearance: none;
  outline: none;
  cursor: pointer;
  transition: border-color .2s;
}
.pm-sort:focus { border-color: var(--blue); }

.view-toggle {
  display: flex;
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius-sm);
  overflow: hidden;
}
.view-btn {
  padding: 8px 13px;
  background: transparent;
  border: none;
  cursor: pointer;
  color: var(--gray-400);
  transition: all .15s;
  font-size: 13px;
}
.view-btn:not(:last-child) { border-right: 1px solid var(--gray-200); }
.view-btn.active { background: var(--blue); color: #fff; }

/* ═══════════════════════════════════════════
   PROPERTY CARDS
═══════════════════════════════════════════ */
.pm-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
  gap: 22px;
}
.pm-grid.list-view {
  grid-template-columns: 1fr;
}

/* Entrance animation */
@keyframes cardIn {
  from { opacity: 0; transform: translateY(24px); }
  to   { opacity: 1; transform: translateY(0); }
}

.pm-card {
  background: var(--white);
  border: 1px solid var(--gray-200);
  border-radius: var(--radius);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: transform .25s cubic-bezier(.16,1,.3,1), box-shadow .25s, border-color .2s;
  animation: cardIn .45s ease both;
  box-shadow: var(--shadow-sm);
  position: relative;
}

.pm-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
  border-color: var(--blue-200);
}

.pm-card:nth-child(1)  { animation-delay: .04s; }
.pm-card:nth-child(2)  { animation-delay: .08s; }
.pm-card:nth-child(3)  { animation-delay: .12s; }
.pm-card:nth-child(4)  { animation-delay: .16s; }
.pm-card:nth-child(5)  { animation-delay: .20s; }
.pm-card:nth-child(6)  { animation-delay: .24s; }
.pm-card:nth-child(7)  { animation-delay: .28s; }
.pm-card:nth-child(8)  { animation-delay: .32s; }
.pm-card:nth-child(9)  { animation-delay: .36s; }

/* Image */
.pm-card__img {
  position: relative;
  height: 230px;
  overflow: hidden;
  background: linear-gradient(135deg, var(--gray-100), var(--gray-200));
  flex-shrink: 0;
}
.pm-card__img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform .6s cubic-bezier(.16,1,.3,1);
  display: block;
}
.pm-card:hover .pm-card__img img { transform: scale(1.07); }

.pm-card__placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  background: linear-gradient(135deg, #f0f4ff, #e8edf8);
}
.pm-card__placeholder i { font-size: 44px; color: var(--blue-200); }
.pm-card__placeholder span {
  font-size: 11px;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--gray-400);
  font-weight: 600;
}

/* Overlay */
.pm-card__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(17,24,39,.5) 0%, transparent 50%);
  pointer-events: none;
}

/* Badges on image */
.pm-card__img-top {
  position: absolute;
  top: 14px;
  left: 14px;
  right: 14px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  pointer-events: none;
}
.pm-badge-status {
  padding: 5px 11px;
  border-radius: 50px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .04em;
  backdrop-filter: blur(6px);
}
.pbs-active  { background: rgba(21,128,61,.9);  color: #fff; }
.pbs-pending { background: rgba(161,98,7,.9);   color: #fff; }
.pbs-sold    { background: rgba(75,85,99,.85);  color: #fff; }
.pbs-rented  { background: rgba(67,56,202,.9);  color: #fff; }

.pm-badge-type {
  padding: 5px 11px;
  border-radius: 50px;
  font-size: 11px;
  font-weight: 600;
  background: rgba(255,255,255,.85);
  color: var(--gray-700);
  backdrop-filter: blur(6px);
}

/* Price on image */
.pm-card__img-bottom {
  position: absolute;
  bottom: 14px;
  left: 14px;
  right: 14px;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  pointer-events: none;
}
.pm-card__price-badge {
  background: rgba(255,255,255,.95);
  backdrop-filter: blur(8px);
  border-radius: var(--radius-sm);
  padding: 8px 14px;
  pointer-events: all;
}
.pm-card__price {
  font-family: 'Lora', serif;
  font-size: 22px;
  font-weight: 600;
  color: var(--gray-900);
  line-height: 1;
}
.pm-card__price-note {
  font-size: 10px;
  color: var(--gray-400);
  font-weight: 500;
  letter-spacing: .04em;
  margin-top: 2px;
}
/* fav btn */
.pm-card__fav {
  width: 34px; height: 34px;
  background: rgba(255,255,255,.9);
  border-radius: 50%;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 14px;
  color: var(--gray-300);
  transition: all .2s;
  pointer-events: all;
  box-shadow: 0 2px 8px rgba(0,0,0,.12);
}
.pm-card__fav:hover { transform: scale(1.1); color: #ef4444; }
.pm-card__fav.faved { color: #ef4444; }

/* Card Body */
.pm-card__body { padding: 18px 20px 14px; flex: 1; }
.pm-card__title {
  font-size: 16px;
  font-weight: 700;
  color: var(--gray-900);
  line-height: 1.35;
  margin-bottom: 6px;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.pm-card__loc {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12.5px;
  color: var(--gray-500);
  margin-bottom: 16px;
}
.pm-card__loc i { color: var(--blue); font-size: 10px; flex-shrink: 0; }

/* Specs */
.pm-card__specs {
  display: flex;
  border: 1.5px solid var(--gray-100);
  border-radius: var(--radius-sm);
  overflow: hidden;
  background: var(--gray-50);
  margin-bottom: 14px;
}
.pm-spec {
  flex: 1;
  text-align: center;
  padding: 10px 6px;
  position: relative;
}
.pm-spec:not(:last-child) { border-right: 1.5px solid var(--gray-200); }
.pm-spec__val {
  font-size: 17px;
  font-weight: 800;
  color: var(--gray-800);
  line-height: 1;
  margin-bottom: 3px;
}
.pm-spec__icon { color: var(--blue); font-size: 11px; margin-bottom: 3px; }
.pm-spec__lbl {
  font-size: 9.5px;
  color: var(--gray-400);
  letter-spacing: .1em;
  text-transform: uppercase;
  font-weight: 600;
}

/* Tags */
.pm-card__tags {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}
.pm-card__tag {
  padding: 3px 9px;
  background: var(--gray-100);
  border-radius: 50px;
  font-size: 11px;
  font-weight: 600;
  color: var(--gray-500);
}

/* Footer */
.pm-card__foot {
  padding: 12px 20px;
  border-top: 1px solid var(--gray-100);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  background: var(--gray-50);
}
.pm-card__agent {
  display: flex;
  align-items: center;
  gap: 9px;
  font-size: 12.5px;
  color: var(--gray-500);
  font-weight: 500;
  min-width: 0;
}
.pm-card__agent-av {
  width: 30px; height: 30px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--blue-200), var(--blue));
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 800;
  color: #fff;
  flex-shrink: 0;
}
.pm-card__agent-name {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.pm-card__ctas {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-shrink: 0;
}
.pm-card__view-btn {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 7px 14px;
  background: var(--blue);
  color: #fff;
  border-radius: var(--radius-sm);
  font-size: 12px;
  font-weight: 700;
  text-decoration: none;
  transition: background .15s;
  letter-spacing: .01em;
}
.pm-card__view-btn:hover { background: var(--blue-dark); }
.pm-card__icon-btn {
  width: 32px; height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius-sm);
  font-size: 12px;
  color: var(--gray-400);
  cursor: pointer;
  transition: all .15s;
  background: var(--white);
  text-decoration: none;
}
.pm-card__icon-btn:hover { border-color: var(--blue); color: var(--blue); background: var(--blue-xlight); }
.pm-card__icon-btn.del:hover { border-color: #ef4444; color: #ef4444; background: var(--red-100); }

/* ═══════════════════════════════════════════
   EMPTY STATE
═══════════════════════════════════════════ */
.pm-empty {
  grid-column: 1 / -1;
  text-align: center;
  padding: 80px 40px;
  background: var(--white);
  border: 2px dashed var(--gray-200);
  border-radius: var(--radius);
}
.pm-empty__icon {
  width: 80px; height: 80px;
  background: var(--blue-xlight);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  color: var(--blue);
  margin: 0 auto 24px;
}
.pm-empty h3 {
  font-size: 22px;
  font-weight: 800;
  color: var(--gray-800);
  margin-bottom: 8px;
}
.pm-empty p { color: var(--gray-500); font-size: 14px; margin-bottom: 28px; }
.pm-empty-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 14px 28px;
  background: var(--blue);
  color: #fff;
  text-decoration: none;
  border-radius: var(--radius);
  font-size: 14px;
  font-weight: 700;
  box-shadow: var(--shadow-blue);
  transition: background .2s, transform .15s;
}
.pm-empty-btn:hover { background: var(--blue-dark); transform: translateY(-2px); }

/* ═══════════════════════════════════════════
   PAGINATION
═══════════════════════════════════════════ */
.pm-pag { 
  margin-top: 40px; 
  display: flex; 
  justify-content: center; 
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.pm-pag .pagination {
  display: flex;
  align-items: center;
  gap: 4px;
  list-style: none;
  margin: 0;
  padding: 0;
}

.pm-pag .page-item {
  display: inline-block;
}

.pm-pag .page-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 40px;
  height: 40px;
  padding: 0 12px;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  border-radius: 8px;
  transition: all 0.2s;
  border: 1px solid var(--gray-200);
  background: var(--white);
  color: var(--gray-700);
  margin: 0 2px;
}

.pm-pag .page-link:hover {
  background: var(--blue);
  border-color: var(--blue);
  color: var(--white);
  transform: translateY(-1px);
}

.pm-pag .page-item.active .page-link {
  background: var(--blue);
  border-color: var(--blue);
  color: var(--white);
}

.pm-pag .page-item.disabled .page-link {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}

.pm-pag .pagination-info {
  font-size: 13px;
  color: var(--gray-500);
  margin: 0 16px;
  white-space: nowrap;
}

/* ═══════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════ */
@media(max-width:1200px) {
  .pm-layout { padding: 28px 24px 48px; gap: 20px; }
  :root { --sidebar-w: 270px; }
  .pm-grid { grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); }
}
@media(max-width:900px) {
  .pm-hero__inner { grid-template-columns: 1fr; }
  .pm-hero__stats { display: flex; flex-wrap: wrap; padding-top: 24px; }
  .pm-add-btn { margin-top: 12px; }
  .pm-layout { flex-direction: column; }
  .pm-sidebar { width: 100%; position: static; }
  .chips-grid { grid-template-columns: repeat(4,1fr); }
}
@media(max-width:600px) {
  .pm-hero__inner { padding: 36px 20px 80px; }
  .pm-layout { padding: 20px 16px 40px; }
  .pm-grid { grid-template-columns: 1fr; }
  .chips-grid { grid-template-columns: repeat(2,1fr); }
  .pm-search-bar__select { display: none; }
  .pm-search-bar__divider { display: none; }
}
</style>

<div class="pm-wrap">

  {{-- ══════════ HERO ══════════ --}}
  <div class="pm-hero">
    <div class="pm-hero__shapes">
      {{-- big circle --}}
      <svg class="shape-3" viewBox="0 0 600 600" fill="none">
        <circle cx="300" cy="300" r="280" stroke="white" stroke-width="1.5"/>
        <circle cx="300" cy="300" r="220" stroke="white" stroke-width="1"/>
        <circle cx="300" cy="300" r="160" stroke="white" stroke-width=".5"/>
      </svg>
      {{-- top-right grid --}}
      <svg class="shape-1" viewBox="0 0 400 400" fill="none">
        <defs><pattern id="g" width="40" height="40" patternUnits="userSpaceOnUse">
          <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
        </pattern></defs>
        <rect width="400" height="400" fill="url(#g)"/>
      </svg>
    </div>

    <div class="pm-hero__inner">
      <div class="pm-hero__left">
        <div class="pm-hero__breadcrumb">
          <a href="#"><i class="fas fa-home"></i></a>
          <i class="fas fa-chevron-right"></i>
          <span>Properties Management</span>
        </div>

        <h1 class="pm-hero__title">
          Manage Your<br><em>Property Portfolio</em>
        </h1>
        <p class="pm-hero__sub">
          Browse, filter, and manage all your property listings in one powerful dashboard. Find the perfect match for every client.
        </p>

        <div class="pm-search-bar">
          <span class="pm-search-bar__icon"><i class="fas fa-search"></i></span>
          <input type="text" placeholder="Search by title, city, or reference number…" id="mainSearch">
          <div class="pm-search-bar__divider"></div>
          <select class="pm-search-bar__select">
            <option>All Types</option>
            <option>Apartment</option>
            <option>House</option>
            <option>Villa</option>
            <option>Land</option>
            <option>Office</option>
          </select>
          <button class="pm-search-bar__btn"><i class="fas fa-search"></i> Search</button>
        </div>

        <div class="pm-hero__tabs">
          <button class="pm-hero__tab active" onclick="heroTab(this,'')"><i class="fas fa-layer-group"></i> All</button>
          <button class="pm-hero__tab" onclick="heroTab(this,'active')"><i class="fas fa-circle" style="color:#4ade80;font-size:8px"></i> Active</button>
          <button class="pm-hero__tab" onclick="heroTab(this,'pending')"><i class="fas fa-clock"></i> Pending</button>
          <button class="pm-hero__tab" onclick="heroTab(this,'sold')"><i class="fas fa-tag"></i> Sold</button>
          <button class="pm-hero__tab" onclick="heroTab(this,'rented')"><i class="fas fa-key"></i> Rented</button>
        </div>
      </div>

      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:20px">
        <a href="{{ route('properties.create') }}" class="pm-add-btn">
          <i class="fas fa-plus"></i> Add New Property
        </a>
        <div class="pm-hero__stats">
          <div class="pm-hero__stat">
            <div class="pm-hero__stat-val">{{ $properties->total() ?? $properties->count() }}<sup>+</sup></div>
            <div class="pm-hero__stat-lbl">Total</div>
          </div>
          <div class="pm-hero__stat">
            <div class="pm-hero__stat-val" style="color:#4ade80">{{ $properties->where('status','active')->count() }}</div>
            <div class="pm-hero__stat-lbl">Active</div>
          </div>
          <div class="pm-hero__stat">
            <div class="pm-hero__stat-val" style="color:#fbbf24">{{ $properties->where('status','pending')->count() }}</div>
            <div class="pm-hero__stat-lbl">Pending</div>
          </div>
          <div class="pm-hero__stat">
            <div class="pm-hero__stat-val" style="color:#a78bfa">{{ $properties->where('status','sold')->count() }}</div>
            <div class="pm-hero__stat-lbl">Sold</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ══════════ LAYOUT ══════════ --}}
  <div class="pm-layout">

    {{-- ════ SIDEBAR ════ --}}
    <aside class="pm-sidebar">

      {{-- Price --}}
      <div class="sb-card">
        <div class="sb-head" onclick="toggleSb(this)">
          <span class="sb-head__label">
            <span class="sb-head__label-icon"><i class="fas fa-tag"></i></span>
            Price Range
          </span>
          <i class="fas fa-chevron-up sb-head__chevron"></i>
        </div>
        <div class="sb-body">
          <div class="price-row">
            <div class="price-input-wrap">
              <label>Minimum</label>
              <input type="text" id="minPriceInput" value="$0" readonly>
            </div>
            <span class="price-sep">—</span>
            <div class="price-input-wrap">
              <label>Maximum</label>
              <input type="text" id="maxPriceInput" value="$5,000,000" readonly>
            </div>
          </div>
          <div class="dual-range" id="dualRange">
            <div class="dual-range__fill" id="dualFill"></div>
            <input type="range" min="0" max="5000000" step="50000" value="0"       id="rMin" oninput="syncRange()">
            <input type="range" min="0" max="5000000" step="50000" value="5000000" id="rMax" oninput="syncRange()">
          </div>
          <div style="display:flex;justify-content:space-between;margin-top:8px">
            @foreach(['Any','<$100K','$100-500K','$500K-1M','$1M-3M','$3M+'] as $pl)
            <span onclick="this.parentElement.querySelectorAll('span').forEach(s=>s.style.cssText='');this.style.color='var(--blue)';this.style.fontWeight='700'"
              style="font-size:10px;color:var(--gray-400);cursor:pointer;transition:color .15s;font-weight:500">{{ $pl }}</span>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Property Type --}}
      <div class="sb-card">
        <div class="sb-head" onclick="toggleSb(this)">
          <span class="sb-head__label">
            <span class="sb-head__label-icon"><i class="fas fa-building"></i></span>
            Property Type
          </span>
          <i class="fas fa-chevron-up sb-head__chevron"></i>
        </div>
        <div class="sb-body" style="padding-top:14px;padding-bottom:14px">
          <div class="chips-grid">
            @foreach([
              ['All','fa-th','active'],
              ['Apartment','fa-city',''],
              ['House','fa-home',''],
              ['Villa','fa-hotel',''],
              ['Land','fa-map',''],
              ['Office','fa-briefcase',''],
              ['Commercial','fa-store',''],
              ['Chalet','fa-umbrella-beach',''],
            ] as [$label,$icon,$cls])
            <div class="chip-item {{ $cls }}" onclick="toggleType(this)">
              <i class="fas {{ $icon }}"></i> {{ $label }}
            </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Status --}}
      <div class="sb-card">
        <div class="sb-head" onclick="toggleSb(this)">
          <span class="sb-head__label">
            <span class="sb-head__label-icon"><i class="fas fa-signal"></i></span>
            Status
          </span>
          <i class="fas fa-chevron-up sb-head__chevron"></i>
        </div>
        <div class="sb-body">
          <div class="status-row">
            <span class="status-chip sc-all sel"    onclick="selStatus(this)">All</span>
            <span class="status-chip sc-active"     onclick="selStatus(this)">Active</span>
            <span class="status-chip sc-pending"    onclick="selStatus(this)">Pending</span>
            <span class="status-chip sc-sold"       onclick="selStatus(this)">Sold</span>
            <span class="status-chip sc-rented"     onclick="selStatus(this)">Rented</span>
          </div>
        </div>
      </div>

      {{-- Bedrooms --}}
      <div class="sb-card">
        <div class="sb-head" onclick="toggleSb(this)">
          <span class="sb-head__label">
            <span class="sb-head__label-icon"><i class="fas fa-bed"></i></span>
            Bedrooms
          </span>
          <i class="fas fa-chevron-up sb-head__chevron"></i>
        </div>
        <div class="sb-body">
          <div class="beds-row">
            <button class="bed-btn active" onclick="selBed(this)">Any</button>
            <button class="bed-btn" onclick="selBed(this)">1</button>
            <button class="bed-btn" onclick="selBed(this)">2</button>
            <button class="bed-btn" onclick="selBed(this)">3</button>
            <button class="bed-btn" onclick="selBed(this)">4</button>
            <button class="bed-btn" onclick="selBed(this)">5+</button>
          </div>
        </div>
      </div>

      {{-- Area --}}
      <div class="sb-card">
        <div class="sb-head" onclick="toggleSb(this)">
          <span class="sb-head__label">
            <span class="sb-head__label-icon"><i class="fas fa-ruler-combined"></i></span>
            Area (sqm)
          </span>
          <i class="fas fa-chevron-up sb-head__chevron"></i>
        </div>
        <div class="sb-body">
          <div class="area-row">
            <input class="area-input" type="number" placeholder="Min" min="0">
            <span class="area-sep">—</span>
            <input class="area-input" type="number" placeholder="Max" min="0">
          </div>
        </div>
      </div>

      {{-- Location --}}
      <div class="sb-card">
        <div class="sb-head" onclick="toggleSb(this)">
          <span class="sb-head__label">
            <span class="sb-head__label-icon"><i class="fas fa-map-marker-alt"></i></span>
            Location
          </span>
          <i class="fas fa-chevron-up sb-head__chevron"></i>
        </div>
        <div class="sb-body">
          <select class="sb-select">
            <option value="">All Governorates</option>
            <option>Cairo</option>
            <option>Giza</option>
            <option>Alexandria</option>
            <option>Red Sea</option>
            <option>North Sinai</option>
          </select>
          <select class="sb-select">
            <option value="">All Cities</option>
            <option>New Cairo</option>
            <option>6th October</option>
            <option>Ain Sokhna</option>
            <option>North Coast</option>
            <option>Hurghada</option>
            <option>Sharm El-Sheikh</option>
          </select>
          <select class="sb-select" style="margin-bottom:0">
            <option value="">All Districts</option>
            <option>Nasr City</option>
            <option>Maadi</option>
            <option>Zamalek</option>
            <option>Heliopolis</option>
            <option>Mohandessin</option>
            <option>Dokki</option>
          </select>
        </div>
      </div>

      {{-- Amenities --}}
      <div class="sb-card">
        <div class="sb-head" onclick="toggleSb(this)">
          <span class="sb-head__label">
            <span class="sb-head__label-icon"><i class="fas fa-star"></i></span>
            Amenities
          </span>
          <i class="fas fa-chevron-up sb-head__chevron"></i>
        </div>
        <div class="sb-body" style="padding-top:8px;padding-bottom:8px">
          <div class="amenity-list">
            @foreach([
              ['Swimming Pool','fa-swimming-pool',24],
              ['Gym & Fitness','fa-dumbbell',18],
              ['Parking Garage','fa-car',41],
              ['Garden & Yard','fa-leaf',15],
              ['24/7 Security','fa-shield-alt',36],
              ['Elevator','fa-arrow-up',29],
              ['Balcony','fa-sun',22],
              ['Central A/C','fa-snowflake',33],
            ] as [$name,$icon,$cnt])
            <div class="amenity-row" onclick="toggleAmenity(this)">
              <div class="amenity-check"><i class="fas fa-check"></i></div>
              <div class="amenity-icon-wrap"><i class="fas {{ $icon }}"></i></div>
              <span class="amenity-name">{{ $name }}</span>
              <span class="amenity-cnt">{{ $cnt }}</span>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Agent --}}
      <div class="sb-card">
        <div class="sb-head" onclick="toggleSb(this)">
          <span class="sb-head__label">
            <span class="sb-head__label-icon"><i class="fas fa-user-tie"></i></span>
            Agent
          </span>
          <i class="fas fa-chevron-up sb-head__chevron"></i>
        </div>
        <div class="sb-body">
          <select class="sb-select" style="margin-bottom:0">
            <option value="">All Agents</option>
          </select>
        </div>
      </div>

      <button class="sb-apply"><i class="fas fa-sliders-h"></i> Apply Filters</button>
      <button class="sb-clear" onclick="clearAllFilters()"><i class="fas fa-times" style="margin-right:6px"></i> Clear All Filters</button>

    </aside>

    {{-- ════ MAIN CONTENT ════ --}}
    <main class="pm-main">

      {{-- Toolbar --}}
      <div class="pm-toolbar">
        <div class="pm-toolbar__left">
          <div class="pm-toolbar__count">
            <span>{{ $properties->total() ?? $properties->count() }}</span> Properties
            <small>found</small>
          </div>
          <div class="pm-active-tags" id="activeTags">
            <div class="pm-tag">Active <button onclick="this.closest('.pm-tag').remove()"><i class="fas fa-times"></i></button></div>
            <div class="pm-tag">Cairo <button onclick="this.closest('.pm-tag').remove()"><i class="fas fa-times"></i></button></div>
          </div>
        </div>
        <div class="pm-toolbar__right">
          <select class="pm-sort">
            <option>Latest First</option>
            <option>Price: Low → High</option>
            <option>Price: High → Low</option>
            <option>Most Bedrooms</option>
            <option>Largest Area</option>
          </select>
          <div class="view-toggle">
            <button class="view-btn active" id="gridViewBtn" onclick="setView('grid')" title="Grid"><i class="fas fa-th-large"></i></button>
            <button class="view-btn" id="listViewBtn" onclick="setView('list')" title="List"><i class="fas fa-list-ul"></i></button>
          </div>
        </div>
      </div>

      {{-- GRID --}}
      <div class="pm-grid" id="pmGrid">
        @forelse ($properties as $property)
          <div class="pm-card">

            {{-- Image --}}
            <div class="pm-card__img">
              @if ($property->media && $property->media->isNotEmpty())
                <img src="{{ $property->media->first()->url }}" alt="{{ $property->title }}" loading="lazy">
              @else
                <div class="pm-card__placeholder">
                  <i class="fas fa-building"></i>
                  <span>No Image</span>
                </div>
              @endif
              <div class="pm-card__overlay"></div>

              <div class="pm-card__img-top">
                <div style="display:flex;gap:6px;flex-wrap:wrap">
                  <span class="pm-badge-status pbs-{{ $property->status }}">{{ ucfirst($property->status) }}</span>
                  @if($property->type ?? null)
                  <span class="pm-badge-type">{{ ucfirst($property->type) }}</span>
                  @endif
                </div>
              </div>

              <div class="pm-card__img-bottom">
                <div class="pm-card__price-badge">
                  <div class="pm-card__price">${{ number_format($property->price, 0) }}</div>
                  <div class="pm-card__price-note">Asking Price</div>
                </div>
                <button class="pm-card__fav" onclick="toggleFav(this)"><i class="far fa-heart"></i></button>
              </div>
            </div>

            {{-- Body --}}
            <div class="pm-card__body">
              <div class="pm-card__title">{{ $property->title }}</div>
              <div class="pm-card__loc">
                <i class="fas fa-map-marker-alt"></i>
                {{ $property->location ?? 'Location not specified' }}
              </div>

              <div class="pm-card__specs">
                <div class="pm-spec">
                  <div class="pm-spec__icon"><i class="fas fa-bed"></i></div>
                  <div class="pm-spec__val">{{ $property->bedrooms ?? '–' }}</div>
                  <div class="pm-spec__lbl">Beds</div>
                </div>
                <div class="pm-spec">
                  <div class="pm-spec__icon"><i class="fas fa-bath"></i></div>
                  <div class="pm-spec__val">{{ $property->bathrooms ?? '–' }}</div>
                  <div class="pm-spec__lbl">Baths</div>
                </div>
                <div class="pm-spec">
                  <div class="pm-spec__icon"><i class="fas fa-vector-square"></i></div>
                  <div class="pm-spec__val">{{ $property->area ?? '–' }}</div>
                  <div class="pm-spec__lbl">Sqm</div>
                </div>
                <div class="pm-spec">
                  <div class="pm-spec__icon"><i class="fas fa-layer-group"></i></div>
                  <div class="pm-spec__val">{{ $property->floors ?? '–' }}</div>
                  <div class="pm-spec__lbl">Floor</div>
                </div>
              </div>
            </div>

            {{-- Footer --}}
            <div class="pm-card__foot">
              @if ($property->agent)
              <div class="pm-card__agent">
                <div class="pm-card__agent-av">
                  {{ strtoupper(substr($property->agent->first_name,0,1)) }}{{ strtoupper(substr($property->agent->last_name,0,1)) }}
                </div>
                <span class="pm-card__agent-name">{{ $property->agent->first_name }} {{ $property->agent->last_name }}</span>
              </div>
              @else
              <div class="pm-card__agent">
                <div class="pm-card__agent-av" style="background:var(--gray-300)">?</div>
                <span class="pm-card__agent-name" style="color:var(--gray-400)">Unassigned</span>
              </div>
              @endif
              <div class="pm-card__ctas">
                <a href="{{ route('properties.show', $property) }}" class="pm-card__view-btn">
                  View <i class="fas fa-arrow-right" style="font-size:10px"></i>
                </a>
                <a href="#" class="pm-card__icon-btn" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                <form action="#" method="POST" class="inline" style="display:inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="pm-card__icon-btn del" title="Delete"
                    onclick="return confirm('Delete this property?')">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </form>
              </div>
            </div>

          </div>
        @empty
          <div class="pm-empty">
            <div class="pm-empty__icon"><i class="fas fa-building"></i></div>
            <h3>No Properties Found</h3>
            <p>Start building your portfolio by adding your first property listing.</p>
            <a href="{{ route('properties.create') }}" class="pm-empty-btn">
              <i class="fas fa-plus"></i> Add First Property
            </a>
          </div>
        @endforelse
      </div>

      {{-- Pagination --}}
      @if ($properties->hasPages())
        <div class="pm-pag">{{ $properties->links('vendor.pagination.bootstrap-4') }}</div>
      @endif

    </main>
  </div>
</div>

<script>
// Sidebar collapse
function toggleSb(head) {
  const body = head.nextElementSibling;
  const chev = head.querySelector('.sb-head__chevron');
  const open = body.style.display !== 'none';
  body.style.display = open ? 'none' : '';
  head.classList.toggle('collapsed', open);
}

// Dual range slider
function syncRange() {
  let min = +document.getElementById('rMin').value;
  let max = +document.getElementById('rMax').value;
  if (min > max) { [min, max] = [max, min]; }
  const total = 5000000;
  const fmt = v => v === 0 ? '$0' : v >= 1000000 ? '$' + (v/1000000).toFixed(1) + 'M' : '$' + (v/1000).toFixed(0) + 'K';
  document.getElementById('minPriceInput').value = fmt(min);
  document.getElementById('maxPriceInput').value = fmt(max);
  document.getElementById('dualFill').style.left  = (min / total * 100) + '%';
  document.getElementById('dualFill').style.right = ((total - max) / total * 100) + '%';
}
syncRange();

// Type chips
function toggleType(el) {
  if (el.textContent.trim() === 'All') {
    el.closest('.chips-grid').querySelectorAll('.chip-item').forEach(c => c.classList.remove('active','active-ghost'));
    el.classList.add('active');
  } else {
    const allBtn = Array.from(el.closest('.chips-grid').querySelectorAll('.chip-item')).find(c => c.textContent.trim() === 'All');
    if (allBtn) { allBtn.classList.remove('active','active-ghost'); }
    el.classList.toggle('active');
    el.classList.remove('active-ghost');
  }
}

// Status chips
function selStatus(el) {
  el.closest('.status-row').querySelectorAll('.status-chip').forEach(c => c.classList.remove('sel'));
  el.classList.add('sel');
}

// Bed buttons
function selBed(el) {
  el.closest('.beds-row').querySelectorAll('.bed-btn').forEach(b => b.classList.remove('active'));
  el.classList.add('active');
}

// Amenity toggle
function toggleAmenity(row) {
  row.classList.toggle('checked');
}

// Hero quick tabs
function heroTab(el, status) {
  document.querySelectorAll('.pm-hero__tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
}

// Fav toggle
function toggleFav(btn) {
  btn.classList.toggle('faved');
  btn.querySelector('i').className = btn.classList.contains('faved') ? 'fas fa-heart' : 'far fa-heart';
}

// View toggle
function setView(mode) {
  const grid = document.getElementById('pmGrid');
  document.getElementById('gridViewBtn').classList.toggle('active', mode === 'grid');
  document.getElementById('listViewBtn').classList.toggle('active', mode === 'list');
  grid.classList.toggle('list-view', mode === 'list');
}

// Clear filters
function clearAllFilters() {
  document.querySelectorAll('.chip-item').forEach((c,i) => {
    c.classList.remove('active','active-ghost');
    if (c.textContent.trim() === 'All') c.classList.add('active');
  });
  document.querySelectorAll('.status-chip').forEach((c,i) => {
    c.classList.remove('sel');
    if (i === 0) c.classList.add('sel');
  });
  document.querySelectorAll('.bed-btn').forEach((b,i) => {
    b.classList.remove('active');
    if (i === 0) b.classList.add('active');
  });
  document.querySelectorAll('.amenity-row').forEach(r => r.classList.remove('checked'));
  document.getElementById('rMin').value = 0;
  document.getElementById('rMax').value = 5000000;
  syncRange();
}
</script>

@endsection