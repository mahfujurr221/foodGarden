<?php
namespace Database\Seeders;
use App\Courier;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Courier::create(["name"=>"Pathao"]);
        Courier::create(["name"=>"eCourier"]);
        Courier::create(["name"=>"GoGo Bangla"]);
        Courier::create(["name"=>"PaperFly"]);
        Courier::create(["name"=>"Sundarban"]);
        Courier::create(["name"=>"SA Paribahan"]);
    }
}
