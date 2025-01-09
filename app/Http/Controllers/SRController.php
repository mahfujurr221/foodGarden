<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SR;

class SRController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create-sr',  ['only' => ['create', 'store']]);
        $this->middleware('can:edit-sr',  ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-sr', ['only' => ['destroy']]);
        $this->middleware('can:list-sr', ['only' => ['index']]);
        // $this->middleware('can:show-customer', ['only' => ['show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sr_list = SR::query();
        if ($request->name) {
            $sr_list->where('name', 'LIKE', '%' . $request->name . '%');
        }
        if ($request->mobile) {
            $sr_list->where('mobile', $request->mobile);
        }
        return view('pages.sr.index')->with([
            'sr_list' => $sr_list->latest()->withCount('customers')->paginate()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.sr.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|min:3|max:191',
            'mobile'    => 'required|string|min:3|unique:s_r_s',
            'email'     => 'nullable|string|email|max:191',
            'address'   => 'nullable|string|max:500'
        ]);

        $sr = SR::create($data);
        session()->flash('success', 'New SR has been created');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SR  $sR
     * @return \Illuminate\Http\Response
     */
    public function show(SR $sr)
    {
        $customers = $sr->customers()->paginate();
        return view('pages.sr.report')
            ->with('customers', $customers)
            ->with('sr', $sr);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SR  $sR
     * @return \Illuminate\Http\Response
     */
    public function edit(SR $sr)
    {
        return view('pages.sr.edit')->with([
            'sr' => $sr
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SR  $sR
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SR $sr)
    {
        $data = $request->validate([
            'name'      => 'required|string|min:3|max:191',
            'mobile'    => 'required|string|min:3|unique:s_r_s,mobile,' . $sr->id,
            'email'     => 'nullable|string|email|max:191',
            'address'   => 'nullable|string|max:500'
        ]);

        $sr->update($data);

        session()->flash('success', 'SR has been updated');

        return redirect()->route('sr.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SR  $sR
     * @return \Illuminate\Http\Response
     */
    public function destroy(SR $sr)
    {
        if ($sr->forceDelete()) {
            session()->flash('success', 'SR has been deleted');
            return back();
        }

        session()->flash('warning', 'SR does not deleted');
        return back();
    }
}
