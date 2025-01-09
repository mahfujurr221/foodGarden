<?php

namespace App;

use App\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function todayExpense()
    {
        $expense = Expense::where('expense_date', date('Y-m-d'))->get();
        return $expense;
    }
    public function totalExpense()
    {
        return Expense::sum('amount');
    }

    // filter
    public function filter($request, $expenses)
    {
        if ($request->start_date && $request->end_date && $request->category) {
            $expenses = $expenses->whereBetween('expense_date', [$request->start_date, $request->end_date])->where('category_id', $request->category);
        } else if ($request->start_date && $request->end_date) {
            $expenses = $expenses->whereBetween('expense_date', [$request->start_date, $request->end_date]);
        } else if ($request->category) {
            $expenses = $expenses->where('category_id', $request->category);
        }
        return $expenses;
    }

    public function date_to_date($start_date, $end_date)
    {
        $expenses = $this->whereBetween('expense_date', [$start_date, $end_date])->sum('amount');
        // dd($expenses);
        return $expenses;
    }
}
