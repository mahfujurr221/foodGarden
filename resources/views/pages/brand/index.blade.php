@extends('layouts.master')
@section('title', 'Brand List')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>Brands</strong>
    </h1>
  </div>

  <div class="header-action">
    <nav class="nav">
      <a class="nav-link active" href="{{ route('brand.index') }}">
        Brands
      </a>
       {{-- <a class="nav-link" href="{{ route('brand.import') }}">Import Brands</a> --}}
      <a class="nav-link" href="{{ route('brand.create') }}">
        <i class="fa fa-plus"></i>
        Add Brand
      </a>
    </nav>
  </div>
</header>
@endsection

@section('content')
<div class="col-12">
  <div class="card">
    <h4 class="card-title"><strong>Brands</strong></h4>

    <div class="card-body card-body-soft">
      @if($brands->count() > 0)
      <div class="table-responsive table-bordered">
        <table class="table table-soft">
          <thead>
            <tr class="bg-primary">
              <th>#</th>
              <th>Brand</th>
              <th>Description</th>
              <th>Logo</th>
              <th>Count Products</th>
              <th>#</th>
            </tr>
          </thead>
          <tbody>
            @foreach($brands as $key => $brand)
            <tr>
              <th scope="row">{{ ++$key }}</th>
              <td>{{ $brand->name }}</td>
              <td>{{ $brand->description }}</td>
              <td>
                <img src="{{ $brand->logo ? asset($brand->logo->link) : asset('dashboard/images/not-available.png') }}"
                  width="80" alt="logo">
                {{-- {{ $brand->logo ? $brand->logo->link : 'Not Found Logo'   }} --}}
              </td>
              <td>
                {{$brand->products->count()}}
              </td>
              <td>
                <div class="btn-group">
                  <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-cogs"></i>
                    Manage
                  </button>
                  <div class="dropdown-menu" x-placement="bottom-start">
                    <a class="dropdown-item" href="{{ route('brand.edit', $brand->id) }}">
                      <i class="fa fa-edit"></i>
                      Edit
                    </a>
                    <a class="dropdown-item delete" href="{{ route('brand.destroy',$brand->id) }}">
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
        {{ $brands->links() }}
      </div>
      @else
      <div class="alert alert-danger" role="alert">
        <strong>You have no brands</strong>
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