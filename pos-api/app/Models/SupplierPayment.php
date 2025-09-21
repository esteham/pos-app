<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $fillable = [

    	'supplier_id','purchase_id','amount','method','note','paid_at','user_id'
    ];

    protected $casts = ['amount' => 'decimal:2', 'paid_at' => 'datetime'];

    public function supplier()
    {
    	return $this->belongsTo(Supplier::class);
    }

     public function purchase()
    {
    	return $this->belongsTo(Purchase::class);
    }
}
