<?php

namespace App\Http\Controllers;

use App\AccountToAccountTransection;
use App\BankAccount;
use App\Expense;
use App\Payment;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BankAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create-bank_account',  options: ['only' => ['create', 'store']]);
        $this->middleware('can:edit-bank_account',  ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-bank_account', ['only' => ['destroy']]);
        $this->middleware('can:list-bank_account', ['only' => ['index']]);
        $this->middleware('can:show-bank_account', ['only' => ['show']]);

        $this->middleware('can:bank_account-add_money', ['only' => ['add_money','add_money_store']]);
        $this->middleware('can:bank_account-withdraw_money', ['only' => ['withdraw_money','withdraw_money_store']]);
        $this->middleware('can:bank_account-transfer', ['only' => ['transfer','transfer_store']]);
        $this->middleware('can:bank_account-history', ['only' => ['history']]);
    }

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        // return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, ['path' => Paginator::resolveCurrentPath()]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = BankAccount::all();

        return view('pages.account.index', compact('accounts'));
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
        // validate
        $request->validate([
            'name' => 'required|string',
            'opening_balance' => 'required|numeric',
        ]);

        // store
        BankAccount::create($request->all());

        // redirect back with success message
        session()->flash('success', 'Account Added!');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccount $bankAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(BankAccount $bankAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccount $bankAccount)
    {
        //
    }

    public function transfer(BankAccount $account)
    {
        // dd("HELLO");
        // dd($account);
        return view('pages.account.form.transfer', compact('account'));
    }

    public function transfer_store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            "from" => "required",
            'to' => 'required',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        // $data=$request->all();
        // $data["user_id"]=auth()->user()->id;
        AccountToAccountTransection::create($request->all());

        return response()->json(['success' => 'Added new records.']);
    }

    public function add_money(BankAccount $bank_account)
    {
        return view('pages.account.form.add_balance', compact('bank_account'));
    }

    public function add_money_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // "from" => "required",
            'to' => 'required',
            'amount' => 'required',
            'owner_id'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        // $data=$request->all();
        // $data["user_id"]=auth()->user()->id;
        AccountToAccountTransection::create($request->all());

        return response()->json(['success' => 'Added new records.']);
    }
    public function withdraw_money(BankAccount $bank_account)
    {
        return view('pages.account.form.withdraw_balance', compact('bank_account'));
    }

    public function withdraw_money_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required',
            'amount' => 'required',
            'owner_id'=>'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        AccountToAccountTransection::create($request->all());

        return response()->json(['success' => 'Withdraw Money Successfully.']);
    }

    public function history(BankAccount $account)
    {
        // dd($account);
        if (!isset($_GET['page'])) {
            $page = 1;
        } else {
            $page = $_GET['page'];
        }

        // IDENTIFIER

        $payment = Payment::where('bank_account_id', $account->id)
            // ->get();
            // ->where('payment_type','receive')
            ->where('wallet_payment',0)
            ->get(['created_at as payment_date', 'pay_amount as amount', 'payment_type as type', DB::raw('"\\App\\\Payment" as model'), 'id', DB::raw('"" as note')])->toArray();
        // ->get(['payment_date', 'pay_amount as amount', 'payment_type as type', DB::raw('"\\App\\\Payment" as model'), 'id'])->toArray();


        $normal_expense = Expense::where('bank_account_id', $account->id)
            ->get(['created_at as payment_date', 'amount', DB::raw('"pay" as type'), DB::raw('"\\App\\\Expense" as model'), 'id', 'note'])->toArray();

        // Money Transfered OUt
        $transferred_out = AccountToAccountTransection::where('from', $account->id)
            ->get(['created_at as payment_date', 'amount', DB::raw('"pay" as type'), DB::raw('"\\App\\\AccountToAccountTransection" as model'), 'id', 'note'])->toArray();

        // Money Transfered In
        $account_to_account_type = "\\App\\\AccountToAccountTransection";
        // dd($account_to_account_type);
        $transferred_in = AccountToAccountTransection::where('to', $account->id)
            ->get(['created_at as payment_date', 'amount', DB::raw('"receive" as type'), DB::raw("'$account_to_account_type' as model"), 'id', 'note'])->toArray();


        $payment = array_merge($payment, $normal_expense, $transferred_out, $transferred_in);
        $payment = collect($payment);
        $history = $payment->sortByDesc('payment_date');
        $history = $this->paginate($history, 20);
        // $history=$payment->paginate(20);

        // dd($history);

        return view('pages.account.history', compact('history', 'account'));
    }
}
