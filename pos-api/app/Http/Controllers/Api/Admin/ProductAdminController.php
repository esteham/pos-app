<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductAdminController extends Controller
{
    private function ensureAdmin(Request $request)
    {
    	$u = $request->user();
    	$role = strtolower((string)($u->role ?? $u->user_type ?? $u->type ?? ''));
    	if(!$u || !in_array($role, ['admin','manager','super admin','super_admim','superadmin']))
    	{
    		abort(403, 'Forbidden');
    	}
    }

    public function index(Request $request)
    {
    	$this->ensureAdmin($request);

    	$q = trim((string)$request->query('q',''));
    	$per = min(100, max(5, (int)$request->query('per_page',20)));

    	$rows = Product::query()->when($q !== '', function($qq) use ($q){

    		$qq->where(function($w) use ($q){

    			$w->where('name','like',"%{$q}%")
    			->orWhere('sku','like',"%{$q}%")
    			->orWhere('barcode','like',"%{$q}%");
    		});
    	})->orderBy('name')->paginate($per);

    	return response()->json($rows);
    }

    public function show(Request $request, Product $product)
    {
    	$this->ensureAdmin($request);
    	return response()->json($product);
    }

    public function store(Request $request)
    {
    	$this->ensureAdmin($request);

    	$data = $request->validate([

    		'category_id' => 'nullable|integer|exists:categories,id',
    		'name' => 'required|string|max:255',
    		'sku' => 'required|string|max:255|unique:products,sku',
    		'barcode' => 'nullable|string|max:255|unique:products, barcode',
    		'unit' => ['required', Rule::in(['PCS','KG','LT'])],
    		'price' => 'required|numeric|min:0',
    		'vat_percent' => 'nullable|numeric|min:0',
    		'is_active' => 'required|boolean',
    		'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
    	]);


    	$p = Product::create([

    		'category_id' => $data['category_id'] ?? null,
    		'name' => $data['name'],
    		'sku' => $data['sku'],
    		'barcode' => $data['barcode'] ?? null,
    		'unit' => $data['unit'],
    		'price' => $data['price'],
    		'vat_percent' => $data['vat_percent'] ?? null,
    		'is_active' => $data['is_active'] ? 1 : 0,
    		'image' => null,
    	]);

    	if($request->hasFile('image'))
    	{
    		$file = $request->file('image');
    		$ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
    		$dir = public_path('images/products');
    		if(!is_dir($dir)) @mkdir($dir, 0775, true);
    		$filename = 'p_'.$p->id.'_'.time().'.'.$ext;
    		$file->move($dir, $filename);
    		$file->image = $filename;
    		$p->save();
    	}

    	return response()->json(['message' => 'Created', 'product' => $p], 201);
    }

    public function update(Request $request, Product $product)
    {
    	$this->ensureAdmin($request);

    	$data = $request->validate([

    		'category_id' => 'nullable|integer|exists:categories,id',
    		'name' => 'required|string|max:255',
    		'sku' => ['required','string','max:255', Rule::unique('products','sku')->ignore($product->id)],
    		'barcode' => 'nullable|string|max:255|unique:products, barcode',
    		'unit' => ['required', Rule::in(['PCS','KG','LT'])],
    		'price' => 'required|numeric|min:0',
    		'vat_percent' => 'nullable|numeric|min:0',
    		'is_active' => 'required|boolean',
    		'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
    	]);

    	$product->fill([

    		'category_id' => $data['category_id'] ?? null,
    		'name' => $data['name'],
    		'sku' => $data['sku'],
    		'barcode' => $data['barcode'] ?? null,
    		'unit' => $data['unit'],
    		'price' => $data['price'],
    		'vat_percent' => $data['vat_percent'] ?? null,
    		'is_active' => $data['is_active'] ? 1 : 0,
    	])->save();

    	if($request->hasFile('image'))
    	{
    		$file = $request->file('image');
    		$ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
    		$dir = public_path('images/products');
    		if(!is_dir($dir)) @mkdir($dir, 0775, true);

    		if(!empty($product->image) && is_file($dir.DIRECTORY_SEPARATOR.$product->image))
    		{
    			@unlink($dir.DIRECTORY_SEPARATOR.$product->image);
    		}

    		$filename = 'p_'.$product->id.'_'.time().'.'.$ext;
    		$file->move($dir, $filename);
    		$product->image = $filename;
    		$product->save();
    	}

    	return response()->json(['message' => 'Updated', 'product'=>$product]);

    }

    public function destroy(Request $request, Product $product)
    {
    	$this->ensureAdmin($request);

    	$product->delete();
    	return response()->json(['message' => 'Deleted']);
    }
}
