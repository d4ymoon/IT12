@extends('layouts.app')
@section('title', 'Product Returns - ATIN Admin')
@push('styles')
<link href="{{ asset('css/page-style.css') }}" rel="stylesheet">
@endpush
@section('content')
    @include('components.alerts')

    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">
                <b>Product Returns</b>
            </h2>
            <a href="{{ route('returns.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>
                Process New Return
            </a>
        </div>
    </div>

    <!-- Search Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <!-- Search & Clear -->
                <div class="col-md-6">
                    <div class="d-flex gap-2 align-items-center">
                        <form action="{{ route('returns.index') }}" method="GET" class="d-flex w-90">
                            <div class="input-group search-box w-100">
                                <input type="text" class="form-control" name="search" placeholder="Search by Sale ID, or Return Reason..." value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                        
                        @if(request('search'))
                            <a href="{{ route('returns.index') }}" class="btn btn-outline-danger flex-shrink-0" title="Clear search">
                                <i class="bi bi-x-circle"></i> Clear
                            </a>
                        @endif
                    </div>
                </div>
                
                <!-- Date Filter -->
                <div class="col-md-6">
                    <form action="{{ route('returns.index') }}" method="GET" class="d-flex gap-2">
                        <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}" placeholder="Start Date">
                        <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}" placeholder="End Date">
                        <button type="submit" class="btn btn-outline-primary">Filter</button>
                        @if(request('start_date') || request('end_date'))
                            <a href="{{ route('returns.index') }}" class="btn btn-outline-secondary">Clear Dates</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Returns Table -->
    <div class="table-container"> 
        <div class="table-responsive">
            <!-- Results Count -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted">
                    @if(request('search') || request('start_date') || request('end_date'))
                        Displaying {{ $returns->count() }} of {{ $returns->total() }} results
                        @if(request('search'))
                            for "{{ request('search') }}"
                        @endif
                    @else
                        Displaying {{ $returns->count() }} of {{ $returns->total() }} returns
                    @endif
                </div>
                <div class="text-muted">
                    Total Refunded: ${{ number_format($totalRefunded, 2) }}
                </div>
            </div>
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Return ID</th>
                        <th>Sale ID</th>
                        <th>Return Reason</th>
                        <th>Total Refund</th>
                        <th>Items Returned</th>
                        <th>Processed By</th>
                        <th>Return Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                    <tr>
                        <td>{{ $return->id }}</td>
                        <td>{{ $return->sale_id }}</td>
                        <td>{{ $return->return_reason }}</td>
                        <td class="text-danger">-${{ number_format($return->total_refund_amount, 2) }}</td>
                        <td>{{ $return->returnItems->count() }} item(s)</td>
                        <td>{{ $return->user->f_name ?? 'N/A' }}</td>
                        <td>{{ $return->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-info btn-action view-return" data-id="{{ $return->id }}" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <p class="mt-3 mb-0">No returns found</p>
                            @if(request('search') || request('start_date') || request('end_date'))
                                <a href="{{ route('returns.index') }}" class="btn btn-primary mt-2">Clear Filters</a>
                            @else
                                <a href="{{ route('returns.create') }}" class="btn btn-primary mt-2">Process First Return</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $returns->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>

    <!-- View Return Modal -->
    <div class="modal fade" id="viewReturnModal" tabindex="-1" aria-labelledby="viewReturnModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewReturnModalLabel">
                        <i class="bi bi-eye me-2"></i>
                        Return Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Return Information</h6>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item px-0">
                                    <small class="text-muted d-block">Return ID</small>
                                    <span class="fw-semibold" id="viewReturnId"></span>
                                </div>
                                <div class="list-group-item px-0">
                                    <small class="text-muted d-block">Sale ID</small>
                                    <span class="fw-semibold" id="viewSaleId"></span>
                                </div>
                                <div class="list-group-item px-0">
                                    <small class="text-muted d-block">Customer</small>
                                    <span class="fw-semibold" id="viewCustomerName"></span>
                                </div>
                                <div class="list-group-item px-0">
                                    <small class="text-muted d-block">Customer Contact</small>
                                    <span class="fw-semibold" id="viewCustomerContact"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Financial Details</h6>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item px-0">
                                    <small class="text-muted d-block">Total Refund</small>
                                    <span class="fw-semibold text-danger" id="viewTotalRefund"></span>
                                </div>
                                <div class="list-group-item px-0">
                                    <small class="text-muted d-block">Refund Method</small>
                                    <span class="fw-semibold" id="viewRefundMethod"></span>
                                </div>
                                <div class="list-group-item px-0">
                                    <small class="text-muted d-block">Return Reason</small>
                                    <span class="fw-semibold" id="viewReturnReason"></span>
                                </div>
                                <div class="list-group-item px-0">
                                    <small class="text-muted d-block">Reference No</small>
                                    <span class="fw-semibold" id="viewReferenceNo"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6>Returned Items</h6>
                    <div class="table-responsive">
                        <table class="table table-sm" id="returnItemsTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total Refund</th>
                                    <th>Condition</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Items will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <h6>Notes</h6>
                        <div class="card">
                            <div class="card-body">
                                <p class="mb-0" id="viewReturnNotes"></p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted">Processed by: <span id="viewProcessedBy"></span> on <span id="viewReturnDate"></span></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // View Return Details
        document.querySelectorAll('.view-return').forEach(button => {
            button.addEventListener('click', function() {
                const returnId = this.getAttribute('data-id');
                
                fetch(`/returns/${returnId}`)
                    .then(response => response.json())
                    .then(returnData => {
                        // Populate basic info
                        document.getElementById('viewReturnId').textContent = returnData.id;
                        document.getElementById('viewSaleId').textContent = returnData.sale_id;
                        document.getElementById('viewCustomerName').textContent = returnData.sale.customer_name || 'N/A';
                        document.getElementById('viewCustomerContact').textContent = returnData.sale.customer_contact || 'N/A';
                        document.getElementById('viewTotalRefund').textContent = '-$' + parseFloat(returnData.total_refund_amount).toFixed(2);
                        document.getElementById('viewRefundMethod').textContent = returnData.refund_payment.payment_method;
                        document.getElementById('viewReturnReason').textContent = returnData.return_reason;
                        document.getElementById('viewReferenceNo').textContent = returnData.refund_payment.reference_no || 'Not applicable (Cash refund)';
                        document.getElementById('viewProcessedBy').textContent = returnData.user.f_name + ' ' + returnData.user.l_name;
                        document.getElementById('viewReturnDate').textContent = new Date(returnData.created_at).toLocaleDateString('en-US', {
                            month: 'short', day: '2-digit', year: 'numeric'
                        }) + ' ' + new Date(returnData.created_at).toLocaleTimeString('en-US', {
                            hour: '2-digit', minute: '2-digit', hour12: true
                        });
                        document.getElementById('viewReturnNotes').textContent = returnData.notes || 'No notes provided.';

                        // Populate return items
                        const itemsTable = document.getElementById('returnItemsTable').querySelector('tbody');
                        itemsTable.innerHTML = '';
                        
                        returnData.return_items.forEach(item => {
                            const row = itemsTable.insertRow();
                            row.innerHTML = `
                                <td>${item.product.name}</td>
                                <td>${item.product.sku}</td>
                                <td>${item.quantity_returned}</td>
                                <td>$${parseFloat(item.refunded_price_per_unit).toFixed(2)}</td>
                                <td>$${parseFloat(item.total_line_refund).toFixed(2)}</td>
                                <td>${item.inventory_adjusted ? 'Resaleable' : 'Damaged'}</td>
                                <td>${item.inventory_adjusted ? 'Restocked' : 'Scrapped/Loss'}</td>
                            `;
                        });

                        const modal = new bootstrap.Modal(document.getElementById('viewReturnModal'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error fetching return details:', error);
                        alert('Error loading return details. Please try again.');
                    });
            });
        });
    </script>
    @endpush
@endsection