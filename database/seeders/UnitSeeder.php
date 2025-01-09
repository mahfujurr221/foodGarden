<?php
namespace Database\Seeders;
use App\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $piece = Unit::create([
            'name'=>'pc',
            'default'=>1
        ]);

        Unit::create([
            'name'=>'Dozen',
            'related_to_unit_id'=>$piece->id,
            'related_sign'=>'*',
            'related_by'=>12
        ]);

        $gm = Unit::create([
            'name'=>'gm'
        ]);


        Unit::create([
            'name'=>'Kg',
            'related_to_unit_id'=>$gm->id,
            'related_sign'=>'*',
            'related_by'=>1000
        ]);

        $ml = Unit::create([
            'name'=>'ml'
        ]);


        Unit::create([
            'name'=>'Litre',
            'related_to_unit_id'=>$ml->id,
            'related_sign'=>'*',
            'related_by'=>1000
        ]);

    }
}
