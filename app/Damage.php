<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Damage extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(DamageItem::class);
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
        // for created & updated
        // static::saved(function($item){
        //     $item->product->update_stock();
        // });
        // static::deleted(function($item){
        //     $item->product->update_stock();
        // });
        static::deleting(function ($damage) {
            // dd($item);
            foreach($damage->stock as $stock){
                $stock->delete();
            }
        });
    }
}
