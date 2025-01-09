@extends('layouts.master')
@section('title', 'Create Purchase')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Purchase</strong>
            </h1>
        </div>

        <div class="header-action">
            <nav class="nav">
                <a class="nav-link" href="{{ route('purchase.index') }}">
                    Purchases
                </a>
                <a class="nav-link active" href="{{ route('purchase.create') }}">
                    <i class="fa fa-plus"></i>
                    Add Purchase
                </a>
            </nav>
        </div>

    </header>
@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="card-title" style="display: inline-block">Create Purchase</h4>
                    <a href="{{ route('purchase.add_supplier') }}" class="edit btn btn-outline btn-primary float-right mt-2"
                        data-toggle="modal" data-target="#edit" id="Add Supplier" style="margin-left: 30px;">Add
                        Supplier</a>


                </div>
            </div>
            <form action="{{ route('purchase.store') }}" method="POST" onkeydown="return event.key != 'Enter';">
                @csrf
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="supplier">Supplier</label>
                            <select name="supplier_id" id="supplier" data-provide="selectpicker" data-live-search="true"
                                class="form-control {{ $errors->has('supplier_id') ? 'is-invalid' : '' }}" data-size="10">
                                <option value="">Select Supplier </option>
                                @foreach ($suppliers as $item)
                                    <option value="{{ $item->id }}" {{ $item->default == 1 ? 'disabled' : '' }}>
                                        {{ $item->name }} - {{ $item->phone }} </option>
                                @endforeach
                            </select>
                            @if ($errors->has('supplier_id'))
                                <span class="invalid-feedback">{{ $errors->first('supplier_id') }}</span>
                            @endif
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Purchase Date</label>
                            <input type="text"
                                class="form-control {{ $errors->has('purchase_date') ? 'is-invalid' : '' }} date"
                                data-provide="datepicker" name="purchase_date" data-date-today-highlight="true"
                                data-date-format="yyyy-mm-dd" value="{{ date('Y-m-d') }}">
                            @if ($errors->has('purchase_date'))
                                <span class="invalid-feedback">{{ $errors->first('purchase_date') }}</span>
                            @endif
                        </div>

                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="Product">Product</label>
                            <input type="text" id="product_search" class="form-control" placeholder="Write product."
                                onkeup="return event.keyCode !== 13">
                        </div>
                        {{-- <div class="form-group col-md-6">
            <label for="">Carrying Cost</label>
            <input type="text" name="carrying_cost" value="{{ old("carrying_cost") }}" class="form-control"
          placeholder="Carrying Cost">
          @if ($errors->has('carrying_cost'))
          <div class="alert alert-danger">{{ $errors->first('carrying_cost') }}</div>
          @endif
        </div> --}}
                    </div>
                    <hr>

                    <div class="row">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-primary">
                                    <th style="width:80px">#SL</th>
                                    <th>Product</th>
                                    <th>Rate</th>
                                    <th style="width:320px;">Qty</th>
                                    {{-- <th style="width:220px;">Dis.Qty</th> --}}
                                    <th>Sub Total</th>
                                    <th style="width:50px">
                                        <i class="fa fa-trash"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="table_body">

                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="4"></td>
                                    <td colspan="2">
                                        <strong>Grand Total: <span id="total">0</span> Tk</strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12 mt-4">
                            <button type="button" id="payment_btn" class="btn btn-primary mx-auto">
                                <i class="fa fa-money"></i>
                                Payment
                            </button>
                        </div>
                    </div>
                </div>
                {{-- Payment Modal --}}
                <div class="modal fade" id="payment-modal" tabindex="-1">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">Payment</h4>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-bordered text-left">
                                    <tr>
                                        <td width="50%">
                                            <strong class="float-left">Paying Items: </strong>
                                            <strong class="float-right">(<span id="items">0</span>)</strong>
                                        </td>
                                        <td>
                                            <strong class="float-left">Total Payable: </strong>
                                            <strong class="float-right">(<span id="payable">0</span> Tk)</strong>
                                            <input type="hidden" name="payable" id="payable_input">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <strong class="float-left pl-3">Due</strong>
                                            <strong class="float-right pr-3">
                                                (<span id="due">0</span> Tk)
                                            </strong>
                                            <input type="hidden" id="due_input" name="due_amount">
                                        </td>
                                    </tr>
                                </table>
                                <div class="form-row">
                                    <div class="form-group col-12">
                                        <label for="note">Note</label>
                                        <textarea name="note" class="form-control" placeholder="Enter Note (Optional) "></textarea>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="">Transaction Account</label>
                                        <select name="bank_account_id" id="" class="form-control" required>
                                            @foreach (\App\BankAccount::all() as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('bank_account_id') == $item->id ? 'SELECTED' : '' }}>
                                                    {{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('bank_account_id'))
                                            <div class="alert alert-danger">{{ $errors->first('bank_account_id') }}</div>
                                        @endif
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="pay_amount">Pay Amount</label>
                                        <div class="input-group">
                                            <input type="number" step="any" class="form-control" name="pay_amount"
                                                id="pay_amount" placeholder="Pay Amount...">
                                            <span class="input-group-btn">
                                                <button class="btn btn-warning" type="button"
                                                    id="paid_btn">PAID!</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-bold btn-secondary"
                                    data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-bold btn-primary" id="order-btn">
                                    <i class="fa fa-shopping-cart"></i>
                                    Purchase
                                </button>
                            </div>
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
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    @include('pages.purchases.script')

    <script src="{{ asset('js/modal_form.js') }}"></script>

    @include('includes.placeholder_model')

@endsection
