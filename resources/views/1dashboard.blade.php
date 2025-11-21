@extends('layouts.app')




@section('title', 'Dashboard - ATIN Admin')




@section('content')
<style>
    .dashboard-title {
        color: #002B7F;
        font-weight: 700;
    }




    .card-outline {
        border: 3px solid #002B7F;
        border-radius: 18px;
        padding: 20px;
        height: 240px;
        cursor: pointer;
        transition: 0.25s ease;
    }




    .card-outline:hover {
        background: rgba(0, 43, 127, 0.05);
        transform: scale(1.02);
    }




    .chart-card {
        border-radius: 20px;
        padding: 25px;
        border: 2px solid #eee;
    }


    /* Compact card style for small tables (like the screenshot) */
    .compact-card {
        padding: 16px;
        border-radius: 12px;
        box-shadow: 0 1px 6px rgba(16,24,40,0.06);
        background: #fff;
    }
    /* Make compact cards visually match the Total Transactions widget */
    #total-transactions-widget, .chart-card.compact-card {
        border-radius: 12px;
        box-shadow: 0 1px 6px rgba(16,24,40,0.06);
        padding: 18px;
        min-height: 120px;
    }
    .chart-card.compact-card h5.fw-bold,
    #total-transactions-widget .card-body h6 {
        color: #002B7F;
        font-weight: 700;
    }
    /* Tighter table spacing to match screenshot */
    .compact-card table thead th {
        padding: .6rem .75rem;
        font-weight: 700;
        font-size: 13px;
        color: #111827;
    }
    .compact-card table tbody td {
        padding: .55rem .75rem;
        vertical-align: middle;
        font-size: 13px;
        color: #111827;
    }
    .compact-card table tbody tr + tr td {
        border-top: 1px solid #eef2f6;
    }
    /* Make the Total Transactions number match color/weight */
    #total-transactions-widget .display-4 {
        color: #111827;
        font-size: 3.2rem;
        font-weight: 800;
    }
    /* Subtle hover for compact rows */
    .compact-card tbody tr:hover { background: #fbfdff; }
    .compact-card .card-title {
        font-size: 14px;
        font-weight: 700;
    }
    .compact-card table {
        font-size: 13px;
    }
    .compact-card thead th {
        font-size: 12px;
        color: #6b7280;
        background: transparent;
        border-bottom: none;
    }
    .compact-card tbody tr { cursor: pointer; }
    .compact-card .small-btn { padding: .25rem .45rem; font-size: .78rem; }


    /* Interactive top card nav styling */
    #interactive-nav {
        display: inline-flex;
        gap: .5rem;
        align-items: center;
    }
    #interactive-nav .btn {
        border-radius: 6px;
        padding: .35rem .6rem;
        font-size: .85rem;
    }
    #interactive-nav .btn.active {
        background: #002B7F;
        color: #fff;
        border-color: #002B7F;
    }
    /* give a small visual separator between total and the tabs */
    .interactive-left { display:flex; gap:1.25rem; align-items:center; }




    .filter-option {
        font-size: 14px;
        cursor: pointer;
        padding: 5px 12px;
        border-radius: 6px;
        transition: 0.2s ease;
    }




    .filter-active {
        background: #002B7F;
        color: white;
    }




    .loading-box {
        display: flex;
        height: 100%;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #002B7F;
        font-weight: 600;
    }
    /* Top cards pager and fixed sizing */
    .top-cards-pager { display: flex; flex-direction: column; gap: .5rem; }
    .top-cards-controls { display:flex; justify-content:flex-end; gap:.5rem; }
    .top-cards-controls .btn { padding:.25rem .5rem; font-size:.9rem; }
    .top-cards-pages { overflow: hidden; }
    .top-cards-page { display: flex; gap:1rem; }
    .top-cards-page .col-md-4 { display:flex; }
    .top-card-fixed { flex:1; display:flex; flex-direction:column; min-height:140px; }
    .top-card-fixed .card-body, .top-card-fixed .table-responsive { overflow:auto; }
</style>


{{-- PAGE HEADER --}}
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2 class="mb-0 dashboard-title">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </h2>
            <p class="text-muted mb-0 mt-1">
                Welcome back, {{ session('user_name') }}! ({{ session('user_role') }})
                <!--  -->
                <!-- <tr>
                    <td>#${r.id}</td>
                    <td>${r.date}</td>
                    <td>${r.processed_by}</td>
                    <td class="text-end">${r.total}</td>
                `; -->
        </div>
    </div>
</div>

<!-- Top row: Total Transactions, Top Selling, Low Stock -->
<div class="top-cards-pager">
<!--  -->
    <!-- <div class="top-cards-controls">
        <div class="me-auto"></div>
        <div>
            <button id="top-cards-prev" class="btn btn-sm btn-outline-secondary" disabled>&lsaquo;</button>
            <span id="top-cards-page-num" class="mx-2 small">1/1</span>
            <button id="top-cards-next" class="btn btn-sm btn-outline-secondary" disabled>&rsaquo;</button>
        </div>
    </div> -->
    <div class="top-cards-pages">
        <div class="top-cards-page">
            <div class="row mt-4 g-3 w-100">
    <div class="col-md-4">
        <div class="card chart-card compact-card text-center top-card-fixed" id="total-sales-widget" style="cursor:pointer;transition:0.2s;">
            <div class="card-body text-start">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-1">Total Sales Today</h6>
                    </div>
                    <!-- <div>
                        <input id="total-sales-date" type="date" class="form-control form-control-sm" style="width:140px;" title="Select date to view" />
                    </div> -->
                </div>
                <div class="display-6 fw-bold" id="total-sales-amount">₱0.00</div>
                <div class="mt-2">
                    <span id="total-sales-change" class="small text-muted">&nbsp;</span>
                </div>
                <div class="mt-3 text-muted small" id="total-orders-processed">0 orders processed</div>
            </div>
        </div>
    </div>


    <div class="col-md-4">
        <div class="card chart-card compact-card top-card-fixed" id="top-selling-widget">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="m-0">Top Selling</h6>
                <div class="d-flex gap-2 align-items-center">
                    <input id="top-selling-date" type="month" class="form-control form-control-sm" style="width:150px;" value="2025-11" title="Select month and year">
                    <!-- <input id="top-selling-threshold" type="number" min="0" value="0" class="form-control form-control-sm" style="width:110px;" title="Minimum units sold to include" placeholder="Min units"> -->
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" id="top-selling-table">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th class="text-end">Units</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="col-md-4">
        <div class="card chart-card compact-card top-card-fixed" id="category-performance-widget">
                <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="m-0">Category Performance</h6>
                <div class="d-flex gap-2 align-items-center">
                    <input id="category-performance-date" type="month" class="form-control form-control-sm" style="width:150px;" value="2025-11" title="Select month and year">
                    <!-- <button class="btn btn-sm btn-outline-secondary small-btn" title="PDF" onclick="downloadTable('top-selling-card','pdf')">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </button> -->
                </div>
                </div>
            <div id="category-performance-list"></div>
        </div>
    </div>


            </div>
        </div>
    </div>
        </div>
    </div>
</div>


<!-- Low Stock Alert and Recent Orders side-by-side -->
<div class="row mt-3">
    <div class="col-md-6">
        <div id="low-stock-alert-container"></div>
    </div>
    <div class="col-md-6">
        <div class="card chart-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Recent Orders</h5>
                <div class="d-flex gap-2 align-items-center">
                    <!-- Downloads removed per request -->
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" id="recent-orders-table">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th class="text-end">Total / Processed By</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="d-flex justify-content-end mt-2"><ul class="pagination pagination-sm mb-0" id="recent-orders-pagination"></ul></div>
            </div>
        </div>
    </div>
</div>




{{-- SALES OVERVIEW --}}
<div class="chart-card mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold m-0">Sales Overview</h5>
        <div class="d-flex gap-2">
            <div class="filter-option filter-active disabled" data-period="month" style="pointer-events:none;opacity:0.6;">Month</div>
        </div>
    </div>
    <canvas id="salesChart" height="120"></canvas>
</div>












{{-- INTERACTIVE BOXES --}}




<!-- Removed the two large tables: using compact cards above instead -->








<!-- Modal for Today's Transactions -->
<div class="modal fade" id="transactionsModal" tabindex="-1" aria-labelledby="transactionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionsModalLabel">Today's Transactions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="modal-trans-table">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection












{{-- JAVASCRIPT --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>




<script>
console.log("Dashboard script loaded!");




/* -----------------------------
   SALES CHART (Dynamic Filter)
--------------------------------*/
let chart;




// Dummy monthly sales data
const salesMonths = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
const salesMonthData = [1200, 1500, 1350, 1800, 1950, 2200, 2100, 2300, 2500, 2700, 3000, 3200];




// Dummy transactions for each month (amount is a number for sorting)
const dummyMonthTransactions = {
    Jan: [
        {id: 1001, date: '2025-01-15', total: 1200, status: 'Paid'},
        {id: 1002, date: '2025-01-20', total: 800, status: 'Paid'},
        {id: 1003, date: '2025-01-25', total: 400, status: 'Cancelled'}
    ],
    Feb: [
        {id: 1004, date: '2025-02-10', total: 1500, status: 'Paid'},
        {id: 1005, date: '2025-02-18', total: 900, status: 'Paid'}
    ],
    Mar: [
        {id: 1006, date: '2025-03-05', total: 1350, status: 'Paid'}
    ],
    Apr: [
        {id: 1007, date: '2025-04-12', total: 1800, status: 'Paid'},
        {id: 1008, date: '2025-04-20', total: 950, status: 'Pending'}
    ],
    May: [
        {id: 1009, date: '2025-05-03', total: 1950, status: 'Paid'}
    ],
    Jun: [
        {id: 1010, date: '2025-06-14', total: 2200, status: 'Paid'}
    ],
    Jul: [
        {id: 1011, date: '2025-07-22', total: 2100, status: 'Paid'}
    ],
    Aug: [
        {id: 1012, date: '2025-08-09', total: 2300, status: 'Paid'}
    ],
    Sep: [
        {id: 1013, date: '2025-09-17', total: 2500, status: 'Paid'}
    ],
    Oct: [
        {id: 1014, date: '2025-10-05', total: 2700, status: 'Paid'}
    ],
    Nov: [
        {id: 1015, date: '2025-11-11', total: 3000, status: 'Paid'}
    ],
    Dec: [
        {id: 1016, date: '2025-12-25', total: 3200, status: 'Paid'}
    ]
};




function loadSales() {
    if (chart) chart.destroy();
    const ctx = document.getElementById('salesChart').getContext('2d');
    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesMonths,
            datasets: [{
                label: "Sales",
                data: salesMonthData,
                borderColor: "#002B7F",
                backgroundColor: "#002B7F",
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            plugins: { legend: { display:false } },
            responsive:true,
            onClick: function(evt, elements) {
                if (elements && elements.length > 0) {
                    const idx = elements[0].index;
                    const month = salesMonths[idx];
                    showMonthTransactionsModal(month);
                }
            },
            hover: { mode: 'nearest', intersect: true }
        }
    });
}




// Initial load
loadSales();




/* -----------------------------
   CLICKABLE FILTER BUTTONS
--------------------------------*/




document.querySelectorAll(".filter-option").forEach(btn => {
    btn.addEventListener("click", () => {
        document.querySelectorAll(".filter-option").forEach(b => b.classList.remove("filter-active"));
        btn.classList.add("filter-active");
        loadSales(btn.dataset.period);
    });
});








// Modal for Month Transactions
function showMonthTransactionsModal(month) {
        const modalId = 'monthTransactionsModal';
        let modal = document.getElementById(modalId);
        if (!modal) {
                // Create modal if not exists
                const modalHtml = `
                <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}Label" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="${modalId}Label">Transactions for ${month}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" id="modal-month-trans-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Date</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                modal = document.getElementById(modalId);
        }
        // Fill table
        const tbody = modal.querySelector('tbody');
        tbody.innerHTML = '';
        let data = dummyMonthTransactions[month] || [];
        data = [...data].sort((a, b) => b.total - a.total);
        if (data.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="4" class="text-center">No transactions for this month</td>`;
                tbody.appendChild(row);
        } else {
                data.forEach(tran => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                                <td>${tran.id}</td>
                                <td>${tran.date}</td>
                                <td>₱${tran.total.toLocaleString()}</td>
                                <td>${tran.status}</td>
                        `;
                        tbody.appendChild(row);
                });
        }
        // Show modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
}




/* -----------------------------
   INTERACTIVE BOXES
--------------------------------*/
// --- Dummy Data ---
const dummyTopProducts = {
    day: [
        {id: 1, name: 'Cement 40kg', category: 'Construction', image: 'https://via.placeholder.com/40', sold: 12},
        {id: 2, name: 'GI Pipe 1"', category: 'Plumbing', image: 'https://via.placeholder.com/40', sold: 9},
        {id: 3, name: 'LED Bulb 9W', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 7},
        {id: 4, name: 'Marine Plywood ¼', category: 'Wood', image: 'https://via.placeholder.com/40', sold: 6},
        {id: 5, name: 'Concrete Nails', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 5},
        {id: 6, name: 'Paint 1L', category: 'Paint', image: 'https://via.placeholder.com/40', sold: 4},
        {id: 7, name: 'PVC Pipe', category: 'Plumbing', image: 'https://via.placeholder.com/40', sold: 4},
        {id: 8, name: 'Sandpaper', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 3},
        {id: 9, name: 'Switch', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 2},
        {id: 10, name: 'Door Knob', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 2},
        {id: 11, name: 'Wire 10m', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 1},
    ],
    week: [
        {id: 1, name: 'Cement 40kg', category: 'Construction', image: 'https://via.placeholder.com/40', sold: 60},
        {id: 2, name: 'GI Pipe 1"', category: 'Plumbing', image: 'https://via.placeholder.com/40', sold: 45},
        {id: 3, name: 'LED Bulb 9W', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 38},
        {id: 4, name: 'Marine Plywood ¼', category: 'Wood', image: 'https://via.placeholder.com/40', sold: 32},
        {id: 5, name: 'Concrete Nails', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 28},
        {id: 6, name: 'Paint 1L', category: 'Paint', image: 'https://via.placeholder.com/40', sold: 24},
        {id: 7, name: 'PVC Pipe', category: 'Plumbing', image: 'https://via.placeholder.com/40', sold: 22},
        {id: 8, name: 'Sandpaper', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 18},
        {id: 9, name: 'Switch', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 15},
        {id: 10, name: 'Door Knob', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 13},
        {id: 11, name: 'Wire 10m', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 10},
    ],
    month: [
        {id: 1, name: 'Cement 40kg', category: 'Construction', image: 'https://via.placeholder.com/40', sold: 312},
        {id: 2, name: 'GI Pipe 1"', category: 'Plumbing', image: 'https://via.placeholder.com/40', sold: 255},
        {id: 3, name: 'LED Bulb 9W', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 176},
        {id: 4, name: 'Marine Plywood ¼', category: 'Wood', image: 'https://via.placeholder.com/40', sold: 144},
        {id: 5, name: 'Concrete Nails', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 188},
        {id: 6, name: 'Paint 1L', category: 'Paint', image: 'https://via.placeholder.com/40', sold: 120},
        {id: 7, name: 'PVC Pipe', category: 'Plumbing', image: 'https://via.placeholder.com/40', sold: 110},
        {id: 8, name: 'Sandpaper', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 98},
        {id: 9, name: 'Switch', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 85},
        {id: 10, name: 'Door Knob', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 80},
        {id: 11, name: 'Wire 10m', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 70},
    ]
};
// Dummy top products keyed by year and month — values vary so selection affects output
const dummyTopProductsByYear = {
    '2025': {
        Jan: [
            {id: 1, name: 'Cement 40kg', category: 'Construction', sold: 120},
            {id: 2, name: 'GI Pipe 1"', category: 'Plumbing', sold: 95},
            {id: 3, name: 'LED Bulb 9W', category: 'Electrical', sold: 80}
        ],
        Feb: [
            {id: 1, name: 'Cement 40kg', category: 'Construction', sold: 140},
            {id: 4, name: 'Marine Plywood ¼', category: 'Wood', sold: 100},
            {id: 5, name: 'Concrete Nails', category: 'Hardware', sold: 75}
        ],
        Mar: [
            {id: 2, name: 'GI Pipe 1"', category: 'Plumbing', sold: 180},
            {id: 3, name: 'LED Bulb 9W', category: 'Electrical', sold: 130},
            {id: 6, name: 'Paint 1L', category: 'Paint', sold: 90}
        ],
        Nov: [
            {id: 1, name: 'Cement 40kg', category: 'Construction', sold: 312},
            {id: 2, name: 'GI Pipe 1"', category: 'Plumbing', sold: 255},
            {id: 3, name: 'LED Bulb 9W', category: 'Electrical', sold: 176}
        ],
        Dec: [
            {id: 7, name: 'PVC Pipe', category: 'Plumbing', sold: 220},
            {id: 8, name: 'Sandpaper', category: 'Hardware', sold: 150},
            {id: 9, name: 'Switch', category: 'Electrical', sold: 130}
        ]
    },
    '2024': {
        Jan: [
            {id: 1, name: 'Cement 40kg', category: 'Construction', sold: 200},
            {id: 5, name: 'Concrete Nails', category: 'Hardware', sold: 150},
            {id: 6, name: 'Paint 1L', category: 'Paint', sold: 120}
        ],
        Feb: [
            {id: 4, name: 'Marine Plywood ¼', category: 'Wood', sold: 180},
            {id: 2, name: 'GI Pipe 1"', category: 'Plumbing', sold: 160},
            {id: 3, name: 'LED Bulb 9W', category: 'Electrical', sold: 90}
        ]
    },
    '2023': {
        Jan: [
            {id: 10, name: 'Door Knob', category: 'Hardware', sold: 80},
            {id: 11, name: 'Wire 10m', category: 'Electrical', sold: 60}
        ]
    }
};
const dummyLowStock = [
    {id: 11, name: '10mm Rebar', category: 'Construction', image: 'https://via.placeholder.com/40', stock: 6},
    {id: 12, name: '½" PVC Tee', category: 'Plumbing', image: 'https://via.placeholder.com/40', stock: 4},
    {id: 13, name: 'Roof Sealant', category: 'Hardware', image: 'https://via.placeholder.com/40', stock: 2},
    {id: 14, name: 'Concrete Hollow Blocks', category: 'Construction', image: 'https://via.placeholder.com/40', stock: 12},
    {id: 15, name: '2x3 Lumber', category: 'Wood', image: 'https://via.placeholder.com/40', stock: 3},
    {id: 16, name: 'Paint 1L', category: 'Paint', image: 'https://via.placeholder.com/40', stock: 5},
    {id: 17, name: 'PVC Pipe', category: 'Plumbing', image: 'https://via.placeholder.com/40', stock: 7},
    {id: 18, name: 'Sandpaper', category: 'Hardware', image: 'https://via.placeholder.com/40', stock: 8},
    {id: 19, name: 'Switch', category: 'Electrical', image: 'https://via.placeholder.com/40', stock: 9},
    {id: 20, name: 'Door Knob', category: 'Hardware', image: 'https://via.placeholder.com/40', stock: 2},
];
const dummyRecentTrans = [
    // Today's transactions (dummy) with processed_by
    {id: 2060, date: '2025-11-20', processed_by: 'Admin A', total: '₱1,450', status: 'Paid'},
    {id: 2059, date: '2025-11-20', processed_by: 'Admin B', total: '₱3,200', status: 'Pending'},
    {id: 2058, date: '2025-11-20', processed_by: 'Admin C', total: '₱760', status: 'Paid'},
    {id: 2057, date: '2025-11-20', processed_by: 'Admin D', total: '₱2,100', status: 'Paid'},
    {id: 2056, date: '2025-11-20', processed_by: 'Admin E', total: '₱980', status: 'Paid'},


    // Recent past transactions
    {id: 2055, date: '2025-11-19', processed_by: 'Admin A', total: '₱2,150', status: 'Paid'},
    {id: 2054, date: '2025-11-19', processed_by: 'Admin B', total: '₱1,200', status: 'Paid'},
    {id: 2053, date: '2025-11-18', processed_by: 'Admin C', total: '₱980', status: 'Paid'},
    {id: 2052, date: '2025-11-18', processed_by: 'Admin D', total: '₱1,300', status: 'Paid'},
    {id: 2051, date: '2025-11-17', processed_by: 'Admin A', total: '₱640', status: 'Paid'},
    {id: 2050, date: '2025-11-17', processed_by: 'Admin B', total: '₱5,320', status: 'Paid'},
    {id: 2049, date: '2025-11-16', processed_by: 'Admin C', total: '₱720', status: 'Cancelled'},
    {id: 2048, date: '2025-11-16', processed_by: 'Admin D', total: '₱1,200', status: 'Paid'},
    {id: 2047, date: '2025-11-15', processed_by: 'Admin A', total: '₱2,800', status: 'Paid'},
    {id: 2046, date: '2025-11-15', processed_by: 'Admin B', total: '₱1,100', status: 'Paid'},
    {id: 2045, date: '2025-11-14', processed_by: 'Admin C', total: '₱3,000', status: 'Paid'},
    {id: 2044, date: '2025-11-14', processed_by: 'Admin D', total: '₱2,500', status: 'Paid'},
    {id: 2043, date: '2025-11-13', processed_by: 'Admin A', total: '₱900', status: 'Paid'},
    {id: 2042, date: '2025-11-13', processed_by: 'Admin B', total: '₱1,050', status: 'Paid'},
    {id: 2041, date: '2025-11-12', processed_by: 'Admin C', total: '₱1,250', status: 'Paid'},
    {id: 2040, date: '2025-11-12', processed_by: 'Admin D', total: '₱850', status: 'Paid'},
    {id: 2039, date: '2025-11-11', processed_by: 'Admin E', total: '₱2,300', status: 'Paid'},
    {id: 2038, date: '2025-11-11', processed_by: 'Admin A', total: '₱1,400', status: 'Pending'},
    {id: 2037, date: '2025-11-10', processed_by: 'Admin B', total: '₱980', status: 'Paid'},
    {id: 2036, date: '2025-11-10', processed_by: 'Admin C', total: '₱1,100', status: 'Paid'},
    {id: 2035, date: '2025-11-09', processed_by: 'Admin D', total: '₱760', status: 'Paid'},
    {id: 2034, date: '2025-11-09', processed_by: 'Admin E', total: '₱1,900', status: 'Paid'},
];


// Dummy category performance data (percent values)
const dummyCategoryPerformance = [
    { name: 'Power Tools', pct: 41 },
    { name: 'Hand Tools', pct: 21 },
    { name: 'PPE', pct: 15 },
    { name: 'Electrical', pct: 14 },
    { name: 'Consumables', pct: 9 }
];




// --- Pagination and Rendering ---
function paginate(array, page, perPage) {
    const start = (page - 1) * perPage;
    return array.slice(start, start + perPage);
}
function renderPagination(total, page, perPage, containerId, onPage) {
    const pageCount = Math.ceil(total / perPage);
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    if (pageCount <= 1) return;
    // Left arrow
    const prevLi = document.createElement('li');
    prevLi.className = 'page-item' + (page === 1 ? ' disabled' : '');
    const prevA = document.createElement('a');
    prevA.className = 'page-link';
    prevA.href = '#';
    prevA.innerHTML = '&laquo;';
    prevA.onclick = (e) => { e.preventDefault(); if (page > 1) onPage(page - 1); };
    prevLi.appendChild(prevA);
    container.appendChild(prevLi);
    // Page numbers
    for (let i = 1; i <= pageCount; i++) {
        const li = document.createElement('li');
        li.className = 'page-item' + (i === page ? ' active' : '');
        const a = document.createElement('a');
        a.className = 'page-link';
        a.href = '#';
        a.textContent = i;
        a.onclick = (e) => { e.preventDefault(); onPage(i); };
        li.appendChild(a);
        container.appendChild(li);
    }
    // Right arrow
    const nextLi = document.createElement('li');
    nextLi.className = 'page-item' + (page === pageCount ? ' disabled' : '');
    const nextA = document.createElement('a');
    nextA.className = 'page-link';
    nextA.href = '#';
    nextA.innerHTML = '&raquo;';
    nextA.onclick = (e) => { e.preventDefault(); if (page < pageCount) onPage(page + 1); };
    nextLi.appendChild(nextA);
    container.appendChild(nextLi);
}
function renderTopProductsTable(period = 'day', page = 1) {
    const perPage = 15;
    const tbody = document.querySelector('#top-products-table tbody');
    tbody.innerHTML = '';
    const products = dummyTopProducts[period] || [];
    paginate(products, page, perPage).forEach(prod => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${prod.id}</td>
            <td><img src="${prod.image}" alt="${prod.name}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;"></td>
            <td>${prod.name}</td>
            <td>${prod.category}</td>
            <td>${prod.sold}</td>
        `;
        tbody.appendChild(row);
    });
    renderPagination(products.length, page, perPage, 'top-products-pagination', (p) => renderTopProductsTable(period, p));
}
function renderLowStockTable(page = 1) {
    const perPage = 10;
    const tbody = document.querySelector('#low-stock-table tbody');
    tbody.innerHTML = '';
    paginate(dummyLowStock, page, perPage).forEach(prod => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${prod.id}</td>
            <td><img src="${prod.image}" alt="${prod.name}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;"></td>
            <td>${prod.name}</td>
            <td>${prod.category}</td>
            <td>${prod.stock}</td>
        `;
        tbody.appendChild(row);
    });
    renderPagination(dummyLowStock.length, page, perPage, 'low-stock-pagination', renderLowStockTable);
}
// Render today's transactions in modal
function renderModalTransactionsTable() {
    const tbody = document.querySelector('#modal-trans-table tbody');
    tbody.innerHTML = '';
    const today = new Date().toISOString().slice(0, 10);
    const todaysTrans = dummyRecentTrans.filter(tran => tran.date === today);
    if (todaysTrans.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="4" class="text-center">No transactions for today</td>`;
        tbody.appendChild(row);
    } else {
        todaysTrans.forEach(tran => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${tran.id}</td>
                <td>${tran.date}</td>
                <td>${tran.total}</td>
                <td>${tran.status}</td>
            `;
            tbody.appendChild(row);
        });
    }
}




// --- Download to Excel/PDF ---
function downloadTable(type, format) {
    let data = [];
    let columns = [];
    if (type === 'top-products') {
        const period = document.querySelector('.filter-option.filter-active').dataset.period || 'day';
        data = dummyTopProducts[period] || [];
        columns = ['ID', 'Name', 'Category', 'Sold'];
    } else if (type === 'low-stock') {
        data = dummyLowStock || [];
        columns = ['ID', 'Name', 'Category', 'Stock Left'];
    } else if (type === 'recent-trans') {
        data = dummyRecentTrans || [];
        columns = ['ID', 'Date', 'Total', 'Status'];
    } else if (type === 'top-selling-card') {
        // use the top-selling month input and threshold
        const monthInput = document.getElementById('top-selling-date');
        const month = monthShortFromMonthInput(monthInput ? monthInput.value : null);
        const year = yearFromMonthInput(monthInput ? monthInput.value : null);
        let products = (dummyTopProductsByYear[year] && dummyTopProductsByYear[year][month]) || [];
        if (!products || products.length === 0) products = dummyTopProducts.month || [];
        const thresholdEl = document.getElementById('top-selling-threshold');
        const threshold = thresholdEl ? Math.max(0, parseInt(thresholdEl.value || '0')) : 0;
        data = products.filter(p => (p.sold ?? p.units ?? 0) >= threshold);
        columns = ['Rank', 'Name', 'Category', 'Units'];
    } else if (type === 'low-stock-card') {
        data = dummyLowStock || [];
        columns = ['Product ID', 'Name', 'Current Stock', 'Reorder Level'];
    } else if (type === 'recent-orders-card') {
        data = dummyRecentTrans || [];
        columns = ['ID', 'Date', 'Total', 'Processed By'];
    }


    if (format === 'excel') {
        // Excel export using SheetJS
        const rows = [columns];
        data.forEach((row, idx) => {
            if (type === 'top-products') rows.push([row.id, row.name, row.category, row.sold]);
            else if (type === 'low-stock') rows.push([row.id, row.name, row.category, row.stock]);
            else if (type === 'recent-trans') rows.push([row.id, row.date, row.total, row.status]);
            else if (type === 'top-selling-card') rows.push([idx + 1, row.name, row.category || '', row.sold ?? row.units ?? '']);
            else if (type === 'low-stock-card') rows.push([row.id, row.name, row.stock, 10]);
            else if (type === 'recent-orders-card') rows.push([row.id, row.date, row.total, row.processed_by]);
        });
        const ws = XLSX.utils.aoa_to_sheet(rows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, `${type}.xlsx`);
    } else if (format === 'pdf') {
        // PDF export using jsPDF + autotable
        const doc = new jsPDF();
        const rows = data.map((row, idx) => {
            if (type === 'top-products') return [row.id, row.name, row.category, row.sold];
            else if (type === 'low-stock') return [row.id, row.name, row.category, row.stock];
            else if (type === 'recent-trans') return [row.id, row.date, row.processed_by, row.total, row.status];
            else if (type === 'top-selling-card') return [idx + 1, row.name, row.category || '', row.sold ?? row.units ?? ''];
            else if (type === 'low-stock-card') return [row.id, row.name, row.stock, 10];
            else if (type === 'recent-orders-card') return [row.id, row.date, row.total, row.processed_by];
        });
        doc.autoTable({ head: [columns], body: rows });
        doc.save(`${type}.pdf`);
    }
}


// Small modals for product and order quick view
// --- Renderers for top cards and recent orders (populate with dummy data) ---
// helper to convert YYYY-MM to short month name like 'Nov'
function monthShortFromMonthInput(val) {
    if (!val) return 'Nov';
    const parts = val.split('-');
    if (parts.length < 2) return 'Nov';
    const monthIndex = Number(parts[1]) - 1;
    const names = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return names[monthIndex] || 'Nov';
}


function yearFromMonthInput(val) {
    if (!val) return String(new Date().getFullYear());
    return val.split('-')[0];
}


function renderTopSellingByMonth(month, year) {
    // kept for backward compatibility; same as before
    const tbody = document.querySelector('#top-selling-table tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    const yr = String(year || (new Date()).getFullYear());
    const mon = String(month || 'Nov');
    let products = (dummyTopProductsByYear[yr] && dummyTopProductsByYear[yr][mon]) || [];
    // fallback to generic month data if not found
    if (!products || products.length === 0) products = dummyTopProducts.month || [];
    // apply threshold filter (min units sold)
    const thresholdEl = document.getElementById('top-selling-threshold');
    const threshold = thresholdEl ? Math.max(0, parseInt(thresholdEl.value || '0')) : 0;
    const filtered = products.filter(p => (p.sold ?? p.units ?? 0) >= threshold);
    filtered.slice(0, 10).forEach((p, idx) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${idx + 1}</td>
            <td>
                <div class="d-flex align-items-center">
                    <img src="${p.image || 'https://via.placeholder.com/40'}" alt="" style="width:36px;height:36px;object-fit:cover;border-radius:6px;margin-right:8px;">
                    <div>${p.name}</div>
                </div>
            </td>
            <td>${p.category || '-'}</td>
            <td class="text-end">${p.sold ?? p.units ?? '-'}</td>
        `;
        tbody.appendChild(row);
    });
}


function renderTopSellingByDateInput(dateVal) {
    const mon = monthShortFromMonthInput(dateVal);
    const yr = yearFromMonthInput(dateVal);
    renderTopSellingByMonth(mon, yr);
}


function renderLowStockCard(dateVal) {
    // Now renders category performance into the new widget
    // dateVal is accepted for future server integration; currently not used for filtering dummy data
    const container = document.getElementById('category-performance-list');
    if (!container) return;
    container.innerHTML = '';
    dummyCategoryPerformance.forEach(cat => {
        const row = document.createElement('div');
        row.className = 'mb-2';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-1">
                <div class="small">${cat.name}</div>
                <div class="small text-muted">${cat.pct}%</div>
            </div>
            <div class="progress" style="height:8px;border-radius:6px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: ${cat.pct}%;" aria-valuenow="${cat.pct}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        `;
        container.appendChild(row);
    });
}


function renderRecentOrdersCard(limit = 10) {
    const tbody = document.querySelector('#recent-orders-table tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    const perPage = 10;
    const page = arguments[0] && Number.isInteger(arguments[0]) ? arguments[0] : 1;
    const rows = paginate(dummyRecentTrans || [], page, perPage);
    rows.forEach(r => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${r.id}</td>
            <td>${r.date}</td>
            <td class="text-end">
                <div><strong>${r.total}</strong></div>
                <div class="text-muted small">${r.processed_by}</div>
            </td>
        `;
        tr.onclick = () => showOrderModal(r.id);
        tbody.appendChild(tr);
    });
    // pagination
    renderPagination((dummyRecentTrans || []).length, page, perPage, 'recent-orders-pagination', (p) => renderRecentOrdersCard(p));
}


// --- New: Render transactions today for the interactive tab ---
function renderTransactionsToday() {
    const tbody = document.querySelector('#transactions-today-table tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    const today = new Date().toISOString().slice(0,10);
    const todays = dummyRecentTrans.filter(t => t.date === today);
    if (todays.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="4" class="text-center">No transactions for today</td>`;
        tbody.appendChild(row);
    } else {
        todays.forEach(t => {
            const r = document.createElement('tr');
            r.innerHTML = `
                <td>${t.id}</td>
                <td>${t.date}</td>
                <td>${t.total}</td>
                <td>${t.status}</td>
            `;
            r.onclick = () => showOrderModal(t.id);
            tbody.appendChild(r);
        });
    }
    // update totals in widget — show friendly message when zero
    const ordersWidget = document.getElementById('total-orders-processed');
    if (ordersWidget) {
        if (todays.length === 0) {
            ordersWidget.innerHTML = '<span class="text-muted small">No orders processed</span>';
        } else {
            ordersWidget.textContent = `${todays.length} orders processed`;
        }
    }
    // compute today's sales total and comparison with yesterday
    computeSalesComparison();
}


// parse currency string like '₱1,450' to number 1450
function parseCurrency(v) {
    if (!v) return 0;
    return Number(String(v).replace(/[^0-9.-]+/g, '')) || 0;
}


function formatCurrency(n) {
    return '₱' + Number(n).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}


function computeSalesComparison() {
    const today = new Date().toISOString().slice(0,10);
    const yesterday = new Date(Date.now() - 24*60*60*1000).toISOString().slice(0,10);
    const todays = dummyRecentTrans.filter(t => t.date === today);
    const yest = dummyRecentTrans.filter(t => t.date === yesterday);
    const totalToday = todays.reduce((s,t) => s + parseCurrency(t.total), 0);
    const totalYest = yest.reduce((s,t) => s + parseCurrency(t.total), 0);
    const changeEl = document.getElementById('total-sales-change');
    const amtEl = document.getElementById('total-sales-amount');
    if (amtEl) amtEl.textContent = formatCurrency(totalToday);
    if (!changeEl) return;
    if (totalYest === 0) {
        if (totalToday === 0) {
            changeEl.innerHTML = '<span class="text-muted small">No change</span>';
            changeEl.className = 'small text-muted';
        } else {
            changeEl.innerHTML = '<span class="text-success small">▲ New sales today</span>';
            changeEl.className = 'small text-success';
        }
        return;
    }
    const pct = ((totalToday - totalYest) / totalYest) * 100;
    const rounded = Math.round(pct * 10) / 10;
    if (rounded >= 0) {
        changeEl.innerHTML = `<span class="text-success">▲ ${Math.abs(rounded)}% from yesterday</span>`;
        changeEl.className = 'small text-success';
    } else {
        changeEl.innerHTML = `<span class="text-danger">▼ ${Math.abs(rounded)}% from yesterday</span>`;
        changeEl.className = 'small text-danger';
    }
}


// --- New: Low Stock Alert rendering ---
function renderLowStockAlert() {
    const container = document.getElementById('low-stock-alert-container');
    if (!container) return;
    const reorderLevel = 10;
    const lowItems = dummyLowStock.filter(p => p.stock < reorderLevel);
    if (lowItems.length === 0) { container.innerHTML = ''; return; }
    // pagination params
    const perPage = 15;
    // render helper will be called by pagination
    function renderPage(page = 1) {
        const pageItems = paginate(lowItems, page, perPage);
        let html = `
            <div class="card chart-card">
                <div class="p-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="text-warning" style="font-size:20px;margin-top:4px;"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        <div>
                            <div class="fw-bold">Low Stock Alert</div>
                            <div class="text-muted small">Items that need restocking</div>
                        </div>
                    </div>
                    <div style="height:12px;"></div>
        `;
        pageItems.forEach(p => {
            html += `
                <div class="p-3 mb-2" style="background:#fff7f0;border-radius:8px;display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <div class="fw-semibold">${p.name}</div>
                        <div class="text-muted small">SKU: PROD-${p.id.toString().padStart(3,'0')}</div>
                    </div>
                    <div style="text-align:right">
                        <div class="badge bg-warning text-dark" style="font-size:12px;padding:.45rem .6rem;border-radius:8px;">${p.stock} left</div>
                        <div class="text-muted small" style="margin-top:6px;">Min: ${reorderLevel}</div>
                    </div>
                </div>
            `;
        });
        // pagination container
        html += `<div class="d-flex justify-content-end mt-2"><ul class="pagination pagination-sm mb-0" id="low-stock-alert-pagination"></ul></div>`;
        html += `</div></div>`;
        container.innerHTML = html;
        renderPagination(lowItems.length, page, perPage, 'low-stock-alert-pagination', (p) => renderPage(p));
    }
    renderPage(1);
}


// --- Tab switching ---
function switchDashboardTab(tab) {
    document.querySelectorAll('#interactive-nav .btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.dashboard-tab').forEach(d => d.style.display = 'none');
    if (tab === 'transactions') {
        document.querySelector('#interactive-nav .btn').classList.add('active');
        document.querySelector('#tab-transactions').style.display = '';
    }
    const buttons = Array.from(document.querySelectorAll('#interactive-nav .btn'));
    buttons.forEach(btn => { if (btn.textContent.trim().toLowerCase().includes(tab.replace('-',' '))) btn.classList.add('active'); });
    if (tab === 'top-selling') document.getElementById('tab-top-selling').style.display = '';
    if (tab === 'low-stock') document.getElementById('tab-low-stock').style.display = '';
}


function downloadActiveTabPdf() {
    const visible = document.querySelector('.dashboard-tab:not([style*="display: none"])');
    if (!visible) return;
    if (visible.id === 'tab-top-selling') downloadTable('top-selling-card','pdf');
    else if (visible.id === 'tab-low-stock') downloadTable('low-stock-card','pdf');
    else if (visible.id === 'tab-transactions') downloadTable('recent-trans','pdf');
}
function showProductModal(productId) {
    const product = [...dummyTopProducts.day, ...dummyTopProducts.week, ...dummyTopProducts.month].find(p => p.id === productId) || dummyLowStock.find(p => p.id === productId);
    const modalId = 'quickProductModal';
    let modal = document.getElementById(modalId);
    if (!modal) {
        const html = `
        <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="${modalId}Body"></div>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', html);
        modal = document.getElementById(modalId);
    }
    const body = modal.querySelector('#' + modalId + 'Body');
    if (!product) {
        body.innerHTML = '<div class="text-center text-muted">Product not found</div>';
    } else {
        body.innerHTML = `
            <div class="d-flex gap-3 align-items-center">
                <img src="${product.image || 'https://via.placeholder.com/64'}" alt="" style="width:64px;height:64px;object-fit:cover;border-radius:6px;">
                <div>
                    <div class="fw-semibold">${product.name}</div>
                    <div class="text-muted small">${product.category || ''}</div>
                    <div class="mt-2">Sold: <strong>${product.sold ?? '-'}</strong></div>
                </div>
            </div>
        `;
    }
    new bootstrap.Modal(modal).show();
}


function showOrderModal(orderId) {
    const order = dummyRecentTrans.find(t => t.id === orderId);
    const modalId = 'quickOrderModal';
    let modal = document.getElementById(modalId);
    if (!modal) {
        const html = `
        <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="${modalId}Body"></div>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', html);
        modal = document.getElementById(modalId);
    }
    const body = modal.querySelector('#' + modalId + 'Body');
    if (!order) {
        body.innerHTML = '<div class="text-center text-muted">Order not found</div>';
    } else {
        body.innerHTML = `
            <div>
                <div class="d-flex justify-content-between">
                    <div><strong>Order #${order.id}</strong></div>
                    <div class="text-muted">${order.date}</div>
                </div>
                <div class="mt-2">Processed By: <strong>${order.processed_by}</strong></div>
                <div class="mt-2">Total: <strong>${order.total}</strong></div>
                <div class="mt-2">Status: <span class="badge bg-secondary">${order.status}</span></div>
            </div>
        `;
    }
    new bootstrap.Modal(modal).show();
}


// Wire up selects and button
document.addEventListener('DOMContentLoaded', function() {
    const monthSelect = document.getElementById('top-month-select');
    const yearSelect = document.getElementById('top-year-select');
    if (monthSelect) {
        monthSelect.addEventListener('change', () => renderTopSellingByMonth(monthSelect.value, yearSelect.value));
    }
    if (yearSelect) {
        yearSelect.addEventListener('change', () => renderTopSellingByMonth(monthSelect.value, yearSelect.value));
    }
    // new selects inside Top Selling tab
    const tsDate = document.getElementById('top-selling-date');
    if (tsDate) tsDate.addEventListener('change', () => renderTopSellingByDateInput(tsDate.value));
    const tsThreshold = document.getElementById('top-selling-threshold');
    if (tsThreshold) tsThreshold.addEventListener('input', () => renderTopSellingByDateInput(tsDate ? tsDate.value : null));
    const catDateInput = document.getElementById('category-performance-date');
    if (catDateInput) catDateInput.addEventListener('change', () => renderLowStockCard(catDateInput.value));
    // create-po button removed from UI; no handler needed


    // Initial render
    // render using default month/year selects for top-selling
    const initMonthVal = (document.getElementById('top-selling-date') || {}).value || '2025-11';
    renderTopSellingByDateInput(initMonthVal);
    const initCatDate = (document.getElementById('category-performance-date') || {}).value || '2025-11';
    renderLowStockCard(initCatDate);
    renderRecentOrdersCard(1);
    renderTransactionsToday();
    renderLowStockAlert();
    // default tab
    switchDashboardTab('transactions');
    // Top cards pager initialization (if multiple pages are present)
    (function initTopCardsPager(){
        const pages = Array.from(document.querySelectorAll('.top-cards-page'));
        if (!pages || pages.length === 0) return;
        let current = 0;
        const total = pages.length;
        const prev = document.getElementById('top-cards-prev');
        const next = document.getElementById('top-cards-next');
        const pageNum = document.getElementById('top-cards-page-num');
        function updatePager(){
            pages.forEach((p,i)=> p.style.display = (i===current)?'flex':'none');
            if (pageNum) pageNum.textContent = `${current+1}/${total}`;
            if (prev) prev.disabled = current === 0;
            if (next) next.disabled = current === total - 1;
        }
        if (prev) prev.addEventListener('click', (e)=>{ e.preventDefault(); if (current>0) { current--; updatePager(); } });
        if (next) next.addEventListener('click', (e)=>{ e.preventDefault(); if (current<total-1) { current++; updatePager(); } });
        updatePager();
    })();
});


// SheetJS and jsPDF CDN
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.0/jspdf.plugin.autotable.min.js"></script>
@endpush








@extends('layouts.app')




@section('title', 'Dashboard - ATIN Admin')




@section('content')
<style>
    .dashboard-title {
        color: #002B7F;
        font-weight: 700;
    }




    .card-outline {
        border: 3px solid #002B7F;
        border-radius: 18px;
        padding: 20px;
        height: 240px;
        cursor: pointer;
        transition: 0.25s ease;
    }




    .card-outline:hover {
        background: rgba(0, 43, 127, 0.05);
        transform: scale(1.02);
    }




    .chart-card {
        border-radius: 20px;
        padding: 25px;
        border: 2px solid #eee;
    }


    /* Compact card style for small tables (like the screenshot) */
    .compact-card {
        padding: 16px;
        border-radius: 12px;
        box-shadow: 0 1px 6px rgba(16,24,40,0.06);
        background: #fff;
    }
    /* Make compact cards visually match the Total Transactions widget */
    #total-transactions-widget, .chart-card.compact-card {
        border-radius: 12px;
        box-shadow: 0 1px 6px rgba(16,24,40,0.06);
        padding: 18px;
        min-height: 120px;
    }
    .chart-card.compact-card h5.fw-bold,
    #total-transactions-widget .card-body h6 {
        color: #002B7F;
        font-weight: 700;
    }
    /* Tighter table spacing to match screenshot */
    .compact-card table thead th {
        padding: .6rem .75rem;
        font-weight: 700;
        font-size: 13px;
        color: #111827;
    }
    .compact-card table tbody td {
        padding: .55rem .75rem;
        vertical-align: middle;
        font-size: 13px;
        color: #111827;
    }
    .compact-card table tbody tr + tr td {
        border-top: 1px solid #eef2f6;
    }
    /* Make the Total Transactions number match color/weight */
    #total-transactions-widget .display-4 {
        color: #111827;
        font-size: 3.2rem;
        font-weight: 800;
    }
    /* Subtle hover for compact rows */
    .compact-card tbody tr:hover { background: #fbfdff; }
    .compact-card .card-title {
        font-size: 14px;
        font-weight: 700;
    }
    .compact-card table {
        font-size: 13px;
    }
    .compact-card thead th {
        font-size: 12px;
        color: #6b7280;
        background: transparent;
        border-bottom: none;
    }
    .compact-card tbody tr { cursor: pointer; }
    .compact-card .small-btn { padding: .25rem .45rem; font-size: .78rem; }


    /* Interactive top card nav styling */
    #interactive-nav {
        display: inline-flex;
        gap: .5rem;
        align-items: center;
    }
    #interactive-nav .btn {
        border-radius: 6px;
        padding: .35rem .6rem;
        font-size: .85rem;
    }
    #interactive-nav .btn.active {
        background: #002B7F;
        color: #fff;
        border-color: #002B7F;
    }
    /* give a small visual separator between total and the tabs */
    .interactive-left { display:flex; gap:1.25rem; align-items:center; }




    .filter-option {
        font-size: 14px;
        cursor: pointer;
        padding: 5px 12px;
        border-radius: 6px;
        transition: 0.2s ease;
    }




    .filter-active {
        background: #002B7F;
        color: white;
    }




    .loading-box {
        display: flex;
        height: 100%;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #002B7F;
        font-weight: 600;
    }
    /* Top cards pager and fixed sizing */
    .top-cards-pager { display: flex; flex-direction: column; gap: .5rem; }
    .top-cards-controls { display:flex; justify-content:flex-end; gap:.5rem; }
    .top-cards-controls .btn { padding:.25rem .5rem; font-size:.9rem; }
    .top-cards-pages { overflow: hidden; }
    .top-cards-page { display: flex; gap:1rem; }
    .top-cards-page .col-md-4 { display:flex; }
    .top-card-fixed { flex:1; display:flex; flex-direction:column; min-height:140px; }
    .top-card-fixed .card-body, .top-card-fixed .table-responsive { overflow:auto; }
</style>




<div style="height: 24px;"></div>
















<!-- Top row: Total Transactions, Top Selling, Low Stock -->
<div class="top-cards-pager">
<!--  -->
    <!-- <div class="top-cards-controls">
        <div class="me-auto"></div>
        <div>
            <button id="top-cards-prev" class="btn btn-sm btn-outline-secondary" disabled>&lsaquo;</button>
            <span id="top-cards-page-num" class="mx-2 small">1/1</span>
            <button id="top-cards-next" class="btn btn-sm btn-outline-secondary" disabled>&rsaquo;</button>
        </div>
    </div> -->
    <div class="top-cards-pages">
        <div class="top-cards-page">
            <div class="row mt-4 g-3 w-100">
    <div class="col-md-4">
        <div class="card chart-card compact-card text-center top-card-fixed" id="total-sales-widget" style="cursor:pointer;transition:0.2s;">
            <div class="card-body text-start">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-1">Total Sales Today</h6>
                    </div>
                    <!-- <div>
                        <input id="total-sales-date" type="date" class="form-control form-control-sm" style="width:140px;" title="Select date to view" />
                    </div> -->
                </div>
                <div class="display-6 fw-bold" id="total-sales-amount">₱0.00</div>
                <div class="mt-2">
                    <span id="total-sales-change" class="small text-muted">&nbsp;</span>
                </div>
                <div class="mt-3 text-muted small" id="total-orders-processed">0 orders processed</div>
            </div>
        </div>
    </div>


    <div class="col-md-4">
        <div class="card chart-card compact-card top-card-fixed" id="top-selling-widget">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="m-0">Top Selling</h6>
                <div class="d-flex gap-2 align-items-center">
                    <input id="top-selling-date" type="month" class="form-control form-control-sm" style="width:150px;" value="2025-11" title="Select month and year">
                    <!-- <input id="top-selling-threshold" type="number" min="0" value="0" class="form-control form-control-sm" style="width:110px;" title="Minimum units sold to include" placeholder="Min units"> -->
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" id="top-selling-table">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th class="text-end">Units</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="col-md-4">
        <div class="card chart-card compact-card top-card-fixed" id="category-performance-widget">
                <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="m-0">Category Performance</h6>
                <div class="d-flex gap-2 align-items-center">
                    <input id="category-performance-date" type="month" class="form-control form-control-sm" style="width:150px;" value="2025-11" title="Select month and year">
                    <!-- <button class="btn btn-sm btn-outline-secondary small-btn" title="PDF" onclick="downloadTable('top-selling-card','pdf')">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </button> -->
                </div>
                </div>
            <div id="category-performance-list"></div>
        </div>
    </div>


            </div>
        </div>
    </div>
        </div>
    </div>
</div>


<!-- Low Stock Alert and Recent Orders side-by-side -->
<div class="row mt-3">
    <div class="col-md-6">
        <div id="low-stock-alert-container"></div>
    </div>
    <div class="col-md-6">
        <div class="card chart-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Recent Orders</h5>
                <div class="d-flex gap-2 align-items-center">
                    <!-- Downloads removed per request -->
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" id="recent-orders-table">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th class="text-end">Total / Processed By</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="d-flex justify-content-end mt-2"><ul class="pagination pagination-sm mb-0" id="recent-orders-pagination"></ul></div>
            </div>
        </div>
    </div>
</div>




{{-- SALES OVERVIEW --}}
<div class="chart-card mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold m-0">Sales Overview</h5>
        <div class="d-flex gap-2">
            <div class="filter-option filter-active disabled" data-period="month" style="pointer-events:none;opacity:0.6;">Month</div>
        </div>
    </div>
    <canvas id="salesChart" height="120"></canvas>
</div>












{{-- INTERACTIVE BOXES --}}




<!-- Removed the two large tables: using compact cards above instead -->








<!-- Modal for Today's Transactions -->
<div class="modal fade" id="transactionsModal" tabindex="-1" aria-labelledby="transactionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionsModalLabel">Today's Transactions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="modal-trans-table">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection












{{-- JAVASCRIPT --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>




<script>
console.log("Dashboard script loaded!");




/* -----------------------------
   SALES CHART (Dynamic Filter)
--------------------------------*/
let chart;




// Dummy monthly sales data
const salesMonths = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
const salesMonthData = [1200, 1500, 1350, 1800, 1950, 2200, 2100, 2300, 2500, 2700, 3000, 3200];




// Dummy transactions for each month (amount is a number for sorting)
const dummyMonthTransactions = {
    Jan: [
        {id: 1001, date: '2025-01-15', total: 1200, status: 'Paid'},
        {id: 1002, date: '2025-01-20', total: 800, status: 'Paid'},
        {id: 1003, date: '2025-01-25', total: 400, status: 'Cancelled'}
    ],
    Feb: [
        {id: 1004, date: '2025-02-10', total: 1500, status: 'Paid'},
        {id: 1005, date: '2025-02-18', total: 900, status: 'Paid'}
    ],
    Mar: [
        {id: 1006, date: '2025-03-05', total: 1350, status: 'Paid'}
    ],
    Apr: [
        {id: 1007, date: '2025-04-12', total: 1800, status: 'Paid'},
        {id: 1008, date: '2025-04-20', total: 950, status: 'Pending'}
    ],
    May: [
        {id: 1009, date: '2025-05-03', total: 1950, status: 'Paid'}
    ],
    Jun: [
        {id: 1010, date: '2025-06-14', total: 2200, status: 'Paid'}
    ],
    Jul: [
        {id: 1011, date: '2025-07-22', total: 2100, status: 'Paid'}
    ],
    Aug: [
        {id: 1012, date: '2025-08-09', total: 2300, status: 'Paid'}
    ],
    Sep: [
        {id: 1013, date: '2025-09-17', total: 2500, status: 'Paid'}
    ],
    Oct: [
        {id: 1014, date: '2025-10-05', total: 2700, status: 'Paid'}
    ],
    Nov: [
        {id: 1015, date: '2025-11-11', total: 3000, status: 'Paid'}
    ],
    Dec: [
        {id: 1016, date: '2025-12-25', total: 3200, status: 'Paid'}
    ]
};




function loadSales() {
    if (chart) chart.destroy();
    const ctx = document.getElementById('salesChart').getContext('2d');
    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesMonths,
            datasets: [{
                label: "Sales",
                data: salesMonthData,
                borderColor: "#002B7F",
                backgroundColor: "#002B7F",
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            plugins: { legend: { display:false } },
            responsive:true,
            onClick: function(evt, elements) {
                if (elements && elements.length > 0) {
                    const idx = elements[0].index;
                    const month = salesMonths[idx];
                    showMonthTransactionsModal(month);
                }
            },
            hover: { mode: 'nearest', intersect: true }
        }
    });
}




// Initial load
loadSales();




/* -----------------------------
   CLICKABLE FILTER BUTTONS
--------------------------------*/




document.querySelectorAll(".filter-option").forEach(btn => {
    btn.addEventListener("click", () => {
        document.querySelectorAll(".filter-option").forEach(b => b.classList.remove("filter-active"));
        btn.classList.add("filter-active");
        loadSales(btn.dataset.period);
    });
});








// Modal for Month Transactions
function showMonthTransactionsModal(month) {
        const modalId = 'monthTransactionsModal';
        let modal = document.getElementById(modalId);
        if (!modal) {
                // Create modal if not exists
                const modalHtml = `
                <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}Label" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="${modalId}Label">Transactions for ${month}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" id="modal-month-trans-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Date</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                modal = document.getElementById(modalId);
        }
        // Fill table
        const tbody = modal.querySelector('tbody');
        tbody.innerHTML = '';
        let data = dummyMonthTransactions[month] || [];
        data = [...data].sort((a, b) => b.total - a.total);
        if (data.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="4" class="text-center">No transactions for this month</td>`;
                tbody.appendChild(row);
        } else {
                data.forEach(tran => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                                <td>${tran.id}</td>
                                <td>${tran.date}</td>
                                <td>₱${tran.total.toLocaleString()}</td>
                                <td>${tran.status}</td>
                        `;
                        tbody.appendChild(row);
                });
        }
        // Show modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
}




/* -----------------------------
   INTERACTIVE BOXES
--------------------------------*/
// --- Dummy Data ---
const dummyTopProducts = {
    day: [
        {id: 1, name: 'Cement 40kg', category: 'Construction', image: 'https://via.placeholder.com/40', sold: 12},
        {id: 2, name: 'GI Pipe 1"', category: 'Plumbing', image: 'https://via.placeholder.com/40', sold: 9},
        {id: 3, name: 'LED Bulb 9W', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 7},
        {id: 4, name: 'Marine Plywood ¼', category: 'Wood', image: 'https://via.placeholder.com/40', sold: 6},
        {id: 5, name: 'Concrete Nails', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 5},
        {id: 6, name: 'Paint 1L', category: 'Paint', image: 'https://via.placeholder.com/40', sold: 4},
        {id: 7, name: 'PVC Pipe', category: 'Plumbing', image: 'https://via.placeholder.com/40', sold: 4},
        {id: 8, name: 'Sandpaper', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 3},
        {id: 9, name: 'Switch', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 2},
        {id: 10, name: 'Door Knob', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 2},
        {id: 11, name: 'Wire 10m', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 1},
    ],
    week: [
        {id: 1, name: 'Cement 40kg', category: 'Construction', image: 'https://via.placeholder.com/40', sold: 60},
        {id: 2, name: 'GI Pipe 1"', category: 'Plumbing', image: 'https://via.placeholder.com/40', sold: 45},
        {id: 3, name: 'LED Bulb 9W', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 38},
        {id: 4, name: 'Marine Plywood ¼', category: 'Wood', image: 'https://via.placeholder.com/40', sold: 32},
        {id: 5, name: 'Concrete Nails', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 28},
        {id: 6, name: 'Paint 1L', category: 'Paint', image: 'https://via.placeholder.com/40', sold: 24},
        {id: 7, name: 'PVC Pipe', category: 'Plumbing', image: 'https://via.placeholder.com/40', sold: 22},
        {id: 8, name: 'Sandpaper', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 18},
        {id: 9, name: 'Switch', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 15},
        {id: 10, name: 'Door Knob', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 13},
        {id: 11, name: 'Wire 10m', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 10},
    ],
    month: [
        {id: 1, name: 'Cement 40kg', category: 'Construction', image: 'https://via.placeholder.com/40', sold: 312},
        {id: 2, name: 'GI Pipe 1"', category: 'Plumbing', image: 'https://via.placeholder.com/40', sold: 255},
        {id: 3, name: 'LED Bulb 9W', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 176},
        {id: 4, name: 'Marine Plywood ¼', category: 'Wood', image: 'https://via.placeholder.com/40', sold: 144},
        {id: 5, name: 'Concrete Nails', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 188},
        {id: 6, name: 'Paint 1L', category: 'Paint', image: 'https://via.placeholder.com/40', sold: 120},
        {id: 7, name: 'PVC Pipe', category: 'Plumbing', image: 'https://via.placeholder.com/40', sold: 110},
        {id: 8, name: 'Sandpaper', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 98},
        {id: 9, name: 'Switch', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 85},
        {id: 10, name: 'Door Knob', category: 'Hardware', image: 'https://via.placeholder.com/40', sold: 80},
        {id: 11, name: 'Wire 10m', category: 'Electrical', image: 'https://via.placeholder.com/40', sold: 70},
    ]
};
// Dummy top products keyed by year and month — values vary so selection affects output
const dummyTopProductsByYear = {
    '2025': {
        Jan: [
            {id: 1, name: 'Cement 40kg', category: 'Construction', sold: 120},
            {id: 2, name: 'GI Pipe 1"', category: 'Plumbing', sold: 95},
            {id: 3, name: 'LED Bulb 9W', category: 'Electrical', sold: 80}
        ],
        Feb: [
            {id: 1, name: 'Cement 40kg', category: 'Construction', sold: 140},
            {id: 4, name: 'Marine Plywood ¼', category: 'Wood', sold: 100},
            {id: 5, name: 'Concrete Nails', category: 'Hardware', sold: 75}
        ],
        Mar: [
            {id: 2, name: 'GI Pipe 1"', category: 'Plumbing', sold: 180},
            {id: 3, name: 'LED Bulb 9W', category: 'Electrical', sold: 130},
            {id: 6, name: 'Paint 1L', category: 'Paint', sold: 90}
        ],
        Nov: [
            {id: 1, name: 'Cement 40kg', category: 'Construction', sold: 312},
            {id: 2, name: 'GI Pipe 1"', category: 'Plumbing', sold: 255},
            {id: 3, name: 'LED Bulb 9W', category: 'Electrical', sold: 176}
        ],
        Dec: [
            {id: 7, name: 'PVC Pipe', category: 'Plumbing', sold: 220},
            {id: 8, name: 'Sandpaper', category: 'Hardware', sold: 150},
            {id: 9, name: 'Switch', category: 'Electrical', sold: 130}
        ]
    },
    '2024': {
        Jan: [
            {id: 1, name: 'Cement 40kg', category: 'Construction', sold: 200},
            {id: 5, name: 'Concrete Nails', category: 'Hardware', sold: 150},
            {id: 6, name: 'Paint 1L', category: 'Paint', sold: 120}
        ],
        Feb: [
            {id: 4, name: 'Marine Plywood ¼', category: 'Wood', sold: 180},
            {id: 2, name: 'GI Pipe 1"', category: 'Plumbing', sold: 160},
            {id: 3, name: 'LED Bulb 9W', category: 'Electrical', sold: 90}
        ]
    },
    '2023': {
        Jan: [
            {id: 10, name: 'Door Knob', category: 'Hardware', sold: 80},
            {id: 11, name: 'Wire 10m', category: 'Electrical', sold: 60}
        ]
    }
};
const dummyLowStock = [
    {id: 11, name: '10mm Rebar', category: 'Construction', image: 'https://via.placeholder.com/40', stock: 6},
    {id: 12, name: '½" PVC Tee', category: 'Plumbing', image: 'https://via.placeholder.com/40', stock: 4},
    {id: 13, name: 'Roof Sealant', category: 'Hardware', image: 'https://via.placeholder.com/40', stock: 2},
    {id: 14, name: 'Concrete Hollow Blocks', category: 'Construction', image: 'https://via.placeholder.com/40', stock: 12},
    {id: 15, name: '2x3 Lumber', category: 'Wood', image: 'https://via.placeholder.com/40', stock: 3},
    {id: 16, name: 'Paint 1L', category: 'Paint', image: 'https://via.placeholder.com/40', stock: 5},
    {id: 17, name: 'PVC Pipe', category: 'Plumbing', image: 'https://via.placeholder.com/40', stock: 7},
    {id: 18, name: 'Sandpaper', category: 'Hardware', image: 'https://via.placeholder.com/40', stock: 8},
    {id: 19, name: 'Switch', category: 'Electrical', image: 'https://via.placeholder.com/40', stock: 9},
    {id: 20, name: 'Door Knob', category: 'Hardware', image: 'https://via.placeholder.com/40', stock: 2},
];
const dummyRecentTrans = [
    // Today's transactions (dummy) with processed_by
    {id: 2060, date: '2025-11-20', processed_by: 'Admin A', total: '₱1,450', status: 'Paid'},
    {id: 2059, date: '2025-11-20', processed_by: 'Admin B', total: '₱3,200', status: 'Pending'},
    {id: 2058, date: '2025-11-20', processed_by: 'Admin C', total: '₱760', status: 'Paid'},
    {id: 2057, date: '2025-11-20', processed_by: 'Admin D', total: '₱2,100', status: 'Paid'},
    {id: 2056, date: '2025-11-20', processed_by: 'Admin E', total: '₱980', status: 'Paid'},


    // Recent past transactions
    {id: 2055, date: '2025-11-19', processed_by: 'Admin A', total: '₱2,150', status: 'Paid'},
    {id: 2054, date: '2025-11-19', processed_by: 'Admin B', total: '₱1,200', status: 'Paid'},
    {id: 2053, date: '2025-11-18', processed_by: 'Admin C', total: '₱980', status: 'Paid'},
    {id: 2052, date: '2025-11-18', processed_by: 'Admin D', total: '₱1,300', status: 'Paid'},
    {id: 2051, date: '2025-11-17', processed_by: 'Admin A', total: '₱640', status: 'Paid'},
    {id: 2050, date: '2025-11-17', processed_by: 'Admin B', total: '₱5,320', status: 'Paid'},
    {id: 2049, date: '2025-11-16', processed_by: 'Admin C', total: '₱720', status: 'Cancelled'},
    {id: 2048, date: '2025-11-16', processed_by: 'Admin D', total: '₱1,200', status: 'Paid'},
    {id: 2047, date: '2025-11-15', processed_by: 'Admin A', total: '₱2,800', status: 'Paid'},
    {id: 2046, date: '2025-11-15', processed_by: 'Admin B', total: '₱1,100', status: 'Paid'},
    {id: 2045, date: '2025-11-14', processed_by: 'Admin C', total: '₱3,000', status: 'Paid'},
    {id: 2044, date: '2025-11-14', processed_by: 'Admin D', total: '₱2,500', status: 'Paid'},
    {id: 2043, date: '2025-11-13', processed_by: 'Admin A', total: '₱900', status: 'Paid'},
    {id: 2042, date: '2025-11-13', processed_by: 'Admin B', total: '₱1,050', status: 'Paid'},
    {id: 2041, date: '2025-11-12', processed_by: 'Admin C', total: '₱1,250', status: 'Paid'},
    {id: 2040, date: '2025-11-12', processed_by: 'Admin D', total: '₱850', status: 'Paid'},
    {id: 2039, date: '2025-11-11', processed_by: 'Admin E', total: '₱2,300', status: 'Paid'},
    {id: 2038, date: '2025-11-11', processed_by: 'Admin A', total: '₱1,400', status: 'Pending'},
    {id: 2037, date: '2025-11-10', processed_by: 'Admin B', total: '₱980', status: 'Paid'},
    {id: 2036, date: '2025-11-10', processed_by: 'Admin C', total: '₱1,100', status: 'Paid'},
    {id: 2035, date: '2025-11-09', processed_by: 'Admin D', total: '₱760', status: 'Paid'},
    {id: 2034, date: '2025-11-09', processed_by: 'Admin E', total: '₱1,900', status: 'Paid'},
];


// Dummy category performance data (percent values)
const dummyCategoryPerformance = [
    { name: 'Power Tools', pct: 41 },
    { name: 'Hand Tools', pct: 21 },
    { name: 'PPE', pct: 15 },
    { name: 'Electrical', pct: 14 },
    { name: 'Consumables', pct: 9 }
];




// --- Pagination and Rendering ---
function paginate(array, page, perPage) {
    const start = (page - 1) * perPage;
    return array.slice(start, start + perPage);
}
function renderPagination(total, page, perPage, containerId, onPage) {
    const pageCount = Math.ceil(total / perPage);
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    if (pageCount <= 1) return;
    // Left arrow
    const prevLi = document.createElement('li');
    prevLi.className = 'page-item' + (page === 1 ? ' disabled' : '');
    const prevA = document.createElement('a');
    prevA.className = 'page-link';
    prevA.href = '#';
    prevA.innerHTML = '&laquo;';
    prevA.onclick = (e) => { e.preventDefault(); if (page > 1) onPage(page - 1); };
    prevLi.appendChild(prevA);
    container.appendChild(prevLi);
    // Page numbers
    for (let i = 1; i <= pageCount; i++) {
        const li = document.createElement('li');
        li.className = 'page-item' + (i === page ? ' active' : '');
        const a = document.createElement('a');
        a.className = 'page-link';
        a.href = '#';
        a.textContent = i;
        a.onclick = (e) => { e.preventDefault(); onPage(i); };
        li.appendChild(a);
        container.appendChild(li);
    }
    // Right arrow
    const nextLi = document.createElement('li');
    nextLi.className = 'page-item' + (page === pageCount ? ' disabled' : '');
    const nextA = document.createElement('a');
    nextA.className = 'page-link';
    nextA.href = '#';
    nextA.innerHTML = '&raquo;';
    nextA.onclick = (e) => { e.preventDefault(); if (page < pageCount) onPage(page + 1); };
    nextLi.appendChild(nextA);
    container.appendChild(nextLi);
}
function renderTopProductsTable(period = 'day', page = 1) {
    const perPage = 15;
    const tbody = document.querySelector('#top-products-table tbody');
    tbody.innerHTML = '';
    const products = dummyTopProducts[period] || [];
    paginate(products, page, perPage).forEach(prod => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${prod.id}</td>
            <td><img src="${prod.image}" alt="${prod.name}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;"></td>
            <td>${prod.name}</td>
            <td>${prod.category}</td>
            <td>${prod.sold}</td>
        `;
        tbody.appendChild(row);
    });
    renderPagination(products.length, page, perPage, 'top-products-pagination', (p) => renderTopProductsTable(period, p));
}
function renderLowStockTable(page = 1) {
    const perPage = 10;
    const tbody = document.querySelector('#low-stock-table tbody');
    tbody.innerHTML = '';
    paginate(dummyLowStock, page, perPage).forEach(prod => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${prod.id}</td>
            <td><img src="${prod.image}" alt="${prod.name}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;"></td>
            <td>${prod.name}</td>
            <td>${prod.category}</td>
            <td>${prod.stock}</td>
        `;
        tbody.appendChild(row);
    });
    renderPagination(dummyLowStock.length, page, perPage, 'low-stock-pagination', renderLowStockTable);
}
// Render today's transactions in modal
function renderModalTransactionsTable() {
    const tbody = document.querySelector('#modal-trans-table tbody');
    tbody.innerHTML = '';
    const today = new Date().toISOString().slice(0, 10);
    const todaysTrans = dummyRecentTrans.filter(tran => tran.date === today);
    if (todaysTrans.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="4" class="text-center">No transactions for today</td>`;
        tbody.appendChild(row);
    } else {
        todaysTrans.forEach(tran => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${tran.id}</td>
                <td>${tran.date}</td>
                <td>${tran.total}</td>
                <td>${tran.status}</td>
            `;
            tbody.appendChild(row);
        });
    }
}




// --- Download to Excel/PDF ---
function downloadTable(type, format) {
    let data = [];
    let columns = [];
    if (type === 'top-products') {
        const period = document.querySelector('.filter-option.filter-active').dataset.period || 'day';
        data = dummyTopProducts[period] || [];
        columns = ['ID', 'Name', 'Category', 'Sold'];
    } else if (type === 'low-stock') {
        data = dummyLowStock || [];
        columns = ['ID', 'Name', 'Category', 'Stock Left'];
    } else if (type === 'recent-trans') {
        data = dummyRecentTrans || [];
        columns = ['ID', 'Date', 'Total', 'Status'];
    } else if (type === 'top-selling-card') {
        // use the top-selling month input and threshold
        const monthInput = document.getElementById('top-selling-date');
        const month = monthShortFromMonthInput(monthInput ? monthInput.value : null);
        const year = yearFromMonthInput(monthInput ? monthInput.value : null);
        let products = (dummyTopProductsByYear[year] && dummyTopProductsByYear[year][month]) || [];
        if (!products || products.length === 0) products = dummyTopProducts.month || [];
        const thresholdEl = document.getElementById('top-selling-threshold');
        const threshold = thresholdEl ? Math.max(0, parseInt(thresholdEl.value || '0')) : 0;
        data = products.filter(p => (p.sold ?? p.units ?? 0) >= threshold);
        columns = ['Rank', 'Name', 'Category', 'Units'];
    } else if (type === 'low-stock-card') {
        data = dummyLowStock || [];
        columns = ['Product ID', 'Name', 'Current Stock', 'Reorder Level'];
    } else if (type === 'recent-orders-card') {
        data = dummyRecentTrans || [];
        columns = ['ID', 'Date', 'Total', 'Processed By'];
    }


    if (format === 'excel') {
        // Excel export using SheetJS
        const rows = [columns];
        data.forEach((row, idx) => {
            if (type === 'top-products') rows.push([row.id, row.name, row.category, row.sold]);
            else if (type === 'low-stock') rows.push([row.id, row.name, row.category, row.stock]);
            else if (type === 'recent-trans') rows.push([row.id, row.date, row.total, row.status]);
            else if (type === 'top-selling-card') rows.push([idx + 1, row.name, row.category || '', row.sold ?? row.units ?? '']);
            else if (type === 'low-stock-card') rows.push([row.id, row.name, row.stock, 10]);
            else if (type === 'recent-orders-card') rows.push([row.id, row.date, row.total, row.processed_by]);
        });
        const ws = XLSX.utils.aoa_to_sheet(rows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, `${type}.xlsx`);
    } else if (format === 'pdf') {
        // PDF export using jsPDF + autotable
        const doc = new jsPDF();
        const rows = data.map((row, idx) => {
            if (type === 'top-products') return [row.id, row.name, row.category, row.sold];
            else if (type === 'low-stock') return [row.id, row.name, row.category, row.stock];
            else if (type === 'recent-trans') return [row.id, row.date, row.processed_by, row.total, row.status];
            else if (type === 'top-selling-card') return [idx + 1, row.name, row.category || '', row.sold ?? row.units ?? ''];
            else if (type === 'low-stock-card') return [row.id, row.name, row.stock, 10];
            else if (type === 'recent-orders-card') return [row.id, row.date, row.total, row.processed_by];
        });
        doc.autoTable({ head: [columns], body: rows });
        doc.save(`${type}.pdf`);
    }
}


// Small modals for product and order quick view
// --- Renderers for top cards and recent orders (populate with dummy data) ---
// helper to convert YYYY-MM to short month name like 'Nov'
function monthShortFromMonthInput(val) {
    if (!val) return 'Nov';
    const parts = val.split('-');
    if (parts.length < 2) return 'Nov';
    const monthIndex = Number(parts[1]) - 1;
    const names = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return names[monthIndex] || 'Nov';
}


function yearFromMonthInput(val) {
    if (!val) return String(new Date().getFullYear());
    return val.split('-')[0];
}


function renderTopSellingByMonth(month, year) {
    // kept for backward compatibility; same as before
    const tbody = document.querySelector('#top-selling-table tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    const yr = String(year || (new Date()).getFullYear());
    const mon = String(month || 'Nov');
    let products = (dummyTopProductsByYear[yr] && dummyTopProductsByYear[yr][mon]) || [];
    // fallback to generic month data if not found
    if (!products || products.length === 0) products = dummyTopProducts.month || [];
    // apply threshold filter (min units sold)
    const thresholdEl = document.getElementById('top-selling-threshold');
    const threshold = thresholdEl ? Math.max(0, parseInt(thresholdEl.value || '0')) : 0;
    const filtered = products.filter(p => (p.sold ?? p.units ?? 0) >= threshold);
    filtered.slice(0, 10).forEach((p, idx) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${idx + 1}</td>
            <td>
                <div class="d-flex align-items-center">
                    <img src="${p.image || 'https://via.placeholder.com/40'}" alt="" style="width:36px;height:36px;object-fit:cover;border-radius:6px;margin-right:8px;">
                    <div>${p.name}</div>
                </div>
            </td>
            <td>${p.category || '-'}</td>
            <td class="text-end">${p.sold ?? p.units ?? '-'}</td>
        `;
        tbody.appendChild(row);
    });
}


function renderTopSellingByDateInput(dateVal) {
    const mon = monthShortFromMonthInput(dateVal);
    const yr = yearFromMonthInput(dateVal);
    renderTopSellingByMonth(mon, yr);
}


function renderLowStockCard(dateVal) {
    // Now renders category performance into the new widget
    // dateVal is accepted for future server integration; currently not used for filtering dummy data
    const container = document.getElementById('category-performance-list');
    if (!container) return;
    container.innerHTML = '';
    dummyCategoryPerformance.forEach(cat => {
        const row = document.createElement('div');
        row.className = 'mb-2';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-1">
                <div class="small">${cat.name}</div>
                <div class="small text-muted">${cat.pct}%</div>
            </div>
            <div class="progress" style="height:8px;border-radius:6px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: ${cat.pct}%;" aria-valuenow="${cat.pct}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        `;
        container.appendChild(row);
    });
}


function renderRecentOrdersCard(limit = 10) {
    const tbody = document.querySelector('#recent-orders-table tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    const perPage = 10;
    const page = arguments[0] && Number.isInteger(arguments[0]) ? arguments[0] : 1;
    const rows = paginate(dummyRecentTrans || [], page, perPage);
    rows.forEach(r => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${r.id}</td>
            <td>${r.date}</td>
            <td class="text-end">
                <div><strong>${r.total}</strong></div>
                <div class="text-muted small">${r.processed_by}</div>
            </td>
        `;
        tr.onclick = () => showOrderModal(r.id);
        tbody.appendChild(tr);
    });
    // pagination
    renderPagination((dummyRecentTrans || []).length, page, perPage, 'recent-orders-pagination', (p) => renderRecentOrdersCard(p));
}


// --- New: Render transactions today for the interactive tab ---
function renderTransactionsToday() {
    const tbody = document.querySelector('#transactions-today-table tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    const today = new Date().toISOString().slice(0,10);
    const todays = dummyRecentTrans.filter(t => t.date === today);
    if (todays.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="4" class="text-center">No transactions for today</td>`;
        tbody.appendChild(row);
    } else {
        todays.forEach(t => {
            const r = document.createElement('tr');
            r.innerHTML = `
                <td>${t.id}</td>
                <td>${t.date}</td>
                <td>${t.total}</td>
                <td>${t.status}</td>
            `;
            r.onclick = () => showOrderModal(t.id);
            tbody.appendChild(r);
        });
    }
    // update totals in widget — show friendly message when zero
    const ordersWidget = document.getElementById('total-orders-processed');
    if (ordersWidget) {
        if (todays.length === 0) {
            ordersWidget.innerHTML = '<span class="text-muted small">No orders processed</span>';
        } else {
            ordersWidget.textContent = `${todays.length} orders processed`;
        }
    }
    // compute today's sales total and comparison with yesterday
    computeSalesComparison();
}


// parse currency string like '₱1,450' to number 1450
function parseCurrency(v) {
    if (!v) return 0;
    return Number(String(v).replace(/[^0-9.-]+/g, '')) || 0;
}


function formatCurrency(n) {
    return '₱' + Number(n).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}


function computeSalesComparison() {
    const today = new Date().toISOString().slice(0,10);
    const yesterday = new Date(Date.now() - 24*60*60*1000).toISOString().slice(0,10);
    const todays = dummyRecentTrans.filter(t => t.date === today);
    const yest = dummyRecentTrans.filter(t => t.date === yesterday);
    const totalToday = todays.reduce((s,t) => s + parseCurrency(t.total), 0);
    const totalYest = yest.reduce((s,t) => s + parseCurrency(t.total), 0);
    const changeEl = document.getElementById('total-sales-change');
    const amtEl = document.getElementById('total-sales-amount');
    if (amtEl) amtEl.textContent = formatCurrency(totalToday);
    if (!changeEl) return;
    if (totalYest === 0) {
        if (totalToday === 0) {
            changeEl.innerHTML = '<span class="text-muted small">No change</span>';
            changeEl.className = 'small text-muted';
        } else {
            changeEl.innerHTML = '<span class="text-success small">▲ New sales today</span>';
            changeEl.className = 'small text-success';
        }
        return;
    }
    const pct = ((totalToday - totalYest) / totalYest) * 100;
    const rounded = Math.round(pct * 10) / 10;
    if (rounded >= 0) {
        changeEl.innerHTML = `<span class="text-success">▲ ${Math.abs(rounded)}% from yesterday</span>`;
        changeEl.className = 'small text-success';
    } else {
        changeEl.innerHTML = `<span class="text-danger">▼ ${Math.abs(rounded)}% from yesterday</span>`;
        changeEl.className = 'small text-danger';
    }
}


// --- New: Low Stock Alert rendering ---
function renderLowStockAlert() {
    const container = document.getElementById('low-stock-alert-container');
    if (!container) return;
    const reorderLevel = 10;
    const lowItems = dummyLowStock.filter(p => p.stock < reorderLevel);
    if (lowItems.length === 0) { container.innerHTML = ''; return; }
    // pagination params
    const perPage = 15;
    // render helper will be called by pagination
    function renderPage(page = 1) {
        const pageItems = paginate(lowItems, page, perPage);
        let html = `
            <div class="card chart-card">
                <div class="p-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="text-warning" style="font-size:20px;margin-top:4px;"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        <div>
                            <div class="fw-bold">Low Stock Alert</div>
                            <div class="text-muted small">Items that need restocking</div>
                        </div>
                    </div>
                    <div style="height:12px;"></div>
        `;
        pageItems.forEach(p => {
            html += `
                <div class="p-3 mb-2" style="background:#fff7f0;border-radius:8px;display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <div class="fw-semibold">${p.name}</div>
                        <div class="text-muted small">SKU: PROD-${p.id.toString().padStart(3,'0')}</div>
                    </div>
                    <div style="text-align:right">
                        <div class="badge bg-warning text-dark" style="font-size:12px;padding:.45rem .6rem;border-radius:8px;">${p.stock} left</div>
                        <div class="text-muted small" style="margin-top:6px;">Min: ${reorderLevel}</div>
                    </div>
                </div>
            `;
        });
        // pagination container
        html += `<div class="d-flex justify-content-end mt-2"><ul class="pagination pagination-sm mb-0" id="low-stock-alert-pagination"></ul></div>`;
        html += `</div></div>`;
        container.innerHTML = html;
        renderPagination(lowItems.length, page, perPage, 'low-stock-alert-pagination', (p) => renderPage(p));
    }
    renderPage(1);
}


// --- Tab switching ---
function switchDashboardTab(tab) {
    document.querySelectorAll('#interactive-nav .btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.dashboard-tab').forEach(d => d.style.display = 'none');
    if (tab === 'transactions') {
        document.querySelector('#interactive-nav .btn').classList.add('active');
        document.querySelector('#tab-transactions').style.display = '';
    }
    const buttons = Array.from(document.querySelectorAll('#interactive-nav .btn'));
    buttons.forEach(btn => { if (btn.textContent.trim().toLowerCase().includes(tab.replace('-',' '))) btn.classList.add('active'); });
    if (tab === 'top-selling') document.getElementById('tab-top-selling').style.display = '';
    if (tab === 'low-stock') document.getElementById('tab-low-stock').style.display = '';
}


function downloadActiveTabPdf() {
    const visible = document.querySelector('.dashboard-tab:not([style*="display: none"])');
    if (!visible) return;
    if (visible.id === 'tab-top-selling') downloadTable('top-selling-card','pdf');
    else if (visible.id === 'tab-low-stock') downloadTable('low-stock-card','pdf');
    else if (visible.id === 'tab-transactions') downloadTable('recent-trans','pdf');
}
function showProductModal(productId) {
    const product = [...dummyTopProducts.day, ...dummyTopProducts.week, ...dummyTopProducts.month].find(p => p.id === productId) || dummyLowStock.find(p => p.id === productId);
    const modalId = 'quickProductModal';
    let modal = document.getElementById(modalId);
    if (!modal) {
        const html = `
        <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="${modalId}Body"></div>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', html);
        modal = document.getElementById(modalId);
    }
    const body = modal.querySelector('#' + modalId + 'Body');
    if (!product) {
        body.innerHTML = '<div class="text-center text-muted">Product not found</div>';
    } else {
        body.innerHTML = `
            <div class="d-flex gap-3 align-items-center">
                <img src="${product.image || 'https://via.placeholder.com/64'}" alt="" style="width:64px;height:64px;object-fit:cover;border-radius:6px;">
                <div>
                    <div class="fw-semibold">${product.name}</div>
                    <div class="text-muted small">${product.category || ''}</div>
                    <div class="mt-2">Sold: <strong>${product.sold ?? '-'}</strong></div>
                </div>
            </div>
        `;
    }
    new bootstrap.Modal(modal).show();
}


function showOrderModal(orderId) {
    const order = dummyRecentTrans.find(t => t.id === orderId);
    const modalId = 'quickOrderModal';
    let modal = document.getElementById(modalId);
    if (!modal) {
        const html = `
        <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="${modalId}Body"></div>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', html);
        modal = document.getElementById(modalId);
    }
    const body = modal.querySelector('#' + modalId + 'Body');
    if (!order) {
        body.innerHTML = '<div class="text-center text-muted">Order not found</div>';
    } else {
        body.innerHTML = `
            <div>
                <div class="d-flex justify-content-between">
                    <div><strong>Order #${order.id}</strong></div>
                    <div class="text-muted">${order.date}</div>
                </div>
                <div class="mt-2">Processed By: <strong>${order.processed_by}</strong></div>
                <div class="mt-2">Total: <strong>${order.total}</strong></div>
                <div class="mt-2">Status: <span class="badge bg-secondary">${order.status}</span></div>
            </div>
        `;
    }
    new bootstrap.Modal(modal).show();
}


// Wire up selects and button
document.addEventListener('DOMContentLoaded', function() {
    const monthSelect = document.getElementById('top-month-select');
    const yearSelect = document.getElementById('top-year-select');
    if (monthSelect) {
        monthSelect.addEventListener('change', () => renderTopSellingByMonth(monthSelect.value, yearSelect.value));
    }
    if (yearSelect) {
        yearSelect.addEventListener('change', () => renderTopSellingByMonth(monthSelect.value, yearSelect.value));
    }
    // new selects inside Top Selling tab
    const tsDate = document.getElementById('top-selling-date');
    if (tsDate) tsDate.addEventListener('change', () => renderTopSellingByDateInput(tsDate.value));
    const tsThreshold = document.getElementById('top-selling-threshold');
    if (tsThreshold) tsThreshold.addEventListener('input', () => renderTopSellingByDateInput(tsDate ? tsDate.value : null));
    const catDateInput = document.getElementById('category-performance-date');
    if (catDateInput) catDateInput.addEventListener('change', () => renderLowStockCard(catDateInput.value));
    // create-po button removed from UI; no handler needed


    // Initial render
    // render using default month/year selects for top-selling
    const initMonthVal = (document.getElementById('top-selling-date') || {}).value || '2025-11';
    renderTopSellingByDateInput(initMonthVal);
    const initCatDate = (document.getElementById('category-performance-date') || {}).value || '2025-11';
    renderLowStockCard(initCatDate);
    renderRecentOrdersCard(1);
    renderTransactionsToday();
    renderLowStockAlert();
    // default tab
    switchDashboardTab('transactions');
    // Top cards pager initialization (if multiple pages are present)
    (function initTopCardsPager(){
        const pages = Array.from(document.querySelectorAll('.top-cards-page'));
        if (!pages || pages.length === 0) return;
        let current = 0;
        const total = pages.length;
        const prev = document.getElementById('top-cards-prev');
        const next = document.getElementById('top-cards-next');
        const pageNum = document.getElementById('top-cards-page-num');
        function updatePager(){
            pages.forEach((p,i)=> p.style.display = (i===current)?'flex':'none');
            if (pageNum) pageNum.textContent = `${current+1}/${total}`;
            if (prev) prev.disabled = current === 0;
            if (next) next.disabled = current === total - 1;
        }
        if (prev) prev.addEventListener('click', (e)=>{ e.preventDefault(); if (current>0) { current--; updatePager(); } });
        if (next) next.addEventListener('click', (e)=>{ e.preventDefault(); if (current<total-1) { current++; updatePager(); } });
        updatePager();
    })();
});


// SheetJS and jsPDF CDN
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.0/jspdf.plugin.autotable.min.js"></script>
@endpush









