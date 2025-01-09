@extends('layouts.master')
@section('title', 'Create Product')

@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>New Product</strong>
        </h1>
    </div>
    <div class="header-action">
        <nav class="nav">
            <a class="nav-link" href="{{ route('product.index') }}">
                Products
            </a>
            {{-- <a class="nav-link" href="#">Import Product</a> --}}
            <a class="nav-link active" href="{{ route('product.create') }}">
                <i class="fa fa-plus"></i>
                Add Product
            </a>
        </nav>
    </div>
</header>
@endsection
@section('content')
<div class="col-12">
    <div class="card">
        <div class="row">
            <div class="col-12" style="">
                <h4 class="card-title" style="display: inline-block;">New Product</h4>
            </div>
        </div>
        <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="form-row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">

                        <div class="row mb-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Product Name<span class="field_required"></span></label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name"
                                        value="{{ old('name') }}" placeholder="Enter Product Name..."
                                        autocomplete="off">
                                    @if ($errors->has('name'))
                                    <span class="invalid-feedback">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Product Code</label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" name="code"
                                        value="{{ old('code') }}" placeholder="Enter Product Code...">
                                    @if ($errors->has('code'))
                                    <span class="invalid-feedback">{{ $errors->first('code') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Category<span class="field_required"></span></label>
                                    <select name="category_id" id="category" class="form-control">
                                    </select>
                                    @if ($errors->has('category_id'))
                                    <span class="invalid-feedback">{{ $errors->first('category_id') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-2">
                                <label for="" style="display: block; visibility:hidden;">Add Category</label>
                                <a href="{{ route('product.add_category') }}" class="edit btn btn-outline btn-primary"
                                    data-toggle="modal" data-target="#edit" id="Add Category">Add Category </a>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Brand</label>
                                    <select name="brand_id" id="brand" class="form-control">

                                    </select>
                                    @if ($errors->has('brand_id'))
                                    <span class="invalid-feedback">{{ $errors->first('brand_id') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-2">
                                <label for="" style="display: block; visibility:hidden;">Add Brand</label>
                                <a href="{{ route('product.add_brand') }}" class="edit btn btn-outline btn-primary"
                                    data-toggle="modal" data-target="#edit" id="Add Brand" style="">Add Brand </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Main Unit</label>
                                    <select name="main_unit_id" id="" class="form-control main_unit">
                                        @foreach ($units as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('main_unit_id'))
                                    <div class="alert alert-danger">{{ $errors->first('main_unit_id') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Sub Unit</label>
                                    <select name="sub_unit_id" id="" class="form-control sub_unit">
                                        @if ($first_unit->related_unit)
                                        <option value="{{ $first_unit->related_unit->id }}">
                                            {{ $first_unit->related_unit->name }}</option>
                                        @else
                                        <option value="">No Related Unit Found</option>
                                        @endif
                                    </select>
                                    @if ($errors->has('sub_unit_id'))
                                    <div class="alert alert-danger">{{ $errors->first('sub_unit_id') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Opening Stock</label>
                                    <div class="opening_stocks form-row" style="padding-left: 5px; padding-right:5px;">
                                        <input type="text" name="opening_stock[]" value="" class="form-control col"
                                            placeholder="{{ $first_unit->name }}">
                                    </div>
                                    @if ($errors->has('opening_stock'))
                                    <div class="alert alert-danger">{{ $errors->first('opening_stock') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Sale Price<span class="field_required"></span></label>
                                    <input type="text" name="price"
                                        class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" name="name"
                                        value="{{ old('price') }}" placeholder="Enter Product price...">
                                    @if ($errors->has('price'))
                                    <span class="invalid-feedback">{{ $errors->first('price') }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cost">Purchase Cost<span class="field_required"></span></label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('cost') ? 'is-invalid' : '' }}" name="cost"
                                        value="{{ old('cost') }}" placeholder="Enter Product cost...">
                                    @if ($errors->has('cost'))
                                    <span class="invalid-feedback">{{ $errors->first('cost') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="damage_price">Damage Price</label>
                                    <input type="text" class="form-control" name="damage_price"
                                        value="{{ old('damage_price')?? '0' }}"
                                        Placeholder="Enter Product Damage Price...">
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="details">Product Details</label>
                            <textarea name="details" data-provide="summernote" data-min-height="100"
                                placeholder="Write Product Details">{{ old('details') }}</textarea>
                            @if ($errors->has('details'))
                            <span class="invalid-feedback">{{ $errors->first('details') }}</span>
                            @endif
                        </div>

                        <div class="form-group form-type-line file-group">
                            <label for="logo">Product Image</label>
                            <input type="text" class="form-control file-value file-browser" placeholder="Choose file..."
                                readonly="">
                            <input type="file" name="use_file">
                            <small>Size: 298x284 pixels</small>
                            @if ($errors->has('use_file'))
                            <span class="invalid-feedback">{{ $errors->first('use_file') }}</span>
                            @endif
                        </div>

                    </div>
                    <div class="form-group col-md-6">
                        {{-- <label for="description">Product Description</label> --}}
                    </div>
                </div>
                <hr>
                <div class="form-row justify-content-center">
                    <div class="form-group ">
                        <button type="submit" class="btn btn-info">
                            <i class="fa fa-save"></i>
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
{{--
<link rel="stylesheet" href="{{ asset('back/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}"> --}}
<style>
    .form-control {
        border-color: #b5b1b1;
    }

    label {
        font-size: 13px;
        font-weight: 600;
    }
</style>
@endsection

@section('scripts')
<script src="{{ asset('js/modal_form_no_reload.js') }}"></script>
<script>
    @php
            // $categories=[];
            // foreach(\App\Category::all() as $category){
            //   $categories[]=['id'=>$category->id,'name'=>$category->name];
            // }
            // $categories = json_encode($categories);
        @endphp
        $(document).ready(function() {
            $("#category").select2({
                ajax: {
                    url: "{{ route('product.categories') }}",
                    dataType: 'json',
                    delay: 250,
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
                placeholder: 'Search Categories',
            });

            $("#brand").select2({
                ajax: {
                    url: "{{ route('product.brands') }}",
                    dataType: 'json',
                    delay: 250,
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
                placeholder: 'Search Brands',
                // minimumInputLength: 1,
                //   templateResult: formatRepo,
                //   templateSelection: formatRepoSelection
            });
        });
</script>

<script>
    $('.main_unit').change(function() {
            $('.sub_unit').html('<option value="">No Related Unit Found</option>');
            var main_unit_id = $(this).find(':selected').val();
            var main_unit_text = $(this).find(':selected').text();

            let url = "{{ route('unit.get_related', 'my_id') }}".replace('my_id', main_unit_id);;

            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    if (data) {
                        var sub_value = '<option value="">Select Unit</option><option value="' + data
                            .id + '">' + data.name + '</option>';
                        $('.sub_unit').html(sub_value);
                        // Opening Stock
                        var opening_stock = "";
                        opening_stock +=
                            `<input type="text" name="opening_stock[]" value="" class="form-control col" placeholder="${main_unit_text}">`;
                        // opening_stock+=`<input type="text" name="opening_stock[]" value="" class="form-control col" placeholder="${data.name}">`;
                        $('.opening_stocks').html(opening_stock);
                    } else {
                        $('.sub_unit').html('<option value="">No Related Unit Found</option>');
                        // opening Stock

                        var opening_stock =
                            `<input type="text" name="opening_stock[]" value="" class="form-control col" placeholder="${main_unit_text}">`;
                        $('.opening_stocks').html(opening_stock);
                    }
                }
            });
        });

        $('.sub_unit').change(function() {
            var sub_unit_id = $(this).find(':selected').val();
            var sub_unit_text = $(this).find(':selected').text();

            var main_unit_id = $('.main_unit').find(':selected').val();
            var main_unit_text = $('.main_unit').find(':selected').text();
            var opening_stock = '';
            if (sub_unit_id == "") {
                opening_stock =
                    `<input type="text" name="opening_stock[]" value="" class="form-control col" placeholder="${main_unit_text}">`;
            } else {
                opening_stock +=
                    `<input type="text" name="opening_stock[]" value="" class="form-control col" placeholder="${main_unit_text}" style="margin-right:5px;">`;
                opening_stock +=
                    `<input type="text" name="opening_stock[]" value="" class="form-control col" placeholder="${sub_unit_text}">`;
            }

            $('.opening_stocks').html(opening_stock);
        });
</script>

@include('includes.placeholder_model')
@endsection