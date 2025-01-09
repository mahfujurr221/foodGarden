@extends('layouts.app')
@section('title', 'Reset Your Passwrod')

@section('content')
    <div class="col-12">
        <div class="card card-shadowed px-50 py-30 w-400px mx-auto" style="max-width: 100%">
          <h5 class="text-uppercase">{{ __('Recover password')}}</h5>
          <br>
            @if(session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
          <form class="form-type-material" action="{{ route('password.email') }}" method="POST">
          @csrf
            <div class="form-group">
              <input type="text" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" id="email" name="email">
              <label for="email">{{ __('Email address') }}</label>
              @error('email')
                <span class="invalid-feedback"> <strong>{{ $message }}</strong> </span>
              @enderror
            </div>

            <br>
            <button class="btn btn-bold btn-block btn-primary" type="submit">{{ __('Reset') }}</button>
          </form>
        </div>
      </div>
@endsection