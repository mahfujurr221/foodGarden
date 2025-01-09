@extends('layouts.master')
@section('title', 'Delivery List')
@section('page-header')
<div class="header-info mb-1">
    <h1 class="header-title">
        <strong>Todays Delivery List</strong>
    </h1>
</div>
@endsection
@section('content')
<div class="card print_area" style="width:100%;">
    <div class="card-body">
        @if ($estimates->count() > 0)
        <table class="table table-responsive table-bordered table-striped" data-provide="datatables"
            data-page-length="100">
            <thead>
                <tr class="bg-primary">
                    <th>#</th>
                    <th>Shop.Name</th>
                    <th>Address</th>
                    <th>Brand</th>
                    <th>Delivery By</th>
                    {{-- <th style="min-width:150px">Product.name</th>
                    <th style="min-width:120px">Quantity</th>
                    @can('pos-profit')
                    <th>Price</th>
                    @endcan --}}
                    <th>Order.Amount</th>
                    <th>Status</th>
                    <th class="print_hidden">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($estimates as $key => $estimate)
                <tr>
                    <td>
                        {{ isset($_GET['page']) ? ($_GET['page'] - 1) * $estimates->count() + $key + 1 : $key +
                        1 }}
                        <i class="ml-2 fa fa-fw fa-eye bg-success print_hidden" id="showCustomerIcon"
                            style="cursor:pointer" data-name="{{ $estimate->customer->name??'No Name Given' }}"
                            data-phone="{{ $estimate->customer->phone?? 'No Phone Given' }}"
                            data-note="{{ $estimate->customer->note?? 'No Note given'}}"
                            data-busigness_category="{{ $estimate->customer->business_category->name??'No Busigness Category Given'}}"></i>
                    </td>

                    <td>
                        {{ $estimate->customer ? $estimate->customer->shop_name_bangla : 'Walk-in Customer' }}
                    </td>
                    <td>
                        {{ $estimate->customer ? $estimate->customer->address->name : 'Walk-in Address' }}
                    </td>
                    <td>{{ @$estimate->items()->first()->product->brand->name }}
                        <button class="btn btn-sm btn-primary btn-toggle pull-right" data-toggle="modal"
                        data-target="#itemsModal" data-items='@json($estimate->items)'
                        style="padding: 0px 5px;">
                        <i class="fa fa-eye"></i>
                    </button>
                    </td>
                    
                    <td>
                        {{ $estimate->deliveryMan->fname??'-' }}
                    </td>

                    <td>{{ round($estimate->receivable) }}/-</td>

                    <td>
                        @if ($estimate->status == '1')
                        <span style="padding: 5px 5px" class="badge badge-success">Delivered</span>
                        @else
                        <span style="padding: 5px 5px" class="badge badge-warning">Pending</span>
                        @endif
                    </td>

                    <td class="print_hidden">
                        <div class="btn-group">
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fa fa-cogs"></i>
                            </button>
                            <div class="dropdown-menu" x-placement="bottom-start">
                                @can('show-estimate')
                                <a class="dropdown-item" href="{{ route('estimate.show', $estimate->id) }}">
                                    <i class="fa fa-print text-primary"></i>
                                    Print
                                </a>
                                @endcan
                                    
                                @if ($estimate->convert_status != '1')
                                <a class="dropdown-item" href="{{ route('convert.invoice', $estimate->id) }}">
                                    <i class="fa fa-print text-primary"></i>
                                    Sale
                                </a>
                                @can('edit-estimate')
                                <a class="dropdown-item" href="{{ route('estimate.edit', $estimate->id) }}">
                                    <i class="fa fa-pencil-square-o text-warning"></i>
                                    Edit
                                </a>
                                @endif
                                @endcan
                                @if($estimate->status == '0')
                                <a class="dropdown-item"
                                    href="{{ route('estimate.delivery_complete', $estimate->id) }}">
                                    <i class="fa fa-check text-success"></i>
                                    Delivery
                                </a>
                                @endif
                            </div>
                        </div>
                    </td>

                </tr>
                @endforeach
            </tbody>

        </table>
        @else
        <div class="alert alert-danger text-center" role="alert">
            <strong>You have no Order</strong>
        </div>
        @endif
    </div>
</div>
{{-- show modal --}}
<div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="showModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Customer Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Product Details Modal -->
<div class="modal fade" id="itemsModal" tabindex="-1" role="dialog" aria-labelledby="itemsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="itemsModalLabel">Product Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modalItemsContent">

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    .top-summary td {
        width: 12.5%;
        font-size: 1.5em;
        vertical-align: middle !important;
    }

    .table td,
    .table th {
        padding: 7px;
        vertical-align: baseline;
        border-top: 1px solid #e9ecef;
        text-align: center;
    }

    .card {
        margin-bottom: 0px;
    }

    .card-body {
        padding: 15px;
    }

    .center-cell-text {
        text-align: center;
        vertical-align: middle;
    }

    .table-cell {
        display: table-cell;
        min-height: 126px;
    }

    .product-list li {
        text-align: left;
    }

    /* .dataTables_wrapper .buttons-csv,.buttons-excel,.buttons-pdf{
        display: none !important;
        margin-top: 100px;
    } */

    @media screen and (max-width: 576px) {
        .smHidden {
            display: none;
        }
    }
</style>
@endsection

@section('scripts')
@include('includes.delete-alert')
@include('includes.placeholder_model')
<script src="{{ asset('js/modal_form.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#itemsModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var items = button.data('items');
            console.log(items);
            var modal = $(this);
            var modalContent = modal.find('#modalItemsContent');

            // Clear any previous content
            modalContent.html('');

            // Start the table structure
            var tableHtml = `
                <table class="table table-bordered table-striped">
                    <thead class="bg-primary">
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            @can('pos-profit')
                            <th>Price</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
            `;

            // Populate the table rows with item data
            items.forEach(function(item) {
                var rowClass = item.product_stock < item.qty ? 'bg-danger' : '';
                tableHtml += `
                    <tr class="${rowClass}">
                        <td>${item.product.name}</td>
                        <td>${item.readable_quantity}</td>
                        @can('pos-profit')
                        <td>${item.rate}</td>
                        @endcan
                    </tr>
                `;
            });

            // Close the table structure
            tableHtml += `
                    </tbody>
                </table>
            `;

            modalContent.append(tableHtml);
        });
    });
</script>

<script>
    $(document).on('click', '#show_more', function(){
    var saleDetailsId = $(this).data('saledetailsid');
    var detailsSale = $('#detailsSale-'+saleDetailsId);

    if (detailsSale.hasClass('d-none')) {
        detailsSale.fadeIn('slow').removeClass('d-none');
    } else {
        detailsSale.addClass('d-none');
    }
    });

    //onclick showCustomerIcon
    $(document).on('click', '#showCustomerIcon', function() {
        var name = $(this).data('name');
        var phone = $(this).data('phone');
        var note = $(this).data('note');
        var busigness_category = $(this).data('busigness_category');
        $('#showModal').modal('show');
        $('#showModal').find('.modal-body').html(`
            <div class="row">
                <div class="col-md-5 col-sm-5 col-5 text-right"><strong>Name :</strong></div>
                <div class="col-md-6 col-sm-6 col-6">${name}</div>
                <div class="col-md-5 col-sm-5 col-5 text-right"><strong>Category :</strong></div>
                <div class="col-md-6 col-sm-6 col-6">${busigness_category}</div>
                <div class="col-md-5 col-sm-5 col-5 text-right"><strong>Phone :</strong></div>
                <div class="col-md-6 col-sm-6 col-6">${phone}</div>
                <div class="col-md-5 col-sm-5 col-5 text-right"><strong>Note :</strong></div>
                <div class="col-md-6 col-sm-6 col-6">${note}</div>
            </div>
        `);
    });
</script>

@endsection