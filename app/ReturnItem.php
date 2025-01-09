<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $guarded = [];

    public function return()
    {
        return $this->belongsTo(OrderReturn::class,'order_return_id');
    }

    public function stock()
    {
        return $this->morphMany(Stock::class, 'stockable');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function boot()
    {
        parent::boot();

        // Created & Updated
        static::saved(function($item){
            $item->product->update_stock();
            // $item->return->pos->update_calculated_data();
        });

        static::deleted(function($item){
            $item->product->update_stock();
        });

        static::deleting(function ($item) {
            // dd($item);
            $item->stock()->delete();
        });
    }
}
