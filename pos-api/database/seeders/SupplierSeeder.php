<?php

namespace Database\Seeders;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
  
    public function run()
    {
         $rows = [
            ['name'=>'Agro Foods Ltd','phone'=>'01711000001','email'=>'agro@example.com','company'=>'Agro Foods','address'=>'Farmgate, Dhaka','opening_balance'=>0,'is_active'=>1],
            ['name'=>'Fresh Dairy','phone'=>'01711000002','email'=>'dairy@example.com','company'=>'Fresh Dairy Co','address'=>'Uttara, Dhaka','opening_balance'=>0,'is_active'=>1],
            ['name'=>'Spice World','phone'=>'01711000003','email'=>'spice@example.com','company'=>'Spice World','address'=>'Chawkbazar, Dhaka','opening_balance'=>0,'is_active'=>1],
            ['name'=>'Daily Essentials','phone'=>'01711000004','email'=>null,'company'=>null,'address'=>'Mirpur, Dhaka','opening_balance'=>5000,'is_active'=>1],
            ['name'=>'Ocean Fishers','phone'=>null,'email'=>null,'company'=>null,'address'=>'Karnaphuli, CTG','opening_balance'=>0,'is_active'=>1],
        ];
        foreach ($rows as $r) Supplier::create($r);
    }
}
