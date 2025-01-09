<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SR extends Model
{
    protected $guarded = ['id'];

    /**
     * Get all of the customers for the SR
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'sr_id', 'id');
    }

    public function receivable()
    {
        $amount = 0;
        // foreach ($this->customers as $customer) {
        //     $amount += $customer->receivable();
        // }
        $sr_id=$this->id;
        return Pos::whereHas('customer',function($customer)use($sr_id){
            $customer->where('sr_id',$sr_id);
        })->sum('final_receivable');
        return $amount;
    }
    
    public function wallet_balance()
    {
        $amount = 0;
        // foreach ($this->customers as $customer) {
        //     $amount += $customer->wallet_balance();
        // }
        return $this->customers()->sum('wallet_balance');
        return $amount;
    }

    public function paid()
    {
        $amount = 0;
        // foreach ($this->customers as $customer) {
        //     $amount += $customer->paid();
        // }
        $sr_id=$this->id;
        return Pos::whereHas('customer',function($customer)use($sr_id){
            $customer->where('sr_id',$sr_id);
        })->sum('paid');
        return $amount;
    }

    public function due()
    {
        return $this->receivable() - $this->paid();
    }
    
    public function total_due()
    {
        return $this->due() - $this->wallet_balance();
    }

    // Don't delete if any relation is existing
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($telco) {
            $relationMethods = ['customers'];
            foreach ($relationMethods as $relationMethod) {
                if ($telco->$relationMethod()->count() > 0) {
                    return false;
                }
            }
        });
    }
}
