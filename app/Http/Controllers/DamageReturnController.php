<?php

namespace App\Http\Controllers;

use App\DamageReturn;
use App\Product;
use Illuminate\Http\Request;

class DamageReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $damages = new DamageReturn();
        if ($request->product) {
            $damages = $damages->where('product_id', $request->product);
        }

        if ($request->id) {
            $damages = $damages->where('id', $request->id);
        }

        $damages = $damages->orderBy('date', 'desc')->paginate(10);
        $products = Product::select('id', 'name', 'code')->get();
        return view('pages.damage.damage-return', compact('damages', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $damage = DamageReturn::find($id);
        $damage->delete();
        return redirect()->back()->with('success', 'Damage Return Deleted Successfully');
    }
}