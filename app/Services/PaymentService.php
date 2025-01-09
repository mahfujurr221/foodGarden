<?php

namespace App\Services;
use App\ActualPayment;
use App\Customer;
use App\Supplier;

class PaymentService
{
    // customer Payment
    public static function add_customer_payment($request)
    {
        $customer      = Customer::findOrFail($request->account_id);
        $requestAmount = $request->amount;

        //actual payment insert
        $actual_payment              = new ActualPayment();
        $actual_payment->customer_id = $request->account_id;
        $actual_payment->amount      = $request->amount;
        $actual_payment->date        = $request->payment_date;
        $actual_payment->payment_type = $request->payment_type;
        $actual_payment->note = $request->note;
        $actual_payment->save();
        if ($request->direct_transection == 1) {
            $customer->payments()->create([
                'actual_payment_id' => $actual_payment->id,
                'bank_account_id'   => $request->bank_account_id,
                'payment_date' => $request->payment_date,
                'payment_type' => $request->payment_type,
                'pay_amount' => $requestAmount,
                'method' => $request->method,
            ]);
        } else {
            if ($request->payment_type == 'receive') {
                $due_pos_list  = $customer->sales()->where('due', '>', 0)->get();

                $tempAmount = $requestAmount;

                if ($due_pos_list->count() > 0) {
                    foreach ($due_pos_list as $due_pos) {
                        $due_amount = $due_pos->due;

                        if ($tempAmount >= 0 && $due_amount <= $tempAmount) {
                            $tempAmount = $tempAmount - $due_amount;
                            // Due Amount full paid
                            $due_pos->payments()->create([
                                'actual_payment_id' => $actual_payment->id,
                                'bank_account_id'   => $request->bank_account_id,
                                'payment_date'      => $request->payment_date,
                                'payment_type'      => $request->payment_type,
                                'pay_amount'        => $due_amount,
                                'method'            => $request->method,
                            ]);
                        } else {
                            // Due amount in pay extra amount
                            $due_pos->payments()->create([
                                // 'transaction_id' => $request->transaction_id,
                                'actual_payment_id' => $actual_payment->id,
                                'bank_account_id'   => $request->bank_account_id,
                                'payment_date'      => $request->payment_date,
                                'payment_type'      => $request->payment_type,
                                'pay_amount'        => $tempAmount,
                                'method'            => $request->method,
                            ]);
                            $tempAmount = 0;
                            break;
                        }
                    }

                    if ($tempAmount > 0) {
                        $customer->payments()->create([
                            'actual_payment_id' => $actual_payment->id,
                            'bank_account_id'   => $request->bank_account_id,
                            'payment_date' => $request->payment_date,
                            'payment_type' => $request->payment_type,
                            'pay_amount' => $tempAmount,
                            'method' => $request->method,
                        ]);
                    }
                } else {
                    $customer->payments()->create([
                        'actual_payment_id' => $actual_payment->id,
                        'bank_account_id'   => $request->bank_account_id,
                        'payment_date' => $request->payment_date,
                        'payment_type' => $request->payment_type,
                        'pay_amount' => $tempAmount,
                        'method' => $request->method,
                    ]);
                }
            } else {
                // pay customer
                $customer->payments()->create([
                    'actual_payment_id' => $actual_payment->id,
                    'bank_account_id'   => $request->bank_account_id,
                    'payment_date' => $request->payment_date,
                    'payment_type' => $request->payment_type,
                    'pay_amount' => $requestAmount,
                    'method' => $request->method,
                ]);
            }
        }

        return $actual_payment;
    }

    public static function add_supplier_payment($request)
    {
        $supplier           = Supplier::findOrFail($request->account_id);
        $requestAmount      = $request->amount;

        // Actual Payment insert
        $actual_payment              = new ActualPayment();
        $actual_payment->supplier_id = $request->account_id;
        $actual_payment->amount      = $request->amount;
        $actual_payment->date        = $request->payment_date;
        $actual_payment->payment_type = $request->payment_type;
        $actual_payment->note = $request->note;
        $actual_payment->save();

        // dd($due_purchases_list);
        if ($request->direct_transection == 1) {
            $supplier->payments()->create([
                'actual_payment_id' => $actual_payment->id,
                'bank_account_id'   => $request->bank_account_id,
                'payment_date' => $request->payment_date,
                'payment_type' => $request->payment_type,
                'pay_amount' => $requestAmount,
                'method' => $request->method,
            ]);
        } else {
            if ($request->payment_type == 'pay') {
                $due_purchases_list = $supplier->purchases()->where('due', '>', 0)->get();

                $tempAmount = $requestAmount;

                if ($due_purchases_list->count() > 0) {
                    foreach ($due_purchases_list as $due_purchase) {
                        $due_amount = $due_purchase->due;

                        if ($tempAmount >= 0 && $due_amount <= $tempAmount) {
                            $tempAmount = $tempAmount - $due_amount;
                            // Due Amount full paid
                            $due_purchase->payments()->create([
                                // 'transaction_id' => $request->transaction_id,
                                'actual_payment_id' => $actual_payment->id,
                                'bank_account_id'   => $request->bank_account_id,
                                'payment_date'      => $request->payment_date,
                                'payment_type'      => 'pay',
                                'pay_amount'        => $due_amount,
                                'method'            => $request->method,
                            ]);
                        } else {
                            // Due amount in pay extra amount
                            $due_purchase->payments()->create([
                                // 'transaction_id' => $request->transaction_id,
                                'actual_payment_id' => $actual_payment->id,
                                'bank_account_id'   => $request->bank_account_id,
                                'payment_date'      => $request->payment_date,
                                'payment_type'      => 'pay',
                                'pay_amount'        => $tempAmount,
                                'method'            => $request->method,
                            ]);
                            $tempAmount = 0;
                            break;
                        }
                    }
                    if ($tempAmount > 0) {
                        $supplier->payments()->create([
                            'actual_payment_id' => $actual_payment->id,
                            'bank_account_id'   => $request->bank_account_id,
                            'payment_date' => $request->payment_date,
                            'payment_type' => 'pay',
                            'pay_amount' => $tempAmount,
                            'method' => $request->method,
                        ]);
                    }
                } else {
                    $supplier->payments()->create([
                        'actual_payment_id' => $actual_payment->id,
                        'bank_account_id'   => $request->bank_account_id,
                        'payment_date' => $request->payment_date,
                        'payment_type' => 'pay',
                        'pay_amount' => $tempAmount,
                        'method' => $request->method,
                    ]);
                }
            } else {
                // Receive from supplier
                $supplier->payments()->create([
                    'actual_payment_id' => $actual_payment->id,
                    'bank_account_id'   => $request->bank_account_id,
                    'payment_date' => $request->payment_date,
                    'payment_type' => $request->payment_type,
                    'pay_amount' => $requestAmount,
                    'method' => $request->method,
                ]);
            }
        }

        return $actual_payment;
    }

}
