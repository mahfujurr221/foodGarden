<div class="card">
    <div class="row">
        <div class="col-md-12">
            <div class="card-header">
                <h3 class="card-title">Product List</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-2">
                        <form action="{{ route('pos.products') }}" class="product-filter">
                            <div class="row justify-content-center">
                                <div class="col-md-6 col-sm-6 col-6">
                                    <input type="text" name="code" class="form-control code">
                                </div>
                                <div class="col-md-3 col-sm-3 col-3">
                                    <input type="submit" class="btn search_btn" value="Search">
                                </div>
                                {{-- <div class="col-md-2 ">
                                    <a href="{{ route('pos.create') }}" class="btn btn-danger">Reset</a>
                                </div> --}}
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            @php
                            if (
                            auth()
                            ->user()
                            ->hasRole('admin')
                            ) {
                            $brands = \App\Brand::orderBy('name', 'ASC')->get();
                            } else {
                            $brands = \App\Brand::whereIn('id', json_decode(auth()->user()->brand_id))
                            ->orderBy('name', 'ASC')
                            ->get();
                            }
                            @endphp
                            @foreach ($brands as $item)
                            @php
                            $name = $item->name;
                            $words = explode(' ', $name); // Split the string into an array of words
                            $brandName = $words[0];
                            @endphp
                            <div class="col-md-2 col-sm-4 col-4">
                                <a href="#" class="btn btn-block brand_btn"
                                    onclick="getProductsByCat({{ $item->id }})">{{ $brandName }}</a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center" style="max-height: 400px; overflow-y: scroll;">
                    @forelse($products as $product)
                    <div class="product text-center col-md-2 col-sm-5 col-5" data-value="{{ $product->id }}">
                        <img src="/{{ $product->image }}" class="align-self-start img-thumbnail"
                            alt="{{ $product->name }}" />
                        <span>{{ $product->name}}</span><br>
                        <span>{{ number_format($product->cost) }}/-</span><br>
                        <span>{{ $product->readable_qty($product->stock)}}</span>
                    </div>
                    @empty
                    <div class="alert alert-danger" role="alert">
                        Products not available! Please add.
                    </div>
                    @endforelse
                </div>
                {{-- {!! $products->appends(request()->query())->links() !!} --}}
            </div>
        </div>
    </div>
</div>