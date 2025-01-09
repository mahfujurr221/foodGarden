<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CrudController extends Controller
{
    // protected $model = "";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data['items']= $this->model::orderBy('id','desc')->paginate(20);

        return view($this->view_path.".index",$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->view_path . ".create");

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $this->model::create($request->all());

        session()->flash('success', 'Created Successfully!');

        return redirect()->route($this->route.".index");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['item'] = $this->model::find($id);
        return view($this->view_path . ".show", $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['item']= $this->model::find($id);
        return view($this->view_path . ".edit", $data);
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
        $object=$this->model::find($id);
        $object->update($request->all());

        session()->flash('success', 'Updated Successfully!');


        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object= $this->model::find($id);

        if($object->delete()){
            // deleted message
            session()->flash('success', 'Deleted Successfully!');

        }else{
            // not deleted
            session()->flash('error', 'Deletion Failed!');
        }

        return redirect()->back();
    }


    // Validated
    public function store_validated($validated)
    {
        $this->model::create($validated);

        session()->flash('success', 'Created Successfully!');

        return redirect()->route($this->route.".index");
    }

    public function update_validated($validated, $id)
    {
        $object=$this->model::find($id);
        $object->update($validated);

        session()->flash('success', 'Updated Successfully!');


        return redirect()->back();
    }
}
