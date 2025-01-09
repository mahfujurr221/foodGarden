@extends('layouts.master')
@section('title', 'Damages')

@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Damages</strong>
        </h1>
    </div>
</header>
@endsection

@section('content')
<div class="col-12">
    <div class="card print_area">
        <div class="row">
            <div class="col-12" style="margin-top: 10px;">
                <h4 class="card-title"><strong>Damages</strong></h4>
            </div>
        </div>
        <div class="card-body">
            @if ($damages->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped" data-provide="datatables"
                data-page-length="100">
                    <thead>
                        <tr class="bg-primary">
                            <th>#</th>
                            <th>Id</th>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Note</th>
                            <th class="print_hidden">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($damages as $key => $item)
                        <tr>
                            <td>{{ isset($_GET['page']) ? ($_GET['page'] - 1) * 20 + $key + 1 : $key + 1 }}
                            </td>
                            <td>{{ $item->id }}</td>
                            <td>{{ date('d/m/Y', strtotime($item->date)) }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->product->readable_qty($item->qty) }}</td>
                            <td>{{ $item->note }}</td>
                            <td class="print_hidden">
                                <div class="btn-group">
                                    <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fa fa-cogs"></i>
                                    </button>
                                    <div class="dropdown-menu" x-placement="bottom-start">
                                        <a class="dropdown-item" href="{{ route('damage.edit', $item->id) }}">
                                            <i class="fa fa-edit"></i>
                                            Edit
                                        </a>
                                        <a class="dropdown-item delete" href="{{ route('damage.destroy', $item->id) }}">
                                            <i class="fa fa-trash text-danger"></i>
                                            Delete
                                        </a>

                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
                {{-- {{ $damages->links() }} --}}
            </div>
            @else
            <div class="alert alert-danger" role="alert">
                <strong>You have no damages</strong>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
@section('scripts')
@include('includes.delete-alert')
@endsection