<?php
namespace Database\Seeders;
use App\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentMethod::create(['name'=>'Hand Cash']);
        PaymentMethod::create(['name'=>'Bank']);
        PaymentMethod::create(['name'=>'Rocket']);
        PaymentMethod::create(['name'=>'Bkash']);
        PaymentMethod::create(['name'=>'Cash On Delivery']);
    }
}
