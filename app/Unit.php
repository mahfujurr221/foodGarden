<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $guarded=[];

    public function main_unit_products()
    {
        return $this->hasMany(Product::class,'main_unit_id');
    }

    public function sub_unit_products()
    {
        return $this->hasMany(Product::class,'sub_unit_id');
    }


    public function related_unit()
    {
        return $this->belongsTo(Unit::class,'related_to_unit_id');
    }


    public function child_units()
    {
        return $this->hasMany(Unit::class,'related_to_unit_id');
    }


    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($rel) {
            $relationMethods = ['main_unit_products', 'sub_unit_products','child_units'];

            foreach ($relationMethods as $relationMethod) {
                if ($rel->$relationMethod()->count() > 0) {
                    return false;
                }
            }
        });
    }
}
