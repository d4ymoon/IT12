@extends('layouts.app')
@section('title', 'Stock Adjustments - ATIN Admin')
@push('styles')
<link href="{{ asset('css/page-style.css') }}" rel="stylesheet">
<style>
    .financial-impact-negative {
        color: #dc3545;
        font-weight: bold;
    }
    .financial-impact-positive {
        color: #198754;
        font-weight: bold;
    }
    .quantity-negative {
        color: #dc3545;
        font-weight: bold;
    }
    .quantity-positive {
        color: #198754;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
    @include('components.alerts')
    
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">
                <b>Stock Adjustments</b>
            </h2>
            <a href="{{ route('stock-adjustments.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>
                New Adjustment
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
        <!-- Search & Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('stock-adjustments.index') }}">
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input type="hidden" name="direction" value="{{ $direction }}">
                    
                    <div class="row g-3 align-items-center">
                        <!-- Search & Clear -->
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="input-group search-box w-100 me-2">
                                    <input type="text" class="form-control" name="search" placeholder="Search by product, SKU, reason..." value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                                
                                @if(request('search') || request('adjustment_type') || request('start_date') || request('end_date'))
                                    <a href="{{ route('stock-adjustments.index') }}" class="btn btn-outline-danger flex-shrink-0" title="Clear filters">
                                        <i class="bi bi-x-circle"></i> Clear
                                    </a>
                                @endif
                            </div>
                        </div>
    
                        <!-- Sort -->
                        <div class="col-md-4">
                            <div class="d-flex gap-2 justify-content-end">
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-sort-down me-1"></i>Sort
                                        @if($sort)
                                            <small class="ms-1">({{ $direction == 'asc' ? '↑' : '↓' }})</small>
                                        @endif
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item {{ $sort == 'id' ? 'active' : '' }}" 
                                               href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => $sort == 'id' && $direction == 'asc' ? 'desc' : 'asc']) }}">
                                            ID @if($sort == 'id') <i class="bi bi-arrow-{{ $direction == 'asc' ? 'up' : 'down' }} float-end"></i> @endif
                                        </a></li>
                                        <li><a class="dropdown-item {{ $sort == 'adjustment_date' ? 'active' : '' }}" 
                                               href="{{ request()->fullUrlWithQuery(['sort' => 'adjustment_date', 'direction' => $sort == 'adjustment_date' && $direction == 'asc' ? 'desc' : 'asc']) }}">
                                            Date @if($sort == 'adjustment_date') <i class="bi bi-arrow-{{ $direction == 'asc' ? 'up' : 'down' }} float-end"></i> @endif
                                        </a></li>
                                        <li><a class="dropdown-item {{ $sort == 'adjustment_type' ? 'active' : '' }}" 
                                               href="{{ request()->fullUrlWithQuery(['sort' => 'adjustment_type', 'direction' => $sort == 'adjustment_type' && $direction == 'asc' ? 'desc' : 'asc']) }}">
                                            Type @if($sort == 'adjustment_type') <i class="bi bi-arrow-{{ $direction == 'asc' ? 'up' : 'down' }} float-end"></i> @endif
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <!-- Additional Filters -->
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label class="form-label">Adjustment Type</label>
                            <select class="form-select" name="adjustment_type" onchange="this.form.submit()">
                                <option value="">All Types</option>
                                @foreach($adjustmentTypes as $type)
                                    <option value="{{ $type }}" {{ request('adjustment_type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100" style="display: none;">Apply Filters</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    <!-- Stock Adjustments Table -->
    <div class="table-container">    
        <div class="table-responsive">
            <!-- Results Count -->
            <div class="text-muted mb-3">
                @if(request('search') || request('adjustment_type') || request('start_date') || request('end_date'))
                    Displaying {{ $stockAdjustments->count() }} of {{ $stockAdjustments->total() }} filtered results
                @else
                    Displaying {{ $stockAdjustments->count() }} of {{ $stockAdjustments->total() }} adjustment records
                @endif
            </div>
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>Items</th>
                        <th>Processed By</th>
                        <th>Net Qty Change</th>
                        <th>Financial Impact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockAdjustments as $adjustment)
                    @php
                        $totalQtyChange = 0;
                        $totalFinancialImpact = 0;
                        foreach($adjustment->items as $item) {
                            $totalQtyChange += $item->quantity_change;
                            $totalFinancialImpact += $item->quantity_change * $item->unit_cost_at_adjustment;
                        }
                    @endphp
                    <tr>
                        <td>{{ $adjustment->id }}</td>
                        <td>{{ $adjustment->adjustment_date->format('M d, Y h:i A') }}</td>
                        <td class="
                            @if($adjustment->adjustment_type == 'Damage/Scrap') text-danger
                            @elseif($adjustment->adjustment_type == 'Found Stock') text-success
                            @elseif($adjustment->adjustment_type == 'Physical Count') text-primary
                            @elseif($adjustment->adjustment_type == 'Internal Use') text-warning
                            @else text-secondary
                            @endif fw-semibold">
                            {{ $adjustment->adjustment_type }}
                        </td>
                        <td>
                            <span title="{{ $adjustment->reason_notes }}">
                                {{ Str::limit($adjustment->reason_notes, 25) }}
                            </span>
                        </td>
                        <td>{{ $adjustment->items->count() }}</td>
                        <td>{{ $adjustment->processedBy ? $adjustment->processedBy->full_name : 'Unknown User' }}</td>
                        <td class="{{ $totalQtyChange < 0 ? 'quantity-negative' : ($totalQtyChange > 0 ? 'quantity-positive' : '') }}">
                            {{ $totalQtyChange > 0 ? '+' : '' }}{{ $totalQtyChange }}
                        </td>
                        <td class="{{ $totalFinancialImpact < 0 ? 'financial-impact-negative' : ($totalFinancialImpact > 0 ? 'financial-impact-positive' : '') }}">
                            ₱{{ number_format($totalFinancialImpact, 2) }}
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-info btn-action view-adjustment" data-id="{{ $adjustment->id }}" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            No stock adjustment records found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $stockAdjustments->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>

    <!-- View Adjustment Modal -->
    <div class="modal fade" id="viewAdjustmentModal" tabindex="-1" aria-labelledby="viewAdjustmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewAdjustmentModalLabel">
                        <i class="bi bi-clipboard-check me-2"></i>
                        Stock Adjustment Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between px-0">
                                    <small class="text-muted">Adjustment ID:</small>
                                    <span class="fw-semibold" id="viewAdjustmentId"></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between px-0">
                                    <small class="text-muted">Date:</small>
                                    <span class="fw-semibold" id="viewAdjustmentDate"></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between px-0">
                                    <small class="text-muted">Type:</small>
                                    <span class="fw-semibold" id="viewAdjustmentType"></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between px-0">
                                    <small class="text-muted">Processed By:</small>
                                    <span class="fw-semibold" id="viewProcessedBy"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between px-0">
                                    <small class="text-muted">Total Items:</small>
                                    <span class="fw-semibold" id="viewTotalItems"></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between px-0">
                                    <small class="text-muted">Net Qty Change:</small>
                                    <span class="fw-semibold" id="viewNetQtyChange"></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between px-0">
                                    <small class="text-muted">Financial Impact:</small>
                                    <span class="fw-semibold" id="viewFinancialImpact"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reason Notes -->
                    <div class="mt-3">
                        <small class="text-muted">Reason Notes:</small>
                        <div class="border rounded p-3 bg-light" style="word-break: break-word; white-space: normal;">
                            <span id="viewReasonNotes"></span>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="mt-4">
                        <h6 class="mb-3">Adjustment Items</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>Quantity Change</th>
                                        <th>Unit Cost</th>
                                        <th>Total Value</th>
                                    </tr>
                                </thead>
                                <tbody id="viewItemsTable">
                                    <!-- Items will be populated here -->
                                </tbody>
                                <tfoot class="table-active">
                                    <tr>
                                        <td colspan="2" class="text-end"><strong>Totals:</strong></td>
                                        <td id="viewTotalQtyChange" class="fw-bold"></td>
                                        <td></td>
                                        <td id="viewTotalValue" class="fw-bold"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // View Adjustment
        document.querySelectorAll('.view-adjustment').forEach(button => {
            button.addEventListener('click', function() {
                const adjustmentId = this.getAttribute('data-id');
                
                fetch(`/stock-adjustments/${adjustmentId}`)
                    .then(response => response.json())
                    .then(adjustment => {
                        // Populate header information
                        document.getElementById('viewAdjustmentId').textContent = adjustment.id;
                        document.getElementById('viewAdjustmentDate').textContent = new Date(adjustment.adjustment_date).toLocaleDateString('en-US', {
                            month: 'short', day: '2-digit',  year: 'numeric'
                        }) + ' ' + new Date(adjustment.adjustment_date).toLocaleTimeString('en-US', {
                            hour: '2-digit', minute: '2-digit', hour12: true
                        });
                        document.getElementById('viewAdjustmentType').textContent = adjustment.adjustment_type;
                        document.getElementById('viewProcessedBy').textContent = adjustment.processed_by.full_name;
                        document.getElementById('viewTotalItems').textContent = adjustment.items.length;
                        document.getElementById('viewReasonNotes').textContent = adjustment.reason_notes;
                        
                        // Calculate totals
                        let totalQtyChange = 0;
                        let totalFinancialImpact = 0;
                        
                        adjustment.items.forEach(item => {
                            totalQtyChange += parseInt(item.quantity_change);
                            totalFinancialImpact += (item.quantity_change * item.unit_cost_at_adjustment);
                        });
                        
                        document.getElementById('viewNetQtyChange').textContent = totalQtyChange > 0 ? `+${totalQtyChange}` : totalQtyChange;
                        document.getElementById('viewNetQtyChange').className = totalQtyChange < 0 ? 'quantity-negative fw-semibold' : (totalQtyChange > 0 ? 'quantity-positive fw-semibold' : 'fw-semibold');
                        
                        document.getElementById('viewFinancialImpact').textContent = '₱' + parseFloat(totalFinancialImpact).toFixed(2);
                        document.getElementById('viewFinancialImpact').className = totalFinancialImpact < 0 ? 'financial-impact-negative fw-semibold' : (totalFinancialImpact > 0 ? 'financial-impact-positive fw-semibold' : 'fw-semibold');
                        
                        // Populate items table
                        const itemsTable = document.getElementById('viewItemsTable');
                        itemsTable.innerHTML = '';
                        
                        let tableTotalQty = 0;
                        let tableTotalValue = 0;
                        
                        adjustment.items.forEach(item => {
                            const row = document.createElement('tr');
                            const itemTotalValue = item.quantity_change * item.unit_cost_at_adjustment;
                            tableTotalQty += item.quantity_change;
                            tableTotalValue += itemTotalValue;
                            
                            row.innerHTML = `
                                <td>${item.product.name}</td>
                                <td>${item.product.sku}</td>
                                <td class="${item.quantity_change < 0 ? 'quantity-negative' : (item.quantity_change > 0 ? 'quantity-positive' : '')}">
                                    ${item.quantity_change > 0 ? '+' : ''}${item.quantity_change}
                                </td>
                                <td>₱${parseFloat(item.unit_cost_at_adjustment).toFixed(2)}</td>
                                <td class="${itemTotalValue < 0 ? 'financial-impact-negative' : (itemTotalValue > 0 ? 'financial-impact-positive' : '')}">
                                    ₱${parseFloat(itemTotalValue).toFixed(2)}
                                </td>
                            `;
                            itemsTable.appendChild(row);
                        });
                        
                        // Update table footer totals
                        document.getElementById('viewTotalQtyChange').textContent = tableTotalQty > 0 ? `+${tableTotalQty}` : tableTotalQty;
                        document.getElementById('viewTotalQtyChange').className = tableTotalQty < 0 ? 'quantity-negative' : (tableTotalQty > 0 ? 'quantity-positive' : '');
                        
                        document.getElementById('viewTotalValue').textContent = '₱' + parseFloat(tableTotalValue).toFixed(2);
                        document.getElementById('viewTotalValue').className = tableTotalValue < 0 ? 'financial-impact-negative' : (tableTotalValue > 0 ? 'financial-impact-positive' : '');
                        
                        const modal = new bootstrap.Modal(document.getElementById('viewAdjustmentModal'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error fetching adjustment:', error);
                        alert('Error loading adjustment details');
                    });
            });
        });
    </script>
    @endpush
@endsection