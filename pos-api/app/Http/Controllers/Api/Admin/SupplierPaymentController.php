<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierPaymentController extends Controller
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

    public function store(Request $r)
    {
    	$this->ensureAdmin($r);

    	$d = $r->validate([

    		'supplier_id' => 'required|exists:suppliers,id',
    		'purchase_id' => 'nullable|exists:purchases,id',
    		'amount' => 'required|numeric|min:0.01',
    		'method' => ['required', Rule::in(['CASH','BKASH','CARD','BANK'])],
    		'note' => 'nullable|string|max:255',
    		'paid_at' => 'nullable|date',
    	]);

    	$d['user_id'] = optional($r->user())->id;
    	$pay = SupplierPayment::create($d);
    	return response()->json(['message' =>'Payment Recorded', 'payment' => $pay],201);
    }
}
