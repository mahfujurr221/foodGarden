{{-- az-gallery --}}
@extends('layouts.master')
@section('title', 'Pos Receipt')


@section('content')
<div class="col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-7 card card-body print">
            <div id="print-area">
                <div class="invoice-header">
                    <address>
                        @if ($pos_setting->invoice_logo_type == 'Logo' && $pos_setting->logo != null)
                        <img src="{{ asset($pos_setting->logo) }}" alt="logo">
                        <br>
                        @elseif($pos_setting->invoice_logo_type=="Name"&&$pos_setting->company!=null)
                        <h4 style="font-weight:bold">{{ $pos_setting->company }}</h4>
                        @else
                        <img src="{{ asset($pos_setting->logo) }}" alt="logo">
                        <h4 style="font-weight:bold; margin-top:10px;">{{ $pos_setting->company }}</h4>
                        @endif
                        Address : <strong>{{ $pos_setting->address }}</strong>
                        <br>
                        Phone : <strong>{{ $pos_setting->phone }}</strong>
                        <br>
                        Email : <strong>{{ $pos_setting->email }}</strong>
                    </address>
                </div>

                <div class="bill-date">
                    <div class="bill-no">
                        Invoice No: {{ $estimate->id }}
                    </div>
                    <div class="date">
                        Date: <strong>{{ date('d M, Y', strtotime($estimate->estimate_date)) }}</strong>
                    </div>
                </div>
                <div class="deatails">
                    <div class="name">
                        Client Name : <span>{{ $estimate->customer ? $estimate->customer->name : '' }}</span>
                    </div>
                    <div class="address">
                        Address : <span>{{ $estimate->customer ? $estimate->customer->address : ''}}</span>
                    </div>
                    <div class="mobile-no">
                        Mobile : <span>{{ $estimate->customer ? $estimate->customer->phone : ''}}</span>
                    </div>
                    <div class="time">
                        Time : <span>{{ $estimate->created_at->format('H:i:s')}}</span>
                    </div>
                </div>

                {{-- <div class="clearfix"></div> --}}
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
                        <td>{{ $item->product_name }}</td>
                        <td>@if($item->main_unit_qty){{ $item->main_unit_qty }} {{ $item->product->main_unit->name }}@endif @if($item->sub_unit_qty) {{ $item->sub_unit_qty }} {{ $item->product->sub_unit?$item->product->sub_unit->name:"" }} @endif</td>
                        <td>{{ $item->rate }} Tk</td>
                        <td>{{ $item->sub_total }} Tk</td>
                    </tr>
                    @endforeach

                    <tr>
                        <td colspan="3" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Total : </td>
                        <td>
                            <strong>{{ number_format($estimate->items->sum('sub_total'),2) }} </strong>Tk
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Grand Total : </td>
                        <td>
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
        border-bottom: 1px solid rgb(8, 8, 8);
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
        font-size: 26px;
    }

    .invoice-header .logo-area {
        width: 100%;
        text-align: center;
        padding: 5px;
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
        border: 0px solid #e9ecef;
    }

    .table-bordered td,
    .table-bordered th {
        border: 0px solid #e9ecef;
    }

    .table tbody th {
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
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
            border: 0px solid #e9ecef;
        }

        .table-bordered td,
        .table-bordered th {
            border: 0px solid #e9ecef !important;
        }

        .table tbody th {
            border-top: 1px solid #000 !important;
            border-bottom: 1px solid #000 !important;
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

    address img{
        max-width:40%;
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
