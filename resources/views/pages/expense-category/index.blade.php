@extends('layouts.master')
@section('title', 'Expense Category List')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>Expense Category</strong>
    </h1>
  </div>

  <div class="header-action">
    <nav class="nav">
      <a class="nav-link" href="{{ route('expense.index') }}">
        <i class="fa fa-list"></i>
        Expenses
      </a>
      <a class="nav-link" href="{{ route('expense.create') }}">
        <i class="fa fa-plus"></i>
        Add Expense
      </a>
      <a class="nav-link active" href="{{ route('expense-category.index') }}">
        Expense Categories
      </a>
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
    @isset($expenseCategory)
    <h4 class="card-title">Update Expense Category</h4>
    @else
    <h4 class="card-title">New Expense Category</h4>
    @endisset
    <div class="card-body">
      <form
        action="@isset($expenseCategory) {{ route('expense-category.update', $expenseCategory->id) }} @else {{ route('expense-category.store') }} @endisset"
        method="POST">
        @csrf
        @isset($expenseCategory)
        @method('PUT')
        @endisset
        <div class="form-row">
          <div class="form-group col-md-4 mt-4">
            {{-- <label for="name">Expense Category</label> --}}
            <input type="text" name="name" placeholder="Enter Expense Category" class="form-control {{ $errors->has('name') ? 'is-invalid': '' }}"
              @isset($expenseCategory) value="{{ $expenseCategory->name }}" @endisset>
            @if($errors->has('name'))
            <span class="invalid-feedback">{{ $errors->first('name') }}</span>
            @endif
          </div>
          {{-- <div class="form-group col-md-4">
            <label class="custom-control custom-control-lg custom-checkbox ml-5 mt-4">
              <input type="checkbox" class="custom-control-input" name="active" @isset($expenseCategory)
                {{ $expenseCategory->active ? 'checked' : '' }} @endisset>
              <span class="custom-control-indicator"></span>
              <span class="custom-control-description">Active Category</span>
            </label>

          </div> --}}
          <div class="form-group col-md-4">
            <button type="submit" class="btn btn-primary mt-4">
              @isset($expenseCategory)
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
    <h4 class="card-title"><strong>Expense Category</strong></h4>
    <div class="card-body card-body-soft">
      @if($expenseCategories->count() > 0)
      <div class="table-responsive table-bordered">
        <table class="table table-soft">
          <thead>
            <tr class="bg-primary">
              <th style="width:10%;">#</th>
              <th>Category Name</th>
              {{-- <th>Status</th> --}}
              <th style="width:20%;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($expenseCategories as $key => $item)
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
                <a href="{{ route('expense-category.edit', $item->id) }}" class="btn btn-primary btn-sm">
                  <i class="fa fa-edit"></i>
                  Edit
                </a>
                <a href="{{ route('expense-category.destroy',$item->id) }}" onclick="" class="btn btn-danger btn-sm delete">
                  <i class="fa fa-trash"></i> Delete
                </a>
              </td>
            </tr>
            @endforeach

          </tbody>
        </table>
        {{ $expenseCategories->links() }}
      </div>
      @else
      <div class="alert alert-danger text-center" role="alert">
        <strong>You have no Expense Category </strong>
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
