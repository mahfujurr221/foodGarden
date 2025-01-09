@extends('layouts.master')
@section('title', 'Unit List')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>Units</strong>
    </h1>
  </div>

  <div class="header-action">
    <nav class="nav">
      <a class="nav-link active" href="{{ route('unit.index') }}">
        Units
      </a>
      {{-- <a class="nav-link" href="#">Import Products</a> --}}
      <a class="nav-link" href="{{ route('unit.create') }}">
        <i class="fa fa-plus"></i>
        Add Unit
      </a>
    </nav>
  </div>
</header>
@endsection

@section('content')
<div class="col-12">

  {{-- <div class="card card-body mb-2">
    <form action="{{ route('product.index') }}">
      <div class="form-row">
        <div class="form-group col-md-4">
          <input type="text" name="code" class="form-control" placeholder="Product Code">
        </div>
        <div class="form-group col-md-4">
          <input type="text" class="form-control" name="name" placeholder="Product Name">
        </div>
        <div class="form-group col-md-4">
          <div class="form-group">
            <select name="category" id="" class="form-control">
              <option value="">Select Category</option>
              @foreach (\App\Category::all() as $item)
              <option value="{{ $item->id }}" {{ old("category")==$item->id?"SELECTED":"" }}>{{ $item->name }}</option>
              @endforeach
            </select>
            @if($errors->has('category'))
            <div class="alert alert-danger">{{ $errors->first('category') }}</div>
            @endif
          </div>
        </div>
      </div>
      <div class="form-row mt-2">
        <div class="form-group float-right">
          <button class="btn btn-primary" type="submit">
            <i class="fa fa-sliders"></i>
            Filter
          </button>
          <a href="{{ route('product.index') }}" class="btn btn-info">Reset</a>
        </div>
      </div>
    </form>
  </div> --}}


  <div class="card">
    <h4 class="card-title"><strong>Units</strong></h4>

    <div class="card-body">
      <div class="">
        <table class="table table-responsive table-bordered units-table" data-provide="">
          <thead>
            <tr class="bg-primary">
              <th class="text-center">#</th>
              <th>Name</th>
              <th>Related To</th>
              <th>Related Sign</th>
              <th>Related By</th>
              <th>Result</th>
              {{-- <th>Children</th> --}}
              <th class="text-center">#</th>
            </tr>
          </thead>
          <tbody>
            {{-- @dd($units) --}}
            @forelse($units as $key => $unit)
            <tr>
              <td>{{ (isset($_GET['page']))? ($_GET['page']-1)*20+$key+1 : $key+1 }}</td>
              <td>{{ $unit->name }}</td>
              <td>{{ $unit->related_unit?$unit->related_unit->name:"-" }}</td>
              <td>{{ $unit->related_sign?$unit->related_sign:"-" }}</td>
              <td>{{ $unit->related_by?$unit->related_by:"-" }}</td>
              <td>@if($unit->related_unit){{ $unit->name }} = 1 {{ $unit->related_unit?$unit->related_unit->name:"-" }}
                {{ $unit->related_sign?$unit->related_sign:"-" }} {{ $unit->related_by?$unit->related_by:"-" }}@endif
              </td>
              {{-- <td>{{ $unit->child_units }}</td> --}}
              <td class="text-center">
                <div class="btn-group">
                  <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-cogs"></i>
                  </button>
                  <div class="dropdown-menu" x-placement="bottom-start">
                    @if(!$unit->default)
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#unitEditModal-{{ $unit->id }}">
                      <i class="fa fa-edit"></i>
                      Edit
                    </a>

                    <a class="dropdown-item delete" href="{{ route('unit.destroy', $unit->id) }}">
                      <i class="fa fa-trash"></i>
                      Delete
                    </a>
                    @endif
                  </div>
                </div>
              </td>
            </tr>

            {{-- Edit Modal --}}
            <form method="post" action="{{ route('unit.update', $unit->id) }}">
              @csrf
              @method('PUT')
            <div class="modal fade" id="unitEditModal-{{ $unit->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
              aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    
                      <div class="form-group">
                        <label for="name">Unit Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $unit->name }}">
                      </div>
                    
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
            @empty
            <tr>
              <td colspan="12">
                <div class="alert alert-danger" role="alert">
                  <strong>You have no Units</strong>
                </div>
              </td>
            </tr>
            @endforelse

          </tbody>
        </table>

        {!! $units->appends(Request::except("_token"))->links() !!}

      </div>

    </div>
  </div>
</div>


{{-- End Modal --}}
@endsection

@section('styles')
<style>
  .units-table td {
    text-align: center;
  }

  .table>p {
    font-size: 19px;
    padding-top: 5px;
    letter-spacing: 4px;
    margin-bottom: 0px;
  }

  .p_img {
    border: 1px solid rgb(0, 0, 0);
    padding: 5px;
  }

  @media print {
    body {
      width: 100%;
      height: 100%;
      margin: 0;
      padding: 0;
    }

    body * {
      visibility: visible;
    }

    @page {
      size: 'A4'
    }

    #barcode-page {}
  }
</style>
@endsection

@section('scripts')

@include('includes.delete-alert')
@endsection