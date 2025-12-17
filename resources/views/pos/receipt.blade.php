    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Receipt - Sale #{{ $sale->id }}</title>
        <style>
            @page {
                size: 80mm 297mm;
                margin: 0;
            }
            body {
                /* Total paper is 80mm, printable area is usually 72mm */
                width: 72mm; 
                margin: 0 auto; /* Centers the content on the paper */
                padding: 2mm 0;
                font-family: 'DejaVu Sans', monospace;
                font-size: 10px;
                line-height: 1.2;
            }

            .receipt-header, .footer {
                text-align: center;
            }
            .receipt-header strong {
                font-size: 12px;
            }
            .line {
                border-top: 1px dashed #000;
                margin: 4px 0;
            }
            .items-table {
                width: 100%;
                table-layout: fixed;
            }
            .items-table th, .items-table td {
                word-wrap: break-word;
                overflow-wrap: break-word;
                padding: 2px 0;
                vertical-align: top;
            }
            .items-table th {
                text-align: left;
                font-weight: bold;
            }
            
            /* Fixed column widths */
            .col-qty { width: 10%; }
            .col-item { width: 60%; }
            .col-total { width: 30%; }
                    
            .text-right { text-align: right; }
            .text-center { text-align: center; }

            .amount-table {
                width: 100%;
            }
            .amount-table td {
                padding: 1px 0;
            }
            .total-section {
    width: 100%;
    margin-top: 5px;
    border-top: 1px solid #000;
    padding-top: 2px;
    font-weight: bold;
    border-collapse: collapse; /* Prevents tiny gaps in the border */
}

.total-section td {
    padding: 0;
    vertical-align: bottom;
}
            .payment-info {
                margin-top: 5px;
                border-top: 1px dashed #000;
                padding-top: 2px;
            }
            
            /* Item name wrapping */
            .item-name {
                word-wrap: break-word;
                word-break: break-word;
                overflow-wrap: break-word;
                white-space: normal;
                display: block;
                max-width: 45mm;
            }
        </style>
    </head>
    <body>
        <div class="receipt-header">
            <strong>ATIN Industrial Hardware Supply Inc.</strong><br>
            Door 3 Corner Guerrero St., Ramon Magsaysay Ave.<br>
            Poblacion District, Davao City, 8000 Davao del Sur<br>
            (082) 286 6300
        </div>
        
        <div class="line"></div>
        <div class="text-center"><strong>SALES RECEIPT</strong></div>
        <div class="line"></div>

        <div>
            <strong>Transaction #:</strong> {{ $sale->id }}<br>
            <strong>Date:</strong> {{ $sale->sale_date ? $sale->sale_date->format('M d, Y') : now()->format('M d, Y') }}<br>
            <strong>Time:</strong> {{ $sale->sale_date ? $sale->sale_date->format('h:i A') : now()->format('h:i A') }}<br>
            <strong>Cashier:</strong> {{ $sale->user->full_name ?? 'N/A' }}<br>
            @if($sale->customer_name)
            <strong>Customer:</strong> {{ $sale->customer_name }}<br>
            @endif
            @if($sale->customer_contact)
            <strong>Contact:</strong> {{ $sale->customer_contact }}<br>
            @endif
        </div>

        <div class="line"></div>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-qty">QTY</th>
                    <th class="col-item">ITEM</th>
                    <th class="col-total">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td class="col-qty">{{ $item->quantity_sold }}</td>
                    <td class="col-item">
                        <span class="item-name">{{ $item->product->name ?? 'N/A' }}</span>
                        @if(isset($item->product->model) && $item->product->model)
                            <br><small>Model: {{ $item->product->model }}</small>
                        @endif
                    </td>
                    <td class="col-total text-right">₱{{ number_format($item->quantity_sold * $item->unit_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="line"></div>

        @php
            $total = $sale->items->sum(fn($item) => $item->quantity_sold * $item->unit_price);
            $vatRate = 0.12;
            $vatableSales = $total / (1 + $vatRate);
            $vatAmount = $total - $vatableSales;
        @endphp

        <table class="amount-table">
            <tr>
                <td>AMOUNT DUE:</td>
                <td class="text-right">₱{{ number_format($total, 2) }}</td>
            </tr>
            <tr>
                <td>VAT SALES:</td>
                <td class="text-right">₱{{ number_format($vatableSales, 2) }}</td>
            </tr>
            <tr>
                <td>VAT 12%:</td>
                <td class="text-right">₱{{ number_format($vatAmount, 2) }}</td>
            </tr>
        </table>

        <div class="total-section text-right">
            GRAND TOTAL: ₱{{ number_format($total, 2) }}
        </div>

        <div class="line"></div>
        <div class="text-start"><strong>PAYMENT INFO</strong></div>
        <div class="line"></div>

        <div class="payment-info">
            @if($sale->payment)
                @php $payment = $sale->payment; @endphp
                <strong>Method:</strong> {{ $payment->payment_method ?? 'N/A' }}<br>
                <strong>Amount Tendered:</strong> ₱{{ number_format($payment->amount_tendered ?? 0, 2) }}<br>
                <strong>Change Given:</strong> ₱{{ number_format($payment->change_given ?? 0, 2) }}<br>
                @if(!empty($payment->reference_no))
                    <strong>Reference #:</strong> {{ $payment->reference_no }}<br>
                @endif
            @else
                <em>No payment data recorded.</em>
            @endif
        </div>

        <div class="line"></div>
        <div class="footer">
            Thank You For Shopping With Us!<br>
            Please Come Again.
        </div>
        <div class="footer" style="font-style: italic; margin-top: 2px;">
            Keep this invoice for return/refund.<br>
            Return/refund within 7 days from purchase date.
        </div>
    </body>
    </html>