<?php

namespace App\Http\Controllers;

use App\DeliveryMethod;
use App\Pos;
use App\PosItem;
use App\Product;
use App\Customer;

// use App\DeliveryMethod;
use App\ActualPayment;
use App\DueCollection;
use App\Estimate;
use App\EstimateItem;
use App\OrderDamageItem;
use App\OrderReturnItem;
use App\PosSetting;
use App\Services\StockService;
use App\Services\TransactionService;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PosController extends Controller
{
    public function __construct()
    {
        // $this->middleware('can:create-pos', ['only' => ['create', 'store']]);
        $this->middleware('can:edit-pos', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-pos', ['only' => ['destroy']]);
        $this->middleware('can:list-pos', ['only' => ['index']]);
        $this->middleware('can:show-pos', ['only' => ['show']]);

        $this->middleware('can:pos-add_payment', ['only' => ['add_payment', 'store_payment']]);
        $this->middleware('can:pos-add_customer', ['only' => ['add_customer', 'store_customer']]);
        $this->middleware('can:pos_receipt', ['only' => ['pos_receipt']]);
        $this->middleware('can:chalan_receipt', ['only' => ['chalan_receipt']]);

        $this->middleware('can:pos-purchase_cost_breakdown', ['only' => ['purchase_cost_breakdown']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request->all());
        $sales = new Pos();
        $todaySale = Pos::where('sale_date', date('Y-m-d'))->get();
        $pos = new Pos();
        $sales = $sales->filter($request, $sales);
        // if (Role::is_user_role("admin")) {
        //     $sales = $sales->orderBy('created_at', 'desc');
        // } elseif (Role::is_user_role("operator")) {
        //     $sales = $sales->whereDate('created_at', Carbon::today());
        // }
        $total = $sales->sum('final_receivable');
        if (auth()->user()->hasRole('admin')) {
            $sales = $sales->with('customer')->orderBy('id', 'desc')->paginate(20);
        } elseif (auth()->user()->hasRole('SR')) {
            $sales = $sales->where('brand_id', auth()->user()->brand_id)->with('customer')->orderBy('id', 'desc')->paginate(20);
        }
        $customers = Customer::get();
        $products = Product::select('id', 'name', 'code')->get();
        $brands = Supplier::where('status', 1)->select('id', 'name')->get();
        return view('pages.pos.index', compact('sales', 'customers', 'products', 'total', 'brands'))
            ->withPos($pos);
        // ->withCustomers(Customer::all());
    }

    public function pos_products(Request $request)
    {
        $data = [];

        if ($request->ajax()) {
            $products = new Product();

            if ($request->code != null) {
                $products = $products->whereIn('brand_id', json_decode(auth()->user()->brand_id))->where('name', 'like', '%' . $request->code . '%')->orWhere('code', 'like', '%' . $request->code . '%');
            }
            if ($request->category != null) {
                $products = $products->where('brand_id', $request->category);
                $data['category_id'] = $request->category;
            }
            $data['products'] = $products->orderBy('name')->whereIn('brand_id', json_decode(auth()->user()->brand_id))->where('status', 1)->get();

            return view('pages.pos.products', $data)->render();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // dd("");
        $customers = Customer::latest()->get();
        $products = new Product();

        if ($request->code != null) {
            $products = $products->where('code', 'like', '%' . $request->code . '%');
        }
        if (auth()->user()->hasRole('admin')) {
            $products = $products->where('brand_id',1)->orderBy('name')->get();
        } else {
            // $products = $products->where('brand_id', auth()->user()->brand_id)->orderBy('name')->get();
            $products = $products->where('brand_id', 1)->orderBy('name')->get();
        }
        // $delivery_methods = DeliveryMethod::all();
        // dd($delivery_methods);
        
        return view('pages.pos.create', compact('products', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'product_id' => 'required|array',
            'sub_total' => 'required',
        ]);
        try {
            DB::beginTransaction();
            $saleBy = auth()->id();
            if ($request->estimate) {
                $estimate = Estimate::find($request->estimate);
                $estimate->convert_status = 1;
                $estimate->save();

                $saleBy = $estimate->delivery_by;
            }
            $pos = Pos::create([
                'estimate_id' => $request->estimate,
                'brand_id' => $request->brand_id,
                'sale_date' => $request->sale_date,
                'sale_by' => $saleBy,
                'customer_id' => $request->customer,
                // 'balance'     => $request->balance,
                'discount' => $request->discount,
                'receivable' => $request->receivable_amount,
                'final_receivable' => $request->receivable_amount,
                'note' => $request->note,
                // 'delivery_cost'=>$request->delivery_cost,
                // 'delivery_method_id'=>$request->delivery_method,
            ]);

            $pos_number = str_pad($pos->id + 1, 8, '0', STR_PAD_LEFT);
            $pos->pos_number = '# ' . $pos_number;
            $pos->save();
            StockService::add_new_pos_items_and_recalculate_cost($request, $pos);
            $pos->update_calculated_data();

            if ($request->pay_amount != null) {
                $actual_payment = ActualPayment::create([
                    'customer_id' => $request->customer,
                    'amount' => $request->pay_amount,
                    'date' => $request->sale_date,
                    'payment_type' => 'receive',
                    'note' => $request->note
                ]);
                $pos->payments()->create([
                    'payment_date' => $request->sale_date,
                    'actual_payment_id' => $actual_payment->id,
                    'bank_account_id' => $request->bank_account_id,
                    'payment_type' => 'receive',
                    'pay_amount' => $request->pay_amount,
                    'method' => $request->payment_method,
                ]);
            }
            if(($request->receivable_amount-$request->pay_amount)!=0){
                $dueCollection = DueCollection::create([
                    'customer_id' => $request->customer,
                    'pos_id'=>$pos->id,
                    'due_by' => $saleBy,
                    'direct_transection'=>0,
                    'last_due_date' => $actual_payment->date??$request->sale_date,
                    'committed_due_date' => $request->committed_date,
                    'amount'=>$request->receivable_amount,
                    'paid'=>$request->pay_amount??0,
                    'due'=>$request->receivable_amount-$request->pay_amount,
                    'brand_id' => $request->brand_id,
                ]);
            }

            //if request has estimated id 
            if ($request->estimate) {
                $estimate = Estimate::find($request->estimate);
                $estimate->convert_status = 1;
                $estimate->save();
            }
            DB::commit();
            return redirect()->route('pos_receipt', $pos->id);
        } catch (\Exception $e) {
            DB::rollback();
            info($e);
            // dd($e->getMessage());
            if ($e->getMessage() == "Quantity Empty") {
                session()->flash('warning', 'Please enter product quantity properly.');
            } elseif ($e->getMessage() == "Low Stock") {
                session()->flash('warning', 'Some Products Does not Have stock!');
            } else {
                session()->flash('error', 'Oops Something went wrong!');
            }
            return back();
        }
        return redirect()->route('pos_receipt', $pos->id);
    }

    public function pos_receipt($pos_id)
    {
        $pos = Pos::findOrFail($pos_id);
        $pos_settings = PosSetting::first();
        return view('pages.pos.receipts.' . $pos_settings->invoice_type)->with('pos', $pos);
    }

    public function chalan_receipt($pos_id)
    {
        $pos = Pos::findOrFail($pos_id);
        return view('pages.pos.chalan')->with('pos', $pos);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Pos $pos
     * @return \Illuminate\Http\Response
     */
    public function show(Pos $po)
    {
        return view('pages.pos.show', ['pos' => $po]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Pos $po
     * @return \Illuminate\Http\Response
     */
    public function edit(Pos $po)
    {
        $pos = $po;
        $customers = Customer::latest()->get();
        $products = Product::orderBy('name')->paginate(10);
        $delivery_methods = DeliveryMethod::all();
        return view('pages.pos.edit', compact('products', 'customers', 'delivery_methods', 'pos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Pos $po
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pos $po)
    {
        // dd($request->all());
        $pos = $po;
        //dd( $request->all() );
        $pos_id = $request->pos_id;
        try {
            DB::beginTransaction();
            // Update Portion
            StockService::update_pos_items_and_recalculate_cost($request, $pos);
            // Calculate individual purchase
            StockService::add_new_pos_items_and_recalculate_cost($request, $pos);

            if (strpos($pos->discount, '%') !== false) {
                $discount = (float) str_replace("%", " ", $pos->discount);

                $discount_amount = $pos->items()->sum('sub_total') * ($discount / 100);

                $new_receivable = $pos->delivery_cost + $pos->items()->sum('sub_total') - $discount_amount;
            } else {
                $new_receivable = $pos->delivery_cost + $pos->items()->sum('sub_total') - $pos->discount;
            }
            $pos->receivable = $new_receivable;
            $pos->save();
            // update total purchase cost
            $pos->update_calculated_data();
            TransactionService::update_pos_transaction($pos);
            DB::commit();
            return redirect()->route('pos_receipt', $pos->id);
        } catch (\Exception $e) {
            info($e);
            DB::rollback();
            session()->flash('warning', 'Oops! Something went wrong');
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Pos $pos
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pos $po)
    {
        try {
            // dd($po);
            DB::beginTransaction();
            $po->forceDelete();
            $po->payments()->forceDelete();
            
            $order_damage_items = OrderDamageItem::where('estimate_id', $po->estimate_id)->get();
            if($order_damage_items){
                foreach ($order_damage_items as $order_damage_item) {
                    $order_damage_item->delete();
                }
            }
            $order_return_items = OrderReturnItem::where('estimate_id', $po->estimate_id)->get();
            if($order_return_items){
                foreach ($order_return_items as $order_return_item) {
                    $order_return_item->delete();
                }
            }
            //delete estimate and estimateItems
            $estimate = Estimate::find($po->estimate_id);
            if ($estimate) {
                $estimate->delete();
            }
            $estimateItems = EstimateItem::where('estimate_id', $po->estimate_id)->get();
            if ($estimateItems) {
                foreach ($estimateItems as $estimateItem) {
                    $estimateItem->delete();
                }
            }

            $dueCollection=DueCollection::where('pos_id',$po->id)->first();
            if($dueCollection){
                $dueCollection->delete();
            }
    
            DB::commit();
            session()->flash('success', 'Sale Deleted');
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('warning', 'Oops! Something went wrong');
            return back();
        }    
    }

    public function get_product()
    {
        $product = Product::where('code', request('code'))->first();
        if ($product) {
            return response($product);
        } else {
            return [];
        }
    }

    // public function product_search_by_name()
    // {
    //     $query = request('req');
    //     $brand_id = request('brand_id');
    //     $products = Product::where('name', 'LIKE', "%$query%")->orWhere('code', 'LIKE', "%$query%")->get();
    //     return response()->json($products);
    // }

    public function product_search_by_name()
{
    $query = request('req');
    $brand_id = request('brand_id');

    $products = Product::where(function ($q) use ($query) {
        $q->where('name', 'like', "%$query%")
          ->orWhere('code', 'like', "%$query%");
    });

    if ($brand_id) {
        $products->where('brand_id', $brand_id);
    }

    return response()->json($products->get());
}
    public function product_search_by_code()
    {
        $query = request('req');
        $products = Product::where('code', 'LIKE', "%$query%")->get();
        return response()->json($products);
    }

    public function pos_item_product_id($posId)
    {
        $product = PosItem::where('pos_id', $posId)->pluck('product_id');
        return $product;
    }

    public function partial_destroy($id)
    {
        $pos_item = PosItem::find($id);
        $pos_id = $pos_item->pos_id;
        $pos = Pos::find($pos_id);

        $total_pos = PosItem::where('pos_id', $pos_id)->get()->count();
        if ($total_pos > 1) {
            $pos_item->delete();
            $pos_item->stock()->delete();

            if (strpos($pos->discount, '%') !== false) {
                $discount = (float) str_replace("%", " ", $pos->discount);

                $discount_amount = $pos->items()->sum('sub_total') * ($discount / 100);
                $new_receivable = $pos->delivery_cost + $pos->items()->sum('sub_total') - $discount_amount;
            } else {
                $new_receivable = $pos->delivery_cost + $pos->items()->sum('sub_total') - $pos->discount;
            }

            $pos->receivable = $new_receivable;

            $pos->save();

            $pos->update_calculated_data();
            TransactionService::update_pos_transaction($pos);
            session()->flash('success', 'Sale Returned');
            return redirect()->route('pos.edit', $pos_id);
        } else {
            $pos_item->delete();
            $pos_item->stock()->delete();
            $pos->payments()->delete();
            $pos->forceDelete();
            session()->flash('success', 'Sale Deleted');
            return redirect()->route('pos.index');
        }
    }

    public function add_payment(Pos $pos)
    {
        return view('pages.pos.forms.add_payment', compact('pos'));
    }
    public function store_payment(Request $request, Pos $pos)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            "payment_date" => "required",
            "pay_amount" => [
                'required',
                function ($attribute, $value, $fail) use ($pos, $request) {
                    // dd($value);
                    if ($value <= 0) {
                        return $fail('Amount need to be greater than 0');
                    }
                    if ($pos->receivable < $pos->paid + $request->pay_amount) {
                        return $fail('Over Payment not Alowed! Due is ' . $pos->due . ' Tk');
                    }
                }
            ]
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }
        // $data=$request->all();
        // $data["user_id"]=auth()->user()->id;
        $actual_payment = ActualPayment::create([
            'customer_id' => $pos->customer_id,
            'amount' => $request->pay_amount,
            'payment_type' => 'receive',
            'date' => $request->payment_date,
            'note' => $request->note
        ]);
        $pos->payments()->create([
            'payment_date' => $request->payment_date,
            'actual_payment_id' => $actual_payment->id,
            'bank_account_id' => $request->bank_account_id,
            'payment_type' => 'receive',
            'pay_amount' => $request->pay_amount,
            'method' => $request->payment_method,
        ]);
        return response()->json(['success' => 'Added new records.']);
    }

    // AJAX ADD CUSTOMER
    public function add_customer()
    {
        return view('pages.pos.forms.add_customer');
    }

    public function store_customer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'phone' => 'required|unique:customers',
            // 'address' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $data = $request->all();
        // $data["user_id"]=auth()->user()->id;
        Customer::create($data);
        return response()->json(['success' => 'Added new records.']);
    }

    public function purchase_cost_breakdown(Pos $pos)
    {
        return view('pages.pos.purchase_cost_breakdown', compact('pos'));
    }
    
    
    public function deliveryBy(Request $request)
    {
        $query = Pos::query();

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('sale_date', [$request->start_date, $request->end_date]);
        } else {
            $query->where('sale_date', date('Y-m-d'));
        }

        $sales = $query->with(['sales_man', 'items'])
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('sale_by');

        return view('pages.pos.delivery_by', compact('sales'));
    }

}