<!-- Navbar -->
<nav class="navbar-custom">
    <div class="navbar-content">
        <!-- Search Bar -->
        <div class="navbar-search">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search...">
            <button class="search-clear" onclick="clearSearch()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Right Side Icons -->
        <div class="navbar-actions">
            <!-- Notifications -->
            <div class="navbar-icon-wrapper">
                <button class="navbar-icon" onclick="toggleNotifications()" id="notificationBtn">
                    <i class="fas fa-bell"></i>
                    @if(isset($alertCounts) && $alertCounts['total'] > 0)
                        <span class="notification-badge {{ isset($hasCriticalAlerts) && $hasCriticalAlerts ? 'critical' : '' }}">
                            {{ $alertCounts['total'] > 99 ? '99+' : $alertCounts['total'] }}
                        </span>
                    @endif
                </button>

                <!-- Notifications Dropdown -->
                <div class="notifications-dropdown" id="notificationsDropdown">
                    <div class="dropdown-header">
                        <h6><i class="fas fa-bell"></i> Inventory Alerts</h6>
                        @if(isset($alertCounts) && $alertCounts['total'] > 0)
                            <span class="badge {{ $alertCounts['critical'] > 0 ? 'bg-danger' : 'bg-warning' }}">
                                {{ $alertCounts['total'] }} Alert{{ $alertCounts['total'] > 1 ? 's' : '' }}
                            </span>
                        @endif
                    </div>

                    <!-- Alert Summary -->
                    @if(isset($alertCounts))
                        <div class="alert-summary">
                            @if($alertCounts['out_of_stock'] > 0)
                                <div class="alert-summary-item critical">
                                    <i class="fas fa-times-circle"></i>
                                    <span>{{ $alertCounts['out_of_stock'] }} Out of Stock</span>
                                </div>
                            @endif
                            @if($alertCounts['expired'] > 0)
                                <div class="alert-summary-item critical">
                                    <i class="fas fa-skull-crossbones"></i>
                                    <span>{{ $alertCounts['expired'] }} Expired</span>
                                </div>
                            @endif
                            @if($alertCounts['low_stock'] > 0)
                                <div class="alert-summary-item warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>{{ $alertCounts['low_stock'] }} Low Stock</span>
                                </div>
                            @endif
                            @if($alertCounts['expiring_soon'] > 0)
                                <div class="alert-summary-item warning">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $alertCounts['expiring_soon'] }} Expiring Soon</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="notifications-list">
                        @if(isset($inventoryAlerts) && $inventoryAlerts->count() > 0)
                            @foreach($inventoryAlerts as $alert)
                                <a href="{{ $alert['action_url'] }}" class="notification-item {{ $alert['priority'] === 'critical' ? 'critical' : ($alert['priority'] === 'high' ? 'high' : '') }}">
                                    <div class="notification-icon {{ $alert['icon_class'] }}">
                                        <i class="fas {{ $alert['icon'] }}"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p class="notification-text">{{ $alert['message'] }}</p>
                                        <div class="notification-meta">
                                            <small class="notification-time">
                                                <i class="fas fa-clock"></i> {{ $alert['time_ago'] }}
                                            </small>
                                            <span class="notification-action">{{ $alert['action_label'] }} <i class="fas fa-arrow-right"></i></span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="no-notifications">
                                <i class="fas fa-check-circle"></i>
                                <p>All clear! No alerts at this time.</p>
                            </div>
                        @endif
                    </div>

                    <div class="dropdown-footer">
                        <a href="{{ route('admin.inventory-alerts.index') }}">
                            <i class="fas fa-list"></i> View All Alerts
                        </a>
                        <button class="refresh-btn" onclick="refreshAlerts()" title="Refresh Alerts">
                            <i class="fas fa-sync-alt" id="refreshIcon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- User Avatar Dropdown -->
            <div class="navbar-icon-wrapper">
                <button class="user-avatar" onclick="toggleUserMenu()">
                    {{ strtoupper(substr(auth()->user()->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
                </button>

                <!-- User Dropdown Menu -->
                <div class="user-dropdown" id="userDropdown">
                    <div class="user-dropdown-header">
                        <div class="user-avatar-large">
                            {{ strtoupper(substr(auth()->user()->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
                        </div>
                        <div class="user-dropdown-info">
                            <h6>{{ auth()->user()->full_name ?? 'User' }}</h6>
                            <p>{{ auth()->user()->email ?? 'user@example.com' }}</p>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                    <a href="" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    /* Navbar Styles */
    .navbar-custom {
        position: fixed;
        top: 0;
        left: var(--sidebar-width, 260px);
        right: 0;
        height: 70px;
        background: linear-gradient(135deg, var(--primary-green, #1a4d2e) 0%, var(--light-green, #2d7a4e) 100%);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        z-index: 999;
        transition: left 0.3s ease;
    }

    .navbar-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 2rem;
        height: 100%;
    }

    /* Search Bar */
    .navbar-search {
        position: relative;
        flex: 1;
        max-width: 500px;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.7);
        font-size: 1.1rem;
    }

    .search-input {
        width: 100%;
        padding: 0.75rem 3rem 0.75rem 3rem;
        border: none;
        border-radius: 25px;
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .search-input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .search-input:focus {
        outline: none;
        background-color: rgba(255, 255, 255, 0.3);
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    }

    .search-clear {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.7);
        font-size: 1.3rem;
        cursor: pointer;
        padding: 0;
        display: none;
        transition: all 0.2s ease;
    }

    .search-input:not(:placeholder-shown) ~ .search-clear {
        display: block;
    }

    .search-clear:hover {
        color: white;
    }

    /* Navbar Actions */
    .navbar-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-left: 2rem;
    }

    .navbar-icon-wrapper {
        position: relative;
    }

    .navbar-icon {
        position: relative;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .navbar-icon:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.05);
    }

    .navbar-icon i {
        font-size: 1.2rem;
    }

    /* Notification Badge */
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #ffc107;
        color: #000;
        border-radius: 10px;
        padding: 0.15rem 0.5rem;
        font-size: 0.7rem;
        font-weight: 700;
        border: 2px solid var(--primary-green, #1a4d2e);
        min-width: 20px;
        text-align: center;
    }

    .notification-badge.critical {
        background-color: #dc3545;
        color: white;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    /* User Avatar */
    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
        color: var(--primary-green, #1a4d2e);
        border: 3px solid rgba(255, 255, 255, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .user-avatar:hover {
        transform: scale(1.05);
        border-color: rgba(255, 255, 255, 0.5);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    /* Notifications Dropdown */
    .notifications-dropdown,
    .user-dropdown {
        position: absolute;
        top: calc(100% + 15px);
        right: 0;
        width: 380px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .notifications-dropdown.show,
    .user-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    /* Dropdown Arrow */
    .notifications-dropdown::before,
    .user-dropdown::before {
        content: '';
        position: absolute;
        top: -8px;
        right: 15px;
        width: 16px;
        height: 16px;
        background: white;
        transform: rotate(45deg);
        border-radius: 2px;
    }

    /* Dropdown Header */
    .dropdown-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e9ecef;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 12px 12px 0 0;
    }

    .dropdown-header h6 {
        margin: 0;
        font-weight: 700;
        color: #2c3e50;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Alert Summary */
    .alert-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .alert-summary-item {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.6rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .alert-summary-item.critical {
        background-color: #f8d7da;
        color: #721c24;
    }

    .alert-summary-item.warning {
        background-color: #fff3cd;
        color: #856404;
    }

    .alert-summary-item i {
        font-size: 0.7rem;
    }

    /* Notifications List */
    .notifications-list {
        max-height: 350px;
        overflow-y: auto;
    }

    .notification-item {
        display: flex;
        align-items: flex-start;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f3f5;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
        gap: 0.75rem;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
    }

    .notification-item.critical {
        background-color: #fff5f5;
        border-left: 3px solid #dc3545;
    }

    .notification-item.critical:hover {
        background-color: #ffe5e5;
    }

    .notification-item.high {
        background-color: #fffaf0;
        border-left: 3px solid #fd7e14;
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
        background-color: #f8f9fa;
    }

    .notification-icon.text-danger {
        background-color: #f8d7da;
        color: #dc3545;
    }

    .notification-icon.text-warning {
        background-color: #fff3cd;
        color: #856404;
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-text {
        margin: 0;
        font-size: 0.85rem;
        color: #2c3e50;
        font-weight: 500;
        line-height: 1.4;
    }

    .notification-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.35rem;
    }

    .notification-time {
        color: #6c757d;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .notification-action {
        font-size: 0.75rem;
        color: var(--primary-green, #1a4d2e);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .notification-action i {
        font-size: 0.65rem;
    }

    /* No Notifications */
    .no-notifications {
        padding: 2rem;
        text-align: center;
        color: #6c757d;
    }

    .no-notifications i {
        font-size: 3rem;
        color: #28a745;
        margin-bottom: 0.75rem;
    }

    .no-notifications p {
        margin: 0;
        font-size: 0.9rem;
    }

    /* Dropdown Footer */
    .dropdown-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1.25rem;
        border-top: 1px solid #e9ecef;
        background-color: #f8f9fa;
        border-radius: 0 0 12px 12px;
    }

    .dropdown-footer a {
        color: var(--primary-green, #1a4d2e);
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .dropdown-footer a:hover {
        text-decoration: underline;
    }

    .refresh-btn {
        background: none;
        border: none;
        color: var(--primary-green, #1a4d2e);
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .refresh-btn:hover {
        background-color: rgba(26, 77, 46, 0.1);
    }

    .refresh-btn.refreshing i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* User Dropdown */
    .user-dropdown {
        width: 280px;
    }

    .user-dropdown::before {
        right: 10px;
    }

    .user-dropdown-header {
        display: flex;
        align-items: center;
        padding: 1.25rem;
        gap: 1rem;
    }

    .user-avatar-large {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-green, #1a4d2e) 0%, var(--light-green, #2d7a4e) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .user-dropdown-info h6 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .user-dropdown-info p {
        margin: 0;
        font-size: 0.8rem;
        color: #6c757d;
    }

    .dropdown-divider {
        height: 1px;
        background-color: #e9ecef;
        margin: 0;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        padding: 0.85rem 1.25rem;
        color: #495057;
        text-decoration: none;
        transition: all 0.2s ease;
        gap: 0.75rem;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
        font-size: 0.95rem;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: var(--primary-green, #1a4d2e);
    }

    .dropdown-item i {
        font-size: 1.1rem;
        width: 20px;
    }

    .dropdown-item.logout-btn {
        color: #dc3545;
    }

    .dropdown-item.logout-btn:hover {
        background-color: #fff5f5;
        color: #c82333;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .navbar-custom {
            left: 0;
        }

        .navbar-content {
            padding: 0 1rem;
        }

        .navbar-search {
            max-width: 300px;
        }

        .navbar-actions {
            margin-left: 1rem;
        }

        .notifications-dropdown,
        .user-dropdown {
            right: -10px;
        }

        .notifications-dropdown {
            width: 340px;
        }
    }

    @media (max-width: 576px) {
        .navbar-search {
            max-width: 200px;
        }

        .search-input {
            font-size: 0.85rem;
            padding: 0.6rem 2.5rem 0.6rem 2.5rem;
        }

        .notifications-dropdown {
            width: 300px;
            right: -50px;
        }

        .user-dropdown {
            right: -20px;
        }
    }
</style>

<script>
    function toggleNotifications() {
        const dropdown = document.getElementById('notificationsDropdown');
        const userDropdown = document.getElementById('userDropdown');

        dropdown.classList.toggle('show');
        userDropdown.classList.remove('show');
    }

    function toggleUserMenu() {
        const dropdown = document.getElementById('userDropdown');
        const notificationsDropdown = document.getElementById('notificationsDropdown');

        dropdown.classList.toggle('show');
        notificationsDropdown.classList.remove('show');
    }

    function clearSearch() {
        const searchInput = document.querySelector('.search-input');
        searchInput.value = '';
        searchInput.focus();
    }

    // Refresh alerts via AJAX
    function refreshAlerts() {
        const refreshBtn = document.querySelector('.refresh-btn');
        const refreshIcon = document.getElementById('refreshIcon');

        refreshBtn.classList.add('refreshing');

        fetch('{{ route("admin.inventory-alerts.data") }}')
            .then(response => response.json())
            .then(data => {
                // Update notification badge
                const badge = document.querySelector('.notification-badge');
                if (data.counts.total > 0) {
                    if (badge) {
                        badge.textContent = data.counts.total > 99 ? '99+' : data.counts.total;
                        badge.classList.toggle('critical', data.counts.critical > 0);
                    } else {
                        // Create badge if it doesn't exist
                        const newBadge = document.createElement('span');
                        newBadge.className = 'notification-badge' + (data.counts.critical > 0 ? ' critical' : '');
                        newBadge.textContent = data.counts.total;
                        document.getElementById('notificationBtn').appendChild(newBadge);
                    }
                } else if (badge) {
                    badge.remove();
                }

                // Show success message
                console.log('Alerts refreshed successfully');
            })
            .catch(error => {
                console.error('Error refreshing alerts:', error);
            })
            .finally(() => {
                refreshBtn.classList.remove('refreshing');
            });
    }

    // Auto-refresh alerts every 60 seconds
    setInterval(refreshAlerts, 60000);

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const notificationsDropdown = document.getElementById('notificationsDropdown');
        const userDropdown = document.getElementById('userDropdown');

        if (!event.target.closest('.navbar-icon-wrapper')) {
            notificationsDropdown?.classList.remove('show');
            userDropdown?.classList.remove('show');
        }
    });

    // Handle search input
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            console.log('Searching for:', e.target.value);
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                console.log('Search submitted:', e.target.value);
            }
        });
    }
</script>
