<form action="{{ route("bank_account.add_money.store") }}" id="edit_form" method="POST" enctype="multipart/form-data">

     @csrf
     <div class="errors"></div>

     <input type="text" name="to" id="" value="{{ $bank_account->id }}" hidden>

     {{-- <div class="form-group">
      <label for="">To Account</label>
      <select name="to" id="" class="form-control">
        @foreach (\App\BankAccount::where('id','!=',$account->id)->get() as $item)
          <option value="{{ $item->id }}" {{ old("to")==$item->id?"SELECTED":"" }}>{{ $item->name }}</option>
     @endforeach
     </select>
     @if($errors->has('to'))
     <div class="alert alert-danger">{{ $errors->first('to') }}</div>
     @endif
     </div> --}}


     <div class="form-group">
          <label for="">Amount</label>
          <input type="text" name="amount" value="{{ old("amount") }}" class="form-control">
          @if($errors->has('amount'))
          <div class="alert alert-danger">{{ $errors->first('amount') }}</div>
          @endif
     </div>

     <div class="form-group">
        <label for="">Note</label>
        <textarea name="note" id="" cols="30" rows="4" class="form-control"></textarea>
        @if($errors->has('note'))
          <div class="alert alert-danger">{{ $errors->first('note') }}</div>
        @endif
      </div>

      <div class="form-group">
        <label for="">Owner</label>
        <select name="owner_id" id="" class="form-control">
          @foreach (\App\Owner::get() as $item)
            <option value="{{ $item->id }}" {{ old("owner_id")==$item->id?"SELECTED":"" }}>{{ $item->name }}</option>
          @endforeach
        </select>
        @error('owner_id')
          <div class="text-danger">{{ $message }}</div>
        @enderror
      </div>


     <input type="submit" class="btn btn-info" value="Add Balance" onclick="return confirm('Are You Sure?')">
</form>
