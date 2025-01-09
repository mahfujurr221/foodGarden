<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DueCollection extends Model
{
    protected $guarded = [];
    use HasFactory;

    //customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // due_by 
    public function dueBy()
    {
        return $this->belongsTo(User::class, 'due_by');
    }

    // payment
    public function payment()
    {
        return $this->belongsTo(ActualPayment::class, 'payment_id');
    }

    //brand
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
