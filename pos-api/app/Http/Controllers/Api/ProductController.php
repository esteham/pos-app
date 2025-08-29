<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //search function
    public function search(Request $request)
    {
        $q = trim($request->query('q', ''));

        $items = Product::query()
            
                ->when($q !== '', function ($qry) use ($q){ 
                    $qry->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%")
                        ->orWhere('barcode', 'like', "%{$q}%");
                        
                    if (is_numeric($q)) {
                        $qry->orWhere('id', intval($q));
                    }

                })
                ->where('is_active', true)
                ->orderBy('name')
                ->limit(20)
                ->get();

        return response()->json($items);

    }//end function


    //find function
    public function find(Request $request)
    {
        $id = $request->query('id');
        $sku = $request->query('sku');
        $barcode = $request->query('barcode');

        $product = null;

        if ($id){
            $product = Product::where('id', $id)->first();
        }
        elseif($sku){
            $product = Product::where('sku', $sku)->first();
        }
        elseif ($barcode){
            $product = Product::where('barcode', $barcode)->first();
        }

        if (!$product) {
            return response()->json([
                'message' => "Not Found"
            ], 404);
        }

        return response()->json($product);

    }

}
