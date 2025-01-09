<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
    
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function purchase_item()
    {
        return $this->belongsTo(PurchaseItem::class, 'purchase_item_id');
    }

    public function sold_quantity()
    {
        return Stock::where('stockable_type', 'App\PosItem')->where('purchase_item_id', $this->id)->sum('qty');
    }

    public function damage_count()
    {
        return Stock::where('stockable_type', 'App\Damage')->where('purchase_item_id', $this->id)->sum('qty');
    }

    public function return_count()
    {
        return Stock::where('stockable_type', 'App\ReturnItem')->where('purchase_item_id', $this->id)->sum('qty');
    }


    public function spent()
    {
        return $this->sold_quantity() + $this->damage_count();
    }

    public function remaining()
    {
        // purchase-Sell+return-damage
        return $this->qty - $this->spent() + $this->return_count();
    }

    public function update_remaining()
    {
        $this->update(['remaining'=>$this->remaining()]);
    }

    public static function boot()
    {
        parent::boot();

        // for created & updated
        static::saved(function($item){
            $item->product->update_calculated_data();
            // $item->purchase->update_calculated_data();
        });

        static::deleted(function($item){
            $item->product->update_calculated_data();
            // $item->purchase->update_calculated_data();
        });
    }
}
