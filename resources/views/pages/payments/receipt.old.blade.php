@extends('layouts.master')
@section('title', 'Payment Receipt')


@section('content')
    <div class="col-md-12">
        <div class="row justify-content-center">
            <div class="col-md-8 card card-body">
                <div id="print-area">
                    <div class="invoice-header">
                        <div class="logo-area">
                            {{-- @if ($pos_setting->logo != null)
                            <img src="{{ asset($pos_setting->logo) }}" alt="logo">
                            @else --}}
                            <h1 class="title">{{ $pos_setting->company }}</h1>
                            {{-- @endif --}}
                        </div>
                        <address>
                            {{ $pos_setting->address }}
                            <br>
                            Phone : <strong>{{ $pos_setting->phone }}</strong>
                            <br>
                            Email : <strong>{{ $pos_setting->email }}</strong>
                        </address>

                    </div>

                    @php
                        if ($payment->customer) {
                            $user = $payment->customer;
                        } elseif ($payment->supplier) {
                            $user = $payment->supplier;
                        }

                        $first_item=$payment->payments()->first();
                    @endphp

                    <table class="table payment-invoice-header mt-2">
                        <tbody>
                            <tr>
                                <td colspan="4" style="border-top: 0">
                                    <h3 style="text-align: center; font-weight:bold;">Payment Invoice</h3>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:15%;">Payment No :</td>
                                <td style="width: 35%;">{{ $payment->id }}</td>
                                <td style="width:15%;">Date :</td>
                                <td>{{ date('d M, Y', strtotime($payment->date)) }}</td>
                            </tr>

                            <tr>
                                <td>Name :</td>
                                <td colspan="3">{{ $user->name ?? '' }}</td>
                                
                            </tr>
                            <tr>
                                <td>Shop Name :</td>
                                <td colspan="3">{{ $user->shop_name_bangla ?? '' }}</td>
                                
                            </tr>
                            <tr>
                                <td>Address :</td>
                                <td colspan="3">{{ $user->address->name ?? '' }}</td>
                            </tr>

                            <tr>
                                <td>Mobile :</td>
                                <td colspan="3">{{ $user->phone ?? '' }}</td>
                            </tr>

                            <tr>
                                <td>Account Type :</td>
                                <td>
                                    @php

                                    @endphp
                                    @if ($payment->customer_id)
                                        Customer
                                    @elseif($payment->supplier_id)
                                        Supplier
                                    @else
                                        Customer
                                    @endif
                                </td>
                                <td>Account:</td>
                                <td>{{ $first_item->account->name??'' }}</td>
                            </tr>

                            <tr>
                                <td>Transaction Type :</td>
                                <td colspan="3" style="text-transform: capitalize">{{ $payment->payment_type }}</td>
                            </tr>
                            <tr>
                                <td>Note :</td>
                                <td colspan="3">{{ $payment->note??'---' }}</td>
                            </tr>
                        </tbody>
                    </table>





                        {{-- <div class="clearfix"></div> --}}
                        {{-- items Design --}}
                        <table class="table table-bordered table-plist my-3">
                            <tr class="bg-primary">
                                <th>Date</th>
                                <th>Previous Due</th>
                                <th>Paid</th>
                                <th>Due</th>
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
                </div>
                <button class="btn btn-secondary btn-block" onclick="print_receipt('print-area')">
                    <i class="fa fa-print"></i>
                    Print
                </button>
                <a href="{{ route('payment.create') }}" class="btn btn-primary btn-block">
                    <i class="fa fa-reply"></i>
                    Back
                </a>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link href="https://fonts.googleapis.com/css?family=Petrona&display=swap" rel="stylesheet">
    <style rel="stylesheet">

        /* invoice header border */
        table.payment-invoice-header {
            border-collapse: collapse;
        }

        .payment-invoice-header td {
            padding: 10px;
            border-bottom: 1px solid #ccc; /* Add border to the bottom of each cell */
        }

        .payment-invoice-header tr td:first-child{
            border-left: 1px solid #ccc;
        }

        .payment-invoice-header tr td:last-child{
            border-right: 1px solid #ccc;
        }

        .payment-invoice-header tr:first-child td{
            border-left: 0;
        }
        
        .payment-invoice-header tr:first-child td{
            border-right: 0;
        }
        /* end of -> invoice header border */

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



        table td,table th{
            padding:1px !important;
        }


        .card * {
            color: #000000 !important;
        }

        @media print {
            body * {
                visibility: visible;
                color: #000000 !important;
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
