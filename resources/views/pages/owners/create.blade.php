@extends('layouts.master')
@section('title', 'Create Owner')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>New Owner</strong>
    </h1>
  </div>

  <div class="header-action">
    <nav class="nav">
      <a class="nav-link" href="{{ route('owners.index') }}">
        Owners
      </a>
      <a class="nav-link active" href="{{ route('owners.create') }}">
        <i class="fa fa-plus"></i>
        Add Owner
      </a>
    </nav>
  </div>
</header>
@endsection

@section('content')
<div class="col-12">
  <div class="card">
    <div class="row">
      <div class="col-12" style="">
        <h4 class="card-title" style="display: inline-block;">New Owner</h4>

        {{-- <a href="{{ route('product.add_category') }}" class="edit btn btn-info-outline float-right mt-2 ml-4"
          data-target="#edit" id="Add Category">Add Category</a> --}}

      </div>
    </div>


    <form action="{{ route('owners.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="">Name</label>
                <input type="text" name="name" value="{{ old("name") }}" class="form-control">
                @error('name')
                  <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group col-md-6">
                <label for="">Mobile</label>
                <input type="text" name="mobile" value="{{ old("mobile") }}" class="form-control">
                @error('mobile')
                  <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group col-12">
                <label for="">Address</label>
                <textarea name="address" id="" cols="30" rows="4" class="form-control">{{ old('address') }}</textarea>
                @error('address')
                  <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
              </div>
        </div>

        <hr>
        <div class="form-row justify-content-center">
          <div class="form-group ">
            <button type="submit" class="btn btn-info">
              <i class="fa fa-save"></i>
              Add Owner
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('styles')
{{--
<link rel="stylesheet" href="{{ asset('back/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}"> --}}
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

<script src="{{ asset('js/modal_form_no_reload.js') }}"></script>

@include('includes.placeholder_model')
@endsection
