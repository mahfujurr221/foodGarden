<?php

namespace App\Http\Controllers;

use App\Brand;
use App\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create-expense_category',  ['only' => ['create', 'store']]);
        $this->middleware('can:edit-expense_category',  ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-expense_category', ['only' => ['destroy']]);
        $this->middleware('can:list-expense_category', ['only' => ['index']]);
        // $this->middleware('can:show-customer', ['only' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ex_categories = ExpenseCategory::latest()->paginate(20);
        return view('pages.expense-category.index')
            ->withExpenseCategories($ex_categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name' => 'required|max:255'
        ]);
        $ex_category = new ExpenseCategory();
        $ex_category->name = $request->name;
        // $request->active ? $ex_category->active = 1 : $ex_category->active = 0;
        $ex_category->save();

        session()->flash('success', 'Expense Category Created...');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ExpenseCategory  $expenseCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ExpenseCategory $expenseCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ExpenseCategory  $expenseCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ExpenseCategory $expense_category)
    {
        $ex_categories = ExpenseCategory::latest()->paginate(25);
        return view('pages.expense-category.index')
            ->withExpenseCategory($expense_category)
            ->withExpenseCategories($ex_categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ExpenseCategory  $expenseCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExpenseCategory $expense_category)
    {
        $this->validate($request, [
            'name' => 'required|max:255'
        ]);
        $expense_category->name = $request->name;
        //$request->active ? $expense_category->active = 1 : $expense_category->active = 0;
        $expense_category->save();

        session()->flash('success', 'Expense Category Updated...');
        return redirect()->route('expense-category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ExpenseCategory  $expenseCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpenseCategory $expense_category)
    {
        if($expense_category->forceDelete()){
            session()->flash('success', 'Expense Category Deleted...');
        } else {
            session()->flash('error', 'Expense Category can\'t be Deleted!');
        }

        return redirect()->back();
    }
}
