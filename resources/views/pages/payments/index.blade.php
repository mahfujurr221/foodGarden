@extends('layouts.master')
@section('title', 'Customer Payments History')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>Customer Payments </strong>
    </h1>
  </div>

  <div class="header-action">
    <nav class="nav">
      <a class="nav-link active" href="{{ route('payment.index') }}">
        Customer Payments
      </a>
      <a class="nav-link" href="{{ route('payment.create') }}">
        <i class="fa fa-plus"></i>
        Add Payment
      </a>
    </nav>
  </div>

</header>
@endsection

@section('content')
<div class="col-lg-12">

  <div class="card">
    <form>
      <div class="card-body">
        <div class="row">
          <div class="form-group col-md-4">
            <label for="start_date">Start Date</label>
            <input type="text" data-provide="datepicker" data-date-today-highlight="true" data-orientation="bottom"
              data-date-format="yyyy-mm-dd" data-date-autoclose="true" class="form-control" name="start_date"
              placeholder="Start Date" autocomplete="off" value="{{ request('start_date') }}">
          </div>
          <div class="form-group col-md-4">
            <label for="end_date">End Date</label>
            <input type="text" data-provide="datepicker" data-date-today-highlight="true" data-orientation="bottom"
              data-date-format="yyyy-mm-dd" data-date-autoclose="true" class="form-control" name="end_date"
              placeholder="End Date" autocomplete="off" value="{{ request('end_date') }}">
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label form="customer">Customer</label>
              <select name="customer" class="form-control" data-provide="selectpicker" data-size="10"
                data-live-search="true">
                <option value="">Select Customer</option>
                @foreach($customers as $customer)
                <option {{ request()->customer == $customer->id ? 'selected' : '' }} value="{{ $customer->id }}">{{
                  $customer->shop_name . '-'.
                  $customer->address->name . ' (' . $customer->name .')' }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-success">
          Filter
        </button>
        <a href="{{ request()->url() }}" class="btn btn-info"> Reset </a>

        <div class="float-right">
          <a href="" class="btn btn-primary pull-right" onclick="window.print()">Print</a>
        </div>
      </div>
    </form>
  </div>

  <div class="card print_area">
    <h5 class="card-title"><strong>Payments History </strong></h5>

    <div class="card-body">
      <table class="table table-responsive table-striped table-bordered" data-provide="">
        <thead class="bg-primary">
          <tr>
            <th>SL</th>
            <th>Shop Name</th>
            <th>Address</th>
            <th>CollectBy</th>
            <th>Payment Date</th>
            {{-- <th>Method</th> --}}
            <th>Amount</th>
            <th>Payment Type</th>
            <th>Wallet Payment</th>
            <th>Note</th>
            <th class="print_hidden">#</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($payments as $key => $item)
          <tr>
            <td>{{ (isset($_GET['page']))? ($_GET['page']-1)*20+$key+1 : $key+1 }}
              <i class="ml-2 fa fa-fw fa-eye bg-success print_hidden" id="showCustomerIcon"
                style="cursor:pointer" 
                data-name="{{ $item->customer->name??'No Name Given' }}"
                data-phone="{{ $item->customer->phone?? 'No Phone Given' }}"
                data-note="{{ $item->customer->note?? 'No Note Given' }}"
                data-busigness_category="{{ $item->customer->business_category->name?? 'No Busigness Category Given'}}">
              </i>
            </td>
            <td>{{ $item->customer->shop_name }}</td>
            <td>{{ $item->customer->address->name }}</td>
            <td>{{ $item->collectBy->fname??'-' }}</td>
            <td>{{ date('d M, Y', strtotime($item->date)) }}</td>
            {{-- <td>{{ $item->payment_method!=null?$item->payment_method->name:"" }}</td> --}}
            <td>{{ number_format($item->amount )}}/-</td>
            <td>{{ $item->payment_type == 'pay' ? 'Cash Pay' : 'Cash Received' }}</td>
            <td style="text-align:center;">{{ $item->wallet_payment==1?"YES":"NO" }}</td>
            <td>{{ $item->note??'' }}</td>

            <td class="print_hidden">
              <div class="btn-group">
                <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                  <i class="fa fa-cogs"></i>
                </button>
                <div class="dropdown-menu" x-placement="bottom-start">
                  <a href="{{ route('payment_receipt',$item->id) }}" class="dropdown-item" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                  </a>

                  <a href="{{ route('pos.index') }}?customer={{ $item->customer_id }}" class="dropdown-item" target="_blank">
                    <i class="fa fa-list"></i>
                    Sales List
                  </a>

                  <a href="{{ route('payment.index') }}?customer={{ $item->customer_id }}" class="dropdown-item"
                    target="_blank">
                    <i class="fa fa-money"></i>
                    Payments List
                  </a>

                  <a class="dropdown-item" href="{{ route('customer.report', $item->customer_id) }}">
                    <i class="fa fa-file-excel-o"></i>
                    Report
                  </a>

                  <a href="{{ route('report.customer_ledger') }}?customer_id={{ $item->customer_id }}" class="dropdown-item">
                    <i class="fa fa-book"></i>
                    Ledger
                  </a>

                  <a href="{{ route('payment.destroy', $item->id) }}" class="dropdown-item delete">
                    <i class="fa fa-trash"></i>
                    Delete
                  </a>

                </div>
              </div>
            </td>

          </tr>
          @endforeach
        </tbody>

      </table>

      {!! $payments->appends(Request::except("_token"))->links() !!}

    </div>
  </div>
</div>

{{-- customer details modal --}}
<div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="showModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Customer Details</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">

          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>
      </div>
  </div>
</div>

@endsection

@section('styles')
<style>
</style>
@endsection

@section('scripts')
@include('includes.delete-alert')
<script>
  //onclick showCustomerIcon
  $(document).on('click', '#showCustomerIcon', function() {
      var name = $(this).data('name');
      var phone = $(this).data('phone');
      var note = $(this).data('note');
      var busigness_category = $(this).data('busigness_category');
      $('#showModal').modal('show');
      $('#showModal').find('.modal-body').html(`
          <div class="row">
              <div class="col-md-5 col-sm-5 col-5 text-right"><strong>Name :</strong></div>
              <div class="col-md-6 col-sm-6 col-6">${name}</div>
              <div class="col-md-5 col-sm-5 col-5 text-right"><strong>Category :</strong></div>
              <div class="col-md-6 col-sm-6 col-6">${busigness_category}</div>
              <div class="col-md-5 col-sm-5 col-5 text-right"><strong>Phone :</strong></div>
              <div class="col-md-6 col-sm-6 col-6">${phone}</div>
              <div class="col-md-5 col-sm-5 col-5 text-right"><strong>Note :</strong></div>
              <div class="col-md-6 col-sm-6 col-6">${note}</div>
          </div>
      `);
  });
</script>
@endsection