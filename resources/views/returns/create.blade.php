@extends('layouts.app')
@section('title', 'Process New Return - ATIN Admin')

@push('styles')
<link href="{{ asset('css/page-style.css') }}" rel="stylesheet">
<style>
    .return-panel {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
        background-color: #f8f9fa;
    }
    .item-row {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: white;
    }
    .remove-item {
        color: #dc3545;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
    @include('components.alerts')

    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">
                <a href="{{ route('returns.index') }}" class="text-decoration-none text-dark">
                    <b class="underline">Product Returns</b>
                </a>
                > Process New Return
            </h2>
            <a href="{{ route('returns.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Returns
            </a>
        </div>
    </div>

    <!-- Step 1: Locate Sale -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Locate Original Sale</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sale_id" class="form-label">Enter Sale ID</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="sale_id" 
                                   placeholder="Enter the original Sale ID">
                            <button type="button" class="btn btn-primary" id="lookup-sale">
                                <i class="bi bi-search me-1"></i> Lookup Sale
                            </button>
                        </div>
                        <div class="form-text">Enter the Sale ID from the original receipt.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sale Details (Hidden until sale is found) -->
    <div id="sale-details-section" class="d-none">
        <!-- Sale Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Original Sale Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-2">
                            <small class="text-muted d-block">Sale ID</small>
                            <strong id="sale-id-display">-</strong>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-2">
                            <small class="text-muted d-block">Sale Date</small>
                            <strong id="sale-date-display">-</strong>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-2">
                            <small class="text-muted d-block">Customer</small>
                            <strong id="customer-name-display">-</strong>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-2">
                            <small class="text-muted d-block">Contact</small>
                            <strong id="customer-contact-display">-</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Return Form -->
        <form id="return-form" method="POST" action="{{ route('returns.store') }}">
            @csrf
            <input type="hidden" name="sale_id" id="form-sale-id">
            
            <!-- Step 2: Select Items & Condition -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Select Items & Condition</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Original Price</th>
                                    <th>Quantity Sold</th>
                                    <th>Already Returned</th>
                                    <th>Return Quantity</th>
                                    <th>Condition</th>
                                    <th>Refund Amount</th>
                                </tr>
                            </thead>
                            <tbody id="return-items-tbody">
                                <!-- Items will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Step 3: Finalize Financials -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Step 3: Finalize Financials</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="return_reason" class="form-label">Return Reason <span class="text-danger">*</span></label>
                                <select class="form-select" id="return_reason" name="return_reason" required>
                                    <option value="">Select Reason</option>
                                    <option value="Defective">Defective Product</option>
                                    <option value="Wrong Item">Wrong Item Received</option>
                                    <option value="Customer Change Mind">Customer Changed Mind</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Return Date</label>
                                <input type="text" class="form-control" 
                                       value="{{ now()->format('M d, Y h:i A') }}" 
                                       readonly 
                                       style="background-color: #f8f9fa;">
                                <input type="hidden" id="return_date" name="return_date" 
                                       value="{{ now()->format('Y-m-d H:i:s') }}">
                                <div class="form-text">Automatically recorded</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="processed_by" class="form-label">Processed By</label>
                                <input type="text" class="form-control" value="{{ session('user_name') ?? 'Current User' }}" readonly>
                                <input type="hidden" id="processed_by_user_id" name="processed_by_user_id" value="{{ session('user_id') ?? '' }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="refund_method" class="form-label">Refund Method <span class="text-danger">*</span></label>
                                <select class="form-select" id="refund_method" name="refund_method" required>
                                    <option value="">Select Method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="GCash">GCash</option>
                                    <option value="Card">Card</option>
                                </select>
                            </div>
                        </div>
        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="refund_method" class="form-label">Refund Method <span class="text-danger">*</span></label>
                                <select class="form-select" id="refund_method" name="refund_method" required>
                                    <option value="">Select Method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="GCash">GCash</option>
                                    <option value="Card">Card</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Reference Number Field (shown only for GCash and Card) -->
                    <div class="row" id="reference_no_field" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reference_no" class="form-label">Reference Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="reference_no" name="reference_no" 
                                    placeholder="Enter transaction reference number">
                                <div class="form-text">Required for GCash and Card refunds</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Additional notes about this return..." maxlength="250"></textarea>
                        <div class="form-text text-start">Maximum 250 characters</div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="alert alert-info mb-0">
                                <h6 class="alert-heading">Refund Summary</h6>
                                <p class="mb-1"><strong>Total Refund Amount:</strong> $<span id="total-refund-amount">0.00</span></p>
                                <small class="d-block mt-1">Items marked as "Damaged" will not be restocked.</small>
                            </div>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('returns.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success" id="process-return-btn">
                            Process Return & Issue Refund
                        </button>
                    </div>
                </div>
            </div>

            
        </form>
    </div>

    <!-- Error Alert -->
    <div id="error-alert" class="alert alert-danger d-none" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> <span id="error-message"></span>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentSale = null;

    $('#refund_method').change(function() {
        const method = $(this).val();
        const referenceField = $('#reference_no_field');
        
        if (method === 'GCash' || method === 'Card') {
            referenceField.show();
            $('#reference_no').prop('required', true);
        } else {
            referenceField.hide();
            $('#reference_no').prop('required', false);
            $('#reference_no').val('');
        }
    });

    $('#refund_method').trigger('change');

    // Lookup sale
    $('#lookup-sale').click(function() {
        const saleId = $('#sale_id').val().trim();
        
        if (!saleId) {
            showError('Please enter a Sale ID');
            return;
        }

        $('#lookup-sale').prop('disabled', true).html('<i class="ri-loader-4-line spin"></i> Searching...');

        // FIX: Build the URL manually to avoid route parameter issues
        const url = `/returns/get-sale/${saleId}`;
        
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $('#lookup-sale').prop('disabled', false).html('<i class="ri-search-line"></i> Lookup Sale');
                
                if (response.success) {
                    currentSale = response.sale;
                    displaySaleDetails();
                    $('#error-alert').addClass('d-none');
                } else {
                    showError(response.message);
                }
            },
            error: function(xhr, status, error) {
                $('#lookup-sale').prop('disabled', false).html('<i class="ri-search-line"></i> Lookup Sale');
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    showError(xhr.responseJSON.message);
                } else {
                    showError('Error looking up sale. Please try again.');
                }
            }
        });
    });

    function displaySaleDetails() {
        // Update sale info display
        $('#sale-id-display').text(currentSale.id);
        $('#sale-date-display').text(new Date(currentSale.sale_date).toLocaleDateString());
        $('#customer-name-display').text(currentSale.customer_name || 'N/A');
        $('#customer-contact-display').text(currentSale.customer_contact || 'N/A');
        
        // Set hidden form field
        $('#form-sale-id').val(currentSale.id);

        // Populate return items table
        const tbody = $('#return-items-tbody');
        tbody.empty();

        currentSale.items.forEach(item => {
            if (item.max_returnable > 0) {
                const row = `
                    <tr>
                        <td>
                            ${item.product_name}
                            <input type="hidden" name="items[${item.id}][sale_item_id]" value="${item.id}">
                        </td>
                        <td>${item.product_sku}</td>
                        <td>$${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td>${item.quantity_sold}</td>
                        <td>${item.already_returned}</td>
                        <td>
                            <input type="number" 
                                   name="items[${item.id}][quantity]" 
                                   class="form-control return-quantity" 
                                   min="1" 
                                   max="${item.max_returnable}"
                                   value="0"
                                   data-unit-price="${item.unit_price}"
                                   data-max="${item.max_returnable}">
                        </td>
                        <td>
                            <select name="items[${item.id}][condition]" class="form-select item-condition">
                                <option value="resaleable">Resaleable</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </td>
                        <td>
                            $<span class="line-refund-amount">0.00</span>
                            <input type="hidden" name="items[${item.id}][refund_amount]" class="line-refund-input" value="0">
                        </td>
                    </tr>
                `;
                tbody.append(row);
            }
        });

        // Show the form section
        $('#sale-details-section').removeClass('d-none');

        // Scroll to form
        $('html, body').animate({
            scrollTop: $('#sale-details-section').offset().top - 20
        }, 500);
    }

    // Calculate refund amounts when quantity or condition changes
    $(document).on('change', '.return-quantity, .item-condition', function() {
        calculateRefundAmounts();
    });

    function calculateRefundAmounts() {
        let totalRefund = 0;

        $('.return-quantity').each(function() {
            const quantity = parseInt($(this).val()) || 0;
            const unitPrice = parseFloat($(this).data('unit-price'));
            const maxQuantity = parseInt($(this).data('max'));
            const condition = $(this).closest('tr').find('.item-condition').val();
            
            // Validate quantity
            if (quantity < 0) {
                $(this).val(0);
                return;
            }
            if (quantity > maxQuantity) {
                $(this).val(maxQuantity);
                return;
            }

            // Calculate refund amount (full refund for resaleable, potentially adjust for damaged)
            let refundAmount = quantity * unitPrice;
            
            // For damaged items, you might want to implement partial refund logic
            // For now, we'll do full refund for both conditions
            // if (condition === 'damaged') {
            //     refundAmount = refundAmount * 0.5; // 50% refund for damaged
            // }

            const lineRefundElement = $(this).closest('tr').find('.line-refund-amount');
            const lineRefundInput = $(this).closest('tr').find('.line-refund-input');
            
            lineRefundElement.text(refundAmount.toFixed(2));
            lineRefundInput.val(refundAmount.toFixed(2));
            
            totalRefund += refundAmount;
        });

        $('#total-refund-amount').text(totalRefund.toFixed(2));
    }

    function showError(message) {
        $('#error-message').text(message);
        $('#error-alert').removeClass('d-none');
        
        $('html, body').animate({
            scrollTop: $('#error-alert').offset().top - 20
        }, 500);
    }

    // Form validation before submission
    $('#return-form').on('submit', function(e) {
        let hasItems = false;
        let totalQuantity = 0;

        $('.return-quantity').each(function() {
            const quantity = parseInt($(this).val()) || 0;
            if (quantity > 0) {
                hasItems = true;
                totalQuantity += quantity;
            }
        });

        if (!hasItems || totalQuantity === 0) {
            e.preventDefault();
            showError('Please select at least one item to return with quantity greater than 0.');
            return false;
        }

        // Confirm before processing
        const totalRefund = $('#total-refund-amount').text();
        if (!confirm(`Are you sure you want to process this return and issue a refund of $${totalRefund}?`)) {
            e.preventDefault();
            return false;
        }
    });
});
</script>

<style>
.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
</style>
@endpush