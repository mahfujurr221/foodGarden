@extends('layouts.master')
@section('title', 'Payments Create')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>Add Payment </strong>
    </h1>
  </div>

  <div class="header-action">
    <nav class="nav">
      <a class="nav-link" href="{{ route('payment.index') }}">
        Payments
      </a>

      <a class="nav-link active" href="{{ route('payment.create') }}">
        <i class="fa fa-plus"></i>
        Add Customer Payment
      </a>

      <a class="nav-link active" href="{{ route('payment.create') }}">
        <i class="fa fa-plus"></i>
        Add Supplier Payment
      </a>
    </nav>
  </div>

</header>
@endsection

@section('content')
<div class="col-lg-12">
  <div class="card">
    <h4 class="card-title">Create Payment</h4>

    <form action="{{ route('payment.store') }}" method="POST">
      @csrf
      <div class="card-body">
        <div class="form-row">
          {{-- <div class="form-group col-lg-6">
            <label for="transaction_id">Transaction Number</label>
            <input type="text" class="form-control {{ $errors->has('transaction_id') ? 'is-invalid': '' }}"
              name="transaction_id" value="{{ strtoupper(uniqid('TRANSACTION_')) }}" readonly>
            @if($errors->has('transaction_id'))
            <span class="invalid-feedback">{{ $errors->first('transaction_number') }}</span>
            @endif
          </div> --}}

          <div class="form-group col-lg-6">
            <label for="payment_date">Payment Date</label>
            <input type="text" class="form-control {{ $errors->has('payment_date') ? 'is-invalid': '' }}"
              data-provide="datepicker" data-date-today-highlight="true" data-date-format="yyyy-mm-dd"
              name="payment_date" value="{{ date('Y-m-d') }}">
            @if($errors->has('payment_date'))
            <span class="invalid-feedback">{{ $errors->first('payment_date') }}</span>
            @endif
          </div>

          <div class="form-group col-lg-6">
            <label for="transaction_type">Transaction Type</label>
            <select name="transaction_type" id=""
              class="form-control {{ $errors->has('transaction_type') ? 'is-invalid': '' }}">
              <option value="">Select Type</option>
              <option value="receive">Cash Receive</option>
              <option value="pay">Cash Pay</option>
            </select>
            @if($errors->has('transaction_type'))
            <span class="invalid-feedback">{{ $errors->first('transaction_type') }}</span>
            @endif
          </div>

          <div class="form-group col-lg-6">
            <label for="account_type">Account Type</label>
            <select name="account_type" id="account_type"
              class="form-control {{ $errors->has('account_type') ? 'is-invalid': '' }}">
              <option value="">Select Type</option>
              <option value="supplier">Supplier</option>
              <option value="customer">Customer</option>
            </select>
            @if($errors->has('account_type'))
            <span class="invalid-feedback">{{ $errors->first('account_type') }}</span>
            @endif
          </div>

          <div class="form-group col-lg-6">
            <label for="account_id">Account ID</label>
            <select name="account_id" id="account_id"
              {{-- data-provide="selectpicker"
              data-live-search="true" --}}
              class="form-control {{ $errors->has('account_id') ? 'is-invalid': '' }}">
              <option value="">Select Account</option>
            </select>
            @if($errors->has('account_id'))
            <span class="invalid-feedback">{{ $errors->first('account_id') }}</span>
            @endif
          </div>

          <div class="form-group col-lg-6">
            <label for="amount">Amount </label>
            <input type="number" name="amount" class="form-control {{ $errors->has('amount') ? 'is-invalid': '' }}"
              placeholder="Enter Amount" id="amount">
            @if($errors->has('amount'))
            <span class="invalid-feedback">{{ $errors->first('amount') }}</span>
            @endif
          </div>
          <div class="form-group col-lg-12" id="details">
            <strong>Name : <span id="account_name">xx</span></strong>
            <br>
            <strong>Due Invoice : <span id="due_invoice">0</span></strong>
            <br>
            <strong>Total Due: <span id="total_due">0</span> Tk</strong>
          </div>
          <div class="form-group col-lg-6">
            <label for="method">Payment Method </label>
            <select name="method" id="p_method" class="form-control">
              <option value="">Select Method</option>
              <option value="hand-cash">Hand Cash</option>
              <option value="bank">Bank</option>
              <option value="rocket">Rocket</option>
              <option value="bkash">Bkash</option>
            </select>
          </div>
          <div class="form-group col-lg-12">
            <label for="note">Note </label>
            <textarea name="note" class="form-control {{ $errors->has('note') ? 'is-invalid': '' }}"
              placeholder="Write Note. (optional)"></textarea>
            @if($errors->has('note'))
            <span class="invalid-feedback">{{ $errors->first('note') }}</span>
            @endif
          </div>

          <div class="col-12">
            <button class="btn btn-primary btn-block" type="submit">
              <i class="fa fa-money"></i>
              Payment
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('styles')
<style>
  .table tr td {
    text-align: center;
    vertical-align: baseline;
    padding: 4px;
  }

  .table tr th {
    text-align: center;
    padding: 5px;
  }

  .table tr td input {
    text-align: center;
  }

  .header {
    margin-bottom: 10px;
  }

  .main-content {
    padding-top: 10px;
  }
</style>
@endsection
 
@section('scripts')
@include('pages.payments.script')
@endsection