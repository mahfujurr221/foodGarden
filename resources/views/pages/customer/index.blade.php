@extends('layouts.master')
@section('title', 'Customer List')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Customers</strong>
            </h1>
        </div>

        <div class="header-action">
            <nav class="nav">
                <a class="nav-link active" href="{{ route('customer.index') }}">
                    Customers
                </a>
                <a class="nav-link" href="{{ route('customer.create') }}">
                    <i class="fa fa-plus"></i>
                    New Customer
                </a>
            </nav>
        </div>
    </header>
@endsection

@section('content')
    <div class="col-12">

        <div class="card print_area">
            <div class="row">
                <div class="col-12" style="display:flex; justify-content:space-between">
                    <div class="col-2">
                        <h4 class="card-title"><strong>Customers</strong></h4>
                    </div>
                    <div class="col-md-9 print_hidden mt-3">
                        <form action="{{ route('customer.index') }}">
                            <div class="form-row">
                                {{-- <div class="form-group col-md-2">
                                    <input type="text" class="form-control" name="name" placeholder="Name"
                                        value="{{ old('name') }}">
                                </div> --}}
                                {{-- <div class="form-group col-md-2">
                                    <input type="text" class="form-control" name="mobile" placeholder="Mobile Number"
                                        value="{{ old('name') }}">
                                </div> --}}
                                <div class="form-group col-md-3">
                                    <select name="customer" id="" class="form-control" data-provide="selectpicker"
                                        data-live-search="true" data-size="10">
                                        <option value="">Select Customer</option>
                                        @foreach (\App\Customer::get() as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->shop_name . '-'.
                                    $customer->address->name . ' (' . $customer->name .')' }}
                                </option>
                                        @endforeach
                                    </select>
                                </div>
            
                                <div class="form-group col-md-2">
                                    <select name="address_id" id="address" class="form-control">
                                    </select>
                                    @if ($errors->has('address_id'))
                                        <span class="invalid-feedback">{{ $errors->first('address_id') }}</span>
                                    @endif
                                </div>
                                <div class="form-group col-md-2">
                                    <select name="business_cat_id" id="business_category" class="form-control">
                                    </select>
                                    @if ($errors->has('business_cat_id'))
                                        <span class="invalid-feedback">{{ $errors->first('business_cat_id') }}</span>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fa fa-sliders"></i>
                                            Filter
                                        </button>
                                        <a href="{{ route('customer.index') }}" class="btn btn-info">Reset</a>
                                    </div>
                                </div>
                            </div>
            
                        </form>
                    </div>
                    <div class="col-1 print_hidden">
                        <a href="" class="btn btn-primary pull-right mt-3" onclick="window.print()">Print</a>
                    </div>
                </div>
            </div>

            <div class="card-body card-body-soft p-4">
                @if ($customers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" {{-- data-provide="datatables" --}}>
                            <thead>
                                <tr class="bg-primary">
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Shop Name</th>
                                    <th>Shop Category</th>
                                    <th>Address</th>
                                    <th>Phone</th>
                                    <th>Receivable</th>
                                    <th>Paid</th>
                                    <th>Sale Due</th>
                                    <th>Wallet Balance</th>
                                    <th>Total Due</th>
                                    <th class="print_hidden">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $key => $customer)
                                    <tr>
                                        <th scope="row">
                                            {{ $loop->iteration + $customers->perPage() * ($customers->currentPage() - 1) }}
                                        </th>
                                        <td>{{ $customer->name??'' }}</td>
                                        <td>{{ $customer->shop_name_bangla??'' }}({{ $customer->shop_name??'' }})</td>
                                        <td>{{ $customer->business_category->name??''}}</td>
                                        <td>{{ $customer->address->name??'' }}</td>
                                        <td>{{ $customer->phone }}</td>
                                        <td class="font-weight-bold">
                                            {{ number_format($customer->receivable()) }} /-
                                        </td>
                                        <td class="font-weight-bold">
                                            {{ number_format($customer->paid()) }} /-
                                        </td>
                                        <td class="font-weight-bold">
                                            {{ number_format($customer->receivable() - $customer->paid()) }} /-
                                        </td>
                                        <td style="text-align:center;">
                                            @php
                                                $wallet_balance = $customer->wallet_balance();
                                            @endphp
                                            <span style="font-weight: bold; font-size:1.3em;">{{number_format($wallet_balance)}}
                                                /-</span>
                                            <br>
                                            @if ($wallet_balance > 0)
                                                <p>** কাস্টমারের টাকা আপনার কাছে জমা আছে</p>
                                            @elseif($wallet_balance < 0)
                                                <p>** কাস্টমারের কাছে আপনার পাওনা রয়েছে</p>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold">
                                            @php
                                                $total_due = $customer->due();
                                                $wallet_balance < 0 ? ($total_due += abs($wallet_balance)) : 0;
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
                                                    <a class="dropdown-item"
                                                        href="{{ route('customer.edit', $customer->id) }}">
                                                        <i class="fa fa-edit"></i>
                                                        Edit
                                                    </a>

                                                    <a href="{{ route('customer.wallet_payment', $customer->id) }}"
                                                        class="edit dropdown-item" data-toggle="modal" data-target="#edit"
                                                        id="Wallet Payment">
                                                        <i class="fa fa-money text-primary"></i>
                                                        Wallet Payment
                                                    </a>

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

                                                    <a class="dropdown-item delete"
                                                        href="{{ route('customer.destroy', $customer->id) }}">
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
                        {!! $customers->appends(Request::except('_token'))->links() !!}
                    </div>
                @else
                    <div class="alert alert-danger" role="alert">
                        <strong>You have no Customers</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')

@endsection

@section('scripts')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script>
        $("#address").select2({
            ajax: {
                url: "{{ route('customer.address') }}",
                dataType: 'json',
                delay: 100,
                data: function(params) {
                    return {

                        query: params.term,
                    };
                },
                processResults: function(data, params) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Address',
            // minimumInputLength: 1,
            //   templateResult: formatRepo,
            //   templateSelection: formatRepoSelection
        });

        $("#business_category").select2({
            ajax: {
                url: "{{ route('customer.business_category') }}",
                dataType: 'json',
                delay: 100,
                data: function(params) {
                    return {

                        query: params.term,
                    };
                },
                processResults: function(data, params) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Business Category',
            // minimumInputLength: 1,
            //   templateResult: formatRepo,
            //   templateSelection: formatRepoSelection
        });
    </script>

    @include('includes.delete-alert')
    @include('includes.placeholder_model')
    <script src="{{ asset('js/modal_form.js') }}"></script>
@endsection
