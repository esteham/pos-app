<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(['Groceery', 'Dairy', 'Beverages', 'Personal Care'] as $name)
        {
            Category::updateOrCreate(

                ['name' => $name],
                ['slug' => Str::slug($name)]
            
            );
        }
    }
}
