@extends('layouts.master')
@section('title', 'Return List')

@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Return List</strong>
        </h1>
    </div>
</header>
@endsection

@section('content')
    <div class="col-12" style="">

        <div class="card card-body mb-2">
            <form action="">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                               data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                               class="form-control" name="start_date" placeholder="Start Date" autocomplete="off">
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                               data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                               class="form-control" name="end_date" placeholder="End Date" autocomplete="off">
                    </div>

                    <div class="form-group col-md-4">
                          <input type="text" name="pos_id" value="{{ request("pos_id") }}" class="form-control" placeholder="Pos Id">
                    </div>

                    <div class="form-group col-md-4">
                        <select name="customer" id="" class="form-control" data-provide="selectpicker"
                                data-live-search="true">
                            <option value="">Select Customer</option>
                            @foreach (\App\Customer::all() as $item)
                                <option value="{{ $item->id }}">{{ $item->name." - ".$item->phone }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row mt-2">
                    <div class="form-group float-right">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-sliders"></i>
                            Filter
                        </button>
                        <a href="{{ route('return.index') }}" class="btn btn-info">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <h4 class="card-title"><strong>Return List</strong></h4>

            <div class="card-body">
                @if($returns->count() > 0)
                    <div class="">
                        <table class="table table-responsive table-bordered" data-provide="">
                            <thead>
                            <tr class="bg-primary">
                                <th>#</th>
                                <th>Bill No.</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Sell Date</th>
                                <th>Discount</th>
                                <th>Total</th>
                                {{-- <th>Should Pay</th>
                                <th>Decided To Pay</th>
                                <th>Paid</th>
                                <th>Due</th> --}}
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($returns as $key => $item)
                                <tr>
                                    <td>{{ $loop->iteration + $returns->perPage() * ($returns->currentPage() - 1)}}</td>
                                    <td>
                                        <a href="{{ route('pos_receipt',$item->pos->id) }}">Invoice#{{ $item->pos->id }}</a>
                                    </td>
                                    <td>
                                         {{ $item->pos->customer ? $item->pos->customer->name : 'Walk-in Customer' }}
                                    </td>
                                    <td>
                                        <ul class="product-list">
                                            @foreach ($item->items as $p)
                                                @php
                                                    $product=\App\Product::find($p->product_id);
                                                @endphp
                                                <li>{{ $product->name." * ".$product->readable_qty($p->qty) }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>{{ date('d M, Y', strtotime($item->pos->sale_date)) }}</td>
                                    <td>{{ number_format($item->calculated_discount) }} Tk</td>
                                    <td>{{ number_format($item->return_product_value) }} Tk</td>
                                    {{-- <td>{{ number_format($item->total_after_discount) }} Tk</td> --}}
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                                    aria-expanded="false">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu" x-placement="bottom-start">
                                                {{-- <a class="dropdown-item" href="{{ route('pos_receipt', $sale->id) }}">
                                                    <i class="fa fa-print"></i>
                                                    Print
                                                </a> --}}
                                                {{-- <a href="{{ route("return.add_payment",$item->id) }}" class="edit dropdown-item" data-toggle="modal" data-target="#edit" id="Add Payment">
                                                    <i class="fa fa-money text-primary"></i>
                                                    Add Payment
                                                </a> --}}

                                                    <a class="dropdown-item delete" href="{{ route('return.destroy',$item->id) }}" >
                                                        <i class="fa fa-trash"></i>
                                                        Delete
                                                    </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                        {!! $returns->appends(Request::except("_token"))->links() !!}
                    </div>
                @else
                    <div class="alert alert-danger text-center" role="alert">
                        <strong>You have no Returned List </strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .table td,
        .table th {
            padding: 7px;
            vertical-align: baseline;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }

        .card {
            margin-bottom: 0px;
        }

        .card-body {
            padding: 15px;
        }

        .center-cell-text {
            text-align: center;
            vertical-align: middle;
        }

        .table-cell {
            display: table-cell;
            min-height: 126px;
        }


        .product-list li {
            text-align: left;
        }

    </style>
@endsection

@section('scripts')
    @include('includes.delete-alert')
    @include('includes.placeholder_model')
    <script src="{{ asset('js/modal_form.js') }}"></script>
@endsection
