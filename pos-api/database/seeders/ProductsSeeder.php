<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get existing categories
        $grocery = Category::where('name', 'Grocery')->first();
        $dairy = Category::where('name', 'Dairy')->first();
        $beverages = Category::where('name', 'Beverages')->first();
        $personalCare = Category::where('name', 'Personal Care')->first();
        
        $categories = [$grocery, $dairy, $beverages, $personalCare];

        // 1️⃣ Real products
        $products = [
            [
                'category_id' => $grocery->id,
                'name' => 'Toothpaste 100g',
                'image' => 'toothpaste.jpg',
                'sku' => 'TP-100',
                'barcode' => '89014458745',
                'unit' => 'pcs',
                'price' => 90,
                'stock' => 100,
                'vat_percent' => 5,
                'is_active' => true,
            ],
            [
                'category_id' => $grocery->id,
                'name' => 'Soap 75g',
                'image' => 'soap.jpg',
                'sku' => 'SP-75',
                'barcode' => '900000145',
                'unit' => 'pcs',
                'price' => 45,
                'stock' => 200,
                'vat_percent' => 5,
                'is_active' => true,
            ],
            [
                'category_id' => $grocery->id,
                'name' => 'Rice (Najirshail)',
                'image' => 'Najirshail.jpg',
                'sku' => 'RC-NS',
                'barcode' => null,
                'unit' => 'kg',
                'price' => 90,
                'stock' => 500,
                'vat_percent' => 0,
                'is_active' => true,
            ],
            [
                'category_id' => $dairy->id,
                'name' => 'Loose Cheese',
                'image' => 'Cheese.jpg',
                'sku' => 'CH-LS',
                'barcode' => null,
                'unit' => 'kg',
                'price' => 850,
                'stock' => 20,
                'vat_percent' => 5,
                'is_active' => true,
            ]
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['sku' => $p['sku']], $p);
        }

        // 2️⃣ Fake products
        for ($i = 1; $i <= 17; $i++) {
            $category = $faker->randomElement($categories);
            $unit = $faker->randomElement(['pcs','kg','liters']);

            Product::create([
                'category_id' => $category->id,
                'name' => ucfirst($faker->words(2, true)),
                'image' => $faker->imageUrl(640, 480, 'product', true),
                'description' => $faker->paragraph(),
                'sku' => 'SKU-' . strtoupper($faker->unique()->bothify('???-###')),
                'barcode' => $faker->optional()->ean13(),
                'unit' => $unit,
                'price' => $faker->randomFloat(2, 10, 1000),
                'stock' => $faker->randomFloat(2, 0, 500),
                'vat_percent' => $faker->randomFloat(2, 0, 20),
                'is_active' => $faker->boolean(90),
            ]);
        }
    }
}
