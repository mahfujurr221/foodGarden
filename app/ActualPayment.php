<?php

namespace App;

use App\Services\TransactionService;
use Illuminate\Database\Eloquent\Model;

class ActualPayment extends Model
{
    protected $guarded = [];
    protected static $relations_to_cascade = ['payments'];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }



    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // //due collection
    public function collectBy()
    {
        return $this->belongsTo(User::class, 'collect_by');
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'transactable');
    }

    //due collection
    public function dueCollection()
    {
        return $this->belongsTo(DueCollection::class, 'payment_id');
    }

    public function update_calculated_data() {
        if($this->payments()->first()){
            $due = 0;
            if ($this->customer_id&&$this->customer_id>0) {
                $due = $this->customer->total_due();
            } elseif ($this->supplier_id) {
                $due = $this->supplier->total_due();
            }
    
            $this->update([
                'due' => $due,
                'amount'=>$this->payments()->sum('pay_amount')
            ]);
        }else{
            $this->delete();
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function($actual_payment){
            $previous_due=0;
            if ($actual_payment->customer_id&&$actual_payment->customer_id>0) {
                $previous_due = $actual_payment->customer->total_due();
            } elseif ($actual_payment->supplier_id) {
                $previous_due = $actual_payment->supplier->total_due();
            }
            $actual_payment->previous_due=$previous_due;
        });

        static::created(function($actual_payment){
            // info($actual_payment);
            TransactionService::create_payment_transaction($actual_payment->id);
        });

        static::deleting(function ($actual_payment) {
            foreach (static::$relations_to_cascade as $relation) {
                foreach ($actual_payment->{$relation}()->get() as $item) {
                    $item->delete();
                }
            }
            if($actual_payment->transaction){
                $actual_payment->transaction->delete();
            }
        });

        // static::restoring(function ($resource) {
        //     foreach (static::$relations_to_cascade as $relation) {
        //         foreach ($resource->{$relation}()->get() as $item) {
        //             $item->withTrashed()->restore();
        //         }
        //     }
        // });
    }

}
