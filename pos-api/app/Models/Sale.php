<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'customer_id',
        'customer_name',
        'customer_phone',
        'sold_by',
        'subtotal',
        'discount',
        'grand_total',
        'paid_ammount',
        'payment_method',
        'status',
        'user_id'
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public static function nextInvoice(): string
    {
        $prefix = 'INV-'.NOW()->format('Ymd').'-';
        $last   = self::where('invoice_no', 'like', $prefix.'%')->max('invoice_no');

        if(!$last) return $prefix. '0001';

        $n = (int)substr($last, -4) + 1;

        return $prefix.str_pad($n, 4, '0', STR_PAD_LEFT);
    }

}
