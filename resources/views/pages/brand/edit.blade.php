@extends('layouts.master')
@section('title', 'Edit Brand')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
          <h1 class="header-title">
            <strong>Edit Brand</strong>
          </h1>
        </div>

        <div class="header-action">
          <nav class="nav">
            <a class="nav-link" href="{{ route('brand.index') }}">
              Brands
            </a>
            <a class="nav-link active" href="{{ route('brand.create') }}">
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
      <h4 class="card-title">Edit Brand</h4>

    <form action="{{ route('brand.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
      <div class="card-body">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="">Brand Name<span class="field_required"></span></label>
            <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid': '' }}" name="name" value="{{ $brand->name }}" placeholder="Enter Brand Name...">
            @if($errors->has('name'))
                <span class="invalid-feedback">{{ $errors->first('name') }}</span>
            @endif
          </div>
          <div class="form-group col-md-6">
            <label for="description">Brand Description</label>
            <textarea name="description" class="form-control" placeholder="Enter Brand Description">{{ $brand->description }}</textarea>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6 form-type-line file-group">
            <label for="logo">Brand Logo</label>
            <input type="text" class="form-control file-value file-browser" placeholder="Choose file..." readonly="">
            <input type="file" name="logo">
            @if($errors->has('logo'))
              <div class="alert alert-danger">{{ $errors->first('logo') }}</div>
            @endif
          </div>
          <div class="form-group col-md-6 mt-4">
            <button type="submit" class="btn btn-info float-right">
              <i class="fa fa-refresh"></i>
              Update Brand
            </button>
          </div>
        </div>
      </div>
      </form>
    </div>
  </div>
@endsection

@section('styles')

@endsection

@section('scripts')

@endsection
