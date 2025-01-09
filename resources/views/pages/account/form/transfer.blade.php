<form action="{{ route("bank_account.transfer_store") }}" id="edit_form" method="POST" enctype="multipart/form-data">

     @csrf
     <div class="errors"></div>

     <input type="text" name="from" id="" value="{{ $account->id }}" hidden>

     <div class="form-group">
          <label for="">To Account</label>
          <select name="to" id="" class="form-control">
               @foreach (\App\BankAccount::where('id','!=',$account->id)->get() as $item)
               <option value="{{ $item->id }}" {{ old("to")==$item->id?"SELECTED":"" }}>{{ $item->name }}</option>
               @endforeach
          </select>
          @if($errors->has('to'))
          <div class="alert alert-danger">{{ $errors->first('to') }}</div>
          @endif
     </div>


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


     <input type="submit" class="btn btn-info" value="Transfer" onclick="return confirm('Are You Sure?')">
</form>
