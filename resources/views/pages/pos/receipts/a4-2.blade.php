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
                    {{--<address>
                        Address : <strong>{{ $pos_setting->address }}</strong>
                        <br>
                        Phone : <strong>{{ $pos_setting->phone }}</strong>
                        <br>
                        Email : <strong>{{ $pos_setting->email }}</strong>
                        <br>
                        Facebook Page : <strong>{{ $pos_setting->page_link }}</strong>
                        <br>
                        Website : <strong>{{ $pos_setting->website }}</strong> 
                    </address>--}}
                    <div class="barcode">
                        <h4>Invoice</h4>
                        @php 
                            $barcode = '<img src="data:image/png;base64,' . (new Milon\Barcode\DNS1D)->getBarcodePNG(sprintf("%04s", $pos->id), 'C39', 1, 30) . '" alt="BAR CODE" />';
                        @endphp
                         {!! $barcode !!}
                         <h6>#{{sprintf("%06s", $pos->id)}}</h6>
                        <p> <strong> Date: </strong>{{ date('d M, Y', strtotime($pos->sale_date)) }}</p>
                    </div>

                </div>
                <hr style="background:#000;margin:5px"/>
                <div class="bill-date">
                    {{--<div class="bill-no">
                        <strong>Sold By:</strong> {{ $pos->sales_man->fname. " " .$pos->sales_man->lname }}
                    </div>
                    <div class="date">
                        Date: <strong>{{ date('d M, Y', strtotime($pos->sale_date)) }}</strong>
                    </div>--}}
                </div>
                <div class="name">
                    Name : <span>{{ $pos->customer ? $pos->customer->name : 'Walk-in Customer' }}</span>
                </div>
                <div class="address">
                    Address : <span>{{ $pos->customer ? $pos->customer->address : 'Walk-in Customer'}}</span>
                </div>
                <div class="mobile-no">
                    Number : <span>{{ $pos->customer ? $pos->customer->phone : 'Walk-in Customer'}}</span>
                </div>

                {{-- <div class="cus_info">
                    Courier : <span>{{ $pos->delivery_method ? $pos->delivery_method->name : '-'}}</span>
                </div> --}}

                {{-- cus_info --}}



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
                    @foreach ($pos->items as $key => $item)
                    <tr>
                        {{--<td>{{ ++$key }}</td>--}}
                        <td>{{ $item->product_name }}</td>
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
                        <td class="text-right">Total : </td>
                        <td>
                            <strong>{{ number_format($pos->items->sum('sub_total'),2) }} </strong>Tk
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Discount : </td>
                        <td>
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
                        <td class="text-right">Grand Total : </td>
                        <td>
                            <strong>{{ number_format($pos->receivable,2) }} </strong>Tk
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Total Paid : </td>
                        <td>
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
                            <td class="text-right">Previous Returned : </td>
                            <td>
                                <strong>{{ number_format($pos->previous_returned_product_value(),2) }} </strong>Tk
                            </td>
                        </tr>
                    @endif

                    @if($previousDue>0)
                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Previous Due : </td>
                        <td>
                            <strong>{{ number_format($previousDue,2) }}
                            </strong>Tk
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Current Due : </td>
                        <td>
                            <strong>{{ number_format($currentDue,2)  }}
                            </strong>Tk
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Total Due : </td>
                        <td>
                            <strong>{{ number_format($totalDue,2)  }}
                            </strong>Tk
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="2" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Total Due : </td>
                        <td>
                            <strong>{{ number_format($totalDue,2)  }}
                            </strong>Tk
                        </td>
                    </tr>
                    @endif
                </table>

                {{-- @php
                    $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                @endphp --}}
                {{--<p class="in_word"><strong>In Word: </strong> {{ ucwords($digit->format($pos->receivable)) }} Taka only</p--}}
                <div class="footer">
                    <div class="invoice_footer">
                    <p class="note"><strong>Note:</strong> {{ $pos->note }}</p>
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
                {{--<div class="clearfix"></div>
                <div class="footer_content">
                    <img class="mb-5" src="{{ asset('power-lynux.jpg') }}" alt="logo">
                </div>
                <div class="clearfix"></div>
                <div class="page-footer">
                    <hr>
                    <p class="text-center lead"><small>Software Developed by SOFTGHOR. For query: 01779724380</small><p/>
                </div>--}}
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
