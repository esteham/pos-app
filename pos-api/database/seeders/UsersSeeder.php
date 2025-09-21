<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
 
    public function run(): void
    {
       User::updateOrCreate(

       		['email' => 'admin@pos.local'],
       		['name' => 'Super Admin', 'phone' => '01711455874', 'user_type' => 'admin', 'password' => Hash::make('admin123')]
       );

       User::updateOrCreate(

       		['email' => 'manager@pos.local'],
       		['name' => 'Store Manager', 'phone' => '01819845874', 'user_type' => 'manager', 'password' => Hash::make('manager123')]
       );

       User::updateOrCreate(

       		['email' => 'cashier@pos.local'],
       		['name' => 'Cashier One', 'phone' => '01911452587', 'user_type' => 'cashier', 'password' => Hash::make('cashier123')]
       );
    }
}
