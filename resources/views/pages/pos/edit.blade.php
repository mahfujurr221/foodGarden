@extends('layouts.master')
@section('title', 'Sale Update')
@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Sale Update</strong>
        </h1>
    </div>
</header>
@endsection
@section('content')
<div class="col-md-12">
    <div class="row">
        <div class="col-md-12">`
            <div class="form-row mb-3">
                <div class="col-md-12">
                    <input type="text" id="product_search" class="form-control"
                        placeholder="Start to write product name..." name="p_name" />
                    <input type="hidden" id="search_product_id">
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card card-body">
                {{-- <form action="{{ route('pos.update', $pos) }}" id="sale-manage-form" method="POST"> --}}
                    <form action="#" id="sale-manage-form">
                        {{-- @csrf
                        @method('PUT') --}}
                        <div class="row">
                            <div class="col-md-6">
                                <label class="ml-2 mr-2" style="color:black">Sale Date:</label>
                                <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                                    data-date-format="yyyy-mm-dd" class="form-control" value="{{ $pos->sale_date }}"
                                    name="sale_date">
                                <input type="hidden" name="pos_id" value="{{ $pos->id }}">
                            </div>
                            <div class="col-md-6">
                                <label class="ml-2 mr-2" style="color:black">Sale Note:</label>
                                <textarea class="form-control" name="note">{{ $pos->note }}</textarea>
                            </div>
                            <div class="col-md-12 mt-4">
                                <div class="col-md-12 col-12 col-sm-12">
                                    <table class="table table-bordered">
                                        <thead class="bg-primary">
                                            <tr>
                                                <th>Name</th>
                                                <th>Quantity</th>
                                                {{-- <th>Dis.Qty</th>
                                                <th>Discount Return</th> --}}
                                                <th>Product.Return</th>
                                                {{-- <th>Return value</th> --}}
                                                <th>Damage</th>
                                                <th>Damage Tk</th>
                                                <th>Price</th>
                                                <th>Sub Total</th>
                                                {{-- <th>Final Qty</th> --}}
                                                {{-- <th>
                                                    <i class="fa fa-undo"></i>
                                                </th> --}}
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            @foreach ($pos->items as $pos_item)
                                            <tr>
                                                <td style="background-color:#ece9e9; width:150px;">
                                                    <strong>{{ $pos_item->product->name }} </strong>
                                                    <input type="hidden" value="{{ $pos_item->product_name }}"
                                                        name="name[]">
                                                    <input type="hidden" value="{{ $pos_item->product->id }}"
                                                        name="old_product_id[]">
                                                    <input type="hidden" value="{{ $pos_item->estimate_id }}"
                                                        name="estimate_id">
                                                    <input type="hidden" value="{{ $pos_item->id }}"
                                                        name="old_id[{{ $pos_item->id }}]">
                                                </td>
                                                <td style="width:155px; background-color: #ece9e9;text-align:center">
                                                    <div class="form-row">
                                                        @php
                                                        $product = $pos_item->product;
                                                        @endphp
                                                        @if (!$pos_item->product->sub_unit)
                                                        {{-- ONLY MAIN UNIT --}}
                                                        <input type="text" class="has_sub_unit" hidden value="false">
                                                        <label class="ml-2 mr-2">{{ $product->main_unit->name
                                                            }}:</label>

                                                        <input type="number" value="{{ $pos_item->main_unit_qty }}"
                                                            class="form-control col main_qty"
                                                            name="old_main_qty[{{ $pos_item->id }}]"
                                                            data-old="{{ $pos_item->main_unit_qty }}"
                                                            data-value="{{ $product->stock() }}"
                                                            data-related="{{ $product->main_unit->related_by }}"
                                                            onkeydown="return event.keyCode !== 190" min="1" readonly>
                                                        @else
                                                        {{-- HAS SUB UNIT --}}
                                                        <input type="text" class="has_sub_unit" hidden value="true">
                                                        <input type="text" class="conversion" hidden
                                                            value="{{ $product->main_unit->related_by }}">

                                                        <label class="ml-3 mt-1"><strong>{{$pos_item->main_unit_qty}} {{
                                                                $product->main_unit->name }}</strong></label>
                                                        <input hidden type="number"
                                                            value="{{ $pos_item->main_unit_qty }}" class=" col main_qty"
                                                            name="old_main_qty[{{ $pos_item->id }}]"
                                                            data-old="{{ $pos_item->main_unit_qty }}"
                                                            data-value="{{ $product->stock() }}"
                                                            data-related="{{ $product->main_unit->related_by }}"
                                                            onkeydown="return event.keyCode !== 190" min="1" readonly
                                                            style="border:none !important">

                                                        <label class="ml-1 mt-1"><strong>{{ $pos_item->sub_unit_qty??'0'
                                                                }} {{
                                                                $product->sub_unit->name }}</strong></label>
                                                        <input hidden type="number"
                                                            value="{{ $pos_item->sub_unit_qty }}"
                                                            class="form-control col sub_qty mr-1"
                                                            name="old_sub_qty[{{ $pos_item->id }}]"
                                                            data-old="{{ $pos_item->sub_unit_qty }}"
                                                            onkeydown="return event.keyCode !== 190" min="1"
                                                            max="{{ $product->main_unit->related_by - 1 }}" readonly>
                                                        @endif
                                                    </div>
                                                </td>
                                                {{-- <td style="width:100px"> --}}
                                                    <input hidden type="number" value="{{ $pos_item->discount_qty }}"
                                                        class="form-control discount_qty"
                                                        name="old_discount_qty[{{ $pos_item->id }}]"
                                                        data-discount_qty="{{ $pos_item->discount_qty }}"
                                                        onkeydown="return event.keyCode !== 190" min="0" readonly>
                                                    {{--
                                                </td> --}}
                                                {{-- <td style="width:100px"> --}}
                                                    <input hidden type="number" value="{{ $pos_item->discount_return }}"
                                                        class="form-control discount_return"
                                                        name="old_discount_return[{{ $pos_item->id }}]"
                                                        data-old_discount_return="{{ $pos_item->discount_return }}"
                                                        onkeydown="return event.keyCode !== 190" min="0">
                                                    {{--
                                                </td> --}}
                                                <td class="returnField" style="width:350px">
                                                    <div class="form-row">
                                                        @if (!$pos_item->product->sub_unit)
                                                        {{-- ONLY MAIN UNIT --}}
                                                        <input type="text" class="has_sub_unit" hidden value="false">
                                                        <label class="ml-2 mr-2">{{ $product->main_unit->name }}</label>
                                                        <input type="number" value="{{ $pos_item->returned }}"
                                                            class="form-control col returned"
                                                            name="old_returned[{{ $pos_item->id }}]"
                                                            data-old="{{ $pos_item->returned }}"
                                                            data-value="{{ $product->stock() }}"
                                                            data-related="{{ $product->main_unit->related_by }}"
                                                            onkeydown="return event.keyCode !== 190" min="1">

                                                        <input type="number" value="0"
                                                            class="form-control col returned_sub_unit mr-1"
                                                            name="old_returned_sub_unit[{{ $pos_item->id }}]"
                                                            data-old="{{ $pos_item->returned_sub_unit }}"
                                                            onkeydown="return event.keyCode !== 190" min="1"
                                                            max="{{ $product->main_unit->related_by - 1 }}" hidden>
                                                        @else
                                                        {{-- HAS SUB UNIT --}}
                                                        <input type="text" class="has_sub_unit" hidden value="true">
                                                        <input type="text" class="conversion" hidden
                                                            value="{{ $product->main_unit->related_by }}">
                                                        <label class="mr-1 ml-1">{{ $product->main_unit->name }}</label>
                                                        <input type="number" value="{{ $pos_item->returned }}"
                                                            class="form-control col returned mr-1"
                                                            name="old_returned[{{ $pos_item->id }}]"
                                                            data-old="{{ $pos_item->returned }}"
                                                            data-value="{{ $product->stock() }}"
                                                            data-related="{{ $product->main_unit->related_by }}"
                                                            onkeydown="return event.keyCode !== 190" min="1">
                                                        <label class="mr-1">{{ $product->sub_unit->name }}</label>
                                                        <input type="number" value="{{ $pos_item->returned_sub_unit }}"
                                                            class="form-control col returned_sub_unit mr-1"
                                                            name="old_returned_sub_unit[{{ $pos_item->id }}]"
                                                            data-old="{{ $pos_item->returned_sub_unit }}"
                                                            onkeydown="return event.keyCode !== 190" min="1"
                                                            max="{{ $product->main_unit->related_by - 1 }}">
                                                        @endif
                                                    </div>
                                                </td>
                                                <td style="width:100px;display:none">
                                                    <input type="text" value="{{ $pos_item->returned_value }}"
                                                        class="form-control returnValue"
                                                        name="old_returned_value[{{ $pos_item->id }}]" id="returnValue"
                                                        readonly>
                                                </td>
                                                <td style="width:100px">
                                                    <input type="number" value="{{ $pos_item->damage }}"
                                                        class="form-control damage"
                                                        name="old_damage[{{ $pos_item->id }}]"
                                                        data-damage="{{ $pos_item->damage }}"
                                                        onkeydown="return event.keyCode !== 190" min="0">
                                                </td>
                                                <td style="width:100px">
                                                    <input type="number" value="{{ $pos_item->damaged_value }}"
                                                        class="form-control"
                                                        name="old_damaged_value[{{ $pos_item->id }}]" id="damageValue">
                                                </td>
                                                <td style="width:100px;background-color:#ece9e9">
                                                    <input type="text" value="{{ $pos_item->rate }}"
                                                        class="form-control rate bg_none"
                                                        name="old_rate[{{ $pos_item->id }}]" readonly />
                                                </td>
                                                {{-- <td style="width:100px">
                                                    <input type="text" value="{{ $pos_item->item_discount }}"
                                                        class="form-control item_discount"
                                                        name="old_item_dis[{{$pos_item->id }}]" />
                                                    <input type="hidden" value="{{ $pos_item->discount }}"
                                                        class="form-control discount"
                                                        name="old_dis[{{$pos_item->id }}]" />
                                                </td> --}}
                                                <td style="width:150px;background-color:#ece9e9">
                                                    <input type="text" name="old_sub_total[{{ $pos_item->id }}]"
                                                        class="form-control sub_total bg_none"
                                                        value="{{ $pos_item->sub_total }}" readonly />

                                                    <span class="sub_total_placeholder" hidden>{{ $pos_item->sub_total +
                                                        $pos_item->discount }}</span>
                                                </td>
                                                <td class="d-none">
                                                    <input type="text" name="subtotal_holder[{{ $pos_item->id }}]"
                                                        class="form-control subtotal_holder"
                                                        value="{{ $pos_item->ordered_sub_total }}" hidden />
                                                </td>
                                                {{-- <td>
                                                    <a href="#" class="item-index"
                                                        onclick="partial_handle({{ $pos_item->id }})"><i
                                                            class="fa fa-undo"></i></a>
                                                </td> --}}
                                            </tr>
                                            @endforeach
                                            <td></td>
                                        </tbody>
                                        <tfoot class="bg-danger">
                                            <tr>
                                                {{-- <th class="text-center totalQty" colspan="4"> --}}
                                                <th class="text-center" colspan="4">
                                                    {{-- <strong>{{ $pos->items()->sum('main_unit_qty') }}</strong> --}}
                                                </th>
                                                <th class="text-center d-none">Total: <strong>{{
                                                        number_format($pos->final_receivable) }}</strong>Tk
                                                    <input type="hidden" id="total_input_Amount" value=""
                                                        name="estimate_receivable">
                                                <th class="text-center" colspan="4">Total: <strong id="totalAmount">{{
                                                        number_format($pos->final_receivable) }}</strong>Tk
                                                </th>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div class="form-gorup text-center">
                                        <button type="submit" id="submit-btn" class="btn btn-primary">
                                            <i class="fa fa-undo"></i>
                                            Update
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
</style>
<link rel="stylesheet" href="{{ asset('dashboard/css/pos.css') }}">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@include('pages.estimate.edit-scripts')
<script>
    var db_pid_array;
        $(document).ready(function() {
            getPosItemId({{ $pos->id }});
            // console.log(db_pid_array);
        });
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
                                value: item.name,
                                price: item.price
                            }
                        })); // end res
                    });
                },
                select: function(event, ui) {
                    // alert(ui.item.id);
                    if (db_pid_array.includes(ui.item.id)) {
                        //SHOW ERROR
                        toastr.warning('Please Increase the quantity.');
                        return;
                    }
                    // return;
                    $(this).val(ui.item.value);
                    $("#search_product_id").val(ui.item.id);
                    let url = "{{ route('product.details', 'placeholder_id') }}".replace(
                        'placeholder_id', ui.item.id);
                    $.get(url, (product) => {
                        // check stock
                        if (product.checkSaleOverStock == 0) {
                            if (product.stock <= 0) {
                                toastr.warning(
                                    'This product is Stock out. Please Purchase the Product.'
                                );
                                return false;
                            }
                        }
                        if (pExist(product.id) == true) {
                            toastr.warning('Please Increase the quantity.');
                        } else {
                            addProductToCard(product);
                        }
                        // addProductToCard(pos_product);
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
            var url = "{{ route('estimate.partial_destroy', 'partial_id') }}".replace('partial_id', id);
            $("#partial-delete-form").attr('action', url);
            $("#partial-confirm-modal").modal('show');
        }
        $(document).on('keyup', '.sub_total', function(e) {
            var sub_total = $(this).val();
            var total = 0;
            var qty = $(this).parents('tr').find('.main_qty').val();
            var rate = parseFloat(sub_total / qty);
            rate = rate.toFixed(3);
            $(this).parents('tr').find('.rate').val(rate);
            $('.sub_total').each(function() {
                total += parseFloat($(this).val());
            });
            $('#totalAmount').text(total.toFixed(2));
        });
        //on keyup return 
        $(document).on('keyup', '.returned', function(e) {
            var returned = $(this).val();
            var damage_value = $(this).parents('tr').find('#damageValue').val();
            var rate = $(this).parents('tr').find('.rate').val();
            var input_subtotal = $(this).parents('tr').find('.subtotal_holder').val();
            var return_value = parseFloat(returned * rate);
            damage_value = parseFloat(damage_value);
            var sub_total = parseFloat((input_subtotal - return_value) - damage_value);
            $(this).parents('tr').find('#returnValue').val(return_value.toFixed(2));
            $(this).parents('tr').find('.sub_total').val(sub_total.toFixed(2));
        });
        //on keyup #returnValue
        $('#returnValue').on('keyup', function(e) {
            var return_value = $(this).val();
            var damageValue = $(this).parents('tr').find('#damageValue').val();
            var input_subtotal = $(this).parents('tr').find('.subtotal_holder').val();
            return_value = parseFloat(return_value);
            damageValue = parseFloat(damageValue);
            var sub_total = parseFloat(input_subtotal - return_value - damageValue);
            $(this).parents('tr').find('.sub_total').val(sub_total.toFixed(2));
        });
        //on keyup damage value
        $(document).on('keyup', '.damage', function(e) {
            var damage = $(this).val();
            var return_value = $(this).parents('tr').find('#returnValue').val();
            var rate = $(this).parents('tr').find('.rate').val();
            var input_subtotal = $(this).parents('tr').find('.subtotal_holder').val();
            var damage_value = parseFloat(damage * rate);
            return_value = parseFloat(return_value);
            var sub_total = parseFloat((input_subtotal - return_value) - damage_value);
            $(this).parents('tr').find('#damageValue').val(damage_value.toFixed(2));
            $(this).parents('tr').find('.sub_total').val(sub_total.toFixed(2));
        });
        //on keyup #damageValue
        $(document).on('keyup', '#damageValue', function(e) {
            var damage_value = $(this).val();
            var return_value = $(this).parents('tr').find('#returnValue').val();
            var input_subtotal = $(this).parents('tr').find('.subtotal_holder').val();
            damage_value = parseFloat(damage_value);
            return_value = parseFloat(return_value);
            var sub_total = parseFloat(input_subtotal - return_value - damage_value);
            $(this).parents('tr').find('.sub_total').val(sub_total.toFixed(3));
        });

        //onclick #submit-btn show an warning toaster 
        $('#submit-btn').on('click', function(e) {
            e.preventDefault();
            toastr.warning('Edit Is Under Construction.');
        });
</script>
@endsection