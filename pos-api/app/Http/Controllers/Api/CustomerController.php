<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function find(Request $request)
    {
    	$phone = trim((String)$request->query('phone',''));
    	if($phone === '') return response()->json(['message'=>'Phone number required'], 422);
    	$c = Customer::where('phone', $phone)->first();
    	if(!$c) return response()->json(['found'=> false], 200);
    	return response()->json(['found'=>true, 'customer'=>$c], 200);
    }

    public function search(Request $request)
    {
    	$q = trim((String)$request->query('q',''));
    	if($q === '') return response()->json([]);
    	$items = Customer::where('phone','like',"%{$q}%")->orWhere('name','like',"%{$q}%")->limit(10)->get();
    	return response()->json($items);

    }
}
