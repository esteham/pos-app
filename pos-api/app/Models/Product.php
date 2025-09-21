<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

     protected $fillable = ['category_id','name','image','sku','barcode','unit','price','stock','vat_percent','is_active'];

     protected $appends = ['image_url'];

     public function setBarcodeAttribute($value)
     {
     	$this->attributes['barcode'] = ($value === '' ? null : $value);
     }

     public function category() { return $this->belongsTo(Category::class); }

     public function getImageUrlAttribute()
     {
     	if(!$this->image) return null;

     	return url('images/products/'.$this->image);
     }
}
