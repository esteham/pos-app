<?php

namespace Database\Seeders;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
   
    public function run(): void
    {
       	foreach(['Grocery','Dairy','Beverages','Personal Care'] as $name)
       	{
       		Category::updateOrCreate(

       			['name' => $name],
       			['slug' => Str::slug($name)]
       		);
       	}
    }
}
