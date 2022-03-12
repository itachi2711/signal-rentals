<?php

namespace Database\Seeders;

use App\Models\LeaseMode;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LeaseModeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('lease_modes')->delete();

        LeaseMode::create([
            'lease_mode_name' => 'fixed_period',
            'lease_mode_display_name' => 'Fixed Period',
            'lease_mode_description' => "Fixed Period"
        ]);

        LeaseMode::create([
            'lease_mode_name' => 'period_period',
            'lease_mode_display_name' => 'Period to Period',
            'lease_mode_description' => "Period to Period"
        ]);
    }
}
