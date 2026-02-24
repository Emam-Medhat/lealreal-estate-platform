<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ __('dashboard.control_center') }} — Real Estate Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link
    href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Cairo:wght@300;400;600;700;900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    *,
    *::before,
    *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --blue: #0D6EFD;
      --blue2: #0B5ED7;
      --blue-g: linear-gradient(135deg, #0D6EFD, #0a58ca);
      --purple: #6F42C1;
      --purple-g: linear-gradient(135deg, #6F42C1, #59359a);
      --green: #198754;
      --green-g: linear-gradient(135deg, #198754, #146c43);
      --orange: #FD7E14;
      --orange-g: linear-gradient(135deg, #FD7E14, #dc6a0c);
      --teal: #0DCAF0;
      --red: #DC3545;
      --yellow: #FFC107;
      --sc-blue: linear-gradient(135deg, #0D6EFD 0%, #0a58ca 100%);
      --sc-purple: linear-gradient(135deg, #6F42C1 0%, #59359a 100%);
      --sc-green: linear-gradient(135deg, #198754 0%, #146c43 100%);
      --sc-orange: linear-gradient(135deg, #FD7E14 0%, #dc6a0c 100%);
      --bg: #F1F3F8;
      --bg2: #EAECF3;
      --card: #FFFFFF;
      --card2: #F8F9FC;
      --t1: #1A1F36;
      --t2: #374151;
      --t3: #6B7280;
      --t4: #9CA3AF;
      --bd: #E5E7EB;
      --bd2: #D1D5DB;
      --bd-blue: rgba(13, 110, 253, .25);
      --sh-sm: 0 1px 4px rgba(0, 0, 0, .06), 0 2px 8px rgba(0, 0, 0, .04);
      --sh-md: 0 4px 16px rgba(0, 0, 0, .08), 0 2px 6px rgba(0, 0, 0, .05);
      --sh-lg: 0 8px 32px rgba(0, 0, 0, .1);
      --sh-blue: 0 6px 24px rgba(13, 110, 253, .3);
      --sh-purple: 0 6px 24px rgba(111, 66, 193, .25);
      --sh-green: 0 6px 24px rgba(25, 135, 84, .25);
      --sh-orange: 0 6px 24px rgba(253, 126, 20, .25);
      --r1: 10px;
      --r2: 14px;
      --r3: 18px;
      --r4: 24px;
      --tr: all .25s cubic-bezier(.4, 0, .2, 1);
      --tr-sp: all .35s cubic-bezier(.34, 1.3, .64, 1);
      --ff: 'Cairo', 'Plus Jakarta Sans', system-ui, sans-serif;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: var(--ff);
      background: var(--bg);
      color: var(--t1);
      min-height: 100vh;
      direction: rtl;
      overflow-x: hidden;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background:
        radial-gradient(ellipse 60% 40% at 80% 5%, rgba(13, 110, 253, .04) 0%, transparent 55%),
        radial-gradient(ellipse 50% 50% at 5% 90%, rgba(111, 66, 193, .03) 0%, transparent 55%);
      pointer-events: none;
      z-index: 0;
    }

    .shell {
      display: flex;
      min-height: 100vh;
      position: relative;
      z-index: 1;
    }

    /* SIDEBAR */
    .sidebar {
      width: 250px;
      flex-shrink: 0;
      background: var(--card);
      border-left: 1px solid var(--bd);
      display: flex;
      flex-direction: column;
      position: sticky;
      top: 0;
      height: 100vh;
      overflow-y: auto;
      overflow-x: hidden;
      box-shadow: 2px 0 12px rgba(0, 0, 0, .04);
    }

    .sidebar::-webkit-scrollbar {
      width: 3px;
    }

    .sidebar::-webkit-scrollbar-thumb {
      background: var(--bd2);
      border-radius: 10px;
    }

    .sb-logo {
      padding: 22px 18px 20px;
      border-bottom: 1px solid var(--bd);
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .sb-logo-mark {
      width: 38px;
      height: 38px;
      border-radius: var(--r1);
      background: var(--blue-g);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      color: white;
      flex-shrink: 0;
      box-shadow: var(--sh-blue);
    }

    .sb-brand {
      font-size: 17px;
      font-weight: 800;
      color: var(--t1);
    }

    .sb-brand span {
      color: var(--blue);
    }

    .sb-tagline {
      font-size: 10px;
      color: var(--t4);
      font-weight: 500;
    }

    .sb-nav {
      padding: 14px 10px;
      flex: 1;
    }

    .sb-sec {
      font-size: 9.5px;
      font-weight: 700;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: var(--t4);
      padding: 0 10px;
      margin: 16px 0 5px;
    }

    .sb-sec:first-child {
      margin-top: 0;
    }

    .sb-link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 12px;
      border-radius: var(--r1);
      text-decoration: none;
      color: var(--t3);
      font-size: 13px;
      font-weight: 600;
      transition: var(--tr);
      margin-bottom: 2px;
    }

    .sb-link:hover {
      background: var(--bg);
      color: var(--t2);
    }

    .sb-link.active {
      background: rgba(13, 110, 253, .08);
      color: var(--blue);
    }

    .sb-link i {
      font-size: 13px;
      width: 17px;
      text-align: center;
      color: var(--t4);
      transition: color .2s;
      flex-shrink: 0;
    }

    .sb-link:hover i,
    .sb-link.active i {
      color: var(--blue);
    }

    .sb-badge {
      margin-right: auto;
      background: var(--blue);
      color: white;
      font-size: 10px;
      font-weight: 700;
      padding: 2px 7px;
      border-radius: 20px;
    }

    .sb-foot {
      padding: 12px 14px 18px;
      border-top: 1px solid var(--bd);
    }

    .sb-user {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .sb-av {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: var(--blue-g);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      color: white;
      font-weight: 700;
      flex-shrink: 0;
    }

    .sb-uname {
      font-size: 13px;
      font-weight: 700;
      color: var(--t1);
    }

    .sb-urole {
      font-size: 10.5px;
      color: var(--t4);
    }

    /* MAIN */
    .main {
      flex: 1;
      overflow-x: hidden;
      display: flex;
      flex-direction: column;
    }

    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 30px;
      background: var(--card);
      border-bottom: 1px solid var(--bd);
      position: sticky;
      top: 0;
      z-index: 100;
      gap: 16px;
      box-shadow: var(--sh-sm);
    }

    .topbar-eyebrow {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: var(--blue);
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 3px;
    }

    .topbar-eyebrow::before {
      content: '';
      display: inline-block;
      width: 16px;
      height: 2px;
      background: var(--blue);
      border-radius: 2px;
    }

    .topbar-title {
      font-size: clamp(18px, 2vw, 26px);
      font-weight: 800;
      color: var(--t1);
    }

    .topbar-title em {
      font-style: italic;
      color: var(--blue);
    }

    .topbar-right {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-shrink: 0;
    }

    .live-pill {
      display: flex;
      align-items: center;
      gap: 7px;
      padding: 5px 13px;
      background: rgba(25, 135, 84, .08);
      border: 1px solid rgba(25, 135, 84, .2);
      border-radius: 30px;
      font-size: 11px;
      font-weight: 700;
      color: var(--green);
    }

    .live-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: var(--green);
      animation: liveping 2s ease-in-out infinite;
    }

    @keyframes liveping {

      0%,
      100% {
        box-shadow: 0 0 0 0 rgba(25, 135, 84, .4);
      }

      60% {
        box-shadow: 0 0 0 6px rgba(25, 135, 84, 0);
      }
    }

    .tbtn {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 7px 17px;
      border-radius: var(--r1);
      font-size: 12.5px;
      font-weight: 700;
      cursor: pointer;
      border: none;
      font-family: var(--ff);
      text-decoration: none;
      transition: var(--tr);
    }

    .tbtn-ghost {
      background: var(--bg);
      color: var(--t3);
      border: 1px solid var(--bd);
    }

    .tbtn-ghost:hover {
      background: var(--bg2);
      color: var(--t2);
    }

    .tbtn-primary {
      background: var(--blue-g);
      color: white;
      box-shadow: var(--sh-blue);
    }

    .tbtn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 28px rgba(13, 110, 253, .35);
    }

    .content {
      padding: 26px 30px 60px;
      flex: 1;
    }

    /* STAT CARDS */
    .stat-row {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      margin-bottom: 22px;
    }

    .sc {
      border-radius: var(--r3);
      padding: 22px 20px;
      position: relative;
      overflow: hidden;
      transition: var(--tr-sp);
      color: white;
    }

    .sc:hover {
      transform: translateY(-6px) scale(1.015);
    }

    .sc-blue {
      background: var(--sc-blue);
      box-shadow: var(--sh-blue);
    }

    .sc-purple {
      background: var(--sc-purple);
      box-shadow: var(--sh-purple);
    }

    .sc-green {
      background: var(--sc-green);
      box-shadow: var(--sh-green);
    }

    .sc-orange {
      background: var(--sc-orange);
      box-shadow: var(--sh-orange);
    }

    .sc:hover.sc-blue {
      box-shadow: 0 16px 40px rgba(13, 110, 253, .4);
    }

    .sc:hover.sc-purple {
      box-shadow: 0 16px 40px rgba(111, 66, 193, .35);
    }

    .sc:hover.sc-green {
      box-shadow: 0 16px 40px rgba(25, 135, 84, .35);
    }

    .sc:hover.sc-orange {
      box-shadow: 0 16px 40px rgba(253, 126, 20, .35);
    }

    .sc::before {
      content: '';
      position: absolute;
      top: -30px;
      left: -30px;
      width: 110px;
      height: 110px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .08);
      pointer-events: none;
    }

    .sc::after {
      content: '';
      position: absolute;
      bottom: -20px;
      right: -20px;
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .06);
      pointer-events: none;
    }

    .sc-head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      margin-bottom: 14px;
      position: relative;
      z-index: 1;
    }

    .sc-label {
      font-size: 12px;
      font-weight: 700;
      opacity: .88;
    }

    .sc-ic {
      width: 42px;
      height: 42px;
      border-radius: var(--r1);
      background: rgba(255, 255, 255, .18);
      border: 1px solid rgba(255, 255, 255, .25);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 17px;
      transition: var(--tr-sp);
      flex-shrink: 0;
    }

    .sc:hover .sc-ic {
      transform: scale(1.12) rotate(8deg);
    }

    .sc-val {
      font-size: clamp(26px, 3vw, 38px);
      font-weight: 800;
      line-height: 1;
      margin-bottom: 10px;
      position: relative;
      z-index: 1;
    }

    .sc-footer {
      display: flex;
      align-items: center;
      gap: 8px;
      position: relative;
      z-index: 1;
    }

    .sc-delta {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 8px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 700;
      background: rgba(255, 255, 255, .18);
      border: 1px solid rgba(255, 255, 255, .22);
    }

    .sc-ftext {
      font-size: 11.5px;
      opacity: .8;
    }

    /* PANELS */
    .panel {
      background: var(--card);
      border: 1px solid var(--bd);
      border-radius: var(--r3);
      overflow: hidden;
      box-shadow: var(--sh-sm);
      transition: var(--tr);
    }

    .panel:hover {
      box-shadow: var(--sh-md);
    }

    .panel-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 20px;
      border-bottom: 1px solid var(--bd);
    }

    .panel-title {
      font-size: 14.5px;
      font-weight: 800;
      color: var(--t1);
    }

    .panel-sub {
      font-size: 11px;
      color: var(--t4);
      margin-top: 2px;
    }

    .panel-body {
      padding: 16px 20px;
    }

    .p-badge {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      padding: 4px 11px;
      border-radius: 20px;
      border: 1px solid;
    }

    .pb-blue {
      background: rgba(13, 110, 253, .08);
      color: var(--blue);
      border-color: rgba(13, 110, 253, .2);
    }

    .pb-purple {
      background: rgba(111, 66, 193, .08);
      color: var(--purple);
      border-color: rgba(111, 66, 193, .2);
    }

    .pb-green {
      background: rgba(25, 135, 84, .08);
      color: var(--green);
      border-color: rgba(25, 135, 84, .2);
    }

    .pb-orange {
      background: rgba(253, 126, 20, .08);
      color: var(--orange);
      border-color: rgba(253, 126, 20, .2);
    }

    /* CHARTS */
    .charts-row {
      display: grid;
      grid-template-columns: 1.6fr 1fr;
      gap: 16px;
      margin-bottom: 18px;
    }

    .chart-wrap {
      position: relative;
      height: 195px;
    }

    /* MIDDLE ROW */
    .mid-row {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 16px;
      margin-bottom: 18px;
    }

    .ri-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 6px;
      border-radius: var(--r1);
      transition: background .18s;
    }

    .ri-item:hover {
      background: var(--bg);
    }

    .ri-av {
      width: 32px;
      height: 32px;
      border-radius: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      flex-shrink: 0;
    }

    .ra-blue {
      background: rgba(13, 110, 253, .1);
      color: var(--blue);
    }

    .ra-purple {
      background: rgba(111, 66, 193, .1);
      color: var(--purple);
    }

    .ra-green {
      background: rgba(25, 135, 84, .1);
      color: var(--green);
    }

    .ra-orange {
      background: rgba(253, 126, 20, .1);
      color: var(--orange);
    }

    .ri-name {
      font-size: 12.5px;
      font-weight: 700;
      color: var(--t1);
    }

    .ri-sub {
      font-size: 11px;
      color: var(--t4);
      margin-top: 1px;
    }

    .ri-time {
      font-size: 10.5px;
      color: var(--t4);
      white-space: nowrap;
      margin-right: auto;
      flex-shrink: 0;
    }

    .sys-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 9px 11px;
      border-radius: var(--r1);
      border: 1px solid var(--bd);
      margin-bottom: 6px;
      transition: var(--tr);
    }

    .sys-row:last-child {
      margin-bottom: 0;
    }

    .sys-row:hover {
      border-color: var(--bd-blue);
      background: rgba(13, 110, 253, .02);
    }

    .sys-left {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .sys-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
    }

    .sd-ok {
      background: var(--green);
      box-shadow: 0 0 0 3px rgba(25, 135, 84, .15);
    }

    .sd-warn {
      background: var(--yellow);
      box-shadow: 0 0 0 3px rgba(255, 193, 7, .15);
    }

    .sys-name {
      font-size: 12px;
      font-weight: 600;
      color: var(--t2);
    }

    .sys-tag {
      font-size: 10.5px;
      font-weight: 700;
      padding: 3px 10px;
      border-radius: 20px;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .st-ok {
      background: rgba(25, 135, 84, .1);
      color: var(--green);
    }

    .st-warn {
      background: rgba(255, 193, 7, .12);
      color: #92740A;
    }

    /* QUICK ACTIONS */
    .qa-row {
      display: grid;
      grid-template-columns: repeat(6, 1fr);
      gap: 12px;
    }

    .qa-card {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      padding: 16px 10px;
      background: var(--card2);
      border: 1px solid var(--bd);
      border-radius: var(--r2);
      text-decoration: none;
      cursor: pointer;
      transition: var(--tr-sp);
      font-family: var(--ff);
    }

    .qa-card:hover {
      transform: translateY(-4px);
      background: var(--card);
      border-color: var(--bd-blue);
      box-shadow: var(--sh-md);
    }

    .qa-ic {
      width: 40px;
      height: 40px;
      border-radius: var(--r1);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      transition: var(--tr-sp);
    }

    .qa-card:hover .qa-ic {
      transform: scale(1.12) rotate(5deg);
    }

    .qic-blue {
      background: rgba(13, 110, 253, .1);
      color: var(--blue);
    }

    .qic-purple {
      background: rgba(111, 66, 193, .1);
      color: var(--purple);
    }

    .qic-green {
      background: rgba(25, 135, 84, .1);
      color: var(--green);
    }

    .qic-orange {
      background: rgba(253, 126, 20, .1);
      color: var(--orange);
    }

    .qic-teal {
      background: rgba(13, 202, 240, .12);
      color: #0891b2;
    }

    .qic-red {
      background: rgba(220, 53, 69, .1);
      color: var(--red);
    }

    .qa-label {
      font-size: 11px;
      font-weight: 700;
      color: var(--t3);
      text-align: center;
      line-height: 1.4;
      transition: color .18s;
    }

    .qa-card:hover .qa-label {
      color: var(--t1);
    }

    /* SECTION */
    .sec-head {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 16px;
    }

    .sec-accent {
      width: 3px;
      height: 24px;
      background: var(--blue-g);
      border-radius: 2px;
      flex-shrink: 0;
    }

    .sec-title {
      font-size: 17px;
      font-weight: 800;
      color: var(--t1);
    }

    .sec-pill {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .14em;
      text-transform: uppercase;
      color: var(--t4);
      background: var(--bg2);
      padding: 3px 11px;
      border-radius: 20px;
      border: 1px solid var(--bd);
    }

    .sec-div {
      height: 1px;
      background: linear-gradient(90deg, transparent, var(--bd2) 30%, var(--bd2) 70%, transparent);
      margin: 24px 0;
    }

    /* HUB GRID */
    .hub-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 14px;
    }

    .hc {
      background: var(--card);
      border: 1px solid var(--bd);
      border-radius: var(--r3);
      overflow: hidden;
      transition: var(--tr);
      position: relative;
      box-shadow: var(--sh-sm);
    }

    .hc::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      opacity: 0;
      transition: opacity .3s;
    }

    .hc:hover {
      transform: translateY(-4px);
    }

    .hc:hover::before {
      opacity: 1;
    }

    .hc-blue::before {
      background: var(--blue-g);
    }

    .hc-blue:hover {
      border-color: rgba(13, 110, 253, .2);
      box-shadow: 0 10px 28px rgba(13, 110, 253, .1);
    }

    .hc-purple::before {
      background: var(--purple-g);
    }

    .hc-purple:hover {
      border-color: rgba(111, 66, 193, .2);
      box-shadow: 0 10px 28px rgba(111, 66, 193, .1);
    }

    .hc-green::before {
      background: var(--green-g);
    }

    .hc-green:hover {
      border-color: rgba(25, 135, 84, .2);
      box-shadow: 0 10px 28px rgba(25, 135, 84, .1);
    }

    .hc-orange::before {
      background: var(--orange-g);
    }

    .hc-orange:hover {
      border-color: rgba(253, 126, 20, .2);
      box-shadow: 0 10px 28px rgba(253, 126, 20, .1);
    }

    .hc-head {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 16px 16px 13px;
      border-bottom: 1px solid var(--bd);
    }

    .hc-icon {
      width: 40px;
      height: 40px;
      border-radius: var(--r1);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      flex-shrink: 0;
      transition: var(--tr-sp);
    }

    .hc:hover .hc-icon {
      transform: scale(1.1) rotate(5deg);
    }

    .hi-blue {
      background: rgba(13, 110, 253, .1);
      color: var(--blue);
      border: 1px solid rgba(13, 110, 253, .18);
    }

    .hi-purple {
      background: rgba(111, 66, 193, .1);
      color: var(--purple);
      border: 1px solid rgba(111, 66, 193, .18);
    }

    .hi-green {
      background: rgba(25, 135, 84, .1);
      color: var(--green);
      border: 1px solid rgba(25, 135, 84, .18);
    }

    .hi-orange {
      background: rgba(253, 126, 20, .1);
      color: var(--orange);
      border: 1px solid rgba(253, 126, 20, .18);
    }

    .hi-teal {
      background: rgba(13, 202, 240, .1);
      color: #0891b2;
      border: 1px solid rgba(13, 202, 240, .18);
    }

    .hi-red {
      background: rgba(220, 53, 69, .1);
      color: var(--red);
      border: 1px solid rgba(220, 53, 69, .18);
    }

    .hi-gray {
      background: var(--bg2);
      color: var(--t3);
      border: 1px solid var(--bd);
    }

    .hc-title {
      font-size: 13px;
      font-weight: 800;
      color: var(--t1);
    }

    .hc-sub {
      font-size: 10px;
      color: var(--t4);
      margin-top: 2px;
      font-weight: 600;
      letter-spacing: .04em;
    }

    .hc-links {
      padding: 6px 8px 10px;
    }

    .hc-link {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 7px 10px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 12px;
      font-weight: 600;
      color: var(--t3);
      transition: var(--tr);
    }

    .hc-link:hover {
      background: rgba(13, 110, 253, .05);
      color: var(--blue);
      padding-right: 14px;
    }

    .hc-link i {
      font-size: 11px;
      width: 14px;
      text-align: center;
      color: var(--t4);
      transition: color .18s;
      flex-shrink: 0;
    }

    .hc-link:hover i {
      color: var(--blue);
    }

    .hc-arr {
      margin-right: auto;
      font-size: 9px;
      color: var(--blue);
      opacity: 0;
      transition: opacity .18s, transform .18s;
    }

    .hc-link:hover .hc-arr {
      opacity: 1;
      transform: translateX(-3px);
    }

    /* ACTIVITY */
    .act-item {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 11px 8px;
      border-bottom: 1px solid var(--bd);
      transition: background .18s;
    }

    .act-item:last-child {
      border-bottom: none;
    }

    .act-item:hover {
      background: var(--bg);
      border-radius: var(--r1);
    }

    .act-ic {
      width: 32px;
      height: 32px;
      border-radius: 9px;
      background: rgba(13, 110, 253, .1);
      color: var(--blue);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      flex-shrink: 0;
    }

    .act-msg {
      font-size: 12.5px;
      font-weight: 700;
      color: var(--t1);
      line-height: 1.4;
    }

    .act-time {
      font-size: 11px;
      color: var(--t4);
      margin-top: 3px;
    }

    .act-new {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: var(--blue);
      flex-shrink: 0;
      margin-top: 5px;
      box-shadow: 0 0 0 3px rgba(13, 110, 253, .15);
    }

    /* MODAL */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .45);
      backdrop-filter: blur(8px);
      z-index: 9000;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      opacity: 0;
      visibility: hidden;
      transition: opacity .28s, visibility .28s;
    }

    .modal-overlay.open {
      opacity: 1;
      visibility: visible;
    }

    .modal-box {
      background: var(--card);
      border: 1px solid var(--bd);
      border-radius: var(--r4);
      width: 100%;
      max-width: 660px;
      overflow: hidden;
      transform: translateY(20px) scale(.97);
      transition: transform .3s cubic-bezier(.34, 1.2, .64, 1);
      box-shadow: 0 32px 80px rgba(0, 0, 0, .18);
    }

    .modal-overlay.open .modal-box {
      transform: translateY(0) scale(1);
    }

    .modal-top {
      height: 4px;
      background: var(--blue-g);
    }

    .modal-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 20px 24px;
      border-bottom: 1px solid var(--bd);
    }

    .modal-title {
      font-size: 19px;
      font-weight: 800;
      color: var(--t1);
    }

    .modal-close {
      width: 32px;
      height: 32px;
      border-radius: var(--r1);
      background: var(--bg);
      border: 1px solid var(--bd);
      color: var(--t3);
      font-size: 13px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: var(--tr);
    }

    .modal-close:hover {
      background: rgba(220, 53, 69, .08);
      color: var(--red);
      border-color: rgba(220, 53, 69, .2);
    }

    .modal-body {
      padding: 22px 24px;
    }

    .modal-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 13px;
    }

    .rep-card {
      background: var(--card2);
      border: 1px solid var(--bd);
      border-radius: var(--r2);
      padding: 18px;
      cursor: pointer;
      transition: var(--tr);
    }

    .rep-card:hover {
      border-color: var(--bd-blue);
      transform: translateY(-2px);
      box-shadow: var(--sh-md);
    }

    .rep-card-top {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 9px;
    }

    .rep-ic {
      width: 34px;
      height: 34px;
      border-radius: 9px;
      background: rgba(13, 110, 253, .1);
      color: var(--blue);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
    }

    .rep-title {
      font-size: 13.5px;
      font-weight: 800;
      color: var(--t1);
    }

    .rep-desc {
      font-size: 12px;
      color: var(--t4);
      line-height: 1.7;
      margin-bottom: 13px;
    }

    .rep-action {
      font-size: 11px;
      font-weight: 700;
      color: var(--blue);
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .modal-foot {
      display: flex;
      justify-content: flex-end;
      padding: 14px 24px;
      border-top: 1px solid var(--bd);
    }

    /* ANIMATIONS */
    @keyframes fadeUp {
      from {
        opacity: 0;
        transform: translateY(16px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .anim {
      opacity: 0;
      animation: fadeUp .5s ease forwards;
    }

    .a1 {
      animation-delay: .04s
    }

    .a2 {
      animation-delay: .08s
    }

    .a3 {
      animation-delay: .12s
    }

    .a4 {
      animation-delay: .16s
    }

    .a5 {
      animation-delay: .2s
    }

    .a6 {
      animation-delay: .26s
    }

    /* SCROLLBAR */
    ::-webkit-scrollbar {
      width: 5px;
    }

    ::-webkit-scrollbar-track {
      background: var(--bg);
    }

    ::-webkit-scrollbar-thumb {
      background: var(--bd2);
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: var(--blue);
    }

    /* RESPONSIVE */
    @media(max-width:1400px) {
      .hub-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    @media(max-width:1200px) {
      .stat-row {
        grid-template-columns: repeat(2, 1fr);
      }

      .hub-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .mid-row {
        grid-template-columns: 1fr 1fr;
      }

      .charts-row {
        grid-template-columns: 1fr;
      }

      .qa-row {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    @media(max-width:900px) {
      .shell {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
      }

      .mid-row {
        grid-template-columns: 1fr;
      }

      .hub-grid {
        grid-template-columns: 1fr 1fr;
      }

      .qa-row {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    @media(max-width:640px) {
      .stat-row {
        grid-template-columns: 1fr;
      }

      .hub-grid {
        grid-template-columns: 1fr;
      }

      .qa-row {
        grid-template-columns: repeat(2, 1fr);
      }

      .modal-grid {
        grid-template-columns: 1fr;
      }

      .content,
      .topbar {
        padding-left: 14px;
        padding-right: 14px;
      }
    }
  </style>
</head>

<body>
  <div class="shell">

    <aside class="sidebar">
      <div class="sb-logo">
        <div class="sb-logo-mark"><i class="fas fa-building"></i></div>
        <div>
          <div class="sb-brand">Real<span>Estate</span></div>
          <div class="sb-tagline">Admin Portal</div>
        </div>
      </div>
      <nav class="sb-nav">
        <div class="sb-sec">{{ __('dashboard.home') }}</div>
        <a href="{{ route('admin.dashboard') }}" class="sb-link active"><i class="fas fa-th-large"></i>{{ __('dashboard.dashboard') }}</a>
        <a href="{{ route('admin.performance.dashboard') }}" class="sb-link"><i
            class="fas fa-chart-line"></i>{{ __('dashboard.analytics') }}</a>
        <a href="{{ route('admin.system.logs') }}" class="sb-link"><i class="fas fa-bell"></i>{{ __('dashboard.notifications') }}<span
            class="sb-badge">12</span></a>
        <div class="sb-sec">{{ __('dashboard.management') }}</div>
        <a href="{{ route('admin.users.index') }}" class="sb-link"><i class="fas fa-users"></i>{{ __('dashboard.users') }}</a>
        <a href="{{ route('admin.properties.index') }}" class="sb-link"><i class="fas fa-city"></i>{{ __('dashboard.properties') }}</a>
        <a href="{{ route('admin.companies.index') }}" class="sb-link"><i class="fas fa-briefcase"></i>{{ __('dashboard.companies') }}</a>
        <div class="sb-sec">{{ __('dashboard.ai') }}</div>
    <a href="{{ route('ai.dashboard') }}" class="sb-link"><i class="fas fa-robot"></i>{{ __('dashboard.smart_dashboard') }}</a>
    <a href="{{ route('ai.smart-search') }}" class="sb-link"><i class="fas fa-search-plus"></i>{{ __('dashboard.smart_search') }}</a>
    <a href="{{ route('ai.chat') }}" class="sb-link"><i class="fas fa-brain"></i>{{ __('dashboard.property_advisor') }}</a>
    <a href="{{ route('ai.analytics') }}" class="sb-link"><i class="fas fa-chart-pie"></i>{{ __('dashboard.performance_analytics') }}</a>
    <div class="sb-sec">{{ __('dashboard.financial') }}</div>
    <a href="{{ route('admin.financial.wallets') }}" class="sb-link"><i class="fas fa-wallet"></i>{{ __('dashboard.wallets') }}</a>
    <a href="{{ route('admin.financial.invoices') }}" class="sb-link"><i class="fas fa-file-invoice"></i>{{ __('dashboard.invoices') }}</a>
    <a href="{{ route('subscriptions.index') }}" class="sb-link"><i class="fas fa-tags"></i>{{ __('dashboard.subscriptions') }}</a>
    <div class="sb-sec">{{ __('dashboard.system') }}</div>
    <a href="{{ route('admin.settings.index') }}" class="sb-link"><i class="fas fa-cog"></i>{{ __('dashboard.settings') }}</a>
    <a href="{{ route('admin.settings.maintenance') }}" class="sb-link"><i class="fas fa-tools"></i>{{ __('dashboard.maintenance') }}</a>
    <a href="{{ route('admin.system.logs') }}" class="sb-link"><i class="fas fa-history"></i>{{ __('dashboard.logs') }}</a>
  </nav>
  <div class="sb-foot">
    <div class="sb-user">
      <div class="sb-av">م</div>
      <div><div class="sb-uname">محمد الأدمن</div><div class="sb-urole">Super Administrator</div></div>
    </div>
  </div>
</aside>

<main class="main">
  <div class="topbar">
    <div>
      <div class="topbar-eyebrow">نظرة عامة على النظام</div>
      <h1 class="topbar-title">مركز التحكم <em>الرئيسي</em></h1>
    </div>
    <div class="topbar-right">
      <div class="live-pill"><span class="live-dot"></span>مباشر الآن</div>
      <button class="tbtn tbtn-ghost" onclick="location.reload()"><i class="fas fa-sync-alt"></i> تحديث</button>
      <button class="tbtn tbtn-primary" onclick="openModal()"><i class="fas fa-file-alt"></i> التقارير</button>
    </div>
  </div>

  <div class="content">

    <!-- STAT CARDS -->
    <div class="stat-row">
      <div class="sc sc-blue anim a1">
        <div class="sc-head"><div class="sc-label">إجمالي المستخدمين</div><div class="sc-ic"><i class="fas fa-users"></i></div></div>
        <div class="sc-val">{{ number_format(\App\Models\User::count()) }}</div>
        <div class="sc-footer"><span class="sc-delta"><i class="fas fa-arrow-up"></i> +{{ \App\Models\User::whereDate('created_at', today())->count() }} اليوم</span><span class="sc-ftext">مسجل نشط</span></div>
      </div>
      <div class="sc sc-purple anim a2">
        <div class="sc-head"><div class="sc-label">العقارات المنشورة</div><div class="sc-ic"><i class="fas fa-city"></i></div></div>
        <div class="sc-val">{{ number_format(\App\Models\Property::count()) }}</div>
        <div class="sc-footer"><span class="sc-delta"><i class="fas fa-arrow-up"></i> +{{ \App\Models\Property::whereDate('created_at', today())->count() }} اليوم</span><span class="sc-ftext">عقار متاح</span></div>
      </div>
      <div class="sc sc-green anim a3">
        <div class="sc-head"><div class="sc-label">المستثمرون</div><div class="sc-ic"><i class="fas fa-chart-line"></i></div></div>
        <div class="sc-val">{{ number_format(\App\Models\Investor::count()) }}</div>
        <div class="sc-footer"><span class="sc-delta"><i class="fas fa-arrow-up"></i> +{{ \App\Models\Investor::whereDate('created_at', today())->count() }} اليوم</span><span class="sc-ftext">مستثمر فعّال</span></div>
      </div>
      <div class="sc sc-orange anim a4">
        <div class="sc-head"><div class="sc-label">الإيرادات الإجمالية</div><div class="sc-ic"><i class="fas fa-dollar-sign"></i></div></div>
        <div class="sc-val">${{ number_format(\App\Models\SubscriptionInvoice::where('status', 'paid')->sum('amount') / 1000000, 1) }}M</div>
        <div class="sc-footer"><span class="sc-delta"><i class="fas fa-arrow-up"></i> +${{ number_format(\App\Models\SubscriptionInvoice::where('status', 'paid')->whereDate('created_at', today())->sum('amount') / 1000, 1) }}k</span><span class="sc-ftext">اليوم</span></div>
      </div>
      <div class="sc sc-blue anim a5">
        <div class="sc-head"><div class="sc-label">استعلامات AI</div><div class="sc-ic"><i class="fas fa-brain"></i></div></div>
        <div class="sc-val">{{ number_format(\App\Models\AiUsageLog::whereMonth('created_at', now()->month)->count()) }}</div>
        <div class="sc-footer"><span class="sc-delta"><i class="fas fa-bolt"></i> +{{ \App\Models\AiUsageLog::whereDate('created_at', today())->count() }} اليوم</span><span class="sc-ftext">عملية معالجة</span></div>
      </div>
    </div>

    <!-- CHARTS -->
    <div class="charts-row anim a3">
      <div class="panel">
        <div class="panel-head"><div><div class="panel-title">نمو المستخدمين</div><div class="panel-sub">آخر 6 أشهر</div></div><span class="p-badge pb-blue">User Growth</span></div>
        <div class="panel-body"><div class="chart-wrap"><canvas id="userChart"></canvas></div></div>
      </div>
      <div class="panel">
        <div class="panel-head"><div><div class="panel-title">اتجاه الإيرادات</div><div class="panel-sub">آخر 6 أشهر</div></div><span class="p-badge pb-orange">Revenue</span></div>
        <div class="panel-body"><div class="chart-wrap"><canvas id="revChart"></canvas></div></div>
      </div>
    </div>

    <!-- MIDDLE ROW -->
    <div class="mid-row anim a4">
      <div class="panel">
        <div class="panel-head"><div><div class="panel-title">أحدث المستخدمين</div><div class="panel-sub">مسجلون مؤخراً</div></div><a href="{{ route('admin.users.index') }}" class="p-badge pb-blue" style="text-decoration:none">عرض الكل</a></div>
        <div class="panel-body" style="padding-top:10px">
          @php
            $recentUsers = \App\Models\User::latest()->take(5)->get();
          @endphp
          @forelse($recentUsers as $user)
            <div class="ri-item">
              <div class="ri-av ra-blue"><i class="fas fa-user"></i></div>
              <div>
                <div class="ri-name">{{ $user->name ?? 'مستخدم غير معروف' }}</div>
                <div class="ri-sub">{{ $user->email ?? 'لا يوجد بريد' }}</div>
              </div>
              <div class="ri-time">{{ $user->created_at->diffForHumans() }}</div>
            </div>
          @empty
            <div class="ri-item">
              <div class="ri-av ra-gray"><i class="fas fa-info-circle"></i></div>
              <div>
                <div class="ri-name">لا يوجد مستخدمين</div>
                <div class="ri-sub">لم يتم تسجيل أي مستخدمين بعد</div>
              </div>
              <div class="ri-time">--</div>
            </div>
          @endforelse
        </div>
      </div>
      <div class="panel">
        <div class="panel-head"><div><div class="panel-title">أحدث العقارات</div><div class="panel-sub">أُضيفت مؤخراً</div></div><a href="{{ route('admin.properties.index') }}" class="p-badge pb-green" style="text-decoration:none">عرض الكل</a></div>
        <div class="panel-body" style="padding-top:10px">
          @php
            $recentProperties = \App\Models\Property::latest()->take(5)->get();
          @endphp
          @forelse($recentProperties as $property)
            <div class="ri-item">
              <div class="ri-av ra-blue"><i class="fas fa-home"></i></div>
              <div>
                <div class="ri-name">{{ $property->title ?? 'عقار غير معروف' }} — {{ $property->city ?? 'غير محدد' }}</div>
                <div class="ri-sub">${{ number_format($property->price ?? 0) }}</div>
              </div>
              <div class="ri-time">{{ $property->created_at->diffForHumans() }}</div>
            </div>
          @empty
            <div class="ri-item">
              <div class="ri-av ra-gray"><i class="fas fa-info-circle"></i></div>
              <div>
                <div class="ri-name">لا توجد عقارات</div>
                <div class="ri-sub">لم يتم إضافة أي عقارات بعد</div>
              </div>
              <div class="ri-time">--</div>
            </div>
          @endforelse
        </div>
      </div>
      <div class="panel">
        <div class="panel-head"><div><div class="panel-title">حالة النظام</div><div class="panel-sub">System Health</div></div><span class="p-badge pb-green">Normal</span></div>
        <div class="panel-body" style="padding-top:11px">
          <div class="sys-row"><div class="sys-left"><span class="sys-dot sd-ok"></span><span class="sys-name">قاعدة البيانات</span></div><span class="sys-tag st-ok"><i class="fas fa-check-circle"></i> سليم</span></div>
          <div class="sys-row"><div class="sys-left"><span class="sys-dot sd-ok"></span><span class="sys-name">مساحة التخزين</span></div><span class="sys-tag st-ok">65% مستخدم</span></div>
          <div class="sys-row"><div class="sys-left"><span class="sys-dot sd-ok"></span><span class="sys-name">واجهة API</span></div><span class="sys-tag st-ok"><i class="fas fa-wifi"></i> متصل</span></div>
          <div class="sys-row"><div class="sys-left"><span class="sys-dot sd-warn"></span><span class="sys-name">قائمة الانتظار</span></div><span class="sys-tag st-warn"><i class="fas fa-clock"></i> 12 مهمة</span></div>
          <div class="sys-row"><div class="sys-left"><span class="sys-dot sd-ok"></span><span class="sys-name">الخادم</span></div><span class="sys-tag st-ok">99.9% وقت التشغيل</span></div>
        </div>
      </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="panel anim a5" style="margin-bottom:18px">
      <div class="panel-head"><div><div class="panel-title">الإجراءات السريعة</div><div class="panel-sub">Quick Actions</div></div></div>
      <div class="panel-body">
        <div class="qa-row">
          <a href="{{ route('admin.users.create') }}" class="qa-card"><div class="qa-ic qic-blue"><i class="fas fa-user-plus"></i></div><span class="qa-label">مستخدم جديد</span></a>
          <a href="{{ route('admin.properties.create') }}" class="qa-card"><div class="qa-ic qic-purple"><i class="fas fa-plus-circle"></i></div><span class="qa-label">عقار جديد</span></a>
          <a href="{{ route('admin.blog.posts.create') }}" class="qa-card"><div class="qa-ic qic-green"><i class="fas fa-pen-nib"></i></div><span class="qa-label">مقال جديد</span></a>
          <a href="{{ route('investor.stats.public') }}" class="qa-card"><div class="qa-ic qic-orange"><i class="fas fa-chart-line"></i></div><span class="qa-label">إحصاء المستثمرين</span></a>
          <a href="{{ route('admin.settings.index') }}" class="qa-card"><div class="qa-ic qic-teal"><i class="fas fa-cog"></i></div><span class="qa-label">الإعدادات</span></a>
          <a href="{{ route('admin.system.dashboard') }}" class="qa-card"><div class="qa-ic qic-red"><i class="fas fa-tools"></i></div><span class="qa-label">الصيانة</span></a>
        </div>
      </div>
    </div>

    <div class="sec-div"></div>
    <div class="sec-head anim a5"><div class="sec-accent"></div><div class="sec-title">مركز الإدارة</div><div class="sec-pill">Management Hub</div></div>

    <!-- HUB GRID -->
    <div class="hub-grid">
      <div class="hc hc-blue"><div class="hc-head"><div class="hc-icon hi-blue"><i class="fas fa-cogs"></i></div><div><div class="hc-title">العمليات الأساسية</div><div class="hc-sub">Core Operations</div></div></div><div class="hc-links"><a href="{{ route('admin.users.index') }}" class="hc-link"><i class="fas fa-users"></i><span>كل المستخدمين</span><i class="fas fa-chevron-left hc-arr"></i></a>
          <a href="{{ route('admin.users.create') }}" class="hc-link"><i class="fas fa-user-plus"></i><span>إضافة مستخدم</span><i class="fas fa-chevron-left hc-arr"></i></a>
          <a href="{{ route('admin.agents.index') }}" class="hc-link"><i class="fas fa-user-tie"></i><span>الوكلاء</span><i class="fas fa-chevron-left hc-arr"></i></a>
          <a href="{{ route('investor.index') }}" class="hc-link"><i class="fas fa-hand-holding-usd"></i><span>المستثمرون</span><i class="fas fa-chevron-left hc-arr"></i></a>
          <a href="{{ route('admin.system.logs') }}" class="hc-link"><i class="fas fa-history"></i><span>سجل النشاط</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-purple"><div class="hc-head"><div class="hc-icon hi-purple"><i class="fas fa-city"></i></div><div><div class="hc-title">إدارة العقارات</div><div class="hc-sub">Properties</div></div></div><div class="hc-links"><a href="{{ route('admin.properties.index') }}" class="hc-link"><i class="fas fa-building"></i><span>كل العقارات</span><i class="fas fa-chevron-left hc-arr"></i></a>
          <a href="{{ route('admin.properties.create') }}" class="hc-link"><i class="fas fa-plus-circle"></i><span>إضافة عقار</span><i class="fas fa-chevron-left hc-arr"></i></a>
          <a href="{{ route('admin.projects.index') }}" class="hc-link"><i class="fas fa-project-diagram"></i><span>المشاريع</span><i class="fas fa-chevron-left hc-arr"></i></a>
          <a href="{{ route('admin.companies.index') }}" class="hc-link"><i class="fas fa-briefcase"></i><span>الشركات</span><i class="fas fa-chevron-left hc-arr"></i></a>
          <a href="{{ route('properties.search.index') }}" class="hc-link"><i class="fas fa-search-location"></i><span>بحث متقدم</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-green"><div class="hc-head"><div class="hc-icon hi-green"><i class="fas fa-chart-line"></i></div><div><div class="hc-title">المركز المالي</div><div class="hc-sub">Financial Hub</div></div></div><div class="hc-links"><a href="{{ route('admin.financial.expenses') }}" class="hc-link"><i class="fas fa-money-bill-wave"></i><span>المصروفات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.financial.invoices') }}" class="hc-link"><i class="fas fa-file-invoice"></i><span>الفواتير</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.financial.wallets') }}" class="hc-link"><i class="fas fa-wallet"></i><span>المحافظ</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.financial.wallets') }}" class="hc-link"><i class="fas fa-coins"></i><span>محافظ النظام</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.financial.dashboard') }}" class="hc-link"><i class="fas fa-hand-holding-usd"></i><span>الضمان</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-orange"><div class="hc-head"><div class="hc-icon hi-orange"><i class="fas fa-dollar-sign"></i></div><div><div class="hc-title">مصادر الإيرادات</div><div class="hc-sub">Revenue Streams</div></div></div><div class="hc-links"><a href="{{ route('subscriptions.plans.index') }}" class="hc-link"><i class="fas fa-tags"></i><span>خطط الاشتراك</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.financial.reports') }}" class="hc-link"><i class="fas fa-chart-area"></i><span>إحصاء الخطط</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.settings.payment') }}" class="hc-link"><i class="fas fa-calculator"></i><span>إدارة الضرائب</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.reports.custom') }}" class="hc-link"><i class="fas fa-chart-pie"></i><span>حاسبة ROI</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('subscriptions.plans.compare') }}" class="hc-link"><i class="fas fa-balance-scale"></i><span>مقارنة الخطط</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-blue"><div class="hc-head"><div class="hc-icon hi-teal"><i class="fas fa-funnel-dollar"></i></div><div><div class="hc-title">إدارة CRM</div><div class="hc-sub">CRM & Sales</div></div></div><div class="hc-links"><a href="{{ route('admin.reports.client.analytics') }}" class="hc-link"><i class="fas fa-tachometer-alt"></i><span>لوحة العملاء</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('leads.index') }}" class="hc-link"><i class="fas fa-filter"></i><span>خط الأنابيب</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.users.index') }}" class="hc-link"><i class="fas fa-list"></i><span>كل العملاء</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('lead-scoring.index') }}" class="hc-link"><i class="fas fa-star-half-alt"></i><span>تسجيل النقاط</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.reports.client.analytics') }}" class="hc-link"><i class="fas fa-chart-pie"></i><span>التحليلات</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-purple"><div class="hc-head"><div class="hc-icon hi-red"><i class="fas fa-bullhorn"></i></div><div><div class="hc-title">التسويق</div><div class="hc-sub">Marketing Suite</div></div></div><div class="hc-links"><a href="{{ route('admin.banner-ads.index') }}" class="hc-link"><i class="fas fa-ad"></i><span>الحملات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.seo.index') }}" class="hc-link"><i class="fas fa-search"></i><span>إدارة SEO</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.seo.keywords') }}" class="hc-link"><i class="fas fa-key"></i><span>الكلمات المفتاحية</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.seo.analyze') }}" class="hc-link"><i class="fas fa-microscope"></i><span>تحليل SEO</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.seo.sitemap') }}" class="hc-link"><i class="fas fa-sitemap"></i><span>خريطة الموقع</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-green"><div class="hc-head"><div class="hc-icon hi-blue"><i class="fas fa-pen-nib"></i></div><div><div class="hc-title">مركز المحتوى</div><div class="hc-sub">Content Hub</div></div></div><div class="hc-links"><a href="{{ route('admin.content.dashboard') }}" class="hc-link"><i class="fas fa-tachometer-alt"></i><span>لوحة CMS</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.blog.posts.index') }}" class="hc-link"><i class="fas fa-newspaper"></i><span>المدونة</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.pages.index') }}" class="hc-link"><i class="fas fa-file-code"></i><span>الصفحات الثابتة</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('cms.media.library') }}" class="hc-link"><i class="fas fa-images"></i><span>مكتبة الوسائط</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.faqs.index') }}" class="hc-link"><i class="fas fa-question-circle"></i><span>الأسئلة الشائعة</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-orange"><div class="hc-head"><div class="hc-icon hi-purple"><i class="fas fa-chart-bar"></i></div><div><div class="hc-title">مركز التحليلات</div><div class="hc-sub">Analytics Center</div></div></div><div class="hc-links"><a href="{{ route('analytics.overview') }}" class="hc-link"><i class="fas fa-tachometer-alt"></i><span>نظرة عامة</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('analytics.real-time') }}" class="hc-link"><i class="fas fa-clock"></i><span>الوقت الحقيقي</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('analytics.heatmap.index') }}" class="hc-link"><i class="fas fa-fire"></i><span>خرائط الحرارة</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('reports.sales.index') }}" class="hc-link"><i class="fas fa-piggy-bank"></i><span>تقارير المبيعات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.reports.custom') }}" class="hc-link"><i class="fas fa-filter"></i><span>تقارير مخصصة</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-blue"><div class="hc-head"><div class="hc-icon hi-gray"><i class="fas fa-tools"></i></div><div><div class="hc-title">أدوات النظام</div><div class="hc-sub">System Tools</div></div></div><div class="hc-links"><a href="{{ route('admin.system.dashboard') }}" class="hc-link"><i class="fas fa-server"></i><span>لوحة النظام</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.backups') }}" class="hc-link"><i class="fas fa-save"></i><span>النسخ الاحتياطي</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.system.queue') }}" class="hc-link"><i class="fas fa-tasks"></i><span>إدارة الطوابير</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.system.storage') }}" class="hc-link"><i class="fas fa-hdd"></i><span>التخزين</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('routes.index') }}" class="hc-link"><i class="fas fa-route"></i><span>إدارة المسارات</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-purple"><div class="hc-head"><div class="hc-icon hi-gray"><i class="fas fa-sliders-h"></i></div><div><div class="hc-title">إعدادات المنصة</div><div class="hc-sub">Platform Settings</div></div></div><div class="hc-links"><a href="{{ route('admin.settings.general') }}" class="hc-link"><i class="fas fa-sliders-h"></i><span>الإعدادات العامة</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.settings.email') }}" class="hc-link"><i class="fas fa-envelope"></i><span>إعدادات البريد</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.settings.payment') }}" class="hc-link"><i class="fas fa-credit-card"></i><span>المدفوعات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.settings.security') }}" class="hc-link"><i class="fas fa-lock"></i><span>الأمان</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('settings.index') }}" class="hc-link"><i class="fas fa-user-cog"></i><span>تفضيلاتي</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-green"><div class="hc-head"><div class="hc-icon hi-teal"><i class="fas fa-comments"></i></div><div><div class="hc-title">التواصل</div><div class="hc-sub">Communication Hub</div></div></div><div class="hc-links"><a href="{{ route('messages.inbox') }}" class="hc-link"><i class="fas fa-inbox"></i><span>البريد الوارد</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('messages.chat') }}" class="hc-link"><i class="fas fa-comment-dots"></i><span>الدردشة المباشرة</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('messages.appointments') }}" class="hc-link"><i class="fas fa-calendar-check"></i><span>المواعيد</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('messages.notifications') }}" class="hc-link"><i class="fas fa-bell"></i><span>الإشعارات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('messages.chat') }}" class="hc-link"><i class="fas fa-video"></i><span>مكالمات الفيديو</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-orange"><div class="hc-head"><div class="hc-icon hi-purple"><i class="fas fa-rocket"></i></div><div><div class="hc-title">الميزات المتقدمة</div><div class="hc-sub">Advanced Features</div></div></div><div class="hc-links"><a href="{{ route('metaverse.index') }}" class="hc-link"><i class="fas fa-vr-cardboard"></i><span>الميتافيرس</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('defi.dashboard.index') }}" class="hc-link"><i class="fas fa-chart-line"></i><span>منصة DeFi</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('bigdata.predictive-ai') }}" class="hc-link"><i class="fas fa-database"></i><span>البيانات الضخمة</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('ai.dashboard') }}" class="hc-link"><i class="fas fa-robot"></i><span>لوحة AI</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('geospatial.analytics.index') }}" class="hc-link"><i class="fas fa-map-marked-alt"></i><span>التحليلات الجغرافية</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.settings.general') }}" class="hc-link"><i class="fas fa-bell"></i><span>إشعارات API</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-blue"><div class="hc-head"><div class="hc-icon hi-green"><i class="fas fa-brain"></i></div><div><div class="hc-title">مركز الذكاء الاصطناعي</div><div class="hc-sub">AI Core & IQ</div></div></div><div class="hc-links">
          <a href="{{ route('ai.dashboard') }}" class="hc-link"><i class="fas fa-robot"></i><span>لوحة التحكم الذكية</span><i class="fas fa-chevron-left hc-arr"></i></a>
          <a href="{{ route('ai.smart-search') }}" class="hc-link"><i class="fas fa-search"></i><span>البحث باللغة الطبيعية</span><i class="fas fa-chevron-left hc-arr"></i></a>
          <a href="{{ route('ai.chat') }}" class="hc-link"><i class="fas fa-comments"></i><span>المستشار العقاري AI</span><i class="fas fa-chevron-left hc-arr"></i></a>
          <a href="{{ route('ai.price-prediction') }}" class="hc-link"><i class="fas fa-chart-line"></i><span>توقع أسعار السوق</span><i class="fas fa-chevron-left hc-arr"></i></a>
          <a href="{{ route('ai.images') }}" class="hc-link"><i class="fas fa-magic"></i><span>تحليل صور العقارات</span><i class="fas fa-chevron-left hc-arr"></i></a>
          <a href="{{ route('ai.analytics') }}" class="hc-link"><i class="fas fa-microscope"></i><span>تحليل صحة النماذج</span><i class="fas fa-chevron-left hc-arr"></i></a>
      </div></div>
      <div class="hc hc-purple"><div class="hc-head"><div class="hc-icon hi-orange"><i class="fas fa-cube"></i></div><div><div class="hc-title">Blockchain Pro</div><div class="hc-sub">Blockchain Hub</div></div></div><div class="hc-links"><a href="/blockchain/wallets" class="hc-link"><i class="fas fa-wallet"></i><span>محافظ Crypto</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="/blockchain/transactions" class="hc-link"><i class="fas fa-exchange-alt"></i><span>المعاملات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="/blockchain/smartcontracts" class="hc-link"><i class="fas fa-file-contract"></i><span>العقود الذكية</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="/blockchain/nfts" class="hc-link"><i class="fas fa-image"></i><span>NFTs</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="/blockchain/dao" class="hc-link"><i class="fas fa-users"></i><span>DAO</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-blue"><div class="hc-head"><div class="hc-icon hi-blue"><i class="fas fa-hard-hat"></i></div><div><div class="hc-title">المطورين والمشاريع</div><div class="hc-sub">Developer Solutions</div></div></div><div class="hc-links"><a href="{{ route('developer.index') }}" class="hc-link"><i class="fas fa-tachometer-alt"></i><span>لوحة المطور</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('developer.projects.index') }}" class="hc-link"><i class="fas fa-building"></i><span>إدارة المشاريع</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('developer.bim.models') }}" class="hc-link"><i class="fas fa-cube"></i><span>نماذج BIM</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="/permits" class="hc-link"><i class="fas fa-file-contract"></i><span>التصاريح</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('developer.contractors.index') }}" class="hc-link"><i class="fas fa-tools"></i><span>المقاولون</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-green"><div class="hc-head"><div class="hc-icon hi-green"><i class="fas fa-wifi"></i></div><div><div class="hc-title">إنترنت الأشياء</div><div class="hc-sub">IoT & Smart City</div></div></div><div class="hc-links"><a href="{{ route('iot.dashboard') }}" class="hc-link"><i class="fas fa-chart-network"></i><span>لوحة IoT</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('iot.dashboard') }}" class="hc-link"><i class="fas fa-microchip"></i><span>الأجهزة</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('iot.dashboard') }}" class="hc-link"><i class="fas fa-exclamation-triangle"></i><span>التنبيهات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('iot.dashboard') }}" class="hc-link"><i class="fas fa-chart-bar"></i><span>تحليلات الطاقة</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('iot.dashboard') }}" class="hc-link"><i class="fas fa-sliders-h"></i><span>الإعدادات</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-orange"><div class="hc-head"><div class="hc-icon hi-orange"><i class="fas fa-trophy"></i></div><div><div class="hc-title">الولاء والمكافآت</div><div class="hc-sub">Gamification</div></div></div><div class="hc-links"><a href="{{ route('gamification.dashboard') }}" class="hc-link"><i class="fas fa-tachometer-alt"></i><span>لوحة اللعب</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('gamification.achievements') }}" class="hc-link"><i class="fas fa-medal"></i><span>الإنجازات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('gamification.badges') }}" class="hc-link"><i class="fas fa-certificate"></i><span>الشارات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('gamification.rewards') }}" class="hc-link"><i class="fas fa-gift"></i><span>المكافآت</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('gamification.leaderboard') }}" class="hc-link"><i class="fas fa-list-ol"></i><span>لوحة الصدارة</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-purple"><div class="hc-head"><div class="hc-icon hi-purple"><i class="fas fa-user-tie"></i></div><div><div class="hc-title">بوابة الوكلاء</div><div class="hc-sub">Agent Portal</div></div></div><div class="hc-links"><a href="{{ route('agents.dashboard') }}" class="hc-link"><i class="fas fa-tachometer-alt"></i><span>لوحة الوكيل</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('agent.crm.index') }}" class="hc-link"><i class="fas fa-users"></i><span>العملاء (CRM)</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('agent.appointments.index') }}" class="hc-link"><i class="fas fa-calendar-alt"></i><span>المواعيد</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('agent.offers.index') }}" class="hc-link"><i class="fas fa-file-signature"></i><span>العروض</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('agent.profile.show') }}" class="hc-link"><i class="fas fa-id-card"></i><span>الملف الشخصي</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-blue"><div class="hc-head"><div class="hc-icon hi-teal"><i class="fas fa-brain"></i></div><div><div class="hc-title">أدوات ذكية</div><div class="hc-sub">Smart AI Tools</div></div></div><div class="hc-links"><a href="{{ route('ai.fraud-detection') }}" class="hc-link"><i class="fas fa-shield-alt"></i><span>كشف الاحتيال</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('ai.virtual-reality') }}" class="hc-link"><i class="fas fa-couch"></i><span>التأثيث الافتراضي</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('lead-scoring.index') }}" class="hc-link"><i class="fas fa-star-half-alt"></i><span>تصنيف العملاء</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('ai.analytics') }}" class="hc-link"><i class="fas fa-chart-line"></i><span>تحليل السوق</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('ai.images') }}" class="hc-link"><i class="fas fa-image"></i><span>تحليل الصور</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-green"><div class="hc-head"><div class="hc-icon hi-green"><i class="fas fa-wallet"></i></div><div><div class="hc-title">الإدارة المالية</div><div class="hc-sub">Financial Management</div></div></div><div class="hc-links"><a href="{{ route('admin.financial.dashboard') }}" class="hc-link"><i class="fas fa-chart-pie"></i><span>لوحة المالية</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.financial.invoices') }}" class="hc-link"><i class="fas fa-file-invoice-dollar"></i><span>الفواتير</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.financial.transactions') }}" class="hc-link"><i class="fas fa-exchange-alt"></i><span>المعاملات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.financial.wallets') }}" class="hc-link"><i class="fas fa-wallet"></i><span>المحافظ</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.financial.expenses') }}" class="hc-link"><i class="fas fa-receipt"></i><span>المصروفات</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-orange"><div class="hc-head"><div class="hc-icon hi-orange"><i class="fas fa-chart-line"></i></div><div><div class="hc-title">التقارير والتحليلات</div><div class="hc-sub">Reports & Analytics</div></div></div><div class="hc-links"><a href="{{ route('admin.reports.financial.index') }}" class="hc-link"><i class="fas fa-file-invoice"></i><span>التقارير المالية</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.reports.property.performance') }}" class="hc-link"><i class="fas fa-building"></i><span>أداء العقارات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.reports.agent.performance') }}" class="hc-link"><i class="fas fa-user-tie"></i><span>أداء الوكلاء</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.reports.client.analytics') }}" class="hc-link"><i class="fas fa-users"></i><span>تحليلات العملاء</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-blue"><div class="hc-head"><div class="hc-icon hi-newspaper"></i></div><div><div class="hc-title">إدارة المحتوى</div><div class="hc-sub">Content Management</div></div></div><div class="hc-links"><a href="{{ route('admin.content.dashboard') }}" class="hc-link"><i class="fas fa-columns"></i><span>لوحة المحتوى</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.blog.posts.index') }}" class="hc-link"><i class="fas fa-rss"></i><span>المقالات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.news.index') }}" class="hc-link"><i class="fas fa-bullhorn"></i><span>الأخبار</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('cms.media.library') }}" class="hc-link"><i class="fas fa-images"></i><span>مكتبة الوسائط</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.guides.index') }}" class="hc-link"><i class="fas fa-book"></i><span>الأدلة والشروحات</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-purple"><div class="hc-head"><div class="hc-icon hi-purple"><i class="fas fa-cogs"></i></div><div><div class="hc-title">إعدادات النظام</div><div class="hc-sub">System Settings</div></div></div><div class="hc-links"><a href="{{ route('admin.settings.index') }}" class="hc-link"><i class="fas fa-cog"></i><span>الإعدادات العامة</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.system.logs') }}" class="hc-link"><i class="fas fa-history"></i><span>سجلات النظام</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.performance.dashboard') }}" class="hc-link"><i class="fas fa-tachometer-fast"></i><span>الأداء</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.settings.security') }}" class="hc-link"><i class="fas fa-shield-alt"></i><span>الأمان</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-green"><div class="hc-head"><div class="hc-icon hi-green"><i class="fas fa-boxes"></i></div><div><div class="hc-title">المخزون والتوريد</div><div class="hc-sub">Inventory System</div></div></div><div class="hc-links"><a href="{{ route('inventory.index') }}" class="hc-link"><i class="fas fa-box-open"></i><span>لوحة المخزون</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('inventory.items.index') }}" class="hc-link"><i class="fas fa-barcode"></i><span>إدارة الأصناف</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('inventory.movements.index') }}" class="hc-link"><i class="fas fa-dolly"></i><span>حركة المخزون</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('inventory.reports') }}" class="hc-link"><i class="fas fa-clipboard-list"></i><span>تقارير الجرد</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('inventory.index') }}" class="hc-link"><i class="fas fa-truck-loading"></i><span>الموردين</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-red"><div class="hc-head"><div class="hc-icon hi-red"><i class="fas fa-tools"></i></div><div><div class="hc-title">الصيانة والتشغيل</div><div class="hc-sub">Maintenance Ops</div></div></div><div class="hc-links"><a href="{{ route('maintenance.workorders.index') }}" class="hc-link"><i class="fas fa-clipboard-check"></i><span>أوامر العمل</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('maintenance.teams.index') }}" class="hc-link"><i class="fas fa-users-cog"></i><span>فرق الصيانة</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.system.dashboard') }}" class="hc-link"><i class="fas fa-cogs"></i><span>حالة النظام</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('maintenance.workorders.index') }}" class="hc-link"><i class="fas fa-calendar-alt"></i><span>الجدولة</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('maintenance.workorders.index') }}" class="hc-link"><i class="fas fa-file-contract"></i><span>العقود</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-purple"><div class="hc-head"><div class="hc-icon hi-purple"><i class="fas fa-balance-scale"></i></div><div><div class="hc-title">الضرائب والقانونية</div><div class="hc-sub">Tax & Legal</div></div></div><div class="hc-links"><a href="{{ route('taxes.index') }}" class="hc-link"><i class="fas fa-file-invoice-dollar"></i><span>لوحة الضرائب</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('taxes.property.index') }}" class="hc-link"><i class="fas fa-home"></i><span>ضريبة الأملاك</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('taxes.filings.index') }}" class="hc-link"><i class="fas fa-chart-pie"></i><span>الإقرارات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.companies.index') }}" class="hc-link"><i class="fas fa-briefcase"></i><span>الشركات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('taxes.analytics') }}" class="hc-link"><i class="fas fa-calculator"></i><span>الحاسبة</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-blue"><div class="hc-head"><div class="hc-icon hi-blue"><i class="fas fa-building"></i></div><div><div class="hc-title">إدارة المؤسسة</div><div class="hc-sub">Enterprise Suite</div></div></div><div class="hc-links"><a href="{{ route('enterprise.dashboard') }}" class="hc-link"><i class="fas fa-city"></i><span>لوحة المؤسسة</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('enterprise.accounts') }}" class="hc-link"><i class="fas fa-user-shield"></i><span>الحسابات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('enterprise.tenants') }}" class="hc-link"><i class="fas fa-users"></i><span>المستأجرين</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('enterprise.reports') }}" class="hc-link"><i class="fas fa-chart-area"></i><span>تقارير الأداء</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.projects.index') }}" class="hc-link"><i class="fas fa-project-diagram"></i><span>المشاريع</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-red"><div class="hc-head"><div class="hc-icon hi-red"><i class="fas fa-balance-scale"></i></div><div><div class="hc-title">القانونية والعقود</div><div class="hc-sub">Legal & Contracts</div></div></div><div class="hc-links"><a href="{{ route('documents.index') }}" class="hc-link"><i class="fas fa-file-alt"></i><span>المستندات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('contracts.index') }}" class="hc-link"><i class="fas fa-file-contract"></i><span>العقود</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('esignatures.index') }}" class="hc-link"><i class="fas fa-signature"></i><span>التوقيع الإلكتروني</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('notary.index') }}" class="hc-link"><i class="fas fa-stamp"></i><span>كاتب العدل</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('warranties.index') }}" class="hc-link"><i class="fas fa-shield-alt"></i><span>الضمانات</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-orange"><div class="hc-head"><div class="hc-icon hi-orange"><i class="fas fa-gavel"></i></div><div><div class="hc-title">المزادات والسوق</div><div class="hc-sub">Auctions & Market</div></div></div><div class="hc-links"><a href="{{ route('auctions.index') }}" class="hc-link"><i class="fas fa-gavel"></i><span>المزادات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('auctions.create') }}" class="hc-link"><i class="fas fa-plus-circle"></i><span>إضافة مزاد</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('auctions.results') }}" class="hc-link"><i class="fas fa-chart-bar"></i><span>نتائج المزادات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('auctions.my') }}" class="hc-link"><i class="fas fa-user-clock"></i><span>مزاداتي</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('reports.market.index') }}" class="hc-link"><i class="fas fa-chart-line"></i><span>تقرير السوق</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-green"><div class="hc-head"><div class="hc-icon hi-teal"><i class="fas fa-globe"></i></div><div><div class="hc-title">الخدمات العالمية</div><div class="hc-sub">Global Services</div></div></div><div class="hc-links"><a href="{{ route('currency.index') }}" class="hc-link"><i class="fas fa-money-bill-wave"></i><span>العملات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('language.index') }}" class="hc-link"><i class="fas fa-language"></i><span>اللوحات واللغات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('modules.dashboard') }}" class="hc-link"><i class="fas fa-cubes"></i><span>إدارة الموديولز</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('currency.converter') }}" class="hc-link"><i class="fas fa-exchange-alt"></i><span>محول العملات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('admin.settings.general') }}" class="hc-link"><i class="fas fa-cog"></i><span>إعدادات المنطقة</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-cyan"><div class="hc-head"><div class="hc-icon hi-cyan"><i class="fas fa-city"></i></div><div><div class="hc-title">العمليات العقارية</div><div class="hc-sub">Real Estate Ops</div></div></div><div class="hc-links"><a href="{{ route('properties.index') }}" class="hc-link"><i class="fas fa-building"></i><span>إدارة العقارات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('property-types.index') }}" class="hc-link"><i class="fas fa-tags"></i><span>أنواع العقارات</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('leads.index') }}" class="hc-link"><i class="fas fa-user-tag"></i><span>إدارة العملاء</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('companies.index') }}" class="hc-link"><i class="fas fa-briefcase"></i><span>الشركات والمكاتب</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('agents.index') }}" class="hc-link"><i class="fas fa-id-badge"></i><span>سجل الوكلاء</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-purple"><div class="hc-head"><div class="hc-icon hi-purple"><i class="fas fa-chart-line"></i></div><div><div class="hc-title">المالية المتقدمة</div><div class="hc-sub">Adv. Finance & DeFi</div></div></div><div class="hc-links"><a href="{{ route('financial.index') }}" class="hc-link"><i class="fas fa-chart-pie"></i><span>التحليل المالي</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="/roi" class="hc-link"><i class="fas fa-calculator"></i><span>حاسبة العائد</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="/cash-flow" class="hc-link"><i class="fas fa-money-bill-wave"></i><span>التدفق النقدي</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('defi.dashboard.index') }}" class="hc-link"><i class="fas fa-coins"></i><span>التمويل اللامركزي</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('defi.crowdfunding.index') }}" class="hc-link"><i class="fas fa-users-cog"></i><span>التمويل الجماعي</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
      <div class="hc hc-pink"><div class="hc-head"><div class="hc-icon hi-pink"><i class="fas fa-brain"></i></div><div><div class="hc-title">البيانات الضخمة</div><div class="hc-sub">Big Data & AI</div></div></div><div class="hc-links"><a href="{{ route('bigdata.predictive-ai.dashboard') }}" class="hc-link"><i class="fas fa-crystal-ball"></i><span>التحليل التنبؤي</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('bigdata.sentiment-analysis.dashboard') }}" class="hc-link"><i class="fas fa-smile"></i><span>تحليل المشاعر</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('bigdata.heatmaps.dashboard') }}" class="hc-link"><i class="fas fa-map-marked-alt"></i><span>الخرائط الحرارية</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('ai.valuation.dashboard') }}" class="hc-link"><i class="fas fa-search-dollar"></i><span>التقييم الذكي</span><i class="fas fa-chevron-left hc-arr"></i></a><a href="{{ route('ai.price-prediction') }}" class="hc-link"><i class="fas fa-chart-bar"></i><span>توقعات الأسعار</span><i class="fas fa-chevron-left hc-arr"></i></a></div></div>
    </div>

    <div class="sec-div" style="margin-top:24px"></div>
    <div class="sec-head anim a6"><div class="sec-accent"></div><div class="sec-title">سجل الأنشطة الأخيرة</div><div class="sec-pill">Recent Activity</div></div>

    <div class="panel anim a6">
      <div class="panel-head"><div><div class="panel-title">آخر الأنشطة على النظام</div></div><button onclick="location.reload()" class="tbtn tbtn-ghost" style="padding:6px 13px;font-size:11.5px"><i class="fas fa-sync-alt"></i> تحديث</button></div>
      <div class="panel-body" style="padding-top:6px">
        @forelse($stats['recent_activity'] as $activity)
          @php
            $icon = 'fa-bell';
            $color = '';
            if (is_array($activity)) {
              // Handle legacy array format (if any)
              $message = $activity['message'];
              $time = $activity['time'];
              $icon = 'fa-' . ($activity['icon'] ?? 'bell');
            } else {
              // Handle Eloquent Model
              $message = $activity->description ?: ($activity->action ?? 'نشاط غير معروف');
              $time = $activity->created_at->diffForHumans();

              if (str_contains($activity->subject_type ?? '', 'User')) {
                $icon = 'fa-user-plus';
              } elseif (str_contains($activity->subject_type ?? '', 'Property')) {
                $icon = 'fa-home';
              } elseif (str_contains($activity->subject_type ?? '', 'Subscription') || str_contains($activity->subject_type ?? '', 'Payment') || str_contains($activity->subject_type ?? '', 'Invoice')) {
                $icon = 'fa-file-invoice-dollar';
              } elseif (str_contains($activity->action ?? '', 'login') || str_contains($activity->description ?? '', 'security')) {
                $icon = 'fa-shield-alt';
              }
            }
          @endphp
          <div class="act-item">
            <div class="act-ic"><i class="fas {{ $icon }}"></i></div>
            <div>
              <div class="act-msg">{{ $message }}</div>
              <div class="act-time">{{ $time }}</div>
            </div>
            <div class="act-new"></div>
          </div>
        @empty
          <div class="act-item">
            <div class="act-ic"><i class="fas fa-info-circle"></i></div>
            <div>
              <div class="act-msg">لا توجد أنشطة حديثة</div>
              <div class="act-time">الآن</div>
            </div>
          </div>
        @endforelse
      </div>
    </div>

  </div>
</main>
</div>

<!-- MODAL -->
<div class="modal-overlay" id="modal">
  <div class="modal-box">
    <div class="modal-top"></div>
    <div class="modal-head">
      <div><div class="modal-title">تقارير النظام</div><div style="font-size:11.5px;color:var(--t4);margin-top:2px">إنشاء وتنزيل تحليلات النظام</div></div>
      <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="modal-grid">
        <div class="rep-card"><div class="rep-card-top"><div class="rep-ic"><i class="fas fa-users"></i></div><div class="rep-title">تقرير المستخدمين</div></div><div class="rep-desc">إحصاءات تفصيلية عن المستخدمين واتجاهات التسجيل وتحليلات النشاط.</div><div class="rep-action"><i class="fas fa-file-download"></i> إنشاء PDF</div></div>
        <div class="rep-card"><div class="rep-card-top"><div class="rep-ic"><i class="fas fa-chart-line"></i></div><div class="rep-title">التقرير المالي</div></div><div class="rep-desc">تحليل الإيرادات وسجل المدفوعات وسجلات المعاملات التفصيلية.</div><div class="rep-action"><i class="fas fa-file-download"></i> إنشاء PDF</div></div>
        <div class="rep-card"><div class="rep-card-top"><div class="rep-ic"><i class="fas fa-city"></i></div><div class="rep-title">تقرير العقارات</div></div><div class="rep-desc">حالة العقارات المعروضة ومعدلات الإشغال ومقاييس الأداء.</div><div class="rep-action"><i class="fas fa-file-download"></i> إنشاء PDF</div></div>
        <div class="rep-card"><div class="rep-card-top"><div class="rep-ic"><i class="fas fa-server"></i></div><div class="rep-title">تقرير النظام</div></div><div class="rep-desc">صحة الخادم وسجلات الأخطاء ومعايير الأداء التقنية.</div><div class="rep-action"><i class="fas fa-file-download"></i> إنشاء PDF</div></div>
      </div>
    </div>
    <div class="modal-foot"><button class="tbtn tbtn-ghost" onclick="closeModal()">إغلاق</button></div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  Chart.defaults.color = '#9CA3AF';
  Chart.defaults.borderColor = '#E5E7EB';
  Chart.defaults.font.family = "'Cairo','Plus Jakarta Sans',system-ui,sans-serif";
  const months = ['يناير','فبراير','مارس','أبريل','مايو','يونيو'];

  new Chart(document.getElementById('userChart'), {
    type: 'line',
    data: { labels: months, datasets: [{ label:'مستخدمون جدد', data:[82,145,230,178,312,267], borderColor:'#0D6EFD', backgroundColor:ctx=>{const g=ctx.chart.ctx.createLinearGradient(0,0,0,195);g.addColorStop(0,'rgba(13,110,253,.14)');g.addColorStop(1,'rgba(13,110,253,0)');return g;}, borderWidth:2.5, fill:true, tension:.4, pointBackgroundColor:'#0D6EFD', pointBorderColor:'#fff', pointBorderWidth:2.5, pointRadius:5, pointHoverRadius:7 }] },
    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false}, tooltip:{backgroundColor:'#1A1F36',borderColor:'rgba(13,110,253,.3)',borderWidth:1,padding:12,cornerRadius:10} }, scales:{ y:{beginAtZero:true,grid:{color:'#F3F4F6'},ticks:{font:{size:11}}}, x:{grid:{display:false},ticks:{font:{size:11}}} } }
  });

  new Chart(document.getElementById('revChart'), {
    type: 'bar',
    data: { labels: months, datasets: [{ label:'الإيرادات', data:[28000,42000,38000,55000,67000,72000], backgroundColor:ctx=>{const g=ctx.chart.ctx.createLinearGradient(0,0,0,195);g.addColorStop(0,'rgba(253,126,20,.85)');g.addColorStop(1,'rgba(253,126,20,.3)');return g;}, borderColor:'rgba(253,126,20,.8)', borderWidth:1.5, borderRadius:7, borderSkipped:false }] },
    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false}, tooltip:{backgroundColor:'#1A1F36',borderColor:'rgba(253,126,20,.3)',borderWidth:1,padding:12,cornerRadius:10,callbacks:{label:ctx=>'$'+ctx.parsed.y.toLocaleString()}} }, scales:{ y:{beginAtZero:true,grid:{color:'#F3F4F6'},ticks:{font:{size:11},callback:v=>'$'+(v/1000)+'k'}}, x:{grid:{display:false},ticks:{font:{size:11}}} } }
  });

  const cards = document.querySelectorAll('.hc');
  const io = new IntersectionObserver(entries=>{entries.forEach(e=>{if(e.isIntersecting){e.target.style.opacity='1';e.target.style.transform='translateY(0)';io.unobserve(e.target);}});},{threshold:0.05});
  cards.forEach((c,i)=>{c.style.opacity='0';c.style.transform='translateY(18px)';c.style.transition=`opacity .4s ease ${i*30}ms,transform .4s ease ${i*30}ms`;io.observe(c);});
});

function openModal(){document.getElementById('modal').classList.add('open');document.body.style.overflow='hidden';}
function closeModal(){document.getElementById('modal').classList.remove('open');document.body.style.overflow='';}
document.getElementById('modal').addEventListener('click',e=>{if(e.target===e.currentTarget)closeModal();});
</script>
</body>
</html>