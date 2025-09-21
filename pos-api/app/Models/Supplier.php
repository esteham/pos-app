<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [

    	'name','phone','email','company','tax_id','address','opening_balance','is_active','created_by'

    ];

    protected $casts = ['is_active'=> 'boolean', 'opening_balance'=>'decimal:2'];

    public function purchases()
    {
    	return $this->hasMany(Purchase::class);
    }

    public function payments()
    {
    	return $this->hasMany(SupplierPayment::class);
    }

    public function getCurrentDueAttribute()
    {
    	$p = $this->purchases()->sum('grand_total');
    	$paidByPurch = $this->purchases()->sum('paid_amount');
    	$extraPays = $this->payments()->sum('amount');
    	return round($p - $paidByPurch - $extraPays, 2);
    }
}
