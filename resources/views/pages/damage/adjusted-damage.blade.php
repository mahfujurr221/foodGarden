@extends('layouts.master')
@section('title', 'Adjusted Damages')
@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Adjusted Damages</strong>
        </h1>
    </div>
</header>
@endsection
@section('content')
<div class="col-12">
    <div class="card print_area">
        <div class="row">
            <div class="col-12" style="display:flex; justify-content:space-between">
                <div class="col-md-2">
                    <h4 class="card-title"><strong>Adjusted Damages</strong></h4>
                </div>
                <div class="col-md-10 print_hidden">
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
                                    <option value="{{ $brand->id }}" @if (request('brand_id')==$brand->id) selected
                                        @endif>
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
                                <a href="{{ route('damage.order') }}" class="btn btn-info">Reset</a>
                            </div>
                            <div class="col-4">
                                <a href="" class="btn btn-primary pull-right" onclick="window.print()">Print</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if ($damages->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr class="bg-primary">
                            <th>#</th>
                            <th>Adjust Date</th>
                            <th>Damage Date</th>
                            <th>Brand Name</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Average Price</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $total = 0;
                        $total_qty = 0;
                        $total_average = 0;
                        $currentDate= null;
                        @endphp
                        @foreach ($damages as $damage)
                        <tr>
                            <td>
                                {{ $loop->iteration }}
                            </td>
                            <td>
                                @if ($damage->adjust_date != $currentDate)
                                <strong>Adjust Date: {{ $damage->adjust_date }}</strong>
                                @endif
                            </td>
                            @php $currentDate = $damage->adjust_date; @endphp
                            <td>
                                {{ $damage->created_at->format('Y-m-d') }}
                            </td>
                            <td>
                                {{ $damage->brand_name }}
                            </td>
                            <td>
                                {{ $damage->product->name }}
                            </td>
                            <td>
                                {{ $damage->damage_qty }} pc
                            </td>
                            <td>
                                {{ number_format($damage->damage_total/$damage->damage_qty,2) }}/-
                            </td>
                            <td>
                                {{number_format( $damage->damage_total) }}/-
                                @php
                                $total += $damage->damage_total;
                                $total_qty += $damage->damage_qty;
                                $total_average += $damage->damage_total/$damage->damage_qty;
                                @endphp
                            </td>
                        </tr>
                        @endforeach

                        <tr>
                            <td colspan="5" class="text-right"><strong>Total: </strong></td>
                            <td><strong>{{ $total_qty }} pc</strong></td>
                            <td><strong>{{ number_format($total_average,2) }}/-</strong></td>
                            <td><strong>{{ number_format($total) }}/-</strong></td>
                            </td>
                        </tr>

                    </tbody>
                </table>
                {{ $damages->links() }}
            </div>
            @else
            <div class="alert alert-danger" role="alert">
                <strong>You have no adjusted damages in this brand</strong>
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