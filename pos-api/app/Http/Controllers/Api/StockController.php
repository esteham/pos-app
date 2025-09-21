<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function adjust(Request $request)
    {
    	$user = $request->user();

    	$role = strtolower((string)($user->role ?? $user->user_type ?? $user->type ?? ''));

    	if(!$user || !in_array($role, ['admin','manager','super admin','super_admin']))
    	{
    		return response()->json(['message' => 'Forbidden'], 403);
    	}

    	$data = $request->validate([

    		'product_id' => 'required|exists:products,id',
    		'quantity' => 'required|numeric|min:0.001',
    		'note' => 'nullable|string|max:255',
    		'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
    	]);

    	$p = Product::lockForUpdate()->findOrFail($data['product_id']);
    	$qty = (float)$data['quantity'];
    	$p->increment('stock',$qty);

    	StockMovement::create([

    			'product_id' => $p->id,
    			'type' => 'IN',
    			'quantity' =>$qty,
    			'note' => $data['note'] ?? 'Admin Stock Adjust',
    			'created_by' => $user->id,

    	]);

    	if($request->hasFile('image'))
    	{
    		$file = $request->file('image');
    		$ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
    		$dir = public_path('images/products');
    		if(!is_dir($dir)) { @mkdir($dir, 0775, true);}

    		if(!empty($p->image) && is_file($dir.DIRECTORY_SEPERATOR.$p->image))
    		{
    			@unlink($dir.DIRECTORY_SEPERATOR.$p->image);
    		}

    		$filename = 'p_ '.$p->id.'_'.time().'.'.$ext;
    		$file->move($dir, $filename);

    		$p->image = $filename;
    		$p->save();
    	}

    	return response()->json([

    		'message' => 'Stock Added',
    		'product' => $p->fresh(),
    	], 201);

    }
}
