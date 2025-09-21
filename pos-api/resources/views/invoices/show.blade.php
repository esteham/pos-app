<!DOCTYPE html>
<html>
<head>
	<title>Invoice {{ $sale->invoice_no }}</title>

	<style type="text/css">
		body
		{
			font-family: Dejavu Sans, sans-serif;
			font-size: 12px;			
		}

		table
		{
			width: 100%;
			border-collapse: collapse;
		}

		th, td
		{
			border: 1px solid #ddd;
			padding: 6px;
		}

		th
		{
			background: #f5f5f5;			
		}

		.right
		{
			text-align: right;
		}

		.mb-8
		{
			margin-bottom: 8px;
		}
	</style>
</head>
<body>

	<h2>Invoice: {{ $sale->invoice_no }}</h2>
	<div class="mb-8">
		<strong>Date:</strong>{{ $sale->created_at->format('Y-m-d H:i') }}<br>
		<strong>Customer Phone:</strong>{{ $sale->customer_phone ?? '_'}} <br>		
	</div>

	<table>
		<thead>
			<tr>
				<th>#</th>
				<th>Product</th>
				<th class="right">Unit Price</th>
				<th class="right">Qty/KG</th>
				<th class="right">VAT %</th>
				<th class="right">Line Total</th>
			</tr>			
		</thead>
		<tbody>
			@foreach($sale->items as $i => $it)
				@php
					$line = $it->unit_price * $it->quantity;
					$vat = $it->vat_amount;
					$total = $it->line_total;
				@endphp
			<tr>
				<td>{{ $i + 1 }}</td>
				<td>{{ $it->product_name }}</td>
				<td class="right">{{ number_format($it->unit_price, 2) }}</td>
				<td class="right">{{ number_format($it->quantity, 2) }} {{ $it->unit }}</td>
				<td class="right">{{ number_format($it->vat_percent, 2) }}</td>
				<td class="right">{{ number_format($total, 2) }}</td>
			</tr>

			@endforeach
		</tbody>
	</table>

	<div style="margin-top: 10px;">
		<div class="right">
			<strong>Subtotal:</strong> {{ number_format($sale->subtotal,2) }}
		</div>
		<div class="right">
			<strong>Total VAT::</strong> {{ number_format($sale->total_vat,2) }}
		</div>

		@if($sale->discount > 0)
			<div class="right"><strong>Discount:</strong>{{ number_format($sale->discount, 2) }}</div>
		@endif

		<div class="right"><strong>Grand Total:</strong>{{ number_format($sale->grand_total, 2) }}</div>
		<div class="right"><strong>Paid:</strong>{{ number_format($sale->paid_amount, 2)}}</div>
	</div>
</body>
</html>