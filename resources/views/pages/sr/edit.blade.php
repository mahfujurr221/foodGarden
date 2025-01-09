@extends('layouts.master')
@section('title', 'Edit SR')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Edit SR</strong>
          </h1>
     </div>

     <div class="header-action">
          <nav class="nav">
               <a class="nav-link" href="{{ route('sr.index') }}">
                    SR List
               </a>
               <a class="nav-link active" href="">
                    <i class="fa fa-edit"></i>
                    Edit SR
               </a>
          </nav>
     </div>
</header>
@endsection

@section('content')
<div class="col-12">
     <div class="card">
          <h4 class="card-title">Edit SR</h4>

          <form action="{{ route('sr.update', $sr->id) }}" method="POST">
               @csrf
               @method('PUT')

               <div class="card-body">
                    <div class="form-row">
                         <div class="form-group col-md-6">
                              <label for="">SR Name</label>
                              <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid': '' }}"
                                   name="name" value="{{ $sr->name }}">
                              @if($errors->has('name'))
                              <span class="invalid-feedback">{{ $errors->first('name') }}</span>
                              @endif
                         </div>
                         <div class="form-group col-md-6">
                              <label for="email">Email</label>
                              <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid': '' }}"
                                   name="email" value="{{ $sr->email }}">
                              @if($errors->has('email'))
                              <span class="invalid-feedback">{{ $errors->first('email') }}</span>
                              @endif
                         </div>
                         <div class="form-group col-md-6">
                              <label for="phone">Address</label>
                              <textarea name="address"
                                   class="form-control {{ $errors->has('address') ? 'is-invalid': '' }}">{{ $sr->address }}</textarea>
                              @if($errors->has('address'))
                              <span class="invalid-feedback">{{ $errors->first('address') }}</span>
                              @endif
                         </div>

                         <div class="form-group col-md-6">
                              <label for="mobile">Mobile</label>
                              <input type="text" class="form-control {{ $errors->has('mobile') ? 'is-invalid': '' }}"
                                   name="mobile" value="{{ $sr->mobile }}">
                              @if($errors->has('mobile'))
                              <span class="invalid-feedback">{{ $errors->first('mobile') }}</span>
                              @endif
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