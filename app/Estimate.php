<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function items()
    {
        return $this->hasMany(EstimateItem::class);
    }
    public function damage_items()
    {
        return $this->hasMany(OrderDamageItem::class);
    }
    //product 
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sales_man()
    {
        return $this->belongsTo(User::class, 'estimate_by');
    }
    
    public function deliveryMan()
    {
        return $this->belongsTo(User::class, 'delivery_by','id');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    //order_damage_items
    public function order_damage_items()
    {
        return $this->hasMany(OrderDamageItem::class);
    }
    //order_return_items
    public function order_return_items()
    {
        return $this->hasMany(OrderReturnItem::class);
    }
    public function filter($request, $estimates)
    {
        if ($request->start_date) {
            $estimates = $estimates->whereDate('estimate_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $estimates = $estimates->whereDate('estimate_date', '<=', $request->end_date);
        }

        if ($request->customer) {
            $estimates = $estimates->where('customer_id', $request->customer);
        }

        if ($request->bill_no) {
            $estimates = $estimates->where('id', $request->bill_no);
        }

        if ($request->product_id) {
            $estimates = $estimates->whereHas('items', function ($items) use ($request) {
                $items->where('product_id', $request->product_id);
            });
        }

        return $estimates;
    }
}