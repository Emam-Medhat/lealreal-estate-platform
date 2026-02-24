{{-- ════════════════════════════════════════════════════════════
NAVBAR — Real Estate Pro | Dual-Layer Mega Navigation
Top bar (context info) + Main bar (logo + search + actions)
+ Mega-menu bottom tab bar with icons
════════════════════════════════════════════════════════════ --}}

<style>
  /* ─────────────── BODY OFFSET ─────────────── */
  body {
    padding-top: 130px;
  }

  @media(max-width:1024px) {
    body {
      padding-top: 72px;
    }
  }

  /* ─────────────── VARIABLES ─────────────── */
  :root {
    --nb-blue: #2563eb;
    --nb-blue-dark: #1d4ed8;
    --nb-blue-xd: #1e3a8a;
    --nb-blue-light: #3b82f6;
    --nb-indigo: #4f46e5;
    --nb-white: #ffffff;
    --nb-gray-50: #f8fafc;
    --nb-gray-100: #f1f5f9;
    --nb-gray-200: #e2e8f0;
    --nb-gray-400: #94a3b8;
    --nb-gray-500: #64748b;
    --nb-gray-700: #334155;
    --nb-gray-900: #0f172a;
    --nb-h: 130px;
    --nb-top-h: 36px;
    --nb-main-h: 66px;
    --nb-tab-h: 44px;

    --nb-shadow: 0 4px 24px rgba(15, 23, 42, .18), 0 1px 4px rgba(15, 23, 42, .1);
    --nb-glow: 0 0 0 3px rgba(37, 99, 235, .2);
  }

  /* ─────────────── WRAPPER ─────────────── */
  #nb-root {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
  }

  /* ════════════════════════════════════════════
   TOP BAR  — ultra-slim context strip
════════════════════════════════════════════ */
  #nb-top {
    height: var(--nb-top-h);
    background: var(--nb-blue-xd);
    display: flex;
    align-items: center;
    padding: 0 24px;
    gap: 0;
    border-bottom: 1px solid rgba(255, 255, 255, .08);
    position: relative;
    overflow: hidden;
  }

  #nb-top::after {
    content: '';
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(90deg,
        transparent,
        transparent 120px,
        rgba(255, 255, 255, .02) 120px,
        rgba(255, 255, 255, .02) 121px);
    pointer-events: none;
  }

  .nb-top-left {
    display: flex;
    align-items: center;
    gap: 20px;
    flex: 1;
  }

  .nb-top-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    color: rgba(255, 255, 255, .55);
    white-space: nowrap;
    letter-spacing: .02em;
  }

  .nb-top-item i {
    font-size: 10px;
    color: rgba(255, 255, 255, .4);
  }

  .nb-top-item strong {
    color: rgba(255, 255, 255, .85);
    font-weight: 600;
  }

  .nb-top-sep {
    width: 1px;
    height: 16px;
    background: rgba(255, 255, 255, .1);
  }

  /* live dot */
  .nb-live-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #4ade80;
    box-shadow: 0 0 0 2px rgba(74, 222, 128, .25);
    animation: nb-pulse 2s infinite;
    flex-shrink: 0;
  }

  @keyframes nb-pulse {

    0%,
    100% {
      box-shadow: 0 0 0 2px rgba(74, 222, 128, .25);
    }

    50% {
      box-shadow: 0 0 0 5px rgba(74, 222, 128, .0);
    }
  }

  .nb-top-right {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
  }

  .nb-top-lang {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    color: rgba(255, 255, 255, .55);
    padding: 3px 8px;
    border-radius: 4px;
    cursor: pointer;
    transition: background .15s;
    border: none;
    background: transparent;
    font-family: inherit;
  }

  .nb-top-lang:hover {
    background: rgba(255, 255, 255, .08);
    color: rgba(255, 255, 255, .85);
  }

  .nb-top-lang.active {
    color: rgba(255, 255, 255, .9);
    font-weight: 600;
  }

  .nb-top-lang-sep {
    color: rgba(255, 255, 255, .2);
    font-size: 12px;
  }

  .nb-marquee-wrap {
    overflow: hidden;
    flex: 1;
    max-width: 320px;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0 16px;
  }

  .nb-marquee-label {
    font-size: 10px;
    background: rgba(250, 204, 21, .2);
    color: #fde047;
    border: 1px solid rgba(250, 204, 21, .2);
    border-radius: 3px;
    padding: 1px 7px;
    font-weight: 700;
    letter-spacing: .06em;
    white-space: nowrap;
    flex-shrink: 0;
  }

  .nb-marquee {
    font-size: 11px;
    color: rgba(255, 255, 255, .5);
    white-space: nowrap;
    overflow: hidden;
    animation: nb-marquee 28s linear infinite;
    flex: 1;
  }

  @keyframes nb-marquee {
    from {
      transform: translateX(100%);
    }

    to {
      transform: translateX(-100%);
    }
  }

  /* ════════════════════════════════════════════
   MAIN BAR  — logo + search + action icons
════════════════════════════════════════════ */
  #nb-main {
    height: var(--nb-main-h);
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 60%, #3b82f6 100%);
    display: flex;
    align-items: center;
    padding: 0 24px;
    gap: 16px;
    box-shadow: 0 2px 0 rgba(255, 255, 255, .08) inset;
    position: relative;
  }

  /* glass shimmer line on top */
  #nb-main::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .3) 30%, rgba(255, 255, 255, .5) 50%, rgba(255, 255, 255, .3) 70%, transparent);
  }

  /* ── Logo ── */
  .nb-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    flex-shrink: 0;
    position: relative;
  }

  .nb-logo-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, .15);
    border: 1.5px solid rgba(255, 255, 255, .25);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #fff;
    backdrop-filter: blur(4px);
    transition: transform .2s, background .2s;
    flex-shrink: 0;
  }

  .nb-logo:hover .nb-logo-icon {
    transform: rotate(-6deg) scale(1.08);
    background: rgba(255, 255, 255, .22);
  }

  .nb-logo-text {
    display: flex;
    flex-direction: column;
  }

  .nb-logo-name {
    font-size: 17px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1;
    letter-spacing: -.02em;
  }

  .nb-logo-tag {
    font-size: 9px;
    color: rgba(255, 255, 255, .55);
    font-weight: 500;
    letter-spacing: .1em;
    text-transform: uppercase;
    margin-top: 2px;
  }

  /* ── Search (center) ── */
  .nb-search-wrap {
    flex: 1;
    max-width: 560px;
    margin: 0 auto;
    position: relative;
  }

  .nb-search {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, .14);
    border: 1.5px solid rgba(255, 255, 255, .25);
    border-radius: 10px;
    height: 42px;
    transition: all .2s;
    overflow: hidden;
  }

  .nb-search:focus-within {
    background: rgba(255, 255, 255, .22);
    border-color: rgba(255, 255, 255, .6);
    box-shadow: var(--nb-glow);
  }

  .nb-search i {
    padding: 0 14px;
    color: rgba(255, 255, 255, .6);
    font-size: 14px;
    flex-shrink: 0;
  }

  .nb-search input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    font-size: 13.5px;
    font-family: inherit;
    color: #fff;
    caret-color: #fff;
    padding: 0 8px 0 0;
  }

  .nb-search input::placeholder {
    color: rgba(255, 255, 255, .45);
  }

  .nb-search-kbd {
    display: flex;
    align-items: center;
    gap: 3px;
    margin-right: 10px;
    flex-shrink: 0;
  }

  .nb-search-kbd kbd {
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, .12);
    border: 1px solid rgba(255, 255, 255, .2);
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    color: rgba(255, 255, 255, .5);
    width: 20px;
    height: 18px;
    font-family: inherit;
  }

  .nb-search-divider {
    width: 1px;
    height: 26px;
    background: rgba(255, 255, 255, .2);
    flex-shrink: 0;
  }

  .nb-search-type {
    background: transparent;
    border: none;
    outline: none;
    color: rgba(255, 255, 255, .75);
    font-family: inherit;
    font-size: 12px;
    font-weight: 600;
    padding: 0 12px;
    cursor: pointer;
    appearance: none;
    min-width: 100px;
  }

  .nb-search-type option {
    background: #1d4ed8;
  }

  /* ── Right action icons ── */
  .nb-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
  }

  .nb-action-btn {
    position: relative;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, .8);
    cursor: pointer;
    transition: all .18s;
    text-decoration: none;
    font-size: 16px;
  }

  .nb-action-btn:hover {
    background: rgba(255, 255, 255, .14);
    color: #fff;
    transform: translateY(-1px);
  }

  .nb-action-btn .nb-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    min-width: 16px;
    height: 16px;
    background: #ef4444;
    border-radius: 8px;
    border: 2px solid #1d4ed8;
    font-size: 9px;
    font-weight: 800;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 3px;
    line-height: 1;
  }

  /* Separator */
  .nb-actions-sep {
    width: 1px;
    height: 28px;
    background: rgba(255, 255, 255, .15);
    margin: 0 4px;
  }

  /* User avatar button */
  .nb-user-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 4px 12px 4px 4px;
    background: rgba(255, 255, 255, .12);
    border: 1.5px solid rgba(255, 255, 255, .2);
    border-radius: 10px;
    cursor: pointer;
    transition: all .18s;
    color: #fff;
    font-family: inherit;
  }

  .nb-user-btn:hover {
    background: rgba(255, 255, 255, .2);
    border-color: rgba(255, 255, 255, .4);
  }

  .nb-user-av {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: linear-gradient(135deg, #60a5fa, #818cf8);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 800;
    color: #fff;
    flex-shrink: 0;
  }

  .nb-user-info {
    display: flex;
    flex-direction: column;
    text-align: left;
  }

  .nb-user-name {
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    line-height: 1.2;
    max-width: 100px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .nb-user-role {
    font-size: 9.5px;
    color: rgba(255, 255, 255, .55);
    font-weight: 500;
    letter-spacing: .04em;
  }

  .nb-auth-btns {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .nb-login-btn {
    padding: 8px 16px;
    background: rgba(255, 255, 255, .12);
    border: 1.5px solid rgba(255, 255, 255, .25);
    border-radius: 8px;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all .18s;
    font-family: inherit;
    cursor: pointer;
  }

  .nb-login-btn:hover {
    background: rgba(255, 255, 255, .22);
  }

  .nb-register-btn {
    padding: 8px 18px;
    background: #fff;
    border: none;
    border-radius: 8px;
    color: var(--nb-blue-dark);
    font-size: 13px;
    font-weight: 800;
    text-decoration: none;
    transition: all .18s;
    font-family: inherit;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
  }

  .nb-register-btn:hover {
    background: #eff6ff;
    transform: translateY(-1px);
  }

  /* ════════════════════════════════════════════
   TAB BAR  — icon-labeled mega nav
════════════════════════════════════════════ */
  #nb-tabs {
    height: var(--nb-tab-h);
    background: var(--nb-white);
    border-top: 1px solid rgba(37, 99, 235, .08);
    display: flex;
    align-items: stretch;
    padding: 0 16px;
    box-shadow: var(--nb-shadow);
    position: relative;
  }

  /* Active indicator */
  .nb-tab-indicator {
    position: absolute;
    bottom: 0;
    height: 2.5px;
    background: var(--nb-blue);
    border-radius: 2px 2px 0 0;
    transition: left .25s cubic-bezier(.4, 0, .2, 1), width .25s cubic-bezier(.4, 0, .2, 1);
    pointer-events: none;
  }

  /* Each tab trigger */
  .nb-tab {
    position: relative;
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 0 14px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--nb-gray-500);
    cursor: pointer;
    border: none;
    background: transparent;
    font-family: inherit;
    white-space: nowrap;
    transition: color .15s;
    text-decoration: none;
    letter-spacing: .01em;
  }

  .nb-tab:hover {
    color: var(--nb-blue);
  }

  .nb-tab.active {
    color: var(--nb-blue);
  }

  .nb-tab i {
    font-size: 13px;
    transition: transform .2s;
  }

  .nb-tab:hover i {
    transform: translateY(-1px);
  }

  .nb-tab .nb-tab-badge {
    min-width: 16px;
    height: 16px;
    background: var(--nb-blue);
    color: #fff;
    border-radius: 8px;
    font-size: 9px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
  }

  .nb-tab .nb-tab-badge.red {
    background: #ef4444;
  }

  .nb-tab .nb-tab-badge.yellow {
    background: #f59e0b;
  }

  .nb-tab .nb-tab-badge.green {
    background: #10b981;
  }

  .nb-tab-chevron {
    font-size: 9px;
    color: var(--nb-gray-400);
    transition: transform .2s;
    margin-left: -2px;
  }

  .nb-tab.mega-open .nb-tab-chevron {
    transform: rotate(180deg);
  }

  .nb-tab-sep {
    width: 1px;
    height: 20px;
    background: var(--nb-gray-200);
    margin: 0 4px;
    align-self: center;
    flex-shrink: 0;
  }

  /* ════════════════════════════════════════════
   MEGA MENU PANEL
════════════════════════════════════════════ */
  .nb-mega {
    position: fixed;
    top: 130px;
    left: 0;
    right: 0;
    background: var(--nb-white);
    box-shadow: 0 20px 60px rgba(15, 23, 42, .18), 0 4px 16px rgba(15, 23, 42, .08);
    border-top: 2px solid var(--nb-blue);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-8px);
    transition: opacity .22s ease, transform .22s ease, visibility .22s;
    z-index: 9998;
    max-height: calc(100vh - 140px);
    overflow-y: auto;
  }

  .nb-mega.open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
  }

  .nb-mega-inner {
    max-width: 1400px;
    margin: 0 auto;
    padding: 32px 40px;
    display: grid;
    gap: 0;
  }

  /* Different column layouts per mega */
  .nb-mega.cols-3 .nb-mega-inner {
    grid-template-columns: 2fr 1fr 1fr;
    gap: 0;
  }

  .nb-mega.cols-4 .nb-mega-inner {
    grid-template-columns: repeat(4, 1fr);
  }

  .nb-mega.cols-5 .nb-mega-inner {
    grid-template-columns: 260px 1fr 1fr 1fr 1fr;
  }

  /* Featured block */
  .nb-mega-featured {
    padding-right: 32px;
    border-right: 1px solid var(--nb-gray-100);
    margin-right: 32px;
  }

  .nb-mega-featured-img {
    height: 160px;
    background: linear-gradient(135deg, #1e3a8a, #2563eb, #3b82f6);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 16px;
    position: relative;
    overflow: hidden;
  }

  .nb-mega-featured-img::after {
    content: '';
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(45deg, transparent, transparent 30px, rgba(255, 255, 255, .03) 30px, rgba(255, 255, 255, .03) 31px);
  }

  .nb-mega-featured-img i {
    font-size: 36px;
    color: rgba(255, 255, 255, .9);
    position: relative;
    z-index: 1;
  }

  .nb-mega-featured-img span {
    font-size: 13px;
    font-weight: 700;
    color: rgba(255, 255, 255, .8);
    position: relative;
    z-index: 1;
  }

  .nb-mega-featured-title {
    font-size: 16px;
    font-weight: 800;
    color: var(--nb-gray-900);
    margin-bottom: 6px;
  }

  .nb-mega-featured-desc {
    font-size: 12.5px;
    color: var(--nb-gray-500);
    line-height: 1.5;
    margin-bottom: 16px;
  }

  .nb-mega-featured-stats {
    display: flex;
    gap: 16px;
  }

  .nb-mf-stat {
    text-align: center;
  }

  .nb-mf-stat-val {
    font-size: 20px;
    font-weight: 800;
    color: var(--nb-blue);
    line-height: 1;
  }

  .nb-mf-stat-lbl {
    font-size: 10px;
    color: var(--nb-gray-400);
    text-transform: uppercase;
    letter-spacing: .08em;
    font-weight: 600;
  }

  /* Mega section */
  .nb-mega-section {
    padding: 0 20px;
  }

  .nb-mega-section:not(:first-child) {
    border-left: 1px solid var(--nb-gray-100);
  }

  .nb-mega-section-title {
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .15em;
    text-transform: uppercase;
    color: var(--nb-blue);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .nb-mega-section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--nb-gray-100);
  }

  /* Mega link */
  .nb-mega-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    border-radius: 8px;
    text-decoration: none;
    color: var(--nb-gray-700);
    font-size: 13px;
    font-weight: 500;
    transition: all .15s;
    margin-bottom: 2px;
    position: relative;
  }

  .nb-mega-link:hover {
    background: var(--nb-gray-50);
    color: var(--nb-blue);
    transform: translateX(3px);
  }

  .nb-mega-link i {
    width: 30px;
    height: 30px;
    background: var(--nb-gray-100);
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: var(--nb-gray-500);
    flex-shrink: 0;
    transition: all .15s;
  }

  .nb-mega-link:hover i {
    background: #dbeafe;
    color: var(--nb-blue);
  }

  .nb-mega-link-text {
    flex: 1;
  }

  .nb-mega-link-title {
    display: block;
    font-weight: 600;
    font-size: 13px;
  }

  .nb-mega-link-sub {
    display: block;
    font-size: 11px;
    color: var(--nb-gray-400);
    margin-top: 1px;
  }

  .nb-mega-link-badge {
    font-size: 9px;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 10px;
    background: #dbeafe;
    color: var(--nb-blue-dark);
    white-space: nowrap;
  }

  .nb-mega-link-badge.new {
    background: #dcfce7;
    color: #166534;
  }

  .nb-mega-link-badge.hot {
    background: #fee2e2;
    color: #991b1b;
  }

  /* Quick stats in mega */
  .nb-mega-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-top: 16px;
  }

  .nb-mega-stat-card {
    background: var(--nb-gray-50);
    border: 1px solid var(--nb-gray-100);
    border-radius: 8px;
    padding: 10px 12px;
    text-decoration: none;
    transition: all .15s;
    display: block;
  }

  .nb-mega-stat-card:hover {
    border-color: var(--nb-blue);
    background: #eff6ff;
  }

  .nb-mega-stat-card-val {
    font-size: 20px;
    font-weight: 800;
    color: var(--nb-blue);
    line-height: 1;
  }

  .nb-mega-stat-card-lbl {
    font-size: 10px;
    color: var(--nb-gray-500);
    margin-top: 3px;
    font-weight: 600;
  }

  /* Overlay */
  #nb-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, .35);
    backdrop-filter: blur(2px);
    opacity: 0;
    visibility: hidden;
    transition: opacity .22s, visibility .22s;
    z-index: 9997;
    top: 130px;
  }

  #nb-overlay.open {
    opacity: 1;
    visibility: visible;
  }

  /* ════════════════════════════════════════════
   DROPDOWN PANELS  (notifications, user)
════════════════════════════════════════════ */
  .nb-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    background: var(--nb-white);
    border: 1px solid var(--nb-gray-200);
    border-radius: 14px;
    box-shadow: 0 20px 60px rgba(15, 23, 42, .18);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-6px) scale(.97);
    transform-origin: top right;
    transition: all .2s cubic-bezier(.4, 0, .2, 1);
    z-index: 10000;
  }

  .nb-dropdown.open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
  }

  /* Notifications panel */
  #nb-notif-panel {
    width: 380px;
  }

  .nb-dropdown-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 18px;
    border-bottom: 1px solid var(--nb-gray-100);
  }

  .nb-dropdown-head h3 {
    font-size: 15px;
    font-weight: 800;
    color: var(--nb-gray-900);
  }

  .nb-dropdown-head-actions {
    display: flex;
    gap: 8px;
    align-items: center;
  }

  .nb-dha-btn {
    font-size: 11px;
    font-weight: 600;
    color: var(--nb-blue);
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const userId = {{ auth()->id() }};

        if (userId) {
            window.Echo.private(`App.Models.User.${userId}`)
                .notification((notification) => {
                    console.log('New notification received:', notification);

                    // 1. Create the notification element
                    const notificationElement = createNotificationElement(notification);

                    // 2. Add it to the top of the list
                    const list = document.getElementById('nb-notif-list');
                    list.prepend(notificationElement);

                    // 3. Update the unread count
                    updateUnreadCount(1);

                    // 4. Show a toast notification (optional, but good UX)
                    showToast(notification.title, notification.message);
                });
        }

        function createNotificationElement(notification) {
            const element = document.createElement('a');
            element.href = notification.url;
            element.className = 'nb-notif-item unread';
            element.id = `notif-${notification.id}`;

            element.innerHTML = `
                <div class="nb-notif-icon" style="background-color: ${notification.color || '#2563eb'}30; color: ${notification.color || '#2563eb'};">
                    <i class="fa-solid fa-${notification.icon || 'bell'}"></i>
                </div>
                <div class="nb-notif-content">
                    <p class="nb-notif-text">${notification.message}</p>
                    <span class="nb-notif-time">${notification.time}</span>
                </div>
                <div class="nb-notif-dot"></div>
            `;
            return element;
        }

        function updateUnreadCount(change) {
            const unreadCountElement = document.getElementById('nb-unread-count');
            if (unreadCountElement) {
                let currentCount = parseInt(unreadCountElement.textContent) || 0;
                const newCount = currentCount + change;
                unreadCountElement.textContent = newCount;
                unreadCountElement.style.display = newCount > 0 ? 'flex' : 'none';
            }
        }

        function showToast(title, message) {
            // You can implement your own toast notification logic here
            // For example, using a library like Toastify or creating a simple div
            const toast = document.createElement('div');
            toast.className = 'notification-toast';
            toast.innerHTML = `<strong>${title}</strong><p>${message}</p>`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.remove();
            }, 5000);
        }
    });
</script>
    background: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
    padding: 4px 8px;
    border-radius: 6px;
    transition: background .15s;
  }

  .nb-dha-btn:hover {
    background: #eff6ff;
  }

  .nb-notif-list {
    max-height: 340px;
    overflow-y: auto;
  }

  .nb-notif-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 18px;
    border-bottom: 1px solid var(--nb-gray-100);
    cursor: pointer;
    transition: background .15s;
    position: relative;
  }

  .nb-notif-item:hover {
    background: var(--nb-gray-50);
  }

  .nb-notif-item.unread {
    background: #eff6ff;
  }

  .nb-notif-item.unread::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 5px;
    height: 5px;
    background: var(--nb-blue);
    border-radius: 50%;
  }

  .nb-notif-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
  }

  .ni-green {
    background: #dcfce7;
    color: #16a34a;
  }

  .ni-blue {
    background: #dbeafe;
    color: #1d4ed8;
  }

  .ni-red {
    background: #fee2e2;
    color: #dc2626;
  }

  .ni-yellow {
    background: #fef9c3;
    color: #a16207;
  }

  .ni-purple {
    background: #ede9fe;
    color: #7c3aed;
  }

  .nb-notif-body {
    flex: 1;
    min-width: 0;
  }

  .nb-notif-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--nb-gray-800);
    line-height: 1.3;
  }

  .nb-notif-time {
    font-size: 11px;
    color: var(--nb-gray-400);
    margin-top: 3px;
  }

  .nb-notif-footer {
    padding: 12px 18px;
    text-align: center;
    border-top: 1px solid var(--nb-gray-100);
  }

  .nb-notif-footer a {
    font-size: 13px;
    font-weight: 700;
    color: var(--nb-blue);
    text-decoration: none;
  }

  .nb-notif-empty {
    padding: 40px 20px;
    text-align: center;
    color: var(--nb-gray-400);
  }

  .nb-notif-empty i {
    font-size: 32px;
    display: block;
    margin-bottom: 10px;
  }

  .nb-notif-empty p {
    font-size: 13px;
  }

  /* User panel */
  #nb-user-panel {
    width: 300px;
  }

  .nb-user-panel-head {
    padding: 18px;
    background: linear-gradient(135deg, #eff6ff, #f0f9ff);
    border-radius: 14px 14px 0 0;
    border-bottom: 1px solid var(--nb-gray-100);
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .nb-up-av {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--nb-blue-light), var(--nb-indigo));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 800;
    color: #fff;
    flex-shrink: 0;
  }

  .nb-up-name {
    font-size: 14px;
    font-weight: 800;
    color: var(--nb-gray-900);
  }

  .nb-up-email {
    font-size: 11px;
    color: var(--nb-gray-500);
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 180px;
  }

  .nb-up-role-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .07em;
    text-transform: uppercase;
    padding: 2px 8px;
    border-radius: 20px;
    margin-top: 4px;
  }

  .nb-up-role-admin {
    background: #fef9c3;
    color: #a16207;
  }

  .nb-up-role-agent {
    background: #dbeafe;
    color: #1d4ed8;
  }

  .nb-up-role-user {
    background: #dcfce7;
    color: #15803d;
  }

  /* Language Switcher Styles */
  .nb-up-language-switcher {
    padding: 8px;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .nb-up-language-switcher .nb-up-link.active {
    background: linear-gradient(135deg, #eff6ff, #f0f9ff);
    color: var(--nb-blue);
    font-weight: 600;
  }

  .nb-up-links {
    padding: 8px;
  }

  .nb-up-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: 8px;
    text-decoration: none;
    color: var(--nb-gray-700);
    font-size: 13px;
    font-weight: 500;
    transition: all .15s;
    position: relative;
  }

  .nb-up-link:hover {
    background: var(--nb-gray-50);
    color: var(--nb-blue);
  }

  .nb-up-link i {
    width: 20px;
    text-align: center;
    font-size: 13px;
    color: var(--nb-gray-400);
    transition: color .15s;
  }

  .nb-up-link:hover i {
    color: var(--nb-blue);
  }

  .nb-up-link .nb-up-badge {
    margin-left: auto;
    min-width: 18px;
    height: 18px;
    background: #ef4444;
    color: #fff;
    border-radius: 9px;
    font-size: 10px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
  }

  .nb-up-sep {
    height: 1px;
    background: var(--nb-gray-100);
    margin: 4px 12px;
  }

  .nb-up-logout {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 9px 12px;
    border-radius: 8px;
    color: #dc2626;
    font-size: 13px;
    font-weight: 600;
    transition: all .15s;
    background: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
    text-align: left;
  }

  .nb-up-logout:hover {
    background: #fee2e2;
  }

  .nb-up-logout i {
    color: #dc2626;
  }

  /* ════════════════════════════════════════════
   MOBILE NAV (< 1024px)
════════════════════════════════════════════ */
  @media(max-width:1024px) {
    #nb-top {
      display: none;
    }

    #nb-main {
      height: 72px;
    }

    #nb-tabs {
      display: none;
    }

    #nb-mob-btn {
      display: flex !important;
    }

    #nb-main .nb-search-wrap {
      display: none;
    }

    body {
      padding-top: 72px;
    }
  }

  #nb-mob-btn {
    display: none;
    width: 40px;
    height: 40px;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, .14);
    border: 1.5px solid rgba(255, 255, 255, .25);
    border-radius: 10px;
    color: #fff;
    font-size: 18px;
    cursor: pointer;
    margin-left: auto;
    transition: all .18s;
  }

  #nb-mob-btn:hover {
    background: rgba(255, 255, 255, .22);
  }

  #nb-mobile {
    position: fixed;
    top: 72px;
    left: 0;
    right: 0;
    background: var(--nb-white);
    height: calc(100vh - 72px);
    overflow-y: auto;
    transform: translateX(-100%);
    transition: transform .3s cubic-bezier(.4, 0, .2, 1);
    z-index: 9998;
    box-shadow: var(--nb-shadow);
  }

  #nb-mobile.open {
    transform: translateX(0);
  }

  .nb-mob-inner {
    padding: 16px;
  }

  .nb-mob-search {
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--nb-gray-50);
    border: 1.5px solid var(--nb-gray-200);
    border-radius: 10px;
    padding: 10px 14px;
    margin-bottom: 16px;
  }

  .nb-mob-search input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    font-size: 14px;
    font-family: inherit;
    color: var(--nb-gray-900);
  }

  .nb-mob-search i {
    color: var(--nb-gray-400);
  }

  .nb-mob-section {
    margin-bottom: 8px;
  }

  .nb-mob-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: background .15s;
  }

  .nb-mob-section-head:hover {
    background: var(--nb-gray-50);
  }

  .nb-mob-section-label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 700;
    color: var(--nb-gray-800);
  }

  .nb-mob-section-label .nb-mob-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #dbeafe;
    color: var(--nb-blue);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
  }

  .nb-mob-section-chevron {
    font-size: 11px;
    color: var(--nb-gray-400);
    transition: transform .2s;
  }

  .nb-mob-section-head.open .nb-mob-section-chevron {
    transform: rotate(90deg);
  }

  .nb-mob-links {
    padding: 4px 8px 8px 50px;
    display: none;
  }

  .nb-mob-links.open {
    display: block;
  }

  .nb-mob-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 10px;
    border-radius: 7px;
    text-decoration: none;
    color: var(--nb-gray-600);
    font-size: 13px;
    font-weight: 500;
    transition: all .15s;
    margin-bottom: 2px;
  }

  .nb-mob-link:hover {
    background: #eff6ff;
    color: var(--nb-blue);
  }

  .nb-mob-link i {
    width: 16px;
    text-align: center;
    font-size: 12px;
    color: var(--nb-gray-400);
  }

  .nb-mob-sep {
    height: 1px;
    background: var(--nb-gray-100);
    margin: 8px 0;
  }
</style>

{{-- ══════════════════════════════════════════════ --}}
{{-- NAVBAR ROOT --}}
{{-- ══════════════════════════════════════════════ --}}
<div id="nb-root">

  {{-- ── TOP BAR ── --}}
  <div id="nb-top">
    <div class="nb-top-left">
      <span class="nb-top-item">
        <span class="nb-live-dot"></span>
        <strong>System Online</strong>
      </span>
      <div class="nb-top-sep"></div>
      <span class="nb-top-item"><i class="fas fa-building"></i> <strong>12,847</strong>&nbsp;Properties</span>
      <div class="nb-top-sep"></div>
      <span class="nb-top-item"><i class="fas fa-user-tie"></i> <strong>438</strong>&nbsp;Agents</span>
      <div class="nb-top-sep"></div>
      <span class="nb-top-item"><i class="fas fa-clock"></i> {{ now()->format('D, d M Y') }}</span>
    </div>

    <div class="nb-marquee-wrap">
      <span class="nb-marquee-label">NEW</span>
      <div class="nb-marquee">🏡 Villa in New Cairo — $1.2M &nbsp;&nbsp; 🏢 Office Space Maadi — $420K &nbsp;&nbsp; 🌊
        North Coast Chalet — $380K &nbsp;&nbsp; 🏙️ Downtown Apt — $290K &nbsp;&nbsp;</div>
    </div>

    <div class="nb-top-right">
      <button class="nb-top-lang active">EN</button>
      <span class="nb-top-lang-sep">|</span>
      <button class="nb-top-lang">AR</button>
      <span class="nb-top-lang-sep">|</span>
      <a href="#" class="nb-top-lang"><i class="fas fa-headset" style="font-size:10px;margin-right:4px"></i>Support</a>
    </div>
  </div>

  {{-- ── MAIN BAR ── --}}
  <div id="nb-main">
    {{-- Logo --}}
    <a href="/" class="nb-logo">
      <div class="nb-logo-icon"><i class="fas fa-building"></i></div>
      <div class="nb-logo-text">
        <span class="nb-logo-name">Real Estate Pro</span>
        <span class="nb-logo-tag">Enterprise Platform</span>
      </div>
    </a>

    {{-- Search --}}
    <div class="nb-search-wrap">
      <div class="nb-search">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search properties, agents, cities…" id="nbMainSearch" autocomplete="off">
        <div class="nb-search-kbd">
          <kbd>⌘</kbd><kbd>K</kbd>
        </div>
        <div class="nb-search-divider"></div>
        <select class="nb-search-type">
          <option>All Types</option>
          <option>Apartment</option>
          <option>Villa</option>
          <option>House</option>
          <option>Office</option>
          <option>Land</option>
        </select>
      </div>
    </div>

    {{-- Actions --}}
    <div class="nb-actions">
      @auth

        {{-- Favourites --}}
        <a href="{{ route('properties.favorites') }}" class="nb-action-btn" title="Favorites">
          <i class="fas fa-heart"></i>
          <span class="nb-badge" style="display:none">3</span>
        </a>

        {{-- Cart --}}
        <a href="/cart" class="nb-action-btn" title="Cart">
          <i class="fas fa-shopping-cart"></i>
          @if (isset(Auth::user()->cart_count) && Auth::user()->cart_count > 0)
            <span class="nb-badge">{{ Auth::user()->cart_count }}</span>
          @endif
        </a>

        {{-- Notifications --}}
        <div class="nb-action-btn" id="nb-notif-btn" onclick="nbToggle('notif')" title="Notifications"
          style="cursor:pointer">
          <i class="fas fa-bell"></i>
          <span id="nb-notif-badge" class="nb-badge {{ Auth::user()->unreadNotifications()->count() > 0 ? '' : 'hidden' }}">
            {{ Auth::user()->unreadNotifications()->count() > 99 ? '99+' : Auth::user()->unreadNotifications()->count() }}
          </span>
        </div>

        <div class="nb-actions-sep"></div>

        {{-- User button --}}
        <div class="nb-user-btn" id="nb-user-btn" onclick="nbToggle('user')" style="cursor:pointer">
          @if(Auth::user()->avatar_url)
            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="nb-user-av" style="object-fit: cover; background: none;">
          @else
            <div class="nb-user-av">
              <i class="fas fa-user"></i>
            </div>
          @endif
          <div class="nb-user-info">
            <span class="nb-user-name">{{ Auth::user()->name }}</span>
            <span class="nb-user-role">
              @if(Auth::user()->is_admin || Auth::user()->user_type === 'admin') Admin
              @elseif(Auth::user()->is_agent || Auth::user()->user_type === 'agent') Agent
              @else User
              @endif
            </span>
          </div>
          <i class="fas fa-chevron-down" style="font-size:9px;color:rgba(255,255,255,.5);margin-left:2px"></i>
        </div>

      @else
        <div class="nb-auth-btns">
          <a href="{{ url('login') }}" class="nb-login-btn">Login</a>
          <a href="{{ url('register') }}" class="nb-register-btn">Register Free</a>
        </div>
      @endauth

      {{-- Mobile toggle --}}
      <button id="nb-mob-btn" onclick="nbToggleMobile()"><i class="fas fa-bars" id="nb-mob-icon"></i></button>
    </div>

    {{-- Notification Dropdown --}}
    @auth
      <div class="nb-dropdown" id="nb-notif-panel">
        <div class="nb-dropdown-head">
          <h3>Notifications <span id="nb-notif-count"
              style="color:var(--nb-gray-400);font-weight:400;font-size:13px">({{ Auth::user()->unreadNotifications()->count() }}
              unread)</span></h3>
          <div class="nb-dropdown-head-actions">
            <button class="nb-dha-btn" onclick="markAllAsRead()">تحديد الكل كمقروء</button>
            <a href="{{ route('user.notifications') }}" class="nb-dha-btn">عرض الكل</a>
          </div>

<script>
    function markAllAsRead() {
        fetch('{{ route("user.notifications.read.all") }}', {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove unread indicator from all items in the dropdown
                document.querySelectorAll('#nb-notif-list .nb-notif-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    const dot = item.querySelector('.nb-notif-dot');
                    if (dot) {
                        dot.style.display = 'none';
                    }
                });

                // Hide the main unread count badge
                const unreadCountBadge = document.getElementById('nb-unread-count');
                if (unreadCountBadge) {
                    unreadCountBadge.style.display = 'none';
                    unreadCountBadge.textContent = '0';
                }
                
                // Optionally hide the "Mark all read" button itself
                const markAllButton = document.querySelector('.nb-dha-btn[onclick="markAllAsRead()"]');
                if(markAllButton) {
                    markAllButton.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Error marking all as read:', error));
    }
</script>
        </div>
        <div class="nb-notif-list" id="nb-notif-list">
          @php $notifs = Auth::user()->notifications()->latest()->limit(6)->get(); @endphp
          @forelse($notifs as $notif)
            <div class="nb-notif-item {{ $notif->read_at ? '' : 'unread' }}" onclick="nbMarkRead('{{ $notif->id }}')">
              <div class="nb-notif-icon {{ !$notif->read_at ? 'ni-blue' : 'ni-blue' }}">
                @if(($notif->data['type'] ?? '') === 'property_created') <i class="fas fa-home"></i>
                @elseif(($notif->data['type'] ?? '') === 'wallet_deposit') <i class="fas fa-arrow-down"></i>
                @elseif(($notif->data['type'] ?? '') === 'wallet_withdrawal') <i class="fas fa-arrow-up"></i>
                @else <i class="fas fa-bell"></i>
                @endif
              </div>
              <div class="nb-notif-body">
                <div class="nb-notif-title">{{ $notif->data['title'] ?? 'Notification' }}</div>
                <div class="nb-notif-time">{{ $notif->created_at->diffForHumans() }}</div>
              </div>
            </div>
          @empty
            <div class="nb-notif-empty">
              <i class="fas fa-bell-slash"></i>
              <p>No notifications yet</p>
            </div>
          @endforelse
        </div>
        <div class="nb-notif-footer"><a href="{{ route('user.notifications') }}">See all notifications →</a></div>
      </div>

      {{-- User Dropdown --}}
      <div class="nb-dropdown" id="nb-user-panel" style="right:0">
        <div class="nb-user-panel-head">
          @if(Auth::user()->avatar_url)
              <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="nb-up-av" style="object-fit: cover; background: none;">
          @else
              <div class="nb-up-av">
                  <i class="fas fa-user"></i>
              </div>
          @endif
          <div>
            <div class="nb-up-name">{{ Auth::user()->name }}</div>
            <div class="nb-up-email">{{ Auth::user()->email }}</div>
            <div
              class="nb-up-role-badge {{ (Auth::user()->is_admin || Auth::user()->user_type === 'admin') ? 'nb-up-role-admin' : ((Auth::user()->is_agent || Auth::user()->user_type === 'agent') ? 'nb-up-role-agent' : 'nb-up-role-user') }}">
              <i
                class="fas fa-{{ (Auth::user()->is_admin || Auth::user()->user_type === 'admin') ? 'shield-alt' : ((Auth::user()->is_agent || Auth::user()->user_type === 'agent') ? 'briefcase' : 'user') }}"></i>
              {{ (Auth::user()->is_admin || Auth::user()->user_type === 'admin') ? 'Admin' : ((Auth::user()->is_agent || Auth::user()->user_type === 'agent') ? 'Agent' : 'Member') }}
            </div>
          </div>
        </div>
        <div class="nb-up-links">
          <a href="{{ route('dashboard') }}" class="nb-up-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
          <a href="{{ route('user.profile.edit') }}" class="nb-up-link"><i class="fas fa-user"></i> My Profile</a>
          <a href="/wallet" class="nb-up-link"><i class="fas fa-wallet"></i> My Wallet</a>
          <a href="{{ route('properties.favorites') }}" class="nb-up-link"><i class="fas fa-heart"></i> Favourites</a>
          <a href="{{ route('orders.index') }}" class="nb-up-link"><i class="fas fa-shopping-bag"></i> My Orders</a>
          <a href="{{ route('user.notifications') }}" class="nb-up-link"><i class="fas fa-bell"></i> Notifications
            @if(Auth::user()->unreadNotifications()->count() > 0)
              <span class="nb-up-badge">{{ Auth::user()->unreadNotifications()->count() }}</span>
            @endif
          </a>
          <div class="nb-up-sep"></div>
          @if(Auth::user()->is_agent || Auth::user()->user_type === 'agent')
            <a href="{{ route('agent.dashboard') }}" class="nb-up-link"><i class="fas fa-briefcase"></i> Agent Panel</a>
          @endif
          @if(Auth::user()->is_admin || Auth::user()->user_type === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="nb-up-link"><i class="fas fa-shield-alt"></i> Admin Panel</a>
          @endif
          <a href="{{ route('settings.index') }}" class="nb-up-link"><i class="fas fa-cog"></i> Settings</a>
          
          {{-- Language Switcher --}}
          <div class="nb-up-language-switcher">
            <a href="{{ route('lang.switch', 'ar') }}" class="nb-up-link {{ app()->getLocale() === 'ar' ? 'active' : '' }}">
              🇸🇦 العربية
            </a>
            <a href="{{ route('lang.switch', 'en') }}" class="nb-up-link {{ app()->getLocale() === 'en' ? 'active' : '' }}">
              🇬🇧 English
            </a>
          </div>
          
          <div class="nb-up-sep"></div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nb-up-logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
          </form>
        </div>
      </div>
    @endauth
  </div>

  {{-- ── TAB BAR ── --}}
  <div id="nb-tabs">
    <div class="nb-tab-indicator" id="nb-indicator"></div>

    {{-- Home --}}
    <a href="/" class="nb-tab" data-tab="home">
      <i class="fas fa-home"></i> Home
    </a>

    {{-- Properties --}}
    @auth
      <a href="{{ route('properties.index') }}" class="nb-tab" data-tab="properties">
        <i class="fas fa-building"></i> Properties
      </a>

      {{-- Agents --}}
      <a href="{{ route('agents.directory') }}" class="nb-tab" data-tab="agents">
        <i class="fas fa-user-tie"></i> Agents
      </a>
    @endauth

    @auth
      @if(Auth::user()->is_admin || Auth::user()->user_type === 'admin')
        <div class="nb-tab-sep"></div>

        {{-- Admin Panel --}}
        <button class="nb-tab" data-mega="admin" onclick="nbMega('admin',this)">
          <i class="fas fa-shield-alt"></i> Admin Panel
          <i class="fas fa-chevron-down nb-tab-chevron"></i>
        </button>

        {{-- System & Finance --}}
        <button class="nb-tab" data-mega="system" onclick="nbMega('system',this)">
          <i class="fas fa-cogs"></i> System & Finance
          <i class="fas fa-chevron-down nb-tab-chevron"></i>
        </button>

        {{-- Content --}}
        <button class="nb-tab" data-mega="content" onclick="nbMega('content',this)">
          <i class="fas fa-file-alt"></i> Content
          <i class="fas fa-chevron-down nb-tab-chevron"></i>
        </button>
      @endif

      @if(Auth::user()->is_agent || Auth::user()->user_type === 'agent')
        <div class="nb-tab-sep"></div>

        {{-- Agent Panel --}}
        <button class="nb-tab" data-mega="agent" onclick="nbMega('agent',this)">
          <i class="fas fa-briefcase"></i> Agent Panel
          <i class="fas fa-chevron-down nb-tab-chevron"></i>
        </button>

        {{-- Quick Add --}}
        <a href="{{ route('agent.properties.create') }}" class="nb-tab" style="color:var(--nb-blue)">
          <i class="fas fa-plus-circle"></i> Quick Add
        </a>
      @endif

      @if(!Auth::user()->is_admin && !Auth::user()->is_agent && Auth::user()->user_type !== 'admin' && Auth::user()->user_type !== 'agent')
        <div class="nb-tab-sep"></div>
        <a href="{{ route('dashboard') }}" class="nb-tab"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="/wallet" class="nb-tab"><i class="fas fa-wallet"></i> Wallet</a>
        <a href="{{ route('properties.favorites') }}" class="nb-tab"><i class="fas fa-heart"></i> Favorites</a>
        <a href="{{ route('orders.index') }}" class="nb-tab"><i class="fas fa-shopping-bag"></i> Orders</a>
      @endif
    @endauth

    @guest
      <div class="nb-tab-sep"></div>
      <a href="{{ url('login') }}" class="nb-tab"><i class="fas fa-sign-in-alt"></i> Login</a>
      <a href="{{ url('register') }}" class="nb-tab" style="color:var(--nb-blue)"><i class="fas fa-user-plus"></i>
        Register</a>
    @endguest
  </div>

</div>{{-- /nb-root --}}

{{-- ════ MEGA MENUS ════ --}}
<div id="nb-overlay" onclick="nbCloseMega()"></div>

{{-- ADMIN MEGA --}}
<div class="nb-mega cols-5" id="nb-mega-admin">
  <div class="nb-mega-inner">
    {{-- Featured --}}
    <div class="nb-mega-featured">
      <div class="nb-mega-featured-img">
        <i class="fas fa-shield-alt"></i>
        <span>Admin Control Center</span>
      </div>
      <div class="nb-mega-featured-title">System Overview</div>
      <div class="nb-mega-featured-desc">Full control over every module of the Real Estate Pro platform.</div>
      <div class="nb-mega-stats">
        <a href="{{ route('admin.users.index') }}" class="nb-mega-stat-card">
          <div class="nb-mega-stat-card-val">12.8K</div>
          <div class="nb-mega-stat-card-lbl">Total Users</div>
        </a>
        <a href="{{ route('admin.properties.index') }}" class="nb-mega-stat-card">
          <div class="nb-mega-stat-card-val">4,291</div>
          <div class="nb-mega-stat-card-lbl">Properties</div>
        </a>
        <a href="{{ route('admin.agents.index') }}" class="nb-mega-stat-card">
          <div class="nb-mega-stat-card-val">438</div>
          <div class="nb-mega-stat-card-lbl">Active Agents</div>
        </a>
        <a href="{{ route('admin.financial.dashboard') }}" class="nb-mega-stat-card">
          <div class="nb-mega-stat-card-val">$2.4M</div>
          <div class="nb-mega-stat-card-lbl">Monthly Revenue</div>
        </a>
      </div>
    </div>

    {{-- Core --}}
    <div class="nb-mega-section">
      <div class="nb-mega-section-title">Core</div>
      <a href="{{ route('admin.dashboard') }}" class="nb-mega-link"><i class="fas fa-tachometer-alt"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Dashboard</span><span
            class="nb-mega-link-sub">Overview & KPIs</span></span></a>
      <a href="{{ route('admin.users.index') }}" class="nb-mega-link"><i class="fas fa-users"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Users</span><span class="nb-mega-link-sub">Manage
            accounts</span></span></a>
      <a href="{{ route('admin.agents.index') }}" class="nb-mega-link"><i class="fas fa-user-tie"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Agents</span><span class="nb-mega-link-sub">Agents
            & brokers</span></span></a>
      <a href="{{ route('admin.companies.index') }}" class="nb-mega-link"><i class="fas fa-briefcase"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Companies</span><span
            class="nb-mega-link-sub">Developer firms</span></span></a>
    </div>

    {{-- Properties --}}
    <div class="nb-mega-section">
      <div class="nb-mega-section-title">Properties</div>
      <a href="{{ route('admin.properties.index') }}" class="nb-mega-link"><i class="fas fa-building"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">All Properties</span><span
            class="nb-mega-link-sub">Browse & manage</span></span></a>
      <a href="{{ route('properties.create') }}" class="nb-mega-link"><i class="fas fa-plus"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Add Property</span></span><span
          class="nb-mega-link-badge new">New</span></a>
      <a href="{{ route('maintenance.index') }}" class="nb-mega-link"><i class="fas fa-tools"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Maintenance</span></span></a>
      <a href="{{ route('inventory.index') }}" class="nb-mega-link"><i class="fas fa-boxes"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Inventory</span></span></a>
      <a href="{{ route('projects.index') }}" class="nb-mega-link"><i class="fas fa-project-diagram"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Projects</span></span></a>
    </div>

    {{-- Finance --}}
    <div class="nb-mega-section">
      <div class="nb-mega-section-title">Finance</div>
      <a href="{{ route('admin.financial.dashboard') }}" class="nb-mega-link"><i class="fas fa-chart-line"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Financial Dashboard</span></span></a>
      <a href="{{ route('admin.financial.transactions') }}" class="nb-mega-link"><i
          class="fas fa-exchange-alt"></i><span class="nb-mega-link-text"><span
            class="nb-mega-link-title">Transactions</span></span></a>
      <a href="{{ route('admin.financial.invoices') }}" class="nb-mega-link"><i
          class="fas fa-file-invoice-dollar"></i><span class="nb-mega-link-text"><span
            class="nb-mega-link-title">Invoices</span></span></a>
      <a href="{{ route('admin.reports.financial.index') }}" class="nb-mega-link"><i
          class="fas fa-file-invoice"></i><span class="nb-mega-link-text"><span class="nb-mega-link-title">Financial
            Reports</span></span></a>
      <a href="{{ route('admin.reports.agent.performance') }}" class="nb-mega-link"><i
          class="fas fa-user-check"></i><span class="nb-mega-link-text"><span class="nb-mega-link-title">Agent
            Performance</span></span></a>
    </div>

    {{-- System --}}
    <div class="nb-mega-section">
      <div class="nb-mega-section-title">System</div>
      <a href="{{ route('admin.system.dashboard') }}" class="nb-mega-link"><i class="fas fa-server"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">System Dashboard</span></span></a>
      <a href="{{ route('admin.seo.index') }}" class="nb-mega-link"><i class="fas fa-search"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">SEO Manager</span></span></a>
      <a href="{{ route('admin.system.logs') }}" class="nb-mega-link"><i class="fas fa-bug"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">System Logs</span></span><span
          class="nb-mega-link-badge hot">Live</span></a>
      <a href="{{ route('admin.system.backup') }}" class="nb-mega-link"><i class="fas fa-database"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Backups</span></span></a>
      <a href="{{ route('admin.settings.index') }}" class="nb-mega-link"><i class="fas fa-cogs"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Settings</span></span></a>
    </div>
  </div>
</div>

{{-- SYSTEM & FINANCE MEGA --}}
<div class="nb-mega cols-4" id="nb-mega-system">
  <div class="nb-mega-inner">
    <div class="nb-mega-section" style="padding-left:0;border:none">
      <div class="nb-mega-section-title">Finance</div>
      <a href="{{ route('admin.financial.dashboard') }}" class="nb-mega-link"><i class="fas fa-chart-line"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Financial Dashboard</span><span
            class="nb-mega-link-sub">Revenue & expenses</span></span></a>
      <a href="{{ route('admin.financial.transactions') }}" class="nb-mega-link"><i
          class="fas fa-exchange-alt"></i><span class="nb-mega-link-text"><span class="nb-mega-link-title">All
            Transactions</span></span></a>
      <a href="{{ route('admin.financial.invoices') }}" class="nb-mega-link"><i
          class="fas fa-file-invoice-dollar"></i><span class="nb-mega-link-text"><span
            class="nb-mega-link-title">Invoices</span></span></a>
    </div>
    <div class="nb-mega-section">
      <div class="nb-mega-section-title">Reports</div>
      <a href="{{ route('admin.reports.financial.index') }}" class="nb-mega-link"><i
          class="fas fa-file-invoice"></i><span class="nb-mega-link-text"><span class="nb-mega-link-title">Financial
            Reports</span></span></a>
      <a href="{{ route('admin.reports.property.performance') }}" class="nb-mega-link"><i
          class="fas fa-chart-bar"></i><span class="nb-mega-link-text"><span class="nb-mega-link-title">Property
            Performance</span></span></a>
      <a href="{{ route('admin.reports.agent.performance') }}" class="nb-mega-link"><i
          class="fas fa-user-check"></i><span class="nb-mega-link-text"><span class="nb-mega-link-title">Agent
            Performance</span></span></a>
    </div>
    <div class="nb-mega-section">
      <div class="nb-mega-section-title">System</div>
      <a href="{{ route('admin.system.dashboard') }}" class="nb-mega-link"><i class="fas fa-server"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">System Dashboard</span></span></a>
      <a href="{{ route('admin.system.logs') }}" class="nb-mega-link"><i class="fas fa-bug"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Logs</span></span></a>
      <a href="{{ route('admin.system.backup') }}" class="nb-mega-link"><i class="fas fa-database"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Backups</span></span></a>
    </div>
    <div class="nb-mega-section">
      <div class="nb-mega-section-title">Configuration</div>
      <a href="{{ route('admin.seo.index') }}" class="nb-mega-link"><i class="fas fa-search"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">SEO Manager</span></span></a>
      <a href="{{ route('admin.settings.index') }}" class="nb-mega-link"><i class="fas fa-cogs"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Settings</span></span></a>
    </div>
  </div>
</div>

{{-- CONTENT MEGA --}}
<div class="nb-mega cols-3" id="nb-mega-content">
  <div class="nb-mega-inner">
    <div class="nb-mega-featured">
      <div class="nb-mega-featured-img">
        <i class="fas fa-file-alt"></i>
        <span>Content Management</span>
      </div>
      <div class="nb-mega-featured-title">CMS Dashboard</div>
      <div class="nb-mega-featured-desc">Manage blog posts, media, pages and all published content across the platform.
      </div>
      <a href="{{ route('admin.content.dashboard') }}" class="nb-mega-link"
        style="padding:10px;background:#eff6ff;border-radius:8px;margin-top:8px">
        <i class="fas fa-tachometer-alt" style="background:#dbeafe"></i>
        <span class="nb-mega-link-text"><span class="nb-mega-link-title">Go to Content Dashboard</span></span>
        <i class="fas fa-arrow-right"
          style="font-size:11px;color:var(--nb-blue);background:transparent;width:auto;height:auto"></i>
      </a>
    </div>
    <div class="nb-mega-section">
      <div class="nb-mega-section-title">Publishing</div>
      <a href="{{ route('admin.content.blog.posts.index') }}" class="nb-mega-link"><i class="fas fa-blog"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Blog Posts</span><span
            class="nb-mega-link-sub">Articles & news</span></span></a>
      <a href="{{ route('admin.content.media') }}" class="nb-mega-link"><i class="fas fa-images"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Media Library</span><span
            class="nb-mega-link-sub">Images & files</span></span></a>
    </div>
    <div class="nb-mega-section">
      <div class="nb-mega-section-title">Marketing</div>
      <a href="{{ route('ads.campaigns.index') }}" class="nb-mega-link"><i class="fas fa-bullhorn"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Ad Campaigns</span></span></a>
      <a href="{{ route('admin.seo.index') }}" class="nb-mega-link"><i class="fas fa-search"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">SEO Manager</span></span></a>
    </div>
  </div>
</div>

{{-- AGENT MEGA --}}
<div class="nb-mega cols-3" id="nb-mega-agent">
  <div class="nb-mega-inner">
    <div class="nb-mega-featured">
      <div class="nb-mega-featured-img" style="background:linear-gradient(135deg,#0f172a,#1d4ed8,#4f46e5)">
        <i class="fas fa-briefcase"></i>
        <span>Agent Workspace</span>
      </div>
      <div class="nb-mega-featured-title">Your Dashboard</div>
      <div class="nb-mega-featured-desc">Manage properties, track leads, schedule appointments and grow your business.
      </div>
      <a href="{{ route('agent.dashboard') }}" class="nb-mega-link"
        style="padding:10px;background:#eff6ff;border-radius:8px;margin-top:8px">
        <i class="fas fa-tachometer-alt" style="background:#dbeafe"></i>
        <span class="nb-mega-link-text"><span class="nb-mega-link-title">Open Agent Dashboard</span></span>
        <i class="fas fa-arrow-right"
          style="font-size:11px;color:var(--nb-blue);background:transparent;width:auto;height:auto"></i>
      </a>
    </div>
    <div class="nb-mega-section">
      <div class="nb-mega-section-title">Properties</div>
      <a href="{{ route('agent.properties.index') }}" class="nb-mega-link"><i class="fas fa-home"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">My Properties</span><span
            class="nb-mega-link-sub">All your listings</span></span></a>
      <a href="{{ route('agent.properties.create') }}" class="nb-mega-link"><i class="fas fa-plus"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Add Property</span></span><span
          class="nb-mega-link-badge new">+New</span></a>
    </div>
    <div class="nb-mega-section">
      <div class="nb-mega-section-title">Business</div>
      <a href="{{ route('agent.crm.index') }}" class="nb-mega-link"><i class="fas fa-users"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Leads CRM</span><span
            class="nb-mega-link-sub">Manage clients</span></span></a>
      <a href="{{ route('agent.appointments.index') }}" class="nb-mega-link"><i class="fas fa-calendar"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Appointments</span></span></a>
      <a href="{{ route('agent.appointments.calendar') }}" class="nb-mega-link"><i class="fas fa-calendar-alt"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Calendar View</span></span></a>
      <a href="{{ route('ads.campaigns.index') }}" class="nb-mega-link"><i class="fas fa-bullhorn"></i><span
          class="nb-mega-link-text"><span class="nb-mega-link-title">Ad Campaigns</span></span></a>
    </div>
  </div>
</div>

{{-- ════ MOBILE DRAWER ════ --}}
<div id="nb-mobile">
  <div class="nb-mob-inner">
    <div class="nb-mob-search">
      <i class="fas fa-search"></i>
      <input type="text" placeholder="Search…">
    </div>

    <a href="/" class="nb-mob-link"><i class="fas fa-home"></i> Home</a>
    @auth
      <a href="{{ route('properties.index') }}" class="nb-mob-link"><i class="fas fa-building"></i> Properties</a>
      <a href="{{ route('agents.directory') }}" class="nb-mob-link"><i class="fas fa-user-tie"></i> Agents</a>
      <a href="{{ route('dashboard') }}" class="nb-mob-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>

      {{-- Admin --}}
      @if(Auth::user()->is_admin || Auth::user()->user_type === 'admin')
        <div class="nb-mob-sep"></div>
        <div class="nb-mob-section">
          <div class="nb-mob-section-head" onclick="nbMobToggle(this)">
            <span class="nb-mob-section-label"><span class="nb-mob-icon"><i class="fas fa-shield-alt"></i></span> Admin
              Panel</span>
            <i class="fas fa-chevron-right nb-mob-section-chevron"></i>
          </div>
          <div class="nb-mob-links">
            <a href="{{ route('admin.dashboard') }}" class="nb-mob-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('admin.users.index') }}" class="nb-mob-link"><i class="fas fa-users"></i> Users</a>
            <a href="{{ route('admin.agents.index') }}" class="nb-mob-link"><i class="fas fa-user-tie"></i> Agents</a>
            <a href="{{ route('admin.properties.index') }}" class="nb-mob-link"><i class="fas fa-building"></i>
              Properties</a>
            <a href="{{ route('admin.companies.index') }}" class="nb-mob-link"><i class="fas fa-briefcase"></i>
              Companies</a>
            <a href="{{ route('admin.financial.dashboard') }}" class="nb-mob-link"><i class="fas fa-chart-line"></i>
              Finance</a>
            <a href="{{ route('admin.settings.index') }}" class="nb-mob-link"><i class="fas fa-cogs"></i> Settings</a>
          </div>
        </div>
      @endif

      {{-- Agent --}}
      @if(Auth::user()->is_agent || Auth::user()->user_type === 'agent')
        <div class="nb-mob-sep"></div>
        <div class="nb-mob-section">
          <div class="nb-mob-section-head" onclick="nbMobToggle(this)">
            <span class="nb-mob-section-label"><span class="nb-mob-icon"><i class="fas fa-briefcase"></i></span> Agent
              Panel</span>
            <i class="fas fa-chevron-right nb-mob-section-chevron"></i>
          </div>
          <div class="nb-mob-links">
            <a href="{{ route('agent.dashboard') }}" class="nb-mob-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('agent.properties.index') }}" class="nb-mob-link"><i class="fas fa-home"></i> My
              Properties</a>
            <a href="{{ route('agent.properties.create') }}" class="nb-mob-link"><i class="fas fa-plus"></i> Add
              Property</a>
            <a href="{{ route('agent.crm.index') }}" class="nb-mob-link"><i class="fas fa-users"></i> Leads CRM</a>
            <a href="{{ route('agent.appointments.index') }}" class="nb-mob-link"><i class="fas fa-calendar"></i>
              Appointments</a>
          </div>
        </div>
      @endif

      <div class="nb-mob-sep"></div>
      <a href="/wallet" class="nb-mob-link"><i class="fas fa-wallet"></i> My Wallet</a>
      <a href="{{ route('properties.favorites') }}" class="nb-mob-link"><i class="fas fa-heart"></i> Favourites</a>
      <a href="/cart" class="nb-mob-link"><i class="fas fa-shopping-cart"></i> Cart</a>
      <a href="{{ route('user.notifications') }}" class="nb-mob-link"><i class="fas fa-bell"></i> Notifications</a>
      <a href="{{ route('settings.index') }}" class="nb-mob-link"><i class="fas fa-cog"></i> Settings</a>
      <div class="nb-mob-sep"></div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="nb-mob-link"
          style="width:100%;background:none;border:none;cursor:pointer;color:#dc2626;font-family:inherit">
          <i class="fas fa-sign-out-alt" style="color:#dc2626"></i> Logout
        </button>
      </form>
    @else
      <div class="nb-mob-sep"></div>
      <a href="{{ url('login') }}" class="nb-mob-link"><i class="fas fa-sign-in-alt"></i> Login</a>
      <a href="{{ url('register') }}" class="nb-mob-link" style="color:var(--nb-blue);font-weight:700"><i
          class="fas fa-user-plus"></i> Register Free</a>
    @endauth
  </div>
</div>

<script>
  // ── State ──
  let nbOpenMega = null;
  let nbOpenPanel = null;

  // ── Mega Menu ──
  function nbMega(key, btn) {
    const panel = document.getElementById('nb-mega-' + key);
    const overlay = document.getElementById('nb-overlay');
    const allMegas = document.querySelectorAll('.nb-mega');
    const allTabs = document.querySelectorAll('.nb-tab');

    if (nbOpenMega === key) {
      nbCloseMega();
      return;
    }

    allMegas.forEach(m => { m.classList.remove('open'); });
    allTabs.forEach(t => t.classList.remove('mega-open'));

    panel.classList.add('open');
    overlay.classList.add('open');
    btn.classList.add('mega-open');
    nbOpenMega = key;
    nbClosePanel();
  }

  function nbCloseMega() {
    document.querySelectorAll('.nb-mega').forEach(m => m.classList.remove('open'));
    document.querySelectorAll('.nb-tab').forEach(t => t.classList.remove('mega-open'));
    document.getElementById('nb-overlay').classList.remove('open');
    nbOpenMega = null;
  }

  // ── Dropdowns (notif, user) ──
  function nbToggle(key) {
    const panel = document.getElementById('nb-' + key + '-panel');
    const wasOpen = panel.classList.contains('open');
    nbClosePanel();
    nbCloseMega();
    if (!wasOpen) {
      panel.classList.add('open');
      nbOpenPanel = key;
    }
  }

  function nbClosePanel() {
    document.querySelectorAll('.nb-dropdown').forEach(d => d.classList.remove('open'));
    nbOpenPanel = null;
  }

  // Close on outside click
  document.addEventListener('click', function (e) {
    if (!e.target.closest('#nb-notif-btn') && !e.target.closest('#nb-notif-panel') &&
      !e.target.closest('#nb-user-btn') && !e.target.closest('#nb-user-panel')) {
      nbClosePanel();
    }
  });

  // ── Mobile ──
  let nbMobOpen = false;
  function nbToggleMobile() {
    nbMobOpen = !nbMobOpen;
    document.getElementById('nb-mobile').classList.toggle('open', nbMobOpen);
    document.getElementById('nb-mob-icon').className = nbMobOpen ? 'fas fa-times' : 'fas fa-bars';
  }

  function nbMobToggle(head) {
    const links = head.nextElementSibling;
    const open = links.classList.contains('open');
    links.classList.toggle('open', !open);
    head.classList.toggle('open', !open);
  }

  // ── Notification read ──
  function nbMarkRead(id) {
    fetch('/notifications/' + id + '/read', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' } });
  }

  // ── Active tab indicator ──
  (function () {
    const indicator = document.getElementById('nb-indicator');
    const tabs = document.querySelectorAll('#nb-tabs .nb-tab[data-tab]');
    const path = window.location.pathname;

    let activeTab = null;
    tabs.forEach(tab => {
      const href = tab.getAttribute('href') || '';
      if (href && href !== '/' && path.startsWith(href)) activeTab = tab;
      if (href === '/' && path === '/') activeTab = tab;
    });

    function moveIndicator(el) {
      if (!el || !indicator) return;
      const tabsRect = document.getElementById('nb-tabs').getBoundingClientRect();
      const elRect = el.getBoundingClientRect();
      indicator.style.left = (elRect.left - tabsRect.left) + 'px';
      indicator.style.width = elRect.width + 'px';
    }

    if (activeTab) {
      activeTab.classList.add('active');
      requestAnimationFrame(() => moveIndicator(activeTab));
    }

    tabs.forEach(tab => {
      tab.addEventListener('mouseenter', () => moveIndicator(tab));
      tab.addEventListener('mouseleave', () => moveIndicator(activeTab));
    });
  })();

  // ── Keyboard shortcut ⌘K ──
  document.addEventListener('keydown', function (e) {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
      e.preventDefault();
      document.getElementById('nbMainSearch').focus();
    }
    if (e.key === 'Escape') { nbCloseMega(); nbClosePanel(); }
  });

  // ── Real-time notifications (Echo) ──
  if (typeof Echo !== 'undefined') {
    Echo.private('App.Models.User.{{ Auth::id() ?? 0 }}')
      .notification((n) => {
        const badge = document.querySelector('#nb-notif-btn .nb-badge');
        if (badge) {
          let c = parseInt(badge.innerText) || 0;
          badge.style.display = 'flex';
          badge.innerText = (c + 1) > 99 ? '99+' : (c + 1);
        }
        const list = document.getElementById('nb-notif-list');
        if (list) {
          const item = document.createElement('div');
          item.className = 'nb-notif-item unread';
          item.innerHTML = `<div class="nb-notif-icon ni-blue"><i class="fas fa-bell"></i></div><div class="nb-notif-body"><div class="nb-notif-title">${n.title || 'New Notification'}</div><div class="nb-notif-time">Just now</div></div>`;
          list.insertBefore(item, list.firstChild);
        }
      });
  }
</script>