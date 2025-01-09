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
                            @elseif($pos_setting->invoice_logo_type == 'Name' && $pos_setting->company != null)
                                <h4>{{ $pos_setting->company }}</h4>
                            @else
                                <img src="{{ asset($pos_setting->logo) }}" alt="logo">
                                <div class="clearfix"></div>
                            @endif
                        </div>

                        <address>
                            <h4>{{ $pos_setting->company }}</h4>
                            Address : <strong>{{ $pos_setting->address }}</strong>
                            <br>
                            Phone : <strong>{{ $pos_setting->phone }}</strong>
                            <br>
                            Email : <strong>{{ $pos_setting->email }}</strong>
                            <br />
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
                    <div class="name">
                        Client Name :
                        <span>{{ $estimate->customer ? $estimate->customer->name : 'Walk-in Customer' }}</span>
                    </div>
                    <div class="address">
                        Address :
                        <span>{{ $estimate->customer ? $estimate->customer->address->name : 'Walk-in Customer' }}</span>
                    </div>
                    <div class="mobile-no">
                        Mobile : <span>{{ $estimate->customer ? $estimate->customer->phone : 'Walk-in Customer' }}</span>
                    </div>
                    {{-- items Design --}}
                    <table class="table table-bordered table-plist my-3 order-details">
                        <tr class="bg-primary">
                            <th>#</th>
                            <th>Details</th>
                            <th>Qty</th>
                            <th>Discount Qty</th>
                            <th>Price</th>
                            <th>Net.A</th>
                        </tr>
                        @foreach ($estimate->items as $key => $item)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td>
                                    {{ $item->product->readable_qty($item->qty) }}
                                </td>
                                <td>{{ $item->discount_qty }} Pc</td>
                                <td>{{ $item->rate }} Tk</td>
                                <td>{{ $item->sub_total }} Tk</td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="4" class="rm-b-l rm-b-t rm-b-b"></td>
                            <td class="text-right">Total : </td>
                            <td>
                                <strong>{{ number_format($estimate->items->sum('sub_total'), 2) }} </strong>Tk
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4" class="rm-b-l rm-b-t rm-b-b"></td>
                            <td class="text-right">Grand Total : </td>
                            <td>
                                <strong>{{ number_format($estimate->receivable, 2) }} </strong>Tk
                            </td>
                        </tr>
                    </table>

                    {{-- @php
                    $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                @endphp --}}
                    <p class="note">Note: {{ $estimate->note }}</p>
                    <div class="signature">
                        <div class="customers text-center">
                            <span>--------------------------</span>
                            <p>Customer's Signature</p>
                        </div>
                        <div class="authorized text-center">
                            <span>--------------------------</span>
                            <p>FO Signature</p>
                        </div>
                    </div>
                    <div class="page-footer">
                        <hr>
                        <p class="text-center lead"><small>Software Developed by SOFTGHOR. For query: 01779724380</small>
                        </p>
                    </div>
                </div>
                <button class="btn btn-secondary btn-block print_hidden" onclick="print_receipt('print-area')">
                    <i class="fa fa-print"></i>
                    Print
                </button>

                <div class="row mt-4">
                    <div class="col-6">
                        <a href="{{ route('estimate.create') }}" class="btn btn-primary btn-block">
                            <i class="fa fa-reply"></i>
                            New Estimate
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('estimate.index') }}" class="btn btn-primary btn-block">
                            <i class="fa fa-reply"></i>
                            Estimate List
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

        strong {
            font-weight: 800;
        }

        address {
            margin-bottom: 0px;
        }

        address h4 {
            font-weight: bold;
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
            @if ($pos_setting->invoice_logo_type == 'Both')
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
            border: 1px solid #000;
            overflow: hidden;
            padding: 0 15px;
        }

        .date {
            width: 50%;
            float: left;
            font-size: 12px;
        }

        .bill-no {
            width: 50%;
            float: left;
            font-size: 12px;
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
            font-size: 12px;
        }

        .name span,
        .address span,
        .mobile-no span,
        .cus_info span {
            padding-left: 15px;
            font-weight: 400;
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

            .print {
                margin-bottom: 0;
            }

            .table-bordered td,
            .table-bordered th {
                border: 1px solid #000 !important;
            }
        }

        body {
            font-family: 'Petrona', serif;
        }

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

        .note {
            color: black;
            font-size: 14px;
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
            font-weight: 400;
            font-size: 12px;
        }

        .table-plist th {
            padding: 0px;
            text-align: center;
            background: #ddd;
            font-size: 12px;
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
@endsection
