<?php

namespace App;

use App\Payment;
use App\Services\TransactionService;
use App\Supplier;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $guarded = [];


    /**
     * Relations
     */
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'transactable');
    }

    /**
     * CUSTOM/HELPER
     */

    // public function totalPay()
    // {
    //     $purchases = Purchase::all();
    //     $totalPay  = 0;
    //     foreach ($purchases as $purchase) {
    //         $totalPay += $purchase->payments->sum('pay_amount');
    //     }

    //     return $totalPay;
    // }

    // public function totalPurchases()
    // {
    //     return Purchase::get(['id', 'payable']);
    // }

    // public function totalDue()
    // {
    //     return $this->totalPurchases()->sum('payable') - $this->totalPay();
    // }

    // public function due()
    // {
    //     $due = $this->payable - $this->payments->sum('pay_amount');
    //     if ($due <= 0) {
    //         return 0;
    //     } else {
    //         return $due;
    //     }
    // }

    // public function paid()
    // {
    //     return $this->payments->sum('pay_amount');
    // }


    public function filter($request, $purchases)
    {
        if ($request->start_date)
        {
            $purchases = $purchases->where('purchase_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $purchases = $purchases->where('purchase_date', '<=' ,$request->end_date);
        }

        if ($request->supplier) {
            $purchases = $purchases->where('supplier_id', $request->supplier);
        }

        if ($request->bill_no) {
            $purchases = $purchases->where('id', $request->bill_no);
        }

        if($request->product_id){
            $purchases = $purchases->whereHas('items',function($items)use($request){
                $items->where('product_id',$request->product_id);
            });
        }

        return $purchases;
    }



    public function update_calculated_data()
    {
        $this->update([
            'paid' => $this->payments()->sum('pay_amount'),
            'due' => $this->payable - $this->payments()->sum('pay_amount')
        ]);

        $this->supplier->update_calculated_data();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function($purchase){
            TransactionService::create_purchase_transaction($purchase);
        });

        // move to controller -> Because it is being called when payment is made -> as that makes purchase to update again
        // static::updated(function ($purchase) {
        //     TransactionService::update_purchase_transaction($purchase);
        // });

        static::deleting(function ($rel) {
            $relationMethods = ['stocks'];

            foreach ($relationMethods as $relationMethod) {
                if ($rel->$relationMethod()->count() > 0) {
                    return false;
                }
            }

            // If here -> Deletion Possible
        });

        static::deleted(function($purchase){
            foreach($purchase->items as $item){
                $item->delete();
            }

            foreach ($purchase->payments as $payment) {
                $payment->delete();
            }

            $purchase->transaction->delete();
            $purchase->supplier->update_calculated_data();
        });
    }

}

