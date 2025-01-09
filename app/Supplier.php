<?php

namespace App;

use App\Payment;
use App\Purchase;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];

    /**
     * Relations
     */

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function actual_payments()
    {
        return $this->hasMany(ActualPayment::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function opening_transactions()
    {
        return $this->morphMany(Transaction::class, 'transactable');
    }

    // private function date($month = null)
    // {
    //     $date = Carbon::now();
    //     if ($month != null) {
    //         $date = Carbon::createFromFormat('Y-m', $month);
    //     }
    //     return $date;
    // }



    // **** Custom ****

    // purchase payable
    public function due_purchase_count()
    {
        return $this->purchases()->where('due', '>', 0)->count();
    }

    public function purchase_due()
    {
        return $this->purchases()->sum('due');
    }

    public function direct_paid()
    {
        return $this->payments()->where('payment_type', 'pay')
            ->get()->sum(function ($payment) {
            return $payment->pay_amount + $payment->discount;
        });
    }

    public function direct_received()
    {
        return $this->payments()->where('payment_type', 'receive')
            ->get()->sum(function ($payment) {
            return $payment->pay_amount + $payment->discount;
        });
    }

    public function paid_from_wallet()
    {
        return $this->actual_payments()
            ->where('payment_type', 'pay')
            ->where('wallet_payment', 1)
            ->sum('amount');
    }

    public function wallet_balance()
    {
        $amount=0;
        if ($this->opening_receivable != null && $this->opening_receivable != 0) {
            $amount += abs($this->opening_receivable);
        }
        if ($this->opening_payable != null && $this->opening_payable != 0) {
            $amount -= abs($this->opening_payable);
        }

        $amount += $this->direct_paid() - $this->direct_received() - $this->paid_from_wallet();
        return $amount;
    }


    public function payable()
    {
        return $this->purchases()->sum('payable');
    }

    public function paid()
    {
        return $this->purchases()->sum('paid');
    }

    public function due()
    {
        return $this->payable() - $this->paid();
    }

    public function total_due()
    {
        // $this->due()+$this-
        // Invoice Due + direct_payable
        $direct_payable = 0;
        if ($this->wallet_balance() < 0) {
            $direct_payable = abs($this->wallet_balance());
        }

        $invoice_due = $this->due();

        return $invoice_due + $direct_payable;
    }

    public function update_calculated_data()
    {
        // update wallet balance
        // $wallet_balance = $this->wallet_balance();
        $wallet_balance=$this->wallet_balance();
        // Update Customer Receivable
        // 1. Consider wallet balance + total due
        $purchase_due = $this->purchases()->sum('due');

        if ($wallet_balance < 0) {
            $this->update([
                'wallet_balance'=>$wallet_balance,
                'total_receivable'=>0,
                'total_payable'=>$purchase_due+abs($wallet_balance)
            ]);
        }else{
            $this->update([
                'wallet_balance'=>$wallet_balance,
                'total_receivable'=>abs($wallet_balance),
                'total_payable'=>$purchase_due
            ]);
        }
    }

    public function has_default()
    {
        return Supplier::where('default', 1)->first();
    }

    public function get_default()
    {
        $default_supplier=$this->has_default();
        if (!$default_supplier) {
            // dd("NO DEFAULT");
            $default_supplier = Supplier::create([
                'name' => 'Default Supplier',
                'email' => 'default@supplier.com',
                'phone' => '111111',
                'address' => 'Default Address',
                'default' => '1'
            ]);
        }

        return $default_supplier;
    }

    // Don't delete if any relation is existing
    protected static function boot()
    {
        parent::boot();

        static::created(function($supplier){
            $supplier->update_calculated_data();
            TransactionService::add_supplier_opening_balance($supplier);
        });

        static::deleting(function ($rel) {
            $relationMethods = ['actual_payments', 'purchases'];

            foreach ($relationMethods as $relationMethod) {
                if ($rel->$relationMethod()->count() > 0) {
                    return false;
                }
            }
        });

        static::deleted(function($supplier){
            foreach($supplier->opening_transactions as $transaction){
                $transaction->delete();
            }
        });
    }
}
