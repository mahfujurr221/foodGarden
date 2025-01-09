@extends('layouts.master')
@section('title', 'Top Customers')

@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Top Customers</strong>
        </h1>
    </div>
</header>
@endsection

@section('content')
<div class="col-12">
    
    <div class="card col-12 print_area">
        <h2 class="card-title" style="text-align: center;"><strong>Top Customers(Based on Sell Amount)</strong></h2>
        <h3 class="card-title" style="text-align: center;">Report From {{ date('d/m/Y',strtotime($start_date)) }} to {{
            date('d/m/Y',strtotime($end_date)) }}</h3>
            <div class="col-md-12 print_hidden my-2">
                <form action="#">
                    <div class="form-row mt-3">
                        <div class="form-group col-md-2">
                            <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                                data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                                class="form-control" name="start_date" placeholder="Start Date" autocomplete="off"
                                value="{{ $start_date }}">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                                data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                                class="form-control" name="end_date" placeholder="End Date" autocomplete="off"
                                value="{{ $end_date }}">
                        </div>

                        <div class="form-group col-md-2">
                            <select name="customer_id" id="" class="form-control" data-provide="selectpicker"
                                data-live-search="true" data-size="10">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id')==$customer->id ? 'SELECTED' : ''}}>
                                {{ $customer->shop_name_bangla . ' ' . $customer->shop_name . '-'.
                                    $customer->address->name . ' (' . $customer->name . '-'  . $customer->phone .')' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <select name="brand_id" id="" class="form-control" data-provide="selectpicker"
                                data-live-search="true" data-size="10">
                                <option value="">Select Brand</option>
                                @foreach ($brands as $item)
                                <option value="{{ $item->id }}" {{ request('brand_id')==$item->id ? 'SELECTED' : '' }}>
                                    {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
        
                        <div class="form-group col-1">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-sliders"></i>
                                Filter
                            </button>
                        </div>
        
                        <div class="form-group col-1">
                            <a href="{{ route('report.top_customer') }}" class="btn btn-danger">
                                <i class="fa fa-refresh"></i>
                                Reset
                            </a>
                        </div>
        
                    </div>
                </form>
            </div>
        <div class="card-body card-body-soft p-4">
            @if($customers->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" {{-- data-provide="datatables" --}}>
                    <thead>
                        <tr class="bg-primary">
                            <th>#</th>
                            <th>Shop Name</th>
                            <th>Customer Name</th>
                            <th>Address</th>
                            <th>Phone</th>
                            {{-- <th>Opening Balance</th> --}}
                            <th>Total Sell</th>
                            {{-- <th>Paid</th>
                            <th>Due</th>
                            <th>Wallet Balance</th> --}}
                            {{-- <th>#</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $key => $customer)
                        <tr>
                            <th scope="row">{{ (isset($_GET['page']))? ($_GET['page']-1)*20+$key+1 : $key+1 }}</th>
                            <td>{{ $customer->shop_name_bangla??'' }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{!! $customer->address->name ?? '<span class="text-danger">No Address</span>' !!}</td>
                            <td>{{ $customer->phone }}</td>
                            {{-- <th>{{ $customer->opening_balance }}</th> --}}
                            <td class="font-weight-bold">
                                {{ number_format($customer->receivable($start_date,$end_date)) }} Tk
                            </td>
                            {{-- <td class="font-weight-bold">
                                {{ number_format($customer->paid()) }} Tk
                            </td>

                            <td class="font-weight-bold">
                                {{ number_format($customer->receivable() - $customer->paid() ) }} Tk
                            </td>
                            <td>
                                {{ number_format($customer->wallet_balance()) }} Tk
                            </td> --}}
                            {{-- <td>
                                <div class="btn-group">
                                    <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fa fa-cogs"></i>
                                    </button>
                                    <div class="dropdown-menu" x-placement="bottom-start">
                                        <a class="dropdown-item" href="{{ route('customer.edit', $customer->id) }}">
                                            <i class="fa fa-edit"></i>
                                            Edit
                                        </a>
                                        <a class="dropdown-item" href="{{ route('customer.show', $customer->id) }}">
                                            <i class="fa fa-file-excel-o"></i>
                                            Report
                                        </a>
                                        <a class="dropdown-item delete"
                                            href="{{ route('customer.destroy',$customer->id) }}">
                                            <i class="fa fa-trash"></i>
                                            Delete
                                        </a>
                                    </div>
                                </div>
                            </td> --}}
                        </tr>
                        @endforeach

                    </tbody>
                </table>
                {!! $customers->appends(Request::except("_token"))->links() !!}
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


@endsection