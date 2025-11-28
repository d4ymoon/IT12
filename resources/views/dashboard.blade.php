@extends('layouts.app')

@section('title', 'ATIN Admin - Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: #06448a;">Dashboard</h2>
    <div class="d-flex">
        <div class="dropdown me-2">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-filter me-1"></i> 
                <span id="currentFilterLabel">
                    @if(request('filter_type') == 'custom')
                        Custom: {{ request('start_date') }} to {{ request('end_date') }}
                    @else
                        {{ ucfirst(request('filter', 'this month')) }}
                    @endif
                </span>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item filter-option" href="#" data-filter="today">Today</a></li>
                <li><a class="dropdown-item filter-option" href="#" data-filter="this_week">This Week</a></li>
                <li><a class="dropdown-item filter-option" href="#" data-filter="this_month">This Month</a></li>
                <li><a class="dropdown-item filter-option" href="#" data-filter="this_year">This Year</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#customDateModal">Custom Range</a></li>
            </ul>
        </div>
        @if(request('filter_type') == 'custom' || request('filter'))
        <a href="{{ route('dashboard') }}" class="btn btn-outline-danger">
            <i class="bi bi-x-circle me-1"></i> Clear Filter
        </a>
        @endif
    </div>
</div>

<!-- Key Metrics -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card dashboard-card bg-sales text-white">
            <div class="card-body stat-card">
                <i class="bi bi-currency-dollar stat-icon"></i>
                <div class="stat-value">₱{{ number_format($totalRevenue, 2) }}</div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card dashboard-card bg-success text-white">
            <div class="card-body stat-card">
                <i class="bi bi-graph-up stat-icon"></i>
                <div class="stat-value">₱{{ number_format($grossProfit, 2) }}</div>
                <div class="stat-label">Gross Profit</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card dashboard-card bg-info text-white">
            <div class="card-body stat-card">
                <i class="bi bi-cart-check stat-icon"></i>
                <div class="stat-value">₱{{ number_format($averageOrderValue, 2) }}</div>
                <div class="stat-label">Avg Order Value</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card dashboard-card bg-primary text-white">
            <div class="card-body stat-card">
                <i class="bi bi-box-seam stat-icon"></i>
                <div class="stat-value">₱{{ number_format($inventoryValue, 2) }}</div>
                <div class="stat-label">Inventory Value</div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Date Range Modal -->
<div class="modal fade" id="customDateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Custom Date Range</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('dashboard') }}">
                <div class="modal-body">
                    <input type="hidden" name="filter_type" value="custom">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ request('start_date', Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}" 
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ request('end_date', Carbon\Carbon::now()->format('Y-m-d')) }}" 
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Charts and Data -->
<div class="row mb-4">
    <div class="col-md-8 mb-3">
        <div class="card dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Sales Overview</span>
                <div class="btn-group btn-group-sm" id="salesChartType">
                    <button type="button" class="btn btn-outline-primary" data-type="daily">Daily</button>
                    <button type="button" class="btn btn-outline-primary active" data-type="weekly">Weekly</button>
                    <button type="button" class="btn btn-outline-primary" data-type="monthly">Monthly</button>
                    <button type="button" class="btn btn-outline-primary" data-type="yearly">Yearly</button>
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
            <div class="card-header">Sales by Category</div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card dashboard-card">
            <div class="card-header">Top 5 Bestselling Products</div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card dashboard-card">
            <div class="card-header">Total Sales Transactions</div>
            <div class="card-body text-center py-5">
                <div class="display-1 fw-bold text-primary">{{ $totalTransactions }}</div>
                <p class="text-muted">Transactions this period</p>
            </div>
        </div>
    </div>
</div>

<!-- Tables Section -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Low Stock Alerts</span>
                <span class="badge bg-danger">{{ $lowStockAlerts->count() }} Alerts</span>
            </div>
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
                            @forelse($lowStockAlerts as $product)
                                <tr class="{{ $product->current_stock == 0 ? 'out-of-stock' : 'low-stock' }}">
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category_name }}</td>
                                    <td>{{ $product->current_stock }}</td>
                                    <td>{{ $product->reorder_level }}</td>
                                    <td>
                                        @if($product->current_stock == 0)
                                            <span class="badge badge-out">Out of Stock</span>
                                        @else
                                            <span class="badge badge-low">Low Stock</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No low stock alerts</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Recent Voids & Adjustments</span>
                <span class="badge bg-warning">{{ $recentAdjustments->count() }} Recent</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Qty Change</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAdjustments as $adjustment)
                                <tr class="{{ $adjustment->quantity_change < 0 ? 'table-danger' : 'table-info' }}">
                                    <td>{{ $adjustment->adjustment_date->format('M d, H:i') }}</td>
                                    <td>{{ $adjustment->product_name }}</td>
                                    <td>{{ $adjustment->adjustment_type }}</td>
                                    <td>
                                        <span class="{{ $adjustment->quantity_change < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ $adjustment->quantity_change > 0 ? '+' : '' }}{{ $adjustment->quantity_change }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($adjustment->reason_notes, 30) }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No recent adjustments</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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
        background-color: #06448a;
        color: white;
        font-weight: 600;
        border-bottom: none;
    }
    
    .stat-card {
        text-align: center;
        padding: 20px;
    }
    
    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 10px 0;
    }
    
    .stat-label {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.9);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .stat-icon {
        font-size: 1.8rem;
        margin-bottom: 15px;
        opacity: 0.9;
    }
    
    .bg-sales {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }
    
    .low-stock {
        background-color: rgba(255, 193, 7, 0.1);
        border-left: 4px solid #ffc107;
    }
    
    .out-of-stock {
        background-color: rgba(220, 53, 69, 0.1);
        border-left: 4px solid #dc3545;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        color: #06448a;
    }
    
    .badge-low {
        background-color: #ffc107;
        color: #212529;
    }
    
    .badge-out {
        background-color: #e20615;
        color: white;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let salesChart;
        let topProductsChart;
        let categoryChart;

        // Filter functionality
        const filterOptions = document.querySelectorAll('.filter-option');
        const currentFilterLabel = document.getElementById('currentFilterLabel');
        
        filterOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                const filter = this.getAttribute('data-filter');
                
                // Update URL with filter parameter
                const url = new URL(window.location.href);
                url.searchParams.set('filter', filter);
                url.searchParams.delete('filter_type');
                url.searchParams.delete('start_date');
                url.searchParams.delete('end_date');
                
                window.location.href = url.toString();
            });
        });

        // Date validation for custom range
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', function() {
                endDateInput.min = this.value;
                if (new Date(endDateInput.value) < new Date(this.value)) {
                    endDateInput.value = this.value;
                }
            });
            
            endDateInput.addEventListener('change', function() {
                if (new Date(this.value) < new Date(startDateInput.value)) {
                    this.value = startDateInput.value;
                }
            });
        }

        // Sales Chart Type Selector
        const salesChartTypeButtons = document.querySelectorAll('#salesChartType button');
        salesChartTypeButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                salesChartTypeButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                // Update chart data based on selected type
                const chartType = this.getAttribute('data-type');
                updateSalesChart(chartType);
            });
        });

        // Initialize Charts
        initializeCharts();

        function initializeCharts() {
            // Sales Chart (Revenue Trend)
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: @json($salesTrend['labels']),
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: @json($salesTrend['data']),
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
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Revenue: ₱${context.parsed.y.toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
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

            // Top Products Chart (Bar Chart)
            const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
            topProductsChart = new Chart(topProductsCtx, {
                type: 'bar',
                data: {
                    labels: @json($topProducts['labels']),
                    datasets: [{
                        label: 'Quantity Sold',
                        data: @json($topProducts['data']),
                        backgroundColor: [
                            '#06448a',
                            '#28a745',
                            '#ffc107',
                            '#dc3545',
                            '#6f42c1'
                        ],
                        borderWidth: 0
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
                            },
                            ticks: {
                                precision: 0 // This ensures no decimals
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

            // Category Chart (Pie Chart)
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            categoryChart = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($categorySales['labels']),
                    datasets: [{
                        data: @json($categorySales['data']),
                        backgroundColor: [
                            '#06448a',
                            '#28a745',
                            '#ffc107',
                            '#dc3545',
                            '#6f42c1',
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
        }

        function updateSalesChart(chartType) {
            // Get current filter parameters
            const urlParams = new URLSearchParams(window.location.search);
            const filter = urlParams.get('filter');
            const filterType = urlParams.get('filter_type');
            const startDate = urlParams.get('start_date');
            const endDate = urlParams.get('end_date');

            // Show loading state
            const salesChartCanvas = document.getElementById('salesChart');
            salesChartCanvas.style.opacity = '0.5';

            // Fetch updated chart data
            fetch(`/dashboard/sales-chart-data?chart_type=${chartType}&filter=${filter}&filter_type=${filterType}&start_date=${startDate}&end_date=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    // Update chart data
                    salesChart.data.labels = data.labels;
                    salesChart.data.datasets[0].data = data.data;
                    salesChart.update();
                    
                    // Restore opacity
                    salesChartCanvas.style.opacity = '1';
                })
                .catch(error => {
                    console.error('Error fetching chart data:', error);
                    salesChartCanvas.style.opacity = '1';
                });
        }
    });
</script>
@endpush