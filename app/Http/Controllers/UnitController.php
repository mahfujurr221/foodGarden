<?php

namespace App\Http\Controllers;

use App\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create-unit',  ['only' => ['create', 'store']]);
        $this->middleware('can:delete-unit', ['only' => ['destroy']]);
        $this->middleware('can:list-unit', ['only' => ['index']]);
        // $this->middleware('can:show-customer', ['only' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units=Unit::paginate(20);
        return view('pages.unit.index',compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $units=Unit::all();
        return view('pages.unit.create',compact('units'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'related_to_unit_id'=>['nullable',function($attribute, $value, $fail)use($request) {
                if(!$request->related_to_unit_id||!$request->related_sign||!$request->related_by){
                    return $fail("This Field has other related fields.");
                }
            }],
            'related_sign'=>['nullable',function($attribute, $value, $fail)use($request) {
                if(!$request->related_to_unit_id||!$request->related_sign||!$request->related_by){
                    return $fail("This Field has other related fields.");
                }
            }],
            'related_by'=>['nullable',function($attribute, $value, $fail)use($request) {
                if(!$request->related_to_unit_id||!$request->related_sign||!$request->related_by){
                    return $fail("This Field has other related fields.");
                }
            }]
        ]);
        // dd($request->all());
        $unit=Unit::create($request->all());

        if($unit){
            session()->flash('success', 'Product Stored.');
            return redirect()->route('unit.index');
        }

        session()->flash('error', 'Oops Something went wrong!');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(Unit $unit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Unit $unit)
    {
        $unit->name = $request->name;
        $unit->save();
        session()->flash('success', 'Updated Successfully!');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit)
    {
        if ($unit->delete()) {
            session()->flash('success', 'Deleted Successfully!');
        } else {
            session()->flash('warning', 'Deletion Failed!');
        }

        return back();
    }

    public function get_related(Unit $unit)
    {
        // return $unit;
        return $unit->related_unit;
    }
}
