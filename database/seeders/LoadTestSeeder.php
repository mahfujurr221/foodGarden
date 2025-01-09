<?php

namespace Database\Seeders;

use App\Customer;
use App\Pos;
use App\PosItem;
use App\Product;
use App\Purchase;
use App\Services\StockService;
use App\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;


class LoadTestSeeder extends Seeder
{

    public function make_supplier($number)
    {
        $faker = Faker::create();
        for ($i = 0; $i <= $number; $i++) {
            Supplier::create([
                'name' => $faker->name,
                'phone' => rand(1111111111111, 9999999999999),
            ]);

            echo "Supplier Added \n";
        }
    }

    public function make_purchase($number_of_purchase)
    {
        for ($i = 0; $i < $number_of_purchase; $i++) {
            $supplier = Supplier::find(rand(1, 1000));

            $products = [];
            $number_of_products = rand(1, 10);
            $total_payable = 0;

            for ($product_count = 0; $product_count < $number_of_products; $product_count++) {
                $product_id = rand(1, 11);
                $quantity = rand(1, 200);
                $product = Product::find($product_id);

                $unit_price = $product->cost;
                $sub_total = $unit_price * $quantity;

                $total_payable += $sub_total;


                $products[] = [
                    'product_id' => $product_id,
                    'rate' => $unit_price,
                    'qty' => $quantity,
                    'amount' => $sub_total,
                    'remaining' => $quantity
                ];
            }

            DB::transaction(function () use ($supplier, $total_payable, $products) {
                $purchase = Purchase::create([
                    'supplier_id' => $supplier->id,
                    'purchase_date' => Carbon::today()->subDays(rand(0, 365))->format('Y-m-d'),
                    'payable' => $total_payable,
                    'due' => $total_payable,
                    'paid' => 0,
                ]);

                foreach ($products as $product) {
                    $purchase->items()->create($product);
                }
            });
            echo "Purchase Created:$i \n";
        }
    }

    public function add_customer($number)
    {
        $faker = Faker::create();
        for ($i = 0; $i <= $number; $i++) {
            Customer::create([
                'name' => $faker->name,
                'phone' => rand(1111111111111, 9999999999999),
            ]);

            echo "Customer Added \n";
        }
    }


    public function make_sell($number)
    {
        for ($i = 0; $i < $number; $i++) {
            $customer = Customer::find(rand(1, 2000));

            $products = [];
            $number_of_products = rand(1, 20);
            $total_receivable = 0;

            for ($product_count = 0; $product_count < $number_of_products; $product_count++) {
                $product_id = rand(1, 11);
                $quantity = rand(1, 30);
                $product = Product::find($product_id);

                $unit_price = $product->price;
                $sub_total = $unit_price * $quantity;

                $total_receivable += $sub_total;


                $products[] = [
                    'product_name' => $product->name,
                    'product_id' => $product_id,
                    'rate' => $unit_price,
                    'qty' => $quantity,
                    'sub_total' => $sub_total,
                    'remaining' => $quantity
                ];
            }


            DB::transaction(function () use ($customer, $total_receivable, $products) {
                $pos = Pos::create([
                    'customer_id' => $customer->id,
                    'sale_date' => Carbon::today()->subDays(rand(0, 365))->format('Y-m-d'),
                    'total' => $total_receivable,
                    'receivable' => $total_receivable,
                    'due' => $total_receivable,
                ]);


                foreach ($products as $key => $product) {
                    $purchase_distribution = StockService::return_purchase_ids_and_qty_for_the_sell($product['product_id'], $product['qty']);

                    if (isset($purchase_distribution['purchase_items'])) {
                        // Insert POS Item
                        $pos_item = PosItem::create([
                            'pos_id'       => $pos->id,
                            'product_name' => $product['product_name'],
                            'product_id'   => $product['product_id'],
                            'rate'         => $product['rate'],
                            // 'unit_cost'    => $purchase_distribution['average_price'],
                            'total_purchase_cost'    => $purchase_distribution['total_price'],
                            'qty'          => $product['qty'],
                            'sub_total'    => $product['sub_total']
                        ]);



                        foreach ($purchase_distribution['purchase_items'] as $pd_key => $pd_value) {
                            // insert into Stock Table
                            $pos_item->stock()->create([
                                'purchase_id' => $pd_value['purchase_id'],
                                'purchase_item_id' => $pd_value['purchase_item_id'],
                                'product_id' => $product['product_id'],
                                'qty' => $pd_value['qty']
                            ]);
                        }
                    }
                }

                $pos->update_purchase_cost();
            });


            echo "Sell Created:$i \n";
        }
    }

    public function run()
    {
        $this->make_supplier(1000);
        // 20k Purchase
        $this->make_purchase(20000);
        $this->add_customer(2000);
        $this->make_sell(20000);

        // 2 Lacs Sell
    }
}
