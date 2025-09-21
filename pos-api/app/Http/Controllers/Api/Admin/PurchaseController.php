<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    private function ensureAdmin(Request $r)
    {
    	$u = $r->user();
    	$role = strtolower((string)($u->role ?? $u->user_type ?? $u->type ?? ''));
    	if(!$u || !in_array($role, ['admin','manager','super admin','super_admim','superadmin']))
    	{
    		abort(403, 'Forbidden');
    	}
    }

    public function index(Request $r)
    {
    	$this->ensureAdmin($r);

    	$per = min(200, max(5, (int)$r->query('per_page',20)));

    	$q = Purchase::with('supplier')->withCount('items')->orderByDesc('created_at');

    	if($r->query('supplier_id')) $q->where('supplier_id', $r->query('supplier_id'));

    	if($r->query('from')) $q->whereDate('created_at', '>=', $r->query('from'));
    	if($r->query('to')) $q->whereDate('created_at', '<=', $r->query('to'));
    	return response()->json($q->paginate($per));
    }

    public function summary(Request $r)
    {
    	$this->ensureAdmin($r);

    	$q = Purchase::query();

    	if($r->query('supplier_id')) $q->where('supplier_id', $r->query('supplier_id'));

    	if($r->query('from'))	$q->whereDate('created_at', '>=', $r->query('from'));


    	if($r->query('to'))	$q->whereDate('created_at', '<=', $r->query('to'));

    	$stats = $q->selectRaw('COUNT(*) as orders,

    							COALESCE(SUM(subtotal),0) as subtotal,
    							COALESCE(SUM(total_vat),0) as total_vat,
    							COALESCE(SUM(discount),0) as discount,
    							COALESCE(SUM(grand_total),0) as grand_total,
    							COALESCE(SUM(paid_amount),0) as paid_amount
    				')->first();

    	$topSuppliers = Purchase::query()->when($r->query('supplier_id'), fn($qq)=>$qq->where('supplier_id', $r->query('supplier_id')))->when($r->query('from'), fn($qq)=>$qq->whereDate('created_at','>=',$r->query('from')))->when($r->query('to'), fn($qq)=>$qq->whereDate('created_at','<=',$r->query('to')))->selectRaw('supplier_id, COALESCE(SUM(grand_total),0) as amount')->groupBy('supplier_id')->orderByDesc('amount')->with('supplier:id,name,company')->limit(5)->get();


     $due = max(0, ($stats->grand_total ?? 0) - ($stats->paid_amount ?? 0));

    return response()->json([

    			'summary' => [

    						'orders' => (int)($stats->orders ?? 0),
    						'subtotal' => round((float)$stats->subtotal, 2),
    						'total_vat' => round((float)$stats->total_vat, 2),
    						'discount' => round((float)$stats->discount, 2),
    						'grand_total' => round((float)$stats->grand_total, 2),
    						'paid_total' => round((float)$stats->paid_amount, 2),
    						'due_total' => round($due, 2),

    						],

    			'top_suppliers' => $topSuppliers->map(function($r){

    				return [

    						'supplier_id' => $r->supplier_id,
    						'name' => optional($r->supplier)->name,
    						'company' => optional($r->supplier)->company,
    						'amount' => round((float)$r->amount, 2),

    				       ];
    			}),

    ]);

    }

    public function show(Request $r, Purchase $purchase)
    {
    	$this->ensureAdmin($r);

    	$purchase->load('items','supplier');

    	return response()->json($purchase);
    }

    public function store(Request $r)
    {
    	$this->ensureAdmin($r);

    	$data = $r->validate([

    		'supplier_id' => 'required|exists:suppliers,id',
    		'reference_no' => 'nullable|string|max:100',
    		'purchase_date' => 'nullable|date',
    		'discount' => 'nullable|numeric|min:0',
    		'paid_amount' => 'required|numeric|min:0',
    		'payment_method' => ['required', Rule::in(['CASH','BKASH','CARD','BANK','DUE'])],
    		'remarks' => 'nullable|string',
    		'items' => 'required|array|min:1',
    		'items.*.product_id' => 'required|exists:products,id',
    		'items.*.quantity' => 'required|numeric|min:0.001',
    		'items.*.unit_price' => 'required|numeric|min:0',
    		'items.*.unit' => ['required', Rule::in(['PCS','KG','LT'])],
    		'items.*.vat_percent' => 'nullable|numeric|min:0',
    	]);

    	$discount = (float)($data['discount'] ?? 0);
    	$userId = optional($r->user())->id;

    	$purchase = DB::transaction(function () use ($data, $discount, $userId){

    		$subtotal = 0; $totalVat = 0; $itemsToInsert = [];

    		foreach($data['items'] as $line)
    		{
    			$p = Product::lockForUpdate()->find($line['product_id']);

    			if(!$p) abort(422, "Product Not Found");
    			if($line['unit'] !== $p->unit) abort(422, "Unit mismatch for {$p->name}");

    			$qty = (float)$line['quantity'];
    			if($qty <= 0) abort(422, "Quantity must be > 0");

    			$unitPrice = (float)$line['unit_price'];
    			$vatPercent = isset($line['vat_percent']) ? (float)$line['vat_percent'] : (float)$p->vat_percent;

    			$lineAmount = $unitPrice * $qty;

    			$vatAmount = round(($lineAmount * $vatPercent)/100, 2);
    			$lineTotal = $lineAmount + $vatAmount;

    			$subtotal += $lineAmount;
    			$totalVat += $vatAmount;

    			$p->increment('stock', $qty);
    			StockMovement::create([
    				'product_id' => $p->id,
    				'type' => 'IN',
    				'quantity' => $qty,
    				'note' => 'Purchase',
    				'created_by' => $userId,
    			]);

    			$itemsToInsert[] = [

    				'product_id' => $p->id,
    				'product_name' => $p->name,
    				'unit' => $p->unit,
    				'quantity' => $qty,
    				'unit_price' => $unitPrice,
    				'vat_percent' => $vatPercent,
    				'vat_amount' => $vatAmount,
    				'line_total' => $lineTotal,
    				'created_at' => now(),
    				'updated_at' => now(),

    			];

    		}

    		$grand = max(0, $subtotal + $totalVat - $discount);

    		$purchase = Purchase::create([

    			'purchase_no' => Purchase::nextNo(),
    			'supplier_id' => $data['supplier_id'],
    			'reference_no' => $data['reference_no'] ?? null,
    			'purchase_date' => $data['purchase_date'] ?? now(),
    			'subtotal' => round($subtotal, 2),
    			'total_vat' => round($totalVat, 2),
    			'discount' => round($discount, 2),
    			'grand_total' => round($grand, 2),
    			'paid_amount' => round((float)$data['paid_amount'], 2),
    			'payment_method' => $data['payment_method'],
    			'status' => 'COMPLETED',
    			'user_id' => $userId,
    			'remarks' => $data['remarks'] ?? null,

    		]);

    		foreach($itemsToInsert as $it)
    		{
    			$it['purchase_id'] = $purchase->id;
    			PurchaseItem::create($it);
    		}

    		return $purchase;

    	});

    	$purchase->load('items');

    	return response()->json([

    		'message' => 'Purchase Created',
    		'purchase_no' => $purchase->purchase_no,
    		'purchase' => $purchase
    	], 201);
    }

   
}
