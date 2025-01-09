<form action="{{ route('pos.add_customer') }}" method="POST" id="edit_form">
    @csrf
    <div class="errors"></div>

    <div class="form-group">
        <label for="">Name</label>
        <input type="text" name="name" value="{{ old("name") }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="">Email</label>
        <input type="text" name="email" value="{{ old("email") }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="">Address</label>
        <textarea name="address" class="form-control" cols="30" rows="2">{{ old('address') }}</textarea>
    </div>

    <div class="form-group">
        <label for="">Phone</label>
        <input type="text" name="phone" value="{{ old("phone") }}" class="form-control">
    </div>

    {{-- <div class="form-group">
        <label for="">Opening Receivable</label>
        <input type="text" name="opening_receivable" value="{{ old("opening_receivable") }}" class="form-control">
        @if($errors->has('opening_receivable'))
            <div class="alert alert-danger">{{ $errors->first('opening_receivable') }}</div>
        @endif
    </div>

    <div class="form-group">
        <label for="">Opening Payable</label>
        <input type="text" name="opening_payable" value="{{ old("opening_payable") }}" class="form-control">
        @if($errors->has('opening_payable'))
            <div class="alert alert-danger">{{ $errors->first('opening_payable') }}</div>
        @endif
    </div> --}}

    {{-- <div class="form-group">
    <label for="custom_one">Custom Field</label>
    <input type="text" class="form-control {{ $errors->has('custom_one') ? 'is-invalid': '' }}" name="custom_one" value="{{ old('custom_one') }}" placeholder="Custom Field">
    @if($errors->has('custom_one'))
            <span class="invalid-feedback">{{ $errors->first('custom_one') }}</span>
    @endif
    </div> --}}

    <input type="submit" class="btn btn-success" value="Add Customer">
</form>