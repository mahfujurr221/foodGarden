@extends('layouts.master')
@section('title', 'Pos Receipt')


@section('content')
<div class="col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-5 card card-body">
            <div id="print-area">
                <div class="invoice-header">
                    <div class="logo-area">
                        @if($pos_setting->logo!=null)
                        @if($pos->order_of=="edokani")
                        <img src="/edokani.jpg" alt="logo">
                        @else
                        <img src="{{ asset($pos_setting->logo) }}" alt="logo">
                        @endif
                        @else
                        <h1 class="title">{{ $pos_setting->company }}</h1>
                        @endif
                    </div>
                    <address>
                        {{ $pos_setting->address }}
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
                        Bill No: {{ $pos->id }}
                    </div>
                    <div class="date">
                        Date: <strong>{{ date('d M, Y', strtotime($pos->sale_date)) }}</strong>
                    </div>
                </div>
                <div class="name">
                    Client Name : <span>{{ $pos->customer ? $pos->customer->name : 'Walk-in Customer' }}</span>
                </div>
                <div class="address">
                    Address : <span>{{ $pos->customer ? $pos->customer->address : 'Walk-in Customer'}}</span>
                </div>
                <div class="mobile-no">
                    Mobile : <span>{{ $pos->customer ? $pos->customer->phone : 'Walk-in Customer'}}</span>
                </div>

                {{-- <div class="cus_info">
                    Courier : <span>{{ $pos->delivery_method ? $pos->delivery_method->name : '-'}}</span>
                </div> --}}

                {{-- cus_info --}}



                {{-- <div class="clearfix"></div> --}}
                {{-- items Design --}}
                @php
                    // $total_quantity=0;
                @endphp
                <table class="table table-bordered table-plist my-3">
                    <tr class="bg-primary">
                        <th>#</th>
                        <th>Product</th>
                        <th>Quantity</th>
                    </tr>
                    @foreach ($pos->items as $key => $item)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->product->readable_qty($item->qty) }}</td>
                    </tr>
                    @endforeach
                    {{-- <tfoot>
                        <tr>
                            <td colspan="2" class="text-right">Total Quantity : </td>
                            <td>
                                <strong>{{ $total_quantity }} </strong>
                            </td>
                        </tr>
                    </tfoot> --}}
                </table>
            </div>
            <button class="btn btn-secondary btn-block" onclick="print_receipt('print-area')">
                <i class="fa fa-print"></i>
                Print
            </button>
            <a href="{{ route('pos.index') }}" class="btn btn-primary btn-block">
                <i class="fa fa-reply"></i>
                Sales List
            </a>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://fonts.googleapis.com/css?family=Petrona&display=swap" rel="stylesheet">
<style rel="stylesheet">
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
        width: 40%;
        display: inline;
        float: left;
    }

    .logo-area h1 {
        display: inline;
        float: left;
        font-size: 17px;
        padding-left: 8px;
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
    .mobile-no,.cus_info {
        width: 100%;
        border-left: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
        border-right: 1px solid #ccc;
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
            border-bottom: 1px dotted #CCC;
        }
    }

    body {
        font-family: 'Petrona', serif;
    }

    .bill-no,.date,.name,.mobile-no,.address,th,td,address,h4{
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

    .table-plist td {
        padding: 0px;
        text-align: center;
    }

    .table-plist th {
        padding: 0px;
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
