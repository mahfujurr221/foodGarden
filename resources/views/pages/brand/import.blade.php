@extends('layouts.master')
@section('title', 'Create Brand')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>New Brand</strong>
    </h1>
  </div>

  <div class="header-action">
    <nav class="nav">
      <a class="nav-link" href="{{ route('brand.index') }}">
        Brands
      </a>
      <a class="nav-link" href="#">Import Brands</a>
      <a class="nav-link active" href="{{ route('brand.create') }}">
        <i class="fa fa-plus"></i>
        New Brand
      </a>
    </nav>
  </div>
</header>
@endsection

@section('content')
<div class="col-12">
  <div class="card">
    <h4 class="card-title">New Brand</h4>

    <form action="{{ route('brand.import_store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="card-body">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="">File</label>
            <input type="file" class="form-control {{ $errors->has('import_file') ? 'is-invalid': '' }}"
              name="import_file">
            @if($errors->has('import_file'))
            <span class="invalid-feedback">{{ $errors->first('import_file') }}</span>
            @endif
          </div>
          <div class="form-group col-md-6">
            <label for="">First You need to Download the template</label>
            <div class="form-control text-center">
              <a href="{{ asset('import-template/brands_template.xlsx') }}" class="btn btn-primary">
                <i class="fa fa-download"></i>
                Download
              </a>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-12 mt-4">
            <button type="submit" class="btn btn-dark float-left">
              <i class="fa fa-save"></i>
              Import Brand
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