<?php

namespace Database\Seeders;

use App\Models\LeaseType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LeaseTypeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('lease_types')->delete();

        LeaseType::create([
            'lease_type_name' => 'residential',
            'lease_type_display_name' => 'Residential',
            'lease_type_description' => "Residential"
        ]);

        LeaseType::create([
            'lease_type_name' => 'commercial',
            'lease_type_display_name' => 'Commercial',
            'lease_type_description' => "Commercial"
        ]);
    }
}
