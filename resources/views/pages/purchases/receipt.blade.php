@extends('layouts.master')
@section('title', 'Purchase Receipt')


@section('content')
<div class="col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-7 card card-body">
            <div id="print-area">
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

                <div class="bill-date">
                    <div class="bill-no">
                        Invoice No: {{ $purchase->id }}
                    </div>
                    <div class="date">
                        Date: <strong>{{ date('d M, Y', strtotime($purchase->purchase_date)) }}</strong>
                    </div>
                </div>
                <div class="name">
                    Supplier Name :
                    <span>{{ $purchase->supplier->name }}</span>
                </div>
                <div class="address">
                    Address : <span>{{ $purchase->supplier->address }}</span>
                </div>
                <div class="mobile-no">
                    Mobile : <span>{{ $purchase->supplier->phone }}</span>
                </div>

                {{-- <div class="clearfix"></div> --}}
                {{-- items Design --}}
                <table class="table table-bordered table-plist my-3 order-details">
                    <tr class="bg-primary">
                        <th>#</th>
                        <th>Details</th>
                        <th>Qty</th>
                        {{-- <th>Discount Qty</th> --}}
                        <th>Price</th>
                        <th>Net.A</th>
                    </tr>
                    @foreach ($purchase->items as $key => $item)
                    @php
                    // $grandTotal += $item->qty * $item->rate;
                    @endphp
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $item->product->name }} | {{ $item->product->code }} </td>
                        <td>@if($item->main_unit_qty){{ $item->main_unit_qty }} {{ $item->product->main_unit->name }}@endif @if($item->sub_unit_qty) {{ $item->sub_unit_qty }} {{ $item->product->sub_unit?$item->product->sub_unit->name:"" }} @endif</td>
                        {{-- <td>{{ $item->discount_qty }} pc</td> --}}
                        <td>{{ number_format($item->rate, 2) }} Tk</td>
                        <td>{{ number_format($item->sub_total, 2) }} Tk</td>
                    </tr>
                    @endforeach

                    <tr>
                        <td colspan="4" class="text-right">Grand Total : </td>
                        <td>
                            <strong>{{ $purchase->payable }} </strong>Tk
                        </td>
                    </tr>
                    {{-- <tr>
                        <td colspan="4" class="text-right">Discount : </td>
                        <td>
                            <strong>{{ $purchase->discount }} </strong>
                        </td>
                    </tr> --}}
                    <tr>
                        <td colspan="4" class="text-right">Paid : </td>
                        <td>
                            <strong>{{ number_format($purchase->paid, 2) }} </strong>Tk
                        </td>
                    </tr>

                    <tr>
                        <td colspan="4" class="text-right"> Due : </td>
                        <td>
                            <strong>{{ number_format($purchase->due, 2)  }}
                            </strong>Tk
                        </td>
                    </tr>

                </table>

                <p class="note">Note: {{ $purchase->note }}</p>


            </div>
            <button class="btn btn-secondary btn-block" onclick="print_receipt('print-area')">
                <i class="fa fa-print"></i>
                Print
            </button>
            <div class="row mt-4">
                <div class="col-6">
                    <a href="{{ route('purchase.create') }}" class="btn btn-primary btn-block">
                        <i class="fa fa-reply"></i>
                        New Purchase
                    </a>
                </div>

                <div class="col-6">
                    <a href="{{ route('purchase.index') }}" class="btn btn-primary btn-block">
                        <i class="fa fa-reply"></i>
                        Purchase List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://fonts.googleapis.com/css?family=Petrona&display=swap" rel="stylesheet">
<style rel="stylesheet">
    .card *{
        color:black !important;
    }

    .signature {
        margin-top: 50px;
        display: flex;
        justify-content: space-between;
    }

    .signature p {
        margin-top: -10px;
    }


    .order-details th {
        font-weight: bold;
    }

    strong {
        font-weight: 800;
    }

    address {
        margin-bottom: 0px;
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
        @if($pos_setting->invoice_logo_type=="Both") width: 30%;
        @else width: 40%;
        @endif display: inline;
        float: left;
    }

    .logo-area h1 {
        display: inline;
        float: left;
        font-size: 17px;
        padding-left: 8px;
    }

    .logo-area h4 {
        font-weight: bold;
        margin-top: 5px;
    }

    .invoice-header .logo-area {
        width: 50%;
        float: left;
        padding: 5px;
    }

    .bill-date {
        width: 100%;
        border: 1px solid #ccc;
        overflow: hidden;
        padding: 0 15px;
    }

    .date {
        width: 50%;
        float: left;
    }

    .bill-no {
        width: 50%;
        float: left;
    }

    .name,
    .address,
    .mobile-no,
    .cus_info {
        width: 100%;
        border-left: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
        border-right: 1px solid #ccc;
        padding: 0 15px;
    }

    .name span,
    .address span,
    .mobile-no span,
    .cus_info span {
        padding-left: 15px;
        font-weight: 800;
    }

    .sign {
        width: 250px;
        border-top: 1px solid #000;
        float: right;
        margin: 40px 20px 0 0;
        text-align: center;
    }

    @media print {
        body * {
            visibility: visible;
        }

        .table-rheader td {
            border-top: 0px;
            padding: 5px;
            vertical-align: baseline !important;
        }

        .table-plist td {
            padding: 5px;
            text-align: center;
        }

        .table-plist th {
            padding: 5px;
            text-align: center;
        }

        .border-bottom {
            border-bottom: 1px dotted #CCC;
        }
    }

    body {
        font-family: 'Petrona', serif;
    }

    .bill-no,
    .date,
    .name,
    .mobile-no,
    .address,
    th,
    td,
    address,
    h4 {
        color: black;
    }
</style>
{{-- <link rel="stylesheet" href="{{ asset('dashboard/css/receipt.css') }}"> --}}

<style>
    .table-rheader td {
        border-top: 0px;
        padding: 5px;
        vertical-align: baseline !important;
    }

    .table-plist td {
        padding: 5px;
        text-align: center;
    }

    .table-plist th {
        padding: 5px;
        text-align: center;
        background: #ddd;
    }

    .border-bottom {
        border-bottom: 1px dotted #CCC;
    }
</style>
@endsection

@section('scripts')
<script>
    // clear localstore
    localStorage.removeItem('pos-items');

    function print_receipt(divName) {
        let printDoc = $('#' + divName).html();
        let originalContents = $('body').html();
        $("body").html(printDoc);
        window.print();
        $('body').html(originalContents);
    }

</script>
@endsection
