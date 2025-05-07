<?php

namespace App\Http\Controllers;

use App\ActualPayment;
use App\Brand;
use App\Category;
use File;
use App\Product;
use App\Customer;
use Milon\Barcode\DNS1D;
use App\Helpers\InputHelper;
use App\PosItem;
use App\PosSetting;
use App\Services\ProductService;
use App\Services\SupplierService;
use App\Supplier;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    private $file_path;
    private $default_image;

    public function __construct()
    {
        $this->file_path = 'dashboard/images/products/';
        $this->default_image = 'dashboard/images/not-available.png';

        $this->middleware('can:create-product', ['only' => ['create', 'store']]);
        $this->middleware('can:edit-product', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-product', ['only' => ['destroy']]);
        $this->middleware('can:list-product', ['only' => ['index']]);
        // $this->middleware('can:show-brand', ['only' => ['show']]);

        $this->middleware('can:product-sell_history', ['only' => ['sell_history']]);
        $this->middleware('can:product-add_category', ['only' => ['add_category', 'store_category']]);
        $this->middleware('can:product-add_brand', ['only' => ['add_brand', 'store_brand']]);
        // $this->middleware('can:product-barcode', ['only' => ['barcode']]);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = new Product();

        if ($request->code) {
            $products = $products->where('code', $request->code);
        }

        if ($request->name) {
            $products = $products->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->category) {
            $products = $products->where('category_id', $request->category);
        }

        if ($request->brand_id) {
            $products = $products->where('brand_id', $request->brand_id);

        }

        $products = $products->
            // orderBy('name')
            orderBy('id', 'DESC')
            ->paginate(100);
        $customers = Customer::orderBy('name')->get();
        $brands = Supplier::where('status', 1)->select('id', 'name')->get();

        return view('pages.product.index', compact('products', 'customers', 'brands'));
        // ->withProducts($products)
        // ->withCustomers($customers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $unit_exist = Unit::first();
        if (!$unit_exist) {
            session()->flash('error', 'No Unit Exist');
            return back();
        }

        $data['units'] = Unit::all();
        $data['first_unit'] = Unit::first();
        return view('pages.product.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            // 'product_type' => 'required',
            'name' => 'required|max:255',
            'category_id' => 'required',
            'price' => 'required|numeric',
            'cost' => 'required|numeric',
            'damage_price' => 'nullable|numeric',
            'use_file' => 'image|mimes:jpeg,png,jpg|dimensions:min_width=298,min_height=284 ',
            'code' => 'nullable|string|max:191|unique:products',
            'opening_stock' => 'nullable|array',
            'main_unit_id' => 'required',
        ]);

        if ($request->hasFile('use_file')) {
            if (config('pos.app_mode') == 'demo') {
                session()->flash('error', 'Image Upload is disabled in demo.');
                $product_image = $this->default_image;
            } else {
                $product_image = InputHelper::uploadWithCrop($request->use_file, $this->file_path, 298, 284);
            }
        } else {
            $product_image = $this->default_image;
        }

        $product = Product::create([
            // 'type' => $request->product_type,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'cost' => $request->cost,
            // 'tax' => $request->tax,
            'price' => $request->price,
            'damage_price' => $request->damage_price,
            // 'alert_qyt' => $request->alert_qyt,
            'details' => $request->details,
            'image' => $product_image,
            'main_unit_id' => $request->main_unit_id,
            'sub_unit_id' => $request->sub_unit_id,
            // 'opening_stock' => $request->opening_stock!=null? $request->opening_stock:0
        ]);

        if (!$request->code) {
            $product_code = str_pad($product->id, 3, '0', STR_PAD_LEFT);
        } else {
            $product_code = $request->code;
        }

        $product->update([
            'code' => $product_code
        ]);

        if ($request->opening_stock && count($request->opening_stock) > 0) {
            $opening_stock_count = count($request->opening_stock);

            if ($opening_stock_count > 0 && ($request->opening_stock[0] != "" || (array_key_exists(1, $request->opening_stock) && $request->opening_stock[1] != ""))) {
                ProductService::make_opening_stock_purchase($request, $product);
            }
        }

        session()->flash('success', 'Product Stored.');

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $pos_setting = PosSetting::first();
        $product->brand_name = $product->brand ? $product->brand->name : 'No Brand';
        $product->category_name = $product->category ? $product->category->name : 'No Category';
        // $product->stock = $product->stock();
        $product->checkSaleOverStock = $pos_setting->sale_over_sotck;
        return $product;
    }
    public function details(Product $product)
    {
        // $pos_setting = PosSetting::first();
        $product->brand_name = $product->brand ? $product->brand->name : 'No Brand';
        $product->category_name = $product->category ? $product->category->name : 'No Category';
        $product->stock = $product->stock();
        $product->main_unit = $product->main_unit;
        $product->sub_unit = $product->sub_unit;
        // $product->checkSaleOverStock = $pos_setting->sale_over_sotck;
        return $product;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('pages.product.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {

        $this->validate($request, [
            // 'product_type' => 'required',
            'name' => 'required|max:255',
            'category_id' => 'required',
            // 'category_id' => 'required',
            'code' => 'unique:products,code,' . $product->id,
            'price' => 'required',
            'damage_price' => 'nullable|numeric',
            'use_file' => 'image|mimes:jpeg,png,jpg|dimensions:min_width=298,min_height=284'
        ]);

        if ($request->hasFile('use_file')) {
            if (config('pos.app_mode') == 'demo') {
                session()->flash('error', 'Image Upload is disabled in demo.');
            } else {
                // File Delete
                if ($product->image != $this->default_image) {
                    File::delete($product->image);
                }
                $product_image = InputHelper::uploadWithCrop($request->use_file, $this->file_path, 298, 284);
                $product->image = $product_image;
            }
        }

        // $product->type = $request->product_type;
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->cost = $request->cost;
        // $product->tax = $request->tax;
        $product->price = $request->price;
        $product->damage_price = $request->damage_price;
        // $product->alert_qyt = $request->alert_qyt;
        $product->details = $request->details;
        // $product->opening_stock = $request->opening_stock != null ? $request->opening_stock : 0;
        $product->save();

        session()->flash('success', 'Product Updated.');
        return redirect()->route('product.edit', $product->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product->delete()) {
            if ($product->image != $this->default_image) {
                File::delete($product->image);
            }
            session()->flash('success', 'Product Deleted...');
        } else {
            session()->flash('warning', 'Product don\'t delete. please check.');
        }

        return back();
    }

    public function products($category = 0)
    {
        if ($category != 0) {
            $products = Category::find($category)->with('products');
        } else {
            $products = Product::all()->toArray();
        }
        return response()->json($products);
    }

    public function barcode_generate($code)
    {
        // $barcode = '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($code, 'C39', 2, 45) . '" alt="BAR CODE" />';
        // $barcode = (new DNS1D)->getBarcodeSVG($code, 'C39', .9, 40);
        $barcode = (new DNS1D)->getBarcodeSVG($code, 'C128', 1.4, 40);
        return response()->json($barcode);
    }

    public function sell_history(Product $product)
    {
        // dd($product);
        $histories = PosItem::where('product_id', $product->id)->paginate(20);

        // dd($histories);

        return view('pages.product.sell_history', compact('histories'));
    }

    public function categories()
    {
        $query = request('query');
        $categories = Category::select('id', 'name')->where('name', 'LIKE', "%$query%")->get();
        return $categories;
    }

    public function brands()
    {
        $query = request('query');
        $rands = Supplier::where('status', 1)->select('id', 'name')->where('name', 'LIKE', "%$query%")->get();
        return $rands;
    }

    public function add_category()
    {
        return view('pages.product.forms.add_category');
    }

    public function store_category(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }
        // $data["user_id"]=auth()->user()->id;
        Category::create($request->all());
        return response()->json(['success' => 'Added new records.']);
    }

    public function add_brand()
    {
        return view('pages.product.forms.add_brand');
    }

    public function store_brand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }
        Supplier::create([
            'name' => $request->name,
            'slug' => str_slug($request->name)
        ]);
        return response()->json(['success' => 'Added new records.']);
    }
    
    public function statusUpdate(Request $request)
    {
        $product = Product::find($request->id);
        $product->status = $request->status;
        $product->save();
        return response()->json('success');
    }
    
    public function getReadableQuantity($id, $quantity)
    {
        $product = Product::find($id);
        if ($product) {
            return response()->json([
                'readable_quantity' => $product->readable_qty($quantity),
                'stock' => $product->stock
            ]);
        }
        return response()->json(['error' => 'Product not found'], 404);
    }
}