@extends('layouts.master')
@section('title', 'Purchase Report')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Purchase Report</strong>
            </h1>
        </div>
    </header>
@endsection

@section('content')

    <div class="col-12">

        {{-- <div class="card card-body mb-2">
            <form action="{{ route('report.purchase_report') }}">
                <div class="form-row">
                    <div class="form-group col-4">
                        <select name="product_id" id="" class="form-control" data-provide="selectpicker"
                            data-live-search="true" data-size="10">
                            <option value="">Select a Product</option>
                            @foreach (\App\Product::all() as $item)
                                <option value="{{ $item->id }}"
                                    {{ isset($product_id) && $product_id == $item->id ? 'SELECTED' : '' }}>{{ $item->name }} -
                                    {{ $item->code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                               data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                               class="form-control" name="start_date" placeholder="Start Date" autocomplete="off" value="{{ request('start_date') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                               data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                               class="form-control" name="end_date" placeholder="End Date" autocomplete="off" value="{{ request('end_date') }}">
                    </div>
                </div>
                <div class="form-row mt-2">
                    <div class="form-group col-12">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-sliders"></i>
                            Filter
                        </button>
                        <a href="{{ route('report.purchase_report') }}" class="btn btn-info">Reset</a>
                        <a href="" class="btn btn-primary float-right" onclick="window.print()">Print</a>
                    </div>
                </div>

            </form>
        </div> --}}

        {{-- <div class="card card-body">
        <div class="row">
            <div class="col-12">

            </div>
        </div>
    </div> --}}

        <div class="card print_area" style="width:100%;">
            <h4 class="card-title"><strong>Purchase Report</strong></h4>

            <div class="card-body card-body-soft p-4">
                <div class="table-responsive table-responsive-sm">
                    <table class="table table-bordered" data-provide="datatables" data-page-length="100">
                        <thead>
                            <tr class="bg-primary">
                                <th>#</th>
                                <th>Date</th>
                                <th>Purchase No</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                                {{-- <th class="print_hidden">#</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Purchase Items --}}
                            @forelse($purchases as $key => $item)
                                <tr>
                                    <td scope="row">
                                        {{ $purchases->currentPage() != 1 ? $purchases->currentPage() * $purchases->perPage() + $key + 1 : ++$key }}
                                    </td>
                                    <td>{{ $item->purchase->purchase_date }}</td>
                                    <td>
                                        <a href="{{ route('purchase.receipt', $item->purchase_id) }}">Purchase#{{ $item->purchase_id }}</a>
                                    </td>
                                    <td>
                                        {{ $item->product->name }}
                                    </td>
                                    <td>
                                        {{ $item->product->readable_qty($item->qty) }}
                                    </td>
                                    <td>
                                        {{ number_format($item->rate) }} Tk
                                    </td>
                                    <td>
                                        {{ number_format($item->sub_total) }} Tk
                                    </td>

                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="alert alert-danger" role="alert">
                                            <strong>You have no Purchases</strong>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                    {!! $purchases->appends(Request::except('_token'))->links() !!}
                </div>

            </div>
        </div>
    </div>

@endsection

@section('styles')
    <style>
        @media print {

            table,
            table th,
            table td {
                color: black !important;
            }
        }
    </style>
@endsection

@section('scripts')
    @include('includes.delete-alert')
    <script></script>
@endsection
