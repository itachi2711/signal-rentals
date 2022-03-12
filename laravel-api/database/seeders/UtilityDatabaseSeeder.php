<?php

namespace Database\Seeders;

use App\Models\Utility;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UtilityDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('utilities')->delete();

        $permissions = [
            [
                'utility_name'           => 'electricity',
                'utility_display_name'   => 'Electricity',
                'utility_description'    => 'Electricity'
            ],
            [
                'utility_name'           => 'water',
                'utility_display_name'   => 'Water',
                'utility_description'    => 'Water'
            ],
            [
                'utility_name'           => 'garbage',
                'utility_display_name'   => 'Garbage',
                'utility_description'    => 'Garbage'
            ]
        ];

        foreach ($permissions as $key => $value){
            Utility::create($value);
        }
    }
}
