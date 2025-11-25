@extends('layouts.app')

@section('title', 'ATIN - Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: #06448a;">Reports</h2>
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
                <button type="submit" class="btn btn-primary">Apply Filter</button>
                <button type="button" class="btn btn-outline-secondary" onclick="exportReport()">Export PDF</button>
            </div>
        </form>
    </div>
</div>

<!-- Report Tabs -->
<ul class="nav nav-tabs mb-4" id="reportsTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">
            <i class="bi bi-graph-up me-2"></i>Sales Reports
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">
            <i class="bi bi-box-seam me-2"></i>Inventory Reports
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab">
            <i class="bi bi-cash-coin me-2"></i>Financial Reports
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="reportsTabContent">
    <!-- Sales Reports Tab -->
    <div class="tab-pane fade show active" id="sales" role="tabpanel">
        @include('reports.partials.sales')
    </div>
    
    <!-- Inventory Reports Tab -->
    <div class="tab-pane fade" id="inventory" role="tabpanel">
        @include('reports.partials.inventory')
    </div>
    
    <!-- Financial Reports Tab -->
    <div class="tab-pane fade" id="financial" role="tabpanel">
        @include('reports.partials.financial')
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
    }
    .nav-tabs .nav-link {
        font-weight: 600;
        color: #495057;
    }
    .nav-tabs .nav-link.active {
        color: #06448a;
        border-bottom: 3px solid #06448a;
    }
    .table th {
        background-color: #f8f9fa;
        color: #06448a;
        font-weight: 600;
    }
    .low-stock {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .out-of-stock {
        background-color: rgba(220, 53, 69, 0.1);
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
        alert('Export functionality would be implemented here');
    }

    // Auto-submit form when date range changes (except custom)
    document.getElementById('dateRange').addEventListener('change', function() {
        if (this.value !== 'custom') {
            document.getElementById('reportFilterForm').submit();
        }
    });
</script>
@endpush