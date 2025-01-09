@extends('layouts.master')
@section('title', 'Dashboard')
{{-- this query is for brand wise sales report --}}
@php
$brands = \App\Brand::whereIn('id', json_decode(auth()->user()->brand_id))->get();

$return_products = \App\PosItem::join('products', 'products.id', '=', 'pos_items.product_id')
->where('pos_items.created_at', '>=', date('Y-m-d') . ' 00:00:00')
->where('products.brand_id', json_decode(auth()->user()->brand_id))
->select('pos_items.*', DB::raw('SUM(pos_items.returned_qty) as returned'), DB::raw('SUM(pos_items.discount_return) as
discount_return'), DB::raw('SUM(pos_items.damage) as damage'))
->groupBy('pos_items.product_id')
->having('returned', '>', 0)
->get();

// $products = \App\EstimateItem::join('products', 'products.id', '=', 'estimate_items.product_id')
// ->join('estimates', 'estimates.id', '=', 'estimate_items.estimate_id')
// ->whereBetween('estimates.estimate_date', [date('Y-m-d'), date('Y-m-d')])
// ->where('products.brand_id', json_decode(auth()->user()->brand_id))
// ->select('estimate_items.*', DB::raw('SUM(estimate_items.ordered_qty) as qty'),
// DB::raw('SUM(estimate_items.ordered_sub_total)
// as price'), DB::raw('SUM(estimate_items.discount_qty) as discount_qty'))
// ->groupBy('estimate_items.product_id')
// ->get();

$products = \App\EstimateItem::join('products', 'products.id', '=', 'estimate_items.product_id')
    ->join('estimates', 'estimates.id', '=', 'estimate_items.estimate_id')
    ->whereBetween('estimates.delivery_date', [date('Y-m-d', strtotime('+1 day')), date('Y-m-d', strtotime('+1 day'))])
    ->where('products.brand_id', json_decode(auth()->user()->brand_id))
    ->select('estimate_items.*', DB::raw('SUM(estimate_items.ordered_qty) as qty'),
             DB::raw('SUM(estimate_items.ordered_sub_total) as price'), 
             DB::raw('SUM(estimate_items.discount_qty) as discount_qty'))
    ->groupBy('estimate_items.product_id')
    ->get();

@endphp


@section('content')
@can('dashboard')
@canany(['today_sold', 'today_damage', 'today_collection'])
{{-- Daily Summary Report --}}
<div class="card col-12 containing-card ">
    <div class="card-header ">
        <h3 class="card-title ">Today Summary</h3>
    </div>
    <div class="card-body">
        <div class="grid-of-4">
            @php
            $summary = new \App\Services\SummaryService();
            $today_sell = $summary::sell_profit(date('Y-m-d'), date('Y-m-d'));
            @endphp
            @can('today_sold')
            <div class="card card-body bg-success">
                <h6 class="text-white text-uppercase">Today Order</h6>
                <p class="fs-18 fw-700">৳ {{ number_format($summary->today_order()) }}</p>
                {{-- <p class="fs-14 fw-500">Total Quantity: {{ $todayOrderData['totalTodayEstimateQuantity'] }}</p>
                --}}
            </div>
            @endcan
            @can('today_sold')
            <div class="card card-body bg-success">
                <h6 class="text-white text-uppercase">Today Sold</h6>
                <p class="fs-18 fw-700">৳ {{ number_format($summary->today_sold()+$summary->today_damage()) }}</p>
            </div>
            @endcan
            @can('today_damage')
            <div class="card card-body bg-success">
                <h6 class="text-white text-uppercase">Today Damage</h6>
                <p class="fs-18 fw-700">৳ {{ number_format($summary->today_damage()) }}</p>
            </div>
            @endcan
            @can('today_collection')
            <div class="card card-body bg-success">
                <h6 class="text-white text-uppercase">Today Profit</h6>
                <p class="fs-18 fw-700">৳ {{ number_format($summary->today_profit()) }}</p>
            </div>
            @endcan
        </div>
    </div>
</div>
{{-- End Daily report --}}
@endcanany


{{-- Brand Wise Report --}}
@can('total_sold')
<div class="card col-12 containing-card print_area">
    <div class="card-header">
        <h3 class="card-title">Todays Sales Report</h3>
        <div class="filter-form print_hidden">
            <div class="form-row align-items-center">
                <div class="form-group col-md-2 col-sm-3 col-3">
                    <input type="date" name="tstart_date" id="b_start_date" class="form-control form-control-sm"
                        value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group col-md-2 col-sm-3 col-3">
                    <input type="date" name="tend_date" id="b_end_date" class="form-control form-control-sm"
                        value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group col-md-1 mr-3 col-sm-2 col-2">
                    <button type="submit" class="btn btn-primary btn-sm brandWiseFilter">Filter</button>
                </div>
                <div class="form-group col-md-2 ml-2 col-sm-2 col-2">
                    <button type="submit" class="btn btn-danger btn-sm brandWiseReset">Reset</button>
                </div>
                <div class="form-group col-md-4">
                    <div class="btn-group" role="group">
                        <p class="btn btn-sm btn-outline-secondary" id="b_day_7">7
                            Days</p>
                        <p class="btn btn-sm btn-outline-secondary smHidden" id="b_day_15">15
                            Days</p>
                        <p class="btn btn-sm btn-outline-secondary" id="b_day_m">Month</p>
                        <p class="btn btn-sm btn-outline-secondary" value="year" id="b_day_y">Year</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped table-hover table-responsive-sm">
            @php
            if(auth()->user()->hasRole('admin')){
            $brands = \App\Brand::get();
            }else{
            $brands = \App\Brand::whereIn('id', json_decode(auth()->user()->brand_id))->get();
            }
            $summary = new \App\Services\SummaryService();
            @endphp
            <thead class="bg-primary">
                <tr class="text-center" style="text-align:center !important">
                    <th>Name</th>
                    <th>Order</th>
                    <th>Return</th>
                    <th>Damage</th>
                    <th>Discount</th>
                    <th>Net Sale</th>
                    <th>Due</th>
                    <th>Collection</th>
                    <th>Profit</th>
                    <th>Profit(%)</th>
                </tr>
            </thead>
            <tbody id="brand_report">
                @foreach ($brands as $brand)
                <tr>
                    <td class="bg-primary">{{ $brand->name }}</td>
                    <td id="brand_order">{{ number_format($summary::brandOrder($brand->id)) }}/-</td>

                    <td id="brand_return">{{ number_format($summary::brandReturn($brand->id)) }}/-</td>
                    <td id="brand_damage">{{ number_format($summary::brandDamage($brand->id)) }} /-</td>
                    <td id="brand_discount">{{ number_format($summary::brandDiscount($brand->id)) }}/-</td>
                    <td id="brand_sell">{{ number_format($summary::brandSell($brand->id)) }}/-</td>
                    <td id="brand_due">{{ number_format($summary::brandDue($brand->id)) }}/-</td>
                    <td id="brand_collection">{{ number_format($summary::brandCollection($brand->id)) }}/-
                    </td>
                    <td id="brand_profit">{{ number_format($summary::brandProfit($brand->id)) }}/-</td>

                    <td id="brand_profit_percent">{{
                        $summary::brandSell($brand->id)!=0?number_format($summary::brandProfit($brand->id)/$summary::brandSell($brand->id)*100,2):0
                        }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endcan

@can('today_order')
{{-- Todays Order Report --}}
<div id="printable-content2" class="card col-12 containing-card print_area">
    <div class="card-header">
        <h3 class="card-title" style="font-size: 20px; font-weight: bold; color: black;">Todays Order<span
                class="o_brand_name"></span></h3>
        <span class="today_date" style="font-size: 16px; font-weight: bold; color: black;"></span>
        <div class="filter-form print_hidden">
            <div class="form-row d-flex justify-content-center">
                <div class="form-group col-md-3 col-sm-4 col-4">
                    <select name="brand_id" id="brandId" class="form-control form-control-sm">
                        <option value="">Select Brand</option>
                        @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" data-brand="{{ $brand->name }}">
                            {{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4 col-4">
                    <input type="date" name="start_date3" id="b_w_p_start_date" class="form-control form-control-sm"
                        value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group col-md-2 col-sm-4 col-4">
                    <input type="date" name="end_date3" id="b_w_p_end_date" class="form-control form-control-sm"
                        value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group col-md-1 col-sm-2 col-2">
                    <button class="btn btn-primary btn-sm b_w_p_filter">Filter</button>
                </div>
                <div class="form-group col-md-2 ml-2 col-sm-2 col-2">
                    <button type="submit" class="btn btn-danger btn-sm b_w_p_Reset">Reset</button>
                </div>
                <div class="form-group col-md-1 ml-1 col-sm-2 col-2">
                    <button class="btn btn-success btn-sm print_hidden" onclick="printContent2()">Print</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead class="bg-primary">
                    <tr class="text-center" style="text-align:center !important">
                        <th>Product</th>
                        <th>Quantity</th>
                        {{-- <th>Discount/Free</th> --}}
                        <th>Price</th>
                    </tr>
                </thead>
                @php
                $total_price=0;
                @endphp
                <tbody id="brandWiseProductReport">
                    @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->product_name }}</td>
                        <th>{{ $product->product->readable_qty($product->qty) }}</th>
                        {{-- <td>{{ $product->discount_qty }} Pc</td> --}}
                        <td>{{ number_format($product->price) }}/-</td>
                    </tr>
                    @php
                    $total_price+=$product->price;
                    @endphp
                    @endforeach
                </tbody>
                <tfoot class="totalProductPrice">
                    <tr>
                        <th colspan="2" class="text-right">Total</th>
                        <th><span id="totalProductPrice"></span>{{ $total_price }} /-</th>
                    </tr>
                </tfoot>
            </table>

        </div>
    </div>
</div>

{{-- Todays Return Report --}}
<div id="printable-content1" class="card col-12 containing-card print_area">
    <div class="card-header">
        <h3 class="card-title" style="font-size: 20px; font-weight: bold; color: black;">Todays Return<span
                class="r_brand_name"></span></h3>
        <span class="today_date" style="font-size: 18px; font-weight: bold; color: black;"></span>

        <div class="filter-form print_hidden">
            <div class="form-row d-flex justify-content-center">
                <div class="form-group col-md-3 col-sm-4 col-4">
                    <select name="brand_id" class="form-control form-control-sm" id="rBrandId">
                        <option value="">Select Brand</option>
                        @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" data-brand="{{ $brand->name }}">
                            {{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4 col-4">
                    <input type="date" name="start_date4" id="b_w_p_r_start_date" class="form-control form-control-sm"
                        value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group col-md-2 col-sm-4 col-4">
                    <input type="date" name="end_date4" id="b_w_p_r_end_date" class="form-control form-control-sm"
                        value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group col-md-1 col-sm-2 col-2">
                    <button class="btn btn-primary btn-sm b_w_p_r_filter">Filter</button>
                </div>
                <div class="form-group col-md-2 ml-2 col-sm-2 col-2">
                    <button type="submit" class="btn btn-danger btn-sm b_w_p_r_Reset">Reset</button>
                </div>
                <div class="form-group col-md-1 ml-1 col-sm-2 col-2">
                    <button class="btn btn-success btn-sm print_hidden" onclick="printContent1()">Print</button>
                </div>
            </div>
        </div>

    </div>
    <div class="card-body">
        <div class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead class="bg-primary">
                    <tr class="text-center" style="text-align:center !important">
                        <th>Product</th>
                        <th>Return</th>
                        {{-- <th>Return Discount</th> --}}
                        <th>Damage</th>
                    </tr>
                </thead>
                @if ($return_products->count() > 0)
                <tbody id="brandWiseProductReturn">
                    @foreach ($return_products as $product)
                    <tr>
                        <td>{{ $product->product_name }}</td>
                        <th>{{ $product->product->readable_qty($product->returned) }}</th>
                        {{-- <td>{{ $product->discount_return }} Pc</td> --}}
                        <td>{{ $product->damage }} pc</td>
                    </tr>
                    @endforeach
                </tbody>
                @else
                <tbody>
                    <tr>
                        <td colspan="3" class="text-center text-danger">No Data Found</td>
                    </tr>
                </tbody>
                @endif
            </table>
        </div>
    </div>
</div>


@endcan

@canany(['today_sold', 'today_damage', 'today_collection'])
{{-- Monthly Summary Report --}}
<div class="card col-12 containing-card">
    <div class="card-header">
        <h3 class="card-title">Monthly Summary</h3>
        <div class="filter-form">
            <div class="form-row align-items-center">
                <div class="form-group col-md-2 col-sm-3 col-3">
                    <input type="date" name="mstart_date" id="m_start_date" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-2 col-sm-3 col-3">
                    <input type="date" name="mend_date" id="m_end_date" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-1 mr-3 col-sm-2 col-1">
                    <button type="submit" class="btn btn-primary btn-sm montylyFilter">Filter</button>
                </div>
                <div class="form-group col-md-2 col-sm-2 col-2">
                    <button type="submit" class="btn btn-danger btn-sm montylyReset">Reset</button>
                </div>
                <div class="form-group col-md-4 col-sm-4 col-4">
                    <div class="btn-group" role="group">
                        <p class="btn btn-sm btn-outline-secondary" id="m_day_7">7
                            Days</p>
                        <p class="btn btn-sm btn-outline-secondary smHidden" id="m_day_15">15
                            Days</p>
                        <p class="btn btn-sm btn-outline-secondary" id="m_day_m">Month</p>
                        <p class="btn btn-sm btn-outline-secondary" value="year" id="m_day_y">Year</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="grid-of-4">
            @php
            $summary = new \App\Services\SummaryService();
            $filter = request()->input('filter');
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            $monthlyData = $summary->monthly_order_sell_profit($start_date, $end_date);
            @endphp
            @can('today_sold')
            @if (isset($monthlyData['totalMonthlyOrderValue']))
            <div class="card card-body bg-pink">
                <h6 class="text-white text-uppercase">Monthly Order - {{ date('M Y') }}</h6>
                <p class="fs-18 fw-700">৳ <span id="m_order">{{ number_format($monthlyData['totalMonthlyOrderValue'])
                        }}</span>
                </p>
            </div>
            @endif
            @endcan
            @can('today_sold')
            @if (isset($monthlyData['totalMonthlySold']))
            <div class="card card-body bg-pink">
                <h6 class="text-white text-uppercase">Monthly Sold - {{ date('M Y') }}</h6>
                <p class="fs-18 fw-700">৳ <span id="m_sold">{{
                        number_format($monthlyData['totalMonthlySold']+$monthlyData['totalMonthlyDamage']) }}</span></p>
            </div>
            @endif
            @endcan

            @can('today_damage')
            @if (isset($monthlyData['totalMonthlyDamage']))
            <div class="card card-body bg-pink">
                <h6 class="text-white text-uppercase">Monthly Damage - {{ date('M Y') }}</h6>
                <p class="fs-18 fw-700">৳ <span id="m_damage">{{ number_format($monthlyData['totalMonthlyDamage'])
                        }}</span></p>
            </div>
            @endif
            @endcan
            @can('today_collection')
            @if (isset($monthlyData['totalMonthlyProfit']))
            <div class="card card-body bg-pink">
                <h6 class="text-white text-uppercase">Monthly Profit - {{ date('M Y') }}</h6>
                <p class="fs-18 fw-700">৳
                    <span id="m_profit">{{ number_format($monthlyData['totalMonthlyProfit'])}}</span>
            </div>
            @endif
            @endcan
        </div>
    </div>
</div>
{{-- End Daily report --}}
@endcanany
@canany(['total_receivable', 'total_payable'])
<div class="card col-12 containing-card">
    <div class="card-header">
        <h3 class="card-title">Due/Pay (Supplier)</h3>
        <div class="filter-form">
            <div class="form-row">
                <div class="form-group col-md-2 col-sm-4 col-4">
                    <input type="date" name="dstart_date" id="dstart_date" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-2 col-sm-4 col-4">
                    <input type="date" name="dend_date" id="dend_date" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-4 col-sm-4 col-4">
                    <select name="supplier_id" id="supplier_id" class="form-control form-control-sm">
                        <option value="">Select Supplier</option>
                        @foreach (\App\Supplier::get() as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-1 mr-3 col-sm-2 col-2">
                    <button type="submit" class="btn btn-primary btn-sm dueFilter">Filter</button>
                </div>
                <div class="form-group col-md-2 ml-2 col-sm-2 col-2">
                    <button type="submit" class="btn btn-danger btn-sm dueReset">Reset</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="grid-of-2">
            @can('total_sold')
            <div class="card card-body bg-primary">
                <h6>
                    <span class="text-uppercase text-white">Total Customer Due</span>
                </h6>
                <p class="fs-28 fw-700">৳ {{ number_format($summary::customer_receivable(),0) }}</p>
            </div>
            @endcan

            @can('total_purchased')
            <div class="card card-body bg-dark">
                <h6>
                    <span class="text-uppercase text-white">Total Supplier Due</span>
                </h6>
                <p class="fs-28 fw-700 total_payable">৳ {{ number_format($summary::supplier_payable(),0) }}</p>
            </div>
            @endcan
        </div>
    </div>
</div>
@endcanany
{{-- Lifetime Report --}}
@canany(['total_sold', 'total_purchased', 'total_expense', 'total_returned', 'total_profit'])
{{-- Lifetime Report --}}
<div class="card col-12 containing-card">
    <div class="card-header">
        <h3 class="card-title">Total</h3>
        <div class="filter-form">
            <div class="form-row align-items-center">
                <div class="form-group col-md-2 col-sm-3 col-3">
                    <input type="date" name="tstart_date" id="t_start_date" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-2 col-sm-3 col-3">
                    <input type="date" name="tend_date" id="t_end_date" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-1 mr-3 col-sm-2 col-2">
                    <button type="submit" class="btn btn-primary btn-sm totalFilter">Filter</button>
                </div>
                <div class="form-group col-md-2 ml-2 col-sm-2 col-2">
                    <button type="submit" class="btn btn-danger btn-sm totalReset">Reset</button>
                </div>
                <div class="form-group col-md-4">
                    <div class="btn-group" role="group">
                        <p class="btn btn-sm btn-outline-secondary" id="t_day_7">7
                            Days</p>
                        <p class="btn btn-sm btn-outline-secondary smHidden" id="t_day_15">15
                            Days</p>
                        <p class="btn btn-sm btn-outline-secondary" id="t_day_m">Month</p>
                        <p class="btn btn-sm btn-outline-secondary" value="year" id="t_day_y">Year</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="grid-of-4">
            @can('total_sold')
            <div class="card card-body bg-dark">
                <h6 class="text-white text-uppercase">Total Sold</h6>
                <p class="fs-18 fw-700">৳ <span id="t_sold">{{ number_format($summary->sold()) }}</span></p>
            </div>
            @endcan

            @can('total_purchased')
            <div class="card card-body card-secondary">
                <h6 class="text-uppercase">
                    Total Purchased
                </h6>
                <p class="fs-18 fw-700">৳ <span id="t_purchased">{{ number_format($summary->purchased()) }}</span>
                </p>
            </div>
            @endcan

            @can('total_expense')
            <div class="card card-body card-danger">
                <h6 class="text-white text-uppercase">
                    <span>Total Expense</span>
                </h6>
                <p class="fs-18 fw-700 text-white">৳ <span id="t_expense">{{ number_format($summary->expenses())
                        }}</span>
                </p>
            </div>
            @endcan

            {{-- @can('total_returned')
            <div class="card card-body card-cyan">
                <h6 class="text-white text-uppercase">
                    <span>Total Returned</span>
                </h6>
                <p class="fs-18 fw-700 text-white">৳ <span id="t_returned">{{ number_format($summary->returned())
                        }}</span>
                </p>
            </div>
            @endcan --}}

            @can('total_profit')
            <div class="card card-body card-success">
                <h6 class="text-white text-uppercase">
                    Total Profit
                </h6>
                <p class="fs-18 fw-700 text-white">৳ <span id="t_profit">{{ number_format($summary->profit()) }}</span>
                </p>
            </div>
            @endcan
        </div>
    </div>
</div>
{{-- End Lifetime Summary --}}
@endcanany
{{-- End Lifetime Summary --}}

<div class="card col-12 containing-card print_area">
    <div class="card-header">
        <h4>Sale Graph</h4>
    </div>
    <canvas id="productChart" width="700" height="150"></canvas>
</div>
</div>
@endcan

@endsection

@section('styles')
<style>
    .main-content {
        padding-top: 25px;
    }

    @media (min-width: 250px) {
        .grid-of-5 {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(2, minmax(100px, auto));
            grid-column-gap: 1.5%;
        }

        .grid-of-4 {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(2, minmax(100px, auto));
            grid-column-gap: 1.5%;
        }
    }

    @media (min-width: 768px) {
        .grid-of-5 {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(4, minmax(100px, 1fr));
            grid-column-gap: 1.5%;
        }

        .grid-of-4 {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(4, minmax(100px, 1fr));
            grid-column-gap: 1.5%;
        }
    }

    @media (min-width: 992px) {
        .grid-of-5 {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(5, minmax(100px, 1fr));
            grid-column-gap: 1.5%;
        }
    }

    .grid-of-2 {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(2, minmax(100px, 1fr));
        grid-column-gap: 1.5%;
    }

    .card .card {
        margin-bottom: 10px;
    }

    .containing-card>.card-body {
        padding: 10px;
    }

    .card-header {
        padding: 5px;
    }

    @media(max-width: 575px) {
        .table-responsive-sm {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .smHidden {
            display: none;
        }
    }
</style>

<style>
    @media print {

        /* Hide certain elements when printing */
        .no-print {
            display: none;
        }

        /* Adjust font sizes and margins for printing */
        body {
            font-size: 12px;
            margin: 0;
            padding: 0;
            font-color: #000 !important;
            font-family: 'Times New Roman', Times, serif;
        }

        .card {
            border: none;
            box-shadow: none;
            page-break-inside: avoid;
        }

        .table {
            font-size: 10px;
            font-color: #000 !important;
        }

        .grid-of-4,
        .grid-of-5,
        .grid-of-2 {
            display: block;
            width: 100%;
        }

        .card-body {
            padding: 5px;
        }

        .card-header {
            padding: 5px;
        }

        .card-header h3 {
            font-size: 18px;

        }

        /* Center align table headers and cells */
        .table th,
        .table td {
            text-align: center;
            color: black !important;
            font-size: 18px;
            border: 1px solid #000 !important;
        }

        /* Add some space around the table */
        .table {
            margin-bottom: 10px;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script>
    function printContent1() {
        $('.today_date').text(moment().format('YYYY-MM-DD'));
        var brandName = $('#rBrandId option:selected').data('brand');
        brandName='('+brandName+')';
        $('.r_brand_name').text((brandName)); 
        var printContents = document.getElementById('printable-content1').outerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        $('.today_date').text('');
        $('.brand_name').text('');
        // reload
        location.reload();
    }

        function printContent2() {
            $('.today_date').text(moment().format('YYYY-MM-DD'));
            var brandName = $('#brandId option:selected').data('brand');
            brandName='('+brandName+')';
            $('.o_brand_name').text((brandName));
            var printContents = document.getElementById('printable-content2').outerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            $('.today_date').text('');
            $('.o_brand_name').text('');
            // reload
            location.reload();
        }

        localStorage.removeItem('pos-items');
        ///////////////////////start total calculation//////////////////////
        $('.montylyFilter').click(function(e) {
            e.preventDefault();
            //get input start date and end_date value 
            var start_date = $('#m_start_date').val();
            var end_date = $('#m_end_date').val();
            //check if start_date and end_date is empty
            if (start_date == '' || end_date == '') {
                toastr.warning('Please Select Date');
            } else {
                monthlyCalculationFilter(start_date, end_date);
            }
        });

        $('.montylyReset').click(function(e) {
            e.preventDefault();
            //get input start date and end_date value 
            //today date
            var start_date = moment().startOf('month').format('YYYY-MM-DD');
            var end_date = moment().endOf('month').format('YYYY-MM-DD');
            //check if start_date and end_date is empty
            if (start_date == '' || end_date == '') {
                toastr.warning('Please Select Date');
            } else {
                monthlyCalculationFilter(start_date, end_date);
            }
        });
        //onclick #day_7 and call monthlyCalculation function with parammeter start_date and end_date
        $('#m_day_7').click(function(e) {
            e.preventDefault();
            var start_date = moment().subtract(7, 'days').format('YYYY-MM-DD');
            var end_date = moment().format('YYYY-MM-DD');
            monthlyCalculationFilter(start_date, end_date);
        });
        //onclick #day_15 and call monthlyCalculation function with parammeter start_date and end_date
        $('#m_day_15').click(function(e) {
            e.preventDefault();
            var start_date = moment().subtract(15, 'days').format('YYYY-MM-DD');
            var end_date = moment().format('YYYY-MM-DD');
            monthlyCalculationFilter(start_date, end_date);
        });
        //onclick #day_m and call monthlyCalculation function with parammeter start_date and end_date
        $('#m_day_m').click(function(e) {
            e.preventDefault();
            var start_date = moment().subtract(31, 'days').format('YYYY-MM-DD');
            var end_date = moment().endOf('month').format('YYYY-MM-DD');
            monthlyCalculationFilter(start_date, end_date);
        });
        //onclick #day_y and call monthlyCalculation function with parammeter start_date and end_date
        $('#m_day_y').click(function(e) {
            e.preventDefault();
            var start_date = moment().startOf('year').format('YYYY-MM-DD');
            var end_date = moment().endOf('year').format('YYYY-MM-DD');
            monthlyCalculationFilter(start_date, end_date);
        });

        function monthlyCalculationFilter(start_date, end_date) {
            $.ajax({
                url: "{{ route('report.current_month_no_reload') }}",
                type: "POST",
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    $('#m_order').text('');
                    $('#m_order').text(data.totalMonthlyOrderValue);
                    $('#m_sold').text('');
                    $('#m_sold').text(data.totalMonthlySold);
                    $('#m_damage').text('');
                    $('#m_damage').text(data.totalMonthlyDamage);
                    $('#m_profit').text('');
                    $('#m_profit').text(data.totalMonthlyProfit);
                }
            });
        }
        ///////////////////////////////end monthly calculation//////////////////////

        ///////////////////////////////start due calculation//////////////////////
        $('#supplier_id').change(function(e) {
            e.preventDefault();
            var supplier_id = $(this).val();
            // alert(supplier_id);
            //ajax to report.supplier_report 
            $.ajax({
                url: "{{ route('report.supplier_report') }}",
                type: "POST",
                data: {
                    supplier_id: supplier_id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    // console.log(data);
                    // $('.total_receivable').text('');
                    // $('.total_receivable').text(data.supplier_receivable);
                    $('.total_payable').text('');
                    $('.total_payable').text(data.supplier_payable);
                }
            });
        });

        ////////////////////////////////end of due calculation//////////////////////

        ///////////////////////////////start total calculation//////////////////////

        //onclick .totalFilter show an alert prevent default
        $('.totalFilter').click(function(e) {
            e.preventDefault();
            //get input start date and end_date value 
            var start_date = $('#t_start_date').val();
            var end_date = $('#t_end_date').val();
            //check if start_date and end_date is empty
            if (start_date == '' || end_date == '') {
                toastr.warning('Please Select Date');
            } else {
                totalCalculationFilter(start_date, end_date);
            }
        });
        //onclick .totalReset show an alert prevent default
        $('.totalReset').click(function(e) {
            e.preventDefault();
            //get input start date and end_date value
            var start_date = '';
            // alert(start_date);
            var end_date = '';
            totalCalculationFilter(start_date, end_date);
        });
        //onclick #day_7 and call totalCalculation function with parammeter start_date and end_date
        $('#t_day_7').click(function(e) {
            e.preventDefault();
            var start_date = moment().subtract(7, 'days').format('YYYY-MM-DD');
            var end_date = moment().format('YYYY-MM-DD');
            totalCalculationFilter(start_date, end_date);
        });
        //onclick #day_15 and call totalCalculation function with parammeter start_date and end_date
        $('#t_day_15').click(function(e) {
            e.preventDefault();
            var start_date = moment().subtract(15, 'days').format('YYYY-MM-DD');
            var end_date = moment().format('YYYY-MM-DD');
            totalCalculationFilter(start_date, end_date);
        });
        //onclick #day_m and call totalCalculation function with parammeter start_date and end_date
        $('#t_day_m').click(function(e) {
            e.preventDefault();
            var start_date = moment().subtract(31, 'days').format('YYYY-MM-DD');
            var end_date = moment().endOf('month').format('YYYY-MM-DD');
            totalCalculationFilter(start_date, end_date);
        });
        //onclick #day_y and call totalCalculation function with parammeter start_date and end_date
        $('#t_day_y').click(function(e) {
            e.preventDefault();
            var start_date = moment().startOf('year').format('YYYY-MM-DD');
            var end_date = moment().endOf('year').format('YYYY-MM-DD');
            totalCalculationFilter(start_date, end_date);
        });
        /////////////////total calculation//////////////////////
        function totalCalculationFilter(start_date, end_date) {
            $.ajax({
                url: "{{ route('report.total_report_no_reload') }}",
                type: "POST",
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    $('#t_sold').text('');
                    $('#t_sold').text(data.totalSold);
                    $('#t_purchased').text('');
                    $('#t_purchased').text(data.totalPurchased);
                    $('#t_expense').text('');
                    $('#t_expense').text(data.totalExpense);
                    $('#t_returned').text('');
                    $('#t_returned').text(data.totalReturned);
                    $('#t_profit').text('');
                    $('#t_profit').text(data.totalProfit);
                }
            });
        }
        ///////////////////////end total calculation//////////////////////

        ///////////////////////start brand calculation//////////////////////

        $('.brandWiseFilter').click(function(e) {
            e.preventDefault();
            var start_date = $('#b_start_date').val();
            var end_date = $('#b_end_date').val();

            if (start_date == '' || end_date == '') {
                toastr.warning('Please Select Date');
            } else {
                $.ajax({
                    url: "{{ route('report.brands') }}",
                    type: "POST",
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        var html = '';
                        var count = data.brands.length;
                        var completedRequests = 0;

                        $.each(data.brands, function(key, value) {
                            fetchBrandData(value.id, start_date, end_date, function(brandData) {
                                var order = parseFloat(brandData.brandOrder.replace(/,/g, ''));
                                var return_value = parseFloat(brandData.brandReturn.replace(/,/g, ''));
                                var discount = parseFloat(brandData.brandDiscount.replace(/,/g, ''));

                                var sell=(order-return_value-discount);
                                var profit = parseFloat(brandData.BrandProfit.replace(/,/g, ''));
                                var profit_percent = sell !== 0 ? (profit / sell) * 100 : 0;
                                html += `<tr>
                            <td class="bg-primary">${value.name}</td>
                            <td id="brand_order">${brandData.brandOrder}/-</td>
                            <td id="brand_return">${brandData.brandReturn}/-</td>
                            <td id="brand_damage">${brandData.brandDamage}/-</td>
                            <td id="brand_discount">${brandData.brandDiscount}/-</td>
                            <td id="brand_sell">${brandData.brandSell}/-</td>
                            <td id="brand_due">${brandData.brandDue}/-</td>
                            <td id="brand_collection">${brandData.brandCollection}/-</td>
                            <td id="brand_profit">${brandData.BrandProfit}/-</td>

                            <td id="brand_profit_percentage">
                                ${profit_percent.toFixed(2)}%
                            </td>

                            </tr>`;
                                completedRequests++;
                                // Check if all requests are completed
                                if (completedRequests === count) {
                                    $('#brand_report').html(html);
                                }
                            });
                        });
                    }
                });
            }
        });

        //brandWiseReset 
        $('.brandWiseReset').click(function(e) {
            e.preventDefault();
            //get input start date and end_date value
            var start_date = moment().format('YYYY-MM-DD');
            var end_date = moment().format('YYYY-MM-DD');
            $('#b_start_date').val(start_date);
            $('#b_end_date').val(end_date);
            // Trigger the filter button click to apply the filter
            $('.brandWiseFilter').trigger('click');
        });

        //day click function
        $('#b_day_7').click(function(e) {
            e.preventDefault();
            var start_date = moment().subtract(7, 'days').format('YYYY-MM-DD');
            var end_date = moment().format('YYYY-MM-DD');

            // Update the date inputs and trigger the filter
            $('#b_start_date').val(start_date);
            $('#b_end_date').val(end_date);

            // Trigger the filter button click to apply the filter
            $('.brandWiseFilter').trigger('click');
        });
        //for 15 days 
        $('#b_day_15').click(function(e) {
            e.preventDefault();
            var start_date = moment().subtract(15, 'days').format('YYYY-MM-DD');
            var end_date = moment().format('YYYY-MM-DD');
            // Update the date inputs and trigger the filter
            $('#b_start_date').val(start_date);
            $('#b_end_date').val(end_date);
            // Trigger the filter button click to apply the filter
            $('.brandWiseFilter').trigger('click');
        });
        //for month
        $('#b_day_m').click(function(e) {
            e.preventDefault();
            var start_date = moment().subtract(31, 'days').format('YYYY-MM-DD');
            var end_date = moment().endOf('month').format('YYYY-MM-DD');
            // Update the date inputs and trigger the filter
            $('#b_start_date').val(start_date);
            $('#b_end_date').val(end_date);

            // Trigger the filter button click to apply the filter
            $('.brandWiseFilter').trigger('click');
        });
        //for year
        $('#b_day_y').click(function(e) {
            e.preventDefault();
            var start_date = moment().startOf('year').format('YYYY-MM-DD');
            var end_date = moment().endOf('year').format('YYYY-MM-DD');
            // Update the date inputs and trigger the filter
            $('#b_start_date').val(start_date);
            $('#b_end_date').val(end_date);

            // Trigger the filter button click to apply the filter
            $('.brandWiseFilter').trigger('click');
        });

        // Function to fetch all required data for a brand
        function fetchBrandData(brand_id, start_date, end_date, callback) {
            var brandData = {};
            var requestsCompleted = 0;
            var totalRequests = 8; // Total number of requests (one for each data type)

            // Fetch brandOrder value
            brandOrder(brand_id, start_date, end_date, function(data) {
                brandData.brandOrder = data;
                requestsCompleted++;
                checkAllRequestsCompleted();
            });

            // Fetch brandReturn value
            brandReturn(brand_id, start_date, end_date, function(data) {
                brandData.brandReturn = data;
                requestsCompleted++;
                checkAllRequestsCompleted();
            });

            // Fetch brandDamage value
            brandDamage(brand_id, start_date, end_date, function(data) {
                brandData.brandDamage = data;
                requestsCompleted++;
                checkAllRequestsCompleted();
            });

            // Fetch brandSell value
            brandSell(brand_id, start_date, end_date, function(data) {
                brandData.brandSell = data;
                requestsCompleted++;
                checkAllRequestsCompleted();
            });
            // Fetch brandDue value
            brandDue(brand_id, start_date, end_date, function(data) {
                brandData.brandDue = data;
                requestsCompleted++;
                checkAllRequestsCompleted();
            });

            // Fetch brandCollection value
            brandCollection(brand_id, start_date, end_date, function(data) {
                brandData.brandCollection = data;
                requestsCompleted++;
                checkAllRequestsCompleted();
            });

            // Fetch brandProfit value
            brandProfit(brand_id, start_date, end_date, function(data) {
                brandData.BrandProfit = data;
                requestsCompleted++;
                checkAllRequestsCompleted();
            });

            // Fetch brandDiscount value
            brandDiscount(brand_id, start_date, end_date, function(data) {
                brandData.brandDiscount = data;
                requestsCompleted++;
                checkAllRequestsCompleted();
            });

            // Check if all requests are completed
            function checkAllRequestsCompleted() {
                if (requestsCompleted === totalRequests) {
                    callback(brandData);
                }
            }
        }

        // Function to retrieve brandOrder value
        function brandOrder(brand_id, start_date, end_date, callback) {
            $.ajax({
                url: "{{ route('report.brand_order') }}",
                type: "POST",
                data: {
                    brand_id: brand_id,
                    start_date: start_date,
                    end_date: end_date,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    callback(data);
                }
            });
        }

        // Function to retrieve brandReturn value
        function brandReturn(brand_id, start_date, end_date, callback) {
            $.ajax({
                url: "{{ route('report.brand_return') }}",
                type: "POST",
                data: {
                    brand_id: brand_id,
                    start_date: start_date,
                    end_date: end_date,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    callback(data);
                }
            });
        }

        //brand_damage 
        function brandDamage(brand_id, start_date, end_date, callback) {
            $.ajax({
                url: "{{ route('report.brand_damage') }}",
                type: "POST",
                data: {
                    brand_id: brand_id,
                    start_date: start_date,
                    end_date: end_date,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    callback(data);
                }
            });
        }

        //brand_sell
        function brandSell(brand_id, start_date, end_date, callback) {
            $.ajax({
                url: "{{ route('report.brand_sell') }}",
                type: "POST",
                data: {
                    brand_id: brand_id,
                    start_date: start_date,
                    end_date: end_date,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    callback(data);
                }
            });
        }

        //brand_due
        function brandDue(brand_id, start_date, end_date, callback) {
            $.ajax({
                url: "{{ route('report.brand_due') }}",
                type: "POST",
                data: {
                    brand_id: brand_id,
                    start_date: start_date,
                    end_date: end_date,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    callback(data);
                }
            });
        }

        //brand_collection
        function brandCollection(brand_id, start_date, end_date, callback) {
            $.ajax({
                url: "{{ route('report.brand_collection') }}",
                type: "POST",
                data: {
                    brand_id: brand_id,
                    start_date: start_date,
                    end_date: end_date,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    callback(data);
                }
            });
        }
        
        //brand_profit
        function brandProfit(brand_id, start_date, end_date, callback) {
            $.ajax({
                url: "{{ route('report.brand_profit') }}",
                type: "POST",
                data: {
                    brand_id: brand_id,
                    start_date: start_date,
                    end_date: end_date,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    callback(data);
                }
            });
        }

        //brand_profit
        function brandDiscount(brand_id, start_date, end_date, callback) {
            $.ajax({
                url: "{{ route('report.discount') }}",
                type: "POST",
                data: {
                    brand_id: brand_id,
                    start_date: start_date,
                    end_date: end_date,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    callback(data);
                }
            });
        }

        ////////////////////////////////////////end brand calculation///////////////////////////

        ///////////////////////////////////////brand wise product calculation//////////////////

        //change brandId
        $('.b_w_p_filter').click(function(e) {
            e.preventDefault();
            var brand_id = $('#brandId').val();
            // alert(brand_id);
            var start_date = $('#b_w_p_start_date').val();
            var end_date = $('#b_w_p_end_date').val();
            // alert(brand_id+' '+start_date+' '+end_date);
            if (brand_id == '' || start_date == '' || end_date == '') {
                toastr.warning('Please Select Date');
            } else {
                brandWiseProduct(brand_id, start_date, end_date);
            }
        });

        //onchange brandId 
        $('#brandId').change(function(e) {
            e.preventDefault();
            var brand_id = $(this).val();
            var start_date = $('#b_w_p_start_date').val();
            var end_date = $('#b_w_p_end_date').val();
            brandWiseProduct(brand_id, start_date, end_date);
            fetchDataAndRefreshChart(brand_id, start_date, end_date);
        });

        $('.b_w_p_Reset').click(function(e) {
            e.preventDefault();
            var brand_id = '';
            // alert(brand_id);
            var start_date = '';
            var end_date = '';
            brandWiseProduct(brand_id, start_date, end_date);
        });

        function brandWiseProduct(brand_id, start_date, end_date) {
            var total_price = 0;
            $.ajax({
                url: "{{ route('report.brand_wise_product') }}",
                type: "POST",
                data: {
                    brand_id: brand_id,
                    start_date: start_date,
                    end_date: end_date,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    var html = '';
                    //if data is not empty
                    if (data.length > 0) {
                        $.each(data, function(key, value) {
                            total_price += parseFloat(value.price.replace(/,/g, ''));
                            html += `<tr>
                            <td>${value.name}</td>
                            <td>${value.qty}</td>
                            <td>${Math.round(value.price).toLocaleString()}/-</td>
                        </tr>`;
                        });
                        $('.totalProductPrice').addClass('d-none');
                        html += `
                            <tr>
                                <th colspan="2" class="text-right">Total</th>
                                <th><span id="totalProductPrice"></span>${Math.round(total_price).toLocaleString()}/-</th>
                            </tr>`;
                    } else {
                        html += `<tr>
                            <td colspan="4" class="text-center text-danger">No Data Found</td>
                        </tr>`;
                    }
                    $('#brandWiseProductReport').html(html);
                }
            });
        }
        ///////////////////////////////////////end brand wise product calculation//////////////////


        /////////////////////////////////////// brand wise product return calculation//////////////////
        $('.b_w_p_r_filter').click(function(e) {
            e.preventDefault();
            var brand_id = $('#rBrandId').val();
            // alert(brand_id);
            var start_date = $('#b_w_p_r_start_date').val();
            var end_date = $('#b_w_p_r_end_date').val();
            // alert(brand_id+' '+start_date+' '+end_date);
            if (brand_id == '' || start_date == '' || end_date == '') {
                toastr.warning('Please Select Date');
            } else {
                brandWiseProductReturn(brand_id, start_date, end_date);
            }
        });

        //onchange rbrandId
        $('#rBrandId').change(function(e) {
            e.preventDefault();
            var brand_id = $(this).val();
            var start_date = $('#b_w_p_r_start_date').val();
            var end_date = $('#b_w_p_r_end_date').val();
            brandWiseProductReturn(brand_id, start_date, end_date);
        });

        //onclick b_w_p_r_Reset
        $('.b_w_p_r_Reset').click(function(e) {
            e.preventDefault();
            var brand_id = '';
            // alert(brand_id);
            var start_date = '';
            var end_date = '';
            brandWiseProductReturn(brand_id, start_date, end_date);
        });
        
        function brandWiseProductReturn(brand_id, start_date, end_date) {
            var total_price = 0;
            $.ajax({
                url: "{{ route('report.brand_wise_product_return') }}",
                type: "POST",
                data: {
                    brand_id: brand_id,
                    start_date: start_date,
                    end_date: end_date,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    var html = '';
                    //if data is not empty
                    if (data.length > 0) {
                        $.each(data, function(key, value) {
                            html += `<tr>
                            <td>${value.name}</td>
                            <td>${value.returned}</td>
                            <td>${value.damage} pc</td>
                        </tr>`;
                        });
                    } else {
                        html += `<tr>
                            <td colspan="4" class="text-center text-danger">No Data Found</td>
                        </tr>`;
                    }
                    $('#brandWiseProductReturn').html(html);
                }
            });
        }

        ///////////////////////////////////////end brand wise product return calculation//////////////////


        ///////////////////////////////////////start product graph////////////////////////////////
        var products = {!! json_encode($products) !!};
        // Function to initialize the chart with data
        function initializeChartWithData(products) {
            var productNames = products.map(function(product) {
                return product.name;
            });
            var productQuantities = products.map(function(product) {
                return product.qty;
            });
            var productPrices = products.map(function(product) {
                return product.price;
            });

            // Get the canvas element for the chart
            var ctx = document.getElementById('productChart').getContext('2d');

            // Create a new chart instance
            window.productChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: productNames,
                    datasets: [{
                        label: 'Quantity',
                        data: productQuantities,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Price',
                        data: productPrices,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                        }
                    }
                }
            });
        }

        initializeChartWithData(products);
        var ctx = document.getElementById('productChart').getContext('2d');
        var productChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Function to update the chart based on data
        function updateChart(productNames, productQuantities, productPrices) {
            productChart.data.labels = productNames;
            productChart.data.datasets = [{
                label: 'Quantity',
                data: productQuantities,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }, {
                label: 'Price',
                data: productPrices,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }];
            productChart.update();
        }

        // Function to fetch data and update the chart
        function fetchDataAndRefreshChart(brandId, startDate, endDate) {
            $.ajax({
                url: "{{ route('report.brand_wise_product') }}",
                type: "POST",
                data: {
                    brand_id: brandId,
                    start_date: startDate,
                    end_date: endDate,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    var productNames = data.map(function(product) {
                        return product.name;
                    });
                    var productQuantities = data.map(function(product) {
                        return product.qty;
                    });
                    var productPrices = data.map(function(product) {
                        return product.price;
                    });
                    updateChart(productNames, productQuantities, productPrices);
                },
                error: function() {
                    // Handle AJAX errors
                }
            });
        }
        // Initial chart setup
        fetchDataAndRefreshChart('', '', '');
        ///////////////////////////////////////end product graph////////////////////////////////
</script>

@endsection