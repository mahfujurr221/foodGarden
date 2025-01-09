@extends('layouts.master')
@section('title', 'Pos Receipt')


@section('content')
<div class="col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-7 card card-body print">
            <div id="print-area">
                <div class="invoice-header">
                    <address>
                        <div>
                            @if($pos_setting->invoice_logo_type=="Logo"&&$pos_setting->logo!=null)
                                <img src="{{ asset($pos_setting->logo) }}" alt="logo" style="margin-bottom:10px;">
                            @elseif($pos_setting->invoice_logo_type=="Name"&&$pos_setting->company!=null)
                            {{-- <img src="{{ asset($pos_setting->logo) }}" alt="logo"> --}}
                                <h4 style="font-weight: bold">{{ $pos_setting->company }}</h4>
                            @else
                                <img src="{{ asset($pos_setting->logo) }}" alt="logo">
                                <h4 style="font-weight: bold; margin-bottom:0">{{ $pos_setting->company }}</h4>
                            @endif
                        </div>
                        
                        Address : {!! $pos_setting->address !!}
                         <br>
                        Phone : {{ $pos_setting->phone }}
                        <br>
                        Email : {{ $pos_setting->email }}
                        <br/>
                        
                    </address>
    
                    <div class="barcode">
                        <h4>Invoice</h4>
                        @php 
                            $barcode = '<img src="data:image/png;base64,' . (new Milon\Barcode\DNS1D)->getBarcodePNG(sprintf("%04s", $estimate->id), 'C39', 1, 30) . '" alt="BAR CODE" />';
                        @endphp
                         {!! $barcode !!}
                         <h6>#{{sprintf("%06s", $estimate->id)}}</h6>
                        <p> <strong> Date: </strong>{{ date('d M, Y', strtotime($estimate->estimate_date)) }}</p>
                    </div>

                </div>
                <hr style="background:#000;margin:5px"/>
                <div class="bill-date">
                </div>
                <div class="name">
                    Name : <span>{{ $estimate->customer ? $estimate->customer->name : 'Walk-in Customer' }}</span>
                </div>
                <div class="address">
                    Address : <span>{{ $estimate->customer ? $estimate->customer->address : 'Walk-in Customer'}}</span>
                </div>
                <div class="mobile-no">
                    Number : <span>{{ $estimate->customer ? $estimate->customer->phone : 'Walk-in Customer'}}</span>
                </div>

                {{-- <div class="clearfix"></div> --}}
                {{-- items Design --}}
                <table class="table table-bordered table-plist my-3 order-details">
                    <tr class="bg-primary">
                        {{--<th>#</th>--}}
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                    @foreach ($estimate->items as $key => $item)
                    <tr>
                        {{--<td>{{ ++$key }}</td>--}}
                        <td>{{ $item->product_name }}</td>
                        <td>@if($item->main_unit_qty){{ $item->main_unit_qty }} {{ $item->product->main_unit->name }}@endif @if($item->sub_unit_qty) {{ $item->sub_unit_qty }} {{ $item->product->sub_unit?$item->product->sub_unit->name:"" }} @endif</td>
                        <td>{{ $item->rate }} Tk</td>
                        <td>{{ $item->sub_total }} Tk</td>
                    </tr>
                    @endforeach

                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Total : </td>
                        <td>
                            <strong>{{ number_format($estimate->items->sum('sub_total'),2) }} </strong>Tk
                        </td>
                    </tr>
                    
                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Grand Total : </td>
                        <td>
                            <strong>{{ number_format($estimate->receivable,2) }} </strong>Tk
                        </td>
                    </tr>
                   
                </table>

                {{-- @php
                    $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                @endphp --}}
                <div class="footer">
                    <div class="invoice_footer">
                    <p class="note"><strong>Note:</strong> {{ $estimate->note }}</p>
                    <div class="signature">
                        <div class="customers text-center">
                            <span>--------------------------</span>
                            <p>Customer's Signature</p>
                        </div>
                        <div class="authorized text-center">
                            <span>--------------------------</span>
                            <p>Authorized Signature</p>
                        </div>
                    </div>
                  </div>
                </div>

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

    .footer{
        width:100%;
    }
    
    .invoice_footer{
        /*float:left;*/
    }
    .page-footer hr{
        margin:2px;
    }

    .signature {
        margin-top: 30px;
        display: flex;
        justify-content: space-between;
    }

    .signature p {
        margin-top: -10px;
    }


    .order-details th{
        font-weight:bold;
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
        font-size:24px;
    }

    .invoice-header .logo-area {
        width: 50%;
        float: left;
        padding: 5px;
        color:#000;
    }

    .bill-date {
        width: 100%;
        /*border: 1px solid #000;*/
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
    .mobile-no,.cus_info {
        width: 100%;
        /*border-left: 1px solid #000;*/
        /*border-bottom: 1px solid #000;*/
        /*border-right: 1px solid #000;*/
        padding: 0 15px;
    }

    .name span,
    .address span,
    .mobile-no span, .cus_info span {
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
    
    .barcode{
        float:right;
    }
    
    .barcode h4{
        color:red;
        font-size:25px;
        text-transform:uppercase;
        font-weight:600;
    }
    
    .barcode h6{
        
    }
    
    .barcode p {
    margin-top: -9px;
    color: #000;
    }
    
    .footer_content{}
    
    .left_footer_content{
        width:50%;
        float:left;
    }
    
    
    .left_footer_content h3 {
        color: #000;
        font-weight: 600;
        text-transform: uppercase;
    }
        
    .left_footer_content h3 span{
        color:red;
    }
    
    .left_footer_content p {
        color: #000;
        font-weight: 600;
        margin:0;
    }
    
    .right_footer_content {
        width: 50%;
        float: right;
        text-align: right;
    }
    
    
   .right_footer_content h1 {
        color: red;
        font-weight: bold;
        font-style: italic;
        margin-top: 80px;
    }
     
    @media print {
        body * {
            visibility: visible;
            color:#000;
        }

        .table-rheader td {
            border-top: 0px;
            padding: 5px;
            vertical-align: baseline !important;
        }

        .table-plist td {
            padding: 0px;
            text-align: center;
        }

        .table-plist th {
            padding: 0px;
            text-align: center;
            color: #000!important;
        }

        .border-bottom {
            border-bottom: 1px dotted #000;
        }
        .print{
            margin-bottom: 0;
        }

        .table-bordered td, .table-bordered th {
            border: 1px solid #000!important;
        }
    }

    body {
        font-family: 'Petrona', serif;
    }
    
    .note,.in_word,.signature,.bill-no,.date,.name,.mobile-no,.address,th,td,address,h4{
          color:black;
     }
    
    .note, .payment_method{
        color:black;
        font-size:15px;
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
        padding: 0px;
        text-align: center;
    }

    .table-plist th {
        padding: 0px;
        text-align: center;
        background: #00264C;
        color:#ffffff;
        border:0;
    }

    .border-bottom {
        border-bottom: 1px dotted #000;
    }


    address img{
        max-width:60%;
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
