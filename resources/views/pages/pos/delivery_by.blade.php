@extends('layouts.master')
@section('title', 'Sales List')

@section('page-header')
<div class="header-info mb-1">
    <h1 class="header-title">
        <strong>Delivery Details</strong>
    </h1>
</div>
@endsection

@section('content')
<div class="card print_area mt-1" style="width:100%;">
    <div class="row">
        <div class="col-12" style="display:flex; justify-content:space-between">
            <div class="col-2">
                <h4 class="card-title"><strong>Delivery Details</strong></h4>
            </div>
            <div class="col-md-9 print_hidden">
                <form action="#">
                    <div class="form-row mt-3">
                        <div class="form-group col-md-2">
                            <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                                data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                                class="form-control" name="start_date" placeholder="Start Date" autocomplete="off"
                                value="{{ request('start_date')??date('Y-m-d') }}">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                                data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                                class="form-control" name="end_date" placeholder="End Date" autocomplete="off"
                                value="{{ request('end_date')??date('Y-m-d') }}">
                        </div>

                        <div class="form-group col-1">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-sliders"></i>
                                Filter
                            </button>
                        </div>

                        <div class="form-group col-1">
                            <a href="{{ route('pos.delivery_by') }}" class="btn btn-info">Reset</a>
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
            <thead>
                <tr class="bg-primary">
                    <th>#</th>
                    <th>Delivery By</th>
                    <th>Total Order Tk</th>
                    <th>Total Returned Tk</th>
                    <th>Total Damaged Tk</th>
                    <th>Total Discount</th>
                    <th>Net Sale</th>
                    <th>Total Due</th>
                    <th>Total Collection</th>
                </tr>
            </thead>
            @php
            $grand_total_order_price = 0;
            $grand_total_returned_price = 0;
            $grand_total_damage_price = 0;
            $grand_total_due = 0;
            $grand_total_collection = 0;
            @endphp
            <tbody>
                @foreach ($sales as $key => $group)
                @php
                $total_order_price = $group->sum(function ($sale) {
                    return $sale->items->sum('ordered_sub_total');
                });
                $total_returned_price = $group->sum(function ($sale) {
                    return $sale->items->sum('returned_value');
                });
                $total_damage_price = $group->sum(function ($sale) {
                    return $sale->items->sum('damaged_value');
                });
                $total_discount = $group->sum('discount');
                $total_net_sale = $total_order_price - $total_returned_price - $total_damage_price - $total_discount;
                $total_due = $group->sum('due');
                $total_collection = $total_net_sale - $total_due;
        
                $grand_total_order_price += $total_order_price;
                $grand_total_returned_price += $total_returned_price;
                $grand_total_damage_price += $total_damage_price;
                $grand_total_due += $total_due;
                $grand_total_collection += $total_collection;
                @endphp
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $group->first()->sales_man->fname }}</td>
                    <td>{{ number_format($total_order_price) }} Tk</td>
                    <td>{{ number_format($total_returned_price) }} Tk</td>
                    <td>{{ number_format($total_damage_price) }} Tk</td>
                    <td>{{ number_format($total_discount) }} Tk</td>
                    <td>{{ number_format($total_net_sale) }} Tk</td>
                    <td>{{ number_format($total_due) }} Tk</td>
                    <td>{{ number_format($total_collection) }} Tk</td>
                </tr>
                @endforeach
            </tbody>
        
            <tfoot>
                <tr class="bg-dark">
                    <th colspan="2">Grand Total</th>
                    <th>{{ number_format($grand_total_order_price) }} Tk</th>
                    <th>{{ number_format($grand_total_returned_price) }} Tk</th>
                    <th>{{ number_format($grand_total_damage_price) }} Tk</th>
                    <th>{{ number_format($sales->flatten()->sum('discount')) }} Tk</th>
                    <th>{{ number_format($grand_total_order_price - $grand_total_returned_price - $grand_total_damage_price - $sales->flatten()->sum('discount')) }} Tk</th>
                    <th>{{ number_format($grand_total_due) }} Tk</th>
                    <th>{{ number_format($grand_total_collection) }} Tk</th>
                </tr>
            </tfoot>
        </table>
        {{-- {!! $sales->appends(Request::except('_token'))->links() !!} --}}
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
@endsection