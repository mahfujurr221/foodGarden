@extends('layouts.master')
@section('title', 'Supplier Payments History')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>Supplier Payments </strong>
    </h1>
  </div>

  <div class="header-action">
    <nav class="nav">
      <a class="nav-link active" href="{{ route('payment.supplier-payment') }}">
        Supplier Payments
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
              <label form="supplier">Supplier</label>
              <select name="supplier" class="form-control" data-provide="selectpicker" data-size="10"
                data-live-search="true">
                <option value="">Select Supplier</option>
                @foreach($suppliers as $supplier)
                <option {{ request()->supplier == $supplier->id ? 'selected' : '' }} value="{{ $supplier->id }}">{{
                  $supplier->name }} - {{ $supplier->phone }}</option>
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
            {{-- <th>Transaction ID</th> --}}
            <th>Supplier Name</th>
            <th>Address</th>
            <th>Mobile</th>
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
            <td>{{ (isset($_GET['page']))? ($_GET['page']-1)*20+$key+1 : $key+1 }}</td>
            <td>
              {{ $item->supplier->name }}
            </td>
            <td>{{ $item->supplier->address}}</td>
            <td>{{ $item->supplier->phone }}</td>
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
                  <a class="dropdown-item" href="{{ route('supplier.report', $item->supplier_id) }}">
                    <i class="fa fa-file-excel-o"></i>
                    Report
                  </a>
                  <a href="{{ route('report.supplier_ledger') }}?supplier_id={{ $item->supplier_id }}" class="dropdown-item">
                    <i class="fa fa-book"></i>
                    Ledger
                  </a>

                  <a href="{{ route('purchase.index') }}?supplier={{ $item->supplier_id }}" class="dropdown-item"
                    target="_blank">
                    <i class="fa fa-list"></i>
                    Purchase List
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

@endsection

@section('styles')
<style>
</style>
@endsection

@section('scripts')
@include('includes.delete-alert')
<script>
</script>
@endsection