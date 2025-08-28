<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list = [

        	['name' => 'Rahim', 'phone' => '01711458777','email' =>'rahim@gmail.com', 'address' => 'Dhanmondi'],
        	['name' => 'Monir', 'phone' => '01819874521','email' =>'monir@gmail.com', 'address' => 'Mirpur'],
        ];

        foreach($list as $c) Customer::updateOrCreate(['phone' => $c['phone']], $c);
    }
}
