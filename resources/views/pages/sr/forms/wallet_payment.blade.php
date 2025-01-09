<form action="{{ route("customer.wallet_payment",$customer) }}" id="edit_form" method="POST" enctype="multipart/form-data">

    @csrf
    <div class="errors"></div>

    <div class="form-group">
      <label for="">Payment Date</label>
      <input type="text" class="form-control {{ $errors->has('payment_date') ? 'is-invalid': '' }}"
            data-provide="datepicker" data-date-today-highlight="true"
            data-date-format="yyyy-mm-dd"
            name="payment_date" value="{{ date('Y-m-d') }}">
    </div>

    <div class="form-group">
        <label for="method">Payment Method </label>
        <select name="payment_method" id="p_method" class="form-control">
            <option value="">Select Method</option>
            @foreach (\App\PaymentMethod::all() as $item)
                <option
                    value="{{ $item->id }}" {{ old("payment_method")==$item->id?"SELECTED":"" }}>{{ $item->name }}</option>
            @endforeach
        </select>

    </div>

    <div class="form-group">
      <label for="">Amount</label>
      <input type="text" name="pay_amount" value="{{ $customer->due() }}" class="form-control">
    </div>

    <input type="submit" class="btn btn-info" value="Make Wallet Payment">
</form>