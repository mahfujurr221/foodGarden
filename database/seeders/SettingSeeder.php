<?php
namespace Database\Seeders;
use App\PosSetting;
use App\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting_pos = new PosSetting();
        $setting_pos->company = 'Softghor.Com';
        $setting_pos->logo = 'dashboard/images/Final-Logo03.png';
        $setting_pos->address = 'Holding: 53 (1st floor), Road: 04 Block: G,Banasree , Dhaka 1219. ';
        $setting_pos->email = 'info@softghor.com';
        $setting_pos->phone = '01779724380';
        $setting_pos->header_text = 'This is Header Text';
        $setting_pos->footer_text = 'This is Footer Text';
        $setting_pos->save();

        // Setting
        // $setting = new Setting();
        // $setting->name = 'Softghor.Com';
        // $setting->min_logo = 'dashboard/images/logo-light-lg.png';
        // $setting->logo = 'dashboard/images/Final-Logo03.png';
        // $setting->country = 'Bangladesh';
        // $setting->city = 'Dhaka';
        // $setting->address = 'Holding: 05 (2nd floor), Road: 03 Block: G,Banasree , Dhaka 1219. ';
        // $setting->email = 'info@softghor.com';
        // $setting->phone = '01779724380';
        // $setting->header_text = 'This is Header Text';
        // $setting->footer_text = 'This is Footer Text';
        // $setting->link = 'https://softghor.com';
        // $setting->save();
    }
}
