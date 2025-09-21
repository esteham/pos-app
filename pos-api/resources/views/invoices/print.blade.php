<!DOCTYPE html>
<html>
<head>
	<title>Invoice {{ $sale->invoice_no }}</title>

	<style type="text/css">
		body
		{
			font-family: DejaVu Sans, Arial, sans-serif;
			font-size: 12px;
			margin: 16px;
			color: #000;
		}

		.muted
		{
			color: #555;
		}

		.row
		{
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
			margin-bottom: 10px;
		}

		table
		{
			width: 100%;
			border-collapse: collapse;
		}

		th, td
		{
			border: 1px solid #000;
			padding: 6px;
		}

		th
		{
			background-color: #f2f2f2;			
		}

		.right
		{
			text-align: right;
		}

		.totals
		{
			margin-top: 10px;
		}

		@media print
		{
			@page 
			{
				size: auto; margin: 10mm;
			}

			.no-print
			{
				display: none !important;
			}
		}
	</style>

	<script type="text/javascript">
		window.addEventListener('load', ()=> {
			try 
			{
				window.focus();
			} 
			catch(e) 
			{
				
			}

			window.print();
			setTimeout(()=> { try { window.close();} catch(e){}}, 2000);

		});
	</script>
</head>
<body>
	<div class="row">
		<div>
			<h2>Live POS</h2>
			<div class="muted">
				Invoice: <strong>{{ $sale->invoice_no }}</strong>
			</div>
			<div class="muted">
				{{ $sale->created_at->format('Y-m-d H:i') }}
			</div>
		</div>

		<div class="right">
			<h4>Customer</h4>
			<div>Phone: {{ $sale->customer_phone ?? '__'}}</div>
			@if(optional($sale->customer)->name)
				<div>Name: {{ $sale->customer->name }}</div>
			@endif

			@if(optional($sale->customer)->email)
				<div>Email: {{ $sale->customer->email }}</div>
			@endif
		</div>
	</div>

	<table>
		<thead>
			<tr>
				<th>#</th>
				<th>Product</th>
				<th class="right">Unit Price</th>
				<th class="right">Qty</th>
				<th class="right">VAT %</th>
				<th class="right">Line Total</th>
			</tr>
		</thead>

		<tbody>
			@foreach($sale->items as $i => $it)
				@php
					$line = $it->unit_price * $it->quantity;
					$total = $it->line_total;
				@endphp
			<tr>
				<td>{{ $i + 1 }}</td>
				<td>{{ $it->product_name }}</td>
				<td class="right">{{ number_format($it->unit_price, 2)}}</td>
				<td class="right">{{ number_format($it->quantity, 2)}} {{ $it->unit }}</td>
				<td class="right">{{ number_format($it->vat_percent, 2)}}</td>
				<td class="right">{{ number_format($total, 2)}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<div class="totals">
		<div class="right"><strong>Subtotal:</strong>{{ number_format($sale->subtotal, 2)}}</div>
		<div class="right"><strong>Total VAT:</strong>{{ number_format($sale->total_vat, 2)}}</div>

		@if($sale->discount > 0)
			<div class="right"><strong>Discount:</strong>{{ number_format($sale->discount, 2)}}</div>
		@endif

		<div class="right"><strong>Grand Total:</strong>{{ number_format($sale->grand_total, 2)}}</div>
		<div class="right"><strong>Paid:</strong>{{ number_format($sale->paid_amount, 2)}}</div>
		<div class="right"><strong>Payment Method:</strong>{{ $sale->payment_method}}</div>
	</div>

	<div class="no-print" style="margin-top: 12px;">
		<button onclick="window.print()">Print</button>
		<button onclick="window.close()">Close</button>
	</div>
</body>
</html>