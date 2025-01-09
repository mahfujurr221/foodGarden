@extends('layouts.master')
@section('title', 'POS Settings')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Settings</strong>
            </h1>
        </div>

        <div class="header-action">
            <nav class="nav">
                {{-- <a class="nav-link active" href="{{ route('apps.setting') }}">
               App Settings
               </a> --}}
                <a class="nav-link active" href="{{ route('pos.pos_setting') }}">
                    <i class="fa fa-cogs"></i>
                    Settings
                </a>
            </nav>
        </div>
    </header>
@endsection

@section('content')
    <div class="col-lg-12 col-md-12">
        <form class="form-type-material" action="{{ route('pos.pos_setting_update') }}" method="POST"
            enctype="multipart/form-data">
            @csrf

        <div class="card">
            <h4 class="card-title fw-400 bg-secondary">Company Details</h4>

            <div class="card-body">
                <div class="flexbox gap-items-4">
                    <img class="avater img-thumbnail" width="200" src="{{ asset($pos_setting->logo) }}" alt="Large Logo">

                    <div class="flex-grow">
                        <h5>{{ $pos_setting->name }}</h5>
                        <div class="d-flex flex-column flex-sm-row gap-y gap-items-2 mt-16">
                            <div class="file-group file-group-inline">
                                <button class="btn btn-sm btn-w-lg btn-bold btn-secondary file-browser" type="button">Change
                                    Logo</button>
                                <input type="file" name="logo">
                                @if ($errors->has('logo'))
                                    <div class="invalid-feedback">{{ $errors->first('logo') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">


                            <input class="form-control {{ $errors->has('company') ? 'is-invalid' : '' }}" type="text"
                                name="company" value="{{ $pos_setting->company }}">
                            <label>Company</label>
                            @if ($errors->has('company'))
                                <div class="invalid-feedback">{{ $errors->first('company') }}</div>
                            @endif
                    </div>

                    <div class="col-md-6">
                        <div class="">
                            <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email"
                                name="email" value="{{ $pos_setting->email }}">
                            <label>Email Address</label>
                            @if ($errors->has('email'))
                                <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="">
                            <input class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" type="text"
                                value="{{ $pos_setting->phone }}" name="phone">
                            <label>Phone</label>
                            @if ($errors->has('phone'))
                                <div class="invalid-feedback">{{ $errors->first('phone') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class=" col-md-6">
                        <input class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}" type="text"
                            value="{{ $pos_setting->address }}" name="address">
                        <label>Address</label>
                        @if ($errors->has('address'))
                            <div class="invalid-feedback">{{ $errors->first('address') }}</div>
                        @endif
                    </div>
                </div>
                {{-- <div class="row">
                 <div class="col-md-6">
                   <div class="">
                     <label for="">Page Link:</label>
                     <input type="text" name="page_link" value="{{ $pos_setting->page_link }}" class="form-control">
                     @if ($errors->has('page_link'))
                       <div class="alert alert-danger">{{ $errors->first('page_link') }}</div>
                     @endif
                   </div>
                 </div>
                 <div class="col-md-6">
                   <div class="">
                     <label for="">Website</label>
                     <input type="text" name="website" value="{{ $pos_setting->website }}" class="form-control">
                     @if ($errors->has('website'))
                       <div class="alert alert-danger">{{ $errors->first('website') }}</div>
                     @endif
                   </div>
                 </div>
               </div> --}}



                {{-- <div class="row">
                    <div class="col-md-6">
                         <div class="">
                              <input type="text" class="form-control" name="header_text"
                                   value="{{ $pos_setting->header_text }}">
                              <label>Header Text</label>
                         </div>
                    </div>

                    <div class="col-md-6">
                         <div class="">
                              <input class="form-control" type="text" name="footer_text"
                                   value="{{ $pos_setting->footer_text }}">
                              <label>Footer Text</label>
                         </div>
                    </div>
               </div> --}}
            </div>
        </div>

            <div class="card">
                <div class="card-header bg-secondary">
                    <h3 class="card-title">Invoice Settings</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class=" col-md-4">
                            {{-- {{ dd($pos_setting->invoice_logo_type) }} --}}
                            <label for="">Invoice Logo Type</label><br>
                            <label><input type="radio" name="invoice_logo_type" value="Logo"
                                    {{ $pos_setting->invoice_logo_type == 'Logo' ? 'Checked' : '' }}> Logo</label>
                            <label><input type="radio" name="invoice_logo_type" value="Name" style="margin-left:20px;"
                                    {{ $pos_setting->invoice_logo_type == 'Name' ? 'Checked' : '' }}> Name</label>
                            <label><input type="radio" name="invoice_logo_type" value="Both" style="margin-left:20px;"
                                    {{ $pos_setting->invoice_logo_type == 'Both' ? 'Checked' : '' }}> Both</label>
                        </div>

                        <div class=" col-md-4">
                            <label for="">Invoice Design</label>
                            <select name="invoice_type" id="" class="form-control">
                                <option value="a4" {{ $pos_setting->invoice_type == 'a4' ? 'SELECTED' : '' }}>A4</option>
                                <option value="a4-2" {{ $pos_setting->invoice_type == 'a4-2' ? 'SELECTED' : '' }}>A4 - 2</option>
                                <option value="a4-3" {{ $pos_setting->invoice_type == 'a4-3' ? 'SELECTED' : '' }}>A4 - 3</option>
                                <option value="pos" {{ $pos_setting->invoice_type == 'pos' ? 'SELECTED' : '' }}>Pos Printer</option>
                                <option value="pos-2" {{ $pos_setting->invoice_type == 'pos-2' ? 'SELECTED' : '' }}>Pos Printer - 2</option>
                                <option value="pos-3" {{ $pos_setting->invoice_type == 'pos-3' ? 'SELECTED' : '' }}>Pos Printer - 3</option>
                            </select>

                        </div>
                    </div>
                </div>
            </div>


            <div class="card">
                <div class="card-header bg-secondary">
                    <h3 class="card-title">Barcode Settings</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class=" col-md-4">
                            {{-- {{ dd($pos_setting->invoice_logo_type) }} --}}
                            <label for="">Barcode Type</label><br>
                            <label><input type="radio" name="barcode_type" value="single"
                                    {{ $pos_setting->barcode_type == 'single' ? 'Checked' : '' }}> Single</label>
                            <label><input type="radio" name="barcode_type" value="a4" style="margin-left:20px;"
                                    {{ $pos_setting->barcode_type == 'a4' ? 'Checked' : '' }}> A4</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-secondary">
                    <h3 class="card-title">Other Settings</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="switch">
                                <input type="checkbox" name="dark_mode"
                                    {{ $pos_setting->dark_mode == 1 ? 'checked' : '' }}>
                                <span class="switch-indicator"></span>
                                <span class="switch-description">Dark Mode</span>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <div class="">
                                <label for="">Low Stock Quantity</label>
                                <input type="text" name="low_stock" value="{{ $pos_setting->low_stock }}"
                                    class="form-control">
                                @if ($errors->has('low_stock'))
                                    <div class="alert alert-danger">{{ $errors->first('low_stock') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="card card-footer">
                <div class="row">
                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Save Changes</button>
                    </div>
                </div>
            </footer>
        </form>
    </div>
@endsection

@section('styles')
    <style>

    </style>
@endsection
