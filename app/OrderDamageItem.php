<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDamageItem extends Model
{
    use HasFactory;
    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }
    //product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}