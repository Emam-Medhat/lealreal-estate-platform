<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('dashboard.app_title'))</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts - Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * {
            font-family: 'Cairo', 'Segoe UI', sans-serif;
        }

        body {
            background: #f5f7fa;
        }

        .sidebar {
            background: #ffffff;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
        }

        .nav-item {
            transition: all 0.3s ease;
            color: #4a5568;
            font-weight: 500;
        }

        .nav-item:hover {
            background: #f0f4f8;
            color: #2563eb;
            padding-right: 20px;
        }

        .nav-item.active {
            background: #e3f2fd;
            color: #2563eb;
            border-right: 4px solid #2563eb;
            padding-right: calc(1rem - 4px);
        }

        .card-stat {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            border-left: 4px solid;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .card-stat:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #1a202c;
            margin: 12px 0;
        }

        .stat-label {
            color: #718096;
            font-size: 14px;
            font-weight: 500;
        }

        .stat-change {
            color: #48bb78;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
        }

        .stat-change.down {
            color: #f56565;
        }

        .notification-item {
            padding: 16px;
            border-right: 4px solid;
            background: #f9fafb;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .notification-item:hover {
            background: #ffffff;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-secondary {
            background: #ffffff;
            color: #2563eb;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            border: 2px solid #2563eb;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #f0f4f8;
        }

        .hero-section {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 48px;
            border-radius: 16px;
            margin-bottom: 32px;
        }

        .hero-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .hero-subtitle {
            opacity: 0.9;
            font-size: 16px;
        }

        .dropdown-menu {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border: 1px solid #e2e8f0;
            min-width: 320px;
        }

        .search-input {
            background: #f0f4f8;
            border: 1px solid #cbd5e0;
            padding: 10px 16px;
            border-radius: 8px;
            color: #2d3748;
            font-size: 14px;
        }

        .search-input:focus {
            outline: none;
            border-color: #2563eb;
            background: white;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 24px;
            margin-bottom: 12px;
            margin-right: 16px;
        }

        .badge-count {
            background: #2563eb;
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
            margin-right: 8px;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
    </style>
</head>

<body dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="bg-gray-100">

    <!-- Main Navigation -->
    @include('partials.navbar')

    <div class="flex h-screen bg-gray-100" style="margin-top: 130px;">
        <!-- Sidebar -->
        <aside class="w-64 sidebar overflow-y-auto">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-center space-x-2">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-blue-600">{{ __('dashboard.app_name') }}</h1>
                        <p class="text-xs text-gray-500">{{ __('dashboard.app_subtitle') }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="nav-item active flex items-center px-4 py-3 rounded-lg">
                    <i class="fas fa-home w-5 text-lg"></i>
                    <span class="mr-3">{{ __('dashboard.dashboard') }}</span>
                </a>

                <!-- Profile -->
                <a href="{{ route('dashboard.profile') }}" class="nav-item flex items-center px-4 py-3 rounded-lg">
                    <i class="fas fa-user w-5 text-lg"></i>
                    <span class="mr-3">{{ __('dashboard.profile') }}</span>
                </a>

                <!-- Settings -->
                <a href="{{ route('dashboard.settings') }}" class="nav-item flex items-center px-4 py-3 rounded-lg">
                    <i class="fas fa-cog w-5 text-lg"></i>
                    <span class="mr-3">{{ __('dashboard.settings') }}</span>
                </a>

                <!-- Properties Section -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h3 class="section-title">{{ __('dashboard.properties') }}</h3>
                    <a href="{{ route('properties.index') }}"
                        class="nav-item flex items-center justify-between px-4 py-3 rounded-lg">
                        <span class="flex items-center flex-1">
                            <i class="fas fa-building w-5 text-lg"></i>
                            <span class="mr-3">{{ __('dashboard.all_properties') }}</span>
                        </span>
                        <span class="badge-count">12</span>
                    </a>
                    <a href="{{ route('properties.create') }}" class="nav-item flex items-center px-4 py-3 rounded-lg">
                        <i class="fas fa-plus w-5 text-lg"></i>
                        <span class="mr-3">{{ __('dashboard.add_property') }}</span>
                    </a>
                </div>

                <!-- Analytics Section -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h3 class="section-title">{{ __('dashboard.analytics') }}</h3>
                    <a href="{{ route('analytics.dashboard') }}"
                        class="nav-item flex items-center px-4 py-3 rounded-lg">
                        <i class="fas fa-chart-line w-5 text-lg"></i>
                        <span class="mr-3">{{ __('dashboard.analytics') }}</span>
                    </a>
                    <a href="{{ route('reports.dashboard') }}" class="nav-item flex items-center px-4 py-3 rounded-lg">
                        <i class="fas fa-file-alt w-5 text-lg"></i>
                        <span class="mr-3">{{ __('dashboard.reports') }}</span>
                    </a>
                </div>

                <!-- AI Tools Section -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h3 class="section-title">{{ __('dashboard.ai') }}</h3>
                    <a href="{{ route('ai.smart-search') }}" class="nav-item flex items-center px-4 py-3 rounded-lg">
                        <i class="fas fa-robot w-5 text-lg"></i>
                        <span class="mr-3">{{ __('dashboard.smart_search') }}</span>
                    </a>
                    <a href="{{ route('ai.chat') }}" class="nav-item flex items-center px-4 py-3 rounded-lg">
                        <i class="fas fa-comments w-5 text-lg"></i>
                        <span class="mr-3">{{ __('dashboard.ai_assistant') }}</span>
                    </a>
                </div>

                <!-- Admin Section -->
                @if(auth()->user()->is_admin ?? false)
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <h3 class="section-title">{{ __('dashboard.system_admin') }}</h3>

                        <!-- Identity Group -->
                        <div class="space-y-1 mt-2">
                            <p class="text-[10px] font-bold text-gray-400 px-4 uppercase tracking-wider">
                                {{ __('dashboard.identity_users') }}
                            </p>
                            <a href="{{ route('admin.users.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-users w-4"></i>
                                <span class="mr-3">{{ __('dashboard.all_users') }}</span>
                            </a>
                            <a href="{{ route('investor.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-hand-holding-usd w-4"></i>
                                <span class="mr-3">{{ __('dashboard.investors_list') }}</span>
                            </a>
                        </div>

                        <!-- Real Estate Group -->
                        <div class="space-y-1 mt-4">
                            <p class="text-[10px] font-bold text-gray-400 px-4 uppercase tracking-wider">
                                {{ __('dashboard.real_estate_projects') }}
                            </p>
                            <a href="{{ route('admin.properties.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-building w-4"></i>
                                <span class="mr-3">{{ __('dashboard.properties') }}</span>
                            </a>
                            <a href="{{ route('admin.projects.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-project-diagram w-4"></i>
                                <span class="mr-3">{{ __('dashboard.projects') }}</span>
                            </a>
                            <a href="{{ route('admin.companies.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-building w-4"></i>
                                <span class="mr-3">{{ __('dashboard.companies_offices') }}</span>
                            </a>
                            <a href="{{ route('amenities.search') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-list-ul w-4"></i>
                                <span class="mr-3">{{ __('dashboard.amenities') }}</span>
                            </a>
                        </div>

                        <!-- Financial & Investment Group -->
                        <div class="space-y-1 mt-4">
                            <p class="text-[10px] font-bold text-gray-400 px-4 uppercase tracking-wider">
                                {{ __('dashboard.finance_investment') }}
                            </p>
                            <a href="{{ route('payments.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-wallet w-4"></i>
                                <span class="mr-3">{{ __('dashboard.payments') }}</span>
                            </a>
                            <a href="{{ route('investors.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-hand-holding-usd w-4"></i>
                                <span class="mr-3">{{ __('dashboard.investors') }}</span>
                            </a>
                            <a href="{{ route('investors.funds.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-coins w-4"></i>
                                <span class="mr-3">{{ __('dashboard.investment_funds') }}</span>
                            </a>
                        </div>

                        <!-- Sales & CRM Group -->
                        <div class="space-y-1 mt-4">
                            <p class="text-[10px] font-bold text-gray-400 px-4 uppercase tracking-wider">
                                {{ __('dashboard.sales_crm') }}
                            </p>
                            <a href="{{ route('agent.crm.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-address-book w-4"></i>
                                <span class="mr-3">{{ __('dashboard.crm_system') }}</span>
                            </a>
                            <a href="{{ route('leads.dashboard') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-funnel-dollar w-4"></i>
                                <span class="mr-3">{{ __('dashboard.leads_board') }}</span>
                            </a>
                            <a href="{{ route('agent.offers.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-handshake w-4"></i>
                                <span class="mr-3">{{ __('dashboard.offers_system') }}</span>
                            </a>
                            <a href="{{ route('agents.commissions.index', ['agent' => 1]) }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-percent w-4"></i>
                                <span class="mr-3">{{ __('dashboard.commissions') }}</span>
                            </a>
                        </div>

                        <!-- Analytics & AI Group -->
                        <div class="space-y-1 mt-4">
                            <p class="text-[10px] font-bold text-gray-400 px-4 uppercase tracking-wider">
                                {{ __('dashboard.analytics_ai') }}
                            </p>
                            <a href="{{ route('analytics-alt.dashboard') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-brain w-4"></i>
                                <span class="mr-3">{{ __('dashboard.ai_insights') }}</span>
                            </a>
                            <a href="{{ route('ai.price-prediction') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-crystal-ball w-4"></i>
                                <span class="mr-3">{{ __('dashboard.price_prediction') }}</span>
                            </a>
                            <a href="{{ route('ai.fraud-detection') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-user-secret w-4"></i>
                                <span class="mr-3">{{ __('dashboard.fraud_detection') }}</span>
                            </a>
                            <a href="{{ route('ai.virtual-reality') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-vr-cardboard w-4"></i>
                                <span class="mr-3">{{ __('dashboard.virtual_reality') }}</span>
                            </a>
                            <a href="{{ route('analytics-alt.bigdata.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-database w-4"></i>
                                <span class="mr-3">{{ __('dashboard.big_data') }}</span>
                            </a>
                        </div>

                        <!-- Operations Group -->
                        <div class="space-y-1 mt-4">
                            <p class="text-[10px] font-bold text-gray-400 px-4 uppercase tracking-wider">
                                {{ __('dashboard.operations_contracts') }}
                            </p>
                            <a href="{{ route('messages.contracts.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-file-signature w-4"></i>
                                <span class="mr-3">{{ __('dashboard.digital_contracts') }}</span>
                            </a>
                            <a href="{{ route('messages.auctions.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-gavel w-4"></i>
                                <span class="mr-3">{{ __('dashboard.live_auctions') }}</span>
                            </a>
                            <a href="{{ route('maintenance.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-tools w-4"></i>
                                <span class="mr-3">{{ __('dashboard.maintenance') }}</span>
                            </a>
                            <a href="{{ route('warranties.policies.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-shield-alt w-4"></i>
                                <span class="mr-3">{{ __('dashboard.warranties_insurance') }}</span>
                            </a>
                        </div>

                        <!-- Future-Tech & Legal Group -->
                        <div class="space-y-1 mt-4">
                            <p class="text-[10px] font-bold text-gray-400 px-4 uppercase tracking-wider">
                                {{ __('dashboard.tech_compliance') }}
                            </p>
                            <a href="{{ route('blockchain.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-link w-4"></i>
                                <span class="mr-3">{{ __('dashboard.blockchain') }}</span>
                            </a>
                            <a href="{{ route('documents.compliance.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-shield-check w-4"></i>
                                <span class="mr-3">{{ __('dashboard.compliance_center') }}</span>
                            </a>
                            <a href="{{ route('taxes.filing') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-file-contract w-4"></i>
                                <span class="mr-3">{{ __('dashboard.tax_declarations') }}</span>
                            </a>
                        </div>

                        <!-- Real Estate Development -->
                        <div class="space-y-1 mt-4">
                            <p class="text-[10px] font-bold text-gray-400 px-4 uppercase tracking-wider">
                                {{ __('dashboard.real_estate_dev') }}</p>
                            <a href="{{ route('developer.bim.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-cube w-4"></i>
                                <span class="mr-3">{{ __('dashboard.bim_models') }}</span>
                            </a>
                            <a href="{{ route('developer.construction-updates.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-tools w-4"></i>
                                <span class="mr-3">{{ __('dashboard.construction_updates') }}</span>
                            </a>
                        </div>

                        <!-- Human Capital Group -->
                        <div class="space-y-1 mt-4">
                            <p class="text-[10px] font-bold text-gray-400 px-4 uppercase tracking-wider">
                                {{ __('dashboard.team_management') }}</p>
                            <a href="{{ route('agents.performance', ['agent' => 1]) }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-chart-pie w-4"></i>
                                <span class="mr-3">{{ __('dashboard.agent_performance') }}</span>
                            </a>
                            <a href="{{ route('agents.ranking') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-trophy w-4"></i>
                                <span class="mr-3">{{ __('dashboard.honor_board') }}</span>
                            </a>
                        </div>

                        <!-- Content & Site Group -->
                        <div class="space-y-1 mt-4">
                            <p class="text-[10px] font-bold text-gray-400 px-4 uppercase tracking-wider">
                                {{ __('dashboard.content_ads') }}
                            </p>
                            <a href="{{ route('admin.content.dashboard') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-edit w-4"></i>
                                <span class="mr-3">{{ __('dashboard.content_board') }}</span>
                            </a>
                            <a href="{{ route('admin.pages.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-file-alt w-4"></i>
                                <span class="mr-3">{{ __('dashboard.static_pages') }}</span>
                            </a>
                            <a href="{{ route('admin.news.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-bullhorn w-4"></i>
                                <span class="mr-3">{{ __('dashboard.news_center') }}</span>
                            </a>
                            <a href="{{ route('admin.blog.posts.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-blog w-4"></i>
                                <span class="mr-3">{{ __('dashboard.blog_network') }}</span>
                            </a>
                            <a href="{{ route('admin.menus.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-bars w-4"></i>
                                <span class="mr-3">{{ __('dashboard.build_menus') }}</span>
                            </a>
                            <a href="{{ route('admin.media.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-photo-video w-4"></i>
                                <span class="mr-3">{{ __('dashboard.media_library') }}</span>
                            </a>
                            <a href="{{ route('admin.widgets.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-puzzle-piece w-4"></i>
                                <span class="mr-3">{{ __('dashboard.widgets') }}</span>
                            </a>
                            <a href="{{ route('admin.faqs.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-question-circle w-4"></i>
                                <span class="mr-3">{{ __('dashboard.faqs') }}</span>
                            </a>
                            <a href="{{ route('ads.campaigns.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-ad w-4"></i>
                                <span class="mr-3">{{ __('dashboard.ad_campaigns') }}</span>
                            </a>
                        </div>

                        <!-- Subscriptions Group -->
                        <div class="space-y-1 mt-4">
                            <p class="text-[10px] font-bold text-gray-400 px-4 uppercase tracking-wider">
                                {{ __('dashboard.subscriptions_billing') }}
                            </p>
                            <a href="{{ route('subscriptions.plans.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-tags w-4"></i>
                                <span class="mr-3">{{ __('dashboard.subscription_plans') }}</span>
                            </a>
                            <a href="{{ route('subscriptions.invoices.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-file-invoice-dollar w-4"></i>
                                <span class="mr-3">{{ __('dashboard.invoices') }}</span>
                            </a>
                        </div>

                        <!-- Core System Group -->
                        <div class="space-y-1 mt-4">
                                <p class="text-[10px] font-bold text-gray-400 px-4 uppercase tracking-wider">{{ __('dashboard.system_control') }}</p>
                            <a href="{{ route('admin.settings.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-cogs w-4"></i>
                                    <span class="mr-3">{{ __('dashboard.general_settings') }}</span>
                            </a>
                            <a href="{{ route('admin.seo.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-search w-4"></i>
                                    <span class="mr-3">{{ __('dashboard.seo_settings') }}</span>
                            </a>
                            <a href="{{ route('requests.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-exchange-alt w-4"></i>
                                    <span class="mr-3">{{ __('dashboard.requests_log') }}</span>
                            </a>
                            <a href="{{ route('admin.errors.index') }}"
                                class="nav-item flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                <i class="fas fa-bug w-4"></i>
                                    <span class="mr-3">{{ __('dashboard.error_tracking') }}</span>
                            </a>
                        </div>
                    </div>
                @endif
            </nav>

            <!-- User Section -->
            <div class="absolute bottom-0 w-64 p-4 bg-white border-t border-gray-200">
                <div
                    class="flex items-center justify-between cursor-pointer hover:bg-gray-50 p-3 rounded-lg transition">
                    <div class="flex items-center flex-1">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                class="w-10 h-10 rounded-lg object-cover">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-user text-gray-500 text-lg"></i>
                            </div>
                        @endif
                        <div class="mr-3">
                            <p class="text-sm font-bold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ substr(auth()->user()->email ?? 'user@mail.com', 0, 20)
                                }}</p>
                        </div>
                    </div>
                    <i class="fas fa-ellipsis-h text-gray-500"></i>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit"
                        class="w-full text-right text-sm text-red-600 hover:text-red-800 px-3 py-2 rounded-lg hover:bg-red-50 transition">
                        <i class="fas fa-sign-out-alt ml-2"></i>{{ __('dashboard.logout') }}
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto flex flex-col">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
                <div class="px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">@yield('title', __('dashboard.dashboard'))</h1>
                            <p class="text-sm text-gray-600 mt-1">{{ __('dashboard.welcome_back') }}</p>
                        </div>

                        <div class="flex items-center space-x-6 space-x-reverse">
                            <!-- Search -->
                            <div class="hidden lg:flex items-center">
                                <div class="relative w-80">
                                    <input type="text" placeholder="{{ __('dashboard.search_property') }}" class="search-input w-full pl-10">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                            </div>

                            <!-- Notifications -->
                            <div class="relative group">
                                <button class="relative text-gray-600 hover:text-blue-600 transition"
                                    id="notification-bell">
                                    <i class="fas fa-bell text-xl"></i>
                                    <span id="notification-count"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">0</span>
                                </button>
                                <!-- Notifications Dropdown -->
                                <div id="notifications-dropdown"
                                    class="hidden group-hover:block absolute left-0 mt-2 dropdown-menu">
                                    <div class="p-4">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="font-bold text-gray-900">{{ __('dashboard.alerts') }}</h3>
                                            <button onclick="markAllAsRead()"
                                                class="text-xs text-blue-600 hover:text-blue-800">
                                                {{ __('dashboard.mark_all_read') }}
                                            </button>
                                        </div>
                                        <div id="notifications-list" class="space-y-3 max-h-96 overflow-y-auto">
                                            <!-- Notifications will be loaded here dynamically -->
                                            <div class="text-center text-gray-500 py-4">
                                                <i class="fas fa-bell-slash text-2xl mb-2"></i>
                                                <p class="text-sm">{{ __('dashboard.no_new_alerts') }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <a href="{{ route('user.notifications') }}"
                                                class="text-sm text-blue-600 hover:text-blue-800">
                                                {{ __('dashboard.view_all_alerts') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notification Sound -->
                            <audio id="notification-sound" preload="auto">
                                <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
                            </audio>

                            <!-- Messages -->
                            <div class="relative group">
                                <button class="relative text-gray-600 hover:text-blue-600 transition">
                                    <i class="fas fa-envelope text-xl"></i>
                                    <span
                                        class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">5</span>
                                </button>
                                <!-- Messages Dropdown -->
                                <div class="hidden group-hover:block absolute left-0 mt-2 dropdown-menu">
                                    <div class="p-4">
                                        <h3 class="font-bold text-gray-900 mb-4">{{ __('dashboard.messages') }}</h3>
                                        <div class="space-y-3 max-h-96 overflow-y-auto">
                                            <div
                                                class="p-3 hover:bg-gray-50 rounded-lg cursor-pointer transition border-right-4 border-blue-500">
                                                <p class="text-sm font-bold text-gray-900">أحمد محمد</p>
                                                <p class="text-xs text-gray-600 mt-1">هل العقار لا يزال متاحاً؟</p>
                                            </div>
                                            <div
                                                class="p-3 hover:bg-gray-50 rounded-lg cursor-pointer transition border-right-4 border-gray-300">
                                                <p class="text-sm font-bold text-gray-900">فاطمة علي</p>
                                                <p class="text-xs text-gray-600 mt-1">شكراً على المعلومات</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto p-8">
                <!-- Hero Section (for Dashboard) -->
                @if(request()->routeIs('dashboard'))
                    <div class="hero-section mb-8">
                        <h2 class="hero-title">لوحة التقارير</h2>
                        <p class="hero-subtitle">إدارة وتحليل تقارير العقارات</p>
                        <div class="flex gap-4 mt-6">
                            <button class="btn-secondary">عرض التقرير</button>
                            <button class="btn-primary">تقرير جديد +</button>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="card-stat" style="border-left-color: #3b82f6;">
                            <i class="fas fa-file-alt text-2xl text-blue-500"></i>
                            <div class="stat-number">45</div>
                            <div class="stat-label">إجمال التقارير</div>
                            <div class="stat-change">
                                <i class="fas fa-arrow-up ml-1"></i>%5 هذا الشهر
                            </div>
                        </div>

                        <div class="card-stat" style="border-left-color: #10b981;">
                            <i class="fas fa-check-circle text-2xl text-green-500"></i>
                            <div class="stat-number">38</div>
                            <div class="stat-label">التقارير المنتهية</div>
                            <div class="stat-change">
                                <i class="fas fa-arrow-up ml-1"></i>%3 هذا الأسبوع
                            </div>
                        </div>

                        <div class="card-stat" style="border-left-color: #a855f7;">
                            <i class="fas fa-clock text-2xl text-purple-500"></i>
                            <div class="stat-number">7</div>
                            <div class="stat-label">قيد المعالجة</div>
                            <div class="stat-change down">
                                <i class="fas fa-arrow-down ml-1"></i>%2 قائمة الانتظار
                            </div>
                        </div>

                        <div class="card-stat" style="border-left-color: #f97316;">
                            <i class="fas fa-chart-line text-2xl text-orange-500"></i>
                            <div class="stat-number">84%</div>
                            <div class="stat-label">معدل الإنجاز</div>
                            <div class="stat-change">
                                <i class="fas fa-arrow-up ml-1"></i>%6 هذا الشهر
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Flash Messages -->
                @if(session('success'))
                    <div
                        class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg mb-6 flex items-center">
                        <i class="fas fa-check-circle ml-3 text-green-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg mb-6 flex items-center">
                        <i class="fas fa-exclamation-circle ml-3 text-red-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Main Content Yield -->
                @yield('content')
            </div>
        </main>
    </div>

    <!-- AI Chatbot Widget -->
    @include('ai.widget')

    @stack('scripts')
</body>

</html>