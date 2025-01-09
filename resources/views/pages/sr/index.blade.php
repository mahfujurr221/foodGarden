@extends('layouts.master')
@section('title', 'SR List')

@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>SR List</strong>
        </h1>
    </div>

    <div class="header-action">
        <nav class="nav">
            <a class="nav-link active" href="{{ route('sr.index') }}">
                SR List
            </a>
            <a class="nav-link" href="{{ route('sr.create') }}">
                <i class="fa fa-plus"></i>
                New SR
            </a>
        </nav>
    </div>
</header>
@endsection

@section('content')
<div class="col-12">

    <div class="card card-body mb-2">
        <form>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <input type="text" class="form-control" name="name" placeholder="Name"
                        value="{{ request()->name }}">
                </div>
                <div class="form-group col-md-4">
                    <input type="text" class="form-control" name="mobile" placeholder="Mobile Number"
                        value="{{ request()->mobile }}">
                </div>
            </div>
            <div class="form-row mt-2">
                <div class="form-group float-right">
                    <button class="btn btn-primary" type="submit">
                        <i class="fa fa-sliders"></i>
                        Filter
                    </button>
                    <a href="{{ Request::url() }}" class="btn btn-info">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <h4 class="card-title"><strong>SR List</strong></h4>

        <div class="card-body card-body-soft p-4">
            @if($sr_list->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" {{-- data-provide="datatables" --}}>
                    <thead>
                        <tr class="bg-primary">
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Receivable</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Wallet Balance</th>
                            <th>Total Due</th>
                            <th>Customers</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sr_list as $sr)
                        <tr>
                            <th scope="row">
                                {{ $loop->iteration + $sr_list->perPage() * ($sr_list->currentPage() - 1) }}
                            </th>
                            <td>{{ $sr->name }}</td>
                            <td>{{ $sr->email }}</td>
                            <td>{{ $sr->mobile }}</td>
                            <td>{!! $sr->address !!}</td>
                            <td class="font-weight-bold">
                                {{ number_format($sr->receivable(), 2) }} Tk
                            </td>
                            
                         
                            <td class="font-weight-bold">
                                {{ number_format($sr->paid(), 2) }} Tk
                            </td>

                            <td class="font-weight-bold">
                                {{ number_format($sr->due(), 2) }} Tk
                            </td>
                            <td class="font-weight-bold">
                                 {{ number_format($sr->wallet_balance(), 2) }} Tk
                                 
                                  @if($sr->wallet_balance()>0)
                                    <p>** কাস্টমারের টাকা আপনার কাছে জমা আছে</p>
                                  @elseif($sr->wallet_balance()<0)
                                    <p>** কাস্টমারের কাছে আপনার পাওনা রয়েছে</p>
                                  @endif
                            </td>  
                            
                            <td class="font-weight-bold">
                                 {{ number_format($sr->total_due(), 2) }} Tk
                            </td>
                            <td style="text-align:center;">
                                {{ $sr->customers_count }}
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fa fa-cogs"></i>
                                    </button>
                                    <div class="dropdown-menu" x-placement="bottom-start">
                                        <a class="dropdown-item" href="{{ route('sr.edit', $sr->id) }}">
                                            <i class="fa fa-edit"></i>
                                            Edit
                                        </a>
                                        <a class="dropdown-item" href="{{ route('sr.show', $sr->id) }}">
                                            <i class="fa fa-file-excel-o"></i>
                                            Report
                                        </a>
                                        <a class="dropdown-item delete" href="{{ route('sr.destroy',$sr->id) }}">
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
                {!! $sr_list->appends(Request::except("_token"))->links() !!}
            </div>
            @else
            <div class="alert alert-danger" role="alert">
                <strong>You have no SR </strong>
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
@include('includes.placeholder_model')
<script src="{{ asset('js/modal_form.js') }}"></script>
@endsection