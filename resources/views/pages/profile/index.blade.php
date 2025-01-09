@extends('layouts.master')
@section('title', 'User Profile')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>User Profile</strong>
            </h1>
        </div>
    </header>
@endsection

@section('content')
    <div class="col-md-3">
        <div class="card">
            <ul class="nav nav-lg nav-pills flex-column">
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('profile.index') }}">User Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('change.password') }}">Change Passwrod</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-md-9">
        <form class="card form-type-material" method="POST" action="{{ route('profile.update') }}"
            enctype="multipart/form-data">
            <h4 class="card-title fw-400">User Details</h4>
            @csrf
            <div class="card-body">
                <div class="flexbox gap-items-4">
                    <img class="avatar avatar-xl" src="{{ asset($user->profile->avatar) }}" alt="avatar">

                    <div class="flex-grow">
                        <h5>{{ $user->fname }} {{ $user->lname }}</h5>
                        <div class="d-felx flex-column flex-sm-row gap-y gap-items-2 mt-16">
                            <div class="file-group file-group-inline">
                                <button class="btn btn-sm btn-w-lg btn-outline btn-round btn-secondary file-browser"
                                    type="button">Change Picture</button>
                                <input type="file" name="avatar">
                            </div>
                            <small>Image Size Must be 128x128</small>
                        </div>

                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input class="form-control {{ $errors->has('fname') ? 'is-invalid' : '' }}" type="text"
                                name="fname" value="{{ $user->fname }}">
                            <label>First name</label>
                            @if ($errors->has('fname'))
                                <div class="invalid-feedback">{{ $errors->first('fname') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <input class="form-control {{ $errors->has('lname') ? 'is-invalid' : '' }}" type="text"
                                value="{{ $user->lname }}" name="lname">
                            <label>Last name</label>
                            @if ($errors->has('lname'))
                                <div class="invalid-feedback">{{ $errors->first('lname') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <input class="form-control" type="text" name="email" value="{{ $user->email }}">
                    <label>Email</label>
                </div>
            </div>

            <footer class="card-footer text-right">
                <button class="btn btn-primary" type="submit">Save Changes</button>
            </footer>
        </form>
    </div>
@endsection

@section('styles')

@endsection

@section('scripts')

@endsection
