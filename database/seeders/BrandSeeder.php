<?php
namespace Database\Seeders;
use App\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Brand::create([
            'name' => 'Apple',
            'slug' => str_slug('Apple'),
            'description' => 'Apple Brand Description'
        ]);
    }
}
