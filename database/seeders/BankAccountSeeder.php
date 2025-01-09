<?php
namespace Database\Seeders;
use App\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BankAccount::create([
            'name' => 'CASH',
            'default'=>1
        ]);
    }
}
