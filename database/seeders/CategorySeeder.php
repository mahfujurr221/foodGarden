<?php
namespace Database\Seeders;
use App\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'name' => 'Electronics',
            // 'code' => '45478'
        ]);
    }
}
