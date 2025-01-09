@extends('layouts.master')
@section('title', 'Edit Product')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>Edit Product</strong>
    </h1>
  </div>

  <div class="header-action">
    <nav class="nav">
      <a class="nav-link" href="{{ route('product.index') }}">
        Product
      </a>
      <a class="nav-link active" href="{{ route('product.create') }}">
        <i class="fa fa-plus"></i>
        Add Product
      </a>
    </nav>
  </div>
</header>
@endsection

@section('content')
<div class="col-12">
  <div class="card">
    <h4 class="card-title">Edit Product</h4>

    <form action="{{ route('product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="card-body">
        <div class="form-row">
          <div class="col-md-6">

            {{-- @include('components.product.type_options',['type_key' => $product->type]) --}}
            <div class="form-group">
              <label for="">Product Name</label>
              <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid': '' }}" name="name"
                value="{{ $product->name }}">
              @if($errors->has('name'))
              <span class="invalid-feedback">{{ $errors->first('name') }}</span>
              @endif
            </div>
            <div class="form-group">
              <label for="">Product Code</label>
              <input type="text" class="form-control {{ $errors->has('code') ? 'is-invalid': '' }}" readonly name="code"
                value="{{ $product->code }}">
              @if($errors->has('code'))
              <span class="invalid-feedback">{{ $errors->first('code') }}</span>
              @endif
            </div>
            @include('components.product.category_options', ['category_id' => $product->category_id])
            @include('components.product.brand_options', ['brand_id' => $product->brand_id])

            <!--<div class="form-group">-->
            <!--  <label for="">Opening Stock</label>-->
            <!--  <input type="text" name="opening_stock" value="{{ $product->opening_stock }}" class="form-control">-->
            <!--  @if($errors->has('opening_stock'))-->
            <!--    <div class="alert alert-danger">{{ $errors->first('opening_stock') }}</div>-->
            <!--  @endif-->
            <!--</div>-->

            <div class="form-group">
              <label for="">Price</label>
              <input type="text" name="price" class="form-control {{ $errors->has('price') ? 'is-invalid': '' }}"
                name="name" value="{{ $product->price }}">
              @if($errors->has('price'))
              <span class="invalid-feedback">{{ $errors->first('price') }}</span>
              @endif
            </div>
            <div class="form-group">
              <label for="cost">Cost</label>
              <input type="text" class="form-control {{ $errors->has('cost') ? 'is-invalid': '' }}" name="cost"
                value="{{ $product->cost }}">
              @if($errors->has('cost'))
              <span class="invalid-feedback">{{ $errors->first('cost') }}</span>
              @endif
            </div>

            {{-- <div class="form-group">
              <label for="tax">Tax</label>
              <input type="text" class="form-control {{ $errors->has('tax') ? 'is-invalid': '' }}" name="tax"
                value="{{ $product->tax }}">
              @if($errors->has('tax'))
              <span class="invalid-feedback">{{ $errors->first('tax') }}</span>
              @endif
            </div> --}}


            {{-- <div class="form-group">
              <label for="alert_qyt">Alert Quantity</label>
              <div class="input-group">
                <span class="input-group-addon">
                  <label class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" name="has_alert">
                    <span class="custom-control-indicator"></span>
                  </label>
                </span>
                <input type="text" name="alert_qyt" class="form-control" aria-label="Alert Quantity"
                  placeholder="Enter Alert Quantity. Default 0">
              </div>
              @if($errors->has('alert_qyt'))
              <span class="invalid-feedback">{{ $errors->first('alert_qyt') }}</span>
              @endif
            </div> --}}


            <div class="form-group">
              <label for="details">Product Details</label>
              <textarea name="details" data-provide="summernote" data-min-height="100"
                placeholder="Write Product Details">{{ old('details') }}</textarea>
              @if($errors->has('details'))
              <span class="invalid-feedback">{{ $errors->first('details') }}</span>
              @endif
            </div>
            <div class="form-group form-type-line file-group">
              <label for="logo">Product Image</label>
              <input type="text" class="form-control file-value file-browser" placeholder="Choose file..." readonly="">
              <input type="file" name="use_file">
              @if($errors->has('use_file'))
              <span class="invalid-feedback">{{ $errors->first('use_file') }}</span>
              @endif

              <div class="img mt-3">
                <img src="{{ asset($product->image) }}" alt="product image" width="80">
              </div>
            </div>
          </div>
          <div class="form-group col-md-6">
            {{-- <label for="description">Product Description</label> --}}
          </div>
        </div>
        <hr>
        <div class="form-row justify-content-center">
          <div class="form-group ">
            <button type="submit" class="btn btn-info">
              <i class="fa fa-save"></i>
              Save
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('styles')
<style>
  .form-control {
    border-color: #b5b1b1;
  }

  label {
    font-size: 13px;
    font-weight: 600;
  }
</style>
@endsection

@section('scripts')

@endsection