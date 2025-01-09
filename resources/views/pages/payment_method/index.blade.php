@extends('layouts.master')
@section('title', 'Expense Category List')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>Payment Method</strong>
    </h1>
  </div>

  <div class="header-action">
    <nav class="nav">
      {{-- <a class="nav-link" href="{{ route('expense.index') }}">
        <i class="fa fa-plus"></i>
        Expense
      </a>
      <a class="nav-link" href="{{ route('expense.create') }}">
        <i class="fa fa-plus"></i>
        Add Expense
      </a>
      <a class="nav-link active" href="{{ route('expense-category.index') }}">
        Expense Category
      </a> --}}
      {{-- <a class="nav-link" href="{{ route('expense-category.index') }}">
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
    @isset($payment_method)
    <h4 class="card-title">Update Payment Method</h4>
    @else
    <h4 class="card-title">Add Payment Method</h4>
    @endisset
    <div class="card-body">
      <form
        action="@isset($payment_method) {{ route('payment_method.update', $payment_method->id) }} @else {{ route('payment_method.store') }} @endisset"
        method="POST">
        @csrf
        @isset($payment_method)
        @method('PUT')
        @endisset
        <div class="form-row">
          <div class="form-group col-md-4 mt-4">
            {{-- <label for="name">Expense Category</label> --}}
            <input type="text" name="name" placeholder="Enter Payment Method" class="form-control {{ $errors->has('name') ? 'is-invalid': '' }}"
              @isset($payment_method) value="{{ $payment_method->name }}" @endisset>
            @if($errors->has('name'))
            <span class="invalid-feedback">{{ $errors->first('name') }}</span>
            @endif
          </div>
          {{-- <div class="form-group col-md-4">
            <label class="custom-control custom-control-lg custom-checkbox ml-5 mt-4">
              <input type="checkbox" class="custom-control-input" name="active" @isset($payment_method)
                {{ $payment_method->active ? 'checked' : '' }} @endisset>
              <span class="custom-control-indicator"></span>
              <span class="custom-control-description">Active Category</span>
            </label>

          </div> --}}
          <div class="form-group col-md-4">
            <button type="submit" class="btn btn-primary mt-4">
              @isset($payment_method)
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
    <h4 class="card-title"><strong>Payment Methods</strong></h4>
    <div class="card-body card-body-soft">
      @if($payment_methods->count() > 0)
      <div class="table-responsive-sm table-bordered">
        <table class="table table-soft">
          <thead>
            <tr class="bg-primary">
              <th style="width:10%;">#</th>
              <th>Payment Method</th>
              {{-- <th>Status</th> --}}
              <th style="width:20%;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($payment_methods as $key => $item)
            <tr>
              <th scope="row">{{ ++$key }}</th>
              <td>{{ $item->name }}</td>
              {{-- <td>
                @if($item->active)
                <span class="badge badge-pill badge-success">Active</span>
                @else
                <span class="badge badge-pill badge-danger">Inactive</span>
                @endif
              </td> --}}
              <td>
                <a href="{{ route('payment_method.edit', $item->id) }}" class="btn btn-primary btn-sm">
                  <i class="fa fa-edit"></i>
                  Edit
                </a>
                <a href="{{ route('payment_method.destroy',$item->id) }}" class="btn btn-danger btn-sm delete">
                  <i class="fa fa-trash"></i>
                </a>
              </td>
            </tr>
            @endforeach

          </tbody>
        </table>
        {{-- {{ $payment_methods->links() }} --}}
      </div>
      @else
      <div class="alert alert-danger text-center" role="alert">
        <strong>You have no Payment Methods </strong>
      </div>
      @endif
    </div>
  </div>
</div>

@endsection

@section('styles')

@endsection

@section('scripts')
@include('includes.delete-alert')
@endsection