@extends('layouts.master')
@section('title', 'Top Products')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Top Products</strong>
            </h1>
        </div>
    </header>
@endsection

@section('content')
    <div class="col-12">

        <div class="card card-body mb-2">
            <form action="{{ route('report.top_product') }}">
                <div class="row">
                    <div class="form-group col-md-4">
                        <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                               data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                               class="form-control" name="start_date" placeholder="Start Date" autocomplete="off" value="{{ $start_date }}">
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                               data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                               class="form-control" name="end_date" placeholder="End Date" autocomplete="off" value="{{ $end_date }}">
                    </div>
                    <div class="form-group col-4">
                        <button class="btn btn-primary" type="submit">
                            {{-- <i class="fa fa-sliders"></i> --}}
                            Filter
                        </button>
                        <a href="{{ route('report.top_product') }}" class="btn btn-info">Reset</a>
                        <a href="" class="btn btn-primary float-right" onclick="window.print()">Print</a>

                    </div>
                </div>
            </form>
        </div>

        <div class="card print_area" style="width:100%;">

            <h2 class="card-title" style="text-align: center;"><strong>Top Selling Products</strong></h2>
            <h3 class="card-title" style="text-align: center;">Report From {{ date('d/m/Y',strtotime($start_date)) }} to {{ date('d/m/Y',strtotime($end_date)) }}</h3>

            <div class="card-body card-body-soft p-4">
                @if($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered"
                         {{-- data-provide="datatables" --}}
                         >
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
                                        {{ $product->readable_qty($product->sell_count($start_date,$end_date))  }}
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
