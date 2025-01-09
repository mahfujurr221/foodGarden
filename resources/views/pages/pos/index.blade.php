@extends('layouts.master')
@section('title', 'Sales List')

@section('page-header')
<div class="header-info mb-1">
    <h1 class="header-title">
        <strong>Sale List</strong>
    </h1>
</div>
@endsection

@section('content')
<div class="card print_area mt-1" style="width:100%;">
    <div class="row">
        <div class="col-12" style="display:flex; justify-content:space-between">
            <div class="col-2">
                <h4 class="card-title"><strong>Sales</strong></h4>
            </div>
            <div class="col-md-9 print_hidden">
                <form action="#">
                    <div class="form-row mt-3">
                        <div class="form-group col-md-2">
                            <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                                data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                                class="form-control" name="start_date" placeholder="Start Date" autocomplete="off"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                                data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                                class="form-control" name="end_date" placeholder="End Date" autocomplete="off"
                                value="{{ request('end_date') }}">
                        </div>
                        
                        <div class="form-group col-md-2">
                            <select name="customer" id="" class="form-control" data-provide="selectpicker"
                                data-live-search="true" data-size="10">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->shop_name . '-'.
                                    $customer->address->name . ' (' . $customer->name .')' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group col-md-2">
                            <select name="product_id" id="" class="form-control" data-provide="selectpicker"
                                data-live-search="true" data-size="10">
                                <option value="">Select Product</option>
                                @foreach ($products as $item)
                                <option value="{{ $item->id }}" {{ request('product_id')==$item->id ? 'SELECTED' : ''
                                    }}>
                                    {{ $item->name . ' - ' . $item->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <select name="brand_id" id="" class="form-control" data-provide="selectpicker"
                                data-live-search="true" data-size="10">
                                <option value="">Select Brand</option>
                                @foreach ($brands as $item)
                                <option value="{{ $item->id }}" {{ request('brand_id')==$item->id ? 'SELECTED' : '' }}>
                                    {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-1">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-sliders"></i>
                                Filter
                            </button>
                        </div>

                        <div class="form-group col-1">
                            <a href="{{ route('pos.index') }}" class="btn btn-info">Reset</a>
                        </div>

                    </div>
                </form>
            </div>
            <div class="col-1 print_hidden">
                <a href="" class="btn btn-primary pull-right mt-3" onclick="window.print()">Print</a>
            </div>
        </div>
    </div>

    <div class="card-body">
        @if ($sales->count() > 0)
        <table class="table table-bordered table-striped">
            {{-- data-provide="datatables"> --}}
            <thead>
                <tr class="bg-primary">
                    <th>#</th>
                    {{-- <th>Invoice No.</th> --}}
                    <th>Brand</th>
                    <th>Shop Name</th>
                    <th>Adrress</th>
                    <th>Delivery Date</th>
                    {{-- <th>Items</th> --}}
                    {{-- <th>Order Qty</th> --}}
                    {{-- <td>Price</td> --}}
                    {{-- <th>Discount/Free</th> --}}
                    <th>Order Tk</th>
                    <th>Returned Tk</th>
                    {{-- <th>Dis.Returned</th> --}}
                    <th>Damaged Tk</th>
                    <th>Net Sale</th>
                    {{-- <th>Discount</th> --}}
                    <th>Paid</th>
                    {{-- <th>Returned</th> --}}
                    <th>Due</th>
                    {{-- @can('pos-purchase_cost')
                    <th>Purchase Cost</th>
                    @endcan --}}
                    @can('pos-profit')
                    <th>Profit</th>
                    @endcan
                    <th>Status</th>
                    <th class="print_hidden">Actions</th>
                </tr>
            </thead>

            @php
            $discount_qty = 0;
            $total_order_qty = 0;
            $total_discount_qty = 0;
            $total_order_price = 0;
            $total_returned = 0;
            $total_returned_price = 0;
            $total_discount_returned = 0;
            $total_damage = 0;
            $total_damage_price = 0;
            $total_net_sale = 0;
            $total_paid = 0;
            $total_due = 0;
            $total_profit = 0;
            @endphp

            <tbody>
                @foreach ($sales as $key => $sale)
                @php
                $order_price = 0;
                $returned_price = 0;
                $damage_price=0;
                @endphp
                <tr>
                    <td>{{ $key + 1 }}
                        <i class="ml-2 fa fa-fw fa-eye bg-danger print_hidden" id="show_more"
                            data-saledetailsid="{{$sale->id}}" style="cursor:pointer"></i>
                    </td>
                    {{-- <td>{{ $sale->id }}</td> --}}
                    <td>{{ $sale->items()->first()->product->brand->name ?? 0 }}</td>
                    <td>
                        {{ $sale->customer ? $sale->customer->shop_name_bangla : 'Walk-in Customer' }}
                    </td>
                    <td>
                        {{ $sale->customer ? $sale->customer->address->name : 'Walk-in Address' }}
                    </td>
                    <td>{{ date('d M, Y', strtotime($sale->sale_date)) }}</td>

                    @foreach ($sale->items()->get() as $item)
                    {{-- {{ $item->product->readable_qty($item->ordered_qty) }} --}}
                    <!--@if (!$loop->last)-->
                    <!--<hr style="margin: 5px 0px; color:rgb(18, 17, 17);">-->
                    <!--@endif-->

                    @php
                    $total_order_qty += $item->ordered_qty;
                    $order_price += $item->product->quantity_worth($item->ordered_qty, $item->rate);
                    $returned_qty = $item->returned_qty;
                    $returned_price += $item->returned_value;
                    $damage_price += $item->damaged_value;
                    @endphp
                    @endforeach

                    @php
                    $total_order_price += $order_price;
                    $total_returned_price += $returned_price;
                    $total_damage_price += $damage_price;
                    @endphp

                    <td>{{ number_format($order_price) }}/-</td>
                    <td>
                        {{ number_format($returned_price) }}/-
                    </td>
                    <td>
                        {{ number_format($damage_price) }}/-
                    </td>
                    <td>{{ number_format($sale->receivable) }}/-</td>
                    <!--<td>{{ number_format($order_price-$returned_price-$damage_price-$sale->discount) }}/-</td>-->
                    <td>{{ number_format($paid = $sale->paid) }}/-</td>
                    
                    <td>
                        @if ($sale->due<0) 0/- @else {{ number_format($sale->due) }}/-
                            @endif
                    </td>
                    
                    @can('pos-profit')
                    <td>{{ number_format($sale->profit) }}/-</td>
                    @endcan

                    @php
                    $total_net_sale += $sale->receivable;
                    $total_paid += $paid;
                    $total_due += $sale->due;
                    $total_profit += $sale->profit;
                    @endphp
                    <td>
                        @if($sale->due<=0) <span class="badge badge-success">PAID</span>
                            @else
                            <span class="badge badge-danger">UNPAID</span>
                            @endif
                    </td>

                    <td class="print_hidden">
                        <div class="btn-group">
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fa fa-cogs"></i>
                            </button>
                            <div class="dropdown-menu" x-placement="bottom-start">
                                <a class="dropdown-item" href="{{ route('pos_receipt', $sale->id) }}">
                                    <i class="fa fa-print text-primary"></i>
                                    Print
                                </a>
                                <a class="dropdown-item" href="{{ route('pos.show', $sale->id) }}">
                                    <i class="fa fa-desktop text-info"></i>
                                    Show
                                </a>
                                <a class="dropdown-item" href="{{ route('pos.edit', $sale->id) }}">
                                    <i class="fa fa-pencil-square-o text-warning"></i>
                                    Edit
                                </a>
                                <a href="{{ route('pos.add_payment', $sale->id) }}" class="edit dropdown-item"
                                    data-toggle="modal" data-target="#edit" id="Add Payment">
                                    <i class="fa fa-money text-primary"></i>
                                    Add Payment
                                </a>
                                <a class="dropdown-item delete" href="{{ route('pos.destroy', $sale->id) }}">
                                    <i class="fa fa-trash text-danger"></i>
                                    Delete
                                </a>
                            </div>
                        </div>
                    </td>

                </tr>

                <tr id="detailsSale-{{$sale->id}}" class="details-row d-none">
                    <td colspan="2" class="text-left">
                        <strong class="text-center">Customer Details</strong> <br>
                        <strong>Name:</strong> {{ $sale->customer->name?? '' }} <br>
                        <strong>Busigness Name:</strong> {{ $sale->customer->business_category->name?? '' }} <br>
                        <strong>Phone:</strong> {{ $sale->customer->phone??'' }} <br>
                        <strong>Note:</strong> {{ $sale->customer->note??''}}</strong>
                    </td>

                    <td class="text-left">
                        <strong>Order Date:</strong> <br>
                        {{-- {{date('d M, Y', strtotime($sale->estimate->estimate_date))}} --}}
                        {{$sale->estimate->estimate_date??''}}
                    </td>

                    <td class="text-left">
                        <strong>Product Name:</strong> <br>
                        @foreach ($sale->items()->with('product')->get() as $item)
                        {{ $item->product->name }}
                        @if (!$loop->last)
                        <hr style="margin: 5px 0px; color:rgb(18, 17, 17);">
                        @endif
                        @endforeach
                    </td>

                    <td class="text-left">
                        <strong>Rate:</strong> <br>
                        @foreach ($sale->items()->with('product')->get() as $item)
                        {{ $item->rate }}/-
                        @if (!$loop->last)
                        <hr style="margin: 5px 0px; color:rgb(18, 17, 17);">
                        @endif
                        @endforeach
                    </td>
                    
                    <td class="text-left">
                        <strong>Order Qty:</strong> <br>
                        @foreach ($sale->items()->get() as $item)
                        {{ $item->product->readable_qty($item->ordered_qty) }}
                        @if (!$loop->last)
                        <hr style="margin: 5px 0px; color:rgb(18, 17, 17);">
                        @endif
                        @endforeach
                    </td>
                    
                    <td class="text-left">
                        <strong>Returned Qty:</strong> <br>
                        @foreach ($sale->items()->get() as $item)
                        {{ $item->product->readable_qty($item->returned_qty) }}
                        @if (!$loop->last)
                        <hr style="margin: 5px 0px; color:rgb(18, 17, 17);">
                        @endif
                        @endforeach
                    </td>
                    <td class="text-left">
                        <strong>Damage Qty:</strong> <br>
                        @foreach ($sale->items()->get() as $item)
                        {{ $item->damage }} pc
                        @if (!$loop->last)
                        <hr style="margin: 5px 0px; color:rgb(18, 17, 17);">
                        @endif
                        @endforeach
                    </td>

                    <td class="text-left">
                        <strong>Damage Value:</strong> <br>
                        @foreach ($sale->items()->get() as $item)
                        {{ number_format($item->damaged_value) }}/-
                        @if (!$loop->last)
                        <hr style="margin: 5px 0px; color:rgb(18, 17, 17);">
                        @endif
                        @endforeach
                    </td>
                    <td class="text-left">
                        <strong>Order By:</strong> <br>
                        {{ $sale->estimate->sales_man->fname??''}}
                    </td>
                    
                    <td class="text-left">
                        <strong>Delivery By:</strong> <br>
                        {{ $sale->sales_man->fname??'-'}}
                    </td>
                    
                    @if($sale->discount !=null)
                    <td>
                        <strong>Discount:</strong> <br>
                            @if(empty($sale->discount))
                            0 Tk
                            @elseif(strpos($sale->discount, '%'))
                            {{ $sale->discount }}
                            @elseif (strpos($sale->discount, '%') == false)
                            {{ number_format($sale->discount) }}/-
                            @endif
                    </td>
                    @endif
                </tr>

                @endforeach
            </tbody>

            <tfoot>
                <tr class="bg-dark">
                    <th colspan="5">Total</th>
                    {{-- <th>{{ $total_discount_qty }}pc</th> --}}
                    <th>{{ number_format($total_order_price) }}/-</th>
                    <th>{{ number_format($total_returned_price) }}/-</th>
                    <th>{{ number_format($total_damage_price) }}/-</th>
                    {{-- <th>{{ $total_discount_returned }}pc</th> --}}
                    {{-- <th>{{ $total_damage }}pc</th> --}}
                    <th>{{ number_format($total_net_sale) }}/-</th>
                    <th>{{ number_format($total_paid) }}/-</th>
                    <th>{{ number_format($total_due)}} /-</th>
                            @can('pos-profit')
                    <th>{{ number_format($total_profit) }}/-</th>
                    @endcan
                    <th></th>
                    <th class="print_hidden"></th>
                </tr>
            </tfoot>

        </table>
        {!! $sales->appends(Request::except('_token'))->links() !!}
        @else
        <div class="alert alert-danger text-center" role="alert">
            <strong>You have no Sales List </strong>
        </div>
        @endif
    </div>
</div>
@endsection

@section('styles')

<style>
    .top-summary td {
        width: 12.5%;
        font-size: 1.5em;
        vertical-align: middle !important;
    }

    .table td,
    .table th {
        padding: 7px;
        vertical-align: baseline;
        border-top: 1px solid #e9ecef;
        text-align: center;
    }

    .product-list li {
        text-align: left;
    }

    tr.details-row {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        /* Adjust timing and easing as needed */
    }

    tr.details-row.show {
        max-height: 500px;
        /* Adjust the max height according to your content */
    }
</style>

@endsection
@section('scripts')
@include('includes.delete-alert')
@include('includes.placeholder_model')
<script src="{{ asset('js/modal_form.js') }}"></script>

<script>
    $(document).on('click', '#show_more', function(){
    var saleDetailsId = $(this).data('saledetailsid');
    var detailsSale = $('#detailsSale-'+saleDetailsId);

    if (detailsSale.hasClass('d-none')) {
        detailsSale.fadeIn('slow').removeClass('d-none');
    } else {
        detailsSale.addClass('d-none');
    }
});
</script>
@endsection