<?php

namespace App\Http\Controllers;

use App\ActualPayment;
use App\Brand;
use App\Estimate;
use App\OrderReturn;
use App\OrderReturnItem;
use App\PaymentMethod;
use App\Pos;
use App\PosItem;
use App\Services\StockService;
use App\Services\TransactionService;
use App\Stock;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderReturnController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:create-return', ['only' => ['create', 'store']]);
        $this->middleware('can:edit-return', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-return', ['only' => ['destroy']]);
        $this->middleware('can:list-return', ['only' => ['index']]);
        // $this->middleware('can:show-customer', ['only' => ['show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $returns = new OrderReturn();

        if ($request->pos_id) {
            $returns = $returns->where('pos_id', $request->pos_id);
        }

        if ($request->customer != null) {
            $pos_ids = Pos::where('customer_id', $request->customer)->pluck('id');
            $returns = $returns->whereIn('pos_id', $pos_ids);
        }

        $returns = $returns->orderBy('id', 'DESC')->paginate(20);
        return view('pages.pos_return.index', compact("returns"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Pos $pos)
    {
        // if (!$pos->customer) {
        //     session()->flash('warning', 'Customer Not Found!');
        //     return redirect()->back();
        // }

        if ($pos->return) {
            session()->flash('warning', 'Already Returned!');
            return redirect()->back();
        }
        // dd($pos);
        return view('pages.pos_return.create', compact('pos'));
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
            'pos_id' => 'required',
            'return_product_value' => 'required',
            'calculated_discount' => 'required',
            'product_id' => 'required'
        ], [
            'product_id.required' => 'No Product Selected.'
        ]);

        $pos = Pos::find($request->pos_id);

        try {
            DB::beginTransaction();
            $return = OrderReturn::create([
                'pos_id' => $request->pos_id,
                'return_product_value' => $request->return_product_value,
                'calculated_discount' => $request->calculated_discount,
                'customer_id' => $pos->customer_id
            ]);

            if ($return) {
                // $return_items=[];


                foreach ($request->item_id as $key => $value) {
                    // dd($value);
                    $pos_item = PosItem::find($value);
                    $main_qty = 0;
                    $sub_qty = 0;
                    // since old_purchase_item-> find everything using id
                    if ($request->main_qty && array_key_exists($value, $request->main_qty)) {
                        $main_qty = $request->main_qty[$value];
                    }

                    if ($request->sub_qty && array_key_exists($value, $request->sub_qty)) {
                        $sub_qty = $request->sub_qty[$value];
                    }

                    $qty = $pos_item->product->to_sub_quantity($main_qty, $sub_qty);

                    if ($qty == 0) {
                        throw new \Exception('Cant return quantity zero');
                    }
                    // dd($qty);
                    if ($qty <= $pos_item->remaining_quantity()) {
                        $return_item = $return->items()->create([
                            'pos_item_id' => $value,
                            'product_id' => $pos_item->product_id,
                            'main_unit_qty' => $main_qty,
                            'sub_unit_qty' => $sub_qty,
                            'qty' => $qty,
                            'unit_price' => $request->unit_price[$value],
                            'total' => $request->price[$value]
                        ]);

                        StockService::handle_return_stock($pos_item, $return_item, $qty);
                    } else {
                        throw new \Exception('Returning more than remaining');
                    }
                }
            }
            // Handle Return Payment
            DB::commit();

            $return->pos->update_calculated_data();
            TransactionService::update_pos_transaction($return->pos);

            session()->flash('success', 'Your Return has been done!');
            return redirect()->route('pos.index');
        } catch (\Exception $e) {
            info($e);
            DB::rollback();

            $error_message = "Oops! Something Went Wrong!";

            if ($e->getMessage() == 'Cant return quantity zero') {
                $error_message = 'Cant return quantity zero';
            } elseif ($e->getMessage() == 'Returning more than remaining') {
                $error_message = 'Cant return more than remaining quantity';
            }

            session()->flash('warning', $error_message);

            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\OrderReturn  $orderReturn
     * @return \Illuminate\Http\Response
     */
    public function show(OrderReturn $orderReturn)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OrderReturn  $orderReturn
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderReturn $return)
    {
        // dd($return);
        $return->delete();
        session()->flash('success', 'Deleted Successfully!');
        return redirect()->back();
    }


    public function add_payment(OrderReturn $return)
    {
        return view('pages.pos_return.forms.add_payment', compact('return'));
    }

    public function store_payment(Request $request, OrderReturn $return)
    {

        $validator = Validator::make($request->all(), [
            "payment_date" => "required",
            "pay_amount" => [
                'required',
                function ($attribute, $value, $fail) use ($return, $request) {
                    // dd($value);
                    if ($return->payable_to_customer < $return->paid() + $request->pay_amount) {
                        return $fail('Over Payment not Alowed! Due is ' . $return->due() . ' Tk');
                    }
                }
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $actual_payment = ActualPayment::create([
            'payment_type' => 'pay',
            'customer_id' => $return->pos->customer_id,
            'amount' => $request->pay_amount,
            'date' => $request->payment_date,
            'note' => $request->note
        ]);

        $return->payments()->create([
            'payment_type' => 'pay',
            'actual_payment_id' => $actual_payment->id,
            'payment_date' => $request->payment_date,
            'pay_amount' => $request->pay_amount,
        ]);

        return response()->json(['success' => 'Payment Added Successfully.']);
    }

    //return order 
    public function return_order(Request $request)
    {
        // dd($request->brand_id);
        if ($request->brand_id == null) {
            $brand_id = 1;
        } else {
            $brand_id = $request->brand_id;
        }
        if ($request->start_date && $request->end_date) {
            $returns = OrderReturnItem::join('products', 'products.id', '=', 'order_return_items.product_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->select('order_return_items.*', 'brands.name as brand_name')
                ->where('order_return_items.qty', '>', 0)
                ->where('brands.id', $brand_id)
                ->whereBetween('order_return_items.created_at', [$request->start_date, $request->end_date])
                ->orderBy('order_return_items.id', 'desc')->paginate(20);
        } else {
            $returns = OrderReturnItem::join('products', 'products.id', '=', 'order_return_items.product_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->select('order_return_items.*', 'brands.name as brand_name')
                ->where('order_return_items.qty', '>', 0)
                ->where('brands.id', $brand_id)
                ->orderBy('order_return_items.id', 'desc')->paginate(20);
        }
        // dd($damages);
        $brands = Supplier::where('status', 1)->select('id', 'name')->get();
        return view('pages.pos_return.order-return', compact('returns', 'brands', 'brand_id'));
    }

}