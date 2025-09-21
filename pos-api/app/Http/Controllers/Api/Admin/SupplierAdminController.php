<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierAdminController extends Controller
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
    	$q = trim((string)$r->query('q',''));

    	$per = min(100, max(5, (int)$r->query('per_page',20)));
    	$rows = Supplier::query()->when($q !== '', fn($qq)=>
    									$qq->where('name','like',"%{$q}%")
    									->orWhere('phone','like',"%{$q}%")
    									->orWhere('company','like',"%{$q}%")
    								)->orderBy('name')->paginate($per);
    	return response()->json($rows);
    }

    public function show(Request $r, Supplier $supplier)
    {
    	$this->ensureAdmin($r);
    	return response()->json($supplier);
    }

    public function store(Request $r)
    {
    	$this->ensureAdmin($r);

    	$data = $r->validate([

    		'name' => 'required|string|max:255',
    		'phone' => 'nullable|string|max:12',
    		'email' => 'nullable|email',
    		'company' => 'nullable|string|max:255',
    		'tax_id' => 'nullable|string|max:100',
    		'address' => 'nullable|string',
    		'opening_balance' => 'nullable|numeric|min:0',
    		'is_active' => 'required|boolean',
    	]);

    	$data['created_by'] = optional($r->user())->id;
    	$s = Supplier::create($data);
    	return response()->json(['message' => 'Created', 'supplier' => $s], 201);
    }

    public function update(Request $r, Supplier $supplier)
    {
    	$this->ensureAdmin($r);
    	$data = $r->validate([

    		'name' => 'required|string|max:255',
    		'phone' => 'nullable|string|max:12',
    		'email' => 'nullable|email',
    		'company' => 'nullable|string|max:255',
    		'tax_id' => 'nullable|string|max:100',
    		'address' => 'nullable|string',
    		'opening_balance' => 'nullable|numeric|min:0',
    		'is_active' => 'required|boolean',
    	]);

    	$supplier->update($data);
    	return response()->json(['message' => 'Updated', 'supplier' => $supplier]);
    } 

    public function destroy(Request $r, Supplier $supplier)
    {
    	$this->ensureAdmin($r);
    	$supplier->delete();
    	return response()->json(['message' => 'Deleted']);
    }
}
