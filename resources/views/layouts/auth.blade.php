<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EWAN') }} - {{ $title ?? 'Authentication' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Enhanced Animations -->
    <link rel="stylesheet" href="{{ asset('css/enhanced-animations.css') }}" />

    <!-- Additional Styles -->
    <style>
        @keyframes pulse {
            0%, 100% {
                opacity: 0.2;
            }
            50% {
                opacity: 0.3;
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes slideInFromLeft {
            0% {
                transform: translateX(-100%);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideInFromRight {
            0% {
                transform: translateX(100%);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            0% {
                transform: translateY(30px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }

        .animate-pulse {
            animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-slideInLeft {
            animation: slideInFromLeft 0.8s ease-out;
        }

        .animate-slideInRight {
            animation: slideInFromRight 0.8s ease-out;
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out;
        }

        .animate-shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            background-size: 1000px 100%;
            animation: shimmer 3s infinite;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #c471f5 75%, #667eea 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Floating Property Elements */
        .floating-property {
            position: absolute;
            opacity: 0.1;
            animation: float 8s ease-in-out infinite;
            z-index: 1;
        }

        .floating-property:nth-child(odd) {
            animation-direction: reverse;
            animation-duration: 6s;
        }

        .floating-property.building {
            font-size: 3rem;
            color: rgba(255, 255, 255, 0.3);
            top: 10%;
            left: 5%;
        }

        .floating-property.home {
            font-size: 2.5rem;
            color: rgba(255, 255, 255, 0.25);
            top: 20%;
            right: 10%;
            animation-delay: 1s;
        }

        .floating-property.city {
            font-size: 3.5rem;
            color: rgba(255, 255, 255, 0.2);
            bottom: 15%;
            left: 8%;
            animation-delay: 2s;
        }

        .floating-property.key {
            font-size: 2rem;
            color: rgba(255, 255, 255, 0.35);
            top: 50%;
            right: 5%;
            animation-delay: 3s;
        }

        .floating-property.landmark {
            font-size: 2.8rem;
            color: rgba(255, 255, 255, 0.25);
            bottom: 25%;
            right: 15%;
            animation-delay: 4s;
        }

        /* Property Cards Background */
        .property-card-bg {
            position: absolute;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            overflow: hidden;
            z-index: 1;
        }

        .property-card-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: shimmer 4s infinite;
        }

        .property-card-bg.small {
            width: 120px;
            height: 150px;
            top: 15%;
            left: 3%;
            transform: rotate(-5deg);
        }

        .property-card-bg.medium {
            width: 150px;
            height: 180px;
            bottom: 20%;
            right: 5%;
            transform: rotate(3deg);
        }

        .property-card-bg.large {
            width: 180px;
            height: 200px;
            top: 60%;
            left: 8%;
            transform: rotate(-8deg);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(139, 92, 246, 0.5);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(139, 92, 246, 0.7);
        }

        /* Glow Effects */
        .glow-purple {
            box-shadow: 0 0 30px rgba(139, 92, 246, 0.6);
        }

        .glow-pink {
            box-shadow: 0 0 30px rgba(240, 147, 251, 0.6);
        }

        .glow-blue {
            box-shadow: 0 0 30px rgba(102, 126, 234, 0.6);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .floating-property {
                font-size: 1.5rem !important;
            }
            
            .property-card-bg {
                transform: scale(0.7) !important;
            }
        }
    </style>
</head>

<body class="antialiased">
    <!-- Floating Property Icons -->
    <div class="floating-property building animate-float">
        <i class="fas fa-building"></i>
    </div>
    <div class="floating-property home animate-float">
        <i class="fas fa-home"></i>
    </div>
    <div class="floating-property city animate-float">
        <i class="fas fa-city"></i>
    </div>
    <div class="floating-property key animate-float">
        <i class="fas fa-key"></i>
    </div>
    <div class="floating-property landmark animate-float">
        <i class="fas fa-landmark"></i>
    </div>

    <!-- Floating Property Cards -->
    <div class="property-card-bg small animate-slideInLeft">
        <div style="height: 40px; background: linear-gradient(90deg, rgba(139, 92, 246, 0.3), rgba(240, 147, 251, 0.3));"></div>
        <div style="padding: 10px;">
            <div style="width: 80%; height: 8px; background: rgba(255,255,255,0.2); border-radius: 4px; margin-bottom: 8px;"></div>
            <div style="width: 60%; height: 6px; background: rgba(255,255,255,0.15); border-radius: 3px;"></div>
        </div>
    </div>

    <div class="property-card-bg medium animate-slideInRight" style="animation-delay: 0.5s;">
        <div style="height: 50px; background: linear-gradient(90deg, rgba(240, 147, 251, 0.3), rgba(102, 126, 234, 0.3));"></div>
        <div style="padding: 12px;">
            <div style="width: 90%; height: 10px; background: rgba(255,255,255,0.2); border-radius: 5px; margin-bottom: 10px;"></div>
            <div style="width: 70%; height: 8px; background: rgba(255,255,255,0.15); border-radius: 4px; margin-bottom: 8px;"></div>
            <div style="width: 50%; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px;"></div>
        </div>
    </div>

    <div class="property-card-bg large animate-fadeInUp" style="animation-delay: 1s;">
        <div style="height: 60px; background: linear-gradient(90deg, rgba(102, 126, 234, 0.3), rgba(196, 113, 245, 0.3));"></div>
        <div style="padding: 15px;">
            <div style="width: 85%; height: 12px; background: rgba(255,255,255,0.2); border-radius: 6px; margin-bottom: 12px;"></div>
            <div style="width: 75%; height: 10px; background: rgba(255,255,255,0.15); border-radius: 5px; margin-bottom: 10px;"></div>
            <div style="width: 65%; height: 8px; background: rgba(255,255,255,0.1); border-radius: 4px;"></div>
        </div>
    </div>

    @yield('content')

    <!-- Scripts -->
    <script>
        // CSRF Token Setup
        window.axios = window.axios || {};
        window.axios.defaults = window.axios.defaults || {};
        window.axios.defaults.headers = window.axios.defaults.headers || {};
        window.axios.defaults.headers.common = window.axios.defaults.headers.common || {};
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        const token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }

        // Enhanced animations on load
        document.addEventListener('DOMContentLoaded', function() {
            // Add staggered animations to floating elements
            const floatingElements = document.querySelectorAll('.floating-property');
            floatingElements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.5}s`;
            });

            // Add parallax effect on mouse move
            document.addEventListener('mousemove', (e) => {
                const x = e.clientX / window.innerWidth;
                const y = e.clientY / window.innerHeight;
                
                floatingElements.forEach((el, index) => {
                    const speed = (index + 1) * 10;
                    const xOffset = (x - 0.5) * speed;
                    const yOffset = (y - 0.5) * speed;
                    
                    el.style.transform = `translate(${xOffset}px, ${yOffset}px)`;
                });
            });
        });
    </script>

    <!-- AI Chatbot Widget -->
    @include('ai.widget')

    @stack('scripts')
</body>

</html>
