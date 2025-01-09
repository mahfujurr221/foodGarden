@extends('layouts.master')
@section('title', 'Create Unit')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
          <h1 class="header-title">
            <strong>New Unit</strong>
          </h1>
        </div>

        <div class="header-action">
          <nav class="nav">
            <a class="nav-link" href="{{ route('unit.index') }}">
              Units
            </a>
            <a class="nav-link active" href="{{ route('unit.create') }}">
                 <i class="fa fa-plus"></i>
                 Add Unit
            </a>
          </nav>
        </div>
      </header>
@endsection

@section('content')
  <div class="col-12">
    <div class="card">
      <div class="row">
        <div class="col-12" style="">
          <h4 class="card-title" style="display: inline-block;">New Unit</h4>


        </div>
      </div>


    <form action="{{ route('unit.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
      <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="">Unit Name</label>
                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid': '' }}" name="name" value="{{ old('name') }}" placeholder="e.g. Kg">
                @if($errors->has('name'))
                     <span class="invalid-feedback">{{ $errors->first('name') }}</span>
                @endif
           </div>


           <div class="form-group col-md-4">
             <label for="">Related To Unit</label>
             <select name="related_to_unit_id" id="" class="form-control">
                 <option value="">Select Unit</option>
               @foreach ($units as $item)
                 <option value="{{ $item->id }}" {{ old("related_to_unit_id")==$item->id?"SELECTED":"" }}>{{ $item->name }}</option>
               @endforeach
             </select>
             @if($errors->has('related_to_unit_id'))
               <div class="alert alert-danger">{{ $errors->first('related_to_unit_id') }}</div>
             @endif
           </div>

           <div class="form-group col-md-4">
             <label for="">Operator</label>
             <select name="related_sign" id="" class="form-control">
                 <option value="">Select Operator Sign</option>
                 <option value="*" {{ old("related_sign")=="*"?"SELECTED":"" }}>(*) Multiply Operator</option>
                 {{-- <option value="/" {{ old("related_sign")=="/"?"SELECTED":"" }}>(/) Division Operator</option> --}}
             </select>
             @if($errors->has('related_sign'))
               <div class="alert alert-danger">{{ $errors->first('related_sign') }}</div>
             @endif
           </div>

           <div class="form-group col-md-4">
             <label for="">Related By Value</label>
             <input type="text" name="related_by" value="{{ old("related_by") }}" class="form-control">
             @if($errors->has('related_by'))
               <div class="alert alert-danger">{{ $errors->first('related_by') }}</div>
             @endif
           </div>

           <div class="col-12">
            <h4 class="final_text" style="text-align:center;"></h4>
           </div>
        </div>
        {{-- <hr> --}}
        {{-- <div class="form-row justify-content-center">
          <div class="form-group ">
            <button type="submit" class="btn btn-info">
              <i class="fa fa-save"></i>
              Add Unit
            </button>
          </div>
        </div> --}}
        <div class="form-row">
            <div class="col-12">
                <input type="submit" class="btn btn-info float-right" value="Add Unit">
            </div>
        </div>
      </div>
      </form>
    </div>
  </div>
@endsection

@section('styles')
    {{-- <link rel="stylesheet" href="{{ asset('back/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}"> --}}
    <style>
      .form-control {
          border-color: #b5b1b1;
      }
      label {
        font-size: 13px;
        font-weight: 600;
      }
    </style>
@endsection

@section('scripts')
    <script>
        // $('input[name="name"]').change(function(){
        //     update_final_text();
        // });

        $('select[name="related_to_unit_id"]').change(function(){
            update_final_text();
        });

        $('select[name="related_sign"]').change(function(){
            update_final_text();
        });

        $('input[name="related_by"]').change(function(){
            update_final_text();
        });

        function update_final_text(){
            // alert("HELLO");
            var string ="1 ";
            string+=$('input[name="name"]').val();
            string+=" = 1";
            string+=$('select[name="related_to_unit_id"]').find(":selected").text();
            string+=" ";
            string+=$('select[name="related_sign"]').find(":selected").val();
            string+=" ";
            string+=$('input[name="related_by"]').val();
            $('.final_text').html(string);
        }
    </script>
    <script src="{{ asset('js/modal_form_no_reload.js') }}"></script>
    @include('includes.placeholder_model')
@endsection
