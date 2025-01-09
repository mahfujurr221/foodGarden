@extends('layouts.master')
@section('title', 'Permissions')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Permissions</strong>
          </h1>
     </div>

     <div class="header-action">
          <nav class="nav">
               <a class="nav-link" href="{{ route('roles.index') }}">
                    Roles
               </a>
               <a class="nav-link active" href="{{ request()->url() }}">
                    <i class="fa fa-plus"></i>
                    Permissions
               </a>
          </nav>
     </div>
</header>
@endsection

@section('content')
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3>Update - <span style="text-transform: ">{{ $role->name }}</span> - Permissions</h3>
            {{-- <button id="select-all">Select All</button> --}}
            <span class=""><label><input type="checkbox" id="select-all" class="mr-2 ml-2">Select All</label></span>
            {{-- <a href="{{ route('reset_permissions') }}" class="btn btn-info">Reset Permissions</a> --}}
        </div>
        <div class="card-body all_features">


            <form action="{{ route('role_permissions.update', $role) }}" method="POST" class="form-row">
                @method('PUT')
                @csrf

                @foreach ($permission_groups as $key => $permissions)
                    <div class="card col-md-6 feature_group">
                        <div class="card-header">
                            <h4 style="text-transform: capitalize" style="display: inline;">{{ str_replace('_', ' ', $key) }}</h4>
                            <span class=""><label><input type="checkbox" class="mr-2 ml-2 select_group">Select Section</label></span>
                        </div>
                        <div class="card-body">
                            @foreach ($permissions as $permission)
                                <div class="">
                                    <input type="checkbox" value="{{ $permission->name }}" name="permissions[]"
                                        @if ($role->hasPermissionTo($permission->name)) CHECKED @endif class="feature"> <span
                                        style="text-transform: capitalize">{{ str_replace('-',' - ',str_replace('_', ' ', $permission->name)) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                <div class="form-group col-12">
                    <input type="submit" class="btn btn-success" value="Update Permissions">
                </div>
                {{-- <a href="{{ request()->url() }}" class="btn btn-info pull-right">Reset</a> --}}
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $('#select-all').click(function(event) {
            // alert('hello');
            $('.all_features').find("input[type='checkbox']").prop('checked', this.checked);
            // if (this.checked) {
            //     // alert('checked');
            //     // Iterate each checkbox
            //     $('.feature:checkbox').each(function() {
            //         this.checked = true;
            //     });
            // } else {
            //     $('.feature:checkbox').each(function() {
            //         this.checked = false;
            //     });
            // }
        });

        $(".select_group").click(function(event){
            var parent = $(this).closest('.feature_group');
            parent.find("input[type='checkbox']").prop('checked', this.checked);
        });
    </script>
@endsection

@section('styles')
@endsection
