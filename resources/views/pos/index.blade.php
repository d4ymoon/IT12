@extends('layouts.app')
@section('title', 'Categories - ATIN Admin')
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
    
    .total-display {
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    margin: 20px 0;
    color: #28a745;
}

.total-label {
    color: #495057;
    margin-right: 10px;
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
    
    /* New styles for order info */
    .order-info {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        border-left: 4px solid #007bff;
    }
    
    .order-info-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 14px;
    }
    
    .order-info-label {
        font-weight: 600;
        color: #495057;
    }
    
    .order-info-value {
        color: #6c757d;
    }

    /* Receipt Preview */
    .receipt-preview {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        background: white;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-top: 15px;
        display: none;
    }

    .receipt-header {
        text-align: center;
        margin-bottom: 10px;
    }

    .receipt-line {
        border-top: 1px dashed #000;
        margin: 5px 0;
    }

    .receipt-items {
        margin: 10px 0;
    }

    .receipt-item-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 3px;
    }

    .receipt-footer {
        text-align: center;
        margin-top: 10px;
    }

    .qty-input {
    width: 70px;
    text-align: center;
    padding: 2px 4px;
    margin: 0 5px;
}
</style>
@endpush

@section('content')
<div class="pos-container" data-cashier-name="{{ session('user_full_name') ?? session('user_name') ?? 'Cashier' }}">
    <!-- Items Section -->
    <div class="items-section">
        <h3>Point of Sale</h3>
        
        <!-- Product Search -->
        <div class="mb-3">
            <input type="text" 
                   class="form-control search-input" 
                   id="productSearch" 
                   placeholder="Scan barcode or enter SKU..."
                   autofocus>
            <div id="searchError" class="text-danger mt-2" style="display: none;"></div>
        </div>
        
        <!-- Items List -->
        <div class="items-list" id="itemsList">
            <div class="text-center text-muted py-4">No items added yet</div>
        </div>
    </div>
    
    <!-- Summary Section -->
    <div class="summary-section">
        <h4>Order Summary</h4>
        
        <!-- Order Information -->
        <div class="order-info">
            <div class="order-info-item">
                <span class="order-info-label">Date:</span>
                <span class="order-info-value" id="currentDate">{{ now()->format('M d, Y') }}</span>
            </div>
            <div class="order-info-item">
                <span class="order-info-label">Time:</span>
                <span class="order-info-value" id="currentTime">{{ now()->format('h:i A') }}</span>
            </div>
            <div class="order-info-item">
                <span class="order-info-label">Cashier:</span>
                <span class="order-info-value" id="cashierName">{{ session('user_full_name') ?? session('user_name') ?? 'Cashier' }}</span>
            </div>
            <div class="order-info-item">
                <span class="order-info-label">Sale #:</span>
                <span class="order-info-value" id="saleNumber">-</span>
            </div>
        </div>
        
        <!-- Customer Details -->
        <div class="mb-3">
            <label class="form-label">Customer Name (Optional)</label>
            <input type="text" class="form-control" id="customerName">
        </div>
        <div class="mb-3">
            <label class="form-label">Customer Contact (Optional)</label>
            <input type="text" class="form-control" id="customerContact">
        </div>
        
        <!-- Total Display -->
        <div class="total-section">
            <div class="total-label">Total:</div>
            <div class="total-display" id="totalDisplay">
                ₱0.00
            </div>
        </div>
        
        <!-- Payment Section -->
        <div class="payment-section">
            <h5>Payment Method</h5>
            
            <div class="payment-method">
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
            
            <!-- Cash Payment Fields -->
            <div id="cashFields" class="payment-field">
                <label class="form-label">Amount Tendered</label>
                <input type="number" class="form-control" id="amountTendered" step="0.01" min="0">
                <div class="change-display" id="changeDisplay" style="display: none;"></div>
            </div>
            
            <!-- Digital Payment Fields -->
            <div id="digitalFields" class="payment-field" style="display: none;">
                <label class="form-label">Reference Number</label>
                <input type="text" class="form-control" id="referenceNo">
                <div class="form-text" id="digitalAmountInfo"></div>
            </div>
            
            <!-- Receipt Preview -->
            <div class="receipt-preview" id="receiptPreview">
                <div class="receipt-header">
                    <strong>ATIN Industrial Hardware Supply Incorporated</strong><br>
                    Door 3 Corner Guerrero St., Ramon Magsaysay Ave.<br>
                    Poblacion District, Davao City, 8000 Davao del Sur<br>
                    (082) 286 6300
                </div>
                <div class="receipt-line"></div>
                <div style="text-align: center; margin: 8px 0;">
                    <strong>S A L E S &nbsp; R E C E I P T</strong>
                </div>
                <div class="receipt-line"></div>
                
                <div id="receiptTransactionInfo"></div>
                
                <div class="receipt-line"></div>
                <div style="display: flex; justify-content: space-between; font-weight: bold; margin-bottom: 5px;">
                    <span>QTY</span>
                    <span style="flex: 1; text-align: center;">DESCRIPTION</span>
                    <span>UNIT PRICE</span>
                    <span>TOTAL</span>
                </div>
                <div class="receipt-line"></div>
                
                <div class="receipt-items" id="receiptItems"></div>
                
                <div class="receipt-line"></div>
                <div id="receiptTotals"></div>
                
                <div class="receipt-line"></div>
                <div style="text-align: center; margin: 8px 0;">
                    <strong>P A Y M E N T &nbsp; I N F O</strong>
                </div>
                <div class="receipt-line"></div>
                
                <div id="receiptPaymentInfo"></div>
                
                <div class="receipt-line"></div>
                <div class="receipt-footer">
                    Thank You For Shopping With Us!<br>
                    Please Come Again.
                </div>
                <div class="receipt-line"></div>
            </div>
            
            <!-- Complete Sale Button -->
            <button class="btn btn-success btn-lg w-100 mt-3" id="completeSale" disabled>
                Complete Sale
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    class POSSystem {
        constructor() {
            this.saleId = null;
            this.items = [];
            this.total = 0;
            this.init();
        }
    
        async init() {
            await this.initializeSale();
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
    
        async initializeSale() {
            try {
                const response = await fetch('/pos/initialize-sale', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
    
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                
                const data = await response.json();
                if (data.success) {
                    this.saleId = data.sale.id;
                    document.getElementById('saleNumber').textContent = this.saleId;
                    console.log('Sale initialized:', this.saleId);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error initializing sale:', error);
                alert('Error starting sale: ' + error.message);
            }
        }
    
        setupEventListeners() {
            document.getElementById('productSearch').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.searchAndAddProduct();
                }
            });
    
            document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
                radio.addEventListener('change', (e) => {
                    this.handlePaymentMethodChange(e.target.value);
                });
            });
    
            document.getElementById('amountTendered').addEventListener('input', () => {
                this.calculateChange();
                this.updateCompleteButton();
                this.updateReceiptPreview();
            });
    
            document.getElementById('referenceNo').addEventListener('input', () => {
                this.updateCompleteButton();
                this.updateReceiptPreview();
            });
    
            document.getElementById('customerName').addEventListener('input', () => {
                this.updateReceiptPreview();
            });
    
            document.getElementById('customerContact').addEventListener('input', () => {
                this.updateReceiptPreview();
            });
    
            document.getElementById('completeSale').addEventListener('click', () => {
                this.processPayment();
            });
        }

        updateReceiptPreview() {
            if (this.items.length === 0) {
                document.getElementById('receiptPreview').style.display = 'none';
                return;
            }

            document.getElementById('receiptPreview').style.display = 'block';
            
            const cashierName = document.querySelector('.pos-container').dataset.cashierName;
            const customerName = document.getElementById('customerName').value;
            const customerContact = document.getElementById('customerContact').value;
            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
            const amountTendered = parseFloat(document.getElementById('amountTendered').value) || 0;
            const referenceNo = document.getElementById('referenceNo').value;
            const change = amountTendered - this.total;

            // Update transaction info
            document.getElementById('receiptTransactionInfo').innerHTML = `
                Transaction #: ${this.saleId}<br>
                Date: ${new Date().toLocaleDateString()}<br>
                Time: ${new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })}<br>
                Cashier: ${cashierName}<br>
                ${customerName ? `Customer: ${customerName}<br>` : ''}
                ${customerContact ? `Contact: ${customerContact}<br>` : ''}
            `;

            // Update items
            const receiptItems = document.getElementById('receiptItems');
            receiptItems.innerHTML = '';
            
            this.items.forEach(item => {
                const lineTotal = item.quantity_sold * parseFloat(item.unit_price);
                const itemRow = document.createElement('div');
                itemRow.className = 'receipt-item-row';
                itemRow.innerHTML = `
                    <span>${item.quantity_sold}</span>
                    <span style="flex: 1; text-align: left; padding: 0 5px;">${item.product.name}</span>
                    <span>₱${parseFloat(item.unit_price).toFixed(2)}</span>
                    <span>₱${lineTotal.toFixed(2)}</span>
                `;
                receiptItems.appendChild(itemRow);
            });

            // Update totals
            document.getElementById('receiptTotals').innerHTML = `
                <div class="receipt-item-row">
                    <span></span>
                    <span style="flex: 1; text-align: right; padding: 0 5px;">Grand Total:</span>
                    <span></span>
                    <span>₱${this.total.toFixed(2)}</span>
                </div>
            `;

            // Update payment info
            let paymentInfo = `
                <div class="receipt-item-row">
                    <span style="flex: 1;">Method:</span>
                    <span>${paymentMethod}</span>
                </div>
                <div class="receipt-item-row">
                    <span style="flex: 1;">Tendered:</span>
                    <span>₱${amountTendered.toFixed(2)}</span>
                </div>
                <div class="receipt-item-row">
                    <span style="flex: 1;">Change:</span>
                    <span>₱${change > 0 ? change.toFixed(2) : '0.00'}</span>
                </div>
            `;

            if (paymentMethod !== 'Cash' && referenceNo) {
                paymentInfo += `
                    <div class="receipt-item-row">
                        <span style="flex: 1;">Reference No:</span>
                        <span>${referenceNo}</span>
                    </div>
                `;
            }

            document.getElementById('receiptPaymentInfo').innerHTML = paymentInfo;
        }
    
        async searchAndAddProduct() {
            const searchInput = document.getElementById('productSearch');
            const searchTerm = searchInput.value.trim();
            const errorDiv = document.getElementById('searchError');
    
            if (!searchTerm) return;
    
            errorDiv.style.display = 'none';
            searchInput.disabled = true;
    
            try {
                const searchResponse = await fetch('/pos/search-product', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ search_term: searchTerm })
                });
    
                if (!searchResponse.ok) throw new Error(`Search failed: ${searchResponse.status}`);
                
                const searchData = await searchResponse.json();
                if (!searchData.success) {
                    errorDiv.textContent = searchData.message;
                    errorDiv.style.display = 'block';
                    return;
                }
    
                const existingItemIndex = this.items.findIndex(item => item.product_id === searchData.product.id);
                
                if (existingItemIndex !== -1) {
                    this.items[existingItemIndex].quantity_sold++;
                    this.renderItems();
                    this.updateTotal();
                    this.syncItemQuantity(this.items[existingItemIndex].id, this.items[existingItemIndex].quantity_sold);
                } else {
                    const addResponse = await fetch('/pos/add-item', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            sale_id: this.saleId,
                            product_id: searchData.product.id,
                            quantity: 1
                        })
                    });
    
                    if (!addResponse.ok) throw new Error(`Add item failed: ${addResponse.status}`);
                    
                    const addData = await addResponse.json();
                    if (addData.success) {
                        this.items.push(addData.item);
                        this.renderItems();
                        this.updateTotal();
                    } else {
                        throw new Error(addData.message);
                    }
                }
    
                searchInput.value = '';
                searchInput.focus();
    
            } catch (error) {
                console.error('Error in searchAndAddProduct:', error);
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
            } finally {
                searchInput.disabled = false;
            }
        }
    
        async updateQuantity(itemId, newQuantity) {
            const itemIndex = this.items.findIndex(item => item.id === itemId);
            if (itemIndex === -1) return;
    
            if (newQuantity <= 0) {
                this.items.splice(itemIndex, 1);
                this.renderItems();
                this.updateTotal();
                this.removeItemFromServer(itemId);
            } else {
                this.items[itemIndex].quantity_sold = newQuantity;
                this.renderItems();
                this.updateTotal();
                this.syncItemQuantity(itemId, newQuantity);
            }
        }

        async setQuantity(itemId, newQuantity) {
            newQuantity = parseInt(newQuantity);

            if (isNaN(newQuantity) || newQuantity < 1) {
                alert("Quantity must be at least 1");
                return;
            }

            const itemIndex = this.items.findIndex(item => item.id === itemId);
            if (itemIndex === -1) return;

            this.items[itemIndex].quantity_sold = newQuantity;
            this.renderItems();
            this.updateTotal();

            // Sync with backend
            try {
                await fetch(`/pos/update-item/${itemId}`, {
                    method: "PUT",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ quantity: newQuantity })
                });
            } catch (error) {
                console.error('Error updating quantity:', error);
            }
        }
    
        async syncItemQuantity(itemId, quantity) {
            try {
                await fetch(`/pos/update-item/${itemId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ quantity: quantity })
                });
            } catch (error) {
                console.error('Background sync failed:', error);
            }
        }
    
        async removeItemFromServer(itemId) {
            try {
                await fetch(`/pos/remove-item/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            } catch (error) {
                console.error('Background remove failed:', error);
            }
        }
    
        async removeItem(itemId) {
            if (!confirm('Remove this item from sale?')) return;
    
            this.items = this.items.filter(item => item.id !== itemId);
            this.renderItems();
            this.updateTotal();
            this.removeItemFromServer(itemId);
        }
    
        renderItems() {
            const itemsList = document.getElementById('itemsList');
            
            if (this.items.length === 0) {
                itemsList.innerHTML = '<div class="text-center text-muted py-4">No items added yet</div>';
                document.getElementById('receiptPreview').style.display = 'none';
                return;
            }
    
            itemsList.innerHTML = this.items.map(item => `
                <div class="item-row">
                    <div class="item-info">
                        <strong>${item.product.name}</strong><br>
                        <small>₱${parseFloat(item.unit_price).toFixed(2)} × ${item.quantity_sold} = ₱${(item.quantity_sold * item.unit_price).toFixed(2)}</small>
                    </div>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="pos.updateQuantity(${item.id}, ${item.quantity_sold - 1})">-</button>
                         <input type="number" 
       class="qty-input" 
       min="1" 
       max="99999" 
       step="1"
       value="${item.quantity_sold}" 
       onchange="pos.setQuantity(${item.id}, this.value)">
                        <button class="quantity-btn" onclick="pos.updateQuantity(${item.id}, ${item.quantity_sold + 1})">+</button>
                        <span class="remove-btn" onclick="pos.removeItem(${item.id})">
                            <i class="bi bi-trash"></i>
                        </span>
                    </div>
                </div>
            `).join('');

            this.updateReceiptPreview();
        }
    
        updateTotal() {
            this.total = this.items.reduce((sum, item) => {
                return sum + (item.quantity_sold * parseFloat(item.unit_price));
            }, 0);
            
            document.getElementById('totalDisplay').textContent = `₱${this.total.toFixed(2)}`;
            document.getElementById('digitalAmountInfo').textContent = `Amount: ₱${this.total.toFixed(2)}`;
            this.calculateChange();
            this.updateCompleteButton();
            this.updateReceiptPreview();
        }
    
        handlePaymentMethodChange(method) {
            const cashFields = document.getElementById('cashFields');
            const digitalFields = document.getElementById('digitalFields');
            const amountTendered = document.getElementById('amountTendered');
            const referenceNo = document.getElementById('referenceNo');
    
            if (method === 'Cash') {
                cashFields.style.display = 'block';
                digitalFields.style.display = 'none';
                amountTendered.value = '';
                referenceNo.value = '';
            } else {
                cashFields.style.display = 'none';
                digitalFields.style.display = 'block';
                amountTendered.value = this.total.toFixed(2);
            }
    
            this.updateCompleteButton();
            this.updateReceiptPreview();
        }
    
        calculateChange() {
            const amountTendered = parseFloat(document.getElementById('amountTendered').value) || 0;
            const change = amountTendered - this.total;
            const changeDisplay = document.getElementById('changeDisplay');
    
            if (change > 0) {
                changeDisplay.textContent = `Change: ₱${change.toFixed(2)}`;
                changeDisplay.style.display = 'block';
            } else {
                changeDisplay.style.display = 'none';
            }
        }
    
        updateCompleteButton() {
            const completeBtn = document.getElementById('completeSale');
            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
            const amountTendered = parseFloat(document.getElementById('amountTendered').value) || 0;
            const referenceNo = document.getElementById('referenceNo').value;
    
            let isValid = this.total > 0;
    
            if (paymentMethod === 'Cash') {
                isValid = isValid && amountTendered >= this.total;
            } else {
                isValid = isValid && referenceNo.trim() !== '';
            }
    
            completeBtn.disabled = !isValid;
        }
    
        async processPayment() {
            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
            const amountTendered = parseFloat(document.getElementById('amountTendered').value) || this.total;
            const referenceNo = document.getElementById('referenceNo').value;
            const customerName = document.getElementById('customerName').value;
            const customerContact = document.getElementById('customerContact').value;
    
            try {
                const response = await fetch('/pos/process-payment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        sale_id: this.saleId,
                        payment_method: paymentMethod,
                        amount_tendered: amountTendered,
                        reference_no: referenceNo,
                        customer_name: customerName,
                        customer_contact: customerContact
                    })
                });
    
                const data = await response.json();
    
                if (data.success) {
                    // Download PDF receipt
                    this.downloadReceiptPDF(data.sale.id);
                    
                    this.showReceipt(data.sale, data.change_given);
                    await this.initializeSale();
                    this.resetUI();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error processing payment:', error);
                alert('Error processing payment: ' + error.message);
            }
        }

        downloadReceiptPDF(saleId) {
            // Create a hidden iframe to download the PDF
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = `/pos/receipt/${saleId}/pdf`;
            document.body.appendChild(iframe);
            
            // Remove the iframe after download
            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 1000);
        }
    
        showReceipt(sale, changeGiven) {
            const cashierName = document.querySelector('.pos-container').dataset.cashierName;
            const paymentMethod = sale.payment && sale.payment[0] ? sale.payment[0].payment_method : 'Unknown';
            const amountTendered = sale.payment && sale.payment[0] ? sale.payment[0].amount_tendered : this.total;
            
            const receipt = `
Receipt - Sale #${sale.id}
Date: ${new Date(sale.sale_date).toLocaleString()}
Cashier: ${cashierName}

Items:
${sale.items.map(item => 
    `${item.product.name} - ${item.quantity_sold} × ₱${parseFloat(item.unit_price).toFixed(2)} = ₱${(item.quantity_sold * parseFloat(item.unit_price)).toFixed(2)}`
).join('\n')}

Total: ₱${this.total.toFixed(2)}
Payment Method: ${paymentMethod}
Amount Tendered: ₱${amountTendered.toFixed(2)}
${changeGiven > 0 ? `Change Given: ₱${changeGiven.toFixed(2)}` : ''}

Thank you for your purchase!
            `;

            alert('Sale completed successfully! PDF receipt downloaded.');
        }
                
        resetUI() {
            document.getElementById('productSearch').value = '';
            document.getElementById('customerName').value = '';
            document.getElementById('customerContact').value = '';
            document.getElementById('amountTendered').value = '';
            document.getElementById('referenceNo').value = '';
            document.getElementById('changeDisplay').style.display = 'none';
            document.getElementById('receiptPreview').style.display = 'none';
            this.items = [];
            this.total = 0;
            this.renderItems();
            this.updateTotal();
        }
    }
    
    // Initialize POS system when page loads
    let pos;
    document.addEventListener('DOMContentLoaded', () => {
        pos = new POSSystem();
    });
</script>
@endpush