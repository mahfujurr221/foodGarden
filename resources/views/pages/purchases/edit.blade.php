@extends('layouts.master')
@section('title', 'Create Purchase')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Purchase</strong>
            </h1>
        </div>

        <div class="header-action">
            <nav class="nav">
                <a class="nav-link" href="{{ route('purchase.index') }}">
                    Purchases
                </a>
                <a class="nav-link" href="{{ route('purchase.create') }}">
                    <i class="fa fa-plus"></i>
                    Add Purchase
                </a>
                @if (Request::RouteIs('purchase.edit', []))
                    <a class="nav-link active" href="#">
                        <i class="fa fa-edit"></i>
                        Edit Purchase
                    </a>
                @endif
            </nav>
        </div>

    </header>
@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <h4 class="card-title">Update Purchase</h4>

            <form action="{{ route('purchase.update', $purchase->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="supplier">Supplier</label>
                            <select name="supplier_id" id="supplier" data-provide="selectpicker" data-live-search="true"
                                class="form-control {{ $errors->has('supplier_id') ? 'is-invalid' : '' }}">
                                <option value="">Select Supplier</option>
                                @foreach ($suppliers as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $purchase->supplier->id == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('supplier_id'))
                                <span class="invalid-feedback">{{ $errors->first('supplier_id') }}</span>
                            @endif
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Purchase Date</label>
                            <input type="text"
                                class="form-control {{ $errors->has('purchase_date') ? 'is-invalid' : '' }} date"
                                data-provide="datepicker" name="purchase_date" data-date-today-highlight="true"
                                data-date-format="yyyy-mm-dd" value="{{ $purchase->purchase_date }}">
                            @if ($errors->has('purchase_date'))
                                <span class="invalid-feedback">{{ $errors->first('purchase_date') }}</span>
                            @endif
                        </div>

                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="Product">Product</label>
                            <input type="text" id="product_search" class="form-control" placeholder="Write product.">
                        </div>
                        {{-- <div class="form-group col-md-6">
                            <label for="">Carrying Cost</label>
                            <input type="text" name="carrying_cost"
                                   value="{{ old("carrying_cost") ?? $purchase->carrying_cost }}"
                                   class="form-control" placeholder="Carrying Cost">
                            @if ($errors->has('carrying_cost'))
                                <div class="alert alert-danger">{{ $errors->first('carrying_cost') }}</div>
                            @endif
                        </div> --}}
                    </div>
                    <hr>

                    <div class="row">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-primary">
                                    <th style="width:80px">#SL</th>
                                    <th>Product</th>
                                    <th style="width:150px;">Rate</th>
                                    <th style="width:320px;">Qty</th>
                                    <th style="width:150px;">Discount Qty</th>
                                    <th>Sub Total</th>
                                    <th style="width:50px">
                                        <i class="fa fa-trash"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="table_body">
                                @foreach ($purchase->items as $key => $item)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>
                                            {{ $item->product->name . ' - ' . $item->product->code }}
                                            <input type="hidden" value="{{ $item->product->id }}"
                                                name="product[{{ $item->id }}]" class="product">
                                            <input type="hidden" value="{{ $item->id }}"
                                                name="purchase_item_id[{{ $item->id }}]">
                                        </td>
                                        <td>
                                            <input type="text" value="{{ $item->rate }}" class="form-control rate"
                                                name="rate[{{ $item->id }}]">
                                        </td>
                                        <td>
                                            <div class="form-row">
                                                @php
                                                    $product = $item->product;
                                                @endphp
                                                @if ($product->sub_unit == null)
                                                    <input type="text" class="has_sub_unit" hidden value="false">
                                                    <label class="ml-4 mr-2">{{ $product->main_unit->name }}:</label>
                                                    <input type="number" value="{{ $item->main_unit_qty }}"
                                                        class="form-control col main_qty"
                                                        name="main_qty[{{ $item->id }}]"
                                                        onkeydown="return event.keyCode !== 190" min="1">
                                                @else
                                                    <input type="text" class="has_sub_unit" hidden value="true">
                                                    <input type="text" class="conversion" hidden
                                                        value="{{ $product->main_unit->related_by }}">
                                                    <label class="mr-2 ml-4">{{ $product->main_unit->name }}:</label>
                                                    <input type="number" value="{{ $item->main_unit_qty }}"
                                                        class="form-control col main_qty mr-4"
                                                        name="main_qty[{{ $item->id }}]"
                                                        onkeydown="return event.keyCode !== 190" min="1">
                                                    <label class="mr-2">{{ $product->sub_unit->name }}:</label>
                                                    <input type="number" value="{{ $item->sub_unit_qty }}"
                                                        class="form-control col sub_qty"
                                                        name="sub_qty[{{ $item->id }}]"
                                                        onkeydown="return event.keyCode !== 190" min="1">
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                name="discount_qty[{{ $item->id }}]"
                                                value="{{ $item->discount_qty }}">
                                        </td>
                                        <td>
                                            <strong><span class="sub_total">{{ $item->sub_total }}</span> Tk</strong>
                                            <input type="hidden" name="subtotal_input[{{ $item->id }}]"
                                                class="subtotal_input" value="{{ $item->sub_total }}">
                                        </td>
                                        <td>
                                            <a href="#" class="item-index"
                                                onclick="partial_handle({{ $item->id }})"><i
                                                    class="fa fa-undo"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="4"></td>

                                    <td colspan="2">
                                        <strong>Grand Total: <span
                                                id="total">{{ (float) $purchase->items()->sum('sub_total') }}</span>
                                            Tk</strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12 mt-4">
                            <button type="submit" id="payment_btn" class="btn btn-primary mx-auto">
                                <i class="fa fa-save"></i>
                                Update
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade show" id="partial-confirm-modal" tabindex="-1" aria-modal="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">You want to Delete ?</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="partial-delete-form" action="" method="POST">
                    @csrf
                    {{--                    @method('DELETE') --}}
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
        .table tr td {
            text-align: center;
            vertical-align: baseline;
            padding: 4px;
        }

        .table tr th {
            text-align: center;
            padding: 5px;
        }

        .table tr td input {
            text-align: center;
        }

        .header {
            margin-bottom: 10px;
        }

        .main-content {
            padding-top: 10px;
        }
    </style>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>
        $(document).ready(function() {
            getPurchaseItemId({{ $purchase->id }});
        });
    </script>

    @include('pages.purchases.script')
    <script>
        function partial_handle(id) {
            var url = "{{ route('purchase.partial_destroy', 'id') }}".replace('id', id);
            $("#partial-delete-form").attr('action', url);
            $("#partial-confirm-modal").modal('show');
        }
    </script>
@endsection
