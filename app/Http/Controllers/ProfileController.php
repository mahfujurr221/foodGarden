<?php

namespace App\Http\Controllers;

use App\Helpers\InputHelper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    private $avatar;
    private $path;

    public function __construct()
    {
        $this->path = 'dashboard/images/avatars/';
        $this->avatar = 'dashboard/img/avatar/1.jpg';

        $this->middleware('can:profile',  ['only' => ['index', 'update']]);
        $this->middleware('can:change_password',  ['only' => ['change_password', 'update_password']]);

    }

    public function disable_feature()
    {
        session()->flash('error', 'Sorry this feature is disabled.');
        return redirect()->back();
    }


    public function index()
    {
        $user = User::find(Auth::id());
        return view('pages.profile.index')->withUser($user);
    }

    public function update(Request $request)
    {
        // dd("HELLO");
        $user = User::find(Auth::id());

        $this->validate($request, [
            'fname' => 'required|max:255',
            'lname' => 'required|max:255',
            'email' => 'required|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:500'
        ]);

        ///////////////////////////
        if (config('pos.app_mode') == 'demo') {
            return $this->disable_feature();
        }
        //////////////////////////


        if ($request->hasFile('avatar')) {
            if (config('pos.app_mode') == 'demo') {
                session()->flash('error', 'Image Upload is disabled in demo.');
            }else{
                if ($user->profile->avatar != $this->avatar) {
                    InputHelper::delete($user->profile->avatar);
                }
                $avatar = InputHelper::uploadWithCrop($request->avatar, $this->path, 128, 128);
                $user->profile()->update([
                    'avatar' => $avatar
                ]);
            }
        }
        // data upate

        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->email = $request->email;
        $user->save();

        session()->flash('success', 'Profile Update Success...');

        return back();
    }

    public function change_password()
    {
        return view('pages.profile.change-password');
    }

    public function update_password(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'required',
            'password' => 'required|same:password|min:6',
            'password_confirmation' => 'required|same:password'
        ]);
        if (Auth::Check()) {
            $current_password = Auth::User()->password;
            if (Hash::check($request->current_password, $current_password)) {


                ///////////////////////////
                if (config('pos.app_mode') == 'demo') {
                    return $this->disable_feature();
                }
                //////////////////////////

                $user = User::find(Auth::id());
                $user->password = Hash::make($request->password);
                $user->save();
            } else {
                session()->flash('error', 'Sorry, Your current password don\'t match.');
                return back();
            }
        }
        session()->flash('success', 'Password Update Success. Thanks.');
        return back();
    }
}
