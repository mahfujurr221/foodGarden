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
            <a class="nav-link" href="{{ route('supplier.index') }}">
              Brands
            </a>
            {{-- <a class="nav-link" href="#">Import Brands</a> --}}
            <a class="nav-link" href="{{ route('supplier.create') }}">
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
      <h4 class="card-title">Edit Brand</h4>

    <form action="{{ route('supplier.update', $supplier->id) }}" method="POST">
    @csrf
    @method('PUT')
      <div class="card-body">
        <div class="form-row">
             <div class="col-md-6">
               <div class="form-group">
                    <label for="">Brand Name<span class="field_required"></span></label>
                    <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid': '' }}" name="name" value="{{ $supplier->name }}">
                    @if($errors->has('name'))
                         <span class="invalid-feedback">{{ $errors->first('name') }}</span>
                    @endif
               </div>
               <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" class="form-control {{ $errors->has('email') ? 'is-invalid': '' }}" name="email" value="{{ $supplier->email }}">
                    @if($errors->has('email'))
                         <span class="invalid-feedback">{{ $errors->first('email') }}</span>
                    @endif
               </div>
               <div class="form-group">
                    <label for="phone">Address</label>
                    <textarea name="address" class="form-control {{ $errors->has('address') ? 'is-invalid': '' }}">{{ $supplier->address }}</textarea>
                    @if($errors->has('address'))
                         <span class="invalid-feedback">{{ $errors->first('address') }}</span>
                    @endif
               </div>
             </div>
             <div class="col-md-6">
               <div class="form-group">
                    <label for="phone">Phone<span class="field_required"></span></label>
                    <input type="text" class="form-control {{ $errors->has('phone') ? 'is-invalid': '' }}" name="phone" value="{{ $supplier->phone }}">
                    @if($errors->has('phone'))
                         <span class="invalid-feedback">{{ $errors->first('phone') }}</span>
                    @endif
               </div>
             </div>
        </div> <!-- End form-row -->
     <div class="form-row justify-content-center">
          <button class="btn btn-primary">
               <i class="fa fa-refresh mr-2"></i>
                    Update
          </button>
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
