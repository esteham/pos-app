<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'price',
        'image',
        'sku',
        'barcode',
        'unit',
        'stock',
        'vat_percent',
        'is_active'
    ];

    public function setBarcodeAttribute(){
        $this->attributes['barcode'] = ($value === '' ? null : $value);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

}
