<h1>Invoice: {{ $sale->invoice_no }}</h1>
<p>Customer: {{ $sale->customer_name ?? 'N/A' }}</p>
<p>Phone: {{ $sale->customer_phone ?? 'N/A' }}</p>

<table border="1" width="100%">
    <thead>
        <tr>
            <th>Product</th>
            <th>Unit</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>VAT</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sale->items as $item)
        <tr>
            <td>{{ $item->product_name }}</td>
            <td>{{ $item->unit }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $item->unit_price }}</td>
            <td>{{ $item->vat_amount }}</td>
            <td>{{ $item->line_total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p>Subtotal: {{ $sale->subtotal }}</p>
<p>Total VAT: {{ $sale->total_vat }}</p>
<p>Discount: {{ $sale->discount }}</p>
<p>Grand Total: {{ $sale->grand_total }}</p>
