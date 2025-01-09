@extends('layouts.master')
@section('title', 'Order Damages')
@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Order Damages</strong>
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
                        <h4 class="card-title"><strong>Order Damages</strong></h4>
                    </div>
                    <div class="col-md-10">
                        <form action="#">
                            <div class="row mt-3">
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
                                    <select name="brand_id" class="form-control">
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}"
                                                @if (request('brand_id') == $brand->id) selected @endif>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <select name="product_id" class="form-control select2">
                                        <option selected disabled>Select Product</option>
                                        @foreach (\App\Product::select('id', 'name')->get() as $product)
                                            <option value="{{ $product->id }}"
                                                @if (request('product_id') == $product->id) selected @endif>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-2">
                                    <button class="btn btn-primary" type="submit">Filter</button>
                                    <a href="{{ route('damage.order') }}" class="btn btn-danger">Reset</a>
                                </div>
                                <div class="form-group col-1">
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
                        <form action="{{ route('order-damage.adjust') }}" method="POST">
                            @csrf
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr class="bg-primary">
                                        <th>#</th>
                                        <th>Order Id</th>
                                        <th>Date</th>
                                        <th>Brand Name</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Average Price</th>
                                        <th>Amount</th>
                                        <th style="width:100px;" class="print_hidden">Payment Adjust</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                        $total_qty = 0;
                                        $total_average = 0;
                                    @endphp
                                    @foreach ($damages as $damage)
                                        <tr>
                                            <td>
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>
                                                {{ $damage->id }}
                                            </td>
                                            <td>
                                                {{ $damage->created_at->format('d/m/Y') }}
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
                                        <td colspan="2" class="print_hidden">
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#damageModal">
                                                <i class="fa fa-check"></i>
                                                Adjust
                                            </button>
                                            {{-- // Modal --}}
                                            <div class="modal fade" id="damageModal" tabindex="-1">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Damage Adjust</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="text-center">
                                                                <h4 class="text-danger">Are you sure to adjust this damage?</h4>
                                                                <p class="text-info">This action can't be undone.</p>
                                                                @foreach ($all_damage_id as $damage)
                                                                    <input type="hidden" name="damage_id[]"
                                                                        value="{{ $damage->id }}">
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Adjust
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </form>
                        {{ $damages->links() }}
                    </div>
                @else
                    <div class="alert alert-danger" role="alert">
                        <strong>You have no order damages in this brand</strong>
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
    <script></script>
@endsection
