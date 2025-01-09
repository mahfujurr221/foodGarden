@extends('layouts.master')
@section('title', 'Create Category')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
          <h1 class="header-title">
            <strong>New Category</strong>
          </h1>
        </div>

        <div class="header-action">
          <nav class="nav">
            <a class="nav-link" href="{{ route('category.index') }}">
              Category
            </a>
             {{-- <a class="nav-link" href="#">Import Category</a> --}}
            <a class="nav-link active" href="{{ route('category.create') }}">
                 <i class="fa fa-plus"></i>
                 Add Category
            </a>
          </nav>
        </div>
      </header>
@endsection

@section('content')
  <div class="col-12">
    <div class="card">
      <h4 class="card-title">New Category</h4>

    <form action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
      <div class="card-body">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="">Category Name<span class="field_required"></span></label>
            <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid': '' }}" name="name" value="{{ old('name') }}" placeholder="Enter Category Name...">
            @if($errors->has('name'))
                <span class="invalid-feedback">{{ $errors->first('name') }}</span>
            @endif
          </div>
          {{-- <div class="form-group col-md-6">
            <label for="">Category Code</label>
            <input type="text" class="form-control {{ $errors->has('code') ? 'is-invalid': '' }}" name="code" value="{{ old('code') }}" placeholder="Enter Category Code...">
            @if($errors->has('code'))
                <span class="invalid-feedback">{{ $errors->first('code') }}</span>
            @endif
          </div> --}}

        </div>

        <div class="form-row">
          <div class="form-group col-md-6 form-type-line file-group">
            <label for="logo">Cateogyr Image</label>
            <input type="text" class="form-control file-value file-browser" placeholder="Choose file..." readonly="">
            <input type="file" name="use_file">
            @if($errors->has('use_file'))
              <div class="alert alert-danger">{{ $errors->first('use_file') }}</div>
            @endif
          </div>
          <div class="form-group col-md-6 mt-4">
            <button type="submit" class="btn btn-info float-right">
              <i class="fa fa-save"></i>
              Save Category
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
