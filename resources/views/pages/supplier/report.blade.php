@extends('layouts.master')
@section('title', 'Supplier Report')
@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>
                    {{ $supplier->name }}
                    <span class="small">Supplier</span>
                </strong>

            </h1>
        </div>

    </header>
@endsection

@section('content')
    <div class="col-md-3 col-lg-3">
        <div class="card card-body bg-primary">
            <h6>
                <span class="text-uppercase text-white">Total Purchase</span>
            </h6>
            <br>
            <p class="fs-18 fw-600">৳ {{ number_format($supplier->purchases->sum('payable')) }}</p>
        </div>
    </div>
    <div class="col-md-3 col-lg-3">
        <div class="card card-body bg-success">
            <h6>
                <span class="text-uppercase text-white">Total Paid</span>
            </h6>
            <br>
            <p class="fs-18 fw-600">৳ {{ number_format($supplier->paid()) }}</p>
        </div>
    </div>
    <div class="col-md-3 col-lg-3">
        <div class="card card-body bg-danger">
            <h6>
                <span class="text-uppercase text-white">Total Due</span>
            </h6>
            <br>
            <p class="fs-18 fw-600">৳
                {{ number_format($supplier->purchases->sum('payable') - $supplier->paid()) }}
            </p>
        </div>
    </div>
    <div class="col-md-3 col-lg-3">
        <div class="card card-body bg-info">
            <h6>
                <span class="text-uppercase text-white">Information</span>
            </h6>
            <p class="mb-0">Address: {{ $supplier->address }}</p>
            <p>Phone: {{ $supplier->phone }}</p>

        </div>
    </div>

    {{--  End Summary  --}}
    <div class="col-md-12">
        <div class="card">
            <h4 class="card-title"><strong>{{ $supplier->name }} - History</strong></h4>

            <div class="card-body">
                <div class="">
                    <h4 class="p-2">Purchase Report</h4>
                    @if($supplier->purchases->count() > 0)
                        <table class="table table-responsive-sm table-soft table-bordered"
                        {{-- data-provide="datatables" --}}
                        >
                            <thead>
                            <tr class="bg-primary">
                                <th>#</th>
                                <th>Purchases Date</th>
                                <th>Total Item</th>
                                <th>Total Bill</th>
                                <th>Pay</th>
                                <th>Due</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($supplier->purchases as $key => $item)
                                <tr>
                                    <td>{{ ++$key}}</td>
                                    <td>{{ date('d M, Y', strtotime($item->purchase_date)) }}</td>
                                    <td>{{ $item->items->count() }}</td>
                                    <td>{{ $item->payable }} Tk</td>
                                    <td>{{ $item->payments ? $item->payments->sum('pay_amount') : 'No Payment' }}Tk
                                    </td>
                                    <td>{{ $item->payments ? $item->payable - $item->payments->sum('pay_amount') : 'No Payment' }}
                                        Tk
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-warning text-center">
                            <strong>{{ $supplier->name }} - No Purchase History. Sorry !</strong>
                        </div>
                    @endif
                </div>
                <div class="">
                    <h5 class="p-2 mt-4">Purchase Payment Report</h5>
                    <table class="table table-responsive-sm table-soft table-bordered"
                     {{-- data-provide="datatables" --}}
                     >
                        <thead>
                        <tr class="bg-primary">
                            <th>#</th>
                            <th>Payment Date</th>
                            <th>Pay Amount</th>
                            <th>Transaction Account</th>
                            <th>Payment Type</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $x => $payment)
                                <tr>
                                    <td>{{ ++$x}}</td>
                                    <td>{{ date('d M, Y', strtotime($payment->payment_date)) }}</td>
                                    <td>{{ $payment->pay_amount }} Tk</td>
                                    <td>{{ $payment->account ? ucfirst(str_replace('-', ' ', $payment->account->name)) : 'Unknown Method' }}</td>
                                    <td style="text-transform: capitalize">{{ $payment->payment_type }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('styles')

@endsection

@section('scripts')

@endsection
