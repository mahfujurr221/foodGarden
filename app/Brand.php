<?php

namespace App;

use App\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $guarded = [];
    
    public function logo()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function deleteLogo()
    {
        if($this->logo) {
            File::delete($this->logo->link);
            $this->logo()->delete();
        }
    }
    
    public function products() {
        return $this->hasMany( Product::class );
    }

    public function users()
    {
        return $this->belongsTo(User::class);
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
