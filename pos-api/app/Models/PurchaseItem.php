<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

     protected $fillable = [

    	'purchase_id','product_id','product_name','unit','quantity','unit_price','vat_percent','vat_amount','line_total'
    ];

    protected $casts = [

    		'quantity' => 'decimal:2', 'unit_price'=>'decimal:2','vat_percent'=>'decimal:2','vat_amount'=>'decimal:2','line_amount'=>'decimal:2'
    ];

    public function purchase()
    {
    	return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
    	return $this->belongsTo(Product::class);
    }

}
