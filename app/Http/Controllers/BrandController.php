<?php

namespace App\Http\Controllers;

use App\Brand;
use Illuminate\Support\Str;
use App\Helpers\InputHelper;
use App\Imports\BrandImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class BrandController extends Controller
{
    private $file_path;

    public function __construct()
    {
        $this->file_path = 'dashboard/images/brands/';

        $this->middleware('can:create-brand',  ['only' => ['create', 'store']]);
        $this->middleware('can:edit-brand',  ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-brand', ['only' => ['destroy']]);
        $this->middleware('can:list-brand', ['only' => ['index']]);
        // $this->middleware('can:show-brand', ['only' => ['show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = Brand::latest()->paginate(10);
        return view('pages.brand.index')
            ->withBrands($brands);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.brand.create');
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
            'name' => 'required|unique:brands',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:300'
        ]);

        $brand = Brand::create([
            'name' => $request->name,
            'slug' => str_slug($request->name),
            'description' => $request->description
        ]);

        // Logo Upload
        if ($request->hasFile('logo')) {
            if (config('pos.app_mode') == 'demo') {
                session()->flash('error', 'Image Upload is disabled in demo.');
            }else{
                $file_link = InputHelper::upload($request->file('logo'), $this->file_path);
                $brand->logo()->create(['link' => $file_link]);
            }
        }

        session()->flash('success', 'Brand Created Success');
        return redirect()->route('brand.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand)
    {
        return view('pages.brand.edit')->withBrand($brand);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {
        $this->validate($request, [
            'name' => 'required|unique:brands,name,' . $brand->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:300'
        ]);

        $brand->update([
            'name' => $request->name,
            'slug' => str_slug($request->name),
            'description' => $request->description
        ]);

        // Logo Update
        if ($request->hasFile('logo')) {

            if (config('pos.app_mode') == 'demo') {
                session()->flash('error', 'Image Upload is disabled in demo.');
            }else{
            //Delete Existing File
                $brand->deleteLogo();
                $file_link = InputHelper::upload($request->file('logo'), $this->file_path);

                $brand->logo()->create(['link' => $file_link]);
            }
        }

        session()->flash('success', 'Brand Update Success...');

        return redirect()->route('brand.edit', $brand->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        $brand->deleteLogo();
        if($brand->delete()){
            session()->flash('success', 'Brand Deleted !');
        }else{
            session()->flash('error', 'Deletion Failed! This Brand have Products.');
        }

        return back();
    }

    public function import()
    {
        return view('pages.brand.import');
    }

    public function import_store(Request $request)
    {
        $this->validate($request, [
            'import_file' => 'required'
        ]);

        if ($request->hasFile('import_file')) {
            $data = Excel::toCollection(new BrandImport, $request->file('import_file'));

            dd($data->toArray());
            // foreach ($data as $brand) {
            //     $name = $brand[1][0];
            //     $description = $brand[1][1];

            //     $existingBrand = Brand::where('name', $name)->first();
            //     if ($existingBrand) {
            //         $existingBrand->update([
            //             'name' => $name,
            //             'slug' => Str::slug($name),
            //             'description' => $description
            //         ]);
            //     } else {
            //         Brand::create([
            //             'name' => $name,
            //             'slug' => Str::slug($name),
            //             'description' => $description
            //         ]);
            //     }
            // }
        }
        session()->flash('success', 'Import Completed.');
        return back();
    }
}
