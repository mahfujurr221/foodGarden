<?php

namespace App\Http\Controllers;

use App\ActualPayment;
use App\Payment;
use App\Purchase;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create-supplier',  ['only' => ['create', 'store']]);
        $this->middleware('can:edit-supplier',  ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-supplier', ['only' => ['destroy']]);
        $this->middleware('can:list-supplier', ['only' => ['index']]);
        // $this->middleware('can:show-customer', ['only' => ['show']]);

        $this->middleware('can:supplier-wallet_payment', ['only' => ['wallet_payment','store_wallet_payment']]);
        $this->middleware('can:supplier-report', ['only' => ['report']]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd(Supplier::first());
        $data=[];
        $suppliers=new Supplier();

        if($request->name!=null){
            $suppliers=$suppliers->where('name','like','%'.$request->name.'%');
            $data['name']=$request->name;
        }

        if($request->phone!=null){
            // $suppliers=$suppliers->where('phone','like','%'.$request->phone.'%');
            $suppliers=$suppliers->where('phone',$request->phone);
            $data['phone']=$request->phone;
        }


        $data['suppliers'] = $suppliers->latest()->paginate(20);
        // dd($data);
        return view('pages.supplier.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.supplier.create');
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
            'phone' => 'required',
            'email' => 'nullable|email',
            // 'address' => 'required'
        ]);
        $data=$request->all();
        $data['opening_receivable']=$request->opening_receivable!=null?$request->opening_receivable:0;
        $data['opening_payable'] = $request->opening_payable != null ? $request->opening_payable : 0;

        Supplier::create($data);

        session()->flash('success', 'Supplier Information Store');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        return view('pages.supplier.edit')->withSupplier($supplier);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'phone' => 'required',
            'email' => 'nullable|email',
            // 'address' => 'required'
        ]);

        $data = $request->all();
        $data['opening_receivable'] = $request->opening_receivable != null ? $request->opening_receivable : 0;
        $data['opening_payable'] = $request->opening_payable != null ? $request->opening_payable : 0;

        $supplier->update($data);

        session()->flash('success', 'Supplier Information Update');
        return redirect()->route('supplier.edit', $supplier->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        if ($supplier->delete()) {
            session()->flash('success', 'Supplier Information Deleted');
        } else {
            session()->flash('warning', 'Supplier Do\'t Delete. Please Check all relational Data');
        }
        return redirect()->back();
    }

    public function suppliers()
    {
        $suppliers = Supplier::where('status', 1)->latest()->get(['id', 'name', 'phone']);
        return response()->json($suppliers);
    }

    public function supplier_due($id)
    {
        $supplier = Supplier::where('status', 1)->findOrFail($id);
        // $duePurchaseList = $supplier->due_purchases();
        $data = [
            'supplier_name' => $supplier->name,
            'due_invoice' => $supplier->due_purchase_count(),
            'purchase_due' => $supplier->purchase_due(),
            'walletBalance' => $supplier->wallet_balance(),
            'total_due' => $supplier->total_due()
        ];
        return response()->json($data);
    }

    public function wallet_payment(Supplier $supplier)
    {
        return view('pages.supplier.forms.wallet_payment', compact('supplier'));
    }

    public function store_wallet_payment(Request $request, Supplier $supplier)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            "payment_date" => "required",
            "pay_amount" => [
                'required', function ($attribute, $value, $fail) use ($supplier, $request) {
                    // dd($value);
                    if ($request->pay_amount > $supplier->wallet_balance()) {
                        return $fail('Your wallet has ' . $supplier->wallet_balance() . ' Tk');
                    }

                    if ($supplier->due() < $request->pay_amount) {
                        return $fail('Over Payment not Alowed! Due is ' . $supplier->due() . ' Tk');
                    }
                }
            ],
            'bank_account_id'=>'integer|required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }
        // return $request->all();
        $actual_payment              = new ActualPayment();
        $actual_payment->supplier_id = $supplier->id;
        $actual_payment->wallet_payment = 1;
        $actual_payment->amount      = $request->pay_amount;
        $actual_payment->date        = $request->payment_date;
        $actual_payment->payment_type = 'pay';
        $actual_payment->note         = $request->note;
        $actual_payment->save();


        $due_purchases_list = $supplier->purchases()->where('due', '>', 0)->get();

        $tempAmount =$request->pay_amount;;

        if ($due_purchases_list->count() > 0) {
            foreach ($due_purchases_list as $due_purchase) {
                $due_amount = $due_purchase->due;

                if ($tempAmount >= 0 && $due_amount <= $tempAmount
                ) {
                    $tempAmount = $tempAmount - $due_amount;
                    // Due Amount full paid
                    $due_purchase->payments()->create([
                        'actual_payment_id' => $actual_payment->id,
                        'wallet_payment'    => 1,
                        'bank_account_id'   => $request->bank_account_id,
                        'payment_date'      => $request->payment_date,
                        'payment_type'      => 'pay',
                        'pay_amount'        => $due_amount,
                        'method'            => $request->method,
                    ]);
                } else {
                    // Due amount in pay extra amount
                    $due_purchase->payments()->create([
                        'actual_payment_id' => $actual_payment->id,
                        'wallet_payment'    => 1,
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
        }
        else {
            $supplier->payments()->create([
                'actual_payment_id' => $actual_payment->id,
                'bank_account_id'   => $request->bank_account_id,
                'payment_date' => $request->payment_date,
                'payment_type' => 'pay',
                'pay_amount' => $tempAmount,
                'method' => $request->method,
            ]);
        }
    }

    public function report(Supplier $supplier)
    {
        $payments = Payment::whereHas('actual_payment',function($actual_payment)use($supplier){
            $actual_payment->where('supplier_id',$supplier->id);
        })->where(function($query){
            $query->where('paymentable_type',Supplier::class)
                    ->orWhere('paymentable_type',Purchase::class);
        })->get();
        return view('pages.supplier.report',compact('supplier','payments'));
    }


    public function updateStatus(Request $request, $id)
    {
        // dd($request->all(),$id);
        $supplier = Supplier::findOrFail($id);
        
        $supplier->status = $request->status;
        $supplier->save();
        session()->flash('success', 'Supplier Status Updated');
        return back();
    }


}
