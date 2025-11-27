@extends('layouts.app')

@section('title', 'ATIN - Sales Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: #06448a;">
        <i class="bi bi-graph-up me-2"></i>Sales Reports
    </h2>
</div>

<!-- Date Range Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form id="reportFilterForm" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Date Range</label>
                <select class="form-select" name="date_range" id="dateRange">
                    <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ $dateRange == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="thisweek" {{ $dateRange == 'thisweek' ? 'selected' : '' }}>This Week</option>
                    <option value="lastweek" {{ $dateRange == 'lastweek' ? 'selected' : '' }}>Last Week</option>
                    <option value="thismonth" {{ $dateRange == 'thismonth' ? 'selected' : '' }}>This Month</option>
                    <option value="lastmonth" {{ $dateRange == 'lastmonth' ? 'selected' : '' }}>Last Month</option>
                    <option value="thisyear" {{ $dateRange == 'thisyear' ? 'selected' : '' }}>This Year</option>
                    <option value="custom" {{ $dateRange == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            <div class="col-md-3" id="customDateRange" style="{{ $dateRange == 'custom' ? 'display: block;' : 'display: none;' }}">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-3" id="customDateRangeEnd" style="{{ $dateRange == 'custom' ? 'display: block;' : 'display: none;' }}">
                <label class="form-label">End Date</label>
                <input type="date" class="form-control" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-filter me-1"></i>Apply Filter
                </button>
                <button type="button" class="btn btn-outline-success" onclick="exportReport()">
                    <i class="bi bi-file-pdf me-1"></i>Export PDF
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card report-card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Total Transactions</h6>
                        <h3 class="fw-bold text-primary">{{ $salesData['summaryStats']->total_transactions ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-receipt text-primary" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card report-card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Items Sold</h6>
                        <h3 class="fw-bold text-success">{{ $salesData['summaryStats']->total_items_sold ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-box-seam text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card report-card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Net Revenue</h6>
                        <h3 class="fw-bold text-info">₱{{ number_format($salesData['summaryStats']->net_revenue ?? 0, 2) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-currency-dollar text-info" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card report-card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Avg. Transaction</h6>
                        <h3 class="fw-bold text-warning">₱{{ number_format($salesData['summaryStats']->avg_transaction_value ?? 0, 2) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-graph-up text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales Reports Content -->
@include('reports.partials.sales')

<!-- View Sale Modal -->
<div class="modal fade" id="viewSaleModal" tabindex="-1" aria-labelledby="viewSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSaleModalLabel">
                    <i class="bi bi-eye me-2"></i>
                    Sale Details - #<span id="viewSaleId"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Sale Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Sale Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-0">
                                        <small class="text-muted d-block">Sale ID</small>
                                        <span class="fw-semibold" id="viewSaleNumber"></span>
                                    </div>
                                    <div class="list-group-item px-0">
                                        <small class="text-muted d-block">Date & Time</small>
                                        <span class="fw-semibold" id="viewSaleDate"></span>
                                    </div>
                                    <div class="list-group-item px-0">
                                        <small class="text-muted d-block">Cashier</small>
                                        <span class="fw-semibold" id="viewSaleCashier"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Customer Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-0">
                                        <small class="text-muted d-block">Customer Name</small>
                                        <span class="fw-semibold" id="viewSaleCustomer"></span>
                                    </div>
                                    <div class="list-group-item px-0">
                                        <small class="text-muted d-block">Contact</small>
                                        <span class="fw-semibold" id="viewSaleContact"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Items Sold</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="viewSaleItems">
                                    <!-- Items will be populated by JavaScript -->
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Grand Total:</td>
                                        <td class="text-end fw-bold text-success" id="viewSaleTotal"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Payment Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush" id="viewSalePayment">
                            <!-- Payment info will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-success" id="printReceiptBtn" target="_blank">
                    <i class="bi bi-receipt me-1"></i> Print Receipt
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .report-card {
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: none;
        margin-bottom: 20px;
        transition: transform 0.2s;
    }
    .report-card:hover {
        transform: translateY(-2px);
    }
    .table th {
        background-color: #f8f9fa;
        color: #06448a;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
    // Show/hide custom date range
    document.getElementById('dateRange').addEventListener('change', function() {
        const isCustom = this.value === 'custom';
        document.getElementById('customDateRange').style.display = isCustom ? 'block' : 'none';
        document.getElementById('customDateRangeEnd').style.display = isCustom ? 'block' : 'none';
    });

    function exportReport() {
        // Implement PDF export functionality
        alert('Sales Report PDF export functionality would be implemented here');
    }

    // Auto-submit form when date range changes (except custom)
    document.getElementById('dateRange').addEventListener('change', function() {
        if (this.value !== 'custom') {
            document.getElementById('reportFilterForm').submit();
        }
    });

    // View Sale Details
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.view-sale').forEach(button => {
            button.addEventListener('click', function() {
                const saleId = this.getAttribute('data-id');
                
                fetch(`/sales/${saleId}/details`)
                    .then(response => response.json())
                    .then(sale => {
                        // Update modal title and basic info
                        document.getElementById('viewSaleId').textContent = sale.id;
                        document.getElementById('viewSaleNumber').textContent = '#' + sale.id;
                        document.getElementById('viewSaleDate').textContent = new Date(sale.sale_date).toLocaleString();
                        document.getElementById('viewSaleCashier').textContent = sale.user ? (sale.user.f_name + ' ' + sale.user.l_name) : 'N/A';
                        document.getElementById('viewSaleCustomer').textContent = sale.customer_name || 'N/A';
                        document.getElementById('viewSaleContact').textContent = sale.customer_contact || 'N/A';
                        
                        // Update items table
                        const itemsContainer = document.getElementById('viewSaleItems');
                        itemsContainer.innerHTML = '';
                        
                        let total = 0;
                        sale.items.forEach(item => {
                            const itemTotal = item.quantity_sold * item.unit_price;
                            total += itemTotal;
                            
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${item.product ? item.product.name : 'N/A'}</td>
                                <td class="text-center">${item.quantity_sold}</td>
                                <td class="text-end">₱${parseFloat(item.unit_price).toFixed(2)}</td>
                                <td class="text-end">₱${itemTotal.toFixed(2)}</td>
                            `;
                            itemsContainer.appendChild(row);
                        });
                        
                        // Update total
                        document.getElementById('viewSaleTotal').textContent = `₱${total.toFixed(2)}`;
                        
                        // Update payment information
                        const paymentContainer = document.getElementById('viewSalePayment');
                        paymentContainer.innerHTML = '';

                        if (sale.payment) {
                            const payment = sale.payment;
                            paymentContainer.innerHTML = `
                                <div class="list-group-item px-0">
                                    <small class="text-muted d-block">Payment Method</small>
                                    <span class="fw-semibold">${payment.payment_method}</span>
                                </div>
                                <div class="list-group-item px-0">
                                    <small class="text-muted d-block">Amount Tendered</small>
                                    <span class="fw-semibold">₱${parseFloat(payment.amount_tendered).toFixed(2)}</span>
                                </div>
                                <div class="list-group-item px-0">
                                    <small class="text-muted d-block">Change Given</small>
                                    <span class="fw-semibold">₱${parseFloat(payment.change_given).toFixed(2)}</span>
                                </div>
                                ${payment.reference_no ? `
                                <div class="list-group-item px-0">
                                    <small class="text-muted d-block">Reference Number</small>
                                    <span class="fw-semibold">${payment.reference_no}</span>
                                </div>
                                ` : ''}
                            `;
                        } else {
                            paymentContainer.innerHTML = `
                                <div class="list-group-item px-0">
                                    <span class="text-muted">No payment information available</span>
                                </div>
                            `;
                        }
                        
                        // Update print receipt button
                        document.getElementById('printReceiptBtn').href = `/sales/${sale.id}/receipt`;
                        
                        // Show modal
                        const modal = new bootstrap.Modal(document.getElementById('viewSaleModal'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error fetching sale details:', error);
                        alert('Error loading sale details');
                    });
            });
        });
    });
</script>
@endpush