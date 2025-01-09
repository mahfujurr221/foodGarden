@extends('layouts.master')
@section('title', 'SR Report')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>
                    {{ $sr->name }}
                    <span class="small">SR </span>
               </strong>

          </h1>
     </div>

</header>
@endsection

@section('content')
<div class="col-md-3 col-lg-3">
     <div class="card card-body bg-primary">
          <h6>
               <span class="text-uppercase text-white">Total Buy</span>
          </h6>
          <br>
          <p class="fs-18 fw-600">৳ {{ number_format($sr->receivable(), 2) }}</p>
     </div>
</div>
<div class="col-md-3 col-lg-3">
     <div class="card card-body bg-success">
          <h6>
               <span class="text-uppercase text-white">Total Pay</span>
          </h6>
          <br>
          <p class="fs-18 fw-600">৳ {{ number_format($sr->paid(), 2) }}</p>
     </div>
</div>
<div class="col-md-3 col-lg-3">
     <div class="card card-body bg-danger">
          <h6>
               <span class="text-uppercase text-white">Total Due</span>
          </h6>
          <br>
          <p class="fs-18 fw-600">৳
               {{ number_format($sr->due(), 2) }}</p>
     </div>
</div>
<div class="col-md-3 col-lg-3">
     <div class="card card-body bg-info">
          <h6>
               <span class="text-uppercase text-white">Information</span>
          </h6>
          <p class="mb-0">Address: {{ $sr->address }}</p>
          <p>Phone: {{ $sr->mobile }}</p>

     </div>
</div>


<div class="col-md-12">
     <div class="card">
          <h4 class="card-title">
               <strong>{{ $sr->name }} - History</strong>
               <button type="button" class="btn btn-secondary float-right" onclick="print()">
                    <i class="fa fa-print"></i>
                    Print
               </button>
          </h4>

          <div class="card-body">
               <div class="">
                    @if($customers->count() > 0)
                    <table class="table table-responsive-sm table-soft table-bordered">
                         <thead>
                              <tr class="bg-primary">
                                   <th>#</th>
                                   <th>Customer Name</th>
                                   <th>Total Bill</th>
                                   <th>Total Pay </th>
                                   <th>Total Due </th>
                              </tr>
                         </thead>
                         <tbody>
                              @foreach ($customers as $customer)
                              <tr>
                                   <td>{{ $loop->iteration + $customers->perPage() * ($customers->currentPage() - 1) }}
                                   </td>
                                   <td>{{ $customer->name }}</td>
                                   <td>{{ number_format($customer->receivable() , 2) }} Tk</td>
                                   <td>{{ number_format($customer->paid(), 2) }} Tk</td>
                                   <td>{{ number_format($customer->due(), 2) }} Tk</td>
                              </tr>
                              @endforeach
                         </tbody>
                    </table>
                    @else
                    <div class="alert alert-warning text-center">
                         <strong>{{ $sr->name }} - No customer History. Sorry !</strong>
                    </div>
                    @endif
               </div>
               {!! $customers->links() !!}
          </div>
     </div>
</div>

@endsection

@section('styles')
<style>
     @media print {
          body * {
               visibility: visible;
               color: #000000 !important;
          }

          .col-md-3 {
               display: inline-block !important;
               padding: 5px;
               margin: 0px;
               max-width: 25%;
          }

          .card-body {
               background: #fff !important;
               color: #111;
          }

          .card-body h6 span {
               color: #111 !important;
          }

          .btn {
               display: none;
          }
     }
</style>
@endsection

@section('scripts')

@endsection