<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATIN Admin - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --congress-blue: #06448a;
            --amber: #fac307;
            --white: #f8f9fa;
            --monza: #e20615;
            --success: #28a745;
            --warning: #ffc107;
            --info: #17a2b8;
        }
        
        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            overflow: hidden;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background-color: var(--congress-blue);
            color: white;
            font-weight: 600;
            border-bottom: none;
        }
        
        .stat-card {
            text-align: center;
            padding: 20px;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 15px;
            opacity: 0.8;
        }
        
        .bg-sales {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .bg-inventory {
            background: linear-gradient(135deg, #17a2b8, #6f42c1);
            color: white;
        }
        
        .bg-products {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
        }
        
        .bg-users {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }
        
        .low-stock {
            background-color: rgba(255, 193, 7, 0.1);
            border-left: 4px solid var(--warning);
        }
        
        .out-of-stock {
            background-color: rgba(220, 53, 69, 0.1);
            border-left: 4px solid var(--danger);
        }
        
        .recent-activity {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .activity-item {
            padding: 10px 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .activity-sale {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }
        
        .activity-stock {
            background-color: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
        }
        
        .activity-user {
            background-color: rgba(108, 117, 125, 0.2);
            color: #6c757d;
        }
        
        .activity-product {
            background-color: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--congress-blue);
        }
        
        .badge-low {
            background-color: var(--warning);
            color: #212529;
        }
        
        .badge-out {
            background-color: var(--monza);
            color: white;
        }
        
        .main-content {
            margin-left: 280px;
            width: calc(100vw - 280px);
            min-height: 100vh;
            padding: 20px;
            background: #f8f9fa;
        }
        
        
    </style>
</head>
<body>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-congress-blue">Dashboard</h2>
            <div class="d-flex">
                <div class="input-group me-2" style="width: 300px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" placeholder="Search...">
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Today</a></li>
                        <li><a class="dropdown-item" href="#">This Week</a></li>
                        <li><a class="dropdown-item" href="#">This Month</a></li>
                        <li><a class="dropdown-item" href="#">This Year</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card bg-sales text-white">
                    <div class="card-body stat-card">
                        <i class="bi bi-currency-dollar stat-icon"></i>
                        <div class="stat-value">₱24,580</div>
                        <div class="stat-label">Total Sales Today</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card bg-inventory text-white">
                    <div class="card-body stat-card">
                        <i class="bi bi-box-seam stat-icon"></i>
                        <div class="stat-value">1,248</div>
                        <div class="stat-label">Items in Stock</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card bg-products text-white">
                    <div class="card-body stat-card">
                        <i class="bi bi-tags stat-icon"></i>
                        <div class="stat-value">42</div>
                        <div class="stat-label">Products Low Stock</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card bg-users text-white">
                    <div class="card-body stat-card">
                        <i class="bi bi-people stat-icon"></i>
                        <div class="stat-value">18</div>
                        <div class="stat-label">Active Users</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Data -->
        <div class="row mb-4">
            <div class="col-md-8 mb-3">
                <div class="card dashboard-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Sales Overview</span>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-light active">Daily</button>
                            <button type="button" class="btn btn-outline-light">Weekly</button>
                            <button type="button" class="btn btn-outline-light">Monthly</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card">
                    <div class="card-header">Top Selling Products</div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="topProductsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card dashboard-card">
                    <div class="card-header">Low Stock Alert</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Current Stock</th>
                                        <th>Reorder Level</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="low-stock">
                                        <td>Wireless Mouse</td>
                                        <td>Electronics</td>
                                        <td>8</td>
                                        <td>10</td>
                                        <td><span class="badge badge-low">Low Stock</span></td>
                                    </tr>
                                    <tr class="low-stock">
                                        <td>Notebook A4</td>
                                        <td>Stationery</td>
                                        <td>5</td>
                                        <td>15</td>
                                        <td><span class="badge badge-low">Low Stock</span></td>
                                    </tr>
                                    <tr class="out-of-stock">
                                        <td>Blue Pen</td>
                                        <td>Stationery</td>
                                        <td>0</td>
                                        <td>20</td>
                                        <td><span class="badge badge-out">Out of Stock</span></td>
                                    </tr>
                                    <tr class="low-stock">
                                        <td>USB-C Cable</td>
                                        <td>Electronics</td>
                                        <td>12</td>
                                        <td>15</td>
                                        <td><span class="badge badge-low">Low Stock</span></td>
                                    </tr>
                                    <tr class="low-stock">
                                        <td>Stapler</td>
                                        <td>Office Supplies</td>
                                        <td>3</td>
                                        <td>5</td>
                                        <td><span class="badge badge-low">Low Stock</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card dashboard-card">
                    <div class="card-header">Recent Activity</div>
                    <div class="card-body p-0 recent-activity">
                        <div class="activity-item d-flex">
                            <div class="activity-icon activity-sale">
                                <i class="bi bi-cart-check"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">New Sale Completed</div>
                                <div class="text-muted small">Order #4582 for ₱1,250.00</div>
                                <div class="text-muted small">2 minutes ago</div>
                            </div>
                        </div>
                        <div class="activity-item d-flex">
                            <div class="activity-icon activity-stock">
                                <i class="bi bi-box-arrow-in-down"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Stock In Received</div>
                                <div class="text-muted small">45 units of Wireless Headphones</div>
                                <div class="text-muted small">1 hour ago</div>
                            </div>
                        </div>
                        <div class="activity-item d-flex">
                            <div class="activity-icon activity-user">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">New User Added</div>
                                <div class="text-muted small">Maria Santos (Cashier)</div>
                                <div class="text-muted small">3 hours ago</div>
                            </div>
                        </div>
                        <div class="activity-item d-flex">
                            <div class="activity-icon activity-product">
                                <i class="bi bi-tag"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Product Price Updated</div>
                                <div class="text-muted small">USB-C Cable price changed to ₱350.00</div>
                                <div class="text-muted small">5 hours ago</div>
                            </div>
                        </div>
                        <div class="activity-item d-flex">
                            <div class="activity-icon activity-sale">
                                <i class="bi bi-cart-check"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">New Sale Completed</div>
                                <div class="text-muted small">Order #4581 for ₱2,840.00</div>
                                <div class="text-muted small">Yesterday, 4:30 PM</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Recent Sales</span>
                        <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sale ID</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Total Amount</th>
                                        <th>Payment Method</th>
                                        <th>Cashier</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#4582</td>
                                        <td>Today, 10:25 AM</td>
                                        <td>Juan Dela Cruz</td>
                                        <td>3</td>
                                        <td>₱1,250.00</td>
                                        <td><span class="badge bg-success">Cash</span></td>
                                        <td>Maria Santos</td>
                                    </tr>
                                    <tr>
                                        <td>#4581</td>
                                        <td>Yesterday, 4:30 PM</td>
                                        <td>Ana Reyes</td>
                                        <td>5</td>
                                        <td>₱2,840.00</td>
                                        <td><span class="badge bg-info">GCash</span></td>
                                        <td>John Doe</td>
                                    </tr>
                                    <tr>
                                        <td>#4580</td>
                                        <td>Yesterday, 2:15 PM</td>
                                        <td>Robert Lim</td>
                                        <td>2</td>
                                        <td>₱1,580.00</td>
                                        <td><span class="badge bg-primary">Card</span></td>
                                        <td>Maria Santos</td>
                                    </tr>
                                    <tr>
                                        <td>#4579</td>
                                        <td>Yesterday, 11:40 AM</td>
                                        <td>Lisa Garcia</td>
                                        <td>4</td>
                                        <td>₱3,120.00</td>
                                        <td><span class="badge bg-success">Cash</span></td>
                                        <td>John Doe</td>
                                    </tr>
                                    <tr>
                                        <td>#4578</td>
                                        <td>Oct 25, 3:20 PM</td>
                                        <td>Michael Tan</td>
                                        <td>1</td>
                                        <td>₱850.00</td>
                                        <td><span class="badge bg-info">GCash</span></td>
                                        <td>Maria Santos</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: ['8 AM', '10 AM', '12 PM', '2 PM', '4 PM', '6 PM', '8 PM'],
                    datasets: [{
                        label: 'Sales (₱)',
                        data: [3200, 4500, 5800, 6200, 5100, 3800, 2100],
                        borderColor: '#06448a',
                        backgroundColor: 'rgba(6, 68, 138, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Top Products Chart
            const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
            const topProductsChart = new Chart(topProductsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Wireless Mouse', 'USB-C Cable', 'Notebook', 'Blue Pen', 'Stapler'],
                    datasets: [{
                        data: [35, 25, 20, 15, 5],
                        backgroundColor: [
                            '#06448a',
                            '#fac307',
                            '#28a745',
                            '#17a2b8',
                            '#6c757d'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Auto-expand sidebar sections based on current page
            const currentPath = window.location.pathname;
            if (currentPath.includes('/roles') || currentPath.includes('/users')) {
                const userCollapse = document.getElementById('collapseUser');
                if (userCollapse) {
                    userCollapse.classList.add('show');
                    const trigger = document.querySelector('[aria-controls="collapseUser"]');
                    if (trigger) {
                        trigger.classList.remove('collapsed');
                    }
                }
            }
            
            // Auto-expand inventory section if on related pages
            if (currentPath.includes('/products') || currentPath.includes('/categories') || currentPath.includes('/suppliers')) {
                const inventoryCollapse = document.getElementById('collapseInventory');
                if (inventoryCollapse) {
                    inventoryCollapse.classList.add('show');
                    const trigger = document.querySelector('[aria-controls="collapseInventory"]');
                    if (trigger) {
                        trigger.classList.remove('collapsed');
                    }
                }
            }
        });
    </script>
</body>
</html>