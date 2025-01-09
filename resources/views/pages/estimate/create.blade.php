@extends('layouts.master')
@section('title', 'Estimate Manage')

@section('page-header')
<header class="header bg-ui-general sm_hidden">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Order Manage</strong>
        </h1>
    </div>
</header>
@endsection

@section('content')
<div class="col-md-12">
    <div class="row">
        <div class="col-md-6">
            <div id="products">
                @include('pages.estimate.products')
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
                <form action="{{ route('estimate.store') }}" id="order-form" method="POST">
                    @csrf
                    <div class="form-row justify-content-center">
                        <div class="form-group col-6 col-md-6 col-sm-12" style="margin-top:30px;">
                            <select name="customer" id="customer" class="form-control customer_input"
                                data-provide="selectpicker" data-live-search="true" data-size="10" required>
                                {{-- data-provide="selectpicker" --}}
                                <option value="">Walk-in Customer</option>
                                @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->shop_name . ' - '.
                                    $customer->address->name . ' (' . $customer->name . ')' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3 col-md-2 col-sm-3 sm_hidden" style="margin-top: 30px;">
                            {{-- <button class="" type="button" data-toggle="modal"
                                data-target="#add-customer-modal">Add</button> --}}
                            <a href="{{ route('pos.add_customer') }}" class="edit btn add_btn" data-toggle="modal"
                                data-target="#quick-customer">
                                {{-- <i class="fa fa-money text-primary"></i> --}}
                                Add
                            </a>
                        </div>
                        <input hidden type="text" data-provide="datepicker" data-date-today-highlight="true"
                            data-date-format="yyyy-mm-dd" class="btn date" name="estimate_date"
                            value="{{ date('Y-m-d') }}">

                        <div class="col-md-4 col-4 col-sm-5">
                            <label for="order-date " class="ml-2 sm_hidden">Delivery Date:</label><br>
                            <?php
                                $nextDay = date('N') >= 5 ? strtotime('next Saturday') : strtotime('+1 day');
                            ?>
                            <input type="text" id="delivery_date" data-provide="datepicker"
                                data-date-today-highlight="true" data-date-format="yyyy-mm-dd"
                                class="form-control text-center date" name="delivery_date"
                                value="<?php echo date('Y-m-d', $nextDay); ?>">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 col-sm-12 col-12">
                            <table class="table table-bordered">
                                <thead class="bg-primary">
                                    <tr>
                                        <th>Name</th>
                                        <th id="orderQty" style="width:350px;">Quantity</th>
                                        {{-- <th style="width:100px;">Dis.Qty</th> --}}
                                        <th class="sm_hidden">Price</th>
                                        {{-- <th>Disc.</th> --}}
                                        <th>Sub.T</th>
                                        <th style="background: red; text-align: center; cursor: pointer;">
                                            <a href="#" id="clearList">
                                                <i class="fa fa-trash text-white" style="font-size: 12px;"></i>
                                            </a>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody id="tbody">

                                </tbody>

                                <tfoot class="bg-danger">
                                    <tr>
                                        <th class="text-center" colspan="5">Total: <strong id="totalAmount"></strong>
                                            Tk</th>
                                    </tr>
                                </tfoot>

                            </table>

                            <div class="form-gorup text-center">
                                <div class="row justify-content-center">
                                    {{-- <a href="{{route('damage.create')}}" class="btn damage_btn">
                                        <i class="fa fa-money"></i>
                                        Damage
                                    </a> --}}
                                    <button type="submit" id="payment-btn" class="btn order_btn">
                                        <i class="fa fa-money"></i>
                                        Create Order
                                    </button>
                                    {{-- <a a href="{{ route('pos.create') }}" class="btn payment_btn">
                                        <i class="fa fa-money"></i>
                                        POS
                                    </a> --}}
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

                            <div class="modal fade" id="payment" tabindex="-1">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel">Payment</h4>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-bordered text-left">
                                                <tr>
                                                    <td width="50%">
                                                        <strong class="float-left">Paying Items: </strong>
                                                        <strong class="float-right">(<span id="items">0</span>)</strong>
                                                    </td>
                                                    <td>
                                                        <strong class="float-left">Total Receivable: </strong>
                                                        <strong class="float-right">(<span id="receivable">0</span>
                                                            Tk)</strong>
                                                        <input type="hidden" name="receivable_amount"
                                                            id="receivable_input">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="50%">
                                                        <strong class="float-left">After Discount : </strong>
                                                        <strong class="float-right"> (<span id="after_discount">0</span>
                                                            Tk)</strong>
                                                    </td>
                                                    <td>
                                                        <strong class="float-left">Balance </strong>
                                                        <strong class="float-right"> (<span id="balance">0</span>
                                                            Tk)</strong>
                                                        <input type="hidden" name="balance" id="balance_input">
                                                    </td>
                                                </tr>
                                            </table>
                                            <hr>

                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="discount">Discount</label>
                                                    <input type="text" class="form-control" id="discount"
                                                        name="discount" placeholder="0%">
                                                </div>
                                                {{-- <div class="form-group col-md-6">
                                                    <label for="payment_method">Note</label>
                                                    <textarea name="note" class="form-control"></textarea>
                                                </div> --}}
                                            </div>
                                            <hr>
                                            <div class="form-row">

                                                <div class="form-group col-md-6">
                                                    <label for="">Transection Account</label>
                                                    <select name="bank_account_id" class="form-control" required>
                                                        @foreach (\App\BankAccount::all() as $item)
                                                        <option value="{{ $item->id }}" {{
                                                            old('bank_account_id')==$item->id ? 'SELECTED' : '' }}>
                                                            {{ $item->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('bank_account_id'))
                                                    <div class="alert alert-danger">
                                                        {{ $errors->first('bank_account_id') }}</div>
                                                    @endif
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="pay_amount">Pay Amount</label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="form-control"
                                                            name="pay_amount" id="pay_amount"
                                                            placeholder="Pay Amount...">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-warning" type="button"
                                                                id="paid_btn">PAID!</button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-bold btn-secondary"
                                                data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-bold btn-primary" id="order-btn">
                                                <i class="fa fa-shopping-cart"></i>
                                                Order
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</div>

{{-- Alert Modal --}}
<div class="modal fade" id="alert-modal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <h3>Please add some products.</h3>
                </div>
            </div>
        </div>
    </div>
</div>


@include('pages.estimate..forms.add-customer-modal')
@endsection

@section('styles')
<style>
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

    @media (max-width: 575.98px) {
        .category {
            max-height: 300px;
            overflow: auto;
        }

        .list-group {
            width: fit-content;
        }

        .category .list-group-item .btn {
            text-align: left;
        }

        .decrease-qty {
            display: none;
        }

        .increase-qty {
            display: none;
        }

        #orderQty {
            width: 500px;
        }

        .sm_hidden {
            display: none;
        }
        .card-body {
            padding: 10px;
        }

        .card-header {
            padding: 0px;
            margin: 0px;
        }

        .card-title {
            padding: 0px;
        }

        .date{
            margin-top: 10px;
        }
    }
</style>

<link rel="stylesheet" href="{{ asset('dashboard/css/pos.css') }}">
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('scripts')
@include('includes.placeholder_model')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@include('pages.estimate.scripts')
<script>
    //$("#id_code").focus();
        // product search
        $(function() {
            $("#product_search").autocomplete({
                source: function(req, res) {
                    let url = "{{ route('product-search') }}";
                    $.get(url, {
                        req: req.term
                    }, (data) => {
                        res($.map(data, function(item) {
                            return {
                                id: item.id,
                                value: item.name + " " + item.code,
                                price: item.price
                            }
                        })); // end res
                    });
                },
                select: function(event, ui) {
                    $(this).val(ui.item.value);
                    $("#search_product_id").val(ui.item.id);
                    let url = "{{ route('product.details', 'placeholder_id') }}".replace(
                        'placeholder_id', ui.item.id);
                    $.get(url, (product) => {
                        console.log(product);
                        // check stock
                        // if(product.checkSaleOverStock == 0) {
                        //   if(product.stock <= 0) {
                        //     toastr.warning('This product is Stock out. Please Purchases the Product.');
                        //     return false;
                        //   }
                        // }
                        if (pExist(product.id) == true) {
                            toastr.warning('Please Increase the quantity.');
                        } else {
                            addProductToCard(product);
                        }

                    });

                    $(this).val('');

                    return false;
                },
                response: function(event, ui) {
                    if (ui.content.length == 1) {
                        ui.item = ui.content[0];
                        $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                        $(this).autocomplete('close');

                    }
                },
                minLength: 0
            });


            //  CODE SEARCH

            $("#id_code").autocomplete({
                source: function(req, res) {
                    let url = "{{ route('product-code-search') }}";
                    $.get(url, {
                        req: req.term
                    }, (data) => {
                        res($.map(data, function(item) {
                            return {
                                id: item.id,
                                value: item.name + " - " + item.code,
                                price: item.price
                            }
                        })); // end res

                    });
                },
                select: function(event, ui) {

                    $(this).val(ui.item.value);
                    $("#search_product_id").val(ui.item.id);
                    let url = "{{ route('product.details', 'placeholder_id') }}".replace(
                        'placeholder_id', ui.item.id);
                    $.get(url, (product) => {
                        // check stock
                        // if(product.checkSaleOverStock == 0) {
                        //   if(product.stock <= 0) {
                        //     toastr.warning('This product is Stock out. Please Purchases the Product.');
                        //     return false;
                        //   }
                        // }
                        if (pExist(product.id) == true) {
                            toastr.warning('Please Increase the quantity.');
                        } else {
                            addProductToCard(product);
                        }
                    });
                    $(this).val('');

                    return false;
                },
                response: function(event, ui) {
                    if (ui.content.length == 1) {
                        ui.item = ui.content[0];
                        $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                        $(this).autocomplete('close');
                    }
                },
                minLength: 0
            });
        });

        //  Set Product Id
        function productSelected(id) {
            console.log(id);
        }
</script>

<script>
    $(document).ready(function() {
        var currentDate = new Date();
        
        // If today is Friday (day 5), set the date to Saturday; otherwise, set it to the next day
        if (currentDate.getDay() === 5) {
            currentDate.setDate(currentDate.getDate() + 1);
        } else {
            currentDate.setDate(currentDate.getDate() + 1);
        }

        var formattedDate = currentDate.toISOString().slice(0, 10);
        $('#delivery_date').val(formattedDate);
    });
</script>

{{-- <script src="/js/add_customer.js"></script> --}}

<script src="{{ asset('js/modal_form.js') }}"></script>
@endsection