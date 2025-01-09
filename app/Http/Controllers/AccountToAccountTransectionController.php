<?php

namespace App\Http\Controllers;

use App\AccountToAccountTransection;
use Illuminate\Http\Request;

class AccountToAccountTransectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transections = AccountToAccountTransection::paginate(20);
        return view("pages.account_to_account.index", compact('transections'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AccountToAccountTransection  $accountToAccountTransection
     * @return \Illuminate\Http\Response
     */
    public function destroy($accountToAccountTransection)
    {
        $transection = AccountToAccountTransection::find($accountToAccountTransection);

        if ($transection->delete()) {

            session()->flash('success', 'Deleted successfully!');
        } else {
            session()->flash('warning', 'Deletion Failed!');
        }

        return redirect()->back();
    }
}
