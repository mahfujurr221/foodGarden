@extends('layouts.master')
@section('title', 'Change Password ')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Change Password </strong>
          </h1>
     </div>
</header>
@endsection

@section('content')
<div class="col-md-3">
     <div class="card">
          <ul class="nav nav-lg nav-pills flex-column">
               <li class="nav-item">
                    <a class="nav-link" href="{{ route('profile.index') }}">User Profile</a>
               </li>
               <li class="nav-item active">
                    <a class="nav-link" href="{{ route('change.password') }}">Change
                         Passwrod</a>
               </li>
          </ul>
     </div>
</div>
<div class="col-md-9">
     <form class="card form-type-materia" method="POST" action="{{ route('update.password') }}">
          @csrf
          <h4 class="card-title fw-400">Change Password </h4>
          @csrf

          <div class="card-body">
               <div class="form-group">
                    <input class="form-control {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                         type="password" name="current_password" placeholder="Enter Current Password. ">
                    <label>Current Password</label>
                    @if($errors->has('current_password'))
                    <div class="invalid-feedback">{{ $errors->first('current_password') }}</div>
                    @endif
               </div>

               <div class="row">
                    <div class="col-md-6">
                         <div class="form-group">
                              <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                   type="password" name="password" placeholder="Enter new password.">
                              <label>New Password</label>
                              @if($errors->has('password'))
                              <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                              @endif
                         </div>
                    </div>

                    <div class="col-md-6">
                         <div class="form-group">
                              <input
                                   class="form-control  {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                                   type="password" name="password_confirmation" placeholder="Confirm Password.">
                              <label>Confirm Password </label>
                              @if($errors->has('password_confirmation'))
                              <div class="invalid-feedback">{{ $errors->first('password_confirmation') }}</div>
                              @endif
                         </div>
                    </div>
               </div>

          </div>

          <footer class="card-footer text-right">
               <button class="btn  btn-primary" type="submit">Save Changes</button>
          </footer>
     </form>
</div>
@endsection

@section('styles')

@endsection

@section('scripts')

@endsection