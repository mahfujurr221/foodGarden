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
                         <br>
                        {{--<br>
                        Website : <strong>{{ $pos_setting->website }}</strong> --}}
                    </address>

                </div>

                <div class="bill-date">
                    <div class="bill-no">
                        Invoice : {{ $pos->id }}
                    </div>
                    <div class="date">
                        Date: <strong>{{ date('d M, Y', strtotime($pos->created_at)) }}</strong>
                    </div>
                </div>
                <div class="name">
                    Client Name : <span>{{ $pos->customer ? $pos->customer->name : 'Walking Customer' }}</span>
                </div>
                <div class="address">
                    Address : <span>{{ $pos->customer ? $pos->customer->address : 'Walking Customer' }}</span>
                </div>
                <div class="mobile-no">
                    Mobile : <span>{{ $pos->customer ? $pos->customer->phone : 'Walking Customer' }}</span>
                </div>

                {{-- <div class="cus_info">
                    Courier : <span>{{ $pos->delivery_method ? $pos->delivery_method->name : '-'}}</span>
                </div> --}}

                {{-- cus_info --}}



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
                    @foreach ($pos->items as $key => $item)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $item->product_name }} - Size: {{ $item->size->size??'-' }}</td>
                        <td>@if($item->main_unit_qty){{ $item->main_unit_qty }} {{ $item->product->main_unit->name }}@endif @if($item->sub_unit_qty) {{ $item->sub_unit_qty }} {{ $item->product->sub_unit?$item->product->sub_unit->name:"" }} @endif</td>
                        <td>{{ $item->rate }} Tk</td>
                        <td>{{ $item->sub_total }} Tk</td>
                    </tr>
                    @endforeach
                    @php
                        $currentDue = $pos->due;

                        $previousDue = $pos->customer ? bcsub($pos->customer->receivable() , $pos->customer->paid()) : 0;
                        
						$wallet_due=$pos->customer?$pos->customer->wallet_balance():0;
						if($wallet_due<0){
							$previousDue+=abs($wallet_due);
						}
                        if($previousDue>=$currentDue){
							$previousDue = $previousDue - $currentDue;
                        }

                        $totalDue = $previousDue + $currentDue;
                    @endphp
                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td colspan="2">Total : </td>
                        <td colspan="2">
                            <strong>{{ number_format($pos->items->sum('sub_total'),2) }} </strong>Tk
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td colspan="2">Discount : </td>
                        <td colspan="2">
                            <strong>
                                @if(empty($pos->discount))
                                    0 Tk
                                @elseif(strpos($pos->discount, '%'))
                                    {{ $pos->discount }}
                                @elseif (strpos($pos->discount, '%') == false)
                                    {{ number_format($pos->discount,2) }} Tk
                                @endif
                            </strong>
                        </td>
                    </tr>


                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td colspan="2">Grand Total : </td>
                        <td colspan="2">
                            <strong>{{ number_format($pos->receivable,2) }} </strong>Tk
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td colspan="2">Total Paid : </td>
                        <td colspan="2">
                            <strong>{{ number_format($pos->paid,2) }} </strong>Tk
                        </td>
                    </tr>

                    {{-- <tr>
                         <td colspan="4" class="text-right">Delivery Charge : </td>
                         <td>
                             <strong>{{ round($pos->delivery_cost) }} </strong>Tk
                         </td>
                     </tr> --}}

                    @if($pos->previous_returned_product_value()>0)
                        <tr>
                            <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                            <td colspan="2">Previous Returned : </td>
                            <td colspan="2">
                                <strong>{{ number_format($pos->previous_returned_product_value(),2) }} </strong>Tk
                            </td>
                        </tr>
                    @endif

                    @if($previousDue>0)
                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td colspan="2">Previous Due : </td>
                        <td colspan="2">
                            <strong>{{ number_format($previousDue,2) }}
                            </strong>Tk
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td colspan="2">Current Due : </td>
                        <td colspan="2">
                            <strong>{{ number_format($currentDue,2)  }}
                            </strong>Tk
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td colspan="2">Total Due : </td>
                        <td colspan="2">
                            <strong>{{ number_format($totalDue,2)  }}
                            </strong>Tk
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td colspan="2">Total Due : </td>
                        <td colspan="2">
                            <strong>{{ number_format($totalDue,2)  }}
                            </strong>Tk
                        </td>
                    </tr>
                    @endif
                </table>

                @php
                $digit = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                @endphp
                <p><strong>In Word: </strong> {{ ucwords($digit->format($pos->receivable)) }} only</p>
                <p>Note: {{ $pos->note }}</p>
                {{-- <div class="signature">
                    <div class="customers text-center">
                        <span>--------------------------</span>
                        <p>Customer's Signature</p>
                    </div>
                    <div class="authorized text-center">
                        <span>--------------------------</span>
                        <p>Authorized Signature</p>
                    </div>
                </div> --}}
                <hr>
                <p class="text-center lead"><small>Software Developed by SOFTGHOR LTD. For query: 01958-104250</small>
                    <p />
            </div>
            <button class="btn btn-secondary btn-block print_hidden" onclick="print_receipt('print-area')">
                <i class="fa fa-print"></i>
                Print
            </button>
            <div class="row mt-4">
                <div class="col-6">
                    <a href="{{ route('pos.create') }}" class="btn btn-primary btn-block">
                        <i class="fa fa-reply"></i>
                        New Sale
                    </a>
                </div>

                <div class="col-6">
                    <a href="{{ route('pos.index') }}" class="btn btn-primary btn-block">
                        <i class="fa fa-reply"></i>
                        Sale List
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
