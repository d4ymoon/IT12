<!-- Sidebar Only -->
<nav class="sidebar {{ session('role_id') != 1 ? 'collapsed' : '' }}">
    <div class="sidebar-content">
        <div class="logo-container">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('images/atin_logo.png') }}" alt="Company Logo" class="img-fluid me-3 sidebar-logo" style="max-height: 50px;">
                    <div class="logo-text">
                        <div class="fw-bold fs-4 mb-0" style="color: var(--congress-blue);">ATIN</div>
                        <div class="small text-muted mb-0" style="line-height: 1.2;">
                            Industrial Hardware<br>Supply Inc.
                        </div>
                    </div>
                </div>
                @if(session('role_id') != 1)
                {{-- Toggle button hidden for employees (always collapsed) --}}
                @else
                {{-- Toggle button hidden for admins (always expanded) --}}
                @endif
            </div>
        </div>
        
        <ul class="nav flex-column">
            <!-- Dashboard - Show for both roles -->
            @if(session('role_id') == 1)
            <li class="nav-item">
                <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-3"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @endif

            <!-- POS - Show for both roles -->
            <li class="nav-item">
                <a href="{{ route('pos.index') }}" class="nav-link {{ request()->is('pos') ? 'active' : '' }}" title="POS">
                    <i class="bi bi-cash-stack me-3"></i>
                    <span>POS</span>
                </a>
            </li>

            <!-- Products Menu - Admin Only -->
            @if(session('role_id') == 1)
            <li class="nav-item">
                <a href="#collapseInventory" class="nav-link collapsed" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapseInventory">
                    <i class="bi bi-boxes me-3"></i>
                    <span class="pe-2">Products</span>
                    <i class="bi bi-chevron-down ms-auto chevron"></i> 
                </a>
                
                <div class="collapse {{ request()->is('products*') || request()->is('product-prices*') || request()->is('categories*') || request()->is('suppliers*') ? 'show' : '' }}" id="collapseInventory">
                    <ul class="nav flex-column ps-3">
                        <li class="nav-item">
                            <a href="{{ route('products.index') }}" class="nav-link {{ request()->is('products*') ? 'active' : '' }}">
                                <i class="bi bi-box-seam me-3"></i>
                                <span>Products</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('product-prices.index') }}" class="nav-link {{ request()->is('product-prices*') ? 'active' : '' }}">
                                <i class="bi bi-cash-stack me-3"></i>
                                <span>Product Prices</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->is('categories*') ? 'active' : '' }}">
                                <i class="bi bi-funnel me-3"></i>
                                <span>Categories</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->is('suppliers*') ? 'active' : '' }}">
                                <i class="bi bi-truck me-3"></i>
                                <span>Suppliers</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Inventory Management - Admin Only -->
            <li class="nav-item">
                <a href="#collapseInventoryOps" class="nav-link collapsed pe-1" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapseInventoryOps">
                    <i class="bi bi-box-seam me-3"></i>
                    <span class="pe-2">Inventory Management</span>
                    <i class="bi bi-chevron-down ms-auto chevron"></i>
                </a>
                <div class="collapse {{ request()->is('stock-ins*') || request()->is('stock-adjustments*') || request()->is('returns*') ? 'show' : '' }}" id="collapseInventoryOps">
                    <ul class="nav flex-column ps-3">
                        <li class="nav-item">
                            <a href="{{ route('stock-ins.index') }}" class="nav-link {{ request()->is('stock-ins*') ? 'active' : '' }}">
                                <i class="bi bi-box-arrow-in-down me-3"></i>
                                <span>Stock In</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('stock-adjustments.index') }}" class="nav-link {{ request()->is('stock-adjustments*') ? 'active' : '' }}">
                                <i class="bi bi-sliders me-3"></i>
                                <span>Stock Adjustments</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('returns.index') }}" class="nav-link {{ request()->is('returns*') ? 'active' : '' }}">
                                <i class="bi bi-arrow-counterclockwise me-3"></i>
                                <span>Returns</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


            <!-- Reports Menu - Admin Only -->
            <li class="nav-item">
                <a href="#collapseReports" class="nav-link {{ request()->is('reports*') ? '' : 'collapsed' }}" 
                    data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->is('reports*') ? 'true' : 'false' }}" aria-controls="collapseReports">
                    <i class="bi bi-file-text me-3"></i> <span class="pe-2">Reports</span> <i class="bi bi-chevron-down ms-auto chevron"></i> 
                </a>
                <div class="collapse {{ request()->is('reports*') ? 'show' : '' }}" id="collapseReports">
                    <ul class="nav flex-column ps-3">
                        <li class="nav-item">
                            <a href="{{ route('reports.sales.index') }}" class="nav-link {{ request()->is('reports/sales*') ? 'active' : '' }}">
                                <i class="bi bi-graph-up me-3"></i>
                                <span>Sales Reports</span>
                            </a>                            
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.inventory.index') }}" class="nav-link {{ request()->is('reports/inventory*') ? 'active' : '' }}">
                                <i class="bi bi-box-seam me-3"></i>
                                <span>Inventory Reports</span>
                            </a>                            
                        </li>
                    </ul>
                </div>
            </li>

            <!-- User Management Menu - Admin Only -->
            <li class="nav-item">
                <a href="#collapseUser" class="nav-link collapsed" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapseUser">
                    <i class="bi bi-people me-3"></i>
                    <span class="pe-2">User Management</span>
                    <i class="bi bi-chevron-down ms-auto chevron"></i> 
                </a>
                <div class="collapse {{ request()->is('users*') || request()->is('roles*') ? 'show' : '' }}" id="collapseUser">
                    <ul class="nav flex-column ps-3">
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                                <i class="bi bi-people me-3"></i>
                                <span>Users</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}" class="nav-link {{ request()->is('roles*') ? 'active' : '' }}">
                                <i class="bi bi-person-badge me-3"></i>
                                <span>Roles</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif            
        </ul>
    </div> <!-- End sidebar-content -->

    <div class="p-3 border-top border-secondary sidebar-footer">
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle sidebar-user-link" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-avatar me-3">
                    {{ strtoupper(substr(session('user_name'), 0, 1)) }}
                </div>
                <div class="flex-grow-1 sidebar-user-info">
                    <div class="fw-bold small" style="color: var(--congress-blue);">{{ session('user_name') }}</div>
                    <small class="text-muted">{{ session('user_role') }}</small>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu shadow">
                <li>
                    <a class="dropdown-item" href="{{ route('account.settings') }}">
                        <i class="bi bi-gear me-2"></i>Settings
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="/logout" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>