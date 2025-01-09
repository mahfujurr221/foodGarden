<?php

namespace App\Http\Controllers;

use App\Owner;
use Illuminate\Http\Request;

class OwnerController extends CrudController
{
    protected $model = Owner::class;
    protected $view_path = "pages.owners";
    protected $route = 'owners';

    public function __construct()
    {
        $this->middleware('can:create-owner',  ['only' => ['create', 'store']]);
        $this->middleware('can:edit-owner',  ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-owner', ['only' => ['destroy']]);
        $this->middleware('can:list-owner', ['only' => ['index']]);
        // $this->middleware('can:show-customer', ['only' => ['show']]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' =>'nullable|string|max:20',
            'address' =>'nullable|string|max:3000'
        ]);

        return parent::store($request);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' =>'nullable|string|max:20',
            'address' =>'nullable|string|max:3000'
        ]);

        return parent::update($request, $id);
    }
}
