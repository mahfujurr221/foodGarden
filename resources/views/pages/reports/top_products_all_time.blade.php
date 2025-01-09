@extends('layouts.master')
@section('title', 'Top Products - All Time')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Top Products(All Time)</strong>
            </h1>
        </div>

        {{-- <div class="header-action">
            <nav class="nav">
                <a class="nav-link active" href="{{ route('customer.index') }}">
                    Customers
                </a>
                <a class="nav-link" href="{{ route('customer.create') }}">
                    <i class="fa fa-plus"></i>
                    New Customer
                </a>
            </nav>
        </div> --}}
    </header>
@endsection

@section('content')
    <div class="col-12">
        <div class="card print_area" style="width:100%;">
            <h2 class="card-title" style="text-align: center;"><strong>Top Selling Products(All Time)</strong></h2>
            {{-- <h3 class="card-title" style="text-align: center;">Report From {{ date('d/m/Y',strtotime($start_date)) }} to {{ date('d/m/Y',strtotime($end_date)) }}</h3> --}}
            <div class="card-body card-body-soft p-4">
                @if($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered"
                         {{-- data-provide="datatables" --}}>
                            <thead>
                            <tr class="bg-primary">
                                <th>#</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Sold Quantity</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $key => $product)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->code }}</td>
                                    <td class="font-weight-bold">
                                        {{ $product->readable_qty($product->sell_count())  }}
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                        {{-- {!! $customers->appends(Request::except("_token"))->links() !!} --}}
                    </div>
                @else
                    <div class="alert alert-danger" role="alert">
                        <strong>You have no Products</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    @media print{
        table,table th,table td{
            color:black !important;
        }
    }
</style>
@endsection

@section('scripts')


@endsection
