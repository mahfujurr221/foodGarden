<?php

namespace App\Http\Controllers;

use App\Helpers\InputHelper;
use Spatie\Permission\Models\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $avatar;
    private $path;

    public function __construct()
    {
        $this->path = 'dashboard/images/avatars/';
        $this->avatar = 'dashboard/img/avatar/1.jpg';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentUser = Auth::id();
        $users = User::whereHas('roles', function ($roles) {
            $roles->where('name', '!=', 'test_admin');
        // })->orderBy('created_at', 'desc')->where('id', '!=', $currentUser)->paginate(10);
        })->orderBy('created_at', 'desc')->paginate(10);
        return view('pages.users.index')
            ->withUsers($users);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('name', '!=', 'test_admin')->get();
        return view('pages.users.create', compact('roles'));
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
            'fname' => 'required|max:191',
            'lname' => 'required|max:191',
            'email' => 'required|max:191|email|unique:users',
            'role' => 'required',
            'brand_id' => 'required|array',
            'password' => 'required|max:191|min:6|same:password',
            'password_confirmation' => 'required|min:6|max:191|same:password',
        ]);

        $data = $request->all();
        unset($data['role']);
        unset($data['avatar']);
        unset($data['password_confirmation']);
        // $data['role_id'] = $request->role;
        $data['password'] = Hash::make($request->password);
        // dd($data);
        $user = User::create($data);
        $brandIds = $request->input('brand_id', []);
        // $user->brands()->sync($brandIds);
        $update= User::find($user->id);
        $update->brand_id = $brandIds;
        $update->save();

        $user->syncRoles([$request->role]);
        $avatarLink = '';
        if ($request->hasFile('avatar')) {
            if (config('pos.app_mode') == 'demo') {
                session()->flash('error', 'Image Upload is disabled in demo.');
                $avatarLink = $this->avatar;
            } else {
                $avatarLink = InputHelper::uploadWithCrop($request->avatar, $this->path, 128, 128);
            }
        } else {
            $avatarLink = $this->avatar;
        }
        $user->save();

        $user->profile()->create([
            'user_id' => $user->id,
            'avatar' => $avatarLink
        ]);

        session()->flash('success', 'User Create.');
        return back();
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $roles = Role::where('name', '!=', 'test_admin')->get();
        $user = User::find($id);

        return view('pages.users.edit')
            ->withUser($user)
            ->withRoles($roles);
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
        $user = User::find($id);
        $this->validate($request, [
            'fname' => 'required|max:191',
            'lname' => 'required|max:191',
            'email' => 'required|max:191|email|unique:users,email,' . $user->id,
            'role' => 'required',
            'brand_id' => 'required|array',
        ]);
        $brandIds = $request->input('brand_id', []);
        // $user->update([
            //     'fname' => $request->fname,
            //     'lname' => $request->lname,
            //     'email' => $request->email,
            //     'brand_id' => $request->brand_id,
            // ]);
            $user->fname = $request->fname;
            $user->lname = $request->lname;
            $user->email = $request->email;
            $user->brand_id = $brandIds;
        
        $user->syncRoles([$request->role]);

        $avatarLink = '';
        if ($request->hasFile('avatar')) {
            if (config('pos.app_mode') == 'demo') {
                session()->flash('error', 'Image Upload is disabled in demo.');
            } else {
                if ($user->profile->avatar != $this->avatar) {
                    InputHelper::delete($user->profile->avatar);
                }
                $avatarLink = InputHelper::uploadWithCrop($request->avatar, $this->path, 128, 128);
                $user->profile()->update([
                    'avatar' => $avatarLink
                ]);
            }
        }

        $user->save();
        session()->flash('success', 'User Update.');

        return back();

        return $request;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if ($user->profile->avatar != $this->avatar) {
            InputHelper::delete($user->profile->avatar);
        }
        $user->delete();
        $user->profile()->delete();

        session()->flash('success', 'User Delete.');
        return back();
    }
}
