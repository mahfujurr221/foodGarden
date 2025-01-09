<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $guarded = [];

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    // CUSTOM FUNBCTIONS
    public function balance()
    {
        // Opening Balance+SUM OF ALL PAYMENT RECEIVED(SELL)
        $opening_balance = $this->opening_balance;

        $all_received = Payment::where('bank_account_id', $this->id)
            ->where('payment_type', 'receive')
            ->where('wallet_payment',0)
            ->sum('pay_amount');

        // dd($all_received);
        $transfer_received = AccountToAccountTransection::where('to', $this->id)->sum('amount');


        $total_added = $opening_balance + $all_received + $transfer_received;

        // ALL MONEY SPENT+LC PAYMENT
        $all_spent = Payment::where('bank_account_id', $this->id)
            ->where('payment_type', 'pay')
            ->where('wallet_payment',0)
            ->sum('pay_amount');



        $normal_expense = Expense::where('bank_account_id', $this->id)
            ->sum('amount');

        $transfered = AccountToAccountTransection::where('from', $this->id)->sum('amount');

        // ----------------------
        $total_spent = $all_spent + $normal_expense + $transfered;

        // dd($total_spent);
        return $total_added - $total_spent;
    }
}
