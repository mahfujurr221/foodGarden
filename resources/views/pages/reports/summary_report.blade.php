@extends('layouts.master')
@section('title', 'Summary Report')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Summary Report</strong>
          </h1>
     </div>

     <div class="header-action">
          <nav class="nav">
               <a class="nav-link" href="{{ route('today_report') }}">
                    <i class="fa fa-clock-o"></i>
                    Today Report
               </a>
               <a class="nav-link" href="{{ route('current_month_report') }}">
                    <i class="fa fa-calendar"></i>
                    Current Month Report
               </a>
               <a class="nav-link active" href="{{ route('summary_report') }}">
                    <i class="fa fa-file-pdf-o"></i>
                    Summary Report
               </a>
          </nav>
     </div>
</header>
@endsection

@section('content')

{{-- Top Sales Products in This Month --}}
<div class="col-12">
     <div class="card">
          <form action="#" method="GET">
               <div class="card-body">
                    <div class="form-row">
                         <div class="form-group col-md-5">
                              <input type="text" name="start_date" data-provide="datepicker"
                                   data-date-today-highlight="true" data-orientation="bottom"
                                   data-date-format="yyyy-mm-dd" data-date-autoclose="true" class="form-control"
                                   placeholder="Enter Start Date" autocomplete="off" value="{{ $start_date }}">
                         </div>
                         <div class="form-group col-md-5">
                              <input type="text" name="end_date" data-provide="datepicker"
                                   data-date-today-highlight="true" data-orientation="bottom"
                                   data-date-format="yyyy-mm-dd" data-date-autoclose="true" class="form-control"
                                   placeholder="Enter End Date" autocomplete="off" value="{{ $end_date }}">
                         </div>
                         <div class="form-group col-md-2">
                              <button class="btn btn-primary" type="submit">Filter</button>
                              <a href="{{ request()->url() }}" class="btn btn-info">Reset</a>
                         </div>
                    </div>
               </div>
          </form>
     </div>
</div>
{{-- Summary Report --}}
{{-- @dd($sells) --}}
<div class="col-md-3">
     <div class="card card-body bg-success">
          <h6 class="text-white text-uppercase">Sale Amount</h6>
          <p class="fs-18 fw-700">৳ {{ number_format($sells->sum(function($sell){return $sell->profit()['sell_value'];})) }}</p>
     </div>
</div>
<div class="col-md-3">
     <div class="card card-body bg-danger">
          <h6 class="text-white text-uppercase">Purchase Cost</h6>
          <p class="fs-18 fw-700">৳ {{ number_format($sells->sum(function($sell){return $sell->profit()['purchase_cost'];})) }}</p>
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
               {{-- {{ number_format($totalSaleAmount - ($totalProductCosts +  $expenses->sum('amount')) ) }} --}}

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
                              <th>No of Sales</th>
                              <th>Sale Amount</th>
                         </tr>
                    </thead>
                    <tbody>
                         @foreach ($top_sold_products as $key => $item)
                         <tr>
                              <td>{{ ++$key }}</td>
                              <td>
                                   <a class="hover-primary" href="#">{{ $item->name."  ".$item->code }}</a>
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
                                        Qty : {{ $top_sold_products->sum('total_qty') }}
                                   </strong>
                              </th>
                              <th>
                                   <strong>
                                        Total : {{$top_sold_products->sum('no_of_sales') }}
                                   </strong>
                              </th>
                              <th>
                                   <strong>
                                        Total : ৳
                                        {{ number_format($top_sold_products->sum('amount')) }}
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
                                   {{ $item->category ?  $item->category->name : 'No Category'}}
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
                                   {{ $item->actual_payment->supplier_id ? $item->actual_payment->supplier->name : 'No Supplier' }}
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
                                   {{ $item->actual_payment->customer_id ? $item->actual_payment->customer->name : 'No Customer' }}
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
    @media print{
        table,table th,table td{
            color:black !important;
        }
    }
</style>
@endsection

@section('scripts')

@endsection
