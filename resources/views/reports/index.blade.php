<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Reports System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --info-color: #9b59b6;
            --light-gray: #f8f9fa;
            --border-color: #dee2e6;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .dashboard-header {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
            margin-bottom: 1.5rem;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .stat-card {
            text-align: center;
            padding: 1.5rem;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .table-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        .table th {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 1rem;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        .status-in-stock {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .status-out-of-stock {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .status-completed {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .status-pending {
            background-color: rgba(243, 156, 18, 0.1);
            color: var(--warning-color);
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .status-active {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--primary-color);
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .nav-tabs .nav-link {
            color: var(--secondary-color);
            font-weight: 500;
            border: none;
            padding: 0.75rem 1.5rem;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
            background-color: transparent;
        }
        
        .filter-section {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .form-control, .form-select {
            padding: 0.75rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }
        
        .total-value {
            font-weight: 700;
            color: var(--secondary-color);
        }
        
        .product-code {
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .alert-warning {
            background-color: rgba(243, 156, 18, 0.1);
            border: 1px solid rgba(243, 156, 18, 0.2);
            color: var(--warning-color);
            border-radius: 8px;
        }
        
        .module-content {
            display: none;
        }
        
        .module-content.active {
            display: block;
        }
        
        @media (max-width: 768px) {
            .stat-value {
                font-size: 2rem;
            }
            
            .table-container {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Inventory Reports</h1>
                <div class="d-flex align-items-center">
                    <span class="me-3 text-muted">Welcome, Admin</span>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> Account
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="h5 mb-3">Select a report type to view and export data</h2>
                <ul class="nav nav-tabs" id="reportTabs">
                    <li class="nav-item">
                        <a class="nav-link {{ $activeModule === 'inventory' ? 'active' : '' }}" href="#" data-module="inventory">Inventory</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeModule === 'purchase' ? 'active' : '' }}" href="#" data-module="purchase">Purchase</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeModule === 'sales' ? 'active' : '' }}" href="#" data-module="sales">Sales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeModule === 'supplier' ? 'active' : '' }}" href="#" data-module="supplier">Supplier</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeModule === 'user' ? 'active' : '' }}" href="#" data-module="user">User</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Inventory Module -->
        <div id="inventory-module" class="module-content {{ $activeModule === 'inventory' ? 'active' : '' }}">
            <form action="{{ route('reports.index') }}" method="GET">
                <input type="hidden" name="module" value="inventory">
                <div class="filter-section">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="dateFrom" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="dateFrom" name="dateFrom" value="{{ request('dateFrom', '2024-01-01') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="dateTo" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="dateTo" name="dateTo" value="{{ request('dateTo', '2024-12-31') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="All Categories" {{ request('category', 'All Categories') === 'All Categories' ? 'selected' : '' }}>All Categories</option>
                                <option value="Electronics" {{ request('category') === 'Electronics' ? 'selected' : '' }}>Electronics</option>
                                <option value="Furniture" {{ request('category') === 'Furniture' ? 'selected' : '' }}>Furniture</option>
                                <option value="Office Supplies" {{ request('category') === 'Office Supplies' ? 'selected' : '' }}>Office Supplies</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 mb-1">
                                <i class="fas fa-filter me-2"></i> Apply Filters
                            </button>
                            <a href="{{ route('reports.export.pdf', ['module' => 'inventory']) }}?{{ http_build_query(request()->all()) }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-file-pdf me-2"></i> Export to PDF
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-value">{{ $totalItems }}</div>
                            <div class="stat-label">TOTAL ITEMS</div>
                            <small class="text-muted">Product types</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-value">{{ $totalQuantity }}</div>
                            <div class="stat-label">TOTAL QUANTITY</div>
                            <small class="text-muted">Units in stock</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-value">${{ number_format($totalValue, 2) }}</div>
                            <div class="stat-label">TOTAL VALUE</div>
                            <small class="text-muted">Inventory worth</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-value text-warning">{{ $lowStockItems }}</div>
                            <div class="stat-label">LOW STOCK ITEMS</div>
                            <small class="text-muted">Requires attention</small>
                        </div>
                    </div>
                </div>
            </div>

            @if($lowStockItems > 0)
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                    <strong>Attention needed:</strong> {{ $lowStockItems }} items are low in stock and need to be reordered.
                </div>
            </div>
            @endif

            <div class="table-container">
                <div class="p-3 border-bottom">
                    <h3 class="h5 mb-0">Inventory Details</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>PRODUCT CODE</th>
                                <th>PRODUCT NAME</th>
                                <th>CATEGORY</th>
                                <th>QUANTITY</th>
                                <th>UNIT PRICE</th>
                                <th>TOTAL VALUE</th>
                                <th>STATUS</th>
                                <th>LAST UPDATED</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventory as $item)
                            <tr>
                                <td><span class="product-code">{{ $item->product_code }}</span></td>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->category }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                <td class="total-value">${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                <td>
                                    @if($item->quantity > 0)
                                        <span class="status-in-stock">In Stock</span>
                                    @else
                                        <span class="status-out-of-stock">Out of Stock</span>
                                    @endif
                                </td>
                                <td>{{ $item->last_updated }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Purchase Module -->
        <div id="purchase-module" class="module-content {{ $activeModule === 'purchase' ? 'active' : '' }}">
            <form action="{{ route('reports.index') }}" method="GET">
                <input type="hidden" name="module" value="purchase">
                <div class="filter-section">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="purchaseDateFrom" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="purchaseDateFrom" name="dateFrom" value="{{ request('dateFrom', '2024-01-01') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="purchaseDateTo" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="purchaseDateTo" name="dateTo" value="{{ request('dateTo', '2024-12-31') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="purchaseStatus" class="form-label">Status</label>
                            <select class="form-select" id="purchaseStatus" name="status">
                                <option value="All Status" {{ request('status', 'All Status') === 'All Status' ? 'selected' : '' }}>All Status</option>
                                <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 mb-1">
                                <i class="fas fa-filter me-2"></i> Apply Filters
                            </button>
                            <a href="{{ route('reports.export.pdf', ['module' => 'purchase']) }}?{{ http_build_query(request()->all()) }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-file-pdf me-2"></i> Export to PDF
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-value">{{ $totalPurchases ?? 156 }}</div>
                            <div class="stat-label">TOTAL PURCHASES</div>
                            <small class="text-muted">All time</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-value">${{ number_format($totalSpent ?? 45230.75, 2) }}</div>
                            <div class="stat-label">TOTAL SPENT</div>
                            <small class="text-muted">This year</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-value">{{ $pendingOrders ?? 12 }}</div>
                            <div class="stat-label">PENDING ORDERS</div>
                            <small class="text-muted">Awaiting delivery</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-value">{{ $supplierCount ?? 8 }}</div>
                            <div class="stat-label">SUPPLIERS</div>
                            <small class="text-muted">Active vendors</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <div class="p-3 border-bottom">
                    <h3 class="h5 mb-0">Purchase Orders</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ORDER ID</th>
                                <th>SUPPLIER</th>
                                <th>PRODUCT</th>
                                <th>QUANTITY</th>
                                <th>UNIT COST</th>
                                <th>TOTAL COST</th>
                                <th>ORDER DATE</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases ?? [] as $purchase)
                            <tr>
                                <td><span class="product-code">{{ $purchase->order_id }}</span></td>
                                <td>{{ $purchase->supplier }}</td>
                                <td>{{ $purchase->product }}</td>
                                <td>{{ $purchase->quantity }}</td>
                                <td>${{ number_format($purchase->unit_cost, 2) }}</td>
                                <td class="total-value">${{ number_format($purchase->total_cost, 2) }}</td>
                                <td>{{ $purchase->order_date }}</td>
                                <td>
                                    @if($purchase->status === 'Completed')
                                        <span class="status-completed">{{ $purchase->status }}</span>
                                    @elseif($purchase->status === 'Pending')
                                        <span class="status-pending">{{ $purchase->status }}</span>
                                    @else
                                        <span class="status-out-of-stock">{{ $purchase->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add similar sections for Sales, Supplier, and User modules following the same pattern -->

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tab switching functionality
        document.querySelectorAll('#reportTabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                const module = this.getAttribute('data-module');
                const url = new URL(window.location.href);
                url.searchParams.set('module', module);
                window.location.href = url.toString();
            });
        });

        // Initialize date inputs with current date values
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const firstDayOfYear = new Date(new Date().getFullYear(), 0, 1).toISOString().split('T')[0];
            
            // Set default date values if not already set
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');
            
            if (dateFrom && !dateFrom.value) {
                dateFrom.value = firstDayOfYear;
            }
            if (dateTo && !dateTo.value) {
                dateTo.value = today;
            }
        });
    </script>
</body>
</html>
