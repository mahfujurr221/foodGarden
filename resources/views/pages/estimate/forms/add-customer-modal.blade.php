<form action="{{ route('customer.store') }}" method="POST">
    @csrf
    <div class="modal fade" id="quick-customer" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="">Customer Name(বাংলা)<span class="field_required"></span></label>
                                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                    name="name" value="{{ old('name') }}" placeholder="Enter Customer Name...">
                                @if ($errors->has('name'))
                                <span class="invalid-feedback">{{ $errors->first('name') }}</span>
                                @endif
                            </div>

                            <div class="form-group col-md-12">
                                <label for="shop_name_bangla">Shop Name(বাংলা)<span class="field_required"></span></label>
                                <input type="text"
                                    class="form-control {{ $errors->has('shop_name_bangla') ? 'is-invalid' : '' }}"
                                    name="shop_name_bangla" value="{{ old('shop_name_bangla') }}" placeholder="দোকানের নাম দিন">
                                @if ($errors->has('shop_name_bangla'))
                                <span class="invalid-feedback">{{ $errors->first('shop_name_bangla') }}</span>
                                @endif
                            </div>
        
                            <div class="form-group col-md-12">
                                <label for="shop_name">Shop Name<span class="field_required"></span></label>
                                <input type="text" class="form-control {{ $errors->has('shop_name') ? 'is-invalid' : '' }}"
                                    name="shop_name" value="{{ old('shop_name') }}" placeholder="Enter Customer Shop Name...">
                                @if ($errors->has('shop_name'))
                                <span class="invalid-feedback">{{ $errors->first('shop_name') }}</span>
                                @endif
                            </div>
        
                            <div class="col-8">
                                <div class="form-group">
                                    <label for="">Address<span class="field_required"></span></label>
                                    <select name="address_id" id="address" class="form-control" required>
                                        <option value=""> ~ Select ~</option>
                                        @foreach (\App\Address::all() as $address)
                                            <option value="{{ $address->id }}">{{ $address->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('address_id'))
                                        <span class="invalid-feedback">{{ $errors->first('address_id') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-2">
                                <label for="" style="display: block; visibility:hidden;">Add Brand</label>
                                <a href="{{ route('customer.add_address') }}" class="edit btn btn-outline btn-primary"
                                    data-toggle="modal" data-target="#edit" id="Add Address" style="">Add Address
                                </a>
                            </div>
                            <div class="col-8">
                                <div class="form-group">
                                    <label for="">Business Category<span class="field_required"></span></label>
                                    <select name="business_cat_id"  class="form-control" required>
                                        <option value=""> ~ Select ~</option>
                                        @foreach (\App\BusignessCategory::all() as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('business_cat_id'))
                                        <span class="invalid-feedback">{{ $errors->first('business_cat_id') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-2">
                                <label for="" style="display: block; visibility:hidden;">Add Brand</label>
                                <a href="{{ route('customer.add_business_category') }}"
                                    class="edit btn btn-outline btn-primary" data-toggle="modal" data-target="#edit"
                                    id="Add Business Category" style="">Add
                                    Business Category</a>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="phone">Phone<span class="field_required"></span></label>
                                <input type="text" class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                                    name="phone" value="{{ old('phone') }}" placeholder="Enter Customer Phone...">
                                @if ($errors->has('phone'))
                                <span class="invalid-feedback">{{ $errors->first('phone') }}</span>
                                @endif
                            </div>

                            <div class="col-md-12 my-2">
                                <div class="form-group">
                                    <label for="note">Note</label>
                                    <textarea name="note" class="form-control"></textarea>
                                </div>
                            </div>
    
                        </div> <!-- End form-row -->

                        <div class="form-row justify-content-center mt-3">
                            <button class="btn btn-primary">
                                <i class="fa fa-save mr-2"></i>
                                Add Customer
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
