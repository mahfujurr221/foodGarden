<?php

namespace App;

use App\Services\TransactionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded=[];

    // use HasFactory;
    public function transactable()
    {
        return $this->morphTo();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public static function boot()
    {
        parent::boot();

        static::deleted(function($transaction){
            if($transaction->customer_id!=null){
                TransactionService::calculate_customer_balance($transaction);
            }

            if($transaction->supplier_id){
                TransactionService::calculate_supplier_balance($transaction);
            }

        });
    }
}
