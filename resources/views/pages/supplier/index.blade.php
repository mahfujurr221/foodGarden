@extends('layouts.master')
@section('title', 'Supplier List')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Suppliers</strong>
            </h1>
        </div>

        <div class="header-action">
            <nav class="nav">
                <a class="nav-link active" href="{{ route('supplier.index') }}">
                    Suppliers
                </a>
                {{-- <a class="nav-link" href="#">Import Suppliers</a> --}}
                <a class="nav-link" href="{{ route('supplier.create') }}">
                    <i class="fa fa-plus"></i>
                    New Supplier
                </a>
            </nav>
        </div>
    </header>
@endsection

@section('content')
    <div class="col-12">

        <div class="card card-body mb-2">
            <form action="{{ route('supplier.index') }}">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" name="name" placeholder="Name"
                               value="{{ isset($name)?$name:"" }}">
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" name="phone" placeholder="Mobile Number"
                               value="{{ isset($phone)?$phone:"" }}">
                    </div>
                </div>
                <div class="form-row mt-2">
                    <div class="form-group float-right">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-sliders"></i>
                            Filter
                        </button>
                        <a href="{{ route('supplier.index') }}" class="btn btn-info">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card print_area">
            <div class="row">
                <div class="col-12" style="display:flex; justify-content:space-between">
                    <h4 class="card-title"><strong>Suppliers</strong></h4>
                    <a href="" class="btn btn-primary print_hidden mt-2 mr-2" onclick="window.print()" style="height: fit-content;">Print</a>
                </div>
            </div>

            <div class="card-body card-body-soft p-4">
                @if($suppliers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-soft table-bordered"
                         {{-- data-provide="datatables" --}}>
                            <thead>
                            <tr class="bg-primary">
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Payable</th>
                                <th>Paid</th>
                                <th>Purchase Due</th>
                                <th>Wallet Balance</th>
                                <th>Total Due</th>
                                <th class="print_hidden">#</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($suppliers as $key => $supplier)
                                <tr>
                                    <th scope="row">{{ (isset($_GET['page']))? ($_GET['page']-1)*20+$key+1 : $key+1 }}</th>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->email }}</td>
                                    <td>{{ $supplier->phone }}</td>
                                    <td>{!! $supplier->address !!}</td>
                                    <td>{{ number_format($supplier->payable()) }} /-</td>
                                    <td>{{ number_format($supplier->paid()) }} /-</td>

                                    <td>{{ number_format($supplier->due()) }}
                                        /-
                                    </td>
                                    <td style="text-align:center;">
                                        @php
                                        $wallet_balance=$supplier->wallet_balance();
                                        @endphp
                                        <span style="font-weight: bold; font-size:1.3em;">{{ $wallet_balance }} /-</span>
                                        <br>
                                        @if($wallet_balance>0)
                                            <p>** সাপ্লাইয়ারের কাছে আপনার টাকা জমা আছে</p>
                                        @elseif($wallet_balance<0)
                                            <p>** সাপ্লাইয়ার আপনাকে দিয়েছে</p>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold">
                                    @php
                                        $total_due = $supplier->due();
                                        $wallet_balance < 0 ? $total_due +=abs($wallet_balance) : 0;
                                    @endphp
                                    {{ number_format($total_due, 2) }} /-
                                    </td>
                                    <td class="print_hidden">
                                        <div class="btn-group">
                                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                                    aria-expanded="false">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu" x-placement="bottom-start">
                                                @if($supplier->default==0)
                                                <a class="dropdown-item"
                                                   href="{{ route('supplier.edit', $supplier->id) }}">
                                                    <i class="fa fa-edit"></i>
                                                    Edit
                                                </a>
                                                @endif
                                                <a href="{{ route("supplier.wallet_payment",$supplier->id) }}" class="edit dropdown-item" data-toggle="modal" data-target="#edit" id="Wallet Payment">
                                                    <i class="fa fa-money text-primary"></i>
                                                    Wallet Payment
                                                </a>
                                                <a class="dropdown-item"
                                                   href="{{ route('supplier.report', $supplier->id) }}">
                                                    <i class="fa fa-file-excel-o"></i>
                                                    Report
                                                </a>
                                                <a href="{{ route('report.supplier_ledger') }}?supplier_id={{ $supplier->id }}" class="dropdown-item">
                                                    <i class="fa fa-book"></i>
                                                    Ledger
                                                </a>

                                                <a href="{{ route('purchase.index') }}?supplier={{ $supplier->id }}" class="dropdown-item" target="_blank">
                                                    <i class="fa fa-list"></i>
                                                    Purchase List
                                                </a>

                                                <a href="{{ route('payment.index') }}?supplier={{ $supplier->id }}" class="dropdown-item" target="_blank">
                                                    <i class="fa fa-money"></i>
                                                    Payments List
                                                </a>

                                                @if($supplier->default==0)
                                                <a class="dropdown-item delete" href="{{ route('supplier.destroy',$supplier->id) }}">
                                                    <i class="fa fa-trash"></i>
                                                    Delete
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                        {!! $suppliers->appends(Request::except("_token"))->links() !!}
                    </div>
                @else
                    <div class="alert alert-danger" role="alert">
                        <strong>You have no Suppliers</strong>
                    </div>
                @endif
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
@endsection
