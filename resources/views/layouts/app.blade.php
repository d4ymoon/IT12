<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ATIN Admin')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-icons.css') }}" rel="stylesheet">

    <!-- Select 2 CSS -->
    <link href="{{ asset('css/vendor/select2.min.css') }}" rel="stylesheet">
    
<style>
    /* Company Color Variables */
    :root {
        --congress-blue: #06448a;
        --amber: #fac307;
        --white: #f8f9fa;
        --monza: #e20615;
        --sidebar-width: 280px;
        --sidebar-width-collapsed: 80px;
    }
    
    .sidebar {
        background: #f8f9fa;
        color: #333;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        width: var(--sidebar-width);
        max-width: var(--sidebar-width);
        padding-top: 20px;
        box-shadow: 3px 0 10px rgba(0,0,0,0.1);
        z-index: 900;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border-right: 1px solid #e9ecef;
        transition: none !important;
        box-sizing: border-box;
    }
    
    /* After page loads, re-enable transitions */
    .sidebar.transitions-enabled {
        transition: width 0.3s ease !important;
    }
    
    /* Toggle Button Styles - OUTSIDE SIDEBAR */
    .sidebar-toggle-btn {
        position: fixed;
        top: 20px;
        background: white;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        z-index: 910;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        font-size: 1rem;
        color: var(--congress-blue);
        transition: none !important;
        /* Initial position - will be updated by JavaScript */
        left: 265px; /* 280px - 15px */
    }
    
    /* When transitions are enabled */
    .sidebar-toggle-btn.transitions-enabled {
        transition: left 0.3s ease !important;
    }
    
    .sidebar-toggle-btn:hover {
        background: var(--congress-blue);
        color: white;
        transform: scale(1.1);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
    }
    
    .sidebar.collapsed ~ .sidebar-toggle-btn i {
        transform: rotate(180deg);
    }
    
    /* MAIN CONTENT */
    .main-content {
        margin-left: var(--sidebar-width);
        width: calc(100vw - var(--sidebar-width));
        max-width: calc(100vw - var(--sidebar-width));
        height: 100vh;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 20px;
        background: #f8f9fa;
        transition: none !important;
        box-sizing: border-box;
    }
    
    .main-content.transitions-enabled {
        transition: margin-left 0.3s ease, width 0.3s ease !important;
    }
    
    .sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding-bottom: 100px;
    }
    
    .sidebar-content::-webkit-scrollbar {
        width: 6px;
    }
    
    .sidebar-content::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 3px;
    }
    
    .sidebar-content::-webkit-scrollbar-thumb {
        background: #dee2e6;
        border-radius: 3px;
    }
    
    .sidebar-content::-webkit-scrollbar-thumb:hover {
        background: #adb5bd;
    }
    
    .sidebar .nav-link {
        color: #495057;
        padding: 12px 25px;
        margin: 4px 15px;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-weight: 500;
        border: none;
        position: relative;
    }
    
    .sidebar .nav-link:hover {
        background: #f8f9fa;
        color: var(--congress-blue);
        transform: translateX(5px);
    }
    
    .sidebar .nav-link.active {
        background: linear-gradient(135deg, var(--amber) 0%, #ffd43b 100%);
        color: var(--congress-blue);
        box-shadow: 0 4px 15px rgba(250, 195, 7, 0.3);
    }
    
    .sidebar .nav-link.collapsed {
        background: transparent;
    }
    
    .sidebar .nav-link .chevron {
        display: inline-block;
        transform-origin: center center;
        transition: transform 0.28s cubic-bezier(.4,0,.2,1);
        pointer-events: none;
        margin-left: auto;
    }

    .sidebar .nav-link[aria-expanded="true"] .chevron,
    .sidebar .nav-link.expanded .chevron {
        transform: rotate(180deg);
    }
                    
    .sub-link {
        padding: 8px 15px 8px 35px !important;
        font-size: 0.9rem;
        margin: 2px 15px !important;
    }
    
    .sub-icon {
        font-size: 0.5rem;
    }
    
    .main-iframe {
        margin-left: var(--sidebar-width);
        width: calc(100vw - var(--sidebar-width));
        height: 100vh;
        border: none;
        transition: margin-left 0.3s ease, width 0.3s ease;
    }
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--amber);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-weight: bold;
        font-size: 16px;
        border: 3px solid #e9ecef;
    }
    
    .logo-container {
        padding: 0 25px 25px 25px;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 20px;
        position: relative;
    }
    
    /* Collapsed Sidebar Styles */
    .sidebar.collapsed {
        width: var(--sidebar-width-collapsed);
        max-width: var(--sidebar-width-collapsed);
    }
    
    .sidebar.collapsed .logo-text {
        opacity: 0;
        visibility: hidden;
        position: absolute;
    }
    
    .sidebar.collapsed .sidebar-logo {
        margin-right: 0 !important;
        max-width: 40px;
    }
    
    .sidebar.collapsed .nav-link {
        padding: 15px !important;
        margin: 8px 10px !important;
        text-align: center !important;
        justify-content: center !important;
    }
    
    .sidebar.collapsed .nav-link span {
        opacity: 0;
        visibility: hidden;
        position: absolute;
    }
    
    .sidebar.collapsed .nav-link i {
        margin-right: 0 !important;
        font-size: 1.2rem;
    }
    
    /* Show chevron for dropdown parents in collapsed mode */
    .sidebar.collapsed .nav-link[data-bs-toggle="collapse"] .chevron {
        display: block !important;
        position: absolute;
        bottom: 2px;
        right: 2px;
        font-size: 0.6rem;
        color: #000000ff;
        margin-left: 0;
    }
    
    /* Visual feedback for active dropdown parent in collapsed mode */
    .sidebar.collapsed .nav-link[data-bs-toggle="collapse"][aria-expanded="true"] {
        background: rgba(250, 195, 7, 0.2) !important;
        border: 1px solid rgba(250, 195, 7, 0.3);
        position: relative;
    }
    
    /* Show collapse content in collapsed sidebar */
    .sidebar.collapsed .collapse {
        display: none;
    }
    
    /* Show when collapsed AND has .show class */
    .sidebar.collapsed .collapse.show {
        display: block !important;
        position: static !important;
        background: transparent !important;
        box-shadow: none !important;
        width: auto !important;
        height: auto !important;
        margin-top: 5px;
        margin-bottom: 10px;
    }
    
    /* Style for collapsed submenu items */
    .sidebar.collapsed .collapse.show .nav {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    
    .sidebar.collapsed .collapse.show .nav-item {
        margin-bottom: 3px;
    }
    
    .sidebar.collapsed .collapse.show .nav-link {
        padding: 8px 10px !important;
        margin: 2px 5px !important;
        text-align: center !important;
        border-radius: 6px;
        justify-content: center !important;
    }
    
    .sidebar.collapsed .collapse.show .nav-link i {
        margin-right: 0 !important;
        font-size: 1rem !important;
    }
    
    .sidebar.collapsed .collapse.show .nav-link span {
        display: none;
    }
    
    .sidebar.collapsed .collapse.show .nav-link:hover {
        background: rgba(250, 195, 7, 0.2);
    }
    
    .sidebar.collapsed .collapse.show .nav-link.active {
        background: rgba(250, 195, 7, 0.3);
    }
    
    .sidebar.collapsed .sidebar-user-info {
        opacity: 0;
        visibility: hidden;
        position: absolute;
    }
    
    .sidebar.collapsed .sidebar-user-link {
        justify-content: center;
        width: 100%;
    }
    
    .sidebar.collapsed .user-avatar {
        margin-right: 0 !important;
        margin-left: 0 !important;
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        min-height: 40px !important;
        flex-shrink: 0;
    }
    
    .sidebar.collapsed .sidebar-footer {
        padding: 15px 10px !important;
    }
    
    .sidebar.collapsed .dropdown {
        position: static;
    }
    
    /* Only apply fixed positioning when sidebar is collapsed */
    .sidebar.collapsed .dropdown-menu.show {
        position: fixed !important;
        left: var(--sidebar-width-collapsed) !important;
        transform: none !important;
        margin-top: 0 !important;
        z-index: 920 !important;
        min-width: 200px;
    }
    
    /* Reset dropdown positioning for expanded sidebar (admin view) */
    .sidebar:not(.collapsed) .dropdown-menu {
        position: absolute !important;
        left: auto !important;
        transform: translateX(0) !important;
    }
    
    .sidebar.collapsed ~ .main-content,
    .sidebar.collapsed ~ .main-iframe {
        margin-left: var(--sidebar-width-collapsed);
        width: calc(100vw - var(--sidebar-width-collapsed));
        max-width: calc(100vw - var(--sidebar-width-collapsed));
    }

    /* Make sure these are very specific */
    nav.sidebar.collapsed {
        width: 80px !important;
    }

    nav.sidebar:not(.collapsed) {
        width: 280px !important;
    }
    
    .notification-badge {
        background: var(--monza);
        color: var(--white);
    }
    
    /* Ensure modals work properly */
    *, *::before, *::after {
        box-sizing: border-box;
    }
    
    html, body {
        overflow-x: hidden;
        position: relative;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    
    .modal-backdrop {
        z-index: 1040 !important;
    }
    
    .modal {
        z-index: 1050 !important;
    }
    
    .modal.show {
        display: block !important;
    }
    
    .modal-dialog {
        position: relative;
        z-index: 1055 !important;
    }
    
    @media (max-width: 992px) {
        .sidebar {
            width: var(--sidebar-width-collapsed);
        }
        
        .sidebar .nav-link span {
            opacity: 0;
            visibility: hidden;
            position: absolute;
        }
        
        .sidebar .logo-text {
            opacity: 0;
            visibility: hidden;
            position: absolute;
        }
        
        .sidebar .nav-link i {
            margin-right: 0 !important;
            font-size: 1.2rem;
        }
        
        .sidebar .nav-link {
            padding: 15px;
            margin: 8px 10px;
            text-align: center;
        }
        
        .main-iframe,
        .main-content {
            margin-left: var(--sidebar-width-collapsed);
            width: calc(100vw - var(--sidebar-width-collapsed));
        }
        
        /* Hide toggle button on mobile since sidebar is always collapsed */
        .sidebar-toggle-btn {
            display: none;
        }
    }
</style>
    
    @stack('styles')
</head>
<body>

   <!-- Default Password Reminder Modal -->
    <div class="modal fade" id="defaultPasswordModal" tabindex="-1" aria-labelledby="defaultPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-2 shadow-sm">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="defaultPasswordModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Security Reminder
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <p class="fs-6">
                        You are currently using your <strong>default password</strong>.
                    </p>
                    <p>
                        For your account security, we strongly recommend updating it as soon as possible.
                    </p>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-outline-secondary" id="dismissDefaultPasswordModal">Later</button>
                    <a href="{{ route('account.settings') }}" class="btn btn-warning fw-semibold" id="changeNowBtn">Change Password</a>
                </div>
            </div>
        </div>
    </div>

    @include('components.sidebar')
    
    <!-- Everything else is one big iframe -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Modals Section (outside main-content to avoid z-index stacking issues) -->
    @stack('modals')

    <!-- Bootstrap JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <!-- Select 2 JS -->
    <script src="{{ asset('js/vendor/jquery.min.js') }}"></script>
    <script src="{{ asset('js/vendor/select2.min.js') }}"></script>
    
<script>
        // Set sidebar state IMMEDIATELY when script loads (BEFORE DOMContentLoaded)
        (function() {
            const isEmployee = {{ session('user_role') == 'Cashier' ? 'true' : 'false' }};
            
            // Function to set cookie (defined at top level)
            function setCookie(name, value, days) {
                let expires = "";
                if (days) {
                    const date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            }
            
            // Function to get cookie
            function getCookie(name) {
                const nameEQ = name + "=";
                const ca = document.cookie.split(';');
                for(let i = 0; i < ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
                }
                return null;
            }
            
            // Get saved state from localStorage (client-side preference)
            const savedState = localStorage.getItem('sidebarCollapsed');
            
            // Get state from cookie (PHP-readable)
            const cookieState = getCookie('sidebarCollapsed');
            
            // Only force collapsed state for employees if NO preference exists at all
            if (isEmployee && savedState === null && cookieState === null) {
                // First time visit for employee - default to collapsed
                localStorage.setItem('sidebarCollapsed', 'true');
                setCookie('sidebarCollapsed', 'true', 7);
            } else {
                // Sync between localStorage and cookie for consistency
                if (savedState !== null) {
                    setCookie('sidebarCollapsed', savedState, 7);
                } else if (cookieState !== null) {
                    localStorage.setItem('sidebarCollapsed', cookieState);
                }
            }
        })();

        document.addEventListener('DOMContentLoaded', function() {
            // ========== POSITION TOGGLE BUTTON ==========
            function positionToggleButton() {
                const toggleBtn = document.getElementById('sidebarToggle');
                const sidebar = document.querySelector('.sidebar');
                
                if (!toggleBtn || !sidebar) return;
                
                if (sidebar.classList.contains('collapsed')) {
                    toggleBtn.style.left = '65px'; // 80px - 15px
                } else {
                    toggleBtn.style.left = '265px'; // 280px - 15px
                }
            }
            
            // Position toggle button initially
            positionToggleButton();
            
            // ========== HANDLE COLLAPSED DROPDOWNS ==========
            function handleCollapsedDropdowns() {
                const sidebar = document.querySelector('.sidebar');
                if (!sidebar || !sidebar.classList.contains('collapsed')) return;
                
                // When a dropdown is opened in collapsed mode
                document.querySelectorAll('.sidebar .collapse').forEach(collapse => {
                    collapse.addEventListener('show.bs.collapse', function(e) {
                        // Don't let Bootstrap close other dropdowns in collapsed mode
                        e.stopPropagation();
                        
                        // Close other open dropdowns manually
                        document.querySelectorAll('.sidebar .collapse.show').forEach(otherCollapse => {
                            if (otherCollapse !== this) {
                                const bsCollapse = bootstrap.Collapse.getInstance(otherCollapse);
                                if (bsCollapse) bsCollapse.hide();
                            }
                        });
                    });
                });
            }
            
            const modalEl = document.getElementById('defaultPasswordModal');

            // Check the session flag that you set in login controller
            const shouldShowModal = {{ session('show_default_password_modal') ? 'true' : 'false' }};

            if (modalEl && shouldShowModal) {
                console.log('Showing default password modal from session flag');
                const defaultModal = new bootstrap.Modal(modalEl);
                defaultModal.show();

                // Clear the session flag so it doesn't show again on page refresh
                fetch('{{ route("clear-password-modal-flag") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).catch(err => console.log('Flag clear request failed:', err));

                // Later button hides modal
                document.getElementById('dismissDefaultPasswordModal').addEventListener('click', function() {
                    defaultModal.hide();
                });

                // Change Now button navigates to settings
                document.getElementById('changeNowBtn').addEventListener('click', function() {
                    // The href will handle navigation
                });
            }

            // ========== SIDEBAR TOGGLE ==========
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggleBtn = document.getElementById('sidebarToggle');
            const isEmployee = {{ session('user_role') == 'Cashier' ? 'true' : 'false' }};
            
            // Function to set cookie (available in this scope too)
            function setCookie(name, value, days) {
                let expires = "";
                if (days) {
                    const date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            }
            
            // Simple toggle function
            function toggleSidebar() {
                
                sidebar.classList.toggle('collapsed');
                
                const icon = sidebarToggleBtn.querySelector('i');
                if (sidebar.classList.contains('collapsed')) {
                    icon.classList.remove('bi-chevron-left');
                    icon.classList.add('bi-chevron-right');
                } else {
                    icon.classList.remove('bi-chevron-right');
                    icon.classList.add('bi-chevron-left');
                }
                
                // Update toggle button position
                positionToggleButton();
                
                // Handle dropdowns for collapsed mode
                setTimeout(handleCollapsedDropdowns, 10);
                
                // Save state
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
                setCookie('sidebarCollapsed', isCollapsed, 7);
            }
            
            // Add click event to toggle button
            if (sidebarToggleBtn) {
                sidebarToggleBtn.addEventListener('click', toggleSidebar);
            }
            
            // Initialize collapsed dropdown handling
            handleCollapsedDropdowns();
            
            // ========== TOOLTIP INITIALIZATION ==========
            function initializeTooltips() {
                const sidebar = document.querySelector('.sidebar');
                const tooltipTriggerList = document.querySelectorAll('.sidebar .nav-link[title]');
                
                tooltipTriggerList.forEach(el => {
                    const existingTooltip = bootstrap.Tooltip.getInstance(el);
                    if (existingTooltip) {
                        existingTooltip.dispose();
                    }
                });
                
                if (sidebar && sidebar.classList.contains('collapsed')) {
                    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                        new bootstrap.Tooltip(tooltipTriggerEl, {
                            placement: 'right',
                            trigger: 'hover'
                        });
                    });
                    
                    // Also add tooltips for dropdown parent links
                    const dropdownParents = document.querySelectorAll('.sidebar.collapsed .nav-link[data-bs-toggle="collapse"]');
                    dropdownParents.forEach(function(parentLink) {
                        const span = parentLink.querySelector('span');
                        if (span) {
                            new bootstrap.Tooltip(parentLink, {
                                placement: 'right',
                                trigger: 'hover',
                                title: span.textContent
                            });
                        }
                    });
                }
            }
            
            initializeTooltips();
            
            // ========== ENABLE TRANSITIONS AFTER PAGE LOAD ==========
            setTimeout(() => {
                const sidebar = document.querySelector('.sidebar');
                const mainContent = document.querySelector('.main-content');
                const toggleBtn = document.getElementById('sidebarToggle');
                
                if (sidebar) sidebar.classList.add('transitions-enabled');
                if (mainContent) mainContent.classList.add('transitions-enabled');
                if (toggleBtn) toggleBtn.classList.add('transitions-enabled');
            }, 100);
        });
    </script>
    @stack('scripts')
</body>
</html>