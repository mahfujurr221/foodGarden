@extends('layouts.master')
@section('title', 'Customer Ledger')

@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Customer Ledger</strong>
        </h1>
    </div>
</header>
@endsection

@section('content')

<div class="col-12">

    <div class="card card-body mb-2">
            <form action="">
                <div class="form-row">
                    <div class="form-group col-3">
                        <select name="customer_id" id="" class="form-control" data-provide="selectpicker"
                            data-live-search="true" data-size="10">
                            <option value="">Select a Customer</option>
                            @foreach (\App\Customer::all() as $item)
                            <option value="{{ $item->id }}" {{ request('customer_id')==$item->id?"SELECTED":"" }}>{{ $item->name }} {{ $item->phone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text" name="start_date" data-provide="datepicker"
                             data-date-today-highlight="true" data-orientation="bottom"
                             data-date-format="yyyy-mm-dd" data-date-autoclose="true" class="form-control"
                             placeholder="Enter Start Date" autocomplete="off" value="{{ request('start_date') }}">
                   </div>

                   <div class="form-group col-md-3">
                        <input type="text" name="end_date" data-provide="datepicker"
                             data-date-today-highlight="true" data-orientation="bottom"
                             data-date-format="yyyy-mm-dd" data-date-autoclose="true" class="form-control"
                             placeholder="Enter End Date" autocomplete="off" value="{{ request('end_date') }}">
                   </div>
                   <div class="form-group col-3">
                    <button class="btn btn-primary" type="submit">
                        <i class="fa fa-sliders"></i>
                        Filter
                    </button>
                    <a href="{{ request()->url() }}" class="btn btn-info">Reset</a>
                    <a href="" class="btn btn-primary float-right" onclick="window.print()">Print</a>
                </div>
                </div>
            </form>
        </div>

    {{-- <div class="card card-body">
        <div class="row">
            <div class="col-12">

            </div>
        </div>
    </div> --}}

    <div class="card col-12 print_area">
        {{-- <h4 class="card-title"><strong>Customer Ledger</strong></h4> --}}
        
        <div class="card-body">
            <div class="table-responsive-sm">

                <div class="invoice-header">
                    <div class="logo-area">
                        @if($pos_setting->invoice_logo_type=="Logo"&&$pos_setting->logo!=null)
                            <img src="{{ asset($pos_setting->logo) }}" alt="logo">
                        @elseif($pos_setting->invoice_logo_type=="Name"&&$pos_setting->company!=null)
                        {{-- <img src="{{ asset($pos_setting->logo) }}" alt="logo"> --}}
                            <h4>{{ $pos_setting->company }}</h4>
                        @else
                            <img src="{{ asset($pos_setting->logo) }}" alt="logo">
                            <div class="clearfix"></div>
                            <h4>{{ $pos_setting->company }}</h4>
                        @endif
                    </div>
                    <address>
                        Address : <strong>{{ $pos_setting->address }}</strong>
                        <br>
                        Phone : <strong>{{ $pos_setting->phone }}</strong>
                        <br>
                        Email : <strong>{{ $pos_setting->email }}</strong>
                        {{-- <br>
                        Facebook Page : <strong>{{ $pos_setting->page_link }}</strong>
                        <br>
                        Website : <strong>{{ $pos_setting->website }}</strong> --}}
                    </address>
                </div>

                @if(request('customer_id'))
                    @php
                        $customer = \App\Customer::find(request('customer_id'));
                    @endphp
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width:30%;">Account of:</th>
                                <th>{{ $customer->name??'' }}</th>
                            </tr>
                            <tr>
                                <th style="width:30%;">Shop Name:</th>
                                <th>{{ $customer->shop_name_bangla??'' }}</th>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <th>{{ $customer->address->name??'' }}</th>
                            </tr>
                            <tr>
                                <th>Contact No:</th>
                                <th>{{ $customer->phone??'' }}</th>
                            </tr>
                        </tbody>
                    </table>
                    @endif

                    <hr>
                    <h3 style="text-align: center; font-weight:bold; margin-top:50px;">Customer Ledger</h3>

                    <table class="table table-bordered" data-provide="">
                        <thead>
                            <tr class="bg-primary">
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                                {{-- <th class="print_hidden">#</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Purchase Items --}}
                            @forelse($transactions as $key => $item)
                            <tr>
                                <td>{{ $item->date }}</td>
                                <td>{{ $item->particulars }}</td>
                                <td>{{ $item->debit }}</td>
                                <td>{{ $item->credit }}</td>
                                <td>{{ $item->balance }}</td>
                            </tr>

                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="alert alert-danger" role="alert">
                                            <strong>You have no Transactions</strong>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                {{-- @else
                    <div class="alert alert-danger" role="alert">
                        <strong>Please Select a Customer</strong>
                    </div>
                @endif --}}
                {{-- {!! $purchases->appends(Request::except("_token"))->links() !!} --}}
            </div>

        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    table th, table td{
        padding:5px !important;
    }

    .invoice-header {
        width: 100%;
        display: block;
        box-sizing: border-box;
        overflow: hidden;
    }

    .invoice-header address {
        width: 50%;
        float: left;
        padding: 5px;
    }

    .logo-area img {
        @if($pos_setting->invoice_logo_type=="Both")
            width: 30%;
        @else
            width: 40%;
        @endif
        display: inline;
        float: left;
    }

    .logo-area h1 {
        display: inline;
        float: left;
        font-size: 17px;
        padding-left: 8px;
    }

    .logo-area h4{
        font-weight: bold;
        margin-top:5px;
    }

    .invoice-header .logo-area {
        width: 50%;
        float: left;
        padding: 5px;
    }

</style>

<style>
    @media print{
        table,table th,table td{
            color:black !important;
        }
    }
</style>
@endsection

@section('scripts')
@include('includes.delete-alert')
<script>

</script>
@endsection
