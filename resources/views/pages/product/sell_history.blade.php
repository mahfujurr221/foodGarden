@extends('layouts.master')
@section('title', 'Product List')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>Sell History</strong>
    </h1>
  </div>

  {{-- <div class="header-action">
    <nav class="nav">
      <a class="nav-link active" href="{{ route('product.index') }}">
        Products
      </a>
      <a class="nav-link" href="{{ route('product.create') }}">
        <i class="fa fa-plus"></i>
        Add Product
      </a>
    </nav>
  </div> --}}


</header>
@endsection

@section('content')
<div class="col-12">


  {{-- <div class="card card-body mb-2"> --}}
    {{-- <form action="{{ route('product.sell_history') }}">
         <div class="form-row">
              <div class="form-group col-md-4">
                   <input type="text" name="code" class="form-control" placeholder="Product Code">
              </div>
         </div>
         <div class="form-row mt-2">
              <div class="form-group float-right">
                   <button class="btn btn-primary" type="submit">
                        <i class="fa fa-sliders"></i>
                        Filter
                   </button>
                   <a href="{{ route('product.index') }}" class="btn btn-info">Reset</a>
              </div>
         </div>
    </form> --}}
{{-- </div> --}}

  <div class="card">
    <h4 class="card-title"><strong>Sell History</strong></h4>

    <div class="card-body">
      @if($histories->count() > 0)
      <div class="">
        <table class="table table-responsive-sm table-bordered" data-provide="">
          <thead>
            <tr class="bg-primary">
              <th class="text-center">#</th>
              <th style="width:10%;">Sell Date</th>
              <th>Sale#</th>
              <th>Name</th>
              <th>Unit Price:</th>
              <th>Quantity</th>
              <th>Sub Total</th>
              {{-- <th class="text-center">#</th> --}}
            </tr>
          </thead>
          <tbody>
            @foreach($histories as $key => $history)
            <tr>
              <td>{{ (isset($_GET['page']))? ($_GET['page']-1)*20+$key+1 : $key+1 }}</td>
              <td>{{ date('d/m/Y',strtotime($history->pos->sale_date)) }}</td>
              <td>
                <a href="{{ route('pos_receipt', $history->pos_id) }}">Pos#{{ $history->pos_id }}</a>
              </td>
              <td>{{ $history->product_name }}</td>
              <td>{{ $history->rate }}</td>
              <td>{{ $history->product->readable_qty($history->qty) }}</td>
              <td>{{ $history->sub_total }}</td>

            </tr>
            @endforeach

          </tbody>
        </table>

        {!! $histories->appends(Request::except("_token"))->links() !!}

      </div>
      @else
      <div class="alert alert-danger" role="alert">
        <strong>Sell History Not Found!</strong>
      </div>
      @endif
    </div>
  </div>
</div>

{{-- End Modal --}}
@endsection

@section('styles')
@endsection

@section('scripts')
@endsection
