<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [

    	'purchase_no','supplier_id','reference_no','purchase_date','subtotal','total_vat','discount','grand_total','paid_amount','payment_method','status','user_id','remarks'
    ];

    protected $casts = [

    	'subtotal' => 'decimal:2', 'total_vat' => 'decimal:2', 'discount' => 'decimal:2', 'grand_total' => 'decimal:2', 'paid_amount' => 'decimal:2'
    ];

    public function supplier()
    {
    	return $this->belongsTo(Supplier::class);
    } 

    public function items()
    {
    	return $this->hasMany(PurchaseItem::class);
    }

    public static function nextNo(): string
    {
    	$date = now()->format('Ymd');
    	$count = static::whereDate('created_at', now()->toDateString())->count() + 1;
    	return 'PUR-'.$date.'_'.str_pad((string)$count, 4, '0', STR_PAD_LEFT);
    }
}
