<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card report-card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Profit & Loss Summary</h5>
                <small>{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</small>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12 mb-3">
                        <h6 class="text-muted">Total Revenue</h6>
                        <h3 class="text-success">₱{{ number_format($financialData['profitLoss']['revenue'], 2) }}</h3>
                    </div>
                    <div class="col-12 mb-3">
                        <h6 class="text-muted">Cost of Goods Sold</h6>
                        <h4 class="text-danger">₱{{ number_format($financialData['profitLoss']['cogs'], 2) }}</h4>
                    </div>
                    <div class="col-12 mb-3">
                        <h6 class="text-muted">Gross Profit</h6>
                        <h3 class="text-primary">₱{{ number_format($financialData['profitLoss']['grossProfit'], 2) }}</h3>
                    </div>
                    <div class="col-12">
                        <h6 class="text-muted">Gross Margin</h6>
                        <h4 class="{{ $financialData['profitLoss']['grossMargin'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($financialData['profitLoss']['grossMargin'], 2) }}%
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8 mb-4">
        <div class="card report-card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">COGS Analysis by Category</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>COGS</th>
                                <th>Revenue</th>
                                <th>Quantity</th>
                                <th>Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($financialData['cogsAnalysis'] as $analysis)
                            @php
                                $margin = $analysis->total_revenue > 0 ? (($analysis->total_revenue - $analysis->total_cogs) / $analysis->total_revenue) * 100 : 0;
                            @endphp
                            <tr>
                                <td>{{ $analysis->category_name }}</td>
                                <td>₱{{ number_format($analysis->total_cogs, 2) }}</td>
                                <td>₱{{ number_format($analysis->total_revenue, 2) }}</td>
                                <td>{{ $analysis->total_quantity }}</td>
                                <td class="{{ $margin >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($margin, 2) }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4">
        <div class="card report-card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Payment Methods Analysis</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Payment Method</th>
                                <th>Transactions</th>
                                <th>Total Amount</th>
                                <th>Average per Transaction</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalAmount = $financialData['paymentMethods']->sum('total_amount');
                            @endphp
                            @foreach($financialData['paymentMethods'] as $payment)
                            <tr>
                                <td>{{ $payment->payment_method }}</td>
                                <td>{{ $payment->transaction_count }}</td>
                                <td>₱{{ number_format($payment->total_amount, 2) }}</td>
                                <td>₱{{ number_format($payment->total_amount / $payment->transaction_count, 2) }}</td>
                                <td>{{ $totalAmount > 0 ? number_format(($payment->total_amount / $totalAmount) * 100, 2) : 0 }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>