<?php

namespace App\Http\Controllers;

use App\Expense;
use App\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create-expense',  ['only' => ['create', 'store']]);
        $this->middleware('can:edit-expense',  ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-expense', ['only' => ['destroy']]);
        $this->middleware('can:list-expense', ['only' => ['index']]);
        // $this->middleware('can:show-customer', ['only' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $expenses = new Expense();
        $expenses = $expenses->filter($request, $expenses);
        $expenses = $expenses->orderBy('created_at', 'desc');

        $expense_category = ExpenseCategory::all();
        
        return view('pages.expense.index')
            ->withExpenseCategory($expense_category)
            ->withExpenses($expenses->paginate(20));
        
        
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $expese_categories = ExpenseCategory::orderBy('name')->get();
        return view('pages.expense.create')
            ->with('expese_categories', $expese_categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'category_id' => 'required',
            'expense_date' => 'required',
            'amount' => 'required'
        ]);

        $data = $request->all();

        $expense = Expense::create($data);
        session()->flash('success', 'Expense Save...');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        $expese_categories = ExpenseCategory::latest()->get();
        return view('pages.expense.create')
            ->with('expese_categories', $expese_categories)
            ->withExpense($expense);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'category_id' => 'required',
            'expense_date' => 'required',
            'amount' => 'required'
        ]);
        $data = $request->all();
        $expense = $expense->update($data);
        session()->flash('success', 'Expense Update...');
        return redirect()->route('expense.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        session()->flash('success', 'Expense Deleted...');
        return redirect()->back();
    }
}
