@extends('layouts.master')
@section('title', 'Payment Invoice')


@section('content')
<div class="col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-7 card card-body print">
            <div id="print-area">
                <div class="invoice-header row">
                    <address class="col-8">
                        <div class="logo_n_name">
                            @if($pos_setting->invoice_logo_type=="Logo"&&$pos_setting->logo!=null)
                            <img src="{{ asset($pos_setting->logo) }}" alt="logo" style="margin-bottom:10px;">
                            @elseif($pos_setting->invoice_logo_type=="Name"&&$pos_setting->company!=null)
                            {{-- <img src="{{ asset($pos_setting->logo) }}" alt="logo"> --}}
                            <h2 style="font-weight: bold">{{ $pos_setting->company }}</h2>
                            @else
                            <img src="{{ asset($pos_setting->logo) }}" alt="logo">
                            <h2 style="font-weight: bold; margin-bottom:0">{{ $pos_setting->company }}</h2>
                            @endif
                        </div>
                        Address : {{ $pos_setting->address }}
                        <br>
                        Phone : {{ $pos_setting->phone }}
                        Email : {{ $pos_setting->email }}
                        <br />
                    </address>
                    @php
                        if ($payment->customer) {
                            $user = $payment->customer;
                        } elseif ($payment->supplier) {
                            $user = $payment->supplier;
                        }
                        $first_item=$payment->payments()->first();
                    @endphp
                    <div class="invoice col-4">
                        <div class="invoice-content">
                            <h3>INVOICE</h3>
                        </div>
                        <p><strong>Shop Name :</strong>{{ $user->shop_name_bangla ?? '' }}</p>
                        <p><strong>Name :</strong> {{ $user->name ?? '' }}</p>
                        <p><strong>Address</strong> {{ $user->address->name ?? '' }}</p>
                        <p><strong>Mobile : </strong>{{ $user->phone ?? '' }}</p>
                    </div>
                </div>
                <table class="table" style="margin-bottom:0px !important;">
                    <tr>
                        <td><strong>INVOICE : {{ $payment->id }}</td></strong>
                        <td class="text-right"><strong>DATE : {{ date('d/m/ Y', strtotime($payment->date)) }}</td>
                        </strong>
                    </tr>
                </table>
                {{-- <div class="clearfix"></div> --}}
                {{-- items Design --}}
                <table class="table table-bordered table-plist my-3 order-details">
                    <tr class="">
                        <th>Date</th>
                        <th>Previous Due</th>
                        <th>Due/Paid</th>
                        <th>Total Due</th>
                    </tr>
                    <tbody>
                        <tr>
                            <td>{{ date('d/m/Y', strtotime($payment->date)) }}</td>
                            <td>{{ number_format($payment->previous_due, 2) }}</td>
                            <td>{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ number_format($payment->due, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="footer-note">
                    <p class="note">Notes: {{ $payment->note }}</p>
                </div>
                <div class="signature">
                    <div class="customers text-center">
                        <span>--------------------------</span>
                        <p>Customer Signature</p>
                    </div>
                    <div class="authorized text-center">
                        <span>--------------------------</span>
                        <p>Authorized Signature</p>
                    </div>
                </div>
                <div class="page-footer">
                    <hr>
                    <p class="text-center lead"><small>Software Developed by SOFTGHOR LTD. For query:
                            01958-104250</small>
                        <p />
                </div>
            </div>
            <button class="btn btn-secondary btn-block print_hidden" onclick="print_receipt('print-area')">
                <i class="fa fa-print"></i>
                Print
            </button>

            <div class="row mt-4">
                <div class="col-4">
                    <a href="{{ route('payment.create') }}" class="btn btn-dark btn-block">
                        <i class="fa fa-reply"></i>
                        Create Payment
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('payment.customer-due-payment') }}" class="btn btn-dark btn-block">
                        <i class="fa fa-reply"></i>
                        Due Payment
                    </a>
                </div>

                <div class="col-4">
                    <a href="{{ route('payment.index') }}" class="btn btn-dark btn-block">
                        <i class="fa fa-reply"></i>
                        Payment List
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
    .page-footer hr {
        margin: 2px;
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

    .order-details th.details {
        width: 200px;
    }

    strong {
        font-weight: 800;
    }

    address {
        margin-bottom: 0px;
    }

    .invoice-header {
        width: 100%;
        box-sizing: border-box;
        overflow: hidden;
    }

    .invoice-header address {
        text-align: center;
        font-size: 14px;
    }



    .logo-area img {
        @if($pos_setting->invoice_logo_type=="Both") width: 12%;
        @else width: 40%;
        @endif display: inline;
        float: left;
        margin-right: 10px;
    }

    .logo-area h1 {
        display: inline;
        float: left;
        font-size: 17px;
        padding-left: 8px;
    }

    .logo-area h4 {
        font-weight: bold;
        font-size: 50px;
    }


    /* .invoice-header .invoice{} */

    .invoice-header .logo-area {
        width: 75%;
        float: left;
        padding: 5px;
        border-right: 1px dotted #000;
    }

    .invoice-header .invoice {
        color: #000;
        border-left: 1px dashed black
    }


    .invoice-header .invoice h3 {
        background-color: #000;
        font-weight: 600;
        text-align: center;
        width: 70%;
        margin: auto;
        color: #fff;
        border-radius: 10px;
    }

    .invoice p {
        margin-bottom: 0px;
    }

    .invoice .invoice-content {
        margin-bottom: 1em;
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
    .mobile-no,
    .cus_info {
        width: 100%;
        border-left: 1px solid #000;
        border-bottom: 1px solid #000;
        border-right: 1px solid #000;
        padding: 0 15px;
    }

    .name span,
    .address span,
    .mobile-no span,
    .cus_info span {
        padding-left: 15px;
        font-weight: 800;
    }

    .footer-note {
        margin-top: -66px;
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
            background: #000 !important;
            color: #ffffff !important;
            border: 1px solid #e9ecef !important;

        }

        .table-plist th.details {
            width: 200px !important;

        }



        .border-bottom {
            border-bottom: 1px dotted #000;
        }

        .print {
            margin-bottom: 0;
        }

        .table-bordered td {
            border: 1px solid #000 !important;
        }
    }

    body {
        font-family: 'Petrona', serif;
    }

    .note,
    .in_word,
    .signature,
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
{{--
<link rel="stylesheet" href="{{ asset('dashboard/css/receipt.css') }}"> --}}

<style>
    .table-rheader td {
        border-top: 0px;
        padding: 5px;
        vertical-align: baseline !important;
    }

    .table-plist td {
        padding: 0px;
        text-align: center;
        border: 1px solid #000;
    }

    .table-plist-header {
        width: 100%;
        float: left;
    }

    .table-plist-header td {
        padding: 5px;
        text-align: left;
        border: 1px solid #000;
        font-weight: 600;
        font-size: 14px;
        font-family: 'Roboto';
    }

    .table-plist th {
        padding: 0px;
        text-align: center;
        background: #000;
        color: #ffffff;
    }

    .border-bottom {
        border-bottom: 1px dotted #000;
    }

    .logo_n_name {
        text-align: center;
    }

    .logo_n_name img {
        max-width: 70%;
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
        location.reload();
    }

</script>
@endsection