@extends('layouts.master')
@section('title', 'Order Returns')
@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Order Returns</strong>
            </h1>
        </div>
    </header>
@endsection

@section('content')
    <div class="col-12">
        <div class="card card-body mb-2">
            <div class="d-flex justify-content-end mr-5 mb-2">
                <a href="" class="btn btn-primary pull-right" onclick="window.print()">Print</a>
            </div>
        </div>
        <div class="card print_area">
            <div class="row">
                <div class="col-12" style="display:flex; justify-content:space-between">
                    <div class="col-md-2">
                        <h4 class="card-title"><strong>Order Returns</strong></h4>
                    </div>
                    <div class="col-md-10">
                        <form action="#">
                            <div class="form-row mt-3">
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
                                    <select name="brand_id" id="" class="form-control">
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}"
                                                @if (request('brand_id') == $brand->id) selected @endif>
                                                {{ $brand->name }}
                                            </option>
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
                                    <a href="{{ route('pos.index') }}" class="btn btn-info">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if ($returns->count() > 0)
                    <div class="table-responsive">
                        <form action="{{ route('order-damage.adjust') }}" method="POST">
                            @csrf
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-primary">
                                        <th>#</th>
                                        <th>Order Id</th>
                                        <th>Date</th>
                                        <th>Brand </th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Purchase Price</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                    @endphp
                                    @foreach ($returns as $return)
                                        <tr>
                                            <td>
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>
                                                {{ $return->id }}
                                            </td>
                                            <td>
                                                {{ $return->created_at->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                {{ $return->brand_name }}
                                            </td>
                                            <td>
                                                {{ $return->product->name }}
                                            </td>
                                            <td>
                                                {{ $return->qty }}pc
                                            </td>
                                            <td>
                                                {{ $return->product->cost }}Tk
                                            </td>
                                            <td>
                                                {{ $return->total }}Tk
                                                @php
                                                    $total += $return->total;
                                                @endphp
                                            </td>
                                        </tr>
                                        <input type="text" name="estimate_id[]" value="{{ $return->id }}" hidden>
                                    @endforeach
                                    <tr>
                                        <td colspan="8" class="text-right"><strong>Total: {{ $total }}
                                                Tk</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                        {{ $returns->links() }}
                    </div>
                @else
                    <div class="alert alert-danger" role="alert">
                        <strong>You have no order returns in this brand</strong>
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
@endsection
