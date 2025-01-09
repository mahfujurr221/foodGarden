<?php
namespace Database\Seeders;
use App\Product;
use App\Services\ProductService;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product = Product::create([
            // 'type' => 'standard',
            'name' => 'Mobile Phone',
            'code' => '000001',
            'category_id' => random_int(1, 5),
            'brand_id' => random_int(1, 5),
            'cost' => 4150,
            'price' => 4500,
            'image' => 'dashboard/images/not-available.png',
            'main_unit_id'=>1
        ]);
        $data= (object)[
            'opening_stock'=>[100],
        ];
        ProductService::make_opening_stock_purchase($data,$product);

        
    }
}
