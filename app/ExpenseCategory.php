<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $guarded = [];

    public function expenses() {
        return $this->hasMany( Expense::class, 'category_id', 'id' );
    }
    
    // Don't delete if any relation is existing
    protected static function boot() {
        parent::boot();
        static::deleting( function ( $telco ) {
            $relationMethods = [ 'expenses' ];
            foreach ( $relationMethods as $relationMethod ) {
                if ( $telco->$relationMethod()->count() > 0 ) {
                    return false;
                }
            }
        } );
    }
}
