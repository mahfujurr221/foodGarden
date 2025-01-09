@extends('layouts.master')
@section('title', 'Create Customer')

@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Update Customer</strong>
        </h1>
    </div>
    <div class="header-action">
        <nav class="nav">
            <a class="nav-link" href="{{ route('customer.index') }}">
                Customers
            </a>
            <a class="nav-link active" href="{{ route('customer.create') }}">
                <i class="fa fa-plus"></i>
                New Customer
            </a>
        </nav>
    </div>
</header>
@endsection

@section('content')
<div class="col-md-3"></div>
<div class="col-6">
    <div class="card">
        <h4 class="card-title">Update Customer</h4>
        <form action="{{ route('customer.update', $customer->id) }}" method="POST">
            @method('PUT')
            @csrf
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="">Customer Name(বাংলা)<span class="field_required"></span></label>
                        <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            name="name" value="{{ $customer->name }}">
                        @if ($errors->has('name'))
                        <span class="invalid-feedback">{{ $errors->first('name') }}</span>
                        @endif
                    </div>
                    <div class="form-group col-md-12">
                        <label for="shop_name_bangla">Shop Name(বাংলা)<span class="field_required"></span></label>
                        <input type="text"
                            class="form-control {{ $errors->has('shop_name_bangla') ? 'is-invalid' : '' }}"
                            name="shop_name_bangla" value="{{ $customer->shop_name_bangla }}">
                        @if ($errors->has('shop_name_bangla'))
                        <span class="invalid-feedback">{{ $errors->first('shop_name_bangla') }}</span>
                        @endif
                    </div>

                    <div class="form-group col-md-12">
                        <label for="shop_name">Shop Name(English)<span class="field_required"></span></label>
                        <input type="text" class="form-control {{ $errors->has('shop_name') ? 'is-invalid' : '' }}"
                            name="shop_name" value="{{ $customer->shop_name }}">
                        @if ($errors->has('shop_name'))
                        <span class="invalid-feedback">{{ $errors->first('shop_name') }}</span>
                        @endif
                    </div>

                    <div class="col-8">
                        <div class="form-group">
                            <label for="address">Address <span class="field_required"></span></label>
                            <select name="address_id" id="address" class="form-control select2">
                                @foreach ($addresses as $address)
                                <option value="{{ $address->id }}" @if ($address->id == $customer->address_id) selected
                                    @endif>{{ $address->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('address_id'))
                            <span class="invalid-feedback">{{ $errors->first('address_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-4">
                        <label for="" style="display: block; visibility:hidden;">Add Brand</label>
                        <a href="{{ route('customer.add_address') }}" class="edit btn btn-outline btn-primary"
                            data-toggle="modal" data-target="#edit" id="Add Address" style="">Add Address </a>
                    </div>

                    <div class="col-8">
                        <div class="form-group">
                            <label for="">Business Category<span class="field_required"></span></label>
                            <select name="business_cat_id" id="business_category" class="form-control">
                                @foreach ($business_categories as $business_category)
                                <option value="{{ $business_category->id }}" @if ($business_category->id ==
                                    $customer->business_cat_id) selected @endif>{{ $business_category->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('business_cat_id'))
                            <span class="invalid-feedback">{{ $errors->first('business_cat_id') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-4">
                        <label for="" style="display: block; visibility:hidden;">Add Brand</label>
                        <a href="{{ route('customer.add_business_category') }}" class="edit btn btn-outline btn-primary"
                            data-toggle="modal" data-target="#edit" id="Add Business Category" style="">Add
                            Business Category</a>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="phone">Phone<span class="field_required"></span></label>
                        <input type="text" class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                            name="phone" value="{{ $customer->phone }}">
                        @if ($errors->has('phone'))
                        <span class="invalid-feedback">{{ $errors->first('phone') }}</span>
                        @endif
                    </div>

                    {{-- <div class="form-group col-md-6">
                        <label for="sr_id">SR<span class="field_required"></span></label>
                        <select name="sr_id" id="" class="form-control">
                            <option value="">Select SR</option>
                            @foreach ($srs as $sr)
                            <option value="{{ $sr->id }}">{{ $sr->name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('sr_id'))
                        <span class="invalid-feedback">{{ $errors->first('sr_id') }}</span>
                        @endif
                        `
                    </div> --}}

                    {{-- note --}}
                    <div class="col-md-12 my-2">
                        <div class="form-group">
                            <label for="note">Note</label>
                            <textarea name="note" class="form-control">{{ $customer->note }}</textarea>
                        </div>
                    </div>

                    {{-- <div class="form-group col-md-6">
                        <label for="">Opening Receivable</label>
                        <input type="text" name="opening_receivable" value="{{ old('opening_receivable') }}"
                            class="form-control">
                        @if ($errors->has('opening_receivable'))
                        <div class="alert alert-danger">{{ $errors->first('opening_receivable') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">Opening Payable</label>
                        <input type="text" name="opening_payable" value="{{ old('opening_payable') }}"
                            class="form-control">
                        @if ($errors->has('opening_payable'))
                        <div class="alert alert-danger">{{ $errors->first('opening_payable') }}</div>
                        @endif
                    </div> --}}

                </div> <!-- End form-row -->
                <div class="form-row justify-content-center mt-3">
                    <button class="btn btn-primary">
                        <i class="fa fa-save mr-2"></i>
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
php
@section('styles')
@endsection
@section('scripts')
<script src="{{ asset('js/modal_form_no_reload.js') }}"></script>
<script>
    $("#address").select2({
            ajax: {
                url: "{{ route('customer.address') }}",
                dataType: 'json',
                delay: 100,
                data: function(params) {
                    return {
                        query: params.term,
                    };
                },
                processResults: function(data, params) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Address',

            // minimumInputLength: 1,
            //   templateResult: formatRepo,
            //   templateSelection: formatRepoSelection
        });

        $("#business_category").select2({
            ajax: {
                url: "{{ route('customer.business_category') }}",
                dataType: 'json',
                delay: 100,
                data: function(params) {
                    return {

                        query: params.term,
                    };
                },
                processResults: function(data, params) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Business Category',
            // minimumInputLength: 1,
            //   templateResult: formatRepo,
            //   templateSelection: formatRepoSelection
        });
</script>
@include('includes.placeholder_model')
@endsection