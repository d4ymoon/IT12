@extends('layouts.app')
@section('title', 'Sales History - ATIN Admin')
@push('styles')
<link href="{{ asset('css/page-style.css') }}" rel="stylesheet">
@endpush
@section('content')
    @include('components.alerts')

    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">
                <b>Sales History</b>
            </h2>
        </div>
    </div>

    <!-- Search Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <!-- Search & Clear -->
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <form action="{{ route('sales.index') }}" method="GET" class="d-flex w-90 me-2">
                            <div class="input-group search-box w-90">
                                <input type="text" class="form-control" name="search" placeholder="Search by sale ID, customer name..." value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                        
                        @if(request('search'))
                            <a href="{{ route('sales.index') }}" class="btn btn-outline-danger flex-shrink-0" title="Clear search">
                                <i class="bi bi-x-circle"></i> Clear
                            </a>
                        @endif
                    </div>
                </div>
                
                <!-- Date Filter -->
                <div class="col-md-6">
                    <form action="{{ route('sales.index') }}" method="GET" class="d-flex gap-2">
                        <input type="date" class="form-control" name="date" value="{{ request('date') }}" placeholder="Filter by date">
                        <button type="submit" class="btn btn-outline-primary">Filter</button>
                        @if(request('date'))
                            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Clear Date</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="table-container">
        <div class="table-responsive">
            <!-- Results Count -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted">
                    @if(request('search') || request('date'))
                        Displaying {{ $sales->count() }} of {{ $sales->total() }} results
                        @if(request('search')) for "{{ request('search') }}"@endif
                        @if(request('date')) on {{ request('date') }}@endif
                    @else
                        Displaying {{ $sales->count() }} of {{ $sales->total() }} sales
                    @endif
                </div>
            </div>
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Sale ID</th>
                        <th>Date & Time</th>
                        <th>Cashier</th>
                        <th>Items</th>
                        <th>Total Amount</th>
                        <th>Payment Method</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td><strong>#{{ $sale->id }}</strong></td>
                        <td>{{ $sale->sale_date->format('M d, Y h:i A') }}</td>
                        <td>{{ $sale->user->name ?? 'N/A' }}</td>
                        <td>{{ $sale->items->count() }} items</td>
                        <td class="fw-bold text-success">₱{{ number_format($sale->items->sum(function($item) { return $item->quantity_sold * $item->unit_price; }), 2) }}</td>
                        <td>
                            @if($sale->payment)
                                <span class="badge bg-primary">{{ $sale->payment->payment_method }}</span>
                            @else
                                <span class="badge bg-secondary">N/A</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-info btn-action view-sale" data-id="{{ $sale->id }}" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            <a href="{{ route('sales.receipt', $sale->id) }}" class="btn btn-sm btn-outline-success btn-action" title="Print Receipt" target="_blank">
                                <i class="bi bi-receipt"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            No sales found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $sales->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>

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

    @push('scripts')
    <script>
        // View Sale Details
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
                        document.getElementById('viewSaleCashier').textContent = sale.user ? sale.user.name : 'N/A';
                        document.getElementById('viewSaleCustomer').textContent = sale.customer_name || 'Walk-in Customer';
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
    </script>
    @endpush
@endsection