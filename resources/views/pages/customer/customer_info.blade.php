@extends('layouts.master')
@section('title', 'Customer List')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Customers Info</strong>
            </h1>
        </div>
    </header>
@endsection

@section('content')
    <div class="col-12">

        <div class="card print_area">
            <div class="row">
                <div class="col-12" style="display:flex; justify-content:space-between">
                    
                    {{-- <div class="col-md-9 print_hidden mt-3">
                        <form action="{{ route('customer.info') }}">
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <input type="text" class="form-control" name="name" placeholder="Name"
                                        value="{{ old('name') }}">
                                </div> 
                                <div class="form-group col-md-2">
                                    <input type="text" class="form-control" name="mobile" placeholder="Mobile Number"
                                        value="{{ old('name') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <select name="customer" id="" class="form-control" data-provide="selectpicker"
                                        data-live-search="true" data-size="10">
                                        <option value="">Select Customer</option>
                                        @foreach (\App\Customer::get() as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->shop_name_bangla . ' ' . $customer->shop_name . '-'.
                                                    $customer->address->name . ' (' . $customer->name . '-'  . $customer->phone .')' }}
                                                </option>
                                        @endforeach
                                    </select>
                                </div>
            
                                <div class="form-group col-md-2">
                                    <select name="address_id" id="address" class="form-control">
                                    </select>
                                    @if ($errors->has('address_id'))
                                        <span class="invalid-feedback">{{ $errors->first('address_id') }}</span>
                                    @endif
                                </div>
                                <div class="form-group col-md-2">
                                    <select name="business_cat_id" id="business_category" class="form-control">
                                    </select>
                                    @if ($errors->has('business_cat_id'))
                                        <span class="invalid-feedback">{{ $errors->first('business_cat_id') }}</span>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fa fa-sliders"></i>
                                            Filter
                                        </button>
                                        <a href="{{ route('customer.index') }}" class="btn btn-info">Reset</a>
                                    </div>
                                </div>
                            </div>
            
                        </form>
                    </div>
                    --}}
                </div>
            </div>

            <div class="card-body card-body-soft p-4">
                @if ($customers->count() > 0)
                    <table class="table table-bordered table-responsive " data-provide="datatables" data-page-length="100">
                        <thead>
                            <tr class="bg-primary">
                                <th>#</th>
                                <th>Customer.Name</th>
                                <th style="min-width:150px;">Shop Name</th>
                                <th>Shop.Category</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $key => $customer)
                                <tr>
                                    <th scope="row">
                                        {{ $key + 1 }}
                                    </th>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->shop_name_bangla }} <br>({{ $customer->shop_name??'' }})</td>
                                    <td>{{ $customer->business_category->name??''}}</td>
                                    <td>{{ $customer->address->name??'' }}</td>
                                    <td>{{ $customer->phone??'-' }}</td>
                                    <td>{{ $customer->note??'-' }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                @else
                    <div class="alert alert-danger" role="alert">
                        <strong>You have no Customers</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')

@endsection

@section('scripts')
    <script src="{{ asset('js/select2.min.js') }}"></script>
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

    @include('includes.delete-alert')
    @include('includes.placeholder_model')
    <script src="{{ asset('js/modal_form.js') }}"></script>
@endsection
