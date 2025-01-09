@extends('layouts.master')
@section('title', 'Roles & Permissions')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Roles & Permissions</strong>
          </h1>
     </div>

     {{-- <div class="header-action">
          <nav class="nav">
               <a class="nav-link active" href="{{ route('roles.index') }}">
                    Roles
               </a>
               <a class="nav-link" href="{{ route('roles.create') }}">
                    <i class="fa fa-plus"></i>
                    New Role
               </a>
          </nav>
     </div> --}}
</header>
@endsection

@section('content')

<div class="col-12">

     <div class="card">
          <div class="card-header">
               <h4>Add Role</h4>
          </div>
          <div class="card-body">
               <form action="{{ route('roles.store') }}" method="POST" class="form-row">
                    @csrf
                    <div class="form-group col-md-8">
                      <label for="">Role Name</label>
                      <input type="text" name="name" value="{{ old("name") }}" class="form-control">
                      @if($errors->has('name'))
                        <div class="alert alert-danger">{{ $errors->first('name') }}</div>
                      @endif
                    </div>

                    <div class="form-group col-md-4">
                         <label for="" style="display: block;visibility: hidden">X</label>
                         <input type="submit" class="btn btn-success" value="Add Role">
                    </div>
               </form>
          </div>
     </div>

     <div class="card">
          <h4 class="card-title"><strong>Roles</strong></h4>

          <div class="card-body card-body-soft">
               @if($roles->count() > 0)
               <div class="table-responsive-sm table-bordered">
                    <table class="table table-soft">
                         <thead>
                              <tr class="bg-primary">
                                   <th>#</th>
                                   <th>Role</th>
                                   <th>Users</th>
                                   <th>#</th>
                              </tr>
                         </thead>

                         <tbody>
                              @foreach ($roles as $key => $item)
                              <tr>
                                   <td>{{ ++$key }}</td>
                                   <td>{{ ucfirst($item->name) }}</td>
                                   <td>{{ $item->users->count() }}</td>
                                   <td style="width:15%;">
                                        <a href="{{ route('role_permissions.edit',$item->id) }}" data-provide="tooltip" data-tooltip-color="brown"
                                             title="Role Permission List" class="btn btn-primary btn-xs">
                                             <i class="fa fa-list"></i>
                                        </a>
                                        <a href="{{ route('roles.edit',$item->id) }}" data-provide="tooltip" data-tooltip-color="info" title="Role Edit"
                                             class="btn btn-info btn-xs">
                                             <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="{{ route('roles.destroy',$item->id) }}" data-provide="tooltip" data-tooltip-color="danger"
                                             title="Role Delete" class="btn btn-danger btn-xs delete">
                                             <i class="fa fa-trash"></i>
                                        </a>
                                   </td>
                              </tr>
                              @endforeach
                         </tbody>

                    </table>
                    {{ $roles->links() }}
               </div>
               @else
               <div class="alert alert-danger" role="alert">
                    <strong>You have no roles</strong>
               </div>
               @endif
          </div>
     </div>
</div>
@endsection

@section('styles')
<style>
     .table tr td {
          vertical-align: middle;
          padding: 5px;
          text-align: center;
          font-weight: 500;
     }

     .table tr th {
          text-align: center;
     }
</style>
@endsection

@section('scripts')
@include('includes.delete-alert')
@endsection
