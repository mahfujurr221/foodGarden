@extends('layouts.master')
@section('title', 'Account List')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Account</strong>
          </h1>
     </div>

     <div class="header-action">
          <nav class="nav">
               <a class="nav-link active" href="{{ route('bank_account.index') }}">
                    <i class="fa fa-plus"></i>
                    Account
               </a>
               {{-- <a class="nav-link" href="{{ route('expense.create') }}">
               <i class="fa fa-plus"></i>
               Add Expense
               </a>
               <a class="nav-link active" href="{{ route('account.index') }}">
                    Expense Category
               </a> --}}
               {{-- <a class="nav-link" href="{{ route('account.index') }}">
               <i class="fa fa-plus"></i>
               New Category
               </a> --}}
          </nav>
     </div>
</header>
@endsection

@section('content')
<div class="col-12">
     <div class="card">
          @isset($account)
          <h4 class="card-title">Update Account</h4>
          @else
          <h4 class="card-title">New Account</h4>
          @endisset
          <div class="card-body">
               <form action="@isset($account) {{ route('bank_account.update', $account->id) }} @else {{ route('bank_account.store') }} @endisset"
                    method="POST">
                    @csrf
                    @isset($account)
                    @method('PUT')
                    @endisset
                    <div class="form-row">
                         <div class="form-group col-md-4 mt-4">
                              {{-- <label for="name">Expense Category</label> --}}
                              <input type="text" name="name" placeholder="Enter Account Name"
                                   class="form-control {{ $errors->has('name') ? 'is-invalid': '' }}" @isset($account)
                                   value="{{ $account->name }}" @endisset>
                              @if($errors->has('name'))
                              <span class="invalid-feedback">{{ $errors->first('name') }}</span>
                              @endif
                         </div>

                         @isset($account)
                         @else
                         <div class="form-group col-md-4 mt-4">
                              {{-- <label for="">Opening Balance</label> --}}
                              <input type="text" name="opening_balance" placeholder="Opening Balance"
                                   value="{{ old("opening_balance") }}" class="form-control">
                              @if($errors->has('opening_balance'))
                              <div class="alert alert-danger">{{ $errors->first('opening_blanace') }}</div>
                              @endif
                         </div>
                         @endif
                         {{-- <div class="form-group col-md-4">
            <label class="custom-control custom-control-lg custom-checkbox ml-5 mt-4">
              <input type="checkbox" class="custom-control-input" name="active" @isset($account)
                {{ $account->active ? 'checked' : '' }} @endisset>
                         <span class="custom-control-indicator"></span>
                         <span class="custom-control-description">Active Category</span>
                         </label>

                    </div> --}}
                    <div class="form-group col-md-4">
                         <button type="submit" class="btn btn-primary mt-4">
                              @isset($account)
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
<div class="col-12">
     <div class="card">
          <h4 class="card-title"><strong>Accounts</strong></h4>
          <div class="card-body card-body-soft">
               @if($accounts->count() > 0)
               <div class="table-responsive-sm table-bordered">
                    <table class="table table-soft">
                         <thead>
                              <tr class="bg-primary">
                                   <th style="width:10%;">#</th>
                                   <th>Name</th>
                                   <th>Opening Balance</th>
                                   <th>Current Balance</th>
                                   {{-- <th>Status</th> --}}
                                   <th style="width:20%;">Actions</th>
                              </tr>
                         </thead>
                         <tbody>
                              @foreach($accounts as $key => $item)
                              <tr>
                                   <td>{{ (isset($_GET['page']))? ($_GET['page']-1)*20+$key+1 : $key+1 }}</td>
                                   <td>{{ $item->name }}</td>
                                   <td>Tk.{{  number_format($item->opening_balance, 2) }}</td>
                                   <td>Tk.{{ number_format($item->balance(), 2) }}</td>
                                   {{-- <td>
                @if($item->active)
                <span class="badge badge-pill badge-success">Active</span>
                @else
                <span class="badge badge-pill badge-danger">Inactive</span>
                @endif
              </td> --}}
                                   <td>


                                        <a href="{{ route("bank_account.add_money",$item->id) }}"
                                             class="edit btn btn-outline btn-primary" data-toggle="modal"
                                             data-target="#edit" id="Add Balance"><i
                                                  class="la la-edit text-primary"></i>Add Balance </a>
                                        <a href="{{ route("bank_account.withdraw_money",$item->id) }}"
                                             class="edit btn btn-outline btn-primary" data-toggle="modal"
                                             data-target="#edit" id="Withdraw Balance"><i
                                                  class="la la-edit text-primary"></i>Withdraw Balance </a>


                                        {{-- <a href="#" onclick="handle({{ $item->id }})" class="btn btn-danger
                                        btn-sm">
                                        <i class="fa fa-trash"></i>
                                        </a>
                                        --}}

                                        <a href="{{ route("bank_account.transfer",$item->id) }}"
                                             class="edit btn btn-outline btn-primary" data-toggle="modal"
                                             data-target="#edit" id="Add Payment"><i
                                                  class="la la-edit text-primary"></i>Transfer </a>

                                        <a href="{{ route('bank_account.history', $item->id) }}"
                                             class="btn btn-primary btn-sm">
                                             <i class="fa fa-edit"></i>
                                             History
                                        </a>

                                   </td>
                              </tr>
                              @endforeach

                         </tbody>
                    </table>

                    {{-- {{ $accounts->links() }} --}}
               </div>
               @else
               <div class="alert alert-danger text-center" role="alert">
                    <strong>You have no Accounts </strong>
               </div>
               @endif
          </div>
     </div>


</div>
{{-- Delete Confirm Modal --}}


@endsection

@section('styles')
<style>
     .alert {
          display: block;
     }
</style>
@endsection

@section('scripts')
     @include('includes.delete-alert')
     @include('includes.placeholder_model')
     <script src="{{ asset('js/modal_form.js') }}"></script>
@endsection