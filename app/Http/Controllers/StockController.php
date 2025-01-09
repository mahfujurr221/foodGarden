<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:stock', ['only' => ['index']]);

    }

    public function paginate($items, $perPage = 20, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, ['path' => Paginator::resolveCurrentPath()]);
    }

    public function index(Request $request)
    {

        $products = new Product;
        $data = [];
        // if ($request->product_id != null) {
        //     $products = $products->where('id', $request->product_id);
        //     $data['product_id']=$request->product_id;
        // }

        // if ($request->code != null) {
        //     $products = $products->where('code', $request->code);
        //     $data['code'] = $request->code;
        // }

        // if ($request->name != null) {
        //     $products = $products->where('name', 'like', '%' . $request->name . '%');
        //     $data['name']=$request->name;
        // }

        // if ($request->category != null) {
        //     $products = $products->where('category_id', $request->category);
        //     $data['category']=$request->category;
        // }

        if ($request->brand != null) {
            $products = $products->where('brand_id', $request->brand)->get();
            $data['brand']=$request->brand;
        }else{
            $products= $products->where('brand_id',1)->paginate(50);
        }

        return view('pages.stock.index',$data, compact('products'));
    }


}
