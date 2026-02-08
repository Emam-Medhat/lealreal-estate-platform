<!-- Sidebar Menu Items -->
<li class="nav-header">GLOBAL SERVICES</li>

<!-- Currency -->
<li class="nav-item">
    <a href="{{ route('currency.index') }}" class="nav-link {{ request()->is('currency*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-dollar-sign"></i>
        <p>Currency Exchange</p>
    </a>
</li>

<!-- Language -->
<li class="nav-item">
    <a href="{{ route('language.index') }}" class="nav-link {{ request()->is('language*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-language"></i>
        <p>Multi-Language</p>
    </a>
</li>

<!-- Gamification -->
<li class="nav-item {{ request()->is('gamification*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('gamification*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-gamepad"></i>
        <p>
            Gamification
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('gamification.index') }}" class="nav-link {{ request()->is('gamification/index') ? 'active' : '' }}">
                <i class="fas fa-circle nav-icon"></i>
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

<!-- Blockchain -->
<li class="nav-item {{ request()->is('blockchain*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('blockchain*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-link"></i>
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

<!-- AI Services -->
<li class="nav-item {{ request()->is('ai*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('ai*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-robot"></i>
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

<!-- Enterprise -->
<li class="nav-item {{ request()->is('enterprise*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('enterprise*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-building"></i>
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
