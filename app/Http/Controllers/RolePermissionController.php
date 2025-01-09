<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function edit(Role $role)
    {

        // if($role->id==1){
        //     alert()->error('You are not allowed to edit permissions for Super Admin Role');
        //     return redirect()->route('roles.index');
        // }
        // dd($role);
        $permission_groups=Permission::get()->groupBy('feature')->sortBy('order');

        return view('pages.role_permission.edit',compact('role','permission_groups'));
    }


    public function update(Request $request, Role $role)
    {
        $role->syncPermissions($request->permissions);

        session()->flash('success', 'Updated Successfully!');
        return back();
    }
}
