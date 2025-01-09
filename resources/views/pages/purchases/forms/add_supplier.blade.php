<form action="{{ route("purchase.add_supplier") }}" id="edit_form" method="POST" enctype="multipart/form-data">

    @csrf
    <div class="errors"></div>

      <div class="form-group">
          <label for="">Supplier Name</label>
          <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid': '' }}" name="name" value="{{ old('name') }}" placeholder="Enter Supplier Name...">
      </div>

      <div class="form-group">
          <label for="email">Email</label>
          <input type="text" class="form-control {{ $errors->has('email') ? 'is-invalid': '' }}" name="email" value="{{ old('email') }}" placeholder="Enter Supplier Email...">
      </div>

      <div class="form-group">
          <label for="phone">Address</label>
          <textarea name="address" class="form-control {{ $errors->has('address') ? 'is-invalid': '' }}" placeholder="Write Supplier Address"></textarea>
      </div>
    
      <div class="form-group">
          <label for="phone">Phone</label>
          <input type="text" class="form-control {{ $errors->has('phone') ? 'is-invalid': '' }}" name="phone" value="{{ old('phone') }}" placeholder="Enter Supplier Phone...">
      </div>

      <div class="form-group">
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
       </div>

    <input type="submit" class="btn btn-info" value="Add Supplier">
</form>