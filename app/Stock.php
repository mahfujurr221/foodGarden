<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $guarded = [];

    public function stockable()
    {
        return $this->morphTo();
    }

    public function Purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function purchase_item()
    {
        return $this->belongsTo(PurchaseItem::class);
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }



    // #####--CUSTOM CODE - CALCULATE--######
    /**
     * Remaining After -> Stock Return
     */
    public function remaining()
    {
        $returned = Stock::where('stock_id', $this->id)->sum('qty');
        return $this->qty - $returned;
    }

    public static function boot()
    {
        parent::boot();
        static::saved(function($stock){
            $stock->purchase_item->update_remaining();
        });

        static::updated(function($stock){
            $stock->purchase_item->update_remaining();
        });

        static::deleted(function($stock){
            $stock->purchase_item->update_remaining();
        });
    }



    // public function stock($product_id)
    // {
    //     // Sum of remaining quantities

    //     // Or total in minus total out
    //     // purchase in+return in-sell out-damage out
    //     // ****PRODUCT TABLE CAN BE A GOOD PLACE FOR EACH Summary*****

    //     // return the quantities of the ids where remaining is rea

    //     // Search all the purchase items? and dynamically get the purchase_ids where there still is stock??

    //     // Or Flag in Purchase Item? - to identify stock?

    //     // When stock out change flag??   ||   Or stock can be updated?? which one??
    //     // when returned - Change flag??  ||
    // }

    // PURCHSE ITEM ->

    // return


    // damage

}
