@extends('layouts.master')
@section('title', 'New Role')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Update Role</strong>
          </h1>
     </div>

     {{-- <div class="header-action">
          <nav class="nav">
               <a class="nav-link" href="{{ route('roles.index') }}">
                    Roles
               </a>
               <a class="nav-link active" href="{{ route('roles.create') }}">
                    <i class="fa fa-plus"></i>
                    New Role
               </a>
          </nav>
     </div> --}}
</header>
@endsection

@section('content')

<div class="col-md-9">
     <form class="card form-type-materia" method="POST" action="{{ route('roles.update',$role) }}">
          @csrf
          @method('PUT')
          <h4 class="card-title fw-400">Update Role </h4>

          <div class="card-body">
               <div class="row">
                    <div class="col-md-6">
                         <div class="form-group">
                              <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text"
                                   name="name" placeholder="Enter New Role Name" value="{{ $role->name }}">
                              <label>Role Name</label>
                              @if($errors->has('name'))
                              <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                              @endif
                         </div>
                    </div>
               </div>
          </div>

          <footer class="card-footer text-right">
               <button class="btn  btn-primary" type="submit">
                    <i class="fa fa-save"></i>
                    Update Role
               </button>
          </footer>
     </form>
</div>
@endsection

@section('styles')
<style>

</style>
@endsection

@section('scripts')
<script>

</script>
@endsection