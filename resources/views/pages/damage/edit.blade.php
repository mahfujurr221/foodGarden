@extends('layouts.master')
@section('title', 'Update Damage')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Update Damage</strong>
            </h1>
        </div>

        {{-- <div class="header-action">
    <nav class="nav">
      <a class="nav-link active" href="{{ route('brand.index') }}">
        Brands
      </a>
       <a class="nav-link" href="{{ route('brand.import') }}">Import Brands</a>
      <a class="nav-link" href="{{ route('brand.create') }}">
        <i class="fa fa-plus"></i>
        Add Brand
      </a>
    </nav>
  </div> --}}
    </header>
@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <h4 class="card-title"><strong>Update Damage</strong></h4>

            <div class="card-body card-body-soft p-10">

                <form action="{{ route('damage.update', $damage->id) }}" method="POST">
                    @method('PUT')
                    @csrf

                    <input type="text" name="product_id" value="{{ $damage->product_id }}" hidden>

                    <h3>{{ $damage->product->name }}</h3>
                    {{-- <div class="form-group">
          <label for="">Product</label>
          <select name="product_id" class="form-control" data-provide="selectpicker" data-live-search="true" readonly>
            <option value="">Select Product</option>
            @foreach (\App\Product::all() as $item)
              <option value="{{ $item->id }}" {{ $damage->product_id==$item->id?"SELECTED":"" }}>{{ $item->name }}</option>
            @endforeach
          </select>
          @if ($errors->has('product_id'))
            <div class="alert alert-danger">{{ $errors->first('product_id') }}</div>
          @endif
        </div> --}}
                    <div class="form-group">
                        <label for="">Quantity</label>
                        <input type="text" name="qty" value="{{ $damage->qty }}" class="form-control">
                        @if ($errors->has('qty'))
                            <div class="alert alert-danger">{{ $errors->first('qty') }}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="">Date</label>
                        <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                            data-date-format="yyyy-mm-dd" class="form-control" name="date" value="{{ $damage->date }}">
                        @if ($errors->has('date'))
                            <div class="alert alert-danger">{{ $errors->first('date') }}</div>
                        @endif
                    </div>
                    <input type="submit" class="btn btn-success" value="Update Damage">
                </form>
            </div>
        </div>
    </div>
@endsection
@section('styles')
@endsection

@section('scripts')
@endsection
