@extends('layouts.master')
@section('title', 'Product List')

@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Products</strong>
        </h1>
    </div>
    <div class="header-action">
        <nav class="nav">
            <a class="nav-link active" href="{{ route('product.index') }}">
                Products
            </a>
            {{-- <a class="nav-link" href="#">Import Products</a> --}}
            <a class="nav-link" href="{{ route('product.create') }}">
                <i class="fa fa-plus"></i>
                Add Product
            </a>
        </nav>
    </div>
</header>
@endsection

@section('content')
<div class="col-12 mt-0">

    <div class="card print_area">
        <div class="card-body">
            <form action="{{ route('product.index') }}">
                <div class="form-row mb-2">
                    {{-- <div class="form-group col-md-3">
                        <input type="text" name="code" class="form-control" placeholder="Product Code" value="{{ request('code') }}">
                    </div> --}}
    
                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" name="name" placeholder="Product Name" value="{{ request('name') }}">
                    </div>
    
                    <div class="form-group col-md-3">
                        <div class="form-group">
                            <select name="category" id="" class="form-control">
                            <option value="">Select Category</option>
                            @foreach (\App\Category::all() as $item)
                                <option value="{{ $item->id }}" {{ request("category")==$item->id?"SELECTED":"" }}>{{ $item->name }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
    
                    <div class="form-group col-md-3">
                      <select name="brand_id" id="" class="form-control">
                          <option value="">Select Brand</option>
                        @foreach ($brands as $item)
                          <option value="{{ $item->id }}" {{ request("brand_id")==$item->id?"SELECTED":"" }}>{{ $item->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    
                    <div class="form-row col-md-3">
                        <div class="form-group float-right">
                            <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-sliders"></i>
                                    Filter
                            </button>
                            <a href="{{ route('product.index') }}" class="btn btn-info">Reset</a>
                        </div>
                    </div>
                </div>
                
            </form>

            @if ($products->count() > 0)
            <div class="">
                {{-- <table class="table table-responsive table-bordered" data-provide="datatables" data-page-length="20"> --}}
                <table class="table table-responsive table-bordered">
                    <thead>
                        <tr class="bg-primary">
                            <th class="text-center">#</th>
                            <th class="text-center">Image</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Price</th>
                            <th>Cost</th>
                            <th>Damage Price</th>
                            <th>Status</th>
                            <th class="text-center print_hidden">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $key => $product)
                        <tr class="{{ $product->status == 0 ? 'bg-warning' : '' }}">
                            <td>{{ isset($_GET['page']) ? ($_GET['page'] - 1) * 20 + $key + 1 : $key + 1 }}</td>
                            <td style="padding:5px" class="text-center">
                                <img src="{{ asset($product->image) }}" width="40" alt="Image">
                            </td>
                            <td>{{ $product->code }} </td>
                            <td style="max-width:120px;">{{ $product->name }}</td>
                            <td>
                                {{ $product->category ? $product->category->name : 'No Category' }}
                            </td>
                            <td>
                                {{ $product->brand ? $product->brand->name : 'No Brand' }}
                            </td>
                            <td>
                                {{ $product->price }} Tk
                            </td>
                            <td>
                                {{ $product->cost }} Tk
                            </td>
                            <td>
                                {{ $product->damage_price }} Tk
                            </td>

                            <td>
                                <select name="status" class="status">
                                    <option data-id="{{$product->id}}" value="1" {{ $product->status=='1'?'SELECTED':''
                                        }}>Active</option>
                                    <option data-id="{{$product->id}}" value="0" {{ $product->status=='0'?'SELECTED':''
                                        }}>Inactive</option>
                                </select>
                            </td>

                            <td class="text-center print_hidden">
                                <button class="btn btn-brown btn-sm" onclick="productView({{ $product->id }})">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <div class="btn-group">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fa fa-cogs"></i>
                                    </button>
                                    <div class="dropdown-menu" x-placement="bottom-start">

                                        <a class="dropdown-item" href="{{ route('product.edit', $product->id) }}">
                                            <i class="fa fa-edit"></i>
                                            Edit
                                        </a>

                                        <a class="dropdown-item"
                                            href="{{ route('product.sell_history', $product->id) }}">
                                            <i class="fa fa-history"></i>
                                            Sell History
                                        </a>

                                        <a class="dropdown-item delete"
                                            href="{{ route('product.destroy', $product->id) }}">
                                            <i class="fa fa-trash"></i>
                                            Delete
                                        </a>
                                    </div>
                                </div>
                                <button class="btn btn-light btn-sm generated_barcode" data-name="{{ $product->name }}"
                                    data-code="{{ $product->code }}" data-price="{{ $product->price }}">
                                    <i class="fa fa-barcode"></i>
                                </button>

                                <button class="btn btn-light btn-sm" data-toggle="modal"
                                    data-target="#qr_code_{{ $product->id }}"><i class="fa fa-qrcode"></i></button>
                                <div class="modal fade" id="qr_code_{{ $product->id }}" role="modal" aria-modal="true">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <div class="text-center p-4" id="qrcode">
                                                    <img src="data:image/png;base64,{{ \DNS2D::getBarcodePNG($product->code, 'QRCODE', 30, 30) }}"
                                                        alt="QR CODE" />
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" onclick="print_qrcode()" class="btn btn-primary">
                                                    <i class="fa fa-print"></i>
                                                    Print
                                                </button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {!! $products->appends(Request::except('_token'))->links() !!}

            </div>
            @else
            <div class="alert alert-danger" role="alert">
                <strong>You have no Products</strong>
            </div>
            @endif
        </div>
    </div>
</div>


{{-- View Modal --}}
<div class="modal fade" id="product_details" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="product_title">Product Title</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="p-3">
                            <img src="#" id="image" width="120" class="p_img" alt="">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td>Code</td>
                                        <td id="code"></td>
                                    </tr>
                                    <tr>
                                        <td>Category</td>
                                        <td id="category"></td>
                                    </tr>
                                    <tr>
                                        <td>Brand</td>
                                        <td id="brand"></td>
                                    </tr>
                                    <tr>
                                        <td>Price</td>
                                        <td id="price"></td>
                                    </tr>
                                    <tr>
                                        <td>Cost</td>
                                        <td id="cost"></td>
                                    </tr>
                                    <tr>
                                        <td>Stock</td>
                                        <td id="stock"></td>
                                    </tr>
                                    <tr>
                                        <td>Details</td>
                                        <td id="details"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-bold btn-pure btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


{{-- End Modal --}}
@endsection

@section('styles')
<style>
    .table>p {
        font-size: 19px;
        padding-top: 5px;
        letter-spacing: 4px;
        margin-bottom: 0px;
    }

    .p_img {
        border: 1px solid rgb(0, 0, 0);
        padding: 5px;
    }

    @media print {
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .header h1,
        .header a,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        .product,
        .shopping-cart p,
        footer p,
        .select2-container--default .select2-selection--single .select2-selection__rendered,
        strong,
        span {
            color: black !important;
        }

        #barcode-page {}

        #barcode table {
            page-break-after: always
        }
    }
</style>
@endsection

@section('scripts')
@include('pages.product.includes.barcode', ['pos_setting' => $pos_setting])
<script>
    // Product View Handle
        function productView(productId) {
            let url = "{{ route('product.details', 'placeholder_id') }}".replace('placeholder_id', productId);
            $.get(url, (data) => {
                $("#product_title").text(data.name);
                $("#code").text(data.code);
                $("#ptype").text(data.type);
                $("#category").text(data.category_name);
                $("#brand").text(data.brand_name);
                $("#price").text(data.price);
                $("#cost").text(data.cost);
                $("#tax").text(data.tax);
                $("#stock").text(data.stock);
                $("#details").html(data.details);
                $("#image").attr('src', "{{ asset('link') }}".replace('link', data.image));
            });
            $("#product_details").modal('show');
        }
        // Print QR Code
        function print_qrcode(doc) {
        }
</script>

<script>
    $(document).ready(function(){
      $('.status').on('change', function(){
        var status = $(this).val();
        var id = $(this).find(':selected').data('id');
        var url="{{ route('product.status') }}";
        $.ajax({
          url: url,
          type: 'POST',
          data: {
            status: status,
            id: id,
            _token: '{{ csrf_token() }}',
          },
          success: function(data){
            console.log(data);
            toastr.success('Status Updated Successfully');
          }
        });
      });
    });
</script>
@include('includes.delete-alert')
@endsection