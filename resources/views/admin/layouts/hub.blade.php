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
  <style>
    /* --- EWAN Logo Section Stylings --- */
.sb-logo {
  display: flex;
  align-items: center;
  gap: 15px; /* مسافة بين الأيقونة والاسم */
  padding: 15px 20px;
  background: linear-gradient(135deg, #1a1c23 0%, #111217 100%); /* خلفية داكنة فخمة */
  border-bottom: 1px solid rgba(255, 255, 255, 0.05); /* خط سفلي بسيط */
}

/* 1. الأيقونة (The Mark) */
.sb-logo-mark {
  width: 45px;
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #00b4d8 0%, #0077b6 100%); /* تدرج سيان احترافي */
  color: #fff;
  font-size: 22px;
  border-radius: 12px; /* حواف ناعمة */
  box-shadow: 0 4px 15px rgba(0, 180, 216, 0.3); /* ظل مضيء خلف الأيقونة */
  transform: rotate(-5deg); /* ميلان بسيط بيدي روح عصرية */
  transition: all 0.3s ease;
}

.sb-logo:hover .sb-logo-mark {
  transform: rotate(0deg) scale(1.1); /* تأثير عند الماوس */
}

/* 2. حاوي النص */
.sb-text-container {
  display: flex;
  flex-direction: column;
}

/* 3. اسم الشركة (EWAN) */
.sb-brand {
  font-family: 'Poppins', 'Helvetica Neue', sans-serif; /* خط مودرن وواضح */
  font-size: 24px;
  font-weight: 800; /* عريض جداً للقوة */
  letter-spacing: -1px; /* تضييق المسافات للاحترافية */
  color: #fff;
  line-height: 1;
  text-transform: uppercase; /* حروف كبيرة */
  display: flex;
  align-items: center;
}

/* الـ Span (AN) المميزة */
.sb-brand span {
  color: #00b4d8; /* لون السيان المميز */
  position: relative;
  margin-left: 1px;
}

/* خط سفلي فخم تحت الـ AN */
.sb-brand span::after {
  content: '';
  position: absolute;
  bottom: -3px;
  left: 0;
  width: 100%;
  height: 3px;
  background-color: #00b4d8;
  border-radius: 2px;
}

/* 4. الـ Tagline (Admin Portal) */
.sb-tagline {
  font-size: 11px;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.5); /* لون خافت عشان ميسرقش العين */
  text-transform: uppercase;
  letter-spacing: 1.5px; /* مسافات واسعة للشياكة */
  margin-top: 4px;
}
  </style>
</head>

<body>
  <div class="shell">

    <aside class="sidebar">
    <div class="sb-logo">
  <div class="sb-logo-mark">
    <i class="fas fa-building"></i> </div>
  
  <div class="sb-text-container">
    <div class="sb-brand">EW<span>AN</span></div>
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
@yield('content')
  </div>
</main>
</div>
@stack('scripts')
</body>
</html>