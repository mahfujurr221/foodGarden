<?php

namespace App\Services;

use App\ActualPayment;
use App\Transaction;

class TransactionService
{

    public static function calculate_customer_balance(Transaction $from_transaction)
    {
        $last_transaction = Transaction::where('id', '<', $from_transaction->id)
            ->where('customer_id', $from_transaction->customer_id)->orderBy('id', 'desc')->first();

        $transactions = Transaction::where('id', '>', $last_transaction->id ?? 0)
            ->where('customer_id', $from_transaction->customer_id)->orderBy('id')->get();
        foreach ($transactions as $transaction) {
            $balance = $last_transaction->balance ?? 0;
            if ($transaction->debit) {
                if ($transaction->transactable_type != ActualPayment::class || !ActualPayment::find($transaction->transactable_id)->wallet_payment) {
                    $balance = $balance + $transaction->debit;
                }
                $transaction->update([
                    'balance' => $balance
                ]);
            } elseif ($transaction->credit) {
                if ($transaction->transactable_type != ActualPayment::class || !ActualPayment::find($transaction->transactable_id)->wallet_payment) {
                    $balance = $balance - $transaction->credit;
                }
                $transaction->update([
                    'balance' => $balance
                ]);
            }
            $last_transaction = $transaction;
        }
    }

    public static function calculate_supplier_balance(Transaction $from_transaction)
    {
        $last_transaction = Transaction::where('id', '<', $from_transaction->id)
            ->where('supplier_id', $from_transaction->supplier_id)->orderBy('id', 'desc')->first();

        $transactions = Transaction::where('id', '>', $last_transaction->id ?? 0)->where('supplier_id', $from_transaction->supplier_id)->orderBy('id')->get();
        foreach ($transactions as $transaction) {
            $balance = $last_transaction->balance ?? 0;
            if ($transaction->debit) {
                if ($transaction->transactable_type != ActualPayment::class || !ActualPayment::find($transaction->transactable_id)->wallet_payment) {
                    $balance = $balance + $transaction->debit;
                }
                $transaction->update([
                    'balance' => $balance
                ]);
            } elseif ($transaction->credit) {
                if ($transaction->transactable_type != ActualPayment::class || !ActualPayment::find($transaction->transactable_id)->wallet_payment) {
                    $balance = $balance - $transaction->credit;
                }
                $transaction->update([
                    'balance' => $balance
                ]);
            }
            $last_transaction = $transaction;
        }
    }
    /**
     * POS Transactions
     */
    public static function create_pos_transaction($pos)
    {
        $balance = 0;

        $customer = $pos->customer;
        if ($customer) {
            $last_transaction = $customer->transactions()->orderBy('id', 'desc')->first();

            if ($last_transaction) {
                $balance = $last_transaction->balance;
            }
        }
        $pos->transaction()->create([
            'date' => $pos->sale_date,
            'debit' => $pos->receivable,
            'particulars' => 'Sale #' . $pos->id,
            'customer_id' => $pos->customer_id,
            'balance' => $balance + $pos->receivable
        ]);
    }

    public static function update_pos_transaction($pos)
    {
        if ($pos->transaction) {
            $balance = 0;
            $customer = $pos->customer;
            if ($customer) {
                $last_transaction = $customer->transactions()->where('id', '!=', $pos->transaction->id ?? null)->where('id', '<', $pos->transaction->id ?? null)->orderBy('id', 'desc')->first();
                if ($last_transaction) {
                    $balance = $last_transaction->balance;
                }
            }
            $pos->transaction->update([
                'date' => $pos->sale_date,
                'debit' => $pos->final_receivable,
                'particulars' => 'Sale #' . $pos->id . " Updated",
                'balance' => $balance + $pos->final_receivable
            ]);
            static::calculate_customer_balance($pos->transaction);
        }
    }
    /**
     * PURCHASE Transactions
     */
    public static function create_purchase_transaction($purchase)
    {
        $balance = 0;
        $supplier = $purchase->supplier;
        if ($supplier) {
            $last_transaction = $supplier->transactions()->orderBy('id', 'desc')->first();
            // info($last_transaction);
            if ($last_transaction) {
                $balance = $last_transaction->balance;
            }
        }
        $transaction = $purchase->transaction()->create([
            'date' => $purchase->purchase_date,
            'credit' => $purchase->payable,
            'particulars' => 'Purchase #' . $purchase->id,
            'supplier_id' => $purchase->supplier_id,
            'balance' => $balance - $purchase->payable
        ]);
        // info($transaction);
    }

    public static function update_purchase_transaction($purchase)
    {
        // info($purchase);
        if ($purchase->transaction) {
            $balance = 0;
            $supplier = $purchase->supplier;
            if ($supplier) {
                // info($purchase->transaction);
                $last_transaction = $supplier->transactions()->where('id', '!=', $purchase->transaction->id ?? null)->where('id', '<', $purchase->transaction->id ?? null)->orderBy('id', 'desc')->first();
                // info($last_transaction);
                if ($last_transaction) {
                    $balance = $last_transaction->balance;
                }
            }
            // info($balance);
            $purchase->transaction->update([
                'date' => $purchase->purchase_date,
                'credit' => $purchase->payable,
                // 'particulars' => 'Purchase #' . $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'balance' => $balance - $purchase->payable
            ]);

            static::calculate_supplier_balance($purchase->transaction);
        }
    }

    /**
     * ActualPayment Transaction
     */
    public static function customer_payment_transaction($actual_payment)
    {
        // info($actual_payment);
        $customer = $actual_payment->customer;
        $balance = 0;
        if ($customer) {
            $last_transaction = $customer->transactions()->orderBy('id', 'desc')->first();

            if ($last_transaction) {
                $balance = $last_transaction->balance;
            }
        }


        // Receive or pay?

        if ($actual_payment->payment_type == 'receive') {
            $actual_payment->transaction()->create([
                'date' => $actual_payment->date,
                'credit' => $actual_payment->amount,
                'particulars' => $actual_payment->wallet_payment ? 'Received from Customer - Paid From Wallet' : 'Received from Customer',
                'customer_id' => $actual_payment->customer_id,
                'balance' => $actual_payment->wallet_payment ? $balance : $balance - $actual_payment->amount
            ]);
        } else if ($actual_payment->payment_type == 'pay') {
            $actual_payment->transaction()->create([
                'date' => $actual_payment->date,
                'debit' => $actual_payment->amount,
                'particulars' => 'Paid to Customer',
                'customer_id' => $actual_payment->customer_id,
                'balance' => $actual_payment->wallet_payment ? $balance : $balance + $actual_payment->amount
            ]);
        } else {
            info("NOT RECEIVE OR PAY");
            info($actual_payment->payment_type);
        }
    }

    public static function supplier_payment_transaction($actual_payment)
    {
        $supplier = $actual_payment->supplier;
        $balance = 0;
        if ($supplier) {
            $last_transaction = $supplier->transactions()->orderBy('id', 'desc')->first();

            if ($last_transaction) {
                $balance = $last_transaction->balance;
            }
        }

        // info($balance);

        // Receive or pay?

        if ($actual_payment->payment_type == 'receive') {
            $actual_payment->transaction()->create([
                'date' => $actual_payment->date,
                'credit' => $actual_payment->amount,
                'particulars' => 'Received from Supplier',
                'supplier_id' => $actual_payment->supplier_id,
                'balance' => $actual_payment->wallet_payment ? $balance : $balance - $actual_payment->amount
            ]);
        }

        if ($actual_payment->payment_type == 'pay') {
            $actual_payment->transaction()->create([
                'date' => $actual_payment->date,
                'debit' => $actual_payment->amount,
                'particulars' => $actual_payment->wallet_payment ? 'Paid to Supplier - Paid From Wallet' : 'Paid to Supplier',
                'supplier_id' => $actual_payment->supplier_id,
                'balance' => $actual_payment->wallet_payment ? $balance : $balance + $actual_payment->amount
            ]);
        }
    }

    public static function create_payment_transaction($actual_payment_id)
    {
        /**
         * For whatever reason ActualPayment is not returning all its properties
         * when passed as an object
         * So I decided to implement it this way
         */
        $actual_payment = ActualPayment::find($actual_payment_id);
        // info($actual_payment);
        if ($actual_payment->supplier_id) {
            static::supplier_payment_transaction($actual_payment);
        } else if ($actual_payment->customer_id != null || $actual_payment->customer_id == 0) {
            static::customer_payment_transaction($actual_payment);
        } else {
            info('Neither Customer Nor Supplier if found when creating payment_transaction');
        }
    }


    /**
     * RETURN Transactions
     */

    public function create_return_transaction($return)
    {
        $balance = 0;

        $customer = $return->customer;
        if ($customer) {
            $last_transaction = $customer->transactions()->orderBy('id', 'desc')->first();

            if ($last_transaction) {
                $balance = $last_transaction->balance;
            }
        }


        $return->transaction()->create([
            'date' => $return->sale_date,
            'credit' => $return->receivable,
            'particulars' => 'Return #' . $return->id,
            'customer_id' => $return->customer_id,
            'balance' => $balance - $return->receivable
        ]);
    }


    /**
     * Customer Opening Balance
     */
    public static function add_customer_opening_balance($customer)
    {
        $balance = 0;
        // receivable
        if ($customer->opening_receivable) {
            $customer->opening_transactions()->create([
                'date' => date('Y-m-d'),
                'debit' => $customer->opening_receivable,
                'particulars' => 'Opening Receivable',
                'customer_id' => $customer->id,
                'balance' => $balance + $customer->opening_receivable
            ]);
        }

        // payable
        if ($customer->opening_payable) {
            $opening_receivable_transaction = $customer->opening_transactions()->first();
            if ($opening_receivable_transaction) {
                $balance = $opening_receivable_transaction->balance;
            }

            $customer->opening_transactions()->create([
                'date' => date('Y-m-d'),
                'credit' => $customer->opening_payable,
                'particulars' => 'Opening Payable',
                'customer_id' => $customer->id,
                'balance' => $balance - $customer->opening_payable
            ]);
        }
    }

    /**
     * Supplier Opening Balance
     */
    public static function add_supplier_opening_balance($supplier)
    {
        $balance = 0;
        // receivable
        if ($supplier->opening_receivable) {
            $supplier->opening_transactions()->create([
                'date' => date('Y-m-d'),
                'debit' => $supplier->opening_receivable,
                'particulars' => 'Opening Receivable',
                'supplier_id' => $supplier->id,
                'balance' => $balance + $supplier->opening_receivable
            ]);
        }

        // payable
        if ($supplier->opening_payable) {
            $opening_receivable_transaction = $supplier->opening_transactions()->first();
            if ($opening_receivable_transaction) {
                $balance = $opening_receivable_transaction->balance;
            }

            $supplier->opening_transactions()->create([
                'date' => date('Y-m-d'),
                'credit' => $supplier->opening_payable,
                'particulars' => 'Opening Payable',
                'supplier_id' => $supplier->id,
                'balance' => $balance - $supplier->opening_payable
            ]);
        }
    }
}