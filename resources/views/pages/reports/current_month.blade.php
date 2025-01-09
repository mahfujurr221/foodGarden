@extends('layouts.master')
@section('title', 'Current Month Report')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Current Month Report</strong>
          </h1>
     </div>

     <div class="header-action">
          <nav class="nav">
               <a class="nav-link" href="{{ route('today_report') }}">
                    <i class="fa fa-clock-o"></i>
                    Today Report
               </a>
               <a class="nav-link active" href="{{ route('current_month_report') }}">
                    <i class="fa fa-calendar"></i>
                    Current Month Report
               </a>
               <a class="nav-link" href="{{ route('summary_report') }}">
                    <i class="fa fa-file-pdf-o"></i>
                    Summary Report
               </a>
          </nav>
     </div>
</header>
@endsection

@section('content')

<div class="col-md-3">
     <div class="card card-body bg-success">
          <h6 class="text-white text-uppercase">Sale Amount</h6>
          <p class="fs-18 fw-700">৳ {{ number_format($sells->sum(function($sell){return
               $sell->profit()['sell_value'];})) }}</p>
     </div>
</div>
<div class="col-md-3">
     <div class="card card-body bg-danger">
          <h6 class="text-white text-uppercase">Purchase Cost</h6>
          <p class="fs-18 fw-700">৳ {{ number_format($sells->sum(function($sell){return
               $sell->profit()['purchase_cost'];})) }}</p>
     </div>
</div>
<div class="col-md-3">
     <div class="card card-body bg-dark">
          <h6 class="text-white text-uppercase">Expense</h6>
          <p class="fs-18 fw-700">৳ {{ number_format($expenses->sum('amount')) }}</p>
     </div>
</div>
<div class="col-md-3">
     <div class="card card-body bg-primary">
          <h6 class="text-white text-uppercase">Sell Profit</h6>
          <p class="fs-18 fw-700">৳
               {{-- {{ number_format($totalSaleAmount - ($totalProductCosts + $expenses->sum('amount')) ) }} --}}
               {{ number_format($sells->sum(function($sell){return $sell->profit()['profit'];})) }}
          </p>
     </div>
</div>

<div class="col-lg-6 col-md-6 col-sm-12">
     <div class="card">
          <div class="card-header bg-primary">
               <h5 class="card-title text-white"><strong>Top Sale Product</strong></h5>
          </div>
          <div class="card-body">
               <table class="table table-bordered table-hover table-striped table-responsive" data-provide="datatables">
                    <thead class="bg-light">
                         <tr>
                              <th>#</th>
                              <th>Product Name</th>
                              <th>Quantity</th>
                              <th>Total Sale</th>
                              <th>Sale Amount</th>
                         </tr>
                    </thead>
                    <tbody>
                         @foreach ($pos_items as $key => $item)
                         <tr>
                              <td>#{{ ++$key }}</td>
                              <td>
                                   <a class="hover-primary" href="#">{{ $item->name." ".$item->code }}</a>
                              </td>
                              <td>
                                   {{ $item->total_qty }}
                              </td>
                              <td>{{ $item->no_of_sales }}</td>
                              <td>৳ {{ number_format($item->amount) }}</td>
                         </tr>
                         @endforeach
                    <tfoot class="bg-light">
                         <tr>
                              <th colspan="2"></th>
                              <th>
                                   <strong>
                                        Qyt : {{ $pos_items->sum('total_qyt') }}
                                   </strong>
                              </th>
                              <th>
                                   <strong>
                                        Total : {{$pos_items->sum('total') }}
                                   </strong>
                              </th>
                              <th>
                                   <strong>
                                        Total : ৳
                                        {{ number_format($pos_items->sum('amount')) }}
                                   </strong>
                              </th>
                         </tr>
                    </tfoot>
                    </tbody>
               </table>
          </div>
     </div>
</div>
{{-- Expense this month --}}
<div class="col-lg-6 col-md-6 col-sm-12">
     <div class="card">
          <div class="card-header bg-danger">
               <h5 class="card-title text-white"><strong>Expense</strong></h5>
          </div>

          <div class="card-body">
               <table class="table table-bordered table-hover table-striped table-responsive" data-provide="datatables">
                    <thead class="bg-light">
                         <tr>
                              <th>#</th>
                              <th>Expense</th>
                              <th>Category</th>
                              <th>Amount</th>
                         </tr>
                    </thead>
                    <tbody>
                         @foreach ($expenses as $key => $item)
                         <tr>
                              <td>#{{ ++$key }}</td>
                              <td>
                                   {{ $item->name }}
                              </td>
                              <td>
                                   {{ $item->category ? $item->category->name : 'No Category'}}
                              </td>
                              <td>৳ {{ $item->amount }}</td>
                         </tr>
                         @endforeach
                    <tfoot class="bg-light">
                         <tr>
                              <th colspan="3"></th>

                              <th>
                                   <strong>
                                        ৳
                                        {{ number_format($expenses->sum('amount')) }}
                                   </strong>
                              </th>
                         </tr>
                    </tfoot>
                    </tbody>
               </table>
          </div>
     </div>
</div>
{{-- Monthly Pay to SUpplier --}}
<div class="col-lg-6 col-md-6 col-sm-12">
     <div class="card">
          <div class="card-header bg-secondary">
               <h5 class="card-title text-black"><strong>Pay to Supplier</strong></h5>
          </div>

          <div class="card-body">
               <table class="table table-bordered table-hover table-striped table-responsive" data-provide="datatables">
                    <thead class="bg-light">
                         <tr>
                              <th>#</th>
                              <th>Supplier</th>
                              <th>Paymnet Date</th>
                              <th>Amount</th>
                         </tr>
                    </thead>
                    <tbody>
                         @foreach ($payments_paid as $key => $item)
                         <tr>
                              <td>#{{ ++$key }}</td>
                              <td>
                                   {{ $item->paymentable->supplier ? $item->paymentable->supplier->name : 'No Supplier'
                                   }}
                              </td>
                              <td>
                                   {{ date('d M, Y', strtotime($item->payment_date)) }}
                              </td>
                              <td>
                                   {{ $item->pay_amount }}
                              </td>
                         </tr>
                         @endforeach
                    <tfoot class="bg-light">
                         <tr>
                              <th colspan="3"></th>

                              <th>
                                   <strong>
                                        ৳
                                        {{ number_format($payments_paid->sum('pay_amount')) }}
                                   </strong>
                              </th>
                         </tr>
                    </tfoot>
                    </tbody>
               </table>
          </div>
     </div>
</div>
{{-- Monthly Receive from Customer --}}
<div class="col-lg-6 col-md-6 col-sm-12">
     <div class="card">
          <div class="card-header bg-secondary">
               <h5 class="card-title text-black"><strong>Receive from Customer</strong></h5>
          </div>

          <div class="card-body">
               <table class="table table-bordered table-hover table-striped table-responsive" data-provide="datatables">
                    <thead class="bg-light">
                         <tr>
                              <th>#</th>
                              <th>Customer</th>
                              <th>Paymnet Date</th>
                              <th>Amount</th>
                         </tr>
                    </thead>
                    <tbody>
                         @foreach ($payments_received as $key => $item)
                         <tr>
                              <td>#{{ ++$key }}</td>
                              <td>
                                   {{ $item->paymentable->customer ? $item->paymentable->customer->name : 'No Customer'
                                   }}
                              </td>
                              <td>
                                   {{ date('d M, Y', strtotime($item->payment_date)) }}
                              </td>
                              <td>
                                   {{ $item->pay_amount }}
                              </td>
                         </tr>
                         @endforeach
                    <tfoot class="bg-light">
                         <tr>
                              <th colspan="3"></th>

                              <th>
                                   <strong>
                                        ৳
                                        {{ number_format($payments_received->sum('pay_amount')) }}
                                   </strong>
                              </th>
                         </tr>
                    </tfoot>
                    </tbody>
               </table>
          </div>
     </div>
</div>
@endsection

@section('styles')
<style>
     @media print {

          table,
          table th,
          table td {
               color: black !important;
          }
     }
</style>
@endsection

@section('scripts')

@endsection