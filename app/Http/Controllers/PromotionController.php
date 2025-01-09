<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:promotional_sms',  ['only' => ['promotion_sms', 'send_promotion_sms']]);
    }

    public function promotion_sms()
    {
        $customers = Customer::all();
        return view('pages.promotion.sms')
            ->with([
                'customers' => $customers
            ]);
    }

    public function send_promotion_sms(Request $request)
    {
        $request->validate([
            'customers' => 'required',
            'sms' => 'required|string|max:159'
        ]);

        foreach ($request->customers as $key => $cId) {
            $customer = Customer::findOrFail($cId);
            $sms_body = "Dear " . $customer->name . ",\n" . $request->sms;
            $mobile_number = "88" . $customer->phone;
            $api_key = "\$2y\$10\$";

            if ($mobile_number != null) {
                $url = "http://sms.softghor.com/smsapi/non-masking?api_key=".$api_key."&smsType=text&mobileNo=" . $mobile_number . "&smsContent=" . urlencode($sms_body);

                $ch = curl_init($url); // such as http://example.com/example.xml
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $data = curl_exec($ch);
                curl_close($ch);
            }
        }
        
        if(isset($data) && $data != 1003){
            session()->flash('success', 'Promotional SMS send successfully.');
        } else {
            session()->flash('error', 'Something went wrong. Promotional SMS can\'t be send.');
        }

        return back();
    }
}
