@extends('layouts.master')
@section('title', 'Low Stock Report')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Low Stock Report</strong>
          </h1>
     </div>
</header>
@endsection

@section('content')

<div class="col-12">
     <div class="card">
          <div class="print_area">
               <div class="row">
                    <div class="col-12" style="display:flex; justify-content:space-between">

                         <div class="col-2">
                              <h4 class="card-title"><strong>Sales</strong></h4>
                         </div>
                         <div class="col-md-10">
                              <form action="{{ route('report.low_stock') }}">
                                   <div class="form-row">
                                        <div class="form-group col-md-4 mt-3">
                                             <select name="brand" id="" class="form-control" data-provide="selectpicker"
                                                  data-live-search="true" data-size="10">
                                                  @foreach (\App\Supplier::where('status', 1)->get() as $item)
                                                  <option value="{{ $item->id }}" {{ isset($brand) && $brand==$item->id
                                                       ?
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
                                             <a href="" class="btn btn-primary print_hidden mr-2 pull-right"
                                                  onclick="window.print()" style="height: fit-content;">Print</a>
                                        </div>
                                   </div>
                              </form>
                         </div>
                    </div>
               </div>

               <div class="card-body">
                    @if($products->count() > 0)
                    <div class="">
                         <table class="table table-responsive table-bordered pt-2">
                              <thead>
                                   <tr class="bg-primary">
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Sale</th>
                                        <th>Purchases</th>
                                        <th>Available Stock</th>
                                        <th>Sell Value</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   @foreach($products as $key => $product)
                                   <tr>
                                        <th scope="row">{{ ++$key }}</th>
                                        <td style="padding:5px" class="text-center">
                                             <img src="{{ asset($product->image) }}" width="40" alt="Image">
                                        </td>
                                        <td>
                                             <a href="#">{{ $product->name}}</a>
                                        </td>
                                        <td>
                                             {{ $product->category ? $product->category->name : 'No Category' }}
                                        </td>
                                        <td>
                                             {{ $product->price }} Tk

                                        </td>
                                        <td>
                                             {{ $product->readable_qty($product->sell_count()) }}
                                        </td>
                                        <td>
                                             {{ $product->readable_qty($product->purchase_count()) }}
                                        </td>
                                        <td>
                                             {{ $product->readable_qty($product->stock) }}
                                        </td>
                                        <td>
                                             {{ $product->quantity_worth($product->stock,$product->price) }} Tk
                                        </td>
                                   </tr>
                                   @endforeach
                              </tbody>
                         </table>
                         {!! $products->appends(Request::except("_token"))->links() !!}
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
<script>

</script>
@endsection