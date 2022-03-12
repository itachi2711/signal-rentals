<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AmenityDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('amenities')->delete();

        $permissions = [
            [
                'amenity_name'           => 'a_c',
                'amenity_display_name'   => 'A/C',
                'amenity_description'    => 'A/C'
            ],
            [
                'amenity_name'           => 'pool',
                'amenity_display_name'   => 'Pool',
                'amenity_description'    => 'Pool'
            ],
            [
                'amenity_name'           => 'pets_allowed',
                'amenity_display_name'   => 'Pets Allowed',
                'amenity_description'    => 'Pets Allowed'
            ],
            [
                'amenity_name'           => 'furnished',
                'amenity_display_name'   => 'Furnished',
                'amenity_description'    => 'Furnished'
            ],
            [
                'amenity_name'           => 'balcony',
                'amenity_display_name'   => 'Balcony/Deck',
                'amenity_description'    => 'Balcony/Deck'
            ],
            [
                'amenity_name'           => 'hardwood_floor',
                'amenity_display_name'   => 'Hardwood Floor',
                'amenity_description'    => 'Hardwood Floor'
            ],
            [
                'amenity_name'           => 'wheelchair_access',
                'amenity_display_name'   => 'Wheelchair Access',
                'amenity_description'    => 'Wheelchair Access'
            ],
            [
                'amenity_name'           => 'parking',
                'amenity_display_name'   => 'Parking',
                'amenity_description'    => 'Parking'
            ]
        ];

        foreach ($permissions as $key => $value){
            Amenity::create($value);
        }
    }
}
