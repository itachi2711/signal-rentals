<?php

namespace Database\Seeders;

use App\Models\UnitType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class UnitTypeDatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('unit_types')->delete();

        UnitType::create([
            'unit_type_name' => 'single_room',
            'unit_type_display_name' => 'Single Room',
            'unit_type_description' => "Single Room"
        ]);

        UnitType::create([
            'unit_type_name' => 'bed_sitter',
            'unit_type_display_name' => 'Bed Sitter',
            'unit_type_description' => "Bed Sitter"
        ]);

        UnitType::create([
            'unit_type_name' => 'one_bed_apartment',
            'unit_type_display_name' => 'One Bed Room',
            'unit_type_description' => "One Bed Room"
        ]);

        UnitType::create([
            'unit_type_name' => 'two_bed_apartment',
            'unit_type_display_name' => 'Two Bed Room',
            'unit_type_description' => "Two Bed Room"
        ]);
    }
}
