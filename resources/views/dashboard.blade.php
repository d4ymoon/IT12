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
            </p>
        </div>
        <div class="col-md-6 text-end">
            <div class="text-muted">
                Logged in as: <strong>{{ session('username') }}</strong>
            </div>
        </div>
    </div>
</div>


<div style="height: 24px;"></div>








<!-- Total Transactions Widget -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center shadow-sm" id="total-transactions-widget" style="cursor:pointer;transition:0.2s;">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Transactions Today</h6>
                <div class="display-4 fw-bold" id="total-transactions-count">0</div>
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


<!-- Top Selling Products Table -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Top Selling Products</h5>
            <div>
                <button class="btn btn-sm btn-outline-success" title="Download Excel" onclick="downloadTable('top-products', 'excel')">
                    <i class="bi bi-file-earmark-excel"></i>
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="top-products-table">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Sold</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <nav>
            <ul class="pagination pagination-sm justify-content-end mt-2 mb-0" id="top-products-pagination"></ul>
        </nav>
    </div>
</div>


<!-- Low Stock Products Table -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Low Stock Products</h5>
            <div>
                <button class="btn btn-sm btn-outline-success" title="Download Excel" onclick="downloadTable('low-stock', 'excel')">
                    <i class="bi bi-file-earmark-excel"></i>
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="low-stock-table">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Stock Left</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <nav>
            <ul class="pagination pagination-sm justify-content-end mt-2 mb-0" id="low-stock-pagination"></ul>
        </nav>
    </div>
</div>




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
    {id: 2031, date: '2025-11-18', customer: 'Juan Dela Cruz', total: '₱2,150', status: 'Paid'},
    {id: 2030, date: '2025-11-18', customer: 'Maria Santos', total: '₱980', status: 'Paid'},
    {id: 2029, date: '2025-11-17', customer: 'Pedro Reyes', total: '₱640', status: 'Paid'},
    {id: 2028, date: '2025-11-17', customer: 'Ana Lim', total: '₱5,320', status: 'Paid'},
    {id: 2027, date: '2025-11-16', customer: 'Jose Tan', total: '₱720', status: 'Cancelled'},
    {id: 2026, date: '2025-11-16', customer: 'Liza Cruz', total: '₱1,200', status: 'Paid'},
    {id: 2025, date: '2025-11-15', customer: 'Mark Lee', total: '₱2,800', status: 'Paid'},
    {id: 2024, date: '2025-11-15', customer: 'Grace Yu', total: '₱1,100', status: 'Paid'},
    {id: 2023, date: '2025-11-14', customer: 'Rico Chan', total: '₱3,000', status: 'Paid'},
    {id: 2022, date: '2025-11-14', customer: 'Ella Cruz', total: '₱2,500', status: 'Paid'},
    {id: 2021, date: '2025-11-13', customer: 'Sam Lim', total: '₱900', status: 'Paid'},
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
    const perPage = 10;
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
        data = dummyTopProducts[period];
        columns = ['ID', 'Name', 'Category', 'Sold'];
    } else if (type === 'low-stock') {
        data = dummyLowStock;
        columns = ['ID', 'Name', 'Category', 'Stock Left'];
    } else if (type === 'recent-trans') {
        data = dummyRecentTrans;
        columns = ['ID', 'Date', 'Total', 'Status'];
    }
    if (format === 'excel') {
        // Excel export using SheetJS
        const rows = [columns];
        data.forEach(row => {
            if (type === 'top-products') rows.push([row.id, row.name, row.category, row.sold]);
            else if (type === 'low-stock') rows.push([row.id, row.name, row.category, row.stock]);
            else if (type === 'recent-trans') rows.push([row.id, row.date, row.total, row.status]);
        });
        const ws = XLSX.utils.aoa_to_sheet(rows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, `${type}.xlsx`);
    } else if (format === 'pdf') {
        // PDF export using jsPDF + autotable
        const doc = new jsPDF();
        const rows = data.map(row => {
            if (type === 'top-products') return [row.id, row.name, row.category, row.sold];
            else if (type === 'low-stock') return [row.id, row.name, row.category, row.stock];
            else if (type === 'recent-trans') return [row.id, row.date, row.customer, row.total, row.status];
        });
        doc.autoTable({ head: [columns], body: rows });
        doc.save(`${type}.pdf`);
    }
}


// --- Initial Render ---
let currentTopProductsPeriod = 'day';
renderTopProductsTable(currentTopProductsPeriod, 1);
renderLowStockTable(1);


// Set total transactions count and widget click
function updateTotalTransactionsWidget() {
    const today = new Date().toISOString().slice(0, 10);
    const todaysTrans = dummyRecentTrans.filter(tran => tran.date === today);
    document.getElementById('total-transactions-count').textContent = todaysTrans.length;
    document.getElementById('total-transactions-widget').onclick = function() {
        renderModalTransactionsTable();
        const modal = new bootstrap.Modal(document.getElementById('transactionsModal'));
        modal.show();
    };
}
updateTotalTransactionsWidget();


// --- Update Top Products table when filter changes ---
document.querySelectorAll('.filter-option').forEach(btn => {
    btn.addEventListener('click', () => {
        currentTopProductsPeriod = btn.dataset.period;
        renderTopProductsTable(currentTopProductsPeriod, 1);
    });
});
if (document.getElementById('day-picker')) {
    document.getElementById('day-picker').addEventListener('change', function() {
        currentTopProductsPeriod = 'day';
        renderTopProductsTable('day', 1);
    });
}
if (document.getElementById('week-picker')) {
    document.getElementById('week-picker').addEventListener('change', function() {
        currentTopProductsPeriod = 'week';
        renderTopProductsTable('week', 1);
    });
}
// SheetJS and jsPDF CDN
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.0/jspdf.plugin.autotable.min.js"></script>
@endpush



