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
      <a class="nav-link active" href="{{ route('expense.index') }}">
        <i class="fa fa-list"></i>
        Expenses
      </a>
      <a class="nav-link" href="{{ route('expense.create') }}">
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
  <div class="card card-body mb-2">
    <form action="#">
      <div class="form-row">
        <div class="form-group col-md-4">
          <input type="text" data-provide="datepicker" data-date-today-highlight="true" data-orientation="bottom"
            data-date-format="yyyy-mm-dd" data-date-autoclose="true" class="form-control" name="start_date"
            placeholder="Start Date">
        </div>
        <div class="form-group col-md-4">
          <input type="text" data-provide="datepicker" data-date-today-highlight="true" data-orientation="bottom"
            data-date-format="yyyy-mm-dd" data-date-autoclose="true" class="form-control" name="end_date"
            placeholder="End Date">
        </div>
        <div class="form-group col-md-4">
          <select name="category" id="" class="form-control" data-provide="selectpicker" data-live-search="true">
            <option value="">Select Category</option>
            @foreach ($expenseCategory as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-row mt-2">
        <div class="form-group col-12">
          <button class="btn btn-primary" type="submit">
            <i class="fa fa-sliders"></i>
            Filter
          </button>
          <a href="{{ route('expense.index') }}" class="btn btn-info">Reset</a>
          <a href="" class="btn btn-primary pull-right" onclick="window.print()">Print</a>
        </div>
      </div>
    </form>
  </div>

  <div class="card print_area" style="width:100%;">
    <h4 class="card-title"><strong>Expense</strong></h4>
    <div class="card-body">
      @if($expenses->count() > 0)
      <div class="table-responsive">
        <table class="table  table-bordered" data-provide="">
          <thead>
            <tr class="bg-primary">
              <th>#</th>
              <th>Expense</th>
              <th>Date</th>
              <th>Category</th>
              <th>Amount</th>
              <th>Note</th>
              <th class="print_hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($expenses as $key => $item)
            <tr>
              <td>{{ (isset($_GET['page']))? ($_GET['page']-1)*20+$key+1 : $key+1 }}</td>
              <td>{{ $item->name }}</td>
              <td>
                {{ date('d M, Y', strtotime($item->expense_date)) }}
              </td>
              <td>
                {{ $item->category ? $item->category->name : '' }}
              </td>
              <td>
                {{ $item->amount }} Tk
              </td>
              <td>
                {!! $item->note !!}
              </td>
              <td style="width:20%;" class="print_hidden">
                <a href="{{ route('expense.edit', $item->id) }}" class="btn btn-primary btn-sm">
                  <i class="fa fa-edit"></i>
                  Edit
                </a>
                <a href="{{ route('expense.destroy',$item->id) }}"  class="btn btn-danger btn-sm delete">
                  <i class="fa fa-trash"></i>Delete
                </a>
              </td>
            </tr>
            @endforeach

          </tbody>
          <tfoot>
            <tr>
              <td colspan="4"></td>
              <td>
                <strong>
                  Total : {{ number_format($expenses->sum('amount')) }} Tk
                </strong>
              </td>
              <td colspan="2"></td>
            </tr>
          </tfoot>
        </table>

        {!! $expenses->appends(Request::except("_token"))->links() !!}
      </div>
      @else
      <div class="alert alert-danger text-center" role="alert">
        <strong>You have no Expenses. </strong>
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
