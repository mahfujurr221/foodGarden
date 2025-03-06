<?php
namespace App\Services;

use App\Supplier;

class SupplierService{
    public static function default_supplier()
    {
        $exist=Supplier::where('status', 1)->where('default', 1)->first();
        if($exist){
            return $exist;
        }else{
            return Supplier::create([
                'name' => 'Default Supplier',
                'email' => 'default@supplier.com',
                'phone' => '111111',
                'address' => 'Default Address',
                'default' => '1'
            ]);
        }
    }
}
