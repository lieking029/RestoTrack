<button class="sidebar-toggle" onclick="toggleSidebar()">
    <i class="fas fa-list fs-4"></i>
</button>

<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <h4>RestoTrack</h4>
        <p>Restaurant Management</p>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-title">Main Menu</div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.product.*') ? 'active' : '' }}"  href="{{ route('admin.product.index') }}">
                    <i class="fas fa-boxes"></i>
                    <span>Inventory</span>
                    @if (isset($lowStockCount) && $lowStockCount > 0)
                        <span class="nav-badge">{{ $lowStockCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.user.*') ? 'active' : '' }}"  href="{{ route('admin.user.index') }}">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link ">
                    <i class="fas fa-receipt"></i>
                    <span>Transactions</span>
                </a>
            </li>
        </ul>

        {{-- <div class="nav-section-title">Management</div> --}}

        {{-- <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link ">
                    <i class="fas fa-clipboard"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link ">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul> --}}
    </nav>

    {{-- <div class="sidebar-bottom">
        <div class="user-profile" onclick="window.location.href=''">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
            </div>
            <div class="user-info">
                <h6>{{ auth()->user()->name ?? 'User' }}</h6>
                <p>{{ auth()->user()->role ?? 'Staff' }}</p>
            </div>
            <i class="fas fa-chevron-right ms-auto"></i>
        </div>
    </div> --}}
</div>

<style>
    :root {
        --primary-green: #1a4d2e;
        --light-green: #2d7a4e;
        --hover-bg: #f0f7f4;
        --sidebar-width: 260px;
    }

    .sidebar {
        min-height: 100vh;
        background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
        width: var(--sidebar-width);
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .sidebar-logo {
        padding: 2rem 1.5rem;
        border-bottom: 1px solid #e9ecef;
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
    }

    .sidebar-logo h4 {
        color: white;
        font-weight: 700;
        font-size: 1.5rem;
        margin: 0;
        letter-spacing: 0.5px;
    }

    .sidebar-logo p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.75rem;
        margin: 0;
        margin-top: 0.25rem;
    }

    .sidebar-nav {
        padding: 1.5rem 0;
        padding-bottom: 100px;
    }

    .nav-section-title {
        padding: 0.5rem 1.5rem;
        font-size: 0.7rem;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 1rem;
    }

    .nav-item {
        margin: 0.25rem 0.75rem;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 0.85rem 1rem;
        color: #495057;
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.3s ease;
        font-weight: 500;
        font-size: 0.95rem;
        position: relative;
    }

    .nav-link i {
        font-size: 1.25rem;
        margin-right: 1rem;
        color: var(--primary-green);
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        background-color: var(--hover-bg);
        color: var(--primary-green);
        transform: translateX(5px);
    }

    .nav-link:hover i {
        transform: scale(1.1);
    }

    .nav-link.active {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(26, 77, 46, 0.2);
    }

    .nav-link.active i {
        color: white;
    }

    .nav-badge {
        margin-left: auto;
        background-color: #dc3545;
        color: white;
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        border-radius: 10px;
        font-weight: 600;
    }

    .sidebar-bottom {
        position: absolute;
        bottom: 0;
        width: 100%;
        padding: 1rem;
        border-top: 1px solid #e9ecef;
        background-color: white;
    }

    .user-profile {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .user-profile:hover {
        background-color: var(--hover-bg);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        margin-right: 0.75rem;
        font-size: 0.9rem;
    }

    .user-info h6 {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .user-info p {
        margin: 0;
        font-size: 0.75rem;
        color: #6c757d;
    }

    .sidebar-toggle {
        display: none;
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1001;
        background: var(--primary-green);
        color: white;
        border: none;
        padding: 0.75rem;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .sidebar-toggle {
            display: block;
        }
    }
</style>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
    }

    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.querySelector('.sidebar-toggle');

        if (window.innerWidth <= 768) {
            if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                sidebar.classList.remove('show');
            }
        }
    });
</script>
