<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_name',
        'unit_price',
        'quantity',
        'unit',
        'discount',
        'vat_percent',
        'vat_amount',
        'total_price'
    ];

}
