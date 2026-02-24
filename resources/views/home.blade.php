@extends('layouts.app')

@section('title', __('Home') . ' | ' . config('app.name', 'Real Estate Pro'))

@section('content')
  {{--
  ╔══════════════════════════════════════════════════════════════════
  ║ HOMEPAGE — "EDITORIAL MIDNIGHT AMBER"
  ║ Concept: فاخر، داكن، أنيق — مثل مجلة Architectural Digest
  ║ Palette: Midnight Black + Warm Amber + Ivory Cream + Copper
  ║ Typography: Cormorant Garamond (serif display) + Syne (body)
  ╚══════════════════════════════════════════════════════════════════
  --}}

  {{-- ════════════ INLINE STYLES ════════════ --}}
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,600&family=Syne:wght@400;500;600;700;800&display=swap');

    /* ── TOKENS ── */
    :root {
      --am: #1a0e00;
      /* Abyss midnight */
      --am2: #110b00;
      --am3: #0a0600;
      --ab: #e8a832;
      /* Amber bright */
      --ag: #c98f1e;
      /* Amber gold */
      --ad: #8a5e08;
      /* Amber dark */
      --ap: rgba(232, 168, 50, .12);
      /* Amber pale tint */
      --iv: #faf6ee;
      /* Ivory */
      --iv2: #f0ead8;
      --co: #b5763d;
      /* Copper */
      --co2: #d4925a;
      --ink: #0f0a02;
      --ink2: #2d2010;
      --ink3: #7a6040;
      --ink4: #b89870;
      --white: #ffffff;
      --ff-h: 'Cormorant Garamond', Georgia, serif;
      --ff-b: 'Syne', 'Segoe UI', system-ui, sans-serif;
      --ease: cubic-bezier(.4, 0, .2, 1);
      --ease-spring: cubic-bezier(.34, 1.56, .64, 1);
    }

    /* ── RESET ── */
    *,
    *::before,
    *::after {
      box-sizing: border-box
    }

    .hp-body {
      font-family: var(--ff-b);
      color: var(--ink2);
      background: var(--iv);
      overflow-x: hidden
    }

    /* ────────────────────────────────────────────────
     SECTION 1: HERO
  ──────────────────────────────────────────────── */
    .hp-hero {
      position: relative;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      background: var(--am3);
      overflow: hidden;
    }

    /* Split background: image right, dark left */
    .hp-hero-bg {
      position: absolute;
      inset: 0;
      display: grid;
      grid-template-columns: 1fr 1fr;
    }

    @media(max-width:900px) {
      .hp-hero-bg {
        grid-template-columns: 1fr
      }
    }

    .hp-hero-bg-left {
      background: linear-gradient(135deg, var(--am3) 0%, var(--am2) 50%, var(--am) 100%);
      position: relative;
      z-index: 1;
    }

    .hp-hero-bg-left::after {
      content: '';
      position: absolute;
      inset: 0;
      background: repeating-linear-gradient(60deg, transparent, transparent 55px,
          rgba(232, 168, 50, .025) 55px, rgba(232, 168, 50, .025) 56px);
    }

    .hp-hero-bg-right {
      position: relative;
      overflow: hidden;
    }

    .hp-hero-bg-right img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      opacity: .45;
      transform: scale(1.08);
      animation: hp-img-breathe 20s ease-in-out infinite;
    }

    @keyframes hp-img-breathe {

      0%,
      100% {
        transform: scale(1.08)
      }

      50% {
        transform: scale(1.14)
      }
    }

    .hp-hero-bg-right::before {
      content: '';
      position: absolute;
      inset: 0;
      z-index: 1;
      background: linear-gradient(90deg, var(--am3) 0%, transparent 40%),
        linear-gradient(to top, var(--am3) 0%, transparent 40%);
    }

    .hp-hero-bg-right::after {
      content: '';
      position: absolute;
      inset: 0;
      z-index: 2;
      background: linear-gradient(135deg, rgba(232, 168, 50, .1), rgba(181, 118, 61, .05));
    }

    /* Floating orbs */
    .hp-orb {
      position: absolute;
      border-radius: 50%;
      pointer-events: none;
      filter: blur(80px);
    }

    .hp-orb-1 {
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(232, 168, 50, .15), transparent 70%);
      top: -150px;
      right: 15%;
      animation: hp-orb-float 12s ease-in-out infinite;
    }

    .hp-orb-2 {
      width: 400px;
      height: 400px;
      background: radial-gradient(circle, rgba(181, 118, 61, .1), transparent 70%);
      bottom: -100px;
      left: 5%;
      animation: hp-orb-float 16s ease-in-out infinite reverse;
    }

    @keyframes hp-orb-float {

      0%,
      100% {
        transform: translateY(0) scale(1)
      }

      50% {
        transform: translateY(-30px) scale(1.05)
      }
    }

    /* Hero inner */
    .hp-hero-inner {
      position: relative;
      z-index: 10;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 120px 6vw 80px;
      max-width: 1400px;
      margin: 0 auto;
      width: 100%;
    }

    /* Eyebrow */
    .hp-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: rgba(232, 168, 50, .1);
      border: 1px solid rgba(232, 168, 50, .2);
      backdrop-filter: blur(8px);
      padding: 8px 20px;
      border-radius: 30px;
      margin-bottom: 40px;
      align-self: flex-start;
    }

    .hp-eyebrow-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: var(--ab);
      box-shadow: 0 0 0 0 rgba(232, 168, 50, .4);
      animation: hp-dot-ping 2s ease-in-out infinite;
    }

    @keyframes hp-dot-ping {

      0%,
      100% {
        box-shadow: 0 0 0 0 rgba(232, 168, 50, .4)
      }

      60% {
        box-shadow: 0 0 0 6px rgba(232, 168, 50, 0)
      }
    }

    .hp-eyebrow span {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: var(--ab);
    }

    /* Headline */
    .hp-h1 {
      font-family: var(--ff-h);
      font-size: clamp(52px, 7vw, 110px);
      line-height: 1.0;
      font-weight: 300;
      color: var(--white);
      margin-bottom: 32px;
      opacity: 0;
      animation: hp-fade-up .9s var(--ease) .1s forwards;
    }

    .hp-h1 em {
      font-style: italic;
      font-weight: 300;
      background: linear-gradient(120deg, var(--ab) 20%, var(--co2) 80%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hp-h1-line2 {
      display: block;
      font-size: .55em;
      font-weight: 600;
      letter-spacing: .04em;
      color: rgba(255, 255, 255, .55);
      font-family: var(--ff-b);
      font-style: normal;
      -webkit-text-fill-color: rgba(255, 255, 255, .55);
      background: none;
    }

    @keyframes hp-fade-up {
      from {
        opacity: 0;
        transform: translateY(30px)
      }

      to {
        opacity: 1;
        transform: translateY(0)
      }
    }

    @keyframes hp-fade-down {
      from {
        opacity: 0;
        transform: translateY(-20px)
      }

      to {
        opacity: 1;
        transform: translateY(0)
      }
    }

    @keyframes hp-fade-in {
      from {
        opacity: 0
      }

      to {
        opacity: 1
      }
    }

    .hp-hero-desc {
      font-size: clamp(15px, 1.2vw, 18px);
      color: rgba(255, 255, 255, .45);
      max-width: 520px;
      line-height: 1.8;
      margin-bottom: 56px;
      font-weight: 400;
      opacity: 0;
      animation: hp-fade-up .9s var(--ease) .25s forwards;
    }

    /* Search box */
    .hp-search-box {
      background: rgba(255, 255, 255, .04);
      border: 1px solid rgba(232, 168, 50, .18);
      backdrop-filter: blur(20px);
      border-radius: 24px;
      padding: 6px 6px 6px 6px;
      max-width: 820px;
      opacity: 0;
      animation: hp-fade-up .9s var(--ease) .4s forwards;
      box-shadow: 0 24px 60px rgba(0, 0, 0, .4), inset 0 1px 0 rgba(255, 255, 255, .04);
    }

    /* Tabs */
    .hp-tabs {
      display: flex;
      gap: 4px;
      padding: 8px 12px;
      margin-bottom: 4px;
    }

    .hp-tab {
      padding: 9px 22px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      border: none;
      cursor: pointer;
      font-family: var(--ff-b);
      transition: all .25s var(--ease);
      color: rgba(255, 255, 255, .4);
      background: transparent;
    }

    .hp-tab:hover {
      color: rgba(255, 255, 255, .8);
      background: rgba(255, 255, 255, .05)
    }

    .hp-tab.hp-tab-active {
      background: linear-gradient(135deg, var(--ag), var(--ab));
      color: var(--am3);
      box-shadow: 0 4px 16px rgba(232, 168, 50, .3);
    }

    /* Search fields */
    .hp-search-fields {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr auto;
      gap: 6px;
      padding: 0 6px 6px;
    }

    @media(max-width:768px) {
      .hp-search-fields {
        grid-template-columns: 1fr 1fr;
      }

      .hp-search-btn {
        grid-column: 1/-1
      }
    }

    @media(max-width:480px) {
      .hp-search-fields {
        grid-template-columns: 1fr
      }
    }

    .hp-field {
      position: relative;
    }

    .hp-field-icon {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--ab);
      font-size: 14px;
      opacity: .7;
      pointer-events: none;
    }

    .hp-field input,
    .hp-field select {
      width: 100%;
      background: rgba(255, 255, 255, .06);
      border: 1px solid rgba(255, 255, 255, .08);
      border-radius: 14px;
      padding: 16px 16px 16px 48px;
      font-family: var(--ff-b);
      font-size: 13px;
      color: var(--white);
      outline: none;
      transition: border-color .2s, background .2s, box-shadow .2s;
      caret-color: var(--ab);
      -webkit-appearance: none;
    }

    .hp-field input::placeholder {
      color: rgba(255, 255, 255, .3)
    }

    .hp-field input:focus,
    .hp-field select:focus {
      border-color: rgba(232, 168, 50, .4);
      background: rgba(255, 255, 255, .08);
      box-shadow: 0 0 0 3px rgba(232, 168, 50, .1);
    }

    .hp-field select option {
      background: var(--am);
      color: var(--white)
    }

    .hp-search-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 16px 32px;
      background: linear-gradient(135deg, var(--ag), var(--ab));
      border: none;
      border-radius: 14px;
      font-family: var(--ff-b);
      font-size: 13px;
      font-weight: 700;
      letter-spacing: .06em;
      text-transform: uppercase;
      color: var(--am3);
      cursor: pointer;
      white-space: nowrap;
      transition: all .22s var(--ease);
      box-shadow: 0 6px 20px rgba(232, 168, 50, .3);
    }

    .hp-search-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 32px rgba(232, 168, 50, .45);
      background: linear-gradient(135deg, var(--ab), var(--co2));
    }

    /* Quick links */
    .hp-quick-links {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 40px;
      opacity: 0;
      animation: hp-fade-up .9s var(--ease) .55s forwards;
    }

    .hp-quick {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      color: rgba(255, 255, 255, .5);
      font-size: 12.5px;
      font-weight: 600;
      letter-spacing: .04em;
      transition: color .2s;
      padding: 4px 0;
    }

    .hp-quick:hover {
      color: var(--ab)
    }

    .hp-quick-ic {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background: rgba(255, 255, 255, .05);
      border: 1px solid rgba(255, 255, 255, .08);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      color: var(--ab);
      transition: all .22s var(--ease);
    }

    .hp-quick:hover .hp-quick-ic {
      background: var(--ap);
      border-color: rgba(232, 168, 50, .3);
      transform: translateY(-2px);
    }

    /* Scroll indicator */
    .hp-scroll-ind {
      position: absolute;
      bottom: 40px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 10;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      opacity: 0;
      animation: hp-fade-in 1s var(--ease) 1.2s forwards;
    }

    .hp-scroll-ind span {
      font-size: 9px;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: rgba(255, 255, 255, .3);
      font-weight: 700;
    }

    .hp-scroll-track {
      width: 1px;
      height: 50px;
      background: rgba(255, 255, 255, .1);
      border-radius: 1px;
      overflow: hidden;
      position: relative;
    }

    .hp-scroll-line {
      position: absolute;
      top: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(to bottom, transparent, var(--ab), transparent);
      animation: hp-scroll-slide 2s ease-in-out infinite;
    }

    @keyframes hp-scroll-slide {
      0% {
        top: -100%
      }

      100% {
        top: 100%
      }
    }

    /* ────────────────────────────────────────────────
     SECTION 2: STATS
  ──────────────────────────────────────────────── */
    .hp-stats {
      background: var(--am2);
      padding: 0;
      position: relative;
      overflow: hidden;
    }

    .hp-stats::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, var(--ag), transparent);
    }

    .hp-stats::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(232, 168, 50, .3), transparent);
    }

    .hp-stats-inner {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 6vw;
      display: grid;
      grid-template-columns: repeat(4, 1fr);
    }

    @media(max-width:768px) {
      .hp-stats-inner {
        grid-template-columns: 1fr 1fr
      }
    }

    .hp-stat {
      padding: 52px 32px;
      text-align: center;
      position: relative;
      transition: background .3s;
    }

    .hp-stat+.hp-stat::before {
      content: '';
      position: absolute;
      left: 0;
      top: 20%;
      bottom: 20%;
      width: 1px;
      background: linear-gradient(to bottom, transparent, rgba(232, 168, 50, .2), transparent);
    }

    .hp-stat:hover {
      background: rgba(232, 168, 50, .04)
    }

    .hp-stat-icon {
      width: 56px;
      height: 56px;
      background: var(--ap);
      border: 1px solid rgba(232, 168, 50, .2);
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      color: var(--ab);
      margin: 0 auto 20px;
      transition: transform .3s var(--ease-spring), box-shadow .3s;
    }

    .hp-stat:hover .hp-stat-icon {
      transform: rotate(8deg) scale(1.1);
      box-shadow: 0 8px 24px rgba(232, 168, 50, .3);
    }

    .hp-stat-num {
      font-family: var(--ff-h);
      font-size: clamp(38px, 4vw, 58px);
      font-weight: 600;
      color: var(--white);
      line-height: 1;
      margin-bottom: 8px;
    }

    .hp-stat-num .hp-stat-plus {
      font-size: .5em;
      color: var(--ab);
      font-family: var(--ff-b);
      font-weight: 700;
    }

    .hp-stat-lbl {
      font-size: 10.5px;
      font-weight: 700;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: rgba(255, 255, 255, .3);
    }

    /* ────────────────────────────────────────────────
     SECTION 3: FEATURED PROPERTIES
  ──────────────────────────────────────────────── */
    .hp-featured {
      background: var(--iv);
      padding: 120px 0;
      position: relative;
      overflow: hidden;
    }

    .hp-featured::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 40%;
      height: 100%;
      background: linear-gradient(to left, rgba(232, 168, 50, .04), transparent);
      pointer-events: none;
    }

    .hp-sec-inner {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 6vw
    }

    /* Section header */
    .hp-sec-head {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      margin-bottom: 72px;
      gap: 24px;
      flex-wrap: wrap;
    }

    .hp-sec-label {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: var(--ag);
      margin-bottom: 16px;
    }

    .hp-sec-label::before {
      content: '';
      display: block;
      width: 24px;
      height: 1.5px;
      background: var(--ag);
    }

    .hp-sec-h2 {
      font-family: var(--ff-h);
      font-size: clamp(36px, 4vw, 60px);
      font-weight: 400;
      color: var(--ink);
      line-height: 1.1;
      margin-bottom: 16px;
    }

    .hp-sec-h2 em {
      font-style: italic;
      color: var(--ag);
    }

    .hp-sec-p {
      font-size: 15px;
      color: var(--ink3);
      max-width: 440px;
      line-height: 1.75;
    }

    .hp-see-all {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--ink2);
      white-space: nowrap;
      padding: 14px 28px;
      border: 1.5px solid rgba(0, 0, 0, .15);
      border-radius: 50px;
      transition: all .22s var(--ease);
    }

    .hp-see-all:hover {
      background: var(--ink2);
      color: var(--white);
      border-color: var(--ink2);
      gap: 18px;
    }

    .hp-see-all i {
      font-size: 11px;
      transition: transform .22s
    }

    .hp-see-all:hover i {
      transform: translateX(3px)
    }

    /* Properties grid — asymmetric editorial layout */
    .hp-props-grid {
      display: grid;
      grid-template-columns: 1.3fr 1fr 1fr;
      grid-template-rows: auto auto;
      gap: 24px;
    }

    @media(max-width:1024px) {
      .hp-props-grid {
        grid-template-columns: 1fr 1fr
      }
    }

    @media(max-width:640px) {
      .hp-props-grid {
        grid-template-columns: 1fr
      }
    }

    /* Property card */
    .hp-pcard {
      background: var(--white);
      border-radius: 24px;
      overflow: hidden;
      border: 1px solid rgba(0, 0, 0, .06);
      transition: all .3s var(--ease);
      position: relative;
    }

    .hp-pcard:hover {
      transform: translateY(-6px);
      box-shadow: 0 24px 60px rgba(26, 14, 0, .12);
      border-color: rgba(201, 143, 30, .25);
    }

    /* Big card (first) */
    .hp-pcard-big {
      grid-row: span 2;
    }

    .hp-pcard-big .hp-pcard-img {
      height: 480px
    }

    @media(max-width:1024px) {
      .hp-pcard-big {
        grid-row: auto
      }

      .hp-pcard-big .hp-pcard-img {
        height: 280px
      }
    }

    .hp-pcard-img {
      height: 240px;
      overflow: hidden;
      position: relative;
    }

    .hp-pcard-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform .6s var(--ease);
    }

    .hp-pcard:hover .hp-pcard-img img {
      transform: scale(1.05)
    }

    .hp-pcard-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(to top, rgba(26, 14, 0, .55) 0%, rgba(26, 14, 0, .05) 55%, transparent 100%);
      transition: opacity .3s;
    }

    .hp-pcard:hover .hp-pcard-overlay {
      opacity: .8
    }

    /* Card badges */
    .hp-pcard-badges {
      position: absolute;
      top: 14px;
      left: 14px;
      display: flex;
      gap: 6px;
      z-index: 2;
    }

    .hp-badge {
      font-size: 9.5px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      padding: 5px 12px;
      border-radius: 20px;
      backdrop-filter: blur(8px);
    }

    .hb-status {
      background: rgba(232, 168, 50, .9);
      color: var(--am3)
    }

    .hb-type {
      background: rgba(15, 10, 2, .6);
      color: rgba(255, 255, 255, .9);
      border: 1px solid rgba(255, 255, 255, .1)
    }

    .hb-premium {
      background: rgba(181, 118, 61, .9);
      color: var(--white)
    }

    /* Wishlist */
    .hp-pcard-wish {
      position: absolute;
      top: 14px;
      right: 14px;
      z-index: 2;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .15);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, .25);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: rgba(255, 255, 255, .8);
      font-size: 13px;
      transition: all .22s var(--ease);
    }

    .hp-pcard-wish:hover {
      background: var(--white);
      color: #e74c3c;
      transform: scale(1.1)
    }

    /* Price overlay */
    .hp-pcard-price {
      position: absolute;
      bottom: 16px;
      left: 14px;
      z-index: 2;
      display: flex;
      flex-direction: column;
    }

    .hp-price-val {
      font-family: var(--ff-h);
      font-size: clamp(22px, 2vw, 28px);
      font-weight: 600;
      color: var(--white);
      line-height: 1;
    }

    .hp-price-cur {
      font-size: 11px;
      color: rgba(255, 255, 255, .6);
      margin-top: 2px
    }

    /* Card body */
    .hp-pcard-body {
      padding: 20px 22px 22px
    }

    .hp-pcard-title {
      font-family: var(--ff-h);
      font-size: clamp(18px, 1.5vw, 22px);
      font-weight: 400;
      color: var(--ink);
      margin-bottom: 8px;
      line-height: 1.25;
    }

    .hp-pcard-title a {
      text-decoration: none;
      color: inherit;
      transition: color .18s
    }

    .hp-pcard:hover .hp-pcard-title a {
      color: var(--ag)
    }

    .hp-pcard-loc {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 12px;
      color: var(--ink3);
      margin-bottom: 16px;
    }

    .hp-pcard-loc i {
      color: var(--ag);
      font-size: 10px
    }

    .hp-pcard-meta {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 0;
      border-top: 1px solid rgba(0, 0, 0, .06);
      padding-top: 14px;
    }

    .hp-meta-item {
      text-align: center;
      padding: 4px 0
    }

    .hp-meta-item+.hp-meta-item {
      border-left: 1px solid rgba(0, 0, 0, .06)
    }

    .hp-meta-val {
      font-size: 15px;
      font-weight: 700;
      color: var(--ink);
      display: block;
      line-height: 1.2
    }

    .hp-meta-lbl {
      font-size: 9.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: var(--ink4);
      display: block;
      margin-top: 2px
    }

    .hp-pcard-foot {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 16px;
      padding-top: 14px;
      border-top: 1px solid rgba(0, 0, 0, .06);
      gap: 10px;
    }

    .hp-pcard-cta {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 10px 20px;
      background: var(--ink2);
      color: var(--white);
      border-radius: 10px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .06em;
      text-transform: uppercase;
      text-decoration: none;
      transition: all .2s var(--ease);
      flex: 1;
    }

    .hp-pcard-cta:hover {
      background: var(--ag);
      transform: translateY(-1px)
    }

    .hp-pcard-share {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      border: 1.5px solid rgba(0, 0, 0, .1);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--ink3);
      font-size: 13px;
      cursor: pointer;
      transition: all .18s;
      background: transparent;
      flex-shrink: 0;
    }

    .hp-pcard-share:hover {
      border-color: var(--ag);
      color: var(--ag)
    }

    /* Empty state */
    .hp-props-empty {
      grid-column: 1/-1;
      text-align: center;
      padding: 80px 40px;
      border: 2px dashed rgba(0, 0, 0, .08);
      border-radius: 24px;
      background: rgba(0, 0, 0, .02);
    }

    .hp-props-empty-icon {
      width: 80px;
      height: 80px;
      background: var(--iv2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 32px;
      color: var(--ink4);
    }

    .hp-props-empty h3 {
      font-size: 18px;
      font-weight: 700;
      color: var(--ink);
      margin-bottom: 8px
    }

    .hp-props-empty p {
      font-size: 14px;
      color: var(--ink3)
    }

    /* ────────────────────────────────────────────────
     SECTION 4: CATEGORIES
  ──────────────────────────────────────────────── */
    .hp-cats {
      background: var(--am3);
      padding: 120px 0;
      position: relative;
      overflow: hidden;
    }

    /* Dot matrix bg */
    .hp-cats-dots {
      position: absolute;
      inset: 0;
      background-image: radial-gradient(rgba(232, 168, 50, .12) 1px, transparent 0);
      background-size: 36px 36px;
      pointer-events: none;
    }

    .hp-cats-glow {
      position: absolute;
      width: 800px;
      height: 800px;
      background: radial-gradient(circle, rgba(232, 168, 50, .06), transparent 70%);
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      pointer-events: none;
    }

    .hp-cats-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 16px;
      margin-top: 72px;
    }

    @media(max-width:1024px) {
      .hp-cats-grid {
        grid-template-columns: repeat(3, 1fr)
      }
    }

    @media(max-width:640px) {
      .hp-cats-grid {
        grid-template-columns: 1fr 1fr
      }
    }

    .hp-cat {
      position: relative;
      background: rgba(255, 255, 255, .03);
      border: 1px solid rgba(255, 255, 255, .07);
      border-radius: 24px;
      padding: 40px 24px 36px;
      text-align: center;
      text-decoration: none;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: all .35s var(--ease);
      overflow: hidden;
      cursor: pointer;
    }

    .hp-cat::before {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: 24px;
      background: linear-gradient(135deg, rgba(232, 168, 50, .08), rgba(181, 118, 61, .04));
      opacity: 0;
      transition: opacity .35s;
    }

    .hp-cat:hover {
      border-color: rgba(232, 168, 50, .3);
      transform: translateY(-8px);
      box-shadow: 0 20px 50px rgba(0, 0, 0, .4), 0 0 0 1px rgba(232, 168, 50, .15);
    }

    .hp-cat:hover::before {
      opacity: 1
    }

    .hp-cat-glow {
      position: absolute;
      width: 120px;
      height: 120px;
      border-radius: 50%;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0);
      transition: transform .5s var(--ease);
      pointer-events: none;
      filter: blur(30px);
    }

    .hp-cat:hover .hp-cat-glow {
      transform: translate(-50%, -50%) scale(1.5)
    }

    .hp-cat-ic-wrap {
      width: 72px;
      height: 72px;
      border-radius: 20px;
      background: rgba(255, 255, 255, .05);
      border: 1px solid rgba(255, 255, 255, .08);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 26px;
      color: var(--ab);
      margin-bottom: 20px;
      transition: all .35s var(--ease-spring);
      position: relative;
      z-index: 1;
    }

    .hp-cat:hover .hp-cat-ic-wrap {
      background: var(--ap);
      border-color: rgba(232, 168, 50, .3);
      transform: scale(1.12) rotate(6deg);
      box-shadow: 0 8px 24px rgba(232, 168, 50, .25);
    }

    .hp-cat-name {
      font-size: 14px;
      font-weight: 700;
      color: var(--white);
      margin-bottom: 8px;
      position: relative;
      z-index: 1;
      transition: color .25s;
    }

    .hp-cat:hover .hp-cat-name {
      color: var(--ab)
    }

    .hp-cat-count {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: rgba(255, 255, 255, .3);
      background: rgba(255, 255, 255, .04);
      padding: 4px 12px;
      border-radius: 20px;
      position: relative;
      z-index: 1;
      transition: all .25s;
    }

    .hp-cat:hover .hp-cat-count {
      background: var(--ap);
      color: var(--ab)
    }

    /* ────────────────────────────────────────────────
     SECTION 5: WHY US
  ──────────────────────────────────────────────── */
    .hp-why {
      background: var(--iv);
      padding: 120px 0;
      overflow: hidden;
    }

    .hp-why-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: center;
      margin-top: 0;
    }

    @media(max-width:900px) {
      .hp-why-grid {
        grid-template-columns: 1fr;
        gap: 48px
      }
    }

    .hp-why-visual {
      position: relative;
      height: 560px;
      border-radius: 32px;
      overflow: hidden;
    }

    .hp-why-visual img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform .6s var(--ease);
    }

    .hp-why-visual:hover img {
      transform: scale(1.04)
    }

    .hp-why-visual::before {
      content: '';
      position: absolute;
      inset: 0;
      z-index: 1;
      background: linear-gradient(135deg, rgba(26, 14, 0, .4), rgba(26, 14, 0, .1));
    }

    /* Floating badge on image */
    .hp-why-badge {
      position: absolute;
      z-index: 2;
      bottom: 28px;
      left: 28px;
      background: rgba(26, 14, 0, .7);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(232, 168, 50, .25);
      border-radius: 20px;
      padding: 16px 20px;
      display: flex;
      align-items: center;
      gap: 14px;
      transition: transform .3s var(--ease-spring);
    }

    .hp-why-visual:hover .hp-why-badge {
      transform: translateY(-4px)
    }

    .hp-why-badge-av {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      background: linear-gradient(135deg, var(--ag), var(--ab));
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      color: var(--am3);
      flex-shrink: 0;
    }

    .hp-why-badge-val {
      font-family: var(--ff-h);
      font-size: 26px;
      font-weight: 600;
      color: var(--white);
      line-height: 1
    }

    .hp-why-badge-lbl {
      font-size: 11px;
      color: rgba(255, 255, 255, .5);
      font-weight: 600;
      letter-spacing: .04em
    }

    /* Accent corner */
    .hp-why-visual-accent {
      position: absolute;
      top: 24px;
      right: 24px;
      z-index: 2;
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--ag), var(--ab));
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: var(--ff-h);
      font-size: 13px;
      font-weight: 600;
      color: var(--am3);
      text-align: center;
      line-height: 1.2;
      box-shadow: 0 8px 24px rgba(232, 168, 50, .4);
    }

    /* Why features */
    .hp-why-features {
      display: flex;
      flex-direction: column;
      gap: 28px;
      margin-top: 52px
    }

    .hp-why-feat {
      display: flex;
      gap: 20px;
      padding: 24px 28px;
      border-radius: 18px;
      border: 1px solid rgba(0, 0, 0, .06);
      background: var(--white);
      transition: all .25s var(--ease);
      cursor: default;
    }

    .hp-why-feat:hover {
      border-color: rgba(201, 143, 30, .2);
      box-shadow: 0 8px 30px rgba(26, 14, 0, .07);
      transform: translateX(6px);
    }

    .hp-why-feat-ic {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      flex-shrink: 0;
      transition: transform .3s var(--ease-spring);
    }

    .hp-why-feat:hover .hp-why-feat-ic {
      transform: rotate(8deg) scale(1.1)
    }

    .wfi-1 {
      background: #fff7ed;
      color: var(--ag)
    }

    .wfi-2 {
      background: #f0fdf4;
      color: #16a34a
    }

    .wfi-3 {
      background: #fef3c7;
      color: #d97706
    }

    .wfi-4 {
      background: #eff6ff;
      color: #2563eb
    }

    .hp-why-feat-body {}

    .hp-why-feat-title {
      font-size: 15px;
      font-weight: 700;
      color: var(--ink);
      margin-bottom: 5px
    }

    .hp-why-feat-desc {
      font-size: 13px;
      color: var(--ink3);
      line-height: 1.65
    }

    /* ────────────────────────────────────────────────
     SECTION 6: CTA BANNER
  ──────────────────────────────────────────────── */
    .hp-cta {
      background: var(--iv);
      padding: 0 0 120px;
    }

    .hp-cta-card {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 6vw;
    }

    .hp-cta-inner {
      position: relative;
      border-radius: 40px;
      padding: 80px;
      overflow: hidden;
      background: var(--am2);
    }

    @media(max-width:768px) {
      .hp-cta-inner {
        padding: 48px 32px
      }
    }

    .hp-cta-inner::before {
      content: '';
      position: absolute;
      inset: 0;
      background: repeating-linear-gradient(-45deg, transparent, transparent 30px,
          rgba(232, 168, 50, .018) 30px, rgba(232, 168, 50, .018) 31px);
    }

    .hp-cta-inner::after {
      content: '';
      position: absolute;
      top: -100px;
      right: -100px;
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(232, 168, 50, .12), transparent 70%);
      border-radius: 50%;
      pointer-events: none;
    }

    .hp-cta-content {
      position: relative;
      z-index: 1;
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 60px;
      align-items: center;
    }

    @media(max-width:768px) {
      .hp-cta-content {
        grid-template-columns: 1fr
      }
    }

    .hp-cta-tag {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: var(--ab);
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .hp-cta-tag::before {
      content: '';
      display: block;
      width: 20px;
      height: 1.5px;
      background: var(--ab)
    }

    .hp-cta-h2 {
      font-family: var(--ff-h);
      font-size: clamp(34px, 3.5vw, 56px);
      font-weight: 400;
      color: var(--white);
      line-height: 1.1;
      margin-bottom: 20px;
    }

    .hp-cta-h2 em {
      font-style: italic;
      color: var(--ab)
    }

    .hp-cta-p {
      font-size: 15px;
      color: rgba(255, 255, 255, .45);
      line-height: 1.75;
      max-width: 480px
    }

    .hp-cta-actions {
      display: flex;
      flex-direction: column;
      gap: 16px;
      align-items: center;
      flex-shrink: 0;
    }

    @media(max-width:768px) {
      .hp-cta-actions {
        flex-direction: row;
        flex-wrap: wrap
      }
    }

    .hp-cta-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 16px 40px;
      border-radius: 14px;
      font-family: var(--ff-b);
      font-size: 13px;
      font-weight: 700;
      letter-spacing: .06em;
      text-transform: uppercase;
      text-decoration: none;
      white-space: nowrap;
      transition: all .22s var(--ease);
      cursor: pointer;
      border: none;
    }

    .hp-cta-btn-primary {
      background: linear-gradient(135deg, var(--ag), var(--ab));
      color: var(--am3);
      box-shadow: 0 6px 24px rgba(232, 168, 50, .3);
    }

    .hp-cta-btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 36px rgba(232, 168, 50, .45);
    }

    .hp-cta-btn-ghost {
      background: rgba(255, 255, 255, .06);
      color: rgba(255, 255, 255, .75);
      border: 1.5px solid rgba(255, 255, 255, .12);
    }

    .hp-cta-btn-ghost:hover {
      background: rgba(255, 255, 255, .1);
      color: var(--white);
      border-color: rgba(255, 255, 255, .25);
    }

    /* CTA sub-features */
    .hp-cta-features {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 16px;
      margin-top: 60px;
      position: relative;
      z-index: 1;
    }

    @media(max-width:640px) {
      .hp-cta-features {
        grid-template-columns: 1fr
      }
    }

    .hp-cta-feat {
      background: rgba(255, 255, 255, .04);
      border: 1px solid rgba(255, 255, 255, .06);
      border-radius: 20px;
      padding: 24px;
      transition: all .25s var(--ease);
    }

    .hp-cta-feat:hover {
      background: rgba(255, 255, 255, .07);
      border-color: rgba(232, 168, 50, .2);
      transform: translateY(-2px);
    }

    .hp-cta-feat-ic {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      background: var(--ap);
      border: 1px solid rgba(232, 168, 50, .2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      color: var(--ab);
      margin-bottom: 14px;
      transition: transform .3s var(--ease-spring);
    }

    .hp-cta-feat:hover .hp-cta-feat-ic {
      transform: rotate(8deg) scale(1.08)
    }

    .hp-cta-feat-title {
      font-size: 14px;
      font-weight: 700;
      color: var(--white);
      margin-bottom: 6px
    }

    .hp-cta-feat-desc {
      font-size: 12.5px;
      color: rgba(255, 255, 255, .4);
      line-height: 1.6
    }

    .hp-cta-feat-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 10.5px;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--ab);
      text-decoration: none;
      margin-top: 12px;
      transition: gap .2s;
    }

    .hp-cta-feat-link:hover {
      gap: 10px
    }

    .hp-cta-feat-link i {
      font-size: 9px
    }
  </style>

  <div class="hp-body">

    {{-- ════ SECTION 1: HERO ════ --}}
    <section class="hp-hero">
      <div class="hp-hero-bg">
        <div class="hp-hero-bg-left"></div>
        <div class="hp-hero-bg-right">
          <img
            src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80"
            alt="">
        </div>
      </div>
      <div class="hp-orb hp-orb-1"></div>
      <div class="hp-orb hp-orb-2"></div>

      <div class="hp-hero-inner">
        <div class="hp-eyebrow">
          <span class="hp-eyebrow-dot"></span>
          <span>{{ __('Trusted by 50,000+ Clients Worldwide') }}</span>
        </div>

        <h1 class="hp-h1">
          {{ __('Find Your') }}<br>
          <em>{{ __('Architectural Dream.') }}</em>
          <span class="hp-h1-line2">{{ __('Premium Real Estate — Redefined.') }}</span>
        </h1>

        <p class="hp-hero-desc">
          {{ __('Experience a new standard of property acquisition. Verified listings, trusted agents, and a seamless digital journey — all in one platform.') }}
        </p>

        {{-- Search Box --}}
        <div class="hp-search-box">
          <div class="hp-tabs">
            @php $tabs = ['Buy', 'Rent', 'Sell', 'Invest']; @endphp
            @foreach($tabs as $idx => $tab)
              <button class="hp-tab {{ $idx === 0 ? 'hp-tab-active' : '' }}"
                onclick="this.closest('.hp-search-box').querySelectorAll('.hp-tab').forEach(t=>t.classList.remove('hp-tab-active'));this.classList.add('hp-tab-active')">
                {{ __($tab) }}
              </button>
            @endforeach
          </div>
          <form action="{{ route('properties.index') }}" method="GET">
            <div class="hp-search-fields">
              <div class="hp-field">
                <i class="hp-field-icon fas fa-location-dot"></i>
                <input type="text" name="location" placeholder="{{ __('City, Area or Address') }}">
              </div>
              <div class="hp-field">
                <i class="hp-field-icon fas fa-home"></i>
                <select name="property_type">
                  <option value="">{{ __('All Property Types') }}</option>
                  <option value="villa">{{ __('Villas') }}</option>
                  <option value="apartment">{{ __('Apartments') }}</option>
                  <option value="house">{{ __('Houses') }}</option>
                  <option value="office">{{ __('Offices') }}</option>
                  <option value="land">{{ __('Land') }}</option>
                </select>
              </div>
              <div class="hp-field">
                <i class="hp-field-icon fas fa-coins"></i>
                <input type="text" name="price_range" placeholder="{{ __('Budget Range') }}">
              </div>
              <button type="submit" class="hp-search-btn">
                <i class="fas fa-search"></i>
                {{ __('Search') }}
              </button>
            </div>
          </form>
        </div>

        <div class="hp-quick-links">
          <a href="#" class="hp-quick"><span class="hp-quick-ic"><i
                class="fas fa-bolt"></i></span>{{ __('Hot Deals') }}</a>
          <a href="#" class="hp-quick"><span class="hp-quick-ic"><i
                class="fas fa-crown"></i></span>{{ __('Premium Listings') }}</a>
          <a href="#" class="hp-quick"><span class="hp-quick-ic"><i
                class="fas fa-map-marked-alt"></i></span>{{ __('Map Search') }}</a>
          <a href="#" class="hp-quick"><span class="hp-quick-ic"><i
                class="fas fa-percent"></i></span>{{ __('New Discounts') }}</a>
        </div>
      </div>

      <div class="hp-scroll-ind">
        <span>{{ __('Scroll') }}</span>
        <div class="hp-scroll-track">
          <div class="hp-scroll-line"></div>
        </div>
      </div>
    </section>

    {{-- ════ SECTION 2: STATS ════ --}}
    <section class="hp-stats">
      <div class="hp-stats-inner">
        @php
          $counterStats = [
            ['val' => 12500, 'suf' => '+', 'lbl' => 'Curated Properties', 'icon' => 'fas fa-building'],
            ['val' => 8400, 'suf' => '+', 'lbl' => 'Successful Deals', 'icon' => 'fas fa-handshake'],
            ['val' => 1200, 'suf' => '+', 'lbl' => 'Verified Agents', 'icon' => 'fas fa-user-tie'],
            ['val' => 450, 'suf' => '+', 'lbl' => 'Partner Cities', 'icon' => 'fas fa-globe'],
          ];
        @endphp
        @foreach($counterStats as $s)
          <div class="hp-stat">
            <div class="hp-stat-icon"><i class="{{ $s['icon'] }}"></i></div>
            <div class="hp-stat-num">
              <span data-counter="{{ $s['val'] }}">0</span><span class="hp-stat-plus">{{ $s['suf'] }}</span>
            </div>
            <div class="hp-stat-lbl">{{ __($s['lbl']) }}</div>
          </div>
        @endforeach
      </div>
    </section>

    {{-- ════ SECTION 3: FEATURED PROPERTIES ════ --}}
    <section class="hp-featured">
      <div class="hp-sec-inner">
        <div class="hp-sec-head">
          <div>
            <div class="hp-sec-label">{{ __('Handpicked for You') }}</div>
            <h2 class="hp-sec-h2">Featured <em>Masterpieces</em></h2>
            <p class="hp-sec-p">
              {{ __('Our curated selection of architectural marvels — each property a testament to luxury, design, and comfort.') }}
            </p>
          </div>
          <a href="{{ route('properties.index', ['featured' => 1]) }}" class="hp-see-all">
            {{ __('View All Properties') }} <i class="fas fa-arrow-right"></i>
          </a>
        </div>

        <div class="hp-props-grid">
          @forelse($featuredProperties ?? [] as $property)
            <div class="hp-pcard {{ $loop->first ? 'hp-pcard-big' : '' }}">
              <div class="hp-pcard-img">
                <img
                  src="{{ $property->media->first() ? Storage::url($property->media->first()->file_path) : 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80' }}"
                  alt="{{ $property->title }}">
                <div class="hp-pcard-overlay"></div>
                <div class="hp-pcard-badges">
                  <span class="hp-badge hb-status">{{ $property->status }}</span>
                  <span class="hp-badge hb-type">{{ $property->propertyType->name ?? 'Property' }}</span>
                </div>
                <button class="hp-pcard-wish"><i class="far fa-heart"></i></button>
                <div class="hp-pcard-price">
                  <div class="hp-price-val">{{ number_format($property->price->price ?? 0) }}</div>
                  <div class="hp-price-cur">{{ $property->price->currency ?? 'USD' }}</div>
                </div>
              </div>
              <div class="hp-pcard-body">
                <div class="hp-pcard-title"><a
                    href="{{ route('properties.show', $property->id) }}">{{ $property->title }}</a></div>
                <div class="hp-pcard-loc"><i
                    class="fas fa-map-marker-alt"></i>{{ $property->location->address ?? 'Location not available' }}</div>
                <div class="hp-pcard-meta">
                  <div class="hp-meta-item"><span class="hp-meta-val">{{ $property->bedrooms ?? 0 }}</span><span
                      class="hp-meta-lbl">Beds</span></div>
                  <div class="hp-meta-item"><span class="hp-meta-val">{{ $property->bathrooms ?? 0 }}</span><span
                      class="hp-meta-lbl">Baths</span></div>
                  <div class="hp-meta-item"><span class="hp-meta-val">{{ $property->area ?? 0 }}</span><span
                      class="hp-meta-lbl">m²</span></div>
                </div>
                <div class="hp-pcard-foot">
                  <a href="{{ route('properties.show', $property->id) }}" class="hp-pcard-cta"><i class="fas fa-eye"></i>
                    {{ __('View Details') }}</a>
                  <button class="hp-pcard-share"><i class="fas fa-paper-plane"></i></button>
                </div>
              </div>
            </div>
          @empty
            {{-- Demo cards when no data --}}
            @for($i = 1; $i <= 4; $i++)
              <div class="hp-pcard {{ $i === 1 ? 'hp-pcard-big' : '' }}">
                <div class="hp-pcard-img">
                  @php
                    $imgs = [
                      'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80',
                      'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&q=80',
                      'https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=800&q=80',
                      'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800&q=80'
                    ];
                  @endphp
                  <img src="{{ $imgs[$i - 1] }}" alt="">
                  <div class="hp-pcard-overlay"></div>
                  <div class="hp-pcard-badges">
                    <span class="hp-badge hb-status">For Sale</span>
                    <span class="hp-badge {{ $i === 1 ? 'hb-premium' : 'hb-type' }}">{{ $i === 1 ? 'Premium' : 'Villa' }}</span>
                  </div>
                  <button class="hp-pcard-wish"><i class="far fa-heart"></i></button>
                  <div class="hp-pcard-price">
                    <div class="hp-price-val">${{ number_format(1200000 + $i * 380000) }}</div>
                    <div class="hp-price-cur">USD</div>
                  </div>
                </div>
                <div class="hp-pcard-body">
                  <div class="hp-pcard-title"><a href="#">Luxury Estate {{ $i }} — Riverside</a></div>
                  <div class="hp-pcard-loc"><i class="fas fa-map-marker-alt"></i>New Cairo, Egypt</div>
                  <div class="hp-pcard-meta">
                    <div class="hp-meta-item"><span class="hp-meta-val">{{ 3 + $i }}</span><span class="hp-meta-lbl">Beds</span>
                    </div>
                    <div class="hp-meta-item"><span class="hp-meta-val">{{ 2 + $i }}</span><span
                        class="hp-meta-lbl">Baths</span></div>
                    <div class="hp-meta-item"><span class="hp-meta-val">{{ 280 + $i * 60 }}</span><span
                        class="hp-meta-lbl">m²</span></div>
                  </div>
                  <div class="hp-pcard-foot">
                    <a href="#" class="hp-pcard-cta"><i class="fas fa-eye"></i> View Details</a>
                    <button class="hp-pcard-share"><i class="fas fa-paper-plane"></i></button>
                  </div>
                </div>
              </div>
            @endfor
          @endforelse
        </div>
      </div>
    </section>

    {{-- ════ SECTION 4: CATEGORIES ════ --}}
    <section class="hp-cats">
      <div class="hp-cats-dots"></div>
      <div class="hp-cats-glow"></div>
      <div class="hp-sec-inner" style="position:relative;z-index:1">
        <div style="text-align:center;max-width:600px;margin:0 auto">
          <div class="hp-sec-label" style="justify-content:center;color:var(--ab)">{{ __('Browse by Category') }}</div>
          <h2 class="hp-sec-h2" style="color:var(--white)">{{ __('Every') }} <em>Property
              Type</em><br>{{ __('You Need') }}</h2>
          <p class="hp-sec-p" style="color:rgba(255,255,255,.4);margin:0 auto">
            {{ __('Navigate through our curated ecosystem of property segments tailored for modern lifestyle needs.') }}
          </p>
        </div>

        <div class="hp-cats-grid">
          @php
            $cats = [
              ['name' => 'Apartments', 'icon' => 'fas fa-building', 'count' => '4,200+', 'glow' => 'rgba(232,168,50,.3)'],
              ['name' => 'Villas', 'icon' => 'fas fa-house', 'count' => '1,850+', 'glow' => 'rgba(181,118,61,.3)'],
              ['name' => 'Offices', 'icon' => 'fas fa-briefcase', 'count' => '920+', 'glow' => 'rgba(37,99,235,.25)'],
              ['name' => 'Land', 'icon' => 'fas fa-mountain', 'count' => '540+', 'glow' => 'rgba(22,163,74,.25)'],
              ['name' => 'Commercial', 'icon' => 'fas fa-store', 'count' => '320+', 'glow' => 'rgba(232,68,50,.25)'],
            ];
          @endphp
          @foreach($cats as $c)
            <a href="{{ route('properties.index', ['type' => strtolower($c['name'])]) }}" class="hp-cat">
              <div class="hp-cat-glow" style="background:{{ $c['glow'] }}"></div>
              <div class="hp-cat-ic-wrap"><i class="{{ $c['icon'] }}"></i></div>
              <div class="hp-cat-name">{{ __($c['name']) }}</div>
              <div class="hp-cat-count">{{ $c['count'] }} {{ __('listings') }}</div>
            </a>
          @endforeach
        </div>
      </div>
    </section>

    {{-- ════ SECTION 5: WHY US ════ --}}
    <section class="hp-why">
      <div class="hp-sec-inner">
        <div class="hp-why-grid">
          {{-- Visual --}}
          <div class="hp-why-visual">
            <img
              src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80"
              alt="">
            <div class="hp-why-badge">
              <div class="hp-why-badge-av"><i class="fas fa-trophy"></i></div>
              <div>
                <div class="hp-why-badge-val">#1 Platform</div>
                <div class="hp-why-badge-lbl">Real Estate · 2024</div>
              </div>
            </div>
            <div class="hp-why-visual-accent">15+<br><span style="font-size:10px;font-family:var(--ff-b)">Years</span>
            </div>
          </div>

          {{-- Content --}}
          <div>
            <div class="hp-sec-label">{{ __('Why Choose Us') }}</div>
            <h2 class="hp-sec-h2">The Platform Built<br>for <em>Serious Buyers</em></h2>
            <p class="hp-sec-p" style="margin-bottom:0">
              {{ __('We combine technology, expertise, and a passion for real estate to deliver an unmatched experience for buyers, sellers, and agents alike.') }}
            </p>

            <div class="hp-why-features">
              @php
                $feats = [
                  ['ic' => 'fas fa-shield-check', 'cls' => 'wfi-1', 't' => 'Verified Listings Only', 'd' => 'Every property is manually reviewed and verified by our expert team before going live.'],
                  ['ic' => 'fas fa-bolt', 'cls' => 'wfi-2', 't' => 'Instant Notifications', 'd' => 'Get real-time alerts the moment a property matching your criteria hits the market.'],
                  ['ic' => 'fas fa-headset', 'cls' => 'wfi-3', 't' => '24/7 Expert Support', 'd' => 'Our dedicated advisors are always available to guide you through every step.'],
                  ['ic' => 'fas fa-chart-line', 'cls' => 'wfi-4', 't' => 'Market Intelligence', 'd' => 'Access live price trends, neighborhood data, and investment insights.'],
                ];
              @endphp
              @foreach($feats as $f)
                <div class="hp-why-feat">
                  <div class="hp-why-feat-ic {{ $f['cls'] }}"><i class="{{ $f['ic'] }}"></i></div>
                  <div class="hp-why-feat-body">
                    <div class="hp-why-feat-title">{{ __($f['t']) }}</div>
                    <div class="hp-why-feat-desc">{{ __($f['d']) }}</div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- ════ SECTION 6: CTA ════ --}}
    <section class="hp-cta">
      <div class="hp-cta-card">
        <div class="hp-cta-inner">
          <div class="hp-cta-content">
            <div>
              <div class="hp-cta-tag">{{ __('Get Started Today') }}</div>
              <h2 class="hp-cta-h2">Elevate Your<br><em>Real Estate Journey</em></h2>
              <p class="hp-cta-p">
                {{ __('Whether buying, selling, or investing — our platform gives you every tool you need to succeed in today\'s market.') }}
              </p>
            </div>
            <div class="hp-cta-actions">
              <a href="{{ route('register') }}" class="hp-cta-btn hp-cta-btn-primary">
                <i class="fas fa-rocket"></i> {{ __('Get Started Free') }}
              </a>
              <a href="{{ route('contact') }}" class="hp-cta-btn hp-cta-btn-ghost">
                <i class="fas fa-comments"></i> {{ __('Talk to Experts') }}
              </a>
            </div>
          </div>

          <div class="hp-cta-features">
            @php
              $cfeats = [
                ['ic' => 'fas fa-users-gear', 't' => 'For Agents', 'd' => 'Market intelligence & lead pipeline automation.', 'r' => 'register'],
                ['ic' => 'fas fa-home-circle', 't' => 'For Owners', 'd' => 'Maximum exposure for your high-value asset.', 'r' => 'register'],
                ['ic' => 'fas fa-city', 't' => 'For Developers', 'd' => 'Showcase your vision to thousands of buyers.', 'r' => 'register'],
                ['ic' => 'fas fa-chart-pie', 't' => 'For Investors', 'd' => 'Data-driven analytics to grow your portfolio.', 'r' => 'register'],
              ];
            @endphp
            @foreach($cfeats as $cf)
              <div class="hp-cta-feat">
                <div class="hp-cta-feat-ic"><i class="{{ $cf['ic'] }}"></i></div>
                <div class="hp-cta-feat-title">{{ __($cf['t']) }}</div>
                <div class="hp-cta-feat-desc">{{ __($cf['d']) }}</div>
                <a href="{{ route($cf['r']) }}" class="hp-cta-feat-link">{{ __('Learn More') }} <i
                    class="fas fa-arrow-right"></i></a>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </section>

  </div>{{-- /hp-body --}}
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      /* ── Counter animation ── */
      function animateCounter(el, target) {
        const dur = 2200, fps = 1000 / 60, frames = Math.round(dur / fps);
        let f = 0, cur = 0;
        const inc = target / frames;
        const t = setInterval(() => {
          f++; cur += inc;
          if (f >= frames) { cur = target; clearInterval(t); }
          el.textContent = Math.floor(cur).toLocaleString();
        }, fps);
      }
      const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
          if (e.isIntersecting && !e.target.dataset.counted) {
            e.target.dataset.counted = '1';
            animateCounter(e.target, parseInt(e.target.dataset.counter));
          }
        });
      }, { threshold: 0.6 });
      document.querySelectorAll('[data-counter]').forEach(el => io.observe(el));

      /* ── Scroll-reveal (stagger) ── */
      const revealIO = new IntersectionObserver(entries => {
        entries.forEach((e, i) => {
          if (e.isIntersecting) {
            e.target.style.transitionDelay = (e.target.dataset.delay || 0) + 'ms';
            e.target.classList.add('hp-revealed');
            revealIO.unobserve(e.target);
          }
        });
      }, { threshold: 0.12 });

      document.querySelectorAll('.hp-pcard, .hp-cat, .hp-why-feat, .hp-cta-feat, .hp-stat').forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(24px)';
        el.style.transition = 'opacity .6s ease, transform .6s ease';
        el.dataset.delay = (i % 4) * 80;
        revealIO.observe(el);
      });

      // Add revealed class styles
      const style = document.createElement('style');
      style.textContent = '.hp-revealed { opacity: 1 !important; transform: translateY(0) !important; }';
      document.head.appendChild(style);

      /* ── Wishlist toggle ── */
      document.querySelectorAll('.hp-pcard-wish').forEach(btn => {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          const icon = this.querySelector('i');
          const isLiked = icon.classList.contains('fas');
          icon.classList.toggle('fas', !isLiked);
          icon.classList.toggle('far', isLiked);
          this.style.background = isLiked ? 'rgba(255,255,255,.15)' : '#fff';
          this.style.color = isLiked ? 'rgba(255,255,255,.8)' : '#e74c3c';
        });
      });
    });
  </script>
@endpush