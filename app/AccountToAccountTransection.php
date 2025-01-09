<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountToAccountTransection extends Model
{
    protected $guarded = [];

    public function from_account()
    {
        return $this->belongsTo(BankAccount::class, 'from');
    }

    public function to_account()
    {
        return $this->belongsTo(BankAccount::class, 'to');
    }
}
