@extends('layouts.master')
@section('title', 'Add Damage')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Add Damage</strong>
            </h1>
        </div>
    </header>
@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="row">
                {{-- <div class="form-gorup text-center">
                    <div class="row justify-content-center mt-2">
                        <a href="{{ route('estimate.create') }}" class="btn order_btn">
                            <i class="fa fa-money"></i>
                            Order
                        </a>
                        <a a href="{{ route('pos.create') }}" class="btn payment_btn">
                            <i class="fa fa-money"></i>
                            POS
                        </a>
                    </div>
                </div> --}}
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div id="products">
                        @include('pages.damage.products')
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-body">
                        {{-- <div class="form-row mb-3">
                            <div class="col-md-12">
                                <input type="text" id="product_search" class="form-control"
                                    placeholder="Start to write product name..." name="p_name" />
                                <input type="hidden" id="search_product_id">
                            </div>
                        </div> --}}

                        <form action="{{ route('damage.store') }}" id="order-form" method="POST">
                            @csrf
                            <div class="form-row justify-content-end">
                                <div class="col-md-3">
                                    <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                                        data-date-format="yyyy-mm-dd" class="btn date" name="date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mt-4">
                                    <table class="table table-bordered">
                                        <thead class="bg-primary table_head">
                                            <tr>
                                                <th>Name</th>
                                                <th style="width:320px;">Quantity</th>
                                                <th>Sub T</th>
                                                <th
                                                    style="background: red; width: 50px; text-align: center; cursor: pointer;">
                                                    <a href="#" id="clearList">
                                                        <i class="fa fa-trash text-white"></i>
                                                    </a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody"></tbody>
                                        <tfoot class="bg-danger">
                                            <tr>
                                                {{-- <th class="text-center" colspan="2">Total Qty: <strong id="totalQty"></strong> </th> --}}
                                                <th class="text-center" colspan="1">Total Qty: <strong
                                                        id="totalQty"></strong>
                                                <th class="text-center" ><strong>Total</strong></th>
                                                <th class="text-center" colspan="2"><strong id="totalAmount"></strong>Tk
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div class="form-gorup text-center">
                                        <div class="row justify-content-center">
                                            <button type="submit" class="btn damage_btn active">
                                                <i class="fa fa-money"></i>
                                                Add Damage
                                            </button>
                                        </div>
                                        @if ($errors->any())
                                            <div class="text-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        hr {
            margin: 5px auto;
        }

        .category {
            max-height: 600px;
            overflow: auto;
        }

        .list-group {
            width: fit-content;
        }

        .category .list-group-item .btn {
            text-align: left;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('dashboard/css/pos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('scripts')
    <script src="{{ asset('js/modal_form_no_reload.js') }}"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    @include('pages.damage.scripts')

    <script src="{{ asset('js/modal_form.js') }}"></script>
@endsection
