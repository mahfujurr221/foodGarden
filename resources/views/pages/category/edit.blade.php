@extends('layouts.master')
@section('title', 'Edit Category')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
          <h1 class="header-title">
            <strong>Edit Category</strong>
          </h1>
        </div>

        <div class="header-action">
          <nav class="nav">
            <a class="nav-link" href="{{ route('category.index') }}">
              Category
            </a>
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
      <h4 class="card-title">Edit Category</h4>

    <form action="{{ route('category.update', $category->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
      <div class="card-body">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="">Category Name<span class="field_required"></span></label>
            <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid': '' }}" name="name" value="{{ $category->name }}">
            @if($errors->has('name'))
                <span class="invalid-feedback">{{ $errors->first('name') }}</span>
            @endif
          </div>
          {{-- <div class="form-group col-md-6">
            <label for="">Category Code</label>
            <input type="text" class="form-control {{ $errors->has('code') ? 'is-invalid': '' }}" name="code" value="{{ $category->code }}" >
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
          </div>
          <div class="form-group col-md-6 mt-4">
            <button type="submit" class="btn btn-info float-right">
              <i class="fa fa-refresh"></i>
              Update Category
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
