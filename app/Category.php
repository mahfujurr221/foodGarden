<?php

namespace App;

use App\Image;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [ 'name', 'code' ];

    public function image() {
        return $this->morphOne( Image::class, 'imageable' );
    }

    public function products() {
        return $this->hasMany( Product::class );
    }

    public function products_count() {
        $products = $this->hasMany( Product::class );
        return $products->count();
    }

    // Don't delete if any relation is existing
    protected static function boot() {
        parent::boot();
        static::deleting( function ( $telco ) {
            $relationMethods = [ 'products' ];
            foreach ( $relationMethods as $relationMethod ) {
                if ( $telco->$relationMethod()->count() > 0 ) {
                    return false;
                }
            }
        } );
    }

}
