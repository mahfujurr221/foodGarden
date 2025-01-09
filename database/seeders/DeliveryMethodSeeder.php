<?php
namespace Database\Seeders;
use App\DeliveryMethod;
use Illuminate\Database\Seeder;

class DeliveryMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DeliveryMethod::create([
            'name' => 'RedX'
        ]);

        DeliveryMethod::create([
            'name' => 'Pathao'
        ]);

        DeliveryMethod::create([
            'name' => 'Paperfly'
        ]);

        DeliveryMethod::create([
            'name' => 'eCourier'
        ]);

        DeliveryMethod::create([
            'name' => 'S.A Paribahan'
        ]);

        DeliveryMethod::create([
            'name' => 'Sundarban'
        ]);

        DeliveryMethod::create([
            'name' => 'GoGo Bangla'
        ]);
    }
}
