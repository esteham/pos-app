<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use Barryvdh\DomPDF\Facade\Pdf as PDF;         
use Illuminate\Support\Facades\Storage;        
use Illuminate\Support\Facades\Mail;            
use Illuminate\Support\Facades\Log;            

class SalesController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_phone' => 'nullable|string',
            'customer_name'  => 'nullable|string',
            'customer_email' => 'nullable|email',
            'payment_method' => ['required', Rule::in(['CASH','BKASH','CARD'])],
            'discount'       => 'nullable|numeric|min:0',
            'paid_amount'    => 'required|numeric|min:0',
            'items'          => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|numeric|min:0.001',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.unit'        => ['required', Rule::in(['PCS','KG','LT'])],
            'items.*.vat_percent' => 'nullable|numeric|min:0',
        ]);

        $discount = $data['discount'] ?? 0;

     
        $customer = null;
        if (!empty($data['customer_phone'])) 
        {
            $customer = Customer::updateOrCreate(
                ['phone' => $data['customer_phone']],
                [
                    'name'  => $data['customer_name'] ?? null,
                    'email' => $data['customer_email'] ?? null,
                ]
            );
        }

    
        $sale = DB::transaction(function () use ($data, $customer, $discount, $request) {
            $subtotal = 0; $totalVat = 0; $itemsToInsert = [];

            foreach ($data['items'] as $line) {
              
                $p = Product::lockForUpdate()->find($line['product_id']);
                if (!$p) abort(422, "Product not found");

                if ($line['unit'] !== $p->unit) {
                    abort(422, "Unit mismatch for product {$p->name}");
                }

                $qty = (float)$line['quantity'];
                if ($qty <= 0) abort(422, "Quantity must be > 0");

                if ($p->stock < $qty) {
                    abort(422, "Insufficient stock for {$p->name}");
                }

                $unitPrice = (float)$line['unit_price'];
                $vatPercent = isset($line['vat_percent']) ? (float)$line['vat_percent'] : (float)$p->vat_percent;
                $lineAmount = $unitPrice * $qty;
                $vatAmount  = round(($lineAmount * $vatPercent) / 100, 2);
                $lineTotal  = $lineAmount + $vatAmount;

                $subtotal += $lineAmount;
                $totalVat += $vatAmount;

               
                $p->decrement('stock', $qty);
                StockMovement::create([
                    'product_id' => $p->id,
                    'type'       => 'OUT',
                    'quantity'   => $qty,
                    'note'       => 'POS sale',
                    'created_by' => optional($request->user())->id,
                ]);

                $itemsToInsert[] = [
                    'product_id'   => $p->id,
                    'product_name' => $p->name,
                    'unit_price'   => $unitPrice,
                    'quantity'     => $qty,
                    'unit'         => $p->unit,
                    'vat_percent'  => $vatPercent,
                    'vat_amount'   => $vatAmount,
                    'line_total'   => $lineTotal,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }

            $grand = max(0, $subtotal + $totalVat - $discount);

            $sale = Sale::create([
                'invoice_no'    => Sale::nextInvoice(),
                'customer_id'   => optional($customer)->id,
                'customer_phone'=> $customer->phone ?? ($data['customer_phone'] ?? null),
                'subtotal'      => round($subtotal,2),
                'total_vat'     => round($totalVat,2),
                'discount'      => round($discount,2),
                'grand_total'   => round($grand,2),
                'paid_amount'   => round((float)$data['paid_amount'],2),
                'payment_method'=> $data['payment_method'],
                'status'        => 'PAID',
                'user_id'       => optional($request->user())->id,
            ]);

            foreach ($itemsToInsert as $it) {
                $it['sale_id'] = $sale->id;
                SaleItem::create($it);
            }

            return $sale; 
        });

       
        $emailed = false;                                                           
        $invoiceNo = $sale->invoice_no;                                             
        $sale->load('items');                                                       

      
        $pdfOutput = null;                                                         
        try 
        {
            $pdfOutput = PDF::loadView('invoices.show', ['sale' => $sale])->output();
        } 
        catch (\Throwable $e)
         {
            Log::warning('PDF render failed: '.$e->getMessage());                   
        }

     
        if ($pdfOutput) 
        {                                                           
            try 
            {
                Storage::makeDirectory('invoices');
                Storage::put('invoices/'.$invoiceNo.'.pdf', $pdfOutput);
            } 
            catch (\Throwable $e) {
                Log::warning('PDF save failed: '.$e->getMessage());                 
            }
        }

     
        $to = optional($customer)->email;                                           
        if ($to && $pdfOutput)
         {                                                     
            try 
            {
                Mail::send('emails.invoice_plain', ['sale' => $sale], function ($m) use ($to, $pdfOutput, $invoiceNo) 
                {
                    $m->to($to)
                      ->subject('Your Invoice '.$invoiceNo)
                      ->attachData($pdfOutput, $invoiceNo.'.pdf', ['mime' => 'application/pdf']);
                });
                $emailed = true;
            } 
            catch (\Throwable $e) 
            {
                Log::warning('Invoice mail failed: '.$e->getMessage());           
            }
        }
     

        return response()->json([
            'invoice_no'  => $invoiceNo,
            'sale'        => $sale,
            'invoice_pdf' => url('/api/sales/'.$invoiceNo.'/invoice'),  
            'emailed'     => $emailed,                                   
        ], 201);
    }

    public function show(string $invoice_no)
    {
        $sale = Sale::with('items')->where('invoice_no',$invoice_no)->first();
        if (!$sale) return response()->json(['message'=>'Not found'], 404);
        return response()->json($sale);
    }

  
    public function invoice(string $invoice_no)
    {
        $sale = Sale::with('items')->where('invoice_no',$invoice_no)->firstOrFail();

        $pdfPath = 'invoices/'.$invoice_no.'.pdf';
        if (!Storage::exists($pdfPath)) 
        {
          
            try 
            {
                $pdfOutput = PDF::loadView('invoices.show', ['sale'=>$sale])->output();
                Storage::makeDirectory('invoices');
                Storage::put($pdfPath, $pdfOutput);
            } 
            catch (\Throwable $e) 
            {
                Log::warning('PDF lazy-gen failed: '.$e->getMessage());
            }
        }

        $abs = Storage::path($pdfPath);
        if (request()->boolean('download')) 
        {
            return response()->download($abs, $invoice_no.'.pdf', ['Content-Type' => 'application/pdf']);
        }
        return response()->file($abs, ['Content-Type' => 'application/pdf']);
    }

    public function print(string $invoice_no)
    {
        $sale = Sale::with('items')->where('invoice_no', $invoice_no)->firstOrFail();
        return response()->view('invoices.print', ['sale' => $sale]);
    }
}
