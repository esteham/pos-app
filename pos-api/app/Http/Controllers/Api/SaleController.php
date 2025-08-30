<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// import models
use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\StockMovement;

// Database
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SaleController extends Controller
{

    // Customer Store
    public function store(Request $request)
    {
        $data = $request->validate([

            'customer_phone'    => 'nullable|regex:/^[0-9]{10,15}$/',
            'customer_name'     => 'nullable|string',
            'payment_method'    => ['required', Rule::in(['cash', 'credit_card', 'internet_banking', 'bank_transfer'])],
            'discount'          => 'nullable|numeric|min:0',
            'paid_amount'      => 'required|numeric|min:0',
            'items'             => 'required|array|min:1',
            'items.*.product_id'=> 'required|exists:products,id',
            'items.*.quantity'  => 'required|numeric|min:0.001|max:100000',
            'items.*.unit_price'=> 'required|numeric|min:0|max:100000',
            'items.*.unit'      => ['required', Rule::in(['pcs', 'kg', 'liters'])],
            'items.*.vat_percent'=> 'nullable|numeric|min:0',
        ]);

        $customer = null;

        if (!empty($data['customer_phone']))
        {
            $customer = Customer::firstOrCreate(
                ['phone' => $data['customer_phone']],
                ['name' => $data['customer_name'] ?? null ]
            );

        }// end if

        $discount = $data['discount'] ?? 0;

        return DB::transaction(function() use ($data, $customer, $discount, $request){

            $subtotal = 0;
            $totalVat = 0;
            $itemsToInsert = [];

            foreach($data['items'] as $line){

                $p = Product::lockForUpdate()->find($line['product_id']);
                if (!$p) abort(422, 'Product not found');

                if ($line['unit'] !== $p->unit)
                {
                    abort(422, 'Unit mismatch for product {$p->name}');
                }

                $qty = (float)$line['quantity'];
                if ($qty <= 0) abort(422, 'Quantity must be > 0');

                if ($p->stock < $qty)
                {
                    abort(422, 'Insufficient Stock for');
                }

                $unitPrice =(float)$line['unit_price'];
                $vatPercent = isset($line['vat_percent']) ? (float)$line['vat_percent'] : (float)$p->vat_percent;
                $lineAmount = $unitPrice * $qty;
                $vatAmount = round(($lineAmount * $vatPercent) / 100, 2);
                $lineTotal = $lineAmount + $vatAmount;

                $subtotal += $lineAmount;
                $totalVat += $vatAmount;

                $p->decrement('stock', $qty);

                StockMovement::create([

                    'product_id' => $p->id,
                    'type' => 'out',
                    'quantity' => $qty,
                    'notes' => 'Pos sale',
                    'created_by' => optional($request->user())->id, 
                
                ]); //end create stockMovement

                $itemsToInsert[] = [

                    'product_id' => $p->id,
                    'product_name' => $p->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $qty,
                    'unit' => $p->unit,
                    'vat_percent' =>$vatPercent,
                    'vat_amount' => $vatAmount,
                    'line_total' => $lineTotal,
                    'created_at' => now(),
                    'updated_at' => now()

                ]; //end inset items

            } //end foreach

            $grandTotal = max(0, $subtotal + $totalVat - $discount);

            $sale = Sale::create([

                'invoice_no' => Sale::nextInvoice(),
                'customer_id' => optional($customer)->id,
                'customer_phone' => $customer->phone ?? ($data['customer_phone'] ?? null),
                'subtotal' =>  round($subtotal, 2),
                'total_vat' => round($totalVat, 2),
                'discount' => round($discount, 2),
                'paid_amount' => round((float)$data['paid_amount'], 2),
                'payment_method' => $data['payment_method'],
                'grand_total' => round($grandTotal, 2),
                'status' => 'paid',
                'user_id' => optional($request->user())->id,

            ]); //end sale create

            foreach ($itemsToInsert as $item)
            {

                $item['sale_id'] = $sale->id;
                SaleItem::create($item);

            } //end items foreach

            return response()->json([

                'invoice_no' => $sale->invoice_no,
                'sale' => $sale->load('items'),

            ]); //end response return

        }); //end transction return 

    } //End store method

    public function show(string $invoce_no)
    {

        $sale = Sale::with('items')->where('invoice_no', $invoce_no)->first();

        if (!$sale) 
        return response()->json([
            'message' => 'Not found'
        ], 404);
        
        return response()->json($sale);

    } //end show method

}
