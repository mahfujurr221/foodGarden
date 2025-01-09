@extends('layouts.master')
@section('title', 'Customer Due Payment')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>Customer Due Payment</strong>
    </h1>
  </div>

</header>
@endsection

@section('content')
<div class="col-lg-12">

  {{-- <div class="card">
    <form>
      <div class="card-body">
        <div class="row">
          <div class="form-group col-md-2">
            <label for="start_date">Commited Start Date</label>
            <input type="text" data-provide="datepicker" data-date-today-highlight="true" data-orientation="bottom"
              data-date-format="yyyy-mm-dd" data-date-autoclose="true" class="form-control" name="start_date"
              placeholder="Start Date" autocomplete="off" value="{{ request('start_date') }}">
          </div>
          <div class="form-group col-md-2">
            <label for="end_date">Commited End Date</label>
            <input type="text" data-provide="datepicker" data-date-today-highlight="true" data-orientation="bottom"
              data-date-format="yyyy-mm-dd" data-date-autoclose="true" class="form-control" name="end_date"
              placeholder="End Date" autocomplete="off" value="{{ request('end_date') }}">
          </div>

          <div class="col-md-2">
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
          <div class="col-md-2">
            <div class="form-group">
              <label form="brand">Brand</label>
              <select name="brand" class="form-control" data-provide="selectpicker" data-size="10"
                data-live-search="true">
                <option value="">Select Brand</option>
                @foreach($brands as $brand)
                <option {{ request()->brand == $brand->id ? 'selected' : '' }} value="{{ $brand->id }}">
                {{ $brand->name}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="col-md-2">
            <div class="form-group">
              <label form="due_by">Due By</label>
              <select name="due_by" class="form-control" data-provide="selectpicker" data-size="10"
                data-live-search="true">
                <option value="">Select Due By</option>
                @foreach(\APP\User::get() as $user)
                <option {{ request()->due_by == $user->id ? 'selected' : '' }} value="{{ $user->id }}">
                {{ $user->fname}}</option>
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
  </div> --}}

  <div class="card print_area">
    <div class="card-body">
      <table class="table table-responsive table-striped table-bordered" data-provide="datatables" data-page-length="100">
        <thead class="bg-primary">
          <tr>
            <th>SL</th>
            <th>Shop Name</th>
            <th>Address</th>
            <th>Due By</th>
            <th>Due Date</th>
            <th>Commited Date</th>
            <th>Brand</th>
            <th>Due Type</th>
            <th>Amount</th>
            <th>paid</th>
            <th>Due</th>
            <th>Payment</th>
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
            <td>{{ $item->dueBy->fname }}</td>
            <td>{{ date('d M, Y', strtotime($item->last_due_date)) }}</td>
            <td>{{ date('d M, Y', strtotime($item->committed_due_date)) }}</td>
            <td>{{ $item->brand->name??'-' }}</td>
            <td>
              @if($item->direct_transection==1)
              <span class="badge badge-warning">Wallet Due</span>
              @else
              <span class="badge badge-info">Sale Due</span>
              @endif
            </td>
            <td>{{ number_format($item->amount) }}/-</td>
            <td class="text-success">{{ number_format($item->paid) }}/-</td>
            <td class="text-danger">{{ number_format($item->due) }}/-</td>

            <td>
              <button class="btn btn-primary btn-sm customer_due_payment" data-toggle="modal"
                  data-target="#customer_due_payment" data-id="{{ $item->id }}"
                  data-customer_id="{{$item->customer_id??'0'}}"
                  data-due="{{$item->due??'0'}}"
                  data-due_type="{{$item->direct_transection??'0'}}"
                  data-pos_id="{{$item->pos_id}}">
                  <i class="fa fa-money"></i> Payment
            </td>

            <td class="print_hidden">
              <div class="btn-group">
                <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                  <i class="fa fa-cogs"></i>
                </button>
                <div class="dropdown-menu" x-placement="bottom-start">
                  @if($item->payment_id)
                  <a href="{{ route('payment_receipt',$item->payment_id) }}" class="dropdown-item" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                  </a>
                  @endif
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
        {{-- <tfoot>
          <tr>
            <td colspan="9" class="text-right">Total</td>
            <td>{{ number_format($payments->sum('amount')) }}/-</td>
          </tr>
        </tfoot> --}}
      </table>

      {{-- {!! $payments->appends(Request::except("_token"))->links() !!} --}}
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

{{--customer due payment modal --}}
<div class="modal fade" id="customer_due_payment" tabindex="-1" role="dialog" aria-labelledby="customer_due_payment"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('payment.due_payment') }}" method="POST">
            {{-- <form action="#" method="POST"> --}}
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="customer_due_payment">Customer Due Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">

                        <div class="form-group col-lg-6">
                            <label for="payment_date">Payment Date<span class="field_required"></span></label>
                            <input type="date"
                                class="form-control {{ $errors->has('payment_date') ? 'is-invalid': '' }}" data-date-format="yyyy-mm-dd" name="payment_date"
                                value="{{ date('Y-m-d') }}">
                            @if($errors->has('payment_date'))
                            <span class="invalid-feedback">{{ $errors->first('payment_date') }}</span>
                            @endif
                        </div>

                        <div class="form-group col-6">
                            <label for="">Transaction Account</label>
                            <select name="bank_account_id" id="" class="form-control" required>
                                @foreach (\App\BankAccount::all() as $item)
                                <option value="{{ $item->id }}" {{ old("bank_account_id")==$item->id?"SELECTED":"" }}>
                                    {{ $item->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('bank_account_id'))
                            <div class="alert alert-danger">{{ $errors->first('bank_account_id') }}</div>
                            @endif
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="committed_date">Committed Date<span class="field_required"></span></label>
                            <input type="date" class="form-control"
                                data-date-format="yyyy-mm-dd" name="committed_date" value="{{ date('Y-m-d', strtotime(' +1 day')) }}">
                        </div>
                        <div class="form-group col-6">
                            <label for="">Receicve By</label>
                            <select name="due_by" id="" class="form-control" required>
                                <option value="">Select Account</option>
                                @foreach (\App\User::select('id', 'fname')->get() as $item)
                                <option value="{{ $item->id }}" {{ old("due_by")==$item->id?"SELECTED":"" }}>
                                    {{ $item->fname }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-6">
                          <label for="amount">Amount<span class="field_required"></span></label>
                          <input type="number" name="amount" step="any"
                              class="form-control {{ $errors->has('amount') ? 'is-invalid': '' }}"
                              placeholder="Enter Amount" id="amount">
                          @if($errors->has('amount'))
                          <span class="invalid-feedback">{{ $errors->first('amount') }}</span>
                          @endif
                      </div>
  
                      <div class="form-group col-lg-6">
                          <label for="note">Note </label>
                          <textarea name="note" class="form-control {{ $errors->has('note') ? 'is-invalid': '' }}"
                              placeholder="Write Note. (optional)"></textarea>
                          @if($errors->has('note'))
                          <span class="invalid-feedback">{{ $errors->first('note') }}</span>
                          @endif
                      </div>

                    </div>


                    <input type="hidden" id="due_type" name="direct_transection">
                    <input type="hidden" name="payment_type" value="receive">
                    <input type="hidden" name="account_type" value="customer">
                    <input type="hidden" name="account_id" id="customer_id">
                    <input type="hidden" name="from_customer" value="1">
                    <input type="hidden" name="due_collection_id" id="due_collection_id">

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Payment</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
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

  $(document).on('click', '.customer_due_payment', function() {
    var id = $(this).data('id');
    var customer_id=$(this).data('customer_id');
    var due= $(this).data('due');
    var due_type=$(this).data('due_type');
    var pos_id=$(this).data('pos_id');

    $('#customer_id').val(customer_id);
    $('#amount').val(due);
    $('#due_type').val(due_type);
    $('#due_collection_id').val(id);
});

</script>
@endsection