@extends('layouts.master')
@section('title', 'Customer Due Report')

@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Customer Due Report</strong>
        </h1>
    </div>
</header>
@endsection

@section('content')

<div class="col-12">
    {{-- <div class="card card-body">
        <div class="row">
            <div class="col-12">
                <form action="">
                    <div class="form-row">

                        <div class="form-group col-md-3">
                            <select name="customer_id" id="" class="form-control" data-provide="selectpicker"
                                data-live-search="true" data-size="10">
                                <option value="">Select Customer</option>
                                @foreach ($filter_customers as $item)
                                <option value="{{ $item->id }}" {{ request('customer_id')==$item->id?'SELECTED':'' }}>{{
                                    $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-9">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-sliders"></i>
                                Filter
                            </button>
                            <a href="{{ request()->url() }}" class="btn btn-info">Reset</a>
                            <a href="" class="btn btn-primary pull-right" onclick="window.print()">Print</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

    <div class="card">
        <div class="print_area" style="width:100%;">
            {{-- <a href="" class="btn btn-primary float-right print_hidden" onclick="window.print()"
                style="margin-top:10px;">Print</a> --}}
            <h3 class="card-title" style=" text-align: left;"><strong>Customer Due Report</strong></h3>
            <div class="card-body">

                <table class="table table-striped table-responsive  table-bordered" data-provide="datatables"
                    data-page-length="100">
                    <thead>
                        <tr class="bg-primary">
                            <th style="min-width:180px">Shop Name</th>
                            <th style="min-width:120px">Name</th>
                            <th>Address</th>
                            <th>Phone</th>
                            {{-- <th>Due By</th>
                            <th>last Paid Date</th>
                            <th>Comitted Date</th> --}}
                            <th>Total.Due</th>
                            @can('pos-profit')
                            <th>Payment</th>
                            <th>Action</th>
                            @endcan
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($customers as $key => $customer)
                        <tr>
                            {{-- <th scope="row">{{ (isset($_GET['page']))? ($_GET['page']-1)*20+$key+1 : $key+1 }}</th>
                            --}}
                            <td>{{ $customer->shop_name_bangla }} ({{$customer->shop_name}})
                            <td>{{ $customer->name }}</td>
                            <td>{!! $customer->address->name !!}</td>
                            <td>{{ $customer->phone }}</td>
                            {{-- <td>{{ $customer->due_collections->last()->due_by??'' }}</td>
                            <td>{{ $customer->due_collections->last()->committed_date??'-' }}</td> --}}
                            {{-- @php
                            $lastDueCollection = $customer->due_collections->last();
                            @endphp
                            <td>{{ $customer->dueCollection()['due_by']??'-' }}</td>
                            <td>{{ $customer->dueCollection()['last_paid_date']??'-' }}</td>
                            <td>{{ $customer->dueCollection()['committed_due_date']??'-' }}</td> --}}

                            {{--<td class="font-weight-bold">
                                {{ number_format($customer->receivable()) }} /-
                            </td>
                            <td class="font-weight-bold">
                                {{ number_format($customer->paid()) }} /-
                            </td>--}}

                            @php
                            $customer_due=$customer->due();
                            $wallet_balance = $customer->wallet_balance();
                            $wallet_balance=$wallet_balance < 0 ? abs($wallet_balance) : 0; @endphp <td
                                class="font-weight-bold">{{number_format($wallet_balance+$customer_due) }} /-</td>
                                @can('pos-profit')
                                <td>
                                    <button class="btn btn-primary btn-sm customer_due_payment" data-toggle="modal"
                                        data-target="#customer_due_payment" data-id="{{ $customer->id }}"
                                        data-total_due="{{ $wallet_balance+$customer_due }}">
                                        <i class="fa fa-money"></i> Payment
                                </td>
                                <td class="print_hidden">
                                    <div class="btn-group">
                                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fa fa-cogs"></i>
                                        </button>
                                        <div class="dropdown-menu" x-placement="bottom-start">

                                            <a href="{{ route('pos.index') }}?customer={{ $customer->id }}"
                                                class="dropdown-item" target="_blank">
                                                <i class="fa fa-list"></i>
                                                Sales List
                                            </a>

                                            <a href="{{ route('payment.index') }}?customer={{ $customer->id }}"
                                                class="dropdown-item" target="_blank">
                                                <i class="fa fa-money"></i>
                                                Payments List
                                            </a>

                                            <a class="dropdown-item"
                                                href="{{ route('customer.report', $customer->id) }}">
                                                <i class="fa fa-file-excel-o"></i>
                                                Report
                                            </a>

                                            <a href="{{ route('report.customer_ledger') }}?customer_id={{ $customer->id }}"
                                                class="dropdown-item">
                                                <i class="fa fa-book"></i>
                                                Ledger
                                            </a>

                                        </div>
                                    </div>
                                </td>
                                @endcan
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="alert alert-danger" role="alert">
                                    <strong>You have no Customers with Due</strong>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

        </div>
    </div>

</div>

<div class="modal fade" id="customer_due_payment" tabindex="-1" role="dialog" aria-labelledby="customer_due_payment"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('payment.store') }}" method="POST">
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
                            <input type="text"
                                class="form-control {{ $errors->has('payment_date') ? 'is-invalid': '' }}"
                                data-provide="datepicker" data-date-format="yyyy-mm-dd" name="payment_date"
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

                        <div class="form-group col-lg-6 d-none">
                            <label for="committed_date">Committed Date<span class="field_required"></span></label>
                            <input type="date" class="form-control" data-provide="datepicker"
                                data-date-format="yyyy-mm-dd" name="committed_date">
                        </div>
                        <div class="form-group col-12">
                            <label for="">Due/Receicve By</label>
                            <select name="due_by" id="" class="form-control" required>
                                <option value="">Select Account</option>
                                @foreach (\App\User::select('id', 'fname')->get() as $item)
                                <option value="{{ $item->id }}" {{ old("due_by")==$item->id?"SELECTED":"" }}>
                                    {{ $item->fname }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="form-group col-lg-12">
                        <label for="amount">Amount<span class="field_required"></span></label>
                        <input type="number" name="amount" step="any"
                            class="form-control {{ $errors->has('amount') ? 'is-invalid': '' }}"
                            placeholder="Enter Amount" id="amount">
                        @if($errors->has('amount'))
                        <span class="invalid-feedback">{{ $errors->first('amount') }}</span>
                        @endif
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="note">Note </label>
                        <textarea name="note" class="form-control {{ $errors->has('note') ? 'is-invalid': '' }}"
                            placeholder="Write Note. (optional)"></textarea>
                        @if($errors->has('note'))
                        <span class="invalid-feedback">{{ $errors->first('note') }}</span>
                        @endif
                    </div>

                    <input type="hidden" name="direct_transection" value="0">
                    <input type="hidden" name="payment_type" value="receive">
                    <input type="hidden" name="account_type" value="customer">
                    <input type="hidden" name="account_id" id="customer_id">
                    <input type="hidden" name="from_customer" value="1">

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
@include('includes.delete-alert')
<script>
    //customer_due_payment 
$(document).on('click', '.customer_due_payment', function() {
    var id = $(this).data('id');
    var total_due = $(this).data('total_due');
    $('#customer_id').val(id);
    $('#amount').attr('max', total_due);
    $('#amount').attr('placeholder', 'Max: ' + total_due);
    $('#amount').val(total_due);

});
</script>
@endsection