@extends('layouts.master')
@section('title', 'Expense List')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Expense</strong>
          </h1>
     </div>

     <div class="header-action">
          <nav class="nav">
               <a class="nav-link" href="{{ route('expense.index') }}">
                    <i class="fa fa-list"></i>
                    Expenses
               </a>
               <a class="nav-link active" href="{{ route('expense.create') }}">
                    <i class="fa fa-plus"></i>
                    Add Expense
               </a>
               <a class="nav-link" href="{{ route('expense-category.index') }}">
                    {{-- <i class="fa fa-plus"></i> --}}
                    Expense Categories
               </a>
          </nav>
     </div>
</header>
@endsection

@section('content')
<div class="col-12">
     <div class="card">
          @isset($expense)
          <h4 class="card-title">Edit Expense</h4>
          @else
          <h4 class="card-title">New Expense</h4>
          @endisset
          <div class="card-body">
               <form action="@isset($expense) {{ route('expense.update', $expense->id) }} @else {{ route('expense.store') }} @endisset"
                    method="POST">
                    @csrf
                    @isset($expense)
                    @method('PUT')
                    @endisset
                    <div class="form-row">
                         <div class="form-group col-md-4">
                              <label for="name">Expense Name<span class="field_required"></span>:</label>
                              <input type="text" name="name" placeholder="Expense Name"
                                   class="form-control {{ $errors->has('name') ? 'is-invalid': '' }}" @isset($expense)
                                   value="{{ $expense->name }}" @endisset value="{{ old('name') }}">
                              @if($errors->has('name'))
                              <span class="invalid-feedback">{{ $errors->first('name') }}</span>
                              @endif
                         </div>
                         <div class="form-group col-md-4">
                              <label for="name">Expense Amount<span class="field_required"></span>:</label>
                              <input type="number" step="any" name="amount" placeholder="Enter Expense Amount"
                                   class="form-control {{ $errors->has('amount') ? 'is-invalid': '' }}" @isset($expense)
                                   value="{{ $expense->amount }}" @endisset value="{{ old('amount') }}">
                              @if($errors->has('amount'))
                              <span class="invalid-feedback">{{ $errors->first('amount') }}</span>
                              @endif
                         </div>
                         <div class="form-group col-md-4">
                              <label for="name">Expense Date<span class="field_required"></span>:</label>
                              <input type="text" data-provide="datepicker" name="expense_date"
                                   data-date-today-highlight="true" data-date-format="yyyy-mm-dd"
                                   class="form-control {{ $errors->has('expense_date') ? 'is-invalid': '' }}"
                                   @isset($expense) value="{{ $expense->expense_date }}" @else
                                   value="{{ date('Y-m-d') }}" @endisset value="expense_date">

                              @if($errors->has('expense_date'))
                              <span class="invalid-feedback">{{ $errors->first('expense_date') }}</span>
                              @endif
                         </div>

                         <div class="form-group col-md-4">
                              <label for="name">Expense Category<span class="field_required"></span>:</label>
                              <select name="category_id" id="" required
                                   class="form-control {{ $errors->has('category_id') ? 'is-invalid': '' }}">
                                   <option value="">Select Category</option>
                                   @foreach ($expese_categories as $item)
                                   <option
                                   @isset($expense)
                                       {{ $expense->category_id == $item->id ? 'selected' : '' }}
                                   @endisset
                                   {{ old('category_id') == $item->id ? 'selected' : '' }}
                                   value="{{ $item->id }}">{{ $item->name }}</option>
                                   @endforeach
                              </select>
                              @if($errors->has('category_id'))
                              <span class="invalid-feedback">{{ $errors->first('category_id') }}</span>
                              @endif
                         </div>

                         <div class="form-group col-md-4">
                            <label for="">Transaction Account</label>
                            <select name="bank_account_id" class="form-control" required>
                              @foreach (\App\BankAccount::all() as $item)
                              <option value="{{ $item->id }}" {{ old("bank_account_id") == $item->id ? "SELECTED":"" }}>
                                {{ $item->name }}</option>
                              @endforeach
                            </select>
                            @if($errors->has('bank_account_id'))
                            <div class="alert alert-danger">{{ $errors->first('bank_account_id') }}</div>
                            @endif
                          </div>

                         <div class="form-group col-md-4">
                              <label for="note">Expense Note:</label>
                              <textarea placeholder="Enter Optional Note" name="note"
                                   class="form-control {{ $errors->has('note') ? 'is-invalid': '' }}">@isset($expense){{ $expense->note }} @endisset{{ old('note') }}</textarea>

                              @if($errors->has('note'))
                              <span class="invalid-feedback">{{ $errors->first('note') }}</span>
                              @endif
                         </div>
                    </div>
                    <div class="form-row">
                         <div class="form-group">
                              <button type="submit" class="btn btn-primary mt-4">
                                   @isset($expense)
                                   <i class="fa fa-refresh"></i>
                                   Update
                                   @else
                                   <i class="fa fa-save"></i>
                                   Save
                                   @endisset

                              </button>
                         </div>
                    </div>
                    <hr style="margin:0px">
               </form>
          </div>
     </div>
</div>
@endsection

@section('styles')

@endsection

@section('scripts')
<script>

</script>
@endsection
