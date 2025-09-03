<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $sale->invoice_no }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;700&family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            padding: 20px;
            font-size: 14px;
        }
        
        .invoice-container {
            width: 100%;
            max-width: 400px;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .invoice-header {
            background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
            color: white;
            padding: 25px 25px 20px;
            text-align: center;
            position: relative;
        }
        
        .store-name {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 1px;
            font-family: 'Roboto Mono', monospace;
        }
        
        .store-details {
            font-size: 11px;
            opacity: 0.9;
            margin-top: 8px;
            line-height: 1.4;
        }
        
        .invoice-title {
            font-size: 18px;
            margin: 15px 0 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }
        
        .invoice-info {
            padding: 20px;
            background: #f9f9f9;
            border-bottom: 1px dashed #ddd;
            display: flex;
            flex-wrap: wrap;
        }
        
        .info-left {
            width: 58%;
            padding-right: 15px;
        }
        
        .info-right {
            width: 40%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .qr-code {
            width: 90px;
            height: 90px;
            background: #fff;
            padding: 5px;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Roboto Mono', monospace;
            font-size: 6px;
            text-align: center;
            color: #333;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            width: 100%;
        }
        
        .info-label {
            font-weight: 500;
            color: #555;
            font-size: 12px;
        }
        
        .info-value {
            font-weight: 600;
            font-size: 12px;
            text-align: right;
        }
        
        .invoice-body {
            padding: 20px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        
        .items-table th {
            background-color: #f1f5f9;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        
        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .summary {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 13px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #e2e8f0;
        }
        
        .summary-row:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 16px;
            color: #182848;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 2px solid #e2e8f0;
        }
        
        .footer {
            padding: 20px;
            background: #f1f5f9;
            color: #64748b;
            font-size: 11px;
            line-height: 1.5;
        }
        
        .terms-section {
            margin-bottom: 15px;
        }
        
        .terms-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #4b6cb7;
            font-size: 12px;
        }
        
        .terms-list {
            padding-left: 15px;
        }
        
        .terms-list li {
            margin-bottom: 3px;
        }
        
        .barcode {
            margin-top: 15px;
            text-align: center;
            padding: 8px;
            background: #f9f9f9;
            border-radius: 6px;
            font-size: 12px;
        }
        
        .thank-you {
            margin-top: 20px;
            font-weight: 700;
            color: #4b6cb7;
            text-align: center;
            font-size: 15px;
            padding: 8px;
            border-top: 1px dashed #4b6cb7;
            border-bottom: 1px dashed #4b6cb7;
        }
        
        .hotline {
            margin-top: 10px;
            font-weight: 600;
            color: #e11d48;
            text-align: center;
            font-size: 13px;
        }
        
        .decoration-top {
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            overflow: hidden;
        }
        
        .decoration-top::before {
            content: "";
            position: absolute;
            top: -40px;
            right: -40px;
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
        }
        
        .decoration-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 60px;
            overflow: hidden;
        }
        
        .decoration-bottom::before {
            content: "";
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
        }
        
        @media print {
            body {
                background: none;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
            }
            
            .footer {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="decoration-top"></div>
            <div class="decoration-bottom"></div>
            <div class="store-name">SUPER SHOP</div>
            <div class="store-details">
                123 Supermarket Street, City Center<br>
                Phone: (123) 456-7890 | Email: info@supershop.com<br>
                VAT Registration No: 123456789
            </div>
            <div class="invoice-title">Tax Invoice</div>
        </div>
        
        <div class="invoice-info">
            <div class="info-left">
                <div class="info-row">
                    <span class="info-label">Invoice No:</span>
                    <span class="info-value">{{ $sale->invoice_no }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date & Time:</span>
                    <span class="info-value">{{ date('d M Y, h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Customer:</span>
                    <span class="info-value">{{ $sale->customer_name ?? 'Walk-in Customer' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $sale->customer_phone ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Cashier:</span>
                    <span class="info-value">John Doe</span>
                </div>
            </div>
            <div class="info-right">
                <div class="qr-code">
                    <!-- <img src="data:image/png;base64," alt="QR Code"> -->
                </div>
            </div>

        </div>
        
        <div class="invoice-body">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}<br><small>{{ $item->unit }}</small></td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>{{ number_format($sale->subtotal, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>VAT ({{ $sale->total_vat > 0 ? round(($sale->total_vat / $sale->subtotal) * 100, 2) : 0 }}%):</span>
                    <span>{{ number_format($sale->total_vat, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Discount:</span>
                    <span>-{{ number_format($sale->discount, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Grand Total:</span>
                    <span>{{ number_format($sale->grand_total, 2) }}</span>
                </div>
            </div>
            
            <div class="barcode">
                <div>Invoice #{{ $sale->invoice_no }}</div>
                <div style="margin-top: 5px; font-family: 'Libre Barcode 128', cursive; font-size: 28px;">
                    {{ str_replace('-', '', $sale->invoice_no) }}{{ date('Ymd') }}
                </div>
            </div>
            
            <div class="thank-you">
                Thank you for shopping with us!
            </div>
            
            <div class="hotline">
                Customer Care: 16500 | Website: www.supershop.com
            </div>
        </div>
        
        <div class="footer">
            <div class="terms-section">
                <div class="terms-title">RETURN POLICY:</div>
                <ul class="terms-list">
                    <li>Returns accepted within 7 days with original receipt</li>
                    <li>Items must be unused and in original packaging</li>
                    <li>Electronics must be returned in sealed box</li>
                </ul>
            </div>
            
            <div class="terms-section">
                <div class="terms-title">WARRANTY TERMS:</div>
                <ul class="terms-list">
                    <li>Manufacturer warranty applies to electronic items</li>
                    <li>Keep this invoice for warranty claims</li>
                    <li>Some items may require online registration</li>
                </ul>
            </div>
            
            <div class="terms-section">
                <div class="terms-title">OTHER TERMS:</div>
                <ul class="terms-list">
                    <li>Prices include VAT where applicable</li>
                    <li>We are not responsible for lost or stolen items</li>
                    <li>This invoice is proof of purchase and must be presented for returns</li>
                </ul>
            </div>
            
            <div style="text-align: center; margin-top: 15px;">
                Software by SuperShop POS v2.5 | Printed on: {{ date('d M Y, h:i A') }}
            </div>
        </div>
    </div>
</body>
</html>