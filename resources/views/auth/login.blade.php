@extends('layouts.app')
@section('title', 'Login')

@section('content')

<div class="col-12">
  <div class="card card-shadowed px-50 py-30 w-400px mx-auto" style="max-width: 100%">
    <h5 class="text-uppercase">{{ __('Sign in') }}</h5>
    <br>

    <form class="form-type-material" action="{{ route('login') }}" method="POST">
      @csrf
      <div class="form-group">
        <input type="text" class="form-control @error('email') is-invalid @enderror" id="username" name="email"
          value="{{ config('pos.app_mode') == 'demo'?"admin@softghor.com":"" }}" >
        <label for="username">{{ __('User Email...') }}</label>
        @error('name')
        <span class="invalid-feedback" role="alert"> <strong>{{ $message }}</strong> </span>
        @enderror
      </div>

      <div class="form-group">
        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
          name="password" value="{{ config('pos.app_mode') == 'demo'?"admin":"" }}">
        <label for="password">{{ __('Password') }}</label>
        @error('password')
        <span class="invalid-feedback"> <strong>{{ $message }}</strong> </span>
        @enderror
      </div>

      <div class="form-group flexbox flex-column flex-md-row">
        <label class="custom-control custom-checkbox">
          <input type="checkbox" class="custom-control-input" name="remember" {{ old('remember') ? 'checked' : '' }}>
          <span class="custom-control-indicator"></span>
          <span class="custom-control-description">Remember me</span>
        </label>

        {{-- <a class="text-muted hover-primary fs-13 mt-2 mt-md-0"
          href="{{ route('password.request') }}">{{ __('Forgot password ?') }}</a> --}}
      </div>

      <div class="form-group">
        <button class="btn btn-bold btn-block btn-primary" type="submit">{{ __('Login') }}</button>
      </div>
    </form>


  </div>
    {{-- <div class="card card-shadowed px-50 py-30 w-400px mx-auto" style="margin-top:20px;">
        <h4>নতুন ওর্ডারের জন্যঃ</h4>
        <span style="display: block;font-size:1.5em;"><i class="fa fa-phone text-primary"></i><span style="margin-left:10px;">01958-104255</span></span>
        <span style="display: block;font-size:1.5em;"><i class="fa fa-phone text-primary"></i><span style="margin-left:10px;">01958-104250</span></span>

        <h4 style="margin-top:20px;">সাপোর্টের জন্যঃ</h4>
        <span style="font-size:1.5em;"><i class="fa fa-phone text-primary"></i><span style="margin-left:10px;">01958-104256 (10 AM - 06 PM)</span></span>
        <span style="font-size:1.5em;"><i class="fa fa-phone text-primary"></i><span style="margin-left:10px;">01958-104257 (10 AM - 06 PM)</span></span>

    </div> --}}

  {{-- <p class="text-center text-muted fs-13 mt-20">Don't have an account? <a class="text-primary fw-500" href="#m">Sign up</a></p> --}}
</div>
@endsection
