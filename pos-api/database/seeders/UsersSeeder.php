<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@mail.com',
                'name'  => 'Super Admin',
                'phone' => '01723456785',
                'user_type' => 'admin',
                'password'  => Hash::makes('admin123')
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'manager@mail.com',
                'name'  => 'Store Manager',
                'phone' => '01765786865',
                'user_type' => 'manager',
                'password'  => Hash::makes('manager123')
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'cashire@mail.com',
                'name'  => 'Cashire01',
                'phone' => '017094748593',
                'user_type' => 'cashier',
                'password'  => Hash::makes('cashire123')
            ]
        );
    }
}
