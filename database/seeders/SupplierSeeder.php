<?php
namespace Database\Seeders;
use App\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config('pos.app_mode') == 'demo') {
            Supplier::create([
                'name' => 'Ripath Hasan',
                'email' => 'ripat@gmail.com',
                'phone' => '0124578778',
                'address' => 'Address'
            ]);
            Supplier::create([
                'name' => 'Sakib Al Hasan',
                'email' => 'sakib@gmail.com',
                'phone' => '014578785',
                'address' => 'Address'
            ]);
            Supplier::create([
                'name' => 'Md Hossain',
                'email' => 'hossain@gmail.com',
                'phone' => '014787845',
                'address' => 'Address'
            ]);
            Supplier::create([
                'name' => 'Md Sahadat',
                'email' => 'sahadat@gmail.com',
                'phone' => '45784545',
                'address' => 'Address'
            ]);
            Supplier::create([
                'name' => 'Aftab Kahn',
                'email' => 'aftab@gmail.com',
                'phone' => '01457878',
                'address' => 'Address'
            ]);
            Supplier::create([
                'name' => 'Monsur Ali',
                'email' => 'monsur@gmail.com',
                'phone' => '4548784154',
                'address' => 'Address'
            ]);
        } else {
            Supplier::create([
                'name' => 'Default Supplier',
                'email' => 'default@supplier.com',
                'phone' => '111111',
                'address' => 'Default Address',
                'default' => '1'
            ]);
        }
    }
}
