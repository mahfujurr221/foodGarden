<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Customer;
use App\Damage;
use App\Estimate;
use App\EstimateItem;
use App\Expense;
use App\OrderReturn;
use App\Payment;
use App\Pos;
use App\PosItem;
use App\PosSetting;
use App\Product;
use App\Purchase;
use App\PurchaseItem;
use App\ReturnItem;
use App\Services\ProductService;
use App\Services\SummaryService;
use App\Supplier;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    public function paginate($items, $perPage = 20, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, ['path' => Paginator::resolveCurrentPath()]);
    }

    public function today_report()
    {
        Gate::authorize('today_report');

        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');

        $sells = Pos::where('sale_date', '>=', $start_date)->where('sale_date', '<=', $end_date)->get();
        $expenses = Expense::whereBetween('expense_date', [$start_date, $end_date])->get();
        $pos_items = ProductService::topSaleProductsDateToDate($start_date, $end_date);
        $payments_paid = Payment::where('payment_type', 'pay')->whereBetween('payment_date', [$start_date, $end_date])->get();
        $payments_received = Payment::where('payment_type', 'receive')->whereBetween('payment_date', [$start_date, $end_date])->get();
        // dd($payments_paid);
        return view('pages.reports.today', compact('sells', 'expenses', 'pos_items', 'payments_paid', 'payments_received'));
    }

    public function current_month_report()
    {
        Gate::authorize('current_month_report');
        $start_date = date('Y-m-1');
        $end_date = date('Y-m-t');

        $sells = Pos::where('sale_date', '>=', $start_date)->where('sale_date', '<=', $end_date)->get();
        $expenses = Expense::whereBetween('expense_date', [$start_date, $end_date])->get();
        $pos_items = ProductService::topSaleProductsDateToDate($start_date, $end_date);
        $payments_paid = Payment::where('payment_type', 'pay')->whereBetween('payment_date', [$start_date, $end_date])->get();
        $payments_received = Payment::where('payment_type', 'receive')->whereBetween('payment_date', [$start_date, $end_date])->get();
        return view('pages.reports.current_month', compact('sells', 'expenses', 'pos_items', 'payments_paid', 'payments_received'));
    }

    public function summary_report(Request $request)
    {
        Gate::authorize('summary_report');
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');

        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->end_date;
        }

        $sells = Pos::where('sale_date', '>=', $start_date)->where('sale_date', '<=', $end_date)->get();
        $expenses = Expense::whereBetween('expense_date', [$start_date, $end_date])->get();
        $top_sold_products = ProductService::topSaleProductsDateToDate($start_date, $end_date);
        $payments_paid = Payment::where('payment_type', 'pay')->whereBetween('payment_date', [$start_date, $end_date])->get();
        $payments_received = Payment::where('payment_type', 'receive')->whereBetween('payment_date', [$start_date, $end_date])->get();

        return view('pages.reports.summary_report', compact('sells', 'expenses', 'top_sold_products', 'payments_paid', 'payments_received', 'start_date', 'end_date'));
    }

    public function daily_report(Request $request)
    {
        Gate::authorize('daily_report');
        $start_date = date('Y-m-1');
        $end_date = date('Y-m-t');
        $brands = Supplier::where('status', 1)->select('id', 'name')->get();
        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->end_date;
        }

        return view('pages.reports.daily_report', compact('start_date', 'end_date', 'brands'));
    }

    public function customer_due(Request $request)
    {
        Gate::authorize('customer_due_report');
        $customers = Customer::query();

        if ($request->customer_id) {
            $csutomers = $customers->where('id', $request->customer_id);
        }

        $customers = $customers->where('total_receivable', '>', 0)->get();
        // $customers = $customers->sortByDesc(function ($customer, $key) {
        //     return $customer->total_due();
        // })->filter(function ($customer) {
        //     if ($customer->total_due() > 0) {
        //         return $customer;
        //     }
        // });

        // $customers = $this->paginate($customers);

        $filter_customers = Customer::select('id', 'name')->get();

        return view('pages.reports.customer_due', compact('customers', 'filter_customers'));
    }

    public function supplier_due(Request $request)
    {
        Gate::authorize('supplier_due_report');
        $suppliers = Supplier::query();

        if ($request->supplier_id) {
            $suppliers = $suppliers->where('id', $request->supplier_id);
        }

        $suppliers = $suppliers->where('total_payable', '>', 0)->get();

        $suppliers = $this->paginate($suppliers);

        $filter_suppliers = Supplier::where('status', 1)->select('id', 'name')->get();

        return view('pages.reports.supplier_due', compact('suppliers', 'filter_suppliers'));
    }

    public function low_stock(Request $request)
    {
        Gate::authorize('low_stock_report');
        $products = new Product();
        if ($request->brand != null) {
            $brand = $request->brand;
        }else{
            $brand = 1;
        }

        $low_stock_quantity = PosSetting::first()->low_stock;

        $products = Product::where('main_unit_stock', '<', $low_stock_quantity)->where('brand_id', $brand)->get();

        $data['products'] = $this->paginate($products);
        return view('pages.reports.low_stock', $data);
    }

    public function top_customer(Request $request)
    {
        Gate::authorize('top_customer_report');
        $customers = Customer::get();

        $start_date = $request->start_date ?? date('Y-01-01');
        $end_date = $request->end_date ?? date('Y-12-31');
        $brand_id=$request->brand_id??1;

        $customers = $customers->sortByDesc(function ($customer, $key) use ($start_date, $end_date,$brand_id) {
            return $customer->receivable($start_date, $end_date,$brand_id); 
        });

        if($request->customer_id){
            $customers = $customers->where('id', $request->customer_id);
        }

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['customers'] = $this->paginate($customers);
        $data['brands']=Supplier::where('status', 1)->select('id','name')->get();
        return view('pages.reports.top_customer', $data);
    }

    
    public function top_product(Request $request)
    {
        Gate::authorize('top_product_report');
        $products = Product::all();

        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');

        if ($request->start_date) {
            $start_date = $request->start_date;
        }

        if ($request->end_date) {
            $end_date = $request->end_date;
        }

        $products = $products->sortByDesc(function ($product, $key) use ($start_date, $end_date) {
            return $product->sell_count($start_date, $end_date);
        });

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        $data['products'] = $products;
        // $this->paginate($products);
        return view('pages.reports.top_products', $data);
    }

    public function top_product_all_time(Request $request)
    {
        Gate::authorize('top_product_all_time_report');
        $products = Product::orderBy('total_sold', 'desc')->select('id', 'name', 'code', 'main_unit_id', 'sub_unit_id')->get();
        // $this->paginate($products);
        return view('pages.reports.top_products_all_time', compact('products'));
    }
    public function purchase_report(Request $request)
    {
        Gate::authorize('purchase_report');
        // $data['items']=collect();

        $purchase_items = new PurchaseItem();
        if ($request->product_id) {
            $purchase_items = $purchase_items->where('product_id', $request->product_id);
        }

        if ($request->start_date) {
            $purchase_items = $purchase_items->whereHas('purchase', function ($purchase) use ($request) {
                $purchase->where('purchase_date', '>=', $request->start_date);
            });
        }

        if ($request->end_date) {
            $purchase_items = $purchase_items->whereHas('purchase', function ($purchase) use ($request) {
                $purchase->where('purchase_date', '<=', $request->end_date);
            });
        }

        $purchases = $purchase_items->paginate(20);

        return view('pages.reports.purchase_report', compact('purchases'));
    }

    public function customer_ledger(Request $request)
    {
        Gate::authorize('customer_ledger');
        $transactions = new Transaction();

        if ($request->customer_id) {
            $transactions = $transactions->where('customer_id', $request->customer_id);
        }

        if ($request->start_date) {
            $transactions = $transactions->where('date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $transactions = $transactions->where('date', '<=', $request->end_date);
        }

        $transactions = $transactions->get();

        return view('pages.reports.customer_ledger', compact('transactions'));
    }

    public function supplier_ledger(Request $request)
    {
        // dd('supplier_ledger');
        Gate::authorize('supplier_ledger');
        $transactions = new Transaction();
        if ($request->supplier_id) {
            $transactions = $transactions->where('supplier_id', $request->supplier_id);
        }

        if ($request->start_date) {
            $transactions = $transactions->where('date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $transactions = $transactions->where('date', '<=', $request->end_date);
        }

        $transactions = $transactions->get();

        return view('pages.reports.supplier_ledger', compact('transactions'));
    }

    public function profit_loss_report(Request $request)
    {
        Gate::authorize('profit_loss_report');

        if ($request->start_date) {
            $start_date = date('Y-m-d', strtotime($request->start_date . '-01'));
        } else {
            $start_date = Carbon::now()->startOfYear()->toDateString();
        }

        if ($request->end_date) {
            $end_date = date('Y-m-t', strtotime($request->end_date . '-01'));
        } else {
            $end_date = Carbon::now()->endOfYear()->toDateString();
        }

        $brands = Supplier::where('status', 1)->select('id', 'name')->get();

        return view('pages.reports.profit_loss_report', compact('start_date', 'end_date', 'brands'));
    }

    public function current_month_no_reload(Request $request)
    {
        Gate::authorize('current_month_report');

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $totalMonthlyOrderValue =
            Estimate::whereDate('estimate_date', '>=', $start_date)
            ->whereDate('estimate_date', '<=', $end_date)
            ->sum('final_receivable');
        $totalMonthlySold = PosItem::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->sum('sub_total');
        $totalMonthlyDamage = Damage::whereDate('date', '>=', $start_date)
            ->whereDate('date', '<=', $end_date)
            ->sum('qty');
        $totalMonthlyProfit = Pos::whereDate('sale_date', '>=', $start_date)
            ->whereDate('sale_date', '<=', $end_date)
            ->sum('profit');

        return response()->json([
            'totalMonthlyOrderValue' => number_format($totalMonthlyOrderValue, 0),
            'totalMonthlySold' =>   number_format($totalMonthlySold, 0),
            'totalMonthlyDamage' => number_format($totalMonthlyDamage, 0),
            'totalMonthlyProfit' => number_format($totalMonthlyProfit, 0),
        ]);
    }
    //supplier report no reload
    public function supplier_report(Request $request)
    {
        $supplierId = $request->supplier_id;
        $supplier_receivable = SummaryService::supplier_receivable($supplierId);
        $supplier_payable = SummaryService::supplier_payable($supplierId);
        return response()->json([
            'supplier_receivable' => number_format($supplier_receivable, 0),
            'supplier_payable' => number_format($supplier_payable, 0),
        ]);
    }

    public function total_report_no_reload(Request $request)
    {
        // dd($request->all());
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if ($start_date != null && $end_date != null) {
            $totalSold = Pos::where('sale_date', '>=', $start_date)->where('sale_date', '<=', $end_date)->sum('final_receivable');
            $totalPurchased = Purchase::where('purchase_date', '>=', $start_date)->where('purchase_date', '<=', $end_date)->sum('payable');
            $totalExpense = Expense::where('expense_date', '>=', $start_date)->where('expense_date', '<=', $end_date)->sum('amount');
            $totalReturned = OrderReturn::whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->sum('return_product_value');
            // $totalDamage=Damage::whereDate('date', '>=', $start_date)->whereDate('date', '<=', $end_date)->sum('qty');
            $totalProfit = Pos::where('sale_date', '>=', $start_date)->where('sale_date', '<=', $end_date)->sum('profit');
        } else {
            $totalSold = Pos::sum('final_receivable');
            $totalPurchased = Purchase::sum('payable');
            $totalExpense = Expense::sum('amount');
            $totalReturned = OrderReturn::sum('return_product_value');
            // $totalDamage=Damage::sum('qty');
            $totalProfit = Pos::sum('profit');
        }
        return response()->json([
            'totalSold' => number_format($totalSold, 0),
            'totalPurchased' => number_format($totalPurchased, 0),
            'totalExpense' => number_format($totalExpense, 0),
            'totalReturned' => number_format($totalReturned, 0),
            // 'totalDamage' => $totalDamage,
            'totalProfit' => number_format($totalProfit, 0),
        ]);
    }

    //brands 
    public function brands(Request $request)
    {
        $brands = Supplier::where('status', 1)->select('id', 'name')->get();
        return response()->json([
            'brands' => $brands
        ]);
    }

    // brand_report_no_reload
    public function brand_order(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $brandId = $request->brand_id;
        $brandOrder = SummaryService::brandOrder($brandId, $start_date, $end_date);
        return response()->json(number_format($brandOrder));
    }

    // brand_wise_product_report_no_reload
    public function brand_return(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $brandId = $request->brand_id;
        $brandReturn = SummaryService::brandReturn($brandId, $start_date, $end_date);
        return response()->json(number_format($brandReturn));
    }

    //brand damage 
    public function brand_damage(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $brandId = $request->brand_id;
        // Calculate the total cost by multiplying quantity with product's cost
        $brandDamage = SummaryService::brandDamage($brandId, $start_date, $end_date);
        return response()->json(number_format($brandDamage));
    }

    //brand_sell 
    public function brand_sell(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $brandId = $request->brand_id;
        $brandSell = SummaryService::brandSell($brandId, $start_date, $end_date);
        return response()->json(number_format($brandSell));
    }

    //brand_due
    public function brand_due(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $brandId = $request->brand_id;
        $brandDue = SummaryService::brandDue($brandId, $start_date, $end_date);
        return response()->json(number_format($brandDue));
    }

    //brand_collection 
    public function brand_collection(Request $request)
    {
        //brand sell-return-damage
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $brandId = $request->brand_id;
        //call summery service and get brand wise collection
        $brandCollection = SummaryService::brandCollection($brandId, $start_date, $end_date);
        return response()->json(number_format($brandCollection));
    }

    //brand_profit 
    public function brand_profit(Request $request)
    {
        // dd($request->all());
        //brand sell-return-damage
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $brandId = $request->brand_id;
        //call summery service and get brand wise collection
        $brandProfit = SummaryService::brandProfit($brandId, $start_date, $end_date);
        // dd($brandCollection);
        return response()->json(number_format($brandProfit));
    }
    public function brand_wise_product(Request $request)
    {
        $brandId = $request->brand_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $products = Estimate::join('estimate_items', 'estimates.id', '=', 'estimate_items.estimate_id')
            ->join('products', 'products.id', '=', 'estimate_items.product_id')
            ->where('products.brand_id', $brandId)
            ->whereBetween('estimates.estimate_date', [$startDate, $endDate])
            ->select(
                'estimate_items.product_id as id',
                'estimate_items.product_name as name',
                DB::raw('SUM(estimate_items.ordered_qty) as qty'),
                DB::raw('SUM(estimate_items.ordered_sub_total) as price'),
                DB::raw('SUM(estimate_items.discount_qty) as discount_qty')
            )
            ->groupBy('estimate_items.product_id')
            ->get();

        foreach ($products as $product) {
            $productModel = Product::find($product->id);
            if ($productModel) {
                $product->qty = $productModel->readable_qty($product->qty);
            }
        }
        return response()->json($products);
    }

    public function brand_wise_product_return(Request $request)
    {
        $brandId = $request->brand_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $products = Pos::join('pos_items', 'pos_items.pos_id', '=', 'pos.id')
            ->join('products', 'products.id', '=', 'pos_items.product_id')
            ->where('products.brand_id', $brandId)
            ->whereBetween('pos.sale_date', [$startDate, $endDate])
            ->select(
                'pos_items.product_id as id',
                'pos_items.product_name as name',
                DB::raw('SUM(pos_items.returned_qty) as returned'),
                DB::raw('SUM(pos_items.damage) as damage')
            )
            ->groupBy('pos_items.product_id')
            ->get();

        foreach ($products as $product) {
            $productModel = Product::find($product->id);
            if ($productModel) {
                $product->returned = $productModel->readable_qty($product->returned);
            }
        }

        return response()->json($products);
    }
    
    public function brand_discount(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $brandId = $request->brand_id;
        $brandDiscount = SummaryService::brandDiscount($brandId, $start_date, $end_date);
        return response()->json(number_format($brandDiscount));
    }
}
