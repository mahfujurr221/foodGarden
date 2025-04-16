@extends('layouts.master')
@section('title', 'User List')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Users</strong>
            </h1>
        </div>
        <div class="header-action">
            <nav class="nav">
                <a class="nav-link active" href="{{ route('users.index') }}">
                    Users
                </a>
                <a class="nav-link" href="{{ route('users.create') }}">
                    <i class="fa fa-plus"></i>
                    New User
                </a>
            </nav>
        </div>
    </header>
@endsection

@section('content')

    <div class="col-12">
        <div class="card">
            <h4 class="card-title"><strong>Users</strong></h4>
            <div class="card-body card-body-soft">
                @if ($users->count() > 0)
                    <div class="table-responsive-sm table-bordered">
                        <table class="table table-soft">
                            <thead>
                                <tr class="bg-primary">
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Brand</th>
                                    <th>Avater</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $key => $item)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>{{ ucfirst($item->fname) }} {{ ucfirst($item->lname) }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>
                                            @foreach ($item->getRoleNames() as $role)
                                                {{ ucfirst($role) }},
                                            @endforeach
                                        </td>
                                        {{-- <td>{{ $item->brand_id ? $item->brands->name : 'N/A' }}</td> --}}
                                        <td>
                                            @if($item->brand_id)
                                                @foreach(json_decode($item->brand_id) as $brandId)
                                                    {{ \App\Supplier::find($brandId)->name }},
                                                @endforeach
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <img src="{{ asset($item->profile->avatar) }}" alt="Avatar" width="60">
                                        </td>
                                        <td>
                                            <a href="{{ route('users.edit', $item->id) }}" data-provide="tooltip"
                                                data-tooltip-color="info" title="Role Edit" class="btn btn-info btn-xs">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ route('users.destroy', $item->id) }}" data-provide="tooltip"
                                                data-tooltip-color="danger" title="Role Delete"
                                                class="btn btn-danger btn-xs delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                        {{ $users->links() }}
                    </div>
                @else
                    <div class="alert alert-danger" role="alert">
                        <strong>You have no Users</strong>
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
