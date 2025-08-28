<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $faker = Faker::create();

        foreach(['Grocery', 'Dairy', 'Beverages', 'Personal Care'] as $name)
        {
            Category::updateOrCreate(

                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'description' => $faker->sentence(10) //random descripton
                ]
            
            );
        }
    }
}
