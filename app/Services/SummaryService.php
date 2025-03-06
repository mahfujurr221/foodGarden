<?php

namespace App\Services;

use App\Customer;
use App\Damage;
use App\DamageReturn;
use App\Estimate;
use App\EstimateItem;
use App\Expense;
use App\OrderDamageItem;
use App\OrderReturn;
use App\OrderReturnItem;
use App\Payment;
use App\Pos;
use App\PosItem;
use App\Product;
use App\Purchase;
use App\PurchaseItem;
use App\ReturnItem;
use App\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SummaryService
{
    // public static function today_order()
    // {
    //     $today = now()->format('Y-m-d');
    //     $todayEstimates = EstimateItem::whereDate('created_at', $today)->get();
    //     $totalTodayEstimateValue = $todayEstimates->sum('sub_total');
    //     // $totalTodayEstimateQuantity = $todayEstimates->sum('main_unit_qty');
    //     return [
    //         'totalTodayEstimateValue' => $totalTodayEstimateValue,
    //         // 'totalTodayEstimateQuantity' => $totalTodayEstimateQuantity
    //     ];
    // }
    public static function today_order()
    {
        $today = today()->format('Y-m-d');
        $totalTodayOrder = Estimate::join('estimate_items', 'estimate_items.estimate_id', 'estimates.id')
            ->whereDate('estimates.estimate_date', $today)->sum('estimate_items.sub_total');
        // $totalTodayOrder = Pos::join('pos_items', 'pos_items.pos_id', 'pos.id')
        //     ->whereDate('pos.sale_date', $today)->sum('pos_items.ordered_sub_total');
        return $totalTodayOrder;
    }
    public static function today_sold()
    {
        $today = now()->format('Y-m-d');
        $totalTodaySold = Pos::join('pos_items', 'pos_items.pos_id', 'pos.id')
            ->whereDate('pos.sale_date', $today)->sum('pos_items.sub_total');
        return $totalTodaySold;
    }
    
    //today damage 
    public static function today_damage()
    {
        $today = now()->format('Y-m-d');
        return OrderDamageItem::join('estimates', 'estimates.id', '=', 'order_damage_items.estimate_id')
            ->whereDate('estimates.estimate_date', $today)->sum(DB::raw('order_damage_items.total'));
    }
    public static function today_profit()
    {
        return Pos::whereDate('sale_date', Carbon::today()->format('Y-m-d'))->sum('profit');
    }
    public static function monthly_order_sell_profit($start_date = null, $end_date = null)
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastDayOfMonth = Carbon::now()->endOfMonth();
        if (!$start_date && !$end_date) {
            $start_date = $currentMonth;
            $end_date = $lastDayOfMonth;
        }
        // Get the data from the database
        $monthlyData = [
            'totalMonthlyOrderValue' => intval(
                // PosItem::join('pos', 'pos_items.pos_id', '=', 'pos.id')
                //     ->whereDate('pos.sale_date', '>=', $start_date)
                //     ->whereDate('pos.sale_date', '<=', $end_date)
                //     ->sum('ordered_sub_total')
                EstimateItem::join('estimates', 'estimate_items.estimate_id', 'estimates.id')
                    ->whereDate('estimates.estimate_date', '>=', $start_date)
                    ->whereDate('estimates.estimate_date', '<=', $end_date)
                    ->sum('sub_total')
            ),
            'totalMonthlySold' => intval(
                PosItem::join('pos', 'pos_items.pos_id', '=', 'pos.id')
                    ->whereDate('pos.sale_date', '>=', $start_date)
                    ->whereDate('pos.sale_date', '<=', $end_date)
                    ->sum('sub_total')
            ),

            'totalMonthlyDamage' => intval(
                OrderDamageItem::join('products', 'order_damage_items.product_id', '=', 'products.id')
                    ->join('estimates', 'estimates.id', '=', 'order_damage_items.estimate_id')
                    ->whereDate('estimates.estimate_date', '>=', $start_date)
                    ->whereDate('estimates.estimate_date', '<=', $end_date)
                    ->sum(DB::raw('order_damage_items.total'))
            ),
            'totalMonthlyProfit' => intval(
                Pos::whereDate('sale_date', '>=', $start_date)
                    ->whereDate('sale_date', '<=', $end_date)
                    ->sum('profit')
            )
        ];
        // Calculate the total monthly profit
        return $monthlyData;
    }
    //monthly sale 
    public static function monthly_sold()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $totalMonthlySold = PosItem::where('created_at', '>=', $currentMonth)
            ->sum('sub_total');
        return $totalMonthlySold;
    }
    public static function monthly_profit()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastDayOfMonth = Carbon::now()->endOfMonth();
        $profit = 0;
        while ($currentMonth <= $lastDayOfMonth) {
            $profitData = self::sell_profit($currentMonth, $currentMonth);
            $profit += $profitData['profit'];
            $currentMonth->addDay();
        }
        return $profit;
    }

    public static function total_product_stock_price(array $productIds = [])
    {
        $total_stock = Product::sum('stock');
        $total_price = Product::select(DB::raw('SUM(stock*price) as total_price'))->first()->total_price ?? 0;
        return ['stock' => $total_stock, 'price' => $total_price];
    }
    public static function customer_receivable()
    {
        return Customer::sum('total_receivable');
    }

    public static function supplier_receivable($supplier_id = null)
    {
        if ($supplier_id) {
            $totalReceivable = Supplier::where('status', 1)->where('id', $supplier_id)->sum('total_receivable');
            $openingReceivable = Supplier::where('status', 1)->where('id', $supplier_id)->sum('opening_receivable');
            return $totalReceivable + $openingReceivable;
        }
        return Supplier::where('status', 1)->where('id', 1)->sum('total_receivable');
    }

    public static function total_receivable()
    {
        $customer_receivable = SummaryService::customer_receivable();
        $supplier_receivable = SummaryService::supplier_receivable();
        return $supplier_receivable + $customer_receivable;
    }
    public static function customer_payable()
    {
        return Customer::sum('total_payable');
    }

    public static function supplier_payable($supplier_id = null)
    {
        if ($supplier_id) {
            return Supplier::where('status', 1)->where('id', $supplier_id)->sum('total_payable');
        }
        return Supplier::where('status', 1)->sum('total_payable');
    }

    public static function total_payable()
    {
        $customer_payable = SummaryService::customer_payable();
        $supplier_payable = SummaryService::supplier_payable();
        return $customer_payable + $supplier_payable;
    }

    public static function sell_profit($start_date = null, $end_date = null, $brandId = null)
    {
        $profit = 0;
        $purchase_cost = 0;
        $sell_value = 0;

        $sells = new Pos();
        $purchase = new Purchase();
        $sells = $sells->where('brand_id', $brandId);
        if ($start_date && $end_date) {
            $sells->whereBetween('pos.sale_date', [$start_date, $end_date]);
        }
        $sell_value = $sells->sum('pos.final_receivable');
        $profit = $sells->sum('pos.profit');

        $purchase = $purchase->join('purchase_items', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->join('products', 'products.id', '=', 'purchase_items.product_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->where('products.brand_id', $brandId);
        if ($start_date && $end_date) {
            $purchase->whereBetween('purchases.purchase_date', [$start_date, $end_date]);
        }
        $purchase_cost = $purchase->sum('purchase_items.sub_total');

        return [
            'sell_value' => $sell_value,
            'purchase_cost' => $purchase_cost,
            'profit' => $profit
        ];
    }

    // Today Summary
    public static function stock_value()
    {
        $stock_value = Product::join('units', 'units.id', '=', 'products.main_unit_id')
            ->select(DB::raw('SUM(stock*(1/IFNULL(units.related_by, 1))*price) as sell_value'))->first();
        $purchase_cost = PurchaseItem::join('products', 'products.id', '=', 'purchase_items.product_id')
            ->join('units', 'units.id', '=', 'products.main_unit_id')
            ->select(DB::raw('SUM(remaining*(1/IFNULL(units.related_by, 1))*rate) as purchase_cost'))
            ->first();
        return [
            'total_purchase_value' => $purchase_cost->purchase_cost,
            'total_sell_value' => $stock_value->sell_value
        ];
    }
    // Date to Date Summary
    
    public static function sold($start_date = null, $end_date = null)
    {
        if ($start_date && $end_date) {
            $sale=Pos::where('sale_date', '>=', $start_date)->where('sale_date', '<=', $end_date)->sum('final_receivable');
            $damage=OrderDamageItem::join('products', 'order_damage_items.product_id', '=', 'products.id')
            ->join('estimates', 'estimates.id', '=', 'order_damage_items.estimate_id')
            ->whereDate('estimates.estimate_date', '>=', $start_date)
            ->whereDate('estimates.estimate_date', '<=', $end_date)
            ->sum(DB::raw('order_damage_items.total'));

            return $sale+$damage;
        }
        $sale=Pos::sum('final_receivable');
        $damage=OrderDamageItem::join('products', 'order_damage_items.product_id', '=', 'products.id')
            ->join('estimates', 'estimates.id', '=', 'order_damage_items.estimate_id')
            ->sum(DB::raw('order_damage_items.total'));
            return $sale+$damage;
    }

    public static function purchased($start_date = null, $end_date = null)
    {
        if ($start_date && $end_date) {
            return Purchase::where('purchase_date', '>=', $start_date)->where('purchase_date', '<=', $end_date)->sum('payable');
        }
        return Purchase::sum('payable');
    }

    public static function returned($start_date = null, $end_date = null)
    {
        if ($start_date && $end_date) {
            return OrderReturn::whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->sum('return_product_value');
        }
        return OrderReturn::sum('return_product_value');
    }

    public static function expenses($start_date = null, $end_date = null)
    {
        if ($start_date && $end_date) {
            return Expense::whereDate('expense_date', '>=', $start_date)->whereDate('expense_date', '<=', $end_date)->sum('amount');
        }
        return Expense::sum('amount');
    }

    public static function profit($start_date = null, $end_date = null)
    {
        $profit = Pos::query();
        if ($start_date && $end_date) {
            $profit = $profit->where('sale_date', '>=', $start_date)->where('sale_date', '<=', $end_date);
        }
        $profit = $profit->sum('profit');
        return $profit - static::expenses($start_date, $end_date);
    }

    public static function date_expense($date)
    {
        return Expense::where('expense_date', $date)->sum('amount');
    }

    public static function date_discount($date)
    {
        return Payment::where('payment_date', $date)->sum('discount');
    }
    public function payment_received($start_date = null, $end_date = null)
    {
        $payments = Payment::whereHasMorph('paymentable', [Pos::class])->where('payment_type', 'receive');

        if ($start_date && $end_date) {
            $payments->whereBetween('payment_date', [$start_date, $end_date]);
        }
        return $payments->sum('pay_amount');
    }

    ///////////////////////// Brand Summary /////////////////////////
    public static function brandOrder($brandId, $start_d = null, $end_d = null)
    {
        $query = Pos::join('pos_items', 'pos_items.pos_id', '=', 'pos.id')
        ->where('pos.brand_id', $brandId);
        if ($start_d && $end_d) {
            $query->whereBetween('pos.sale_date', [$start_d, $end_d]);
        } elseif (!$start_d && !$end_d) {
            $query->whereDate('pos.sale_date', Carbon::today()->format('Y-m-d'));
        }
        return $query->sum('pos_items.ordered_sub_total');
        
        // $query = Estimate::query();
        // $query->where('brand_id', $brandId);
        // if ($start_d && $end_d) {
        //     $query->whereBetween('estimate_date', [$start_d, $end_d]);
        // } elseif (!$start_d && !$end_d) {
        //     $query->whereDate('estimate_date', Carbon::today()->format('Y-m-d'));
        // }
        // return $query->sum('final_receivable');
    }
    public static function brandReturn($brandId, $start_date = null, $end_date = null)
    {
        $query = PosItem::join('products', 'pos_items.product_id', '=', 'products.id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->join('pos', 'pos.id', '=', 'pos_items.pos_id')
            ->where('products.brand_id', $brandId);
        if ($start_date && $end_date) {
            $query->whereBetween('pos.sale_date', [$start_date, $end_date]);
        } elseif (!$start_date && !$end_date) {
            $query->whereDate('pos.sale_date', Carbon::today()->format('Y-m-d'));
        }
        return $query->sum('pos_items.returned_value');
    }

    public static function brandDamage($brandId, $start_date = null, $end_date = null)
    {
        $query = PosItem::join('products', 'pos_items.product_id', '=', 'products.id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->join('pos', 'pos.id', '=', 'pos_items.pos_id')
            ->where('products.brand_id', $brandId);
        if ($start_date && $end_date) {
            $query->whereBetween('pos.sale_date', [$start_date, $end_date]);
        } elseif (!$start_date && !$end_date) {
            $query->whereDate('pos.sale_date', Carbon::today()->format('Y-m-d'));
        }
        return $query->sum('pos_items.damaged_value');
    }

    public static function brandSell($brandId, $start_date = null, $end_date = null)
    {
        $query = Pos::query();
        if ($start_date && $end_date) {
            $query->whereBetween('sale_date', [$start_date, $end_date]);
        } elseif (!$start_date && !$end_date) {
            $query->whereDate('sale_date', Carbon::today()->format('Y-m-d'));
        }
        $query->whereHas('items', function ($q) use ($brandId) {
            $q->join('products', 'products.id', '=', 'pos_items.product_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->where('products.brand_id', $brandId);
        });
        return $query->sum('final_receivable');
    }
    //brand due 
    public static function brandDue($brandId, $start_date = null, $end_date = null)
    {
        $query = Pos::query();
        if ($start_date && $end_date) {
            $query->whereBetween('sale_date', [$start_date, $end_date]);
        } elseif (!$start_date && !$end_date) {
            $query->whereDate('sale_date', Carbon::today()->format('Y-m-d'));
        }
        $query->whereHas('items', function ($q) use ($brandId) {
            $q->join('products', 'products.id', '=', 'pos_items.product_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->where('products.brand_id', $brandId);
        });
        return $query->sum('due');
    }
    public static function brandCollection($brandId, $start_d = null, $end_d = null)
    {
        //brandSell-brandDue
        $sell = self::brandSell($brandId, $start_d, $end_d);
        $due = self::brandDue($brandId, $start_d, $end_d);
        return $sell - $due;
    }
    //brand sell purchase cost 
    public static function brandPurchaseCost($brandId, $start_d = null, $end_d = null)
    {
        $query = PosItem::join('products', 'products.id', '=', 'pos_items.product_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->join('units', 'units.id', '=', 'products.main_unit_id')
            ->join('pos', 'pos.id', '=', 'pos_items.pos_id')
            ->where('products.brand_id', $brandId);
        if ($start_d && $end_d) {
            $query->whereBetween('pos.sale_date', [$start_d, $end_d]);
        } elseif (!$start_d && !$end_d) {
            $query->whereDate('pos.sale_date', Carbon::today()->format('Y-m-d'));
        }
        return $query->sum(DB::raw('(qty-damage) * (products.cost/IFNULL(units.related_by, 1))'));
    }

    public static function brandProfit($brandId, $start_date = null, $end_date = null)
    {
        $query = Pos::query();
        if ($start_date && $end_date) {
            $query->whereBetween('sale_date', [$start_date, $end_date]);
        } elseif (!$start_date && !$end_date) {
            $query->whereDate('sale_date', Carbon::today()->format('Y-m-d'));
        }
        $query->whereHas('items', function ($q) use ($brandId) {
            $q->join('products', 'products.id', '=', 'pos_items.product_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->where('products.brand_id', $brandId);
        });
        return $query->sum('profit');
    }
    
    public static function brandDiscount($brandId, $start_date = null, $end_date = null)
    {
        $query = Pos::query();
        if ($start_date && $end_date) {
            $query->whereBetween('sale_date', [$start_date, $end_date]);
        } elseif (!$start_date && !$end_date) {
            $query->whereDate('sale_date', Carbon::today()->format('Y-m-d'));
        }

        return $query->where('brand_id', $brandId)->sum('discount');
    }
}
