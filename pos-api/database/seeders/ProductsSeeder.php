<?php

namespace Database\Seeders;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
   
    public function run(): void
    {
        $grocery = Category::where('name','Grocery')->first();
         $dairy = Category::where('name','Dairy')->first();

         $data = [

         	['category_id' => $grocery->id, 'name' => 'Toothpaste 100g','image' =>'toothpaste.jpg','sku' => 'TP-100', 'barcode' => '89014458745', 'unit' => 'PCS', 'price' => 90, 'stock' => 100, 'vat_percent' => 5],

         	['category_id' => $grocery->id, 'name' => 'Soap 75g','image' =>'soap.jpg','sku' => 'SP-75', 'barcode' => '900000145', 'unit' => 'PCS', 'price' => 45, 'stock' => 200, 'vat_percent' => 5],

         	['category_id' => $grocery->id, 'name' => 'Rice (Najirshail)','image' =>'Najirshail.jpg','sku' => 'RC-NS', 'barcode' => null, 'unit' => 'KG', 'price' => 90, 'stock' => 500.000, 'vat_percent' => 0],

         	['category_id' => $dairy->id, 'name' => 'Loose Cheese','image' =>'Cheese.jpg','sku' => 'CH-LS', 'barcode' => null, 'unit' => 'KG', 'price' => 850, 'stock' => 20.000, 'vat_percent' => 5]
         ];

         foreach($data as $p)
         {
         	if(($p['barcode'] ?? null) === '')
         	{
         		$p['barcode'] = null;
         	}

         	Product::updateOrCreate(['sku' => $p['sku']], $p);
         }
    }
}
