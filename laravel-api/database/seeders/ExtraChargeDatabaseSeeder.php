<?php

namespace Database\Seeders;

use App\Models\ExtraCharge;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExtraChargeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('extra_charges')->delete();

        ExtraCharge::create([
            'extra_charge_name' => 'Processing Fee',
            'extra_charge_display_name' => 'Processing Fee',
            'extra_charge_description' => 'Processing Fee'
        ]);

        ExtraCharge::create([
            'extra_charge_name' => 'Service Fee',
            'extra_charge_display_name' => 'Service Fee',
            'extra_charge_description' => 'Service Fee'
        ]);

        ExtraCharge::create([
            'extra_charge_name' => 'VAT',
            'extra_charge_display_name' => 'VAT',
            'extra_charge_description' => 'VAT'
        ]);
    }
}
