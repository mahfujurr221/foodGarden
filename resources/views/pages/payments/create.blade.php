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
                Customer Payments
            </a>
            <a class="nav-link" href="{{ route('payment.supplier-payment') }}">
                Supplier Payments
            </a>
            <a class="nav-link" href="{{ route('payment.customer-due-list') }}">
                Due List
            </a>
            <a class="nav-link active" href="{{ route('payment.create') }}">
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
        <h4 class="card-title">Create Payment</h4>

        {{-- {{ $errors }} --}}

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

                    <div class="form-group col-2">
                        <label for="direct_transection">Wallet/Direct Transaction</label>
                        <select name="direct_transection" id=""
                            class="form-control {{ $errors->has('direct_transection') ? 'is-invalid': '' }}">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                        @if($errors->has('direct_transection'))
                        <span class="invalid-feedback">{{ $errors->first('direct_transection') }}</span>
                        @endif
                    </div>

                    <div class="form-group col-lg-4">
                        <label for="payment_date">Payment Date<span class="field_required"></span></label>
                        <input type="text" class="form-control {{ $errors->has('payment_date') ? 'is-invalid': '' }}"
                            data-provide="datepicker" data-date-today-highlight="true" data-date-format="yyyy-mm-dd"
                            name="payment_date" value="{{ date('Y-m-d') }}">
                        @if($errors->has('payment_date'))
                        <span class="invalid-feedback">{{ $errors->first('payment_date') }}</span>
                        @endif
                    </div>

                    <div class="form-group col-lg-6">
                        <label for="payment_type">Payment Type<span class="field_required"></span></label>
                        <select name="payment_type" id="payment_type"
                            class="form-control {{ $errors->has('payment_type') ? 'is-invalid': '' }}">
                            <option value="pay">Add Due</option>
                            <option value="receive">Cash Receive</option>
                        </select>
                        @if($errors->has('payment_type'))
                        <span class="invalid-feedback">{{ $errors->first('payment_type') }}</span>
                        @endif
                    </div>

                    <div class="form-group col-lg-6 d-none">
                        <label for="account_type">Account Type<span class="field_required"></span></label>
                        <select name="account_type" id="account_type"
                            class="form-control {{ $errors->has('account_type') ? 'is-invalid': '' }}">
                            {{-- <option value="">Select Type</option> --}}
                            <option value="customer">Customer</option>
                            <option value="supplier">Supplier</option>
                        </select>
                        @if($errors->has('account_type'))
                        <span class="invalid-feedback">{{ $errors->first('account_type') }}</span>
                        @endif
                    </div>

                    <div class="form-group col-lg-6">
                        <label for="account_id">Add Customer<span class="field_required"></span></label>
                        <select name="account_id" id="account_id"
                            class="form-control select2 {{ $errors->has('account_id') ? 'is-invalid': '' }} customer">
                            {{-- <option value="">Select Customer/Supplier</option> --}}
                            <option value="">Select Customer</option>
                            @foreach (\App\Customer::get() as $item)
                            <option value="{{ $item->id }}" {{ old("account_id")==$item->id?"SELECTED":"" }}>
                                {{ $item->shop_name . " - " . $item->name . " - " . $item->address->name . "-" .
                                $item->phone }}
                            </option>
                            @endforeach
                        </select>
                        @if($errors->has('account_id'))
                        <span class="invalid-feedback">{{ $errors->first('account_id') }}</span>
                        @endif
                    </div>

                    <div class="form-group col-lg-6" id="committed_date">
                        <label for="committed_date">Committed Date<span class="field_required"></span></label>
                        <input type="date" class="form-control" data-provide="datepicker" data-date-format="yyyy-mm-dd"
                            value="{{ date('Y-m-d', strtotime('+1 day')) }}" name="committed_date">
                    </div>

                    <div class="form-group col-lg-6">
                        <label for="amount">Amount<span class="field_required"></span></label>
                        <input type="number" name="amount"
                            class="form-control {{ $errors->has('amount') ? 'is-invalid': '' }}"
                            placeholder="Enter Amount" id="amount">
                        @if($errors->has('amount'))
                        <span class="invalid-feedback">{{ $errors->first('amount') }}</span>
                        @endif
                    </div>

                    <div class="form-group col-lg-6" id="brand">
                        <label for="brand">Due Brand <span class="field_required"></span></label>
                        <select name="brand" class="form-control {{ $errors->has('brand') ? 'is-invalid': '' }}">
                            <option value="">Select Brand</option>
                            @foreach (\App\Supplier::get() as $item)
                            <option value="{{ $item->id }}" {{ old("brand")==$item->id?"SELECTED":"" }}>
                                {{ $item->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="form-group col-6" id="bank_account_id">
                        <label for="">Transaction Account</label>
                        <select name="bank_account_id" id="" class="form-control bank_account_id" required>
                            <option value="">Select Account</option>
                            @foreach (\App\BankAccount::all() as $item)
                            <option value="{{ $item->id }}" {{ old("bank_account_id")==$item->id?"SELECTED":"" }}>
                                {{ $item->name }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('bank_account_id'))
                        <div class="alert alert-danger">{{ $errors->first('bank_account_id') }}</div>
                        @endif
                    </div>


                    <div class="form-group col-lg-12" id="details">
                        <strong>Name : <span id="account_name">xx</span></strong>
                        <br>
                        <strong>Due Invoice Count: <span id="due_invoice">0</span></strong>
                        <br>
                        <strong>Total Invoice Due: <span id="total_invoice_due">0</span> Tk</strong>
                        <span id="id_hint"></span>
                        <br>
                        <strong><span class="wb_text">Wallet Balance:</span> <span id="wallet_balance">0</span>
                            Tk</strong>
                        <span id="wb_hint"></span>
                        <br>
                        <strong><span class="wb_text">Total Due:</span> <span id="total_due">0</span> Tk</strong>
                    </div>

                    <div class="form-group col-6">
                        <label for="">Due/Receicve By</label>
                        <select name="due_by" id="" class="form-control">
                            <option value="">Select Account</option>
                            @foreach (\App\User::select('id', 'fname')->get() as $item)
                            <option value="{{ $item->id }}" {{ old("due_by")==$item->id?"SELECTED":"" }}>
                                {{ $item->fname }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="form-group col-lg-6">
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
<style>
    .select2-container .select2-selection--single {
        height: 35px !important;
    }

    .select2-container--classic .select2-selection--single .select2-selection__rendered {
        line-height: 35px !important;
    }

    .select2-container--classic .select2-selection--single .select2-selection__arrow {
        height: 33px !important;
    }

    .select2-container--classic .select2-selection--single .select2-selection__rendered {
        color: #929daf !important;
    }

    .select2-container--classic .select2-selection--single {
        border: 1px solid #ebebeb !important;
        border-radius: 1px !important;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('scripts')
@include('pages.payments.script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
    $("#account_id").select2({
            theme: "classic"
        });
        //onclick payment_type
        $("#payment_type").on('change', function () {
            var payment_type = $(this).val();
            if (payment_type == 'pay') {
                $("#committed_date").removeClass('d-none');
                $("#committed_date").attr('required', 'required');
                $("#brand").removeClass('d-none');
                $("#brand").attr('required', 'required');

                $('.bank_account_id').removeAttr('required');
                $('#bank_account_id').addClass('d-none');
            }
            if (payment_type == 'receive') {
                $("#committed_date").addClass('d-none');
                $("#committed_date").removeAttr('required');
                $("#brand").addClass('d-none');
                $("#brand").removeAttr('required');

                $('.bank_account_id').attr('required', 'required');
                $('#bank_account_id').removeClass('d-none');
            }
        });
</script>

@endsection