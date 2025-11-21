<div class="row">
    <div class="col-12 mb-4">
        <div class="card report-card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">Low Stock Alerts</h5>
                <small>{{ $inventoryData['lowStockAlerts']->count() }} products need attention</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Current Stock</th>
                                <th>Reorder Level</th>
                                <th>Unit Cost</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventoryData['lowStockAlerts'] as $product)
                            <tr class="{{ $product->quantity_in_stock == 0 ? 'out-of-stock' : 'low-stock' }}">
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category_name }}</td>
                                <td>{{ $product->quantity_in_stock }}</td>
                                <td>{{ $product->reorder_level }}</td>
                                <td>₱{{ number_format($product->latest_unit_cost, 2) }}</td>
                                <td>
                                    @if($product->quantity_in_stock == 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @else
                                        <span class="badge bg-warning">Low Stock</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card report-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Recent Stock Movement</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Date</th>
                                <th>Qty Received</th>
                                <th>Unit Cost</th>
                                <th>Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventoryData['stockMovement'] as $movement)
                            <tr>
                                <td>{{ $movement->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($movement->stock_in_date)->format('M d, Y') }}</td>
                                <td>{{ $movement->quantity_received }}</td>
                                <td>₱{{ number_format($movement->actual_unit_cost, 2) }}</td>
                                <td>₱{{ number_format($movement->total_cost, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card report-card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Inventory Valuation by Category</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Products</th>
                                <th>Total Quantity</th>
                                <th>Total Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventoryData['valuationReport'] as $valuation)
                            <tr>
                                <td>{{ $valuation->category_name }}</td>
                                <td>{{ $valuation->product_count }}</td>
                                <td>{{ $valuation->total_quantity }}</td>
                                <td>₱{{ number_format($valuation->total_value, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>