@extends('layouts.master')
@section('title', 'Expense Category List')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Account</strong>
          </h1>
     </div>

     <div class="header-action">
          <nav class="nav">
               <a class="nav-link active" href="{{ route('bank_account.index') }}">
                    <i class="fa fa-plus"></i>
                    Account
               </a>
               {{-- <a class="nav-link" href="{{ route('account.index') }}">
               <i class="fa fa-plus"></i>
               Account
               </a> --}}
               {{-- <a class="nav-link active" href="{{ route('account.index') }}">
               Expense Category
               </a> --}}
               {{-- <a class="nav-link" href="{{ route('account.history') }}">
               <i class="fa fa-plus"></i>
               Account History
               </a> --}}
          </nav>
     </div>
</header>
@endsection

@section('content')

<div class="col-12">

     <div class="card card-body">
          <div class="row">
               <div class="col-12">
                    <a href="" class="btn btn-primary pull-right" onclick="window.print()">Print</a>
               </div>
          </div>
     </div>


     <div class="card print_area" style="width:100%;">
          <h4 class="card-title"><strong>Transaction History</strong></h4>
          <div class="card-body card-body-soft">
               <table style="margin-top:20px; width:60%;" class="table">
                    <tbody>
                         <tr>
                              <th style="width:50%;">Account Name:</th>
                              <td>{{ $account->name }}</td>
                         </tr>
                    </tbody>
               </table>

               {{-- @if($history->count() > 0) --}}
               <div class="table table-bordered">
                    <table class="table table-soft">
                         <thead>
                              <tr class="bg-primary">
                                   <th style="width:10%;">#</th>
                                   <th>Date</th>
                                   <th>Amount</th>
                                   <th>Type</th>
                                   <th>Note</th>
                                   {{-- <th>Status</th> --}}
                                   {{-- <th style="width:20%;">Actions</th> --}}
                              </tr>
                         </thead>
                         <tbody>
                             @if($history->currentPage()==1)
                              <tr>
                                   <td>0</td>
                                   <td>{{ date("d/m/Y",strtotime($account->created_at)) }}</td>
                                   <td>{{ $account->opening_balance }}</td>
                                   <td>Opening Balance</td>
                              </tr>
                              @endif
                              @php $counter=1;@endphp
                              @foreach($history as $key => $item)
                              @php
                              $model=$item['model'];
                              // dd($model);
                              @endphp
                              <tr>
                                   <td scope="row">

                                        {{ $history->currentPage()!=1?$history->currentPage()*$history->perPage()+$counter+1:$counter }}

                                   </td>
                                   <td>{{ date("d/m/Y",strtotime($item['payment_date'])) }}</td>
                                   <td><span
                                             class="{{ $item['type']=="pay"?"text-danger":"text-success" }}">{{ $item['amount'] }}</span>
                                   </td>
                                   <td>{{ $item['type']=="pay"?"Spent / Withdraw":"Received" }}</td>
                                   <td>{{ $item['note'] }}</td>
                              </tr>
                              @php ++$counter;@endphp
                              {{-- {{ $counter }} --}}
                              @endforeach
                              {{-- {{ $history->currentPage() }} --}}
                         </tbody>
                    </table>
                    {{ $history->links() }}
               </div>
               {{-- @else
      <div class="alert alert-danger text-center" role="alert">
        <strong>Nothing Found! </strong>
      </div>
      @endif --}}
          </div>
     </div>
</div>
{{-- Delete Confirm Modal --}}
{{-- <div class="modal fade show" id="confirm-modal" tabindex="-1" aria-modal="true">
     <div class="modal-dialog modal-sm">
          <div class="modal-content">
               <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">You want to delete ?</h4>
                    <button type="button" class="close" data-dismiss="modal">
                         <span aria-hidden="true">×</span>
                    </button>
               </div>
               <form id="delete-form" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-dismiss="modal">No. Back !</button>
                         <button type="submit" class="btn btn-primary">Yes, Delete</button>
                    </div>
               </form>
          </div>
     </div>
</div>
@include('partials.edit-modal') --}}

@endsection

@section('styles')
<style>
     .alert {
          display: block;
     }

     @media print {

          /* body *{
      display:none;
      } */
          body header {
               display: none !important;
          }

          .print_area {
               position: absolute;
               top: 0;
          }

          .print_area * {
               visibility: visible !important;
          }

          .print_hidden {
               display: none;
          }
     }
</style>
@endsection

@section('scripts')
<script>
     function handle(id) {
       var url = "{{ route('bank_account.destroy', 'my_id') }}".replace('my_id', id);
        $("#delete-form").attr('action', url);
       $("#confirm-modal").modal('show');
     }
</script>

<script src="/js/crud.js"></script>
@endsection
