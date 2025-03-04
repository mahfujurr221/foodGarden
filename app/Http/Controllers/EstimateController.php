<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\DeliveryMethod;
use App\Estimate;
use App\EstimateItem;
use App\Product;
use App\Customer;
use App\Services\StockService;

// use App\DeliveryMethod;
use App\Payment;
use App\Stock;
use App\ActualPayment;
use App\OrderDamageItem;
use App\OrderReturnItem;
use App\PosSetting;
use App\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EstimateController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create-estimate', ['only' => ['create', 'store']]);
        $this->middleware('can:edit-estimate', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-estimate', ['only' => ['destroy']]);
        $this->middleware('can:list-estimate', ['only' => ['index']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $brands = json_decode(auth()->user()->brand_id, true);

        $estimatesQuery = Estimate::query();

        if (auth()->user()->hasRole('admin')) {
            $estimatesQuery->with(['customer', 'items.product'])->where('convert_status', 0);
        } else {
            $estimatesQuery->whereIn('brand_id', $brands)->with(['customer', 'items.product'])->where('convert_status', 0);
        }

        $estimates = $estimatesQuery->orderBy('priority', 'asc')->get();

        // Preprocess items with readable quantities
        $estimates->each(function ($estimate) {
            $estimate->items->each(function ($item) {
                $product = $item->product;
                $item->readable_quantity = $product ? $product->readable_qty($item->qty) : null;
                $item->product_stock = $product ? $product->stock : null;
                $item->return_qty = $product ? $product->readable_qty($item->returned_qty) : null;
            });
        });

        $customers = Customer::get();

        return view('pages.estimate.index', compact('estimates', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $brands = json_decode(auth()->user()->brand_id, true);
        $brands = $brands[0];
        $customers = Customer::latest()->get();
        $products = new Product();
        if ($request->code != null) {
            $products = $products->where('code', 'like', '%' . $request->code . '%');
        }
        if (auth()->user()->hasRole('admin')) {
            $products = $products->where('brand_id', 1)->orderBy('name')->get();
        } else {
            $products = $products->where('brand_id', $brands)->orderBy('name')->get();
        }
        return view('pages.estimate.create', compact('products', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'product_id' => 'required|array',
            'sub_total' => 'required',
        ]);
        try {
            DB::beginTransaction();
            $estimate = Estimate::create([
                'estimate_date' => $request->estimate_date,
                'delivery_date' => $request->delivery_date,
                'brand_id' => $request->brand_id,
                'estimate_by' => auth()->id(),
                'delivery_by' => auth()->id(),
                'customer_id' => $request->customer,
                'note' => $request->note,
                'receivable' => $request->receivable_amount,
                'final_receivable' => $request->receivable_amount,
            ]);
            $estimate_number = str_pad($estimate->id + 1, 8, '0', STR_PAD_LEFT);
            $estimate->estimate_number = '# ' . $estimate_number;
            $estimate->save();
            if ($request->product_id) {
                foreach ($request->product_id as $key => $value) {
                    $main_qty = 0;
                    $sub_qty = 0;
                    if ($request->main_qty && array_key_exists($request->product_id[$key], $request->main_qty)) {
                        $main_qty = $request->main_qty[$request->product_id[$key]];
                    }
                    if ($request->sub_qty && array_key_exists($request->product_id[$key], $request->sub_qty)) {
                        $sub_qty = $request->sub_qty[$request->product_id[$key]];
                    }
                    $product = Product::find($request->product_id[$key]);
                    $qty = $product->to_sub_quantity($main_qty, $sub_qty);
                    // Insert EstimateI Item
                    $estimate_item = EstimateItem::create([
                        'estimate_id' => $estimate->id,
                        'product_name' => $request->name[$key],
                        'product_id' => $request->product_id[$key],
                        'rate' => $request->rate[$key],
                        // 'item_discount' => $request->item_discount[$key],
                        // 'discount'      => $request->dis[$key],
                        'main_unit_qty' => $main_qty,
                        'sub_unit_qty' => $sub_qty,
                        'ordered_qty' => $qty,
                        'returned_qty' => 0,
                        'qty' => $qty,
                        'discount_qty' => $request->discount_qty[$key],
                        'sub_total' => $request->sub_total[$key],
                        'ordered_sub_total' => $request->sub_total[$key],
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('estimate.show', $estimate->id);
        } catch (\Exception $e) {
            DB::rollback();
            info($e);
            session()->flash('error', 'Oops Something went wrong!');
            return back();
        }
        session()->flash('success', 'Succesfully Create Estimate');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Estimate $estimate)
    {
        $pos_settings = PosSetting::first();
        return view('pages.estimate.receipts.' . $pos_settings->invoice_type)->with('estimate', $estimate);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Estimate $estimate)
    {
        // $estimate       = $po;
        $customers = Customer::latest()->get();
        $products = Product::orderBy('name')->paginate(10);
        // $delivery_methods = DeliveryMethod::all();
        return view('pages.estimate.edit', compact('products', 'customers', 'estimate'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Estimate $estimate)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $estimate = Estimate::find($request->estimate);
            $estimate->update([
                'estimate_date' => $request->estimate_date,
                'delivery_date' => $request->delivery_date,
                'updated_by' => auth()->id(),
                'customer_id' => $request->customer,
                'receivable' => $request->estimate_receivable,
                'final_receivable' => $request->estimate_receivable,
                'note' => $request->note,
            ]);
            if ($estimate) {
                foreach ($request->old_id as $key => $value) {
                    $main_unit = 0;
                    $sub_unit = 0;
                    $returned = 0;
                    $returned_sub = 0;
                    $estimate_item = EstimateItem::find($value);
                    if ($request->old_main_qty && array_key_exists($value, $request->old_main_qty)) {
                        $main_unit = $request->old_main_qty[$value];
                    }
                    if ($request->old_sub_qty && array_key_exists($value, $request->old_sub_qty)) {
                        $sub_unit = $request->old_sub_qty[$value];
                    }
                    if ($request->old_returned && array_key_exists($value, $request->old_returned)) {
                        $returned = $request->old_returned[$value];
                    }
                    if ($request->old_returned_sub_unit && array_key_exists($value, $request->old_returned_sub_unit)) {
                        $returned_sub = $request->old_returned_sub_unit[$value];
                    }
                    // dd($returned_sub);
                    $ordered_qty = $estimate_item->product->to_sub_quantity($main_unit, $sub_unit);
                    $returned_qty = $estimate_item->product->to_sub_quantity($returned, $returned_sub);
                    $qty = $ordered_qty - $returned_qty;
                    $ordered_quantity_worth = $estimate_item->product->quantity_worth($ordered_qty, $estimate_item->rate);
                    $estimate_item->update([
                        'estimate_id' => $request->estimate,
                        'rate' => $request->old_rate[$key],
                        'main_unit_qty' => $main_unit,
                        'sub_unit_qty' => $sub_unit,
                        'ordered_qty' => $ordered_qty,
                        'qty' => $qty,
                        'returned' => $request->old_returned[$key],
                        'returned_sub_unit' => $request->old_returned_sub_unit[$key],
                        'returned_qty' => $returned_qty,
                        'returned_value' => $request->old_returned_value[$key],
                        'damage' => $request->old_damage[$key],
                        'damaged_value' => $request->old_damaged_value[$key],
                        'discount_qty' => $request->old_discount_qty[$key],
                        'discount_return' => $request->old_discount_return[$key],
                        'sub_total' => $request->old_sub_total[$key],
                        'ordered_sub_total' => $ordered_quantity_worth,
                    ]);
                }
            }
            DB::commit();
            session()->flash('success', 'Estimate Update Success');
            return redirect()->route('convert.invoice', $estimate->id);
        } catch (\Exception $e) {
            DB::rollback();
            info($e);
            // dd($e->getMessage());
            session()->flash('error', 'Oops Something went wrong!');
            return back();
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function partial_destroy($id)
    {
        $estimate_item = EstimateItem::find($id);
        $estimate_id = $estimate_item->estimate_id;
        $estimate = Estimate::find($estimate_id);
        $total_estimate = EstimateItem::where('estimate_id', $estimate_id)->get()->count();
        if ($total_estimate > 1) {
            $estimate_item->delete();
            session()->flash('success', 'Estimate Returned');
            return redirect()->route('estimate.edit', $estimate_id);
        } else {
            $estimate_item->delete();
            $estimate->forceDelete();
            session()->flash('success', 'Estimate Deleted');
            return redirect()->route('estimate.index');
        }
    }

    public function destroy(Estimate $estimate)
    {
        $estimate = Estimate::find($estimate->id);
        $estimate->delete();
        //estimate item delete
        $estimate_items = EstimateItem::where('estimate_id', $estimate->id)->get();
        foreach ($estimate_items as $estimate_item) {
            $estimate_item->delete();
        }
        if ($estimate->delete() && $estimate->items()->delete()) {
            $order_damage_items = OrderDamageItem::where('estimate_id', $estimate->id)->get();
            foreach ($order_damage_items as $order_damage_item) {
                $order_damage_item->delete();
            }
            $order_return_items = OrderReturnItem::where('estimate_id', $estimate->id)->get();
            foreach ($order_return_items as $order_return_item) {
                $order_return_item->delete();
            }
            session()->flash('success', 'Your Estimate has been deleted !');
        } else {
            session()->flash('warning', 'Estimate deleted but Order Damage Item and Order Return Item not found !');
        }
        return back();
    }
    public function convert_invoice(Estimate $estimate)
    {
        // $estimate       = $esti;
        $customers = Customer::latest()->get();
        $products = Product::orderBy('name')->paginate(10);
        // $delivery_methods = DeliveryMethod::all();
        return view('pages.estimate.convert-invoice', compact('products', 'customers', 'estimate'));
    }

    // public function today_delivery(Request $request)
    // {
    //     // dd($request->all());
    //     $estimates = new Estimate();
    //     $todaySale = Estimate::where('delivery_date', date('Y-m-d'))->get();
    //     $estimate = new Estimate();

    //     $estimates = $estimates->filter($request, $estimates);
    //     $estimates = $estimates->orderBy('delivery_date', 'desc');
    //     if (auth()->user()->hasRole('admin')) {
    //         $estimates = $estimates->with('customer')->whereDate('delivery_date', date('Y-m-d'))->where('convert_status', 0)
    //             ->orderBy('priority', 'asc')->get();
    //     } else {
    //         $estimates = $estimates->whereIn('brand_id', json_decode(auth()->user()->brand_id))->with('customer')
    //             ->whereDate('delivery_date', date('Y-m-d'))->where('convert_status', 0)
    //             ->orderBy('priority', 'asc')->get();
    //     }

    //     $customers = Customer::get();
    //     $products = Product::select('id', 'name', 'code')->get();
    //     return view('pages.estimate.today_delivery', compact('estimates', 'customers', 'products'))
    //         ->withEstimate($estimate)
    //         ->withTodaySale($todaySale);
    //     // ->withCustomers(Customer::all());
    // }

    public function today_delivery(Request $request)
    {
        $brands = json_decode(auth()->user()->brand_id, true);

        $estimatesQuery = Estimate::query();

        if (auth()->user()->hasRole('admin')) {
            $estimatesQuery->with(['customer', 'items.product'])->whereDate('delivery_date', date('Y-m-d'))->where('convert_status', 0);
        } else {
            $estimatesQuery->whereIn('brand_id', $brands)->with(['customer', 'items.product'])->whereDate('delivery_date', date('Y-m-d'))->where('convert_status', 0);
        }

        $estimates = $estimatesQuery->orderBy('delivery_date', 'desc')->get();

        // Preprocess items with readable quantities
        $estimates->each(function ($estimate) {
            $estimate->items->each(function ($item) {
                $product = $item->product;
                $item->readable_quantity = $product ? $product->readable_qty($item->qty) : null;
                $item->product_stock = $product ? $product->stock : null;
            });
        });

        $customers = Customer::get();

        return view('pages.estimate.today_delivery', compact('estimates', 'customers'));
    }

    public function delivery_complete($id)
    {
        $estimate = Estimate::find($id);
        $estimate->status = 1;
        $estimate->save();
        session()->flash('success', 'Estimate Delivery Complete');
        return back();
    }

    public function set_priority(Request $request)
    {
        // dd($request->all());
        $estimate = Estimate::find($request->estimate_id);
        $estimate->priority = $request->priority;
        $estimate->save();
        session()->flash('success', 'Estimate Priority Set');
        return back();
    }

    public function updateDeliveryBy(Request $request)
    {

        $estimate = Estimate::findOrFail($request->estimate_id);
        $estimate->delivery_by = $request->delivery_by;
        $estimate->save();

        return response()->json(['message' => 'Delivery person updated successfully.']);
    }
}
