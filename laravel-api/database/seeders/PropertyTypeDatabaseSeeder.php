<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PropertyTypeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('property_types')->delete();

        PropertyType::create([
            'name' => 'apartment',
            'display_name' => 'Apartment',
            'description' => "Apartment"
        ]);

        PropertyType::create([
            'name' => 'commercial',
            'display_name' => 'Commercial',
            'description' => "Commercial"
        ]);

        PropertyType::create([
            'name' => 'duplex',
            'display_name' => 'Duplex',
            'description' => "Duplex"
        ]);

        PropertyType::create([
            'name' => 'house',
            'display_name' => 'House',
            'description' => "House"
        ]);

        PropertyType::create([
            'name' => 'mixed-use',
            'display_name' => 'Mixed Use',
            'description' => "Mixed Use"
        ]);

        PropertyType::create([
            'name' => 'other',
            'display_name' => 'Other',
            'description' => "Other"
        ]);
    }
}
