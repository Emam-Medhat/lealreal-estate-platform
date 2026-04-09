<?php

// Navigation Bar Routes - Complete System Navigation

return [
    // Main Navigation
    'main' => [
        'home' => ['route' => 'home', 'label' => 'الرئيسية', 'icon' => 'home'],
        'properties' => ['route' => 'properties.index', 'label' => 'العقارات', 'icon' => 'building'],
        'projects' => ['route' => 'projects.index', 'label' => 'المشاريع', 'icon' => 'briefcase'],
        'agents' => ['route' => 'agents.directory', 'label' => 'الوكلاء', 'icon' => 'users'],
        'about' => ['route' => 'about', 'label' => 'من نحن', 'icon' => 'info'],
        'contact' => ['route' => 'contact', 'label' => 'اتصل بنا', 'icon' => 'phone'],
    ],
    
    // User Dashboard Navigation
    'dashboard' => [
        'dashboard' => ['route' => 'dashboard', 'label' => 'لوحة التحكم', 'icon' => 'dashboard'],
        'profile' => ['route' => 'profile', 'label' => 'الملف الشخصي', 'icon' => 'user'],
        'properties' => ['route' => 'user.properties', 'label' => 'عقاراتي', 'icon' => 'building'],
        'favorites' => ['route' => 'user.favorites', 'label' => 'المفضلة', 'icon' => 'heart'],
        'messages' => ['route' => 'messages.index', 'label' => 'الرسائل', 'icon' => 'message'],
        'notifications' => ['route' => 'notifications', 'label' => 'الإشعارات', 'icon' => 'bell'],
        'settings' => ['route' => 'settings', 'label' => 'الإعدادات', 'icon' => 'settings'],
    ],
    
    // Admin Navigation
    'admin' => [
        'dashboard' => ['route' => 'admin.dashboard', 'label' => 'لوحة التحكم', 'icon' => 'dashboard'],
        'users' => ['route' => 'admin.users.index', 'label' => 'المستخدمون', 'icon' => 'users'],
        'properties' => ['route' => 'admin.properties.index', 'label' => 'العقارات', 'icon' => 'building'],
        'projects' => ['route' => 'admin.projects.index', 'label' => 'المشاريع', 'icon' => 'briefcase'],
        'agents' => ['route' => 'admin.agents.index', 'label' => 'الوكلاء', 'icon' => 'users'],
        'leads' => ['route' => 'leads.index', 'label' => 'العملاء المحتملون', 'icon' => 'user-plus'],
        'documents' => ['route' => 'documents.index', 'label' => 'الوثائق', 'icon' => 'file'],
        'reports' => ['route' => 'reports.dashboard', 'label' => 'التقارير', 'icon' => 'chart-bar'],
        'analytics' => ['route' => 'analytics.dashboard', 'label' => 'التحليلات', 'icon' => 'chart-line'],
        'content' => ['route' => 'admin.content.dashboard', 'label' => 'المحتوى', 'icon' => 'document-text'],
        'blockchain' => ['route' => 'blockchain.dashboard', 'label' => 'البلوكشين', 'icon' => 'link'],
        'taxes' => ['route' => 'taxes.dashboard', 'label' => 'الضرائب', 'icon' => 'calculator'],
        'settings' => ['route' => 'admin.settings', 'label' => 'الإعدادات', 'icon' => 'settings'],
    ],
    
    // Blockchain Navigation
    'blockchain' => [
        'dashboard' => ['route' => 'blockchain.dashboard', 'label' => 'لوحة البلوكشين', 'icon' => 'dashboard'],
        'records' => ['route' => 'blockchain.records.index', 'label' => 'سجلات البلوكشين', 'icon' => 'database'],
        'contracts' => ['route' => 'smartcontracts.index', 'label' => 'العقود الذكية', 'icon' => 'file-contract'],
        'nfts' => ['route' => 'nfts.index', 'label' => 'الـ NFTs', 'icon' => 'image'],
        'wallets' => ['route' => 'crypto.wallets.index', 'label' => 'المحافظ', 'icon' => 'wallet'],
        'transactions' => ['route' => 'crypto.transactions.index', 'label' => 'المعاملات', 'icon' => 'exchange'],
        'tokens' => ['route' => 'tokens.index', 'label' => 'الرموز', 'icon' => 'coin'],
        'daos' => ['route' => 'daos.index', 'label' => 'الـ DAOs', 'icon' => 'users'],
        'defi' => ['route' => 'defi.index', 'label' => 'الـ DeFi', 'icon' => 'bank'],
        'staking' => ['route' => 'staking.index', 'label' => 'الـ Staking', 'icon' => 'lock'],
        'yield' => ['route' => 'yield.index', 'label' => 'ـ Yield Farming', 'icon' => 'trending-up'],
        'pools' => ['route' => 'pools.index', 'label' => 'مجمعات السيولة', 'icon' => 'layers'],
        'tokenization' => ['route' => 'tokenization.index', 'label' => 'توريق العقارات', 'icon' => 'building'],
    ],
    
    // Property Management Navigation
    'properties' => [
        'list' => ['route' => 'properties.index', 'label' => 'قائمة العقارات', 'icon' => 'list'],
        'add' => ['route' => 'properties.create', 'label' => 'إضافة عقار', 'icon' => 'plus'],
        'categories' => ['route' => 'properties.categories', 'label' => 'الفئات', 'icon' => 'folder'],
        'features' => ['route' => 'properties.features', 'label' => 'المميزات', 'icon' => 'star'],
        'locations' => ['route' => 'properties.locations', 'label' => 'المواقع', 'icon' => 'map-marker'],
        'search' => ['route' => 'properties.search', 'label' => 'البحث', 'icon' => 'search'],
        'favorites' => ['route' => 'properties.favorites', 'label' => 'المفضلة', 'icon' => 'heart'],
        'comparison' => ['route' => 'properties.comparison', 'label' => 'المقارنة', 'icon' => 'compare'],
    ],
    
    // Project Management Navigation
    'projects' => [
        'list' => ['route' => 'projects.index', 'label' => 'قائمة المشاريع', 'icon' => 'list'],
        'add' => ['route' => 'projects.create', 'label' => 'إضافة مشروع', 'icon' => 'plus'],
        'phases' => ['route' => 'projects.phases', 'label' => 'المراحل', 'icon' => 'layers'],
        'tasks' => ['route' => 'projects.tasks', 'label' => 'المهام', 'icon' => 'check-square'],
        'timeline' => ['route' => 'projects.timeline', 'label' => 'الجدول الزمني', 'icon' => 'clock'],
        'budget' => ['route' => 'projects.budget', 'label' => 'الميزانية', 'icon' => 'dollar-sign'],
        'reports' => ['route' => 'projects.reports', 'label' => 'التقارير', 'icon' => 'file-alt'],
        'team' => ['route' => 'projects.team', 'label' => 'الفريق', 'icon' => 'users'],
    ],
    
    // Lead Management Navigation
    'leads' => [
        'pipeline' => ['route' => 'leads.pipeline', 'label' => 'خط الأنابيب', 'icon' => 'filter'],
        'list' => ['route' => 'leads.index', 'label' => 'قائمة العملاء', 'icon' => 'list'],
        'add' => ['route' => 'leads.create', 'label' => 'إضافة عميل', 'icon' => 'plus'],
        'sources' => ['route' => 'leads.sources', 'label' => 'المصادر', 'icon' => 'source'],
        'status' => ['route' => 'leads.status', 'label' => 'الحالات', 'icon' => 'flag'],
        'scoring' => ['route' => 'leads.scoring', 'label' => 'التقييم', 'icon' => 'star'],
        'nurturing' => ['route' => 'leads.nurturing', 'label' => 'الرعاية', 'icon' => 'heart'],
        'analytics' => ['route' => 'leads.analytics', 'label' => 'التحليلات', 'icon' => 'chart-line'],
    ],
    
    // Document Management Navigation
    'documents' => [
        'list' => ['route' => 'documents.index', 'label' => 'قائمة الوثائق', 'icon' => 'list'],
        'upload' => ['route' => 'documents.create', 'label' => 'رفع وثيقة', 'icon' => 'upload'],
        'templates' => ['route' => 'documents.templates', 'label' => 'القوالب', 'icon' => 'file'],
        'contracts' => ['route' => 'contracts.index', 'label' => 'العقود', 'icon' => 'file-contract'],
        'signatures' => ['route' => 'documents.signatures', 'label' => 'التوقيعات', 'icon' => 'pen'],
        'categories' => ['route' => 'documents.categories', 'label' => 'الفئات', 'icon' => 'folder'],
        'search' => ['route' => 'documents.search', 'label' => 'البحث', 'icon' => 'search'],
    ],
    
    // Analytics Navigation
    'analytics' => [
        'dashboard' => ['route' => 'analytics.dashboard', 'label' => 'لوحة التحليل', 'icon' => 'dashboard'],
        'bigdata' => ['route' => 'analytics.bigdata.index', 'label' => 'البيانات الضخمة', 'icon' => 'database'],
        'predictions' => ['route' => 'analytics.predictions.index', 'label' => 'التنبؤات', 'icon' => 'crystal-ball'],
        'market' => ['route' => 'analytics.market.index', 'label' => 'تحليل السوق', 'icon' => 'chart-bar'],
        'behavior' => ['route' => 'analytics.behavior.index', 'label' => 'سلوك المستخدم', 'icon' => 'user'],
        'heatmaps' => ['route' => 'analytics.heatmaps.index', 'label' => 'خرائط الحرارة', 'icon' => 'fire'],
        'funnel' => ['route' => 'analytics.funnel.index', 'label' => 'تحليل القمع', 'icon' => 'filter'],
        'cohort' => ['route' => 'analytics.cohort.index', 'label' => 'تحليل الفئات', 'icon' => 'users'],
        'ai' => ['route' => 'analytics.ai-insights.index', 'label' => 'رؤى الذكاء الاصطناعي', 'icon' => 'brain'],
        'sentiment' => ['route' => 'analytics.sentiment.index', 'label' => 'تحليل المشاعر', 'icon' => 'smile'],
        'trends' => ['route' => 'analytics.trends.index', 'label' => 'الاتجاهات', 'icon' => 'trending-up'],
        'competitive' => ['route' => 'analytics.competitive.index', 'label' => 'التحليل التنافسي', 'icon' => 'crosshairs'],
    ],
    
    // Reports Navigation
    'reports' => [
        'dashboard' => ['route' => 'reports.dashboard', 'label' => 'لوحة التقارير', 'icon' => 'dashboard'],
        'sales' => ['route' => 'reports.sales', 'label' => 'تقارير المبيعات', 'icon' => 'dollar-sign'],
        'performance' => ['route' => 'reports.performance', 'label' => 'تقارير الأداء', 'icon' => 'chart-line'],
        'financial' => ['route' => 'reports.financial.index', 'label' => 'التقارير المالية', 'icon' => 'calculator'],
        'market' => ['route' => 'reports.market.index', 'label' => 'تقارير السوق', 'icon' => 'chart-bar'],
        'custom' => ['route' => 'reports.custom.index', 'label' => 'تقارير مخصصة', 'icon' => 'cog'],
        'templates' => ['route' => 'reports.templates', 'label' => 'قوالب التقارير', 'icon' => 'file'],
        'schedule' => ['route' => 'reports.schedule', 'label' => 'جدولة التقارير', 'icon' => 'clock'],
        'export' => ['route' => 'reports.export', 'label' => 'تصدير التقارير', 'icon' => 'download'],
    ],
    
    // Content Management Navigation
    'content' => [
        'dashboard' => ['route' => 'content.dashboard', 'label' => 'لوحة المحتوى', 'icon' => 'dashboard'],
        'blog' => ['route' => 'blog.index', 'label' => 'المدونة', 'icon' => 'edit'],
        'pages' => ['route' => 'pages.index', 'label' => 'الصفحات', 'icon' => 'file'],
        'news' => ['route' => 'news.index', 'label' => 'الأخبار', 'icon' => 'newspaper'],
        'guides' => ['route' => 'guides.index', 'label' => 'الأدلة', 'icon' => 'book'],
        'faq' => ['route' => 'faq.index', 'label' => 'الأسئلة الشائعة', 'icon' => 'help-circle'],
        'media' => ['route' => 'media.index', 'label' => 'المكتبة', 'icon' => 'image'],
        'seo' => ['route' => 'seo.index', 'label' => 'تحسين محركات البحث', 'icon' => 'search'],
        'menus' => ['route' => 'menus.index', 'label' => 'القوائم', 'icon' => 'menu'],
        'widgets' => ['route' => 'widgets.index', 'label' => 'العناصر', 'icon' => 'grid'],
    ],
    
    // Tax Management Navigation
    'taxes' => [
        'dashboard' => ['route' => 'taxes.dashboard', 'label' => 'لوحة الضرائب', 'icon' => 'dashboard'],
        'records' => ['route' => 'taxes.records.index', 'label' => 'سجلات الضرائب', 'icon' => 'database'],
        'returns' => ['route' => 'taxes.returns.index', 'label' => 'الإقرارات الضريبية', 'icon' => 'file-text'],
        'payments' => ['route' => 'taxes.payments.index', 'label' => 'المدفوعات', 'icon' => 'credit-card'],
        'invoices' => ['route' => 'taxes.invoices.index', 'label' => 'الفواتير', 'icon' => 'file-invoice'],
        'reports' => ['route' => 'taxes.reports', 'label' => 'تقارير الضرائب', 'icon' => 'chart-bar'],
        'settings' => ['route' => 'taxes.settings', 'label' => 'إعدادات الضرائب', 'icon' => 'settings'],
    ],
    
    // Quick Actions
    'quick_actions' => [
        'add_property' => ['route' => 'properties.create', 'label' => 'إضافة عقار', 'icon' => 'plus'],
        'add_project' => ['route' => 'projects.create', 'label' => 'إضافة مشروع', 'icon' => 'plus'],
        'add_lead' => ['route' => 'leads.create', 'label' => 'إضافة عميل', 'icon' => 'user-plus'],
        'upload_document' => ['route' => 'documents.create', 'label' => 'رفع وثيقة', 'icon' => 'upload'],
        'create_report' => ['route' => 'reports.create', 'label' => 'إنشاء تقرير', 'icon' => 'file'],
        'mint_nft' => ['route' => 'nfts.create', 'label' => 'صك NFT', 'icon' => 'image'],
        'deploy_contract' => ['route' => 'smartcontracts.create', 'label' => 'نشر عقد ذكي', 'icon' => 'code'],
        'tokenize_property' => ['route' => 'tokenization.create', 'label' => 'توريق عقار', 'icon' => 'building'],
    ],
    
    // Footer Navigation
    'footer' => [
        'home' => ['route' => 'home', 'label' => 'الرئيسية'],
        'properties' => ['route' => 'properties.index', 'label' => 'العقارات'],
        'projects' => ['route' => 'projects.index', 'label' => 'المشاريع'],
        'about' => ['route' => 'about', 'label' => 'من نحن'],
        'contact' => ['route' => 'contact', 'label' => 'اتصل بنا'],
        'privacy' => ['route' => 'privacy', 'label' => 'سياسة الخصوصية'],
        'terms' => ['route' => 'terms', 'label' => 'الشروط والأحكام'],
        'blog' => ['route' => 'blog.index', 'label' => 'المدونة'],
        'faq' => ['route' => 'faq.index', 'label' => 'الأسئلة الشائعة'],
        'support' => ['route' => 'support', 'label' => 'الدعم'],
    ],
    
    // Breadcrumbs
    'breadcrumbs' => [
        'home' => ['route' => 'home', 'label' => 'الرئيسية'],
        'dashboard' => ['route' => 'dashboard', 'label' => 'لوحة التحكم'],
        'properties' => ['route' => 'properties.index', 'label' => 'العقارات'],
        'projects' => ['route' => 'projects.index', 'label' => 'المشاريع'],
        'leads' => ['route' => 'leads.index', 'label' => 'العملاء'],
        'documents' => ['route' => 'documents.index', 'label' => 'الوثائق'],
        'reports' => ['route' => 'reports.dashboard', 'label' => 'التقارير'],
        'analytics' => ['route' => 'analytics.dashboard', 'label' => 'التحليلات'],
        'blockchain' => ['route' => 'blockchain.dashboard', 'label' => 'البلوكشين'],
        'taxes' => ['route' => 'taxes.dashboard', 'label' => 'الضرائب'],
    ],
    
    // User Menu
    'user_menu' => [
        'profile' => ['route' => 'profile', 'label' => 'الملف الشخصي', 'icon' => 'user'],
        'settings' => ['route' => 'settings', 'label' => 'الإعدادات', 'icon' => 'settings'],
        'notifications' => ['route' => 'notifications', 'label' => 'الإشعارات', 'icon' => 'bell'],
        'messages' => ['route' => 'messages.index', 'label' => 'الرسائل', 'icon' => 'message'],
        'favorites' => ['route' => 'user.favorites', 'label' => 'المفضلة', 'icon' => 'heart'],
        'history' => ['route' => 'user.history', 'label' => 'السجل', 'icon' => 'history'],
        'logout' => ['route' => 'logout', 'label' => 'تسجيل الخروج', 'icon' => 'sign-out-alt'],
    ],
    
    // Admin Settings
    'admin_settings' => [
        'general' => ['route' => 'admin.settings.general', 'label' => 'الإعدادات العامة', 'icon' => 'cog'],
        'security' => ['route' => 'admin.settings.security', 'label' => 'الأمان', 'icon' => 'shield'],
        'email' => ['route' => 'admin.settings.email', 'label' => 'البريد الإلكتروني', 'icon' => 'envelope'],
        'payment' => ['route' => 'admin.settings.payment', 'label' => 'الدفع', 'icon' => 'credit-card'],
        'social' => ['route' => 'admin.settings.social', 'label' => 'وسائل التواصل', 'icon' => 'share'],
        'seo' => ['route' => 'admin.settings.seo', 'label' => 'تحسين محركات البحث', 'icon' => 'search'],
        'backup' => ['route' => 'admin.settings.backup', 'label' => 'النسخ الاحتياطي', 'icon' => 'database'],
        'logs' => ['route' => 'admin.settings.logs', 'label' => 'السجلات', 'icon' => 'file-alt'],
    ],
];
