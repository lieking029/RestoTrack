<!-- Navbar -->
<nav class="navbar-custom">
    <div class="navbar-content">
        <!-- Search Bar -->
        <div class="navbar-search">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search...">
            <button class="search-clear" onclick="clearSearch()">
                <i class="bi bi-x"></i>
            </button>
        </div>

        <!-- Right Side Icons -->
        <div class="navbar-actions">
            <!-- Notifications -->
            <div class="navbar-icon-wrapper">
                <button class="navbar-icon" onclick="toggleNotifications()">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>

                <!-- Notifications Dropdown -->
                <div class="notifications-dropdown" id="notificationsDropdown">
                    <div class="dropdown-header">
                        <h6>Notifications</h6>
                        <span class="badge bg-danger">3 New</span>
                    </div>
                    <div class="notifications-list">
                        <a href="#" class="notification-item unread">
                            <i class="bi bi-exclamation-circle text-danger"></i>
                            <div>
                                <p class="notification-text">Low stock alert: Tomatoes</p>
                                <small class="notification-time">5 minutes ago</small>
                            </div>
                        </a>
                        <a href="#" class="notification-item unread">
                            <i class="bi bi-person-check text-success"></i>
                            <div>
                                <p class="notification-text">New employee registered</p>
                                <small class="notification-time">1 hour ago</small>
                            </div>
                        </a>
                        <a href="#" class="notification-item">
                            <i class="bi bi-receipt text-info"></i>
                            <div>
                                <p class="notification-text">Transaction #1234 completed</p>
                                <small class="notification-time">2 hours ago</small>
                            </div>
                        </a>
                    </div>
                    <div class="dropdown-footer">
                        <a href="#">View all notifications</a>
                    </div>
                </div>
            </div>

            <!-- User Avatar Dropdown -->
            <div class="navbar-icon-wrapper">
                <button class="user-avatar" onclick="toggleUserMenu()">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </button>

                <!-- User Dropdown Menu -->
                <div class="user-dropdown" id="userDropdown">
                    <div class="user-dropdown-header">
                        <div class="user-avatar-large">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="user-dropdown-info">
                            <h6>{{ auth()->user()->name ?? 'User' }}</h6>
                            <p>{{ auth()->user()->email ?? 'user@example.com' }}</p>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="" class="dropdown-item">
                        <i class="bi bi-person"></i>
                        <span>Profile</span>
                    </a>
                    <a href="" class="dropdown-item">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item logout-btn">
                            <i class="bi bi-box-arrow-right"></i>
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
        background-color: #dc3545;
        color: white;
        border-radius: 10px;
        padding: 0.15rem 0.4rem;
        font-size: 0.7rem;
        font-weight: 700;
        border: 2px solid var(--primary-green, #1a4d2e);
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
        width: 350px;
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
    }

    .dropdown-header h6 {
        margin: 0;
        font-weight: 700;
        color: #2c3e50;
        font-size: 1rem;
    }

    /* Notifications List */
    .notifications-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .notification-item {
        display: flex;
        align-items: start;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f3f5;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
        gap: 1rem;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
    }

    .notification-item.unread {
        background-color: #f0f7f4;
    }

    .notification-item i {
        font-size: 1.3rem;
        margin-top: 0.2rem;
    }

    .notification-text {
        margin: 0;
        font-size: 0.9rem;
        color: #2c3e50;
        font-weight: 500;
    }

    .notification-time {
        color: #6c757d;
        font-size: 0.8rem;
    }

    /* Dropdown Footer */
    .dropdown-footer {
        padding: 0.75rem 1.25rem;
        border-top: 1px solid #e9ecef;
        text-align: center;
    }

    .dropdown-footer a {
        color: var(--primary-green, #1a4d2e);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .dropdown-footer a:hover {
        text-decoration: underline;
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

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const notificationsDropdown = document.getElementById('notificationsDropdown');
        const userDropdown = document.getElementById('userDropdown');
        const notificationBtn = document.querySelector('.navbar-icon');
        const userAvatar = document.querySelector('.user-avatar');

        if (!event.target.closest('.navbar-icon-wrapper')) {
            notificationsDropdown?.classList.remove('show');
            userDropdown?.classList.remove('show');
        }
    });

    // Handle search input
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            // Add your search logic here
            console.log('Searching for:', e.target.value);
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                // Handle search submit
                console.log('Search submitted:', e.target.value);
            }
        });
    }
</script>