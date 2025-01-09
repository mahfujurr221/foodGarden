<?php

namespace App\Http\Controllers;

use App\ActualPayment;
use App\Product;
use App\Purchase;
use App\PurchaseItem;
use App\Services\PurchaseService;
use App\Services\TransactionService;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:create-purchase', ['only' => ['create', 'store']]);
        $this->middleware('can:edit-purchase', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-purchase', ['only' => ['destroy']]);
        $this->middleware('can:list-purchase', ['only' => ['index']]);
        $this->middleware('can:show-purchase', ['only' => ['show']]);

        $this->middleware('can:purchase-add_payment', ['only' => ['add_payment', 'store_payment']]);
        $this->middleware('can:purchase-add_supplier', ['only' => ['add_supplier', 'store_supplier']]);
        $this->middleware('can:purchase-receipt', ['only' => ['receipt']]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $purchases = new Purchase();
        $purchases = $purchases->filter($request, $purchases);
        $purchases = $purchases->orderBy('id', 'DESC')->paginate(20);
        $suppliers = Supplier::select('id', 'name', 'phone')->get();
        $products = Product::select('id', 'name', 'code')->get();
        $purchase_service = new PurchaseService();

        return view('pages.purchases.index', compact('products', 'suppliers', 'purchases', 'purchase_service'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::orderBy('name')->get();
        return view('pages.purchases.create', compact('suppliers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'supplier_id' => 'required',
            'purchase_date' => 'required'
        ]);

        $purchase = PurchaseService::make_purchase($request);
        // dd($purchase);
        if ($purchase) {
            $purchase->update_calculated_data();
            session()->flash('success', 'Your Purchase has been done!');
            return redirect()->route('purchase.receipt', $purchase->id);
        } else {
            session()->flash('error', 'Purchase Failed!');
            return back();
        }
    }
    /**
     * Display the specified resource.
     *
     * @param \App\Purchase $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Purchase $purchase)
    {
        // dd($purchase);
        return view('pages.purchases.show', compact('purchase'));
    }

    public function receipt($id)
    {
        $purchase = Purchase::findOrFail($id);
        return view('pages.purchases.receipt')->withPurchase($purchase);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Purchase $purchase
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Purchase $purchase)
    {
        // if ($purchase->stocks->count() > 0) {
        //     session()->flash('error', 'Can\'t edit this Purchase. You already used items of this Purchase');
        //     return back();
        // }

        $suppliers = Supplier::all();
        return view('pages.purchases.edit', compact('purchase', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Purchase $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purchase $purchase)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            foreach ($request->purchase_item_id as $key => $purchase_item_id) {
                $purchase_item = PurchaseItem::find($purchase_item_id);
                $main_qty = 0;
                $sub_qty = 0;
                // since old_purchase_item-> find everything using id
                if ($request->main_qty && array_key_exists($purchase_item_id, $request->main_qty)) {
                    $main_qty = $request->main_qty[$purchase_item_id];
                }

                if ($request->sub_qty && array_key_exists($purchase_item_id, $request->sub_qty)) {
                    $sub_qty = $request->sub_qty[$purchase_item_id];
                }

                $qty = $purchase_item->product->to_sub_quantity($main_qty, $sub_qty);

                if ($purchase_item->spent() > $qty) {
                    throw new \Exception('Already spent more.');
                    session()->flash('error', $purchase_item->product->name . ' already Sold+Damaged amount is ' . $purchase_item->spent());
                } else {
                    $purchase_item->update([
                        'main_unit_qty' => $main_qty,
                        'sub_unit_qty' => $sub_qty,
                        'qty' => $qty,
                        'discount_qty' => $request->discount_qty[$purchase_item_id],
                        'rate' => $request->rate[$purchase_item_id],
                        'sub_total' => $request->subtotal_input[$purchase_item_id],
                        'product_id' => $request->product[$purchase_item_id],
                    ]);

                    $purchase_item->update_remaining();
                }
            }

            // New Items

            foreach ($request->new_product ?? collect([]) as $key => $product_id) {
                $product = Product::find($product_id);
                $main_qty = 0;
                $sub_qty = 0;

                if ($request->new_main_qty && array_key_exists($product_id, $request->new_main_qty)) {
                    $main_qty = $request->new_main_qty[$product_id];
                }
                if ($request->sub_qty && array_key_exists($product_id, $request->sub_qty)) {
                    $sub_qty = $request->sub_qty[$product_id];
                }
                $qty = $product->to_sub_quantity($main_qty, $sub_qty);
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $request->new_product[$product_id],
                    'rate' => $request->new_rate[$product_id],
                    'main_unit_qty' => $main_qty,
                    'sub_unit_qty' => $sub_qty,
                    'qty' => $qty,
                    'discount_qty' => $request->discount_qty[$product_id],
                    'remaining' => $qty,
                    'sub_total' => $request->new_subtotal_input[$product_id],
                ]);
            }

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'payable' => $purchase->items()->sum('sub_total'),
                'purchase_date' => $request->purchase_date
            ]);

            $purchase->update_calculated_data();

            TransactionService::update_purchase_transaction($purchase);
            DB::commit();
            session()->flash('success', 'Purchase Updated');
        } catch (\Exception $e) {
            // dd($e);
            info($e);
            DB::rollback();
            session()->flash('error', 'Oops! SomeThing Went Wrong!');
        }


        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Purchase $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {

        // dd($purchase);
        if ($purchase->delete()) {
            session()->flash('success', 'Your Purchase has been deleted !');
        } else {
            session()->flash('warning', 'Purchase not Deleted. Please check.');
        }
        return back();
    }

    public function purchase_item_product_id($purchaseId)
    {
        $product = PurchaseItem::where('purchase_id', $purchaseId)->pluck('product_id');
        return $product;
    }

    public function partial_destroy($id)
    {
        $purchaseItem = PurchaseItem::find($id);
        $purchaseId = $purchaseItem->purchase_id;
        $purchase = Purchase::find($purchaseId);

        $totalPurchaseItem = PurchaseItem::where('purchase_id', $purchaseId)->get()->count();
        if ($totalPurchaseItem > 1) {
            $used_qty = $purchaseItem->stocks->count();
            if ($used_qty > 0) {
                session()->flash('error', 'This item is already used.');
                return back();
            }

            $purchase->update([
                'payable' => $purchase->payable - $purchaseItem->sub_total
            ]);
            $purchaseItem->delete();

            $purchase->update_calculated_data();
            TransactionService::update_purchase_transaction($purchase);
            session()->flash('success', 'Purchase Item Deleted');
            return redirect()->route('purchase.edit', $purchaseId);
        } else {
            if ($purchase->payments) {
                $purchase->payments()->delete();
            }
            $purchaseItem->delete();
            $purchase->forceDelete();
            session()->flash('success', 'Purchase Deleted');
            return redirect()->route('purchase.index');
        }
    }

    public function add_supplier()
    {
        return view('pages.purchases.forms.add_supplier');
    }

    public function store_supplier(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'phone' => 'required',
            'email' => 'nullable|email'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        // $data=$request->all();
        // $data["user_id"]=auth()->user()->id;
        Supplier::create($request->all());

        return response()->json(['success' => 'Added Supplier.']);
    }


    // Add Payment
    public function add_payment(Purchase $purchase)
    {
        // dd("ADD PAYMENT");
        return view('pages.purchases.forms.add_payment', compact('purchase'));
    }

    public function store_payment(Request $request, Purchase $purchase)
    {

        $validator = Validator::make($request->all(), [
            "payment_date" => "required",
            "pay_amount" => [
                'required',
                function ($attribute, $value, $fail) use ($purchase, $request) {
                    // dd($value);
                    if ($purchase->payable < $purchase->paid + $request->pay_amount) {
                        return $fail('Over Payment not Alowed! Due is ' . $purchase->due . ' Tk');
                    }
                }
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $actual_payment = ActualPayment::create([
            'payment_type' => 'pay',
            'supplier_id' => $purchase->supplier_id,
            'amount' => $request->pay_amount,
            'date' => $request->payment_date,
            'note' => $request->note
        ]);

        $purchase->payments()->create([
            'actual_payment_id' => $actual_payment->id,
            'bank_account_id' => $request->bank_account_id,
            'payment_date' => $request->payment_date,
            'payment_type' => 'pay',
            'pay_amount' => $request->pay_amount,
            'method' => $request->payment_method,
        ]);

        return response()->json(['success' => 'Added new records.']);
    }
}