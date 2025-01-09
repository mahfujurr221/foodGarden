<?php

namespace App;

use App\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PosItem extends Model
{
    protected $guarded = [];

    public function pos()
    {
        return $this->belongsTo(Pos::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function stock()
    {
        return $this->morphMany(Stock::class, 'stockable');
    }

    public function return_items()
    {
        return $this->hasMany(ReturnItem::class);
    }

    public function returned()
    {
        return $this->return_items()->sum('qty');
    }

    // Custom Report/Calculations


    // public function profit()
    // {
    //     $sales = $this->saleDateToDate();
    //     $profit = $sales->sum('total_sale') - $sales->sum('total_cost');
    //     return $profit;
    // }



    // After Return
    public function remaining_quantity()
    {
        return $this->qty - $this->returned();
    }


    public function remaining_main_sub()
    {
        if($this->return_items->count()==0){
            return [
                'main_qty'=>$this->main_unit_qty,
                'sub_qty'=>$this->sub_unit_qty,
            ];
        }
        return $this->product->separate_main_sub_qty($this->remaining_quantity());
    }

    function remaining_product_vale() {

        if($this->return_items->count()==0){
            return $this->sub_total;
        }

        $product=$this->product;
        $unit_price=$this->rate;
        $quantity=$product->separate_main_sub_qty($this->remaining_quantity());

        $main_qty=$quantity['main_qty'];
        $sub_qty=$quantity['sub_qty'];

        $sub_unit_price = 0;

        if ($quantity['sub_qty']) {
            $sub_unit_price = $unit_price / $product->main_unit->related_by;
        }

        $main_price = $main_qty * $unit_price;
        $sub_price = $sub_qty * $sub_unit_price;

        return number_format($main_price + $sub_price,2);
    }

    public function update_total_purchase_cost()
    {
        $total = 0;
        foreach ($this->stock as $stock) {
            $purchase_item = PurchaseItem::find($stock->purchase_item_id);
            $unit_price=$purchase_item->rate;
            $quantity=$stock->qty;
            $product=$stock->product;
            $cost=$product->quantity_worth($quantity,$unit_price);
            $total += $cost;
        }

        $this->update([
            'total_purchase_cost' => $total
        ]);
    }


    public function easy_qty()
    {
        $text="";
        if($this->main_unit_qty){
            $text.=$this->main_unit_qty." ".$this->product->main_unit->name." ";
        }

        if($this->sub_unit_qty){
            $text.=$this->sub_unit_qty." ".$this->product->sub_unit->name;
        }

        return $text;
    }


    public static function boot()
    {
        parent::boot();

        static::saved(function ($pos_item) {
            $pos_item->product->update_calculated_data();
            // $pos_item->pos->update_calculated_data();
            // $pos_item->pos->update_purchase_cost();
            // $pos_item->pos->customer->update_calculated_data();
        });

        // static::updated(function($pos_item){
        //     $pos_item->product->update_stock();
        // });

        static::deleted(function ($pos_item) {
            $pos_item->product->update_calculated_data();
            // $pos_item->pos->update_calculated_data();

            // $pos_item->pos->customer->update_calculated_data();
            // $pos_item->pos->update_purchase_cost();
        });

        static::deleting(function ($item) {
            foreach($item->stock as $stock){
                $stock->delete();
            }
        });
    }

    //     // After Return
    // public function remaining_quantity()
    // {
    //     return $this->qty - $this->returned();
    // }


    // public function update_total_purchase_cost()
    // {
    //     $total = 0;
    //     foreach ($this->stock as $stock) {
    //         $purchase_item = PurchaseItem::find($stock->purchase_item_id);

    //         $total += $purchase_item->rate * $stock->quantity;
    //     }

    //     $this->update([
    //         'total_purchase_cost' => $total
    //     ]);
    // }

}
