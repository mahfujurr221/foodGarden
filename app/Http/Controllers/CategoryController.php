<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    private $file_path;
    public function __construct()
    {
        $this->file_path = 'dashboard/images/category/';

        $this->middleware('can:create-category',  ['only' => ['create', 'store']]);
        $this->middleware('can:edit-category',  ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-category', ['only' => ['destroy']]);
        $this->middleware('can:list-category', ['only' => ['index']]);
        // $this->middleware('can:show-brand', ['only' => ['show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('pages.category.index')
            ->withCategories($categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.category.create');
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
            'name' => 'required',
            'use_file'=>'nullable|image|mimes:jpg,jpeg,png'
            // 'code' => 'required'
        ]);
        $category = Category::create([
            'name' => $request->name,
            // 'code' => $request->code
        ]);
        if ($request->hasFile('use_file')) {
            if (config('pos.app_mode') == 'demo') {
                session()->flash('error', 'Image Upload is disabled in demo.');
            }else{
                $files = $request->file('use_file');
                $fileName = time() . '.' . $files->getClientOriginalExtension();
                $destination_path = public_path($this->file_path);
                $files->move($destination_path, $fileName);
                $fileLink = $this->file_path . $fileName;
                $category->image()->create(['link' => $fileLink]);
            }
        }

        session()->flash('success', 'Category Created...');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return view('pages.category.edit')->withCategory($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $this->validate($request, [
            'name' => 'required',
            'use_file'=>'nullable|image|mimes:jpg,jpeg,png'
            // 'code' => 'required'
        ]);
        $category->update([
            'name' => $request->name
            // 'code' => $request->code
        ]);

        if ($request->hasFile('use_file')) {
            if (config('pos.app_mode') == 'demo') {
                session()->flash('error', 'Image Upload is disabled in demo.');
            }else{
                // delete old image
                if ($category->image) {
                    File::delete($category->image->link);
                    $category->image()->delete();
                }

                $files = $request->file('use_file');
                $fileName = time() . '.' . $files->getClientOriginalExtension();
                $destination_path = public_path($this->file_path);
                $files->move($destination_path, $fileName);
                $fileLink = $this->file_path . $fileName;
                $category->image()->create(['link' => $fileLink]);
            }

        }

        session()->flash('success', 'Category Update...');
        return redirect()->route('category.edit', $category->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        if ($category->delete()) {
            if ($category->image) {
                File::delete($category->image->link);
                $category->image()->delete();
            }
            session()->flash('success', 'Deleted Successfully.');
        } else {
            session()->flash('warning', 'Deletion Failed. Category Might have product.');
        }
        return redirect()->back();
    }
}
