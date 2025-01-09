<?php

namespace App;

use App\Payment;
use App\Pos;
use App\ActualPayment;
use App\Address;
use App\BusignessCategory;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    /****************************
     * RELATIONS
     ***************************/
    public function actual_payments()
    {
        return $this->hasMany(ActualPayment::class);
    }

    //join with address table
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    //join with businessCategory table
    public function business_category()
    {
        return $this->belongsTo(BusignessCategory::class,'business_cat_id');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function returns()
    {
        return $this->hasMany(OrderReturn::class,'customer_id');
    }

    public function sales()
    {
        return $this->hasMany(Pos::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }


    public function opening_transactions()
    {
        return $this->morphMany(Transaction::class, 'transactable');
    }

    //due collection
    public function due_collections()
    {
        return $this->hasMany(DueCollection::class, 'customer_id');
    }
    
    /****************************
     * HELPER METHODS
     ***************************/
    public function due_invoice_count()
    {
        return $this->sales()->where('due','>',0)->count();
    }

    public function sell_due()
    {
        return $this->sales()->sum('due');
    }

    //last due collection date
    // public function dueCollection()
    // {
    //     $data=$this->due_collections()->orderBy('id','desc')->first();
    //     if($data!=null){
    //         $last_paid_date=$data->last_due_date;
    //         $committed_due_date=$data->committed_due_date;
    //         $due_by=$data->dueBy->fname.' '.$data->dueBy->lname;
    //         return [
    //             'last_paid_date'=>$last_paid_date,
    //             'committed_due_date'=>$committed_due_date,
    //             'due_by'=>$due_by
    //         ];
    //     }
    //     return null;
    // }

    public function direct_received()
    {
        return $this->payments()->where('payment_type', 'receive')->sum('pay_amount');
            // ->get()->sum(function ($payment) {
            //     return $payment->pay_amount + $payment->discount;
            // });
    }

    public function direct_paid()
    {
        return $this->payments()->where('payment_type', 'pay')->sum('pay_amount');
            // ->get()->sum(function ($payment) {
            //     return $payment->pay_amount + $payment->discount;
            // });
    }

    public function received_from_wallet()
    {
        // return 0;
        return $this->actual_payments()
            ->where('payment_type', 'receive')
            ->where('wallet_payment', 1)
            ->sum('amount');
    }

    public function wallet_balance()
    {
        $amount = 0;
        // return  4000;
        // return $this->received_from_wallet();
        if ($this->opening_receivable != null && $this->opening_receivable != 0) {
            $amount -= abs($this->opening_receivable);
        }
        if ($this->opening_payable != null && $this->opening_payable != 0) {
            $amount += abs($this->opening_payable);
        }

        $amount += $this->direct_received() - $this->direct_paid() - $this->received_from_wallet();
        return $amount;
        return $amount > 0 ? $amount : 0;
    }

    public function receivable($start_date = null, $end_date = null,$brand_id=null)
    {
        // dd($brand_id);
        $amount = 0;
        //if ($this->opening_balance > 0) {
        //  $amount = $this->opening_balance;
        //}

        if ($start_date != null && $end_date != null) {
            $amount += $this->sales()->where('sale_date', '>=', $start_date)->where('sale_date', '<=', $end_date)->sum('final_receivable');
        } 
        if($brand_id!=null){
            $amount += $this->sales()->where('brand_id',$brand_id)->sum('final_receivable');
        }
        else {
            $amount += $this->sales->sum('final_receivable');
        }
        return $amount;
    }

    public function paid_as_return()
    {
        return $this->returns()->sum('return_product_value');
    }

    public function paid()
    {
        // $sales = $this->sales;
        $totalPaid = 0;

        // if ($this->opening_balance < 0) {
        //     $totalPaid = abs($this->opening_balance);
        // }

        // foreach ($sales as $sale) {
        //     $totalPaid += $sale->payments->sum('pay_amount');
        // }
        $totalPaid+=$this->sales()->sum('paid');

        // $direct_customer_payment =
        // $paid_as_return = $this->paid_as_return();

        return $totalPaid;
        // + $paid_as_return;
    }

    public function due()//sale_due
    {
        return $this->receivable() - $this->paid();
    }

	public function total_due()
    {
        // $this->due()+$this-
        // Invoice Due + direct_receivable
        $direct_receivable = 0;
        if ($this->wallet_balance() < 0) {
            $direct_receivable = abs($this->wallet_balance());
        }

        $invoice_due = $this->due();

        return $invoice_due + $direct_receivable;
    }



    public function update_calculated_data()
    {
        // update wallet balance
        // $wallet_balance = $this->wallet_balance();
        $wallet_balance=$this->wallet_balance();
        // Update Customer Receivable
        // 1. Consider wallet balance + total due
        $sale_due = $this->sales()->sum('due');

        if ($wallet_balance < 0) {
            $this->update([
                'wallet_balance'=>$wallet_balance,
                'total_receivable'=>$sale_due+abs($wallet_balance),
                'total_payable'=>0
            ]);
        }else{
            $this->update([
                'wallet_balance'=>$wallet_balance,
                'total_receivable'=>$sale_due,
                'total_payable'=>abs($wallet_balance)
            ]);
        }
    }

    // Don't delete if any relation is existing
    protected static function boot()
    {
        parent::boot();

        static::created(function($customer){
            $customer->update_calculated_data();
            TransactionService::add_customer_opening_balance($customer);
        });

        static::deleting(function ($rel) {
            $relationMethods = ['actual_payments', 'sales'];

            foreach ($relationMethods as $relationMethod) {
                if ($rel->$relationMethod()->count() > 0) {
                    return false;
                }
            }
        });

        static::deleted(function($customer){
            foreach($customer->opening_transactions as $transaction){
                $transaction->delete();
            }
        });
    }
}
