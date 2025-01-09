@extends('layouts.master')
@section('title', 'Brand List')

@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Business</strong>
        </h1>
    </div>
</header>
@endsection

@section('content')
<div class="col-12">
    <div class="card">
        <h4 class="card-title">
            <strong>Business</strong>
            <a href="{{ route('customer.add_business_category') }}" class="edit btn btn-outline btn-primary  pull-right"
                data-toggle="modal" data-target="#edit" id="Add Business Category" style="">Add
                Business Category</a>
        </h4>
        <div class="card-body card-body-soft">
            @if($business->count() > 0)
            <div class="table-responsive table-bordered">
                <table class="table table-soft">
                    <thead>
                        <tr class="bg-primary">
                            <th>#</th>
                            <th style="width: 70%;">Name</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($business as $key => $data)
                        <tr>
                            <th scope="row">{{ ++$key }}</th>
                            <td>{{ $data->name }}</td>
                            <td>
                                <a class="btn btn-primary text-white" data-target="#editModal-{{$data->id}}" data-toggle="modal">
                                    <i class="fa fa-edit"></i>
                                    Edit
                                </a>
                            </td>
                        </tr>

                        <form action="{{route('customer.update_business_category', $data->id)}}" method="POST">
                            @csrf
                            <div class="modal fade" id="editModal-{{$data->id}}" role="dialog"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="modal-loader" style="display: none; text-align: center;">
                                                <img src="/loading.gif">
                                            </div>
                                            <input type="text" name="name" value="{{ $data->name }}"
                                                class="form-control">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endforeach
                    </tbody>
                </table>
                {{ $business->links() }}
            </div>
            @else
            <div class="alert alert-danger" role="alert">
                <strong>You have no business category</strong>
            </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection

@section('styles')

@endsection

@section('scripts')
<script src="{{ asset('js/modal_form_no_reload.js') }}"></script>
@include('includes.delete-alert')
@include('includes.placeholder_model')
@endsection