@extends('layouts.master')
@section('title', 'Purchases List')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Purchases</strong>
            </h1>
        </div>

        <div class="header-action">
            <nav class="nav">
                <a class="nav-link active" href="{{ route('purchase.index') }}">
                    Purchases
                </a>
                <a class="nav-link" href="{{ route('purchase.create') }}">
                    <i class="fa fa-plus"></i>
                    Add Purchase
                </a>
            </nav>
        </div>
    </header>
@endsection

@section('content')
    <div class="card" style="width:100%;">
        <div class="row">
            <div class="col-12" style="display:flex; justify-content:space-between">
                <div class="col-2">
                    <h4 class="card-title"><strong>Purchases</strong></h4>
                </div>
                <div class="col-md-11">
                    <form action="#">
                        <div class="form-row mt-3">
                            {{-- <div class="form-group col-md-1">
                                <input type="text" class="form-control" name="bill_no" placeholder="Bill Number"
                                    autocomplete="off" value="{{ request('bill_no') }}">
                            </div> --}}
                            <div class="form-group col-md-2">
                                <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                                    data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                                    class="form-control" name="start_date" placeholder="Start Date" autocomplete="off"
                                    value="{{ request('start_date') }}">
                            </div>
                            <div class="form-group col-md-2">
                                <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                                    data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                                    class="form-control" name="end_date" placeholder="End Date" autocomplete="off"
                                    value="{{ request('end_date') }}">
                            </div>
                            <div class="form-group col-md-2">
                                <select name="supplier" id="" class="form-control" data-provide="selectpicker"
                                    data-live-search="true" data-size="14">
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $item)
                                        <option value="{{ $item->id }}"
                                            {{ request('supplier') == $item->id ? 'SELECTED' : '' }}>
                                            {{ $item->name }} - {{ $item->phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <select name="product_id" id="" class="form-control" data-provide="selectpicker"
                                    data-live-search="true" data-size="10">
                                    <option value="">Select Product</option>
                                    @foreach ($products as $item)
                                        <option value="{{ $item->id }}"
                                            {{ request('product_id') == $item->id ? 'SELECTED' : '' }}>
                                            {{ $item->name }} - {{ $item->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-1">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-sliders"></i>
                                    Filter
                                </button>
                            </div>
                            <div class="form-group col-2">
                                <a href="{{ route('pos.index') }}" class="btn btn-info">Reset</a>
                                <a href="" class="btn btn-primary pull-right" onclick="window.print()">Print</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body print_area">
            @if ($purchases->count() > 0)
                <div class="">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr class="bg-primary">
                                <th>#</th>
                                <th>Bill No.</th>
                                <th>Supplier</th>
                                <th>Purchase Date</th>
                                <th>Items</th>
                                <th>Payable</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th class="print_hidden">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchases as $key => $purchase)
                                <tr>
                                    <td>{{ isset($_GET['page']) ? ($_GET['page'] - 1) * $purchases->count() + $key + 1 : $key + 1 }}
                                    </td>
                                    <td>{{ $purchase->id }}</td>
                                    <td>
                                        <a href="{{ route('supplier.index') }}?phone={{ $purchase->supplier->phone }}"
                                            onclick="purchaseView({{ $purchase->id }})">{{ $purchase->supplier->name }}</a>
                                    </td>
                                    <td>{{ date('d M, Y', strtotime($purchase->purchase_date)) }}</td>
                                    <td style="width: 200px">
                                        <ul>
                                            @foreach ($purchase->items as $item)
                                                <li>{{ $item->product->name }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        {{ number_format($purchase->payable, 2) }} Tk
                                    </td>
                                    <td>
                                        {{ number_format($purchase->paid, 2) }} Tk
                                    </td>
                                    <td>
                                        {{ number_format($purchase->due, 2) }} Tk
                                    </td>
                                    <td class="print_hidden">
                                        <div class="btn-group">
                                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fa fa-cogs text-primary"></i>
                                                Manage
                                            </button>
                                            <div class="dropdown-menu" x-placement="bottom-start">
                                                {{-- <a class="dropdown-item" href="#" onclick="purchaseView({{ $purchase->id }})">
                                            <i class="fa fa-eye text-primary"></i>
                                            View
                                        </a> --}}
                                                <a class="dropdown-item"
                                                    href="{{ route('purchase.receipt', $purchase->id) }}">
                                                    <i class="fa fa-print text-primary"></i>
                                                    Invoice
                                                </a>

                                                <a class="dropdown-item" href="{{ route('purchase.show', $purchase) }}">
                                                    <i class="fa fa-desktop text-info"></i>
                                                    Show
                                                </a>

                                                <a class="dropdown-item"
                                                    href="{{ route('purchase.edit', $purchase->id) }}">
                                                    <i class="fa fa-edit text-info"></i>
                                                    Edit
                                                </a>
                                                <a href="{{ route('purchase.add_payment', $purchase->id) }}"
                                                    class="edit dropdown-item" data-toggle="modal" data-target="#edit"
                                                    id="Add Payment">
                                                    <i class="fa fa-money text-primary"></i>
                                                    Add Payment
                                                </a>
                                                <a class="dropdown-item delete"
                                                    href="{{ route('purchase.destroy', $purchase->id) }}">
                                                    <i class="fa fa-trash text-danger"></i>
                                                    Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-dark">
                                <th colspan="5"></th>
                                <th>
                                    <strong>{{ number_format($purchase_service->total_payable(), 2) }}
                                        Tk</strong>
                                </th>
                                <th>
                                    <strong>{{ number_format($purchase_service->total_paid(), 2) }} Tk</strong>
                                </th>
                                <th>
                                    <strong>{{ number_format($purchase_service->total_due(), 2) }} Tk</strong>
                                </th>

                                <th colspan="2"></th>

                            </tr>
                        </tfoot>
                    </table>

                    {!! $purchases->appends(Request::except('_token'))->links() !!}

                </div>
            @else
                <div class="alert alert-danger" role="alert">
                    <strong>You have no Purchases</strong>
                </div>
            @endif
        </div>
    </div>


    {{-- Purchase Show Modal --}}
    <div class="modal fade show" id="purchase-show" tabindex="-1" aria-modal="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Item List</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-responsive table-bordered">
                        <thead class="bg-primary">
                            <tr>
                                <th>SL</th>
                                <th>Product</th>
                                <th>Rate</th>
                                <th>Qyt</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_modal"></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-right">
                                    <strong>Total : <span id="total">0</span> Tk</strong>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
@endsection

@section('scripts')
    @include('includes.delete-alert')
    @include('includes.placeholder_model')
    <script src="{{ asset('js/modal_form.js') }}"></script>
    <script></script>
@endsection
