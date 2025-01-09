@extends('layouts.master')
@section('title', 'Owners')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>Owners</strong>
    </h1>
  </div>

  <div class="header-action">
    <nav class="nav">
      <a class="nav-link active" href="{{ route('owners.index') }}">
        Owners
      </a>
      {{-- <a class="nav-link" href="#">Import Products</a> --}}
      <a class="nav-link" href="{{ route('owners.create') }}">
        <i class="fa fa-plus"></i>
        Add Owner
      </a>
    </nav>
  </div>
</header>
@endsection

@section('content')
<div class="col-12">

  {{-- <div class="card card-body mb-2">
    <form action="{{ route('product.index') }}">
      <div class="form-row">
        <div class="form-group col-md-4">
          <input type="text" name="code" class="form-control" placeholder="Product Code">
        </div>
        <div class="form-group col-md-4">
          <input type="text" class="form-control" name="name" placeholder="Product Name">
        </div>
        <div class="form-group col-md-4">
          <div class="form-group">
            <select name="category" id="" class="form-control">
              <option value="">Select Category</option>
              @foreach (\App\Category::all() as $item)
              <option value="{{ $item->id }}" {{ old("category")==$item->id?"SELECTED":"" }}>{{ $item->name }}</option>
              @endforeach
            </select>
            @if($errors->has('category'))
            <div class="alert alert-danger">{{ $errors->first('category') }}</div>
            @endif
          </div>
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
    </form>
  </div> --}}

  <div class="card">
    <h4 class="card-title"><strong>Owners</strong></h4>

    <div class="card-body">
      @if($items->count() > 0)
      <div class="table-responsive-md">
        <table class="table table-sm table-bordered">
          <thead>
            <tr class="bg-primary">
              <th class="text-center">#</th>
              <th>Name</th>
                <th>Mobile</th>
                <th>Address</th>
                <th>Invested</th>
                <th>Withdrawn</th>
                <th>Balance</th>
              <th class="text-center">#</th>
            </tr>
          </thead>
          <tbody>
            @foreach($items as $key => $item)
            <tr>
              <td>{{ $loop->iteration + $items->perPage() * ($items->currentPage() - 1) }}</td>
              <td>{{ $item->name }}</td>
              <td>{{ $item->mobile }}</td>
              <td>{{ $item->address }}</td>
              <td>{{ $invested=$item->invested() }}</td>
              <td>{{ $withdrawn=$item->withdrawn() }}</td>
              <td>{{ $invested-$withdrawn }}</td>

              <td class="text-center">

                {{-- <a href="{{ route('product.details', $item->id) }}" class="btn btn-brown btn-sm">
                  <i class="fa fa-eye"></i>
                </a> --}}
                <div class="btn-group">
                  <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-cogs"></i>
                  </button>
                  <div class="dropdown-menu" x-placement="bottom-start">

                    <a class="dropdown-item" href="{{ route('owners.edit', $item->id) }}">
                      <i class="fa fa-edit"></i>
                      Edit
                    </a>



                    <a class="dropdown-item delete" href="{{ route('owners.destroy', $item->id) }}">
                      <i class="fa fa-trash"></i>
                      Delete
                    </a>
                  </div>
                </div>

              </td>
            </tr>
            @endforeach

          </tbody>
        </table>

        {!! $items->appends(Request::except("_token"))->links() !!}

      </div>
      @else
      <div class="alert alert-danger" role="alert">
        <strong>No Owner Found!</strong>
      </div>
      @endif
    </div>
  </div>
</div>

{{-- End Modal --}}
@endsection

@section('styles')
<style>
  .table>p {
    font-size: 19px;
    padding-top: 5px;
    letter-spacing: 4px;
    margin-bottom: 0px;
  }

  .p_img {
    border: 1px solid rgb(0, 0, 0);
    padding: 5px;
  }

  @media print {
    body {
      width: 100%;
      height: 100%;
      margin: 0;
      padding: 0;
    }

    body * {
      visibility: visible;
    }

    @page {
      size: 'A4'
    }

    #barcode-page {}
  }
</style>
@endsection

@section('scripts')
@include('includes.delete-alert')
@endsection
