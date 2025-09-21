<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function today(Request $request)
    {
    	$this->ensureAdmin($request);

    	$start = Carbon::today();
    	$end = Carbon::tomorrow();

    	$stats = Sale::whereBetween('created_at', [$start, $end])->selectRaw('COUNT(*) as orders, COALESCE(SUM(subtotal),0) as subtotal,
    		 COALESCE(SUM(total_vat),0) as total_vat,
    		  COALESCE(SUM(discount),0) as discount,
    		   COALESCE(SUM(grand_total),0) as grand_total,
    		    COALESCE(SUM(paid_amount),0) as paid_amount
    		')->first();

    	$items =  SaleItem::whereBetween('created_at', [$start, $end])->selectRaw('product_name, SUM(quantity) as qty, SUM(line_total) as amount')->groupBy('product_name')->orderByDesc('qty')->limit(10)->get();

    	return response()->json([

    			'date' => $start->toDateString(),
    			'stats' => $stats,
    			'top' => $items,
    	]);
    }

    public function topSales(Request $request)
    {
    	$this->ensureAdmin($request);

    	$days = max(1, (int) $request->query('days', 7));
    	$from = Carbon::today()->subDays($days-1);
    	$items = SaleItem::where('created_at', '>=', $from)->selectRaw('product_name, SUM(quantity) as qty, SUM(line_total) as amount')->groupBy('product_name')->orderByDesc('qty')->limit(50)->get();

    	return response()->json([

    			'from' => $from->toDateString(),
    			'days' => $days,
    			'rows' => $items
    	]);
    }

    public function sales(Request $request)
    {
    	$this->ensureAdmin($request);
    	$from = $request->query('from');
    	$to = $request->query('to');

    	$q = Sale::query();

    	if($from) $q->whereDate('created_at', '>=', $from);
    	if($to) $q->whereDate('created_at', '<=', $to);

    	$rows = $q->orderByDesc('created_at')->limit(500)->get(['invoice_no','created_at','grand_total','paid_amount','discount','payment_method','customer_phone']);

    	$summary = [

    		'orders' => $rows->count(),
    		'grand_total' => round($rows->sum('grand_total'),2),
    		'paid_total' => round($rows->sum('paid_amount'),2),
    		'discount' => round($rows->sum('discount'),2),
    	];

    	return response()->json(['summary' => $summary, 'rows' => $rows]);
    }

    private function ensureAdmin(Request $request)
    {
    	$role = optional($request->user())->user_type ?? '';
    	if(!in_array($role, ['admin','manager'])) abort(403, 'Forbidden');
    }
}
