@extends('layouts.app')
@section('title', 'POS - ATIN')
@push('styles')
<style>
    .pos-container {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 20px;
        height: 100vh;
        padding: 20px;
        background: #f8f9fa;
    }
    
    .items-section {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .summary-section {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
    }
    
    .search-input {
        font-size: 18px;
        padding: 15px;
        height: 60px;
    }
    
    .items-list {
        max-height: 400px;
        overflow-y: auto;
        margin: 20px 0;
    }
    
    .item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .quantity-btn {
        width: 30px;
        height: 30px;
        border: 1px solid #ddd;
        background: #f8f9fa;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .remove-btn {
        color: #dc3545;
        cursor: pointer;
        margin-left: 10px;
    }
    
    .payment-section {
        margin-top: auto;
    }
    
    .payment-method {
        margin: 10px 0;
    }
    
    .payment-field {
        margin: 10px 0;
    }
    
    .change-display {
        color: #28a745;
        font-weight: bold;
        margin: 10px 0;
    }

    .qty-input {
        width: 70px;
        text-align: center;
        padding: 2px 4px;
        margin: 0 5px;
    }
    .summary-card {
        background: #fff;
        border-radius: 10px;
        padding: 12px 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 10px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
        margin-bottom: 6px;
    }

    .summary-row.small {
        font-size: 13px;
        color: #6c757d;
    }

    .summary-row input {
        max-width: 150px;
    }

    .total-display {
        font-size: 28px;
        font-weight: bold;
        color: #28a745;
    }

    .total-label {
        font-size: 16px;
        color: #495057;
    }

    .payment-method .form-check {
        flex: 1;
    }

</style>
@endpush

@section('content')
<div class="pos-container" data-cashier-name="{{ session('user_full_name') ?? session('user_name') ?? 'Cashier' }}">
    <!-- Items Section -->
    <div class="items-section">
        <h4 class="mb-3">Checkout</h4>
        
        <!-- Product Search -->
        <div class="mb-5">
            <input type="text" 
                class="form-control search-input" 
                id="productSearch" 
                placeholder="Scan barcode, enter SKU, or type Model..."
                autofocus>
            <div id="searchError" class="text-danger mt-2" style="display: none;"></div>
        </div>

            <!-- Table Header (always visible) -->
        <div class="item-row header-row" style="font-weight:bold; border-bottom:2px solid #ccc; padding:0px; display:flex;">
            <div style="flex:2">Product</div>
            <div style="flex:1; text-align:center">Qty</div>
            <div style="flex:1; text-align:right">Price</div>
            <div style="flex:1; text-align:right">Total</div>
            <div style="flex:0.5"></div>
        </div>
        
        <!-- Items List -->
        <div class="items-list" id="itemsList">
            <div class="text-center text-muted py-4">No items added yet</div>
        </div>
    </div>
    
    <!-- Summary Section -->
    <div class="summary-section">
        <h4 class="mb-3">Order Summary</h4>
    
        <!-- Order & Cashier Info -->
        <div class="summary-card">
            <div class="summary-row">
                <span>Date:</span>
                <span id="currentDate">{{ now()->format('M d, Y') }}</span>
            </div>
            <div class="summary-row">
                <span>Time:</span>
                <span id="currentTime">{{ now()->format('h:i A') }}</span>
            </div>
            <div class="summary-row">
                <span>Cashier:</span>
                <span id="cashierName">{{ session('user_full_name') ?? session('user_name') ?? 'Cashier' }}</span>
            </div>
        </div>
    
        <!-- Customer Info -->
        <div class="summary-card mt-3">
            <div class="summary-row">
                <span>Customer Name:</span>
                <input type="text" id="customerName" class="form-control form-control-sm" placeholder="Optional">
            </div>
            <div class="summary-row mt-2">
                <span>Customer Contact:</span>
                <input type="text" id="customerContact" class="form-control form-control-sm" placeholder="Optional">
            </div>
        </div>
    
        <!-- Subtotal & VAT -->
        <div class="summary-card mt-3">
            <div class="summary-row" style="font-size: 13px; color: #6c757d;">
                <span>Subtotal:</span>
                <span id="subtotalDisplay">₱0.00</span>
            </div>
            <div class="summary-row" style="font-size: 13px; color: #6c757d;">
                <span>VAT (12%):</span>
                <span id="vatDisplay">₱0.00</span>
            </div>
            <hr class="my-2">
            <div class="summary-row" style="font-size: 14px; font-weight: bold;">
                <span class="total-label">Total:</span>
                <span class="total-display" id="totalDisplay">₱0.00</span>
            </div>
        </div>
    
        <!-- Payment -->
        <div class="summary-card mt-3">
            <h5 class="mb-2">Payment Method</h5>
            <div class="payment-method d-flex gap-2 mb-2">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" value="Cash" checked>
                    <label class="form-check-label">Cash</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" value="GCash">
                    <label class="form-check-label">GCash</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" value="Card">
                    <label class="form-check-label">Card</label>
                </div>
            </div>
    
            <div id="cashFields" class="payment-field mb-2">
                <label class="form-label">Amount Tendered</label>
                <div class="input-group">
                    <input type="number" id="amountTendered" class="form-control" step="0.01" min="0" placeholder="0.00">
                    <button type="button" id="exactBtn" class="btn btn-outline-secondary">
                        Exact
                    </button>
                </div>
                <div id="changeDisplay" class="change-display mt-2" style="display:none;"></div>
            </div>
    
            <div id="digitalFields" class="payment-field mb-2" style="display:none;">
                <label>Reference Number</label>
                <input type="text" id="referenceNo" class="form-control form-control-sm">
                <small id="digitalAmountInfo" class="form-text text-muted"></small>
            </div>
        </div>
    
        <!-- Complete Sale -->
        <button class="btn btn-success btn-lg w-100 mt-3" id="completeSale" disabled>
            Complete Sale
        </button>
        <!-- Cancel Sale -->
        <button class="btn btn-danger btn-lg w-100 mt-2" id="cancelSale">
            Cancel Sale
        </button>

    </div>
    
</div>

<!-- Sale Success Modal -->
<div class="modal fade" id="saleSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>
                    Sale Completed
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <p class="mb-2">The sale was completed successfully.</p>
                <p class="fw-bold mb-3">
                    Sale ID: <span id="successSaleId"></span>
                </p>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success btn-lg" onclick="printReceipt(document.getElementById('successSaleId').textContent)">
                        <i class="bi bi-printer me-1"></i> Print Receipt
                    </button>

                    <a href="{{ route('pos.my-transactions') }}"
                    class="btn btn-outline-secondary">
                        View Recent Transactions
                    </a>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Continue Selling
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    class POSSystem {

        constructor() {
            this.items = JSON.parse(localStorage.getItem('posItems')) || [];
            this.total = 0;
            this.init();
            this.renderItems();
            this.updateTotal();
            this.restorePaymentMethod();
        }

        init() {
            this.setupEventListeners();
            this.startClock();
        }

        startClock() {
            setInterval(() => {
                const now = new Date();
                document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }, 1000);
        }

        setupEventListeners() {
            document.getElementById('productSearch').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.searchAndAddProduct();
            });

            document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
                radio.addEventListener('change', (e) => this.handlePaymentMethodChange(e.target.value));
            });

            document.getElementById('amountTendered').addEventListener('input', () => {
                this.calculateChange();
                this.updateCompleteButton();
            });

            document.getElementById('exactBtn').addEventListener('click', () => {
                this.setExactAmount();
            });


            document.getElementById('referenceNo').addEventListener('input', () => {
                this.updateCompleteButton();
            });

            document.getElementById('completeSale').addEventListener('click', () => this.processPayment());

            document.getElementById('cancelSale').addEventListener('click', () => this.cancelSale());
        }

        cancelSale() {
            if (!this.items.length) return; // nothing to cancel

            if (!confirm("Are you sure you want to cancel this sale? All items will be removed.")) return;

            this.resetCart();
        }

        setExactAmount() {
            const amountTenderedInput = document.getElementById('amountTendered');
            amountTenderedInput.value = this.total.toFixed(2);
            this.calculateChange();
            this.updateCompleteButton();
        }

        async searchAndAddProduct() {
            const searchInput = document.getElementById('productSearch');
            const searchTerm = searchInput.value.trim();
            const errorDiv = document.getElementById('searchError');

            if (!searchTerm) return;

            errorDiv.style.display = 'none';
            searchInput.disabled = true;

            try {
                const response = await fetch('/pos/search-product', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ search_term: searchTerm })
                });

                const text = await response.text(); 
                console.log(text);                   
                const data = JSON.parse(text);   
                
                if (!data.success) {
                    errorDiv.textContent = data.message;
                    errorDiv.style.display = 'block';
                    return;
                }

                // Add product to local cart (memory)
                const product = data.products[0];
                if (!product) {
                    errorDiv.textContent = 'Product not found';
                    errorDiv.style.display = 'block';
                    return;
                }

                // Check if product has a price
                if (!product.latest_product_price) {
                    errorDiv.textContent = 'Product has no price set';
                    errorDiv.style.display = 'block';
                    return;
                }

                // Check if product is out of stock
                if (product.quantity_in_stock <= 0) {
                    errorDiv.textContent = 'Product out of stock';
                    errorDiv.style.display = 'block';
                    return;
                }

                // Add product to local cart (memory)
                const existingIndex = this.items.findIndex(item => item.product.id === product.id);
                if (existingIndex !== -1) {
                    // Check if adding more would exceed stock
                    const currentQty = this.items[existingIndex].quantity_sold;
                    if (currentQty + 1 > product.quantity_in_stock) {
                        errorDiv.textContent = `Cannot exceed available stock (${product.quantity_in_stock} remaining)`;
                        errorDiv.style.display = 'block';
                        return;
                    }
                    this.items[existingIndex].quantity_sold++;
                } else {
                    this.items.push({
                        product: product,
                        quantity_sold: 1,
                        unit_price: parseFloat(product.latest_product_price.retail_price || 0)
                    });
                }

                this.renderItems();
                this.updateTotal();

            } catch (err) {
                errorDiv.textContent = err.message;
                errorDiv.style.display = 'block';
            } finally {
                searchInput.value = '';
                searchInput.focus();
                searchInput.disabled = false;
            }
        }

       renderItems() {
            const itemsList = document.getElementById('itemsList');
            let html = '';
            if (this.items.length === 0) {
                html += '<div class="text-center text-muted py-4">No items added yet</div>';
            } else {
                html += this.items.map((item, index) => `
                    <div class="item-row" style="display:flex; align-items:center; padding:5px 0; border-bottom:1px solid #eee;">
                        <div style="flex:2">
                            <strong>${item.product.name}</strong><br>
                            <small class="text-muted">Model: ${item.product.model || 'N/A'}</small><br>
                            <small class="text-muted">Stock: ${item.product.quantity_in_stock}</small>
                        </div>
                        <div style="flex:1; text-align:center">
                            <input type="number" class="qty-input" min="1" step="1" value="${item.quantity_sold}" onchange="pos.setQuantity(${index}, this.value)">
                        </div>
                        <div style="flex:1; text-align:right">₱${item.unit_price.toFixed(2)}</div>
                        <div style="flex:1; text-align:right">₱${(item.unit_price * item.quantity_sold).toFixed(2)}</div>
                        <div style="flex:0.5; text-align:center">
                            <span class="remove-btn" onclick="pos.removeItem(${index})"><i class="bi bi-trash"></i></span>
                        </div>
                    </div>
                `).join('');
            }

            itemsList.innerHTML = html;

            // Persist to localStorage
            localStorage.setItem('posItems', JSON.stringify(this.items));
        }

        updateTotal() {
            this.total = this.items.reduce((sum, item) => sum + item.unit_price * item.quantity_sold, 0);

            const subtotal = this.total / 1.12; 
            const vat = this.total - subtotal;  

            document.getElementById('subtotalDisplay').textContent = `₱${subtotal.toFixed(2)}`;
            document.getElementById('vatDisplay').textContent = `₱${vat.toFixed(2)}`;
            document.getElementById('totalDisplay').textContent = `₱${this.total.toFixed(2)}`;
            document.getElementById('digitalAmountInfo').textContent = `Amount: ₱${this.total.toFixed(2)}`;
            this.calculateChange();
            this.updateCompleteButton();
        }

        restorePaymentMethod() {
            const savedMethod = localStorage.getItem('posPaymentMethod') || 'Cash';
            
            // Find and check the radio button
            const radioButton = document.querySelector(`input[name="paymentMethod"][value="${savedMethod}"]`);
            if (radioButton) {
                radioButton.checked = true;
                // Trigger the change event to update UI
                this.handlePaymentMethodChange(savedMethod);
            } else {
                // Default to Cash if saved method doesn't exist
                document.querySelector('input[name="paymentMethod"][value="Cash"]').checked = true;
                this.handlePaymentMethodChange('Cash');
            }
        }

        setQuantity(index, value) {
            const qty = parseInt(value);
            const maxStock = this.items[index].product.quantity_in_stock;

            if (isNaN(qty) || qty < 1) {
                return alert("Quantity must be at least 1");
            }

            if (qty > maxStock) {
                alert(`Cannot sell more than ${maxStock} in stock`);
                this.items[index].quantity_sold = maxStock;
            } else {
                this.items[index].quantity_sold = qty;
            }

            this.renderItems();
            this.updateTotal();
        }

        updateQuantity(index, newQty) {
            const maxStock = this.items[index].product.quantity_in_stock;
            if (newQty < 1) return this.removeItem(index);

            if (newQty > maxStock) {
                alert(`Cannot sell more than ${maxStock} in stock`);
                this.items[index].quantity_sold = maxStock;
            } else {
                this.items[index].quantity_sold = newQty;
            }

            this.renderItems();
            this.updateTotal();
        }

        removeItem(index) {
            this.items.splice(index, 1);
            this.renderItems();
            this.updateTotal();
        }

        handlePaymentMethodChange(method) {
            document.getElementById('cashFields').style.display = method === 'Cash' ? 'block' : 'none';
            document.getElementById('digitalFields').style.display = method === 'Cash' ? 'none' : 'block';
            document.getElementById('amountTendered').value = '';
            document.getElementById('referenceNo').value = '';
            this.updateCompleteButton();
        }

        calculateChange() {
            const tendered = parseFloat(document.getElementById('amountTendered').value) || 0;
            const change = tendered - this.total;
            const display = document.getElementById('changeDisplay');
            
            if (tendered >= this.total && change > 0) {
                display.textContent = `Change: ₱${change.toFixed(2)}`;
                display.style.display = 'block';
                display.style.color = '#28a745';
            } else if (tendered < this.total && tendered > 0) {
                display.textContent = `Amount Insufficient (Short: ₱${Math.abs(change).toFixed(2)})`;
                display.style.display = 'block';
                display.style.color = '#dc3545'; 
            } else {
                display.style.display = 'none';
            }
        }

        updateCompleteButton() {
            const btn = document.getElementById('completeSale');
                const cancelBtn = document.getElementById('cancelSale');
            const method = document.querySelector('input[name="paymentMethod"]:checked').value;
            const tendered = parseFloat(document.getElementById('amountTendered').value) || 0;
            const refNo = document.getElementById('referenceNo').value;

            let valid = this.items.length > 0;

            if (method === 'Cash') {
                valid = valid && tendered >= this.total;
            } else {
                valid = valid && refNo.trim() !== '';
            }

            btn.disabled = !valid;
                cancelBtn.disabled = this.items.length === 0;
        }

        async processPayment() {
            if (this.items.length === 0) return alert("No items in cart!");

            if (!confirm("Are you sure you want to complete this sale?")) {
                return; // User cancelled
            }

            const method = document.querySelector('input[name="paymentMethod"]:checked').value;
            const tendered = parseFloat(document.getElementById('amountTendered').value) || this.total;
            const refNo = document.getElementById('referenceNo').value;
            const customerName = document.getElementById('customerName').value;
            const customerContact = document.getElementById('customerContact').value;

            try {
                const res = await fetch('/pos/complete-sale', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        items: this.items,
                        payment_method: method,
                        amount_tendered: tendered,
                        reference_no: refNo,
                        customer_name: customerName,
                        customer_contact: customerContact
                    })
                });

                const text = await res.text();
                console.log(text);
                let data;
                try {
                    data = JSON.parse(text);
                } catch(e) {
                    console.error('JSON parse error:', e, text);
                    return;
                }
                if (!data.success) throw new Error(data.message);

                // Show success modal
                document.getElementById('successSaleId').textContent = data.sale.id;

                // Show modal
                const modal = new bootstrap.Modal(
                    document.getElementById('saleSuccessModal')
                );
                modal.show();

                // Reset cart AFTER showing success
                this.resetCart();

            } catch (err) {
                alert("Error: " + err.message);
            }
        }

        resetCart() {
            this.items = [];
            this.total = 0;
            localStorage.removeItem('posItems'); // clear cart
            document.querySelector('input[name="paymentMethod"][value="Cash"]').checked = true;
            this.handlePaymentMethodChange('Cash'); 
            document.getElementById('productSearch').value = '';
            document.getElementById('customerName').value = '';
            document.getElementById('customerContact').value = '';
            document.getElementById('amountTendered').value = '';
            document.getElementById('referenceNo').value = '';
            document.getElementById('changeDisplay').style.display = 'none';
            this.renderItems();
            this.updateTotal();
        }
    }

    let pos;
    document.addEventListener('DOMContentLoaded', () => { pos = new POSSystem(); });

    function printReceipt(id) {
        const url = "{{ route('receipt.print', ['id' => '__ID__']) }}".replace('__ID__', id);

        const win = window.open(
            url,
            '_blank',
            'width=600,height=600,top=100,left=100,scrollbars=yes'
        );

        if (!win) {
            alert('Popup blocked! Please allow popups for this site.');
            return;
        }

        // Grey overlay (blocks clicks, clear UX)
        const overlay = document.createElement('div');
        overlay.id = 'printOverlay';
        overlay.style.cssText = `
            position:fixed;
            inset:0;
            background:rgba(0,0,0,0.35);
            z-index:1050;
            cursor:wait;
        `;
        document.body.appendChild(overlay);

        win.onload = () => {
            win.focus();
            win.print();
        };

        const timer = setInterval(() => {
            if (win.closed) {
                clearInterval(timer);
                overlay.remove();
            }
        }, 300);
    }
</script>
@endpush