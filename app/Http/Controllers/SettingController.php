<?php

namespace App\Http\Controllers;

use App\Setting;
use App\Helpers\InputHelper;
use App\PosSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    private $min_logo;
    private $logo;
    private $path;

    public function __construct()
    {
        $this->path = 'dashboard/images/logos/';
        $this->logo = 'dashboard/images/Final-Logo03.png';
        $this->mini_logo = 'dashboard/images/logo-light-lg.png';

        $this->middleware('can:setting',  ['only' => ['create_pos_setting', 'update_pos_setting']]);

    }

    // public function index()
    // {
    //     return view('pages.settings.index');
    // }

    // public function setting_update(Request $request)
    // {
    //     // dd($request->all());
    //     $this->validate($request, [
    //         'name' => 'required|max:255',
    //         'email' => 'required|email',
    //         'address' => 'required',
    //         'phone' => 'required'
    //     ]);
    //     $setting = Setting::first();
    //     // Mini Logo
    //     if ($request->hasFile('mini_logo')) {
    //         $mini_logo = InputHelper::upload($request->mini_logo, $this->path);
    //         $setting->mini_logo = $mini_logo;
    //     } else {
    //         $setting->min_logo = $this->mini_logo;
    //     }
    //     // Large Logo
    //     if ($request->hasFile('logo')) {
    //         $logo = InputHelper::upload($request->logo, $this->path);
    //         $setting->logo = $logo;
    //     } else {
    //         $setting->logo = $this->logo;
    //     }

    //     // Upate data
    //     $setting->name = $request->name;
    //     $setting->email = $request->email;
    //     $setting->link = $request->link;
    //     $setting->country = $request->country;
    //     $setting->city = $request->city;
    //     $setting->address = $request->address;




    //     $setting->header_text = $request->header_text;
    //     $setting->footer_text = $request->footer_text;
    //     $setting->phone = $request->phone;


    //     if ($request->sale_over_sotck) {
    //         $setting->sale_over_sotck = 1;
    //     } else {
    //         $setting->sale_over_sotck = 0;
    //     }

    //     $setting->save();

    //     session()->flash('success', 'Setting is Updated');

    //     return back();
    // }

    public function create_pos_setting()
    {
        $pos_setting = PosSetting::first();

        return view('pages.settings.pos_setting')
            ->with('pos_setting', $pos_setting);
    }

    public function update_pos_setting(Request $request)
    {
        $this->validate($request, [
            'company' => 'required|max:255',
            'email' => 'email|max:255',
            'phone' => 'required|max:191',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:500'
        ]);

        $posSetting = PosSetting::first();

        // Large Logo
        if ($request->hasFile('logo')) {
            if (config('pos.app_mode') == 'demo') {
                session()->flash('error', 'Image Upload is disabled in demo.');
            }else{
                if ($posSetting->logo != $this->logo) {
                    InputHelper::delete($posSetting);
                }
                $logo = InputHelper::upload($request->logo, $this->path);
                $posSetting->logo = $logo;
            }
        }
        //data
        $posSetting->company = $request->company;
        $posSetting->email = $request->email;
        $posSetting->phone = $request->phone;
        $posSetting->address = $request->address;
        $posSetting->header_text = $request->header_text;
        $posSetting->footer_text = $request->footer_text;
        $posSetting->invoice_type = $request->invoice_type;
        $posSetting->barcode_type = $request->barcode_type;

        $posSetting->invoice_logo_type = $request->invoice_logo_type;
        $posSetting->low_stock = $request->low_stock;
        $posSetting->low_stock = $request->low_stock;

        if ($request->dark_mode) {
            if (config('pos.app_mode') == 'demo') {
                session()->flash('error', 'Dark mode is disabled in demo.');
                return back();
            }else{
                $posSetting->dark_mode = 1;
            }
        } else {
            $posSetting->dark_mode = 0;
        }

        Cache::forget('dark-mode');

        // $posSetting->page_link = $request->page_link;
        // $posSetting->website = $request->website;

        // if ($request->sale_over_sotck) {
        //     $posSetting->sale_over_sotck = 1;
        // } else {
        //     $posSetting->sale_over_sotck = 0;
        // }
        $posSetting->save();

        session()->flash('success', 'POS Setting Updated.');
        return back();
    }
}
