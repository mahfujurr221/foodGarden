@extends('layouts.master')
@section('title', 'Product Stock')

@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Product Stock</strong>
        </h1>
    </div>
</header>
@endsection

@section('content')
<div class="col-12">

    <div class="card print_area">
        <div class="row">
            <div class="col-12" style="display:flex; justify-content:space-between">

                <div class="col-2">
                    <h4 class="card-title"><strong>Sales</strong></h4>
                </div>
                <div class="col-md-10">
                    <form action="{{ route('stock.index') }}">
                        <div class="form-row">
                            <div class="form-group col-md-4 mt-3">
                                <select name="brand" id="" class="form-control" data-provide="selectpicker"
                                    data-live-search="true" data-size="10">
                                    @foreach (\App\Supplier::all() as $item)
                                    <option value="{{ $item->id }}" {{ isset($brand) && $brand==$item->id ?
                                        'SELECTED' :
                                        ''
                                        }}>{{ $item->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('brand'))
                                <div class="alert alert-danger">{{ $errors->first('brand') }}</div>
                                @endif
                            </div>
                            <div class="col-md-2 mt-3">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-sliders"></i>
                                    Filter
                                </button>
                                <a href="{{ route('stock.index') }}" class="btn btn-info">Reset</a>
                            </div>
                            <div class="col-md-6 mt-3">
                                <a href="" class="btn btn-primary print_hidden mr-2 pull-right" onclick="window.print()"
                                    style="height: fit-content;">Print</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if ($products->count() > 0)
            <div class="">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr class="bg-primary">
                            <th>#</th>
                            <th>Brand</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Purchased</th>
                            {{-- <th>Dis.Purchased</th> --}}
                            <th>Sold</th>
                            {{-- <th>Dis.Sold</th> --}}
                            {{-- <th>Damaged</th>
                            <th>Returned</th>--}}
                            <th>Available Stock</th>
                            {{-- <th>Available Dis.Stock</th> --}}
                            <th>Purchase Value</th>
                            <th>Sell Value</th>
                        </tr>
                    </thead>

                    @php
                    $total_purchase = 0;
                    $total_sell = 0;
                    @endphp

                    <tbody>
                        @foreach ($products as $key => $product)
                        <tr>
                            <th scope="row">{{ ++$key }}</th>
                            <td>
                                <a href="#">{{ $product->brand ? $product->brand->name : 'No Brand' }}</a>
                            </td>
                            <td>
                                <a href="#">{{ $product->name }}</a>
                            </td>
                            <td>
                                {{ $product->category ? $product->category->name : 'No Category' }}
                            </td>
                            <td>
                                {{ $product->price }} /-
                            </td>
                            <td>
                                {{ $product->readable_qty($product->purchase_count()) }}
                            </td>

                            {{-- <td>
                                {{ $product->discount_purchase_count() }} pc
                            </td> --}}

                            <td>
                                {{ $product->readable_qty($product->sell_count()) }}
                            </td>

                            {{-- <td>
                                {{ $product->readable_qty($product->discount_sell_count()) }}
                            </td> --}}

                            <!--<td>{{ $product->readable_qty($product->damage_count()) }}</td>-->
                            <!--<td>{{ $product->readable_qty($product->return_count()) }}</td>-->

                            <td>
                                {{ $product->readable_qty($product->stock), $stock = $product->stock }}
                            </td>

                            {{-- <td>
                                {{ $product->discount_stock() }} pc
                            </td> --}}

                            <td>
                                {{ number_format($product->quantity_worth($product->stock(), $product->cost)) }} /-
                            </td>
                            <td>
                                {{ number_format($product->quantity_worth($product->stock(), $product->price)) }} /-
                            </td>

                        </tr>

                        @php
                        $total_purchase += $product->quantity_worth($product->stock(), $product->cost);
                        $total_sell += $product->quantity_worth($product->stock(), $product->price);
                        @endphp

                        @endforeach
                    </tbody>

                    <tfoot class="bg-dark text-white">
                        <tr>
                            <th colspan="8" class="text-right text-bold"><strong>Total:</strong></th>
                            <th><strong>{{ number_format($total_purchase) }}/-</strong></th>
                            <th><strong>{{ number_format($total_sell) }}/-</strong></th>
                        </tr>
                    </tfoot>

                </table>
                @if(request()->brand==null)
                {!! $products->appends(['brand' => request()->brand])->links() !!}
                @endif
            </div>
            @else
            <div class="alert alert-danger" role="alert">
                <strong>You have no Stocks</strong>
            </div>
            @endif
        </div>

    </div>
</div>
</div>
@endsection

@section('styles')
<style>
    .table tr td {
        vertical-align: middle;
        padding: 5px;
        text-align: center;
        font-weight: bold;
    }

    .table tr th {
        text-align: center;
    }
</style>
@endsection

@section('scripts')
<script></script>
@endsection