@extends('layouts.master')
@section('title', 'Pos Receipt')


@section('content')
<div class="col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-7 card card-body print">
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
                </div>

                <h2>Purchase Cost BreakDown</h2>

                <?php 
                    $background_colors = array('#FFE194', '#A3CFA7', '#F7DCEC', '#FFF1DE','#FFE4C2','#F8BEB5','#EA9FAC','#C76F9B','#9676B6','#7393C9','#EDBE71','#EFD790','#FCF2CD','#DFE6BE','#97C1AB','#61AC95');
                ?>
                
                <table class="table table-bordered table-plist my-3 order-details">
                    <tr>
                        <th rowspan="2" style="vertical-align:middle;">#</th>
                        <th rowspan="2" style="vertical-align:middle;">Product</th>
                        <th rowspan="2" style="vertical-align:middle;">Qty</th>
                        <th colspan="5">Details</th>
                        {{--<th rowspan="2">Total Cost</th>--}}
                    </tr>
                    <tr>
                        <th>Purchase Id</th>
                        <th>Purchase Item Id</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Cost</th>
                    </tr>
                    @php 
                        $total_cost=0;
                    @endphp
                    @foreach ($pos->items as $key => $item)
                        @php
                            $rowspan=$item->stock->count();
                            $rand_background = $rowspan>1?$background_colors[array_rand($background_colors)]:'';
                        @endphp
                        <tr style="background:{{ $rand_background }}">
                            <td rowspan="{{ $rowspan }}">{{ ++$key }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $item->product_name }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $item->product->readable_qty($item->qty) }}</td>
                            @foreach($item->stock as $stock)
                                @if($rowspan>1&&$loop->iteration>1)
                                    <tr style="background:{{ $rand_background }}">
                                @endif
                                    <td>{{ $stock->purchase_id }}</td>
                                    <td>{{ $stock->purchase_item_id }}</td>
                                    <td>{{ $item->product->readable_qty($stock->qty) }}</td>
                                    <td>{{ $unit_price=$stock->purchase_item->rate }}</td>
                                    <td>{{ $total=$item->product->quantity_worth($stock->qty,$unit_price),$total_cost+=$total; }} Tk</td>
                                    
                                    {{--@if($loop->first)
                                    <td rowspan="{{ $rowspan }}" style="vertical-align:middle;">{{ $total_cost,$footer_costing+=$total_cost }} Tk</td>
                                    @endif--}}
                                @if($rowspan>1&&$loop->iteration>1)
                                    </tr>
                                @endif
                            @endforeach
                            
                        </tr>
                    @endforeach
                    
                        <tr>
                            <td colspan="7" style="text-align:right;">Total:</td>
                            <td>{{ $total_cost }} Tk</td>
                        </tr>
                </table>

            </div>




            <div class="row">
                <div class="col-12 text-center">
                    <button class="btn btn-secondary print_hidden" onclick="print_receipt('print-area')">
                        <i class="fa fa-print"></i>
                        Print
                    </button>
                </div>
            </div>


        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://fonts.googleapis.com/css?family=Petrona&display=swap" rel="stylesheet">
<style rel="stylesheet">
    .page-footer hr{
        margin:2px;
    }

    .signature {
        margin-top: 50px;
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
    }

    .invoice-header .logo-area {
        width: 50%;
        float: left;
        padding: 5px;
    }

    .bill-date {
        width: 100%;
        border: 1px solid #000;
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
        border-left: 1px solid #000;
        border-bottom: 1px solid #000;
        border-right: 1px solid #000;
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
            padding: 0px;
            text-align: center;
        }

        .table-plist th {
            padding: 0px;
            text-align: center;
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

</style>
{{-- <link rel="stylesheet" href="{{ asset('dashboard/css/receipt.css') }}"> --}}

<style>
    .table-rheader td {
        border-top: 0px;
        padding: 5px;
        vertical-align: baseline !important;
    }

    .table td {
        padding: 0px;
        text-align: center;
    }

    .table th {
        padding: 0px;
        text-align: center;
        background: #ddd;
    }

    .border-bottom {
        border-bottom: 1px dotted #000;
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
@include('includes.delete-alert')
@include('includes.placeholder_model')
<script src="{{ asset('js/modal_form.js') }}"></script>
@endsection
