<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ActualPayment;

class Payment extends Model
{
    protected $guarded = [];

    /**
     * Relations
     */
    public function paymentable()
    {
        return $this->morphTo();
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'method');
    }

    public function pos()
    {
        return $this->belongsTo(Pos::class, 'paymentable_id', 'id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'paymentable_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'paymentable_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'paymentable_id', 'id');
    }

    public function actual_payment()
    {
        return $this->belongsTo(ActualPayment::class, 'actual_payment_id', 'id');
    }

    
    public function account()
    {
        return $this->belongsTo(BankAccount::class,'bank_account_id');
    }



    protected static function boot(){
        parent::boot();

        static::saved(function($payment){
            $payment->paymentable->update_calculated_data();
            if($payment->paymentable_type==Purchase::class){
                $payment->paymentable->supplier->update_calculated_data();
            }

            if($payment->paymentable_type==Pos::class){
                // info('Pos Payment Created');
                // $payment->paymentable->update_calculated_data();
                $payment->paymentable->customer?$payment->paymentable->customer->update_calculated_data():null;
            }

            $payment->actual_payment->update_calculated_data();
        });

        static::deleted(function($payment){
            $payment->actual_payment->update_calculated_data();

            if($payment->paymentable){
                $payment->paymentable->update_calculated_data();
            }
            if($payment->paymentable&&$payment->paymentable_type==Purchase::class){
                $payment->paymentable->supplier->update_calculated_data();

            }

            if($payment->paymentable&&$payment->paymentable_type==Pos::class){
                // $payment->paymentable->update_calculated_data();
                $payment->paymentable->customer?$payment->paymentable->customer->update_calculated_data():null;
            }

            // $payment->transaction->delete();
        });
    }
}
