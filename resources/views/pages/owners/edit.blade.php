@extends('layouts.master')
@section('title', 'Edit Owner')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>Edit Owner</strong>
    </h1>
  </div>

  <div class="header-action">
    <nav class="nav">
      <a class="nav-link" href="{{ route('owners.index') }}">
        Owners
      </a>
      <a class="nav-link active" href="{{ route('owners.create') }}">
        <i class="fa fa-plus"></i>
        Add Owner
      </a>
    </nav>
  </div>
</header>
@endsection

@section('content')
<div class="col-12">
  <div class="card">
    <h4 class="card-title">Edit Owner</h4>

    <form action="{{ route('owners.update',$item) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
          <div class="form-row">
              <div class="form-group col-md-6">
                  <label for="">Name</label>
                  <input type="text" name="name" value="{{ $item->name }}" class="form-control">
                  @error('name')
                    <div class="text-danger mt-2">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-group col-md-6">
                  <label for="">Mobile</label>
                  <input type="text" name="mobile" value="{{ $item->mobile }}" class="form-control">
                  @error('mobile')
                    <div class="text-danger mt-2">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-group col-12">
                  <label for="">Address</label>
                  <textarea name="address" id="" cols="30" rows="4" class="form-control">{{ $item->address }}</textarea>
                  @error('address')
                    <div class="text-danger mt-2">{{ $message }}</div>
                  @enderror
                </div>
          </div>

          <hr>
          <div class="form-row justify-content-center">
            <div class="form-group ">
              <button type="submit" class="btn btn-info">
                <i class="fa fa-save"></i>
                Update Owner
              </button>
            </div>
          </div>
        </div>
      </form>
  </div>
</div>
@endsection

@section('styles')
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
  $("#category_id").on("change", function () {
      let categoryId = $(this).val();
      let url = '/back/get-sub-category/'+ categoryId;

      if(!categoryId) {
        $("#sub_categories").html('')
      }

      $.get(url, function (data) {
        let options = '';
        if(data.length > 0) {
          data.forEach((val, index) => {
            options += `<option value="${val.id}">${val.name}</option>`;
          });
          let select = `
          <label for="">Sub Category</label>
              <select multiple name="sub_categories[]" id="sub_categories" class="form-control"
                data-provide="selectpicker" data-live-search="true">
                  ${options}
              </select>
        `;
          $("#sub_categories").html(select)
          return;
        }
        $("#sub_categories").html('')
      });
  })
</script>

@endsection
