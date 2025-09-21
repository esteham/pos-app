<!DOCTYPE html>
<html>
<head>
	<title>POS Email</title>
</head>
<body>
	<p>Dear Customer,</p>
	<p>Tanks for your purchase. Your invoice <strong>{{ $sale->invoice_no }}</strong> is attached</p>
	<p>Grand Total: <strong>{{ number_format($sale->grand_total, 2) }}</strong></p>
	<p>Regards, <br> Live POS</p>

</body>
</html>