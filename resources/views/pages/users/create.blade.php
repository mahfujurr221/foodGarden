@extends('layouts.master')
@section('title', 'New User')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>New User</strong>
            </h1>
        </div>

        <div class="header-action">
            <nav class="nav">
                <a class="nav-link" href="{{ route('users.index') }}">
                    Users
                </a>
                <a class="nav-link active" href="{{ route('users.create') }}">
                    <i class="fa fa-plus"></i>
                    New User
                </a>
            </nav>
        </div>
    </header>
@endsection

@section('content')

    <div class="col-md-12">
        <form class="card" method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
            @csrf
            <h4 class="card-title fw-400">Make User </h4>
            @csrf

            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Frist Name</label>
                            <input class="form-control {{ $errors->has('fname') ? 'is-invalid' : '' }}" type="text"
                                name="fname" placeholder="Enter First Name" value="{{ old('fname') }}">

                            @if ($errors->has('fname'))
                                <div class="invalid-feedback">{{ $errors->first('fname') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Last Name</label>
                            <input class="form-control {{ $errors->has('lname') ? 'is-invalid' : '' }}" type="text"
                                name="lname" placeholder="Enter Last Name" value="{{ old('lname') }}">

                            @if ($errors->has('lname'))
                                <div class="invalid-feedback">{{ $errors->first('lname') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email</label>
                            <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text"
                                name="email" placeholder="Enter Login Email." value="{{ old('email') }}">

                            @if ($errors->has('email'))
                                <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{-- select brand  --}}
                            <label>Brand</label>
                            <select name="brand_id[]" class="form-control {{ $errors->has('brand_id') ? 'is-invalid' : '' }} select2" multiple
                                required>
                                <option disabled>Select Brand</option>
                                @foreach (\App\Supplier::where('status', 1)->get() as $brand)
                                    <option {{ old('brand_id') == $brand->id ? 'selected' : '' }}
                                        value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" class="form-control {{ $errors->has('role') ? 'is-invalid' : '' }}"
                                required>
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                    <option {{ old('role') == $role->name ? 'selected' : '' }} value="{{ $role->name }}">
                                        {{ $role->name }}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('role'))
                                <div class="invalid-feedback">{{ $errors->first('role') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Password</label>
                            <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password"
                                name="password" placeholder="******">

                            @if ($errors->has('password'))
                                <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Password Confirmation</label>
                            <input class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                                type="password" name="password_confirmation" placeholder="******">

                            @if ($errors->has('password_confirmation'))
                                <div class="invalid-feedback">{{ $errors->first('password_confirmation') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>User Image</label>
                            <input class="form-control {{ $errors->has('avatar') ? 'is-invalid' : '' }}" type="file"
                                name="avatar">
                            <small>Image Size Must be 128x128</small>

                            @if ($errors->has('avatar'))
                                <div class="invalid-feedback">{{ $errors->first('avatar') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <footer class="card-footer text-right">
                <button class="btn  btn-primary" type="submit">
                    <i class="fa fa-save"></i>
                    Save User
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
    <script></script>
@endsection
