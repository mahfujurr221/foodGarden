@extends('layouts.master')
@section('title', 'Brand List')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
          <h1 class="header-title">
            <strong>Categories</strong>
          </h1>
        </div>

        <div class="header-action">
          <nav class="nav">
            <a class="nav-link active" href="{{ route('category.index') }}">
              Categories
            </a>
             {{-- <a class="nav-link" href="#">Import Ccategory</a> --}}
            <a class="nav-link" href="{{ route('category.create') }}">
                 <i class="fa fa-plus"></i>
                 Add Category
            </a>
          </nav>
        </div>
      </header>
@endsection

@section('content')
    <div class="col-12">
            <div class="card">
              <h4 class="card-title"><strong>Categories</strong></h4>

              <div class="card-body card-body-soft">
                   @if($categories->count() > 0)
                <div class="table-responsive table-bordered">
                  <table class="table table-soft">
                    <thead>
                      <tr class="bg-primary">
                        <th>#</th>
                        <th>Image</th>
                        {{-- <th>Code</th> --}}
                        <th>Category</th>
                        <th>Count Products</th>
                        <th>#</th>
                      </tr>
                    </thead>
                    <tbody>
                         @foreach($categories as $key => $category)
                      <tr>
                        <th scope="row">
                            {{ $loop->iteration + $categories->perPage() * ($categories->currentPage() - 1) }}
                        </th>
                        <td>
                             <img src="{{ $category->image ? asset($category->image->link) : asset('dashboard/images/not-available.png') }}" width="50" alt="">
                        </td>
                        {{-- <td>{{ $category->code }}</td> --}}
                        <td>{{ $category->name }}</td>
                        <td>
                            {{ $category->products_count() }}
                        </td>
                        <td>
                          <div class="btn-group">
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                              <i class="fa fa-cogs"></i>
                              Manage
                              </button>
                            <div class="dropdown-menu" x-placement="bottom-start">
                              <a class="dropdown-item" href="{{ route('category.edit', $category->id) }}">
                                <i class="fa fa-edit"></i>
                                Edit
                              </a>
                              <a class="dropdown-item delete" href="{{ route('category.destroy',$category->id) }}" >
                                <i class="fa fa-trash"></i>
                                Delete
                              </a>
                            </div>
                        </div>
                        </td>
                      </tr>
                      @endforeach

                    </tbody>
                  </table>
                  {{ $categories->links() }}
                </div>
                @else
                <div class="alert alert-danger" role="alert">
                     <strong>You have no Category</strong>
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