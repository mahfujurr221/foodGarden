<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Damage;
use App\DamageReturn;
use App\Estimate;
use App\OrderDamageItem;
use App\Product;
use App\Services\StockService;
use App\Stock;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DamageController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create-damage', ['only' => ['create', 'store']]);
        $this->middleware('can:edit-damage', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-damage', ['only' => ['destroy']]);
        $this->middleware('can:list-damage', ['only' => ['index']]);
        // $this->middleware('can:show-customer', ['only' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $damages = new Damage();
        if ($request->product) {
            $damages = $damages->where('product_id', $request->product);
        }

        if ($request->id) {
            $damages = $damages->where('id', $request->id);
        }

        $damages = $damages->orderBy('date', 'desc')->paginate(10);
        $products = Product::select('id', 'name', 'code')->get();
        return view('pages.damage.index', compact('damages', 'products'));
    }
    public function damage_products(Request $request)
    {
        $data = [];

        if ($request->ajax()) {
            $products = new Product();

            if ($request->code != null) {
                $products = $products->where('name', 'like', '%' . $request->code . '%')->orWhere('code', 'like', '%' . $request->code . '%');
            }

            if ($request->category != null) {
                $products = $products->where('brand_id', $request->category);
                $data['category_id'] = $request->category;
            }
            $data['products'] = $products->orderBy('name')->paginate(10);
            return view('pages.damage.products', $data)->render();
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $products = new Product();

        if ($request->code != null) {
            $products = $products->where('code', 'like', '%' . $request->code . '%');
        }

        if (auth()->user()->hasRole('admin')) {
            $products = $products->orderBy('name')->paginate(10);
        } else {
            $products = $products->where('brand_id', auth()->user()->brand_id)->orderBy('name')->paginate(10);
        }
        return view('pages.damage.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * */

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'product_id' => 'required',
            'date' => 'required',
            'main_unit_qty' => 'nullable|integer',
            'sub_unit_qty' => 'nullable|integer',
            'note' => 'nullable',
        ]);

        try {
            DB::beginTransaction();
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
                    if ($main_qty == 0 && $sub_qty == 0) {
                        throw new \Exception('Quantity Empty');
                    }
                    $product = Product::find($request->product_id[$key]);
                    $qty = $product->to_sub_quantity($main_qty, $sub_qty);
                    // dd($qty);

                    // dd($request->damage_type[$key]);
                    if ($request->damage_type[$key] == 'product') {
                        $purchase_distribution = StockService::return_purchase_ids_and_qty_for_the_sell($request->product_id[$key], $qty, $qty);
                        if (isset($purchase_distribution['purchase_items'])) {
                            $damage = Damage::create([
                                'product_id' => $request->product_id[$key],
                                'main_unit_qty' => $main_qty,
                                'date' => $request->date,
                                'note' => $request->note,
                                'sub_unit_qty' => $sub_qty,
                                'qty' => $qty,
                            ]);
                            foreach ($purchase_distribution['purchase_items'] as $pd_key => $pd_value) {
                                // insert into Stock Table
                                $damage->stock()->create([
                                    'purchase_id' => $pd_value['purchase_id'],
                                    'purchase_item_id' => $pd_value['purchase_item_id'],
                                    'product_id' => $request->product_id[$key],
                                    'qty' => $pd_value['qty']
                                ]);
                            }
                        } else {
                            throw new \Exception('Low Stock');
                        }
                    }
                }
            }
            DB::commit();
            session()->flash('success', 'Damage Added');
            return back();
            // return redirect()->route('damage.index');
            // return back();
        } catch (\Exception $e) {
            DB::rollback();
            info($e);
            // dd($e->getMessage());
            if ($e->getMessage() == "Quantity Empty") {
                session()->flash('warning', 'Please enter product quantity properly.');
            } elseif ($e->getMessage() == "Low Stock") {
                session()->flash('warning', 'Some Products Does not Have stock!');
                return back();
            }
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Damage  $damage
     * @return \Illuminate\Http\Response
     */
    public function show(Damage $damage)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Damage  $damage
     * @return \Illuminate\Http\Response
     */
    public function edit(Damage $damage)
    {
        // dd($damage);
        return view('pages.damage.edit', compact('damage'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Damage  $damage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Damage $damage)
    {
        $request->validate([
            'product_id' => 'required',
            'qty' => 'required',
            'date' => 'required',
        ]);
        $damage->update([
            'product_id' => $request->product_id,
            'qty' => $request->qty,
            'date' => $request->date,
            'note' => $request->note,
        ]);
        session()->flash('success', 'Damage Updated');
        return redirect()->back();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Damage  $damage
     * @return \Illuminate\Http\Response
     */
    public function destroy(Damage $damage)
    {
        // $damage->stock()->delete();
        $damage->delete();
        session()->flash('success', 'Damage Deleted');
        return redirect()->route('damage.index');
    }

    //order damage
    public function damage_from_order(Request $request)
    {
        if ($request->brand_id == null) {
            $brand_id = 1;
        } else {
            $brand_id = $request->brand_id;
            // dd($brand_id);
        }

        if ($request->start_date && $request->end_date) {
            $damages = OrderDamageItem::join('products', 'products.id', '=', 'order_damage_items.product_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->where('order_damage_items.collection_status', '!=', '1')
                ->where('order_damage_items.qty', '>', 0)
                ->where('brands.id', $brand_id)
                ->where('order_damage_items.product_id', $request->product_id)
                ->groupBy('order_damage_items.product_id', DB::raw('DATE(order_damage_items.created_at)'))
                ->select(
                    'order_damage_items.*',
                    'brands.name as brand_name',
                    DB::raw('SUM(order_damage_items.qty) as damage_qty'),
                    DB::raw('SUM(order_damage_items.total) as damage_total')
                )
                ->whereBetween('order_damage_items.created_at', [$request->start_date, $request->end_date])
                ->orderBy('order_damage_items.id', 'desc')->paginate(20);
        }

        if ($request->product_id != '') {
            $damages = OrderDamageItem::join('products', 'products.id', '=', 'order_damage_items.product_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->where('order_damage_items.collection_status', '!=', '1')
                ->where('order_damage_items.qty', '>', 0)
                ->where('brands.id', $brand_id)
                ->where('order_damage_items.product_id', $request->product_id)
                ->groupBy('order_damage_items.product_id', DB::raw('DATE(order_damage_items.created_at)'))
                ->select(
                    'order_damage_items.*',
                    'brands.name as brand_name',
                    DB::raw('SUM(order_damage_items.qty) as damage_qty'),
                    DB::raw('SUM(order_damage_items.total) as damage_total')
                )
                ->orderBy('order_damage_items.id', 'desc')->paginate(20);
        } else {
            $damages = OrderDamageItem::join('products', 'products.id', '=', 'order_damage_items.product_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->where('order_damage_items.collection_status', '!=', '1')
                ->where('brands.id', $brand_id)
                ->groupBy('order_damage_items.product_id')
                ->groupBy('order_damage_items.product_id', DB::raw('DATE(order_damage_items.created_at)'))
                ->select(
                    'order_damage_items.*',
                    'brands.name as brand_name',
                    DB::raw('SUM(order_damage_items.qty) as damage_qty'),
                    DB::raw('SUM(order_damage_items.total) as damage_total')
                )
                ->orderBy('order_damage_items.id', 'desc')->paginate(20);
        }

        $all_damage_id = OrderDamageItem::join('products', 'products.id', '=', 'order_damage_items.product_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->where('order_damage_items.collection_status', '!=', '1')
            ->select('order_damage_items.id')
            ->where('brands.id', $brand_id)
            ->orderBy('order_damage_items.id', 'desc')->get();

        // dd($damages);
        $brands = Supplier::select('id', 'name')->where('status', 1)->get();
        $brand_name = Supplier::where('status', 1)->find($brand_id)->name;
        return view('pages.damage.order-damage', compact('damages', 'brands', 'brand_id', 'brand_name', 'all_damage_id'));
    }

    public function order_damage_adjust(Request $request)
    {
        // dd($request->damage_id);
        foreach ($request->damage_id as $key => $value) {
            $damage = OrderDamageItem::find($value);
            $damage->collection_status = 1;
            $damage->adjust_date = date('Y-m-d');
            $damage->save();
        }
        session()->flash('success', 'Order Damage Adjusted');
        return back();
    }
    public function adjusted_damages(Request $request)
    {
        // dd($request->brand_id);
        if ($request->brand_id == null) {
            $brand_id = 1;
        } else {
            $brand_id = $request->brand_id;
            // dd($brand_id);
        }
        if ($request->start_date && $request->end_date) {
            $damages = OrderDamageItem::join('products', 'products.id', '=', 'order_damage_items.product_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->where('order_damage_items.collection_status', '!=', '0')
                ->where('order_damage_items.qty', '>', 0)
                ->where('brands.id', $brand_id)
                ->where('order_damage_items.product_id', $request->product_id)
                ->groupBy('order_damage_items.product_id', DB::raw('DATE(order_damage_items.created_at)'))
                ->select(
                    'order_damage_items.*',
                    'brands.name as brand_name',
                    DB::raw('SUM(order_damage_items.qty) as damage_qty'),
                    DB::raw('SUM(order_damage_items.total) as damage_total')
                )
                ->whereBetween('order_damage_items.created_at', [$request->start_date, $request->end_date])
                ->orderBy('order_damage_items.id', 'desc')->paginate(20);
        } else {
            $damages = OrderDamageItem::join('products', 'products.id', '=', 'order_damage_items.product_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->where('order_damage_items.collection_status', '!=', '0')
                ->where('brands.id', $brand_id)
                ->groupBy('order_damage_items.product_id')
                ->groupBy('order_damage_items.product_id', DB::raw('DATE(order_damage_items.created_at)'))
                ->select(
                    'order_damage_items.*',
                    'brands.name as brand_name',
                    DB::raw('SUM(order_damage_items.qty) as damage_qty'),
                    DB::raw('SUM(order_damage_items.total) as damage_total')
                )
                ->orderBy('order_damage_items.id', 'desc')->paginate(20);
        }
        // dd($damages);
        $brands = Supplier::select('id', 'name')->where('status', 1)->get();
        $brand_name = Supplier::where('status', 1)->find($brand_id)->name;
        return view('pages.damage.adjusted-damage', compact('damages', 'brands', 'brand_id', 'brand_name'));
    }
}
