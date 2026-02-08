<!-- Dynamic Module System Sidebar -->
<li class="nav-header">MODULE SYSTEM</li>

<li class="nav-item {{ request()->is('modules*') ? 'menu-open' : '' }}">
    <a href="{{ route('modules.dashboard') }}" class="nav-link {{ request()->is('modules*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-th-large"></i>
        <p>
            Modules
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview" id="moduleMenuItems">
        <!-- Module items will be loaded dynamically -->
        <li class="nav-item">
            <a href="{{ route('modules.dashboard') }}" class="nav-link {{ request()->is('modules/dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt nav-icon"></i>
                <p>Dashboard</p>
            </a>
        </li>
    </ul>
</li>

<!-- Core Module -->
<li class="nav-header {{ request()->is(['properties*', 'users*', 'agents*', 'companies*', 'leads*', 'investments*']) ? '' : 'd-none' }}" id="coreModuleHeader">CORE MODULE</li>
<li class="nav-item {{ request()->is(['properties*', 'users*', 'agents*', 'companies*', 'leads*', 'investments*']) ? 'menu-open' : '' }} d-none" id="coreModuleMenu">
    <a href="#" class="nav-link {{ request()->is(['properties*', 'users*', 'agents*', 'companies*', 'leads*', 'investments*']) ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>
            Core Real Estate
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('properties.index') }}" class="nav-link {{ request()->is('properties*') ? 'active' : '' }}">
                <i class="fas fa-building nav-icon"></i>
                <p>Properties</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                <i class="fas fa-users nav-icon"></i>
                <p>Users</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('agents.index') }}" class="nav-link {{ request()->is('agents*') ? 'active' : '' }}">
                <i class="fas fa-user-tie nav-icon"></i>
                <p>Agents</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('companies.index') }}" class="nav-link {{ request()->is('companies*') ? 'active' : '' }}">
                <i class="fas fa-building nav-icon"></i>
                <p>Companies</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('leads.index') }}" class="nav-link {{ request()->is('leads*') ? 'active' : '' }}">
                <i class="fas fa-phone nav-icon"></i>
                <p>Leads</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('investments.index') }}" class="nav-link {{ request()->is('investments*') ? 'active' : '' }}">
                <i class="fas fa-chart-line nav-icon"></i>
                <p>Investments</p>
            </a>
        </li>
    </ul>
</li>

<!-- Global Services Module -->
<li class="nav-header {{ request()->is(['currency*', 'language*', 'gamification*', 'blockchain*', 'ai*', 'enterprise*']) ? '' : 'd-none' }}" id="globalServicesHeader">GLOBAL SERVICES</li>
<li class="nav-item {{ request()->is(['currency*', 'language*', 'gamification*', 'blockchain*', 'ai*', 'enterprise*']) ? 'menu-open' : '' }} d-none" id="globalServicesMenu">
    <a href="#" class="nav-link {{ request()->is(['currency*', 'language*', 'gamification*', 'blockchain*', 'ai*', 'enterprise*']) ? 'active' : '' }}">
        <i class="nav-icon fas fa-globe"></i>
        <p>
            Global Services
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('currency.index') }}" class="nav-link {{ request()->is('currency*') ? 'active' : '' }}">
                <i class="fas fa-dollar-sign nav-icon"></i>
                <p>Currency Exchange</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('language.index') }}" class="nav-link {{ request()->is('language*') ? 'active' : '' }}">
                <i class="fas fa-language nav-icon"></i>
                <p>Multi-Language</p>
            </a>
        </li>
        <li class="nav-item {{ request()->is('gamification*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('gamification*') ? 'active' : '' }}">
                <i class="fas fa-gamepad nav-icon"></i>
                <p>
                    Gamification
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('gamification.index') }}" class="nav-link {{ request()->is('gamification/index') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('gamification.achievements') }}" class="nav-link {{ request()->is('gamification/achievements') ? 'active' : '' }}">
                        <i class="fas fa-trophy nav-icon"></i>
                        <p>Achievements</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('gamification.badges') }}" class="nav-link {{ request()->is('gamification/badges') ? 'active' : '' }}">
                        <i class="fas fa-medal nav-icon"></i>
                        <p>Badges</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('gamification.challenges') }}" class="nav-link {{ request()->is('gamification/challenges') ? 'active' : '' }}">
                        <i class="fas fa-gamepad nav-icon"></i>
                        <p>Challenges</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('gamification.leaderboard') }}" class="nav-link {{ request()->is('gamification/leaderboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar nav-icon"></i>
                        <p>Leaderboard</p>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item {{ request()->is('blockchain*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('blockchain*') ? 'active' : '' }}">
                <i class="fas fa-link nav-icon"></i>
                <p>
                    Blockchain & Web3
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('blockchain.dashboard') }}" class="nav-link {{ request()->is('blockchain/dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('blockchain.contracts') }}" class="nav-link {{ request()->is('blockchain/contracts*') ? 'active' : '' }}">
                        <i class="fas fa-file-contract nav-icon"></i>
                        <p>Smart Contracts</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('blockchain.nfts.index') }}" class="nav-link {{ request()->is('blockchain/nfts*') ? 'active' : '' }}">
                        <i class="fas fa-image nav-icon"></i>
                        <p>NFTs</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('blockchain.transactions') }}" class="nav-link {{ request()->is('blockchain/transactions') ? 'active' : '' }}">
                        <i class="fas fa-exchange-alt nav-icon"></i>
                        <p>Transactions</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('blockchain.dao.index') }}" class="nav-link {{ request()->is('blockchain/dao*') ? 'active' : '' }}">
                        <i class="fas fa-users nav-icon"></i>
                        <p>DAOs</p>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item {{ request()->is('ai*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('ai*') ? 'active' : '' }}">
                <i class="fas fa-robot nav-icon"></i>
                <p>
                    AI Services
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('ai.index') }}" class="nav-link {{ request()->is('ai/dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.descriptions') }}" class="nav-link {{ request()->is('ai/descriptions') ? 'active' : '' }}">
                        <i class="fas fa-file-alt nav-icon"></i>
                        <p>Descriptions</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.images') }}" class="nav-link {{ request()->is('ai/images') ? 'active' : '' }}">
                        <i class="fas fa-image nav-icon"></i>
                        <p>AI Images</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.analysis') }}" class="nav-link {{ request()->is('ai/analysis') ? 'active' : '' }}">
                        <i class="fas fa-chart-line nav-icon"></i>
                        <p>Analysis</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.predictions') }}" class="nav-link {{ request()->is('ai/predictions') ? 'active' : '' }}">
                        <i class="fas fa-crystal-ball nav-icon"></i>
                        <p>Predictions</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.chat') }}" class="nav-link {{ request()->is('ai/chat') ? 'active' : '' }}">
                        <i class="fas fa-comments nav-icon"></i>
                        <p>AI Chat</p>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item {{ request()->is('enterprise*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('enterprise*') ? 'active' : '' }}">
                <i class="fas fa-building nav-icon"></i>
                <p>
                    Enterprise
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('enterprise.dashboard') }}" class="nav-link {{ request()->is('enterprise/dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('enterprise.accounts') }}" class="nav-link {{ request()->is('enterprise/accounts*') ? 'active' : '' }}">
                        <i class="fas fa-users nav-icon"></i>
                        <p>Accounts</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('enterprise.subscriptions') }}" class="nav-link {{ request()->is('enterprise/subscriptions') ? 'active' : '' }}">
                        <i class="fas fa-credit-card nav-icon"></i>
                        <p>Subscriptions</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('enterprise.reports') }}" class="nav-link {{ request()->is('enterprise/reports') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar nav-icon"></i>
                        <p>Reports</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('enterprise.integrations') }}" class="nav-link {{ request()->is('enterprise/integrations') ? 'active' : '' }}">
                        <i class="fas fa-plug nav-icon"></i>
                        <p>Integrations</p>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</li>

<!-- Developer Module -->
<li class="nav-header {{ request()->is('developer*') ? '' : 'd-none' }}" id="developerModuleHeader">DEVELOPER MODULE</li>
<li class="nav-item {{ request()->is('developer*') ? 'menu-open' : '' }} d-none" id="developerModuleMenu">
    <a href="#" class="nav-link {{ request()->is('developer*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-hard-hat"></i>
        <p>
            Developer
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('developer.index') }}" class="nav-link {{ request()->is('developer') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt nav-icon"></i>
                <p>Dashboard</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.projects.index') }}" class="nav-link {{ request()->is('developer/projects*') ? 'active' : '' }}">
                <i class="fas fa-project-diagram nav-icon"></i>
                <p>Projects</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.bim.index') }}" class="nav-link {{ request()->is('developer/bim*') ? 'active' : '' }}">
                <i class="fas fa-cube nav-icon"></i>
                <p>BIM Models</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.permits.index') }}" class="nav-link {{ request()->is('developer/permits*') ? 'active' : '' }}">
                <i class="fas fa-file-signature nav-icon"></i>
                <p>Permits</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.construction-updates.index') }}" class="nav-link {{ request()->is('developer/construction-updates*') ? 'active' : '' }}">
                <i class="fas fa-tools nav-icon"></i>
                <p>Construction</p>
            </a>
        </li>
    </ul>
</li>

<!-- Payments Module -->
<li class="nav-header {{ request()->is('payments*') ? '' : 'd-none' }}" id="paymentsModuleHeader">PAYMENTS MODULE</li>
<li class="nav-item {{ request()->is('payments*') ? 'menu-open' : '' }} d-none" id="paymentsModuleMenu">
    <a href="#" class="nav-link {{ request()->is('payments*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-wallet"></i>
        <p>
            Payments
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('payments.transactions.index') }}" class="nav-link {{ request()->is('payments/transactions*') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt nav-icon"></i>
                <p>Transactions</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('payments.invoices.index') }}" class="nav-link {{ request()->is('payments/invoices*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice nav-icon"></i>
                <p>Invoices</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('payments.receipts.index') }}" class="nav-link {{ request()->is('payments/receipts*') ? 'active' : '' }}">
                <i class="fas fa-receipt nav-icon"></i>
                <p>Receipts</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('payments.escrow.index') }}" class="nav-link {{ request()->is('payments/escrow*') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-usd nav-icon"></i>
                <p>Escrow</p>
            </a>
        </li>
    </ul>
</li>

<script>
// Dynamic Module Loading
$(document).ready(function() {
    loadModuleMenu();
});

function loadModuleMenu() {
    $.ajax({
        url: '/api/modules',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                updateModuleMenu(response.modules);
                showRelevantModules();
            }
        }
    });
}

function updateModuleMenu(modules) {
    const moduleMenu = $('#moduleMenuItems');
    
    // Clear existing module items (except dashboard)
    moduleMenu.find('li:not(:first)').remove();
    
    // Add module items dynamically
    Object.keys(modules).forEach(key => {
        const module = modules[key];
        if (module.status === 'active') {
            const menuItem = createModuleMenuItem(key, module);
            moduleMenu.append(menuItem);
        }
    });
}

function createModuleMenuItem(key, module) {
    return `
        <li class="nav-item">
            <a href="#" class="nav-link module-toggle" data-module-key="${key}">
                <i class="${module.icon} nav-icon text-${module.color}"></i>
                <p>
                    ${module.name}
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview" style="display: none;" id="module-${key}-menu">
                ${Object.keys(module.routes).slice(0, 5).map(routeKey => 
                    `<li class="nav-item">
                        <a href="/modules/${key}/${routeKey}" class="nav-link">
                            <i class="fas fa-circle nav-icon"></i>
                            <p>${module.routes[routeKey]}</p>
                        </a>
                    </li>`
                ).join('')}
                ${Object.keys(module.routes).length > 5 ? 
                    `<li class="nav-item">
                        <a href="/modules/${key}" class="nav-link">
                            <i class="fas fa-ellipsis-h nav-icon"></i>
                            <p>View All</p>
                        </a>
                    </li>` : ''
                }
            </ul>
        </li>
    `;
}

function showRelevantModules() {
    // Show module headers based on current route
    const currentPath = window.location.pathname;
    
    // Core Module
    if (currentPath.includes('/properties') || currentPath.includes('/users') || 
        currentPath.includes('/agents') || currentPath.includes('/companies') || 
        currentPath.includes('/leads') || currentPath.includes('/investments')) {
        $('#coreModuleHeader').removeClass('d-none');
        $('#coreModuleMenu').removeClass('d-none');
    }
    
    // Global Services Module
    if (currentPath.includes('/currency') || currentPath.includes('/language') || 
        currentPath.includes('/gamification') || currentPath.includes('/blockchain') || 
        currentPath.includes('/ai') || currentPath.includes('/enterprise')) {
        $('#globalServicesHeader').removeClass('d-none');
        $('#globalServicesMenu').removeClass('d-none');
    }
}

// Module toggle functionality
$(document).on('click', '.module-toggle', function(e) {
    e.preventDefault();
    const moduleKey = $(this).data('module-key');
    const submenu = $(`#module-${moduleKey}-menu`);
    
    // Toggle submenu
    submenu.slideToggle();
    
    // Toggle chevron
    const chevron = $(this).find('.fa-angle-left');
    chevron.toggleClass('fa-angle-down');
});

// Auto-expand module menu when accessing module routes
$(document).ready(function() {
    const currentPath = window.location.pathname;
    
    // Find and expand relevant module menu
    $('.module-toggle').each(function() {
        const moduleKey = $(this).data('module-key');
        if (currentPath.includes(`/modules/${moduleKey}/`)) {
            const submenu = $(`#module-${moduleKey}-menu`);
            submenu.show();
            $(this).find('.fa-angle-left').addClass('fa-angle-down');
        }
    });
});
</script>
