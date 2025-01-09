@extends('layouts.master')
@section('title', 'App Settings')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>App Settings</strong>
          </h1>
     </div>

     <div class="header-action">
          <nav class="nav">
               {{-- <a class="nav-link active" href="{{ route('apps.setting') }}">
               App Settings
               </a> --}}
               <a class="nav-link" href="{{ route('pos.pos_setting') }}">
                    <i class="fa fa-cogs"></i>
                    POS Settings
               </a>
          </nav>
     </div>
</header>
@endsection

@section('content')
<div class="col-lg-12 col-md-12">
     <form class="card form-type-material" action="{{ route('apps.setting_update') }}" method="POST"
          enctype="multipart/form-data">
          @csrf
          <h4 class="card-title fw-400">Company Details</h4>

          <div class="card-body">
               <div class="flexbox gap-items-4">
                    <img class="avater img-thumbnail" width="200" src="{{ asset($setting->logo) }}" alt="Large Logo">

                    <div class="flex-grow">
                         <h5>{{ $setting->name }}</h5>
                         <div class="d-flex flex-column flex-sm-row gap-y gap-items-2 mt-16">
                              <div class="file-group file-group-inline">
                                   <button class="btn btn-sm btn-w-lg btn-bold btn-secondary file-browser"
                                        type="button">Change Logo</button>
                                   <input type="file" name="logo">
                                   @if($errors->has('logo'))
                                   <div class="invalid-feedback">{{ $errors->first('logo') }}</div>
                                   @endif
                              </div>
                         </div>
                    </div>

                    <img class="avatar avatar-xl" src="{{ asset($setting->min_logo) }}" alt="Mini Logo">

                    <div class="flex-grow">
                         <h5>Mini Logo</h5>
                         <div class="d-flex flex-column flex-sm-row gap-y gap-items-2 mt-16">
                              <div class="file-group file-group-inline">
                                   <button class="btn btn-sm btn-w-lg btn-bold btn-secondary file-browser"
                                        type="button">Change Mini Logo</button>
                                   <input type="file" name="mini_logo">
                                   @if($errors->has('mini_logo'))
                                   <div class="invalid-feedback">{{ $errors->first('mini_logo') }}</div>
                                   @endif
                              </div>
                         </div>
                    </div>

               </div>

               <hr>

               <div class="row">
                    <div class="col-md-6">
                         <div class="form-group">
                              <input class="form-control {{ $errors->has('name') ? 'is-invalid' : 'is-valid' }}"
                                   type="text" name="name" value="{{ $setting->name }}">
                              <label>Name</label>
                              @if($errors->has('name'))
                              <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                              @endif
                         </div>
                    </div>

                    <div class="col-md-6">
                         <div class="form-group">
                              <input class="form-control {{ $errors->has('email') ? 'is-invalid' : 'is-valid' }}"
                                   type="email" name="email" value="{{ $setting->email }}">
                              <label>Email Address</label>
                              @if($errors->has('email'))
                              <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                              @endif
                         </div>
                    </div>

                    <div class="col-md-6">
                         <div class="form-group">
                              <input class="form-control {{ $errors->has('link') ? 'is-invalid' : 'is-valid' }}"
                                   type="text" name="link" value="{{ $setting->link }}">
                              <label>Website</label>
                              @if($errors->has('link'))
                              <div class="invalid-feedback">{{ $errors->first('link') }}</div>
                              @endif
                         </div>
                    </div>

                    <div class="col-md-6">
                         <div class="form-group">
                              <input class="form-control {{ $errors->has('phone') ? 'is-invalid' : 'is-valid' }}"
                                   type="text" value="{{ $setting->phone }}" name="phone">
                              <label>Phone</label>
                              @if($errors->has('phone'))
                              <div class="invalid-feedback">{{ $errors->first('phone') }}</div>
                              @endif
                         </div>
                    </div>
               </div>

               <div class="row">
                    <div class="col-md-6">
                         <div class="form-group">
                              <input type="text"
                                   class="form-control {{ $errors->has('country') ? 'is-invalid' : 'is-valid' }}"
                                   name="country" value="{{ $setting->country }}">
                              <label>Country</label>
                              @if($errors->has('country'))
                              <div class="invalid-feedback">{{ $errors->first('country') }}</div>
                              @endif
                         </div>
                    </div>

                    <div class="col-md-6">
                         <div class="form-group">
                              <input class="form-control {{ $errors->has('city') ? 'is-invalid' : 'is-valid' }}"
                                   type="text" name="city" value="{{ $setting->city }}">
                              <label>City</label>
                              @if($errors->has('city'))
                              <div class="invalid-feedback">{{ $errors->first('city') }}</div>
                              @endif
                         </div>
                    </div>
               </div>

               <div class="form-group">
                    <input class="form-control {{ $errors->has('address') ? 'is-invalid' : 'is-valid' }}" type="text"
                         value="{{ $setting->address }}" name="address">
                    <label>Address</label>
                    @if($errors->has('address'))
                    <div class="invalid-feedback">{{ $errors->first('address') }}</div>
                    @endif
               </div>

               <div class="row">
                    <div class="col-md-6">
                         <div class="form-group">
                              <input type="text" class="form-control" name="header_text"
                                   value="{{ $setting->header_text }}">
                              <label>Header Text</label>
                         </div>
                    </div>

                    <div class="col-md-6">
                         <div class="form-group">
                              <input class="form-control" type="text" name="footer_text"
                                   value="{{ $setting->footer_text }}">
                              <label>Footer Text</label>
                         </div>
                    </div>
               </div>

               <div class="row">
                    <div class="col-md-6">
                         <label class="switch switch-default">
                              <input type="checkbox" name="sale_over_sotck"
                                   {{ $setting->sale_over_sotck ? 'checked' : '' }}>
                              <span class="switch-indicator"></span>
                              <span class="switch-description">Sale Over Sock</span>
                         </label>
                    </div>
               </div>

          </div>

          <footer class="card-footer text-right">
               <button class="btn btn-primary" type="submit">Save Changes</button>
          </footer>
     </form>
</div>
@endsection

@section('styles')
<style>

</style>
@endsection