{{-- Sparrow-mart --}}
@extends('layouts.master')
@section('title', 'Pos Receipt')

@section('content')
<div class="col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-7 card card-body print">
            <div id="print-area">
                <div class="invoice-header">
                    <div class="logo-area">
                        @if ($pos_setting->invoice_logo_type == 'Logo' && $pos_setting->logo != null)
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
                    </address>

                </div>

                <div class="bill-date">
                    <div class="bill-no">
                        Invoice : {{ $estimate->id }}
                    </div>
                    <div class="date">
                        Date: <strong>{{ date('d M, Y', strtotime($estimate->estimate_date)) }}</strong>
                    </div>
                </div>
                <div class="name">
                    Client Name : <span>{{ $estimate->customer ? $estimate->customer->name : 'Walking Customer' }}</span>
                </div>
                <div class="address">
                    Address : <span>{{ $estimate->customer ? $estimate->customer->address : 'Walking Customer' }}</span>
                </div>
                <div class="mobile-no">
                    Mobile : <span>{{ $estimate->customer ? $estimate->customer->phone : 'Walking Customer' }}</span>
                </div>

                {{-- items Design --}}
                <table class="table table-bordered table-plist my-3 order-details">
                    <tr class="">
                        <th>#</th>
                        <th>Details</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Net.A</th>
                    </tr>
                    @foreach ($estimate->items as $key => $item)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $item->product_name }} - Size: {{ $item->size->size??'-' }}</td>
                        <td>@if($item->main_unit_qty){{ $item->main_unit_qty }} {{ $item->product->main_unit->name }}@endif @if($item->sub_unit_qty) {{ $item->sub_unit_qty }} {{ $item->product->sub_unit?$item->product->sub_unit->name:"" }} @endif</td>
                        <td>{{ $item->rate }} Tk</td>
                        <td>{{ $item->sub_total }} Tk</td>
                    </tr>
                    @endforeach

                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td colspan="2">Total : </td>
                        <td colspan="2">
                            <strong>{{ number_format($estimate->items->sum('sub_total'),2) }} </strong>Tk
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td colspan="2">Grand Total : </td>
                        <td colspan="2">
                            <strong>{{ number_format($estimate->receivable,2) }} </strong>Tk
                        </td>
                    </tr>
                </table>

                @php
                $digit = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                @endphp
                <p><strong>In Word: </strong> {{ ucwords($digit->format($estimate->receivable)) }} only</p>
                <p>Note: {{ $estimate->note }}</p>
                <hr>
                <p class="text-center lead"><small>Software Developed by SOFTGHOR. For query: 01779724380</small>
                    <p />
            </div>
            <button class="btn btn-secondary btn-block print_hidden" onclick="print_receipt('print-area')">
                <i class="fa fa-print"></i>
                Print
            </button>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://fonts.googleapis.com/css?family=Petrona&display=swap" rel="stylesheet">
<style rel="stylesheet">
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
        border-bottom: 1px dashed rgb(8, 8, 8);
        margin-bottom: 10px;
    }

    .invoice-header address {
        width: 100%;
        text-align: center;
        padding: 5px;
    }

    .logo-area img {
        @if($pos_setting->invoice_logo_type=="Both") width: 30%;
        @else width: 40%;
        @endif display: inline;
        /*float: left;*/
    }

    .logo-area h1 {
        display: inline;
        float: left;
        font-size: 17px;
        padding-left: 8px;
    }

    .logo-area h4 {
        font-weight: bold;
        font-size: 26px;
    }

    .invoice-header .logo-area {
        width: 100%;
        text-align: center;
        /*padding: 5px;*/
    }

    .bill-date {
        width: 100%;
        overflow: hidden;
        padding: 0 15px;
    }

    .date {
        width: 50%;
        float: right;
        text-align: end;
    }

    .bill-no {
        width: 50%;
        float: left;
    }

    .name,
    .address,
    .saler,
    .time,
    .mobile-no,
    .cus_info {
        width: 100%;
        /* border-left: 1px solid #ccc; */
        /* border-bottom: 1px solid #ccc; */
        /* border-right: 1px solid #ccc; */
        padding: 0 15px;
    }

    .name span,
    .address span,
    .mobile-no span,
    .cus_info span,
    .saler span,
    .time span {
        padding-left: 5px;
        font-weight: 800;
    }

    .sign {
        width: 250px;
        border-top: 1px solid #000;
        float: right;
        margin: 40px 20px 0 0;
        text-align: center;
    }

    .table-bordered {
       border-top: 1px dashed #000;
        border-bottom: 1px dashed #000;
        border-left: 0px;
        border-right: 0px;
    }

    .table-bordered td,
    .table-bordered th {
        border-top: 1px dashed #000;
        border-bottom: 1px dashed #000;
        border-left: 0px;
        border-right: 0px;
    }

    .table tbody th {
        border-top: 1px dashed #000;
        border-bottom: 1px dashed #000;
        border-left: 0px;
        border-right: 0px;
    }

    @media print {
        body * {
            visibility: visible;
            color: #000 !important;
            font-size: 10px !important;
            line-height: 12px;
            font-weight:800 !important;
        }

        .table-rheader td {
            border-top: 0px;
            padding: 5px !important;
            vertical-align: baseline !important;
        }

        .table-plist td {
            padding: 5px !important;
            text-align: left !important;
            width: 300px !important;
        }

        .table-plist th {
            padding: 5px;
            text-align: left !important;
            width: 300px !important;
        }

        .border-bottom {
            /* border-bottom: 1px dotted #CCC; */
        }

        .print {
            margin-bottom: 0;
        }

        .customers,
        .authorized {
            line-height: 2;
            margin-top:15px;
        }

        .table-bordered {
            border-top: 1px dashed #000 !important;
            border-bottom: 1px dashed #000 !important;
            border-left: 0px !important;
            border-right: 0px !important;
        }

        .table-bordered td,
        .table-bordered th {
            border-top: 1px dashed #000 !important;
            border-bottom: 1px dashed #000 !important;
            border-left: 0px !important;
            border-right: 0px !important;
        }

        .table tbody th {
            border-top: 1px dashed #000 !important;
            border-bottom: 1px dashed #000 !important;
            border-left: 0px !important;
            border-right: 0px !important;
        }
        
        .lead {
            margin-top: -43px !important;
            line-height: 2;
        }

    }

    body {
        font-family: 'Petrona', serif;
    }

    .bill-no,
    .date,
    .saler,
    .time,
    .name,
    .mobile-no,
    .address,
    th,
    td,
    address,
    h4 {
        color: black;
    }

    .saler {
        float: left;
        width: 50%;
    }

    .time {
        float: right;
        text-align: end;
        width: 50%;
    }
</style>
{{--
<link rel="stylesheet" href="{{ asset('dashboard/css/receipt.css') }}"> --}}

<style>
    .table-rheader td {
        border-top: 0px;
        padding: 5px;
        vertical-align: baseline !important;
    }

    .table-plist td {
        padding: 5px;
        text-align: center !important;
    }

    .table-plist th {
        padding: 5px;
        text-align: center;
        /* background: #ddd; */
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
