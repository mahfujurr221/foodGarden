@extends('layouts.master')
@section('title', 'Convert Invoice')

@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Convert Invoice</strong>
        </h1>
    </div>
</header>
@endsection
@section('content')
<div class="col-md-12">
    <div class="row">
        {{-- <div class="col-md-12">`
            <div class="form-row mb-3">
                <div class="col-md-12">
                    <input type="text" id="product_search" class="form-control"
                        placeholder="Start to write product name..." name="p_name" />
                    <input type="hidden" id="search_product_id">
                </div>
            </div>
        </div> --}}

        <div class="col-md-12">
            <div class="card card-body">
                <form action="{{ route('pos.store') }}" id="sale-manage-form" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 col-sm-12">
                            {{-- <label class="ml-2 mr-2" style="color:black">Order Note:</label>
                            <textarea class="form-control" name="note">{{ $estimate->note }}</textarea> --}}
                            <strong>Customer Name : </strong>{{ $estimate->customer->name??'' }}<br>
                            <strong>Business Category : </strong>{{ $estimate->customer->phone??'' }}<br>
                            <strong>Customer Shop Name : </strong>{{ $estimate->customer->shop_name_bangla??'' }}<br>
                            <strong>Note : </strong>{{ $estimate->customer->note?? '' }}<br>
                        </div>

                        <div class="col-md-2">
                            <label class="mr-2" style="color:black">Order Date:</label>
                            <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                                data-date-format="yyyy-mm-dd" class="form-control"
                                value="{{ $estimate->estimate_date }}" name="estimate_date_holder" disabled>
                        </div>

                        <div class="col-md-2">
                            <label class="mr-2" style="color:black">Delivery Date:</label>
                            <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                            data-date-format="yyyy-mm-dd" class="form-control"
                            value="{{ $estimate->delivery_date }}" disabled>
                        </div>

                        <div class="col-md-6">
                            <input type="hidden" class="form-control" value="{{ $estimate->customer_id }}"
                                name="customer" />
                            <input type="hidden" class="form-control" value="{{ $estimate->id }}" name="estimate" />
                            <input hidden type="text" data-provide="datepicker" data-date-today-highlight="true"
                                data-date-format="yyyy-mm-dd" class="form-control" name="sale_date"
                                value="{{ $estimate->delivery_date }}" />
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-md-12 mt-4">
                            <table class="table table-bordered table-responsive-sm">
                                
                                <thead class="bg-primary">
                                    <tr>
                                        <th style="min-width:120px;">Product</th>
                                        <th style="min-width:120px;">Order Qty</th>
                                        <th style="min-width:120px;">Returned Qty</th>
                                        <th>Damage</th>
                                        <th>D.Value</th>
                                        <th class="smHidden">Price</th>
                                        <th>S.Total</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    @foreach ($estimate->items as $pos_item)
                                    <tr>
                                        <td style="background-color:#ece9e9; width:100px;">
                                            <strong>{{ $pos_item->product->name }}</strong>
                                            <input type="hidden" class="form-control"
                                                value="{{ $pos_item->product_name }}"
                                                name="name[{{ $pos_item->product->id }}]" readonly />
                                            <input type="hidden" value="{{ $pos_item->product->id }}"
                                                name="product_id[{{ $pos_item->product->id }}]" readonly />
                                        </td>
                                        @php
                                        $product = $pos_item->product;
                                        @endphp
                                        <td style="width:120px; background-color: #ece9e9;text-align:center">
                                            <div class="form-row">
                                                @php
                                                $product = $pos_item->product;
                                                @endphp
                                                @if (!$pos_item->product->sub_unit)
                                                {{-- ONLY MAIN UNIT --}}
                                                <input type="text" class="has_sub_unit" hidden value="false">
                                                <label class="ml-3 mt-1"><strong>{{$pos_item->main_unit_qty}} {{
                                                        $product->main_unit->name }}</strong></label>
                                                <input type="number" value="{{ $pos_item->main_unit_qty }}"
                                                    class="form-control col main_qty mr-1"
                                                    name="main_qty[{{ $pos_item->product->id }}]"
                                                    data-value="{{ $pos_item->product->stock }}"
                                                    data-related="{{ $pos_item->product->main_unit->related_by }}"
                                                    onkeydown="return event.keyCode !== 190" min="0" readonly hidden>
                                                @else
                                                <input type="text" class="has_sub_unit" hidden value="true">
                                                <input type="text" class="conversion" hidden
                                                    value="{{ $product->main_unit->related_by }}">
                                                <label class="ml-3 mt-1"><strong>{{$pos_item->main_unit_qty}} {{
                                                        $product->main_unit->name }}</strong></label>
                                                <input hidden type="number" value="{{ $pos_item->main_unit_qty }}"
                                                    class="form-control col main_qty mr-1"
                                                    name="main_qty[{{ $pos_item->product->id }}]"
                                                    data-value="{{ $pos_item->product->stock }}"
                                                    data-related="{{ $pos_item->product->main_unit->related_by }}"
                                                    onkeydown="return event.keyCode !== 190" min="0" readonly>
                                                <label class="ml-1 mt-1"><strong>{{ $pos_item->sub_unit_qty??'0' }} {{
                                                        $product->sub_unit->name }}</strong></label>
                                                <input hidden type="number" value="{{ $pos_item->sub_unit_qty }}"
                                                    class="form-control col sub_qty mr-1"
                                                    name="sub_qty[{{ $pos_item->product->id }}]"
                                                    onkeydown="return event.keyCode !== 190" min="0"
                                                    max="{{ $product->main_unit->related_by - 1 }}" readonly>
                                                @endif
                                            </div>
                                        </td>

                                        <td style="width:100px;display:none">
                                            <input readonly type="number" value="{{ $pos_item->discount_qty }}"
                                                class="form-control discount"
                                                name="discount_qty[{{ $pos_item->product->id }}]" readonly />
                                        </td>

                                        <td style="width:100px;display:none">
                                            <input readonly type="number" value="{{ $pos_item->discount_return }}"
                                                class="form-control discount_return"
                                                name="discount_return[{{ $pos_item->product->id }}]" readonly />
                                        </td>
                                        
                                        <td style="width:250px; background-color: #ece9e9;text-align:center">
                                            <div class="form-row">
                                                @if (!$pos_item->product->sub_unit)
                                                {{-- ONLY MAIN UNIT --}}
                                                <input type="text" class="has_sub_unit" hidden value="false">
                                                <label class="ml-2 mr-2">{{ $pos_item->returned?? '0'}} {{ $product->main_unit->name }} </label>
                                                <input hidden type="number" value="{{ $pos_item->returned }}"
                                                    class="form-control col returned"
                                                    name="old_returned[{{ $pos_item->product->id }}]"
                                                    data-old="{{ $pos_item->returned }}"
                                                    data-value="{{ $product->stock() }}"
                                                    data-related="{{ $product->main_unit->related_by }}"
                                                    onkeydown="return event.keyCode !== 190" readonly>
                                                @else
                                                {{-- HAS SUB UNIT --}}
                                                <input type="text" class="has_sub_unit" hidden value="true">
                                                <input type="text" class="conversion" hidden
                                                    value="{{ $product->main_unit->related_by }}">
                                                <label class="mr-1 ml-1">{{ $pos_item->returned?? '0' }} {{ $product->main_unit->name }}</label>
                                                <input hidden type="number" value="{{ $pos_item->returned }}"
                                                    class="form-control col returned mr-1 bg_none"
                                                    name="old_returned[{{ $pos_item->product->id }}]"
                                                    data-old="{{ $pos_item->returned }}"
                                                    data-value="{{ $product->stock() }}"
                                                    data-related="{{ $product->main_unit->related_by }}"
                                                    onkeydown="return event.keyCode !== 190" readonly>
                                                    
                                                <label class="mr-1">{{ $pos_item->returned_sub_unit??'0' }} {{ $product->sub_unit->name }}</label>
                                                <input hidden type="number" value="{{ $pos_item->returned_sub_unit }}"
                                                    class="form-control col returned_sub_unit mr-1 bg_none"
                                                    name="old_returned_sub_unit[{{ $pos_item->product->id }}]"
                                                    data-old="{{ $pos_item->returned_sub_unit }}"
                                                    onkeydown="return event.keyCode !== 190"
                                                    max="{{ $product->main_unit->related_by - 1 }}" readonly>
                                                @endif
                                            </div>
                                        </td>

                                        <td style="width:100px;display:none">
                                            <input type="number" value="{{ $pos_item->returned_value }}"
                                                class="form-control bg_none" name="returned_value[{{ $pos_item->product->id }}]"
                                                readonly />
                                        </td>

                                        <td style="width:100px; background-color: #ece9e9;text-align:center">
                                            <input type="number" value="{{ $pos_item->damage }}"
                                                class="form-control damage bg_none" name="damage[{{ $pos_item->product->id }}]"
                                                readonly />
                                        </td>
                                        
                                        <td style="width:100px;background-color: #ece9e9;text-align:center">
                                            <input type="number" value="{{ $pos_item->damaged_value }}"
                                                class="form-control bg_none" name="damaged_value[{{ $pos_item->product->id }}]"
                                                readonly />
                                        </td>
                                        
                                        <td class="smHidden" style="width:100px;background-color: #ece9e9;text-align:center">
                                            <input type="text" value="{{ $pos_item->rate }}" class="form-control rate bg_none"
                                                name="rate[{{ $pos_item->product->id }}]" readonly />

                                            <input type="text" value="{{ $pos_item->product->brand_id }}"
                                                name="brand_id" hidden />

                                            <input type="text" value="{{ $pos_item->ordered_qty }}"
                                                name="ordered_qty[{{ $pos_item->product->id }}]" hidden />
                                        </td>
                                        
                                        <td style="width:150px;background-color: #ece9e9;text-align:center">
                                            <input type="text" readonly name="sub_total[{{ $pos_item->product->id }}]"
                                                class="form-control sub_total bg_none" value="{{ $pos_item->sub_total }}"
                                                readonly />
                                            <span class="sub_total_placeholder" hidden>{{ $pos_item->sub_total }}</span>
                                        </td>
                                        
                                        <td class="d-none">
                                            <input type="text" name="subtotal_holder[{{ $pos_item->product->id }}]"
                                                class="form-control subtotal_holder"
                                                value="{{ $pos_item->ordered_sub_total }}" hidden />
                                        </td>
                                        
                                        <td style="display:none">
                                            <a href="#" class="item-index"
                                                onclick="partial_handle({{ $pos_item->id }})"><i
                                                    class="fa fa-undo"></i></a>
                                        </td>
                                        
                                    </tr>
                                    @endforeach
                                    <td></td>
                                </tbody>
                                <tfoot class="bg-danger">
                                    <tr>
                                        <th class="smHidden"></th>
                                        <th class="text-center" colspan="7"><strong>Total: <span id="totalAmount">{{
                                                $estimate->items()->sum('sub_total') }}</span> Tk</strong>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="row d-flex justify-content-center">
                                <div class="form-gorup">
                                <button type="button" id="payment-btn" class="btn btn-primary">
                                    <i class="fa fa-undo"></i>
                                    Sale
                                </button>
                            </div>
                            <div class="form-gorup ml-4">
                                <a href="{{route('estimate.today_delivery')}}" class="btn btn-success">
                                    <i class="fa fa-undo"></i>
                                    Back
                                </a>
                            </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Payment Modal --}}
                    <div class="modal fade" id="payment-modal" tabindex="-1">
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
                                                <strong class="float-left">Paying Items:</strong>
                                                <strong class="float-right">(<span id="items">0</span>)</strong>
                                            </td>
                                            <td>
                                                <strong class="float-left">T.Receivable: </strong>
                                                <strong class="float-right">(<span id="receivable">0</span>Tk)</strong>
                                                <input type="hidden" name="receivable_amount" id="receivable_input">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="50%">
                                                <strong class="float-left">After Discount: </strong>
                                                <strong class="float-right"> (<span id="after_discount">0</span>Tk)</strong>
                                            </td>
                                            <td>
                                                <strong class="float-left">Balance:</strong>
                                                <strong class="float-right"> (<span id="balance">0</span>Tk)</strong>
                                                <input type="hidden" name="balance" id="balance_input">
                                            </td>
                                        </tr>
                                    </table>
                                    <hr>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="discount">Discount</label>
                                            <input type="text" class="form-control" id="discount" name="discount"
                                                placeholder="0%">
                                        </div>
                                        <div class="form-group col-md-6" id="committed_date">
                                            <label for="committed_date">Committed Date<span class="field_required"></span></label>
                                            <input type="date" class="form-control" data-provide="datepicker" data-date-format="yyyy-mm-dd"
                                                   value="{{ date('Y-m-d', strtotime('+1 day')) }}" name="committed_date">
                                        </div>
                                        <div class="form-group col-md-12 smHidden">
                                            <label for="payment_method">Note</label>
                                            <textarea name="note"
                                                class="form-control">{{ $estimate->note ?? '' }}</textarea>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="">Transection Account</label>
                                            <select name="bank_account_id" class="form-control" required>
                                                @foreach (\App\BankAccount::all() as $item)
                                                <option value="{{ $item->id }}" {{ old('bank_account_id')==$item->id ?
                                                    'SELECTED' : '' }}>
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
                                                <input type="number" class="form-control" name="pay_amount"
                                                    id="pay_amount" placeholder="Pay Amount...">
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
                                        Sale
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


<div class="modal fade show" id="partial-confirm-modal" tabindex="-1" aria-modal="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">You want to Return ?</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="partial-delete-form" action="" method="POST">
                @csrf

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No. Back !</button>
                    <button type="submit" class="btn btn-primary">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('styles')
<style>
    hr {
        margin: 5px auto;
    }
    .bg_none {
        background: transparent !important;
        border:none !important;
    }
    @media screen and (max-width: 576px) {
    .table-responsive-sm {
        font-size:12px;
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    label {
        font-size: 12px;
        margin: 0; 
    }
    strong{
            font-size: 12px;
            margin: 0;
    }
    .form-control {
        font-size: 12px;
        margin: 0px;
        padding: 0px
    }
    .form-control {
        font-size: 12px;
        margin: 0px;
        padding: 0px
    }
    .mr-1.ml-1 {
        margin: 0;
    }
    .smHidden{
        display:none;
    }
}
</style>
<link rel="stylesheet" href="{{ asset('dashboard/css/pos.css') }}">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('scripts')
@include('includes.placeholder_model')

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@include('pages.estimate.estimate-scripts')

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
                        if (product.checkSaleOverStock == 0) {
                            if (product.stock <= 0) {
                                toastr.warning(
                                    'This product is Stock out. Please Purchases the Product.'
                                );
                                return false;
                            }
                        }

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
                        if (product.checkSaleOverStock == 0) {
                            if (product.stock <= 0) {
                                toastr.warning(
                                    'This product is Stock out. Please Purchases the Product.'
                                );
                                return false;
                            }
                        }

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


        function partial_handle(id) {
            //alert('HELLO');
            var url = "{{ route('estimate.partial_destroy', 'id') }}".replace('id', id);
            $("#partial-delete-form").attr('action', url);
            $("#partial-confirm-modal").modal('show');
        }
</script>

{{-- <script src="/js/add_customer.js"></script> --}}

<script src="{{ asset('js/modal_form.js') }}"></script>
@endsection