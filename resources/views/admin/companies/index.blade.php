@extends('layouts.app')
@section('title', 'Companies Management')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
/* ════════════════════════════════════════
   TOKENS
════════════════════════════════════════ */
:root {
  --blue:       #2563eb;
  --blue-d:     #1d4ed8;
  --blue-xd:    #1e3a8a;
  --blue-l:     #3b82f6;
  --blue-xl:    #60a5fa;
  --blue-100:   #dbeafe;
  --blue-50:    #eff6ff;

  --ink:        #0f172a;
  --ink-2:      #1e293b;
  --ink-3:      #334155;
  --muted:      #64748b;
  --muted-l:    #94a3b8;
  --border:     #e2e8f0;
  --border-l:   #f1f5f9;
  --surface:    #ffffff;
  --bg:         #f8fafc;

  --green:      #10b981;
  --green-bg:   #d1fae5;
  --green-tx:   #065f46;
  --red:        #ef4444;
  --red-bg:     #fee2e2;
  --red-tx:     #991b1b;
  --amber:      #f59e0b;
  --amber-bg:   #fef3c7;
  --amber-tx:   #92400e;
  --violet:     #8b5cf6;
  --violet-bg:  #ede9fe;
  --violet-tx:  #5b21b6;

  --radius:     14px;
  --radius-sm:  8px;
  --shadow-sm:  0 1px 3px rgba(15,23,42,.07), 0 1px 2px rgba(15,23,42,.05);
  --shadow-md:  0 4px 16px rgba(15,23,42,.09);
  --shadow-lg:  0 12px 40px rgba(15,23,42,.13);
  --shadow-xl:  0 24px 64px rgba(15,23,42,.16);
  --shadow-blue:0 8px 32px rgba(37,99,235,.28);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.cm-page {
  font-family: 'DM Sans', system-ui, sans-serif;
  background: var(--bg);
  min-height: 100vh;
  color: var(--ink);
}

/* ════════════════════════════════════════
   HERO HEADER
════════════════════════════════════════ */
.cm-hero {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 45%, #1d4ed8 80%, #2563eb 100%);
  overflow: hidden;
  padding: 0;
}

/* animated mesh background */
.cm-hero-mesh {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
}
.cm-hero-mesh svg {
  position: absolute;
  width: 100%; height: 100%;
}
/* shimmer line */
.cm-hero::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; height: 1px;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,.4) 40%, rgba(255,255,255,.7) 60%, transparent);
}

.cm-hero-inner {
  position: relative;
  max-width: 1440px;
  margin: 0 auto;
  padding: 52px 48px 100px;
  display: grid;
  grid-template-columns: 1fr auto;
  align-items: start;
  gap: 48px;
}

/* breadcrumb */
.cm-breadcrumb {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: rgba(255,255,255,.5);
  margin-bottom: 18px;
  letter-spacing: .03em;
}
.cm-breadcrumb a { color: rgba(255,255,255,.5); text-decoration: none; transition: color .15s; }
.cm-breadcrumb a:hover { color: rgba(255,255,255,.85); }
.cm-breadcrumb i { font-size: 9px; }
.cm-breadcrumb span { color: rgba(255,255,255,.9); font-weight: 600; }

.cm-hero-title {
  font-family: 'Bricolage Grotesque', sans-serif;
  font-size: clamp(30px, 3.5vw, 52px);
  font-weight: 800;
  color: #fff;
  line-height: 1.08;
  letter-spacing: -.025em;
  margin-bottom: 10px;
}
.cm-hero-title span {
  background: linear-gradient(135deg, #93c5fd, #60a5fa);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.cm-hero-sub {
  font-size: 15px;
  color: rgba(255,255,255,.55);
  font-weight: 400;
  line-height: 1.6;
  margin-bottom: 36px;
  max-width: 480px;
}

/* search bar inside hero */
.cm-hero-search {
  display: flex;
  align-items: center;
  max-width: 560px;
  background: rgba(255,255,255,.1);
  border: 1.5px solid rgba(255,255,255,.2);
  border-radius: var(--radius-sm);
  overflow: hidden;
  transition: all .2s;
  backdrop-filter: blur(12px);
  box-shadow: 0 8px 32px rgba(0,0,0,.15);
}
.cm-hero-search:focus-within {
  background: rgba(255,255,255,.18);
  border-color: rgba(255,255,255,.55);
  box-shadow: 0 0 0 3px rgba(255,255,255,.1), 0 8px 32px rgba(0,0,0,.2);
}
.cm-hero-search i { padding: 0 18px; color: rgba(255,255,255,.55); font-size: 14px; flex-shrink: 0; }
.cm-hero-search input {
  flex: 1; background: transparent; border: none; outline: none;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px; color: #fff; padding: 16px 0;
  caret-color: #fff;
}
.cm-hero-search input::placeholder { color: rgba(255,255,255,.38); }
.cm-hero-search-btn {
  background: #fff; border: none;
  color: var(--blue-d);
  font-family: 'DM Sans', sans-serif;
  font-size: 13px; font-weight: 700;
  padding: 0 24px; height: 100%;
  cursor: pointer; white-space: nowrap;
  display: flex; align-items: center; gap: 7px;
  transition: background .15s;
  border-radius: 0 calc(var(--radius-sm) - 2px) calc(var(--radius-sm) - 2px) 0;
  min-height: 52px;
}
.cm-hero-search-btn:hover { background: var(--blue-50); }

/* filter tabs in hero */
.cm-hero-tabs {
  display: flex;
  gap: 8px;
  margin-top: 18px;
  flex-wrap: wrap;
}
.cm-hero-tab {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all .18s;
  border: 1.5px solid rgba(255,255,255,.15);
  background: rgba(255,255,255,.08);
  color: rgba(255,255,255,.65);
  font-family: 'DM Sans', sans-serif;
  letter-spacing: .02em;
}
.cm-hero-tab:hover { border-color: rgba(255,255,255,.4); color: #fff; background: rgba(255,255,255,.14); }
.cm-hero-tab.on {
  background: rgba(255,255,255,.2);
  border-color: rgba(255,255,255,.5);
  color: #fff;
}
.cm-hero-tab .dot {
  width: 6px; height: 6px; border-radius: 50%;
}
.dot-all    { background: rgba(255,255,255,.7); }
.dot-active { background: #4ade80; box-shadow: 0 0 0 2px rgba(74,222,128,.3); }
.dot-inactive { background: #f87171; }
.dot-pending  { background: #fbbf24; }

/* hero right — KPI cards */
.cm-hero-kpis {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  padding-top: 28px;
}
.cm-kpi {
  background: rgba(255,255,255,.1);
  backdrop-filter: blur(12px);
  border: 1.5px solid rgba(255,255,255,.15);
  border-radius: var(--radius);
  padding: 18px 20px;
  transition: all .2s;
  cursor: default;
}
.cm-kpi:hover { background: rgba(255,255,255,.16); transform: translateY(-2px); }
.cm-kpi-val {
  font-family: 'Bricolage Grotesque', sans-serif;
  font-size: 32px;
  font-weight: 800;
  color: #fff;
  line-height: 1;
  margin-bottom: 4px;
}
.cm-kpi-val sup { font-size: 16px; }
.cm-kpi-lbl {
  font-size: 10.5px;
  color: rgba(255,255,255,.5);
  font-weight: 600;
  letter-spacing: .1em;
  text-transform: uppercase;
}
.cm-kpi-trend {
  font-size: 11px;
  color: #4ade80;
  font-weight: 700;
  margin-top: 6px;
  display: flex;
  align-items: center;
  gap: 3px;
}
.cm-kpi-trend.down { color: #f87171; }

/* add button */
.cm-add-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: #fff;
  color: var(--blue-d);
  border: none;
  border-radius: var(--radius-sm);
  padding: 14px 28px;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  font-weight: 800;
  cursor: pointer;
  text-decoration: none;
  box-shadow: var(--shadow-blue);
  transition: transform .2s, box-shadow .2s;
  white-space: nowrap;
}
.cm-add-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(37,99,235,.4); }
.cm-add-btn-icon {
  width: 28px; height: 28px;
  background: var(--blue);
  color: #fff;
  border-radius: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  flex-shrink: 0;
}

/* wave bottom of hero */
.cm-hero-wave {
  position: absolute;
  bottom: -2px; left: 0; right: 0;
  height: 80px;
  pointer-events: none;
}

/* ════════════════════════════════════════
   MAIN CONTENT
════════════════════════════════════════ */
.cm-body {
  max-width: 1440px;
  margin: 0 auto;
  padding: 0 48px 60px;
  margin-top: -48px;
  position: relative;
}

/* ─── Toolbar card ─── */
.cm-toolbar {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-md);
  padding: 16px 22px;
  display: flex;
  align-items: center;
  gap: 14px;
  flex-wrap: wrap;
  margin-bottom: 20px;
}

.cm-toolbar-count {
  font-family: 'Bricolage Grotesque', sans-serif;
  font-size: 16px;
  font-weight: 700;
  color: var(--ink);
  flex: 1;
  min-width: 200px;
}
.cm-toolbar-count span { color: var(--blue); }
.cm-toolbar-count small { font-family: 'DM Sans', sans-serif; font-size: 12px; color: var(--muted); font-weight: 400; margin-left: 6px; }

.cm-toolbar-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

.cm-select {
  padding: 9px 36px 9px 13px;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  font-weight: 500;
  color: var(--ink-3);
  background-color: var(--bg);
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  appearance: none;
  outline: none;
  cursor: pointer;
  transition: border-color .18s;
}
.cm-select:focus { border-color: var(--blue); background-color: var(--surface); }

/* view toggle */
.cm-view-toggle {
  display: flex;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  overflow: hidden;
}
.cm-view-btn {
  padding: 8px 12px;
  background: transparent;
  border: none;
  cursor: pointer;
  color: var(--muted-l);
  font-size: 13px;
  transition: all .15s;
}
.cm-view-btn:not(:last-child) { border-right: 1px solid var(--border); }
.cm-view-btn.on { background: var(--blue); color: #fff; }

/* ─── TABLE CARD ─── */
.cm-table-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-md);
  overflow: hidden;
  animation: fadeUp .4s ease both;
}
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(18px); }
  to   { opacity: 1; transform: translateY(0); }
}

.cm-table-wrap { overflow-x: auto; }

table.cm-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}

/* thead */
.cm-table thead tr {
  background: var(--bg);
  border-bottom: 1px solid var(--border);
}
.cm-table thead th {
  padding: 13px 18px;
  font-size: 10.5px;
  font-weight: 800;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--muted);
  white-space: nowrap;
  text-align: left;
  border-bottom: 1.5px solid var(--border);
}
.cm-table thead th:first-child { padding-left: 24px; }
.cm-table thead th:last-child  { padding-right: 24px; }

/* col sort indicator */
.th-sort {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  cursor: pointer;
  user-select: none;
  transition: color .15s;
}
.th-sort:hover { color: var(--blue); }
.th-sort i { font-size: 9px; opacity: .5; }

/* tbody rows */
.cm-table tbody tr {
  transition: background .15s, transform .15s;
  cursor: default;
  animation: rowIn .35s ease both;
}
@keyframes rowIn {
  from { opacity: 0; transform: translateX(-8px); }
  to   { opacity: 1; transform: translateX(0); }
}
.cm-table tbody tr:nth-child(1)  { animation-delay: .04s; }
.cm-table tbody tr:nth-child(2)  { animation-delay: .07s; }
.cm-table tbody tr:nth-child(3)  { animation-delay: .10s; }
.cm-table tbody tr:nth-child(4)  { animation-delay: .13s; }
.cm-table tbody tr:nth-child(5)  { animation-delay: .16s; }
.cm-table tbody tr:nth-child(6)  { animation-delay: .19s; }
.cm-table tbody tr:nth-child(7)  { animation-delay: .22s; }
.cm-table tbody tr:nth-child(8)  { animation-delay: .25s; }

.cm-table tbody tr:hover {
  background: #f8faff;
}
.cm-table tbody tr:not(:last-child) td {
  border-bottom: 1px solid var(--border-l);
}

.cm-table td {
  padding: 14px 18px;
  vertical-align: middle;
  font-size: 13.5px;
  color: var(--ink-3);
}
.cm-table td:first-child { padding-left: 24px; }
.cm-table td:last-child  { padding-right: 24px; }

/* ─── Company cell ─── */
.co-cell { display: flex; align-items: center; gap: 13px; }

.co-logo {
  width: 44px; height: 44px;
  border-radius: 11px;
  overflow: hidden;
  flex-shrink: 0;
  border: 1.5px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--blue-50);
  position: relative;
}
.co-logo img { width: 100%; height: 100%; object-fit: cover; }
.co-logo-fallback {
  font-family: 'Bricolage Grotesque', sans-serif;
  font-size: 15px;
  font-weight: 800;
  color: var(--blue);
}

.co-info-name {
  font-size: 14px;
  font-weight: 700;
  color: var(--ink);
  line-height: 1.2;
  margin-bottom: 3px;
  max-width: 180px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.co-info-web {
  font-size: 11.5px;
  color: var(--muted-l);
  display: flex;
  align-items: center;
  gap: 4px;
}
.co-info-web i { font-size: 10px; }
.co-info-web a { color: var(--blue); text-decoration: none; }
.co-info-web a:hover { text-decoration: underline; }

/* contact cell */
.co-email { font-size: 13px; color: var(--ink-3); margin-bottom: 3px; }
.co-phone { font-size: 12px; color: var(--muted); display: flex; align-items: center; gap: 4px; }
.co-phone i { font-size: 10px; color: var(--muted-l); }

/* license */
.co-lic {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  font-weight: 600;
  color: var(--ink-3);
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 6px;
  padding: 4px 9px;
  font-variant-numeric: tabular-nums;
}
.co-lic i { font-size: 10px; color: var(--muted-l); }
.co-lic.none { color: var(--muted-l); font-style: italic; background: transparent; border-color: transparent; }

/* status badges */
.cm-status {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 11.5px;
  font-weight: 700;
  padding: 4px 11px;
  border-radius: 50px;
  white-space: nowrap;
  letter-spacing: .02em;
}
.cms-active   { background: var(--green-bg); color: var(--green-tx); }
.cms-inactive { background: var(--red-bg);   color: var(--red-tx);   }
.cms-pending  { background: var(--amber-bg); color: var(--amber-tx); }
.cms-status-dot {
  width: 5px; height: 5px; border-radius: 50%;
}
.cms-active   .cms-status-dot { background: var(--green); }
.cms-inactive .cms-status-dot { background: var(--red); }
.cms-pending  .cms-status-dot { background: var(--amber); }

/* properties count */
.co-prop-count {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-weight: 700;
  color: var(--blue);
}
.co-prop-bar {
  width: 48px; height: 4px;
  background: var(--blue-100);
  border-radius: 2px;
  overflow: hidden;
}
.co-prop-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--blue), var(--blue-l));
  border-radius: 2px;
  transition: width .6s ease;
}

/* date cell */
.co-date { font-size: 12.5px; color: var(--muted); }
.co-date b { display: block; font-weight: 600; color: var(--ink-3); font-size: 13px; margin-bottom: 1px; }

/* actions cell */
.co-actions { display: flex; align-items: center; gap: 4px; }
.co-action-btn {
  width: 32px; height: 32px;
  display: flex; align-items: center; justify-content: center;
  border-radius: var(--radius-sm);
  font-size: 13px;
  text-decoration: none;
  border: 1.5px solid var(--border);
  background: transparent;
  cursor: pointer;
  transition: all .15s;
  color: var(--muted);
  position: relative;
}
.co-action-btn:hover { transform: translateY(-1px); box-shadow: var(--shadow-sm); }
.co-action-btn.view:hover  { border-color: var(--blue);  color: var(--blue);  background: var(--blue-50); }
.co-action-btn.edit:hover  { border-color: var(--amber); color: var(--amber); background: var(--amber-bg); }
.co-action-btn.del:hover   { border-color: var(--red);   color: var(--red);   background: var(--red-bg); }
/* tooltip on action buttons */
.co-action-btn::before {
  content: attr(data-tip);
  position: absolute;
  bottom: calc(100% + 7px);
  left: 50%; transform: translateX(-50%);
  background: var(--ink);
  color: #fff;
  font-size: 10px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 5px;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: opacity .15s;
  font-family: 'DM Sans', sans-serif;
}
.co-action-btn::after {
  content: '';
  position: absolute;
  bottom: calc(100% + 3px);
  left: 50%; transform: translateX(-50%);
  border: 4px solid transparent;
  border-top-color: var(--ink);
  opacity: 0;
  pointer-events: none;
  transition: opacity .15s;
}
.co-action-btn:hover::before,
.co-action-btn:hover::after { opacity: 1; }

/* select checkbox */
.co-check {
  width: 17px; height: 17px;
  border: 2px solid var(--border);
  border-radius: 5px;
  appearance: none;
  cursor: pointer;
  transition: all .15s;
  position: relative;
  flex-shrink: 0;
}
.co-check:checked {
  background: var(--blue);
  border-color: var(--blue);
}
.co-check:checked::after {
  content: '';
  position: absolute;
  left: 3px; top: 1px;
  width: 8px; height: 5px;
  border-left: 2px solid #fff;
  border-bottom: 2px solid #fff;
  transform: rotate(-45deg);
}

/* ─── EMPTY STATE ─── */
.cm-empty {
  padding: 80px 40px;
  text-align: center;
}
.cm-empty-icon {
  width: 80px; height: 80px;
  background: var(--blue-50);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 32px;
  color: var(--blue);
  margin: 0 auto 20px;
}
.cm-empty h3 {
  font-family: 'Bricolage Grotesque', sans-serif;
  font-size: 22px;
  font-weight: 800;
  margin-bottom: 8px;
  color: var(--ink);
}
.cm-empty p { color: var(--muted); font-size: 14px; margin-bottom: 24px; }
.cm-empty-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 13px 26px;
  background: var(--blue);
  color: #fff;
  text-decoration: none;
  border-radius: var(--radius-sm);
  font-size: 14px;
  font-weight: 700;
  box-shadow: var(--shadow-blue);
  transition: all .2s;
}
.cm-empty-btn:hover { background: var(--blue-d); transform: translateY(-2px); }

/* ─── BULK ACTION BAR ─── */
.cm-bulk-bar {
  display: none;
  position: fixed;
  bottom: 32px;
  left: 50%; transform: translateX(-50%);
  background: var(--ink);
  color: #fff;
  border-radius: 50px;
  padding: 12px 24px;
  box-shadow: 0 20px 48px rgba(15,23,42,.4);
  gap: 16px;
  align-items: center;
  z-index: 1000;
  animation: bulkIn .3s cubic-bezier(.34,1.56,.64,1);
  min-width: 360px;
  justify-content: space-between;
}
.cm-bulk-bar.show { display: flex; }
@keyframes bulkIn {
  from { opacity: 0; transform: translateX(-50%) translateY(20px); }
  to   { opacity: 1; transform: translateX(-50%) translateY(0); }
}
.cm-bulk-count { font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
.cm-bulk-count span {
  background: var(--blue); color: #fff;
  border-radius: 50%; width: 24px; height: 24px;
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 800;
}
.cm-bulk-actions { display: flex; gap: 6px; }
.cm-bulk-btn {
  display: flex; align-items: center; gap: 6px;
  padding: 7px 14px; border-radius: 50px;
  font-size: 12px; font-weight: 700;
  cursor: pointer; border: none;
  font-family: 'DM Sans', sans-serif;
  transition: all .15s;
}
.cm-bulk-btn.del  { background: #ef4444; color: #fff; }
.cm-bulk-btn.exp  { background: rgba(255,255,255,.12); color: rgba(255,255,255,.85); }
.cm-bulk-btn:hover { transform: translateY(-1px); }
.cm-bulk-close {
  background: none; border: none; color: rgba(255,255,255,.5);
  cursor: pointer; font-size: 16px; padding: 4px; transition: color .15s;
}
.cm-bulk-close:hover { color: #fff; }

/* ─── PAGINATION ─── */
.cm-pag {
  padding: 20px 24px;
  border-top: 1px solid var(--border-l);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  background: var(--bg);
}
.cm-pag-info { font-size: 13px; color: var(--muted); }
.cm-pag-info b { color: var(--ink-3); font-weight: 700; }

/* ─── GRID VIEW ─── */
.cm-company-grid {
  display: none;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 18px;
}
.cm-company-grid.show { display: grid; }
.cm-table-card.grid-mode .cm-table-wrap { display: none; }
.cm-table-card.grid-mode .cm-company-grid { display: grid !important; padding: 24px; }

.cg-card {
  background: var(--surface);
  border: 1.5px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  transition: all .22s cubic-bezier(.16,1,.3,1);
  display: flex;
  flex-direction: column;
  animation: fadeUp .4s ease both;
}
.cg-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
  border-color: var(--blue-100);
}

.cg-card-header {
  height: 80px;
  background: linear-gradient(135deg, var(--blue-xd), var(--blue), var(--blue-l));
  position: relative;
  display: flex;
  align-items: flex-end;
  padding: 0 18px 0;
}
.cg-card-logo {
  width: 52px; height: 52px;
  border-radius: 12px;
  border: 3px solid var(--surface);
  overflow: hidden;
  background: var(--blue-50);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  transform: translateY(26px);
  box-shadow: var(--shadow-md);
}
.cg-card-logo img { width: 100%; height: 100%; object-fit: cover; }
.cg-card-logo-txt {
  font-family: 'Bricolage Grotesque', sans-serif;
  font-size: 18px; font-weight: 800; color: var(--blue);
}
.cg-card-status-badge {
  position: absolute;
  top: 12px; right: 14px;
}

.cg-card-body { padding: 36px 18px 16px; flex: 1; }
.cg-card-name {
  font-family: 'Bricolage Grotesque', sans-serif;
  font-size: 16px; font-weight: 800; color: var(--ink);
  margin-bottom: 4px; line-height: 1.25;
}
.cg-card-web { font-size: 12px; color: var(--blue); margin-bottom: 14px; }
.cg-card-meta { display: flex; gap: 16px; margin-bottom: 14px; flex-wrap: wrap; }
.cg-meta-item { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--muted); }
.cg-meta-item i { font-size: 11px; color: var(--muted-l); }

.cg-card-stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  padding: 12px;
  background: var(--bg);
  border-radius: var(--radius-sm);
  margin-bottom: 14px;
}
.cg-stat { text-align: center; }
.cg-stat-val { font-family: 'Bricolage Grotesque', sans-serif; font-size: 20px; font-weight: 800; color: var(--blue); line-height: 1; }
.cg-stat-lbl { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; font-weight: 600; margin-top: 3px; }

.cg-card-foot {
  padding: 12px 18px;
  border-top: 1px solid var(--border-l);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}
.cg-foot-date { font-size: 11.5px; color: var(--muted); display: flex; align-items: center; gap: 5px; }
.cg-foot-actions { display: flex; gap: 4px; }

/* responsive */
@media(max-width:1024px) { .cm-body { padding: 0 24px 48px; } .cm-hero-inner { padding: 40px 24px 80px; } }
@media(max-width:768px) {
  .cm-hero-inner { grid-template-columns: 1fr; }
  .cm-hero-kpis { display: flex; flex-wrap: wrap; padding-top: 16px; }
  .cm-add-btn { margin-top: 12px; }
  .cm-body { padding: 0 16px 40px; }
}
</style>

<div class="cm-page">

  {{-- ══════════ HERO ══════════ --}}
  <div class="cm-hero">
    <div class="cm-hero-mesh">
      <svg viewBox="0 0 1440 320" preserveAspectRatio="xMidYMid slice" fill="none">
        <defs>
          <radialGradient id="rg1" cx="80%" cy="30%" r="60%">
            <stop offset="0%" stop-color="rgba(96,165,250,.18)"/>
            <stop offset="100%" stop-color="transparent"/>
          </radialGradient>
          <radialGradient id="rg2" cx="10%" cy="80%" r="50%">
            <stop offset="0%" stop-color="rgba(139,92,246,.12)"/>
            <stop offset="100%" stop-color="transparent"/>
          </radialGradient>
          <pattern id="grid" width="48" height="48" patternUnits="userSpaceOnUse">
            <path d="M 48 0 L 0 0 0 48" fill="none" stroke="rgba(255,255,255,.04)" stroke-width="1"/>
          </pattern>
        </defs>
        <rect width="1440" height="320" fill="url(#grid)"/>
        <rect width="1440" height="320" fill="url(#rg1)"/>
        <rect width="1440" height="320" fill="url(#rg2)"/>
        {{-- floating circles --}}
        <circle cx="1200" cy="60" r="140" stroke="rgba(255,255,255,.05)" stroke-width="1" fill="none"/>
        <circle cx="1200" cy="60" r="200" stroke="rgba(255,255,255,.03)" stroke-width="1" fill="none"/>
        <circle cx="100"  cy="260" r="120" stroke="rgba(255,255,255,.04)" stroke-width="1" fill="none"/>
      </svg>
    </div>

    <div class="cm-hero-inner">
      <div>
        <div class="cm-breadcrumb">
          <a href="/"><i class="fas fa-home"></i></a>
          <i class="fas fa-chevron-right"></i>
          <a href="{{ route('admin.dashboard') }}">Admin</a>
          <i class="fas fa-chevron-right"></i>
          <span>Companies</span>
        </div>

        <h1 class="cm-hero-title">
          Companies<br><span>Management</span>
        </h1>
        <p class="cm-hero-sub">
          Oversee all real estate development companies — track listings, verify licenses, and manage statuses from one unified dashboard.
        </p>

        {{-- Search --}}
        <form method="GET" action="{{ route('admin.companies.index') }}">
          <div class="cm-hero-search">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Search by name, email, license number…" value="{{ request('search') }}">
            <button type="submit" class="cm-hero-search-btn">
              <i class="fas fa-search"></i> Search
            </button>
          </div>
        </form>

        {{-- Quick tabs --}}
        <div class="cm-hero-tabs">
          <button class="cm-hero-tab on" onclick="filterStatus(this,'')">
            <span class="dot dot-all"></span> All Companies
          </button>
          <button class="cm-hero-tab" onclick="filterStatus(this,'active')">
            <span class="dot dot-active"></span> Active
          </button>
          <button class="cm-hero-tab" onclick="filterStatus(this,'inactive')">
            <span class="dot dot-inactive"></span> Inactive
          </button>
          <button class="cm-hero-tab" onclick="filterStatus(this,'pending')">
            <span class="dot dot-pending"></span> Pending
          </button>
        </div>
      </div>

      {{-- KPIs --}}
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:16px;padding-top:28px">
        <a href="{{ route('admin.companies.create') }}" class="cm-add-btn">
          <span class="cm-add-btn-icon"><i class="fas fa-plus"></i></span>
          Add New Company
        </a>
        <div class="cm-hero-kpis">
          <div class="cm-kpi">
            <div class="cm-kpi-val">{{ $companies->total() }}<sup></sup></div>
            <div class="cm-kpi-lbl">Total</div>
            <div class="cm-kpi-trend"><i class="fas fa-arrow-up"></i> +8 this month</div>
          </div>
          <div class="cm-kpi">
            <div class="cm-kpi-val" style="color:#4ade80">{{ $companies->where('status','active')->count() }}</div>
            <div class="cm-kpi-lbl">Active</div>
            <div class="cm-kpi-trend"><i class="fas fa-circle"></i> Live now</div>
          </div>
          <div class="cm-kpi">
            <div class="cm-kpi-val" style="color:#f87171">{{ $companies->where('status','inactive')->count() }}</div>
            <div class="cm-kpi-lbl">Inactive</div>
            <div class="cm-kpi-trend down"><i class="fas fa-minus"></i> No change</div>
          </div>
          <div class="cm-kpi">
            <div class="cm-kpi-val" style="color:#fbbf24">{{ $companies->where('status','pending')->count() }}</div>
            <div class="cm-kpi-lbl">Pending</div>
            <div class="cm-kpi-trend" style="color:#fbbf24"><i class="fas fa-clock"></i> Review needed</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Wave --}}
    <svg class="cm-hero-wave" viewBox="0 0 1440 80" preserveAspectRatio="none" fill="#f8fafc">
      <path d="M0,80 C360,0 1080,80 1440,0 L1440,80 L0,80 Z"/>
    </svg>
  </div>

  {{-- ══════════ BODY ══════════ --}}
  <div class="cm-body">

    {{-- Toolbar --}}
    <div class="cm-toolbar">
      <div class="cm-toolbar-count">
        <span>{{ $companies->total() }}</span> Companies
        <small>· Showing {{ $companies->firstItem() }}–{{ $companies->lastItem() }}</small>
      </div>
      <div class="cm-toolbar-right">
        <select class="cm-select" onchange="sortTable(this.value)">
          <option value="newest">Newest First</option>
          <option value="oldest">Oldest First</option>
          <option value="name">Name A–Z</option>
          <option value="properties">Most Properties</option>
        </select>
        <select class="cm-select" onchange="filterByStatus(this.value)">
          <option value="">All Statuses</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
          <option value="pending">Pending</option>
        </select>
        <div class="cm-view-toggle">
          <button class="cm-view-btn on" id="tableViewBtn" onclick="setView('table')" title="Table view"><i class="fas fa-list"></i></button>
          <button class="cm-view-btn"    id="gridViewBtn"  onclick="setView('grid')"  title="Grid view"><i class="fas fa-th-large"></i></button>
        </div>
      </div>
    </div>

    {{-- Table Card --}}
    <div class="cm-table-card" id="mainCard">

      {{-- ── TABLE VIEW ── --}}
      <div class="cm-table-wrap" id="tableView">
        <table class="cm-table">
          <thead>
            <tr>
              <th style="width:36px"><input type="checkbox" class="co-check" id="checkAll" onclick="toggleAll(this)"></th>
              <th><span class="th-sort">Company <i class="fas fa-sort"></i></span></th>
              <th><span class="th-sort">Contact <i class="fas fa-sort"></i></span></th>
              <th>License</th>
              <th><span class="th-sort">Status <i class="fas fa-sort"></i></span></th>
              <th><span class="th-sort">Properties <i class="fas fa-sort"></i></span></th>
              <th><span class="th-sort">Joined <i class="fas fa-sort"></i></span></th>
              <th style="text-align:center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($companies as $i => $company)
            <tr data-status="{{ $company->status ?? 'active' }}">
              {{-- Checkbox --}}
              <td><input type="checkbox" class="co-check row-check" onchange="updateBulk()"></td>

              {{-- Company --}}
              <td>
                <div class="co-cell">
                  <div class="co-logo">
                    @if($company->logo)
                      <img src="{{ $company->logo }}" alt="{{ $company->name }}">
                    @else
                      <span class="co-logo-fallback">{{ strtoupper(substr($company->name,0,2)) }}</span>
                    @endif
                  </div>
                  <div>
                    <div class="co-info-name">{{ $company->name }}</div>
                    <div class="co-info-web">
                      @if($company->website ?? null)
                        <i class="fas fa-globe"></i>
                        <a href="{{ $company->website }}" target="_blank">{{ parse_url($company->website, PHP_URL_HOST) ?? $company->website }}</a>
                      @else
                        <i class="fas fa-link"></i> <span style="color:var(--muted-l)">No website</span>
                      @endif
                    </div>
                  </div>
                </div>
              </td>

              {{-- Contact --}}
              <td>
                <div class="co-email">{{ $company->email }}</div>
                <div class="co-phone">
                  <i class="fas fa-phone"></i>
                  {{ $company->phone ?? 'Not provided' }}
                </div>
              </td>

              {{-- License --}}
              <td>
                @if($company->license_number ?? null)
                  <span class="co-lic"><i class="fas fa-certificate"></i>{{ $company->license_number }}</span>
                @else
                  <span class="co-lic none">No license</span>
                @endif
              </td>

              {{-- Status --}}
              <td>
                @php $st = $company->status ?? 'active'; @endphp
                <span class="cm-status cms-{{ $st }}">
                  <span class="cms-status-dot"></span>
                  {{ ucfirst($st) }}
                </span>
              </td>

              {{-- Properties --}}
              <td>
                @php $cnt = $company->properties_count ?? 0; $pct = min($cnt / max($companies->max('properties_count') ?? 1, 1) * 100, 100); @endphp
                <div class="co-prop-count">
                  {{ $cnt }}
                </div>
                <div class="co-prop-bar" style="margin-top:5px">
                  <div class="co-prop-bar-fill" style="width:{{ $pct }}%"></div>
                </div>
              </td>

              {{-- Joined --}}
              <td>
                <div class="co-date">
                  <b>{{ $company->created_at->format('M j, Y') }}</b>
                  {{ $company->created_at->diffForHumans() }}
                </div>
              </td>

              {{-- Actions --}}
              <td>
                <div class="co-actions" style="justify-content:center">
                  <a href="{{ route('admin.companies.show', $company->id) }}" class="co-action-btn view" data-tip="View">
                    <i class="fas fa-eye"></i>
                  </a>
                  <a href="{{ route('admin.companies.edit', $company->id) }}" class="co-action-btn edit" data-tip="Edit">
                    <i class="fas fa-pencil"></i>
                  </a>
                  <form action="{{ route('admin.companies.destroy', $company->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(event,'{{ $company->name }}')">
                    @csrf @method('DELETE')
                    <button type="submit" class="co-action-btn del" data-tip="Delete">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8">
                <div class="cm-empty">
                  <div class="cm-empty-icon"><i class="fas fa-building"></i></div>
                  <h3>No Companies Found</h3>
                  <p>Start adding real estate companies to your platform.</p>
                  <a href="{{ route('admin.companies.create') }}" class="cm-empty-btn">
                    <i class="fas fa-plus"></i> Add First Company
                  </a>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- ── GRID VIEW (rendered from same data) ── --}}
      <div id="gridView" style="display:none; padding:24px">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:18px">
          @foreach($companies as $company)
          @php $st = $company->status ?? 'active'; @endphp
          <div class="cg-card">
            <div class="cg-card-header">
              <div class="cg-card-logo">
                @if($company->logo)
                  <img src="{{ $company->logo }}" alt="{{ $company->name }}">
                @else
                  <span class="cg-card-logo-txt">{{ strtoupper(substr($company->name,0,2)) }}</span>
                @endif
              </div>
              <div class="cg-card-status-badge">
                <span class="cm-status cms-{{ $st }}">
                  <span class="cms-status-dot"></span>{{ ucfirst($st) }}
                </span>
              </div>
            </div>
            <div class="cg-card-body">
              <div class="cg-card-name">{{ $company->name }}</div>
              @if($company->website ?? null)
                <div class="cg-card-web"><i class="fas fa-globe" style="font-size:10px;margin-right:4px"></i>{{ parse_url($company->website, PHP_URL_HOST) ?? $company->website }}</div>
              @endif
              <div class="cg-card-meta">
                <span class="cg-meta-item"><i class="fas fa-envelope"></i>{{ $company->email }}</span>
                @if($company->phone ?? null)
                  <span class="cg-meta-item"><i class="fas fa-phone"></i>{{ $company->phone }}</span>
                @endif
                @if($company->license_number ?? null)
                  <span class="cg-meta-item"><i class="fas fa-certificate"></i>{{ $company->license_number }}</span>
                @endif
              </div>
              <div class="cg-card-stats">
                <div class="cg-stat">
                  <div class="cg-stat-val">{{ $company->properties_count ?? 0 }}</div>
                  <div class="cg-stat-lbl">Properties</div>
                </div>
                <div class="cg-stat">
                  <div class="cg-stat-val" style="color:var(--green)">{{ $company->agents_count ?? 0 }}</div>
                  <div class="cg-stat-lbl">Agents</div>
                </div>
              </div>
            </div>
            <div class="cg-card-foot">
              <span class="cg-foot-date">
                <i class="fas fa-calendar" style="font-size:10px"></i>
                {{ $company->created_at->format('M Y') }}
              </span>
              <div class="cg-foot-actions">
                <a href="{{ route('admin.companies.show', $company->id) }}" class="co-action-btn view" data-tip="View"><i class="fas fa-eye"></i></a>
                <a href="{{ route('admin.companies.edit', $company->id) }}" class="co-action-btn edit" data-tip="Edit"><i class="fas fa-pencil"></i></a>
                <form action="{{ route('admin.companies.destroy', $company->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(event,'{{ $company->name }}')">
                  @csrf @method('DELETE')
                  <button type="submit" class="co-action-btn del" data-tip="Delete"><i class="fas fa-trash-alt"></i></button>
                </form>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Pagination --}}
      @if ($companies->hasPages())
        <div class="cm-pag">
          <div class="cm-pag-info">
            Showing <b>{{ $companies->firstItem() }}</b> – <b>{{ $companies->lastItem() }}</b> of <b>{{ $companies->total() }}</b> companies
          </div>
          {{ $companies->links() }}
        </div>
      @endif
    </div>
  </div>

  {{-- ══════════ BULK ACTION BAR ══════════ --}}
  <div class="cm-bulk-bar" id="bulkBar">
    <div class="cm-bulk-count">
      <span id="bulkCount">0</span> companies selected
    </div>
    <div class="cm-bulk-actions">
      <button class="cm-bulk-btn exp" onclick="bulkExport()"><i class="fas fa-download"></i> Export</button>
      <button class="cm-bulk-btn del" onclick="bulkDelete()"><i class="fas fa-trash-alt"></i> Delete</button>
    </div>
    <button class="cm-bulk-close" onclick="clearBulk()"><i class="fas fa-times"></i></button>
  </div>
</div>

{{-- ══════════ CONFIRM MODAL ══════════ --}}
<div id="confirmModal" style="
  display:none; position:fixed; inset:0; z-index:9999;
  background:rgba(15,23,42,.5); backdrop-filter:blur(4px);
  align-items:center; justify-content:center;
">
  <div style="
    background:#fff; border-radius:16px; padding:32px; max-width:420px; width:90%;
    box-shadow:0 32px 80px rgba(15,23,42,.25); text-align:center;
    animation:fadeUp .25s ease;
  ">
    <div style="width:60px;height:60px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;font-size:24px;color:#ef4444">
      <i class="fas fa-exclamation-triangle"></i>
    </div>
    <h3 style="font-family:'Bricolage Grotesque',sans-serif;font-size:20px;font-weight:800;color:#0f172a;margin-bottom:8px">Delete Company?</h3>
    <p style="font-size:14px;color:#64748b;margin-bottom:24px" id="confirmMsg">This action cannot be undone.</p>
    <div style="display:flex;gap:10px;justify-content:center">
      <button onclick="closeConfirm()" style="padding:10px 24px;border:1.5px solid #e2e8f0;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:600;cursor:pointer;background:#fff;color:#334155">Cancel</button>
      <button id="confirmOk" style="padding:10px 24px;background:#ef4444;border:none;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;cursor:pointer;color:#fff;box-shadow:0 4px 14px rgba(239,68,68,.3)">Delete</button>
    </div>
  </div>
</div>

<script>
/* ── View toggle ── */
function setView(mode) {
  const tv = document.getElementById('tableView');
  const gv = document.getElementById('gridView');
  document.getElementById('tableViewBtn').classList.toggle('on', mode === 'table');
  document.getElementById('gridViewBtn').classList.toggle('on', mode === 'grid');
  tv.style.display = mode === 'table' ? '' : 'none';
  gv.style.display = mode === 'grid'  ? '' : 'none';
}

/* ── Filter quick tabs ── */
function filterStatus(btn, status) {
  document.querySelectorAll('.cm-hero-tab').forEach(t => t.classList.remove('on'));
  btn.classList.add('on');
  document.querySelectorAll('tbody tr[data-status]').forEach(r => {
    r.style.display = (!status || r.dataset.status === status) ? '' : 'none';
  });
}

function filterByStatus(val) {
  document.querySelectorAll('tbody tr[data-status]').forEach(r => {
    r.style.display = (!val || r.dataset.status === val) ? '' : 'none';
  });
}

/* ── Bulk actions ── */
function toggleAll(cb) {
  document.querySelectorAll('.row-check').forEach(c => { c.checked = cb.checked; });
  updateBulk();
}
function updateBulk() {
  const checked = document.querySelectorAll('.row-check:checked').length;
  const bar = document.getElementById('bulkBar');
  const cnt = document.getElementById('bulkCount');
  cnt.textContent = checked;
  bar.classList.toggle('show', checked > 0);
}
function clearBulk() {
  document.querySelectorAll('.row-check, #checkAll').forEach(c => c.checked = false);
  document.getElementById('bulkBar').classList.remove('show');
}
function bulkExport() { alert('Export functionality — connect to your export route.'); }
function bulkDelete() {
  if (confirm('Delete all selected companies? This cannot be undone.')) {
    clearBulk();
  }
}

/* ── Delete confirm modal ── */
let _pendingForm = null;
function confirmDelete(e, name) {
  e.preventDefault();
  _pendingForm = e.target;
  document.getElementById('confirmMsg').textContent = `Delete "${name}"? This action cannot be undone.`;
  const modal = document.getElementById('confirmModal');
  modal.style.display = 'flex';
  document.getElementById('confirmOk').onclick = () => { modal.style.display = 'none'; _pendingForm.submit(); };
  return false;
}
function closeConfirm() { document.getElementById('confirmModal').style.display = 'none'; _pendingForm = null; }
document.getElementById('confirmModal').addEventListener('click', function(e) {
  if (e.target === this) closeConfirm();
});

/* ── Progress bars animate on load ── */
window.addEventListener('load', () => {
  document.querySelectorAll('.co-prop-bar-fill').forEach(el => {
    const w = el.style.width;
    el.style.width = '0';
    setTimeout(() => { el.style.width = w; }, 300);
  });
});

/* ── Sort stub ── */
function sortTable(val) { /* connect to backend */ }
</script>

@endsection