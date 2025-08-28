<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // 1️⃣ Real customers
        $list = [
            ['name' => 'Rahim', 'phone' => '01711458777', 'email' =>'rahim@gmail.com', 'address' => 'Dhanmondi'],
            ['name' => 'Monir', 'phone' => '01819874521', 'email' =>'monir@gmail.com', 'address' => 'Mirpur'],
        ];

        foreach ($list as $c) {
            Customer::updateOrCreate(['phone' => $c['phone']], $c);
        }

        // 2️⃣ Fake customers
        for ($i = 0; $i < 10; $i++) { // 10 fake customers
            $name = $faker->name();
            $phone = '01' . $faker->numberBetween(100000000, 999999999); // 11-digit Bangladesh style
            $email = $faker->unique()->safeEmail();
            $address = $faker->address();

            Customer::create([
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
            ]);
        }
    }
}
