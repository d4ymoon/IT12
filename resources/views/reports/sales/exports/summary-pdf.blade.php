<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Summary Report - {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .kpi-section { margin-bottom: 20px; }
        .kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 15px; }
        .kpi-card { border: 1px solid #ddd; padding: 10px; border-radius: 5px; }
        .kpi-label { font-size: 10px; color: #666; margin-bottom: 5px; }
        .kpi-value { font-size: 14px; font-weight: bold; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table th { background-color: #f8f9fa; border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table td { border: 1px solid #ddd; padding: 8px; }
        .section-title { background-color: #e9ecef; padding: 8px; margin: 15px 0 10px 0; font-weight: bold; }
        .negative { color: #dc3545; }
        .positive { color: #28a745; }
        .footer { margin-top: 30px; text-align: center; color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ATIN - Sales Summary Report</h1>
        <p>Period: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
        <p>Generated on: {{ $exportDate }}</p>
    </div>

    <!-- Summary Statistics -->
    <div class="section-title">Summary Statistics</div>
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">Total Transactions</div>
            <div class="kpi-value">{{ $salesData['summaryStats']->total_transactions ?? 0 }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Items Sold</div>
            <div class="kpi-value">{{ $salesData['summaryStats']->total_items_sold ?? 0 }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Gross Revenue</div>
            <div class="kpi-value">₱{{ number_format($salesData['summaryStats']->gross_revenue ?? 0, 2) }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Total Refunds</div>
            <div class="kpi-value negative">-₱{{ number_format($salesData['summaryStats']->total_returns ?? 0, 2) }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Net Revenue</div>
            <div class="kpi-value positive">₱{{ number_format($salesData['summaryStats']->net_revenue ?? 0, 2) }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Avg. Transaction</div>
            <div class="kpi-value">₱{{ number_format($salesData['summaryStats']->avg_transaction_value ?? 0, 2) }}</div>
        </div>
    </div>

    <!-- Sales by Date -->
    <div class="section-title">Sales by Date Range</div>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Transactions</th>
                <th>Total Revenue</th>
                <th>Average per Transaction</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesData['salesByDate'] as $sale)
            <tr>
                <td>{{ \Carbon\Carbon::parse($sale->date)->format('M d, Y') }}</td>
                <td>{{ $sale->transaction_count }}</td>
                <td>₱{{ number_format($sale->total_revenue, 2) }}</td>
                <td>₱{{ number_format($sale->total_revenue / $sale->transaction_count, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Top Products by Quantity -->
    <div class="section-title">Top 10 Products by Items Sold</div>
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity Sold</th>
                <th>Revenue</th>
                <th>Avg Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesData['topProductsByQuantity'] as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->total_quantity }}</td>
                <td>₱{{ number_format($product->total_revenue, 2) }}</td>
                <td>₱{{ number_format($product->avg_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Top Products by Revenue -->
    <div class="section-title">Top 10 Products by Revenue</div>
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Revenue</th>
                <th>Avg Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesData['topProductsByRevenue'] as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->total_quantity }}</td>
                <td>₱{{ number_format($product->total_revenue, 2) }}</td>
                <td>₱{{ number_format($product->avg_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Sales by Category -->
    <div class="section-title">Sales by Category</div>
    <table class="table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Revenue</th>
                <th>Quantity</th>
                <th>Transactions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesData['categoryAnalysis'] as $category)
            <tr>
                <td>{{ $category->category_name }}</td>
                <td>₱{{ number_format($category->total_revenue, 2) }}</td>
                <td>{{ $category->total_quantity }}</td>
                <td>{{ $category->transaction_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        ATIN Sales Report | Confidential Business Document
    </div>
</body>
</html>